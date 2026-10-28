<?php
// ============================================
// API HANDLER - CRUD Transaksi & Produk
// File: api.php
// ============================================

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'includes/config.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    switch ($action) {

        // ---- DASHBOARD ----
        case 'dashboard':
            $data = [];

            // Total pemasukan bulan ini
            $stmt = $pdo->query("SELECT COALESCE(SUM(total_amount),0) as total FROM transaksi WHERE jenis_transaksi='pemasukan' AND MONTH(tanggal_transaksi)=MONTH(CURDATE()) AND status='selesai'");
            $data['pemasukan_bulan'] = $stmt->fetch()['total'];

            // Total pengeluaran bulan ini
            $stmt = $pdo->query("SELECT COALESCE(SUM(total_amount),0) as total FROM transaksi WHERE jenis_transaksi='pengeluaran' AND MONTH(tanggal_transaksi)=MONTH(CURDATE()) AND status='selesai'");
            $data['pengeluaran_bulan'] = $stmt->fetch()['total'];

            // Saldo
            $data['saldo'] = $data['pemasukan_bulan'] - $data['pengeluaran_bulan'];

            // Total transaksi hari ini
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM transaksi WHERE DATE(tanggal_transaksi)=CURDATE()");
            $data['transaksi_hari'] = $stmt->fetch()['total'];

            // Total produk
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM produk");
            $data['total_produk'] = $stmt->fetch()['total'];

            // Transaksi pending
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM transaksi WHERE status='pending'");
            $data['transaksi_pending'] = $stmt->fetch()['total'];

            // Grafik 7 hari terakhir
            $stmt = $pdo->query("
                SELECT tanggal_transaksi as tgl,
                    SUM(CASE WHEN jenis_transaksi='pemasukan' THEN total_amount ELSE 0 END) as pemasukan,
                    SUM(CASE WHEN jenis_transaksi='pengeluaran' THEN total_amount ELSE 0 END) as pengeluaran
                FROM transaksi
                WHERE tanggal_transaksi >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                  AND status = 'selesai'
                GROUP BY tanggal_transaksi
                ORDER BY tanggal_transaksi ASC
            ");
            $data['grafik'] = $stmt->fetchAll();

            // Transaksi terbaru
            $stmt = $pdo->query("SELECT * FROM transaksi ORDER BY created_at DESC LIMIT 5");
            $data['transaksi_terbaru'] = $stmt->fetchAll();

            // Top Produk Terlaris Bulan Ini
            $stmt = $pdo->query("
                SELECT
                    p.nama_produk,
                    SUM(dt.jumlah) AS total_terjual
                FROM detail_transaksi dt
                INNER JOIN produk p ON p.id = dt.produk_id
                INNER JOIN transaksi t ON t.id = dt.transaksi_id
                WHERE t.jenis_transaksi = 'pemasukan'
                AND t.status = 'selesai'
                AND MONTH(t.tanggal_transaksi) = MONTH(CURDATE())
                AND YEAR(t.tanggal_transaksi) = YEAR(CURDATE())
                GROUP BY p.id, p.nama_produk
                ORDER BY total_terjual DESC
                LIMIT 5
            ");

$data['top_produk'] = $stmt->fetchAll();

// Stok Menipis
$stmt = $pdo->query("
    SELECT nama_produk, stok
    FROM produk
    WHERE stok < 5
    ORDER BY stok ASC
");

$data['stok_menipis'] = $stmt->fetchAll();

            echo json_encode(['success' => true, 'data' => $data]);
            break;

        // ---- TRANSAKSI LIST ----
        case 'get_transaksi':
            $where = "WHERE 1=1";
            $params = [];
            if (!empty($_GET['jenis'])) {
                $where .= " AND jenis_transaksi = ?";
                $params[] = $_GET['jenis'];
            }
            if (!empty($_GET['status'])) {
                $where .= " AND status = ?";
                $params[] = $_GET['status'];
            }
            if (!empty($_GET['cari'])) {
                $where .= " AND (kode_transaksi LIKE ? OR keterangan LIKE ?)";
                $params[] = "%{$_GET['cari']}%";
                $params[] = "%{$_GET['cari']}%";
            }
            $stmt = $pdo->prepare("SELECT * FROM transaksi $where ORDER BY created_at DESC LIMIT 100");
            $stmt->execute($params);
            echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
            break;

        // ---- TRANSAKSI DETAIL ----
        case 'get_transaksi_detail':
            $id = intval($_GET['id']);
            $stmt = $pdo->prepare("SELECT t.*, GROUP_CONCAT(p.nama_produk SEPARATOR ', ') as produk_list FROM transaksi t LEFT JOIN detail_transaksi dt ON t.id=dt.transaksi_id LEFT JOIN produk p ON dt.produk_id=p.id WHERE t.id=? GROUP BY t.id");
            $stmt->execute([$id]);
            $transaksi = $stmt->fetch();

            $stmt2 = $pdo->prepare("SELECT dt.*, p.nama_produk, p.kode_produk, p.satuan FROM detail_transaksi dt JOIN produk p ON dt.produk_id=p.id WHERE dt.transaksi_id=?");
            $stmt2->execute([$id]);
            $detail = $stmt2->fetchAll();

            echo json_encode(['success' => true, 'transaksi' => $transaksi, 'detail' => $detail]);
            break;

        // ---- SIMPAN TRANSAKSI ----
        case 'simpan_transaksi':
            if ($method !== 'POST') { echo json_encode(['success'=>false,'message'=>'Method not allowed']); break; }
            $body = json_decode(file_get_contents('php://input'), true);

            $kode = generateKodeTransaksi($pdo);
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO transaksi (kode_transaksi, tanggal_transaksi, jenis_transaksi, keterangan, total_amount, status, created_by) VALUES (?,?,?,?,?,?,?)");
            $stmt->execute([
                $kode,
                $body['tanggal'],
                $body['jenis'],
                $body['keterangan'],
                $body['total'],
                $body['status'] ?? 'selesai',
                $body['created_by'] ?? 'Admin'
            ]);
            $transaksi_id = $pdo->lastInsertId();

            if (!empty($body['items'])) {
                $stmtD = $pdo->prepare("INSERT INTO detail_transaksi (transaksi_id, produk_id, jumlah, harga_satuan, subtotal) VALUES (?,?,?,?,?)");
                foreach ($body['items'] as $item) {
                    $subtotal = $item['jumlah'] * $item['harga'];
                    $stmtD->execute([$transaksi_id, $item['produk_id'], $item['jumlah'], $item['harga'], $subtotal]);
                    // Update stok
                    if ($body['jenis'] === 'pengeluaran') {
                        $pdo->prepare("UPDATE produk SET stok = stok + ? WHERE id = ?")->execute([$item['jumlah'], $item['produk_id']]);
                    } else {
                        $pdo->prepare("UPDATE produk SET stok = stok - ? WHERE id = ?")->execute([$item['jumlah'], $item['produk_id']]);
                    }
                }
            }

            $pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Transaksi berhasil disimpan', 'kode' => $kode]);
            break;

        // ---- UPDATE STATUS ----
        case 'update_status':
            if ($method !== 'POST') { echo json_encode(['success'=>false,'message'=>'Method not allowed']); break; }
            $body = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare("UPDATE transaksi SET status=? WHERE id=?");
            $stmt->execute([$body['status'], $body['id']]);
            echo json_encode(['success' => true, 'message' => 'Status diperbarui']);
            break;

        // ---- HAPUS TRANSAKSI ----
        case 'hapus_transaksi':
            if ($method !== 'POST') { echo json_encode(['success'=>false,'message'=>'Method not allowed']); break; }
            $body = json_decode(file_get_contents('php://input'), true);
            $pdo->prepare("DELETE FROM transaksi WHERE id=?")->execute([$body['id']]);
            echo json_encode(['success' => true, 'message' => 'Transaksi dihapus']);
            break;

        // ---- GET PRODUK ----
        case 'get_produk':
            $stmt = $pdo->query("SELECT p.*, k.nama_kategori FROM produk p LEFT JOIN kategori k ON p.kategori_id=k.id ORDER BY p.nama_produk ASC");
            echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
            break;

        // ---- SIMPAN PRODUK ----
        case 'simpan_produk':
            if ($method !== 'POST') { echo json_encode(['success'=>false,'message'=>'Method not allowed']); break; }
            $body = json_decode(file_get_contents('php://input'), true);
            if (!empty($body['id'])) {
                $stmt = $pdo->prepare("UPDATE produk SET nama_produk=?, kategori_id=?, harga=?, stok=?, satuan=? WHERE id=?");
                $stmt->execute([$body['nama'], $body['kategori_id'], $body['harga'], $body['stok'], $body['satuan'], $body['id']]);
                echo json_encode(['success' => true, 'message' => 'Produk diperbarui']);
            } else {
                $stmt = $pdo->prepare("INSERT INTO produk (kode_produk, nama_produk, kategori_id, harga, stok, satuan) VALUES (?,?,?,?,?,?)");
                $kode = 'PRD' . str_pad(rand(100,999), 3, '0');
                $stmt->execute([$kode, $body['nama'], $body['kategori_id'], $body['harga'], $body['stok'], $body['satuan']]);
                echo json_encode(['success' => true, 'message' => 'Produk ditambahkan']);
            }
            break;

        // ---- HAPUS PRODUK ----
        case 'hapus_produk':
            if ($method !== 'POST') { echo json_encode(['success'=>false,'message'=>'Method not allowed']); break; }
            $body = json_decode(file_get_contents('php://input'), true);
            $pdo->prepare("DELETE FROM produk WHERE id=?")->execute([$body['id']]);
            echo json_encode(['success' => true, 'message' => 'Produk dihapus']);
            break;

        // ---- GET KATEGORI ----
        case 'get_kategori':
            $stmt = $pdo->query("SELECT * FROM kategori ORDER BY nama_kategori");
            echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
            break;

        // ---- LAPORAN ----
        case 'laporan':
            $bulan = $_GET['bulan'] ?? date('m');
            $tahun = $_GET['tahun'] ?? date('Y');

            $stmt = $pdo->prepare("
                SELECT DATE_FORMAT(tanggal_transaksi,'%d %b') as tgl,
                    jenis_transaksi, SUM(total_amount) as total, COUNT(*) as jml
                FROM transaksi
                WHERE MONTH(tanggal_transaksi)=? AND YEAR(tanggal_transaksi)=? AND status='selesai'
                GROUP BY tanggal_transaksi, jenis_transaksi
                ORDER BY tanggal_transaksi ASC
            ");
            $stmt->execute([$bulan, $tahun]);
            echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Action tidak dikenal']);
    }
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
