<?php

if (!function_exists('tbEscape')) {
    function tbEscape($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('tbRenderHeader')) {
    function tbRenderHeader(array $options = [])
    {
        $homeUrl = (string) ($options['home_url'] ?? 'index.php');
        $isLoggedIn = !empty($options['is_logged_in']);
        $userId = (int) ($options['user_id'] ?? 0);
        $userName = trim((string) ($options['user_name'] ?? 'Traveler'));
        $showWelcome = !array_key_exists('show_welcome', $options) || (bool) $options['show_welcome'];
        $theme = ((string) ($options['preferred_theme'] ?? 'dark')) === 'dark' ? 'dark' : 'light';
        $showThemeButton = !array_key_exists('show_theme_button', $options) || (bool) $options['show_theme_button'];
        $navId = (string) ($options['nav_id'] ?? 'mainNav');
        $linksId = (string) ($options['links_id'] ?? 'navLinks');
        $menuToggleId = (string) ($options['menu_toggle_id'] ?? 'mobile-menu');
        $logoIcon = (string) ($options['logo_icon'] ?? 'fas fa-globe');
        $logoPrefix = (string) ($options['logo_prefix'] ?? 'Travel');
        $logoSuffix = (string) ($options['logo_suffix'] ?? 'Blog');
        $links = $options['links'] ?? [];

        if (!is_array($links)) {
            $links = [];
        }

        echo '<nav class="navbar" id="' . tbEscape($navId) . '">';
        echo '<a href="' . tbEscape($homeUrl) . '" class="logo">';
        echo '<i class="' . tbEscape($logoIcon) . '"></i><span>' . tbEscape($logoPrefix) . '</span>' . tbEscape($logoSuffix);
        echo '</a>';

        echo '<div class="nav-links" id="' . tbEscape($linksId) . '">';

        if ($isLoggedIn && $showWelcome) {
            echo '<a href="profile.php?user_id=' . $userId . '" class="user-welcome">';
            echo '<i class="fas fa-user"></i> Hi, ' . tbEscape($userName);
            echo '</a>';
        }

        foreach ($links as $link) {
            if (!is_array($link)) {
                continue;
            }

            $when = (string) ($link['when'] ?? 'all');
            if ($when === 'user' && !$isLoggedIn) {
                continue;
            }
            if ($when === 'guest' && $isLoggedIn) {
                continue;
            }

            $href = (string) ($link['href'] ?? '#');
            $label = (string) ($link['label'] ?? '');
            if ($label === '') {
                continue;
            }

            $class = trim((string) ($link['class'] ?? ''));
            $icon = trim((string) ($link['icon'] ?? ''));

            echo '<a href="' . tbEscape($href) . '"' . ($class !== '' ? ' class="' . tbEscape($class) . '"' : '') . '>';
            if ($icon !== '') {
                echo '<i class="' . tbEscape($icon) . '"></i> ';
            }
            echo tbEscape($label) . '</a>';
        }

        echo '</div>';

        if ($showThemeButton) {
            echo '<button class="theme-btn" id="themeBtn" type="button" aria-label="Toggle theme" onclick="toggleTheme()">';
            echo '<i class="fas ' . ($theme === 'dark' ? 'fa-sun' : 'fa-moon') . '"></i>';
            echo '</button>';
        }

        echo '<button class="menu-toggle" id="' . tbEscape($menuToggleId) . '" type="button" aria-label="Open menu" aria-controls="' . tbEscape($linksId) . '" aria-expanded="false">';
        echo '<i class="fas fa-bars"></i>';
        echo '</button>';
        echo '</nav>';
    }
}

if (!function_exists('tbRenderFooter')) {
    function tbRenderFooter(array $options = [])
    {
        $isLoggedIn = !empty($options['is_logged_in']);
        $userId = (int) ($options['user_id'] ?? 0);
        $showNewsletter = !array_key_exists('show_newsletter', $options) || (bool) $options['show_newsletter'];
        $footerClass = trim((string) ($options['footer_class'] ?? 'main-footer'));
        $tagline = (string) ($options['tagline'] ?? 'Simple travel stories, smooth reading, and memories worth keeping.');
        $bottomText = (string) ($options['bottom_text'] ?? 'Simple, smooth, made for travelers.');

        echo '<footer class="' . tbEscape($footerClass) . '">';
        echo '<div class="footer-grid">';

        echo '<div class="footer-col footer-brand">';
        echo '<a href="index.php" class="footer-logo"><i class="fas fa-globe"></i> <span>Travel</span>Blog</a>';
        echo '<p class="footer-text">' . tbEscape($tagline) . '</p>';
        echo '<div class="social-icons">';
        echo '<a href="https://facebook.com" class="social-btn" title="Facebook" target="_blank" rel="noopener noreferrer"><i class="fab fa-facebook-f"></i></a>';
        echo '<a href="https://twitter.com" class="social-btn" title="Twitter" target="_blank" rel="noopener noreferrer"><i class="fab fa-twitter"></i></a>';
        echo '<a href="https://instagram.com" class="social-btn" title="Instagram" target="_blank" rel="noopener noreferrer"><i class="fab fa-instagram"></i></a>';
        echo '<a href="https://youtube.com" class="social-btn" title="YouTube" target="_blank" rel="noopener noreferrer"><i class="fab fa-youtube"></i></a>';
        echo '</div>';
        echo '</div>';

        echo '<div class="footer-col">';
        echo '<h4>Quick Links</h4>';
        echo '<ul class="footer-links">';
        echo '<li><a href="index.php"><i class="fas fa-house"></i> Home</a></li>';
        echo '<li><a href="index.php#categories"><i class="fas fa-compass"></i> Categories</a></li>';
        echo '<li><a href="' . ($isLoggedIn ? 'add-post.php' : 'login.php') . '"><i class="fas fa-pen-to-square"></i> Write Story</a></li>';
        echo '</ul>';
        echo '</div>';

        echo '<div class="footer-col">';
        echo '<h4>Explore</h4>';
        echo '<ul class="footer-links">';
        echo '<li><a href="index.php#postsGrid"><i class="fas fa-bolt"></i> Latest Stories</a></li>';
        echo '<li><a href="index.php#categories"><i class="fas fa-mountain"></i> Adventures</a></li>';
        echo '<li><a href="' . ($isLoggedIn ? 'profile.php?user_id=' . $userId : 'login.php') . '"><i class="fas fa-user"></i> Profile</a></li>';
        echo '</ul>';
        echo '</div>';

        echo '<div class="footer-col">';
        echo '<h4>Newsletter</h4>';
        if ($showNewsletter) {
            echo '<p class="footer-text">One good travel story every week. No spam.</p>';
            echo '<form class="footer-form" id="newsletterForm" novalidate>';
            echo '<input type="email" id="newsletterEmail" name="newsletter_email" placeholder="you@example.com" aria-label="Email address" autocomplete="email" required>';
            echo '<button class="btn btn-primary" type="submit">Subscribe</button>';
            echo '</form>';
        } else {
            echo '<p class="footer-text">Travel updates, destination ideas, and smooth stories. No spam.</p>';
            if ($isLoggedIn) {
                echo '<a href="index.php#postsGrid" class="btn btn-primary">Explore Stories</a>';
            } else {
                echo '<a href="signup.php" class="btn btn-primary">Join Free</a>';
            }
        }
        echo '</div>';

        echo '</div>';
        echo '<div class="footer-bottom">';
        echo '<div class="footer-bottom-inner">';
        echo '<span>&copy; ' . date('Y') . ' TravelBlog</span>';
        echo '<span>' . tbEscape($bottomText) . '</span>';
        echo '</div>';
        echo '</div>';
        echo '</footer>';
    }
}
