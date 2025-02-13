<?php
session_start(); // Mulai session di awal skrip

// Database configuration file
require_once '../database.php';

// Check if required parameters are provided
if (!isset($_POST['note_path']) || !isset($_POST['password'])) {
    http_response_code(400);
    die('Access Denied.');
}

$note_path = $_POST['note_path'];
$password = $_POST['password'];

// Hash the password using MD5
$password_hash = md5($password);

// Escape variables for security
$note_path_safe = $conn->real_escape_string($note_path);
$password_hash_safe = $conn->real_escape_string($password_hash);

// Update or insert password into the database
$query = "INSERT INTO notes (note_path, note_password) VALUES ('$note_path_safe', '$password_hash_safe')
          ON DUPLICATE KEY UPDATE note_password = '$password_hash_safe'";

if ($conn->query($query) === TRUE) {
    // Tandai note sebagai valid di session
    $_SESSION['authenticated_notes'][$note_path_safe] = 'valid'; 

    echo 'Password set successfully.';
} else {
    http_response_code(500);
    echo 'Error setting password: ' . $conn->error;
}

$conn->close();
?>