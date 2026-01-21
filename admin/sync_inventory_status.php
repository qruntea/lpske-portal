<?php
// ===================================================================
// STEP 1: sync_status_fix.php
// Lokasi: htdocs/lpske-portal/sync_status_fix.php
// Tugas: Sinkronkan status database dengan quantity dan fix logic
// ===================================================================

require 'api/koneksi.php';

echo "<h2>🔧 Fix Status Logic - LPSKE Portal</h2>";
echo "<hr>";

// 1. ANALISIS MASALAH SAAT INI
echo "<h3>1. 📊 Analisis Status Saat Ini:</h3>";
$analisis_sql = "SELECT 
                    nama_alat, 
                    kode_alat, 
                    status, 
                    jumlah_total, 
                    jumlah_tersedia,
                    CASE 
                        WHEN jumlah_tersedia = 0 AND status != 'Rusak' THEN 'MASALAH: Habis tapi status bukan Habis'
                        WHEN jumlah_tersedia > 0 AND status = 'Habis' THEN 'MASALAH: Ada stock tapi status Habis'
                        WHEN status = 'Rusak' AND jumlah_tersedia > 0 THEN 'MASALAH: Rusak tapi masih ada tersedia'
                        ELSE 'OK'
                    END as diagnosis
                 FROM inventory 
                 ORDER BY 
                     CASE diagnosis WHEN 'OK' THEN 2 ELSE 1 END,
                     nama_alat";

$analisis_result = mysqli_query($koneksi, $analisis_sql);

echo "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background: #f0f0f0;'>";
echo "<th>Nama Alat</th><th>Kode</th><th>Status DB</th><th>Total</th><th>Tersedia</th><th>Diagnosis</th>";
echo "</tr>";

$masalah_count = 0;
while ($row = mysqli_fetch_assoc($analisis_result)) {
    $bg_color = ($row['diagnosis'] == 'OK') ? '#ccffcc' : '#ffcccc';
    if ($row['diagnosis'] != 'OK') $masalah_count++;
    
    echo "<tr style='background: {$bg_color};'>";
    echo "<td>{$row['nama_alat']}</td>";
    echo "<td><strong>{$row['kode_alat']}</strong></td>";
    echo "<td>{$row['status']}</td>";
    echo "<td>{$row['jumlah_total']}</td>";
    echo "<td>{$row['jumlah_tersedia']}</td>";
    echo "<td><strong>{$row['diagnosis']}</strong></td>";
    echo "</tr>";
}
echo "</table>";

echo "<br><p><strong>🔍 Ditemukan {$masalah_count} item dengan status yang tidak konsisten.</strong></p>";

// 2. PERBAIKAN OTOMATIS
echo "<h3>2. 🔧 Perbaikan Otomatis:</h3>";

// Strategi perbaikan:
// - Jika status = 'Rusak' → biarkan, tapi set jumlah_tersedia = 0
// - Jika jumlah_tersedia = 0 dan status != 'Rusak' → set status = 'Habis'
// - Jika jumlah_tersedia > 0 dan jumlah_tersedia < jumlah_total → set status = 'Dipinjam'
// - Jika jumlah_tersedia = jumlah_total → set status = 'Tersedia'

$fixes = [
    "Fix items yang rusak" => "UPDATE inventory SET jumlah_tersedia = 0 WHERE status = 'Rusak'",
    "Fix items habis" => "UPDATE inventory SET status = 'Habis' WHERE jumlah_tersedia = 0 AND status != 'Rusak'",
    "Fix items dipinjam" => "UPDATE inventory SET status = 'Dipinjam' WHERE jumlah_tersedia > 0 AND jumlah_tersedia < jumlah_total AND status != 'Rusak'", 
    "Fix items tersedia" => "UPDATE inventory SET status = 'Tersedia' WHERE jumlah_tersedia = jumlah_total AND status != 'Rusak'"
];

foreach ($fixes as $description => $sql) {
    if (mysqli_query($koneksi, $sql)) {
        $affected = mysqli_affected_rows($koneksi);
        echo "<p style='color: green;'>✅ {$description}: {$affected} item diperbaiki</p>";
    } else {
        echo "<p style='color: red;'>❌ Error {$description}: " . mysqli_error($koneksi) . "</p>";
    }
}

// 3. HASIL SETELAH PERBAIKAN
echo "<h3>3. ✅ Status Setelah Perbaikan:</h3>";
$result_sql = "SELECT 
                nama_alat, 
                kode_alat, 
                status, 
                jumlah_total, 
                jumlah_tersedia 
               FROM inventory 
               ORDER BY 
                   CASE status
                       WHEN 'Tersedia' THEN 1
                       WHEN 'Dipinjam' THEN 2  
                       WHEN 'Habis' THEN 3
                       WHEN 'Rusak' THEN 4
                   END, nama_alat";

$result = mysqli_query($koneksi, $result_sql);

echo "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background: #f0f0f0;'>";
echo "<th>Nama Alat</th><th>Kode</th><th>Status</th><th>Total</th><th>Tersedia</th><th>Logic Check</th>";
echo "</tr>";

$status_count = ['Tersedia' => 0, 'Dipinjam' => 0, 'Habis' => 0, 'Rusak' => 0];

while ($row = mysqli_fetch_assoc($result)) {
    $status_color = match($row['status']) {
        'Tersedia' => '#ccffcc',
        'Dipinjam' => '#ffffcc', 
        'Habis' => '#ffcccc',
        'Rusak' => '#ffcc99',
        default => '#ffffff'
    };
    
    // Validasi logic
    $logic_check = '✅';
    if ($row['status'] == 'Rusak' && $row['jumlah_tersedia'] > 0) $logic_check = '❌';
    if ($row['status'] == 'Habis' && $row['jumlah_tersedia'] > 0) $logic_check = '❌';
    if ($row['status'] == 'Tersedia' && $row['jumlah_tersedia'] != $row['jumlah_total']) $logic_check = '❌';
    if ($row['status'] == 'Dipinjam' && ($row['jumlah_tersedia'] == 0 || $row['jumlah_tersedia'] == $row['jumlah_total'])) $logic_check = '❌';
    
    $status_count[$row['status']]++;
    
    echo "<tr style='background: {$status_color};'>";
    echo "<td>{$row['nama_alat']}</td>";
    echo "<td><strong>{$row['kode_alat']}</strong></td>";
    echo "<td><strong>{$row['status']}</strong></td>";
    echo "<td>{$row['jumlah_total']}</td>";
    echo "<td>{$row['jumlah_tersedia']}</td>";
    echo "<td style='font-size: 20px;'>{$logic_check}</td>";
    echo "</tr>";
}
echo "</table>";

echo "<br><h3>📈 Ringkasan Status:</h3>";
echo "<ul>";
foreach ($status_count as $status => $count) {
    $icon = match($status) {
        'Tersedia' => '🟢',
        'Dipinjam' => '🟡',
        'Habis' => '🔴', 
        'Rusak' => '🟠',
        default => '❓'
    };
    echo "<li>{$icon} <strong>{$status}:</strong> {$count} item</li>";
}
echo "</ul>";

mysqli_close($koneksi);
?>

<?php