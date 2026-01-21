<?php
// api/get_presensi_hari_ini.php (FIXED STATUS LOGIC)
require 'koneksi.php';

// Check admin access
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied']);
    exit;
}

try {
    $today = date('Y-m-d');

    $sql = "SELECT 
                p.id,
                p.tanggal,
                p.waktu_masuk,
                p.waktu_keluar,
                p.asisten_user_id,
                u.nama_lengkap as nama_asisten,
                u.nomor_induk,
                u.role,
                DATE_FORMAT(p.waktu_masuk, '%H:%i') as waktu_masuk_formatted,
                DATE_FORMAT(p.waktu_keluar, '%H:%i') as waktu_keluar_formatted,
                -- FIXED: Hitung status berdasarkan waktu_keluar, gunakan 'sedang piket' dan 'selesai'
                CASE 
                    WHEN p.waktu_keluar IS NULL THEN 'sedang piket'
                    ELSE 'selesai'
                END as status_calculated,
                CASE 
                    WHEN p.waktu_keluar IS NOT NULL THEN 
                        CONCAT(
                            FLOOR(TIMESTAMPDIFF(MINUTE, p.waktu_masuk, p.waktu_keluar) / 60), 'h ',
                            TIMESTAMPDIFF(MINUTE, p.waktu_masuk, p.waktu_keluar) % 60, 'm'
                        )
                    ELSE '-'
                END as durasi
            FROM presensi_piket p
            LEFT JOIN users u ON p.asisten_user_id = u.id
            WHERE DATE(p.tanggal) = '$today'
            ORDER BY p.waktu_masuk DESC";

    $result = mysqli_query($koneksi, $sql);
    if (!$result) {
        throw new Exception("Query error: " . mysqli_error($koneksi));
    }

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Fallback jika nama tidak ditemukan
        if (!$row['nama_asisten']) {
            $row['nama_asisten'] = 'User ID: ' . $row['asisten_user_id'];
        }
        
        // FIXED: Gunakan status_calculated, bukan status dari database
        $row['status'] = $row['status_calculated'];
        
        $data[] = $row;
    }

    // Return dengan debug info
    echo json_encode([
        'data' => $data,
        'total_today' => count($data),
        'query_date' => $today
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

mysqli_close($koneksi);
?>