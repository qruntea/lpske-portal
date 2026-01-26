<?php
// ===================================================================
// FILE: update_inventory.php - FIXED for existing DB structure
// Lokasi: htdocs/lpske-portal/api/update_inventory.php
// Tugas: Update data inventory di database
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
    $inventory_id = intval($_POST['inventory_id'] ?? 0);
    $nama_buku = trim($_POST['nama_buku'] ?? '');
    $kode_buku = trim($_POST['kode_buku'] ?? '');
    $jumlah_total = intval($_POST['jumlah_total'] ?? 1);
    $status = trim($_POST['status'] ?? 'Tersedia');
    
    // Debug: Log received data
    error_log("Update Inventory - ID: $inventory_id, Data: nama=$nama_buku, kode=$kode_buku");
    
    // Validation
    if ($inventory_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID inventory tidak valid: ' . $inventory_id]);
        exit;
    }
    
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
    
    // Check if inventory exists
    $checkSql = "SELECT jumlah_tersedia FROM buku WHERE id = " . $inventory_id;
    $result = mysqli_query($koneksi, $checkSql);
    
    if (mysqli_num_rows($result) === 0) {
        echo json_encode(['success' => false, 'message' => 'Item inventory tidak ditemukan dengan ID: ' . $inventory_id]);
        exit;
    }
    
    $currentData = mysqli_fetch_assoc($result);
    $currentTersedia = $currentData['jumlah_tersedia'];
    
    // Check duplicate kode_buku (exclude current item)
    $dupSql = "SELECT id FROM buku WHERE kode_buku = '" . mysqli_real_escape_string($koneksi, $kode_buku) . "' AND id != " . $inventory_id;
    $dupResult = mysqli_query($koneksi, $dupSql);
    
    if (mysqli_num_rows($dupResult) > 0) {
        echo json_encode(['success' => false, 'message' => 'Kode buku sudah digunakan']);
        exit;
    }
    
    // Calculate new jumlah_tersedia
    $newJumlahTersedia = $currentTersedia;
    if ($jumlah_total < $currentTersedia) {
        $newJumlahTersedia = $jumlah_total;
    }
    
    // Update database - SESUAI STRUKTUR DB BUKU
    $updateSql = "UPDATE buku SET 
                nama_buku = '" . mysqli_real_escape_string($koneksi, $nama_buku) . "', 
                kode_buku = '" . mysqli_real_escape_string($koneksi, $kode_buku) . "', 
                jumlah_total = " . $jumlah_total . ", 
                jumlah_tersedia = " . $newJumlahTersedia . ",
                status = '" . mysqli_real_escape_string($koneksi, $status) . "'
            WHERE id = " . $inventory_id;
    
    $updateResult = mysqli_query($koneksi, $updateSql);
    
    if ($updateResult) {
        error_log("Update success - ID: $inventory_id");
        
        echo json_encode([
            'success' => true, 
            'message' => 'Item inventory berhasil diupdate',
            'id' => $inventory_id,
            'data' => [
                'id' => $inventory_id,
                'nama_buku' => $nama_buku,
                'kode_buku' => $kode_buku,
                'jumlah_total' => $jumlah_total,
                'jumlah_tersedia' => $newJumlahTersedia,
                'status' => $status
            ]
        ]);
    } else {
        throw new Exception('Execute update failed: ' . mysqli_error($koneksi));
    }
    
} catch (Exception $e) {
    error_log("UPDATE INVENTORY ERROR: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
} finally {
    if (isset($koneksi)) {
        mysqli_close($koneksi);
    }
}
?>