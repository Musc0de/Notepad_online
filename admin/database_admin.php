<?php
$dsn = 'mysql:host=localhost;dbname=inote_note_db'; // Adjust DSN if necessary
$username = 'inote_note_dbapp';
$password = 'zlMhMEaK16#LA*0g';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log('Connection failed: ' . $e->getMessage());
    die('Database connection failed.');
}
?>
