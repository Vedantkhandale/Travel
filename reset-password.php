<?php
include 'includes/db.php';
$message = "";

if (!isset($_GET['token'])) { header("Location: login.php"); exit(); }

$token = mysqli_real_escape_string($conn, $_GET['token']);
$check = mysqli_query($conn, "SELECT * FROM users WHERE reset_token='$token'");

if (mysqli_num_rows($check) == 0) { 
    die("<div style='height:100vh; display:flex; align-items:center; justify-content:center; background:#0f172a; color:white; font-family:sans-serif;'><h2>❌ Invalid or Expired Link!</h2></div>"); 
}

if (isset($_POST['update_pass'])) {
    $p1 = $_POST['pass'];
    $p2 = $_POST['conf_pass'];

    if ($p1 === $p2) {
        $hashed = password_hash($p1, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE users SET password='$hashed', reset_token=NULL WHERE reset_token='$token'");
        echo "<script>alert('✅ Password Updated! Please Login.'); window.location.href='login.php';</script>";
    } else {
        $message = "❌ Passwords do not match!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | TravelBlog</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }

        body {
            background: linear-gradient(rgba(15, 23, 42, 0.85), rgba(15, 23, 42, 0.85)), 
                        url('https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=1600&q=80');
            background-size: cover; background-position: center;
            height: 100vh; display: flex; justify-content: center; align-items: center; overflow: hidden;
        }

        .reset-box {
            background: rgba(30, 41, 59, 0.85); /* Visibility ke liye solid dark background */
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 45px 35px; border-radius: 32px;
            width: 90%; max-width: 400px; text-align: center;
            box-shadow: 0 30px 60px rgba(0,0,0,0.5);
            color: white; animation: popIn 0.6s cubic-bezier(0.17, 0.67, 0.83, 0.67);
        }

        @keyframes popIn { from { opacity: 0; transform: scale(0.9); } to { opacity: 1; transform: scale(1); } }

        h2 { font-size: 1.8rem; font-weight: 800; margin-bottom: 8px; letter-spacing: -0.5px; }
        .subtitle { color: rgba(255, 255, 255, 0.6); font-size: 0.9rem; margin-bottom: 30px; }

        .input-group { position: relative; margin-bottom: 18px; }

        .input-group i {
            position: absolute; left: 18px; top: 50%; transform: translateY(-50%);
            color: #10b981; /* Success Green Icon */
            font-size: 1.1rem;
        }

        input {
            width: 100%;
            padding: 15px 15px 15px 52px;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            color: #ffffff; /* User typing color white */
            font-size: 1rem; outline: none; transition: 0.3s;
        }

        /* FIX: Placeholder clearly visible */
        input::placeholder { color: rgba(255, 255, 255, 0.75); }

        input:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: #10b981;
            box-shadow: 0 0 15px rgba(16, 185, 129, 0.2);
        }

        button {
            width: 100%; padding: 16px; border-radius: 16px; border: none;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white; font-size: 1rem; font-weight: 700;
            cursor: pointer; transition: 0.4s; margin-top: 10px;
            box-shadow: 0 10px 20px rgba(16, 185, 129, 0.2);
        }

        button:hover { transform: translateY(-2px); filter: brightness(1.1); }

        .error-message {
            background: rgba(244, 63, 94, 0.2);
            color: #fda4af; padding: 12px; border-radius: 12px;
            font-size: 0.85rem; margin-top: 20px; border: 1px solid rgba(244, 63, 94, 0.3);
        }
        
        .step-indicator { display: flex; justify-content: center; gap: 8px; margin-bottom: 25px; }
        .dot { width: 8px; height: 8px; border-radius: 50%; background: rgba(255,255,255,0.2); }
        .dot.active { background: #10b981; width: 20px; border-radius: 10px; }
    </style>
</head>
<body>
    <div class="reset-box">
        <div class="step-indicator">
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot active"></div>
        </div>
        
        <h2>Secure Your Account</h2>
        <p class="subtitle">Set a strong password to protect your travel memories.</p>

        <form method="POST">
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="pass" placeholder="New Password" required minlength="6">
            </div>
            <div class="input-group">
                <i class="fas fa-shield-alt"></i>
                <input type="password" name="conf_pass" placeholder="Confirm Password" required>
            </div>
            <button name="update_pass">Update Password</button>
        </form>

        <?php if($message){ ?>
            <div class="error-message"><?php echo $message; ?></div>
        <?php } ?>
    </div>
</body>
</html>