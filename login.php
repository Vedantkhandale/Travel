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
    <title>Login | TravelBlog</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #6366f1;
            --secondary: #a855f7;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background: #0f172a;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            position: relative;
        }

        /* Background Animated Circles */
        body::before, body::after {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            filter: blur(80px);
            z-index: -1;
            animation: move 10s infinite alternate;
        }
        body::before {
            background: rgba(99, 102, 241, 0.4);
            top: -50px;
            left: -50px;
        }
        body::after {
            background: rgba(168, 85, 247, 0.3);
            bottom: -50px;
            right: -50px;
        }

        @keyframes move {
            from { transform: translate(0, 0); }
            to { transform: translate(50px, 100px); }
        }

        .container {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 28px;
            width: 100%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            color: white;
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 10px;
            letter-spacing: -1px;
        }

        p.subtitle {
            color: #94a3b8;
            font-size: 0.9rem;
            margin-bottom: 30px;
        }

        .input-group {
            position: relative;
            margin-bottom: 20px;
            text-align: left;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            transition: 0.3s;
        }

        input {
            width: 100%;
            padding: 14px 15px 14px 45px;
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 14px;
            color: white;
            font-size: 1rem;
            outline: none;
            transition: all 0.3s ease;
        }

        input:focus {
            background: rgba(255, 255, 255, 0.12);
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.2);
        }

        input:focus + i {
            color: var(--primary);
        }

        button {
            width: 100%;
            padding: 15px;
            border-radius: 14px;
            border: none;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.4s ease;
            margin-top: 10px;
            box-shadow: 0 10px 20px -5px rgba(99, 102, 241, 0.4);
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 25px -5px rgba(99, 102, 241, 0.5);
            filter: brightness(1.1);
        }

        button:active { transform: translateY(0); }

        .error {
            background: rgba(244, 63, 94, 0.1);
            color: #fb7185;
            padding: 10px;
            border-radius: 10px;
            font-size: 0.85rem;
            margin-bottom: 20px;
            border: 1px solid rgba(244, 63, 94, 0.2);
        }

        .links {
            margin-top: 25px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .links a {
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.85rem;
            transition: 0.3s;
        }

        .links a:hover {
            color: white;
        }

        /* Success Popup */
        #popup {
            display: none;
            position: fixed;
            top: 30px;
            right: 30px;
            background: #10b981;
            color: white;
            padding: 16px 25px;
            border-radius: 16px;
            font-weight: 700;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            z-index: 9999;
            animation: slideIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes slideIn {
            from { transform: translateX(120%); }
            to { transform: translateX(0); }
        }
    </style>
</head>

<body>

<div class="container">
    <h2>Welcome Back</h2>
    <p class="subtitle">Please enter your details to sign in.</p>

    <?php if($message){ ?>
        <div class="error"><?php echo $message; ?></div>
    <?php } ?>

    <form method="POST">
        <div class="input-group">
            <input type="email" name="email" placeholder="Email Address" required>
            <i class="fas fa-envelope"></i>
        </div>
        <div class="input-group">
            <input type="password" name="password" placeholder="Password" required>
            <i class="fas fa-lock"></i>
        </div>
        <button name="login">Sign In</button>
    </form>

    <div class="links">
        <a href="signup.php">Don't have an account? <span style="color: var(--primary); font-weight: 700;">Sign Up</span></a>
        <a href="forgot-password.php">Forgot Password?</a>
    </div>
</div>

<div id="popup">
    <i class="fas fa-check-circle" style="margin-right: 10px;"></i> Login Successful!
</div>

<script>
<?php if($loginSuccess){ ?>
    const popup = document.getElementById("popup");
    popup.style.display = "block";

    setTimeout(function(){
        window.location.href = "index.php";
    }, 2000);
<?php } ?>
</script>

</body>
</html>