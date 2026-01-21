<?php
// File: api/get_peminjaman_admin.php (VERSI FINAL SESUAI STRUKTUR DB ANDA)

header('Content-Type: application/json');
require 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

// Pastikan hanya admin yang bisa akses
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
    exit;
}

try {
    // ===== QUERY SQL YANG SUDAH DISESUAIKAN DENGAN STRUKTUR TABEL ANDA =====
    $sql = "SELECT 
                p.id as peminjaman_id,
                p.inventory_id,
                p.peminjam_user_id as user_id, -- Menggunakan kolom `peminjam_user_id`
                p.status,
                p.tgl_pinjam,
                p.tgl_rencana_kembali,
                p.tgl_aktual_kembali,
                
                -- Mengambil nama dan nim langsung dari tabel peminjaman
                p.nama_peminjam,
                p.nim_peminjam AS nomor_induk_peminjam, -- Menggunakan `nim_peminjam` dan di-alias-kan
                
                -- Mengambil role dari tabel users untuk badge (Asisten/Dosen)
                u.role AS role_peminjam,
                
                -- Mengambil detail alat dari tabel inventory
                i.nama_alat,
                i.kode_alat,
                i.kategori,
                i.lokasi
            FROM 
                peminjaman p
            LEFT JOIN 
                users u ON p.peminjam_user_id = u.id -- Kondisi JOIN diperbaiki ke `peminjam_user_id`
            LEFT JOIN 
                inventory i ON p.inventory_id = i.id
            ORDER BY 
                CASE
                    WHEN p.status = 'Dipinjam' AND p.tgl_rencana_kembali < CURDATE() THEN 1
                    WHEN p.status = 'Diajukan' THEN 2
                    WHEN p.status = 'Dipinjam' THEN 3
                    ELSE 4
                END,
                p.tgl_pinjam DESC";

    $result = $koneksi->query($sql);
    
    if ($result === false) {
        // Jika query gagal, tampilkan error SQL
        throw new Exception("SQL Error: " . $koneksi->error);
    }

    $data = [];
    $today = new DateTime();
    $today->setTime(0, 0, 0);

    while ($row = $result->fetch_assoc()) {
        $row['status_display'] = $row['status'];
        if ($row['status'] === 'Dipinjam') {
            $tgl_rencana_kembali = new DateTime($row['tgl_rencana_kembali']);
            if ($tgl_rencana_kembali < $today) {
                $row['status_display'] = 'Terlambat';
            }
        }
        
        $row['tgl_pinjam_formatted'] = $row['tgl_pinjam'] ? date('d M Y', strtotime($row['tgl_pinjam'])) : '-';
        $row['tgl_rencana_kembali_formatted'] = $row['tgl_rencana_kembali'] ? date('d M Y', strtotime($row['tgl_rencana_kembali'])) : '-';
        $row['tgl_aktual_kembali_formatted'] = $row['tgl_aktual_kembali'] ? date('d M Y', strtotime($row['tgl_aktual_kembali'])) : '-';
        
        if ($row['tgl_aktual_kembali']) {
            $pinjam = new DateTime($row['tgl_pinjam']);
            $kembali = new DateTime($row['tgl_aktual_kembali']);
            $row['durasi_hari'] = $kembali->diff($pinjam)->days;
        } else {
            $pinjam = new DateTime($row['tgl_pinjam']);
            $row['durasi_hari'] = $today->diff($pinjam)->days;
        }

        $data[] = $row;
    }

    echo json_encode([
        'success' => true,
        'data' => $data
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server Error: ' . $e->getMessage()]);
}

$koneksi->close();
?>