<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'includes/db.php';

$message = "";

if (isset($_POST['reset_request'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

    if (mysqli_num_rows($query) > 0) {
        $token = bin2hex(random_bytes(16));
        // Token update logic
        $update = mysqli_query($conn, "UPDATE users SET reset_token='$token' WHERE email='$email'");
        
        if($update) {
            // Success: Direct redirect to reset page with token
            header("Location: reset-password.php?token=$token");
            exit();
        } else {
            $message = "❌ Database Error: " . mysqli_error($conn);
        }
    } else {
        $message = "❌ Email not found!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <style>
        body { background: linear-gradient(135deg, #667eea, #764ba2); font-family: Poppins, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin:0; }
        .box { background: rgba(255,255,255,0.1); backdrop-filter: blur(15px); padding: 30px; border-radius: 15px; width: 320px; text-align: center; color: white; border: 1px solid rgba(255,255,255,0.2); }
        input, button { width: 100%; padding: 12px; margin: 10px 0; border-radius: 8px; border: none; outline: none; }
        button { background: #ff7eb3; color: white; cursor: pointer; font-weight: bold; }
        .error { color: #ffbcbc; font-size: 14px; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Forgot Password</h2>
        <form method="POST">
            <input type="email" name="email" placeholder="Enter Registered Email" required>
            <button name="reset_request">Reset Password</button>
        </form>
        <p class="error"><?php echo $message; ?></p>
        <a href="login.php" style="color:white; font-size:13px; text-decoration:none;">← Back to Login</a>
    </div>
</body>
</html>