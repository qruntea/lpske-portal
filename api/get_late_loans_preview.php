<?php
// ===================================================================
// File: get_late_loans_preview.php
// Lokasi: htdocs/lpske-portal/api/get_late_loans_preview.php
// Fungsi: Preview peminjaman yang terlambat sebelum kirim notifikasi
// ===================================================================

session_start();

// Cek apakah user adalah admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak. Hanya admin yang dapat mengakses.']);
    exit();
}

require 'koneksi.php';
header('Content-Type: application/json');

try {
    // Query untuk mendapatkan preview peminjaman yang terlambat
    $sql = "
        SELECT 
            p.id AS peminjaman_id,
            p.peminjam_user_id,
            p.tgl_pinjam,
            p.tgl_rencana_kembali,
            DATEDIFF(CURDATE(), p.tgl_rencana_kembali) as hari_terlambat,
            
            -- Data Peminjam
            u.nama_lengkap AS nama_peminjam,
            u.nomor_induk,
            u.email,
            u.role,
            
            -- Data Alat
            i.nama_alat,
            i.kode_alat,
            i.kategori,
            i.lokasi
            
        FROM peminjaman p
        LEFT JOIN users u ON p.peminjam_user_id = u.id
        LEFT JOIN inventory i ON p.inventory_id = i.id
        WHERE p.tgl_aktual_kembali IS NULL 
        AND p.tgl_rencana_kembali < CURDATE()
        ORDER BY hari_terlambat DESC, p.tgl_pinjam ASC
    ";
    
    $result = $koneksi->query($sql);
    
    if (!$result) {
        throw new Exception("Query error: " . $koneksi->error);
    }
    
    $late_loans = [];
    while ($row = $result->fetch_assoc()) {
        $late_loans[] = [
            'peminjaman_id' => $row['peminjaman_id'],
            'nama_peminjam' => $row['nama_peminjam'],
            'nomor_induk' => $row['nomor_induk'],
            'email' => $row['email'],
            'role' => $row['role'],
            'nama_alat' => $row['nama_alat'],
            'kode_alat' => $row['kode_alat'],
            'kategori' => $row['kategori'],
            'lokasi' => $row['lokasi'],
            'tgl_pinjam' => date('d/m/Y', strtotime($row['tgl_pinjam'])),
            'tgl_rencana_kembali' => date('d/m/Y', strtotime($row['tgl_rencana_kembali'])),
            'hari_terlambat' => (int)$row['hari_terlambat']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'message' => count($late_loans) . ' peminjaman terlambat ditemukan',
        'count' => count($late_loans),
        'data' => $late_loans,
        'query_info' => [
            'executed_at' => date('Y-m-d H:i:s'),
            'admin_id' => $_SESSION['user_id']
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
} finally {
    if (isset($result)) {
        $result->free();
    }
    $koneksi->close();
}
?>