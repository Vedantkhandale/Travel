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

    $image = $_FILES['image']['name'];
    $extension = pathinfo($image, PATHINFO_EXTENSION);
    $new_name = time() . '.' . $extension; // Unique name taaki image overwrite na ho
    $tmp = $_FILES['image']['tmp_name'];

    if(move_uploaded_file($tmp, "uploads/".$new_name)){
        mysqli_query($conn, "INSERT INTO posts (user_id, title, slug, description, image) 
        VALUES ('$user_id', '$title', '$slug', '$desc', '$new_name')");
        $message = "success";
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

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0; background: var(--bg); color: var(--text);
            overflow-x: hidden;
        }

        .wrapper { display: flex; min-height: 100vh; }

        /* Left Side: Form */
        .form-side {
            width: 45%; padding: 60px;
            background: #fff; border-right: 1px solid var(--border);
            display: flex; flex-direction: column; justify-content: center;
        }

        .form-side h2 { font-size: 2.5rem; font-weight: 800; margin-bottom: 10px; letter-spacing: -1.5px; }
        .form-side p { color: #64748b; margin-bottom: 40px; }

        .input-group { margin-bottom: 25px; }
        .input-group label { display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem; }
        
        input, textarea {
            width: 100%; padding: 15px; border-radius: 12px;
            border: 2px solid var(--border); background: #fdfdfd;
            font-family: inherit; font-size: 1rem; transition: 0.3s;
        }

        input:focus, textarea:focus { border-color: var(--primary); outline: none; background: #fff; }

        .upload-area {
            border: 2px dashed var(--primary); padding: 30px;
            border-radius: 16px; text-align: center; cursor: pointer;
            background: rgba(99, 102, 241, 0.03); transition: 0.3s;
        }
        .upload-area:hover { background: rgba(99, 102, 241, 0.08); }
        .upload-area i { font-size: 2rem; color: var(--primary); margin-bottom: 10px; }

        .btn-publish {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white; border: none; padding: 18px; border-radius: 14px;
            font-weight: 700; font-size: 1rem; cursor: pointer;
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.2); transition: 0.3s;
            margin-top: 20px;
        }
        .btn-publish:hover { transform: translateY(-3px); box-shadow: 0 15px 25px rgba(99, 102, 241, 0.3); }

        /* Right Side: Preview */
        .preview-side {
            width: 55%; background: #f1f5f9;
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
        .preview-content h3 { font-size: 1.8rem; margin: 0 0 15px; font-weight: 800; }
        .preview-content p { color: #64748b; line-height: 1.6; }

        /* Success Popup */
        .success-msg {
            position: fixed; top: 20px; right: 20px; background: #22c55e;
            color: white; padding: 15px 30px; border-radius: 12px;
            font-weight: 600; animation: slideIn 0.5s forwards;
        }

        @keyframes slideIn { from { transform: translateX(100%); } to { transform: translateX(0); } }

        @media (max-width: 1000px) {
            .wrapper { flex-direction: column; }
            .form-side, .preview-side { width: 100%; height: auto; padding: 30px; }
            .preview-side { position: static; }
        }
    </style>
</head>
<body>

<?php if($message == "success"): ?>
    <div class="success-msg">🚀 Story Published Successfully!</div>
    <script>setTimeout(()=> window.location.href="index.php", 2000);</script>
<?php endif; ?>

<div class="wrapper">
    <div class="form-side">
        <a href="index.php" style="text-decoration:none; color:var(--primary); font-weight:700; margin-bottom:20px; display:block;">
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

    // Live Title Update
    titleInput.addEventListener('input', (e) => {
        document.getElementById('prevTitle').innerText = e.target.value || "Your Title Here";
    });

    // Live Description Update
    descInput.addEventListener('input', (e) => {
        document.getElementById('prevDesc').innerText = e.target.value.substring(0, 150) + (e.target.value.length > 150 ? '...' : '') || "Your story preview...";
    });

    // Live Image Preview
    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            document.getElementById('fileName').innerText = file.name;
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('prevImg').src = e.target.result;
                document.getElementById('cardWrap').style.transform = "scale(1.02)";
                setTimeout(()=> document.getElementById('cardWrap').style.transform = "scale(1)", 300);
            }
            reader.readAsDataURL(file);
        }
    });
</script>

</body>
</html>