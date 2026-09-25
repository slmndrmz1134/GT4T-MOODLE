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
 * Collects a streamed Chat Completions answer (the OpenAI format, which many other services also use).
 *
 * The HTTP body arrives in chunks. Error bodies (status 400 and above) are kept for the error message. A service that
 * ignores "stream" and sends one JSON document is also understood.
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class openai_stream {
    /** Longest body kept for error messages and for services that do not stream, in bytes. */
    private const MAX_KEPT_BODY = 1048576;

    /** @var sse_parser Event splitter. */
    private sse_parser $parser;

    /** @var callable|null Called with each new piece of the answer. */
    private $ondelta;

    /** @var string The answer so far. */
    private string $text = '';

    /** @var int Input tokens reported by the service. */
    private int $prompttokens = 0;

    /** @var int Output tokens reported by the service. */
    private int $completiontokens = 0;

    /** @var string Why the answer ended. */
    private string $finishreason = '';

    /** @var int HTTP status of the response, 0 until known. */
    private int $status = 0;

    /** @var string The body as received, capped at MAX_KEPT_BODY. */
    private string $body = '';

    /** @var bool Whether any part of the body has arrived. */
    private bool $received = false;

    /** @var string Error the service reported inside the stream. */
    private string $streamerror = '';

    /** @var string Message for that error: errorbusy, errorratelimit or errorservice. */
    private string $streamerrorcode = 'errorservice';

    /**
     * Constructor.
     *
     * @param callable|null $ondelta Called with each new piece of the answer.
     */
    public function __construct(?callable $ondelta = null) {
        $this->parser = new sse_parser();
        $this->ondelta = $ondelta;
    }

    /**
     * Add a chunk of the HTTP body.
     *
     * @param string $chunk The chunk.
     * @param int $status HTTP status of the response (0 if unknown).
     * @return bool False when the service reported an error and the transfer should stop.
     */
    public function write(string $chunk, int $status): bool {
        $this->received = true;
        if ($status > 0) {
            $this->status = $status;
        }
        if (strlen($this->body) < self::MAX_KEPT_BODY) {
            $this->body .= $chunk;
        }
        if ($this->status >= 400) {
            return true;
        }
        foreach ($this->parser->feed($chunk) as $data) {
            $this->handle_event($data);
        }
        return $this->streamerror === '';
    }

    /**
     * Whether any part of the body has arrived.
     *
     * @return bool
     */
    public function received(): bool {
        return $this->received;
    }

    /**
     * Whether the service reported an error inside the stream.
     *
     * @return bool
     */
    public function has_error(): bool {
        return $this->streamerror !== '';
    }

    /**
     * HTTP status of the response, 0 until known.
     *
     * @return int
     */
    public function get_status(): int {
        return $this->status;
    }

    /**
     * The body as received (capped), for error messages.
     *
     * @return string
     */
    public function get_body(): string {
        return $this->body;
    }

    /**
     * The transfer is over: return the answer.
     *
     * @return chat_result
     * @throws provider_exception If the service reported an error or sent no answer.
     */
    public function finish(): chat_result {
        foreach ($this->parser->finish() as $data) {
            $this->handle_event($data);
        }
        if ($this->streamerror !== '') {
            throw new provider_exception($this->streamerrorcode, $this->streamerror);
        }
        if ($this->text === '') {
            $this->read_single_document();
        }
        if (trim($this->text) === '') {
            throw new provider_exception('errorempty', $this->finishreason);
        }
        return new chat_result($this->text, $this->prompttokens, $this->completiontokens, $this->finishreason);
    }

    /**
     * Handle the data of one event.
     *
     * @param string $data JSON of one chunk, or "[DONE]".
     */
    private function handle_event(string $data): void {
        if ($data === '[DONE]' || $this->streamerror !== '') {
            return;
        }
        $event = json_decode($data, true);
        if (!is_array($event)) {
            return;
        }
        if (isset($event['error'])) {
            $error = is_array($event['error']) ? $event['error'] : [];
            $this->streamerror = (string)($error['message'] ?? $data);
            $code = (int)($error['code'] ?? 0);
            $status = strtoupper((string)($error['status'] ?? $error['type'] ?? ''));
            $this->streamerrorcode = match (true) {
                // Overloaded or temporarily failing: worth retrying or trying another model.
                in_array($code, [500, 502, 503, 504, 529], true)
                    || in_array($status, ['UNAVAILABLE', 'INTERNAL', 'OVERLOADED_ERROR', 'SERVER_ERROR'], true) => 'errorbusy',
                $code === 429 || $status === 'RESOURCE_EXHAUSTED' => 'errorratelimit',
                default => 'errorservice',
            };
            return;
        }
        $choice = $event['choices'][0] ?? null;
        if (is_array($choice)) {
            $delta = $choice['delta']['content'] ?? null;
            if (is_string($delta) && $delta !== '') {
                $this->add_text($delta);
            }
            if (!empty($choice['finish_reason'])) {
                $this->finishreason = (string)$choice['finish_reason'];
            }
        }
        $this->read_usage($event);
    }

    /**
     * Understand a service that answered with one JSON document instead of a stream.
     */
    private function read_single_document(): void {
        $document = json_decode($this->body, true);
        if (!is_array($document)) {
            return;
        }
        $content = $document['choices'][0]['message']['content'] ?? null;
        if (is_string($content) && $content !== '') {
            $this->finishreason = (string)($document['choices'][0]['finish_reason'] ?? '');
            $this->add_text($content);
        }
        $this->read_usage($document);
    }

    /**
     * Take the token counts from a chunk or document that has them.
     *
     * @param array $data Decoded chunk or document.
     */
    private function read_usage(array $data): void {
        if (!empty($data['usage']) && is_array($data['usage'])) {
            $this->prompttokens = (int)($data['usage']['prompt_tokens'] ?? 0);
            $this->completiontokens = (int)($data['usage']['completion_tokens'] ?? 0);
        }
    }

    /**
     * Add a piece of the answer.
     *
     * @param string $text The piece.
     */
    private function add_text(string $text): void {
        $this->text .= $text;
        if ($this->ondelta) {
            ($this->ondelta)($text);
        }
    }
}
