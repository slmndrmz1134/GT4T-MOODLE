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
 * Tests for the OpenAI client and the streamed answer reader. HTTP responses are mocked.
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\local_diverse_assistant\local\provider\openai::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(\local_diverse_assistant\local\provider\openai_compatible::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(\local_diverse_assistant\local\provider\openai_stream::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(\local_diverse_assistant\local\provider\factory::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(\local_diverse_assistant\local\provider\gemini::class)]
final class openai_test extends \advanced_testcase {
    /**
     * A streamed answer as OpenAI sends it.
     *
     * @return string
     */
    private function streamed_answer(): string {
        return implode("\n\n", [
            'data: {"choices":[{"delta":{"role":"assistant","content":""}}]}',
            'data: {"choices":[{"delta":{"content":"Mer"}}]}',
            'data: {"choices":[{"delta":{"content":"haba"},"finish_reason":"stop"}]}',
            'data: {"choices":[],"usage":{"prompt_tokens":12,"completion_tokens":3}}',
            'data: [DONE]',
        ]) . "\n\n";
    }

    /**
     * The model list is read and sorted.
     */
    public function test_list_models(): void {
        global $CFG;
        require_once($CFG->libdir . '/filelib.php');
        \curl::mock_response('{"object":"list","data":[{"id":"gpt-6-luna"},{"id":"gpt-4o"}]}');

        $client = new openai('sk-test', 'gpt-6-luna');
        $this->assertSame(['gpt-4o', 'gpt-6-luna'], $client->list_models());
    }

    /**
     * A streamed answer is collected; pieces are passed on as they arrive; token counts are read.
     */
    public function test_chat_streams_answer(): void {
        global $CFG;
        require_once($CFG->libdir . '/filelib.php');
        \curl::mock_response($this->streamed_answer());

        $pieces = [];
        $client = new openai('sk-test', 'gpt-6-luna', false, 'low');
        $result = $client->chat([['role' => 'user', 'content' => 'Hi']], function (string $text) use (&$pieces): void {
            $pieces[] = $text;
        });

        $this->assertSame('Merhaba', $result->text);
        $this->assertSame(['Mer', 'haba'], $pieces);
        $this->assertSame(12, $result->prompttokens);
        $this->assertSame(3, $result->completiontokens);
        $this->assertSame('stop', $result->finishreason);
    }

    /**
     * Chunk boundaries do not matter.
     */
    public function test_stream_in_small_chunks(): void {
        $stream = new openai_stream();
        foreach (str_split($this->streamed_answer(), 5) as $chunk) {
            $this->assertTrue($stream->write($chunk, 200));
        }
        $this->assertSame('Merhaba', $stream->finish()->text);
    }

    /**
     * A service that ignores "stream" and answers with one document is understood.
     */
    public function test_single_document_answer(): void {
        $stream = new openai_stream();
        $stream->write('{"choices":[{"message":{"content":"Tek parça"},"finish_reason":"stop"}],'
            . '"usage":{"prompt_tokens":5,"completion_tokens":2}}', 200);
        $result = $stream->finish();
        $this->assertSame('Tek parça', $result->text);
        $this->assertSame(5, $result->prompttokens);
    }

    /**
     * An error inside the stream stops the transfer and becomes an exception with the service's message.
     */
    public function test_error_inside_stream(): void {
        $stream = new openai_stream();
        $this->assertFalse($stream->write("data: {\"error\":{\"message\":\"Quota exceeded\"}}\n\n", 200));
        $this->assertTrue($stream->has_error());
        try {
            $stream->finish();
            $this->fail('An exception was expected.');
        } catch (provider_exception $e) {
            $this->assertSame('errorservice', $e->errorcode);
            $this->assertStringContainsString('Quota exceeded', $e->debuginfo);
        }
    }

    /**
     * An answer without text is an error.
     */
    public function test_empty_answer(): void {
        $stream = new openai_stream();
        $stream->write("data: [DONE]\n\n", 200);
        $this->expectException(provider_exception::class);
        $stream->finish();
    }

    /**
     * The EU setting switches to the EU data residency address.
     */
    public function test_eu_address(): void {
        $this->assertTrue((new openai('sk-test', 'gpt-6-luna', true))->is_eu());
        $this->assertFalse((new openai('sk-test', 'gpt-6-luna'))->is_eu());
    }

    /**
     * The client is built from the settings; the API key is stored encrypted.
     */
    public function test_factory_from_settings(): void {
        $this->resetAfterTest();

        try {
            factory::create();
            $this->fail('An exception was expected without an API key.');
        } catch (provider_exception $e) {
            $this->assertSame('errornotconfigured', $e->errorcode);
        }

        set_config('apikey_openai', \core\encryption::encrypt('sk-test'), 'local_diverse_assistant');
        set_config('provider', 'openai', 'local_diverse_assistant');
        set_config('openairegion', 'eu', 'local_diverse_assistant');
        $this->assertSame('sk-test', factory::get_api_key());
        $client = factory::create();
        $this->assertInstanceOf(openai::class, $client);
        $this->assertTrue($client->is_eu());

        // Every service has its own key; switching keeps them.
        set_config('provider', 'anthropic', 'local_diverse_assistant');
        $this->assertSame('', factory::get_api_key());
        set_config('apikey_anthropic', \core\encryption::encrypt('sk-ant-test'), 'local_diverse_assistant');
        $this->assertInstanceOf(anthropic::class, factory::create());
        set_config('provider', 'gemini', 'local_diverse_assistant');
        set_config('apikey_gemini', \core\encryption::encrypt('AIzaTest'), 'local_diverse_assistant');
        $this->assertInstanceOf(gemini::class, factory::create());
        $this->assertSame('sk-test', factory::get_api_key('openai'));

        set_config('provider', 'openaicompatible', 'local_diverse_assistant');
        set_config('apikey_openaicompatible', \core\encryption::encrypt('sk-other'), 'local_diverse_assistant');
        try {
            factory::create();
            $this->fail('An exception was expected without an address.');
        } catch (provider_exception $e) {
            $this->assertSame('errornoendpoint', $e->errorcode);
        }
        set_config('endpoint', 'https://api.example.eu/v1', 'local_diverse_assistant');
        $this->assertInstanceOf(openai_compatible::class, factory::create());
    }

    /**
     * Keys of other companies are recognised, and point to their service.
     */
    public function test_detect_key_vendor(): void {
        $this->assertSame('anthropic', factory::detect_key_vendor('sk-ant-api03-abc'));
        $this->assertSame('openai', factory::detect_key_vendor('sk-proj-abc'));
        $this->assertSame('google', factory::detect_key_vendor('AIzaSyAbc'));
        $this->assertSame('openrouter', factory::detect_key_vendor('sk-or-v1-abc'));
        $this->assertSame('', factory::detect_key_vendor('abc'));

        $this->assertSame('anthropic', factory::provider_for_key('sk-ant-api03-abc', 'openai'));
        $this->assertSame('gemini', factory::provider_for_key('AIzaSyAbc', 'anthropic'));
        $this->assertSame('openai', factory::provider_for_key('sk-proj-abc', 'gemini'));
        // A key fitting the field, and sk- keys of compatible services, stay where they were typed.
        $this->assertSame('', factory::provider_for_key('sk-proj-abc', 'openai'));
        $this->assertSame('', factory::provider_for_key('sk-proj-abc', 'openaicompatible'));
        $this->assertSame('', factory::provider_for_key('mistral-key', 'openaicompatible'));
    }

    /**
     * Only models that accept it get a reasoning effort, and "none" is not sent to Gemini.
     */
    public function test_gemini_request(): void {
        $client = new class('AIzaTest', 'gemini-3.8-flash', 'low') extends gemini {
            /**
             * Expose the request body.
             *
             * @param array $messages Messages.
             * @return array
             */
            public function body(array $messages): array {
                return $this->build_body($messages);
            }

            /**
             * Expose the error classification.
             *
             * @param int $status HTTP status.
             * @param string $detail Error message.
             * @return string
             */
            public function classify(int $status, string $detail): string {
                return $this->classify_error($status, $detail);
            }
        };
        $body = $client->body([['role' => 'user', 'content' => 'Hi']]);
        $this->assertSame('gemini-3.8-flash', $body['model']);
        $this->assertSame('low', $body['reasoning_effort']);
        $this->assertTrue($body['stream_options']['include_usage']);
        $this->assertArrayNotHasKey('user', $body);
        $this->assertFalse($client->is_eu());

        // Gemini answers an invalid key with 400.
        $this->assertSame('errorauth', $client->classify(400, 'API key not valid. Please pass a valid API key.'));
        $this->assertSame('errorauth', $client->classify(400, 'Please pass a valid API key'));
        $this->assertSame('errorservice', $client->classify(400, 'Invalid argument'));
        $this->assertSame('errorratelimit', $client->classify(429, 'Quota exceeded'));
        $this->assertSame('errorbusy', $client->classify(503, 'This model is currently experiencing high demand.'));
        $this->assertSame('errorbusy', $client->classify(500, 'Internal error'));
    }

    /**
     * An overloaded service is asked again, but never after text has reached the student.
     */
    public function test_retry_when_busy(): void {
        $client = new class('https://example.com/v1', 'key', 'model') extends openai_compatible {
            /** @var int Attempts made. */
            public int $attempts = 0;
            /** @var int Attempts that fail before one succeeds. */
            public int $failures = 2;
            /** @var bool Whether a failing attempt streams text first. */
            public bool $partial = false;

            #[\Override]
            protected function send_chat(array $messages, callable $ondelta): chat_result {
                $this->attempts++;
                if ($this->attempts <= $this->failures) {
                    if ($this->partial) {
                        $ondelta('Half an answer');
                    }
                    throw new provider_exception('errorbusy', 'HTTP 503 high demand');
                }
                $ondelta('Answer');
                return new chat_result('Answer');
            }
        };
        $this->assertSame('Answer', $client->chat([['role' => 'user', 'content' => 'Hi']])->text);
        $this->assertSame(3, $client->attempts);

        $client->attempts = 0;
        $client->failures = 5;
        try {
            $client->chat([['role' => 'user', 'content' => 'Hi']]);
            $this->fail('An exception was expected.');
        } catch (provider_exception $e) {
            $this->assertSame('errorbusy', $e->errorcode);
            $this->assertSame(3, $client->attempts);
        }

        $client->attempts = 0;
        $client->partial = true;
        try {
            $client->chat([['role' => 'user', 'content' => 'Hi']]);
            $this->fail('An exception was expected.');
        } catch (provider_exception $e) {
            $this->assertSame(1, $client->attempts);
        }
    }

    /**
     * An overload reported inside the stream is recognised as such.
     */
    public function test_busy_error_inside_stream(): void {
        $stream = new openai_stream();
        $stream->write("data: {\"error\":{\"code\":503,\"message\":\"high demand\",\"status\":\"UNAVAILABLE\"}}\n\n", 200);
        try {
            $stream->finish();
            $this->fail('An exception was expected.');
        } catch (provider_exception $e) {
            $this->assertSame('errorbusy', $e->errorcode);
        }
    }

    /**
     * Gemini model ids lose the "models/" prefix older versions of the endpoint add.
     */
    public function test_gemini_list_models(): void {
        global $CFG;
        require_once($CFG->libdir . '/filelib.php');
        \curl::mock_response('{"object":"list","data":[{"id":"models/gemini-3.8-flash"},{"id":"gemini-3.5-flash-lite"}]}');

        $client = new gemini('AIzaTest', 'gemini-3.8-flash');
        $this->assertSame(['gemini-3.5-flash-lite', 'gemini-3.8-flash'], $client->list_models());
    }

    /**
     * Error bodies wrapped in a list (as Gemini sends them) are read.
     */
    public function test_error_detail_in_list(): void {
        $client = new class('https://example.com/v1', 'key', 'model') extends openai_compatible {
            /**
             * Expose the error detail reader.
             *
             * @param string $body Error body.
             * @return string
             */
            public function detail(string $body): string {
                return $this->error_detail($body);
            }
        };
        $this->assertSame('API key not valid.',
            $client->detail('[{"error":{"code":400,"message":"API key not valid.","status":"INVALID_ARGUMENT"}}]'));
        $this->assertSame('Bad key', $client->detail('{"error":{"message":"Bad key"}}'));
    }
}
