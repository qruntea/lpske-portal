<?php
header('Content-Type: application/json');
require 'koneksi.php';

// Test tabel structure dan data
echo "<h3>Debug Dosen Operations</h3>";

// 1. Test struktur tabel
echo "<h4>1. Struktur Tabel Dosen:</h4>";
$structure = mysqli_query($koneksi, "DESCRIBE dosen");
echo "<table border='1'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
while ($row = mysqli_fetch_assoc($structure)) {
    echo "<tr>";
    foreach ($row as $value) {
        echo "<td>" . ($value ?? 'NULL') . "</td>";
    }
    echo "</tr>";
}
echo "</table>";

// 2. Test data yang ada
echo "<h4>2. Data Dosen yang Ada:</h4>";
$data = mysqli_query($koneksi, "SELECT * FROM dosen LIMIT 5");
if ($data && mysqli_num_rows($data) > 0) {
    echo "<table border='1'>";
    $first = true;
    while ($row = mysqli_fetch_assoc($data)) {
        if ($first) {
            echo "<tr>";
            foreach (array_keys($row) as $key) {
                echo "<th>$key</th>";
            }
            echo "</tr>";
            $first = false;
        }
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . ($value ?? 'NULL') . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Tidak ada data dosen.";
}

// 3. Test CREATE operation
echo "<h4>3. Test CREATE Operation:</h4>";
$test_nidn = 'TEST_' . time();
$insert_query = "INSERT INTO dosen (nama_dosen, nidn) VALUES (?, ?)";
$stmt = mysqli_prepare($koneksi, $insert_query);

if ($stmt) {
    $test_nama = "Test Dosen " . date('Y-m-d H:i:s');
    mysqli_stmt_bind_param($stmt, "ss", $test_nama, $test_nidn);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "✅ CREATE test berhasil dengan NIDN: $test_nidn<br>";
        
        // Test UPDATE
        echo "<h4>4. Test UPDATE Operation:</h4>";
        $update_query = "UPDATE dosen SET nama_dosen = ? WHERE nidn = ?";
        $update_stmt = mysqli_prepare($koneksi, $update_query);
        
        if ($update_stmt) {
            $new_nama = "Updated Test Dosen " . date('Y-m-d H:i:s');
            mysqli_stmt_bind_param($update_stmt, "ss", $new_nama, $test_nidn);
            
            if (mysqli_stmt_execute($update_stmt)) {
                echo "✅ UPDATE test berhasil<br>";
            } else {
                echo "❌ UPDATE test gagal: " . mysqli_stmt_error($update_stmt) . "<br>";
            }
            mysqli_stmt_close($update_stmt);
        } else {
            echo "❌ UPDATE prepare gagal: " . mysqli_error($koneksi) . "<br>";
        }
        
        // Clean up test data
        $delete_query = "DELETE FROM dosen WHERE nidn = ?";
        $delete_stmt = mysqli_prepare($koneksi, $delete_query);
        mysqli_stmt_bind_param($delete_stmt, "s", $test_nidn);
        mysqli_stmt_execute($delete_stmt);
        mysqli_stmt_close($delete_stmt);
        echo "🧹 Test data cleaned up<br>";
        
    } else {
        echo "❌ CREATE test gagal: " . mysqli_stmt_error($stmt) . "<br>";
    }
    mysqli_stmt_close($stmt);
} else {
    echo "❌ CREATE prepare gagal: " . mysqli_error($koneksi) . "<br>";
}

// 4. Test form data simulation
echo "<h4>5. Simulasi POST Data:</h4>";
$sample_post = [
    'nama_dosen' => 'Dr. Test Dosen',
    'gelar_depan' => 'Prof.',
    'gelar_belakang' => 'M.T., Ph.D',
    'nidn' => '1234567890123456',
    'nip' => '1234567890123456',
    'email' => 'test@example.com',
    'homebase_prodi' => 'Test Prodi',
    'admin_id' => '74'
];

echo "<pre>";
print_r($sample_post);
echo "</pre>";

echo "<h4>6. Database Connection Info:</h4>";
echo "MySQL Version: " . mysqli_get_server_info($koneksi) . "<br>";
echo "Connection Status: " . (mysqli_ping($koneksi) ? "OK" : "Failed") . "<br>";
echo "Database: " . mysqli_get_server_info($koneksi) . "<br>";

?>

<style>
table { border-collapse: collapse; margin: 10px 0; }
th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
th { background-color: #f2f2f2; }
h3, h4 { color: #333; margin-top: 20px; }
</style>