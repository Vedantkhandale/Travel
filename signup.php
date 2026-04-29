<?php
// ... (Tera PHP logic same rahega) ...
include 'includes/db.php';
$message = "";
$success = false;

if (isset($_POST['signup'])) {
    $name  = trim(mysqli_real_escape_string($conn, $_POST['name']));
    $email = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $password = $_POST['password'];

    $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        $message = "⚠️ Email already exists!";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $insertStmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $insertStmt->bind_param("sss", $name, $email, $hashedPassword);
        if ($insertStmt->execute()) { $success = true; } 
        else { $message = "❌ Error: Something went wrong."; }
        $insertStmt->close();
    }
    $checkStmt->close();
}
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
            background:
                radial-gradient(circle at 8% -8%, rgba(99, 102, 241, 0.36), transparent 40%),
                radial-gradient(circle at 92% -10%, rgba(168, 85, 247, 0.32), transparent 42%),
                linear-gradient(180deg, rgba(15, 23, 42, 0.98), rgba(30, 41, 59, 0.98));
            height: 100vh; display: flex; justify-content: center; align-items: center; padding-top: 80px;
        }

        .signup-card {
            background: rgba(30, 41, 59, 0.7); /* Thoda dark background box ke liye */
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 40px; border-radius: 30px; width: 90%; max-width: 400px;
            text-align: center; box-shadow: 0 25px 50px rgba(0,0,0,0.5);
        }

        h2 { color: white; font-size: 2rem; margin-bottom: 30px; }

        .input-group { position: relative; margin-bottom: 20px; }

        .input-group i {
            position: absolute; left: 18px; top: 50%; transform: translateY(-50%);
            color: #6366f1; /* Icons ko thoda bright kiya */
            font-size: 1.1rem;
        }

        input {
            width: 100%;
            padding: 15px 15px 15px 52px;
            background: rgba(255, 255, 255, 0.1); /* Input box thoda visible kiya */
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            color: #ffffff; /* Typing text pure white */
            font-size: 1rem;
            outline: none;
            transition: 0.3s;
        }

        /* YAHAN FIX HAI: Placeholder color ko bright kiya */
        input::placeholder {
            color: rgba(255, 255, 255, 0.7); 
            font-weight: 400;
        }

        input:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: #6366f1;
        }

        button {
            width: 100%; padding: 15px; border-radius: 15px; border: none;
            background: linear-gradient(135deg, #6366f1, #a855f7);
            color: white; font-size: 1.1rem; font-weight: 700;
            cursor: pointer; transition: 0.3s; margin-top: 10px;
        }

        button:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(99, 102, 241, 0.4); }

        .error-msg { color: #ff8e8e; margin-bottom: 15px; font-size: 0.9rem; font-weight: 600; }
        
        .login-link { margin-top: 25px; color: rgba(255, 255, 255, 0.7); font-size: 0.9rem; }
        .login-link a { color: #6366f1; text-decoration: none; font-weight: 700; }

        #popup {
            display: none; position: fixed; top: 20px; right: 20px;
            background: #10b981; color: white; padding: 15px 25px;
            border-radius: 12px; font-weight: 700; box-shadow: 0 10px 20px rgba(0,0,0,0.3);
            z-index: 1000; animation: slideIn 0.5s ease;
        }
        @keyframes slideIn { from { transform: translateX(100%); } to { transform: translateX(0); } }
    </style>
</head>
<body>

    <nav class="nav">
        <a href="index.php" class="logo"><i class="fas fa-map-marked-alt"></i> Travel<span>Blog</span></a>
        <a href="login.php">Login</a>
    </nav>

    <div class="signup-card">
        <h2>Create Account</h2>

        <?php if($message): ?>
            <div class="error-msg"><?php echo $message; ?></div>
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
            <button type="submit" name="signup">Sign Up</button>
        </form>

        <div class="login-link">
            Already a member? <a href="login.php">Login</a>
        </div>
    </div>

    <div id="popup"><i class="fas fa-check-circle"></i> &nbsp; Success! Redirecting...</div>

    <footer style="position: fixed; bottom: 0; width: 100%; text-align: center; padding: 10px; color: rgba(255,255,255,0.6); font-size: 0.8rem;">
        &copy; 2026 TravelBlog. All rights reserved.
    </footer>

    <script>
    <?php if($success): ?>
        document.getElementById("popup").style.display = "block";
        setTimeout(() => { window.location.href = "login.php"; }, 2000);
    <?php endif; ?>

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
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
            return;
        }
    });
    </script>
</body>
</html>
