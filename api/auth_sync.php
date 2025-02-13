<?php
session_start(); 
require_once '../database.php';

// Pengambilan nama catatan dan token CSRF dari permintaan
$noteName = isset($_GET['note']) ? $_GET['note'] : '';
$csrfToken = isset($_GET['csrf_token']) ? $_GET['csrf_token'] : '';

$validApiKey = 'a4feaaa3-1686-4e42-919d-17198c355939';
$apiKey = isset($_SERVER['HTTP_X_API_KEY']) ? $_SERVER['HTTP_X_API_KEY'] : '';

// Verifikasi API Key
if ($apiKey !== $validApiKey) {
    http_response_code(403); // Forbidden
    die('Access Denied.'); // Tolak akses jika API key tidak valid
}

// Fungsi untuk memeriksa apakah catatan memiliki kata sandi
function noteHasPassword($noteName) {
    global $conn;
    $stmt = $conn->prepare("SELECT note_password FROM notes WHERE note_path = ?");
    $stmt->bind_param("s", $noteName);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return !empty($row['note_password']);
}

// Fungsi untuk memeriksa otentikasi catatan jika ada kata sandi
function authenticateNoteAccess($noteName) {
    if (noteHasPassword($noteName)) {
        // Jika catatan memiliki kata sandi, cek apakah catatan sudah terautentikasi di sesi
        if (isset($_SESSION['authenticated_notes'][$noteName]) && 
            $_SESSION['authenticated_notes'][$noteName] === 'valid') {
            return true;
        } else {
            http_response_code(401); // Unauthorized
            die('Unauthorized access.');
        }
    }
    return true; // Tidak ada kata sandi, akses diizinkan
}

// Validasi Token CSRF (Implementasikan logika validasi Anda di sini)
function verifyCSRFToken($token) {
    return true; // Sementara mengembalikan nilai true untuk mem-bypass validasi CSRF
}

if (!verifyCSRFToken($csrfToken)) {
    http_response_code(403); 
    die('Invalid CSRF Token.'); 
}

// Jalankan fungsi untuk otentikasi akses note jika ada password
if (authenticateNoteAccess($noteName)) {
    // Path untuk menyimpan catatan (sesuaikan dengan path Anda)
    $save_path = '../_tmp';

    // Dapatkan isi catatan
    $path = $save_path . '/' . $noteName;
    if (is_file($path)) {
        echo file_get_contents($path);
    } else {
        http_response_code(404); // Not Found
        echo 'Note not found.';
    }
} else {
    // If note has password and is not authenticated, return unauthorized
    http_response_code(401); // Unauthorized
    die('Unauthorized access.');
}