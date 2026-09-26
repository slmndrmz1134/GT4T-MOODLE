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
 * Splits a server-sent events stream into the data of each event.
 *
 * Chunks from the network can end anywhere, even in the middle of a line, so incomplete lines are kept until the rest
 * arrives. Only "data:" fields are used; comments and the event, id and retry fields are ignored. Any other text is kept
 * apart: some services write a plain JSON error body into the stream when they fail in the middle of an answer.
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sse_parser {
    /** @var string Received text that does not end with a line break yet. */
    private string $buffer = '';

    /** @var string[] Data lines of the event being read. */
    private array $datalines = [];

    /** @var string Lines that are not part of the event stream format. */
    private string $other = '';

    /** Most text kept from lines that are not part of the event stream format, in bytes. */
    private const MAX_OTHER = 65536;

    /**
     * Add received text.
     *
     * @param string $chunk Text as it came from the network.
     * @return string[] Data of every event completed by this chunk.
     */
    public function feed(string $chunk): array {
        $this->buffer .= $chunk;
        $events = [];
        while (($pos = strpos($this->buffer, "\n")) !== false) {
            $line = rtrim(substr($this->buffer, 0, $pos), "\r");
            $this->buffer = substr($this->buffer, $pos + 1);
            $this->read_line($line, $events);
        }
        return $events;
    }

    /**
     * The stream has ended: return the last event if it was not followed by an empty line.
     *
     * @return string[]
     */
    public function finish(): array {
        $events = [];
        if ($this->buffer !== '') {
            $this->read_line(rtrim($this->buffer, "\r"), $events);
            $this->buffer = '';
        }
        $this->read_line('', $events);
        return $events;
    }

    /**
     * Text of the lines that were not part of the event stream format, e.g. a plain JSON error body.
     *
     * @return string
     */
    public function get_other_text(): string {
        return $this->other;
    }

    /**
     * Handle one complete line.
     *
     * @param string $line The line without its line break.
     * @param string[] $events Completed events are added here.
     */
    private function read_line(string $line, array &$events): void {
        if ($line === '') {
            if ($this->datalines) {
                $events[] = implode("\n", $this->datalines);
                $this->datalines = [];
            }
            return;
        }
        if (str_starts_with($line, 'data:')) {
            $value = substr($line, 5);
            // The format allows one optional space after the colon.
            $this->datalines[] = str_starts_with($value, ' ') ? substr($value, 1) : $value;
        } else if (!preg_match('/^(:|event:|id:|retry:)/', $line) && strlen($this->other) < self::MAX_OTHER) {
            $this->other .= $line . "\n";
        }
    }
}
