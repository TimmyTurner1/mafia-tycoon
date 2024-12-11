<?php
// Database connection settings
$host = ''; // Your database host (e.g., localhost)
$username = ''; // Your database username
$password = ''; // Your database password
$database = ''; // Your database name

// Create a connection using MySQLi
$mysqli = new mysqli($host, $username, $password, $database);

// Check if the connection was successful
if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
} else {
    // You can remove or comment this out in production
    // echo 'Connected successfully';
}
?>