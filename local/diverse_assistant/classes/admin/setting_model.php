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
 * Model setting: a menu of the chat models the saved API key can use, from the last connection check.
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class setting_model extends \admin_setting_configselect {
    /**
     * Constructor.
     *
     * @param string $name Setting name.
     * @param string $visiblename Label.
     * @param string $description Description.
     * @param string $defaultsetting Default model.
     */
    public function __construct($name, $visiblename, $description, $defaultsetting) {
        parent::__construct($name, $visiblename, $description, $defaultsetting, null);
    }

    #[\Override]
    public function load_choices() {
        if (is_array($this->choices)) {
            return true;
        }
        $this->choices = connection::get_model_choices((string)$this->get_setting(),
            factory::get_default_model(factory::get_active_provider()));
        return true;
    }
}
