<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'includes/db.php';
session_start();

$message = "";
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
            $message = "❌ Wrong Password!";
        }
    } else {
        $message = "❌ User not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Explore The World</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }

        /* Simple Nav */
        .nav {
            position: fixed; top: 0; width: 100%; z-index: 1000;
            background: rgba(0,0,0,0.3); backdrop-filter: blur(10px);
            padding: 15px 5%; display: flex; justify-content: space-between; align-items: center;
        }
        .nav .logo { color: white; font-size: 1.5rem; font-weight: 800; text-decoration: none; }
        .nav .logo span { color: #6366f1; }
        .nav a { color: rgba(255,255,255,0.8); text-decoration: none; font-weight: 600; }
        .nav a:hover { color: white; }

        body {
            background: linear-gradient(rgba(15, 23, 42, 0.85), rgba(15, 23, 42, 0.85)), 
                        url('https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?auto=format&fit=crop&w=1600&q=80');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding-top: 80px;
        }

        .login-card {
            background: rgba(30, 41, 59, 0.8); /* Darker background for visibility */
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 45px 35px;
            border-radius: 30px;
            width: 90%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
            color: white;
            animation: slideUp 0.8s ease;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 { font-size: 2.2rem; font-weight: 800; margin-bottom: 8px; letter-spacing: -1px; }

        .subtitle { color: rgba(255, 255, 255, 0.6); font-size: 0.9rem; margin-bottom: 35px; }

        .input-box { position: relative; margin-bottom: 20px; }

        .input-box i {
            position: absolute; left: 18px; top: 50%; transform: translateY(-50%);
            color: #6366f1; /* Bright Blue/Indigo for icons */
            font-size: 1.1rem;
        }

        input {
            width: 100%;
            padding: 15px 15px 15px 52px;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            color: #ffffff; /* Pure white text while typing */
            font-size: 1rem;
            outline: none;
            transition: 0.3s;
        }

        /* FIX: Better visibility for placeholder */
        input::placeholder { color: rgba(255, 255, 255, 0.75); }

        input:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: #6366f1;
        }

        button {
            width: 100%;
            padding: 15px;
            border-radius: 15px;
            border: none;
            background: linear-gradient(135deg, #6366f1, #a855f7);
            color: white;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: 0.4s;
            margin-top: 10px;
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3);
        }

        button:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 25px rgba(99, 102, 241, 0.4);
        }

        .error-msg {
            background: rgba(255, 77, 77, 0.2);
            color: #ffb3b3;
            padding: 12px;
            border-radius: 12px;
            font-size: 0.85rem;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 77, 77, 0.3);
        }

        .footer-links {
            margin-top: 25px;
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
        }

        .footer-links a { color: rgba(255, 255, 255, 0.8); text-decoration: none; transition: 0.3s; }
        .footer-links a:hover { color: #6366f1; text-decoration: underline; }

        #popup {
            display: none; position: fixed; top: 25px; right: 25px;
            background: #10b981; color: white; padding: 15px 25px;
            border-radius: 12px; font-weight: 600; z-index: 1000;
            animation: slideIn 0.5s ease-out;
        }
        @keyframes slideIn { from { transform: translateX(100%); } to { transform: translateX(0); } }
    </style>
</head>

<body>

    <nav class="nav">
        <a href="index.php" class="logo"><i class="fas fa-map-marked-alt"></i> Travel<span>Blog</span></a>
        <a href="signup.php">Sign Up</a>
    </nav>

    <div class="login-card">
        <h2>Travel Login</h2>
        <p class="subtitle">Start your journey today</p>

        <?php if($message){ ?>
            <div class="error-msg"><?php echo $message; ?></div>
        <?php } ?>

        <form method="POST">
            <div class="input-box">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Email Address" required>
            </div>
            <div class="input-box">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button name="login">Sign In</button>
        </form>

        <div class="footer-links">
            <a href="signup.php">Create Account</a>
            <a href="forgot-password.php">Forgot Password?</a>
        </div>
    </div>

    <div id="popup"><i class="fas fa-check-circle"></i> &nbsp; Login Successful!</div>

    <footer style="position: fixed; bottom: 0; width: 100%; text-align: center; padding: 10px; color: rgba(255,255,255,0.6); font-size: 0.8rem;">
        &copy; 2026 TravelBlog. All rights reserved.
    </footer>

    <script>
    <?php if($loginSuccess){ ?>
        const popup = document.getElementById("popup");
        popup.style.display = "block";
        setTimeout(function(){ window.location.href = "index.php"; }, 2000);
    <?php } ?>

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
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
            return;
        }
    });
    </script>

</body>
</html>