-- 1. Buat Database Baru
DROP DATABASE IF EXISTS `katalog2`;
CREATE DATABASE `katalog2`;
USE `katalog2`;

-- 2. Tabel User
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','pembeli') NOT NULL DEFAULT 'pembeli',
  `alamat` text DEFAULT NULL,
  `kota` varchar(100) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Tabel Kategori
CREATE TABLE `kategori` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Masukkan Data Kategori
INSERT INTO `kategori` (`nama_kategori`) VALUES 
('Semen & Pasir'),      -- ID 1
('Cat & Pelapis'),      -- ID 2
('Peralatan Tukang'),   -- ID 3
('Batu & Bata'),        -- ID 4
('Pipa & Plumbing'),    -- ID 5
('Keramik & Lantai'),   -- ID 6
('Listrik & Lampu'),    -- ID 7
('Atap & Genteng');     -- ID 8

-- 4. Tabel Produk
CREATE TABLE `produk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_kategori` int(11) NOT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL,
  `harga_ecer` decimal(10,2) NOT NULL,
  `harga_grosir` decimal(10,2) NOT NULL,
  `min_belanja_grosir` int(11) NOT NULL,
  `stok` int(11) NOT NULL,
  `satuan` varchar(50) NOT NULL DEFAULT 'pcs',
  `gambar` varchar(255) DEFAULT NULL,
  `terjual` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`id_kategori`) REFERENCES `kategori`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- MASUKKAN SAMPEL PRODUK (Data Dummy)
INSERT INTO `produk` (`id_kategori`, `nama_barang`, `deskripsi`, `harga_ecer`, `harga_grosir`, `min_belanja_grosir`, `stok`, `satuan`, `gambar`, `terjual`) VALUES
-- Kategori 1: Semen & Pasir
(1, 'Semen Gresik 40kg', 'Semen PCC kualitas premium untuk bangunan kokoh. Cocok untuk plesteran dan acian.', 58000, 56000, 10, 200, 'zak', 'semen_gresik.jpg', 50),
(1, 'Semen Tiga Roda 40kg', 'Semen serbaguna dengan kuat tekan tinggi. Standar SNI.', 57000, 55500, 10, 150, 'zak', 'semen_tigaroda.jpg', 42),
(1, 'Pasir Lumajang (1 Pick Up)', 'Pasir hitam kualitas super, tidak mengandung lumpur. Cocok untuk cor beton.', 350000, 330000, 3, 20, 'pickup', 'pasir_lumajang.jpg', 5),

-- Kategori 2: Cat & Pelapis
(2, 'Cat Tembok Dulux Catylac 5kg Putih', 'Cat tembok interior dengan teknologi Chroma Brite, warna lebih cerah dan tahan lama.', 145000, 140000, 4, 30, 'kaleng', 'dulux_catylac.jpg', 12),
(2, 'Cat No Drop Pelapis Anti Bocor 4kg', 'Cat pelapis anti bocor yang elastis, kedap air, dan tahan cuaca. Warna Abu-abu.', 185000, 178000, 4, 25, 'kaleng', 'nodrop.jpg', 20),
(2, 'Avian Cat Kayu & Besi 1kg', 'Cat kayu dan besi mengkilap, cepat kering dan daya tutup maksimal.', 65000, 60000, 12, 100, 'kaleng', 'avian_kayu.jpg', 88),

-- Kategori 3: Peralatan Tukang
(3, 'Palu Kambing Gagang Karet 16oz', 'Palu tukang dengan gagang karet anti slip, nyaman digenggam dan kuat.', 45000, 38000, 6, 50, 'pcs', 'palu_kambing.jpg', 15),
(3, 'Meteran Dorong 50 Meter', 'Meteran jalan/dorong untuk mengukur tanah atau bangunan luas.', 120000, 110000, 3, 10, 'unit', 'meteran.jpg', 2),
(3, 'Cangkul Buaya Asli', 'Cangkul baja asli super tajam dan kuat untuk menggali tanah keras.', 85000, 80000, 5, 40, 'pcs', 'cangkul.jpg', 10),

-- Kategori 4: Batu & Bata
(4, 'Bata Merah Press Jumbo (1000 pcs)', 'Bata merah ukuran besar, pembakaran matang sempurna. Harga per 1000 pcs.', 750000, 720000, 2, 50, 'paket', 'bata_merah.jpg', 8),
(4, 'Bata Ringan Hebel 10cm', 'Bata ringan / hebel tebal 10cm. Presisi dan mempercepat pembangunan.', 650000, 630000, 5, 100, 'kubik', 'hebel.jpg', 25),

-- Kategori 5: Pipa & Plumbing
(5, 'Pipa PVC Rucika 3/4 inch AW', 'Pipa air standar JIS, tebal dan kuat untuk saluran air bertekanan.', 35000, 32000, 10, 200, 'batang', 'pipa_rucika.jpg', 120),
(5, 'Kran Air Tembok Onda 1/2 inch', 'Kran air bahan babet finishing chrome, anti karat dan awet.', 25000, 22500, 12, 150, 'pcs', 'kran_onda.jpg', 60),

-- Kategori 6: Keramik & Lantai
(6, 'Keramik Lantai Platinum 40x40 Putih', 'Keramik lantai motif marmer putih, permukaan glossy. 1 Dus isi 6 keping.', 55000, 52000, 10, 80, 'dus', 'keramik_platinum.jpg', 40),
(6, 'Granit Roman 60x60 Cream Polos', 'Granit tile kualitas ekspor, double loading. Mewah dan elegan.', 180000, 175000, 10, 40, 'dus', 'granit_roman.jpg', 15),

-- Kategori 7: Listrik & Lampu
(7, 'Lampu LED Philips 12 Watt Putih', 'Lampu LED hemat energi, cahaya terang dan tahan hingga 15 tahun.', 45000, 42000, 12, 100, 'pcs', 'lampu_philips.jpg', 200),
(7, 'Kabel Eterna NYM 2x1.5 (50 Meter)', 'Kabel listrik kawat tembaga murni standar PLN/SNI.', 320000, 310000, 3, 20, 'roll', 'kabel_eterna.jpg', 5),

-- Kategori 8: Atap
(8, 'Seng Galvalum 0.3mm (Per Meter)', 'Atap seng galvalum anti karat, lebar 80cm. Harga per meter lari.', 45000, 42000, 20, 500, 'meter', 'galvalum.jpg', 150);


-- 5. Tabel Keranjang
CREATE TABLE `keranjang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`id_user`) REFERENCES `user`(`id`),
  FOREIGN KEY (`id_produk`) REFERENCES `produk`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Tabel Pesanan
CREATE TABLE `pesanan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `tanggal` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `alamat_kirim` text NOT NULL,
  `total_barang` int(11) NOT NULL,
  `total_bayar` decimal(10,2) NOT NULL,
  `status` enum('Pending','Menunggu Konfirmasi','Dikemas','Dikirim','Selesai','Dibatalkan') NOT NULL DEFAULT 'Pending',
  `metode_bayar` varchar(50) NOT NULL DEFAULT 'Transfer',
  `bukti_bayar` varchar(255) DEFAULT NULL,
  `resi` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`id_user`) REFERENCES `user`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Tabel Detail Pesanan
CREATE TABLE `detail_pesanan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_pesanan` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `harga_deal` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan`(`id`),
  FOREIGN KEY (`id_produk`) REFERENCES `produk`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Tabel Ulasan
CREATE TABLE `ulasan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `rating` int(1) NOT NULL,
  `komentar` text,
  `tanggal` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`id_user`) REFERENCES `user`(`id`),
  FOREIGN KEY (`id_produk`) REFERENCES `produk`(`id`),
  FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert Admin (Pass: admin123)
INSERT INTO `user` (`nama`, `email`, `password`, `role`, `no_hp`) VALUES 
('Super Admin', 'admin@toko.com', '$2y$10$R9h/cIPz0gi.URNNXRim.e.M0/6X.0yX0j.a.k.z.l.m.n.o.p', 'admin', '08123456789');

-- Insert Pembeli Dummy (Pass: user123)
INSERT INTO `user` (`nama`, `email`, `password`, `role`, `no_hp`, `alamat`, `kota`) VALUES 
('Budi Santoso', 'budi@gmail.com', '$2y$10$R9h/cIPz0gi.URNNXRim.e.M0/6X.0yX0j.a.k.z.l.m.n.o.p', 'pembeli', '08567890123', 'Jl. Merpati No. 10', 'Surabaya');