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
 * Server calls of the AI assistant panel.
 *
 * @module     local_diverse_assistant/repository
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';

/**
 * Call one web service function.
 *
 * @param {String} methodname Function name.
 * @param {Object} args Arguments.
 * @returns {Promise}
 */
const call = (methodname, args = {}) => Ajax.call([{methodname, args}])[0];

/**
 * Whether the assistant can be used in a course, the user's choices and saved chats.
 *
 * @param {Number} courseid Course id.
 * @returns {Promise}
 */
export const getState = courseid => call('local_diverse_assistant_get_state', {courseid});

/**
 * Messages of a saved chat.
 *
 * @param {Number} conversationid Conversation id.
 * @returns {Promise}
 */
export const getMessages = conversationid => call('local_diverse_assistant_get_messages', {conversationid});

/**
 * Delete a saved chat.
 *
 * @param {Number} conversationid Conversation id.
 * @returns {Promise}
 */
export const deleteConversation = conversationid => call('local_diverse_assistant_delete_conversation', {conversationid});

/**
 * Delete all saved chats of the user.
 *
 * @returns {Promise}
 */
export const deleteHistory = () => call('local_diverse_assistant_delete_history');

/**
 * Set how long chats are kept.
 *
 * @param {Number} days Days; 0 not saved, -1 until deleted.
 * @returns {Promise}
 */
export const setRetention = days => call('local_diverse_assistant_set_retention', {days});

/**
 * Record that the notice was read.
 *
 * @returns {Promise}
 */
export const acceptNotice = () => call('local_diverse_assistant_accept_notice');

/**
 * Apply, undo or decline a proposed change (teacher mode).
 *
 * @param {Number} proposalid Proposal id.
 * @param {String} action apply, undo or discard.
 * @returns {Promise<Object>} ok, message and the updated proposal.
 */
export const proposalAction = (proposalid, action) => call('local_diverse_assistant_proposal_action', {proposalid, action});

/**
 * An error reported by the server while answering.
 */
export class AnswerError extends Error {
    /**
     * Constructor.
     *
     * @param {String} message Message for the student.
     */
    constructor(message) {
        super(message);
        this.name = 'AnswerError';
    }
}

/**
 * Ask a question and read the streamed answer.
 *
 * @param {String} url Address of stream.php.
 * @param {Object} params Form fields.
 * @param {AbortSignal} signal Aborts the request when the student presses stop.
 * @param {Function} onDelta Called with each new piece of the answer.
 * @param {Function} onStatus Called with a message while a proposal is being written (teacher mode).
 * @returns {Promise<Object>} The final "done" event: conversationid, saved, text, historytext, html, truncated,
 *     proposals and problems.
 */
export const streamAnswer = async(url, params, signal, onDelta, onStatus = () => null) => {
    const response = await fetch(url, {
        method: 'POST',
        body: new URLSearchParams(params),
        credentials: 'same-origin',
        signal,
    });
    if (!response.ok || !response.body) {
        throw new Error('HTTP ' + response.status);
    }

    const reader = response.body.getReader();
    const decoder = new TextDecoder();
    let buffer = '';
    let result = null;

    const handleEvent = raw => {
        const data = raw.split('\n')
            .filter(line => line.startsWith('data:'))
            .map(line => line.slice(5).replace(/^ /, ''))
            .join('\n');
        if (!data) {
            return;
        }
        const event = JSON.parse(data);
        if (event.type === 'delta') {
            onDelta(event.text);
        } else if (event.type === 'status') {
            onStatus(event.text);
        } else if (event.type === 'done') {
            result = event;
        } else if (event.type === 'error') {
            throw new AnswerError(event.message);
        }
    };

    for (;;) {
        const {value, done} = await reader.read();
        buffer += decoder.decode(value || new Uint8Array(), {stream: !done});
        let boundary;
        while ((boundary = buffer.indexOf('\n\n')) !== -1) {
            handleEvent(buffer.slice(0, boundary));
            buffer = buffer.slice(boundary + 2);
        }
        if (done) {
            break;
        }
    }
    if (buffer.trim()) {
        handleEvent(buffer);
    }
    if (!result) {
        throw new Error('The answer ended unexpectedly.');
    }
    return result;
};
