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
        $update = mysqli_query($conn, "UPDATE users SET reset_token='$token' WHERE email='$email'");
        
        if($update) {
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | TravelBlog</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }

        body {
            background: linear-gradient(rgba(15, 23, 42, 0.85), rgba(15, 23, 42, 0.85)), 
                        url('https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=1600&q=80');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .reset-card {
            background: rgba(30, 41, 59, 0.8); /* Darker for better visibility */
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 40px;
            border-radius: 30px;
            width: 90%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
            color: white;
            animation: fadeIn 0.8s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        h2 { font-size: 2rem; font-weight: 800; margin-bottom: 10px; }

        .subtitle { color: rgba(255, 255, 255, 0.6); font-size: 0.9rem; margin-bottom: 30px; line-height: 1.5; }

        .input-box { position: relative; margin-bottom: 20px; }

        .input-box i {
            position: absolute; left: 18px; top: 50%; transform: translateY(-50%);
            color: #6366f1; /* Bright icon */
            font-size: 1.1rem;
        }

        input {
            width: 100%;
            padding: 15px 15px 15px 52px;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            color: #ffffff; /* Typing color clear white */
            font-size: 1rem;
            outline: none;
            transition: 0.3s;
        }

        /* Visibility Fix for Placeholder */
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
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: 0.4s;
            margin-top: 5px;
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3);
        }

        button:hover {
            transform: translateY(-2px);
            filter: brightness(1.1);
        }

        .error {
            background: rgba(255, 77, 77, 0.2);
            color: #ffb3b3;
            padding: 12px;
            border-radius: 12px;
            font-size: 0.85rem;
            margin-top: 15px;
            border: 1px solid rgba(255, 77, 77, 0.3);
        }

        .back-link {
            display: inline-block;
            margin-top: 25px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 0.9rem;
            transition: 0.3s;
        }

        .back-link:hover { color: #6366f1; transform: translateX(-5px); }
    </style>
</head>
<body>
    <div class="reset-card">
        <div style="background: rgba(99, 102, 241, 0.2); width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
            <i class="fas fa-key" style="font-size: 24px; color: #6366f1;"></i>
        </div>
        
        <h2>Recover Access</h2>
        <p class="subtitle">Enter your email and we'll help you get back on track.</p>
        
        <form method="POST">
            <div class="input-box">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Registered Email" required>
            </div>
            <button name="reset_request">Reset Password</button>
        </form>

        <?php if($message){ ?>
            <p class="error"><?php echo $message; ?></p>
        <?php } ?>

        <a href="login.php" class="back-link">
            <i class="fas fa-arrow-left" style="font-size: 12px; margin-right: 5px;"></i> Back to Login
        </a>
    </div>
</body>
</html>