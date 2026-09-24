// GT4T login/signup password visibility toggles.
define(['core/togglesensitive'], function(ToggleSensitive) {

    // Register a global capture-phase event listener to handle password visibility toggling.
    // This bypasses the buggy core/togglesensitive click listener which has a singleton bug
    // that prevents multiple password toggle buttons on the same page from working correctly.
    document.addEventListener('click', function(event) {
        const button = event.target.closest('.toggle-sensitive-btn');
        if (!button) {
            return;
        }

        // Intercept and prevent Moodle's buggy singleton listener from running
        event.stopPropagation();
        event.stopImmediatePropagation();

        const wrapper = button.closest('.toggle-sensitive-wrapper');
        if (!wrapper) {
            return;
        }

        const input = wrapper.querySelector('input');
        if (!input) {
            return;
        }

        const isPassword = input.getAttribute('type') === 'password';
        const nextType = isPassword ? 'text' : 'password';
        input.setAttribute('type', nextType);

        // Toggle FontAwesome icons if present
        const icon = button.querySelector('i');
        if (icon) {
            if (isPassword) {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Toggle standard Moodle image icons if present
        const img = button.querySelector('img');
        if (img) {
            let src = img.getAttribute('src');
            if (src) {
                if (isPassword) {
                    src = src.replace('show', 'hide');
                    img.setAttribute('alt', 'Hide password');
                    img.setAttribute('title', 'Hide password');
                } else {
                    src = src.replace('hide', 'show');
                    img.setAttribute('alt', 'Show password');
                    img.setAttribute('title', 'Show password');
                }
                img.setAttribute('src', src);
            }
        }
    }, true); // Use capture phase to intercept the event before Moodle's bubble-phase listener sees it!

    const initField = (id) => {
        const el = document.getElementById(id);
        if (el && !el.closest('.toggle-sensitive-wrapper')) {
            ToggleSensitive.init(id, false);
        }
    };

    const initLogin = () => {
        initField('password');
    };

    const initSignup = () => {
        initField('id_password');
        initField('id_password2');
    };

    return {
        initLogin,
        initSignup,
    };
});

