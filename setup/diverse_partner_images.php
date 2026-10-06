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

/**
 * DIVERSE partner images: uploads the partners' logos and login photos from a folder, in the places an admin would
 * upload them by hand (requested by the project owner, October 2026).
 *
 * The folder has one subfolder per partner, named after its tenant idnumber (satakunta, beykent...), with
 * logo.png|jpg|webp|svg and/or photo.jpg|png|webp. A subfolder "diverse" holds the login photo shown while no partner
 * is chosen.
 *
 * - logo: the partner's logo on the login form (theme_diverse > Partner login pages) and the partner's own logo and
 *   compact logo in the menu for its users (tool_mutenancy > partner > Appearance > Logos).
 * - photo: the partner's login photo (theme_diverse > Partner login pages).
 *
 * An image an admin has already uploaded is kept unless --replace is given; uploads whose file is missing on this
 * site are replaced. The images themselves are not part of the repository.
 *
 * Usage:
 *   php setup/diverse_partner_images.php --dir=/path/to/partner-images [--dry-run] [--replace]
 *
 * @package    theme_diverse
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/clilib.php');

[$options, $unrecognised] = cli_get_params(['help' => false, 'dry-run' => false, 'replace' => false, 'dir' => ''],
    ['h' => 'help', 'n' => 'dry-run']);

if ($unrecognised) {
    cli_error(get_string('cliunknowoption', 'admin', implode(PHP_EOL . '  ', $unrecognised)));
}

if ($options['help'] || $options['dir'] === '') {
    cli_writeln("DIVERSE partner logos and login photos from a folder.

Options:
      --dir=PATH   Folder with one subfolder per partner (tenant idnumber) holding logo.* and/or photo.* (required).
  -n, --dry-run    Show what would change without changing anything.
      --replace    Also replace images an admin has already uploaded.
  -h, --help       Print this help.

Example:
  php setup/diverse_partner_images.php --dir=/home/thinkhub/partner-images --dry-run");
    exit($options['help'] ? 0 : 1);
}

$dir = rtrim($options['dir'], '/\\');
if (!is_dir($dir)) {
    cli_error("Folder $dir does not exist.");
}
$dryrun = (bool) $options['dry-run'];
$replace = (bool) $options['replace'];

/**
 * Print one step result.
 *
 * @param string $status OK (already fine), SET (changed), WOULD (dry run), SKIP (left alone), WARN
 * @param string $message
 */
function diverse_images_report(string $status, string $message): void {
    cli_writeln(str_pad('[' . $status . ']', 8) . $message);
}

/**
 * The image file named $name.* in a folder.
 *
 * @param string $folder
 * @param string $name
 * @param string[] $extensions
 * @return string|null
 */
function diverse_images_find(string $folder, string $name, array $extensions): ?string {
    foreach ($extensions as $extension) {
        if (is_file("$folder/$name.$extension")) {
            return "$folder/$name.$extension";
        }
    }
    return null;
}

/**
 * Put an image into a file area (one file per area), unless the same image or an admin's own image is there.
 *
 * @param string $path Local image.
 * @param int $contextid
 * @param string $component
 * @param string $filearea
 * @param int $itemid
 * @param string $what For the report.
 * @param bool $dryrun
 * @param bool $replace
 * @return string|null '/filename' of the stored image (null when nothing is stored, e.g. a dry run or a kept image).
 */
function diverse_images_store(string $path, int $contextid, string $component, string $filearea, int $itemid, string $what,
        bool $dryrun, bool $replace): ?string {
    $fs = get_file_storage();
    $hash = sha1_file($path);
    $filename = basename($path);
    $current = $fs->get_area_files($contextid, $component, $filearea, $itemid, 'id DESC', false);
    $file = reset($current);
    // Moodle removes EXIF data from uploaded JPEGs, so the stored content can differ from the image: the image's
    // own hash is kept as the file's source.
    $source = 'diverse_partner_images:' . $hash;
    if ($file && ($file->get_contenthash() === $hash || $file->get_source() === $source)) {
        diverse_images_report('OK', "$what is up to date.");
        return null;
    }
    $readable = $file && $fs->get_file_system()->is_file_readable_locally_by_storedfile($file);
    if ($readable && !$replace) {
        diverse_images_report('SKIP', "$what: an admin uploaded \"" . $file->get_filename() . '"; kept (use --replace).');
        return null;
    }
    if ($dryrun) {
        diverse_images_report('WOULD', "Upload $what ($filename)" . ($file ? ', replacing "' . $file->get_filename() . '"'
            . ($readable ? '' : ' (its file is missing)') : '') . '.');
        return null;
    }
    $fs->delete_area_files($contextid, $component, $filearea, $itemid);
    $fs->create_file_from_pathname(['contextid' => $contextid, 'component' => $component, 'filearea' => $filearea,
        'itemid' => $itemid, 'filepath' => '/', 'filename' => $filename, 'source' => $source], $path);
    diverse_images_report('SET', "Uploaded $what ($filename).");
    return '/' . $filename;
}

cli_heading('DIVERSE partner images' . ($dryrun ? ' (dry run, nothing is changed)' : ''));

if (!function_exists('mutenancy_is_active') || !mutenancy_is_active()) {
    cli_error('Partner organisations (tool_mutenancy) are not active.');
}

$syscontext = context_system::instance();
$logoext = ['png', 'jpg', 'jpeg', 'webp', 'svg'];
$photoext = ['jpg', 'jpeg', 'png', 'webp'];
$changed = false;

foreach (array_filter(glob("$dir/*"), 'is_dir') as $folder) {
    $idnumber = basename($folder);
    if ($idnumber[0] === '_') {
        continue;
    }
    if ($idnumber === 'diverse') {
        $tenant = null;
        $tenantid = 0;
        $label = 'DIVERSE';
    } else {
        $tenant = \tool_mutenancy\local\tenant::fetch_by_idnumber($idnumber);
        if (!$tenant || $tenant->archived) {
            diverse_images_report('WARN', "No active partner \"$idnumber\": folder skipped.");
            continue;
        }
        $tenantid = (int)$tenant->id;
        $label = $tenant->name;
    }

    if ($photo = diverse_images_find($folder, 'photo', $photoext)) {
        $stored = diverse_images_store($photo, $syscontext->id, 'theme_diverse', \theme_diverse\local\partners::PHOTO_AREA,
            $tenantid, "$label login photo", $dryrun, $replace);
        if ($stored) {
            set_config("loginphoto_$tenantid", $stored, 'theme_diverse');
            $changed = true;
        }
    }

    $logo = diverse_images_find($folder, 'logo', $logoext);
    if ($logo && $tenant) {
        $stored = diverse_images_store($logo, $syscontext->id, 'theme_diverse', \theme_diverse\local\partners::LOGO_AREA,
            $tenantid, "$label logo on the login form", $dryrun, $replace);
        if ($stored) {
            set_config("loginlogo_$tenantid", $stored, 'theme_diverse');
            $changed = true;
        }
        $tenantcontext = context_tenant::instance($tenantid);
        foreach (['logo' => 'menu logo', 'logocompact' => 'compact menu logo'] as $setting => $name) {
            $stored = diverse_images_store($logo, $tenantcontext->id, 'core_admin', $setting, 0, "$label $name", $dryrun,
                $replace);
            if ($stored) {
                \tool_mutenancy\local\config::override($tenantid, $setting, $stored, 'core_admin');
                $changed = true;
            }
        }
    }
}

if ($changed) {
    theme_reset_all_caches();
    diverse_images_report('SET', 'Theme caches reset.');
}
cli_writeln('');
cli_writeln($dryrun ? 'Dry run finished.' : 'DIVERSE partner images finished.');
