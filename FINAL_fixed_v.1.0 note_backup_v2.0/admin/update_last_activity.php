<?php
session_start();
if (isset($_POST['update']) && $_POST['update'] === 'true') {
    $_SESSION['last_activity'] = time();
    echo 'Activity updated';
}
?>
