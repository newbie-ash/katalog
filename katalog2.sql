-- phpMyAdmin SQL Dump
-- Version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 16 Feb 2026
-- Versi server: 10.4.24-MariaDB
-- Versi PHP: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+07:00";

--
-- Database: `katalog2`
--
CREATE DATABASE IF NOT EXISTS `katalog2` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `katalog2`;

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','pembeli') NOT NULL DEFAULT 'pembeli',
  `no_hp` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `kota` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expiry` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id`, `nama`, `email`, `password`, `role`, `no_hp`, `alamat`, `kota`) VALUES
(1, 'Admin Ganteng', 'admin@gmail.com', '$2y$10$wK1y5/pXy.xXyXyXyXyXyO1234567890abcdefghij', 'admin', '081234567890', 'Kantor Pusat Matria Mart', 'Jakarta'),
(2, 'Fikri Pembeli', 'user@gmail.com', '$2y$10$wK1y5/pXy.xXyXyXyXyXyO1234567890abcdefghij', 'pembeli', '089876543210', 'Jl. Merdeka No. 45', 'Surabaya');
-- Password default: "password" (hash mungkin berbeda, sesuaikan jika perlu reset)

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `kategori`
--

INSERT INTO `kategori` (`id`, `nama_kategori`) VALUES
(1, 'Material Alam'),
(2, 'Semen & Perekat'),
(3, 'Cat & Finishing'),
(4, 'Besi & Logam'),
(5, 'Pipa & Plumbing'),
(6, 'Alat Tukang'),
(7, 'Lantai & Dinding'),
(8, 'Kayu & Triplek'),
(9, 'Atap & Plafon');

-- --------------------------------------------------------

--
-- Struktur dari tabel `produk`
--

CREATE TABLE `produk` (
  `id` int(11) NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga_ecer` int(11) NOT NULL,
  `harga_grosir` int(11) NOT NULL,
  `min_belanja_grosir` int(11) NOT NULL DEFAULT 5,
  `stok` int(11) NOT NULL DEFAULT 0,
  `satuan` varchar(50) NOT NULL DEFAULT 'Pcs',
  `gambar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `produk`
--

INSERT INTO `produk` (`id`, `id_kategori`, `nama_barang`, `deskripsi`, `harga_ecer`, `harga_grosir`, `min_belanja_grosir`, `stok`, `satuan`, `gambar`) VALUES
(1, 2, 'Semen Gresik 40kg', 'Semen PCC kemasan 40kg, kuat dan tahan retak. Cocok untuk segala bangunan.', 52000, 50000, 10, 100, 'Sak', 'prod_1766234605.jpg'),
(2, 3, 'Cat Dulux Catylac Putih 5kg', 'Cat tembok interior warna putih (Super White), anti pudar dan menutup sempurna.', 145000, 140000, 4, 25, 'Galon', 'prod_1766234581.jpg'),
(3, 4, 'Besi Beton 10mm SNI', 'Besi beton ulir full SNI diameter 10mm panjang 12 meter.', 85000, 82000, 50, 500, 'Batang', 'prod_1766324990.jpg'),
(4, 5, 'Pipa PVC Rucika 3 Inch AW', 'Pipa PVC tebal tipe AW ukuran 3 inch, panjang 4 meter.', 110000, 105000, 10, 50, 'Batang', 'prod_1766234532.jpg'),
(5, 6, 'Palu Kambing 16oz', 'Palu gagang fiber anti slip, kepala baja karbon kuat.', 35000, 32000, 6, 20, 'Pcs', 'prod_1766234568.webp'),
(6, 1, 'Pasir Lumajang (1 Truk)', 'Pasir hitam cor kualitas super, isi +/- 6 kubik per truk.', 1800000, 1750000, 2, 5, 'Truk', 'prod_1766325012.jpg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `ongkir`
--

CREATE TABLE `ongkir` (
  `id` int(11) NOT NULL,
  `nama_kota` varchar(100) NOT NULL,
  `tarif` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `ongkir`
--

INSERT INTO `ongkir` (`id`, `nama_kota`, `tarif`) VALUES
(1, 'Surabaya', 15000),
(2, 'Sidoarjo', 20000),
(3, 'Gresik', 25000),
(4, 'Jakarta', 50000),
(5, 'Malang', 35000);

-- --------------------------------------------------------

--
-- Struktur dari tabel `banner`
--

CREATE TABLE `banner` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) DEFAULT NULL,
  `gambar` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `aktif` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `banner`
--

INSERT INTO `banner` (`id`, `judul`, `gambar`, `link`, `aktif`) VALUES
(1, 'Promo Semen', 'banner_1766306230.jpg', 'index.php?kategori=2', 1),
(2, 'Diskon Akhir Tahun', 'banner_1766306375.png', 'index.php', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `keranjang`
--

CREATE TABLE `keranjang` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `qty` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pesanan`
--

CREATE TABLE `pesanan` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `tanggal` datetime DEFAULT current_timestamp(),
  `alamat_kirim` text NOT NULL,
  `total_barang` int(11) NOT NULL,
  `ongkir` int(11) NOT NULL DEFAULT 0,
  `total_bayar` int(11) NOT NULL,
  `status` enum('Pending','Menunggu Konfirmasi','Dikemas','Dikirim','Selesai','Dibatalkan') NOT NULL DEFAULT 'Pending',
  `metode_bayar` varchar(50) DEFAULT 'Transfer',
  `bukti_bayar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_pesanan`
--

CREATE TABLE `detail_pesanan` (
  `id` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `harga_deal` int(11) NOT NULL,
  `subtotal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pesan` (Chat)
--

CREATE TABLE `pesan` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `isi_pesan` text NOT NULL,
  `pengirim` enum('user','admin') NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `tanggal` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `pesan`
--

INSERT INTO `pesan` (`id`, `id_user`, `isi_pesan`, `pengirim`, `is_read`, `tanggal`) VALUES
(1, 2, 'Halo Admin, apakah Semen Gresik ready stok 50 sak?', 'user', 1, '2026-02-15 10:00:00'),
(2, 2, 'Halo Kak, barang ready siap kirim hari ini.', 'admin', 0, '2026-02-15 10:05:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `ulasan`
--

CREATE TABLE `ulasan` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `rating` int(1) NOT NULL,
  `komentar` text DEFAULT NULL,
  `tanggal` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indeks untuk tabel `ongkir`
--
ALTER TABLE `ongkir`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `banner`
--
ALTER TABLE `banner`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `keranjang`
--
ALTER TABLE `keranjang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indeks untuk tabel `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pesanan` (`id_pesanan`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indeks untuk tabel `pesan`
--
ALTER TABLE `pesan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `ulasan`
--
ALTER TABLE `ulasan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_produk` (`id_produk`),
  ADD KEY `id_pesanan` (`id_pesanan`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

ALTER TABLE `user` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
ALTER TABLE `kategori` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
ALTER TABLE `produk` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
ALTER TABLE `ongkir` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
ALTER TABLE `banner` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
ALTER TABLE `keranjang` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `pesanan` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `detail_pesanan` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `pesan` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
ALTER TABLE `ulasan` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Foreign Keys)
--

ALTER TABLE `produk`
  ADD CONSTRAINT `produk_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id`) ON DELETE CASCADE;

ALTER TABLE `keranjang`
  ADD CONSTRAINT `keranjang_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `keranjang_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id`) ON DELETE CASCADE;

ALTER TABLE `pesanan`
  ADD CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE CASCADE;

ALTER TABLE `detail_pesanan`
  ADD CONSTRAINT `detail_pesanan_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_pesanan_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id`) ON DELETE NO ACTION;

ALTER TABLE `pesan`
  ADD CONSTRAINT `pesan_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE CASCADE;

ALTER TABLE `ulasan`
  ADD CONSTRAINT `ulasan_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ulasan_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ulasan_ibfk_3` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id`) ON DELETE CASCADE;

COMMIT;