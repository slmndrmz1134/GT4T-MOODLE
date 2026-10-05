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
 * current tenant and the login photos and logos uploaded in this theme's settings.
 *
 * @package    theme_diverse
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class partners {
    /** @var string File area of the login photos (system context, item id = tenant id, 0 = DIVERSE). */
    const PHOTO_AREA = 'loginphoto';

    /** @var string File area of the partner logos on the login form (system context, item id = tenant id). */
    const LOGO_AREA = 'loginlogo';

    /** @var string File area of the trimmed copies of the partner logos (see logo_url()). */
    const LOGO_TRIMMED_AREA = 'loginlogotrimmed';

    /** @var int Height in pixels of the trimmed logo copies: four times the 2.5rem the login form shows. */
    const LOGO_HEIGHT = 160;

    /**
     * URL of a partner's login logo, without the empty margins the uploaded file may have.
     *
     * Logos are often uploaded with wide transparent or white margins, which make them look smaller than others
     * at the same height on the login form. A copy cut to the visible logo is made once per uploaded file and
     * kept in LOGO_TRIMMED_AREA; the upload itself is left as it is. SVG files and images this server cannot read
     * are used as uploaded.
     *
     * @param int $tenantid
     * @return \core\url|null
     */
    public static function logo_url(int $tenantid): ?\core\url {
        $context = \core\context\system::instance();
        $fs = get_file_storage();
        $files = $fs->get_area_files($context->id, 'theme_diverse', self::LOGO_AREA, $tenantid, 'filename', false);
        $original = reset($files);
        if (!$original) {
            $fs->delete_area_files($context->id, 'theme_diverse', self::LOGO_TRIMMED_AREA, $tenantid);
            return null;
        }

        $name = 'logo-' . substr($original->get_contenthash(), 0, 12) . '.png';
        $trimmed = $fs->get_file($context->id, 'theme_diverse', self::LOGO_TRIMMED_AREA, $tenantid, '/', $name);
        if (!$trimmed) {
            $content = self::trim_image($original->get_content());
            if ($content === null) {
                return self::file_url(self::LOGO_AREA, $tenantid);
            }
            // The previous copy belongs to a replaced upload.
            $fs->delete_area_files($context->id, 'theme_diverse', self::LOGO_TRIMMED_AREA, $tenantid);
            $fs->create_file_from_string([
                'contextid' => $context->id,
                'component' => 'theme_diverse',
                'filearea' => self::LOGO_TRIMMED_AREA,
                'itemid' => $tenantid,
                'filepath' => '/',
                'filename' => $name,
            ], $content);
        }
        return self::file_url(self::LOGO_TRIMMED_AREA, $tenantid);
    }

    /**
     * Cut an image to its visible part (not transparent, not white) and scale it to LOGO_HEIGHT.
     *
     * @param string $content Image file content.
     * @return string|null PNG content, or null if the image cannot be read (e.g. SVG).
     */
    protected static function trim_image(string $content): ?string {
        $image = @imagecreatefromstring($content);
        if (!$image) {
            return null;
        }
        if (!imageistruecolor($image)) {
            imagepalettetotruecolor($image);
        }

        // Look for the visible part on a small copy: much faster on large uploads, precise enough for margins.
        [$width, $height] = [imagesx($image), imagesy($image)];
        $scale = min(1, 400 / max($width, $height));
        [$sw, $sh] = [max(1, (int)round($width * $scale)), max(1, (int)round($height * $scale))];
        $small = imagecreatetruecolor($sw, $sh);
        imagealphablending($small, false);
        imagesavealpha($small, true);
        imagecopyresampled($small, $image, 0, 0, 0, 0, $sw, $sh, $width, $height);

        [$left, $top, $right, $bottom] = [$sw, $sh, -1, -1];
        for ($y = 0; $y < $sh; $y++) {
            for ($x = 0; $x < $sw; $x++) {
                $rgba = imagecolorat($small, $x, $y);
                $alpha = ($rgba >> 24) & 0x7F;
                $white = (($rgba >> 16) & 0xFF) > 240 && (($rgba >> 8) & 0xFF) > 240 && ($rgba & 0xFF) > 240;
                if ($alpha < 110 && !$white) {
                    $left = min($left, $x);
                    $right = max($right, $x);
                    $top = min($top, $y);
                    $bottom = max($bottom, $y);
                }
            }
        }
        if ($right < 0) {
            // Nothing visible found (e.g. a white logo on transparency): keep the whole image.
            [$left, $top, $right, $bottom] = [0, 0, $sw - 1, $sh - 1];
        }

        // Back to the original size, with one small pixel of room so that no edge is cut.
        $x = max(0, (int)floor(($left - 1) / $scale));
        $y = max(0, (int)floor(($top - 1) / $scale));
        $w = min($width, (int)ceil(($right + 2) / $scale)) - $x;
        $h = min($height, (int)ceil(($bottom + 2) / $scale)) - $y;

        $outheight = min(self::LOGO_HEIGHT, $h);
        $outwidth = max(1, (int)round($w * $outheight / $h));
        $out = imagecreatetruecolor($outwidth, $outheight);
        imagealphablending($out, false);
        imagesavealpha($out, true);
        imagefill($out, 0, 0, imagecolorallocatealpha($out, 0, 0, 0, 127));
        imagecopyresampled($out, $image, 0, 0, $x, $y, $outwidth, $outheight, $w, $h);

        ob_start();
        imagepng($out);
        return ob_get_clean();
    }

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
     * URL of a login photo or logo uploaded in the theme settings.
     *
     * @param string $filearea PHOTO_AREA or LOGO_AREA.
     * @param int $tenantid Partner (tenant) id, 0 for DIVERSE itself.
     * @return \core\url|null
     */
    public static function file_url(string $filearea, int $tenantid): ?\core\url {
        $context = \core\context\system::instance();
        $files = get_file_storage()->get_area_files(
            $context->id,
            'theme_diverse',
            $filearea,
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
            $filearea,
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
