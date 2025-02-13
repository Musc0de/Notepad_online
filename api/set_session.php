<?php
session_start();
$note_path_safe = $_POST['note_path'] ?? '';
$status = $_POST['status'] ?? ''; // Tambahkan untuk menerima status

if (!empty($note_path_safe)) {
    $_SESSION['authenticated_notes'][$note_path_safe] = $status; // Simpan status yang dikirim
}
