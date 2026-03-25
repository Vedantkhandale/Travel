<?php
include 'includes/db.php';
session_start();

if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    // Security check: Sirf owner hi delete kar paye (Optional but recommended)
    // $user_id = $_SESSION['user_id'];
    // mysqli_query($conn, "DELETE FROM posts WHERE id = '$id' AND user_id = '$user_id'");
    
    mysqli_query($conn, "DELETE FROM posts WHERE id = '$id'");
    echo "Success"; 
}
?>