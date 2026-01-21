<?php
// exports/jadwal-csv.php
require_once '../config/database.php';

// Set headers untuk download CSV
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="jadwal-praktikum-lstars-' . date('Y-m-d') . '.csv"');
header('Pragma: no-cache');
header('Expires: 0');

try {
    $stmt = $pdo->query("
        SELECT 
            jp.mata_kuliah,
            jp.kode_mk,
            jp.semester,
            jp.kelas,
            jp.hari,
            jp.waktu_mulai,
            jp.waktu_selesai,
            jp.ruangan,
            d.nama AS dosen,
            a.nama AS asisten,
            jp.kapasitas,
            jp.jumlah_mahasiswa,
            jp.status,
            jp.keterangan
        FROM jadwal_praktikum jp
        LEFT JOIN dosen d ON jp.dosen_id = d.id
        LEFT JOIN asisten a ON jp.asisten_id = a.id
        WHERE jp.status = 'aktif'
        ORDER BY jp.semester, jp.kelas, 
                 CASE jp.hari
                     WHEN 'senin' THEN 1 WHEN 'selasa' THEN 2 WHEN 'rabu' THEN 3
                     WHEN 'kamis' THEN 4 WHEN 'jumat' THEN 5 WHEN 'sabtu' THEN 6
                 END, jp.waktu_mulai
    ");
    
    // Output CSV header dengan BOM untuk Excel
    echo "\xEF\xBB\xBF"; // BOM untuk UTF-8
    echo "Mata Kuliah,Kode MK,Semester,Kelas,Hari,Waktu Mulai,Waktu Selesai,Ruangan,Dosen,Asisten,Kapasitas,Jumlah Mahasiswa,Status,Keterangan\n";
    
    // Output data
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $csv_row = [];
        foreach ($row as $field) {
            // Escape quotes dan wrap dengan quotes
            $csv_row[] = '"' . str_replace('"', '""', $field ?? '') . '"';
        }
        echo implode(',', $csv_row) . "\n";
    }
    
} catch(PDOException $e) {
    // Jika error, output error message dalam format CSV
    echo "Error,,,,,,,,,,,,,\n";
    echo '"' . str_replace('"', '""', $e->getMessage()) . '",,,,,,,,,,,,,\n';
}
?>