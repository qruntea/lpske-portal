<?php
header('Content-Type: application/json');
session_start();

// Periksa apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Anda harus login untuk melihat riwayat peminjaman.']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Mengimpor file koneksi database
require_once 'db_connect.php';

try {
    // Query untuk mengambil riwayat peminjaman untuk user yang sedang login
    // Menggunakan JOIN untuk mendapatkan nama peminjam (dari tabel users) dan nama alat (dari tabel inventory)
    $sql = "
    SELECT 
        p.id AS peminjaman_id,
        p.user_id,
        p.inventory_id,
        p.tanggal_pinjam AS tgl_pinjam,
        p.tanggal_rencana_kembali AS tgl_rencana_kembali,
        p.tanggal_aktual_kembali AS tgl_aktual_kembali,
        p.status AS status_db,
        u.nama AS nama_peminjam,
        u.nomor_induk AS nim_peminjam,
        i.nama_alat,
        i.kode_alat
    FROM peminjaman p
    JOIN users u ON p.user_id = u.id
    JOIN inventory i ON p.inventory_id = i.id
    WHERE p.user_id = ?
    ORDER BY p.tanggal_pinjam DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $riwayat = [];
    while ($row = $result->fetch_assoc()) {
        $riwayat[] = $row;
    }

    echo json_encode(['success' => true, 'data' => $riwayat]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Gagal memuat riwayat: ' . $e->getMessage()]);
}

$conn->close();
?>