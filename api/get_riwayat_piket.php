<?php
// File: api/get_riwayat_piket.php

header('Content-Type: application/json');
session_start();
date_default_timezone_set('Asia/Jakarta');

if (!isset($_SESSION['presensi_piket_hari_ini'])) {
    $_SESSION['presensi_piket_hari_ini'] = [];
}

$currentDate = date('Y-m-d');
$riwayat_hari_ini = [];

foreach ($_SESSION['presensi_piket_hari_ini'] as $presensi) {
    if ($presensi['tanggal'] === $currentDate) {
        // Format tanggal agar lebih mudah dibaca
        $dateObj = DateTime::createFromFormat('Y-m-d', $presensi['tanggal']);
        $presensi['tanggal_formatted'] = $dateObj ? $dateObj->format('d M Y') : $presensi['tanggal'];

        // Hitung durasi jika sudah clock out
        if ($presensi['clock_in'] && $presensi['clock_out']) {
            $clock_in_time = new DateTime($presensi['clock_in']);
            $clock_out_time = new DateTime($presensi['clock_out']);
            $durasi = $clock_in_time->diff($clock_out_time);
            $presensi['durasi'] = $durasi->format('%H jam %i menit');
        } else {
            $presensi['durasi'] = '-';
        }

        $riwayat_hari_ini[] = $presensi;
    }
}

// Urutkan berdasarkan waktu clock in terbaru di atas
usort($riwayat_hari_ini, function($a, $b) {
    return strtotime($b['clock_in']) - strtotime($a['clock_in']);
});

echo json_encode(['success' => true, 'data' => $riwayat_hari_ini]);
?>