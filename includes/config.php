<?php
// ============================================
// KONFIGURASI DATABASE
// File: includes/config.php
// ============================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'manajemen_transaksi');
define('APP_NAME', 'Manajemen Transaksi');
define('APP_VERSION', '1.0.0');

// Koneksi PDO
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die(json_encode(['error' => 'Koneksi database gagal: ' . $e->getMessage()]));
}

// Helper: format rupiah
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// Helper: generate kode transaksi
function generateKodeTransaksi($pdo) {
    $tanggal = date('Ymd');
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM transaksi WHERE DATE(created_at) = CURDATE()");
    $stmt->execute();
    $row = $stmt->fetch();
    $urutan = str_pad($row['total'] + 1, 3, '0', STR_PAD_LEFT);
    return "TRX-{$tanggal}-{$urutan}";
}
