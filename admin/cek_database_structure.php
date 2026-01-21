<?php
// ===================================================================
// 6. FILE BANTUAN: cek_database_structure.php (FILE DEBUGGING)
// Lokasi: htdocs/lpske-portal/admin/cek_database_structure.php
// Tugas: Melihat struktur database untuk debugging
// ===================================================================

require '../api/koneksi.php';

echo "<h2>Struktur Database LPSKE Portal</h2>";
echo "<hr>";

// 1. Cek tabel inventory
echo "<h3>1. Struktur Tabel Inventory:</h3>";
$sql = "SHOW COLUMNS FROM inventory";
$result = mysqli_query($koneksi, $sql);

echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>{$row['Field']}</td>";
    echo "<td>{$row['Type']}</td>";
    echo "<td>{$row['Null']}</td>";
    echo "<td>{$row['Key']}</td>";
    echo "<td>{$row['Default']}</td>";
    echo "</tr>";
}
echo "</table>";

// 2. Cek tabel peminjaman
echo "<h3>2. Struktur Tabel Peminjaman:</h3>";
$sql = "SHOW COLUMNS FROM peminjaman";
$result = mysqli_query($koneksi, $sql);

echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>{$row['Field']}</td>";
    echo "<td>{$row['Type']}</td>";
    echo "<td>{$row['Null']}</td>";
    echo "<td>{$row['Key']}</td>";
    echo "<td>{$row['Default']}</td>";
    echo "</tr>";
}
echo "</table>";

// 3. Cek data sample
echo "<h3>3. Sample Data Inventory:</h3>";
$sql = "SELECT * FROM inventory LIMIT 5";
$result = mysqli_query($koneksi, $sql);

if (mysqli_num_rows($result) > 0) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    $first_row = true;
    while ($row = mysqli_fetch_assoc($result)) {
        if ($first_row) {
            echo "<tr>";
            foreach (array_keys($row) as $header) {
                echo "<th>{$header}</th>";
            }
            echo "</tr>";
            $first_row = false;
        }
        
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>{$value}</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
}

// 4. Cek peminjaman aktif
echo "<h3>4. Peminjaman Aktif:</h3>";
$sql = "SELECT p.*, i.nama_alat FROM peminjaman p 
        JOIN inventory i ON p.inventory_id = i.id 
        WHERE p.status = 'Dipinjam' 
        LIMIT 5";
$result = mysqli_query($koneksi, $sql);

if (mysqli_num_rows($result) > 0) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    $first_row = true;
    while ($row = mysqli_fetch_assoc($result)) {
        if ($first_row) {
            echo "<tr>";
            foreach (array_keys($row) as $header) {
                echo "<th>{$header}</th>";
            }
            echo "</tr>";
            $first_row = false;
        }
        
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>{$value}</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>Tidak ada peminjaman aktif</p>";
}

mysqli_close($koneksi);
?>