<?php
$servername = "localhost"; // Replace with your server name
$username = "drea_dreamnotevs"; // Replace with your database username
$password = "++vLEz!q@nraBo69"; // Replace with your database password
$dbname = "drea_dreamnotev"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
}
?>
