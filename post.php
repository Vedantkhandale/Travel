<?php
include 'includes/db.php';
session_start();

if (!isset($_GET['slug'])) {
    header("Location: index.php");
    exit();
}

$slug = mysqli_real_escape_string($conn, $_GET['slug']);
$result = mysqli_query($conn, "SELECT * FROM posts WHERE slug='$slug'");
$post = mysqli_fetch_assoc($result);

if (!$post) {
    die("Post not found!");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $post['title']; ?> | TravelBlog</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #6366f1;
            --text: #0f172a;
            --muted: #64748b;
            --glass: rgba(255, 255, 255, 0.9);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: #f1f5f9; 
            color: var(--text); 
            line-height: 1.8; 
            overflow-x: hidden;
        }

        /* Simple Nav */
        .nav {
            background: var(--glass); backdrop-filter: blur(10px);
            padding: 15px 5%; display: flex; justify-content: space-between; align-items: center;
            position: fixed; top: 0; width: 100%; z-index: 1000; border-bottom: 1px solid rgba(0,0,0,0.1);
        }
        .nav .logo { color: var(--text); font-size: 1.5rem; font-weight: 800; text-decoration: none; }
        .nav .logo span { color: var(--primary); }
        .nav a { color: var(--text); text-decoration: none; font-weight: 600; padding: 8px 16px; border-radius: 8px; transition: 0.3s; }
        .nav a:hover { background: rgba(99,102,241,0.1); color: var(--primary); }

        /* Hero Image Section */
        .post-hero {
            width: 100%; 
            height: 60vh; /* Responsive height */
            min-height: 350px;
            position: relative;
            background: #000;
        }
        .post-hero img { 
            width: 100%; height: 100%; 
            object-fit: cover; 
            opacity: 0.7;
        }
        .post-hero::after {
            content: ''; position: absolute; bottom: 0; left: 0; width: 100%;
            height: 70%; background: linear-gradient(to top, rgba(0,0,0,0.9), transparent);
        }

        .post-header-content {
            position: absolute; bottom: 100px; left: 50%;
            transform: translateX(-50%);
            width: 90%; max-width: 900px;
            color: white; z-index: 10;
            text-align: center;
        }
        .post-header-content h1 { 
            font-size: clamp(1.8rem, 5vw, 3.5rem); 
            font-weight: 800; 
            line-height: 1.2; 
            letter-spacing: -1px;
            margin-top: 15px;
            text-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }

        /* Floating Content Card */
        .content-container {
            max-width: 850px; 
            margin: -60px auto 60px; /* Overlap effect */
            background: #ffffff;
            padding: 40px 25px; /* Mobile-friendly padding */
            border-radius: 25px; 
            position: relative;
            box-shadow: 0 30px 60px -12px rgba(0,0,0,0.1);
            z-index: 20;
        }
        
        /* Desktop specific card style */
        @media (min-width: 768px) {
            .content-container {
                margin: -100px auto 100px;
                padding: 60px;
                border-radius: 35px;
                background: var(--glass);
                backdrop-filter: blur(15px);
                border: 1px solid rgba(255,255,255,0.4);
            }
        }
        
        .meta-info {
            display: flex; gap: 15px; margin-bottom: 30px;
            padding-bottom: 20px; border-bottom: 1px solid #f1f5f9;
            color: var(--muted); font-weight: 600; font-size: 0.85rem;
            flex-wrap: wrap; justify-content: center;
        }
        .meta-info i { color: var(--primary); }

        .post-text { font-size: 1.1rem; color: #334155; text-align: left; }
        .post-text p { margin-bottom: 20px; }

        /* Smooth Floating Back Button */
        .back-btn {
            position: fixed; top: 20px; left: 20px;
            background: white; width: 45px; height: 45px;
            border-radius: 12px; display: flex; align-items: center; justify-content: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            text-decoration: none; color: var(--text); z-index: 100;
            transition: 0.3s ease;
        }
        .back-btn:hover { background: var(--primary); color: white; transform: scale(1.05); }

        /* Footer & Buttons */
        .post-footer {
            margin-top: 40px; padding-top: 30px;
            border-top: 1px solid #f1f5f9;
            display: flex; justify-content: space-between; align-items: center;
            flex-wrap: wrap; gap: 20px;
        }

        .share-btns { font-size: 1.2rem; }
        .share-btns i { margin: 0 10px; cursor: pointer; transition: 0.3s; }
        .share-btns i:hover { color: var(--primary); }

        @media (max-width: 600px) {
            .post-footer { flex-direction: column; text-align: center; }
            .post-header-content { bottom: 80px; }
            .back-btn { top: 15px; left: 15px; width: 38px; height: 38px; }
        }

        /* Print Button Responsive */
        .btn-print {
            border: 2px solid var(--primary); 
            background: transparent; 
            color: var(--primary); 
            padding: 10px 20px; 
            border-radius: 12px; 
            font-weight: 700; 
            cursor: pointer; 
            transition: 0.3s;
            width: 100%; /* Mobile par full width */
        }
        @media (min-width: 600px) { .btn-print { width: auto; } }
        .btn-print:hover { background: var(--primary); color: white; }

    </style>
</head>
<body>

    <nav class="nav">
        <a href="index.php" class="logo"><i class="fas fa-map-marked-alt"></i> Travel<span>Blog</span></a>
        <div>
            <a href="index.php">Feed</a>
            <a href="add-post.php">New Story</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <a href="index.php" class="back-btn"><i class="fas fa-chevron-left"></i></a>

    <header class="post-hero">
        <img src="uploads/<?php echo $post['image']; ?>" alt="Cover Image">
        <div class="post-header-content">
            <span style="background: var(--primary); padding: 5px 14px; border-radius: 50px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">
                Adventure
            </span>
            <h1><?php echo $post['title']; ?></h1>
        </div>
    </header>

    <main class="content-container">
        <div class="meta-info">
            <span><i class="far fa-calendar-alt"></i> March 2026</span>
            <span><i class="far fa-user"></i> By Admin</span>
            <span><i class="far fa-clock"></i> 5 Min Read</span>
        </div>

        <article class="post-text">
            <?php echo nl2br($post['description']); ?>
        </article>

        <footer class="post-footer">
            <div class="share-btns">
                <span style="font-weight: 800; font-size: 0.9rem; display: block; margin-bottom: 10px;">Share Story:</span>
                <i class="fab fa-twitter" style="color: #1DA1F2;" onclick="shareOnTwitter()" title="Share on Twitter"></i>
                <i class="fab fa-facebook" style="color: #4267B2;" onclick="shareOnFacebook()" title="Share on Facebook"></i>
                <i class="fab fa-whatsapp" style="color: #25D366;" onclick="shareOnWhatsApp()" title="Share on WhatsApp"></i>
                <i class="fab fa-instagram" style="color: #E4405F;" onclick="shareOnInstagram()" title="Share on Instagram"></i>
                <i class="fas fa-link" onclick="copyLink()" title="Copy Link"></i>
            </div>
            <button class="btn-print" onclick="window.print()">
                <i class="fas fa-print"></i> Save PDF
            </button>
        </footer>
    </main>

    <script>
        const postTitle = "<?php echo addslashes($post['title']); ?>";
        const postUrl = window.location.href;

        function shareOnTwitter() {
            const url = `https://twitter.com/intent/tweet?text=${encodeURIComponent(postTitle)}&url=${encodeURIComponent(postUrl)}`;
            window.open(url, '_blank', 'width=600,height=400');
        }

        function shareOnFacebook() {
            const url = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(postUrl)}`;
            window.open(url, '_blank', 'width=600,height=400');
        }

        function shareOnWhatsApp() {
            const url = `https://wa.me/?text=${encodeURIComponent(postTitle + ' ' + postUrl)}`;
            window.open(url, '_blank');
        }

        function shareOnInstagram() {
            // Instagram doesn't support direct sharing, so copy to clipboard
            copyLink();
            alert('Link copied! Share it on Instagram manually.');
        }

        function copyLink() {
            navigator.clipboard.writeText(postUrl).then(() => {
                // Show temporary feedback
                const linkIcon = document.querySelector('.fa-link');
                const originalColor = linkIcon.style.color;
                linkIcon.style.color = '#10b981';
                setTimeout(() => linkIcon.style.color = originalColor, 1000);
            });
        }
    </script>

</body>
</html>