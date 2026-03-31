<?php
include 'includes/db.php';
$message = "";

if (!isset($_GET['token'])) { header("Location: login.php"); exit(); }

$token = mysqli_real_escape_string($conn, $_GET['token']);
$check = mysqli_query($conn, "SELECT * FROM users WHERE reset_token='$token'");

if (mysqli_num_rows($check) == 0) { die("❌ Invalid or Expired Link!"); }

if (isset($_POST['update_pass'])) {
    $p1 = $_POST['pass'];
    $p2 = $_POST['conf_pass'];

    if ($p1 === $p2) {
        $hashed = password_hash($p1, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE users SET password='$hashed', reset_token=NULL WHERE reset_token='$token'");
        echo "<script>alert('✅ Password Updated!'); window.location.href='login.php';</script>";
    } else {
        $message = "❌ Passwords do not match!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <style>
        body { background: linear-gradient(135deg, #667eea, #764ba2); font-family: Poppins, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin:0; }
        .box { background: rgba(255,255,255,0.1); backdrop-filter: blur(15px); padding: 30px; border-radius: 15px; width: 320px; text-align: center; color: white; border: 1px solid rgba(255,255,255,0.2); }
        input, button { width: 100%; padding: 12px; margin: 10px 0; border-radius: 8px; border: none; }
        button { background: #28a745; color: white; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Set New Password</h2>
        <form method="POST">
            <input type="password" name="pass" placeholder="New Password" required minlength="6">
            <input type="password" name="conf_pass" placeholder="Confirm Password" required>
            <button name="update_pass">Update Password</button>
        </form>
        <p><?php echo $message; ?></p>
    </div>
</body>
</html>