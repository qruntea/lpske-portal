<?php
// Selama pengembangan, sangat disarankan untuk menampilkan error
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
require 'koneksi.php';

if (!$koneksi) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Koneksi ke database gagal.']);
    exit;
}

// PERBAIKAN:
// 1. SELECT kolom `d.id` sebagai ID utama.
// 2. Gunakan LEFT JOIN untuk mengambil `email` dan `username` dari tabel `users`.
$sql = "SELECT 
            d.id, 
            d.nidn, 
            d.nip, 
            d.gelar_depan, 
            d.nama_dosen, 
            d.gelar_belakang, 
            d.homebase_prodi,
            d.foto,
            u.email,
            u.username
        FROM dosen d
        LEFT JOIN users u ON d.user_id = u.id
        ORDER BY d.nama_dosen ASC";
        
$hasil = mysqli_query($koneksi, $sql);

if (!$hasil) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Query Gagal: ' . mysqli_error($koneksi)]);
    mysqli_close($koneksi);
    exit;
}

$data = [];
while($baris = mysqli_fetch_assoc($hasil)) {
    // Tidak perlu lagi membuat ID palsu, karena kita sudah mengambil `id` yang benar.
    $data[] = $baris;
}

echo json_encode($data);
mysqli_close($koneksi);
?>