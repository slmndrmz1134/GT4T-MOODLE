// AMD module to handle login/signup role toggling between Students and Academicians.
define([], function() {

    const init = (opts) => {
        const isSignup = opts && opts.page === 'signup';
        const panel = document.querySelector('.gt4t-signin-panel');
        if (!panel) {
            return;
        }

        // Create the elegant sliding toggle switch at the top of the card
        const header = panel.querySelector('.gt4t-signin-header');
        if (!header) {
            return;
        }

        const toggleWrapper = document.createElement('div');
        toggleWrapper.className = 'gt4t-role-toggle-wrapper';
        toggleWrapper.innerHTML = `
            <button type="button" class="gt4t-role-btn active" data-role="student">
                <span>Student</span>
            </button>
            <button type="button" class="gt4t-role-btn" data-role="academician">
                <span>Academician</span>
            </button>
            <div class="gt4t-role-slider"></div>
        `;

        // Insert right after the logo, or as the first element of the header
        const logo = header.querySelector('.gt4t-signin-logo');
        if (logo) {
            logo.after(toggleWrapper);
        } else {
            header.insertBefore(toggleWrapper, header.firstChild);
        }

        // Add a hidden role input to the form
        const form = panel.querySelector('form');
        let roleInput = null;
        if (form) {
            roleInput = document.createElement('input');
            roleInput.type = 'hidden';
            roleInput.name = 'user_role';
            roleInput.value = 'student';
            form.appendChild(roleInput);
        }

        // Keep references to subheadings
        const subtitle = panel.querySelector('.gt4t-signin-subtitle');
        const defaultSubtitleText = subtitle ? subtitle.textContent : '';

        // Role-specific descriptions
        const subtexts = {
            student: defaultSubtitleText || 'Sign in using your student account and access your learning hub.',
            academician: 'Sign in to the faculty portal to manage courses and academic operations.'
        };

        const buttons = toggleWrapper.querySelectorAll('.gt4t-role-btn');
        const slider = toggleWrapper.querySelector('.gt4t-role-slider');

        const setRole = (role) => {
            buttons.forEach(btn => {
                if (btn.getAttribute('data-role') === role) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });

            // Update theme class on panel to trigger SCSS color shifts
            panel.classList.remove('gt4t-theme--student', 'gt4t-theme--academician');
            panel.classList.add('gt4t-theme--' + role);

            // Update slider positioning
            if (role === 'academician') {
                slider.style.transform = 'translateX(100%)';
            } else {
                slider.style.transform = 'translateX(0)';
            }

            // Update subtitle texts
            if (subtitle) {
                subtitle.textContent = subtexts[role];
            }

            // Update hidden input
            if (roleInput) {
                roleInput.value = role;
            }

            // Signup page field filtering
            if (isSignup && form) {
                // Dynamically scan form rows and toggle their visibility based on roles
                const formItems = form.querySelectorAll('.fitem');
                formItems.forEach(item => {
                    const label = item.querySelector('.col-form-label');
                    const text = label ? label.textContent.toLowerCase() : '';
                    const id = item.id ? item.id.toLowerCase() : '';

                    // Identify student-specific fields
                    const isStudentField = text.includes('student') || id.includes('student');
                    // Identify academician-specific fields
                    const isAcademicField = text.includes('academic') ||
                        text.includes('faculty') ||
                        text.includes('akad') ||
                        id.includes('academic') ||
                        id.includes('faculty');

                    if (isStudentField) {
                        item.style.display = (role === 'student') ? 'flex' : 'none';
                    } else if (isAcademicField) {
                        item.style.display = (role === 'academician') ? 'flex' : 'none';
                    }
                });
            }
        };

        // Click handlers for switching
        buttons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const role = btn.getAttribute('data-role');
                setRole(role);
            });
        });

        // Initialize default view
        setRole('student');
    };

    return {
        init: init
    };
});
