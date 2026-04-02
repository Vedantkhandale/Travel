<?php
include 'includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";
if(isset($_POST['submit'])){
    $user_id = $_SESSION['user_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    
    // Ensure slug is unique
    $original_slug = $slug;
    $counter = 1;
    while(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM posts WHERE slug='$slug'")) > 0){
        $slug = $original_slug . '-' . $counter;
        $counter++;
    }

    // Validate inputs
    if(empty($title) || empty($desc)){
        $message = "error: Please fill all required fields";
    } elseif(strlen($desc) < 50){
        $message = "error: Description must be at least 50 characters";
    } elseif(!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK){
        $message = "error: Please select an image to upload";
    } else {
        $image = $_FILES['image']['name'];
        $extension = strtolower(pathinfo($image, PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if(!in_array($extension, $allowed_extensions)){
            $message = "error: Only JPG, JPEG, PNG, and GIF files are allowed";
        } elseif($_FILES['image']['size'] > 5000000){ // 5MB limit
            $message = "error: Image size must be less than 5MB";
        } else {
            $new_name = time() . '.' . $extension; 
            $tmp = $_FILES['image']['tmp_name'];
            $upload_path = "uploads/" . $new_name;

            // Check if uploads directory exists and is writable
            if(!is_dir("uploads/") || !is_writable("uploads/")){
                $message = "error: Upload directory is not accessible";
            } elseif(move_uploaded_file($tmp, $upload_path)){
                $query = "INSERT INTO posts (user_id, title, slug, description, image) 
                         VALUES ('$user_id', '$title', '$slug', '$desc', '$new_name')";
                
                if(mysqli_query($conn, $query)){
                    $message = "success";
                } else {
                    $message = "error: Failed to save post to database";
                    // Delete uploaded file if DB insert fails
                    unlink($upload_path);
                }
            } else {
                $message = "error: Failed to upload image. Check file permissions.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Story | TravelBlog</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #6366f1;
            --secondary: #a855f7;
            --bg: #f8fafc;
            --card: #ffffff;
            --text: #0f172a;
            --border: rgba(0,0,0,0.08);
        }

        * { box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0; background: var(--bg); color: var(--text);
            overflow-x: hidden;
        }

        /* Simple Nav */
        .nav {
            background: var(--card); border-bottom: 1px solid var(--border);
            padding: 15px 5%; display: flex; justify-content: space-between; align-items: center;
            position: sticky; top: 0; z-index: 1000;
        }
        .nav .logo { color: var(--text); font-size: 1.5rem; font-weight: 800; text-decoration: none; }
        .nav .logo span { color: var(--primary); }
        .nav a { color: var(--text); text-decoration: none; font-weight: 600; padding: 8px 16px; border-radius: 8px; transition: 0.3s; }
        .nav a:hover { background: rgba(99,102,241,0.1); color: var(--primary); }

        /* Responsive Wrapper */
        .wrapper { display: flex; min-height: 100vh; flex-wrap: wrap; }

        /* Left Side: Form */
        .form-side {
            flex: 1; min-width: 320px; padding: 60px;
            background: #fff; border-right: 1px solid var(--border);
            display: flex; flex-direction: column; justify-content: center;
        }

        .form-side h2 { font-size: clamp(1.8rem, 5vw, 2.5rem); font-weight: 800; margin-bottom: 10px; letter-spacing: -1.5px; }
        .form-side p { color: #64748b; margin-bottom: 40px; }

        .input-group { margin-bottom: 25px; }
        .input-group label { display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem; }
        
        input, textarea {
            width: 100%; padding: 15px; border-radius: 12px;
            border: 2px solid var(--border); background: #fdfdfd;
            font-family: inherit; font-size: 1rem; transition: 0.3s;
        }

        input:focus, textarea:focus { border-color: var(--primary); outline: none; background: #fff; }

        .char-counter {
            text-align: right;
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-top: 5px;
        }

        .char-counter.warning { color: #f59e0b; }
        .char-counter.danger { color: #ef4444; }

        .upload-area {
            border: 2px dashed var(--primary); padding: 30px;
            border-radius: 16px; text-align: center; cursor: pointer;
            background: rgba(99, 102, 241, 0.03); transition: 0.3s;
        }
        .upload-area:hover { background: rgba(99, 102, 241, 0.08); }
        .upload-area i { font-size: 2rem; color: var(--primary); margin-bottom: 10px; }

        .btn-publish {
            width: 100%; background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white; border: none; padding: 18px; border-radius: 14px;
            font-weight: 700; font-size: 1rem; cursor: pointer;
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.2); transition: 0.3s;
            margin-top: 20px;
        }
        .btn-publish:hover { transform: translateY(-3px); box-shadow: 0 15px 25px rgba(99, 102, 241, 0.3); }

        /* Right Side: Preview */
        .preview-side {
            flex: 1.2; min-width: 320px; background: #f1f5f9;
            display: flex; align-items: center; justify-content: center;
            padding: 40px; position: sticky; top: 0; height: 100vh;
        }

        .preview-card {
            background: white; width: 100%; max-width: 450px;
            border-radius: 32px; overflow: hidden;
            box-shadow: 0 40px 100px -20px rgba(0,0,0,0.15);
            transition: 0.5s;
        }

        .preview-img { width: 100%; height: 280px; object-fit: cover; background: #e2e8f0; }
        .preview-content { padding: 30px; }
        .preview-content h3 { font-size: 1.8rem; margin: 0 0 15px; font-weight: 800; line-height: 1.2; }
        .preview-content p { color: #64748b; line-height: 1.6; }

        .success-msg {
            position: fixed; top: 20px; right: 20px; background: #22c55e;
            color: white; padding: 15px 30px; border-radius: 12px;
            font-weight: 600; z-index: 1000; animation: slideIn 0.5s forwards;
        }

        .error-msg {
            position: fixed; top: 20px; right: 20px; background: #ef4444;
            color: white; padding: 15px 30px; border-radius: 12px;
            font-weight: 600; z-index: 1000; animation: slideIn 0.5s forwards;
        }

        @keyframes slideIn { from { transform: translateX(100%); } to { transform: translateX(0); } }

        /* Mobile Adjustments */
        @media (max-width: 1024px) {
            .wrapper { flex-direction: column; }
            .form-side { padding: 40px 20px; border-right: none; }
            .preview-side { position: relative; height: auto; padding: 60px 20px; }
            .preview-card { max-width: 100%; }
        }
    </style>
</head>
<body>

    <nav class="nav">
        <a href="index.php" class="logo"><i class="fas fa-map-marked-alt"></i> Travel<span>Blog</span></a>
        <div>
            <a href="index.php">Feed</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

<?php if($message == "success"): ?>
    <div class="success-msg">🚀 Story Published Successfully!</div>
    <script>setTimeout(()=> window.location.href="index.php", 2000);</script>
<?php elseif(strpos($message, "error:") === 0): ?>
    <div class="error-msg"><?php echo str_replace("error:", "❌", $message); ?></div>
<?php endif; ?>

<div class="wrapper">
    <div class="form-side">
        <a href="index.php" style="text-decoration:none; color:var(--primary); font-weight:700; margin-bottom:20px; display:inline-block;">
            <i class="fas fa-arrow-left"></i> Back to Feed
        </a>
        <h2>Share Your Story</h2>
        <p>Every journey has a soul. Tell yours to the world.</p>

        <form method="POST" enctype="multipart/form-data">
            <div class="input-group">
                <label>Catchy Title</label>
                <input type="text" id="titleInput" name="title" placeholder="e.g. A Sunset in Santorini" required>
            </div>

            <div class="input-group">
                <label>Story Details</label>
                <textarea id="descInput" name="description" rows="6" placeholder="Start writing your adventure..." required></textarea>
                <div class="char-counter" id="charCounter">0 / 1000 characters</div>
            </div>

            <div class="input-group">
                <label>Featured Image</label>
                <div class="upload-area" onclick="document.getElementById('fileInput').click()">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p id="fileName">Click to upload or drag and drop</p>
                    <span style="font-size:0.8rem; color:#94a3b8;">PNG, JPG up to 10MB</span>
                </div>
                <input type="file" id="fileInput" name="image" hidden required accept="image/*">
            </div>

            <button type="submit" name="submit" class="btn-publish">🚀 Publish Story</button>
        </form>
    </div>

    <div class="preview-side">
        <div class="preview-card" id="cardWrap">
            <img id="prevImg" class="preview-img" src="https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=800&q=60">
            <div class="preview-content">
                <div style="color:var(--primary); font-weight:800; font-size:0.7rem; text-transform:uppercase; margin-bottom:10px;">Preview Mode</div>
                <h3 id="prevTitle">Your Title Here</h3>
                <p id="prevDesc">Your story will start appearing here as you type. Make it inspiring!</p>
                <div style="margin-top:20px; display:flex; align-items:center; gap:10px; opacity:0.6;">
                    <i class="far fa-heart"></i> <i class="far fa-comment"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const titleInput = document.getElementById('titleInput');
    const descInput = document.getElementById('descInput');
    const fileInput = document.getElementById('fileInput');
    const charCounter = document.getElementById('charCounter');
    const maxChars = 1000;

    // Live preview for title
    titleInput.addEventListener('input', function() {
        document.getElementById('prevTitle').textContent = this.value || 'Your Title Here';
    });

    // Live preview for description with character counter
    descInput.addEventListener('input', function() {
        const count = this.value.length;
        charCounter.textContent = `${count} / ${maxChars} characters`;

        // Update preview
        document.getElementById('prevDesc').textContent = this.value.substring(0, 150) + (this.value.length > 150 ? '...' : '') || 'Your story will start appearing here as you type. Make it inspiring!';

        // Color coding
        charCounter.classList.remove('warning', 'danger');
        if (count > 800) {
            charCounter.classList.add('warning');
        }
        if (count > 950) {
            charCounter.classList.add('danger');
        }
    });

    // File upload preview
    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            document.getElementById('fileName').innerText = file.name;
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('prevImg').src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });
</script>

</body>
</html>