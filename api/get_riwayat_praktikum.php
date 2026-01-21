<?php
// File: api/get_riwayat_praktikum.php

header('Content-Type: application/json');
require 'koneksi.php';

$data = [];
$tanggal_sekarang = date('Y-m-d');

// HANYA ambil data untuk hari ini
$sql = "SELECT nama_mahasiswa, nim_mahasiswa, mata_praktikum,
               DATE_FORMAT(waktu_masuk, '%H:%i') as waktu_masuk_formatted, 
               DATE_FORMAT(waktu_keluar, '%H:%i') as waktu_keluar_formatted, 
               durasi 
        FROM presensi_praktikum 
        WHERE tanggal = ? 
        ORDER BY waktu_masuk DESC";

$stmt = $koneksi->prepare($sql);
$stmt->bind_param("s", $tanggal_sekarang);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

$stmt->close();
$koneksi->close();

echo json_encode($data);
?>