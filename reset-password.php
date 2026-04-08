<?php
include 'includes/db.php';

$message = '';
$preferredTheme = (isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'light') ? 'light' : 'dark';

if (!isset($_GET['token'])) {
    header('Location: login.php');
    exit();
}

$token = mysqli_real_escape_string($conn, $_GET['token']);
$check = mysqli_query($conn, "SELECT * FROM users WHERE reset_token='$token'");
$invalidLink = mysqli_num_rows($check) === 0;

if (!$invalidLink && isset($_POST['update_pass'])) {
    $p1 = $_POST['pass'] ?? '';
    $p2 = $_POST['conf_pass'] ?? '';

    if ($p1 === $p2) {
        $hashed = password_hash($p1, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE users SET password='$hashed', reset_token=NULL WHERE reset_token='$token'");
        echo "<script>alert('Password updated. Please log in.'); window.location.href='login.php';</script>";
        exit();
    }

    $message = 'Passwords do not match.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | TravelBlog</title>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg: #07131f;
            --card-bg: rgba(9, 16, 28, 0.76);
            --card-border: rgba(148, 163, 184, 0.16);
            --text-main: #f8fafc;
            --text-muted: #9fb0c2;
            --accent: #22c55e;
            --accent-strong: #16a34a;
            --surface: rgba(255, 255, 255, 0.08);
            --danger-bg: rgba(244, 63, 94, 0.16);
            --danger-text: #fecdd3;
            --danger-border: rgba(244, 63, 94, 0.22);
        }

        [data-theme="light"] {
            --bg: #eef4ff;
            --card-bg: rgba(255, 255, 255, 0.68);
            --card-border: rgba(99, 102, 241, 0.12);
            --text-main: #142033;
            --text-muted: #5f6b7a;
            --surface: rgba(255, 255, 255, 0.78);
            --danger-bg: rgba(244, 63, 94, 0.08);
            --danger-text: #be123c;
            --danger-border: rgba(244, 63, 94, 0.16);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100svh;
            padding: 24px 16px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-main);
            background:
                linear-gradient(rgba(7, 19, 31, 0.62), rgba(7, 19, 31, 0.72)),
                radial-gradient(circle at top left, rgba(34, 197, 94, 0.14), transparent 28%),
                radial-gradient(circle at top right, rgba(56, 189, 248, 0.12), transparent 34%),
                url('https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=1600&q=80') center/cover no-repeat fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            transition: background 0.25s ease, color 0.25s ease;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background: linear-gradient(180deg, rgba(7, 19, 31, 0.16), rgba(7, 19, 31, 0.54));
            pointer-events: none;
        }

        body[data-theme="light"] {
            background:
                linear-gradient(rgba(238, 244, 255, 0.26), rgba(238, 244, 255, 0.58)),
                radial-gradient(circle at top left, rgba(34, 197, 94, 0.12), transparent 28%),
                radial-gradient(circle at top right, rgba(56, 189, 248, 0.12), transparent 34%),
                url('https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=1600&q=80') center/cover no-repeat fixed;
        }

        .theme-toggle {
            position: fixed;
            top: 18px;
            right: 18px;
            width: 46px;
            height: 46px;
            border: 1px solid var(--card-border);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--surface);
            color: var(--text-main);
            backdrop-filter: blur(20px);
            box-shadow: 0 18px 38px rgba(2, 6, 23, 0.16);
            cursor: pointer;
            z-index: 4;
            transition: transform 0.2s ease, background 0.2s ease;
        }

        .theme-toggle:hover {
            transform: translateY(-1px);
        }

        .auth-shell {
            position: relative;
            z-index: 1;
            width: min(100%, 460px);
        }

        .auth-card {
            border-radius: 34px;
            border: 1px solid var(--card-border);
            background: var(--card-bg);
            backdrop-filter: blur(24px);
            box-shadow: 0 30px 70px rgba(2, 6, 23, 0.32);
            padding: 34px 28px 28px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 34px;
            padding: 0 14px;
            border-radius: 999px;
            background: rgba(34, 197, 94, 0.14);
            border: 1px solid rgba(74, 222, 128, 0.18);
            color: #bbf7d0;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        body[data-theme="light"] .eyebrow {
            color: #15803d;
            background: rgba(34, 197, 94, 0.1);
        }

        .step-indicator {
            display: flex;
            gap: 8px;
            margin: 22px 0 18px;
        }

        .dot {
            width: 9px;
            height: 9px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.16);
        }

        .dot.active {
            width: 30px;
            background: linear-gradient(135deg, var(--accent), var(--accent-strong));
        }

        h1 {
            margin: 0;
            font-size: clamp(2rem, 5vw, 2.65rem);
            line-height: 0.98;
            letter-spacing: -0.05em;
        }

        h1 span {
            font-family: 'Instrument Serif', serif;
            font-style: italic;
            font-weight: 400;
        }

        .subtitle {
            margin: 14px 0 28px;
            color: var(--text-muted);
            line-height: 1.7;
            font-size: 0.96rem;
        }

        .input-group {
            position: relative;
            margin-bottom: 14px;
        }

        .input-group i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--accent);
            font-size: 1rem;
        }

        input {
            width: 100%;
            min-height: 54px;
            padding: 0 16px 0 50px;
            border-radius: 18px;
            border: 1px solid rgba(148, 163, 184, 0.14);
            background: rgba(255, 255, 255, 0.08);
            color: var(--text-main);
            font-size: 0.98rem;
            outline: none;
            transition: border-color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
        }

        body[data-theme="light"] input {
            background: rgba(255, 255, 255, 0.82);
        }

        input::placeholder {
            color: rgba(159, 176, 194, 0.82);
        }

        input:focus {
            border-color: rgba(34, 197, 94, 0.34);
            background: rgba(255, 255, 255, 0.12);
            box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.12);
        }

        body[data-theme="light"] input:focus {
            background: rgba(255, 255, 255, 0.94);
        }

        .primary-btn,
        button[type="submit"] {
            width: 100%;
            min-height: 54px;
            border: none;
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 8px;
            background: linear-gradient(135deg, var(--accent), var(--accent-strong));
            color: #ffffff;
            font-size: 0.98rem;
            font-weight: 800;
            text-decoration: none;
            cursor: pointer;
            box-shadow: 0 18px 34px rgba(34, 197, 94, 0.24);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .primary-btn:hover,
        button[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 22px 40px rgba(34, 197, 94, 0.3);
        }

        .status-message {
            margin-top: 16px;
            padding: 13px 14px;
            border-radius: 16px;
            border: 1px solid var(--danger-border);
            background: var(--danger-bg);
            color: var(--danger-text);
            font-size: 0.9rem;
            line-height: 1.6;
        }

        .card-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin-top: 22px;
        }

        .secondary-link {
            color: var(--text-main);
            text-decoration: none;
            font-weight: 700;
            font-size: 0.94rem;
            opacity: 0.88;
        }

        .secondary-link:hover {
            opacity: 1;
        }

        @media (max-width: 680px) {
            body {
                padding: 18px 12px;
                align-items: stretch;
            }

            .theme-toggle {
                top: 12px;
                right: 12px;
                width: 42px;
                height: 42px;
            }

            .auth-shell {
                display: flex;
                align-items: center;
            }

            .auth-card {
                width: 100%;
                padding: 28px 20px 22px;
                border-radius: 26px;
            }

            .card-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .secondary-link {
                text-align: center;
            }
        }
    </style>
</head>
<body class="auth-page" data-theme="<?php echo $preferredTheme; ?>">
    <button class="theme-toggle" id="themeToggle" type="button" onclick="toggleTheme()" aria-label="Toggle theme">
        <i class="fas fa-moon" id="themeIcon"></i>
    </button>

    <main class="auth-shell">
        <section class="auth-card">
            <span class="eyebrow"><i class="fas fa-shield-heart"></i> Account Recovery</span>

            <?php if ($invalidLink): ?>
                <div class="step-indicator">
                    <span class="dot active"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                </div>
                <h1>Reset link <span>expired</span></h1>
                <p class="subtitle">This password reset link is no longer valid. Request a fresh link and get back into the account safely.</p>
                <div class="card-actions">
                    <a href="forgot-password.php" class="primary-btn">Request New Link</a>
                    <a href="login.php" class="secondary-link">Back to Login</a>
                </div>
            <?php else: ?>
                <div class="step-indicator">
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot active"></span>
                </div>
                <h1>Set a fresh <span>password</span></h1>
                <p class="subtitle">Create a strong password so the next sign-in feels instant, secure, and smooth.</p>

                <form method="POST">
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="pass" placeholder="New password" required minlength="6">
                    </div>
                    <div class="input-group">
                        <i class="fas fa-shield-alt"></i>
                        <input type="password" name="conf_pass" placeholder="Confirm password" required>
                    </div>
                    <button type="submit" name="update_pass">Update Password</button>
                </form>

                <?php if ($message !== ''): ?>
                    <div class="status-message"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>

                <div class="card-actions">
                    <span></span>
                    <a href="login.php" class="secondary-link">Back to Login</a>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <script>
        function syncThemeIcon() {
            const icon = document.getElementById('themeIcon');
            if (!icon) return;

            const isLight = document.body.getAttribute('data-theme') === 'light';
            icon.className = isLight ? 'fas fa-sun' : 'fas fa-moon';
        }

        function applySavedTheme() {
            const storedTheme = localStorage.getItem('theme') || '<?php echo $preferredTheme; ?>';
            document.body.setAttribute('data-theme', storedTheme === 'light' ? 'light' : 'dark');
            syncThemeIcon();
        }

        function toggleTheme() {
            const body = document.body;
            const nextTheme = body.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
            body.setAttribute('data-theme', nextTheme);
            localStorage.setItem('theme', nextTheme);
            document.cookie = 'theme=' + nextTheme + ';path=/';
            syncThemeIcon();
        }

        document.addEventListener('DOMContentLoaded', applySavedTheme);
    </script>
</body>
</html>
