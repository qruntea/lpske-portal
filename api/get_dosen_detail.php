<?php
require 'koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id === 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID dosen tidak valid.']);
    exit();
}

$sql = "SELECT 
            u.nama_lengkap,
            u.email,
            u.nomor_induk,
            d.nidn,
            d.nip,
            d.gelar_depan,
            d.nama_dosen,
            d.gelar_belakang,
            d.homebase_prodi,
            d.foto
        FROM users u
        LEFT JOIN dosen d ON u.id = d.user_id
        WHERE u.id = ? AND u.role = 'dosen'";

$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$hasil = $stmt->get_result();

$data = null;
if ($hasil && $row = $hasil->fetch_assoc()) {
    // Jika foto kosong, isi default
    if (empty($row['foto'])) {
        $row['foto'] = 'default-placeholder.jpg';
    }
    $data = $row;
}

if ($data) {
    echo json_encode($data);
} else {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Detail dosen tidak ditemukan.']);
}

mysqli_close($koneksi);
?>