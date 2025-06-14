<?php
require_once 'koneksi.php';
check_login();

// Parameter filter
$kategori_filter = isset($_GET['kategori']) ? (int)$_GET['kategori'] : null;
$tanggal_mulai = isset($_GET['tanggal_mulai']) ? $_GET['tanggal_mulai'] : null;
$tanggal_selesai = isset($_GET['tanggal_selesai']) ? $_GET['tanggal_selesai'] : null;
$search = isset($_GET['search']) ? trim($_GET['search']) : null;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

try {
    // Ambil daftar kategori untuk filter
    $stmt = $pdo->query("SELECT * FROM kategori ORDER BY nama_kategori");
    $kategori_list = $stmt->fetchAll();
    
    // MENGGUNAKAN STORED PROCEDURE untuk mengambil dokumen dengan filter
    $stmt = $pdo->prepare("CALL get_dokumen_filter(?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $kategori_filter,
        $tanggal_mulai,
        $tanggal_selesai,
        $search,
        $limit,
        $offset
    ]);
    $dokumen_list = $stmt->fetchAll();
    
    // Hitung total untuk pagination
    $count_sql = "SELECT COUNT(*) as total FROM dokumen d JOIN kategori k ON d.kategori_id = k.id WHERE 1=1";
    $params = [];
    
    if ($kategori_filter) {
        $count_sql .= " AND d.kategori_id = ?";
        $params[] = $kategori_filter;
    }
    if ($tanggal_mulai) {
        $count_sql .= " AND DATE(d.uploaded_at) >= ?";
        $params[] = $tanggal_mulai;
    }
    if ($tanggal_selesai) {
        $count_sql .= " AND DATE(d.uploaded_at) <= ?";
        $params[] = $tanggal_selesai;
    }
    if ($search) {
        $count_sql .= " AND (d.nama_dokumen LIKE ? OR d.deskripsi LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    $stmt = $pdo->prepare($count_sql);
    $stmt->execute($params);
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
    <title>Daftar Dokumen - ArsipQue</title>
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
        .document-item {
            transition: all 0.3s ease;
        }
        .document-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
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
            
            <div class="col-md-9 col-lg-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="bi bi-files"></i> Daftar Dokumen</h2>
                    <a href="upload.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Upload Dokumen
                    </a>
                </div>
                
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Kategori</label>
                                <select class="form-select" name="kategori">
                                    <option value="">Semua Kategori</option>
                                    <?php foreach ($kategori_list as $kategori): ?>
                                        <option value="<?= $kategori['id'] ?>" <?= $kategori_filter == $kategori['id'] ? 'selected' : '' ?>>
                                            <?= $kategori['nama_kategori'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" name="tanggal_mulai" value="<?= $tanggal_mulai ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Tanggal Selesai</label>
                                <input type="date" class="form-control" name="tanggal_selesai" value="<?= $tanggal_selesai ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Pencarian</label>
                                <input type="text" class="form-control" name="search" placeholder="Nama dokumen..." value="<?= htmlspecialchars($search ?? '') ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <?php if (empty($dokumen_list)): ?>
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted mb-3"></i>
                            <h5 class="text-muted">Tidak ada dokumen ditemukan</h5>
                            <p class="text-muted">Silakan upload dokumen atau ubah filter pencarian</p>
                            <a href="upload.php" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Upload Dokumen
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($dokumen_list as $doc): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card document-item h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div class="flex-grow-1">
                                                <h6 class="card-title mb-1"><?= htmlspecialchars($doc['nama_dokumen']) ?></h6>
                                                <small class="text-primary"><?= $doc['nama_kategori'] ?></small>
                                            </div>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="detail.php?id=<?= $doc['id'] ?>">
                                                        <i class="bi bi-eye"></i> Lihat Detail
                                                    </a></li>
                                                    <li><a class="dropdown-item" href="<?= $doc['path_file'] ?>" target="_blank">
                                                        <i class="bi bi-download"></i> Download
                                                    </a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item text-danger" href="hapus.php?id=<?= $doc['id'] ?>" onclick="return confirm('Yakin ingin menghapus dokumen ini?')">
                                                        <i class="bi bi-trash"></i> Hapus
                                                    </a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        
                                        <?php if ($doc['deskripsi']): ?>
                                            <p class="card-text text-muted small"><?= htmlspecialchars(substr($doc['deskripsi'], 0, 100)) ?><?= strlen($doc['deskripsi']) > 100 ? '...' : '' ?></p>
                                        <?php endif; ?>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="bi bi-calendar"></i> <?= format_tanggal($doc['uploaded_at']) ?>
                                            </small>
                                            <small class="text-muted">
                                                <i class="bi bi-file-earmark"></i> <?= format_bytes($doc['ukuran_file']) ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if ($total_pages > 1): ?>
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $page-1 ?>&<?= http_build_query(array_filter($_GET, fn($k) => $k != 'page', ARRAY_FILTER_USE_KEY)) ?>">
                                        <i class="bi bi-chevron-left"></i>
                                    </a>
                                </li>
                                
                                <?php for ($i = max(1, $page-2); $i <= min($total_pages, $page+2); $i++): ?>
                                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?>&<?= http_build_query(array_filter($_GET, fn($k) => $k != 'page', ARRAY_FILTER_USE_KEY)) ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                
                                <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $page+1 ?>&<?= http_build_query(array_filter($_GET, fn($k) => $k != 'page', ARRAY_FILTER_USE_KEY)) ?>">
                                        <i class="bi bi-chevron-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                        
                        <div class="text-center text-muted">
                            Menampilkan <?= ($offset + 1) ?> - <?= min($offset + $limit, $total_records) ?> dari <?= $total_records ?> dokumen
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>