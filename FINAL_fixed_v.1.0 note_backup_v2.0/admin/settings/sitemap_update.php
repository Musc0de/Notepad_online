<?php
session_start();
require_once '../database.php';
require_once 'sitemap.php'; // Assuming the updateSitemap function is in this file

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || $_SESSION['user_role'] !== 'admin') {
    header('Location: login');
    exit;
}

// Call the updateSitemap function
updateSitemap();

// Set a success message and redirect back to the admin dashboard
$_SESSION['message'] = 'Sitemap successfully updated!';
header('Location: index.php');
exit;
?>
