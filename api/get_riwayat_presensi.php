<?php
// File: api/get_riwayat_presensi.php (VERSI FINAL BERSIH)

header('Content-Type: application/json');
require 'koneksi.php';

date_default_timezone_set('Asia/Jakarta');
$today = date('Y-m-d');

try {
    $stmt = $koneksi->prepare(
        "SELECT 
            nama_asisten,
            nim_asisten,
            DATE_FORMAT(tanggal, '%d %b %Y') as tanggal_formatted,
            DATE_FORMAT(waktu_masuk, '%H:%i:%s') as waktu_masuk_formatted,
            DATE_FORMAT(waktu_keluar, '%H:%i:%s') as waktu_keluar_formatted,
            durasi
        FROM presensi_piket
        WHERE tanggal = ?
        ORDER BY waktu_masuk DESC"
    );

    $stmt->bind_param("s", $today);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    echo json_encode($data);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'SQL Error: ' . $e->getMessage()]);
}

$koneksi->close();
?>