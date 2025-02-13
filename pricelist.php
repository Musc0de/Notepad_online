<?php
// Daftar harga pulsa nasional
$pulsa_nasional = array(
    "5k" => 7500,
    "10k" => 10000,
    "20k" => 20000,
    "25k" => 25000,
    "50k" => 50000,
    "100k" => 100000
);

// Fungsi untuk menampilkan daftar harga pulsa nasional
function showPulsaPrices() {
    global $pulsa_nasional;
    echo "Daftar Harga Pulsa Nasional:\n";
    foreach ($pulsa_nasional as $denomination => $price) {
        echo "$denomination = Rp" . number_format($price, 0, ',', '.') . "\n";
    }
}

// Menjalankan fungsi untuk menampilkan daftar harga pulsa nasional
showPulsaPrices();
?>
