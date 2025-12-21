-- JALANKAN SQL INI DI PHPMYADMIN (DATABASE: katalog2)
-- File ini melengkapi tabel yang kurang dari file katalog2.sql sebelumnya

USE `katalog2`;

-- 1. Tabel Banner (Untuk Slider di Halaman Depan)
CREATE TABLE IF NOT EXISTS `banner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judul` varchar(255) DEFAULT NULL,
  `gambar` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `aktif` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data Dummy Banner (Pastikan Anda punya gambar dengan nama ini atau update lewat admin)
INSERT INTO `banner` (`judul`, `gambar`, `link`, `aktif`) VALUES
('Promo Semen Gresik', 'banner_1.jpg', 'produk.php?id=1', 1),
('Diskon Cat Tembok', 'banner_2.jpg', 'index.php?kategori=2', 1);


-- 2. Tabel Ongkir (Untuk Manajemen Ongkos Kirim Admin)
CREATE TABLE IF NOT EXISTS `ongkir` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kota` varchar(100) NOT NULL,
  `tarif` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data Dummy Ongkir
INSERT INTO `ongkir` (`nama_kota`, `tarif`) VALUES
('Surabaya', 15000),
('Sidoarjo', 20000),
('Gresik', 25000),
('Jakarta', 50000);


-- 3. Tabel Pesan (Untuk Fitur Chat Pelanggan - Admin)
CREATE TABLE IF NOT EXISTS `pesan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `isi_pesan` text NOT NULL,
  `pengirim` enum('user','admin') NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `tanggal` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`id_user`) REFERENCES `user`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data Dummy Pesan
INSERT INTO `pesan` (`id_user`, `isi_pesan`, `pengirim`, `is_read`, `tanggal`) VALUES
(2, 'Halo Admin, apakah Semen Gresik ready stok 50 sak?', 'user', 1, NOW()),
(2, 'Halo Kak, barang ready siap kirim hari ini.', 'admin', 0, NOW());