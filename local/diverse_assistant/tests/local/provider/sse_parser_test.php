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

namespace local_diverse_assistant\local\provider;

/**
 * Tests for the server-sent events splitter.
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\local_diverse_assistant\local\provider\sse_parser::class)]
final class sse_parser_test extends \basic_testcase {
    /**
     * Events split across network chunks, with Windows line breaks, are put back together.
     */
    public function test_chunks_split_anywhere(): void {
        $parser = new sse_parser();
        $events = array_merge(
            $parser->feed("data: {\"a\":1}\n\nda"),
            $parser->feed("ta: [DONE]\r\n"),
            $parser->feed("\r\n"),
            $parser->finish(),
        );
        $this->assertSame(['{"a":1}', '[DONE]'], $events);
    }

    /**
     * Several data lines form one event; comments and other fields are ignored.
     */
    public function test_multiline_data_and_ignored_fields(): void {
        $parser = new sse_parser();
        $events = array_merge($parser->feed(": keep-alive\nevent: message\nid: 7\ndata: a\ndata:b\n\n"), $parser->finish());
        $this->assertSame(["a\nb"], $events);
    }

    /**
     * An event not followed by an empty line is returned when the stream ends.
     */
    public function test_last_event_without_blank_line(): void {
        $parser = new sse_parser();
        $this->assertSame([], $parser->feed('data: last'));
        $this->assertSame(['last'], $parser->finish());
    }
}
