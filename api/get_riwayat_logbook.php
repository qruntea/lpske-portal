<?php
// get_riwayat_logbook.php
header('Content-Type: application/json');
require 'koneksi.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Tidak terautentikasi']);
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT nama_pengisi, nim_pengisi, judul, tanggal_kegiatan, deskripsi
        FROM logbook
        WHERE user_id = ?
        ORDER BY tanggal_kegiatan DESC";

$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode(['success' => true, 'data' => $data]);

$stmt->close();
mysqli_close($koneksi);
?>