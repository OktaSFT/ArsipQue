# ArsipQue
Proyek ini merupakan sistem pengarsipan dokumen sederhana yang dikembangkan menggunakan PHP dan MySQL. Tujuan utamanya adalah untuk mengelola transaksi keuangan dengan aman dan menjaga konsistensi data. Sistem ini memanfaatkan fitur-fitur canggih dari database seperti stored procedure, trigger, transaction, dan stored function untuk memastikan integritas operasional. Selain itu, proyek ini dilengkapi dengan mekanisme backup otomatis untuk perlindungan data dari kejadian yang tidak diinginkan.
![image](https://github.com/user-attachments/assets/ae6ddbfd-ed5c-4c8b-9dd5-4b48d2799531)

# 🙌 Fitur Utama
- Autentikasi Admin : sistem login aman untuk administrator
  ![image](https://github.com/user-attachments/assets/ad76de2b-99c3-47b6-a141-2b990d7d2285)

- Manajemen dokumen :
  - Upload dokumen : mengunggahh dokumen baru
    ![image](https://github.com/user-attachments/assets/ab837103-0c82-4968-aef7-243f1dbb973d)

  - Daftar dokumen : menampilkan dan memfilter dokumen berdasarkan kategori, rentang tanggal, dan pencarian teks
    ![image](https://github.com/user-attachments/assets/ab490154-9b64-45ae-ad0f-d5156ada4f6a)

  - Detail dokumen : menampilkan informasi lengkap dan pratinjau file
  - Download dan hapus dokumen : mengunduh atau menghapus file dokumen
    ![image](https://github.com/user-attachments/assets/f5d7610b-6a65-43dd-9a7f-79b7ad1c6389)
    
- Kategorisasi dokumen : dokumen diorganisir ke dalam berbagi kategori
  ![image](https://github.com/user-attachments/assets/c7e4e642-365b-480d-9d18-d601ea31ebd6)

- Sistem log aktivitas : pencatatan otomatis setiap menambah dan menghapus dokumen ke log_arsip
  ![image](https://github.com/user-attachments/assets/f1e9d3bd-0433-4ed9-b2df-d441777c15f5)

- Dashboard statistik : ringkasan total dokumen, kategori, ukuran file, dan dokuemn terbaru
  ![image](https://github.com/user-attachments/assets/7377ee6b-c49d-4a89-82c9-c45073fe8323)


# 📌 Detail Konsep
# ⚠ Disclaimer
Peran stored procedure, trigger, transaction, dan stored function dalam proyek ini dirancang khusus untuk kebutuhan sistem pdtbank. Penerapannya bisa berbeda pada sistem lain, tergantung arsitektur dan kebutuhan masing-masing sistem.

# 🧠 Stored Procedure
Stored procedure adalah potongan kode SQL yang disimpan dalam database dan dapat dieksekusi beki-kali. Stored procedure membantu meningkatkan performa, keamanan, dan reusabilitas kode.
- proyek ArsipQue menggunakan stored procedure bernama **get_dokumen_filter**. prosedur ini dirancang untuk mengambil daftar dokumen berdasarkan beberapa parameter filter seperti ID kategori, rentang tanggal upload, dan kata kunci pencarian pada nama atau deskripsi dokumen.
  ![image](https://github.com/user-attachments/assets/01eb44ab-2cb7-4293-b519-2b0d342ce9c0)

  ![image](https://github.com/user-attachments/assets/91da6c4c-5286-4254-a6b1-7b9ec26f2b3b)
 
- **daftar_dokumen.php** memanggil prosedur iniuntuk menampilkan dokumen yang difilter kepada pengguna.
dengan menggunakan stored procedure, logika kompleks untuk memfilter dan mengambil data dienkapsulasi di sisi database, mengurangi kebutuhan query dinamis yang panjang yang meningkatkan kinerja dan keamanan.
  ![image](https://github.com/user-attachments/assets/c37e68c2-629d-4410-93b9-c1b698ab87b0)

# 🚨 Trigger
Trigger adalah objek database yang secara otomatis dieksekusi ketika peristiwa tertentu pada tabel.
- **log_tambah_dokumen** trigger ini di aktifkan setelah ada baris baru dimasukkan ke tabel **dokumen (AFTER INSERT ON dokumen)**. fungsinya untuk secara otomatis mencatat detail dokumen yang baru ditambahkan ke dalam tabel **log_arsip** dengan aksi 'ADD'.
- **log_hapus_dokumen** trigger ini di aktifkan sebelum ada baris dihapus dari tabel **dokumen (BEFORE DELETE ON dokumen)**. tujuannya untuk mencatat informasi dokumen yang akan dihapus ke dalam tabel **log_arsip** dengan aksi 'DELETE'.
  ![image](https://github.com/user-attachments/assets/4da123f7-76bd-4610-9bf0-9da76213613d)
  
  ![image](https://github.com/user-attachments/assets/2434f56d-2b8e-451b-b67b-7c0188f66a4d)

Trigger memastikan bahwa setiap aktivitas penting terkait penambahan dan penghapusan dokumen secara otomatis tercatat di log arsip tanpa perlu intervensi kode aplikasi secara manual, sehingga menjaga jejak audit dan integritas data.

# 🔄 Transaction (Transaksi)
Transaction adalah serangkaian operasi database yang diperlakukan sebagai satu unit kerja logis yang atomik. Artinya, semua operasi dalam transaction harus berhasil atau tidak ada satupun yang berhasil (semua dibatalkan atau rolled back) jika terjadi kesalahan.
- File **hapus.php** menggunakan transaction saat menghapus dokumen. Proses ini melibatkan dua langkah penting: menghapus file fisik dari sistem penyimpanan dan menghapus entri dokumen dari tabel **dokumen** di database.
- Dengan menggunakan **PDO::beginTransaction()**, **PDO::commit()**, dan **PDO::rollBack()**, ArsipQue memastikan bahwa jika penghapusan file fisik gagal, atau jika ada masalah saat menghapus data dari database, seluruh operasi akan dibatalkan, menjaga konsistensi antara data di database dan file di server.
  ```try {
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

Transaction sangat penting untuk menjaga integritas data dalam operasi yang melibatkan beberapa langkah, memastikan bahwa sistem selalu berada dalam keadaan konsisten bahkan jika terjadi kegagalan di tengah jalan.

# 📺 Stored Function
Stored Function dirancang untuk mengembalikan nilai tunggal dan dapat dipanggil dalam ekspresi SQL seperti **SELECT**.
- Proyek ini mendefinisikan sebuah stored function bernama **hitung_dokumen_per_kategori**. Fungsi ini menerima **kategori_id_param** sebagai input dan mengembalikan jumlah total dokumen yang terdaftar di bawah kategori tersebut. Fungsi ini digunakan di **dashboard.php** untuk menampilkan statistik distribusi dokumen per kategori.
  ![image](https://github.com/user-attachments/assets/6ec4809c-2fa3-4be6-9665-a71a2a009b12)

  ```try {
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
Stored function memungkinkan perhitungan dan agregasi data yang sering dilakukan untuk dieksekusi langsung di database, mengurangi kompleksitas pada sisi aplikasi dan mengoptimalkan kinerja query yang memerlukan nilai terhitung.

# 🔄 Backup Otomatis
Proses otomatis menyalin data database ke lokasi terpisah secara berkala untuk tujuan pemulihan jika terjadi kehilangan data.
- Proyek ini menyertakan skrip shell bernama **backup_script.sh**. Skrip ini dirancang untuk dijalankan secara otomatis untuk membuat backup database **arsipku** menggunakan utilitas **mysqldump**.
- Fitur tambahan dari skrip ini adalah kemampuan untuk mengkompres file backup dengan **gzip** dan secara otomatis menghapus file backup yang berusia lebih dari 30 hari untuk menghemat ruang penyimpanan.
  ![image](https://github.com/user-attachments/assets/f8338699-f948-423b-806c-59e3f9cfec01)
  ![image](https://github.com/user-attachments/assets/103af1f6-75e7-4542-b751-03e20600f21e)
memastikan bahwa meskipun terjadi kegagalan sistem, kerusakan hardware, atau insiden tak terduga lainnya, data arsip dapat dipulihkan ke kondisi sebelumnya, menjaga ketersediaan dan keandalan sistem.

# 🧩 Relevansi Proyek dengan Pemrosesan Data Terdistribusi
1. Konsistensi data : pengguna transaction dan trigger adalah inti dari menjaga konsistensi data. Dalam sistem terdistribusi, tantangan untuk mencapai konsistensi di berbagai node jauh lebih besar, tetapi prinsip dasar dari operasi atomik dan logging yang andal tetap krusial.
2. Modularitas Logika : Stored procedure dan function menunjukkan bagaimana logika bisnis dapat dimodularisasi. Dalam arsitektur terdistribusi, logika ini sering didistribusikan ke layanan-layanan terpisah, tetapi prinsip pemisahan tanggung jawab tetap ada.
3. Ketahanan : Mekanisme backup otomatis adalah langkah pertama dalam strategi ketahanan data. Sistem terdistribusi memperluas ini dengan replikasi, sharding, dan distribusi geografis untuk memastikan ketersediaan data yang tinggi.
4. Auditabilitas: Sistem log melalui trigger memberikan jejak audit. Dalam sistem terdistribusi, logging terpusat dari berbagai komponen sangat penting untuk pemantauan, debugging, dan kepatuhan.
