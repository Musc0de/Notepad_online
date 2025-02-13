<?php
// Check maintenance mode from the file
$maintenanceMode = (int)file_get_contents('maintenance_status.txt');
// Check for Maintenance Mode
if ($maintenanceMode) {
    $maintenanceMessage = "This website is currently under maintenance. Please check back later.";
    include 'maintenance.php';
    exit;
}


// Base URL of the website, without trailing slash.
$base_url = 'https://dreamnote.biz.id';

// Path to the directory to save the notes in, without trailing slash.
$save_path = '_tmp';

// Disable caching.
header('Cache-Control: no-store');

// If no note name is provided, or if the name is too long, or if it contains invalid characters.
if (!isset($_GET['note']) || strlen($_GET['note']) > 64 || !preg_match('/^[a-zA-Z0-9_-]+$/', $_GET['note'])) {
    // Generate a name with 5 random unambiguous characters. Redirect to it.
    $newNoteName = substr(str_shuffle('1234579abcdefghjkmnpqrstwxyz'), -12);
    header("Location: $base_url/" . $newNoteName);

    // Update sitemap.xml with the new note URL
    updateSitemap($newNoteName);

    die;
}

function updateSitemap($noteName) {
    $sitemapPath = __DIR__ . '/sitemap.xml';

    $sitemapContent = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
    $sitemapContent .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

    // Add existing URLs from sitemap.xml (if it exists)
    if (file_exists($sitemapPath)) {
        $existingSitemap = simplexml_load_file($sitemapPath);
        if ($existingSitemap) {
            foreach ($existingSitemap->url as $url) {
                $sitemapContent .= $url->asXML() . PHP_EOL;
            }
        }
    }

    // Add new note URL
    $sitemapContent .= '<url>' . PHP_EOL;
    $sitemapContent .= '  <loc>' . $base_url . '/' . $noteName . '</loc>' . PHP_EOL;
    $sitemapContent .= '  <lastmod>' . date('Y-m-d') . '</lastmod>' . PHP_EOL; // Include last modified date
    $sitemapContent .= '  <changefreq>daily</changefreq>' . PHP_EOL; 
    $sitemapContent .= '</url>' . PHP_EOL;

    $sitemapContent .= '</urlset>';
    
    // Lock and write to the file (atomically)
    if (file_put_contents($sitemapPath, $sitemapContent, LOCK_EX) === false) {
        // Log an error if the sitemap update fails
        error_log("Failed to update sitemap.xml");
    }
}


// Mendapatkan IP pengguna
$userIP = $_SERVER['REMOTE_ADDR'];

// Mendapatkan User-Agent pengguna
$userAgent = $_SERVER['HTTP_USER_AGENT'];

// Path ke file tujuan
$filePath = __DIR__ . '/admin/home.txt';

// Cek apakah file sudah ada dan apakah sudah lebih dari 24 jam sejak terakhir dimodifikasi
if (file_exists($filePath) && (time() - filemtime($filePath) > 24 * 60 * 60)) {
    // Jika ya, hapus file
    unlink($filePath);
}

// Menyusun informasi yang akan disimpan
$info = "User IP: $userIP\nUser Agent: $userAgent\n";

// Menambahkan informasi ke dalam file
file_put_contents($filePath, $info, FILE_APPEND | LOCK_EX);

// URL API
$url = 'https://api.dreamnote.biz.id/api/request/v1/index';

// Panggil API dengan menggunakan file_get_contents
$response = file_get_contents($url);

// Cek jika respons berhasil
if ($response === false) {
    // Tangani jika gagal mengambil respons
    echo "Gagal mengambil data dari API.";
} else {
    // Tangani jika respons berhasil
    $data = json_decode($response, true);
    if ($data['error']) {
        echo "Terjadi kesalahan: " . $data['message'];
    } else {
        // echo "Data dari API: " . $response;
    }
}

// Get note name from the query parameter
$note_name = $_GET['note'];
$logFilePath = __DIR__ . '/notes_log.txt';


// Database configuration file
require_once 'database.php';


// Function to update the log file
function updateNoteLog($logFilePath) {
    global $save_path; // Access the $save_path variable

    // Get the number of files in the _tmp directory
    $noteCount = count(glob($save_path . '/*'));

    // Write the updated count back to the log file
    file_put_contents($logFilePath, $noteCount, LOCK_EX); 
}
// Function to check if a note has a password set
function noteHasPassword($note_path) {
    global $conn;
    $note_path_safe = $conn->real_escape_string($note_path);
    $query = "SELECT note_password FROM notes WHERE note_path = '$note_path_safe'";
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return !empty($row['note_password']);
    }
    return false;
}

// Function to generate CSRF token
function generateCSRFToken() {
    return bin2hex(random_bytes(32));
}

// Get CSRF token or generate a new one if not present
$csrf_token = isset($_COOKIE['csrf_token']) ? $_COOKIE['csrf_token'] : generateCSRFToken();
setcookie('csrf_token', $csrf_token, time() + (86400 * 30), '/', '', true, true); // Secure and httponly

$path = $save_path . '/' . $note_name;

// Handle POST request to update or delete note
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start(); // Start session

    // Verify CSRF token
    $submitted_csrf_token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($csrf_token, $submitted_csrf_token)) {
        http_response_code(403);
        die('CSRF token validation failed.');
    }

    $text = isset($_POST['text']) ? $_POST['text'] : file_get_contents("php://input");

    // Check if the note has a password set in the database
    if (noteHasPassword($note_name)) {
        // If the note has a password, check the session for authentication status
        if (isset($_SESSION['authenticated_notes'][$note_name]) && $_SESSION['authenticated_notes'][$note_name] === 'valid') {
            // Authenticated: save the content
            file_put_contents($path, $text);

            if (!strlen($text)) {
                unlink($path);
            }

            updateNoteLog($logFilePath);

            // Call the script to create a backup and send to Telegram.
            exec('php send_document_hadler.php');

            die;
        } else {
            http_response_code(401); // Unauthorized
            die("Unauthorized access.");
        }
    } else {
        // If the note doesn't have a password, save the content without authentication
        file_put_contents($path, $text);
        if (!strlen($text)) {
            unlink($path);
        }

        updateNoteLog($logFilePath);

        // Call the script to create a backup and send to Telegram.
        exec('php send_document_hadler.php');

        die;
    }
}

?>