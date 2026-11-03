// DIVERSE full-width drafts: the frame shared by the *-wide.html drafts (icons, icon rail, tab bar, the user's
// buttons and the palette switch). A page names its place with <body data-page="..."> and marks where the
// buttons go with <div class="top-actions"></div>.
(function () {
    const icons = {
        dashboard: '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
        search: '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
        book: '<path d="M4 19.5V5a2 2 0 0 1 2-2h14v15H6.5A2.5 2.5 0 0 0 4 20.5 2.5 2.5 0 0 0 6.5 23H20"/>',
        calendar: '<rect x="3" y="4.5" width="18" height="16.5" rx="2"/><path d="M3 9.5h18M8 2.5v4M16 2.5v4"/>',
        award: '<circle cx="12" cy="9" r="6"/><path d="m8.5 14-1.5 8 5-3 5 3-1.5-8"/>',
        message: '<path d="M21 12a8 8 0 0 1-11.6 7.1L4 20.5l1.4-5.1A8 8 0 1 1 21 12Z"/>',
        help: '<circle cx="12" cy="12" r="9"/><path d="M9.5 9.5a2.5 2.5 0 1 1 3.5 2.3c-.6.3-1 .9-1 1.6v.6M12 17h.01"/>',
        settings: '<path d="M4 6h10M18 6h2M4 12h4M12 12h8M4 18h12M20 18h0"/><circle cx="16" cy="6" r="2"/><circle cx="10" cy="12" r="2"/><circle cx="18" cy="18" r="2"/>',
        bell: '<path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9M10.3 21a1.9 1.9 0 0 0 3.4 0"/>',
        info: '<circle cx="12" cy="12" r="9"/><path d="M12 16v-4M12 8h.01"/>',
        play: '<circle cx="12" cy="12" r="9"/><path d="m10 8.5 5.5 3.5-5.5 3.5Z"/>',
        arrow: '<path d="M7 17 17 7M8 7h9v9"/>',
        chevron: '<path d="m9 18 6-6-6-6"/>',
        back: '<path d="m15 18-6-6 6-6"/>',
        down: '<path d="m6 9 6 6 6-6"/>',
        check: '<path d="M20 6 9 17l-5-5"/>',
        clock: '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        alert: '<path d="M12 8v5M12 16.5h.01"/><circle cx="12" cy="12" r="9"/>',
        up: '<path d="M12 19V5M5 12l7-7 7 7"/>',
        dots: '<circle cx="12" cy="5" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="12" cy="19" r="1"/>',
        filter: '<path d="M3 5h18M6 12h12M10 19h4"/>',
        flame: '<path d="M12 22a7 7 0 0 0 7-7c0-4-3-6-4-9-1.5 2-2 3.5-2 5-1.5-1-2.5-2.5-2.5-4C7 9 5 12 5 15a7 7 0 0 0 7 7Z"/>',
        trend: '<path d="m3 17 6-6 4 4 8-8M15 7h6v6"/>',
        plus: '<path d="M12 5v14M5 12h14"/>',
        star: '<path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2L12 17.3 6.4 20.2l1.1-6.2L3 9.6l6.2-.9Z"/>',
        grid: '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
        list: '<path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>',
        file: '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8Z"/><path d="M14 3v5h5M9 13h6M9 17h4"/>',
        upload: '<path d="M12 16V4M7 9l5-5 5 5M4 20h16"/>',
        quiz: '<circle cx="12" cy="12" r="9"/><path d="M9.5 9.5a2.5 2.5 0 1 1 3.5 2.3c-.6.3-1 .9-1 1.6v.6M12 17h.01"/>',
        video: '<rect x="3" y="5" width="13" height="14" rx="2"/><path d="m16 10 5-3v10l-5-3"/>',
        forum: '<path d="M17 9V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v9l3-3h3M21 11a2 2 0 0 0-2-2h-8a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h7l3 3Z"/>',
        live: '<circle cx="12" cy="12" r="2"/><path d="M16.2 7.8a6 6 0 0 1 0 8.4M7.8 16.2a6 6 0 0 1 0-8.4M19 5a10 10 0 0 1 0 14M5 19A10 10 0 0 1 5 5"/>',
        sparkle: '<path d="M12 3v4M12 17v4M3 12h4M17 12h4M6 6l2.5 2.5M15.5 15.5 18 18M6 18l2.5-2.5M15.5 8.5 18 6"/>',
        users: '<circle cx="9" cy="8" r="4"/><path d="M2 21a7 7 0 0 1 14 0M16 4a4 4 0 0 1 0 8M22 21a7 7 0 0 0-4-6.3"/>',
        globe: '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/>',
        x: '<path d="M18 6 6 18M6 6l12 12"/>',
        lock: '<rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/>',
        moon: '<path d="M20.5 14.5A8.5 8.5 0 0 1 9.5 3.5a8.5 8.5 0 1 0 11 11Z"/>',
        sun: '<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/>',
    };
    document.body.insertAdjacentHTML('afterbegin', '<svg width="0" height="0" style="position:absolute" aria-hidden="true">' +
        Object.entries(icons).map(([id, body]) => `<symbol id="i-${id}" viewBox="0 0 24 24">${body}</symbol>`).join('') + '</svg>');

    const icon = (id, cls = '') => `<svg class="i ${cls}"><use href="#i-${id}"/></svg>`;
    window.diverseIcon = icon;

    const places = [
        ['dashboard', 'Dashboard', 'dashboard', 'Dashboard-wide.html'],
        ['discover', 'Discover', 'search', 'Discover-wide.html'],
        ['courses', 'My courses', 'book', 'MyCourses-wide.html'],
        ['calendar', 'Calendar', 'calendar', '#'],
        ['grades', 'Grades', 'award', '#'],
        ['messages', 'Messages', 'message', '#'],
    ];
    const page = document.body.dataset.page;
    const link = ([key, label, ic, href], rail) => `<a href="${href}"${key === page ? ' class="active" aria-current="page"' : ''}` +
        (rail ? ` data-tip="${label}">${icon(ic)}<span class="sr-only">${label}</span></a>` : `>${icon(ic)}${label}</a>`);

    document.body.insertAdjacentHTML('afterbegin', `<nav class="rail" aria-label="Main">
        <div class="rail-logo" title="İstanbul Beykent Üniversitesi">B</div>
        ${places.map(p => link(p, true)).join('')}
        <div class="rail-sep"></div>
        ${link(['guide', 'Platform guide', 'play', '#'], true)}
        <div class="rail-end">
            <a href="#" class="rail-mode" role="button" data-mode-toggle data-tip="Dark mode" aria-pressed="false">${icon('moon', 'moon')}${icon('sun', 'sun')}<span class="sr-only">Dark mode</span></a>
            ${link(['help', 'Help', 'help', '#'], true)}
            ${link(['preferences', 'Preferences', 'settings', '#'], true)}
            <span class="avatar" title="Student 1">S1</span>
        </div>
    </nav>`);
    document.body.insertAdjacentHTML('beforeend', `<nav class="tabbar" aria-label="Main">
        ${places.filter(p => p[0] !== 'grades').slice(0, 5).map(p => link(p, false)).join('')}</nav>`);

    const switcher = '<div class="palette-switch" role="group" aria-label="Colours (draft only)">' +
        '<button type="button" data-set="orange">Orange</button><button type="button" data-set="green">Green</button></div>';
    document.querySelectorAll('.top-actions').forEach(el => el.insertAdjacentHTML('beforeend',
        (el.dataset.search === 'no' ? '' : `<label class="search">${icon('search')}<span class="sr-only">Search</span><input placeholder="What do you want to learn?"></label>`) +
        switcher + `<button class="chip-btn" type="button">EN ${icon('down', 'sm')}</button>
        <button class="icon-btn" type="button" aria-label="Notifications">${icon('bell')}<span class="dot"></span></button>
        <button class="icon-btn" type="button" aria-label="Messages">${icon('message')}</button>`));
    document.querySelector('main').insertAdjacentHTML('beforeend', switcher.replace('palette-switch', 'palette-switch mobile-palette'));

    // The palette switch of the drafts; the choice carries over to the other drafts.
    function setPalette(name) {
        document.documentElement.dataset.palette = name;
        document.querySelectorAll('[data-set]').forEach(b => b.setAttribute('aria-pressed', String(b.dataset.set === name)));
        try { localStorage.setItem('diverse-draft-palette', name); } catch (e) { /* Private window: not remembered. */ }
    }
    // The dark mode switch of the drafts, remembered like the palette.
    function setMode(dark) {
        document.documentElement.dataset.mode = dark ? 'dark' : 'light';
        document.querySelectorAll('[data-mode-toggle]').forEach(b => {
            b.setAttribute('aria-pressed', String(dark));
            b.dataset.tip = dark ? 'Light mode' : 'Dark mode';
        });
        try { localStorage.setItem('diverse-draft-mode', dark ? 'dark' : 'light'); } catch (e) { /* Private window. */ }
    }
    let mode = 'light';
    try { mode = localStorage.getItem('diverse-draft-mode') || 'light'; } catch (e) { /* Private window. */ }
    setMode(mode === 'dark');
    document.addEventListener('click', e => {
        const button = e.target.closest('[data-mode-toggle]');
        if (button) {
            e.preventDefault();
            setMode(document.documentElement.dataset.mode !== 'dark');
        }
    });

    let saved = 'orange';
    try { saved = localStorage.getItem('diverse-draft-palette') || 'orange'; } catch (e) { /* Private window. */ }
    setPalette(saved);
    document.addEventListener('click', e => {
        const button = e.target.closest('[data-set]');
        if (button) {
            setPalette(button.dataset.set);
        }
    });

    // Icons written as <i data-i="name"></i> in the pages.
    document.querySelectorAll('i[data-i]').forEach(el => { el.outerHTML = icon(el.dataset.i, el.className); });
})();
