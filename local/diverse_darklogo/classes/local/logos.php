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

namespace local_diverse_darklogo\local;

/**
 * DIVERSE dark logos - Where the logos are stored and how to find them
 *
 * The dark logo and the dark compact logo of a partner (tenant) are files in the partner's context, in the file
 * storage of Moodle (the database and moodledata), next to the light ones of tool_mutenancy.
 *
 * @package    local_diverse_darklogo
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class logos {
    /** @var string Component of the files. */
    const COMPONENT = 'local_diverse_darklogo';

    /** @var string File area of the full logo for dark pages. */
    const LOGO = 'logo';

    /** @var string File area of the compact (square) logo for dark pages. */
    const COMPACT = 'logocompact';

    /** @var string[] The file areas. */
    const AREAS = [self::LOGO, self::COMPACT];

    /**
     * File manager options of the two logos. No SVG, like the logos of tool_mutenancy.
     *
     * @return array
     */
    public static function options(): array {
        return [
            'maxfiles' => 1,
            'subdirs' => 0,
            'accepted_types' => ['.jpg', '.png', '.gif'],
        ];
    }

    /**
     * The stored file of a partner's logo.
     *
     * @param int $tenantid
     * @param string $area self::LOGO or self::COMPACT
     * @return \stored_file|null
     */
    public static function file(int $tenantid, string $area): ?\stored_file {
        if (!in_array($area, self::AREAS, true) || !class_exists(\context_tenant::class)) {
            return null;
        }
        $context = \context_tenant::instance($tenantid, IGNORE_MISSING);
        if (!$context) {
            return null;
        }
        $files = get_file_storage()->get_area_files($context->id, self::COMPONENT, $area, 0, 'id DESC', false);
        return $files ? reset($files) : null;
    }

    /**
     * The address of a partner's logo for dark pages.
     *
     * @param int $tenantid
     * @param string $area self::LOGO or self::COMPACT
     * @return \core\url|null Null if the partner has none.
     */
    public static function url(int $tenantid, string $area): ?\core\url {
        $file = self::file($tenantid, $area);
        if (!$file) {
            return null;
        }
        $url = \core\url::make_pluginfile_url(
            $file->get_contextid(),
            self::COMPONENT,
            $area,
            0,
            $file->get_filepath(),
            $file->get_filename()
        );
        // A new file has a new address, so that browsers do not keep the old logo.
        $url->param('rev', $file->get_timemodified());
        return $url;
    }
}
