-- ============================================
-- DATABASE: Manajemen Transaksi
-- ============================================

CREATE DATABASE IF NOT EXISTS manajemen_transaksi
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE manajemen_transaksi;

-- Tabel Kategori
CREATE TABLE IF NOT EXISTS kategori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Produk
CREATE TABLE IF NOT EXISTS produk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_produk VARCHAR(20) UNIQUE NOT NULL,
    nama_produk VARCHAR(150) NOT NULL,
    kategori_id INT,
    harga DECIMAL(15,2) NOT NULL DEFAULT 0,
    stok INT NOT NULL DEFAULT 0,
    satuan VARCHAR(20) DEFAULT 'pcs',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE SET NULL
);

-- Tabel Transaksi
CREATE TABLE IF NOT EXISTS transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_transaksi VARCHAR(30) UNIQUE NOT NULL,
    tanggal_transaksi DATE NOT NULL,
    jenis_transaksi ENUM('pemasukan','pengeluaran') NOT NULL,
    keterangan TEXT,
    total_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
    status ENUM('pending','selesai','batal') DEFAULT 'pending',
    created_by VARCHAR(100) DEFAULT 'Admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Detail Transaksi
CREATE TABLE IF NOT EXISTS detail_transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaksi_id INT NOT NULL,
    produk_id INT NOT NULL,
    jumlah INT NOT NULL DEFAULT 1,
    harga_satuan DECIMAL(15,2) NOT NULL,
    subtotal DECIMAL(15,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (transaksi_id) REFERENCES transaksi(id) ON DELETE CASCADE,
    FOREIGN KEY (produk_id) REFERENCES produk(id) ON DELETE RESTRICT
);

-- ============================================
-- DATA AWAL (SEED DATA)
-- ============================================

INSERT INTO kategori (nama_kategori, deskripsi) VALUES
('Elektronik', 'Produk elektronik dan gadget'),
('Makanan & Minuman', 'Produk konsumsi harian'),
('Pakaian', 'Produk fashion dan tekstil'),
('Alat Tulis', 'Perlengkapan kantor dan sekolah');

INSERT INTO produk (kode_produk, nama_produk, kategori_id, harga, stok, satuan) VALUES
('PRD001', 'Laptop ASUS VivoBook', 1, 7500000, 10, 'unit'),
('PRD002', 'Mouse Wireless Logitech', 1, 250000, 50, 'pcs'),
('PRD003', 'Keyboard Mechanical', 1, 450000, 30, 'pcs'),
('PRD004', 'Air Mineral 600ml', 2, 5000, 200, 'botol'),
('PRD005', 'Kopi Sachet Box', 2, 45000, 100, 'box'),
('PRD006', 'Kaos Polos Cotton', 3, 85000, 75, 'pcs'),
('PRD007', 'Kemeja Formal', 3, 195000, 40, 'pcs'),
('PRD008', 'Pulpen Pilot G2', 4, 15000, 150, 'pcs'),
('PRD009', 'Buku Tulis Kiky', 4, 12000, 200, 'pcs'),
('PRD010', 'Spidol Whiteboard', 4, 35000, 80, 'pcs');

INSERT INTO transaksi (kode_transaksi, tanggal_transaksi, jenis_transaksi, keterangan, total_amount, status) VALUES
('TRX-20240101-001', '2024-01-01', 'pemasukan', 'Penjualan produk elektronik', 8200000, 'selesai'),
('TRX-20240101-002', '2024-01-01', 'pengeluaran', 'Pembelian stok alat tulis', 500000, 'selesai'),
('TRX-20240102-001', '2024-01-02', 'pemasukan', 'Penjualan pakaian', 280000, 'selesai'),
('TRX-20240103-001', '2024-01-03', 'pemasukan', 'Penjualan minuman', 50000, 'pending');

INSERT INTO detail_transaksi (transaksi_id, produk_id, jumlah, harga_satuan, subtotal) VALUES
(1, 1, 1, 7500000, 7500000),
(1, 2, 2, 250000, 500000),
(1, 3, 0, 450000, 200000),
(2, 8, 10, 15000, 150000),
(2, 9, 20, 12000, 240000),
(3, 6, 2, 85000, 170000),
(3, 7, 1, 195000, 195000),
(4, 4, 10, 5000, 50000);
