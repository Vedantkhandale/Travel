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
            --glass: rgba(255, 255, 255, 0.85);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: #f1f5f9; 
            color: var(--text); 
            line-height: 1.8; 
        }

        /* Hero Image Section */
        .post-hero {
            width: 100%; height: 70vh;
            position: relative;
            background: #000;
        }
        .post-hero img { 
            width: 100%; height: 100%; 
            object-fit: cover; 
            opacity: 0.8;
        }
        .post-hero::after {
            content: ''; position: absolute; bottom: 0; left: 0; width: 100%;
            height: 60%; background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
        }

        .post-header-content {
            position: absolute; bottom: 100px; left: 50%;
            transform: translateX(-50%);
            width: 90%; max-width: 900px;
            color: white; z-index: 10;
        }
        .post-header-content h1 { 
            font-size: clamp(2.2rem, 5vw, 4rem); 
            font-weight: 800; 
            line-height: 1.1; 
            letter-spacing: -2px;
            margin-top: 15px;
        }

        /* Floating Content Card */
        .content-container {
            max-width: 850px; 
            margin: -80px auto 100px;
            background: var(--glass);
            backdrop-filter: blur(15px);
            padding: 60px;
            border-radius: 35px; 
            position: relative;
            box-shadow: 0 40px 100px -20px rgba(0,0,0,0.15);
            border: 1px solid rgba(255,255,255,0.4);
        }
        
        .meta-info {
            display: flex; gap: 25px; margin-bottom: 40px;
            padding-bottom: 25px; border-bottom: 1px solid rgba(0,0,0,0.05);
            color: var(--muted); font-weight: 600; font-size: 0.9rem;
            flex-wrap: wrap;
        }
        .meta-info i { color: var(--primary); margin-right: 5px; }

        .post-text { font-size: 1.2rem; color: #334155; }
        .post-text p { margin-bottom: 25px; }

        /* Smooth Floating Back Button */
        .back-btn {
            position: fixed; top: 30px; left: 30px;
            background: white; width: 50px; height: 50px;
            border-radius: 15px; display: flex; align-items: center; justify-content: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            text-decoration: none; color: var(--text); z-index: 100;
            transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .back-btn:hover { transform: scale(1.1) rotate(-5deg); background: var(--primary); color: white; }

        /* Responsive Fixes */
        @media (max-width: 768px) {
            .content-container { margin: -50px 15px 50px; padding: 35px 25px; border-radius: 25px; }
            .post-hero { height: 50vh; }
            .post-header-content { bottom: 70px; }
            .back-btn { top: 20px; left: 20px; width: 45px; height: 45px; }
        }

        /* Footer Decoration */
        .post-footer {
            margin-top: 50px; padding-top: 30px;
            border-top: 1px solid rgba(0,0,0,0.05);
            display: flex; justify-content: space-between; align-items: center;
            flex-wrap: wrap; gap: 20px;
        }
    </style>
</head>
<body>

    <a href="index.php" class="back-btn"><i class="fas fa-chevron-left"></i></a>

    <header class="post-hero">
        <img src="uploads/<?php echo $post['image']; ?>" alt="Cover Image">
        <div class="post-header-content">
            <span style="background: var(--primary); padding: 6px 16px; border-radius: 50px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);">
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
                <span style="font-weight: 800; margin-right: 15px;">Share Story:</span>
                <i class="fab fa-twitter" style="margin-right: 15px; cursor:pointer; color: #1DA1F2;"></i>
                <i class="fab fa-facebook" style="margin-right: 15px; cursor:pointer; color: #4267B2;"></i>
                <i class="fab fa-whatsapp" style="cursor:pointer; color: #25D366;"></i>
            </div>
            <button onclick="window.print()" style="border:2px solid var(--primary); background:transparent; color:var(--primary); padding: 8px 18px; border-radius: 12px; font-weight:700; cursor:pointer; transition: 0.3s;">
                <i class="fas fa-print"></i> Save PDF
            </button>
        </footer>
    </main>

</body>
</html>