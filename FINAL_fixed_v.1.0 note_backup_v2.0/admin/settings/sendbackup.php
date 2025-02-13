<?php
session_start();
require_once '../database.php';

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || $_SESSION['user_role'] !== 'admin') {
    header('Location: login');
    exit;
}

// Call the script to create a backup and send to Telegram.
exec('php ../send_document_handler.php');

// Set a success message and redirect back to the admin dashboard
$_SESSION['message'] = 'Backup successfully sent!';
header('Location: index.php');
exit;
?>
