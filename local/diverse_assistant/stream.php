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
 * Events (JSON in "data:" lines): {"type":"delta","text":...} for each piece of the answer, {"type":"status","text":...}
 * while a teacher's proposal is being written, then either {"type":"done",...} with the final HTML and any proposals
 * or {"type":"error","message":...}. When a proxy buffers the response the browser simply receives all events at once,
 * so no separate non-streaming path is needed.
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_diverse_assistant\local\chat_service;
use local_diverse_assistant\local\provider\aborted_exception;

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
 * @param string $type delta, status, done or error.
 * @param array $data Event data.
 */
function local_diverse_assistant_send_event(string $type, array $data): void {
    echo 'data: ' . json_encode(['type' => $type] + $data, JSON_UNESCAPED_UNICODE) . "\n\n";
    flush();
}

/**
 * End the request to the AI service when the user stopped the answer or left the page, so it is not written (and paid
 * for) to the end. PHP notices a closed connection only when it sends something, so this runs after each event.
 *
 * @throws aborted_exception If the browser closed the connection.
 */
function local_diverse_assistant_stop_if_aborted(): void {
    if (connection_aborted()) {
        throw new aborted_exception();
    }
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

// PHP must not stop in the middle of saving; a closed connection is noticed after each event instead (see above).
ignore_user_abort(true);

try {
    require_sesskey();
    $course = get_course($courseid);
    require_login($course, false, null, false, true);

    $request = chat_service::prepare($course, $cmid, $conversationid, $message, is_array($history) ? $history : []);

    // The answer can take a while: release the session so the user's other pages keep loading.
    \core\session\manager::write_close();
    // A teacher's proposal can be a whole page, which takes minutes with some models.
    \core_php_time_limit::raise(600);

    $result = chat_service::complete($request, function (string $text): void {
        local_diverse_assistant_send_event('delta', ['text' => $text]);
        local_diverse_assistant_stop_if_aborted();
    }, function (string $status): void {
        local_diverse_assistant_send_event('status', ['text' => $status]);
        local_diverse_assistant_stop_if_aborted();
    });
    local_diverse_assistant_send_event('done', $result);
} catch (aborted_exception $e) {
    // The browser is gone: there is no one to tell.
    return;
} catch (\Throwable $e) {
    local_diverse_assistant_send_event('error', ['message' => chat_service::get_error_message($e)]);
}
