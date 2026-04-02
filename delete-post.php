<?php
include 'includes/db.php';
session_start();

if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $user_id = $_SESSION['user_id'];
    
    // Check if the post belongs to the user
    $check_query = "SELECT id FROM posts WHERE id = '$id' AND user_id = '$user_id'";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        mysqli_query($conn, "DELETE FROM posts WHERE id = '$id'");
        echo "Success";
    } else {
        echo "Unauthorized";
    }
}
?>