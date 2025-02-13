<?php
require_once '../database.php'; // Sesuaikan dengan jalur file database Anda

// Tangani form untuk mengaktifkan atau menonaktifkan mode pemeliharaan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $maintenance_mode = isset($_POST['maintenance_mode']) ? 1 : 0;
    
    // Update pengaturan mode pemeliharaan di database
    $stmt = $pdo->prepare("UPDATE settings SET maintenance_mode = ? WHERE id = 1");
    $stmt->execute([$maintenance_mode]);
    
    echo "Pengaturan berhasil diperbarui.";
}

// Ambil status mode pemeliharaan dari database
$stmt = $pdo->query("SELECT maintenance_mode FROM settings WHERE id = 1");
$setting = $stmt->fetch();
$maintenance_mode = $setting['maintenance_mode'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Admin</title>
</head>
<body>
    <h1>Pengaturan Admin</h1>
    <form method="post">
        <label>
            <input type="checkbox" name="maintenance_mode" <?php echo $maintenance_mode ? 'checked' : ''; ?>>
            Aktifkan Mode Pemeliharaan
        </label>
        <button type="submit">Simpan</button>
    </form>
</body>
</html>
