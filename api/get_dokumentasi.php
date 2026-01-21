<?php
// ===================================================================
// File: get_dokumentasi.php
// Lokasi: htdocs/lpske-portal/api/get_dokumentasi.php
// Tugas: Mengambil SEMUA dokumentasi kegiatan untuk ditampilkan di halaman
// ===================================================================

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require 'koneksi.php';

try {
    // Query sederhana dulu tanpa JOIN ke users
    $sql = "SELECT 
                id,
                judul_kegiatan,
                deskripsi,
                tanggal_kegiatan,
                url_gambar,
                uploader_user_id
            FROM dokumentasi_kegiatan 
            ORDER BY tanggal_kegiatan DESC";
    
    $hasil = mysqli_query($koneksi, $sql);
    
    if (!$hasil) {
        throw new Exception("Query error: " . mysqli_error($koneksi));
    }
    
    $dokumentasi = [];
    while ($row = mysqli_fetch_assoc($hasil)) {
        // Format tanggal untuk tampilan
        $row['tanggal_formatted'] = date("d F Y", strtotime($row['tanggal_kegiatan']));
        
        // Tentukan kategori berdasarkan kata kunci dalam judul
        $judul_lower = strtolower($row['judul_kegiatan']);
        if (strpos($judul_lower, 'workshop') !== false || strpos($judul_lower, 'pelatihan') !== false) {
            $row['kategori'] = 'Pelatihan';
        } elseif (strpos($judul_lower, 'seminar') !== false || strpos($judul_lower, 'event') !== false) {
            $row['kategori'] = 'Event';
        } elseif (strpos($judul_lower, 'lab') !== false || strpos($judul_lower, 'fasilitas') !== false) {
            $row['kategori'] = 'Fasilitas';
        } elseif (strpos($judul_lower, 'penelitian') !== false || strpos($judul_lower, 'riset') !== false) {
            $row['kategori'] = 'Penelitian';
        } elseif (strpos($judul_lower, 'penghargaan') !== false || strpos($judul_lower, 'award') !== false) {
            $row['kategori'] = 'Penghargaan';
        } else {
            $row['kategori'] = 'Kegiatan';
        }
        
        // Set uploader default
        $row['uploader_nama'] = 'Admin LPSKE';
        
        $dokumentasi[] = $row;
    }
    
    // Kirim response sukses
    echo json_encode([
        'success' => true,
        'message' => 'Data berhasil diambil',
        'count' => count($dokumentasi),
        'data' => $dokumentasi
    ]);
    
} catch (Exception $e) {
    // Kirim response error
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
        'data' => []
    ]);
}

mysqli_close($koneksi);
?>