<?php
$conn = mysqli_connect("localhost", "root", "", "travel_blog");

if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}
?>