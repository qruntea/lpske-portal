<?php
// exports/jadwal-excel.php
require_once '../config/database.php';

// Set headers untuk download Excel
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="jadwal-praktikum-lstars-' . date('Y-m-d') . '.xls"');
header('Pragma: no-cache');
header('Expires: 0');

// Start HTML untuk Excel
echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
echo '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head>';
echo '<body>';

echo '<table border="1" style="border-collapse: collapse; width: 100%;">';

// Header tabel
echo '<tr style="background-color: #4F46E5; color: white; font-weight: bold; text-align: center;">
        <th style="padding: 10px;">No</th>
        <th style="padding: 10px;">Mata Kuliah</th>
        <th style="padding: 10px;">Kode MK</th>
        <th style="padding: 10px;">Semester</th>
        <th style="padding: 10px;">Kelas</th>
        <th style="padding: 10px;">Hari</th>
        <th style="padding: 10px;">Waktu</th>
        <th style="padding: 10px;">Ruangan</th>
        <th style="padding: 10px;">Dosen</th>
        <th style="padding: 10px;">Asisten</th>
        <th style="padding: 10px;">Kapasitas</th>
        <th style="padding: 10px;">Terdaftar</th>
        <th style="padding: 10px;">Status</th>
      </tr>';

try {
    $stmt = $pdo->query("
        SELECT 
            jp.mata_kuliah,
            jp.kode_mk,
            jp.semester,
            jp.kelas,
            jp.hari,
            CONCAT(jp.waktu_mulai, ' - ', jp.waktu_selesai) as waktu,
            jp.ruangan,
            d.nama AS dosen,
            a.nama AS asisten,
            jp.kapasitas,
            jp.jumlah_mahasiswa,
            jp.status
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
    
    $no = 1;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Tentukan warna baris (zebra striping)
        $bg_color = ($no % 2 == 0) ? '#F9FAFB' : '#FFFFFF';
        
        echo '<tr style="background-color: ' . $bg_color . ';">';
        echo '<td style="padding: 8px; text-align: center;">' . $no . '</td>';
        echo '<td style="padding: 8px;">' . htmlspecialchars($row['mata_kuliah'] ?? '') . '</td>';
        echo '<td style="padding: 8px; text-align: center;">' . htmlspecialchars($row['kode_mk'] ?? '') . '</td>';
        echo '<td style="padding: 8px; text-align: center;">' . htmlspecialchars($row['semester'] ?? '') . '</td>';
        echo '<td style="padding: 8px; text-align: center;">' . htmlspecialchars($row['kelas'] ?? '') . '</td>';
        echo '<td style="padding: 8px; text-align: center;">' . ucfirst(htmlspecialchars($row['hari'] ?? '')) . '</td>';
        echo '<td style="padding: 8px; text-align: center;">' . htmlspecialchars($row['waktu'] ?? '') . '</td>';
        echo '<td style="padding: 8px;">' . htmlspecialchars($row['ruangan'] ?? '') . '</td>';
        echo '<td style="padding: 8px;">' . htmlspecialchars($row['dosen'] ?? 'Belum ditentukan') . '</td>';
        echo '<td style="padding: 8px;">' . htmlspecialchars($row['asisten'] ?? 'Belum ditentukan') . '</td>';
        echo '<td style="padding: 8px; text-align: center;">' . htmlspecialchars($row['kapasitas'] ?? '0') . '</td>';
        echo '<td style="padding: 8px; text-align: center;">' . htmlspecialchars($row['jumlah_mahasiswa'] ?? '0') . '</td>';
        
        // Status dengan warna
        $status_color = ($row['status'] == 'aktif') ? '#10B981' : '#EF4444';
        echo '<td style="padding: 8px; text-align: center; color: ' . $status_color . '; font-weight: bold;">' . ucfirst(htmlspecialchars($row['status'] ?? '')) . '</td>';
        echo '</tr>';
        
        $no++;
    }
    
    // Summary row
    echo '<tr style="background-color: #E5E7EB; font-weight: bold;">';
    echo '<td colspan="10" style="padding: 10px; text-align: right;">Total Jadwal Praktikum:</td>';
    echo '<td colspan="3" style="padding: 10px; text-align: center;">' . ($no - 1) . ' jadwal</td>';
    echo '</tr>';
    
} catch(PDOException $e) {
    echo '<tr><td colspan="13" style="color: red; padding: 10px; text-align: center;">Error: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
}

// Footer
echo '<tr style="background-color: #F3F4F6;">';
echo '<td colspan="13" style="padding: 15px; text-align: center; font-style: italic;">
        Jadwal Praktikum LSTARS - Generated on ' . date('d/m/Y H:i:s') . '
      </td>';
echo '</tr>';

echo '</table>';
echo '</body>';
echo '</html>';
?>