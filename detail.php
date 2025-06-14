<?php
require_once 'koneksi.php';
check_login();

if (!isset($_GET['id'])) {
    header("Location: daftar_dokumen.php");
    exit();
}

$id = (int)$_GET['id'];

try {
    $stmt = $pdo->prepare("
        SELECT d.*, k.nama_kategori 
        FROM dokumen d 
        JOIN kategori k ON d.kategori_id = k.id 
        WHERE d.id = ?
    ");
    $stmt->execute([$id]);
    $dokumen = $stmt->fetch();
    
    if (!$dokumen) {
        header("Location: daftar_dokumen.php?error=Dokumen tidak ditemukan");
        exit();
    }
    
} catch (PDOException $e) {
    header("Location: daftar_dokumen.php?error=Terjadi kesalahan sistem");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Dokumen - ArsipQue</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
        }
        .nav-link {
            color: rgba(255,255,255,0.8) !important;
            border-radius: 10px;
            margin: 2px 0;
        }
        .nav-link:hover, .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white !important;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .file-preview {
            max-width: 100%;
            max-height: 500px;
            border-radius: 10px;
        }
        .info-item {
            border-bottom: 1px solid #eee;
            padding: 15px 0;
        }
        .info-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-3">
                <div class="text-center text-white mb-4">
                    <i class="bi bi-archive fs-1"></i>
                    <h4>ArsipQue</h4>
                    <small>Selamat datang, <?= $_SESSION['admin_nama'] ?></small>
                </div>
                
                <nav class="nav flex-column">
                    <a class="nav-link" href="dashboard.php">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                    <a class="nav-link" href="upload.php">
                        <i class="bi bi-cloud-upload"></i> Upload Dokumen
                    </a>
                    <a class="nav-link active" href="daftar_dokumen.php">
                        <i class="bi bi-files"></i> Daftar Dokumen
                    </a>
                    <a class="nav-link" href="log_arsip.php">
                        <i class="bi bi-journal-text"></i> Log Arsip
                    </a>
                    <hr class="text-white">
                    <a class="nav-link" href="logout.php">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </nav>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="bi bi-file-earmark-text"></i> Detail Dokumen</h2>
                    <div>
                        <a href="daftar_dokumen.php" class="btn btn-outline-secondary me-2">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <a href="<?= $dokumen['path_file'] ?>" target="_blank" class="btn btn-primary me-2">
                            <i class="bi bi-download"></i> Download
                        </a>
                        <a href="hapus.php?id=<?= $dokumen['id'] ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus dokumen ini?')">
                            <i class="bi bi-trash"></i> Hapus
                        </a>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Preview File -->
                    <div class="col-md-8 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="bi bi-eye"></i> Preview Dokumen</h5>
                            </div>
                            <div class="card-body text-center">
                                <?php if (strpos($dokumen['tipe_file'], 'image') !== false): ?>
                                    <img src="<?= $dokumen['path_file'] ?>" alt="Preview" class="file-preview img-fluid">
                                <?php elseif ($dokumen['tipe_file'] == 'application/pdf'): ?>
                                    <embed src="<?= $dokumen['path_file'] ?>" type="application/pdf" width="100%" height="500px" class="rounded">
                                    <p class="mt-3">
                                        <a href="<?= $dokumen['path_file'] ?>" target="_blank" class="btn btn-primary">
                                            <i class="bi bi-file-pdf"></i> Buka PDF di Tab Baru
                                        </a>
                                    </p>
                                <?php else: ?>
                                    <div class="py-5">
                                        <i class="bi bi-file-earmark fs-1 text-muted mb-3"></i>
                                        <h5 class="text-muted">Preview tidak tersedia</h5>
                                        <p class="text-muted">Klik download untuk melihat file</p>
                                        <a href="<?= $dokumen['path_file'] ?>" target="_blank" class="btn btn-primary">
                                            <i class="bi bi-download"></i> Download File
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Informasi Dokumen -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="bi bi-info-circle"></i> Informasi Dokumen</h5>
                            </div>
                            <div class="card-body">
                                <div class="info-item">
                                    <strong>Nama Dokumen</strong>
                                    <p class="mb-0 mt-1"><?= htmlspecialchars($dokumen['nama_dokumen']) ?></p>
                                </div>
                                
                                <div class="info-item">
                                    <strong>Kategori</strong>
                                    <p class="mb-0 mt-1">
                                        <span class="badge bg-primary"><?= $dokumen['nama_kategori'] ?></span>
                                    </p>
                                </div>
                                
                                <?php if ($dokumen['deskripsi']): ?>
                                <div class="info-item">
                                    <strong>Deskripsi</strong>
                                    <p class="mb-0 mt-1"><?= nl2br(htmlspecialchars($dokumen['deskripsi'])) ?></p>
                                </div>
                                <?php endif; ?>
                                
                                <div class="info-item">
                                    <strong>Nama File</strong>
                                    <p class="mb-0 mt-1"><?= htmlspecialchars($dokumen['nama_file']) ?></p>
                                </div>
                                
                                <div class="info-item">
                                    <strong>Ukuran File</strong>
                                    <p class="mb-0 mt-1"><?= format_bytes($dokumen['ukuran_file']) ?></p>
                                </div>
                                
                                <div class="info-item">
                                    <strong>Tipe File</strong>
                                    <p class="mb-0 mt-1">
                                        <?php
                                        $icon = match($dokumen['tipe_file']) {
                                            'application/pdf' => 'bi-file-pdf text-danger',
                                            'image/jpeg', 'image/jpg', 'image/png' => 'bi-file-image text-success',
                                            default => 'bi-file-earmark text-secondary'
                                        };
                                        ?>
                                        <i class="bi <?= $icon ?>"></i> <?= $dokumen['tipe_file'] ?>
                                    </p>
                                </div>
                                
                                <div class="info-item">
                                    <strong>Tanggal Upload</strong>
                                    <p class="mb-0 mt-1">
                                        <i class="bi bi-calendar"></i> <?= format_tanggal($dokumen['uploaded_at']) ?>
                                        <br>
                                        <br>
                                        <small class="text-muted"><?= date('H:i', strtotime($dokumen['uploaded_at'])) ?> WIB</small>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Aksi Cepat -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h6><i class="bi bi-lightning"></i> Aksi Cepat</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="<?= $dokumen['path_file'] ?>" target="_blank" class="btn btn-outline-primary">
                                        <i class="bi bi-eye"></i> Buka di Tab Baru
                                    </a>
                                    <a href="<?= $dokumen['path_file'] ?>" download class="btn btn-outline-success">
                                        <i class="bi bi-download"></i> Download File
                                    </a>
                                    <button class="btn btn-outline-info" onclick="copyToClipboard('<?= $dokumen['nama_file'] ?>')">
                                        <i class="bi bi-clipboard"></i> Copy Nama File
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('Nama file berhasil disalin ke clipboard!');
            });
        }
    </script>
</body>
</html>
