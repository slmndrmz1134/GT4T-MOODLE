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
 * Tests for the students' own retention choice and the stored conversations.
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\local_diverse_assistant\local\retention::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(\local_diverse_assistant\local\store::class)]
final class retention_test extends \advanced_testcase {
    /**
     * Save a conversation whose last message is some days old.
     *
     * @param int $userid Owner.
     * @param int $courseid Course.
     * @param int $daysold Age of the last message in days.
     * @return int Conversation id.
     */
    private function create_conversation(int $userid, int $courseid, int $daysold): int {
        global $DB;
        $id = store::save_exchange(0, $userid, $courseid, "Question {$daysold}", 'Answer');
        $DB->set_field(store::TABLE_CONVERSATIONS, 'timemodified', time() - $daysold * DAYSECS, ['id' => $id]);
        return $id;
    }

    /**
     * By default nothing is saved.
     */
    public function test_default_is_not_saved(): void {
        $this->resetAfterTest();
        $this->setUser($this->getDataGenerator()->create_user());
        $this->assertSame(retention::NOT_SAVED, retention::get());
        $this->assertFalse(retention::is_saved());
    }

    /**
     * Choosing a shorter period deletes older chats at once; "not saved" deletes all; unknown values are refused.
     */
    public function test_set_applies_at_once(): void {
        global $DB;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        retention::set(retention::FOREVER);
        $old = $this->create_conversation($user->id, $course->id, 10);
        $new = $this->create_conversation($user->id, $course->id, 2);

        $this->assertSame(1, retention::set(7));
        $this->assertFalse($DB->record_exists(store::TABLE_CONVERSATIONS, ['id' => $old]));
        $this->assertFalse($DB->record_exists(store::TABLE_MESSAGES, ['conversationid' => $old]));
        $this->assertTrue($DB->record_exists(store::TABLE_CONVERSATIONS, ['id' => $new]));

        $this->assertSame(1, retention::set(retention::NOT_SAVED));
        $this->assertSame(0, $DB->count_records(store::TABLE_CONVERSATIONS, ['userid' => $user->id]));

        $this->expectException(\invalid_parameter_exception::class);
        retention::set(3);
    }

    /**
     * The scheduled cleanup applies each user's own choice and trims the usage log.
     */
    public function test_cleanup(): void {
        global $DB;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $keeper = $this->getDataGenerator()->create_user();
        $weekly = $this->getDataGenerator()->create_user();

        set_user_preference(retention::PREFERENCE, retention::FOREVER, $keeper);
        set_user_preference(retention::PREFERENCE, 7, $weekly);
        $kept = $this->create_conversation($keeper->id, $course->id, 400);
        $expired = $this->create_conversation($weekly->id, $course->id, 8);
        $recent = $this->create_conversation($weekly->id, $course->id, 1);

        store::log_usage($weekly->id, $course->id, 10, 5);
        $DB->insert_record(store::TABLE_USAGE, (object)['userid' => $weekly->id, 'courseid' => $course->id,
            'prompttokens' => 1, 'completiontokens' => 1, 'timecreated' => time() - 31 * DAYSECS]);

        $this->assertSame(1, retention::cleanup());
        $this->assertTrue($DB->record_exists(store::TABLE_CONVERSATIONS, ['id' => $kept]));
        $this->assertFalse($DB->record_exists(store::TABLE_CONVERSATIONS, ['id' => $expired]));
        $this->assertTrue($DB->record_exists(store::TABLE_CONVERSATIONS, ['id' => $recent]));
        $this->assertSame(1, $DB->count_records(store::TABLE_USAGE));
    }

    /**
     * A conversation is continued, listed and only its owner can read or delete it.
     */
    public function test_conversation_ownership(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $owner = $this->getDataGenerator()->create_user();
        $other = $this->getDataGenerator()->create_user();

        $id = store::save_exchange(0, $owner->id, $course->id, 'First question', 'First answer');
        $this->assertSame($id, store::save_exchange($id, $owner->id, $course->id, 'Second', 'Answer'));
        $this->assertCount(4, store::get_messages($id));
        $this->assertCount(2, store::get_messages($id, 2));
        $this->assertSame('Second', store::get_messages($id, 2)[0]->content);
        $this->assertSame('First question', store::list_conversations($owner->id, $course->id)[0]->title);

        $this->assertFalse(store::delete_conversation($id, $other->id));
        try {
            store::get_conversation($id, $other->id);
            $this->fail('An exception was expected.');
        } catch (\moodle_exception $e) {
            $this->assertSame('errornotfound', $e->errorcode);
        }
        $this->assertTrue(store::delete_conversation($id, $owner->id));
    }
}
