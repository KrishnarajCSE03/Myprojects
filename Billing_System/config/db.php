<?php
$host = "localhost";
$user = "root";
$pass = "";
$db_name = "billingsystem";

// Create connection
$conn = new mysqli($host, $user, $pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
