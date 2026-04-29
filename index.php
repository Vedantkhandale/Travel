<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'includes/db.php';
include 'includes/story_helpers.php';
include 'includes/layout_components.php';
session_start();

$posts = fetchHomepageStories($conn, ['limit' => 12]);

function fetchSingleCount($conn, $sql)
{
    $countResult = mysqli_query($conn, $sql);
    if (!$countResult) {
        return 0;
    }

    $row = mysqli_fetch_row($countResult);
    if (!$row || !isset($row[0])) {
        return 0;
    }

    return (int) $row[0];
}

$totalStories = fetchSingleCount($conn, "SELECT COUNT(*) FROM posts");
$communities = fetchSingleCount($conn, "SELECT COUNT(*) FROM users");
$destinations = fetchSingleCount($conn, "SELECT COUNT(*) FROM posts WHERE image IS NOT NULL AND image <> ''");

if ($destinations === 0 && $totalStories > 0) {
    $destinations = max(1, (int) round($totalStories * 0.55));
}

$onlineUsers = 0;
if ($communities > 0) {
    $estimatedOnline = ($communities * 0.22) + ($totalStories * 0.03) + ((int) date('i') % 7);
    $onlineUsers = (int) max(1, min($communities, round($estimatedOnline)));
} elseif ($totalStories > 0) {
    $onlineUsers = (int) max(1, min(25, round($totalStories * 0.04)));
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : '';
$preferredTheme = (isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'light') ? 'light' : 'dark';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TravelBlog - Simple, Smooth Travel Stories</title>
    <meta name="description" content="Read smooth travel stories, photo journals, and destination notes in a stylish dark-glass experience.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&family=Instrument+Serif:ital@0;1&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="assets/css/index.css?v=3">
    <link rel="stylesheet" href="assets/css/enhance.css?v=46">
    <link rel="stylesheet" href="assets/css/sexy-theme.css?v=1">
   

   
</head>

<body class="index-page<?php echo $preferredTheme === 'dark' ? ' dark' : ''; ?>">
    <?php
    tbRenderHeader([
        'is_logged_in' => $isLoggedIn,
        'user_id' => $_SESSION['user_id'] ?? 0,
        'user_name' => $userName,
        'preferred_theme' => $preferredTheme,
        'links' => [
            ['href' => 'index.php', 'label' => 'Home', 'when' => 'all'],
            ['href' => 'index.php#categories', 'label' => 'Categories', 'when' => 'all'],
            ['href' => 'add-post.php', 'label' => 'Write Story', 'class' => 'nav-highlight', 'when' => 'user'],
            ['href' => 'edit-profile.php', 'label' => 'Edit Profile', 'when' => 'user'],
            ['href' => 'logout.php', 'label' => 'Logout', 'when' => 'user'],
            ['href' => 'login.php', 'label' => 'Login', 'class' => 'nav-login', 'when' => 'guest'],
            ['href' => 'signup.php', 'label' => 'Join Free', 'class' => 'nav-signup', 'when' => 'guest']
        ]
    ]);
    ?>

    <section class="hero">
        <video class="hero-video" autoplay muted loop playsinline preload="metadata" poster="assets/videos/hero-poster.avif" aria-hidden="true">
            <source src="assets/videos/heroo.mp4" type="video/mp4">
        </video>
        <div class="hero-shell">
            <span class="hero-kicker">Simple. Smooth. Beautiful.</span>
            <h1>Travel Stories That Feel Effortless.</h1>
            <p>Search any place and read clean, stylish travel stories in seconds.</p>

            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Search places or stories">
            </div>

            <div class="hero-cta">
                <a href="#postsGrid" class="btn btn-primary"><i class="fas fa-compass"></i> Explore Stories</a>
                <?php if ($isLoggedIn): ?>
                    <a href="add-post.php" class="btn btn-secondary"><i class="fas fa-pen-to-square"></i> Write a Story</a>
                <?php else: ?>
                    <a href="signup.php" class="btn btn-secondary"><i class="fas fa-user-plus"></i> Join Free</a>
                <?php endif; ?>
            </div>

            <div class="hero-pills" aria-label="Hero highlights">
                <span class="hero-pill"><i class="fas fa-book-open"></i> <?php echo number_format($totalStories); ?> Stories</span>
                <span class="hero-pill"><i class="fas fa-compass"></i> <?php echo number_format($destinations); ?> Destinations</span>
                <span class="hero-pill"><i class="fas fa-users"></i> <?php echo number_format($communities); ?> Travelers</span>
            </div>

            <div class="hero-cards" aria-label="Hero quick cards">
                <article class="hero-mini-card">
                    <h3>Fresh stories today</h3>
                    <p>New travel posts from real explorers, updated live.</p>
                    <a href="#postsGrid" class="hero-mini-link"><i class="fas fa-arrow-right"></i> View Stories</a>
                </article>

                <article class="hero-mini-card">
                    <h3>Share your next trip</h3>
                    <p>Post your travel story with clean cards and a smooth reader experience.</p>
                    <?php if ($isLoggedIn): ?>
                        <a href="add-post.php" class="hero-mini-link"><i class="fas fa-pen-nib"></i> Write Story</a>
                    <?php else: ?>
                        <a href="login.php" class="hero-mini-link"><i class="fas fa-right-to-bracket"></i> Login to Write</a>
                    <?php endif; ?>
                </article>
            </div>
        </div>
    </section>

    <section class="stories-section fade-in" id="stories">
        <div class="container stories-container">
            <div class="section-header stories-header">
                <div class="section-copy">
                    <span class="section-kicker">Latest Stories</span>
                    <h2 class="stories-title">Latest Stories</h2>
                    <p class="section-subtitle">Fresh travel stories you can search, sort, and read instantly.</p>
                </div>
                <div class="stories-header-side">
                    <span class="stories-status" id="storiesStatus">Ready to explore</span>
                    <?php if ($isLoggedIn): ?>
                        <a href="add-post.php" class="btn btn-primary stories-cta">
                            <i class="fas fa-plus"></i> Share Your Story
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="explorer-toolbar" id="storiesToolbar">
                <div class="explorer-controls">
                    <label class="control-group control-sort" for="sortPosts">
                        <span class="control-label"><i class="fas fa-arrow-up-wide-short"></i> Sort</span>
                        <select id="sortPosts" aria-label="Sort stories">
                            <option value="newest">Newest</option>
                            <option value="title-asc">Title A-Z</option>
                            <option value="title-desc">Title Z-A</option>
                        </select>
                    </label>
                    <label class="control-group control-filter" for="filterPosts">
                        <span class="control-label"><i class="fas fa-sliders"></i> Filter</span>
                        <select id="filterPosts" aria-label="Filter stories">
                            <option value="all">All Stories</option>
                            <option value="with-image">With Image</option>
                            <option value="saved">Saved Stories</option>
                        </select>
                    </label>
                </div>
                <div class="explorer-meta">
                    <span id="resultsCount"><?php echo count($posts); ?> <?php echo count($posts) === 1 ? 'story' : 'stories'; ?></span>
                </div>
            </div>

            <div class="stories-grid-wrap" id="storiesGridWrap">
                <div class="grid" id="postsGrid" aria-live="polite">
                    <?php echo renderHomepageStoriesGrid($posts, $isLoggedIn, $_SESSION['user_id'] ?? null); ?>
                </div>
            </div>
        </div>
    </section>

    <section class="stats-section fade-in">
        <div class="stats-shell">
            <div class="section-copy section-copy-centered">
                <span class="section-kicker">Community Snapshot</span>
                <h2>A quick look around</h2>
                <p class="section-subtitle">See what people are sharing, reading, and saving across the site.</p>
            </div>

            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-number" id="onlineUsers" data-target="<?php echo (int) $onlineUsers; ?>"><?php echo number_format($onlineUsers); ?></span>
                    <span class="stat-label">Online Users <span class="live-indicator"></span></span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" id="totalPosts" data-target="<?php echo (int) $totalStories; ?>"><?php echo number_format($totalStories); ?></span>
                    <span class="stat-label">Total Stories</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" id="destinations" data-target="<?php echo (int) $destinations; ?>"><?php echo number_format($destinations); ?></span>
                    <span class="stat-label">Destinations</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" id="communities" data-target="<?php echo (int) $communities; ?>"><?php echo number_format($communities); ?></span>
                    <span class="stat-label">Communities</span>
                </div>
            </div>
        </div>
    </section>

    <button id="backToTop" class="back-to-top" type="button" aria-label="Back to top">
        <i class="fas fa-arrow-up"></i>
    </button>

    <section class="categories-section fade-in" id="categories">
        <div class="section-copy section-copy-centered">
            <span class="section-kicker">Explore</span>
            <h2 class="section-title">Browse by Category</h2>
            <p class="section-subtitle">Pick a mood and jump straight into the right stories.</p>
        </div>
        <div class="categories-grid">
            <div class="category-card" data-query="beach island coast" data-label="Beach Destinations">
                <div class="category-icon"><i class="fas fa-umbrella-beach"></i></div>
                <h3>Beach Destinations</h3>
                <p>Coastal stories, sunsets, and easy days by the water.</p>
            </div>
            <div class="category-card" data-query="mountain trek alpine" data-label="Mountain Adventures">
                <div class="category-icon"><i class="fas fa-mountain"></i></div>
                <h3>Mountain Adventures</h3>
                <p>Treks, cabins, cold air, and high trails.</p>
            </div>
            <div class="category-card" data-query="city urban skyline" data-label="City Exploration">
                <div class="category-icon"><i class="fas fa-city"></i></div>
                <h3>City Exploration</h3>
                <p>Street food, skylines, and neighborhoods with character.</p>
            </div>
            <div class="category-card" data-query="jungle forest wildlife" data-label="Jungle Trails">
                <div class="category-icon"><i class="fas fa-tree"></i></div>
                <h3>Jungle Trails</h3>
                <p>Off-grid routes, dense greens, and wild energy.</p>
            </div>
            <div class="category-card" data-query="history heritage ancient" data-label="Historical Sites">
                <div class="category-icon"><i class="fas fa-landmark"></i></div>
                <h3>Historical Sites</h3>
                <p>Old places, deep memory, and timeless details.</p>
            </div>
            <div class="category-card" data-query="food culture cuisine" data-label="Food and Culture">
                <div class="category-icon"><i class="fas fa-utensils"></i></div>
                <h3>Food & Culture</h3>
                <p>Local plates, markets, and stories shaped by culture.</p>
            </div>
        </div>
    </section>

    <?php
    tbRenderFooter([
        'is_logged_in' => $isLoggedIn,
        'user_id' => $_SESSION['user_id'] ?? 0,
        'show_newsletter' => true,
        'footer_class' => 'main-footer fade-in',
        'tagline' => 'Simple travel stories, smooth reading, and memories worth keeping.',
        'bottom_text' => 'Simple, smooth, made for travelers.'
    ]);
    ?>

    <script src="assets/js/index.fast.js?v=7"></script>
</body>
</html>
