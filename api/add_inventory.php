<?php
// ===================================================================
// FILE: add_inventory.php - FIXED for existing DB structure
// Lokasi: htdocs/lpske-portal/api/add_inventory.php
// Tugas: Menambah data inventory baru ke database
// FIXED: Removed created_at/updated_at columns
// ===================================================================

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Ambil data dari POST
    $nama_alat = trim($_POST['nama_alat'] ?? '');
    $kode_alat = trim($_POST['kode_alat'] ?? '');
    $jumlah_total = intval($_POST['jumlah_total'] ?? 1);
    $status = trim($_POST['status'] ?? 'Tersedia');
    $lokasi = trim($_POST['lokasi'] ?? '');
    
    // Debug: Log received data
    error_log("Add Inventory - Data: nama=$nama_alat, kode=$kode_alat, jumlah=$jumlah_total");
    
    // Validation
    if (empty($nama_alat)) {
        echo json_encode(['success' => false, 'message' => 'Nama alat wajib diisi']);
        exit;
    }
    
    if (empty($kode_alat)) {
        echo json_encode(['success' => false, 'message' => 'Kode alat wajib diisi']);
        exit;
    }
    
    if ($jumlah_total < 1) {
        echo json_encode(['success' => false, 'message' => 'Jumlah total minimal 1']);
        exit;
    }
    
    // Check duplicate kode_alat
    $checkSql = "SELECT id FROM inventory WHERE kode_alat = '" . mysqli_real_escape_string($koneksi, $kode_alat) . "'";
    $checkResult = mysqli_query($koneksi, $checkSql);
    
    if (mysqli_num_rows($checkResult) > 0) {
        echo json_encode(['success' => false, 'message' => 'Kode alat sudah digunakan']);
        exit;
    }
    
    // Set default values untuk field yang ada di database
    $default_kategori = 'Umum';
    $default_deskripsi = '';
    $jumlah_tersedia = $jumlah_total; // Default: semua tersedia
    
    // Insert to database - SESUAI STRUKTUR DB EXISTING
    $sql = "INSERT INTO inventory (
                nama_alat, kode_alat, kategori, deskripsi, lokasi, 
                jumlah_total, jumlah_tersedia, status
            ) VALUES (
                '" . mysqli_real_escape_string($koneksi, $nama_alat) . "',
                '" . mysqli_real_escape_string($koneksi, $kode_alat) . "',
                '" . mysqli_real_escape_string($koneksi, $default_kategori) . "',
                '" . mysqli_real_escape_string($koneksi, $default_deskripsi) . "',
                '" . mysqli_real_escape_string($koneksi, $lokasi) . "',
                " . $jumlah_total . ",
                " . $jumlah_tersedia . ",
                '" . mysqli_real_escape_string($koneksi, $status) . "'
            )";
    
    $result = mysqli_query($koneksi, $sql);
    
    if ($result) {
        $newId = mysqli_insert_id($koneksi);
        
        error_log("Add Inventory - Success: New ID = " . $newId);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Item inventory berhasil ditambahkan',
            'id' => $newId,
            'data' => [
                'id' => $newId,
                'nama_alat' => $nama_alat,
                'kode_alat' => $kode_alat,
                'kategori' => $default_kategori,
                'jumlah_total' => $jumlah_total,
                'jumlah_tersedia' => $jumlah_tersedia,
                'status' => $status,
                'lokasi' => $lokasi
            ]
        ]);
    } else {
        throw new Exception('Gagal menyimpan data: ' . mysqli_error($koneksi));
    }
    
} catch (Exception $e) {
    error_log("Add Inventory - Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
} finally {
    if (isset($koneksi)) {
        mysqli_close($koneksi);
    }
}
?>