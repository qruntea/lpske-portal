<?php
// File: api/get_all_presensi.php (VERSI FINAL YANG BENAR)
require 'koneksi.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Akses ditolak']);
    exit;
}

header('Content-Type: application/json');

try {
    $tanggal = $_GET['tanggal'] ?? null;
    $status = $_GET['status'] ?? null;

    // Query yang benar, tanpa JOIN
    $sql = "SELECT 
                nama_asisten, 
                nim_asisten as nomor_induk,
                tanggal, 
                status,
                DATE_FORMAT(waktu_masuk, '%H:%i') as waktu_masuk_formatted, 
                DATE_FORMAT(waktu_keluar, '%H:%i') as waktu_keluar_formatted, 
                durasi
            FROM presensi_piket
            WHERE 1=1";

    $params = [];
    $types = "";

    if (!empty($tanggal)) {
        $sql .= " AND tanggal = ?";
        $params[] = $tanggal;
        $types .= 's';
    } else {
        $sql .= " AND tanggal >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
    }

    if (!empty($status)) {
        $sql .= " AND status = ?";
        $params[] = $status;
        $types .= 's';
    }

    $sql .= " ORDER BY tanggal DESC, waktu_masuk DESC";

    $stmt = $koneksi->prepare($sql);

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    
    $stmt->close();
    $koneksi->close();

    echo json_encode($data);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>