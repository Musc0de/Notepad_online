<?php
// Default Base URL
$base_url = 'https://dreamnote.biz.id/'; // Base URL default adalah kosong
// Fungsi untuk menyimpan Base URL baru ke file config.php
function setBaseUrl($newBaseUrl) {
    global $configFile;
    // Menulis ulang file config.php dengan Base URL baru
    $configContent = "<?php\n\$base_url = '" . addslashes($newBaseUrl) . "';\n";
    file_put_contents($configFile, $configContent, LOCK_EX); // Menyimpan file secara aman
}

// Memproses data form ketika tombol Save ditekan
if (isset($_POST['update_base_url'])) {
    $newBaseUrl = trim($_POST['base_url']); // Menghapus spasi di awal/akhir input
    setBaseUrl($newBaseUrl); // Simpan URL baru ke config.php
    $currentBaseUrl = $newBaseUrl; // Update nilai Base URL saat ini

    // Tampilkan pesan sukses menggunakan SweetAlert
    echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: 'Base URL has been updated.'
        });
    </script>";
}
