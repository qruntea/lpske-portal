<?php
// Test file untuk debug inventory
require 'api/koneksi.php';

echo "<h2>🔍 Debug Inventory Buku</h2>";
echo "<hr>";

// 1. Cek koneksi
echo "<h3>1. Status Koneksi Database</h3>";
if ($koneksi && !$koneksi->connect_error) {
    echo "<p style='color: green;'>✅ Koneksi database berhasil</p>";
} else {
    echo "<p style='color: red;'>❌ Koneksi database gagal</p>";
    exit;
}

// 2. Cek apakah tabel buku ada
echo "<h3>2. Cek Tabel Buku</h3>";
$check_table = "SHOW TABLES LIKE 'buku'";
$result = $koneksi->query($check_table);

if ($result && $result->num_rows > 0) {
    echo "<p style='color: green;'>✅ Tabel 'buku' ditemukan</p>";
    
    // 3. Cek struktur tabel
    echo "<h3>3. Struktur Tabel Buku</h3>";
    $describe = "DESCRIBE buku";
    $desc_result = $koneksi->query($describe);
    
    if ($desc_result) {
        echo "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse: collapse;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        while ($row = $desc_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "<td>" . $row['Extra'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // 4. Hitung jumlah record
    echo "<h3>4. Jumlah Data Buku</h3>";
    $count_query = "SELECT COUNT(*) as total FROM buku";
    $count_result = $koneksi->query($count_query);
    $count_row = $count_result->fetch_assoc();
    echo "<p>Total record: <strong>" . $count_row['total'] . "</strong></p>";
    
    // 5. Sample data
    if ($count_row['total'] > 0) {
        echo "<h3>5. Sample Data Buku (Limit 10)</h3>";
        $sample_query = "SELECT id, nama_buku, kode_buku, kategori, jumlah_total, jumlah_tersedia, status FROM buku LIMIT 10";
        $sample_result = $koneksi->query($sample_query);
        
        if ($sample_result && $sample_result->num_rows > 0) {
            echo "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr style='background: #f0f0f0;'><th>ID</th><th>Nama Buku</th><th>Kode Buku</th><th>Kategori</th><th>Total</th><th>Tersedia</th><th>Status</th></tr>";
            while ($row = $sample_result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . ($row['nama_buku'] ?? '-') . "</td>";
                echo "<td>" . ($row['kode_buku'] ?? '-') . "</td>";
                echo "<td>" . ($row['kategori'] ?? '-') . "</td>";
                echo "<td>" . ($row['jumlah_total'] ?? '-') . "</td>";
                echo "<td>" . ($row['jumlah_tersedia'] ?? '-') . "</td>";
                echo "<td>" . ($row['status'] ?? '-') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<p style='color: orange;'>⚠️ Tabel 'buku' kosong - tidak ada data</p>";
    }
    
} else {
    echo "<p style='color: red;'>❌ Tabel 'buku' tidak ditemukan</p>";
    
    echo "<h3>Tabel yang Tersedia:</h3>";
    $tables = "SHOW TABLES";
    $tables_result = $koneksi->query($tables);
    echo "<ul>";
    while ($row = $tables_result->fetch_row()) {
        echo "<li>" . $row[0] . "</li>";
    }
    echo "</ul>";
}

// 6. Test API endpoint
echo "<h3>6. Test API Endpoint</h3>";
echo "<p><a href='api/get_inventory.php' target='_blank'>👉 Klik di sini untuk test get_inventory.php</a></p>";

// 7. Cek error log
echo "<h3>7. Error Log</h3>";
$error_log_file = 'error_log.txt';
if (file_exists($error_log_file)) {
    $log_content = file_get_contents($error_log_file);
    if (!empty($log_content)) {
        echo "<pre style='background: #f5f5f5; padding: 10px; max-height: 300px; overflow-y: auto;'>";
        echo htmlspecialchars($log_content);
        echo "</pre>";
    } else {
        echo "<p>Error log kosong</p>";
    }
} else {
    echo "<p>File error_log.txt tidak ditemukan</p>";
}

$koneksi->close();
?>
