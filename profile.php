<?php
// ... (Tera PHP Logic top par bilkul same rahega)
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'includes/db.php';
session_start();

if (!isset($_GET['user_id'])) { header("Location: index.php"); exit(); }
$user_id = mysqli_real_escape_string($conn, $_GET['user_id']);
$user_query = "SELECT * FROM users WHERE id = '$user_id'";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);
if (!$user) { header("Location: index.php"); exit(); }

$query = "SELECT posts.*, users.name as author_name FROM posts JOIN users ON posts.user_id = users.id WHERE posts.user_id = '$user_id' ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
$posts = [];
while ($row = mysqli_fetch_assoc($result)) { $posts[] = $row; }

$isLoggedIn = isset($_SESSION['user_id']);
$isOwner = $isLoggedIn && $_SESSION['user_id'] == $user_id;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user['name']); ?> - Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --bg-color: #050505;
            --card-bg: #111111;
            --text-main: #ffffff;
            --text-muted: #94a3b8;
            --accent: #6366f1;
            --glass-border: rgba(255, 255, 255, 0.1);
            --nav-bg: #ffffff;
            --nav-text: #0f172a;
        }

        [data-theme="light"] {
            --bg-color: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --glass-border: rgba(0, 0, 0, 0.08);
            --nav-bg: #ffffff;
            --nav-text: #0f172a;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            margin: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: 0.3s ease;
        }

        /* --- Navbar: Pure White Modern --- */
        .navbar {
            background: var(--nav-bg);
            padding: 15px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        .navbar .logo {
            color: var(--nav-text) !important;
            font-weight: 800;
            font-size: 1.5rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar .logo i { color: var(--accent); }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .nav-links a {
            color: #475569 !important;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
        }

        .nav-links a:hover { color: var(--accent) !important; }

        /* Theme Switcher in Nav */
        .theme-switch-nav {
            cursor: pointer;
            width: 40px; height: 40px;
            display: flex; align-items: center; justify-content: center;
            background: #f1f5f9;
            border-radius: 12px;
            color: #0f172a;
            transition: 0.3s;
        }

        .theme-switch-nav:hover { background: var(--accent); color: white; }

        /* --- Hero Section: Massive & Pro --- */
        .profile-hero {
            position: relative;
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 120px 20px 60px;
            background: <?php echo !empty($user['background_image']) ? 'url(uploads/' . htmlspecialchars($user['background_image']) . ') center/cover no-repeat fixed' : 'linear-gradient(135deg, #0f172a, #1e293b)'; ?>;
        }

        .hero-mask {
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(0,0,0,0.3) 0%, var(--bg-color) 100%);
            z-index: 1;
        }

        .profile-glass-card {
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(25px);
            border: 1px solid var(--glass-border);
            border-radius: 50px;
            padding: 60px 40px;
            text-align: center;
            max-width: 850px;
            width: 90%;
            box-shadow: 0 40px 100px rgba(0,0,0,0.5);
        }

        .avatar-container {
            width: 160px; height: 160px;
            margin: -140px auto 30px;
            padding: 6px;
            background: linear-gradient(135deg, var(--accent), #a855f7);
            border-radius: 50%;
            box-shadow: 0 0 40px rgba(99, 102, 241, 0.4);
        }

        .avatar-container img {
            width: 100%; height: 100%;
            border-radius: 50%;
            border: 5px solid var(--bg-color);
            object-fit: cover;
        }

        .profile-info h1 {
            font-size: 4.5rem;
            font-weight: 800;
            letter-spacing: -3px;
            margin: 0;
            background: linear-gradient(to bottom, #fff 40%, #94a3b8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        [data-theme="light"] .profile-info h1 {
            background: linear-gradient(to bottom, #0f172a, #64748b);
            -webkit-background-clip: text;
        }

        /* --- Grid & Cards --- */
        .main-container { padding: 60px 5%; margin-top: -40px; position: relative; z-index: 3; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 35px; }

        .sexy-card {
            background: var(--card-bg);
            border-radius: 30px;
            border: 1px solid var(--glass-border);
            overflow: hidden;
            display: flex; flex-direction: column;
            transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
        }

        .sexy-card:hover { transform: translateY(-15px); border-color: var(--accent); box-shadow: 0 30px 60px rgba(0,0,0,0.4); }

        .sexy-card img { width: 100%; height: 280px; object-fit: cover; }

        .card-info { padding: 25px; flex-grow: 1; display: flex; flex-direction: column; }

        .btn-ui {
            padding: 14px 35px;
            border-radius: 100px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex; align-items: center; gap: 10px;
            transition: 0.3s;
        }

        .btn-add { background: var(--accent); color: white; }
        .btn-edit { background: rgba(255,255,255,0.05); color: var(--text-main); border: 1px solid var(--glass-border); }

        .delete-btn {
            position: absolute; top: 15px; right: 15px;
            background: rgba(239, 68, 68, 0.9);
            color: white; border: none; width: 40px; height: 40px;
            border-radius: 12px; cursor: pointer; z-index: 10;
        }
    </style>
</head>

<body>

    <nav class="navbar">
       <a href="index.php" class="logo">
    <i class="fas fa-globe" style="color: black; font-size: 26px;"></i> 
    <span style="color: #6c5ce7; font-size: 26px;">Travel </span> Blog
</a>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <?php if ($isLoggedIn): ?>
                <a href="logout.php">Logout</a>
            <?php endif; ?>
            <div class="theme-switch-nav" onclick="toggleTheme()">
                <i class="fas fa-moon" id="theme-icon"></i>
            </div>
        </div>
    </nav>

    <header class="profile-hero">
        <div class="hero-mask"></div>
        <div class="profile-glass-card">
            <div class="avatar-container">
                <img src="<?php echo $user['profile_photo'] ? 'uploads/'.htmlspecialchars($user['profile_photo']) : 'assets/img/default.png'; ?>" alt="User">
            </div>

            <div class="profile-info">
                <h1><?php echo htmlspecialchars($user['name']); ?></h1>
                <p style="color: var(--accent); font-size: 1.4rem; font-weight: 600; margin-top: 10px;">Digital Nomad & Photography Enthusiast</p>

                <div style="display: flex; justify-content: center; gap: 40px; margin: 35px 0;">
                    <div style="text-align: center;"><span style="display: block; font-size: 1.8rem; font-weight: 800;"><?php echo count($posts); ?></span> <span style="color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">Stories</span></div>
                    <div style="text-align: center;"><span style="display: block; font-size: 1.8rem; font-weight: 800;"><?php echo date('Y', strtotime($user['created_at'])); ?></span> <span style="color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">Member</span></div>
                </div>

                <?php if ($isOwner): ?>
                    <div class="action-area">
                        <a href="edit-profile.php" class="btn-ui btn-edit"><i class="fas fa-user-edit"></i> Edit Profile</a>
                        <a href="add-post.php" class="btn-ui btn-add"><i class="fas fa-plus-circle"></i> New Story</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="main-container">
        <div class="grid">
            <?php foreach ($posts as $post): ?>
                <article class="sexy-card">
                    <?php if ($isOwner): ?>
                        <button onclick="if(confirm('Delete permanently?')) window.location.href='delete-post.php?id=<?php echo $post['id']; ?>'" class="delete-btn">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    <?php endif; ?>

                    <img src="uploads/<?php echo $post['image'] ?: 'default.jpg'; ?>" alt="Post Image">
                    <div class="card-info">
                        <h3 style="font-size: 1.6rem; margin-bottom: 15px;"><?php echo htmlspecialchars($post['title']); ?></h3>
                        <p style="color: var(--text-muted); line-height: 1.6; margin-bottom: 20px;"><?php echo htmlspecialchars(substr($post['description'], 0, 110)); ?>...</p>
                        <div style="margin-top: auto; display: flex; justify-content: space-between; align-items: center;">
                            <a href="post.php?slug=<?php echo $post['slug']; ?>" style="color: var(--accent); text-decoration: none; font-weight: 800;">READ STORY →</a>
                            <?php if ($isOwner): ?>
                                <a href="edit-post.php?id=<?php echo $post['id']; ?>" style="color: var(--text-muted);"><i class="fas fa-pen-nib"></i></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </main>

    <script>
        function toggleTheme() {
            const body = document.body;
            const icon = document.getElementById('theme-icon');
            if (body.getAttribute('data-theme') === 'light') {
                body.removeAttribute('data-theme');
                icon.className = 'fas fa-moon';
            } else {
                body.setAttribute('data-theme', 'light');
                icon.className = 'fas fa-sun';
            }
        }
    </script>
</body>
</html>