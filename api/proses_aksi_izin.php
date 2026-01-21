<?php
// ===================================================================
// File: proses_aksi_izin.php (FILE BARU)
// Lokasi: htdocs/lstars-portal/api/proses_aksi_izin.php
// Tugas: Memproses persetujuan atau penolakan izin penelitian
// ===================================================================

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require 'koneksi.php';

// Pastikan method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);
    exit;
}

try {
    // Ambil data dari POST
    $izin_id = isset($_POST['izin_id']) ? (int)$_POST['izin_id'] : 0;
    $action = isset($_POST['action']) ? trim($_POST['action']) : '';
    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0; // ID admin/dosen yang melakukan aksi
    
    // Validasi input
    if ($izin_id <= 0) {
        throw new Exception('ID izin tidak valid');
    }
    
    if (!in_array($action, ['setujui', 'tolak'])) {
        throw new Exception('Aksi tidak valid');
    }
    
    if ($user_id <= 0) {
        throw new Exception('User ID tidak valid');
    }
    
    // Tentukan status baru berdasarkan aksi
    $status_baru = ($action === 'setujui') ? 'Disetujui' : 'Ditolak';
    $action_text = ($action === 'setujui') ? 'disetujui' : 'ditolak';
    
    // Mulai transaksi
    mysqli_autocommit($koneksi, false);
    
    // 1. Cek apakah izin penelitian ada dan masih berstatus "Diajukan"
    $check_sql = "SELECT id, status, judul_penelitian FROM izin_penelitian WHERE id = ? AND status = 'Diajukan'";
    $check_stmt = mysqli_prepare($koneksi, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "i", $izin_id);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    
    if (mysqli_num_rows($check_result) === 0) {
        throw new Exception('Izin penelitian tidak ditemukan atau sudah diproses sebelumnya');
    }
    
    $izin_data = mysqli_fetch_assoc($check_result);
    
    // 2. Update status izin penelitian
    $update_sql = "UPDATE izin_penelitian 
                   SET status = ?, 
                       tgl_diproses = NOW(),
                       diproses_oleh = ?
                   WHERE id = ?";
    
    $update_stmt = mysqli_prepare($koneksi, $update_sql);
    mysqli_stmt_bind_param($update_stmt, "sii", $status_baru, $user_id, $izin_id);
    
    if (!mysqli_stmt_execute($update_stmt)) {
        throw new Exception('Gagal mengupdate status izin: ' . mysqli_error($koneksi));
    }
    
    // 3. Log aktivitas (opsional - buat tabel log jika diperlukan)
    $log_sql = "INSERT INTO log_aktivitas (user_id, aktivitas, detail, tgl_aktivitas) 
                VALUES (?, ?, ?, NOW())";
    $log_stmt = mysqli_prepare($koneksi, $log_sql);
    $aktivitas = "Aksi Izin Penelitian";
    $detail = "Izin penelitian '{$izin_data['judul_penelitian']}' telah {$action_text}";
    mysqli_stmt_bind_param($log_stmt, "iss", $user_id, $aktivitas, $detail);
    mysqli_stmt_execute($log_stmt); // Tidak wajib berhasil
    
    // Commit transaksi
    mysqli_commit($koneksi);
    
    echo json_encode([
        'success' => true,
        'message' => "Izin penelitian berhasil {$action_text}!",
        'status_baru' => $status_baru
    ]);
    
} catch (Exception $e) {
    // Rollback transaksi jika ada error
    mysqli_rollback($koneksi);
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} finally {
    // Restore autocommit
    mysqli_autocommit($koneksi, true);
    
    if (isset($koneksi)) {
        mysqli_close($koneksi);
    }
}
?>