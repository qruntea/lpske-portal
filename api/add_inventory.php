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
    $nama_buku = trim($_POST['nama_buku'] ?? '');
    $kode_buku = trim($_POST['kode_buku'] ?? '');
    $jumlah_total = intval($_POST['jumlah_total'] ?? 1);
    $status = trim($_POST['status'] ?? 'Tersedia');
    
    // Debug: Log received data
    error_log("Add Inventory - Data: nama=$nama_buku, kode=$kode_buku, jumlah=$jumlah_total");
    
    // Validation
    if (empty($nama_buku)) {
        echo json_encode(['success' => false, 'message' => 'Nama buku wajib diisi']);
        exit;
    }
    
    if (empty($kode_buku)) {
        echo json_encode(['success' => false, 'message' => 'Kode buku wajib diisi']);
        exit;
    }
    
    if ($jumlah_total < 1) {
        echo json_encode(['success' => false, 'message' => 'Jumlah total minimal 1']);
        exit;
    }
    
    // Check duplicate kode_buku
    $checkSql = "SELECT id FROM buku WHERE kode_buku = '" . mysqli_real_escape_string($koneksi, $kode_buku) . "'";
    $checkResult = mysqli_query($koneksi, $checkSql);
    
    if (mysqli_num_rows($checkResult) > 0) {
        echo json_encode(['success' => false, 'message' => 'Kode buku sudah digunakan']);
        exit;
    }
    
    // Set default values untuk field yang ada di database
    $default_kategori = 'Umum';
    $default_deskripsi = '';
    $jumlah_tersedia = $jumlah_total; // Default: semua tersedia
    
    // Insert to database - SESUAI STRUKTUR DB BUKU
    $sql = "INSERT INTO buku (
                nama_buku, kode_buku, kategori,
                jumlah_total, jumlah_tersedia, status
            ) VALUES (
                '" . mysqli_real_escape_string($koneksi, $nama_buku) . "',
                '" . mysqli_real_escape_string($koneksi, $kode_buku) . "',
                '" . mysqli_real_escape_string($koneksi, $default_kategori) . "',
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
                'nama_buku' => $nama_buku,
                'kode_buku' => $kode_buku,
                'kategori' => $default_kategori,
                'jumlah_total' => $jumlah_total,
                'jumlah_tersedia' => $jumlah_tersedia,
                'status' => $status
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