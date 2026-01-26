<?php
header('Content-Type: application/json');
require 'koneksi.php';

$status_filter  = $_GET['status'] ?? '';
$available_only = isset($_GET['available_only']) && $_GET['available_only'] === 'true';

try {

    $sql = "SELECT 
                id,
                nama_buku,
                kode_buku,
                kategori,
                jumlah_total,
                jumlah_tersedia,
                status
            FROM inventory";

    $conditions = [];
    $params = [];
    $types  = "";

    // Filter hanya yang tersedia
    if ($available_only) {
        $conditions[] = "jumlah_tersedia > 0";
    }

    // Filter status
    if (!empty($status_filter) && !$available_only) {
        $conditions[] = "status = ?";
        $params[] = $status_filter;
        $types .= "s";
    }

    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $sql .= " ORDER BY kode_buku ASC";

    $stmt = mysqli_prepare($koneksi, $sql);

    if (!$stmt) {
        throw new Exception(mysqli_error($koneksi));
    }

    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {

        // Fallback jika data kosong
        if (empty($row['nama_buku'])) {
            $row['nama_buku'] = '-';
        }

        if (empty($row['kode_buku'])) {
            $row['kode_buku'] = '-';
        }

        $row['has_quantity_system'] = true;

        $data[] = $row;
    }

    echo json_encode($data);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error'   => true,
        'message' => 'Gagal memuat data inventaris',
        'detail'  => $e->getMessage(),
        'data'    => []
    ]);
} finally {
    mysqli_close($koneksi);
}
