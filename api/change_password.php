<?php
// Database configuration file
require_once '../database.php';

// Check if required parameters are provided
if (!isset($_POST['note_path']) || !isset($_POST['current_password']) || !isset($_POST['new_password'])) {
    http_response_code(400);
    die('Missing parameters.');
}

$note_path = $_POST['note_path'];
$current_password = $_POST['current_password'];
$new_password = $_POST['new_password'];

// Retrieve hashed password from database
$query = "SELECT note_password FROM notes WHERE note_path = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $note_path);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $stored_password = $row['note_password'];

    // Verify current password using MD5 (for demonstration purposes only; use password_hash with hashed passwords in production)
    $current_password_hash = md5($current_password);

    if ($current_password_hash === $stored_password) {
        // Hash the new password using MD5
        $new_password_hash = md5($new_password); // NOT RECOMMENDED for security reasons, use for demonstration only

        // Escape variables for security
        $note_path_safe = $conn->real_escape_string($note_path);
        $new_password_hash_safe = $conn->real_escape_string($new_password_hash);

        // Update password in the database
        $query = "UPDATE notes SET note_password = '$new_password_hash_safe' WHERE note_path = '$note_path_safe'";

        if ($conn->query($query) === TRUE) {
            echo 'Password changed successfully.';
        } else {
            http_response_code(500);
            echo 'Error changing password: ' . $conn->error;
        }
    } else {
        http_response_code(401);
        echo 'Incorrect current password.';
    }
} else {
    http_response_code(404);
    echo 'Note not found.';
}

// Close database connection
$stmt->close();
$conn->close();
?>