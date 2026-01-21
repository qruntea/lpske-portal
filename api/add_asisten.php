<?php

session_start();
header('Content-Type: application/json');

// Pastikan error PHP tidak ditampilkan di output, hanya di log
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Cek session admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Menggunakan try-catch untuk menangani semua error
try {
    // Memuat koneksi database
    require 'koneksi.php';

    if (!$koneksi) {
        throw new Exception('Database connection failed.');
    }
    
    // Periksa metode request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method.');
    }

    // Ambil dan bersihkan data input dari $_POST
    // Pastikan nama field di form HTML Anda cocok dengan ini
    $nim = trim($_POST['nim'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $jabatan = trim($_POST['jabatan'] ?? '');
    $angkatan = trim($_POST['angkatan'] ?? '');
    $status = trim($_POST['status'] ?? '');
    
    // Validasi data
    if (empty($nama) || empty($nim) || empty($jabatan) || empty($angkatan) || empty($status)) {
        throw new Exception('Semua field harus diisi!');
    }
    
    // Memulai transaksi
    $koneksi->begin_transaction();
    
    // --- Bagian 1: Cek apakah NIM sudah terdaftar sebagai asisten ---
    // Menggunakan kolom `nim` dari tabel `asisten`
    $check_asisten_sql = "SELECT id FROM asisten WHERE nim = ?";
    $check_asisten_stmt = $koneksi->prepare($check_asisten_sql);

    if (!$check_asisten_stmt) {
        throw new Exception('Prepare statement for asisten check failed: ' . $koneksi->error);
    }

    $check_asisten_stmt->bind_param("s", $nim);
    $check_asisten_stmt->execute();
    $asisten_result = $check_asisten_stmt->get_result();

    if ($asisten_result->num_rows > 0) {
        throw new Exception('User dengan NIM ini sudah terdaftar sebagai asisten.');
    }

    $check_asisten_stmt->close();

    // --- Bagian 2: Insert data ke tabel asisten ---
    // QUERY SQL SUDAH DISESUAIKAN DENGAN STRUKTUR TABEL ANDA (TANPA user_id)
    $insert_asisten_sql = "INSERT INTO asisten (nim, nama, jabatan, angkatan, status) VALUES (?, ?, ?, ?, ?)";
    $insert_asisten_stmt = $koneksi->prepare($insert_asisten_sql);
    
    if (!$insert_asisten_stmt) {
        throw new Exception('Prepare statement for asisten insert failed: ' . $koneksi->error);
    }
    
    // Pastikan urutan dan tipe parameter sesuai dengan kolom
    $insert_asisten_stmt->bind_param("sssss", $nim, $nama, $jabatan, $angkatan, $status);
    
    if (!$insert_asisten_stmt->execute()) {
        throw new Exception('Gagal menambahkan asisten: ' . $insert_asisten_stmt->error);
    }
    
    $asisten_id = $insert_asisten_stmt->insert_id;
    $insert_asisten_stmt->close();
    
    // Commit transaksi jika semua berhasil
    $koneksi->commit();
    
    // Kirim respons sukses
    echo json_encode([
        'success' => true,
        'message' => 'Asisten berhasil ditambahkan!',
        'data' => [
            'asisten_id' => $asisten_id,
            'nama' => $nama,
            'nim' => $nim,
            'jabatan' => $jabatan,
            'angkatan' => $angkatan,
            'status' => $status
        ]
    ]);
    
} catch (Exception $e) {
    // Rollback transaksi jika ada error
    if (isset($koneksi) && $koneksi->in_transaction) {
        $koneksi->rollback();
    }
    
    // Kirim respons error yang bersih
    echo json_encode([
        'success' => false,
        'message' => 'Gagal menyimpan data: ' . $e->getMessage()
    ]);
    
    // Log error ke file log server
    error_log('Error di add_asisten.php: ' . $e->getMessage());
    
} finally {
    // Pastikan koneksi database ditutup
    if (isset($koneksi) && $koneksi) {
        $koneksi->close();
    }
}
?>