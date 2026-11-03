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

namespace local_diverse_darklogo\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * DIVERSE dark logos - The form for the two logos of a partner
 *
 * @package    local_diverse_darklogo
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edit extends \moodleform {
    #[\Override]
    protected function definition(): void {
        $mform = $this->_form;

        $mform->addElement('hidden', 'id', $this->_customdata['tenantid']);
        $mform->setType('id', PARAM_INT);

        $options = \local_diverse_darklogo\local\logos::options();
        $mform->addElement('filemanager', 'logo', get_string('logo', 'local_diverse_darklogo'), null, $options);
        $mform->addElement('static', 'logo_desc', '', get_string('logo_desc', 'local_diverse_darklogo'));
        $mform->addElement('filemanager', 'logocompact', get_string('logocompact', 'local_diverse_darklogo'), null, $options);
        $mform->addElement('static', 'logocompact_desc', '', get_string('logocompact_desc', 'local_diverse_darklogo'));

        $this->add_action_buttons(true, get_string('savechanges'));
    }
}
