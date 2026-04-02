<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'includes/db.php';
session_start();

// Get user_id from URL
if (!isset($_GET['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = mysqli_real_escape_string($conn, $_GET['user_id']);

// Fetch user info
$user_query = "SELECT * FROM users WHERE id = '$user_id'";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

if (!$user) {
    header("Location: index.php");
    exit();
}

// Fetch posts by this user
$query = "SELECT posts.*, users.name as author_name FROM posts JOIN users ON posts.user_id = users.id WHERE posts.user_id = '$user_id' ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
$posts = [];
while ($row = mysqli_fetch_assoc($result)) {
    $posts[] = $row;
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : '';
$isOwner = $isLoggedIn && $_SESSION['user_id'] == $user_id;

// Check for update success message
$updateMessage = isset($_GET['updated']) && $_GET['updated'] == '1' ? "✅ Profile updated successfully!" : "";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user['name']); ?>'s Profile - TravelBlog</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>

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
            <a href="#posts">Posts</a>
            <?php if ($isOwner): ?>
                <a href="edit-profile.php">Edit Profile</a>
            <?php endif; ?>
            <?php if ($isLoggedIn): ?>
                <a href="add-post.php">Add Post</a>
                <a href="logout.php">Logout</a>
            <?php endif; ?>
        </div>
        
        <button class="theme-btn" id="themeBtn" onclick="toggleTheme()">
            <i class="fas fa-moon"></i>
        </button>
        
        <button class="menu-toggle" id="mobile-menu">
            <i class="fas fa-bars"></i>
        </button>
    </nav>

    <!-- Profile Header -->
    <section class="hero profile-header" style="padding: 140px 20px 80px; background: <?php echo !empty($user['background_image']) ? 'linear-gradient(135deg, rgba(0,0,0,0.5) 0%, rgba(0,0,0,0.3) 30%, rgba(0,0,0,0.4) 70%, rgba(0,0,0,0.5) 100%), url(uploads/' . htmlspecialchars($user['background_image']) . ') center/cover no-repeat' : 'linear-gradient(135deg, rgba(99,102,241,0.9) 0%, rgba(168,85,247,0.9) 50%, rgba(6,182,212,0.9) 100%)'; ?>; position: relative; min-height: 75vh; display: flex; align-items: center; justify-content: center;">
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(45deg, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0.4) 50%, rgba(0,0,0,0.2) 100%);"></div>
        <div style="text-align: center; position: relative; z-index: 2; color: white; max-width: 900px; margin: 0 auto; padding: 0 20px;">
            <?php if (!empty($updateMessage)): ?>
                <div style="background: var(--success); color: white; padding: 15px 30px; border-radius: 15px; margin-bottom: 30px; display: inline-block; box-shadow: var(--shadow-lg); animation: slideDown 0.5s ease;">
                    <?php echo $updateMessage; ?>
                </div>
            <?php endif; ?>
            
            <div style="width: 160px; height: 160px; border-radius: 50%; overflow: hidden; border: 6px solid rgba(255,255,255,0.95); margin: 0 auto 35px; box-shadow: 0 15px 35px rgba(0,0,0,0.4), 0 0 60px rgba(255,255,255,0.1); transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); position: relative;" class="profile-photo-container">
                <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%); border-radius: 50%;"></div>
                <?php if (!empty($user['profile_photo'])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($user['profile_photo']); ?>" alt="<?php echo htmlspecialchars($user['name']); ?>" style="width: 100%; height: 100%; object-fit: cover; position: relative; z-index: 1;">
                <?php else: ?>
                    <div style="width: 100%; height: 100%; background: linear-gradient(135deg, var(--primary), var(--secondary)); display: flex; align-items: center; justify-content: center; color: white; font-size: 4.5rem; position: relative; z-index: 1;">
                        <i class="fas fa-user"></i>
                    </div>
                <?php endif; ?>
            </div>
            <h1 style="font-size: clamp(2.8rem, 6vw, 4rem); font-weight: 700; margin-bottom: 20px; background: linear-gradient(135deg, #a855f7, #06b6d4, #6366f1); background-size: 300% auto; -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; animation: gradientShift 4s linear infinite, fadeInUp 1s ease-out; text-shadow: 0 2px 4px rgba(0,0,0,0.8), 0 4px 8px rgba(0,0,0,0.6); letter-spacing: -1.5px; line-height: 1.1; position: relative;"><?php echo htmlspecialchars($user['name']); ?>'s Travel Stories</h1>
            <p style="font-size: clamp(1.1rem, 2.5vw, 1.4rem); margin-bottom: 50px; background: linear-gradient(135deg, #a855f7, #06b6d4, #6366f1); background-size: 300% auto; -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; animation: gradientShift 4s linear infinite, fadeInUp 1s ease-out 0.2s both; text-shadow: 0 2px 4px rgba(0,0,0,0.7); font-weight: 300; opacity: 0.9; letter-spacing: 0.5px; position: relative;">Discover amazing destinations through <?php echo htmlspecialchars($user['name']); ?>'s eyes</p>
            
            <div style="display: flex; align-items: center; justify-content: center; gap: 20px; flex-wrap: wrap; margin-bottom: 40px;" class="stats-container">
                <div style="background: rgba(255,255,255,0.95); backdrop-filter: blur(15px); padding: 18px 30px; border-radius: 50px; box-shadow: 0 8px 25px rgba(0,0,0,0.15), 0 0 40px rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.3); transition: all 0.3s ease; position: relative; overflow: hidden;" class="stats-badge">
                    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);"></div>
                    <i class="fas fa-map-marked-alt" style="color: var(--primary); margin-right: 10px; font-size: 1.2rem; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));"></i>
                    <span style="font-weight: 700; color: var(--text-main); font-size: 1.1rem; position: relative; z-index: 1; text-shadow: 0 1px 2px rgba(0,0,0,0.1);"><?php echo count($posts); ?> Adventure<?php echo count($posts) != 1 ? 's' : ''; ?></span>
                </div>
                <div style="background: rgba(255,255,255,0.95); backdrop-filter: blur(15px); padding: 18px 30px; border-radius: 50px; box-shadow: 0 8px 25px rgba(0,0,0,0.15), 0 0 40px rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.3); transition: all 0.3s ease; position: relative; overflow: hidden;" class="stats-badge">
                    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);"></div>
                    <i class="fas fa-calendar-alt" style="color: var(--secondary); margin-right: 10px; font-size: 1.2rem; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));"></i>
                    <span style="font-weight: 700; color: var(--text-main); font-size: 1.1rem; position: relative; z-index: 1; text-shadow: 0 1px 2px rgba(0,0,0,0.1);">Joined <?php echo date('M Y', strtotime($user['created_at'] ?? 'now')); ?></span>
                </div>
            </div>
            
            <?php if ($isOwner): ?>
                <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
                    <a href="add-post.php" style="background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(248,250,252,0.9) 100%); backdrop-filter: blur(15px); color: var(--primary); padding: 16px 32px; border-radius: 50px; text-decoration: none; font-weight: 700; transition: all 0.3s ease; box-shadow: 0 8px 25px rgba(0,0,0,0.15), 0 0 40px rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.3); display: inline-flex; align-items: center; gap: 10px; font-size: 1.1rem; position: relative; overflow: hidden;" class="profile-header-btn">
                        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, rgba(99,102,241,0.1) 0%, rgba(168,85,247,0.05) 100%);"></div>
                        <i class="fas fa-plus" style="position: relative; z-index: 1; filter: drop-shadow(0 1px 2px rgba(0,0,0,0.2));"></i> 
                        <span style="position: relative; z-index: 1; text-shadow: 0 1px 2px rgba(0,0,0,0.1);">Share New Story</span>
                    </a>
                    <a href="edit-profile.php" style="background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(241,245,249,0.85) 100%); backdrop-filter: blur(15px); color: var(--text-main); padding: 16px 32px; border-radius: 50px; text-decoration: none; font-weight: 700; transition: all 0.3s ease; box-shadow: 0 8px 25px rgba(0,0,0,0.15), 0 0 40px rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.3); display: inline-flex; align-items: center; gap: 10px; font-size: 1.1rem; position: relative; overflow: hidden;" class="profile-header-btn">
                        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, rgba(0,0,0,0.05) 0%, rgba(0,0,0,0.02) 100%);"></div>
                        <i class="fas fa-edit" style="position: relative; z-index: 1; filter: drop-shadow(0 1px 2px rgba(0,0,0,0.2));"></i> 
                        <span style="position: relative; z-index: 1; text-shadow: 0 1px 2px rgba(0,0,0,0.1);">Edit Profile</span>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Posts Section -->
    <div class="container" id="posts">
        <div class="section-header">
            <h2>📖 <?php echo htmlspecialchars($user['name']); ?>'s Stories</h2>
            <p><?php echo count($posts); ?> adventure<?php echo count($posts) != 1 ? 's' : ''; ?> shared</p>
        </div>

        <div class="grid" id="postsGrid">
            <?php if (empty($posts)): ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
                    <h3 style="color: var(--text-muted); margin-bottom: 20px;">No stories yet!</h3>
                    <p style="color: var(--text-muted); margin-bottom: 30px;"><?php echo $isOwner ? 'Start sharing your travel adventures' : htmlspecialchars($user['name']) . ' hasn\'t shared any stories yet'; ?>.</p>
                    <?php if ($isOwner): ?>
                        <a href="add-post.php" style="background: var(--primary); color: white; padding: 12px 24px; border-radius: 12px; text-decoration: none; font-weight: 600;">
                            Start Writing
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <div class="card" id="post-<?php echo $post['id']; ?>" data-post-id="<?php echo $post['id']; ?>" data-title="<?php echo htmlspecialchars($post['title']); ?>" data-description="<?php echo htmlspecialchars($post['description']); ?>">
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
                                <a href="post.php?slug=<?php echo htmlspecialchars($post['slug']); ?>" class="btn btn-ghost"><i class="fas fa-book-open" style="margin-right:6px;"></i>Read</a>
                                <?php if ($isOwner): ?>
                                    <a href="edit-post.php?id=<?php echo $post['id']; ?>" class="btn btn-secondary"><i class="fas fa-edit" style="margin-right:6px;"></i>Edit</a>
                                    <button onclick="deletePost(<?php echo $post['id']; ?>)" class="btn btn-secondary" style="background: var(--error);"><i class="fas fa-trash" style="margin-right:6px;"></i>Delete</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

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
                    <li><a href="signup.php" style="color: var(--text-muted); text-decoration: none; display: block; margin-bottom: 12px; transition: 0.3s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'">Join Community</a></li>
                    <li><a href="login.php" style="color: var(--text-muted); text-decoration: none; display: block; margin-bottom: 12px; transition: 0.3s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'">Login</a></li>
                </ul>
            </div>
            <div>
                <h4 style="font-weight: 800; margin-bottom: 20px;">Support</h4>
                <ul style="list-style: none; padding: 0;">
                    <li><a href="#" style="color: var(--text-muted); text-decoration: none; display: block; margin-bottom: 12px; transition: 0.3s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'">Help Center</a></li>
                    <li><a href="#" style="color: var(--text-muted); text-decoration: none; display: block; margin-bottom: 12px; transition: 0.3s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'">Contact Us</a></li>
                    <li><a href="#" style="color: var(--text-muted); text-decoration: none; display: block; margin-bottom: 12px; transition: 0.3s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'">Privacy Policy</a></li>
                </ul>
            </div>
        </div>
        <div style="text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid var(--border);">
            <p style="color: var(--text-muted); font-size: 0.9rem;">© 2024 TravelBlog. Made with ❤️ for travelers worldwide.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="assets/js/index.js"></script>
</body>
</html>