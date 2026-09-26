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

namespace local_diverse_assistant\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use local_diverse_assistant\local\teacher\applier;
use local_diverse_assistant\local\teacher\proposals;

/**
 * Apply, undo or decline a change the assistant proposed to a teacher.
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class proposal_action extends external_api {
    /**
     * Parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'proposalid' => new external_value(PARAM_INT, 'Proposal id'),
            'action' => new external_value(PARAM_ALPHA, 'apply, undo or discard'),
        ]);
    }

    /**
     * Do it.
     *
     * @param int $proposalid Proposal id.
     * @param string $action apply, undo or discard.
     * @return array
     */
    public static function execute(int $proposalid, string $action): array {
        global $USER;
        ['proposalid' => $proposalid, 'action' => $action] = self::validate_parameters(self::execute_parameters(),
            ['proposalid' => $proposalid, 'action' => $action]);
        $record = proposals::get($proposalid, (int)$USER->id);
        $context = \context_course::instance($record->courseid);
        self::validate_context($context);
        require_capability('local/diverse_assistant:teach', $context);

        $message = '';
        try {
            match ($action) {
                'apply' => applier::apply($record),
                'undo' => applier::undo($record),
                'discard' => applier::discard($record),
                default => throw new \invalid_parameter_exception('Unknown action ' . $action),
            };
        } catch (\moodle_exception $e) {
            // The checks run before anything is written. Show why in the card, e.g. the text was changed in the
            // meantime or a permission is missing; anything else is a real error.
            if ($e->module !== 'local_diverse_assistant' && !$e instanceof \required_capability_exception) {
                throw $e;
            }
            $message = $e->getMessage();
        }
        return ['ok' => $message === '', 'message' => $message, 'proposal' => proposals::export($record)];
    }

    /**
     * Return structure.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'ok' => new external_value(PARAM_BOOL, 'Whether it was done'),
            'message' => new external_value(PARAM_TEXT, 'Why not, if not'),
            'proposal' => proposals::export_structure(),
        ]);
    }
}
