<?php
include 'includes/db.php';
include 'includes/layout_components.php';
session_start();

$message = '';
$success = false;

if (isset($_POST['signup'])) {
    $name = trim(mysqli_real_escape_string($conn, $_POST['name']));
    $email = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $password = $_POST['password'];

    $checkStmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
    $checkStmt->bind_param('s', $email);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        $message = 'Email already exists.';
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $insertStmt = $conn->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
        $insertStmt->bind_param('sss', $name, $email, $hashedPassword);

        if ($insertStmt->execute()) {
            $success = true;
        } else {
            $message = 'Error: Something went wrong.';
        }

        $insertStmt->close();
    }

    $checkStmt->close();
}

$preferredTheme = (isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'light') ? 'light' : 'dark';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup | TravelBlog</title>

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

        .signup-card {
            background: rgba(30, 41, 59, 0.78);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 40px;
            border-radius: 30px;
            width: 100%;
            max-width: 420px;
            text-align: center;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
            color: #ffffff;
            z-index: 2;
            margin: 0 auto;
        }

        h2 { color: #ffffff; font-size: 2rem; margin-bottom: 30px; }

        .input-group { position: relative; margin-bottom: 20px; }

        .input-group i {
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

        input::placeholder {
            color: rgba(255, 255, 255, 0.72);
            font-weight: 400;
        }

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
            color: #ffffff;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
            box-shadow: 0 12px 24px rgba(99, 102, 241, 0.32);
        }

        .auth-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 30px rgba(99, 102, 241, 0.42);
        }

        .error-msg {
            color: #fecaca;
            margin-bottom: 15px;
            font-size: 0.9rem;
            font-weight: 600;
            background: rgba(220, 38, 38, 0.2);
            border: 1px solid rgba(248, 113, 113, 0.35);
            border-radius: 12px;
            padding: 10px;
        }

        .login-link { margin-top: 25px; color: rgba(255, 255, 255, 0.74); font-size: 0.95rem; }
        .login-link a { color: #c4b5fd; text-decoration: none; font-weight: 700; }

        #popup {
            display: none;
            position: fixed;
            top: 20px;
            right: 20px;
            background: #10b981;
            color: white;
            padding: 15px 25px;
            border-radius: 12px;
            font-weight: 700;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            animation: slideIn 0.5s ease;
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
            ['href' => 'login.php', 'label' => 'Login', 'class' => 'nav-login', 'when' => 'all']
        ]
    ]);
    ?>

    <div class="signup-card">
        <h2>Create Account</h2>

        <?php if ($message): ?>
            <div class="error-msg"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="name" placeholder="Full Name" required>
            </div>
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Email Address" required>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Create Password" required minlength="6">
            </div>
            <button class="auth-submit" type="submit" name="signup">Sign Up</button>
        </form>

        <div class="login-link">
            Already a member? <a href="login.php">Login</a>
        </div>
    </div>

    <div id="popup"><i class="fas fa-check-circle"></i> Success! Redirecting...</div>

    <?php
    tbRenderFooter([
        'is_logged_in' => false,
        'show_newsletter' => false,
        'footer_class' => 'main-footer auth-footer',
        'tagline' => 'Create your account once, then publish stories from every trip in one premium space.',
        'bottom_text' => 'Fast signup, smooth experience.'
    ]);
    ?>

    <script>
    <?php if ($success): ?>
        document.getElementById('popup').style.display = 'block';
        setTimeout(() => { window.location.href = 'login.php'; }, 2000);
    <?php endif; ?>

    document.querySelector('form').addEventListener('submit', function (e) {
        const name = document.querySelector('input[name="name"]').value.trim();
        const email = document.querySelector('input[name="email"]').value.trim();
        const password = document.querySelector('input[name="password"]').value;

        if (name.length < 2) {
            alert('Name must be at least 2 characters');
            e.preventDefault();
            return;
        }

        if (!email.includes('@') || !email.includes('.')) {
            alert('Please enter a valid email');
            e.preventDefault();
            return;
        }

        if (password.length < 6) {
            alert('Password must be at least 6 characters');
            e.preventDefault();
        }
    });
    </script>
    <script src="assets/js/index.fast.js?v=7"></script>
</body>
</html>
