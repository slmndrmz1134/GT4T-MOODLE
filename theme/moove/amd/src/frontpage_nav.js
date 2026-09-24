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
 * Smooth in-page scrolling and active section highlighting on the GT4T front page.
 *
 * @module     theme_moove/frontpage_nav
 * @copyright  2025 GT4T
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

const NAVBAR_OFFSET = 78;
const SECTION_SELECTOR = '#gt4t-hero, #gt4t-features, #gt4t-collaborate, #gt4t-impact, #gt4t-faq';

/**
 * Scroll to a section element with fixed navbar offset.
 *
 * @param {HTMLElement} target
 */
const scrollToSection = (target) => {
    const top = target.getBoundingClientRect().top + window.pageYOffset - NAVBAR_OFFSET;
    window.scrollTo({top: top, behavior: 'smooth'});
};

/**
 * @param {string} sectionId
 */
const setActiveSection = (sectionId) => {
    document.querySelectorAll('.gt4t-nav-scroll[data-gt4t-section]').forEach((link) => {
        const active = link.getAttribute('data-gt4t-section') === sectionId;
        link.classList.toggle('active', active);
        if (active) {
            link.setAttribute('aria-current', 'true');
        } else {
            link.removeAttribute('aria-current');
        }
    });
};

/**
 * Initialise front page navigation behaviour.
 *
 * @param {{closeIfOpen?: function(): void}} [drawerApi] Mobile menu API from frontpage_drawer.
 */
export const init = (drawerApi) => {
    const links = document.querySelectorAll('.gt4t-nav-scroll[href^="#"]');
    if (!links.length) {
        return;
    }

    links.forEach((link) => {
        link.addEventListener('click', (event) => {
            const hash = link.getAttribute('href');
            if (!hash || hash.length < 2) {
                return;
            }
            const target = document.querySelector(hash);
            if (!target) {
                return;
            }
            event.preventDefault();
            scrollToSection(target);
            history.replaceState(null, '', hash);
            const sectionId = target.id;
            if (sectionId) {
                setActiveSection(sectionId);
            }

            if (drawerApi && typeof drawerApi.closeIfOpen === 'function') {
                drawerApi.closeIfOpen();
            }
        });
    });

    const sections = document.querySelectorAll(SECTION_SELECTOR);
    if (sections.length && 'IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting && entry.intersectionRatio >= 0.2) {
                    setActiveSection(entry.target.id);
                }
            });
        }, {
            rootMargin: `-${NAVBAR_OFFSET}px 0px -55% 0px`,
            threshold: [0, 0.2, 0.5],
        });

        sections.forEach((section) => observer.observe(section));
    }

    if (window.location.hash) {
        const target = document.querySelector(window.location.hash);
        if (target) {
            window.setTimeout(() => scrollToSection(target), 100);
            setActiveSection(target.id);
        }
    }
};
