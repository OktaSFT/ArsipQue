<?php
require_once 'koneksi.php';
check_login();

if (!isset($_GET['id'])) {
    header("Location: daftar_dokumen.php");
    exit();
}

$id = (int)$_GET['id'];

try {
    // Ambil informasi dokumen sebelum dihapus
    $stmt = $pdo->prepare("SELECT * FROM dokumen WHERE id = ?");
    $stmt->execute([$id]);
    $dokumen = $stmt->fetch();
    
    if (!$dokumen) {
        header("Location: daftar_dokumen.php?error=Dokumen tidak ditemukan");
        exit();
    }
    
    // MENGGUNAKAN TRANSACTION untuk menghapus dokumen
    $pdo->beginTransaction();
    
    // Hapus file fisik
    if (file_exists($dokumen['path_file'])) {
        unlink($dokumen['path_file']);
    }
    
    // Hapus record dari database (TRIGGER akan otomatis mencatat ke log_arsip)
    $stmt = $pdo->prepare("DELETE FROM dokumen WHERE id = ?");
    $stmt->execute([$id]);
    
    // Commit transaction
    $pdo->commit();
    
    header("Location: daftar_dokumen.php?success=Dokumen berhasil dihapus");
    exit();
    
} catch (Exception $e) {
    // Rollback transaction
    $pdo->rollBack();
    
    header("Location: daftar_dokumen.php?error=Gagal menghapus dokumen: " . $e->getMessage());
    exit();
}
?>
