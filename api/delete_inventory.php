<?php
// ===================================================================
// FILE: delete_inventory.php
// Lokasi: htdocs/lpske-portal/api/delete_inventory.php
// Tugas: Menghapus data inventory dari database
// ===================================================================

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'koneksi.php';

// Debug: Log all received data
error_log("=== DELETE INVENTORY DEBUG ===");
error_log("Method: " . $_SERVER['REQUEST_METHOD']);
error_log("POST data: " . print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Check if connection exists
    if (!isset($koneksi) || !$koneksi) {
        throw new Exception('Database connection failed');
    }

    // Ambil data dari POST
    $inventory_id = intval($_POST['inventory_id'] ?? 0);
    
    // Debug: Log parsed data
    error_log("Parsed data:");
    error_log("- inventory_id: $inventory_id");
    
    // Validation
    if ($inventory_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID inventory tidak valid: ' . $inventory_id]);
        exit;
    }
    
    // Check if inventory exists and get name for confirmation
    $checkSql = "SELECT nama_buku FROM buku WHERE id = ?";
    $stmt = $koneksi->prepare($checkSql);
    
    if (!$stmt) {
        throw new Exception('Prepare check failed: ' . $koneksi->error);
    }
    
    $stmt->bind_param("i", $inventory_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Item inventory tidak ditemukan dengan ID: ' . $inventory_id]);
        exit;
    }
    
    $itemData = $result->fetch_assoc();
    $namaBuku = $itemData['nama_buku'];
    $stmt->close();
    
    // TODO: Check if item is currently borrowed (add this check if needed)
    // You can add a check here to prevent deletion of borrowed items
    
    // Delete the inventory item
    $deleteSql = "DELETE FROM buku WHERE id = ?";
    $stmt = $koneksi->prepare($deleteSql);
    
    if (!$stmt) {
        throw new Exception('Prepare delete failed: ' . $koneksi->error);
    }
    
    $stmt->bind_param("i", $inventory_id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            error_log("Delete success - ID: $inventory_id, Name: $namaBuku");
            
            echo json_encode([
                'success' => true,
                'message' => "Item '$namaBuku' berhasil dihapus",
                'deleted_id' => $inventory_id,
                'deleted_name' => $namaBuku
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Tidak ada data yang dihapus']);
        }
    } else {
        throw new Exception('Execute delete failed: ' . $stmt->error);
    }
    
} catch (Exception $e) {
    error_log("DELETE INVENTORY ERROR: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage(),
        'debug' => [
            'received_post' => $_POST,
            'error_details' => $e->getMessage()
        ]
    ]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($koneksi)) {
        $koneksi->close();
    }
}
?>