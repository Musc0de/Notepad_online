<?php
// note_has_password.php

// Your database and connection setup
require_once '../database.php';

$note_path = $_POST['note_path'];

// Check if note has a password
$query = "SELECT note_password FROM notes WHERE note_path = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $note_path);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($note_password);
    $stmt->fetch();

    // Return JSON response indicating whether the note has a password
    header('Content-Type: application/json');
    echo json_encode(['has_password' => !empty($note_password)]);
} else {
    // If note not found, return false
    header('Content-Type: application/json');
    echo json_encode(['has_password' => false]);
}

$stmt->close();
$conn->close();
?>
