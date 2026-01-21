<?php
require 'koneksi.php';

// Get users yang belum jadi asisten atau bisa jadi asisten
$sql = "SELECT u.id, u.nama_lengkap, u.nomor_induk 
        FROM users u 
        WHERE u.role = 'user' 
        ORDER BY u.nama_lengkap ASC";
        
$hasil = mysqli_query($koneksi, $sql);
$data = [];

if ($hasil) {
    while($baris = mysqli_fetch_assoc($hasil)) {
        $data[] = $baris;
    }
}

echo json_encode($data);
mysqli_close($koneksi);
?>