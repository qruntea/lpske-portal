<?php
// ===================================================================
// 4. FILE PERBAIKAN: get_inventory_detail.php (VERSI DIPERBAIKI)
// Lokasi: htdocs/lpske-portal/api/get_inventory_detail.php
// ===================================================================

require 'koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID tidak valid']);
    exit;
}

try {
    // Check if quantity columns exist
    $check_columns_sql = "SHOW COLUMNS FROM buku LIKE 'jumlah_total'";
    $check_result = mysqli_query($koneksi, $check_columns_sql);
    $hasQuantityColumns = mysqli_num_rows($check_result) > 0;
    
    // PERBAIKAN: Cek nama kolom yang benar - kode_buku adalah yang baru
    $check_kode_sql = "SHOW COLUMNS FROM buku";
    $kode_result = mysqli_query($koneksi, $check_kode_sql);
    $kode_column = 'kode_buku'; // default ke kode_buku
    
    while ($col = mysqli_fetch_assoc($kode_result)) {
        if (in_array($col['Field'], ['kode_buku', 'kode_alat', 'kode'])) {
            $kode_column = $col['Field'];
            break;
        }
    }
    
    if ($hasQuantityColumns) {
        $sql = "SELECT *, {$kode_column} as kode,
                CASE 
                    WHEN jumlah_tersedia = 0 THEN 'Habis'
                    WHEN jumlah_tersedia < jumlah_total THEN 'Dipinjam'
                    ELSE 'Tersedia'
                END as status_ketersediaan
                FROM buku WHERE id = ?";
    } else {
        $sql = "SELECT *, {$kode_column} as kode FROM buku WHERE id = ?";
    }
    
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $hasil = $stmt->get_result();
    
    if ($hasil && mysqli_num_rows($hasil) > 0) {
        $data = mysqli_fetch_assoc($hasil);
        
        // Add backward compatibility
        if (!$hasQuantityColumns) {
            $data['jumlah_total'] = 1;
            $data['jumlah_tersedia'] = ($data['status'] === 'Tersedia') ? 1 : 0;
            $data['status_ketersediaan'] = $data['status'];
        } else {
            // PERBAIKAN: Sinkronkan status dengan quantity
            $update_status_sql = "UPDATE inventory 
                                 SET status = CASE 
                                     WHEN jumlah_tersedia = 0 THEN 'Habis'
                                     WHEN jumlah_tersedia < jumlah_total THEN 'Dipinjam'
                                     ELSE 'Tersedia'
                                 END
                                 WHERE id = ?";
            $update_stmt = $koneksi->prepare($update_status_sql);
            $update_stmt->bind_param("i", $id);
            $update_stmt->execute();
            
            // Ambil data terbaru setelah update
            $stmt->execute();
            $hasil = $stmt->get_result();
            $data = mysqli_fetch_assoc($hasil);
        }
        
        echo json_encode(array_merge(['success' => true], $data));
    } else {
        echo json_encode(['success' => false, 'message' => 'Item tidak ditemukan']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

mysqli_close($koneksi);
?>
