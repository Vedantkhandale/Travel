<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'includes/db.php';

$message = "";
$success = false;

if(isset($_POST['signup'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

    if(mysqli_num_rows($check) > 0){
        $message = "⚠️ Email already exists! Try login";
    } else {
        $query = mysqli_query($conn, "INSERT INTO users (name, email, password)
        VALUES ('$name', '$email', '$hashedPassword')");

        if($query){
            $success = true;
        } else {
            $message = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="css/style.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<style>
.popup {
    position: fixed;
    top: 20px;
    right: 20px;
    background: #28a745;
    color: white;
    padding: 15px 20px;
    border-radius: 8px;
    display: none;
}

.error {
    color: red;
    margin-top: 10px;
}

.links {
    margin-top: 10px;
}

.links a {
    color: #fff;
    text-decoration: underline;
    font-size: 14px;
}
</style>
</head>

<body>

<div class="container">
    <h2>Signup</h2>

    <form method="POST">
        <input type="text" name="name" placeholder="Enter Name" required>
        <input type="email" name="email" placeholder="Enter Email" required>
        <input type="password" name="password" placeholder="Enter Password" required>
        <button name="signup">Signup</button>
    </form>

    <!-- error -->
    <?php if($message){ ?>
        <p class="error"><?php echo $message; ?></p>
    <?php } ?>

    <!-- Links -->
    <div class="links">
        <a href="login.php">Already have an account? Login</a><br>
        <a href="forgot-password.php">Forgot Password?</a>
    </div>
</div>

<!-- Popup -->
<div id="popup" class="popup">
    ✅ Signup Successful!
</div>

<!-- JS -->
<script>
<?php if($success){ ?>
    document.getElementById("popup").style.display = "block";

    setTimeout(() => {
        window.location.href = "login.php";
    }, 2000);
<?php } ?>
</script>

</body>
</html>