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
 * OpenAI (GPT models), through the Chat Completions API.
 *
 * Requests go to the EU address when the OpenAI project was created with European data residency.
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class openai extends openai_compatible {
    /** Default API address. */
    public const BASEURL_GLOBAL = 'https://api.openai.com/v1';

    /** API address for projects with European data residency. */
    public const BASEURL_EU = 'https://eu.api.openai.com/v1';

    /** Longest answer, in tokens; for reasoning models this also covers the thinking. */
    protected const MAX_OUTPUT_TOKENS = 4000;

    /**
     * Constructor.
     *
     * @param string $apikey API key.
     * @param string $model Model id, e.g. gpt-6-luna.
     * @param bool $eu Use the European data residency address.
     * @param string $reasoningeffort none, low, medium, high; empty for the model's default.
     */
    public function __construct(
        string $apikey,
        string $model,
        bool $eu = false,
        /** @var string Reasoning effort, empty for the model's default. */
        protected string $reasoningeffort = '',
    ) {
        parent::__construct($eu ? self::BASEURL_EU : self::BASEURL_GLOBAL, $apikey, $model, $eu);
    }

    #[\Override]
    public function get_name(): string {
        return get_string('provider_openai', 'local_diverse_assistant');
    }

    #[\Override]
    protected function build_body(array $messages): array {
        $body = [
            'model' => $this->model,
            'messages' => $messages,
            'stream' => true,
            // Token counts arrive in a last chunk.
            'stream_options' => ['include_usage' => true],
            'max_completion_tokens' => static::MAX_OUTPUT_TOKENS,
        ];
        if ($this->reasoningeffort !== '' && self::supports_reasoning_effort($this->model)) {
            $body['reasoning_effort'] = $this->reasoningeffort;
        }
        return $body;
    }

    /**
     * Whether a model accepts reasoning_effort (o-series and GPT-5 or newer); older models reject the request with it.
     *
     * @param string $model Model id.
     * @return bool
     */
    public static function supports_reasoning_effort(string $model): bool {
        if (preg_match('/^o\d/i', $model)) {
            return true;
        }
        return preg_match('/^gpt-(\d+)/i', $model, $matches) && (int)$matches[1] >= 5;
    }
}
