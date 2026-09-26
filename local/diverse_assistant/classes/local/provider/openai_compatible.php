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
 * Any service with an OpenAI-compatible Chat Completions API (Mistral, OpenRouter, Ollama, vLLM, a university gateway...).
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class openai_compatible extends provider {
    /** Longest answer, in tokens. */
    protected const MAX_OUTPUT_TOKENS = 2000;

    /** Waits before retrying when the service is overloaded, in milliseconds. */
    private const RETRY_DELAYS = [1000, 2500];

    /**
     * Constructor.
     *
     * @param string $baseurl API address up to the version, e.g. https://api.mistral.ai/v1
     * @param string $apikey API key, sent as a Bearer token.
     * @param string $model Model id.
     * @param bool $eu Whether the administrator confirmed that the service processes data in the EU.
     */
    public function __construct(
        /** @var string API address up to the version. */
        protected string $baseurl,
        /** @var string API key. */
        protected string $apikey,
        /** @var string Model id. */
        protected string $model,
        /** @var bool Whether data is processed in the EU. */
        protected bool $eu = false,
    ) {
    }

    #[\Override]
    public function get_name(): string {
        return get_string('provider_openaicompatible', 'local_diverse_assistant');
    }

    #[\Override]
    public function is_eu(): bool {
        return $this->eu;
    }

    #[\Override]
    public function list_models(): array {
        $curl = $this->create_curl($this->headers());
        $response = $curl->get($this->url('/models'));
        $body = is_string($response) ? $response : '';
        $this->throw_on_error($curl, (int)($curl->get_info()['http_code'] ?? 0), $body);

        $decoded = json_decode($body, true);
        if (!is_array($decoded) || !isset($decoded['data']) || !is_array($decoded['data'])) {
            throw new provider_exception('errorservice', 'Unexpected answer from ' . $this->url('/models'));
        }
        $ids = [];
        foreach ($decoded['data'] as $model) {
            if (is_array($model) && !empty($model['id']) && is_string($model['id'])) {
                $ids[] = $model['id'];
            }
        }
        sort($ids);
        return $ids;
    }

    #[\Override]
    public function chat(array $messages, ?callable $ondelta = null, ?chat_options $options = null): chat_result {
        if ($this->model === '') {
            throw new provider_exception('errornomodel');
        }
        $attempt = 0;
        while (true) {
            $received = false;
            $relay = function (string $text) use ($ondelta, &$received): void {
                $received = true;
                if ($ondelta) {
                    $ondelta($text);
                }
            };
            try {
                return $this->send_chat($messages, $relay, $options ?? new chat_options());
            } catch (provider_exception $e) {
                // An overloaded service usually answers a moment later. Once text has reached the student, a retry
                // would write the answer twice.
                if ($e->errorcode !== 'errorbusy' || $received || $attempt >= count(self::RETRY_DELAYS)) {
                    throw $e;
                }
                if (!(defined('PHPUNIT_TEST') && PHPUNIT_TEST)) {
                    usleep(self::RETRY_DELAYS[$attempt] * 1000);
                }
                $attempt++;
            }
        }
    }

    /**
     * Send the conversation once and stream the answer.
     *
     * @param array $messages The conversation.
     * @param callable $ondelta Called with each new piece of the answer.
     * @param chat_options $options Tools and answer length.
     * @return chat_result
     * @throws provider_exception
     */
    protected function send_chat(array $messages, callable $ondelta, chat_options $options): chat_result {
        $stream = new openai_stream($ondelta, $options->ontoolstart);
        $curl = $this->create_curl(array_merge($this->headers(), ['Accept: text/event-stream']));
        $curl->setopt([
            'CURLOPT_WRITEFUNCTION' => function ($handle, string $chunk) use ($stream): int {
                $continue = $stream->write($chunk, (int)curl_getinfo($handle, CURLINFO_HTTP_CODE));
                // Returning less than the chunk length stops the transfer.
                return $continue ? strlen($chunk) : 0;
            },
        ]);
        $response = $curl->post($this->url('/chat/completions'), json_encode($this->build_body($messages, $options)));

        // Unit tests and blocked addresses return the body instead of passing it to the callback.
        if (!$stream->received() && is_string($response) && $response !== '') {
            $stream->write($response, (int)($curl->get_info()['http_code'] ?? 0));
        }
        if ($stream->has_error()) {
            // We stopped the transfer because the service reported an error inside the stream; this throws it.
            return $stream->finish();
        }
        $status = $stream->get_status() ?: (int)($curl->get_info()['http_code'] ?? 0);
        $this->throw_on_error($curl, $status, $stream->get_body());
        return $stream->finish();
    }

    /**
     * The request body.
     *
     * @param array $messages The conversation.
     * @param chat_options $options Tools and answer length.
     * @return array
     */
    protected function build_body(array $messages, chat_options $options): array {
        return [
            'model' => $this->model,
            'messages' => $messages,
            'stream' => true,
            'max_tokens' => $options->maxoutputtokens ?: static::MAX_OUTPUT_TOKENS,
        ] + self::tools_body($options);
    }

    /**
     * The tools part of a Chat Completions request.
     *
     * @param chat_options $options Tools and answer length.
     * @return array Empty when there are no tools.
     */
    protected static function tools_body(chat_options $options): array {
        if (!$options->tools) {
            return [];
        }
        $tools = [];
        foreach ($options->tools as $tool) {
            $tools[] = ['type' => 'function', 'function' => [
                'name' => $tool['name'],
                'description' => $tool['description'],
                'parameters' => $tool['parameters'],
            ]];
        }
        return ['tools' => $tools, 'tool_choice' => 'auto'];
    }

    /**
     * HTTP headers sent with every request.
     *
     * @return string[]
     */
    protected function headers(): array {
        return [
            'Authorization: Bearer ' . $this->apikey,
            'Content-Type: application/json',
        ];
    }

    /**
     * Full address of an API path.
     *
     * @param string $path Path after the base address, e.g. /models
     * @return string
     */
    protected function url(string $path): string {
        return rtrim($this->baseurl, '/') . $path;
    }
}
