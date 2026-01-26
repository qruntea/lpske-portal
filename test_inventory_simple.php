<?php
require 'api/koneksi.php';

echo "Testing inventory data...\n";

// Check total records
$sql = 'SELECT COUNT(*) as total FROM inventory';
$result = $koneksi->query($sql);
$row = $result->fetch_assoc();
echo "Total records: " . $row['total'] . "\n\n";

// Get sample data
$sample = $koneksi->query('SELECT id, nama_alat, kode_alat, jumlah_total, jumlah_tersedia, status FROM inventory LIMIT 3');
echo "Sample Data:\n";
while($r = $sample->fetch_assoc()) {
    echo json_encode($r) . "\n";
}

// Test the API endpoint
echo "\n\nTesting API endpoint...\n";
$api_response = file_get_contents('http://localhost/lpske-portal1/api/get_inventory.php');
echo "API Response (first 500 chars):\n";
echo substr($api_response, 0, 500) . "\n...";
?>
