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
 * The AI assistant chat panel on course pages.
 *
 * Chats that the student chose not to save live only in this browser tab (sessionStorage): they survive moving between
 * pages of the course and disappear when the tab is closed or the student logs out.
 *
 * @module     local_diverse_assistant/assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import * as Repository from 'local_diverse_assistant/repository';
import Notification from 'core/notification';
import {getStrings} from 'core/str';
import {add as addToast} from 'core/toast';
import * as FocusLock from 'core/local/aria/focuslock';
import {isSmall} from 'core/pagehelpers';
import DrawerEvents from 'core/drawer_events';
import {subscribe} from 'core/pubsub';
import * as MessageDrawerHelper from 'core_message/message_drawer_helper';
import Drawers from 'theme_boost/drawers';
import Templates from 'core/templates';

/** Retention value meaning "not saved on the server". */
const NOT_SAVED = 0;

/** Prefix of every sessionStorage key of this panel. */
const STORAGE_PREFIX = 'local_diverse_assistant';

/** Class on <body> while the panel is open. */
const BODY_OPEN_CLASS = 'local-diverse-assistant-open';

/** Strings used by the script, loaded once. */
const STRING_KEYS = [
    'stopped', 'retry', 'errorgeneric', 'delete', 'deleteconversation', 'deleteconversation_confirm', 'deletehistory',
    'deletehistory_confirm', 'historydeleted', 'retentionsaved', 'retentionstatus', 'thinking', 'truncated',
];

const Selectors = {
    panel: '[data-region="local-diverse-assistant"]',
    toggle: '[data-action="local-diverse-assistant-toggle"]',
    view: '[data-view]',
    messages: '[data-region="messages"]',
    empty: '[data-region="empty"]',
    composer: '[data-region="composer"]',
    input: '[data-region="input"]',
    send: '[data-action="send"]',
    stop: '[data-action="stop"]',
    historyButton: '[data-action="history"]',
    historyList: '[data-region="history-list"]',
    historyEmpty: '[data-region="history-empty"]',
    retentionStatus: '[data-region="retention-status"]',
    retentionRadio: 'input[name="local-diverse-assistant-retention"]',
    reason: '[data-region="reason"]',
    noticeText: '[data-region="notice-text"]',
    blockDrawer: '#theme_boost-drawers-blocks',
};

/**
 * Read a sessionStorage value; storage can be unavailable (private mode, blocked site data).
 *
 * @param {String} key Key.
 * @returns {String|null}
 */
const storageGet = key => {
    try {
        return window.sessionStorage.getItem(key);
    } catch (e) {
        return null;
    }
};

/**
 * Write or remove a sessionStorage value.
 *
 * @param {String} key Key.
 * @param {String|null} value Value, or null to remove it.
 */
const storageSet = (key, value) => {
    try {
        if (value === null) {
            window.sessionStorage.removeItem(key);
        } else {
            window.sessionStorage.setItem(key, value);
        }
    } catch (e) {
        // Storage is unavailable or full: the chat still works, it is just not kept when the page changes.
    }
};

class Assistant {
    /**
     * Constructor.
     *
     * @param {Object} config From \local_diverse_assistant\output\panel::get_js_config().
     */
    constructor(config) {
        this.config = config;
        this.panel = document.querySelector(Selectors.panel);
        this.toggle = document.querySelector(Selectors.toggle);
        this.messagesRegion = this.panel.querySelector(Selectors.messages);
        this.emptyRegion = this.panel.querySelector(Selectors.empty);
        this.input = this.panel.querySelector(Selectors.input);

        /** @type {Object|null} Answer of get_state. */
        this.state = null;
        /** @type {Array} Messages shown: {role, text, html}. */
        this.messages = [];
        this.conversationId = 0;
        /** @type {AbortController|null} Set while an answer is streaming. */
        this.controller = null;
        this.focusLocked = false;
        this.strings = {};

        // Keys include the session key, so a chat never shows up in another login session in the same tab.
        this.sessionPrefix = `${STORAGE_PREFIX}:${config.userid}:${M.cfg.sesskey}`;
        this.removeOldStorage();

        this.registerEventListeners();
        if (storageGet(`${this.sessionPrefix}:open`) === '1' && !isSmall()) {
            this.open(false);
        }
    }

    /**
     * Remove stored chats of earlier login sessions.
     */
    removeOldStorage() {
        try {
            for (let i = window.sessionStorage.length - 1; i >= 0; i--) {
                const key = window.sessionStorage.key(i);
                if (key && key.startsWith(STORAGE_PREFIX + ':') && !key.startsWith(this.sessionPrefix + ':')) {
                    window.sessionStorage.removeItem(key);
                }
            }
        } catch (e) {
            // Storage unavailable.
        }
    }

    /**
     * Register event listeners.
     */
    registerEventListeners() {
        this.toggle.addEventListener('click', () => (this.isOpen() ? this.close() : this.open()));

        this.panel.addEventListener('click', e => {
            const button = e.target.closest('[data-action]');
            if (!button || !this.panel.contains(button)) {
                return;
            }
            const handlers = {
                'close': () => this.close(),
                'new': () => this.newChat(),
                'history': () => this.showHistory(),
                'settings': () => this.showView('settings'),
                'back': () => this.showMainView(),
                'accept-notice': () => this.acceptNotice(),
                'delete-history': () => this.deleteHistory(),
                'suggest': () => this.send(button.textContent),
                'stop': () => this.controller?.abort(),
                'open-conversation': () => this.openConversation(parseInt(button.dataset.id, 10)),
                'delete-conversation': () => this.deleteConversation(parseInt(button.dataset.id, 10), button.dataset.title),
            };
            if (handlers[button.dataset.action]) {
                e.preventDefault();
                handlers[button.dataset.action]();
            }
        });

        this.panel.querySelector(Selectors.composer).addEventListener('submit', e => {
            e.preventDefault();
            this.send(this.input.value);
        });

        this.input.addEventListener('keydown', e => {
            if (e.key === 'Enter' && !e.shiftKey && !e.isComposing) {
                e.preventDefault();
                this.send(this.input.value);
            }
        });
        this.input.addEventListener('input', () => this.resizeInput());

        this.panel.querySelectorAll(Selectors.retentionRadio).forEach(radio => {
            radio.addEventListener('change', () => this.setRetention(parseInt(radio.value, 10)));
        });

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && this.isOpen() && this.panel.contains(document.activeElement)) {
                this.close();
            }
        });

        // Only one panel on the right at a time: close when Moodle's messaging or block drawer opens.
        subscribe(DrawerEvents.DRAWER_SHOWN, () => this.close());
        document.addEventListener(Drawers.eventTypes.drawerShow, e => {
            if (e.target?.matches?.(Selectors.blockDrawer) || isSmall()) {
                this.close();
            }
        });
    }

    /**
     * Whether the panel is open.
     *
     * @returns {Boolean}
     */
    isOpen() {
        return this.panel.classList.contains('show');
    }

    /**
     * Open the panel.
     *
     * @param {Boolean} focus Move the focus into the panel.
     */
    open(focus = true) {
        MessageDrawerHelper.hide();
        const blockDrawer = document.querySelector(Selectors.blockDrawer);
        const blockDrawerInstance = blockDrawer ? Drawers.getDrawerInstanceForNode(blockDrawer) : null;
        if (blockDrawerInstance?.isOpen) {
            blockDrawerInstance.closeDrawer({focusOnOpenButton: false, updatePreferences: false});
        }

        this.panel.classList.add('show');
        document.body.classList.add(BODY_OPEN_CLASS);
        this.toggle.setAttribute('aria-expanded', 'true');
        storageSet(`${this.sessionPrefix}:open`, '1');

        if (isSmall()) {
            FocusLock.trapFocus(this.panel);
            this.panel.setAttribute('role', 'dialog');
            this.panel.setAttribute('aria-modal', 'true');
            this.focusLocked = true;
        }
        this.loadState(focus);
    }

    /**
     * Close the panel.
     */
    close() {
        if (!this.isOpen()) {
            return;
        }
        if (this.focusLocked) {
            FocusLock.untrapFocus();
            this.panel.removeAttribute('role');
            this.panel.removeAttribute('aria-modal');
            this.focusLocked = false;
        }
        this.panel.classList.remove('show');
        document.body.classList.remove(BODY_OPEN_CLASS);
        this.toggle.setAttribute('aria-expanded', 'false');
        storageSet(`${this.sessionPrefix}:open`, null);
        if (this.panel.contains(document.activeElement)) {
            this.toggle.focus();
        }
    }

    /**
     * Load the state from the server and show the right view.
     *
     * @param {Boolean} focus Move the focus into the panel afterwards.
     */
    async loadState(focus) {
        if (!this.state) {
            this.showView('loading');
        }
        try {
            const [state, strings] = await Promise.all([
                Repository.getState(this.config.courseid),
                this.loadStrings(),
            ]);
            const firstLoad = !this.state;
            this.state = state;
            this.strings = strings;
            if (firstLoad) {
                this.restoreChat();
            }
        } catch (error) {
            Notification.exception(error);
            return;
        }
        this.updateRetentionControls();
        this.showMainView();
        if (focus) {
            (this.panel.querySelector('[data-view]:not([hidden]) textarea, [data-view]:not([hidden]) .btn-primary')
                || this.panel).focus();
        }
    }

    /**
     * Load the script's strings.
     *
     * @returns {Promise<Object>} key => string
     */
    async loadStrings() {
        if (Object.keys(this.strings).length) {
            return this.strings;
        }
        const values = await getStrings(STRING_KEYS.map(key => ({key, component: 'local_diverse_assistant'})));
        return Object.fromEntries(STRING_KEYS.map((key, index) => [key, values[index]]));
    }

    /**
     * Show the chat, or what has to come before it.
     */
    showMainView() {
        if (!this.state) {
            this.showView('loading');
        } else if (!this.state.available) {
            this.panel.querySelector(Selectors.reason).textContent = this.state.reason;
            this.showView('unavailable');
        } else if (!this.state.noticeaccepted) {
            this.panel.querySelector(Selectors.noticeText).textContent = this.state.notice;
            this.showView('notice');
        } else {
            this.showView('chat');
            this.scrollToBottom(true);
        }
    }

    /**
     * Show one of the panel's views.
     *
     * @param {String} name loading, unavailable, notice, settings, history or chat.
     */
    showView(name) {
        this.panel.querySelectorAll(Selectors.view).forEach(view => {
            view.hidden = view.dataset.view !== name;
        });
    }

    /**
     * Whether chats are saved on the server.
     *
     * @returns {Boolean}
     */
    isSaved() {
        return this.state && this.state.retention !== NOT_SAVED;
    }

    /**
     * Update the history button, the retention choice and the line under the input.
     */
    updateRetentionControls() {
        this.panel.querySelector(Selectors.historyButton).hidden = !this.isSaved();
        this.panel.querySelectorAll(Selectors.retentionRadio).forEach(radio => {
            radio.checked = parseInt(radio.value, 10) === this.state.retention;
        });
        this.panel.querySelector(Selectors.retentionStatus).textContent =
            this.strings.retentionstatus.replace('{$a}', this.state.retentionlabel);
    }

    /**
     * Show the chat this tab had open on the previous page.
     */
    async restoreChat() {
        if (!this.isSaved()) {
            try {
                this.messages = JSON.parse(storageGet(this.chatStorageKey()) || '[]');
            } catch (e) {
                this.messages = [];
            }
            this.renderMessages();
            return;
        }
        const conversationId = parseInt(storageGet(this.conversationStorageKey()) || '0', 10);
        if (conversationId && this.state.conversations.some(conversation => conversation.id === conversationId)) {
            await this.openConversation(conversationId, false);
        } else {
            this.renderMessages();
        }
    }

    /**
     * sessionStorage key of the unsaved chat of this course.
     *
     * @returns {String}
     */
    chatStorageKey() {
        return `${this.sessionPrefix}:chat:${this.config.courseid}`;
    }

    /**
     * sessionStorage key of the saved conversation open in this course.
     *
     * @returns {String}
     */
    conversationStorageKey() {
        return `${this.sessionPrefix}:conversation:${this.config.courseid}`;
    }

    /**
     * Remember the current chat in this tab.
     */
    rememberChat() {
        if (this.isSaved()) {
            storageSet(this.conversationStorageKey(), this.conversationId ? String(this.conversationId) : null);
        } else {
            storageSet(this.chatStorageKey(), this.messages.length ? JSON.stringify(this.messages) : null);
        }
    }

    /**
     * Start a new chat.
     */
    newChat() {
        if (this.controller) {
            return;
        }
        this.messages = [];
        this.conversationId = 0;
        this.rememberChat();
        this.renderMessages();
        this.showMainView();
        this.input.focus();
    }

    /**
     * Show all messages.
     */
    renderMessages() {
        this.messagesRegion.querySelectorAll('.local-diverse-assistant-message').forEach(node => node.remove());
        this.messages.forEach(message => this.addMessageElement(message));
        this.emptyRegion.hidden = this.messages.length > 0;
        this.scrollToBottom(true);
    }

    /**
     * Add a message bubble.
     *
     * @param {Object} message role, and html (safe, from the server) or text.
     * @returns {HTMLElement} The content element of the bubble.
     */
    addMessageElement(message) {
        const bubble = document.createElement('div');
        bubble.className = `local-diverse-assistant-message local-diverse-assistant-message-${message.role}`;
        const content = document.createElement('div');
        content.className = 'local-diverse-assistant-message-content';
        if (message.html) {
            // HTML only ever comes from the server, which cleans it.
            content.innerHTML = message.html;
        } else {
            content.textContent = message.text || '';
        }
        bubble.append(content);
        this.messagesRegion.append(bubble);
        this.emptyRegion.hidden = true;
        return content;
    }

    /**
     * Ask a question.
     *
     * @param {String} text The question.
     */
    async send(text) {
        text = (text || '').trim();
        if (!text || this.controller || !this.state?.available) {
            return;
        }
        this.showView('chat');
        this.input.value = '';
        this.resizeInput();

        // Unsaved chats: the earlier messages are sent from here; saved chats: the server has them.
        const history = this.isSaved() ? [] : this.messages
            .filter(message => message.text)
            .slice(-this.config.historylimit)
            .map(message => ({role: message.role, content: message.text}));

        const question = {role: 'user', text};
        this.messages.push(question);
        const questionElement = this.addMessageElement(question).parentElement;

        const answerContent = this.addMessageElement({role: 'assistant', text: this.strings.thinking});
        const answerBubble = answerContent.parentElement;
        answerBubble.classList.add('local-diverse-assistant-message-pending');
        this.scrollToBottom(true);
        this.setStreaming(true);

        let answer = '';
        try {
            const result = await Repository.streamAnswer(this.config.streamurl, {
                sesskey: M.cfg.sesskey,
                courseid: this.config.courseid,
                cmid: this.config.cmid,
                conversationid: this.conversationId,
                message: text,
                history: JSON.stringify(history),
            }, this.controller.signal, delta => {
                if (!answer) {
                    answerBubble.classList.remove('local-diverse-assistant-message-pending');
                    answerBubble.classList.add('local-diverse-assistant-message-streaming');
                }
                answer += delta;
                answerContent.textContent = answer;
                this.scrollToBottom();
            });
            answerContent.innerHTML = result.html;
            if (result.truncated) {
                this.addNote(answerBubble, this.strings.truncated);
            }
            this.messages.push({role: 'assistant', text: result.text, html: result.html});
            if (result.saved) {
                this.conversationId = result.conversationid;
            }
        } catch (error) {
            if (error.name === 'AbortError' && answer) {
                answerContent.textContent = answer;
                this.addNote(answerBubble, this.strings.stopped);
                this.messages.push({role: 'assistant', text: answer});
            } else if (error.name === 'AbortError') {
                answerBubble.remove();
                this.messages.pop();
                questionElement.remove();
                this.input.value = text;
            } else {
                // The question was not answered: do not send it as history, offer to ask again.
                this.messages.pop();
                this.showError(answerBubble, questionElement, error, text);
            }
        } finally {
            answerBubble.classList.remove('local-diverse-assistant-message-pending', 'local-diverse-assistant-message-streaming');
            this.setStreaming(false);
            this.rememberChat();
            this.emptyRegion.hidden = this.messages.length > 0
                || this.messagesRegion.querySelector('.local-diverse-assistant-message') !== null;
        }
    }

    /**
     * Show an error in place of the answer, with a button to ask again.
     *
     * @param {HTMLElement} answerBubble The answer bubble.
     * @param {HTMLElement} questionElement The question bubble.
     * @param {Error} error The error.
     * @param {String} text The question.
     */
    showError(answerBubble, questionElement, error, text) {
        const content = answerBubble.querySelector('.local-diverse-assistant-message-content');
        answerBubble.classList.add('local-diverse-assistant-message-error');
        content.textContent = error instanceof Repository.AnswerError ? error.message : this.strings.errorgeneric;
        const retry = document.createElement('button');
        retry.type = 'button';
        retry.className = 'btn btn-link local-diverse-assistant-retry';
        retry.textContent = this.strings.retry;
        retry.addEventListener('click', () => {
            answerBubble.remove();
            questionElement.remove();
            this.send(text);
        });
        answerBubble.append(retry);
    }

    /**
     * Add a small note under a message.
     *
     * @param {HTMLElement} bubble The message bubble.
     * @param {String} text The note.
     */
    addNote(bubble, text) {
        const note = document.createElement('div');
        note.className = 'local-diverse-assistant-note';
        note.textContent = text;
        bubble.append(note);
    }

    /**
     * Switch the send and stop buttons.
     *
     * @param {Boolean} streaming Whether an answer is being written.
     */
    setStreaming(streaming) {
        this.controller = streaming ? new AbortController() : null;
        this.panel.querySelector(Selectors.send).hidden = streaming;
        this.panel.querySelector(Selectors.stop).hidden = !streaming;
        this.messagesRegion.setAttribute('aria-busy', streaming ? 'true' : 'false');
    }

    /**
     * Grow the input with its text, up to a limit set in CSS.
     */
    resizeInput() {
        this.input.style.height = 'auto';
        this.input.style.height = this.input.scrollHeight + 'px';
    }

    /**
     * Scroll the messages to the end.
     *
     * @param {Boolean} force Also when the student has scrolled up to read.
     */
    scrollToBottom(force = false) {
        const region = this.messagesRegion;
        const nearBottom = region.scrollHeight - region.scrollTop - region.clientHeight < 80;
        if (force || nearBottom) {
            region.scrollTop = region.scrollHeight;
        }
    }

    /**
     * Record that the notice was read and start chatting.
     */
    async acceptNotice() {
        try {
            await Repository.acceptNotice();
            this.state.noticeaccepted = true;
            this.showMainView();
            this.input.focus();
        } catch (error) {
            Notification.exception(error);
        }
    }

    /**
     * Show the saved chats of this course.
     */
    async showHistory() {
        try {
            this.state = await Repository.getState(this.config.courseid);
        } catch (error) {
            Notification.exception(error);
            return;
        }
        const deleteIcon = await Templates.renderPix('t/delete', 'core');
        const list = this.panel.querySelector(Selectors.historyList);
        list.replaceChildren(...this.state.conversations.map(conversation => {
            const item = document.createElement('li');
            item.className = 'local-diverse-assistant-history-item';

            const open = document.createElement('button');
            open.type = 'button';
            open.className = 'btn local-diverse-assistant-history-open';
            open.dataset.action = 'open-conversation';
            open.dataset.id = conversation.id;
            const title = document.createElement('span');
            title.className = 'local-diverse-assistant-history-title';
            title.textContent = conversation.title;
            const time = document.createElement('span');
            time.className = 'local-diverse-assistant-history-time';
            time.textContent = conversation.time;
            open.append(title, time);

            const remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'btn btn-icon local-diverse-assistant-history-delete';
            remove.dataset.action = 'delete-conversation';
            remove.dataset.id = conversation.id;
            remove.dataset.title = conversation.title;
            remove.setAttribute('aria-label', this.strings.delete + ': ' + conversation.title);
            remove.title = this.strings.delete;
            remove.innerHTML = deleteIcon;

            item.append(open, remove);
            return item;
        }));
        this.panel.querySelector(Selectors.historyEmpty).hidden = this.state.conversations.length > 0;
        this.showView('history');
    }

    /**
     * Open a saved chat.
     *
     * @param {Number} conversationId Conversation id.
     * @param {Boolean} show Switch to the chat view.
     */
    async openConversation(conversationId, show = true) {
        if (this.controller) {
            return;
        }
        try {
            const result = await Repository.getMessages(conversationId);
            this.conversationId = result.id;
            this.messages = result.messages.map(message => ({role: message.role, html: message.html}));
        } catch (error) {
            Notification.exception(error);
            return;
        }
        this.rememberChat();
        this.renderMessages();
        if (show) {
            this.showMainView();
        }
    }

    /**
     * Delete a saved chat after asking.
     *
     * @param {Number} conversationId Conversation id.
     * @param {String} title Its title.
     */
    async deleteConversation(conversationId, title) {
        try {
            await Notification.deleteCancelPromise(this.strings.deleteconversation,
                this.strings.deleteconversation_confirm.replace('{$a}', title), this.strings.delete);
        } catch (e) {
            return;
        }
        try {
            await Repository.deleteConversation(conversationId);
        } catch (error) {
            Notification.exception(error);
            return;
        }
        if (conversationId === this.conversationId) {
            this.messages = [];
            this.conversationId = 0;
            this.rememberChat();
            this.renderMessages();
        }
        await this.showHistory();
    }

    /**
     * Delete all saved chats after asking.
     */
    async deleteHistory() {
        try {
            await Notification.deleteCancelPromise(this.strings.deletehistory, this.strings.deletehistory_confirm,
                this.strings.delete);
        } catch (e) {
            return;
        }
        try {
            await Repository.deleteHistory();
        } catch (error) {
            Notification.exception(error);
            return;
        }
        this.clearChats();
        addToast(this.strings.historydeleted);
    }

    /**
     * Save a new retention choice.
     *
     * @param {Number} days Days; 0 not saved, -1 until deleted.
     */
    async setRetention(days) {
        const wasSaved = this.isSaved();
        try {
            const result = await Repository.setRetention(days);
            this.state.retention = result.retention;
            this.state.retentionlabel = result.label;
        } catch (error) {
            Notification.exception(error);
            this.updateRetentionControls();
            return;
        }
        // Chats are kept in a different place when this changes, so start a new one.
        if (wasSaved !== this.isSaved()) {
            this.clearChats();
        }
        this.updateRetentionControls();
        addToast(this.strings.retentionsaved);
    }

    /**
     * Forget the current chat and every unsaved chat of this tab.
     */
    clearChats() {
        this.messages = [];
        this.conversationId = 0;
        try {
            for (let i = window.sessionStorage.length - 1; i >= 0; i--) {
                const key = window.sessionStorage.key(i);
                const isChat = key && (key.startsWith(this.sessionPrefix + ':chat:')
                    || key.startsWith(this.sessionPrefix + ':conversation:'));
                if (isChat) {
                    window.sessionStorage.removeItem(key);
                }
            }
        } catch (e) {
            // Storage unavailable.
        }
        this.renderMessages();
    }
}

/**
 * Start the panel.
 *
 * @param {Object} config From \local_diverse_assistant\output\panel::get_js_config().
 */
export const init = config => {
    if (document.querySelector(Selectors.panel) && document.querySelector(Selectors.toggle)) {
        new Assistant(config);
    }
};
