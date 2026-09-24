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
 * GT4T front page mobile menu (hamburger).
 *
 * @module     theme_moove/frontpage_drawer
 * @copyright  2025 GT4T
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

const MENU_ID = 'gt4t-mobile-menu';
const OPEN_CLASS = 'gt4t-mobile-menu-open';
const TOGGLER_SELECTOR = '#gt4t-navbar-toggler, .gt4t-navbar-toggler';

/**
 * @returns {boolean}
 */
const isOpen = () => document.body.classList.contains(OPEN_CLASS);

/**
 * Close the mobile menu.
 */
const closeMenu = () => {
    document.body.classList.remove(OPEN_CLASS);
    const menu = document.getElementById(MENU_ID);
    const toggler = document.querySelector(TOGGLER_SELECTOR);
    if (menu) {
        menu.setAttribute('aria-hidden', 'true');
        menu.setAttribute('hidden', '');
    }
    if (toggler) {
        toggler.setAttribute('aria-expanded', 'false');
    }
};

/**
 * Open the mobile menu.
 */
const openMenu = () => {
    document.body.classList.add(OPEN_CLASS);
    const menu = document.getElementById(MENU_ID);
    const toggler = document.querySelector(TOGGLER_SELECTOR);
    if (menu) {
        menu.setAttribute('aria-hidden', 'false');
        menu.removeAttribute('hidden');
    }
    if (toggler) {
        toggler.setAttribute('aria-expanded', 'true');
    }
};

/**
 * @param {Event} event
 */
const handleTogglerClick = (event) => {
    event.preventDefault();
    event.stopPropagation();
    if (isOpen()) {
        closeMenu();
    } else {
        openMenu();
    }
};

/**
 * Bind click on hamburger (capture phase so nothing swallows it first).
 *
 * @returns {boolean}
 */
const bindToggler = () => {
    const menu = document.getElementById(MENU_ID);
    const toggler = document.querySelector(TOGGLER_SELECTOR);

    if (!menu || !toggler) {
        return false;
    }

    toggler.addEventListener('click', handleTogglerClick, true);
    toggler.addEventListener('touchend', handleTogglerClick, {passive: false, capture: true});

    return true;
};

/**
 * Initialise mobile menu toggling.
 */
export const init = () => {
    if (!bindToggler()) {
        return;
    }

    const menu = document.getElementById(MENU_ID);
    menu.querySelectorAll('.gt4t-mobile-menu-backdrop, .gt4t-mobile-menu-close').forEach((el) => {
        el.addEventListener('click', (event) => {
            event.preventDefault();
            closeMenu();
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && isOpen()) {
            closeMenu();
        }
    });
};

/**
 * Close menu when a section link is activated (called from frontpage_nav).
 */
export const closeIfOpen = () => {
    if (isOpen()) {
        closeMenu();
    }
};
