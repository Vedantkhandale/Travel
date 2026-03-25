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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #6366f1;
            --bg: #f8fafc;
            --text: #0f172a;
            --muted: #64748b;
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; margin: 0; background: var(--bg); color: var(--text); line-height: 1.8; }
        
        /* Premium Header Image */
        .post-hero {
            width: 100%; height: 60vh;
            position: relative;
            overflow: hidden;
        }
        .post-hero img { width: 100%; height: 100%; object-fit: cover; }
        .post-hero::after {
            content: ''; position: absolute; bottom: 0; left: 0; width: 100%;
            height: 50%; background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
        }

        .post-header-content {
            position: absolute; bottom: 50px; left: 10%; right: 10%;
            color: white; z-index: 10;
        }
        .post-header-content h1 { font-size: clamp(2.5rem, 5vw, 4rem); font-weight: 800; margin: 0; letter-spacing: -2px; }

        /* Article Body */
        .content-container {
            max-width: 800px; margin: -60px auto 100px;
            background: white; padding: 60px;
            border-radius: 40px; position: relative;
            box-shadow: 0 50px 100px -20px rgba(0,0,0,0.1);
        }
        
        .meta-info {
            display: flex; gap: 20px; margin-bottom: 40px;
            padding-bottom: 20px; border-bottom: 1px solid #eee;
            color: var(--muted); font-weight: 600; font-size: 0.9rem;
        }

        .post-text { font-size: 1.2rem; color: #334155; }
        .post-text p { margin-bottom: 30px; }

        /* Back Button */
        .back-btn {
            position: fixed; top: 30px; left: 30px;
            background: white; width: 50px; height: 50px;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            text-decoration: none; color: var(--text); z-index: 100;
            transition: 0.3s;
        }
        .back-btn:hover { transform: scale(1.1); background: var(--primary); color: white; }

        @media (max-width: 768px) {
            .content-container { margin: -30px 15px 50px; padding: 30px; border-radius: 20px; }
            .post-hero { height: 40vh; }
        }
    </style>
</head>
<body>

    <a href="index.php" class="back-btn"><i class="fas fa-arrow-left"></i></a>

    <header class="post-hero">
        <img src="uploads/<?php echo $post['image']; ?>" alt="Cover">
        <div class="post-header-content">
            <span style="background: var(--primary); padding: 5px 15px; border-radius: 50px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Adventure</span>
            <h1><?php echo $post['title']; ?></h1>
        </div>
    </header>

    <main class="content-container">
        <div class="meta-info">
            <span><i class="far fa-calendar-alt"></i> March 2026</span>
            <span><i class="far fa-user"></i> By Explorer</span>
            <span><i class="far fa-clock"></i> 5 Min Read</span>
        </div>

        <article class="post-text">
            <?php echo nl2br($post['description']); ?>
        </article>

        <div style="margin-top: 60px; padding-top: 40px; border-top: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
            <div class="share-btns">
                <span style="font-weight: 800; margin-right: 15px;">Share Story:</span>
                <i class="fab fa-twitter" style="margin-right: 15px; cursor:pointer;"></i>
                <i class="fab fa-facebook" style="margin-right: 15px; cursor:pointer;"></i>
                <i class="fab fa-whatsapp" style="cursor:pointer;"></i>
            </div>
            <button onclick="window.print()" style="border:none; background:none; color:var(--primary); font-weight:700; cursor:pointer;">
                <i class="fas fa-print"></i> Save as PDF
            </button>
        </div>
    </main>

</body>
</html>