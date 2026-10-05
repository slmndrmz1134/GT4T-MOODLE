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

namespace theme_diverse\local;

/**
 * Theme DIVERSE - Partner universities (tool_mutenancy tenants) on the login page.
 *
 * A visitor picks a partner with the "Select Partner" menu of the login page; tool_mutenancy then
 * remembers that partner as the current tenant. Everything here only reads: the tenant list, the
 * current tenant and the login photo uploaded in this theme's settings.
 *
 * @package    theme_diverse
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class partners {
    /** @var string File area of the partner login photos (system context, item id = tenant id). */
    const PHOTO_AREA = 'loginphoto';

    /**
     * Whether partner universities are in use on this site.
     *
     * @return bool
     */
    public static function enabled(): bool {
        return function_exists('mutenancy_is_active') && mutenancy_is_active();
    }

    /**
     * The partners offered on the login page, by name.
     *
     * @return \stdClass[] id, idnumber and display name of each partner.
     */
    public static function login_list(): array {
        global $DB;

        if (!self::enabled()) {
            return [];
        }
        $records = $DB->get_records(
            'tool_mutenancy_tenant',
            ['archived' => 0, 'loginshow' => 1],
            'name ASC',
            'id, idnumber, name, sitefullname'
        );
        return array_values(array_map([self::class, 'export'], $records));
    }

    /**
     * The partner chosen on the login page (or the partner of the logged-in user), if any.
     *
     * @return \stdClass|null id, idnumber and display name.
     */
    public static function current(): ?\stdClass {
        if (!self::enabled()) {
            return null;
        }
        $tenantid = (int)\tool_mutenancy\local\tenancy::get_current_tenantid();
        $tenant = $tenantid ? \tool_mutenancy\local\tenant::fetch($tenantid) : null;
        if (!$tenant || $tenant->archived) {
            return null;
        }
        return self::export($tenant);
    }

    /**
     * URL of the login photo uploaded for a partner in the theme settings.
     *
     * @param int $tenantid
     * @return \core\url|null
     */
    public static function photo_url(int $tenantid): ?\core\url {
        $context = \core\context\system::instance();
        $files = get_file_storage()->get_area_files(
            $context->id,
            'theme_diverse',
            self::PHOTO_AREA,
            $tenantid,
            'filename',
            false
        );
        $file = reset($files);
        if (!$file || !get_file_storage()->get_file_system()->is_file_readable_locally_by_storedfile($file)) {
            return null;
        }
        return \core\url::make_pluginfile_url(
            $context->id,
            'theme_diverse',
            self::PHOTO_AREA,
            $tenantid,
            $file->get_filepath(),
            $file->get_filename()
        );
    }

    /**
     * The fields used by the theme, with the name the login page shows.
     *
     * @param \stdClass $tenant Record of tool_mutenancy_tenant.
     * @return \stdClass
     */
    protected static function export(\stdClass $tenant): \stdClass {
        $name = !empty($tenant->sitefullname) ? $tenant->sitefullname : $tenant->name;
        return (object)[
            'id' => (int)$tenant->id,
            'idnumber' => $tenant->idnumber,
            'name' => format_string($name, true, ['context' => \core\context\system::instance()]),
        ];
    }
}
