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

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/adminlib.php');

/**
 * API key of one AI service: an always visible password field, stored encrypted like Moodle's encrypted password setting.
 *
 * Moodle's own field hides the input behind a "Click to enter text" link, which administrators did not notice.
 * The saved key is never sent back to the browser. Leaving the field empty keeps the saved key; a checkbox removes it.
 * A key that clearly belongs to another service (e.g. a Claude key pasted for OpenAI) is saved for that service,
 * which becomes the active one: the key connects automatically.
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class setting_apikey extends \admin_setting_encryptedpassword {
    /**
     * Constructor.
     *
     * @param string $provider The service this key is for.
     * @param string $visiblename Label.
     * @param string $description Description.
     */
    public function __construct(
        /** @var string The service this key is for. */
        private readonly string $provider,
        string $visiblename,
        string $description,
    ) {
        parent::__construct('local_diverse_assistant/apikey_' . $provider, $visiblename, $description);
    }

    #[\Override]
    public function write_setting($data) {
        if (is_array($data)) {
            $key = trim((string)($data['key'] ?? ''));
            $remove = !empty($data['remove']);
        } else {
            $key = trim((string)$data);
            $remove = false;
        }
        if ($key === '') {
            // Nothing typed: keep the saved key, unless asked to remove it.
            return $remove ? parent::write_setting('') : '';
        }

        $owner = factory::provider_for_key($key, $this->provider);
        if ($owner !== '') {
            set_config('apikey_' . $owner, \core\encryption::encrypt($key), 'local_diverse_assistant');
            set_config('provider', $owner, 'local_diverse_assistant');
            set_config('autoswitched', $owner, 'local_diverse_assistant');
            connection::mark_changed();
            return '';
        }
        return parent::write_setting($key);
    }

    #[\Override]
    public function output_html($data, $query = '') {
        global $OUTPUT;
        $element = $OUTPUT->render_from_template('local_diverse_assistant/setting_apikey', [
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
            'set' => (string)$data !== '',
            'placeholder' => get_string('apikey_placeholder_' . $this->provider, 'local_diverse_assistant'),
        ]);
        return format_admin_setting($this, $this->visiblename, $element, $this->description, true, '', null, $query);
    }
}
