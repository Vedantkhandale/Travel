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

    <link rel="stylesheet" href="assets/css/index.css">
</head>

<body class="index-page">

    <!-- Navbar -->
    <nav class="navbar" id="mainNav">
        <a href="index.php" class="logo">
            <i class="fas fa-globe"></i><span>Travel</span>Blog
        </a>

        <div class="nav-links" id="navLinks">
            <?php if ($isLoggedIn): ?>
                <a href="profile.php?user_id=<?php echo $_SESSION['user_id']; ?>" class="user-welcome">👋 Welcome, <?php echo htmlspecialchars($userName); ?>!</a>
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

        <button class="theme-btn" id="themeBtn" onclick="toggleTheme()">
            <i class="fas fa-moon"></i>
        </button>

        <button class="menu-toggle" id="mobile-menu">
            <i class="fas fa-bars"></i>
        </button>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <h1>The World Is Yours</h1>
        <h2>To Discover.</h2>
        <p>Document your memories, share hidden gems, and get inspired by a global community of modern-day explorers.</p>

        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Search posts, destinations, stories...">
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
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

    <!-- Notifications Container -->
    <div id="notificationContainer"></div>

    <!-- Recent Activity Section -->
    <section class="activity-section">
        <div class="activity-header">
            <h2>📍 Recent Activity</h2>
            <p>See what travelers around the world are up to right now</p>
        </div>

        <div class="activity-feed" id="activityFeed">
            <!-- Activity items will be populated by JavaScript -->
        </div>
    </section>

    <!-- Premium Slider Section -->
    <section class="slider-section">
        <div class="slider-wrapper">
            <div class="slides">
                <img src="https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?auto=format&fit=crop&w=1200&h=550" alt="Travel 1">
                <img src="https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=1200&h=550" alt="Travel 2">
                <img src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&w=1200&h=550" alt="Travel 3">
            </div>
        </div>
    </section>

    <!-- Posts Grid Section -->
    <div class="container">
        <div class="section-header">
            <h2>✨ Latest Stories</h2>
            <?php if ($isLoggedIn): ?>
                <a href="add-post.php" style="background: var(--primary); color: white; padding: 12px 24px; border-radius: 12px; text-decoration: none; font-weight: 600; transition: 0.3s;">
                    + Share Your Story
                </a>
            <?php endif; ?>
        </div>

        <div class="explorer-toolbar">
            <div class="explorer-controls">
                <label>
                    Sort
                    <select id="sortPosts">
                        <option value="newest">Newest</option>
                        <option value="title-asc">Title A-Z</option>
                        <option value="title-desc">Title Z-A</option>
                    </select>
                </label>
                <label>
                    Filter
                    <select id="filterPosts">
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
                            <h3><?php echo htmlspecialchars(substr($post['title'], 0, 50)); ?></h3>
                            <p><?php echo htmlspecialchars(substr($post['description'], 0, 100)); ?>...</p>
                            <p class="author">By <a href="profile.php?user_id=<?php echo $post['user_id']; ?>"><?php echo htmlspecialchars($post['author_name']); ?></a></p>
                        </div>
                        <div class="card-footer">
                            <div class="stats">
                                <button type="button" class="stat-item like-btn" data-post-id="<?php echo $post['id']; ?>" aria-label="Like post">
                                    <i class="far fa-heart"></i>
                                    <span class="like-count">0</span>
                                </button>
                                <button type="button" class="stat-item comment-btn" data-post-id="<?php echo $post['id']; ?>" aria-label="Add comment">
                                    <i class="far fa-comment"></i>
                                    <span class="comment-count">0</span>
                                </button>
                            </div>
                            <div class="card-actions">
                                <a href="post.php?slug=<?php echo htmlspecialchars($post['slug']); ?>" class="btn btn-ghost"><i class="fas fa-book-open"></i>Read</a>
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

    <!-- Categories Section -->
    <section class="categories-section" id="categories">
        <h2 style="text-align: center; font-size: 2.5rem; font-weight: 800; margin-bottom: 50px;">🧭 Popular Categories</h2>
        <div class="categories-grid">
            <div class="category-card">
                <div class="category-icon">🏖️</div>
                <h3>Beach Destinations</h3>
                <p>Explore stunning coastal paradises and island retreats</p>
            </div>
            <div class="category-card">
                <div class="category-icon">🏔️</div>
                <h3>Mountain Adventures</h3>
                <p>Conquer peaks and discover alpine landscapes</p>
            </div>
            <div class="category-card">
                <div class="category-icon">🏙️</div>
                <h3>City Exploration</h3>
                <p>Navigate vibrant urban centers and cultural hubs</p>
            </div>
            <div class="category-card">
                <div class="category-icon">🌴</div>
                <h3>Jungle Trails</h3>
                <p>Venture into lush rainforests and wildlife sanctuaries</p>
            </div>
            <div class="category-card">
                <div class="category-icon">🏛️</div>
                <h3>Historical Sites</h3>
                <p>Discover ancient wonders and cultural heritage</p>
            </div>
            <div class="category-card">
                <div class="category-icon">🍜</div>
                <h3>Food & Culture</h3>
                <p>Taste local cuisines and immerse in traditions</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="footer-grid">
            <div>
                <a href="index.php" class="footer-logo">
                    <i class="fas fa-globe"></i> <span style="color: var(--primary);">Travel</span>Blog
                </a>
                <p style="color: var(--text-muted); margin-bottom: 20px; line-height: 1.8;">Your gateway to discovering extraordinary destinations and sharing unforgettable travel experiences with a global community of adventurers.</p>
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
            <div>
                <h4 style="font-weight: 800; margin-bottom: 20px;">Quick Links</h4>
                <ul style="list-style: none; padding: 0;">
                    <li><a href="index.php" style="color: var(--text-muted); text-decoration: none; display: block; margin-bottom: 12px; transition: 0.3s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'">Home</a></li>
                    <li><a href="#categories" style="color: var(--text-muted); text-decoration: none; display: block; margin-bottom: 12px; transition: 0.3s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'">Categories</a></li>
                    <li><a href="<?php echo $isLoggedIn ? 'add-post.php' : 'login.php'; ?>" style="color: var(--text-muted); text-decoration: none; display: block; margin-bottom: 12px; transition: 0.3s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'">Add Post</a></li>
                    <li><a href="#" style="color: var(--text-muted); text-decoration: none; display: block; margin-bottom: 12px; transition: 0.3s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'">About</a></li>
                </ul>
            </div>
            <div>
                <h4 style="font-weight: 800; margin-bottom: 20px;">Support</h4>
                <ul style="list-style: none; padding: 0;">
                    <li><a href="#" style="color: var(--text-muted); text-decoration: none; display: block; margin-bottom: 12px; transition: 0.3s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'">Help Center</a></li>
                    <li><a href="#" style="color: var(--text-muted); text-decoration: none; display: block; margin-bottom: 12px; transition: 0.3s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'">Privacy Policy</a></li>
                    <li><a href="#" style="color: var(--text-muted); text-decoration: none; display: block; margin-bottom: 12px; transition: 0.3s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'">Terms of Service</a></li>
                    <li><a href="#" style="color: var(--text-muted); text-decoration: none; display: block; margin-bottom: 12px; transition: 0.3s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'">Contact Us</a></li>
                </ul>
            </div>
        </div>
        <div style="text-align: center; padding-top: 30px; border-top: 1px solid var(--border); margin-top: 50px; color: var(--text-muted);">
            <p>&copy; 2024 TravelBlog. All rights reserved. Made with ❤️ for travelers.</p>
        </div>
    </footer>

    <script src="assets/js/index.js"></script>
</body>

</html>