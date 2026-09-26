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

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;

/**
 * Tests for tool calls (teacher mode) in the services' answers, and for errors written into a stream.
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(openai_stream::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(sse_parser::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(openai::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(openai_compatible::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(anthropic::class)]
final class tool_calls_test extends \advanced_testcase {
    /**
     * A tool definition.
     *
     * @return chat_options
     */
    private function options(): chat_options {
        return new chat_options([[
            'name' => 'propose_new_page',
            'description' => 'Propose a page.',
            'parameters' => ['type' => 'object', 'properties' => ['name' => ['type' => 'string']], 'required' => ['name']],
        ]], 16000);
    }

    /**
     * OpenAI sends a tool call in pieces with an index; the pieces are joined and the text is kept.
     */
    public function test_openai_streamed_tool_call(): void {
        $started = [];
        $stream = new openai_stream(null, function (string $name) use (&$started): void {
            $started[] = $name;
        });
        $events = [
            ['choices' => [['delta' => ['content' => 'I propose a page.']]]],
            ['choices' => [['delta' => ['tool_calls' => [['index' => 0, 'id' => 'call_1', 'type' => 'function',
                'function' => ['name' => 'propose_new_page', 'arguments' => '{"name":"Con']]]]]]],
            ['choices' => [['delta' => ['tool_calls' => [['index' => 0, 'function' => ['arguments' => 'tracts"}']]]]]]],
            ['choices' => [['delta' => ['tool_calls' => [['index' => 1, 'id' => 'call_2', 'type' => 'function',
                'function' => ['name' => 'propose_new_label', 'arguments' => '{"content":"<p>Hi</p>"}']]]]]]],
            ['choices' => [['delta' => [], 'finish_reason' => 'tool_calls']]],
        ];
        $body = '';
        foreach ($events as $event) {
            $body .= 'data: ' . json_encode($event) . "\n\n";
        }
        foreach (str_split($body . "data: [DONE]\n\n", 7) as $chunk) {
            $stream->write($chunk, 200);
        }
        $result = $stream->finish();

        $this->assertSame('I propose a page.', $result->text);
        $this->assertSame('tool_calls', $result->finishreason);
        $this->assertSame([
            ['name' => 'propose_new_page', 'arguments' => ['name' => 'Contracts']],
            ['name' => 'propose_new_label', 'arguments' => ['content' => '<p>Hi</p>']],
        ], $result->toolcalls);
        $this->assertSame(['propose_new_page', 'propose_new_label'], $started);
    }

    /**
     * Gemini sends each complete call without an index; an answer with only a tool call is not "empty".
     */
    public function test_gemini_tool_call_without_index(): void {
        $stream = new openai_stream();
        $stream->write('data: ' . json_encode(['choices' => [['delta' => ['role' => 'assistant', 'tool_calls' => [[
            'extra_content' => ['google' => ['thought_signature' => 'abc']],
            'function' => ['arguments' => '{"name":"A"}', 'name' => 'propose_new_page'],
            'id' => 'call_9', 'type' => 'function',
        ]]]]]]) . "\n\n", 200);
        $stream->write('data: ' . json_encode(['choices' => [['delta' => ['role' => 'assistant'],
            'finish_reason' => 'stop']]]) . "\n\ndata: [DONE]\n\n", 200);
        $result = $stream->finish();
        $this->assertSame('', $result->text);
        $this->assertSame([['name' => 'propose_new_page', 'arguments' => ['name' => 'A']]], $result->toolcalls);
    }

    /**
     * Invalid JSON arguments are reported as null, not dropped.
     */
    public function test_invalid_arguments(): void {
        $stream = new openai_stream();
        $stream->write('{"choices":[{"message":{"content":null,"tool_calls":[{"id":"c","type":"function",'
            . '"function":{"name":"propose_new_page","arguments":"{\"name\": \"cut off"}}]},"finish_reason":"length"}]}', 200);
        $result = $stream->finish();
        $this->assertSame([['name' => 'propose_new_page', 'arguments' => null]], $result->toolcalls);
        $this->assertSame('length', $result->finishreason);
    }

    /**
     * Gemini writes a plain JSON error body into the stream when it fails in the middle of an answer: the cut-off
     * answer must not look complete.
     */
    public function test_error_body_in_the_middle_of_a_stream(): void {
        $stream = new openai_stream();
        $stream->write('data: ' . json_encode(['choices' => [['delta' => ['content' => 'Half an ans']]]]) . "\n\n", 200);
        $stream->write("[{\n  \"error\": {\n    \"code\": 503,\n    \"message\": \"This model is currently experiencing high "
            . "demand.\",\n    \"status\": \"UNAVAILABLE\"\n  }\n}\n]", 200);
        try {
            $stream->finish();
            $this->fail('An exception was expected.');
        } catch (provider_exception $e) {
            $this->assertSame('errorbusy', $e->errorcode);
            $this->assertStringContainsString('high demand', $e->debuginfo);
        }
    }

    /**
     * Tools go into the request; GPT-6 models get them without thinking, which their Chat Completions API requires.
     */
    public function test_openai_request_with_tools(): void {
        $client = new class('sk-test', 'gpt-6-luna', false, 'low') extends openai {
            /**
             * Expose the request body.
             *
             * @param chat_options $options Options.
             * @return array
             */
            public function body(chat_options $options): array {
                return $this->build_body([['role' => 'user', 'content' => 'Hi']], $options);
            }
        };
        $body = $client->body($this->options());
        $this->assertSame('function', $body['tools'][0]['type']);
        $this->assertSame('propose_new_page', $body['tools'][0]['function']['name']);
        $this->assertSame('auto', $body['tool_choice']);
        $this->assertSame(16000, $body['max_completion_tokens']);
        $this->assertSame('none', $body['reasoning_effort']);

        // Without tools, the chosen effort and the default answer length.
        $body = $client->body(new chat_options());
        $this->assertArrayNotHasKey('tools', $body);
        $this->assertSame('low', $body['reasoning_effort']);
        $this->assertSame(4000, $body['max_completion_tokens']);
    }

    /**
     * A model that rejects tools together with a reasoning effort is asked again without thinking.
     */
    public function test_openai_retry_without_effort(): void {
        $client = new class('sk-test', 'gpt-5.5', false, 'medium') extends openai {
            /** @var \ArrayObject|null Efforts of the requests sent; shared with clones of the client. */
            public ?\ArrayObject $efforts = null;

            #[\Override]
            protected function send_chat(array $messages, callable $ondelta, chat_options $options): chat_result {
                $this->efforts[] = $this->build_body($messages, $options)['reasoning_effort'] ?? '';
                if (count($this->efforts) === 1) {
                    throw new provider_exception('errorservice', 'HTTP 400 Function tools with reasoning_effort are not '
                        . 'supported for gpt-5.5 in /v1/chat/completions.');
                }
                return new chat_result('', 0, 0, 'tool_calls', [['name' => 'propose_new_page', 'arguments' => []]]);
            }
        };
        $client->efforts = new \ArrayObject();
        $result = $client->chat([['role' => 'user', 'content' => 'Hi']], null, $this->options());
        $this->assertSame(['medium', 'none'], $client->efforts->getArrayCopy());
        $this->assertCount(1, $result->toolcalls);

        // Other errors, and requests without tools, are not repeated.
        $client->efforts->exchangeArray([]);
        try {
            $client->chat([['role' => 'user', 'content' => 'Hi']]);
            $this->fail('An exception was expected.');
        } catch (provider_exception $e) {
            $this->assertSame(['medium'], $client->efforts->getArrayCopy());
        }
    }

    /**
     * Claude: tools go into the request, tool_use blocks come back as tool calls.
     */
    public function test_anthropic_tool_use(): void {
        $events = [
            ['message_start', ['type' => 'message_start', 'message' => ['id' => 'msg_1', 'type' => 'message',
                'role' => 'assistant', 'model' => 'claude-opus-5', 'content' => [], 'stop_reason' => null,
                'stop_sequence' => null, 'usage' => ['input_tokens' => 100, 'output_tokens' => 1]]]],
            ['content_block_start', ['type' => 'content_block_start', 'index' => 0,
                'content_block' => ['type' => 'text', 'text' => '']]],
            ['content_block_delta', ['type' => 'content_block_delta', 'index' => 0,
                'delta' => ['type' => 'text_delta', 'text' => 'I propose a page.']]],
            ['content_block_stop', ['type' => 'content_block_stop', 'index' => 0]],
            ['content_block_start', ['type' => 'content_block_start', 'index' => 1,
                'content_block' => ['type' => 'tool_use', 'id' => 'toolu_1', 'name' => 'propose_new_page', 'input' => []]]],
            ['content_block_delta', ['type' => 'content_block_delta', 'index' => 1,
                'delta' => ['type' => 'input_json_delta', 'partial_json' => '{"name":"Con']]],
            ['content_block_delta', ['type' => 'content_block_delta', 'index' => 1,
                'delta' => ['type' => 'input_json_delta', 'partial_json' => 'tracts"}']]],
            ['content_block_stop', ['type' => 'content_block_stop', 'index' => 1]],
            ['message_delta', ['type' => 'message_delta', 'delta' => ['stop_reason' => 'tool_use', 'stop_sequence' => null],
                'usage' => ['output_tokens' => 30]]],
            ['message_stop', ['type' => 'message_stop']],
        ];
        $body = '';
        foreach ($events as [$name, $data]) {
            $body .= "event: {$name}\ndata: " . json_encode($data) . "\n\n";
        }
        $mock = new MockHandler([new Response(200, ['Content-Type' => 'text/event-stream'], $body)]);
        $started = [];
        $options = new chat_options($this->options()->tools, 0, function (string $name) use (&$started): void {
            $started[] = $name;
        });
        $result = (new anthropic('sk-ant-test', 'claude-opus-5', 'low', ['mock' => $mock]))
            ->chat([['role' => 'system', 'content' => 'Instructions'], ['role' => 'user', 'content' => 'Add a page']],
                null, $options);

        $this->assertSame('I propose a page.', $result->text);
        $this->assertSame('tool_use', $result->finishreason);
        $this->assertSame([['name' => 'propose_new_page', 'arguments' => ['name' => 'Contracts']]], $result->toolcalls);
        $this->assertSame(['propose_new_page'], $started);

        $request = json_decode((string)$mock->getLastRequest()->getBody(), true);
        $this->assertSame('propose_new_page', $request['tools'][0]['name']);
        $this->assertSame('object', $request['tools'][0]['input_schema']['type']);
        $this->assertSame(['name'], $request['tools'][0]['input_schema']['required']);
    }
}
