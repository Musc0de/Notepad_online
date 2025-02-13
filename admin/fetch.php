<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$base_url = 'https://dreamnote.biz.id/admin/index';

// Define session timeout duration (1 minute)
$sessionTimeout = 600; // in seconds

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login"); // Assuming you have a login.php file
    exit;
}

// Check session timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $sessionTimeout) {
    // Session expired
    session_unset();
    session_destroy();
    header("Location: login"); // Redirect to login page
    exit;
}
$_SESSION['last_activity'] = time(); // Update last activity time

$maintenanceStatusFilePath = __DIR__ . '/../maintenance_status.txt';

// Handle maintenance mode toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['maintenance_mode'])) {
    $maintenanceMode = $_POST['maintenance_mode'] === '1'; // '1' for ON, '0' for OFF
    file_put_contents($maintenanceStatusFilePath, $maintenanceMode ? '1' : '0'); // Store status in the file
}

// Check if the file exists, if not, create it with a default value of 1 (maintenance ON)
if (!file_exists($maintenanceStatusFilePath)) {
    file_put_contents($maintenanceStatusFilePath, '1'); 
}

// Retrieve maintenance mode status from the file
$maintenanceMode = (int)file_get_contents($maintenanceStatusFilePath);

// Paths to the files (INISIALISASI LEBIH AWAL)
$filePath = __DIR__ . '/home.txt';
$logFilePath = __DIR__ . '/../notes_log.txt'; // Correct path


function getNoteFiles($directory) {
    $files = scandir($directory);
    // Filter out the current (.) and parent (..) directories
    return array_diff($files, array('.', '..'));
}

// Define the directory where note files are stored
$tmpDirectory = __DIR__ . '/../_tmp'; // Adjust the path as needed

// Handle reset action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_stats'])) {
    // Clear home.txt
    file_put_contents($filePath, '');

    // Clear notes_log.txt
    file_put_contents($logFilePath, '');

    // Refresh data after reset
    $data = processFile($filePath);
    $noteCount = countNotes($logFilePath);

    $_SESSION['message'] = 'Statistics and note count have been reset.';
    header("Location: index.php");
    exit;
}

// Get the list of note files
$noteFiles = getNoteFiles($tmpDirectory);

// Panggil fungsi untuk mendapatkan data dan jumlah catatan
$data = processFile($filePath);
$noteCount = countNotes($logFilePath);

if ($data === false || $noteCount === false) {
    error_log('Error processing files or counting notes');
}

// Generate HTML for displaying note files
$noteLinksHtml = '';
if ($noteFiles) {
    foreach ($noteFiles as $file) {
        // Concatenate the base URL with the file path
        $noteLinksHtml .= '<li><a href="' . $base_url . '/_tmp/' . htmlspecialchars($file) . '" target="_blank">' . htmlspecialchars($file) . '</a></li>';
    }
}

// Handle reset action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_stats'])) {
    // Clear home.txt
    file_put_contents($filePath, ''); // Gunakan variabel $filePath yang sudah diinisialisasi

    // Clear notes_log.txt
    file_put_contents($logFilePath, ''); // atau 0 jika Anda ingin menyimpan file

    // Refresh data after reset
    $data = processFile($filePath);
    $noteCount = countNotes($logFilePath);

    $_SESSION['message'] = 'Statistics and note count have been reset.';
    header("Location: index"); // Refresh the page after resetting
    exit;
}
// Panggil fungsi untuk mendapatkan data dan jumlah catatan
$data = processFile($filePath);
$noteCount = countNotes($logFilePath); 

if ($data === false || $noteCount === false) {
    error_log('Error processing files or counting notes');
}


// Function to process the file
function processFile($filePath) {
    if (!file_exists($filePath) || !is_readable($filePath)) {
        return [
            'total_requests' => 0,
            'unique_ips' => 0,
            'last_visited' => '0'
        ];
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    if (count($lines) % 2 !== 0) {
        return [
            'total_requests' => 0,
            'unique_ips' => 0,
            'last_visited' => '0'
        ];
    }

    $totalRequests = count($lines) / 2;
    $ipAddresses = [];
    $timestamps = [];

    for ($i = 0; $i < count($lines); $i += 2) {
        $ipAddresses[] = $lines[$i];
        $timestamps[] = intval($lines[$i + 1]);
    }

    $uniqueIps = count(array_unique($ipAddresses));

    $currentTimestamp = time();
    $lastVisited = '0';
    foreach ($timestamps as $timestamp) {
        if ($currentTimestamp - $timestamp <= 60) {
            $lastVisited = date('Y-m-d H:i:s', $timestamp);
            break;
        }
    }

    return [
        'total_requests' => $totalRequests,
        'unique_ips' => $uniqueIps,
        'last_visited' => $lastVisited
    ];
}

// Function to count the number of notes
function countNotes($logFilePath) {
    if (!file_exists($logFilePath) || !is_readable($logFilePath)) {
        return 0;
    }

    $lines = file($logFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    // If the file contains a single line with the total count, return it directly
    if (count($lines) === 1) {
        return intval($lines[0]); // Convert the single line to an integer
    }

    // Otherwise, count the number of lines
    return count($lines);
}

$data = processFile($filePath);
$noteCount = countNotes($logFilePath); // Use the corrected $logFilePath

if ($data === false || $noteCount === false) {
    error_log('Error processing files or counting notes');
}

$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']); // Clear the message after displaying it

?>