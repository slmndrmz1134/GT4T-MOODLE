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
 * Initialise navbar controls on the GT4T front page (notifications, messages, user menu).
 *
 * @module     theme_moove/frontpage_navbar
 * @copyright  2025 GT4T
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([
    'jquery',
    'core/pubsub',
    'core/drawer_events',
    'core/usermenu',
    'theme_moove/accessibilitysettings',
    'message_popup/notification_popover_controller',
    'core_message/message_popover',
], function(
    $,
    PubSub,
    DrawerEvents,
    UserMenu,
    AccessibilitySettings,
    NotificationPopoverController,
    MessagePopover
) {
    /**
     * When the message drawer closes, move focus back to the navbar toggle.
     * Avoids Chrome "Blocked aria-hidden on an element because its descendant retained focus".
     */
    const registerMessageDrawerFocusFix = () => {
        PubSub.subscribe(DrawerEvents.DRAWER_HIDDEN, (root) => {
            const drawer = root instanceof $ ? root[0] : root;
            if (!drawer || drawer.getAttribute('data-region') !== 'right-hand-drawer') {
                return;
            }

            const active = document.activeElement;
            if (!active || !drawer.contains(active)) {
                return;
            }

            const originId = drawer.getAttribute('data-origin');
            const toggle = originId ? document.getElementById(originId) : null;
            if (toggle) {
                toggle.focus();
            } else {
                active.blur();
            }
        });
    };

    /**
     * Bind notification and message popovers plus the profile dropdown.
     */
    const init = () => {
        registerMessageDrawerFocusFix();

        if (document.querySelector('.usermenu')) {
            UserMenu.init();
        }

        if (document.getElementById('accessibilitysettings-control')) {
            AccessibilitySettings.init();
        }

        const notificationRoot = $('#nav-notification-popover-container');
        if (notificationRoot.length) {
            const controller = new NotificationPopoverController(notificationRoot);
            controller.registerEventListeners();
            controller.registerListNavigationEventListeners();
        }

        document.querySelectorAll('[id^="message-drawer-toggle-"]').forEach((toggle) => {
            MessagePopover.init($(toggle));
        });
    };

    return {init};
});
