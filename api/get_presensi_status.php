<?php
header('Content-Type: application/json');
require 'koneksi.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Tidak terautentikasi.']);
    exit();
}
$user_id = $_SESSION['user_id'];
$today = date("Y-m-d");

// Cek apakah user sudah clock in hari ini di tabel presensi_piket
$sql = "SELECT id FROM presensi_piket WHERE asisten_user_id = ? AND tanggal = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("is", $user_id, $today);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Jika sudah ada record, berarti sudah selesai presensi
    echo json_encode(['status' => 'completed']);
} else {
    // Jika belum ada, bisa clock in
    echo json_encode(['status' => 'not_clocked_in']);
}
$stmt->close();
$koneksi->close();
?>