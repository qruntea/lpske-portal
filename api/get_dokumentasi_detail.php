<?php
// ===================================================================
// File: get_dokumentasi_detail.php
// Lokasi: htdocs/lpske-portal/api/get_dokumentasi_detail.php
// Tugas: Mengambil detail dari SATU kegiatan berdasarkan ID.
// ===================================================================

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require 'koneksi.php';

// Ambil ID dari URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id === 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID kegiatan tidak valid.']);
    exit();
}

try {
    // Query sederhana tanpa JOIN
    $sql = "SELECT 
                id,
                judul_kegiatan,
                deskripsi,
                tanggal_kegiatan,
                url_gambar,
                uploader_user_id
            FROM dokumentasi_kegiatan
            WHERE id = ?";

    $stmt = $koneksi->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . mysqli_error($koneksi));
    }
    
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $hasil = $stmt->get_result();

    $data = null;
    if ($hasil && $row = $hasil->fetch_assoc()) {
        // Format tanggal agar lebih mudah dibaca
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
        
        $data = $row;
    }

    if ($data) {
        echo json_encode([
            'success' => true,
            'message' => 'Data berhasil diambil',
            'data' => $data
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            'success' => false, 
            'message' => 'Dokumentasi tidak ditemukan.',
            'data' => null
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
        'data' => null
    ]);
}

mysqli_close($koneksi);
?>