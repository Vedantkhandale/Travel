<?php
include 'includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $query = "SELECT * FROM posts WHERE id = '$id'";
    $result = mysqli_query($conn, $query);
    $post = mysqli_fetch_assoc($result);
}

// Update Logic (Same as before but with better feedback)
if (isset($_POST['update_post'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $post_id = $_POST['post_id'];
    
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $target = "uploads/" . basename($image);
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
        $img_query = ", image='$image'";
    } else { $img_query = ""; }

    $update_query = "UPDATE posts SET title='$title', description='$description' $img_query WHERE id='$post_id'";
    if (mysqli_query($conn, $update_query)) {
        header("Location: index.php?status=success");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Story | TravelBlog</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #6366f1;
            --secondary: #a855f7;
            --glass: rgba(255, 255, 255, 0.8);
            --border: rgba(99, 102, 241, 0.2);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            margin: 0;
        }

        .edit-container {
            width: 100%;
            max-width: 700px;
            background: var(--glass);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border);
            border-radius: 40px;
            padding: 50px;
            box-shadow: 0 40px 100px rgba(0,0,0,0.1);
            animation: slideUp 0.6s cubic-bezier(0.23, 1, 0.32, 1);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            font-size: 2.2rem;
            font-weight: 800;
            background: linear-gradient(to right, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 35px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group label {
            font-weight: 700;
            font-size: 0.9rem;
            color: #475569;
            margin-bottom: 10px;
            display: block;
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 18px 25px;
            border-radius: 20px;
            border: 2px solid transparent;
            background: #ffffff;
            font-family: inherit;
            font-size: 1rem;
            box-sizing: border-box;
            transition: 0.3s;
            box-shadow: 0 5px 15px rgba(0,0,0,0.02);
        }

        input:focus, textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.1);
        }

        /* --- Custom File Upload --- */
        .file-upload-box {
            border: 2px dashed var(--border);
            border-radius: 20px;
            padding: 20px;
            text-align: center;
            background: rgba(99, 102, 241, 0.05);
            cursor: pointer;
            transition: 0.3s;
        }

        .file-upload-box:hover {
            background: rgba(99, 102, 241, 0.1);
            border-color: var(--primary);
        }

        #preview-img {
            width: 100%;
            max-height: 250px;
            object-fit: cover;
            border-radius: 15px;
            margin-top: 15px;
            display: block;
        }

        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 40px;
        }

        .btn-save {
            flex: 2;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            padding: 18px;
            border-radius: 20px;
            font-weight: 800;
            cursor: pointer;
            font-size: 1rem;
            box-shadow: 0 15px 30px rgba(99, 102, 241, 0.3);
            transition: 0.3s;
        }

        .btn-save:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(99, 102, 241, 0.4);
        }

        .btn-cancel {
            flex: 1;
            background: #f1f5f9;
            color: #64748b;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 20px;
            font-weight: 700;
            transition: 0.3s;
        }

        .btn-cancel:hover { background: #e2e8f0; }

        @media (max-width: 600px) {
            .edit-container { padding: 30px 20px; }
            .btn-group { flex-direction: column; }
        }
    </style>
</head>
<body>

<div class="edit-container">
    <h2>Edit Your Story</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
        
        <div class="form-group">
            <label><i class="fas fa-heading"></i> Story Title</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" placeholder="Enter a catchy title..." required>
        </div>

        <div class="form-group">
            <label><i class="fas fa-align-left"></i> Description</label>
            <textarea name="description" rows="5" placeholder="Tell your story..." required><?php echo htmlspecialchars($post['description']); ?></textarea>
        </div>

        <div class="form-group">
            <label><i class="fas fa-image"></i> Cover Image</label>
            <div class="file-upload-box" onclick="document.getElementById('fileInput').click()">
                <i class="fas fa-cloud-upload-alt" style="font-size: 2rem; color: var(--primary);"></i>
                <p style="margin: 10px 0; color: #64748b;">Click to change image</p>
                <input type="file" name="image" id="fileInput" hidden onchange="previewImage(this)">
                <img src="uploads/<?php echo $post['image']; ?>" id="preview-img" alt="Preview">
            </div>
        </div>

        <div class="btn-group">
            <button type="submit" name="update_post" class="btn-save">
                <i class="fas fa-check-circle"></i> Update Story
            </button>
            <a href="index.php" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview-img').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

</body>
</html>