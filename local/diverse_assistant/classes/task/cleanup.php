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

namespace local_diverse_assistant\task;

use local_diverse_assistant\local\retention;
use local_diverse_assistant\local\teacher\proposals;

/**
 * Delete chats whose period chosen by their owner has ended, old proposals of the teacher mode, and trim the usage log.
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cleanup extends \core\task\scheduled_task {
    #[\Override]
    public function get_name() {
        return get_string('task_cleanup', 'local_diverse_assistant');
    }

    #[\Override]
    public function execute() {
        $deleted = retention::cleanup();
        mtrace("Deleted {$deleted} expired AI assistant conversations.");
        $deleted = proposals::cleanup();
        mtrace("Deleted {$deleted} AI assistant proposals older than " . proposals::KEEP_DAYS . ' days.');
    }
}
