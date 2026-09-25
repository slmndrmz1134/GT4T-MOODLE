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

use Anthropic\Beta\Messages\BetaRawContentBlockDeltaEvent;
use Anthropic\Beta\Messages\BetaTextDelta;
use Anthropic\Client;
use Anthropic\Core\Exceptions\APIConnectionException;
use Anthropic\Core\Exceptions\APIStatusException;
use Anthropic\Core\Exceptions\AuthenticationException;
use Anthropic\Core\Exceptions\InternalServerException;
use Anthropic\Core\Exceptions\NotFoundException;
use Anthropic\Core\Exceptions\PermissionDeniedException;
use Anthropic\Core\Exceptions\RateLimitException;
use Anthropic\Lib\Streaming\MessageAccumulator;
use Anthropic\RequestOptions;
use GuzzleHttp\Psr7\HttpFactory;

/**
 * Claude, through the official Anthropic PHP SDK (bundled in vendor/, see thirdpartylibs.xml).
 *
 * The SDK sends its requests through Moodle's HTTP client, so the site's proxy and blocked-hosts settings apply.
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class anthropic extends provider {
    /** Recommended model. */
    public const DEFAULT_MODEL = 'claude-opus-5';

    /** API address. */
    private const BASEURL = 'https://api.anthropic.com';

    /** Longest answer, in tokens; with thinking on, this also covers the thinking. */
    private const MAX_OUTPUT_TOKENS = 16000;

    /** Beta that lets the API answer a declined request with Anthropic's recommended fallback model. */
    private const FALLBACK_BETA = 'server-side-fallback-2026-07-01';

    /**
     * Constructor.
     *
     * @param string $apikey API key (sk-ant-...).
     * @param string $model Model id, e.g. claude-opus-5.
     * @param string $effort low, medium or high; empty or none for the model's default.
     * @param array $httpoptions Extra options for Moodle's HTTP client (tests pass a mock handler here).
     */
    public function __construct(
        /** @var string API key. */
        private readonly string $apikey,
        /** @var string Model id. */
        private readonly string $model,
        /** @var string Effort level. */
        private readonly string $effort = '',
        /** @var array Extra options for Moodle's HTTP client. */
        private readonly array $httpoptions = [],
    ) {
    }

    #[\Override]
    public function get_name(): string {
        return get_string('provider_anthropic', 'local_diverse_assistant');
    }

    #[\Override]
    public function is_eu(): bool {
        // The Claude API can pin processing to the US or leave it global, but not to the EU.
        return false;
    }

    #[\Override]
    public function list_models(): array {
        try {
            $ids = [];
            foreach ($this->client()->models->list(limit: 1000)->pagingEachItem() as $model) {
                $ids[] = $model->id;
            }
        } catch (\Throwable $e) {
            throw $this->to_provider_exception($e);
        }
        sort($ids);
        return $ids;
    }

    #[\Override]
    public function chat(array $messages, ?callable $ondelta = null): chat_result {
        [$system, $turns] = self::split_messages($messages);
        $params = [
            'maxTokens' => self::MAX_OUTPUT_TOKENS,
            'messages' => $turns,
            'model' => $this->model,
            // The instructions and course materials repeat with every question of a course: cache them.
            'system' => [['type' => 'text', 'text' => $system, 'cacheControl' => ['type' => 'ephemeral']]],
        ];
        if (in_array($this->effort, ['low', 'medium', 'high'], true) && self::supports_effort($this->model)) {
            $params['outputConfig'] = ['effort' => $this->effort];
        }
        if (self::supports_fallbacks($this->model)) {
            // A declined request is retried on Anthropic's recommended model instead of failing.
            $params['fallbacks'] = 'default';
            $params['betas'] = [self::FALLBACK_BETA];
        }

        try {
            $stream = $this->client()->beta->messages->createStream(...$params);
            $accumulator = MessageAccumulator::forBetaMessages();
            foreach ($stream as $event) {
                $accumulator->accumulate($event);
                if ($ondelta && $event instanceof BetaRawContentBlockDeltaEvent && $event->delta instanceof BetaTextDelta) {
                    $ondelta($event->delta->text);
                }
            }
            $message = $accumulator->message();
        } catch (\Throwable $e) {
            throw $this->to_provider_exception($e);
        }

        if ($message->stopReason === 'refusal') {
            // Every model of the fallback chain declined; any partial text is discarded.
            throw new provider_exception('errorrefusal', (string)($message->stopDetails?->category ?? ''));
        }
        $text = '';
        foreach ($message->content as $block) {
            if ($block->type === 'text') {
                $text .= $block->text;
            }
        }
        if (trim($text) === '') {
            throw new provider_exception('errorempty', (string)$message->stopReason);
        }
        $usage = $message->usage;
        return new chat_result(
            $text,
            $usage->inputTokens + (int)$usage->cacheCreationInputTokens + (int)$usage->cacheReadInputTokens,
            $usage->outputTokens,
            $message->stopReason === 'max_tokens' ? 'length' : (string)$message->stopReason,
        );
    }

    /**
     * Whether a model takes the effort setting (Opus 4.5 and newer, Sonnet 4.6 and newer, Fable).
     *
     * @param string $model Model id.
     * @return bool
     */
    public static function supports_effort(string $model): bool {
        return (bool)preg_match('/^claude-(fable|mythos)-|^claude-opus-(4-[5-9]|[5-9])|^claude-sonnet-(4-[6-9]|[5-9])/', $model);
    }

    /**
     * Whether a model takes the server-side fallbacks parameter (Opus 5 and Fable 5 families).
     *
     * @param string $model Model id.
     * @return bool
     */
    public static function supports_fallbacks(string $model): bool {
        return (bool)preg_match('/^claude-(opus|fable|mythos)-5/', $model);
    }

    /**
     * Split the plugin's message list into Claude's system prompt and alternating turns.
     *
     * @param array $messages List of ['role' => 'system'|'user'|'assistant', 'content' => string].
     * @return array [string system prompt, array turns]
     */
    public static function split_messages(array $messages): array {
        $system = [];
        $turns = [];
        foreach ($messages as $message) {
            if ($message['role'] === 'system') {
                $system[] = $message['content'];
                continue;
            }
            if (!$turns && $message['role'] !== 'user') {
                // A conversation starts with the student.
                continue;
            }
            $last = count($turns) - 1;
            if ($last >= 0 && $turns[$last]['role'] === $message['role']) {
                $turns[$last]['content'] .= "\n\n" . $message['content'];
            } else {
                $turns[] = ['role' => $message['role'], 'content' => $message['content']];
            }
        }
        return [implode("\n\n", $system), $turns];
    }

    /**
     * The SDK client, using Moodle's HTTP client for plain and streamed requests.
     *
     * @return Client
     */
    private function client(): Client {
        require_once(__DIR__ . '/../../../vendor/autoload.php');

        $httpoptions = ['timeout' => static::TIMEOUT, 'connect_timeout' => static::CONNECT_TIMEOUT] + $this->httpoptions;
        $factory = new HttpFactory();
        return new Client(
            apiKey: $this->apikey,
            baseUrl: self::BASEURL,
            requestOptions: RequestOptions::with(
                timeout: (float)static::TIMEOUT,
                // The SDK retries overloaded (529), other 5xx and rate-limited requests with backoff.
                maxRetries: 2,
                transporter: new \core\http_client($httpoptions),
                streamingTransporter: new \core\http_client(['stream' => true] + $httpoptions),
                uriFactory: $factory,
                streamFactory: $factory,
                requestFactory: $factory,
            ),
        );
    }

    /**
     * Turn an SDK error into a message students can read, keeping the details for administrators.
     *
     * @param \Throwable $e The error.
     * @return provider_exception
     */
    private function to_provider_exception(\Throwable $e): provider_exception {
        if ($e instanceof provider_exception) {
            return $e;
        }
        $errorcode = match (true) {
            $e instanceof AuthenticationException => 'errorauth',
            $e instanceof PermissionDeniedException => 'errorforbidden',
            $e instanceof NotFoundException => 'errormodel',
            $e instanceof RateLimitException => 'errorratelimit',
            // 5xx, including 529 "overloaded", after the SDK's own retries.
            $e instanceof InternalServerException => 'errorbusy',
            $e instanceof APIConnectionException => 'errorconnection',
            $e instanceof APIStatusException => 'errorservice',
            default => 'errorservice',
        };
        // The SDK's message includes the response as indented JSON: keep it on one line for the settings page.
        $detail = trim(preg_replace('/\s+/', ' ', $e->getMessage()));
        return new provider_exception($errorcode, \core_text::substr($detail, 0, 500));
    }
}
