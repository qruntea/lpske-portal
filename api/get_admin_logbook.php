<?php
// File: api/get_admin_logbook.php

header('Content-Type: application/json');
require 'koneksi.php'; // Pastikan path ini benar menuju file koneksi Anda

session_start();

// Keamanan: Hanya admin yang boleh mengakses data ini
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403); // Forbidden
    echo json_encode(['success' => false, 'message' => 'Akses ditolak. Anda bukan admin.']);
    exit;
}

// Query untuk mengambil semua data logbook, diurutkan dari yang terbaru
$sql = "SELECT id, user_id, nama_pengisi, nim_pengisi, judul, tanggal_kegiatan, deskripsi, created_at 
        FROM logbook 
        ORDER BY tanggal_kegiatan DESC, created_at DESC";

$result = $koneksi->query($sql);

if (!$result) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'message' => 'Query database gagal: ' . $koneksi->error]);
    exit;
}

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode(['success' => true, 'data' => $data]);

$koneksi->close();
?>