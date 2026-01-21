<?php
// Atur zona waktu default ke Waktu Indonesia Barat (WIB)
date_default_timezone_set('Asia/Jakarta');

// koneksi.php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'lpske-pkl'; // Sesuaikan dengan nama database Anda

try {
    $koneksi = new mysqli($host, $username, $password, $database);
    
    // Set charset ke utf8
    $koneksi->set_charset("utf8");
    
    // Cek koneksi
    if ($koneksi->connect_error) {
        throw new Exception("Connection failed: " . $koneksi->connect_error);
    }
    
} catch (Exception $e) {
    error_log("Database connection error: " . $e->getMessage());
    // Jangan tampilkan error detail ke user di production
    if (php_sapi_name() !== 'cli') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Koneksi database gagal']);
        exit;
    }
}
?>