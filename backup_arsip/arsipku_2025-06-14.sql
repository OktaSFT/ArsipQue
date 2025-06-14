-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: localhost    Database: arsipku
-- ------------------------------------------------------
-- Server version	8.0.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin`
--

LOCK TABLES `admin` WRITE;
/*!40000 ALTER TABLE `admin` DISABLE KEYS */;
INSERT INTO `admin` VALUES (1,'admin','$2y$10$J7CliYOck/J/nG767FOs5e2RuRDVhdcus91x/3n0N6BIAoKG92BVC','Administrator','2025-06-13 20:06:42');
/*!40000 ALTER TABLE `admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dokumen`
--

DROP TABLE IF EXISTS `dokumen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dokumen` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_dokumen` varchar(255) NOT NULL,
  `kategori_id` int NOT NULL,
  `deskripsi` text,
  `nama_file` varchar(255) NOT NULL,
  `ukuran_file` int NOT NULL,
  `tipe_file` varchar(50) NOT NULL,
  `path_file` varchar(500) NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_dokumen_kategori` (`kategori_id`),
  KEY `idx_dokumen_uploaded` (`uploaded_at`),
  CONSTRAINT `dokumen_ibfk_1` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dokumen`
--

LOCK TABLES `dokumen` WRITE;
/*!40000 ALTER TABLE `dokumen` DISABLE KEYS */;
INSERT INTO `dokumen` VALUES (2,'memories',6,'sebuah foto yang mengandung memori berharga selama study lapangan','IMG_20250530_044622.jpg',1104840,'image/jpeg','uploads/2025-06-13_20-18-06_684c877e818d0.jpg','2025-06-13 20:18:06'),(4,'jeepmerahpride',4,'ALTER TABLE log_arsip MODIFY COLUMN aksi ENUM(\'DELETE\', \'ADD\', \'UPDATE\') NOT NULL;','WhatsApp Image 2025-06-14 at 04.17.22_6786923c.jpg',986366,'image/jpeg','uploads/2025-06-13_21-26-56_684c97a02e44b.jpg','2025-06-13 21:26:56'),(5,'adsiiiiiiduarrr',1,'sebuah perjuang di malam hari sampai dengan akhir','progresADSIfinal.png',688327,'image/png','uploads/2025-06-13_22-13-20_684ca28094dd5.png','2025-06-13 22:13:20');
/*!40000 ALTER TABLE `dokumen` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `log_tambah_dokumen` AFTER INSERT ON `dokumen` FOR EACH ROW BEGIN
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
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `log_hapus_dokumen` BEFORE DELETE ON `dokumen` FOR EACH ROW BEGIN
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
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `kategori`
--

DROP TABLE IF EXISTS `kategori`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kategori` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(100) NOT NULL,
  `deskripsi` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nama_kategori` (`nama_kategori`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kategori`
--

LOCK TABLES `kategori` WRITE;
/*!40000 ALTER TABLE `kategori` DISABLE KEYS */;
INSERT INTO `kategori` VALUES (1,'Dokumen Pribadi','KTP, SIM, Paspor, dll','2025-06-13 20:06:42'),(2,'Keuangan','Slip gaji, rekening bank, pajak, dll','2025-06-13 20:06:42'),(3,'Pendidikan','Ijazah, sertifikat, transkrip, dll','2025-06-13 20:06:42'),(4,'Kesehatan','Hasil lab, resep dokter, kartu BPJS, dll','2025-06-13 20:06:42'),(5,'Pekerjaan','Kontrak kerja, surat tugas, dll','2025-06-13 20:06:42'),(6,'Lainnya','Dokumen lain yang tidak masuk kategori di atas','2025-06-13 20:06:42');
/*!40000 ALTER TABLE `kategori` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log_arsip`
--

DROP TABLE IF EXISTS `log_arsip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `log_arsip` (
  `id` int NOT NULL AUTO_INCREMENT,
  `dokumen_id` int DEFAULT NULL,
  `nama_dokumen` varchar(255) NOT NULL,
  `kategori` varchar(100) NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `aksi` enum('DELETE','ADD','UPDATE') NOT NULL,
  `tanggal_aksi` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `keterangan` text,
  PRIMARY KEY (`id`),
  KEY `idx_log_tanggal` (`tanggal_aksi`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log_arsip`
--

LOCK TABLES `log_arsip` WRITE;
/*!40000 ALTER TABLE `log_arsip` DISABLE KEYS */;
INSERT INTO `log_arsip` VALUES (1,1,'bukti','Pendidikan','progresADSIfinal.png','DELETE','2025-06-13 20:19:20','Dokumen dihapus pada 2025-06-14 03:19:20'),(2,4,'jeepmerahpride','Kesehatan','WhatsApp Image 2025-06-14 at 04.17.22_6786923c.jpg','ADD','2025-06-13 21:26:56','Dokumen baru ditambahkan pada 2025-06-14 04:26:56'),(3,5,'adsiiiiiiduarrr','Dokumen Pribadi','progresADSIfinal.png','ADD','2025-06-13 22:13:20','Dokumen baru ditambahkan pada 2025-06-14 05:13:20');
/*!40000 ALTER TABLE `log_arsip` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-06-14  9:08:06
