<?php
include 'db.php';
session_start();

$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : '';
$preferredTheme = (isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'light') ? 'light' : 'dark';
?>

<!DOCTYPE html>
<html lang="en" class="<?php echo $preferredTheme; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'TravelBlog'; ?></title>
    <meta name="description" content="<?php echo $description ?? 'Simple, smooth travel stories.'; ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&family=Instrument+Serif:ital@0;1&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="assets/css/index.css?v=4">
    <link rel="stylesheet" href="assets/css/enhance.css?v=48">
    <link rel="stylesheet" href="assets/css/sexy-theme.css?v=2">
</head>
<body class="index-page <?php echo $preferredTheme === 'dark' ? 'dark' : ''; ?>">

<!-- Heroic Navigation -->
<nav class="navbar">
    <a href="index.php" class="logo">
        <i class="fas fa-globe"></i>
        <span>Travel</span>Blog
    </a>

    <div class="nav-links" id="navLinks">
        <a href="index.php" class="nav-link">Home</a>
        <a href="index.php#categories" class="nav-link">Categories</a>
        <?php if ($isLoggedIn): ?>
            <a href="add-post.php" class="nav-link nav-highlight">Write Story</a>
            <a href="edit-profile.php" class="nav-link">Profile</a>
            <a href="logout.php" class="nav-link">Logout</a>
        <?php else: ?>
            <a href="login.php" class="nav-link nav-login">Login</a>
            <a href="signup.php" class="nav-link nav-signup">Join Free</a>
        <?php endif; ?>
    </div>

    <button class="theme-btn" id="themeBtn" type="button" aria-label="Toggle theme">
        <i class="fas fa-<?php echo $preferredTheme === 'dark' ? 'sun' : 'moon'; ?>"></i>
    </button>

    <button class="menu-toggle" id="mobile-menu" type="button" aria-label="Open menu">
        <i class="fas fa-bars"></i>
    </button>
</nav>

<div class="nav-scrim"></div>