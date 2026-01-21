<?php
// ===================================================================
// File: update_asisten.php - FINAL FIXED VERSION (TANPA user_id)
// ===================================================================

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

try {
    require 'koneksi.php';

    if (!$koneksi) {
        throw new Exception('Database connection failed.');
    }
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method.');
    }

    $asisten_id = trim($_POST['asisten_id'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $nim = trim($_POST['nim'] ?? '');
    $jabatan = trim($_POST['jabatan'] ?? '');
    $angkatan = trim($_POST['angkatan'] ?? '');
    $status = trim($_POST['status'] ?? '');
    
    if (empty($asisten_id) || empty($nama) || empty($nim) || empty($jabatan) || empty($angkatan) || empty($status)) {
        throw new Exception('Semua field harus diisi!');
    }
    
    $koneksi->begin_transaction();
    
    // --- Periksa apakah asisten dengan ID ini ada ---
    $check_sql = "SELECT id FROM asisten WHERE id = ?";
    $check_stmt = $koneksi->prepare($check_sql);
    if (!$check_stmt) {
        throw new Exception('Prepare statement for check failed: ' . $koneksi->error);
    }
    $check_stmt->bind_param("i", $asisten_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Asisten tidak ditemukan.');
    }
    $check_stmt->close();
    
    // --- Update data asisten di tabel `asisten` ---
    // QUERY SQL SUDAH DISESUAIKAN DENGAN STRUKTUR TABEL ANDA (tanpa user_id)
    $update_sql = "UPDATE asisten SET nim = ?, nama = ?, jabatan = ?, angkatan = ?, status = ? WHERE id = ?";
    $update_stmt = $koneksi->prepare($update_sql);
    
    if (!$update_stmt) {
        throw new Exception('Prepare statement for update failed: ' . $koneksi->error);
    }
    
    // Pastikan urutan dan tipe parameter sesuai dengan kolom
    $update_stmt->bind_param("sssssi", $nim, $nama, $jabatan, $angkatan, $status, $asisten_id);
    
    if (!$update_stmt->execute()) {
        throw new Exception('Gagal mengupdate asisten: ' . $update_stmt->error);
    }
    $update_stmt->close();
    
    $koneksi->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Data asisten berhasil diperbarui!'
    ]);
    
} catch (Exception $e) {
    if (isset($koneksi) && $koneksi->in_transaction) {
        $koneksi->rollback();
    }
    
    echo json_encode([
        'success' => false,
        'message' => 'Gagal menyimpan data: ' . $e->getMessage()
    ]);
    
    error_log('Error di update_asisten.php: ' . $e->getMessage());
    
} finally {
    if (isset($koneksi) && $koneksi) {
        $koneksi->close();
    }
}
?>