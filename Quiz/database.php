<?php

//Connection

$host = 'localhost';
$username_db = 'root';
$password_db = 'root';
$database = 'quiz_db';

$conn = mysqli_connect($host, $username_db, $password_db, $database);

if(!$conn){
	die("Connection failed: " . mysqli_connect_error());
} 

?>

