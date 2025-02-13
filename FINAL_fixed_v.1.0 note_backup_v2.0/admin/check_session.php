<?php
session_start();
$sessionTimeout = 150; // in seconds

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $sessionTimeout) {
    // Session expired
    session_unset();
    session_destroy();
    echo 'timeout';
} else {
    echo 'active';
}
?>
