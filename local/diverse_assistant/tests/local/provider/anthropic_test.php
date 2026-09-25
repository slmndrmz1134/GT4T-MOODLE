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
 * Tests for the Claude client: the official SDK runs against mocked HTTP responses through Moodle's HTTP client.
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(anthropic::class)]
final class anthropic_test extends \advanced_testcase {
    /**
     * A streamed Messages API answer.
     *
     * @param string $stopreason Stop reason of the answer.
     * @param array $pieces Text pieces.
     * @return string
     */
    private function streamed_answer(string $stopreason = 'end_turn', array $pieces = ['Mer', 'haba']): string {
        $events = [
            ['message_start', ['type' => 'message_start', 'message' => ['id' => 'msg_1', 'type' => 'message',
                'role' => 'assistant', 'model' => 'claude-opus-5', 'content' => [], 'stop_reason' => null,
                'stop_sequence' => null, 'usage' => ['input_tokens' => 120, 'output_tokens' => 1,
                    'cache_creation_input_tokens' => 0, 'cache_read_input_tokens' => 80]]]],
            ['content_block_start', ['type' => 'content_block_start', 'index' => 0,
                'content_block' => ['type' => 'text', 'text' => '']]],
        ];
        foreach ($pieces as $piece) {
            $events[] = ['content_block_delta', ['type' => 'content_block_delta', 'index' => 0,
                'delta' => ['type' => 'text_delta', 'text' => $piece]]];
        }
        $delta = ['stop_reason' => $stopreason, 'stop_sequence' => null];
        if ($stopreason === 'refusal') {
            $delta['stop_details'] = ['type' => 'refusal', 'category' => 'cyber', 'explanation' => null];
        }
        $events[] = ['content_block_stop', ['type' => 'content_block_stop', 'index' => 0]];
        $events[] = ['message_delta', ['type' => 'message_delta', 'delta' => $delta, 'usage' => ['output_tokens' => 7]]];
        $events[] = ['message_stop', ['type' => 'message_stop']];

        $body = '';
        foreach ($events as [$name, $data]) {
            $body .= "event: {$name}\ndata: " . json_encode($data) . "\n\n";
        }
        return $body;
    }

    /**
     * A client whose HTTP requests are answered by a mock.
     *
     * @param MockHandler $mock The mock.
     * @param string $model Model id.
     * @return anthropic
     */
    private function client(MockHandler $mock, string $model = 'claude-opus-5'): anthropic {
        return new anthropic('sk-ant-test', $model, 'low', ['mock' => $mock]);
    }

    /**
     * The answer streams piece by piece; the request carries the cached instructions, effort and fallbacks.
     */
    public function test_chat_streams_answer(): void {
        $mock = new MockHandler([new Response(200, ['Content-Type' => 'text/event-stream'], $this->streamed_answer())]);
        $pieces = [];
        $result = $this->client($mock)->chat([
            ['role' => 'system', 'content' => 'You are the course assistant. COURSEMATERIALS'],
            ['role' => 'user', 'content' => 'Earlier question'],
            ['role' => 'assistant', 'content' => 'Earlier answer'],
            ['role' => 'user', 'content' => 'What is a contract?'],
        ], function (string $text) use (&$pieces): void {
            $pieces[] = $text;
        });

        $this->assertSame('Merhaba', $result->text);
        $this->assertSame(['Mer', 'haba'], $pieces);
        $this->assertSame(200, $result->prompttokens);
        $this->assertSame(7, $result->completiontokens);
        $this->assertSame('end_turn', $result->finishreason);

        $request = $mock->getLastRequest();
        $this->assertSame('https://api.anthropic.com/v1/messages?beta=true', (string)$request->getUri());
        $this->assertSame('sk-ant-test', $request->getHeaderLine('x-api-key'));
        $this->assertStringContainsString('server-side-fallback-2026-07-01', $request->getHeaderLine('anthropic-beta'));
        $body = json_decode((string)$request->getBody(), true);
        $this->assertSame('claude-opus-5', $body['model']);
        $this->assertTrue($body['stream']);
        $this->assertSame('default', $body['fallbacks']);
        $this->assertSame('low', $body['output_config']['effort']);
        $this->assertStringContainsString('COURSEMATERIALS', $body['system'][0]['text']);
        $this->assertSame('ephemeral', $body['system'][0]['cache_control']['type']);
        $this->assertSame(['user', 'assistant', 'user'], array_column($body['messages'], 'role'));
        $this->assertArrayNotHasKey('metadata', $body);
    }

    /**
     * Models without fallbacks or effort support do not get those parameters.
     */
    public function test_older_model_request(): void {
        $mock = new MockHandler([new Response(200, ['Content-Type' => 'text/event-stream'], $this->streamed_answer())]);
        $this->client($mock, 'claude-haiku-4-5')->chat([['role' => 'user', 'content' => 'Hi']]);

        $request = $mock->getLastRequest();
        $body = json_decode((string)$request->getBody(), true);
        $this->assertArrayNotHasKey('fallbacks', $body);
        $this->assertArrayNotHasKey('output_config', $body);
        $this->assertStringNotContainsString('server-side-fallback', $request->getHeaderLine('anthropic-beta'));
    }

    /**
     * When every model declines, the partial answer is discarded and the student gets a message.
     */
    public function test_refusal(): void {
        $mock = new MockHandler([new Response(200, ['Content-Type' => 'text/event-stream'],
            $this->streamed_answer('refusal', ['Partial']))]);
        try {
            $this->client($mock)->chat([['role' => 'user', 'content' => 'Hi']]);
            $this->fail('An exception was expected.');
        } catch (provider_exception $e) {
            $this->assertSame('errorrefusal', $e->errorcode);
            $this->assertSame('cyber', $e->debuginfo);
        }
    }

    /**
     * A rejected key becomes the "key rejected" message.
     */
    public function test_invalid_key(): void {
        $mock = new MockHandler([new Response(401, ['Content-Type' => 'application/json'],
            '{"type":"error","error":{"type":"authentication_error","message":"invalid x-api-key"}}')]);
        try {
            $this->client($mock)->list_models();
            $this->fail('An exception was expected.');
        } catch (provider_exception $e) {
            $this->assertSame('errorauth', $e->errorcode);
        }
    }

    /**
     * The model list is read and sorted.
     */
    public function test_list_models(): void {
        $model = fn(string $id) => ['type' => 'model', 'id' => $id, 'display_name' => $id,
            'created_at' => '2026-01-01T00:00:00Z'];
        $mock = new MockHandler([new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'data' => [$model('claude-sonnet-5'), $model('claude-opus-5')],
            'has_more' => false, 'first_id' => 'claude-sonnet-5', 'last_id' => 'claude-opus-5',
        ]))]);
        $this->assertSame(['claude-opus-5', 'claude-sonnet-5'], $this->client($mock)->list_models());
    }

    /**
     * The plugin's messages become Claude's system prompt and alternating turns starting with the student.
     */
    public function test_split_messages(): void {
        [$system, $turns] = anthropic::split_messages([
            ['role' => 'system', 'content' => 'Instructions'],
            ['role' => 'assistant', 'content' => 'Orphan answer'],
            ['role' => 'user', 'content' => 'One'],
            ['role' => 'user', 'content' => 'Two'],
            ['role' => 'assistant', 'content' => 'Answer'],
        ]);
        $this->assertSame('Instructions', $system);
        $this->assertSame([['role' => 'user', 'content' => "One\n\nTwo"], ['role' => 'assistant', 'content' => 'Answer']],
            $turns);
    }

    /**
     * Which models get effort and fallbacks.
     */
    public function test_model_capabilities(): void {
        foreach (['claude-opus-5', 'claude-opus-4-8', 'claude-sonnet-5', 'claude-sonnet-4-6', 'claude-fable-5-1'] as $m) {
            $this->assertTrue(anthropic::supports_effort($m), $m);
        }
        foreach (['claude-haiku-4-5', 'claude-sonnet-4-5'] as $m) {
            $this->assertFalse(anthropic::supports_effort($m), $m);
        }
        foreach (['claude-opus-5', 'claude-opus-5-5', 'claude-fable-5-1'] as $m) {
            $this->assertTrue(anthropic::supports_fallbacks($m), $m);
        }
        foreach (['claude-opus-4-8', 'claude-sonnet-5', 'claude-haiku-4-5'] as $m) {
            $this->assertFalse(anthropic::supports_fallbacks($m), $m);
        }
    }
}
