<?php
$conn = mysqli_connect("localhost", "root", "", "travel_blog");

if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}


// Ye details aapko InfinityFree ke MySQL section se milegi

// Ye details aapko InfinityFree ke 'MySQL Databases' section se milegi
// $hostname = "sql100.infinityfree.com"; 
// $username = "if0_41472352";            // Ye aapka Hosting Username hai
// $password = "bMKv5X5lOy";   // Jo password aap login ke liye use karte ho
// $dbname   = "if0_41472352_travel_blog";      // Jo database aapne create kiya hai (Pura Naam)

// // Connection check
// $conn = mysqli_connect("sql100.infinityfree.com", "if0_41472352","bMKv5X5lOy", "if0_41472352_travel_blog", );

// if (!$conn) {
//     die("Connection Failed: " . mysqli_connect_error());
// }

?>
