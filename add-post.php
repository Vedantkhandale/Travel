<?php
include 'includes/db.php';
session_start();

$message = "";

if(isset($_POST['submit'])){
    $user_id = $_SESSION['user_id'];

    $title = $_POST['title'];
    $desc = $_POST['description'];

    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));

    $image = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];

    move_uploaded_file($tmp, "uploads/".$image);

    mysqli_query($conn, "INSERT INTO posts (user_id,title,slug,description,image)
    VALUES ('$user_id','$title','$slug','$desc','$image')");

    $message = "success";
}
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<style>
body {
    margin:0;
    font-family:Poppins;
    background: linear-gradient(135deg,#667eea,#764ba2);
}

/* layout */
.wrapper {
    display:flex;
    min-height:100vh;
}

/* left form */
.form-box {
    width:50%;
    padding:40px;
    background:rgba(255,255,255,0.1);
    backdrop-filter: blur(20px);
    color:white;
}

/* right preview */
.preview-box {
    width:50%;
    background:white;
    padding:30px;
}

/* inputs */
input, textarea {
    width:100%;
    padding:12px;
    margin:10px 0;
    border-radius:8px;
    border:none;
}

/* button */
button {
    width:100%;
    padding:12px;
    border:none;
    border-radius:8px;
    background:#ff7eb3;
    color:white;
    cursor:pointer;
}

/* upload */
.upload {
    border:2px dashed white;
    padding:20px;
    text-align:center;
    cursor:pointer;
}

/* preview */
.preview-img {
    width:100%;
    border-radius:10px;
}

/* responsive */
@media(max-width:800px){
    .wrapper {
        flex-direction:column;
    }
    .form-box,.preview-box {
        width:100%;
    }
}
</style>

</head>

<body>

<div class="wrapper">

<!-- FORM -->
<div class="form-box">
<h2>Create Blog ✍️</h2>

<form method="POST" enctype="multipart/form-data">

<input type="text" id="title" name="title" placeholder="Title" required>

<textarea id="desc" name="description" placeholder="Write your story..."></textarea>

<div class="upload" onclick="file.click()">📸 Upload Image</div>
<input type="file" id="file" name="image" hidden required>

<button name="submit">🚀 Publish</button>

</form>
</div>

<!-- LIVE PREVIEW -->
<div class="preview-box">
<h2 id="previewTitle">Your Title</h2>

<img id="previewImg" class="preview-img" src="https://via.placeholder.com/400">

<p id="previewDesc">Your content preview will appear here...</p>
</div>

</div>

<script>
// live title
document.getElementById("title").addEventListener("input", e=>{
    document.getElementById("previewTitle").innerText = e.target.value;
});

// live desc
document.getElementById("desc").addEventListener("input", e=>{
    document.getElementById("previewDesc").innerText = e.target.value;
});

// image preview
document.getElementById("file").addEventListener("change", e=>{
    let reader = new FileReader();
    reader.onload = ()=>{
        document.getElementById("previewImg").src = reader.result;
    };
    reader.readAsDataURL(e.target.files[0]);
});
</script>

</body>
</html>