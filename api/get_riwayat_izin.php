<?php
// ===================================================================
// File: get_riwayat_izin.php (SOLUSI AKHIR DAN BENAR-BENAR BENAR)
// Deskripsi: Mengambil data riwayat izin dengan JOIN ke tabel 'dosen'
//            menggunakan kolom 'id'.
// ===================================================================

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Anda belum login. Silakan login terlebih dahulu.']);
    exit;
}

require 'koneksi.php';

$user_id = $_SESSION['user_id'];

try {
    // PERBAIKAN: Mengubah kondisi JOIN menjadi d.id
    $sql = "SELECT 
                i.id,
                i.judul_penelitian,
                i.tgl_pengajuan,
                i.status,
                i.nim,
                i.nama_mahasiswa,
                -- Menggabungkan nama dan gelar dari tabel 'dosen'
                CONCAT_WS(' ', d.gelar_depan, d.nama_dosen, d.gelar_belakang) AS nama_dosen
            FROM 
                izin_penelitian i
            LEFT JOIN 
                dosen d ON i.dosen_user_id = d.id
            WHERE 
                i.mahasiswa_user_id = ?
            ORDER BY 
                i.tgl_pengajuan DESC";

    $stmt = $koneksi->prepare($sql);
    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'Query Gagal: ' . $koneksi->error]);
        exit();
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $hasil = $stmt->get_result();

    $data = [];
    while ($baris = $hasil->fetch_assoc()) {
        $baris['tgl_pengajuan'] = date("d M Y, H:i", strtotime($baris['tgl_pengajuan']));
        $data[] = $baris;
    }

    $stmt->close();
    $koneksi->close();

    echo json_encode(['success' => true, 'data' => $data]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    if (isset($koneksi)) {
        $koneksi->close();
    }
}
?>