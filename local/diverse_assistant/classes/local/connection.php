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

namespace local_diverse_assistant\local;

use local_diverse_assistant\local\provider\factory;
use local_diverse_assistant\local\provider\provider_exception;

/**
 * Checks the connection to the AI service and shows the result on the settings page.
 *
 * When an administrator saves the service, key or model, the settings are marked as changed; the check runs the next
 * time the settings page opens, after every changed setting has been saved.
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class connection {
    /** Most model ids kept from the service's list. */
    private const MAX_MODELS = 500;

    /**
     * Settings callback: a connection setting changed.
     *
     * @param string $fullname Name of the changed setting.
     */
    public static function mark_changed(string $fullname = ''): void {
        set_config('statusdirty', 1, 'local_diverse_assistant');
    }

    /**
     * Whether the connection settings changed since the last check.
     *
     * @return bool
     */
    public static function is_changed(): bool {
        return (bool)get_config('local_diverse_assistant', 'statusdirty');
    }

    /**
     * Check the key with the service and remember the result.
     *
     * When the chosen model is not available with this service (for example after switching from OpenAI to Claude),
     * the service's recommended model is chosen instead, so the key connects without further steps.
     *
     * @return \stdClass The status: time, ok, error, detail, service, provider, eu, model, modelfound, modelchanged, models.
     */
    public static function check(): \stdClass {
        $status = (object)[
            'time' => time(),
            'ok' => false,
            'error' => '',
            'detail' => '',
            'service' => factory::get_active_provider(),
            'provider' => '',
            'eu' => false,
            'model' => '',
            'modelfound' => false,
            'modelchanged' => '',
            'models' => [],
        ];
        try {
            $client = factory::create();
            $status->provider = $client->get_name();
            $status->eu = $client->is_eu();
            $models = $client->list_models();

            $model = trim((string)get_config('local_diverse_assistant', 'model'));
            if (!in_array($model, $models, true)) {
                $candidates = self::chat_models($models, $status->service) ?: $models;
                $default = factory::get_default_model($status->service);
                $pick = in_array($default, $candidates, true) ? $default : (string)reset($candidates);
                if ($pick !== '') {
                    $status->modelchanged = $model;
                    $model = $pick;
                    set_config('model', $model, 'local_diverse_assistant');
                }
            }
            $status->ok = true;
            $status->model = $model;
            $status->modelfound = in_array($model, $models, true);
            $status->models = array_slice($models, 0, self::MAX_MODELS);
        } catch (provider_exception $e) {
            $status->error = $e->errorcode;
            $status->detail = (string)$e->debuginfo;
        }
        set_config('connectionstatus', json_encode($status), 'local_diverse_assistant');
        unset_config('statusdirty', 'local_diverse_assistant');
        return $status;
    }

    /**
     * Models to try when the chosen one is overloaded: the same family in other versions, newest first.
     *
     * For example gemini-3.7-flash and gemini-3.6-flash for gemini-3.8-flash. Taken from the last check of the active
     * service; preview and experimental versions are not used.
     *
     * @param string $model The overloaded model.
     * @param int $max Most models to return.
     * @return string[]
     */
    public static function get_fallback_models(string $model, int $max = 2): array {
        $status = self::get_status();
        $service = factory::get_active_provider();
        if (!$status || !$status->ok || ($status->service ?? $service) !== $service) {
            return [];
        }
        $family = self::model_family($model);
        $fallbacks = array_filter(self::chat_models($status->models, $service), fn($candidate) => $candidate !== $model
            && self::model_family($candidate) === $family && !preg_match('/(preview|exp|latest)/i', $candidate));
        return array_slice(array_values($fallbacks), 0, $max);
    }

    /**
     * A model id with its version numbers replaced, e.g. gemini-#-flash for gemini-3.8-flash.
     *
     * @param string $model Model id.
     * @return string
     */
    private static function model_family(string $model): string {
        return preg_replace('/\d+([.-]\d+)*/', '#', $model);
    }

    /**
     * Remember a question the assistant could not answer, for the settings page (no student data is kept).
     *
     * @param string $model Model that was asked.
     * @param provider_exception $e The error.
     */
    public static function record_error(string $model, provider_exception $e): void {
        $detail = (string)$e->debuginfo;
        set_config('lasterror', json_encode(['time' => time(), 'service' => factory::get_active_provider(),
            'model' => $model, 'errorcode' => $e->errorcode, 'detail' => \core_text::substr($detail, 0, 300),
            // Google's free tier allows a few questions per minute per model and may use them for its products.
            'freetier' => (bool)preg_match('/free_tier/i', $detail)]), 'local_diverse_assistant');
    }

    /**
     * Remember that a fallback model answered because the chosen one was overloaded.
     *
     * @param string $from The overloaded model.
     * @param string $to The model that answered.
     */
    public static function record_fallback(string $from, string $to): void {
        set_config('lastfallback', json_encode(['time' => time(), 'from' => $from, 'to' => $to]), 'local_diverse_assistant');
    }

    /**
     * The result of the last check.
     *
     * @return \stdClass|null
     */
    public static function get_status(): ?\stdClass {
        $status = json_decode((string)get_config('local_diverse_assistant', 'connectionstatus'));
        return is_object($status) ? $status : null;
    }

    /**
     * HTML of the status box at the top of the settings page.
     *
     * @return string
     */
    public static function render_status(): string {
        global $OUTPUT;
        $config = get_config('local_diverse_assistant');
        $data = [
            'enabled' => !empty($config->enabled),
            'haskey' => factory::get_api_key() !== '',
            'testurl' => (new \moodle_url('/local/diverse_assistant/testconnection.php', ['sesskey' => sesskey()]))->out(false),
        ];

        // A pasted key of another service switched the service: say so once.
        if (!empty($config->autoswitched)) {
            $data['switched'] = get_string('provider_' . $config->autoswitched, 'local_diverse_assistant');
            unset_config('autoswitched', 'local_diverse_assistant');
        }

        $status = self::get_status();
        if ($data['haskey'] && $status && !self::is_changed()) {
            $data['checked'] = userdate($status->time, get_string('strftimedatetimeshort', 'langconfig'));
            if ($status->ok) {
                $data['ok'] = true;
                $data['provider'] = $status->provider;
                $data['model'] = $status->model;
                $data['modelfound'] = $status->modelfound;
                $data['modelcount'] = count(self::chat_models($status->models,
                    (string)($status->service ?? factory::get_active_provider())));
                if (!empty($status->modelchanged)) {
                    $data['modelchanged'] = get_string('status_modelchanged', 'local_diverse_assistant',
                        (object)['old' => $status->modelchanged, 'new' => $status->model]);
                }
                $data['euwarning'] = !empty($config->euonly) && empty($status->eu);
            } else {
                $data['error'] = get_string($status->error ?: 'errorgeneric', 'local_diverse_assistant');
                $data['detail'] = $status->detail;
            }
        } else if ($data['haskey']) {
            $data['pending'] = true;
        }

        // Problems of the last week while answering students.
        $since = time() - 7 * DAYSECS;
        $format = get_string('strftimedatetimeshort', 'langconfig');
        $lasterror = json_decode((string)($config->lasterror ?? ''));
        if (is_object($lasterror) && $lasterror->time >= $since) {
            $data['lasterror'] = get_string('status_lasterror', 'local_diverse_assistant', (object)[
                'time' => userdate($lasterror->time, $format),
                'model' => $lasterror->model,
                'error' => get_string($lasterror->errorcode, 'local_diverse_assistant'),
            ]);
            $data['lasterrordetail'] = $lasterror->detail;
            $data['freetier'] = !empty($lasterror->freetier) || preg_match('/free_tier/i', (string)$lasterror->detail);
        }
        $lastfallback = json_decode((string)($config->lastfallback ?? ''));
        if (is_object($lastfallback) && $lastfallback->time >= $since) {
            $data['lastfallback'] = get_string('status_lastfallback', 'local_diverse_assistant', (object)[
                'time' => userdate($lastfallback->time, $format),
                'from' => $lastfallback->from,
                'to' => $lastfallback->to,
            ]);
        }

        $day = store::usage_summary(time() - DAYSECS);
        $week = store::usage_summary(time() - 7 * DAYSECS);
        $data['usage'] = get_string('status_usage', 'local_diverse_assistant', (object)[
            'dayquestions' => $day->questions,
            'weekquestions' => $week->questions,
            'weekusers' => $week->users,
            'weektokens' => number_format($week->tokens, 0, ',', '.'),
        ]);

        return $OUTPUT->render_from_template('local_diverse_assistant/admin_status', $data);
    }

    /**
     * Choices for the model menu: the chat models of the last check, newest first, the recommended one on top.
     *
     * Before the first successful check only the current and the default model are offered.
     *
     * @param string $current The saved model, kept in the menu even if the key cannot use it.
     * @param string $default The recommended model.
     * @return array model id => label
     */
    public static function get_model_choices(string $current, string $default): array {
        $status = self::get_status();
        $service = factory::get_active_provider();
        // Only models of the last check of this service; after switching services the list comes with the next check.
        // Checks saved before version 0.2 did not record the service; they were made with the active one.
        $models = ($status && $status->ok && ($status->service ?? $service) === $service)
            ? self::chat_models($status->models, $service) : [];

        $ordered = [];
        if ($default !== '' && (!$models || in_array($default, $models, true))) {
            $ordered[] = $default;
        }
        if ($current !== '' && !in_array($current, $ordered, true)) {
            $ordered[] = $current;
        }
        foreach ($models as $model) {
            if (!in_array($model, $ordered, true)) {
                $ordered[] = $model;
            }
        }

        $choices = [];
        foreach ($ordered as $model) {
            $choices[$model] = $model === $default
                ? get_string('model_recommended', 'local_diverse_assistant', $model) : $model;
        }
        return $choices;
    }

    /**
     * The models that can chat, newest first.
     *
     * OpenAI's and Google's lists also have embedding, audio, image, video and moderation models, and OpenAI's has dated
     * snapshots and models that only work with other APIs; those are left out. Anthropic lists only Claude models.
     * Other services' lists are only cleaned of embedding models.
     *
     * @param string[] $models All model ids.
     * @param string $provider Service name.
     * @return string[]
     */
    public static function chat_models(array $models, string $provider): array {
        $models = match ($provider) {
            'openai' => array_filter($models, fn($id) => preg_match('/^(gpt-|chatgpt-|o\d)/i', $id)
                && !preg_match('/(embed|audio|realtime|tts|transcribe|image|search|moderation|instruct|dall-e|whisper|'
                    . 'codex|computer-use|deep-research|-pro\b|\d{4}-\d{2}-\d{2})/i', $id)),
            'anthropic' => array_filter($models, fn($id) => str_starts_with($id, 'claude-')),
            'gemini' => array_filter($models, fn($id) => str_starts_with($id, 'gemini-')
                && !preg_match('/(embed|imagen|veo|aqa|tts|image|live|audio|computer-use|robotics)/i', $id)),
            default => array_filter($models, fn($id) => !preg_match('/embed/i', $id)),
        };
        $models = array_values($models);
        rsort($models, SORT_NATURAL);
        return $models;
    }
}
