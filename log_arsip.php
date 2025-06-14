<?php
require_once 'koneksi.php';
check_login();

// Parameter pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

try {
    // Pastikan offset dan limit bertipe integer
    $limit = (int)$limit;
    $offset = (int)$offset;

    // Ambil log arsip (gunakan LIMIT dan OFFSET langsung)
    $sql = "SELECT * FROM log_arsip 
            ORDER BY tanggal_aksi DESC 
            LIMIT $limit OFFSET $offset";
    $stmt = $pdo->query($sql);
    $log_list = $stmt->fetchAll();

    // Hitung total data
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM log_arsip");
    $total_records = $stmt->fetch()['total'];
    $total_pages = ceil($total_records / $limit);
} catch (PDOException $e) {
    $error = "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Arsip - ArsipQue</title>
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
                    <a class="nav-link" href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
                    <a class="nav-link" href="upload.php"><i class="bi bi-cloud-upload"></i> Upload Dokumen</a>
                    <a class="nav-link" href="daftar_dokumen.php"><i class="bi bi-files"></i> Daftar Dokumen</a>
                    <a class="nav-link active" href="log_arsip.php"><i class="bi bi-journal-text"></i> Log Arsip</a>
                    <hr class="text-white">
                    <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
                </nav>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="bi bi-journal-text"></i> Log Arsip</h2>
                    <span class="badge bg-secondary">Total: <?= isset($total_records) ? $total_records : 0 ?> log</span>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-body">
                        <?php if (empty($log_list)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-journal-x fs-1 text-muted mb-3"></i>
                                <h5 class="text-muted">Belum ada log arsip</h5>
                                <p class="text-muted">Log akan muncul ketika ada dokumen yang dihapus</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Nama Dokumen</th>
                                            <th>Kategori</th>
                                            <th>Nama File</th>
                                            <th>Aksi</th>
                                            <th>Tanggal</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($log_list as $log): ?>
                                            <tr>
                                                <td><?= $log['dokumen_id'] ?></td>
                                                <td><?= htmlspecialchars($log['nama_dokumen']) ?></td>
                                                <td><span class="badge bg-secondary"><?= $log['kategori'] ?></span></td>
                                                <td><?= htmlspecialchars($log['nama_file']) ?></td>
                                                <td><span class="badge bg-danger"><i class="bi bi-trash"></i> <?= $log['aksi'] ?></span></td>
                                                <td>
                                                    <?= format_tanggal($log['tanggal_aksi']) ?><br>
                                                    <small class="text-muted"><?= date('H:i', strtotime($log['tanggal_aksi'])) ?> WIB</small>
                                                </td>
                                                <td><small class="text-muted"><?= htmlspecialchars($log['keterangan']) ?></small></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            <?php if ($total_pages > 1): ?>
                                <nav aria-label="Page navigation" class="mt-4">
                                    <ul class="pagination justify-content-center">
                                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                            <a class="page-link" href="?page=<?= $page - 1 ?>"><i class="bi bi-chevron-left"></i></a>
                                        </li>
                                        <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                                            <a class="page-link" href="?page=<?= $page + 1 ?>"><i class="bi bi-chevron-right"></i></a>
                                        </li>
                                    </ul>
                                </nav>

                                <div class="text-center text-muted">
                                    Menampilkan <?= ($offset + 1) ?> - <?= min($offset + $limit, $total_records) ?> dari <?= $total_records ?> log
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
