-- Database: katalog


SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------

--
-- 1. TABEL USER
-- Fungsi: Data pengguna (Admin & Pembeli)
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `alamat` text,
  `kota` varchar(100) DEFAULT NULL,
  `role` enum('admin','pembeli') NOT NULL DEFAULT 'pembeli',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `user` (`nama`, `email`, `password`, `role`) VALUES
('Admin Toko', 'admin@toko.com', '12345', 'admin'),
('Budi Santoso', 'budi@gmail.com', '12345', 'pembeli');

-- --------------------------------------------------------

--
-- 2. TABEL SUPPLIER
-- Fungsi: Data pemasok barang (untuk restock)
--

CREATE TABLE `supplier` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_supplier` varchar(100) NOT NULL,
  `kontak_hp` varchar(20) NOT NULL,
  `alamat_gudang` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `supplier` (`nama_supplier`, `kontak_hp`, `alamat_gudang`) VALUES
('PT Semen Indonesia', '08123456789', 'Gudang Pusat Gresik'),
('CV Besi Baja Jaya', '08987654321', 'Kawasan Industri Jakarta');

-- --------------------------------------------------------

--
-- 3. TABEL KATEGORI
--

CREATE TABLE `kategori` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `kategori` (`nama_kategori`) VALUES
('Semen'), ('Besi & Baja'), ('Cat & Finishing');

-- --------------------------------------------------------

--
-- 4. TABEL PRODUK
--

CREATE TABLE `produk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_kategori` int(11) NOT NULL,
  `id_supplier` int(11) DEFAULT NULL,
  `nama_barang` varchar(200) NOT NULL,
  `satuan` varchar(50) NOT NULL,
  `berat` int(11) NOT NULL DEFAULT 1000 COMMENT 'Dalam gram (gr)',
  `stok` int(11) NOT NULL DEFAULT 0,
  
  -- Harga --
  `harga_ecer` int(11) NOT NULL,
  `harga_grosir` int(11) NOT NULL,
  `min_belanja_grosir` int(11) NOT NULL DEFAULT 10,
  
  `gambar` varchar(255) DEFAULT 'default.jpg',
  `deskripsi` text,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id`),
  FOREIGN KEY (`id_supplier`) REFERENCES `supplier` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `produk` (`id_kategori`, `id_supplier`, `nama_barang`, `satuan`, `berat`, `stok`, `harga_ecer`, `harga_grosir`, `min_belanja_grosir`) VALUES
(1, 1, 'Semen Gresik 50kg', 'Sak', 50000, 100, 65000, 62000, 10),
(3, NULL, 'Cat Dulux Putih 5kg', 'Kaleng', 5000, 50, 150000, 140000, 5);

-- --------------------------------------------------------

--
-- 5. TABEL REKENING
-- Fungsi: Data rekening toko.
--

CREATE TABLE `rekening` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_bank` varchar(50) NOT NULL,
  `no_rek` varchar(50) NOT NULL,
  `atas_nama` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `rekening` (`nama_bank`, `no_rek`, `atas_nama`) VALUES
('BCA', '123456789', 'Toko Bangunan Sejahtera'),
('BRI', '987654321', 'Budi Santoso (Owner)');

-- --------------------------------------------------------

--
-- 6. TABEL KERANJANG (BARU - PENTING)
-- Fungsi: Menyimpan barang sementara sebelum checkout.
--

CREATE TABLE `keranjang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 7. TABEL PESANAN
-- Perbaikan: Menambahkan relasi ke tabel REKENING dan metode pembayaran
--

CREATE TABLE `pesanan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_rekening` int(11) DEFAULT NULL COMMENT 'Pembeli transfer ke bank mana (jika Transfer)',
  `tanggal` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  
  -- Info Pengiriman --
  `alamat_kirim` text NOT NULL,
  `ekspedisi` varchar(50) DEFAULT NULL,
  `ongkir` int(11) NOT NULL DEFAULT 0,
  
  -- Info Pembayaran --
  `metode_bayar` enum('Transfer','COD','Tempo') NOT NULL DEFAULT 'Transfer',
  `total_barang` int(11) NOT NULL,
  `total_bayar` int(11) NOT NULL,
  `bukti_bayar` varchar(255) DEFAULT NULL,
  
  `status` enum('Pending','Lunas','Dikirim','Selesai','Batal') NOT NULL DEFAULT 'Pending',
  PRIMARY KEY (`id`),
  FOREIGN KEY (`id_user`) REFERENCES `user` (`id`),
  FOREIGN KEY (`id_rekening`) REFERENCES `rekening` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 8. TABEL DETAIL PESANAN
--

CREATE TABLE `detail_pesanan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_pesanan` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `harga_deal` int(11) NOT NULL,
  `subtotal` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;