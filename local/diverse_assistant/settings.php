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

/**
 * DIVERSE AI assistant - Settings
 *
 * There is deliberately no setting for how long chats are kept: every student chooses that in the chat panel.
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_diverse_assistant\local\chat_service;
use local_diverse_assistant\local\connection;
use local_diverse_assistant\local\provider\factory;

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_diverse_assistant', new lang_string('pluginname', 'local_diverse_assistant'));
    $ADMIN->add('localplugins', $settings);

    if ($ADMIN->fulltree) {
        $component = 'local_diverse_assistant';

        // The status box reads the plugin's tables, which do not exist yet while the plugin is being installed.
        if (!during_initial_install() && get_config($component, 'version')) {
            // Check the connection after the service, key or model was saved; only on this page, so other admin
            // pages stay fast.
            if (optional_param('section', '', PARAM_ALPHANUMEXT) === 'local_diverse_assistant' && connection::is_changed()
                    && factory::get_api_key() !== '') {
                connection::check();
            }
            $settings->add(new admin_setting_description("{$component}/status", '', connection::render_status()));
        }

        // Connection.
        $settings->add(new admin_setting_heading("{$component}/connectionheading",
            new lang_string('connectionheading', $component), new lang_string('connectionheading_desc', $component)));

        $settings->add(new admin_setting_configcheckbox("{$component}/enabled",
            new lang_string('enabled', $component), new lang_string('enabled_desc', $component), 0));

        $setting = new admin_setting_configselect("{$component}/provider",
            new lang_string('provider', $component), new lang_string('provider_desc', $component),
            'openai', factory::get_options());
        $setting->set_updatedcallback(connection::class . '::mark_changed');
        $settings->add($setting);

        // One key per service, so switching services keeps the keys. Stored encrypted and never shown again; a key of
        // another service is saved for that service and switches to it.
        foreach (factory::PROVIDERS as $provider) {
            $setting = new \local_diverse_assistant\admin\setting_apikey($provider,
                new lang_string('apikey', $component), new lang_string('apikey_desc_' . $provider, $component));
            $setting->set_updatedcallback(connection::class . '::mark_changed');
            $settings->add($setting);
            $settings->hide_if("{$component}/apikey_{$provider}", "{$component}/provider", 'neq', $provider);
        }

        $setting = new admin_setting_configselect("{$component}/openairegion",
            new lang_string('openairegion', $component), new lang_string('openairegion_desc', $component), 'global', [
                'global' => new lang_string('openairegion_global', $component),
                'eu' => new lang_string('openairegion_eu', $component),
            ]);
        $setting->set_updatedcallback(connection::class . '::mark_changed');
        $settings->add($setting);
        $settings->hide_if("{$component}/openairegion", "{$component}/provider", 'neq', 'openai');

        $setting = new admin_setting_configtext("{$component}/endpoint",
            new lang_string('endpoint', $component), new lang_string('endpoint_desc', $component), '', PARAM_URL, 60);
        $setting->set_updatedcallback(connection::class . '::mark_changed');
        $settings->add($setting);
        $settings->hide_if("{$component}/endpoint", "{$component}/provider", 'neq', 'openaicompatible');

        $settings->add(new admin_setting_configcheckbox("{$component}/endpointineu",
            new lang_string('endpointineu', $component), new lang_string('endpointineu_desc', $component), 0));
        $settings->hide_if("{$component}/endpointineu", "{$component}/provider", 'neq', 'openaicompatible');

        // A menu of the models the saved key can use.
        $setting = new \local_diverse_assistant\admin\setting_model("{$component}/model",
            new lang_string('model', $component), new lang_string('model_desc', $component),
            factory::get_default_model('openai'));
        $setting->set_updatedcallback(connection::class . '::mark_changed');
        $settings->add($setting);

        $settings->add(new admin_setting_configselect("{$component}/reasoningeffort",
            new lang_string('reasoningeffort', $component), new lang_string('reasoningeffort_desc', $component), 'low', [
                '' => new lang_string('reasoningeffort_default', $component),
                'none' => new lang_string('reasoningeffort_none', $component),
                'low' => new lang_string('reasoningeffort_low', $component),
                'medium' => new lang_string('reasoningeffort_medium', $component),
                'high' => new lang_string('reasoningeffort_high', $component),
            ]));
        $settings->hide_if("{$component}/reasoningeffort", "{$component}/provider", 'eq', 'openaicompatible');

        // Data protection.
        $settings->add(new admin_setting_heading("{$component}/privacyheading",
            new lang_string('privacyheading', $component), new lang_string('privacyheading_desc', $component)));

        $settings->add(new admin_setting_configcheckbox("{$component}/euonly",
            new lang_string('euonly', $component), new lang_string('euonly_desc', $component), 0));

        // Limits.
        $settings->add(new admin_setting_heading("{$component}/limitsheading",
            new lang_string('limitsheading', $component), ''));

        $settings->add(new admin_setting_configtext("{$component}/userhourlylimit",
            new lang_string('userhourlylimit', $component), new lang_string('userhourlylimit_desc', $component),
            chat_service::DEFAULT_HOURLY_LIMIT, PARAM_INT, 6));

        $settings->add(new admin_setting_configtext("{$component}/maxcontextchars",
            new lang_string('maxcontextchars', $component), new lang_string('maxcontextchars_desc', $component),
            chat_service::DEFAULT_MAX_CONTEXT_CHARS, PARAM_INT, 8));

        $settings->add(new admin_setting_configcheckbox("{$component}/quizlock",
            new lang_string('quizlock', $component), new lang_string('quizlock_desc', $component), 1));
    }
}
