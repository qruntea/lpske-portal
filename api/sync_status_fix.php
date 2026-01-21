<?php
// ===================================================================
// FILE: sync_status_fix.php
// Lokasi: htdocs/lstars-portal/api/sync_status_fix.php
// Path: http://localhost/lstars-portal/api/sync_status_fix.php
// ===================================================================

require 'koneksi.php';  // Path benar dari dalam folder api

echo "<h2>🔧 Fix Status Logic - LSTARS Portal</h2>";
echo "<hr>";

// 1. CEK KONEKSI DATABASE
echo "<h3>0. ✅ Test Koneksi Database:</h3>";
if ($koneksi) {
    echo "<p style='color: green;'>✅ Koneksi database berhasil</p>";
} else {
    echo "<p style='color: red;'>❌ Koneksi database gagal: " . mysqli_connect_error() . "</p>";
    exit;
}

// 2. ANALISIS MASALAH SAAT INI
echo "<h3>1. 📊 Analisis Status Saat Ini:</h3>";
$analisis_sql = "SELECT 
                    nama_alat, 
                    kode_alat, 
                    status, 
                    jumlah_total, 
                    jumlah_tersedia,
                    CASE 
                        WHEN jumlah_tersedia = 0 AND status != 'Rusak' AND status != 'Habis' THEN 'MASALAH: Tersedia=0 tapi status bukan Habis/Rusak'
                        WHEN jumlah_tersedia > 0 AND status = 'Habis' THEN 'MASALAH: Ada stock tapi status Habis'
                        WHEN status = 'Rusak' AND jumlah_tersedia > 0 THEN 'MASALAH: Rusak tapi masih ada tersedia'
                        WHEN jumlah_tersedia = jumlah_total AND status != 'Tersedia' AND status != 'Rusak' THEN 'MASALAH: Full stock tapi bukan Tersedia'
                        ELSE 'OK'
                    END as diagnosis
                 FROM inventory 
                 ORDER BY 
                     CASE diagnosis WHEN 'OK' THEN 2 ELSE 1 END,
                     nama_alat";

$analisis_result = mysqli_query($koneksi, $analisis_sql);

if (!$analisis_result) {
    echo "<p style='color: red;'>❌ Error query: " . mysqli_error($koneksi) . "</p>";
    exit;
}

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

if ($masalah_count == 0) {
    echo "<div style='background: #ccffcc; padding: 15px; border: 2px solid green;'>";
    echo "<h4>🎉 BAGUS! Semua status sudah konsisten.</h4>";
    echo "<p>Tidak perlu perbaikan database. Langsung ganti file API saja.</p>";
    echo "</div>";
} else {
    // 3. PERBAIKAN OTOMATIS
    echo "<h3>2. 🔧 Perbaikan Otomatis:</h3>";

    $fixes = [
        "Fix items habis (tersedia=0, bukan rusak)" => 
            "UPDATE inventory SET status = 'Habis' WHERE jumlah_tersedia = 0 AND status != 'Rusak'",
        
        "Fix items tersedia penuh" => 
            "UPDATE inventory SET status = 'Tersedia' WHERE jumlah_tersedia = jumlah_total AND status != 'Rusak'",
        
        "Fix items dipinjam sebagian" => 
            "UPDATE inventory SET status = 'Dipinjam' WHERE jumlah_tersedia > 0 AND jumlah_tersedia < jumlah_total AND status != 'Rusak'",
        
        "Ensure rusak items have 0 tersedia" => 
            "UPDATE inventory SET jumlah_tersedia = 0 WHERE status = 'Rusak'"
    ];

    foreach ($fixes as $description => $sql) {
        if (mysqli_query($koneksi, $sql)) {
            $affected = mysqli_affected_rows($koneksi);
            echo "<p style='color: green;'>✅ {$description}: {$affected} item diperbaiki</p>";
        } else {
            echo "<p style='color: red;'>❌ Error {$description}: " . mysqli_error($koneksi) . "</p>";
        }
    }
}

// 4. HASIL SETELAH PERBAIKAN
echo "<h3>3. ✅ Status Setelah Perbaikan:</h3>";
$result_sql = "SELECT 
                nama_alat, 
                kode_alat, 
                status, 
                jumlah_total, 
                jumlah_tersedia,
                CASE 
                    WHEN status = 'Tersedia' AND jumlah_tersedia = jumlah_total THEN '✅'
                    WHEN status = 'Dipinjam' AND jumlah_tersedia > 0 AND jumlah_tersedia < jumlah_total THEN '✅'
                    WHEN status = 'Habis' AND jumlah_tersedia = 0 THEN '✅'
                    WHEN status = 'Rusak' AND jumlah_tersedia = 0 THEN '✅'
                    ELSE '❌'
                END as valid
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
echo "<th>Nama Alat</th><th>Kode</th><th>Status</th><th>Total</th><th>Tersedia</th><th>Valid</th>";
echo "</tr>";

$status_count = ['Tersedia' => 0, 'Dipinjam' => 0, 'Habis' => 0, 'Rusak' => 0];
$invalid_count = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $status_color = match($row['status']) {
        'Tersedia' => '#ccffcc',
        'Dipinjam' => '#ffffcc', 
        'Habis' => '#ffcccc',
        'Rusak' => '#ffcc99',
        default => '#ffffff'
    };
    
    if ($row['valid'] == '❌') $invalid_count++;
    $status_count[$row['status']]++;
    
    echo "<tr style='background: {$status_color};'>";
    echo "<td>{$row['nama_alat']}</td>";
    echo "<td><strong>{$row['kode_alat']}</strong></td>";
    echo "<td><strong>{$row['status']}</strong></td>";
    echo "<td>{$row['jumlah_total']}</td>";
    echo "<td>{$row['jumlah_tersedia']}</td>";
    echo "<td style='font-size: 20px;'>{$row['valid']}</td>";
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

echo "<h3>4. 🎯 Langkah Selanjutnya:</h3>";
if ($invalid_count == 0) {
    echo "<div style='background: #ccffcc; padding: 15px; border: 2px solid green; margin: 20px 0;'>";
    echo "<h4>🎉 SEMPURNA! Status sudah konsisten</h4>";
    echo "<p>Sekarang ganti file API:</p>";
    echo "<ol>";
    echo "<li>✅ Ganti <code>get_inventory.php</code> → <a href='get_inventory_fixed.php' target='_blank'>Download versi baru</a></li>";
    echo "<li>✅ Test API: <a href='get_inventory.php' target='_blank'>Test get_inventory.php</a></li>";
    echo "<li>✅ Refresh halaman Database Inventory</li>";
    echo "</ol>";
    echo "</div>";
} else {
    echo "<div style='background: #ffffcc; padding: 15px; border: 2px solid orange; margin: 20px 0;'>";
    echo "<h4>⚠️ Masih ada {$invalid_count} item yang tidak valid</h4>";
    echo "<p>Periksa manual item yang masih ❌ di atas</p>";
    echo "</div>";
}

mysqli_close($koneksi);
?>