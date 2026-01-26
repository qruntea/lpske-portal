<?php
// ===================================================================
// File: get_peminjaman_aktif.php (VERSI DIPERBAIKI)
// Lokasi: htdocs/lpske-portal/api/get_peminjaman_aktif.php
// Tugas: Mengambil data peminjaman dengan status 'Dipinjam'
// ===================================================================

// Set header untuk JSON response
header('Content-Type: application/json');

// Mulai session untuk checking (optional, tapi recommended)
session_start();

try {
    // Include file koneksi
    require 'koneksi.php';
    
    // PERBAIKAN 1: Tambahkan error checking untuk koneksi
    if (!$koneksi) {
        throw new Exception('Koneksi database gagal: ' . mysqli_connect_error());
    }
    
    // PERBAIKAN 2: Query diperbaiki dengan format tanggal dan field tambahan
    $sql = "SELECT 
                p.id as peminjaman_id,
                p.inventory_id,
                p.peminjam_user_id,
                p.tgl_pinjam, 
                p.tgl_rencana_kembali,
                p.tgl_aktual_kembali,
                p.status,
                p.catatan,
                
                -- Data inventory
                i.nama_buku,
                i.kode_buku,
                i.kategori,
                i.lokasi,
                
                -- Data peminjam
                u.nama_lengkap as nama_peminjam,
                u.username as username_peminjam,
                u.email as email_peminjam,
                
                -- Format tanggal untuk tampilan
                DATE_FORMAT(p.tgl_pinjam, '%d/%m/%Y') as tgl_pinjam_formatted,
                DATE_FORMAT(p.tgl_rencana_kembali, '%d/%m/%Y') as tgl_rencana_kembali_formatted,
                
                -- Hitung durasi peminjaman
                DATEDIFF(CURDATE(), p.tgl_pinjam) as durasi_hari,
                
                -- Status keterlambatan
                CASE 
                    WHEN CURDATE() > p.tgl_rencana_kembali THEN 'Terlambat'
                    WHEN CURDATE() = p.tgl_rencana_kembali THEN 'Jatuh Tempo Hari Ini'
                    ELSE 'Normal'
                END as status_keterlambatan
                
            FROM peminjaman p
            JOIN inventory i ON p.inventory_id = i.id
            JOIN users u ON p.peminjam_user_id = u.id
            WHERE p.status = 'Dipinjam'
            ORDER BY p.tgl_pinjam ASC";

    // PERBAIKAN 3: Gunakan prepared statement untuk keamanan (opsional untuk query SELECT tanpa parameter)
    $hasil = mysqli_query($koneksi, $sql);
    
    // PERBAIKAN 4: Error handling untuk query
    if (!$hasil) {
        throw new Exception('Query gagal: ' . mysqli_error($koneksi));
    }
    
    // PERBAIKAN 5: Proses data dengan error handling
    $data = [];
    while($baris = mysqli_fetch_assoc($hasil)) {
        // Format data untuk konsistensi dengan frontend
        $data[] = [
            'peminjaman_id' => $baris['peminjaman_id'],
            'inventory_id' => $baris['inventory_id'],
            'peminjam_user_id' => $baris['peminjam_user_id'],
            'nama_alat' => $baris['nama_buku'],
            'kode_alat' => $baris['kode_buku'],
            'kategori' => $baris['kategori'],
            'lokasi' => $baris['lokasi'],
            'nama_peminjam' => $baris['nama_peminjam'],
            'username_peminjam' => $baris['username_peminjam'],
            'email_peminjam' => $baris['email_peminjam'],
            'tgl_pinjam' => $baris['tgl_pinjam'],
            'tgl_rencana_kembali' => $baris['tgl_rencana_kembali'],
            'tgl_pinjam_formatted' => $baris['tgl_pinjam_formatted'],
            'tgl_rencana_kembali_formatted' => $baris['tgl_rencana_kembali_formatted'],
            'durasi_hari' => (int)$baris['durasi_hari'],
            'status' => $baris['status'],
            'status_keterlambatan' => $baris['status_keterlambatan'],
            'catatan' => $baris['catatan']
        ];
    }
    
    // PERBAIKAN 6: Response format yang konsisten
    $response = [
        'success' => true,
        'data' => $data,
        'count' => count($data),
        'message' => count($data) > 0 ? 'Data peminjaman aktif berhasil dimuat' : 'Tidak ada peminjaman aktif',
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    echo json_encode($response, JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    // PERBAIKAN 7: Error handling yang proper
    $error_response = [
        'success' => false,
        'data' => [],
        'count' => 0,
        'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
        'error_details' => [
            'file' => basename(__FILE__),
            'line' => $e->getLine()
        ],
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    // Log error untuk debugging (opsional)
    error_log("Error in get_peminjaman_aktif.php: " . $e->getMessage());
    
    echo json_encode($error_response, JSON_PRETTY_PRINT);
    
} finally {
    // PERBAIKAN 8: Pastikan koneksi ditutup dalam segala kondisi
    if (isset($koneksi) && $koneksi) {
        mysqli_close($koneksi);
    }
}

?>

<?php
// ===================================================================
// VERSI SEDERHANA (jika Anda ingin tetap simpel seperti kode asli)
// ===================================================================

/*
header('Content-Type: application/json');

require 'koneksi.php';

$sql = "SELECT 
            p.id as peminjaman_id,
            p.inventory_id,
            p.tgl_pinjam, 
            p.tgl_rencana_kembali,
            i.nama_alat,
            i.kode_alat,
            u.nama_lengkap as nama_peminjam,
            DATE_FORMAT(p.tgl_pinjam, '%d/%m/%Y') as tgl_pinjam_formatted,
            DATE_FORMAT(p.tgl_rencana_kembali, '%d/%m/%Y') as tgl_rencana_kembali_formatted
        FROM peminjaman p
        JOIN inventory i ON p.inventory_id = i.id
        JOIN users u ON p.peminjam_user_id = u.id
        WHERE p.status = 'Dipinjam'
        ORDER BY p.tgl_pinjam ASC";

$hasil = mysqli_query($koneksi, $sql);

$data = [];
if ($hasil) {
    while($baris = mysqli_fetch_assoc($hasil)) {
        $data[] = $baris;
    }
}

// Response format yang konsisten
echo json_encode([
    'success' => true,
    'data' => $data,
    'count' => count($data)
]);

mysqli_close($koneksi);
*/

?>