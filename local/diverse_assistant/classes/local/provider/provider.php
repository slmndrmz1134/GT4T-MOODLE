<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace local_diverse_assistant\local\provider;

/**
 * An AI service the assistant can talk to.
 *
 * Each service (OpenAI, and in later versions Claude, Gemini...) is one subclass. The rest of the plugin only uses
 * these methods, so adding a service does not change the chat code.
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class provider {
    /** Seconds to wait for the connection. */
    protected const CONNECT_TIMEOUT = 10;

    /** Seconds an answer may take in total. */
    protected const TIMEOUT = 180;

    /**
     * Name of the service, shown to administrators and students.
     *
     * @return string
     */
    abstract public function get_name(): string;

    /**
     * Whether requests are guaranteed to be processed in the European Union.
     *
     * @return bool
     */
    abstract public function is_eu(): bool;

    /**
     * Models this API key can use. Also checks that the key works.
     *
     * @return string[] Model ids, sorted.
     * @throws provider_exception
     */
    abstract public function list_models(): array;

    /**
     * Send a conversation and stream the answer.
     *
     * @param array $messages List of ['role' => 'system'|'user'|'assistant', 'content' => string].
     * @param callable|null $ondelta Called with each new piece of the answer as it arrives.
     * @return chat_result
     * @throws provider_exception
     */
    abstract public function chat(array $messages, ?callable $ondelta = null): chat_result;

    /**
     * A Moodle cURL client: it applies the site's proxy and blocked-hosts settings.
     *
     * @param string[] $headers HTTP headers.
     * @return \curl
     */
    protected function create_curl(array $headers): \curl {
        global $CFG;
        require_once($CFG->libdir . '/filelib.php');

        $curl = new \curl();
        $curl->setHeader($headers);
        $curl->setopt([
            'CURLOPT_CONNECTTIMEOUT' => static::CONNECT_TIMEOUT,
            'CURLOPT_TIMEOUT' => static::TIMEOUT,
        ]);
        return $curl;
    }

    /**
     * Turn a failed request into an exception with a message students can read.
     *
     * @param \curl $curl The client after the request.
     * @param int $status HTTP status (0 if there was no response).
     * @param string $body Response body, used for the administrators' details.
     * @throws provider_exception
     */
    protected function throw_on_error(\curl $curl, int $status, string $body): void {
        if ($curl->get_errno()) {
            throw new provider_exception('errorconnection', $curl->error);
        }
        if ($status >= 200 && $status < 300) {
            return;
        }
        // Keep the service's message on one line for the settings page.
        $detail = trim(preg_replace('/\s+/', ' ', $this->error_detail($body)));
        throw new provider_exception($this->classify_error($status, $detail), trim("HTTP {$status} {$detail}"));
    }

    /**
     * Which message a failed request gets.
     *
     * @param int $status HTTP status (0 if there was no response).
     * @param string $detail The service's error message.
     * @return string String identifier in local_diverse_assistant.
     */
    protected function classify_error(int $status, string $detail): string {
        return match (true) {
            // No HTTP status: the site's security settings blocked the address.
            $status === 0 => 'errorconnection',
            $status === 401 => 'errorauth',
            $status === 403 => 'errorforbidden',
            $status === 404 => 'errormodel',
            $status === 429 => 'errorratelimit',
            // Overloaded or temporarily failing (529 is Anthropic's "overloaded").
            in_array($status, [500, 502, 503, 504, 529], true) => 'errorbusy',
            default => 'errorservice',
        };
    }

    /**
     * The error message inside a service's error body, or the start of the body.
     *
     * @param string $body Response body.
     * @return string
     */
    protected function error_detail(string $body): string {
        $decoded = json_decode($body, true);
        if (is_array($decoded) && array_is_list($decoded) && isset($decoded[0]) && is_array($decoded[0])) {
            // Some services wrap the error in a list.
            $decoded = $decoded[0];
        }
        if (is_array($decoded) && isset($decoded['error'])) {
            $error = $decoded['error'];
            return is_array($error) ? (string)($error['message'] ?? json_encode($error)) : (string)$error;
        }
        return \core_text::substr(trim(strip_tags($body)), 0, 300);
    }
}
