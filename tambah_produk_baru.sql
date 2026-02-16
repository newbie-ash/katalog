-- 1. Pastikan Kategori Tersedia (INSERT IGNORE agar tidak error jika sudah ada)
INSERT IGNORE INTO `kategori` (`id`, `nama_kategori`) VALUES
(1, 'Material Alam'),
(2, 'Semen & Perekat'),
(3, 'Cat & Finishing'),
(4, 'Besi & Logam'),
(5, 'Pipa & Plumbing'),
(6, 'Alat Tukang'),
(7, 'Lantai & Dinding'),
(8, 'Kayu & Triplek'),
(9, 'Atap & Plafon');

-- 2. Tambahkan Produk Baru
INSERT INTO `produk` (`id_kategori`, `nama_barang`, `deskripsi`, `harga_ecer`, `harga_grosir`, `min_belanja_grosir`, `stok`, `satuan`, `gambar`) VALUES
(1, 'Pasir Lumajang (1 Truk)', 'Pasir hitam kualitas super dari Lumajang, butiran kasar dan tajam. Sangat cocok untuk cor beton bertulang dan plesteran dinding.', 1800000, 1750000, 2, 10, 'Truk', 'pasir_lumajang.jpg'),

(4, 'Besi Beton SNI 10mm (Ulir)', 'Besi beton ulir full SNI diameter 10mm, panjang 12 meter standar. Kuat untuk tiang kolom rumah 2 lantai.', 85000, 82000, 50, 500, 'Batang', 'besi_10mm.jpg'),

(7, 'Keramik Asia Tile 40x40 Putih', 'Keramik lantai motif polos putih, ukuran 40x40 cm. Permukaan mengkilap (glossy). 1 dus isi 6 keping (1 meter persegi).', 55000, 52000, 20, 100, 'Dus', 'keramik_asia_putih.jpg'),

(6, 'Kuas Eterna 3 Inch', 'Kuas cat tembok bahan bulu halus, tidak mudah rontok, gagang kayu nyaman digenggam. Bisa untuk cat minyak maupun cat tembok.', 12000, 10000, 12, 200, 'Pcs', 'kuas_3inch.jpg'),

(5, 'Tandon Air Penguin 500L', 'Tangki air plastik teknologi Rotamould 4, anti lumut, anti bakteri, garansi 10 tahun. Warna oranye. Gratis plumbing kit.', 1250000, 1200000, 3, 15, 'Unit', 'tandon_500l.jpg'),

(8, 'Triplek Meranti 12mm', 'Triplek kayu meranti tebal 12mm (banci), ukuran standar 122x244 cm. Permukaan halus, cocok untuk furniture atau bekisting.', 145000, 140000, 10, 50, 'Lembar', 'triplek_12mm.jpg'),

(9, 'Seng Gelombang 0.3mm (1.8m)', 'Seng atap gelombang galvanis tebal 0.3mm panjang 1.8 meter. Tahan karat dan cuaca panas.', 65000, 62000, 20, 300, 'Lembar', 'seng_gelombang.jpg'),

(6, 'Gergaji Kayu Camel 18 Inch', 'Gergaji tangan tajam siap pakai ukuran 18 inch. Mata gergaji sudah diasah, gagang plastik lapis karet.', 45000, 40000, 6, 40, 'Pcs', 'gergaji_kayu.jpg'),

(3, 'Thinner A Special Cobra', 'Thinner pengencer cat minyak dan duco kualitas super. Hasil kilap maksimal dan cepat kering. Kemasan kaleng 1 liter.', 28000, 25000, 12, 60, 'Kaleng', 'thinner_cobra.jpg'),

(2, 'Lem Rajawali 1kg (Plamir)', 'Lem putih PVAc kemasan kantong 1kg. Biasa digunakan untuk campuran plamir tembok, lem kayu, dan kertas.', 30000, 28000, 12, 80, 'Bks', 'lem_rajawali.jpg'),

(4, 'Paku Kayu Campur (1kg)', 'Paku kayu ukuran campur (5cm, 7cm, 10cm). Dijual per kilogram.', 18000, 16000, 10, 100, 'Kg', 'paku_campur.jpg'),

(5, 'Kran Air PVC 1/2 Inch', 'Kran air tembok bahan PVC tebal, putaran enteng, anti karat. Ukuran drat 1/2 inch standar rumah tangga.', 15000, 12500, 24, 150, 'Pcs', 'kran_pvc.jpg');