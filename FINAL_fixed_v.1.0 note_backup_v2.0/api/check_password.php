<?php
session_start(); // Mulai session di awal skrip

// Database configuration file
require_once '../database.php';

// Parse incoming POST data
$note_path = $_POST['note_path'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($note_path) || empty($password)) {
    http_response_code(400);
    die('Missing parameters');
}

// Escape note path untuk keamanan
$note_path_safe = $conn->real_escape_string($note_path);

// Check if note is already authenticated in the session
if (isset($_SESSION['authenticated_notes'][$note_path_safe]) && $_SESSION['authenticated_notes'][$note_path_safe] === 'valid') {
    http_response_code(200); // OK, note is already authenticated
    exit;
} else {
    // If not authenticated in session, check the password in the database
    $query = "SELECT note_password FROM notes WHERE note_path = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        http_response_code(500);
        die("Query preparation failed: " . $conn->error);
    }
    $stmt->bind_param('s', $note_path_safe);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stored_password = $row['note_password'];

        // Verify password using MD5 (not recommended, use password_hash in production)
        if (md5($password) === $stored_password) {
            $_SESSION['authenticated_notes'][$note_path_safe] = 'valid'; // Mark as authenticated in session
            http_response_code(200); // OK
        } else {
            http_response_code(401); // Unauthorized
        }
    } else {
        http_response_code(404); // Not Found
    }

    $stmt->close();
    $conn->close();
}
?>