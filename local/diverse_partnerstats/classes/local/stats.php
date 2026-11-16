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

namespace local_diverse_partnerstats\local;

/**
 * DIVERSE partner statistics - The figures of one partner university
 *
 * A partner is a tool_mutenancy tenant; its students are the accounts of the tenant. Their courses are every course
 * they are enrolled in, also the shared ones outside the partner's category. Only Moodle's own records are read:
 * enrolments, course completions, course totals of the gradebook and the times of last access.
 *
 * @package    local_diverse_partnerstats
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class stats {
    /** @var int Days without access after which a student is no longer active. */
    const ACTIVE_DAYS = 30;

    /**
     * Whether partner universities are in use on this site.
     *
     * @return bool
     */
    public static function enabled(): bool {
        return function_exists('mutenancy_is_active') && mutenancy_is_active() && class_exists(\context_tenant::class);
    }

    /**
     * The partners a user may see the statistics of: every partner for a site administrator, their own for a
     * partner administrator who can view the reports of the partner's category.
     *
     * @param int|null $userid Default the current user.
     * @return \stdClass[] id => id, name, categoryid
     */
    public static function viewable_tenants(?int $userid = null): array {
        global $DB, $USER;

        if (!self::enabled()) {
            return [];
        }
        $userid = $userid ?? $USER->id;
        $tenants = $DB->get_records('tool_mutenancy_tenant', ['archived' => 0], 'name ASC', 'id, name, categoryid');
        if (has_capability('moodle/site:config', \context_system::instance(), $userid)) {
            return $tenants;
        }
        $own = (int)$DB->get_field('user', 'tenantid', ['id' => $userid]);
        if (!$own || empty($tenants[$own])) {
            return [];
        }
        $tenant = $tenants[$own];
        $category = \context_coursecat::instance($tenant->categoryid, IGNORE_MISSING);
        if (!$category || !has_capability('tool/mutenancy:view', \context_tenant::instance($tenant->id), $userid) ||
                !has_capability('moodle/site:viewreports', $category, $userid)) {
            return [];
        }
        return [$tenant->id => $tenant];
    }

    /**
     * Everything the page shows about a partner.
     *
     * @param \stdClass $tenant id, name
     * @return array totals, courses, students
     */
    public static function tenant(\stdClass $tenant): array {
        global $DB;

        $now = time();
        $since = $now - self::ACTIVE_DAYS * DAYSECS;

        // The partner's students: its accounts with a student role in a course (teachers and managers are not).
        $isstudent = "EXISTS (SELECT 1
                                FROM {role_assignments} ra
                                JOIN {role} r ON r.id = ra.roleid AND r.archetype = 'student'
                                JOIN {context} ctx ON ctx.id = ra.contextid AND ctx.contextlevel = :courselevel
                               WHERE ra.userid = u.id)";
        $students = $DB->get_records_sql(
            "SELECT u.id, u.firstname, u.lastname, u.firstnamephonetic, u.lastnamephonetic, u.middlename,
                    u.alternatename, u.email, u.lastaccess, u.suspended
               FROM {user} u
              WHERE u.tenantid = :tenantid AND u.deleted = 0 AND $isstudent
           ORDER BY u.lastname ASC, u.firstname ASC",
            ['tenantid' => $tenant->id, 'courselevel' => CONTEXT_COURSE]
        );

        // Their active enrolments as students, with completion and the course total of the gradebook.
        $rows = $DB->get_recordset_sql(
            "SELECT ue.id, ue.userid, c.id AS courseid, c.fullname, c.shortname,
                    cc.timecompleted, la.timeaccess,
                    gg.finalgrade, gi.grademin, gi.grademax
               FROM {user} u
               JOIN {user_enrolments} ue ON ue.userid = u.id AND ue.status = :active
               JOIN {enrol} e ON e.id = ue.enrolid AND e.status = :enabled
               JOIN {course} c ON c.id = e.courseid
               JOIN {context} cctx ON cctx.instanceid = c.id AND cctx.contextlevel = :courselevel2
          LEFT JOIN {course_completions} cc ON cc.course = c.id AND cc.userid = u.id
          LEFT JOIN {user_lastaccess} la ON la.courseid = c.id AND la.userid = u.id
          LEFT JOIN {grade_items} gi ON gi.courseid = c.id AND gi.itemtype = 'course'
          LEFT JOIN {grade_grades} gg ON gg.itemid = gi.id AND gg.userid = u.id
              WHERE u.tenantid = :tenantid AND u.deleted = 0
                AND EXISTS (SELECT 1
                              FROM {role_assignments} sra
                              JOIN {role} sr ON sr.id = sra.roleid AND sr.archetype = 'student'
                             WHERE sra.userid = u.id AND sra.contextid = cctx.id)",
            ['active' => ENROL_USER_ACTIVE, 'enabled' => ENROL_INSTANCE_ENABLED, 'tenantid' => $tenant->id,
                'courselevel2' => CONTEXT_COURSE]
        );

        $courses = [];
        $perstudent = [];
        $seen = [];
        foreach ($rows as $row) {
            // A student enrolled twice in a course (two methods) counts once.
            $key = $row->userid . '-' . $row->courseid;
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;

            $percent = null;
            if ($row->finalgrade !== null && $row->grademax > $row->grademin) {
                $percent = 100 * ($row->finalgrade - $row->grademin) / ($row->grademax - $row->grademin);
            }
            $course = $courses[$row->courseid] ?? (object)[
                'id' => (int)$row->courseid, 'name' => format_string($row->fullname, true,
                    ['context' => \context_course::instance($row->courseid)]),
                'shortname' => $row->shortname, 'enrolled' => 0, 'active' => 0, 'completed' => 0, 'grades' => [],
            ];
            $course->enrolled++;
            $course->active += ($row->timeaccess && $row->timeaccess >= $since) ? 1 : 0;
            $course->completed += $row->timecompleted ? 1 : 0;
            if ($percent !== null) {
                $course->grades[] = $percent;
            }
            $courses[$row->courseid] = $course;

            $student = $perstudent[$row->userid] ?? (object)['courses' => 0, 'completed' => 0, 'grades' => []];
            $student->courses++;
            $student->completed += $row->timecompleted ? 1 : 0;
            if ($percent !== null) {
                $student->grades[] = $percent;
            }
            $perstudent[$row->userid] = $student;
        }
        $rows->close();

        $average = fn(array $values) => $values ? round(array_sum($values) / count($values)) : null;

        $courselist = array_values(array_map(fn($course) => [
            'name' => $course->name,
            'shortname' => $course->shortname,
            'url' => (new \core\url('/course/view.php', ['id' => $course->id]))->out(false),
            'reporturl' => (new \core\url('/report/completion/index.php', ['course' => $course->id]))->out(false),
            'gradesurl' => (new \core\url('/grade/report/grader/index.php', ['id' => $course->id]))->out(false),
            'enrolled' => $course->enrolled,
            'active' => $course->active,
            'completed' => $course->completed,
            'completedpercent' => (int)round(100 * $course->completed / max(1, $course->enrolled)),
            'average' => $average($course->grades),
        ], $courses));
        usort($courselist, fn($a, $b) => $b['enrolled'] <=> $a['enrolled'] ?: strcmp($a['name'], $b['name']));

        $studentlist = [];
        $activecount = 0;
        $allgrades = [];
        foreach ($students as $user) {
            $mine = $perstudent[$user->id] ?? (object)['courses' => 0, 'completed' => 0, 'grades' => []];
            $active = $user->lastaccess >= $since;
            $activecount += $active ? 1 : 0;
            array_push($allgrades, ...$mine->grades);
            $studentlist[] = [
                'name' => fullname($user),
                'email' => $user->email,
                'url' => (new \core\url('/user/profile.php', ['id' => $user->id]))->out(false),
                'lastaccess' => $user->lastaccess ? format_time($now - $user->lastaccess) : get_string('never'),
                'active' => $active,
                'suspended' => (bool)$user->suspended,
                'courses' => $mine->courses,
                'completed' => $mine->completed,
                'average' => $average($mine->grades),
            ];
        }

        $enrolments = array_sum(array_column($courselist, 'enrolled'));
        $completions = array_sum(array_column($courselist, 'completed'));
        return [
            'totals' => [
                'students' => count($students),
                'active' => $activecount,
                'courses' => count($courselist),
                'enrolments' => $enrolments,
                'completionpercent' => $enrolments ? (int)round(100 * $completions / $enrolments) : null,
                'average' => $average($allgrades),
            ],
            'courses' => $courselist,
            'students' => $studentlist,
        ];
    }
}
