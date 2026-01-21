<?php
// File: api/get_presensi_statistics.php (VERSI FINAL YANG BENAR)
require 'koneksi.php'; 

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
    exit;
}

header('Content-Type: application/json');

try {
    $today = date('Y-m-d');
    $thisMonth = date('Y-m');

    // 1. Presensi Hari Ini (Total)
    $stmt_today = $koneksi->prepare("SELECT COUNT(*) as count FROM presensi_piket WHERE tanggal = ?");
    $stmt_today->bind_param("s", $today);
    $stmt_today->execute();
    $hari_ini = $stmt_today->get_result()->fetch_assoc()['count'];
    $stmt_today->close();

    // 2. Sedang Piket (Aktif Hari Ini) - INI BAGIAN YANG DIPERBAIKI
    $stmt_active = $koneksi->prepare("SELECT COUNT(*) as count FROM presensi_piket WHERE tanggal = ? AND status = 'sedang piket'");
    $stmt_active->bind_param("s", $today);
    $stmt_active->execute();
    $sedang_piket = $stmt_active->get_result()->fetch_assoc()['count'];
    $stmt_active->close();

    // 3. Total Bulan Ini
    $stmt_month = $koneksi->prepare("SELECT COUNT(*) as count FROM presensi_piket WHERE DATE_FORMAT(tanggal, '%Y-%m') = ?");
    $stmt_month->bind_param("s", $thisMonth);
    $stmt_month->execute();
    $bulan_ini = $stmt_month->get_result()->fetch_assoc()['count'];
    $stmt_month->close();
    
    // 4. Rata-rata jam per hari (30 hari terakhir)
    $stmt_avg = $koneksi->prepare(
        "SELECT AVG(TIMESTAMPDIFF(HOUR, waktu_masuk, waktu_keluar)) as avg_hours 
         FROM presensi_piket 
         WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND waktu_keluar IS NOT NULL"
    );
    $stmt_avg->execute();
    $avg_result = $stmt_avg->get_result()->fetch_assoc();
    $rata_rata = round($avg_result['avg_hours'] ?? 0, 1);
    $stmt_avg->close();

    echo json_encode([
        'success' => true,
        'hari_ini' => $hari_ini,
        'sedang_piket' => $sedang_piket,
        'bulan_ini' => $bulan_ini,
        'rata_rata' => $rata_rata . 'h'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$koneksi->close();
?>