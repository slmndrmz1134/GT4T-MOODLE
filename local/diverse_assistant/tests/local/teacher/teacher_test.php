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

namespace local_diverse_assistant\local\teacher;

use local_diverse_assistant\external\get_messages;
use local_diverse_assistant\external\proposal_action;
use local_diverse_assistant\local\chat_service;
use local_diverse_assistant\local\retention;

/**
 * Tests for the teacher mode: the course content the model sees, proposals, applying and undoing them.
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(course_content::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(proposals::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(applier::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(tools::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(\local_diverse_assistant\external\proposal_action::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(\local_diverse_assistant\local\chat_service::class)]
final class teacher_test extends \advanced_testcase {
    /** @var \stdClass The course. */
    private \stdClass $course;

    /** @var \stdClass Editing teacher. */
    private \stdClass $teacher;

    /** @var \stdClass Student. */
    private \stdClass $student;

    /** @var \stdClass The page. */
    private \stdClass $page;

    /** @var \stdClass A hidden text and media area. */
    private \stdClass $label;

    /** @var \stdClass An assignment. */
    private \stdClass $assign;

    /** Page text with two languages and an image. */
    private const PAGE_CONTENT = '<p>{mlang en}Contracts{mlang}{mlang tr}Sözleşmeler{mlang}</p>'
        . '<p><img src="@@PLUGINFILE@@/chart.png" alt="Chart"></p>';

    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $this->course = $generator->create_course(['numsections' => 3, 'enablecompletion' => 1]);
        $this->teacher = $generator->create_and_enrol($this->course, 'editingteacher');
        $this->student = $generator->create_and_enrol($this->course, 'student');
        $this->page = $generator->create_module('page', ['course' => $this->course->id, 'section' => 1,
            'name' => 'Law basics', 'intro' => '<p>About the law.</p>', 'content' => self::PAGE_CONTENT,
            'printintro' => 1, 'printlastmodified' => 0]);
        $this->label = $generator->create_module('label', ['course' => $this->course->id, 'section' => 1,
            'intro' => '<p>Secret teacher note</p>', 'visible' => 0]);
        $this->assign = $generator->create_module('assign', ['course' => $this->course->id, 'section' => 2,
            'name' => 'Essay', 'intro' => '<p>Write an essay.</p>', 'assignsubmission_onlinetext_enabled' => 1]);
        $forum = $generator->create_module('forum', ['course' => $this->course->id, 'section' => 2]);
        $discussion = $generator->get_plugin_generator('mod_forum')->create_discussion(['course' => $this->course->id,
            'forum' => $forum->id, 'userid' => $this->student->id, 'message' => 'Student private post']);
        $this->assertNotEmpty($discussion);
    }

    /**
     * What the model can change: everything it saw in full.
     *
     * @return array
     */
    private function editable(): array {
        $content = course_content::build($this->course, 0, 60000);
        return ['cmids' => $content['cmids'], 'sectionids' => $content['sectionids']];
    }

    /**
     * Store proposals from tool calls as the teacher.
     *
     * @param array $calls List of [name, arguments].
     * @return array records and problems.
     */
    private function propose(array $calls): array {
        $toolcalls = array_map(fn($call) => ['name' => $call[0], 'arguments' => $call[1]], $calls);
        return proposals::create_from_tool_calls($this->course, (int)$this->teacher->id, $toolcalls, $this->editable());
    }

    /**
     * The stored page record.
     *
     * @return \stdClass
     */
    private function page_record(): \stdClass {
        global $DB;
        return $DB->get_record('page', ['id' => $this->page->id]);
    }

    /**
     * Teachers see the stored texts, with every language, links and hidden activities, but nothing students wrote.
     */
    public function test_course_content(): void {
        $this->setUser($this->teacher);
        $content = course_content::build($this->course, (int)$this->page->cmid, 60000);

        $this->assertStringContainsString('{mlang en}Contracts{mlang}{mlang tr}Sözleşmeler{mlang}', $content['text']);
        $this->assertStringContainsString('@@PLUGINFILE@@/chart.png', $content['text']);
        $this->assertStringContainsString("[cmid={$this->page->cmid}]", $content['text']);
        $this->assertStringContainsString('CURRENT ACTIVITY', $content['text']);
        $this->assertStringContainsString('Secret teacher note', $content['text']);
        $this->assertStringContainsString('hidden from students', $content['text']);
        $this->assertStringContainsString('[section=1]', $content['text']);
        $this->assertStringNotContainsString('Student private post', $content['text']);
        $this->assertStringNotContainsString($this->student->email, $content['text']);
        $this->assertContains((int)$this->page->cmid, $content['cmids']);
        $this->assertContains((int)$this->assign->cmid, $content['cmids']);
        $this->assertCount(4, $content['sectionids']);

        // Too long: the current activity comes first, the others are listed without their texts.
        $short = course_content::build($this->course, (int)$this->page->cmid, 300);
        $this->assertSame([(int)$this->page->cmid], $short['cmids']);
        $this->assertStringContainsString('Texts not included', $short['text']);
    }

    /**
     * Only teachers who can edit the course get the authoring assistant.
     */
    public function test_teacher_mode(): void {
        $teacher = $this->getDataGenerator()->create_and_enrol($this->course, 'teacher');
        $this->setUser($this->teacher);
        $this->assertTrue(chat_service::is_teacher_mode($this->course));
        $this->setUser($teacher);
        $this->assertFalse(chat_service::is_teacher_mode($this->course));
        $this->setUser($this->student);
        $this->assertFalse(chat_service::is_teacher_mode($this->course));
    }

    /**
     * Tool calls are checked; only valid proposals are stored, with cleaned texts.
     */
    public function test_proposal_validation(): void {
        $this->setUser($this->teacher);
        $result = $this->propose([
            [tools::NEW_PAGE, ['section' => 2, 'name' => ' Types  of contracts ', 'note' => 'A new page.',
                'content' => '<p>Contracts</p><script>alert(1)</script>']],
            [tools::NEW_PAGE, null],
            [tools::NEW_PAGE, ['section' => 99, 'name' => 'X', 'content' => '<p>X</p>', 'note' => '']],
            [tools::UPDATE_ACTIVITY, ['cmid' => 999999, 'name' => 'X', 'note' => '']],
            [tools::UPDATE_ACTIVITY, ['cmid' => $this->page->cmid, 'name' => 'Law basics', 'note' => '']],
            ['delete_everything', ['cmid' => $this->page->cmid]],
        ]);
        $this->assertCount(1, $result['records']);
        $this->assertCount(5, $result['problems']);
        $proposed = json_decode($result['records'][0]->proposed, true);
        $this->assertSame('Types of contracts', $proposed['name']);
        $this->assertStringNotContainsString('script', $proposed['content']);
        $this->assertSame(proposals::STATUS_PENDING, $result['records'][0]->status);

        // Too many calls in one answer.
        $calls = array_fill(0, proposals::MAX_PER_ANSWER + 2, [tools::NEW_LABEL, ['section' => 1, 'content' => '<p>A</p>',
            'note' => '']]);
        $result = $this->propose($calls);
        $this->assertCount(proposals::MAX_PER_ANSWER, $result['records']);
        $this->assertCount(1, $result['problems']);

        // An activity the model did not see in full cannot be changed.
        $result = proposals::create_from_tool_calls($this->course, (int)$this->teacher->id,
            [['name' => tools::UPDATE_ACTIVITY, 'arguments' => ['cmid' => $this->assign->cmid, 'name' => 'New', 'note' => '']]],
            ['cmids' => [], 'sectionids' => []]);
        $this->assertCount(0, $result['records']);
        $this->assertStringContainsString('Essay', $result['problems'][0]);
    }

    /**
     * A proposed page is created with one click and deleted again by undo.
     */
    public function test_new_page_apply_and_undo(): void {
        global $DB;
        $this->setUser($this->teacher);
        [$record] = $this->propose([[tools::NEW_PAGE, ['section' => 2, 'name' => 'Types of contracts',
            'content' => '<p>Express and implied contracts.</p>', 'description' => '<p>Overview</p>', 'note' => 'n']]])['records'];

        $export = proposals::export($record);
        $this->assertTrue($export['canapply']);
        $this->assertStringContainsString('add=page', $export['formurl']);
        $this->assertSame('/course/modedit.php', json_decode($export['prefill'], true)['match']['path']);

        applier::apply($record);
        $this->assertSame(proposals::STATUS_APPLIED, $record->status);
        $cm = get_fast_modinfo($this->course)->get_cm($record->cmid);
        $this->assertSame('page', $cm->modname);
        $this->assertEquals(2, $cm->sectionnum);
        $page = $DB->get_record('page', ['id' => $cm->instance]);
        $this->assertSame('Types of contracts', $page->name);
        $this->assertSame('<p>Express and implied contracts.</p>', $page->content);
        $this->assertSame('<p>Overview</p>', $page->intro);
        $this->assertStringContainsString('/mod/page/view.php', proposals::export($record)['viewurl']);

        // Handled proposals cannot be applied again.
        try {
            applier::apply($record);
            $this->fail('An exception was expected.');
        } catch (\moodle_exception $e) {
            $this->assertSame('apply_error_status', $e->errorcode);
        }

        applier::undo($record);
        $this->assertSame(proposals::STATUS_UNDONE, $record->status);
        $this->assertFalse($DB->record_exists('course_modules', ['id' => $record->cmid]));
    }

    /**
     * Changing a page keeps its settings and its images; undo restores the old texts.
     */
    public function test_update_page_apply_and_undo(): void {
        $this->setUser($this->teacher);
        $before = $this->page_record();
        $content = '<p>{mlang en}Contracts are agreements.{mlang}{mlang tr}Sözleşmeler anlaşmalardır.{mlang}</p>'
            . '<p><img src="@@PLUGINFILE@@/chart.png" alt="Chart"></p>';
        [$record] = $this->propose([[tools::UPDATE_ACTIVITY, ['cmid' => $this->page->cmid, 'name' => 'Law basics (EN/TR)',
            'content' => $content, 'note' => 'Bilingual']]])['records'];

        $export = proposals::export($record);
        $this->assertTrue($export['canapply']);
        $this->assertSame(['name', 'content'], array_column($export['changes'], 'field'));
        $this->assertStringContainsString('local-diverse-assistant-lang', $export['changes'][1]['after']);

        applier::apply($record);
        $after = $this->page_record();
        $this->assertSame('Law basics (EN/TR)', $after->name);
        // The proposed HTML was cleaned like any submitted text; the image link is kept.
        $this->assertSame(json_decode($record->proposed, true)['content'], $after->content);
        $this->assertStringContainsString('src="@@PLUGINFILE@@/chart.png"', $after->content);
        $this->assertStringContainsString('{mlang tr}Sözleşmeler anlaşmalardır.{mlang}', $after->content);
        $this->assertSame($before->intro, $after->intro);
        $this->assertSame($before->displayoptions, $after->displayoptions);
        $this->assertEquals($before->display, $after->display);
        $this->assertEquals($before->revision + 1, $after->revision);

        applier::undo($record);
        $restored = $this->page_record();
        $this->assertSame($before->name, $restored->name);
        $this->assertSame($before->content, $restored->content);
        $this->assertSame($before->displayoptions, $restored->displayoptions);
    }

    /**
     * Nothing is overwritten when the text changed after the proposal was made or after it was applied.
     */
    public function test_changed_in_the_meantime(): void {
        global $DB;
        $this->setUser($this->teacher);
        [$first, $second] = $this->propose([
            [tools::UPDATE_ACTIVITY, ['cmid' => $this->page->cmid, 'content' => '<p>First</p>', 'note' => '']],
            [tools::UPDATE_ACTIVITY, ['cmid' => $this->page->cmid, 'content' => '<p>Second</p>', 'note' => '']],
        ])['records'];

        applier::apply($first);
        try {
            applier::apply($second);
            $this->fail('An exception was expected.');
        } catch (\moodle_exception $e) {
            $this->assertSame('apply_error_changed', $e->errorcode);
        }
        $this->assertSame('<p>First</p>', $this->page_record()->content);

        // Someone edits the page after the proposal was applied: undo would lose their change.
        $DB->set_field('page', 'content', '<p>Edited by hand</p>', ['id' => $this->page->id]);
        try {
            applier::undo($first);
            $this->fail('An exception was expected.');
        } catch (\moodle_exception $e) {
            $this->assertSame('undo_error_changed', $e->errorcode);
        }
        $this->assertSame('<p>Edited by hand</p>', $this->page_record()->content);
    }

    /**
     * Text and media areas and sections are changed with one click too.
     */
    public function test_label_and_section(): void {
        global $DB;
        $this->setUser($this->teacher);
        $section = get_fast_modinfo($this->course)->get_section_info(1);
        [$label, $sectionproposal] = $this->propose([
            [tools::UPDATE_ACTIVITY, ['cmid' => $this->label->cmid, 'description' => '<p>Welcome!</p>', 'note' => '']],
            [tools::UPDATE_SECTION, ['section' => 1, 'name' => 'Week 1: Contracts', 'summary' => '<p>We start.</p>',
                'note' => '']],
        ])['records'];

        applier::apply($label);
        $this->assertSame('<p>Welcome!</p>', $DB->get_field('label', 'intro', ['id' => $this->label->id]));

        applier::apply($sectionproposal);
        $record = $DB->get_record('course_sections', ['id' => $section->id]);
        $this->assertSame('Week 1: Contracts', $record->name);
        $this->assertSame('<p>We start.</p>', $record->summary);

        applier::undo($sectionproposal);
        $record = $DB->get_record('course_sections', ['id' => $section->id]);
        $this->assertNull($record->name);
        $this->assertSame($section->summary, $record->summary);
    }

    /**
     * Activities with settings only their form handles are changed in the form.
     */
    public function test_assignment_is_changed_in_its_form(): void {
        $this->setUser($this->teacher);
        [$record] = $this->propose([[tools::UPDATE_ACTIVITY, ['cmid' => $this->assign->cmid,
            'description' => '<p>Write a 500-word essay.</p>', 'note' => '']]])['records'];
        $export = proposals::export($record);
        $this->assertFalse($export['canapply']);
        $this->assertTrue($export['canform']);
        $this->assertStringContainsString('update=' . $this->assign->cmid, $export['formurl']);
        $prefill = json_decode($export['prefill'], true);
        $this->assertSame('id_introeditor', $prefill['fields'][0]['id']);
        $this->assertSame('<p>Write a 500-word essay.</p>', $prefill['fields'][0]['value']);

        try {
            applier::apply($record);
            $this->fail('An exception was expected.');
        } catch (\moodle_exception $e) {
            $this->assertSame('apply_error_formonly', $e->errorcode);
        }
    }

    /**
     * Only the teacher a change was proposed to can handle it; the web service reports why an action failed.
     */
    public function test_proposal_action(): void {
        $this->setUser($this->teacher);
        [$record] = $this->propose([[tools::NEW_LABEL, ['section' => 1, 'content' => '<p>Hello</p>', 'note' => '']]])['records'];

        $other = $this->getDataGenerator()->create_and_enrol($this->course, 'editingteacher');
        $this->setUser($other);
        try {
            proposal_action::execute($record->id, 'apply');
            $this->fail('An exception was expected.');
        } catch (\moodle_exception $e) {
            $this->assertSame('errornotfound', $e->errorcode);
        }

        $this->setUser($this->teacher);
        $result = proposal_action::execute($record->id, 'discard');
        $result = \core_external\external_api::clean_returnvalue(proposal_action::execute_returns(), $result);
        $this->assertTrue($result['ok']);
        $this->assertSame(proposals::STATUS_DISCARDED, $result['proposal']['status']);

        $result = proposal_action::execute($record->id, 'apply');
        $this->assertFalse($result['ok']);
        $this->assertNotEmpty($result['message']);
    }

    /**
     * In teacher mode the model gets the tools; its tool calls become proposal cards, saved with the chat.
     */
    public function test_chat_in_teacher_mode(): void {
        global $CFG;
        require_once($CFG->libdir . '/filelib.php');
        set_config('enabled', 1, 'local_diverse_assistant');
        set_config('apikey_openai', \core\encryption::encrypt('sk-test'), 'local_diverse_assistant');
        set_config('provider', 'openai', 'local_diverse_assistant');
        set_config('model', 'gpt-6-luna', 'local_diverse_assistant');
        $this->setUser($this->teacher);
        retention::set(30);

        $request = chat_service::prepare($this->course, (int)$this->page->cmid, 0, 'Add a page about contracts', []);
        $this->assertTrue($request->teacher);
        $this->assertStringContainsString('course authoring assistant', $request->messages[0]['content']);
        $this->assertStringContainsString('{mlang en}Contracts{mlang}', $request->messages[0]['content']);

        \curl::mock_response(implode("\n\n", [
            'data: ' . json_encode(['choices' => [['delta' => ['content' => 'I propose a new page.']]]]),
            'data: ' . json_encode(['choices' => [['delta' => ['tool_calls' => [['index' => 0, 'id' => 'c1',
                'type' => 'function', 'function' => ['name' => tools::NEW_PAGE, 'arguments' => json_encode([
                    'section' => 1, 'name' => 'Contracts', 'content' => '<p>Text</p>', 'note' => 'New page'])]]]]]]]),
            'data: ' . json_encode(['choices' => [['delta' => [], 'finish_reason' => 'tool_calls']]]),
            'data: [DONE]',
        ]) . "\n\n");
        $statuses = [];
        $result = chat_service::complete($request, null, function (string $status) use (&$statuses): void {
            $statuses[] = $status;
        });

        $this->assertSame('I propose a new page.', $result['text']);
        $this->assertCount(1, $result['proposals']);
        $this->assertSame('newpage', $result['proposals'][0]['action']);
        $this->assertSame([], $result['problems']);
        $this->assertCount(1, $statuses);
        $this->assertStringContainsString('new page "Contracts": pending', $result['historytext']);

        // The saved chat shows the card again and reminds the model of it.
        $messages = get_messages::execute($result['conversationid']);
        $this->assertCount(1, $messages['messages'][1]['proposals']);
        $next = chat_service::prepare($this->course, 0, $result['conversationid'], 'Thanks', []);
        $this->assertStringContainsString('new page "Contracts": pending', $next->messages[2]['content']);

        // Students keep the study assistant.
        $this->setUser($this->student);
        $request = chat_service::prepare($this->course, 0, 0, 'Question', []);
        $this->assertFalse($request->teacher);
        $this->assertStringNotContainsString('Secret teacher note', $request->messages[0]['content']);
    }
}
