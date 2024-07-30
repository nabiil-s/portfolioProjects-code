<?php

$host = 'localhost';
$username_db = 'root';
$password_db = 'root';
$database = 'productDB';

// Create connection
$conn = mysqli_connect($host, $username_db, $password_db);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


?>

