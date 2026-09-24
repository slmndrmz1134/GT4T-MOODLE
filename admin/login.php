<?php
/**
 * GT4T Moodle - Custom Premium Administrative Login Portal
 *
 * This file handles secure admin-only authentications.
 */

require(__DIR__ . '/../config.php');

// Define metadata for premium design
$sitename = format_string($SITE->fullname, true, ['context' => context_system::instance()]);
$logourl = $CFG->wwwroot . '/theme/moove/pix/logo-gt4t.png'; // Fallback / default theme logo

// 1. If already logged in as site administrator, redirect directly to admin panel
if (isloggedin() && !isguestuser() && has_capability('moodle/site:config', context_system::instance())) {
    redirect(new moodle_url('/admin/index.php'));
}

$errormsg = '';
$error_type = optional_param('error', '', PARAM_ALPHANUMEXT);

// Pre-populate warning for unauthorized students/academicians redirected here
if ($error_type === 'notadmin') {
    $errormsg = 'Access Denied: You do not have administration privileges. Please log in using an administrator account.';
}

// 2. Handle Login Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic validation & sanitation
    $username = isset($_POST['username']) ? trim(core_text::strtolower($_POST['username'])) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($username) || empty($password)) {
        $errormsg = 'Please enter both your username and password.';
    } else {
        $errorcode = 0;
        // Authenticate credentials against Moodle's secure authentication engine
        $user = authenticate_user_login($username, $password, false, $errorcode);

        if ($user) {
            // Confirm the authenticated user has administrative capabilities
            if (is_siteadmin($user->id) || has_capability('moodle/site:config', context_system::instance(), $user->id)) {
                // Initialize session
                complete_user_login($user);
                \core\session\manager::apply_concurrent_login_limit($user->id, session_id());

                // Fetch return URL (default to admin panel, or wantsurl if it points inside admin area)
                $urltogo = new moodle_url('/admin/index.php');
                if (isset($SESSION->wantsurl) && strpos($SESSION->wantsurl, '/admin/') !== false) {
                    $urltogo = $SESSION->wantsurl;
                    unset($SESSION->wantsurl);
                }

                // Redirect logged-in admin
                redirect($urltogo);
            } else {
                // Deny standard users (e.g. Students, Academicians)
                $errormsg = 'Access Denied: The credentials provided do not have administrative access.';
            }
        } else {
            $errormsg = 'Invalid username or password. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Administration Portal | <?php echo s($sitename); ?></title>
    
    <!-- Premium Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-gradient: linear-gradient(135deg, #0b0f19 0%, #111827 50%, #070a13 100%);
            --accent-cyan: #00f2fe;
            --accent-blue: #4facfe;
            --accent-green: #00f260;
            --accent-mint: #0575e6;
            --text-primary: #ffffff;
            --text-secondary: #9ca3af;
            --glass-bg: rgba(17, 24, 39, 0.7);
            --glass-border: rgba(255, 255, 255, 0.08);
            --input-bg: rgba(255, 255, 255, 0.04);
            --input-border: rgba(255, 255, 255, 0.08);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Outfit', sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        body {
            background: var(--bg-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            position: relative;
            color: var(--text-primary);
        }

        /* Abstract dynamic mesh background particles */
        body::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(0, 242, 254, 0.12) 0%, rgba(0, 0, 0, 0) 70%);
            top: -10%;
            left: -10%;
            border-radius: 50%;
            z-index: 1;
            filter: blur(60px);
            animation: floatLarge 25s infinite alternate ease-in-out;
        }

        body::after {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(5, 117, 230, 0.15) 0%, rgba(0, 0, 0, 0) 70%);
            bottom: -15%;
            right: -10%;
            border-radius: 50%;
            z-index: 1;
            filter: blur(80px);
            animation: floatLarge 30s infinite alternate-reverse ease-in-out;
        }

        @keyframes floatLarge {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(80px, 50px) scale(1.1); }
            100% { transform: translate(-40px, -60px) scale(0.95); }
        }

        .portal-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 460px;
            padding: 20px;
        }

        /* Premium Glassmorphic Card container */
        .portal-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 45px 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5),
                        0 0 40px rgba(0, 242, 254, 0.05);
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.4s ease;
        }

        .portal-card:hover {
            box-shadow: 0 30px 60px -10px rgba(0, 0, 0, 0.6),
                        0 0 50px rgba(0, 242, 254, 0.08);
        }

        /* Brand & Headers */
        .portal-header {
            text-align: center;
            margin-bottom: 35px;
        }

        .portal-logo {
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, var(--accent-cyan) 0%, var(--accent-blue) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 12px;
            display: inline-block;
        }

        .portal-subtitle {
            font-size: 14px;
            color: var(--text-secondary);
            font-weight: 400;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* Floating labels input group */
        .input-group {
            position: relative;
            margin-bottom: 24px;
        }

        .input-control {
            width: 100%;
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 12px;
            padding: 16px 16px 16px 45px;
            color: var(--text-primary);
            font-size: 15px;
            font-weight: 400;
            outline: none;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .input-control:focus {
            border-color: var(--accent-cyan);
            box-shadow: 0 0 0 3px rgba(0, 242, 254, 0.15);
            background: rgba(255, 255, 255, 0.06);
        }

        /* Input icons */
        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            fill: var(--text-secondary);
            transition: fill 0.3s ease;
            pointer-events: none;
        }

        .input-control:focus + .input-icon {
            fill: var(--accent-cyan);
        }

        /* Password visibility toggle button */
        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .password-toggle svg {
            width: 18px;
            height: 18px;
            fill: var(--text-secondary);
            transition: fill 0.3s ease, transform 0.2s ease;
        }

        .password-toggle:hover svg {
            fill: var(--text-primary);
            transform: scale(1.1);
        }

        /* Premium Button */
        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, var(--accent-cyan) 0%, var(--accent-mint) 100%);
            border: none;
            border-radius: 12px;
            padding: 16px;
            color: #0b0f19;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 242, 254, 0.2);
            margin-top: 10px;
        }

        .btn-submit::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.25), transparent);
            transition: all 0.5s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 242, 254, 0.35);
        }

        .btn-submit:hover::before {
            left: 100%;
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        /* Alert Callout */
        .alert-box {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 12px;
            padding: 14px 16px;
            margin-bottom: 25px;
            font-size: 14px;
            line-height: 1.5;
            color: #fca5a5;
            display: flex;
            align-items: flex-start;
            animation: shakeAlert 0.4s ease;
        }

        @keyframes shakeAlert {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-6px); }
            40%, 80% { transform: translateX(6px); }
        }

        .alert-icon {
            width: 18px;
            height: 18px;
            fill: #ef4444;
            margin-right: 12px;
            margin-top: 2px;
            flex-shrink: 0;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 25px;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease;
        }

        .back-link:hover {
            color: var(--accent-cyan);
        }

        .portal-footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.25);
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>

<div class="portal-wrapper">
    <div class="portal-card">
        <div class="portal-header">
            <span class="portal-logo">GT4T HUB</span>
            <div class="portal-subtitle">Administration</div>
        </div>

        <?php if (!empty($errormsg)): ?>
            <div class="alert-box">
                <svg class="alert-icon" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                </svg>
                <div><?php echo htmlspecialchars($errormsg); ?></div>
            </div>
        <?php endif; ?>

        <form action="<?php echo s($CFG->wwwroot); ?>/admin/login.php" method="POST" autocomplete="off">
            <input type="hidden" name="logintoken" value="<?php echo s(\core\session\manager::get_login_token()); ?>">
            
            <div class="input-group">
                <input type="text" 
                       id="username" 
                       name="username" 
                       class="input-control" 
                       placeholder="Administrator Username" 
                       required 
                       autofocus
                       autocomplete="off">
                <svg class="input-icon" viewBox="0 0 24 24">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
            </div>

            <div class="input-group">
                <input type="password" 
                       id="password" 
                       name="password" 
                       class="input-control" 
                       placeholder="Secure Password" 
                       required
                       autocomplete="off">
                <svg class="input-icon" viewBox="0 0 24 24">
                    <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
                </svg>
                <button type="button" class="password-toggle" id="pwdToggle" aria-label="Toggle password visibility">
                    <!-- SVG Eye Icon -->
                    <svg id="eyeIcon" viewBox="0 0 24 24">
                        <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                    </svg>
                </button>
            </div>

            <button type="submit" class="btn-submit">Authenticate Securely</button>
        </form>

        <a href="<?php echo s($CFG->wwwroot); ?>/" class="back-link">&larr; Return to Homepage</a>
    </div>
    
    <div class="portal-footer">
        Powered by GT4T &bull; Secure Administration
    </div>
</div>

<script>
    // Password Reveal/Hide Functionality
    document.addEventListener('DOMContentLoaded', () => {
        const pwdInput = document.getElementById('password');
        const pwdToggle = document.getElementById('pwdToggle');
        const eyeIcon = document.getElementById('eyeIcon');

        // SVG eye shapes
        const eyeOpenPath = "M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z";
        const eyeClosedPath = "M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.82l2.92 2.92c1.51-1.26 2.7-2.89 3.44-4.74-1.73-4.39-6-7.5-11-7.5-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2 4.27l2.28 2.28.46.46C3.08 8.3 1.78 10.02 1 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l.42.42L19.73 22 21 20.73 3.27 3 2 4.27zM7.53 9.8l1.55 1.55c-.05.21-.08.43-.08.65 0 1.66 1.34 3 3 3 .22 0 .44-.03.65-.08l1.55 1.55c-.67.33-1.41.53-2.2.53-2.76 0-5-2.24-5-5 0-.79.2-1.53.53-2.2zm4.31-.78l3.15 3.15.01-.17c0-1.66-1.34-3-3-3l-.16.02z";

        pwdToggle.addEventListener('click', (e) => {
            e.preventDefault();
            
            const isPassword = pwdInput.type === 'password';
            pwdInput.type = isPassword ? 'text' : 'password';
            
            // Toggle eye icon path dynamically
            eyeIcon.querySelector('path').setAttribute('d', isPassword ? eyeClosedPath : eyeOpenPath);
        });
    });
</script>
</body>
</html>
