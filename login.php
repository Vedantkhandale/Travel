<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'includes/db.php';
include 'includes/layout_components.php';
session_start();

$message = '';
$loginSuccess = false;

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

    if (mysqli_num_rows($query) > 0) {
        $user = mysqli_fetch_assoc($query);

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $loginSuccess = true;
        } else {
            $message = 'Wrong password.';
        }
    } else {
        $message = 'User not found.';
    }
}

$preferredTheme = (isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'light') ? 'light' : 'dark';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | TravelBlog</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/index.css?v=3">
    <link rel="stylesheet" href="assets/css/enhance.css?v=46">
    <link rel="stylesheet" href="assets/css/sexy-theme.css?v=1">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }

        body.auth-page {
            min-height: 100vh;
            padding: 110px 16px 120px;
        }

        .login-card {
            background: rgba(30, 41, 59, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 45px 35px;
            border-radius: 30px;
            width: 100%;
            max-width: 420px;
            text-align: center;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
            color: white;
            animation: slideUp 0.8s ease;
            z-index: 2;
            margin: 0 auto;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 { font-size: 2.2rem; font-weight: 800; margin-bottom: 8px; letter-spacing: -1px; }

        .subtitle { color: rgba(255, 255, 255, 0.7); font-size: 0.9rem; margin-bottom: 35px; }

        .input-box { position: relative; margin-bottom: 20px; }

        .input-box i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #818cf8;
            font-size: 1.1rem;
        }

        input {
            width: 100%;
            padding: 15px 15px 15px 52px;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            color: #ffffff;
            font-size: 1rem;
            outline: none;
            transition: 0.3s;
        }

        input::placeholder { color: rgba(255, 255, 255, 0.75); }

        input:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: #818cf8;
        }

        .auth-submit {
            width: 100%;
            padding: 15px;
            border-radius: 15px;
            border: none;
            background: linear-gradient(135deg, #6366f1, #a855f7);
            color: white;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: 0.35s;
            margin-top: 10px;
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3);
        }

        .auth-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 25px rgba(99, 102, 241, 0.4);
        }

        .error-msg {
            background: rgba(255, 77, 77, 0.2);
            color: #ffb3b3;
            padding: 12px;
            border-radius: 12px;
            font-size: 0.9rem;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 77, 77, 0.3);
        }

        .footer-links {
            margin-top: 25px;
            display: flex;
            justify-content: space-between;
            gap: 14px;
            font-size: 0.9rem;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.86);
            text-decoration: none;
            transition: 0.3s;
        }

        .footer-links a:hover { color: #c4b5fd; text-decoration: underline; }

        #popup {
            display: none;
            position: fixed;
            top: 24px;
            right: 24px;
            background: #10b981;
            color: white;
            padding: 15px 25px;
            border-radius: 12px;
            font-weight: 600;
            z-index: 1000;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from { transform: translateX(100%); }
            to { transform: translateX(0); }
        }
    </style>
</head>

<body class="index-page<?php echo $preferredTheme === 'dark' ? ' dark' : ''; ?> auth-page">

    <?php
    tbRenderHeader([
        'is_logged_in' => false,
        'preferred_theme' => $preferredTheme,
        'show_welcome' => false,
        'links' => [
            ['href' => 'index.php', 'label' => 'Home', 'when' => 'all'],
            ['href' => 'signup.php', 'label' => 'Sign Up', 'class' => 'nav-signup', 'when' => 'all']
        ]
    ]);
    ?>

    <div class="login-card">
        <h2>Travel Login</h2>
        <p class="subtitle">Start your journey today</p>

        <?php if ($message): ?>
            <div class="error-msg"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-box">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Email Address" required>
            </div>
            <div class="input-box">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button class="auth-submit" name="login" type="submit">Sign In</button>
        </form>

        <div class="footer-links">
            <a href="signup.php">Create Account</a>
            <a href="forgot-password.php">Forgot Password?</a>
        </div>
    </div>

    <div id="popup"><i class="fas fa-check-circle"></i> Login successful!</div>

    <?php
    tbRenderFooter([
        'is_logged_in' => false,
        'show_newsletter' => false,
        'footer_class' => 'main-footer auth-footer',
        'tagline' => 'Login fast, write smooth, and keep every travel memory in one clean space.',
        'bottom_text' => 'Client-ready travel experience.'
    ]);
    ?>

    <script>
    <?php if ($loginSuccess): ?>
        const popup = document.getElementById('popup');
        popup.style.display = 'block';
        setTimeout(function () { window.location.href = 'index.php'; }, 2000);
    <?php endif; ?>

    document.querySelector('form').addEventListener('submit', function (e) {
        const email = document.querySelector('input[name="email"]').value.trim();
        const password = document.querySelector('input[name="password"]').value;

        if (!email.includes('@') || !email.includes('.')) {
            alert('Please enter a valid email');
            e.preventDefault();
            return;
        }

        if (password.length < 1) {
            alert('Please enter your password');
            e.preventDefault();
        }
    });
    </script>
    <script src="assets/js/index.fast.js?v=7"></script>

</body>
</html>
