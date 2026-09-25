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

/**
 * Tests for the course materials sent to the AI service: only what the student can see, only teacher-written text.
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\local_diverse_assistant\local\course_materials::class)]
final class course_materials_test extends \advanced_testcase {
    /** @var \stdClass Course. */
    private \stdClass $course;

    /** @var \stdClass Student enrolled in the course. */
    private \stdClass $student;

    /** @var \stdClass A visible page. */
    private \stdClass $page;

    #[\Override]
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        set_config('enableavailability', 1);

        $generator = $this->getDataGenerator();
        $this->course = $generator->create_course(['fullname' => 'Data Structures', 'summary' => 'COURSESUMMARY']);
        $this->student = $generator->create_and_enrol($this->course, 'student');

        $this->page = $generator->create_module('page', ['course' => $this->course->id, 'name' => 'Arrays',
            'intro' => 'PAGEINTRO', 'content' => '<p>PAGECONTENT about arrays</p>']);
        $generator->create_module('page', ['course' => $this->course->id, 'name' => 'Secret',
            'content' => 'HIDDENMARKER', 'visible' => 0]);
        $generator->create_module('page', ['course' => $this->course->id, 'name' => 'Later',
            'content' => 'RESTRICTEDMARKER',
            'availability' => json_encode(['op' => '&', 'showc' => [true],
                'c' => [['type' => 'date', 'd' => '>=', 't' => time() + 10 * DAYSECS]]])]);
        $generator->create_module('label', ['course' => $this->course->id, 'intro' => 'LABELMARKER']);
        $generator->create_module('assign', ['course' => $this->course->id, 'intro' => 'ASSIGNINTRO',
            'alwaysshowdescription' => 1]);

        $forum = $generator->create_module('forum', ['course' => $this->course->id, 'intro' => 'FORUMINTRO']);
        $generator->get_plugin_generator('mod_forum')->create_discussion(['course' => $this->course->id,
            'forum' => $forum->id, 'userid' => $this->student->id, 'message' => 'FORUMPOSTMARKER']);
    }

    /**
     * Teacher-written text the student can see is included.
     */
    public function test_includes_visible_teacher_text(): void {
        $this->setUser($this->student);
        $text = course_materials::build(get_course($this->course->id), 0, 60000);

        $this->assertStringContainsString('# Data Structures', $text);
        foreach (['COURSESUMMARY', 'PAGEINTRO', 'PAGECONTENT about arrays', 'LABELMARKER', 'ASSIGNINTRO', 'FORUMINTRO'] as $marker) {
            $this->assertStringContainsString($marker, $text);
        }
        $this->assertStringContainsString('Arrays', $text);
        $this->assertStringNotContainsString('<p>', $text);
    }

    /**
     * Hidden and restricted activities and anything written by students are left out.
     */
    public function test_excludes_hidden_restricted_and_student_text(): void {
        $this->setUser($this->student);
        $text = course_materials::build(get_course($this->course->id), 0, 60000);

        $this->assertStringNotContainsString('HIDDENMARKER', $text);
        $this->assertStringNotContainsString('RESTRICTEDMARKER', $text);
        $this->assertStringNotContainsString('FORUMPOSTMARKER', $text);
    }

    /**
     * The multi-language filter leaves only the student's language.
     */
    public function test_multilang_filter(): void {
        filter_set_global_state('multilang2', TEXTFILTER_ON);
        $this->getDataGenerator()->create_module('page', ['course' => $this->course->id, 'name' => 'Welcome',
            'content' => '{mlang en}ENGLISHMARKER{mlang}{mlang tr}TURKISHMARKER{mlang}']);
        $this->setUser($this->student);

        $text = course_materials::build(get_course($this->course->id), 0, 60000);
        $this->assertStringContainsString('ENGLISHMARKER', $text);
        $this->assertStringNotContainsString('TURKISHMARKER', $text);
    }

    /**
     * Lists, bold text and headings become tidy Markdown-like text, not upper case or bullets on lines of their own.
     */
    public function test_html_becomes_readable_text(): void {
        $this->getDataGenerator()->create_module('page', ['course' => $this->course->id, 'name' => 'Mix',
            'content' => '<blockquote><p>The 4 Ps:</p><ul><li><p><strong>Product:</strong> what you sell</p></li>'
                . '<li><p><strong>Price:</strong> what it costs</p></li></ul><h3>Next step</h3></blockquote>']);
        $this->setUser($this->student);

        $text = course_materials::build(get_course($this->course->id), 0, 60000);
        $this->assertMatchesRegularExpression('/\* \*\*Product:\*\* what you sell/', $text);
        $this->assertStringContainsString('###### Next step', $text);
        $this->assertStringNotContainsString('PRODUCT', $text);
        $this->assertStringNotContainsString('> The 4 Ps', $text);
    }

    /**
     * The activity the student is looking at comes first.
     */
    public function test_current_page_first(): void {
        $this->setUser($this->student);
        $text = course_materials::build(get_course($this->course->id), (int)$this->page->cmid, 60000);

        $current = strpos($text, 'CURRENT PAGE');
        $this->assertNotFalse($current);
        $this->assertLessThan(strpos($text, 'LABELMARKER'), $current);
    }

    /**
     * Long materials are cut off.
     */
    public function test_cut_off(): void {
        $this->setUser($this->student);
        $text = course_materials::build(get_course($this->course->id), 0, 50);

        $this->assertStringContainsString('cut off', $text);
        $this->assertLessThan(200, \core_text::strlen($text));
    }
}
