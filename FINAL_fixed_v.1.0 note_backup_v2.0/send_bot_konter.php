<?php

// Telegram bot token
$telegram_bot_token = '6778439535:AAEPWzYtsCUo7XM52BSgcGEjjUSu3_TT_gk';

// URL to fetch the price list for each command
$price_list_urls = [
    'pulsa' => 'https://onenote.my.id/pricelist.php',
    'topupkuota' => 'https://onenote.my.id/pricelist_topupkuota.php',
    'ewallet' => 'https://onenote.my.id/pricelist_ewallet.php',
    'pembayarantiket' => 'https://onenote.my.id/pricelist_pembayarantiket.php'
];

// Log file for debugging
$log_file = 'handler_log.txt';

// Read the incoming update from Telegram
$update = json_decode(file_get_contents('php://input'), true);

// Log incoming update
file_put_contents($log_file, "Update received: " . json_encode($update) . "\n", FILE_APPEND);

if (isset($update['message'])) {
    $message = $update['message'];
    $chat_id = $message['chat']['id'];
    $command = $message['text'];

    // Check if the user is entering the phone number
    if (isset($GLOBALS['awaiting_phone'][$chat_id])) {
        $phone_number = $command;
        $nominal = $GLOBALS['awaiting_nominal'][$chat_id];
        unset($GLOBALS['awaiting_phone'][$chat_id]);
        unset($GLOBALS['awaiting_nominal'][$chat_id]);

        // Here you would generate the QR code for payment
        $qr_code_url = generateQRCode($nominal); // Assuming this function generates the QR code URL

        sendMessage($chat_id, "Nomor tujuan: $phone_number\nNominal: $nominal\n\nSilakan lakukan pembayaran dengan QRIS berikut:\n$qr_code_url");
    } elseif (isset($GLOBALS['awaiting_nominal'][$chat_id])) {
        $nominal = $command;
        $GLOBALS['awaiting_nominal'][$chat_id] = $nominal;
        $GLOBALS['awaiting_phone'][$chat_id] = true;

        sendMessage($chat_id, "Anda memilih nominal sebesar $nominal. Silakan masukkan nomor telepon tujuan:");
    } else {
        // Handle each command
        switch ($command) {
            case '/pulsa':
                $price_list = fetchPriceList('pulsa');
                if ($price_list) {
                    $price_list_buttons = createPriceListButtons($price_list, 'pulsa');
                    sendMessageWithInlineButtons($chat_id, "Berikut adalah daftar harga pulsa:", $price_list_buttons);
                } else {
                    sendMessage($chat_id, "Gagal mengambil daftar harga. Silakan coba lagi nanti.");
                }
                break;
            case '/topupkuota':
                $price_list = fetchPriceList('topupkuota');
                if ($price_list) {
                    sendMessageWithInlineButtons($chat_id, "Berikut adalah daftar harga top up kuota:\n\n$price_list", [
                        ['text' => 'Membeli', 'callback_data' => 'buy_kuota'],
                        ['text' => 'Kembali ke Menu', 'callback_data' => 'menu_action']
                    ]);
                } else {
                    sendMessage($chat_id, "Gagal mengambil daftar harga. Silakan coba lagi nanti.");
                }
                break;
            case '/ewallet':
                $price_list = fetchPriceList('ewallet');
                if ($price_list) {
                    sendMessageWithInlineButtons($chat_id, "Berikut adalah daftar harga e-wallet:\n\n$price_list", [
                        ['text' => 'Membeli', 'callback_data' => 'buy_ewallet'],
                        ['text' => 'Kembali ke Menu', 'callback_data' => 'menu_action']
                    ]);
                } else {
                    sendMessage($chat_id, "Gagal mengambil daftar harga. Silakan coba lagi nanti.");
                }
                break;
            case '/pembayarantiket':
                $price_list = fetchPriceList('pembayarantiket');
                if ($price_list) {
                    sendMessageWithInlineButtons($chat_id, "Berikut adalah daftar harga pembayaran tiket online:\n\n$price_list", [
                        ['text' => 'Membeli', 'callback_data' => 'buy_tiket'],
                        ['text' => 'Kembali ke Menu', 'callback_data' => 'menu_action']
                    ]);
                } else {
                    sendMessage($chat_id, "Gagal mengambil daftar harga. Silakan coba lagi nanti.");
                }
                break;
            default:
                sendHelpMessage($chat_id);
                break;
        }
    }
} elseif (isset($update['callback_query'])) {
    $callback_query = $update['callback_query'];
    $callback_data = $callback_query['data'];
    $chat_id = $callback_query['message']['chat']['id'];
    $message_id = $callback_query['message']['message_id'];

    if (strpos($callback_data, 'buy_') === 0) {
        $action = str_replace('buy_', '', $callback_data);
        switch ($action) {
            case 'kuota':
            case 'ewallet':
            case 'tiket':
                // Here you can handle the purchase action accordingly
                handlePurchaseAction($chat_id, $action);
                break;
            default:
                // Handle unknown action
                sendMessage($chat_id, "Aksi tidak dikenal.");
                break;
        }
    } elseif ($callback_data === 'menu_action') {
        // Delete the previous message with buttons and send the main menu
        deleteMessage($chat_id, $message_id);
        sendHelpMessage($chat_id);
    }
}

function fetchPriceList($command) {
    global $price_list_urls, $log_file;

    $url = $price_list_urls[$command];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        file_put_contents($log_file, "cURL error: " . curl_error($ch) . "\n", FILE_APPEND);
        curl_close($ch);
        return false;
    }
    curl_close($ch);

    // Log the fetched price list
    file_put_contents($log_file, "Fetched price list for $command: " . $result . "\n", FILE_APPEND);

    return $result;
}

function createPriceListButtons($price_list, $command) {
    $prices = explode("\n", $price_list);
    $buttons = [];
    foreach ($prices as $price) {
        $nominal = explode(' ', $price)[0];
        $buttons[] = ['text' => $price, 'callback_data' => "buy_${command}_${nominal}"];
    }
    return array_chunk($buttons, 2);
}

function generateQRCode($nominal) {
    // For demonstration, return a dummy QR code URL
    return "https://example.com/qrcode?amount=$nominal";
}

function sendMessage($chat_id, $message) {
    global $telegram_bot_token;

    $url = 'https://api.telegram.org/bot' . $telegram_bot_token . '/sendMessage';
    $post_fields = [
        'chat_id' => $chat_id,
        'text' => $message
    ];

    sendCurlRequest($url, $post_fields);
}

function sendMessageWithInlineButtons($chat_id, $message, $buttons) {
    global $telegram_bot_token;

    $inline_keyboard = [
        'inline_keyboard' => $buttons
    ];

    $url = 'https://api.telegram.org/bot' . $telegram_bot_token . '/sendMessage';
    $post_fields = [
        'chat_id' => $chat_id,
        'text' => $message,
        'reply_markup' => json_encode($inline_keyboard)
    ];

    sendCurlRequest($url, $post_fields);
}

function editMessageWithInlineButtons($chat_id, $message_id, $message, $buttons) {
    global $telegram_bot_token;

    $inline_keyboard = [
        'inline_keyboard' => $buttons
    ];

    $url = 'https://api.telegram.org/bot' . $telegram_bot_token . '/editMessageText';
    $post_fields = [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => $message,
        'reply_markup' => json_encode($inline_keyboard)
    ];

    sendCurlRequest($url, $post_fields);
}

function deleteMessage($chat_id, $message_id) {
    global $telegram_bot_token;

    $url = 'https://api.telegram.org/bot' . $telegram_bot_token . '/deleteMessage';
    $post_fields = [
        'chat_id' => $chat_id,
        'message_id' => $message_id
    ];

    sendCurlRequest($url, $post_fields);
}


function sendHelpMessage($chat_id) {
    $message = "Selamat datang di Bot Pembelian!\n\n" .
               "Berikut adalah daftar pilihan yang tersedia:\n" .
               "1. Pulsa\n" .
               "2. Top Up Kuota\n" .
               "3. E-Wallet\n" .
               "4. Pembayaran Tiket Online\n\n" .
               "Silakan pilih salah satu pilihan dengan menekan tombol di bawah ini.";

    $buttons = [
        ['text' => 'Pulsa', 'callback_data' => '/pulsa'],
        ['text' => 'Top Up Kuota', 'callback_data' => '/topupkuota'],
        ['text' => 'E-Wallet', 'callback_data' => '/ewallet'],
        ['text' => 'Pembayaran Tiket Online', 'callback_data' => '/pembayarantiket']
    ];

    sendMessageWithInlineButtons($chat_id, $message, $buttons);
}

?>
