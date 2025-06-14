<?php
require_once 'koneksi.php';
check_login();

// Ambil statistik menggunakan MySQL Function
try {
    // Total dokumen
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM dokumen");
    $total_dokumen = $stmt->fetch()['total'];
    
    // Total kategori
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM kategori");
    $total_kategori = $stmt->fetch()['total'];
    
    // Dokumen per kategori menggunakan MySQL Function
    $stmt = $pdo->query("
        SELECT k.nama_kategori, hitung_dokumen_per_kategori(k.id) as jumlah 
        FROM kategori k 
        ORDER BY jumlah DESC
    ");
    $kategori_stats = $stmt->fetchAll();
    
    // Dokumen terbaru
    $stmt = $pdo->query("
        SELECT d.*, k.nama_kategori 
        FROM dokumen d 
        JOIN kategori k ON d.kategori_id = k.id 
        ORDER BY d.uploaded_at DESC 
        LIMIT 5
    ");
    $dokumen_terbaru = $stmt->fetchAll();
    
    // Total ukuran file
    $stmt = $pdo->query("SELECT SUM(ukuran_file) as total_size FROM dokumen");
    $total_size = $stmt->fetch()['total_size'] ?? 0;
    
} catch (PDOException $e) {
    $error = "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ArsipQue</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            /* Pastikan gradasi warna ungu konsisten */
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
        .stat-card {
            /* Pastikan gradasi warna ungu konsisten */
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-lg-2 sidebar p-3">
                <div class="text-center text-white mb-4">
                    <i class="bi bi-archive fs-1"></i>
                    <h4>ArsipQue</h4>
                    <small>Selamat datang, <?= $_SESSION['admin_nama'] ?></small>
                </div>
                
                <nav class="nav flex-column">
                    <a class="nav-link active" href="dashboard.php">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                    <a class="nav-link" href="upload.php">
                        <i class="bi bi-cloud-upload"></i> Upload Dokumen
                    </a>
                    <a class="nav-link" href="daftar_dokumen.php">
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
            
            <div class="col-md-9 col-lg-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="bi bi-speedometer2"></i> Dashboard</h2>
                    <span class="text-muted"><?= date('d F Y, H:i') ?></span>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card">
                            <div class="card-body text-center">
                                <i class="bi bi-files fs-1 mb-2"></i>
                                <h3><?= $total_dokumen ?></h3>
                                <p class="mb-0">Total Dokumen</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card">
                            <div class="card-body text-center">
                                <i class="bi bi-tags fs-1 mb-2"></i>
                                <h3><?= $total_kategori ?></h3>
                                <p class="mb-0">Kategori</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card">
                            <div class="card-body text-center">
                                <i class="bi bi-hdd fs-1 mb-2"></i>
                                <h3><?= format_bytes($total_size) ?></h3>
                                <p class="mb-0">Total Ukuran</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card">
                            <div class="card-body text-center">
                                <i class="bi bi-calendar-date fs-1 mb-2"></i>
                                <h3><?= date('d') ?></h3>
                                <p class="mb-0"><?= date('M Y') ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="bi bi-pie-chart"></i> Dokumen per Kategori</h5>
                            </div>
                            <div class="card-body">
                                <?php foreach ($kategori_stats as $stat): ?>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span><?= $stat['nama_kategori'] ?></span>
                                        <span class="badge bg-primary"><?= $stat['jumlah'] ?></span>
                                    </div>
                                    <div class="progress mb-3" style="height: 8px;">
                                        <div class="progress-bar" style="width: <?= $total_dokumen > 0 ? ($stat['jumlah'] / $total_dokumen * 100) : 0 ?>%"></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="bi bi-clock-history"></i> Dokumen Terbaru</h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($dokumen_terbaru)): ?>
                                    <p class="text-muted text-center">Belum ada dokumen</p>
                                <?php else: ?>
                                    <?php foreach ($dokumen_terbaru as $doc): ?>
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <h6 class="mb-1"><?= htmlspecialchars($doc['nama_dokumen']) ?></h6>
                                                <small class="text-muted">
                                                    <?= $doc['nama_kategori'] ?> • 
                                                    <?= format_tanggal($doc['uploaded_at']) ?>
                                                </small>
                                            </div>
                                            <a href="detail.php?id=<?= $doc['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>