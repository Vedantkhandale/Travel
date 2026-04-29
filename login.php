<?php
$title = 'Login | TravelBlog';
$description = 'Login to your TravelBlog account to share and read amazing travel stories.';
include 'includes/header.php';
?>

    <div class="auth-container">
        <div class="login-card">
            <h2>Travel Login</h2>
            <p class="subtitle">Start your journey today</p>

            <?php if ($message): ?>
                <div class="error-msg"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="input-box">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" placeholder="Email Address" required>
                </div>
                <div class="input-box">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <button class="auth-submit" name="login" type="submit">Sign In</button>
            </form>

            <div class="footer-links">
                <a href="signup.php">Create Account</a>
                <a href="forgot-password.php">Forgot Password?</a>
            </div>
        </div>
    </div>

    <div id="popup"><i class="fas fa-check-circle"></i> Login successful!</div>

<?php include 'includes/footer.php'; ?>

    <script>
    <?php if ($loginSuccess): ?>
        const popup = document.getElementById('popup');
        popup.style.display = 'block';
        setTimeout(function () { window.location.href = 'index.php'; }, 2000);
    <?php endif; ?>

    document.querySelector('form').addEventListener('submit', function (e) {
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
        }
    });
    </script>
