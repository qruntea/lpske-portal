<?php
// File: api/get_admin_izin.php (VERSI FINAL SESUAI STRUKTUR DB ANDA)

header('Content-Type: application/json');
require 'koneksi.php';

session_start();
// Keamanan: Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
    exit;
}

try {
    // ===== QUERY SQL YANG SUDAH DISESUAIKAN DENGAN STRUKTUR TABEL ANDA =====
    $sql = "SELECT 
                p.id,
                p.judul_penelitian,
                p.nama_mahasiswa,
                p.nim, -- **KITA AMBIL KOLOM `nim` DARI SINI**
                p.status,
                DATE_FORMAT(p.tgl_pengajuan, '%d %b %Y, %H:%i') as tgl_pengajuan,
                -- Menggabungkan nama dosen dari tabel `dosen`
                CONCAT_WS(' ', d.gelar_depan, d.nama_dosen, d.gelar_belakang) as nama_dosen
            FROM 
                izin_penelitian p
            LEFT JOIN 
                dosen d ON p.dosen_user_id = d.id -- Menggabungkan berdasarkan ID Dosen
            ORDER BY 
                p.tgl_pengajuan DESC";

    $result = $koneksi->query($sql);

    // Jika query gagal, hentikan dengan pesan error
    if ($result === false) {
        throw new Exception("SQL Error: " . $koneksi->error);
    }
    
    // Ambil semua data sekaligus
    $data = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode(['success' => true, 'data' => $data]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server Error: ' . $e->getMessage()]);
}

$koneksi->close();
?>