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

namespace local_diverse_policy;

/**
 * The two policy documents of the platform: the Privacy Policy and the Terms of Use.
 *
 * Each is a PDF shipped with this plugin (pdf/, built from docs/policies/ with docs/policies/build_pdf.py), unless an
 * admin enters another address in the plugin settings.
 *
 * @package    local_diverse_policy
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class policies {
    /** @var string The Privacy Policy. */
    const PRIVACY = 'privacy';
    /** @var string The Terms of Use. */
    const TERMS = 'terms';

    /** @var string[] Bundled PDF of each document. */
    const FILES = [
        self::PRIVACY => 'privacy-policy.pdf',
        self::TERMS => 'terms-of-use.pdf',
    ];

    /**
     * Address of a document: the address set in the plugin settings, or the bundled PDF.
     *
     * @param string $document PRIVACY or TERMS
     * @return \core\url
     */
    public static function url(string $document): \core\url {
        $configured = trim((string)get_config('local_diverse_policy', $document . 'url'));
        if ($configured !== '') {
            return new \core\url($configured);
        }
        return new \core\url('/local/diverse_policy/pdf/' . self::FILES[$document]);
    }

    /**
     * Link to a document that opens in a new tab.
     *
     * @param string $document PRIVACY or TERMS
     * @param array $attributes Extra attributes, e.g. a class.
     * @return string
     */
    public static function link(string $document, array $attributes = []): string {
        return \html_writer::link(self::url($document), get_string($document . 'policy', 'local_diverse_policy'),
            $attributes + ['target' => '_blank', 'rel' => 'noopener']);
    }
}
