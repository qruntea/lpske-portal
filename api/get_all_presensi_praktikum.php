<?php
// File: api/get_all_presensi_praktikum.php

header('Content-Type: application/json');
require 'koneksi.php';

$data = [];
$filter_tanggal = $_GET['tanggal'] ?? null;

if ($filter_tanggal) {
    // Ambil data berdasarkan tanggal yang dikirim
    $sql = "SELECT nama_mahasiswa, nim_mahasiswa, mata_praktikum, tanggal,
                   DATE_FORMAT(waktu_masuk, '%H:%i') as waktu_masuk_formatted, 
                   DATE_FORMAT(waktu_keluar, '%H:%i') as waktu_keluar_formatted, 
                   durasi 
            FROM presensi_praktikum 
            WHERE tanggal = ?
            ORDER BY waktu_masuk DESC";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("s", $filter_tanggal);
    
} else {
    // Ambil semua data jika tidak ada filter tanggal
    $sql = "SELECT nama_mahasiswa, nim_mahasiswa, mata_praktikum, tanggal,
                   DATE_FORMAT(waktu_masuk, '%H:%i') as waktu_masuk_formatted, 
                   DATE_FORMAT(waktu_keluar, '%H:%i') as waktu_keluar_formatted, 
                   durasi 
            FROM presensi_praktikum 
            ORDER BY tanggal DESC, waktu_masuk DESC";
    $stmt = $koneksi->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

$stmt->close();
$koneksi->close();

echo json_encode($data);
?>