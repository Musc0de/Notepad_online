<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Telegram bot token
$telegram_bot_token = '5911356888:AAHCNTduVD8g6jj2SmToT7jFxPPYIpAg28Y';
// Path to the directory to save the notes in, without trailing slash.
$save_path = '_tmp';

// Log file for debugging
$log_file = $save_path . '/handler_log.txt';

// Read the incoming update from Telegram
$update_raw = file_get_contents('php://input');
file_put_contents($log_file, "Raw update received: " . $update_raw . "\n", FILE_APPEND);
$update = json_decode($update_raw, true);

// Log incoming update
file_put_contents($log_file, "Update received: " . json_encode($update) . "\n", FILE_APPEND);

// Main handler logic
if (isset($update['message'])) { 
    $message = $update['message'];
    $chat_id = $message['chat']['id'];
    $chat_type = $message['chat']['type'];
    $command = $message['text'];

    // Allow command in groups and private chats
    if ($command === '/sendDocument') { 
        // Reply to the user
        $creating_message_id = sendMessage($chat_id, "Creating Backup Now!");

        // Create and send the latest backup
        $result = createAndSendBackup($chat_id, $creating_message_id);

        if ($result) {
            sendMessage($chat_id, "Backup sent successfully!");
        } else {
            sendMessage($chat_id, "Failed to send the backup.");
        }
    }
} else {
    // Handle other types of updates (e.g., edited messages, callback queries) if needed.
    file_put_contents($log_file, "Update received but not a message: " . json_encode($update) . "\n", FILE_APPEND);
}

function createAndSendBackup($chat_id, $creating_message_id) {
    global $telegram_bot_token, $save_path, $log_file;

    // Create a zip file of the _tmp folder
    $zip_filename = createBackupZip();

    if ($zip_filename) {
        // Send the zip file to Telegram
        $result = sendToTelegram($chat_id, $zip_filename);

        // Delete the "Creating Backup Now!" message
        deleteMessage($chat_id, $creating_message_id);

        return $result;
    } else {
        return false;
    }
}

function createBackupZip() {
    global $save_path, $log_file;

    // Generate the zip file name with sequential numbering
    $zip_number = 1;
    while (file_exists($save_path . '/backup' . $zip_number . '.zip')) {
        $zip_number++;
    }
    $zip_filename = $save_path . '/backup' . $zip_number . '.zip';

    $zip = new ZipArchive();
    if ($zip->open($zip_filename, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
        $files = scandir($save_path);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..' && is_file($save_path . '/' . $file)) {
                $zip->addFile($save_path . '/' . $file, $file);
            }
        }
        $zip->close();

        return $zip_filename;
    } else {
        // Log zip creation failure
        file_put_contents($log_file, "Failed to create zip file: $zip_filename\n", FILE_APPEND);
        return false;
    }
}

function sendToTelegram($chat_id, $file_path) {
    global $telegram_bot_token, $log_file;

    $url = 'https://api.telegram.org/bot' . $telegram_bot_token . '/sendDocument';
    $post_fields = [
        'chat_id' => $chat_id,
        'document' => new CURLFile(realpath($file_path))
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type:multipart/form-data"]);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    $result = curl_exec($ch);
    if ($result === false) {
        file_put_contents($log_file, "cURL error: " . curl_error($ch) . "\n", FILE_APPEND);
    }
    curl_close($ch);

    return $result;
}

function sendMessage($chat_id, $message) {
    global $telegram_bot_token;

    $url = 'https://api.telegram.org/bot' . $telegram_bot_token . '/sendMessage';
    $post_fields = [
        'chat_id' => $chat_id,
        'text' => $message
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    $result = curl_exec($ch);
    curl_close($ch);

    // Decode the JSON response to extract message ID
    $response = json_decode($result, true);
    if (isset($response['result']['message_id'])) {
        return $response['result']['message_id'];
    } else {
        return false;
    }
}

function deleteMessage($chat_id, $message_id) {
    global $telegram_bot_token;

    $url = 'https://api.telegram.org/bot' . $telegram_bot_token . '/deleteMessage';
    $post_fields = [
        'chat_id' => $chat_id,
        'message_id' => $message_id
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}
?>
