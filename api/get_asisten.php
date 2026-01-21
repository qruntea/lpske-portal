<?php 
// (File: api/get_asisten.php) - Dynamic based on user role
require 'koneksi.php';
session_start();

header('Content-Type: application/json');

// Cek role user untuk menentukan query
$isAdmin = isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'dosen');

if ($isAdmin) {
    // Query lengkap untuk admin/dosen
    $sql = "SELECT id, nim, nama, jabatan, status, angkatan FROM asisten ORDER BY angkatan DESC, nama ASC";
} else {
    // Query sederhana untuk user biasa
    $sql = "SELECT id, nim, nama, jabatan, status, angkatan FROM asisten ORDER BY angkatan DESC, nama ASC";
}

$hasil = mysqli_query($koneksi, $sql);

if (!$hasil) {
    echo json_encode(['error' => 'Query Gagal: ' . mysqli_error($koneksi)]);
    mysqli_close($koneksi);
    exit;
}

$data = [];
while($baris = mysqli_fetch_assoc($hasil)) {
    $data[] = $baris;
}

echo json_encode($data);
mysqli_close($koneksi);
?>