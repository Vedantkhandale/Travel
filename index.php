<?php
include 'includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><a href="post.php?slug=<?php echo $row['slug']; ?>" style="text-decoration:none; color:inherit;">
            <h3><?php echo $row['title']; ?></h3>
        </a></title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #6366f1;
            --secondary: #a855f7;
            --bg-light: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --nav-bg: rgba(255, 255, 255, 0.8);
            --border: rgba(0, 0, 0, 0.06);
            --transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
        }

        body.dark {
            --bg-light: #020617;
            --card-bg: #0f172a;
            --text-main: #f1f5f9;
            --text-muted: #94a3b8;
            --nav-bg: rgba(2, 6, 23, 0.85);
            --border: rgba(255, 255, 255, 0.08);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg-light);
            color: var(--text-main);
            margin: 0;
            line-height: 1.6;
            transition: background 0.3s ease;
            overflow-x: hidden;
        }

        /* --- Enhanced Navbar --- */
        .navbar {
            background: var(--nav-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 1rem 8%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid var(--border);
            transition: var(--transition);
        }

        .navbar.scrolled {
            padding: 0.7rem 8%;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .logo {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--text-main);
            text-decoration: none;
            letter-spacing: -1.5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logo span {
            color: var(--primary);
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .nav-links a {
            color: var(--text-main);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            position: relative;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary);
            transition: var(--transition);
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .btn-post {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white !important;
            padding: 10px 22px;
            border-radius: 14px;
            font-size: 0.85rem;
            box-shadow: 0 8px 15px rgba(99, 102, 241, 0.3);
            border: none;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-post:hover {
            transform: scale(1.05) translateY(-2px);
            box-shadow: 0 12px 20px rgba(99, 102, 241, 0.4);
        }

        /* --- Ultra Heroic Section --- */
        .hero {
            padding: 130px 20px 80px;
            text-align: center;
            position: relative;
            background: radial-gradient(circle at top, rgba(99, 102, 241, 0.08) 0%, transparent 60%);
            overflow: hidden;
        }

        .hero h1 {
            font-size: clamp(3rem, 8vw, 4.8rem);
            font-weight: 900;
            margin-bottom: 25px;
            letter-spacing: -3px;
            line-height: 1.05;
            background: linear-gradient(to right, #6366f1, #a855f7, #6366f1);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: gradientShift 4s linear infinite, fadeInUp 0.8s ease both;
        }

        @keyframes gradientShift {
            to {
                background-position: 200% center;
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero p {
            color: var(--text-muted);
            font-size: 1.25rem;
            max-width: 700px;
            margin: 0 auto 55px;
            font-weight: 500;
            animation: fadeInUp 0.8s ease 0.2s both;
        }

        .search-box {
            max-width: 650px;
            margin: 0 auto;
            position: relative;
            z-index: 10;
            animation: fadeInUp 0.8s ease 0.4s both;
        }

        .search-box input {
            width: 100%;
            padding: 24px 35px 24px 80px;
            border-radius: 24px;
            border: 1px solid var(--border);
            background: var(--card-bg);
            color: var(--text-main);
            font-size: 1.1rem;
            backdrop-filter: blur(10px);
            box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.12);
            transition: var(--transition);
        }

        .search-box i {
            position: absolute;
            left: 32px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
            font-size: 1.5rem;
        }

        .search-box input:focus {
            border-color: var(--primary);
            transform: scale(1.02) translateY(-5px);
            box-shadow: 0 40px 70px -15px rgba(99, 102, 241, 0.2);
        }

        /* --- Premium Slider --- */
        .slider-section {
            max-width: 1200px;
            margin: 0 auto 80px;
            padding: 0 20px;
            position: relative;
            z-index: 5;
        }

        .slider-wrapper {
            height: 550px;
            border-radius: 40px;
            overflow: hidden;
            box-shadow: 0 40px 80px -20px rgba(0, 0, 0, 0.25);
            position: relative;
        }

        .slides {
            display: flex;
            height: 100%;
            animation: autoSlide 20s infinite ease-in-out;
        }

        .slides img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            flex-shrink: 0;
            filter: brightness(0.9);
        }

        @keyframes autoSlide {

            0%,
            28% {
                transform: translateX(0);
            }

            33%,
            61% {
                transform: translateX(-100%);
            }

            66%,
            94% {
                transform: translateX(-200%);
            }

            100% {
                transform: translateX(0);
            }
        }

        /* --- Advanced Grid & Cards --- */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px 100px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .section-header h2 {
            font-size: 2rem;
            font-weight: 800;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 40px;
        }

        .card {
            background: var(--card-bg);
            border-radius: 32px;
            border: 1px solid var(--border);
            overflow: hidden;
            transition: var(--transition);
            position: relative;
        }

        .card:hover {
            transform: translateY(-15px);
            box-shadow: 0 40px 60px -15px rgba(0, 0, 0, 0.12);
            border-color: var(--primary);
        }

        .card-img {
            height: 260px;
            overflow: hidden;
            position: relative;
        }

        .card-img::after {
            content: 'NEW';
            position: absolute;
            top: 20px;
            right: 20px;
            background: var(--primary);
            color: white;
            padding: 5px 12px;
            border-radius: 10px;
            font-size: 0.7rem;
            font-weight: 800;
            z-index: 10;
        }

        .card-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: 1.5s cubic-bezier(0.23, 1, 0.32, 1);
        }

        .card:hover .card-img img {
            transform: scale(1.1) rotate(1deg);
        }

        .card-body {
            padding: 35px;
        }

        .card-body h3 {
            font-size: 1.5rem;
            margin-bottom: 12px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .card-body p {
            color: var(--text-muted);
            font-size: 1rem;
            line-height: 1.7;
            opacity: 0.9;
        }

        .card-footer {
            padding: 25px 35px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(0, 0, 0, 0.01);
        }

        .stats {
            display: flex;
            gap: 20px;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-muted);
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .stat-item i {
            font-size: 1.1rem;
        }

        .stat-item:hover {
            color: var(--primary);
            transform: translateY(-2px);
        }

        .stat-item.active {
            color: #f43f5e !important;
        }

        /* --- Theme Button --- */
        .theme-btn {
            width: 45px;
            height: 45px;
            border-radius: 14px;
            background: var(--border);
            border: none;
            cursor: pointer;
            color: var(--text-main);
            font-size: 1.1rem;
            transition: 0.3s;
        }

        .theme-btn:hover {
            background: var(--primary);
            color: white;
            transform: rotate(15deg);
        }

        /* --- Footer --- */
        .main-footer {
            background: var(--card-bg);
            padding: 100px 8% 50px;
            border-top: 1px solid var(--border);
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 60px;
        }

        .footer-logo {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 25px;
            display: block;
        }

        .social-icons {
            display: flex;
            gap: 18px;
            margin-top: 30px;
        }

        .social-btn {
            width: 45px;
            height: 45px;
            border-radius: 14px;
            background: var(--bg-light);
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: 0.4s;
            border: 1px solid var(--border);
        }

        .social-btn:hover {
            background: var(--primary);
            color: white;
            transform: rotate(10deg) translateY(-5px);
        }

        @media (max-width: 768px) {
            .footer-grid {
                grid-template-columns: 1fr;
            }

            .navbar {
                padding: 1rem 5%;
            }

            .hero h1 {
                font-size: 2.8rem;
            }
        }
    </style>
</head>

<body class="<?php echo (isset($_COOKIE['theme']) && $_COOKIE['theme'] == 'dark') ? 'dark' : ''; ?>">

    <nav class="navbar" id="mainNav">
        <a href="index.php" class="logo"><i class="fas fa-map-marked-alt"></i> Travel<span>Blog</span></a>
        <div class="nav-links">
            <a href="index.php">Feed</a>
            <a href="add-post.php" class="btn-post">New Story</a>
            <a href="logout.php">Logout</a>
            <button class="theme-btn" onclick="toggleTheme()" id="themeBtn">
                <i class="fas fa-moon"></i>
            </button>
        </div>
    </nav>

    <header class="hero">
        <div style="display:inline-block; padding: 6px 16px; border-radius: 100px; background: rgba(99, 102, 241, 0.1); color: var(--primary); font-size: 0.75rem; font-weight: 800; margin-bottom: 25px; text-transform: uppercase; letter-spacing: 2px;">
            🌍 Explore the Unexplored
        </div>
        <h1>The World Is Yours <br>To Discover.</h1>
        <p>Document your memories, share hidden gems, and get inspired by a global community of modern-day explorers.</p>

        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Find your next destination...">
        </div>
    </header>

    <section class="slider-section">
        <div class="slider-wrapper">
            <div class="slides">
                <img src="images/1 .jpg" alt="Nature">
                <img src="images/1 .jpg" alt="City">
                <img src="images/1 .jpg" alt="Beach">
            </div>
        </div>
    </section>

    <main class="container">
        <div class="section-header">
            <h2>Trending Stories</h2>
            <div style="color: var(--primary); font-weight: 700; cursor: pointer;">View All <i class="fas fa-arrow-right"></i></div>
        </div>

        <div class="grid">
            <?php
            $result = mysqli_query($conn, "SELECT * FROM posts ORDER BY id DESC");
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
            ?>
                    <article class="card" data-title="<?php echo strtolower($row['title']); ?>">
                        <div class="card-img">
                            <img src="uploads/<?php echo $row['image']; ?>" alt="Blog">
                        </div>
                        <div class="card-body">
                            <a href="<?php echo $row['slug']; ?>" style="text-decoration:none; color:inherit;">
                                <h3><a href="post.php?slug=<?php echo $row['slug']; ?>" style="text-decoration:none; color:inherit;">
                                        <h3><?php echo $row['title']; ?></h3>
                                    </a></h3>
                            </a>
                            <p><?php echo substr(strip_tags($row['description']), 0, 110); ?>...</p>
                        </div>
                        <div class="card-footer">
                            <div class="stats">
                                <div class="stat-item" onclick="toggleLike(this)">
                                    <i class="far fa-heart"></i> <b class="count">1.2k</b>
                                </div>
                                <div class="stat-item">
                                    <i class="far fa-comment"></i> <b>45</b>
                                </div>
                            </div>
                            <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 700;">
                                <i class="far fa-calendar-alt"></i> 2026
                            </span>
                        </div>
                    </article>
            <?php }
            } ?>
        </div>
    </main>

    <footer class="main-footer">
        <div class="footer-grid">
            <div>
                <a href="#" class="logo footer-logo">Travel<span>Blog</span></a>
                <p style="color: var(--text-muted); font-size: 1rem; line-height: 1.8;">Capturing the essence of travel, one story at a time. Join thousands of explorers worldwide.</p>
                <div class="social-icons">
                    <a href="#" class="social-btn"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-btn"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-btn"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            <div style="padding-left: 20px;">
                <h4 style="margin-bottom: 25px;">Company</h4>
                <ul style="list-style: none; padding: 0; line-height: 2.5;">
                    <li><a href="#" style="text-decoration: none; color: var(--text-muted);">Our Story</a></li>
                    <li><a href="#" style="text-decoration: none; color: var(--text-muted);">Careers</a></li>
                    <li><a href="#" style="text-decoration: none; color: var(--text-muted);">Privacy</a></li>
                </ul>
            </div>
            <div>
                <h4 style="margin-bottom: 25px;">Newsletter</h4>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 20px;">Get the best travel tips weekly.</p>
                <input type="email" placeholder="Email address" style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid var(--border); background: var(--bg-light); color: var(--text-main);">
            </div>
        </div>
        <div style="text-align: center; margin-top: 80px; padding-top: 30px; border-top: 1px solid var(--border); color: var(--text-muted); font-size: 0.9rem;">
            &copy; 2026 TravelBlog. Crafted for explorers by <b>Vedant</b>.
        </div>
    </footer>

    <script>
        // Smooth Scroll Navbar
        window.onscroll = () => {
            const nav = document.getElementById('mainNav');
            if (window.scrollY > 50) nav.classList.add('scrolled');
            else nav.classList.remove('scrolled');
        };

        function toggleTheme() {
            const body = document.body;
            body.classList.toggle("dark");
            const isDark = body.classList.contains("dark");
            document.querySelector("#themeBtn i").className = isDark ? "fas fa-sun" : "fas fa-moon";
            document.cookie = "theme=" + (isDark ? "dark" : "light") + ";path=/";
        }

        function toggleLike(el) {
            el.classList.toggle("active");
            const icon = el.querySelector("i");
            if (el.classList.contains("active")) {
                icon.className = "fas fa-heart";
            } else {
                icon.className = "far fa-heart";
            }
        }

        document.getElementById("searchInput").addEventListener("input", function() {
            const query = this.value.toLowerCase();
            document.querySelectorAll(".card").forEach(card => {
                card.style.display = card.getAttribute("data-title").includes(query) ? "flex" : "none";
            });
        });

        window.onload = () => {
            if (document.body.classList.contains("dark")) {
                document.querySelector("#themeBtn i").className = "fas fa-sun";
            }
        }
    </script>
</body>

</html>