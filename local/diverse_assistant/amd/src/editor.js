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
 * Teacher mode: put the assistant's texts into Moodle's edit forms (TinyMCE or plain text areas).
 *
 * Nothing is saved here: the teacher checks the form and saves it as usual.
 *
 * @module     local_diverse_assistant/editor
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {getAllInstances, getInstanceForElementId} from 'editor_tiny/editor';

/** How long to wait for TinyMCE to start on a form page, in milliseconds. */
const EDITOR_WAIT = 8000;

/**
 * Wait a moment.
 *
 * @param {Number} ms Milliseconds.
 * @returns {Promise}
 */
const sleep = ms => new Promise(resolve => setTimeout(resolve, ms));

/**
 * The TinyMCE editor of a text area, once it has started.
 *
 * @param {String} id Id of the text area.
 * @returns {Promise<Object|null>} Null when the field uses the plain text area.
 */
const waitForEditor = async id => {
    const start = Date.now();
    while (Date.now() - start < EDITOR_WAIT) {
        const editor = getInstanceForElementId(id);
        if (editor && editor.initialized) {
            return editor;
        }
        await sleep(200);
    }
    return null;
};

/**
 * Links to files of the text point to the form's draft area, where Moodle copied them, so images show in the editor;
 * Moodle turns them back into @@PLUGINFILE@@ links when the form is saved.
 *
 * @param {String} html The text.
 * @param {String} fieldname Name of the editor field, e.g. introeditor.
 * @param {Number} usercontextid Context id of the current user.
 * @returns {String}
 */
const linkDraftFiles = (html, fieldname, usercontextid) => {
    const itemid = document.querySelector(`input[name="${fieldname}[itemid]"]`)?.value;
    if (!itemid || !html.includes('@@PLUGINFILE@@')) {
        return html;
    }
    return html.replaceAll('@@PLUGINFILE@@', `${M.cfg.wwwroot}/draftfile.php/${usercontextid}/user/draft/${itemid}`);
};

/**
 * Fill form fields.
 *
 * @param {Array} fields List of {id, name, value, editor} from the proposal's prefill data.
 * @param {Number} usercontextid Context id of the current user.
 * @returns {Promise<Boolean>} Whether every field was found.
 */
export const fillForm = async(fields, usercontextid) => {
    let complete = true;
    let first = null;
    for (const field of fields) {
        const element = document.getElementById(field.id);
        if (!element) {
            complete = false;
            continue;
        }
        if (field.editor) {
            const html = linkDraftFiles(field.value, field.name, usercontextid);
            const editor = await waitForEditor(field.id);
            if (editor) {
                editor.setContent(html);
                editor.save();
                editor.setDirty(true);
                first = first || editor.getContainer();
            } else {
                element.value = html;
                element.dispatchEvent(new Event('change', {bubbles: true}));
                first = first || element;
            }
        } else {
            element.value = field.value;
            element.dispatchEvent(new Event('input', {bubbles: true}));
            element.dispatchEvent(new Event('change', {bubbles: true}));
            first = first || element;
        }
    }
    first?.scrollIntoView({behavior: 'smooth', block: 'center'});
    return complete;
};

/**
 * Insert HTML where the cursor was in the rich text field used last.
 *
 * @param {String} html Safe HTML.
 * @returns {Boolean} Whether there was an editor to insert into.
 */
export const insertIntoEditor = html => {
    const editors = [...getAllInstances().values()].filter(editor => !editor.removed);
    const active = window.tinyMCE?.activeEditor;
    const editor = editors.includes(active) ? active : editors[0];
    if (!editor) {
        return false;
    }
    editor.focus();
    editor.insertContent(html);
    editor.setDirty(true);
    return true;
};
