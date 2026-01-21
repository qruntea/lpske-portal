<?php
header('Content-Type: application/json');
require 'koneksi.php';

try {
    // Cari NIDN yang duplikat
    $duplicate_query = "
        SELECT nidn, COUNT(*) as count 
        FROM dosen 
        GROUP BY nidn 
        HAVING COUNT(*) > 1
    ";
    
    $result = mysqli_query($koneksi, $duplicate_query);
    $duplicates = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $duplicates[] = $row;
    }
    
    $cleaned = 0;
    
    // Untuk setiap NIDN yang duplikat, hapus yang bukan yang pertama
    foreach ($duplicates as $dup) {
        $nidn = $dup['nidn'];
        
        // Ambil ID terkecil (yang pertama) untuk dipertahankan
        $keep_query = "SELECT id FROM dosen WHERE nidn = ? ORDER BY id ASC LIMIT 1";
        $keep_stmt = mysqli_prepare($koneksi, $keep_query);
        mysqli_stmt_bind_param($keep_stmt, "s", $nidn);
        mysqli_stmt_execute($keep_stmt);
        $keep_result = mysqli_stmt_get_result($keep_stmt);
        $keep_row = mysqli_fetch_assoc($keep_result);
        $keep_id = $keep_row['id'];
        mysqli_stmt_close($keep_stmt);
        
        // Hapus yang lainnya
        $delete_query = "DELETE FROM dosen WHERE nidn = ? AND id != ?";
        $delete_stmt = mysqli_prepare($koneksi, $delete_query);
        mysqli_stmt_bind_param($delete_stmt, "si", $nidn, $keep_id);
        mysqli_stmt_execute($delete_stmt);
        $cleaned += mysqli_stmt_affected_rows($delete_stmt);
        mysqli_stmt_close($delete_stmt);
    }
    
    echo json_encode([
        'success' => true,
        'message' => "Cleanup completed. Found " . count($duplicates) . " duplicate NIDNs, cleaned $cleaned records.",
        'duplicates_found' => $duplicates,
        'records_cleaned' => $cleaned
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error during cleanup: ' . $e->getMessage()
    ]);
}
?>