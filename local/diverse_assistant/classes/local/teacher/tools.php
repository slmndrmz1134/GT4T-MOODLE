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

/**
 * The tools the model can call in teacher mode. Every tool only proposes a change: the teacher reviews and applies it.
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tools {
    /** Tool: a new page. */
    public const NEW_PAGE = 'propose_new_page';

    /** Tool: a new text and media area (label). */
    public const NEW_LABEL = 'propose_new_label';

    /** Tool: new texts for an activity. */
    public const UPDATE_ACTIVITY = 'propose_activity_update';

    /** Tool: new texts for a section. */
    public const UPDATE_SECTION = 'propose_section_update';

    /**
     * Tool definitions: name, description and JSON schema of the arguments.
     *
     * @return array[]
     */
    public static function definitions(): array {
        $note = ['type' => 'string', 'description' => 'One short sentence for the teacher, in their language: what this '
            . 'proposal changes and why.'];
        $section = ['type' => 'integer', 'description' => 'Section number, as in [section=N].'];
        $html = 'HTML: <p>, <h3>, <h4>, <ul>, <ol>, <li>, <strong>, <em>, <a href>, <table>, <blockquote>. No scripts, '
            . 'styles or classes. May contain {mlang xx}...{mlang} blocks.';

        return [
            [
                'name' => self::NEW_PAGE,
                'description' => 'Propose a new Page (a web page of text) at the end of a course section. Nothing is '
                    . 'created until the teacher approves it.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'section' => $section,
                        'name' => ['type' => 'string', 'description' => 'Title of the page, plain text.'],
                        'content' => ['type' => 'string', 'description' => 'The complete page content. ' . $html],
                        'description' => ['type' => 'string', 'description' => 'Optional short description. ' . $html],
                        'note' => $note,
                    ],
                    'required' => ['section', 'name', 'content', 'note'],
                ],
            ],
            [
                'name' => self::NEW_LABEL,
                'description' => 'Propose a new "Text and media area" (a short text shown directly on the course page, '
                    . 'e.g. an introduction or instructions) at the end of a course section.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'section' => $section,
                        'content' => ['type' => 'string', 'description' => 'The text. ' . $html],
                        'note' => $note,
                    ],
                    'required' => ['section', 'content', 'note'],
                ],
            ],
            [
                'name' => self::UPDATE_ACTIVITY,
                'description' => 'Propose new texts for an existing activity. Give only the fields to change, each with '
                    . 'its complete new text (keep everything the teacher did not ask to change). For a text and media '
                    . 'area (label) the text is the "description" field.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'cmid' => ['type' => 'integer', 'description' => 'Activity id, as in [cmid=N].'],
                        'name' => ['type' => 'string', 'description' => 'New name, plain text.'],
                        'description' => ['type' => 'string', 'description' => 'New description. ' . $html],
                        'content' => ['type' => 'string', 'description' => 'New page content (pages only). ' . $html],
                        'note' => $note,
                    ],
                    'required' => ['cmid', 'note'],
                ],
            ],
            [
                'name' => self::UPDATE_SECTION,
                'description' => 'Propose a new name and/or summary for a course section. Give only the fields to change.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'section' => $section,
                        'name' => ['type' => 'string', 'description' => 'New section name, plain text.'],
                        'summary' => ['type' => 'string', 'description' => 'New section summary. ' . $html],
                        'note' => $note,
                    ],
                    'required' => ['section', 'note'],
                ],
            ],
        ];
    }
}
