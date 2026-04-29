<!-- Heroic Footer -->
<footer class="main-footer fade-in">
    <div class="footer-grid">
        <div class="footer-col footer-brand">
            <a href="index.php" class="footer-logo">
                <i class="fas fa-globe"></i>
                <span>Travel</span>Blog
            </a>
            <p class="footer-text">Simple travel stories, smooth reading, and memories worth keeping.</p>
            <div class="social-icons">
                <a href="#" class="social-btn" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="social-btn" title="Twitter"><i class="fab fa-twitter"></i></a>
                <a href="#" class="social-btn" title="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="#" class="social-btn" title="YouTube"><i class="fab fa-youtube"></i></a>
            </div>
        </div>

        <div class="footer-col">
            <h4>Quick Links</h4>
            <ul class="footer-links">
                <li><a href="index.php"><i class="fas fa-house"></i> Home</a></li>
                <li><a href="index.php#categories"><i class="fas fa-compass"></i> Categories</a></li>
                <li><a href="<?php echo $isLoggedIn ? 'add-post.php' : 'login.php'; ?>"><i class="fas fa-pen-to-square"></i> Write Story</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>Explore</h4>
            <ul class="footer-links">
                <li><a href="index.php#postsGrid"><i class="fas fa-bolt"></i> Latest Stories</a></li>
                <li><a href="index.php#categories"><i class="fas fa-mountain"></i> Adventures</a></li>
                <li><a href="<?php echo $isLoggedIn ? 'profile.php?user_id=' . $_SESSION['user_id'] : 'login.php'; ?>"><i class="fas fa-user"></i> Profile</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>Stay Connected</h4>
            <p class="footer-text">Get travel inspiration delivered weekly.</p>
            <form class="footer-form" id="newsletterForm" novalidate>
                <input type="email" id="newsletterEmail" name="newsletter_email" placeholder="your@email.com" aria-label="Email" autocomplete="email" required>
                <button class="btn btn-primary" type="submit">Subscribe</button>
            </form>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="footer-bottom-inner">
            <span>&copy; <?php echo date('Y'); ?> TravelBlog</span>
            <span>Simple, smooth, made for travelers.</span>
        </div>
    </div>
</footer>

<script src="assets/js/index.fast.js?v=8"></script>
</body>
</html>