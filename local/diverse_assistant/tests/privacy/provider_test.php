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

namespace local_diverse_assistant\privacy;

use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;
use core_privacy\tests\provider_testcase;
use local_diverse_assistant\local\retention;
use local_diverse_assistant\local\store;

/**
 * Tests for the privacy provider.
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\local_diverse_assistant\privacy\provider::class)]
final class provider_test extends provider_testcase {
    /** @var \stdClass Course. */
    private \stdClass $course;

    /** @var \stdClass First student. */
    private \stdClass $user1;

    /** @var \stdClass Second student. */
    private \stdClass $user2;

    #[\Override]
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->course = $this->getDataGenerator()->create_course();
        $this->user1 = $this->getDataGenerator()->create_and_enrol($this->course, 'student');
        $this->user2 = $this->getDataGenerator()->create_and_enrol($this->course, 'student');
        store::save_exchange(0, $this->user1->id, $this->course->id, 'Question one', 'Answer one');
        store::log_usage($this->user1->id, $this->course->id, 10, 5);
        store::save_exchange(0, $this->user2->id, $this->course->id, 'Question two', 'Answer two');
    }

    /**
     * Contexts and users with data are found.
     */
    public function test_contexts_and_users(): void {
        $context = \context_course::instance($this->course->id);
        $this->assertEquals([$context->id], provider::get_contexts_for_userid($this->user1->id)->get_contextids());

        $userlist = new userlist($context, 'local_diverse_assistant');
        provider::get_users_in_context($userlist);
        $this->assertEqualsCanonicalizing([$this->user1->id, $this->user2->id], $userlist->get_userids());
    }

    /**
     * A user's chats and preferences are exported.
     */
    public function test_export(): void {
        $context = \context_course::instance($this->course->id);
        set_user_preference(retention::PREFERENCE, 30, $this->user1);
        $this->export_context_data_for_user($this->user1->id, $context, 'local_diverse_assistant');
        provider::export_user_preferences($this->user1->id);

        $writer = writer::with_context($context);
        $data = $writer->get_data([get_string('pluginname', 'local_diverse_assistant')]);
        $this->assertSame('Question one', $data->conversations[0]['title']);
        $this->assertSame('Answer one', $data->conversations[0]['messages'][1]['content']);
        $this->assertSame(1, $data->questionsinlast30days);
        $this->assertNotEmpty(writer::with_context(\context_system::instance())->get_user_preferences('local_diverse_assistant'));
    }

    /**
     * Deleting one user's data leaves the other user's data.
     */
    public function test_delete_for_user(): void {
        global $DB;
        $context = \context_course::instance($this->course->id);
        provider::delete_data_for_user(new approved_contextlist($this->user1, 'local_diverse_assistant', [$context->id]));

        $this->assertSame(0, $DB->count_records(store::TABLE_CONVERSATIONS, ['userid' => $this->user1->id]));
        $this->assertSame(0, $DB->count_records(store::TABLE_USAGE, ['userid' => $this->user1->id]));
        $this->assertSame(1, $DB->count_records(store::TABLE_CONVERSATIONS, ['userid' => $this->user2->id]));
    }

    /**
     * Deleting for a list of users and for the whole context.
     */
    public function test_delete_for_users_and_context(): void {
        global $DB;
        $context = \context_course::instance($this->course->id);
        provider::delete_data_for_users(new approved_userlist($context, 'local_diverse_assistant', [$this->user2->id]));
        $this->assertSame(0, $DB->count_records(store::TABLE_CONVERSATIONS, ['userid' => $this->user2->id]));
        $this->assertSame(1, $DB->count_records(store::TABLE_CONVERSATIONS, ['userid' => $this->user1->id]));

        provider::delete_data_for_all_users_in_context($context);
        $this->assertSame(0, $DB->count_records(store::TABLE_CONVERSATIONS));
        $this->assertSame(0, $DB->count_records(store::TABLE_MESSAGES));
        $this->assertSame(0, $DB->count_records(store::TABLE_USAGE));
    }
}
