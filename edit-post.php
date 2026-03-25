<?php
include 'includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Post ka data fetch karna edit ke liye
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $query = "SELECT * FROM posts WHERE id = '$id'";
    $result = mysqli_query($conn, $query);
    $post = mysqli_fetch_assoc($result);

    if (!$post) {
        die("Post nahi mili!");
    }
}

// Update Logic
if (isset($_POST['update_post'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $post_id = $_POST['post_id'];
    
    // Agar nayi image upload hui hai
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $target = "uploads/" . basename($image);
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
        $img_query = ", image='$image'";
    } else {
        $img_query = "";
    }

    $update_query = "UPDATE posts SET title='$title', description='$description' $img_query WHERE id='$post_id'";
    
    if (mysqli_query($conn, $update_query)) {
        header("Location: index.php?msg=updated");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Story - TravelBlog</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #6366f1; --bg-light: #f8fafc; --card-bg: #ffffff; --text-main: #0f172a; --border: rgba(0,0,0,0.1); }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg-light); color: var(--text-main); margin: 0; padding: 40px 20px; }
        .form-container { max-width: 600px; margin: 0 auto; background: var(--card-bg); padding: 40px; border-radius: 30px; box-shadow: 0 20px 40px rgba(0,0,0,0.05); }
        h2 { font-weight: 800; letter-spacing: -1px; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.9rem; }
        input, textarea { width: 100%; padding: 15px; border-radius: 12px; border: 1px solid var(--border); font-family: inherit; box-sizing: border-box; }
        .btn-update { background: var(--primary); color: white; border: none; padding: 15px 30px; border-radius: 12px; font-weight: 700; cursor: pointer; width: 100%; transition: 0.3s; }
        .btn-update:hover { opacity: 0.9; transform: translateY(-2px); }
        .current-img { width: 100px; height: 60px; object-fit: cover; border-radius: 8px; margin-top: 10px; border: 2px solid var(--primary); }
    </style>
</head>
<body>

<div class="form-container">
    <h2><i class="fas fa-edit"></i> Edit Your Story</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
        
        <div class="form-group">
            <label>Title</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="6" required><?php echo htmlspecialchars($post['description']); ?></textarea>
        </div>

        <div class="form-group">
            <label>Update Image (Leave blank to keep current)</label>
            <input type="file" name="image">
            <img src="uploads/<?php echo $post['image']; ?>" class="current-img" alt="Current">
        </div>

        <button type="submit" name="update_post" class="btn-update">Save Changes</button>
        <a href="index.php" style="display:block; text-align:center; margin-top:15px; text-decoration:none; color:#64748b; font-size:0.9rem;">Cancel</a>
    </form>
</div>

</body>
</html>