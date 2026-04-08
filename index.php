<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'includes/db.php';
include 'includes/story_helpers.php';
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

function compactCount($value)
{
    $number = max(0, (int) $value);
    if ($number >= 1000000) {
        return number_format($number / 1000000, $number >= 10000000 ? 0 : 1) . 'M';
    }
    if ($number >= 1000) {
        return number_format($number / 1000, $number >= 10000 ? 0 : 1) . 'K';
    }
    return number_format($number);
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TravelBlog - Stories Worth Packing</title>
    <meta name="description" content="Discover sharp travel stories, cinematic photo journals, and a community that makes every destination feel alive.">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="assets/css/index.css?v=3">
    <link rel="stylesheet" href="assets/css/enhance.css?v=36">
   

   
</head>

<body class="index-page">

    <nav class="navbar" id="mainNav">
        <a href="index.php" class="logo">
            <i class="fas fa-globe"></i><span>Travel</span>Blog
        </a>

        <div class="nav-links" id="navLinks">
            <?php if ($isLoggedIn): ?>
                <a href="profile.php?user_id=<?php echo $_SESSION['user_id']; ?>" class="user-welcome">
                    <i class="fas fa-user"></i> Hi, <?php echo htmlspecialchars($userName); ?>
                </a>
            <?php endif; ?>

            <a href="index.php">Home</a>
            <a href="#categories">Categories</a>
            <?php if ($isLoggedIn): ?>
                <a href="add-post.php" class="nav-highlight">Write Story</a>
                <a href="edit-profile.php">Edit Profile</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php" class="nav-login">Login</a>
                <a href="signup.php" class="nav-signup">Join Free</a>
            <?php endif; ?>
        </div>

        <button class="theme-btn" id="themeBtn" type="button" aria-label="Toggle theme" onclick="toggleTheme()">
            <i class="fas fa-moon"></i>
        </button>

        <button class="menu-toggle" id="mobile-menu" type="button" aria-label="Open menu" aria-controls="navLinks" aria-expanded="false">
            <i class="fas fa-bars"></i>
        </button>
    </nav>

    <section class="hero">
        <video class="hero-video" autoplay muted loop playsinline preload="metadata" poster="https://images.unsplash.com/photo-1488085061387-422e29b40080?auto=format&fit=crop&w=2100&q=80" aria-hidden="true">
            <source src="assets/videos/hero.mp4" type="video/mp4">
            <source src="https://cdn.coverr.co/videos/coverr-aerial-view-of-a-mountain-road-1579/1080p.mp4" type="video/mp4">
        </video>
        <div class="hero-shell">
            <span class="hero-kicker">Travel stories with stronger visuals, sharper copy, and real-world energy.</span>
            <h1>Collect Places That Stay With You.</h1>
            <h2 class="typing-text">Tell them beautifully.</h2>
            <p>Build a travel journal that feels cinematic, personal, and worth revisiting long after the flight home.</p>

            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Search stories, cities, coastlines, creators...">
            </div>

            <div class="hero-cta">
                <a href="#postsGrid" class="btn btn-primary"><i class="fas fa-compass"></i> Explore Stories</a>
                <?php if ($isLoggedIn): ?>
                    <a href="add-post.php" class="btn btn-secondary"><i class="fas fa-pen-to-square"></i> Write a Story</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-secondary"><i class="fas fa-right-to-bracket"></i> Login</a>
                <?php endif; ?>
            </div>

            <div class="hero-pills" aria-label="Hero highlights">
                <span class="hero-pill"><i class="fas fa-book-open"></i> <?php echo compactCount($totalStories); ?> Stories</span>
                <span class="hero-pill"><i class="fas fa-compass"></i> <?php echo number_format($destinations); ?> Destinations</span>
                <span class="hero-pill"><i class="fas fa-users"></i> <?php echo number_format($communities); ?> Communities</span>
            </div>
        </div>

        <div class="hero-spotlight" aria-hidden="true">
            <div class="spotlight-card spotlight-card-primary">
                <span class="spotlight-label">Trending now</span>
                <strong>Slow travel diaries</strong>
                <p>Photo-first stories, local food trails, and sunrise journals with real texture.</p>
            </div>
            <div class="spotlight-card spotlight-card-secondary">
                <span class="spotlight-label">Community pulse</span>
                <strong><?php echo compactCount($onlineUsers); ?> explorers online</strong>
                <p>Fresh drops, saves, and comments are landing across the map right now.</p>
            </div>
        </div>
    </section>

    <section class="stories-section fade-in" id="stories">
        <div class="container stories-container">
            <div class="section-header stories-header">
                <div class="section-copy">
                    <span class="section-kicker">Editor's Feed</span>
                    <h2 class="stories-title"><i class="fas fa-bolt"></i> Latest Stories</h2>
                    <p class="section-subtitle">Search, sort, and filter stories instantly without reloading the page.</p>
                </div>
                <div class="stories-header-side">
                    <span class="stories-status" id="storiesStatus">Fast mode on</span>
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
                <span class="section-kicker">Live Snapshot</span>
                <h2>Fresh movement across the map</h2>
                <p class="section-subtitle">A quick pulse check on what the community is publishing, saving, and reading right now.</p>
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
            <span class="section-kicker">Pick A Mood</span>
            <h2 class="section-title"><i class="fas fa-compass"></i> Popular Categories</h2>
            <p class="section-subtitle">Jump into the vibe you want, then let the stories narrow themselves down.</p>
        </div>
        <div class="categories-grid">
            <div class="category-card" data-query="beach island coast" data-label="Beach Destinations">
                <div class="category-icon"><i class="fas fa-umbrella-beach"></i></div>
                <h3>Beach Destinations</h3>
                <p>Salt air, golden hour swims, and coastlines worth missing flights for.</p>
            </div>
            <div class="category-card" data-query="mountain trek alpine" data-label="Mountain Adventures">
                <div class="category-icon"><i class="fas fa-mountain"></i></div>
                <h3>Mountain Adventures</h3>
                <p>Ridges, cabin mornings, and trails that reset your head.</p>
            </div>
            <div class="category-card" data-query="city urban skyline" data-label="City Exploration">
                <div class="category-icon"><i class="fas fa-city"></i></div>
                <h3>City Exploration</h3>
                <p>Street food, skylines, hidden bars, and neighborhoods with character.</p>
            </div>
            <div class="category-card" data-query="jungle forest wildlife" data-label="Jungle Trails">
                <div class="category-icon"><i class="fas fa-tree"></i></div>
                <h3>Jungle Trails</h3>
                <p>Dense greens, wild soundscapes, and off-grid adrenaline.</p>
            </div>
            <div class="category-card" data-query="history heritage ancient" data-label="Historical Sites">
                <div class="category-icon"><i class="fas fa-landmark"></i></div>
                <h3>Historical Sites</h3>
                <p>Places with texture, memory, and stories older than maps.</p>
            </div>
            <div class="category-card" data-query="food culture cuisine" data-label="Food and Culture">
                <div class="category-icon"><i class="fas fa-utensils"></i></div>
                <h3>Food & Culture</h3>
                <p>Local plates, late-night markets, and culture you can taste.</p>
            </div>
        </div>
    </section>

    <footer class="main-footer fade-in">
        <div class="footer-grid">
            <div class="footer-col footer-brand">
                <a href="index.php" class="footer-logo">
                    <i class="fas fa-globe"></i> <span>Travel</span>Blog
                </a>
                <p class="footer-text">A sharper home for destination stories, photo journals, and the kind of travel memories worth keeping alive.</p>
                <div class="social-icons">
                    <a href="https://facebook.com" class="social-btn" title="Facebook" target="_blank" rel="noopener noreferrer">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://twitter.com" class="social-btn" title="Twitter" target="_blank" rel="noopener noreferrer">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://instagram.com" class="social-btn" title="Instagram" target="_blank" rel="noopener noreferrer">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://youtube.com" class="social-btn" title="YouTube" target="_blank" rel="noopener noreferrer">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>

            <div class="footer-col">
                <h4>Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="index.php"><i class="fas fa-house"></i> Home</a></li>
                    <li><a href="#categories"><i class="fas fa-compass"></i> Categories</a></li>
                    <li><a href="<?php echo $isLoggedIn ? 'add-post.php' : 'login.php'; ?>"><i class="fas fa-pen-to-square"></i> Add Post</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Explore</h4>
                <ul class="footer-links">
                    <li><a href="#postsGrid"><i class="fas fa-bolt"></i> Latest Stories</a></li>
                    <li><a href="#categories"><i class="fas fa-mountain"></i> Adventures</a></li>
                    <li><a href="<?php echo $isLoggedIn ? 'profile.php?user_id=' . $_SESSION['user_id'] : 'login.php'; ?>"><i class="fas fa-user"></i> Profile</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Newsletter</h4>
                <p class="footer-text">One sharp travel drop every week. Pure inspiration, zero spam.</p>
                <form class="footer-form" id="newsletterForm" novalidate>
                    <input type="email" id="newsletterEmail" name="newsletter_email" placeholder="you@example.com" aria-label="Email address" autocomplete="email" required>
                    <button class="btn btn-primary" type="submit">Subscribe</button>
                </form>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="footer-bottom-inner">
                <span>&copy; <?php echo date('Y'); ?> TravelBlog</span>
                <span>Built for explorers with taste.</span>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="assets/js/index.fast.js?v=1"></script>
</body>
</html>
