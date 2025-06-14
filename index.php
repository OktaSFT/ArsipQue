<?php
require_once 'koneksi.php';
start_session();

// Redirect jika sudah login
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = 'Username dan password harus diisi!';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
            $stmt->execute([$username]);
            $admin = $stmt->fetch();
            
            if ($admin && password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_nama'] = $admin['nama'];
                
                header("Location: dashboard.php");
                exit();
            } else {
                $error = 'Username atau password salah!';
            }
        } catch (PDOException $e) {
            $error = 'Terjadi kesalahan sistem!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ArsipQue</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            background-image: url('assets/bromo.jpg'); /* Pastikan path gambar benar */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.7); /* Nilai 0.7 untuk transparansi 70% */
            backdrop-filter: blur(10px); /* Efek blur pada latar belakang elemen */
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        .logo {
            /* Mengubah warna logo menjadi gradasi ungu-biru */
            background: linear-gradient(45deg, #764ba2, #667eea); /* Gradasi dari ungu ke biru */
            -webkit-background-clip: text; /* Menerapkan gradasi sebagai klip teks */
            -webkit-text-fill-color: transparent; /* Membuat teks transparan agar gradasi terlihat */
            font-size: 2.5rem;
            font-weight: bold;
        }
        .btn-primary {
            /* Mengubah warna tombol login menjadi gradasi ungu-biru */
            background: linear-gradient(45deg, #764ba2, #667eea); /* Gradasi dari ungu ke biru */
            border: none; /* Hapus border default Bootstrap */
        }
        .btn-primary:hover {
            /* Efek hover untuk tombol */
            background: linear-gradient(45deg, #667eea, #764ba2); /* Membalik gradasi saat hover */
            border: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="login-card p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-archive logo"></i>
                        <h2 class="mt-2">ArsipQue</h2>
                        <p class="text-muted">Sistem Pengarsipan Dokumen Pribadi</p>
                    </div>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle"></i> <?= $error ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" name="username" required>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </button>
                    </form>
                    
                    <div class="text-center mt-4">
                        <small class="text-muted">
                            Default: admin / password
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>