<?php
// proses_logbook.php
header('Content-Type: application/json');
require 'koneksi.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Anda harus login untuk menyimpan logbook.']);
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_pengisi = $_POST['nama_pengisi'];
    $nim_pengisi = $_POST['nim_pengisi'];
    $judul_kegiatan = $_POST['judul'];
    $tanggal_kegiatan = $_POST['tanggal_kegiatan'];
    $deskripsi_kegiatan = $_POST['deskripsi'];

    // Gunakan prepared statement untuk keamanan
    $sql = "INSERT INTO logbook (user_id, nama_pengisi, nim_pengisi, judul, tanggal_kegiatan, deskripsi) VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $koneksi->prepare($sql);
    
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $koneksi->error]);
        exit;
    }
    
    // 'isssss' -> integer, string, string, string, string, string
    $stmt->bind_param("isssss", $user_id, $nama_pengisi, $nim_pengisi, $judul_kegiatan, $tanggal_kegiatan, $deskripsi_kegiatan);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Catatan logbook berhasil disimpan.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menyimpan logbook. Error: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Metode permintaan tidak valid.']);
}

mysqli_close($koneksi);
?>