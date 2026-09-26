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
 * ignores "stream" and sends one JSON document is also understood. Tool calls arrive in pieces too and are joined.
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

    /** @var callable|null Called with the tool name when a tool call starts. */
    private $ontoolstart;

    /** @var array Tool calls so far: list of ['id' => string, 'name' => string, 'arguments' => string JSON]. */
    private array $toolcalls = [];

    /** @var array Position in $toolcalls of each tool call index the service sent. */
    private array $toolcallindexes = [];

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
     * @param callable|null $ontoolstart Called with the tool name when a tool call starts.
     */
    public function __construct(?callable $ondelta = null, ?callable $ontoolstart = null) {
        $this->parser = new sse_parser();
        $this->ondelta = $ondelta;
        $this->ontoolstart = $ontoolstart;
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
        if ($this->status < 400) {
            $this->read_error_body();
        }
        if ($this->streamerror !== '') {
            throw new provider_exception($this->streamerrorcode, $this->streamerror);
        }
        if ($this->text === '' && !$this->toolcalls) {
            $this->read_single_document();
        }
        $toolcalls = [];
        foreach ($this->toolcalls as $call) {
            if ($call['name'] === '') {
                continue;
            }
            $arguments = trim($call['arguments']) === '' ? [] : json_decode($call['arguments'], true);
            $toolcalls[] = ['name' => $call['name'], 'arguments' => is_array($arguments) ? $arguments : null];
        }
        if (trim($this->text) === '' && !$toolcalls) {
            throw new provider_exception('errorempty', $this->finishreason);
        }
        return new chat_result($this->text, $this->prompttokens, $this->completiontokens, $this->finishreason, $toolcalls);
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
            $this->record_error($event['error'], $data);
            return;
        }
        $choice = $event['choices'][0] ?? null;
        if (is_array($choice)) {
            $delta = $choice['delta']['content'] ?? null;
            if (is_string($delta) && $delta !== '') {
                $this->add_text($delta);
            }
            foreach ($choice['delta']['tool_calls'] ?? [] as $piece) {
                if (is_array($piece)) {
                    $this->add_tool_call_piece($piece);
                }
            }
            if (!empty($choice['finish_reason'])) {
                $this->finishreason = (string)$choice['finish_reason'];
            }
        }
        $this->read_usage($event);
    }

    /**
     * Remember an error the service reported inside the stream.
     *
     * @param mixed $error The "error" member of an event or error body.
     * @param string $raw The event or body, used when the error has no message.
     */
    private function record_error(mixed $error, string $raw): void {
        $error = is_array($error) ? $error : ['message' => (string)$error];
        $this->streamerror = trim(preg_replace('/\s+/', ' ', (string)($error['message'] ?? $raw)));
        $code = (int)($error['code'] ?? 0);
        $status = strtoupper((string)($error['status'] ?? $error['type'] ?? ''));
        $this->streamerrorcode = match (true) {
            // Overloaded or temporarily failing: worth retrying or trying another model.
            in_array($code, [500, 502, 503, 504, 529], true)
                || in_array($status, ['UNAVAILABLE', 'INTERNAL', 'OVERLOADED_ERROR', 'SERVER_ERROR'], true) => 'errorbusy',
            $code === 429 || $status === 'RESOURCE_EXHAUSTED' => 'errorratelimit',
            default => 'errorservice',
        };
    }

    /**
     * Find a plain JSON error body written into the stream (Gemini does this when it fails in the middle of an
     * answer, e.g. [{"error": {"code": 503, ...}}]). Without this, the cut-off answer would look complete.
     */
    private function read_error_body(): void {
        $other = trim($this->parser->get_other_text());
        if ($other === '' || $this->streamerror !== '') {
            return;
        }
        $decoded = json_decode($other, true);
        if (is_array($decoded) && array_is_list($decoded) && isset($decoded[0]) && is_array($decoded[0])) {
            $decoded = $decoded[0];
        }
        if (is_array($decoded) && isset($decoded['error'])) {
            $this->record_error($decoded['error'], $other);
        }
    }

    /**
     * Understand a service that answered with one JSON document instead of a stream.
     */
    private function read_single_document(): void {
        $document = json_decode($this->body, true);
        if (!is_array($document)) {
            return;
        }
        $message = $document['choices'][0]['message'] ?? null;
        if (!is_array($message)) {
            return;
        }
        $this->finishreason = (string)($document['choices'][0]['finish_reason'] ?? '');
        if (is_string($message['content'] ?? null) && $message['content'] !== '') {
            $this->add_text($message['content']);
        }
        foreach ($message['tool_calls'] ?? [] as $call) {
            if (is_array($call)) {
                $this->add_tool_call_piece($call);
            }
        }
        $this->read_usage($document);
    }

    /**
     * Add a piece of a tool call: the first piece has the id and name, the following ones more of the arguments.
     *
     * @param array $piece One entry of "tool_calls".
     */
    private function add_tool_call_piece(array $piece): void {
        $index = $piece['index'] ?? null;
        $id = is_string($piece['id'] ?? null) ? $piece['id'] : '';
        $name = is_string($piece['function']['name'] ?? null) ? $piece['function']['name'] : '';
        $arguments = $piece['function']['arguments'] ?? '';
        // Some services send the arguments as an object instead of a JSON string.
        $arguments = is_string($arguments) ? $arguments : json_encode($arguments);

        $known = is_int($index) && isset($this->toolcallindexes[$index]) ? $this->toolcallindexes[$index] : null;
        if ($known !== null && $id !== '' && $this->toolcalls[$known]['id'] !== '' && $this->toolcalls[$known]['id'] !== $id) {
            // A new call that reuses an index (some services number every complete call 0).
            $known = null;
        }
        if ($known !== null) {
            $key = $known;
        } else if (!is_int($index) && $id === '' && $name === '' && $this->toolcalls) {
            // A continuation without an index belongs to the last call.
            $key = array_key_last($this->toolcalls);
        } else {
            $key = count($this->toolcalls);
            $this->toolcalls[$key] = ['id' => $id, 'name' => '', 'arguments' => ''];
            if (is_int($index)) {
                $this->toolcallindexes[$index] = $key;
            }
        }
        if ($name !== '' && $this->toolcalls[$key]['name'] === '') {
            $this->toolcalls[$key]['name'] = $name;
            if ($this->ontoolstart) {
                ($this->ontoolstart)($name);
            }
        }
        $this->toolcalls[$key]['arguments'] .= $arguments;
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
