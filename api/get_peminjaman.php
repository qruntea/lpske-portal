<?php
// ===================================================================
// File: api/get_peminjaman.php (VERSI FINAL + ANTI-CACHE)
// ===================================================================

header('Content-Type: application/json');
// --- TAMBAHKAN 3 BARIS DI BAWAH INI UNTUK MEMATIKAN CACHE ---
header('Cache-Control: no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
// -----------------------------------------------------------

session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Anda harus login untuk melihat riwayat.']);
    exit;
}

require 'koneksi.php';

$user_id_peminjam = $_SESSION['user_id'];

try {
    // Query ini sudah benar, tidak perlu diubah
    $sql = "
        SELECT 
            p.id AS peminjaman_id,
            p.inventory_id,
            p.tgl_pinjam,
            p.tgl_rencana_kembali,
            p.tgl_aktual_kembali,
            p.status,
            COALESCE(p.nama_peminjam, u.nama_lengkap, u.username) AS nama_peminjam,
            COALESCE(p.nim_peminjam, u.nomor_induk) AS nim_peminjam,
            i.nama_alat,
            i.kode_alat
        FROM peminjaman p
        LEFT JOIN users u ON p.peminjam_user_id = u.id
        LEFT JOIN inventory i ON p.inventory_id = i.id
        WHERE p.peminjam_user_id = ?
        ORDER BY p.id DESC
    ";

    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("i", $user_id_peminjam);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $data_peminjaman = [];
    while ($row = $result->fetch_assoc()) {
        $data_peminjaman[] = $row;
    }
    
    $stmt->close();
    
    echo json_encode([
        'success' => true,
        'data' => $data_peminjaman
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan pada server: ' . $e->getMessage()
    ]);
} finally {
    if (isset($koneksi)) {
        $koneksi->close();
    }
}
?>