<?php
require_once 'koneksi.php';
check_login();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: upload.php");
    exit();
}

$nama_dokumen = trim($_POST['nama_dokumen']);
$kategori_id = (int)$_POST['kategori_id'];
$deskripsi = trim($_POST['deskripsi']);

// Validasi input
if (empty($nama_dokumen) || $kategori_id <= 0) {
    header("Location: upload.php?error=Nama dokumen dan kategori harus diisi!");
    exit();
}

// Validasi file
if (!isset($_FILES['file_dokumen']) || $_FILES['file_dokumen']['error'] != UPLOAD_ERR_OK) {
    header("Location: upload.php?error=File dokumen harus dipilih!");
    exit();
}

$file = $_FILES['file_dokumen'];
$allowed_types = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
$max_size = 10 * 1024 * 1024; // 10MB

if (!in_array($file['type'], $allowed_types)) {
    header("Location: upload.php?error=Tipe file tidak diizinkan! Gunakan PDF, JPG, atau PNG.");
    exit();
}

if ($file['size'] > $max_size) {
    header("Location: upload.php?error=Ukuran file terlalu besar! Maksimal 10MB.");
    exit();
}

// Buat folder uploads jika belum ada
$upload_dir = 'uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Generate nama file unik
$file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$unique_filename = date('Y-m-d_H-i-s') . '_' . uniqid() . '.' . $file_extension;
$file_path = $upload_dir . $unique_filename;

try {
    // MENGGUNAKAN TRANSACTION untuk menyimpan dokumen
    $pdo->beginTransaction();
    
    // Upload file
    if (!move_uploaded_file($file['tmp_name'], $file_path)) {
        throw new Exception("Gagal mengupload file!");
    }
    
    // Simpan metadata ke database
    $stmt = $pdo->prepare("
        INSERT INTO dokumen (nama_dokumen, kategori_id, deskripsi, nama_file, ukuran_file, tipe_file, path_file) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $nama_dokumen,
        $kategori_id,
        $deskripsi,
        $file['name'],
        $file['size'],
        $file['type'],
        $file_path
    ]);
    
    // Commit transaction
    $pdo->commit();
    
    header("Location: upload.php?success=1");
    exit();
    
} catch (Exception $e) {
    // Rollback transaction
    $pdo->rollBack();
    
    // Hapus file jika sudah terupload
    if (file_exists($file_path)) {
        unlink($file_path);
    }
    
    header("Location: upload.php?error=" . urlencode($e->getMessage()));
    exit();
}
?>
