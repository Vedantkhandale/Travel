<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'includes/db.php';
session_start();

// Fetch all posts for the homepage
$query = "SELECT posts.*, users.name as author_name FROM posts JOIN users ON posts.user_id = users.id ORDER BY created_at DESC LIMIT 12";
$result = mysqli_query($conn, $query);
$posts = [];
while ($row = mysqli_fetch_assoc($result)) {
    $posts[] = $row;
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
    <title>TravelBlog - Explore The World</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="assets/css/index.css?v=3">
    <link rel="stylesheet" href="assets/css/enhance.css?v=14">
   

   
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
                <a href="add-post.php">Add Post</a> <a href="edit-profile.php">Edit Profile</a> <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="signup.php">Signup</a>
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
        <h1>The World Is Yours</h1>
        <h2 class="typing-text">To Discover.</h2>
        <p>Document your memories, share hidden gems, and get inspired by a global community of modern-day explorers.</p>

        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Search posts, destinations, stories...">
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
            <span class="hero-pill"><i class="fas fa-book-open"></i> 12K+ Stories</span>
            <span class="hero-pill"><i class="fas fa-compass"></i> 89 Destinations</span>
            <span class="hero-pill"><i class="fas fa-users"></i> 156 Communities</span>
        </div>
    </section>

    <section class="stats-section fade-in">
        <div class="stats-grid">
            <div class="stat-item">
                <span class="stat-number" id="onlineUsers">245</span>
                <span class="stat-label">Online Users <span class="live-indicator"></span></span>
            </div>
            <div class="stat-item">
                <span class="stat-number" id="totalPosts">1,248</span>
                <span class="stat-label">Total Stories</span>
            </div>
            <div class="stat-item">
                <span class="stat-number" id="destinations">89</span>
                <span class="stat-label">Destinations</span>
            </div>
            <div class="stat-item">
                <span class="stat-number" id="communities">156</span>
                <span class="stat-label">Communities</span>
            </div>
        </div>
    </section>

    <div id="notificationContainer"></div>

    <section class="activity-section fade-in">
        <div class="activity-header">
            <h2><i class="fas fa-location-dot"></i> Recent Activity</h2>
            <p>See what travelers around the world are up to right now</p>
        </div>

        <div class="activity-feed" id="activityFeed">
            </div>
    </section>

    <div class="container fade-in">
        <div class="section-header">
            <h2><i class="fas fa-bolt"></i> Latest Stories</h2>
            <?php if ($isLoggedIn): ?>
                <a href="add-post.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Share Your Story
                </a>
            <?php endif; ?>
        </div>

        <div class="explorer-toolbar">
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
                <span id="resultsCount">0 stories</span>
            </div>
        </div>

        <div class="grid" id="postsGrid">
            <?php if (empty($posts)): ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
                    <h3 style="color: var(--text-muted); margin-bottom: 20px;">No stories yet!</h3>
                    <p style="color: var(--text-muted); margin-bottom: 30px;">Be the first to share your travel adventure</p>
                    <?php if ($isLoggedIn): ?>
                        <a href="add-post.php" style="background: var(--primary); color: white; padding: 12px 24px; border-radius: 12px; text-decoration: none; font-weight: 600;">
                            Start Writing
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <div class="card" id="post-<?php echo $post['id']; ?>" data-post-id="<?php echo $post['id']; ?>" data-title="<?php echo htmlspecialchars($post['title']); ?>" data-description="<?php echo htmlspecialchars($post['description']); ?>" data-author="<?php echo htmlspecialchars($post['author_name']); ?>" data-has-image="<?php echo !empty($post['image']) ? '1' : '0'; ?>">
                        <div class="card-img">
                            <?php if (!empty($post['image'])): ?>
                                <img src="uploads/<?php echo htmlspecialchars($post['image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/350x260?text=Travel+Story" alt="placeholder">
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <h3>
                                <a href="post.php?slug=<?php echo htmlspecialchars($post['slug']); ?>" class="card-title-link">
                                    <?php echo htmlspecialchars(substr($post['title'], 0, 50)); ?>
                                </a>
                            </h3>
                            <p><?php echo htmlspecialchars(substr($post['description'], 0, 100)); ?>...</p>
                            <p class="author">By <a href="profile.php?user_id=<?php echo $post['user_id']; ?>"><?php echo htmlspecialchars($post['author_name']); ?></a></p>
                        </div>
                        <div class="card-footer premium-card-footer">
                            <div class="stats footer-stats">
                                <button type="button" class="stat-item like-btn" data-post-id="<?php echo $post['id']; ?>" aria-label="Like post">
                                    <i class="far fa-heart"></i>
                                    <span class="like-count">0</span>
                                </button>
                                <button type="button" class="stat-item comment-btn" data-post-id="<?php echo $post['id']; ?>" aria-label="Add comment">
                                    <i class="far fa-comment"></i>
                                    <span class="comment-count">0</span>
                                </button>
                            </div>
                            <div class="card-actions footer-actions">
                                <button type="button" class="btn btn-ghost save-btn" data-post-id="<?php echo $post['id']; ?>" aria-label="Save post">
                                    <i class="far fa-bookmark"></i>Save
                                </button>
                                <?php if ($isLoggedIn && $_SESSION['user_id'] == $post['user_id']): ?>
                                    <a href="edit-post.php?id=<?php echo $post['id']; ?>" class="btn btn-secondary"><i class="fas fa-edit"></i>Edit</a>
                                <?php else: ?>
                                    <button class="btn btn-secondary disabled" title="Only the author can edit"><i class="fas fa-edit"></i>Edit</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <button id="backToTop" class="back-to-top" type="button" aria-label="Back to top">
        <i class="fas fa-arrow-up"></i>
    </button>

    <section class="categories-section fade-in" id="categories">
        <h2 class="section-title"><i class="fas fa-compass"></i> Popular Categories</h2>
        <div class="categories-grid">
            <div class="category-card">
                <div class="category-icon"><i class="fas fa-umbrella-beach"></i></div>
                <h3>Beach Destinations</h3>
                <p>Explore stunning coastal paradises and island retreats</p>
            </div>
            <div class="category-card">
                <div class="category-icon"><i class="fas fa-mountain"></i></div>
                <h3>Mountain Adventures</h3>
                <p>Conquer peaks and discover alpine landscapes</p>
            </div>
            <div class="category-card">
                <div class="category-icon"><i class="fas fa-city"></i></div>
                <h3>City Exploration</h3>
                <p>Navigate vibrant urban centers and cultural hubs</p>
            </div>
            <div class="category-card">
                <div class="category-icon"><i class="fas fa-tree"></i></div>
                <h3>Jungle Trails</h3>
                <p>Venture into lush rainforests and wildlife sanctuaries</p>
            </div>
            <div class="category-card">
                <div class="category-icon"><i class="fas fa-landmark"></i></div>
                <h3>Historical Sites</h3>
                <p>Discover ancient wonders and cultural heritage</p>
            </div>
            <div class="category-card">
                <div class="category-icon"><i class="fas fa-utensils"></i></div>
                <h3>Food & Culture</h3>
                <p>Taste local cuisines and immerse in traditions</p>
            </div>
        </div>
    </section>

    <footer class="main-footer fade-in">
        <div class="footer-grid">
            <div class="footer-col footer-brand">
                <a href="index.php" class="footer-logo">
                    <i class="fas fa-globe"></i> <span>Travel</span>Blog
                </a>
                <p class="footer-text">Your gateway to discovering extraordinary destinations and sharing unforgettable travel experiences with a global community of adventurers.</p>
                <div class="social-icons">
                    <a href="https://facebook.com" class="social-btn" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://twitter.com" class="social-btn" title="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://instagram.com" class="social-btn" title="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://youtube.com" class="social-btn" title="YouTube">
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
                <p class="footer-text">One curated travel story every week. No spam.</p>
                <form class="footer-form" onsubmit="return false">
                    <input type="email" placeholder="you@example.com" aria-label="Email address">
                    <button class="btn btn-primary" type="button">Subscribe</button>
                </form>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="footer-bottom-inner">
                <span>&copy; <?php echo date('Y'); ?> TravelBlog</span>
                <span>Built for explorers.</span>
            </div>
        </div>
    </footer>

    <script src="assets/js/index.js?v=2"></script>
</body>
</html>
