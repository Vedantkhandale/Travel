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
$preferredTheme = (isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'light') ? 'light' : 'dark';
$storyCount = count($posts);
$memberSince = !empty($user['created_at']) ? date('Y', strtotime($user['created_at'])) : date('Y');

function buildAvatarPlaceholder($name) {
    $initial = strtoupper(substr(trim((string) $name), 0, 1));
    if ($initial === '') {
        $initial = 'T';
    }

    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="320" height="320" viewBox="0 0 320 320">'
        . '<defs><linearGradient id="g" x1="0%" y1="0%" x2="100%" y2="100%">'
        . '<stop offset="0%" stop-color="#0f766e"/><stop offset="100%" stop-color="#d97706"/>'
        . '</linearGradient></defs>'
        . '<rect width="320" height="320" rx="48" fill="url(#g)"/>'
        . '<text x="50%" y="54%" dominant-baseline="middle" text-anchor="middle" font-family="Arial, sans-serif" font-size="130" font-weight="700" fill="#ffffff">'
        . htmlspecialchars($initial, ENT_QUOTES, 'UTF-8')
        . '</text></svg>';

    return 'data:image/svg+xml;charset=UTF-8,' . rawurlencode($svg);
}

$profilePhotoSrc = !empty($user['profile_photo'])
    ? 'uploads/' . htmlspecialchars($user['profile_photo'])
    : buildAvatarPlaceholder($user['name'] ?? 'Traveler');

$profileTagline = $isOwner
    ? 'Your stories, profile details, and latest photo-led travel memories.'
    : 'Stories, snapshots, and destination notes collected on the road.';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user['name']); ?> - Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --bg-color: #07131a;
            --bg-accent: #0c1d29;
            --card-bg: rgba(11, 24, 35, 0.82);
            --surface-bg: rgba(8, 15, 24, 0.78);
            --text-main: #f8fafc;
            --text-muted: #9fb0bd;
            --accent: #0f766e;
            --accent-strong: #115e59;
            --warm: #d97706;
            --border: rgba(148, 163, 184, 0.14);
            --shadow: 0 24px 60px rgba(2, 6, 23, 0.34);
        }

        [data-theme="light"] {
            --bg-color: #f4efe4;
            --bg-accent: #edf4f1;
            --card-bg: rgba(255, 255, 255, 0.84);
            --surface-bg: rgba(255, 250, 242, 0.82);
            --text-main: #142033;
            --text-muted: #5b6475;
            --border: rgba(20, 32, 51, 0.08);
            --shadow: 0 22px 56px rgba(20, 32, 51, 0.12);
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            font-family: 'Manrope', sans-serif;
            color: var(--text-main);
            background:
                radial-gradient(circle at top left, rgba(217, 119, 6, 0.12), transparent 28%),
                radial-gradient(circle at top right, rgba(15, 118, 110, 0.1), transparent 32%),
                linear-gradient(180deg, var(--bg-color) 0%, var(--bg-accent) 100%);
            transition: background 0.25s ease, color 0.25s ease;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background:
                linear-gradient(120deg, rgba(255, 255, 255, 0.04), transparent 28%),
                radial-gradient(circle at 24% 18%, rgba(255, 255, 255, 0.06), transparent 22%);
            pointer-events: none;
            z-index: -1;
        }

        .navbar {
            position: sticky;
            top: 14px;
            z-index: 20;
            width: min(1180px, calc(100% - 24px));
            margin: 14px auto 0;
            padding: 14px 18px 14px 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            border-radius: 999px;
            border: 1px solid var(--border);
            background: var(--surface-bg);
            backdrop-filter: blur(22px);
            box-shadow: var(--shadow);
        }

        .navbar .logo {
            color: var(--text-main);
            text-decoration: none;
            font-size: 1.34rem;
            font-weight: 800;
            letter-spacing: -0.04em;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .navbar .logo i {
            color: var(--warm);
        }

        .navbar .logo span {
            font-family: 'Instrument Serif', serif;
            font-size: 1.5rem;
            background: linear-gradient(135deg, var(--accent), var(--warm));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-links {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            flex-wrap: wrap;
            gap: 10px;
        }

        .nav-links a {
            min-height: 42px;
            padding: 0 14px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            color: var(--text-main);
            font-size: 0.9rem;
            font-weight: 700;
            transition: background 0.2s ease, transform 0.2s ease, color 0.2s ease;
        }

        .nav-links a:hover {
            background: rgba(15, 118, 110, 0.1);
            color: var(--accent);
            transform: translateY(-1px);
        }

        .nav-links .nav-cta {
            background: linear-gradient(135deg, var(--accent), var(--accent-strong));
            color: #ffffff;
            box-shadow: 0 16px 32px rgba(15, 118, 110, 0.24);
        }

        .nav-links .nav-cta:hover {
            color: #ffffff;
            background: linear-gradient(135deg, #14827a, var(--accent));
        }

        .theme-switch-nav {
            width: 42px;
            height: 42px;
            border: 1px solid var(--border);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.08);
            color: var(--text-main);
            cursor: pointer;
            transition: transform 0.2s ease, background 0.2s ease;
        }

        .theme-switch-nav:hover {
            transform: translateY(-1px);
            background: rgba(15, 118, 110, 0.12);
        }

        .profile-hero {
            position: relative;
            width: min(1240px, calc(100% - 24px));
            min-height: 74vh;
            margin: 18px auto 0;
            padding: 120px 20px 90px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 44px;
            overflow: hidden;
            background: <?php echo !empty($user['background_image']) ? 'url(uploads/' . htmlspecialchars($user['background_image']) . ') center/cover no-repeat' : 'linear-gradient(135deg, #09203f, #0b3b44 54%, #7c4a03)'; ?>;
            box-shadow: var(--shadow);
        }

        .hero-mask {
            position: absolute;
            inset: 0;
            background:
                linear-gradient(115deg, rgba(4, 18, 34, 0.78), rgba(4, 18, 34, 0.34) 48%, rgba(4, 18, 34, 0.74)),
                linear-gradient(180deg, rgba(4, 18, 34, 0.08) 0%, var(--bg-color) 100%);
            z-index: 1;
        }

        .profile-glass-card {
            position: relative;
            z-index: 2;
            width: min(880px, 100%);
            padding: 42px 34px 36px;
            border-radius: 38px;
            border: 1px solid rgba(255, 255, 255, 0.14);
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(24px);
            text-align: center;
            box-shadow: 0 30px 70px rgba(2, 6, 23, 0.22);
        }

        .avatar-container {
            width: 156px;
            height: 156px;
            margin: 0 auto 24px;
            padding: 6px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), var(--warm));
            box-shadow: 0 18px 46px rgba(15, 118, 110, 0.3);
        }

        .avatar-container img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 5px solid rgba(8, 15, 24, 0.62);
            object-fit: cover;
            display: block;
        }

        [data-theme="light"] .avatar-container img {
            border-color: rgba(255, 255, 255, 0.86);
        }

        .profile-kicker,
        .section-kicker {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            min-height: 34px;
            padding: 0 14px;
            border-radius: 999px;
            border: 1px solid rgba(245, 210, 140, 0.24);
            background: rgba(245, 210, 140, 0.12);
            color: #f5d28c;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .profile-kicker::before,
        .section-kicker::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: currentColor;
            opacity: 0.75;
        }

        [data-theme="light"] .section-kicker {
            border-color: rgba(217, 119, 6, 0.16);
            background: rgba(217, 119, 6, 0.08);
            color: #9a5f12;
        }

        .profile-info h1 {
            margin: 20px 0 0;
            font-family: 'Instrument Serif', serif;
            font-size: clamp(3rem, 7vw, 5.2rem);
            line-height: 0.95;
            letter-spacing: -0.05em;
            color: #f8fafc;
        }

        [data-theme="light"] .profile-info h1 {
            color: #142033;
        }

        .profile-subtitle {
            max-width: 620px;
            margin: 16px auto 0;
            color: rgba(232, 240, 246, 0.82);
            font-size: 1.05rem;
            line-height: 1.8;
            font-weight: 500;
        }

        [data-theme="light"] .profile-subtitle {
            color: #5b6475;
        }

        .profile-highlights {
            margin: 28px auto 0;
            display: flex;
            justify-content: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .profile-stat {
            min-width: 150px;
            padding: 16px 18px;
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(255, 255, 255, 0.08);
        }

        [data-theme="light"] .profile-stat {
            border-color: rgba(20, 32, 51, 0.08);
            background: rgba(255, 255, 255, 0.68);
        }

        .profile-stat strong {
            display: block;
            font-size: 1.9rem;
            line-height: 1;
            letter-spacing: -0.04em;
        }

        .profile-stat span {
            display: block;
            margin-top: 10px;
            color: var(--text-muted);
            font-size: 0.78rem;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .action-area {
            margin-top: 26px;
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn-ui {
            min-height: 48px;
            padding: 0 20px;
            border-radius: 18px;
            border: 1px solid transparent;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 0.92rem;
            font-weight: 800;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .btn-ui:hover {
            transform: translateY(-2px);
        }

        .btn-add {
            background: linear-gradient(135deg, var(--accent), var(--accent-strong));
            color: #ffffff;
            box-shadow: 0 16px 34px rgba(15, 118, 110, 0.24);
        }

        .btn-edit {
            border-color: var(--border);
            background: rgba(255, 255, 255, 0.08);
            color: var(--text-main);
        }

        .main-container {
            width: min(1180px, calc(100% - 24px));
            margin: -66px auto 0;
            padding: 0 0 60px;
            position: relative;
            z-index: 3;
        }

        .section-head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 22px;
        }

        .section-head h2 {
            margin: 14px 0 0;
            font-family: 'Instrument Serif', serif;
            font-size: clamp(2.2rem, 4vw, 3rem);
            line-height: 0.98;
            letter-spacing: -0.04em;
        }

        .section-head p {
            margin: 10px 0 0;
            color: var(--text-muted);
            line-height: 1.75;
        }

        .story-total {
            display: inline-flex;
            align-items: center;
            min-height: 40px;
            padding: 0 14px;
            border-radius: 999px;
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.08);
            color: var(--text-main);
            font-size: 0.82rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
        }

        .sexy-card {
            position: relative;
            overflow: hidden;
            border-radius: 32px;
            border: 1px solid var(--border);
            background: var(--card-bg);
            box-shadow: var(--shadow);
            transition: transform 0.24s ease, box-shadow 0.24s ease, border-color 0.24s ease;
        }

        .sexy-card:hover {
            transform: translateY(-8px);
            border-color: rgba(15, 118, 110, 0.18);
            box-shadow: 0 28px 54px rgba(2, 6, 23, 0.26);
        }

        .card-visual {
            position: relative;
            height: 270px;
            overflow: hidden;
        }

        .card-visual::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, transparent 42%, rgba(4, 18, 34, 0.58) 100%);
        }

        .card-visual img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .delete-btn {
            position: absolute;
            top: 16px;
            right: 16px;
            z-index: 2;
            width: 42px;
            height: 42px;
            border: 0;
            border-radius: 14px;
            background: rgba(239, 68, 68, 0.9);
            color: #ffffff;
            cursor: pointer;
            box-shadow: 0 14px 28px rgba(127, 29, 29, 0.28);
        }

        .card-info {
            padding: 22px 22px 20px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .card-meta {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 34px;
            width: fit-content;
            padding: 0 14px;
            border-radius: 999px;
            background: rgba(15, 118, 110, 0.08);
            color: var(--accent);
            font-size: 0.76rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .card-info h3 {
            margin: 0;
            font-size: 1.42rem;
            line-height: 1.2;
            letter-spacing: -0.04em;
        }

        .card-info p {
            margin: 0;
            color: var(--text-muted);
            line-height: 1.72;
        }

        .card-actions {
            margin-top: auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
        }

        .story-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--accent);
            text-decoration: none;
            font-weight: 800;
        }

        .story-link:hover {
            color: var(--warm);
        }

        .icon-link {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            border: 1px solid var(--border);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            text-decoration: none;
        }

        .empty-profile {
            padding: 54px 24px;
            border-radius: 32px;
            border: 1px solid var(--border);
            background: var(--card-bg);
            box-shadow: var(--shadow);
            text-align: center;
        }

        .empty-profile h3 {
            margin: 18px 0 0;
            font-size: 2rem;
            line-height: 1.05;
            letter-spacing: -0.04em;
        }

        .empty-profile p {
            max-width: 520px;
            margin: 14px auto 0;
            color: var(--text-muted);
            line-height: 1.75;
        }

        @media (max-width: 992px) {
            .navbar,
            .profile-hero,
            .main-container {
                width: calc(100% - 20px);
            }

            .navbar {
                border-radius: 28px;
                align-items: flex-start;
            }

            .nav-links {
                justify-content: flex-start;
            }

            .profile-hero {
                min-height: auto;
                padding: 108px 18px 80px;
            }

            .profile-glass-card {
                padding: 34px 22px 28px;
                border-radius: 30px;
            }

            .main-container {
                margin-top: -48px;
            }

            .section-head {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        @media (max-width: 680px) {
            .navbar {
                width: calc(100% - 16px);
                padding: 14px;
            }

            .nav-links {
                width: 100%;
            }

            .nav-links a {
                min-height: 40px;
            }

            .profile-hero {
                width: calc(100% - 16px);
                margin-top: 14px;
                border-radius: 28px;
                padding: 100px 14px 70px;
            }

            .avatar-container {
                width: 120px;
                height: 120px;
            }

            .profile-subtitle {
                font-size: 0.96rem;
            }

            .profile-highlights,
            .action-area,
            .card-actions {
                flex-direction: column;
            }

            .profile-stat,
            .btn-ui,
            .story-total,
            .card-actions > * {
                width: 100%;
                justify-content: center;
            }

            .main-container {
                width: calc(100% - 16px);
                margin-top: -34px;
                padding-bottom: 42px;
            }

            .card-actions {
                align-items: stretch;
            }
        }

        /* Reference-inspired smooth pass */
        body.profile-page {
            --accent: #6366f1;
            --accent-strong: #8b5cf6;
            --warm: #38bdf8;
            background:
                radial-gradient(circle at top left, rgba(99, 102, 241, 0.14), transparent 28%),
                radial-gradient(circle at top right, rgba(56, 189, 248, 0.1), transparent 34%),
                linear-gradient(180deg, var(--bg-color) 0%, var(--bg-accent) 100%);
        }

        [data-theme="light"].profile-page {
            background:
                radial-gradient(circle at top left, rgba(99, 102, 241, 0.12), transparent 28%),
                radial-gradient(circle at top right, rgba(56, 189, 248, 0.08), transparent 34%),
                linear-gradient(180deg, #eef4ff 0%, #f8fbff 44%, #eff2ff 100%);
        }

        .navbar {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(129, 140, 248, 0.14);
        }

        [data-theme="light"] .navbar {
            background: rgba(255, 255, 255, 0.72);
            border-color: rgba(99, 102, 241, 0.1);
        }

        .nav-links a:hover,
        .theme-switch-nav:hover {
            background: rgba(99, 102, 241, 0.1);
            color: #6366f1;
        }

        .nav-links .nav-cta,
        .btn-add {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            box-shadow: 0 16px 34px rgba(99, 102, 241, 0.24);
        }

        .profile-hero {
            box-shadow: 0 28px 64px rgba(2, 6, 23, 0.28);
        }

        .hero-mask {
            background:
                linear-gradient(115deg, rgba(15, 23, 42, 0.84), rgba(15, 23, 42, 0.42) 48%, rgba(15, 23, 42, 0.78)),
                linear-gradient(180deg, rgba(15, 23, 42, 0.08) 0%, var(--bg-color) 100%);
        }

        .profile-glass-card {
            background: rgba(15, 23, 42, 0.26);
            border-color: rgba(255, 255, 255, 0.12);
            box-shadow: 0 30px 70px rgba(2, 6, 23, 0.22);
        }

        [data-theme="light"] .profile-glass-card {
            background: rgba(255, 255, 255, 0.24);
            border-color: rgba(255, 255, 255, 0.5);
        }

        .profile-kicker,
        .section-kicker,
        .card-meta,
        .story-total {
            border-color: rgba(129, 140, 248, 0.16);
            background: rgba(99, 102, 241, 0.08);
            color: #c7d2fe;
        }

        [data-theme="light"] .profile-kicker,
        [data-theme="light"] .section-kicker,
        [data-theme="light"] .card-meta,
        [data-theme="light"] .story-total {
            color: #4f46e5;
        }

        .profile-stat,
        .sexy-card,
        .empty-profile {
            border-color: rgba(129, 140, 248, 0.14);
            box-shadow: 0 20px 46px rgba(2, 6, 23, 0.2);
        }

        [data-theme="light"] .profile-stat,
        [data-theme="light"] .sexy-card,
        [data-theme="light"] .empty-profile {
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.1);
        }

        .sexy-card:hover {
            transform: translateY(-7px);
            border-color: rgba(129, 140, 248, 0.18);
            box-shadow: 0 26px 52px rgba(2, 6, 23, 0.24);
        }

        .story-link,
        .icon-link:hover {
            color: #6366f1;
        }

        .card-visual::after {
            background: linear-gradient(180deg, transparent 40%, rgba(15, 23, 42, 0.54) 100%);
        }
    </style>
</head>

<body class="profile-page" data-theme="<?php echo $preferredTheme; ?>">

    <nav class="navbar">
        <a href="index.php" class="logo">
            <i class="fas fa-globe"></i>
            <span>Travel</span>Blog
        </a>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <?php if ($isOwner): ?>
                <a href="edit-profile.php">Edit Profile</a>
                <a href="add-post.php" class="nav-cta">New Story</a>
            <?php endif; ?>
            <?php if ($isLoggedIn): ?>
                <a href="logout.php">Logout</a>
            <?php endif; ?>
            <button class="theme-switch-nav" type="button" onclick="toggleTheme()" aria-label="Toggle theme">
                <i class="fas fa-moon" id="theme-icon"></i>
            </button>
        </div>
    </nav>

    <header class="profile-hero">
        <div class="hero-mask"></div>
        <div class="profile-glass-card">
            <div class="avatar-container">
                <img src="<?php echo $profilePhotoSrc; ?>" alt="<?php echo htmlspecialchars($user['name']); ?>">
            </div>

            <div class="profile-info">
                <span class="profile-kicker">Traveler Profile</span>
                <h1><?php echo htmlspecialchars($user['name']); ?></h1>
                <p class="profile-subtitle"><?php echo htmlspecialchars($profileTagline); ?></p>

                <div class="profile-highlights">
                    <div class="profile-stat">
                        <strong><?php echo $storyCount; ?></strong>
                        <span>Stories</span>
                    </div>
                    <div class="profile-stat">
                        <strong><?php echo $memberSince; ?></strong>
                        <span>Member Since</span>
                    </div>
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
        <div class="section-head">
            <div>
                <span class="section-kicker">Latest From This Profile</span>
                <h2>Recent Stories</h2>
                <p>Fresh travel notes, visual memories, and places this explorer chose to publish.</p>
            </div>
            <span class="story-total"><?php echo $storyCount; ?> published</span>
        </div>

        <?php if (empty($posts)): ?>
            <div class="empty-profile">
                <span class="section-kicker">Nothing published yet</span>
                <h3>No travel stories here yet</h3>
                <p>This profile is ready, but the stories have not landed yet. The next trip might start the whole archive.</p>
                <?php if ($isOwner): ?>
                    <div class="action-area">
                        <a href="add-post.php" class="btn-ui btn-add"><i class="fas fa-plus-circle"></i> Publish First Story</a>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
        <div class="grid">
            <?php foreach ($posts as $post): ?>
                <article class="sexy-card">
                    <?php if ($isOwner): ?>
                        <button onclick="if(confirm('Delete permanently?')) window.location.href='delete-post.php?id=<?php echo $post['id']; ?>'" class="delete-btn" type="button">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    <?php endif; ?>

                    <div class="card-visual">
                        <img src="uploads/<?php echo htmlspecialchars($post['image'] ?: 'bg_1_1775321727.jpg'); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" loading="lazy" decoding="async">
                    </div>
                    <div class="card-info">
                        <span class="card-meta"><i class="far fa-calendar"></i><?php echo !empty($post['created_at']) ? date('M d, Y', strtotime($post['created_at'])) : 'Recent'; ?></span>
                        <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                        <p><?php echo htmlspecialchars(substr($post['description'], 0, 110)); ?>...</p>
                        <div class="card-actions">
                            <a href="post.php?slug=<?php echo htmlspecialchars($post['slug']); ?>" class="story-link">Read Story <i class="fas fa-arrow-right"></i></a>
                            <?php if ($isOwner): ?>
                                <a href="edit-post.php?id=<?php echo $post['id']; ?>" class="icon-link" aria-label="Edit story"><i class="fas fa-pen-nib"></i></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </main>

    <script>
        function syncThemeIcon() {
            const icon = document.getElementById('theme-icon');
            if (!icon) return;

            const isLight = document.body.getAttribute('data-theme') === 'light';
            icon.className = isLight ? 'fas fa-sun' : 'fas fa-moon';
        }

        function applySavedTheme() {
            const storedTheme = localStorage.getItem('theme') || '<?php echo $preferredTheme; ?>';
            document.body.setAttribute('data-theme', storedTheme === 'light' ? 'light' : 'dark');
            syncThemeIcon();
        }

        function toggleTheme() {
            const body = document.body;
            const nextTheme = body.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
            body.setAttribute('data-theme', nextTheme);
            localStorage.setItem('theme', nextTheme);
            document.cookie = 'theme=' + nextTheme + ';path=/';
            syncThemeIcon();
        }

        document.addEventListener('DOMContentLoaded', () => {
            applySavedTheme();
        });
    </script>
</body>
</html>
