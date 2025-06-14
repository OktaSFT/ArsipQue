-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 13, 2025 at 11:59 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `arsipku`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_dokumen_filter` (IN `p_kategori_id` INT, IN `p_tanggal_mulai` DATE, IN `p_tanggal_selesai` DATE, IN `p_search` VARCHAR(255), IN `p_limit` INT, IN `p_offset` INT)   BEGIN
    SET @sql = 'SELECT d.*, k.nama_kategori 
                FROM dokumen d 
                JOIN kategori k ON d.kategori_id = k.id 
                WHERE 1=1';
    
    IF p_kategori_id IS NOT NULL AND p_kategori_id > 0 THEN
        SET @sql = CONCAT(@sql, ' AND d.kategori_id = ', p_kategori_id);
    END IF;
    
    IF p_tanggal_mulai IS NOT NULL THEN
        SET @sql = CONCAT(@sql, ' AND DATE(d.uploaded_at) >= "', p_tanggal_mulai, '"');
    END IF;
    
    IF p_tanggal_selesai IS NOT NULL THEN
        SET @sql = CONCAT(@sql, ' AND DATE(d.uploaded_at) <= "', p_tanggal_selesai, '"');
    END IF;
    
    IF p_search IS NOT NULL AND p_search != '' THEN
        SET @sql = CONCAT(@sql, ' AND (d.nama_dokumen LIKE "%', p_search, '%" OR d.deskripsi LIKE "%', p_search, '%")');
    END IF;
    
    SET @sql = CONCAT(@sql, ' ORDER BY d.uploaded_at DESC');
    
    IF p_limit IS NOT NULL AND p_limit > 0 THEN
        SET @sql = CONCAT(@sql, ' LIMIT ', p_limit);
        IF p_offset IS NOT NULL AND p_offset > 0 THEN
            SET @sql = CONCAT(@sql, ' OFFSET ', p_offset);
        END IF;
    END IF;
    
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$

--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `hitung_dokumen_per_kategori` (`kategori_id_param` INT) RETURNS INT DETERMINISTIC READS SQL DATA BEGIN
    DECLARE jumlah INT DEFAULT 0;
    SELECT COUNT(*) INTO jumlah 
    FROM dokumen 
    WHERE kategori_id = kategori_id_param;
    RETURN jumlah;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `nama`, `created_at`) VALUES
(1, 'admin', '$2y$10$J7CliYOck/J/nG767FOs5e2RuRDVhdcus91x/3n0N6BIAoKG92BVC', 'Administrator', '2025-06-13 20:06:42');

-- --------------------------------------------------------

--
-- Table structure for table `dokumen`
--

CREATE TABLE `dokumen` (
  `id` int NOT NULL,
  `nama_dokumen` varchar(255) NOT NULL,
  `kategori_id` int NOT NULL,
  `deskripsi` text,
  `nama_file` varchar(255) NOT NULL,
  `ukuran_file` int NOT NULL,
  `tipe_file` varchar(50) NOT NULL,
  `path_file` varchar(500) NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `dokumen`
--

INSERT INTO `dokumen` (`id`, `nama_dokumen`, `kategori_id`, `deskripsi`, `nama_file`, `ukuran_file`, `tipe_file`, `path_file`, `uploaded_at`) VALUES
(2, 'memories', 6, 'sebuah foto yang mengandung memori berharga selama study lapangan', 'IMG_20250530_044622.jpg', 1104840, 'image/jpeg', 'uploads/2025-06-13_20-18-06_684c877e818d0.jpg', '2025-06-13 20:18:06'),
(4, 'jeepmerahpride', 4, 'ALTER TABLE log_arsip MODIFY COLUMN aksi ENUM(\'DELETE\', \'ADD\', \'UPDATE\') NOT NULL;', 'WhatsApp Image 2025-06-14 at 04.17.22_6786923c.jpg', 986366, 'image/jpeg', 'uploads/2025-06-13_21-26-56_684c97a02e44b.jpg', '2025-06-13 21:26:56'),
(5, 'adsiiiiiiduarrr', 1, 'sebuah perjuang di malam hari sampai dengan akhir', 'progresADSIfinal.png', 688327, 'image/png', 'uploads/2025-06-13_22-13-20_684ca28094dd5.png', '2025-06-13 22:13:20');

--
-- Triggers `dokumen`
--
DELIMITER $$
CREATE TRIGGER `log_hapus_dokumen` BEFORE DELETE ON `dokumen` FOR EACH ROW BEGIN
    DECLARE kategori_nama VARCHAR(100);
    
    SELECT nama_kategori INTO kategori_nama 
    FROM kategori 
    WHERE id = OLD.kategori_id;
    
    INSERT INTO log_arsip (
        dokumen_id, 
        nama_dokumen, 
        kategori, 
        nama_file, 
        aksi, 
        keterangan
    ) VALUES (
        OLD.id,
        OLD.nama_dokumen,
        kategori_nama,
        OLD.nama_file,
        'DELETE',
        CONCAT('Dokumen dihapus pada ', NOW())
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `log_tambah_dokumen` AFTER INSERT ON `dokumen` FOR EACH ROW BEGIN
    DECLARE kategori_nama VARCHAR(100);
    
    -- Ambil nama kategori berdasarkan kategori_id dari baris yang baru dimasukkan (NEW)
    SELECT nama_kategori INTO kategori_nama 
    FROM kategori 
    WHERE id = NEW.kategori_id;
    
    -- Masukkan entri ke tabel log_arsip
    INSERT INTO log_arsip (
        dokumen_id, 
        nama_dokumen, 
        kategori, 
        nama_file, 
        aksi, 
        keterangan
    ) VALUES (
        NEW.id,             -- ID dokumen yang baru dimasukkan
        NEW.nama_dokumen,   -- Nama dokumen yang baru
        kategori_nama,      -- Nama kategori dokumen
        NEW.nama_file,      -- Nama file dokumen
        'ADD',              -- Aksi: Dokumen baru ditambahkan
        CONCAT('Dokumen baru ditambahkan pada ', NOW()) -- Keterangan log
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id` int NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `deskripsi` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id`, `nama_kategori`, `deskripsi`, `created_at`) VALUES
(1, 'Dokumen Pribadi', 'KTP, SIM, Paspor, dll', '2025-06-13 20:06:42'),
(2, 'Keuangan', 'Slip gaji, rekening bank, pajak, dll', '2025-06-13 20:06:42'),
(3, 'Pendidikan', 'Ijazah, sertifikat, transkrip, dll', '2025-06-13 20:06:42'),
(4, 'Kesehatan', 'Hasil lab, resep dokter, kartu BPJS, dll', '2025-06-13 20:06:42'),
(5, 'Pekerjaan', 'Kontrak kerja, surat tugas, dll', '2025-06-13 20:06:42'),
(6, 'Lainnya', 'Dokumen lain yang tidak masuk kategori di atas', '2025-06-13 20:06:42');

-- --------------------------------------------------------

--
-- Table structure for table `log_arsip`
--

CREATE TABLE `log_arsip` (
  `id` int NOT NULL,
  `dokumen_id` int DEFAULT NULL,
  `nama_dokumen` varchar(255) NOT NULL,
  `kategori` varchar(100) NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `aksi` enum('DELETE','ADD','UPDATE') NOT NULL,
  `tanggal_aksi` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `keterangan` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `log_arsip`
--

INSERT INTO `log_arsip` (`id`, `dokumen_id`, `nama_dokumen`, `kategori`, `nama_file`, `aksi`, `tanggal_aksi`, `keterangan`) VALUES
(1, 1, 'bukti', 'Pendidikan', 'progresADSIfinal.png', 'DELETE', '2025-06-13 20:19:20', 'Dokumen dihapus pada 2025-06-14 03:19:20'),
(2, 4, 'jeepmerahpride', 'Kesehatan', 'WhatsApp Image 2025-06-14 at 04.17.22_6786923c.jpg', 'ADD', '2025-06-13 21:26:56', 'Dokumen baru ditambahkan pada 2025-06-14 04:26:56'),
(3, 5, 'adsiiiiiiduarrr', 'Dokumen Pribadi', 'progresADSIfinal.png', 'ADD', '2025-06-13 22:13:20', 'Dokumen baru ditambahkan pada 2025-06-14 05:13:20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `dokumen`
--
ALTER TABLE `dokumen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_dokumen_kategori` (`kategori_id`),
  ADD KEY `idx_dokumen_uploaded` (`uploaded_at`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_kategori` (`nama_kategori`);

--
-- Indexes for table `log_arsip`
--
ALTER TABLE `log_arsip`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_log_tanggal` (`tanggal_aksi`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `dokumen`
--
ALTER TABLE `dokumen`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `log_arsip`
--
ALTER TABLE `log_arsip`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dokumen`
--
ALTER TABLE `dokumen`
  ADD CONSTRAINT `dokumen_ibfk_1` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`) ON DELETE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
