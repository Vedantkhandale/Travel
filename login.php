<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'includes/db.php';
session_start();

$message = "";
$loginSuccess = false; // 👈 important

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

    if (mysqli_num_rows($query) > 0) {
        $user = mysqli_fetch_assoc($query);

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];

            $loginSuccess = true; // 👈 yaha set kar
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

<link rel="stylesheet" href="css/style.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<title>Login</title>

<style>
body {
    background: linear-gradient(135deg, #667eea, #764ba2);
    font-family: Poppins;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.container {
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(15px);
    padding: 30px;
    border-radius: 15px;
    width: 90%;
    max-width: 350px;
    text-align: center;
    color: white;
}

input {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    border-radius: 8px;
    border: none;
}

button {
    width: 100%;
    padding: 12px;
    border-radius: 8px;
    border: none;
    background: #ff7eb3;
    color: white;
    cursor: pointer;
}

button:hover {
    background: #ff4f8b;
}

.error {
    color: #ff4d4d;
}

.links a {
    color: white;
    font-size: 14px;
}

/* popup */
#popup {
    display: none;
    position: fixed;
    top: 20px;
    right: 20px;
    background: #28a745;
    color: white;
    padding: 15px 20px;
    border-radius: 8px;
    animation: slideIn 0.5s ease;
}

@keyframes slideIn {
    from { transform: translateX(100%); }
    to { transform: translateX(0); }
}
</style>
</head>

<body>

<div class="container">
    <h2>Login</h2>

    <form method="POST">
        <input type="email" name="email" placeholder="Enter Email" required>
        <input type="password" name="password" placeholder="Enter Password" required>
        <button name="login">Login</button>
    </form>

    <?php if($message){ ?>
        <p class="error"><?php echo $message; ?></p>
    <?php } ?>

    <div class="links">
        <a href="signup.php">Create Account</a><br>
        <a href="forgot-password.php">Forgot Password?</a>
    </div>
</div>

<!-- popup -->
<div id="popup">✅ Login Successful!</div>

<script>
<?php if($loginSuccess){ ?>
    document.getElementById("popup").style.display = "block";

    setTimeout(function(){
        window.location.href = "index.php";
    }, 2000);
<?php } ?>
</script>

</body>
</html>