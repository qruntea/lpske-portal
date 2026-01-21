<?php
// Set header agar browser tahu ini adalah file JSON
header('Content-Type: application/json');

// Load koneksi PDO
require_once __DIR__ . '/../config/database.php'; // Sesuaikan path jika berbeda

try {
    // Ambil semua info dosen, pastikan hanya 1 baris per id
    $stmt = $pdo->query("
        SELECT 
            id, 
            nidn, 
            user_id, 
            gelar_depan, 
            nama_dosen, 
            gelar_belakang, 
            nip, 
            homebase_prodi, 
            foto,
            CONCAT_WS(' ', gelar_depan, nama_dosen, gelar_belakang) AS nama_lengkap
        FROM dosen
        GROUP BY id
        ORDER BY nama_dosen ASC
    ");

    $dosen_list = $stmt->fetchAll();

    echo json_encode($dosen_list);

} catch (PDOException $e) {
    echo json_encode([
        'error' => 'Gagal memuat data dosen: ' . $e->getMessage()
    ]);
}
