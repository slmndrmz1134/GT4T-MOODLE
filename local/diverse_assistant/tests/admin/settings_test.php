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

namespace local_diverse_assistant\admin;

use local_diverse_assistant\local\connection;
use local_diverse_assistant\local\provider\factory;
use local_diverse_assistant\local\provider\openai;

/**
 * Tests for the API key field, the model menu and the models that get a reasoning effort.
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(setting_apikey::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(setting_model::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(\local_diverse_assistant\local\connection::class)]
final class settings_test extends \advanced_testcase {
    /**
     * An empty field keeps the saved key, a new key replaces it, the checkbox removes it; the key is never in the page.
     */
    public function test_apikey_setting(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $setting = new setting_apikey('openai', 'API key', '');

        $this->assertSame('', $setting->write_setting(['key' => ' sk-first ']));
        $this->assertSame('sk-first', factory::get_api_key());
        $this->assertStringStartsWith('sodium:', get_config('local_diverse_assistant', 'apikey_openai'));

        $setting->write_setting(['key' => '']);
        $this->assertSame('sk-first', factory::get_api_key());

        $setting->write_setting(['key' => 'sk-second']);
        $this->assertSame('sk-second', factory::get_api_key());

        $html = $setting->output_html($setting->get_setting());
        $this->assertStringNotContainsString('sk-second', $html);
        $this->assertStringContainsString('[remove]', $html);

        $setting->write_setting(['key' => '', 'remove' => '1']);
        $this->assertSame('', factory::get_api_key());
    }

    /**
     * A Claude or Gemini key pasted into another service's field is saved for its own service, which becomes active.
     */
    public function test_apikey_connects_to_its_service(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        set_config('provider', 'openai', 'local_diverse_assistant');
        $openai = new setting_apikey('openai', 'API key', '');
        $openai->write_setting(['key' => 'sk-openai']);

        $openai->write_setting(['key' => 'sk-ant-api03-claude']);
        $this->assertSame('anthropic', get_config('local_diverse_assistant', 'provider'));
        $this->assertSame('sk-ant-api03-claude', factory::get_api_key('anthropic'));
        $this->assertSame('sk-openai', factory::get_api_key('openai'));
        $this->assertSame('anthropic', get_config('local_diverse_assistant', 'autoswitched'));
        $this->assertTrue(connection::is_changed());

        (new setting_apikey('anthropic', 'API key', ''))->write_setting(['key' => 'AIzaGemini']);
        $this->assertSame('gemini', get_config('local_diverse_assistant', 'provider'));
        $this->assertSame('AIzaGemini', factory::get_api_key());

        // An sk- key typed for a compatible service stays there.
        set_config('provider', 'openaicompatible', 'local_diverse_assistant');
        (new setting_apikey('openaicompatible', 'API key', ''))->write_setting(['key' => 'sk-router']);
        $this->assertSame('openaicompatible', get_config('local_diverse_assistant', 'provider'));
        $this->assertSame('sk-router', factory::get_api_key());
    }

    /**
     * Before a successful check the menu offers the recommended model; afterwards the chat models of the key.
     */
    public function test_model_choices(): void {
        $this->resetAfterTest();
        set_config('provider', 'openai', 'local_diverse_assistant');

        $choices = connection::get_model_choices('gpt-6-luna', 'gpt-6-luna');
        $this->assertSame(['gpt-6-luna'], array_keys($choices));

        set_config('connectionstatus', json_encode((object)['ok' => true, 'service' => 'openai', 'models' => [
            'gpt-4o', 'gpt-4o-2024-08-06', 'gpt-6-luna', 'gpt-6-sol', 'text-embedding-3-small', 'whisper-1',
            'gpt-4o-mini-tts', 'o3', 'dall-e-3', 'gpt-5-pro', 'omni-moderation-latest',
        ]]), 'local_diverse_assistant');
        $choices = connection::get_model_choices('gpt-6-luna', 'gpt-6-luna');
        $this->assertSame(['gpt-6-luna', 'o3', 'gpt-6-sol', 'gpt-4o'], array_keys($choices));
        $this->assertStringContainsString('gpt-6-luna', $choices['gpt-6-luna']);
        $this->assertNotSame('gpt-6-luna', $choices['gpt-6-luna']);

        // A saved model the key cannot use stays in the menu, so the page shows what is saved.
        $this->assertArrayHasKey('gpt-3.5-turbo', connection::get_model_choices('gpt-3.5-turbo', 'gpt-6-luna'));

        $setting = new setting_model('local_diverse_assistant/model', 'Model', '', 'gpt-6-luna');
        $setting->write_setting('o3');
        $this->assertSame('o3', get_config('local_diverse_assistant', 'model'));
        $setting = new setting_model('local_diverse_assistant/model', 'Model', '', 'gpt-6-luna');
        $setting->write_setting('not-a-model');
        $this->assertSame('o3', get_config('local_diverse_assistant', 'model'));

        // After switching to Claude, the OpenAI list is not offered; the Claude default is.
        set_config('provider', 'anthropic', 'local_diverse_assistant');
        $this->assertSame(['claude-opus-5', 'o3'], array_keys(connection::get_model_choices('o3', 'claude-opus-5')));
    }

    /**
     * Claude and Gemini lists: only chat models, newest first.
     */
    public function test_chat_models_claude_and_gemini(): void {
        $this->assertSame(['claude-sonnet-5', 'claude-opus-5', 'claude-haiku-4-5'],
            connection::chat_models(['claude-haiku-4-5', 'claude-opus-5', 'claude-sonnet-5'], 'anthropic'));
        $this->assertSame(['gemini-3.8-flash', 'gemini-3.5-flash-lite'], connection::chat_models([
            'gemini-3.5-flash-lite', 'gemini-3.8-flash', 'gemini-embedding-001', 'imagen-4.0', 'veo-3.0',
            'gemini-3.8-flash-image', 'gemini-3.5-flash-live', 'gemini-3.8-flash-tts', 'aqa',
        ], 'gemini'));
    }

    /**
     * Other services' model lists are only cleaned of embedding models.
     */
    public function test_chat_models_other_services(): void {
        $this->assertSame(['mistral-small-latest', 'mistral-large-latest'],
            connection::chat_models(['mistral-embed', 'mistral-large-latest', 'mistral-small-latest'], 'openaicompatible'));
    }

    /**
     * Only models that accept it get a reasoning effort.
     */
    public function test_reasoning_effort_support(): void {
        foreach (['gpt-6-luna', 'gpt-5.6-luna', 'gpt-5-mini', 'o3', 'o4-mini'] as $model) {
            $this->assertTrue(openai::supports_reasoning_effort($model), $model);
        }
        foreach (['gpt-4o', 'gpt-4.1-mini', 'gpt-3.5-turbo', 'chatgpt-4o-latest'] as $model) {
            $this->assertFalse(openai::supports_reasoning_effort($model), $model);
        }
    }
}
