<?php
include 'includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

// Fetch current user data
$query = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

if (isset($_POST['update_profile'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    
    // Handle profile photo upload
    $profile_photo = $user['profile_photo']; // Keep existing
    if (!empty($_FILES['profile_photo']['name'])) {
        $extension = strtolower(pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($extension, $allowed) && $_FILES['profile_photo']['size'] <= 5000000) { // 5MB
            $new_name = 'profile_' . $user_id . '_' . time() . '.' . $extension;
            $target = "uploads/" . $new_name;
            
            if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target)) {
                // Delete old profile photo if exists
                if (!empty($user['profile_photo']) && file_exists("uploads/" . $user['profile_photo'])) {
                    unlink("uploads/" . $user['profile_photo']);
                }
                $profile_photo = $new_name;
            }
        }
    }
    
    // Handle background image upload
    $background_image = $user['background_image']; // Keep existing
    if (!empty($_FILES['background_image']['name'])) {
        $extension = strtolower(pathinfo($_FILES['background_image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($extension, $allowed) && $_FILES['background_image']['size'] <= 10000000) { // 10MB
            $new_name = 'bg_' . $user_id . '_' . time() . '.' . $extension;
            $target = "uploads/" . $new_name;
            
            if (move_uploaded_file($_FILES['background_image']['tmp_name'], $target)) {
                // Delete old background if exists
                if (!empty($user['background_image']) && file_exists("uploads/" . $user['background_image'])) {
                    unlink("uploads/" . $user['background_image']);
                }
                $background_image = $new_name;
            }
        }
    }
    
    // Update database
    $update_query = "UPDATE users SET name='$name', profile_photo='$profile_photo', background_image='$background_image' WHERE id='$user_id'";
    if (mysqli_query($conn, $update_query)) {
        $_SESSION['user_name'] = $name;
        header("Location: profile.php?user_id=$user_id&updated=1");
        exit();
    } else {
        $message = "❌ Error updating profile.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile | TravelBlog</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/index.css?v=3">
    <link rel="stylesheet" href="assets/css/enhance.css?v=3">
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar" id="mainNav">
        <a href="index.php" class="logo">
            <i class="fas fa-globe"></i><span>Travel</span>Blog
        </a>
        
        <div class="nav-links" id="navLinks">
            <a href="profile.php?user_id=<?php echo $_SESSION['user_id']; ?>" class="user-welcome">
                <i class="fas fa-user"></i> Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
            </a>
            <a href="index.php">Home</a>
            <a href="add-post.php">Add Post</a>
            <a href="logout.php">Logout</a>
        </div>
        
        <button class="theme-btn" id="themeBtn" type="button" aria-label="Toggle theme" onclick="toggleTheme()">
            <i class="fas fa-moon"></i>
        </button>
        
        <button class="menu-toggle" id="mobile-menu" type="button" aria-label="Open menu" aria-controls="navLinks" aria-expanded="false">
            <i class="fas fa-bars"></i>
        </button>
    </nav>

    <!-- Edit Profile Section -->
    <section style="padding: 120px 20px 60px; min-height: 100vh;">
        <div class="container">
            <div style="max-width: 600px; margin: 0 auto; background: var(--card-bg); padding: 40px; border-radius: 20px; box-shadow: var(--shadow-lg);">
                <h1 style="text-align: center; margin-bottom: 30px; font-size: 2.5rem;">✏️ Edit Your Profile</h1>
                
                <?php if (!empty($message)): ?>
                    <div style="padding: 15px; border-radius: 10px; margin-bottom: 20px; background: <?php echo strpos($message, '✅') === 0 ? 'var(--success)' : 'var(--error)'; ?>; color: white; text-align: center;">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data">
                    <div style="margin-bottom: 25px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--text-main);">Name</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required 
                               style="width: 100%; padding: 15px; border: 2px solid var(--border); border-radius: 12px; font-size: 1rem; background: var(--bg-light); color: var(--text-main);">
                    </div>
                    
                    <div style="margin-bottom: 25px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--text-main);">Profile Photo</label>
                        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 10px;">
                            <div style="width: 80px; height: 80px; border-radius: 50%; overflow: hidden; border: 3px solid var(--primary);">
                                <?php if (!empty($user['profile_photo'])): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($user['profile_photo']); ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <div style="width: 100%; height: 100%; background: var(--gradient-primary); display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div>
                                <input type="file" name="profile_photo" accept="image/*" id="profilePhotoInput" style="display: none;">
                                <button type="button" onclick="document.getElementById('profilePhotoInput').click()" 
                                        style="background: var(--primary); color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer;">
                                    <i class="fas fa-camera"></i> Change Photo
                                </button>
                                <p style="margin: 5px 0 0; font-size: 0.8rem; color: var(--text-muted);">JPG, PNG, GIF up to 5MB</p>
                            </div>
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 25px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--text-main);">Background Image</label>
                        <div style="margin-bottom: 10px;">
                            <?php if (!empty($user['background_image'])): ?>
                                <div style="width: 100%; height: 150px; border-radius: 12px; overflow: hidden; margin-bottom: 10px;">
                                    <img src="uploads/<?php echo htmlspecialchars($user['background_image']); ?>" alt="Background" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                            <?php endif; ?>
                            <input type="file" name="background_image" accept="image/*" id="backgroundInput" style="display: none;">
                            <button type="button" onclick="document.getElementById('backgroundInput').click()" 
                                    style="background: var(--secondary); color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer;">
                                <i class="fas fa-image"></i> <?php echo empty($user['background_image']) ? 'Add' : 'Change'; ?> Background
                            </button>
                            <p style="margin: 5px 0 0; font-size: 0.8rem; color: var(--text-muted);">JPG, PNG, GIF up to 10MB</p>
                        </div>
                    </div>
                    
                    <div style="text-align: center;">
                        <button type="submit" name="update_profile" 
                                style="background: var(--gradient-primary); color: white; border: none; padding: 15px 40px; border-radius: 12px; font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: 0.3s;">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                        <a href="profile.php?user_id=<?php echo $user_id; ?>" 
                           style="display: inline-block; margin-left: 15px; color: var(--text-muted); text-decoration: none; padding: 15px 20px; border-radius: 12px; border: 2px solid var(--border); transition: 0.3s;">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Scripts -->
    <script src="assets/js/index.js"></script>
</body>
</html>
