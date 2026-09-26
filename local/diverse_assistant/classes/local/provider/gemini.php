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
 * Google Gemini, through the Gemini API's official OpenAI-compatible endpoint (keys from Google AI Studio).
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gemini extends openai_compatible {
    /** OpenAI-compatible address of the Gemini API. */
    public const BASEURL = 'https://generativelanguage.googleapis.com/v1beta/openai';

    /** Recommended model. */
    public const DEFAULT_MODEL = 'gemini-3.8-flash';

    /**
     * Constructor.
     *
     * @param string $apikey API key (AIza...).
     * @param string $model Model id, e.g. gemini-3.8-flash.
     * @param string $reasoningeffort low, medium or high; empty or none for the model's default.
     */
    public function __construct(
        string $apikey,
        string $model,
        /** @var string Reasoning effort. */
        protected string $reasoningeffort = '',
    ) {
        parent::__construct(self::BASEURL, $apikey, $model, false);
    }

    #[\Override]
    public function get_name(): string {
        return get_string('provider_gemini', 'local_diverse_assistant');
    }

    #[\Override]
    public function list_models(): array {
        // Older versions of the endpoint prefix the ids with "models/".
        $ids = array_map(fn($id) => preg_replace('~^models/~', '', $id), parent::list_models());
        $ids = array_values(array_unique($ids));
        sort($ids);
        return $ids;
    }

    #[\Override]
    protected function build_body(array $messages, chat_options $options): array {
        // No answer limit: Gemini's default is large and also covers the model's thinking.
        $body = [
            'model' => $this->model,
            'messages' => $messages,
            'stream' => true,
            'stream_options' => ['include_usage' => true],
        ] + self::tools_body($options);
        // Thinking cannot be turned off on Gemini 3 models, so "none" keeps the model's default.
        if (in_array($this->reasoningeffort, ['low', 'medium', 'high'], true)) {
            $body['reasoning_effort'] = $this->reasoningeffort;
        }
        return $body;
    }

    #[\Override]
    protected function classify_error(int $status, string $detail): string {
        // The Gemini API answers an invalid key with 400, not 401.
        if ($status === 400 && preg_match('/API key not valid|API_KEY_INVALID|valid API key/i', $detail)) {
            return 'errorauth';
        }
        return parent::classify_error($status, $detail);
    }
}
