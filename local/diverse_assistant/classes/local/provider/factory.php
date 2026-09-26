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
 * Creates the client of the AI service chosen in the settings.
 *
 * Every service keeps its own API key (setting apikey_<service>), so switching between services does not lose keys.
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class factory {
    /** Services an administrator can choose. */
    public const PROVIDERS = ['openai', 'anthropic', 'gemini', 'openaicompatible'];

    /** Model suggested for each service. */
    private const DEFAULT_MODELS = [
        'openai' => 'gpt-6-luna',
        'anthropic' => anthropic::DEFAULT_MODEL,
        'gemini' => gemini::DEFAULT_MODEL,
    ];

    /**
     * Services for the settings menu.
     *
     * @return array name => visible name
     */
    public static function get_options(): array {
        $options = [];
        foreach (self::PROVIDERS as $provider) {
            $options[$provider] = new \lang_string('provider_' . $provider, 'local_diverse_assistant');
        }
        return $options;
    }

    /**
     * The service chosen in the settings.
     *
     * @return string
     */
    public static function get_active_provider(): string {
        $provider = (string)get_config('local_diverse_assistant', 'provider');
        return in_array($provider, self::PROVIDERS, true) ? $provider : 'openai';
    }

    /**
     * Model suggested for a service, empty if there is no obvious choice.
     *
     * @param string $provider Service name.
     * @return string
     */
    public static function get_default_model(string $provider): string {
        return self::DEFAULT_MODELS[$provider] ?? '';
    }

    /**
     * The model chosen in the settings, or the service's recommended one.
     *
     * @return string
     */
    public static function get_model(): string {
        $model = trim((string)get_config('local_diverse_assistant', 'model'));
        return $model !== '' ? $model : self::get_default_model(self::get_active_provider());
    }

    /**
     * The client for the service in the plugin settings.
     *
     * @param string|null $model Use this model instead of the chosen one (a fallback when that one is overloaded).
     * @return provider
     * @throws provider_exception If the service is not set up.
     */
    public static function create(?string $model = null): provider {
        $config = get_config('local_diverse_assistant');
        $provider = self::get_active_provider();
        $apikey = self::get_api_key($provider);
        if ($apikey === '') {
            throw new provider_exception('errornotconfigured');
        }
        $model = $model ?? self::get_model();
        $effort = (string)($config->reasoningeffort ?? '');

        switch ($provider) {
            case 'anthropic':
                return new anthropic($apikey, $model, $effort);
            case 'gemini':
                return new gemini($apikey, $model, $effort);
            case 'openaicompatible':
                $endpoint = trim($config->endpoint ?? '');
                if ($endpoint === '') {
                    throw new provider_exception('errornoendpoint');
                }
                return new openai_compatible($endpoint, $apikey, $model, !empty($config->endpointineu));
            default:
                return new openai($apikey, $model, ($config->openairegion ?? '') === 'eu', $effort);
        }
    }

    /**
     * A service's API key from the settings, decrypted.
     *
     * @param string|null $provider Service name, null for the active one.
     * @return string Empty if none is set or it can no longer be decrypted (e.g. the site's encryption key changed).
     */
    public static function get_api_key(?string $provider = null): string {
        $encrypted = (string)get_config('local_diverse_assistant', 'apikey_' . ($provider ?? self::get_active_provider()));
        if ($encrypted === '') {
            return '';
        }
        try {
            return trim(\core\encryption::decrypt($encrypted));
        } catch (\moodle_exception $e) {
            return '';
        }
    }

    /**
     * Guess which company issued an API key from how it starts.
     *
     * @param string $apikey The key.
     * @return string anthropic, google, openrouter, openai, or empty if unknown.
     */
    public static function detect_key_vendor(string $apikey): string {
        return match (true) {
            str_starts_with($apikey, 'sk-ant-') => 'anthropic',
            str_starts_with($apikey, 'AIza') => 'google',
            str_starts_with($apikey, 'sk-or-') => 'openrouter',
            str_starts_with($apikey, 'sk-') => 'openai',
            default => '',
        };
    }

    /**
     * The service a key clearly belongs to, for connecting it automatically.
     *
     * OpenAI-style keys (sk-...) are also used by many compatible services, so they only point to OpenAI when the key
     * was not entered for a compatible service.
     *
     * @param string $apikey The key.
     * @param string $enteredfor The service whose field the key was typed into.
     * @return string Service name, or empty if the key fits the service it was entered for.
     */
    public static function provider_for_key(string $apikey, string $enteredfor): string {
        $provider = match (self::detect_key_vendor($apikey)) {
            'anthropic' => 'anthropic',
            'google' => 'gemini',
            'openai' => $enteredfor === 'openaicompatible' ? '' : 'openai',
            default => '',
        };
        return $provider === $enteredfor ? '' : $provider;
    }
}
