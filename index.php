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
<title>Travel Blog</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<style>
body {
    font-family: Poppins;
    background: #f5f5f5;
    margin: 0;
    scroll-behavior: smooth;
}

/* Navbar */
.navbar {
    background: linear-gradient(90deg, #667eea, #764ba2);
    color: white;
    padding: 15px;
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.navbar a {
    color: white;
    margin-left: 10px;
    text-decoration: none;
}

/* HERO */
.hero {
    text-align: center;
    padding: 50px 20px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
}

.hero h1 {
    font-size: 36px;
    animation: fadeDown 1s ease;
}

.hero p {
    opacity: 0.9;
    animation: fadeUp 1.2s ease;
}

.hero-btn {
    padding: 10px 20px;
    background: white;
    color: black;
    border-radius: 8px;
    text-decoration: none;
}

.hero-btn:hover {
    background: #eee;
}

@keyframes fadeDown {
    from {opacity:0; transform: translateY(-20px);}
    to {opacity:1; transform: translateY(0);}
}

@keyframes fadeUp {
    from {opacity:0; transform: translateY(20px);}
    to {opacity:1; transform: translateY(0);}
}

/* SLIDER */
.slider {
    width: 100%;
    height: 300px;
    overflow: hidden;
    border-radius: 10px;
    margin: 20px auto;
    max-width: 1000px;
}

.slides {
    display: flex;
    height: 100%;
    animation: slide 12s infinite ease-in-out;
}

.slides img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    background: #ddd;
}

@keyframes slide {
    0%{transform:translateX(0);}
    33%{transform:translateX(-100%);}
    66%{transform:translateX(-200%);}
}

/* SEARCH */
.search {
    text-align: center;
    margin-bottom: 20px;
}

.search input {
    padding: 12px;
    width: 80%;
    max-width: 400px;
    border-radius: 8px;
    border: 1px solid #ccc;
}

.search input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 10px rgba(102,126,234,0.4);
}

/* Container */
.container { padding: 20px; }

/* Grid */
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

/* Card */
.card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transition: 0.5s;
    cursor: pointer;
    opacity: 0;
    transform: translateY(30px);
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.card img {
    width: 100%;
    height: 180px;
    object-fit: cover;
}

.card-content { padding: 15px; }

.card h3 { margin: 0 0 10px; }

.card p { font-size: 14px; color: #555; }

/* Accordion */
.accordion { padding: 20px; }

.acc-item button {
    width: 100%;
    padding: 10px;
    background: #333;
    color: white;
    border: none;
    text-align: left;
}

.content {
    display: none;
    padding: 10px;
    background: #eee;
}

/* Loader */
#loader {
    position: fixed;
    width: 100%;
    height: 100%;
    background: white;
    z-index: 9999;
}

/* EXTRA FEATURES */
.extra-section {
    padding: 40px 20px;
    text-align: center;
    background: #fff;
}

.feature-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
}

.feature-card {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    padding: 20px;
    border-radius: 15px;
    transition: 0.4s;
}

.feature-card:hover {
    transform: translateY(-10px);
}

/* FOOTER */
.footer {
    background: #111;
    color: #bbb;
    padding: 30px 20px;
    text-align: center;
}

.footer h3 { color: white; }

.footer a {
    color: #667eea;
    text-decoration: none;
    margin: 0 10px;
}

/* Responsive */
@media(max-width: 500px){
    .navbar {
        flex-direction: column;
        text-align: center;
    }
}

</style>

</head>

<body>

<div id="loader"></div>

<div class="navbar">
    <div>🌍 Travel Blog</div>
    <div>
        Welcome <?php echo $_SESSION['user_name']; ?>
        <a href="add-post.php">Add Blog</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<section class="hero">
    <h1>Explore Travel Memories 🌍</h1>
    <p>Write, Share & Relive your journeys</p>
    <a href="add-post.php" class="hero-btn">Start Blogging</a>
</section>

<div class="slider">
    <div class="slides">
        <img src="images/1 .jpg"> 
        <img src="images/1 .jpg">
        <img src="images/1 .jpg">
    </div>
</div>

<div class="container">

    <div class="search">
        <input type="text" id="searchInput" placeholder="Search blogs...">
    </div>

    <h2>All Travel Blogs</h2>

    <div class="grid">
    <?php
    $result = mysqli_query($conn, "SELECT * FROM posts ORDER BY id DESC");
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
    ?>
        <a href="<?php echo $row['slug']; ?>" style="text-decoration:none; color:black;">
            <div class="card">
                <img src="uploads/<?php echo $row['image']; ?>">
                <div class="card-content">
                    <h3><?php echo $row['title']; ?></h3>
                    <p><?php echo substr($row['description'], 0, 100); ?>...</p>
                </div>
            </div>
        </a>
    <?php } } ?>
    </div>

    <!-- EXTRA SECTION -->
    <div class="extra-section">
        <h2>✨ Why Choose Us</h2>
        <div class="feature-grid">
            <div class="feature-card"><h3>🌍 Explore</h3><p>Discover travel stories</p></div>
            <div class="feature-card"><h3>✍️ Write</h3><p>Share your journey</p></div>
            <div class="feature-card"><h3>📸 Memories</h3><p>Save forever</p></div>
            <div class="feature-card"><h3>🚀 Grow</h3><p>Build your audience</p></div>
        </div>
    </div>

    <div class="accordion">
        <div class="acc-item">
            <button>🌍 Why use this blog?</button>
            <div class="content">Store your travel memories forever.</div>
        </div>
    </div>

</div>

<!-- FOOTER -->
<div class="footer">
    <h3>🌍 Travel Blog</h3>
    <p>Made with ❤️ by Vedant</p>
    <a href="#">Home</a>
    <a href="add-post.php">Add Blog</a>
</div>

<script>
// same JS
document.addEventListener("DOMContentLoaded", function(){
    document.querySelectorAll(".acc-item button").forEach(btn => {
        btn.addEventListener("click", () => {
            let content = btn.nextElementSibling;
            content.style.display =
                content.style.display === "block" ? "none" : "block";
        });
    });

    let search = document.getElementById("searchInput");
    if(search){
        search.addEventListener("keyup", function() {
            let value = this.value.toLowerCase();
            document.querySelectorAll(".card").forEach(card => {
                card.style.display =
                    card.innerText.toLowerCase().includes(value)
                    ? "block"
                    : "none";
            });
        });
    }

    window.onload = () => document.getElementById("loader").style.display = "none";

    window.addEventListener("scroll", () => {
        document.querySelectorAll(".card").forEach(card => {
            let top = card.getBoundingClientRect().top;
            if(top < window.innerHeight - 50){
                card.style.opacity = 1;
                card.style.transform = "translateY(0)";
            }
        });
    });
});
</script>

</body>
</html>