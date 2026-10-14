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
 * DIVERSE demo live sessions: one BigBlueButton live session on the given day in each shared course of
 * setup/diverse_demo_accounts.php, so that students and teachers of several partners meet in one room.
 *
 * Teachers are moderators, everybody else joins as a viewer. The room opens 15 minutes before the start and closes
 * 45 minutes after it. Times are Istanbul time; Moodle shows them to every user in their own time zone. Activity
 * names have no {mlang} tags: the BigBlueButton module shows them unfiltered.
 *
 * Run after setup/diverse_demo_accounts.php. It is idempotent: a session that already exists in a course (same
 * name) is left as it is.
 *
 * With --from and --until the day gets a random schedule instead: a session every 30 minutes in a course picked at
 * random from --courses (the same date and options always give the same schedule, so a dry run shows what a real run
 * creates). Each session name carries the date and time, so the fixed sessions above are not touched.
 *
 * Usage:
 *   php setup/diverse_demo_live_sessions.php --date=2026-10-07 [--dry-run]
 *   php setup/diverse_demo_live_sessions.php --date=2026-10-14 --from=12:30 --until=20:00 [--courses=IBM401,...] [--dry-run]
 *
 * @package    theme_diverse
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->dirroot . '/course/lib.php');

[$options, $unrecognised] = cli_get_params(
    ['help' => false, 'dry-run' => false, 'date' => '', 'from' => '', 'until' => '', 'courses' => ''],
    ['h' => 'help', 'n' => 'dry-run']
);

if ($unrecognised) {
    cli_error(get_string('cliunknowoption', 'admin', implode(PHP_EOL . '  ', $unrecognised)));
}

if ($options['help'] || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $options['date'])) {
    cli_writeln("DIVERSE demo live sessions in the shared courses on one day.

Options:
      --date=YYYY-MM-DD  Day of the sessions (required).
      --from=HH:MM       Random schedule: first start time (Istanbul time).
      --until=HH:MM      Random schedule: every room is closed by this time.
      --courses=A,B,...  Random schedule: course shortnames to pick from (default: the shared courses and the
                         Beykent and Griffith courses).
  -n, --dry-run          Show what would change without changing anything.
  -h, --help             Print this help.

Example:
  php setup/diverse_demo_live_sessions.php --date=2026-10-07 --dry-run
  php setup/diverse_demo_live_sessions.php --date=2026-10-14 --from=12:30 --until=20:00 --dry-run");
    exit($options['help'] ? 0 : 1);
}

$dryrun = (bool) $options['dry-run'];

// Course shortname => [start (Istanbul time), session name, description].
$sessions = [
    'GT4T-101' => ['12:00', 'Live session: Kick-off',
        'Kick-off of the course with all partners: introductions, the course plan and the group assignment.'],
    'GT4T-201' => ['13:00', 'Live session: Industry 4.0 lecture',
        'Live lecture by the Rosenheim and Beykent teachers, with questions from all partners.'],
    'GT4T-301' => ['14:00', 'Live session: Challenge team meeting',
        'The international student teams meet the challenge owner and plan their first steps.'],
    'GT4T-401' => ['15:00', 'Live session: Pitch session',
        'Students pitch their sustainable business ideas to the company partners.'],
    'CTP001' => ['16:00', 'Live session: Welcome to the common training programme',
        'Welcome session of the common training programme for all GT4T partners.'],
    'IBM401' => ['17:00', 'Live session: Doing business across cultures',
        'Griffith and Beykent teachers discuss international business cases with students from three partners.'],
];

// Random schedule (--from/--until): a session every 30 minutes, the course and the topic picked at random.
if ($options['from'] !== '' || $options['until'] !== '') {
    if (!preg_match('/^\d{2}:\d{2}$/', $options['from']) || !preg_match('/^\d{2}:\d{2}$/', $options['until'])) {
        cli_error('--from and --until must both be given as HH:MM.');
    }
    $pool = $options['courses'] !== '' ? array_map('trim', explode(',', $options['courses'])) : [
        'IBM401', 'GT4T-101', 'GT4T-201', 'GT4T-301', 'GT4T-401', 'CTP001', 'GRI-DMI-101',
        'BEY-DMG-101', 'BEY-MGT-301', 'BEY-SPM-201', 'MKT201', 'LAW202',
    ];
    $topics = [
        ['Q&A with the teachers', 'Open questions on the topics of the week with the teachers.'],
        ['Case study discussion', 'Students from the partner universities discuss a real company case in small groups.'],
        ['Guest talk', 'A short talk by a company partner, followed by questions from the students.'],
        ['Project team meeting', 'The student teams present their progress and plan the next steps.'],
        ['Weekly lecture', 'Live lecture on the topic of the week, with time for questions.'],
        ['Workshop', 'Hands-on workshop: the students work on a task together and share the results.'],
        ['Presentation practice', 'The teams practise their final presentations and get feedback.'],
    ];
    mt_srand(crc32($options['date'] . $options['from'] . $options['until'] . implode(',', $pool)));
    $timezone = new DateTimeZone('Europe/Istanbul');
    $start = new DateTime($options['date'] . ' ' . $options['from'], $timezone);
    $end = new DateTime($options['date'] . ' ' . $options['until'], $timezone);
    $label = $start->format('j M');
    $sessions = [];
    $previous = null;
    for (; $start->getTimestamp() + 45 * MINSECS <= $end->getTimestamp(); $start->modify('+30 minutes')) {
        do {
            $course = $pool[mt_rand(0, count($pool) - 1)];
        } while (count($pool) > 1 && $course === $previous);
        $previous = $course;
        [$topic, $description] = $topics[mt_rand(0, count($topics) - 1)];
        $time = $start->format('H:i');
        // A course can get more than one session: the key only has to be unique.
        $sessions["$course@$time"] = [$time, "Live session $label, $time: $topic", $description];
    }
}

/**
 * Print one step result.
 *
 * @param string $status OK (already fine), SET (changed), WOULD (dry run), WARN
 * @param string $message
 */
function diverse_live_report(string $status, string $message): void {
    cli_writeln(str_pad('[' . $status . ']', 8) . $message);
}

cli_heading('DIVERSE demo live sessions on ' . $options['date'] . ($dryrun ? ' (dry run, nothing is changed)' : ''));

if (!$DB->get_field('modules', 'visible', ['name' => 'bigbluebuttonbn'])) {
    cli_error('The BigBlueButton activity is not enabled: run setup/diverse_setup.php first.');
}
\core\session\manager::set_user(get_admin());

// Teachers moderate, everybody else watches.
$participants = [['selectiontype' => 'all', 'selectionid' => 'all', 'role' => 'viewer']];
foreach (['editingteacher', 'teacher'] as $shortname) {
    $participants[] = ['selectiontype' => 'role', 'selectionid' => (string)$DB->get_field('role', 'id',
        ['shortname' => $shortname], MUST_EXIST), 'role' => 'moderator'];
}

$timezone = new DateTimeZone('Europe/Istanbul');
foreach ($sessions as $key => [$time, $name, $description]) {
    $shortname = explode('@', $key)[0];
    $course = $DB->get_record('course', ['shortname' => $shortname]);
    if (!$course) {
        diverse_live_report('WARN', "Course $shortname does not exist: run setup/diverse_demo_accounts.php first. Skipped.");
        continue;
    }
    $start = (new DateTime($options['date'] . ' ' . $time, $timezone))->getTimestamp();
    $when = $options['date'] . " $time Istanbul time";
    if ($DB->record_exists('bigbluebuttonbn', ['course' => $course->id, 'name' => $name])) {
        diverse_live_report('OK', "$shortname has \"$name\".");
        continue;
    }
    if ($dryrun) {
        diverse_live_report('WOULD', "Add \"$name\" to $shortname on $when.");
        continue;
    }
    create_module((object)[
        'modulename' => 'bigbluebuttonbn',
        'course' => $course->id,
        'section' => 0,
        'visible' => 1,
        'name' => $name,
        'introeditor' => ['text' => "<p>$description</p>", 'format' => FORMAT_HTML, 'itemid' => 0],
        'type' => 0,
        'participants' => json_encode($participants),
        'openingtime' => $start - 15 * MINSECS,
        'closingtime' => $start + 45 * MINSECS,
        'wait' => 0,
        'record' => 0,
        'groupmode' => NOGROUPS,
        'grade' => 0,
        'cmidnumber' => '',
    ]);
    diverse_live_report('SET', "Added \"$name\" to $shortname on $when.");
}

cli_writeln('');
cli_writeln($dryrun ? 'Dry run finished.' : 'DIVERSE demo live sessions finished.');
