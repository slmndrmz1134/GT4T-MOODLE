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
 * The chat window in the bottom right corner.
 *
 * When the user opens a conversation in Moodle's messages panel, it is remembered for this browser tab
 * (sessionStorage); the next pages show it in the window until the user closes it. Messages are read and sent with
 * Moodle's own web services, so the window is the same conversation as in the messages panel and page.
 *
 * @module     local_diverse_chat/window
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';
import Notification from 'core/notification';

/** sessionStorage key of the conversation in the window. */
const STORAGE_KEY = 'local_diverse_chat';

/** How many of the latest messages the window shows. */
const MESSAGE_COUNT = 30;

/** How often the open window looks for new messages, in milliseconds. */
const REFRESH = 15000;

/**
 * The remembered conversation, or null.
 *
 * @return {Object|null} id, userid, name, picture, minimised
 */
const load = () => {
    try {
        return JSON.parse(window.sessionStorage.getItem(STORAGE_KEY) || 'null');
    } catch (e) {
        return null;
    }
};

/**
 * Remember the conversation, or forget it.
 *
 * @param {Object|null} state
 */
const save = (state) => {
    try {
        if (state) {
            window.sessionStorage.setItem(STORAGE_KEY, JSON.stringify(state));
        } else {
            window.sessionStorage.removeItem(STORAGE_KEY);
        }
    } catch (e) {
        // Without storage the window simply does not follow the user.
    }
};

/**
 * Start the window.
 *
 * @param {Object} config userid, messagesurl
 */
export const init = (config) => {
    const root = document.querySelector('[data-region="local-diverse-chat"]');
    if (!root) {
        return;
    }
    const list = root.querySelector('[data-region="messages"]');
    const form = root.querySelector('[data-region="form"]');
    const text = root.querySelector('[data-region="text"]');
    const title = root.querySelector('[data-action="minimise"]');
    let state = load();
    let timer = null;

    // A conversation opened in the messages panel is the one the next pages show.
    document.addEventListener('click', (e) => {
        const item = e.target.closest('[data-region="message-drawer"] [data-conversation-id]');
        if (!item) {
            return;
        }
        const name = item.querySelector('strong');
        const picture = item.querySelector('img');
        save({
            id: parseInt(item.dataset.conversationId, 10),
            userid: parseInt(item.dataset.userId || '0', 10),
            name: name ? name.textContent.trim() : '',
            picture: picture ? picture.src : '',
            minimised: false,
        });
    }, true);

    const render = (messages) => {
        list.innerHTML = '';
        messages.slice().reverse().forEach((message) => {
            const item = document.createElement('li');
            item.className = 'local-diverse-chat-message ' +
                (message.useridfrom === config.userid ? 'local-diverse-chat-sent' : 'local-diverse-chat-received');
            // The text comes formatted and cleaned by Moodle.
            item.innerHTML = message.text;
            list.appendChild(item);
        });
        list.scrollTop = list.scrollHeight;
    };

    const refresh = () => Ajax.call([{
        methodname: 'core_message_get_conversation_messages',
        args: {currentuserid: config.userid, convid: state.id, newest: true, limitnum: MESSAGE_COUNT},
    }])[0].then((result) => {
        render(result.messages);
        const other = (result.members || []).find((member) => member.id !== config.userid);
        if (other && !state.name) {
            state.name = other.fullname;
            state.picture = other.profileimageurl;
            save(state);
            fill();
        }
        return Ajax.call([{
            methodname: 'core_message_mark_all_conversation_messages_as_read',
            args: {userid: config.userid, conversationid: state.id},
        }])[0];
    }).catch(() => {
        // The conversation is gone or no longer open to the user: the window closes.
        close();
    });

    const fill = () => {
        root.querySelector('[data-region="name"]').textContent = state.name;
        const picture = root.querySelector('[data-region="picture"]');
        picture.hidden = !state.picture;
        if (state.picture) {
            picture.src = state.picture;
        }
        const full = new URL(config.messagesurl);
        if (state.userid) {
            full.searchParams.set('id', state.userid);
        } else {
            full.searchParams.set('convid', state.id);
        }
        root.querySelector('[data-region="full"]').href = full.toString();
    };

    const setMinimised = (minimised) => {
        state.minimised = minimised;
        save(state);
        root.classList.toggle('local-diverse-chat-minimised', minimised);
        title.setAttribute('aria-expanded', minimised ? 'false' : 'true');
        window.clearInterval(timer);
        timer = null;
        if (!minimised) {
            refresh();
            timer = window.setInterval(refresh, REFRESH);
        }
    };

    const close = () => {
        window.clearInterval(timer);
        save(null);
        root.hidden = true;
    };

    title.addEventListener('click', () => setMinimised(!state.minimised));
    root.querySelector('[data-action="close"]').addEventListener('click', close);

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const message = text.value.trim();
        if (!message) {
            return;
        }
        text.disabled = true;
        Ajax.call([{
            methodname: 'core_message_send_messages_to_conversation',
            args: {conversationid: state.id, messages: [{text: message}]},
        }])[0].then(() => {
            text.value = '';
            return refresh();
        }).catch(Notification.exception).then(() => {
            text.disabled = false;
            text.focus();
            return null;
        });
    });

    // Enter sends, Shift+Enter starts a new line.
    text.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            form.requestSubmit();
        }
    });

    if (!state || !state.id) {
        return;
    }
    fill();
    root.hidden = false;
    setMinimised(Boolean(state.minimised));
};
