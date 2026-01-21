<?php
// File: api/get_presensi_status_by_nim.php
header('Content-Type: application/json');
require 'koneksi.php';

$nim = $_GET['nim'] ?? '';
$today = date('Y-m-d');

if (empty($nim)) {
    echo json_encode(['status' => 'belum piket']);
    exit;
}

$stmt = $koneksi->prepare("SELECT status, TIME_FORMAT(waktu_masuk, '%H:%i') as waktu_masuk FROM presensi_piket WHERE nim_asisten = ? AND tanggal = ? ORDER BY id DESC LIMIT 1");
$stmt->bind_param("ss", $nim, $today);
$stmt->execute();
$result = $stmt->get_result();
$presensi = $result->fetch_assoc();
$stmt->close();
$koneksi->close();

if ($presensi) {
    echo json_encode($presensi);
} else {
    echo json_encode(['status' => 'belum piket']);
}
?>