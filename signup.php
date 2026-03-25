<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
        $message = "⚠️ Email already exists! Try logging in.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $insertStmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $insertStmt->bind_param("sss", $name, $email, $hashedPassword);

        if ($insertStmt->execute()) {
            $success = true;
        } else {
            $message = "❌ Error: Something went wrong.";
        }
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
    <title>Signup | Travel Blog</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f5f5;
            margin: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Navbar Style (Same as Index) */
        .navbar {
            background: linear-gradient(90deg, #667eea, #764ba2);
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        /* Hero-like Header */
        .header-section {
            text-align: center;
            padding: 40px 20px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            clip-path: ellipse(150% 100% at 50% 0%); /* Thoda curvy design */
        }

        .container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            margin-top: -50px; /* Overlap effect */
        }

        .form-box {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .form-box h2 { color: #333; margin-bottom: 10px; }

        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            outline: none;
            transition: 0.3s;
        }

        input:focus { border-color: #667eea; box-shadow: 0 0 8px rgba(102,126,234,0.3); }

        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }

        button:hover { opacity: 0.9; transform: translateY(-2px); }

        .error { color: #e74c3c; font-size: 14px; margin-top: 10px; }

        .links { margin-top: 20px; font-size: 14px; }
        .links a { color: #667eea; text-decoration: none; font-weight: 600; }

        .popup {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #2ecc71;
            color: white;
            padding: 15px 25px;
            border-radius: 12px;
            display: none;
            z-index: 1000;
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn { from { transform: translateX(100%); } to { transform: translateX(0); } }
    </style>
</head>
<body>

<div class="navbar">
    <div style="font-weight: 600; font-size: 20px;">🌍 Travel Blog</div>
</div>

<div class="header-section">
    <h1>Join the Community 🌍</h1>
    <p>Create an account to share your travel memories</p>
</div>

<div class="container">
    <div class="form-box">
        <h2>Signup</h2>
        <form method="POST">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Create Password" required>
            <button type="submit" name="signup">Create Account</button>
        </form>

        <?php if($message): ?>
            <div class="error"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="links">
            Already a member? <a href="login.php">Login</a>
        </div>
    </div>
</div>

<div id="popup" class="popup">✅ Success! Redirecting...</div>

<script>
<?php if($success): ?>
    document.getElementById("popup").style.display = "block";
    setTimeout(() => { window.location.href = "login.php"; }, 2000);
<?php endif; ?>
</script>

</body>
</html>