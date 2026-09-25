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
 * DIVERSE AI assistant - Answer a question, streaming the answer as server-sent events.
 *
 * Events (JSON in "data:" lines): {"type":"delta","text":...} for each piece of the answer, then either
 * {"type":"done",...} with the final HTML or {"type":"error","message":...}. When a proxy buffers the response the
 * browser simply receives all events at once, so no separate non-streaming path is needed.
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_diverse_assistant\local\chat_service;

define('NO_OUTPUT_BUFFERING', true);
define('NO_DEBUG_DISPLAY', true);

require_once(__DIR__ . '/../../config.php');

$courseid = required_param('courseid', PARAM_INT);
$cmid = optional_param('cmid', 0, PARAM_INT);
$conversationid = optional_param('conversationid', 0, PARAM_INT);
$message = required_param('message', PARAM_RAW);
$history = json_decode(optional_param('history', '[]', PARAM_RAW), true);

/**
 * Send one event to the browser.
 *
 * @param string $type delta, done or error.
 * @param array $data Event data.
 */
function local_diverse_assistant_send_event(string $type, array $data): void {
    echo 'data: ' . json_encode(['type' => $type] + $data, JSON_UNESCAPED_UNICODE) . "\n\n";
    flush();
}

// Keep compression and proxies from holding the answer back.
if (function_exists('apache_setenv')) {
    @apache_setenv('no-gzip', '1');
}
@ini_set('zlib.output_compression', '0');
while (ob_get_level() > 0) {
    ob_end_flush();
}
header('Content-Type: text/event-stream; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('X-Accel-Buffering: no');
header('X-Content-Type-Options: nosniff');

// Finish and save the answer even if the student closes the panel in the meantime.
ignore_user_abort(true);

try {
    require_sesskey();
    $course = get_course($courseid);
    require_login($course, false, null, false, true);

    $request = chat_service::prepare($course, $cmid, $conversationid, $message, is_array($history) ? $history : []);

    // The answer can take a while: release the session so the student's other pages keep loading.
    \core\session\manager::write_close();

    $result = chat_service::complete($request, function (string $text): void {
        local_diverse_assistant_send_event('delta', ['text' => $text]);
    });
    local_diverse_assistant_send_event('done', $result);
} catch (\Throwable $e) {
    local_diverse_assistant_send_event('error', ['message' => chat_service::get_error_message($e)]);
}
