<?php
// ===================================================================
// FILE: get_inventory.php (VERSI DIPERBAIKI)
// Lokasi: htdocs/lpske-portal/api/get_inventory.php
// Tugas: Mengambil data inventaris dari database dan mengembalikan dalam format JSON
// ===================================================================

header('Content-Type: application/json'); // Pastikan header ini ada

require 'koneksi.php'; 

$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$available_only = isset($_GET['available_only']) && $_GET['available_only'] === 'true';

try {
    $sql = "SELECT
                id,
                nama_alat,
                kode_alat,
                kategori,
                lokasi,
                jumlah_total,
                jumlah_tersedia,
                status  -- Tetap ambil status dari DB, tapi frontend akan mengoverride berdasarkan kuantitas
            FROM inventory"; // Ganti 'inventory' jika nama tabel Anda berbeda

    $where_conditions = [];

    // Filter berdasarkan ketersediaan (available_only)
    if ($available_only) {
        $where_conditions[] = "jumlah_tersedia > 0"; // Hanya yang tersedia > 0
    }

    // Filter berdasarkan status dari parameter GET
    if (!empty($status_filter)) {
        // PERHATIKAN: Jika Anda menggunakan available_only, jangan tambahkan filter status lagi yang mungkin bertentangan.
        // Jika status_filter bisa 'Dipinjam' dll, dan available_only true, ini akan konflik.
        // Asumsi: status_filter juga bisa dipakai sendiri, atau hanya 'Tersedia' saat available_only true.
        // Jika available_only true, sebaiknya abaikan status_filter
        if (!$available_only || $status_filter === 'Tersedia') { // Hanya tambahkan filter status jika tidak available_only, atau jika filter adalah 'Tersedia'
            $where_conditions[] = "status = '" . mysqli_real_escape_string($koneksi, $status_filter) . "'";
        }
    }

    if (!empty($where_conditions)) {
        $sql .= " WHERE " . implode(" AND ", $where_conditions);
    }

    // Urutkan berdasarkan ketersediaan dan nama
    // Urutkan berdasarkan kode_alat secara menaik (A-Z, 1-100)
$sql .= " ORDER BY kode_alat ASC";

    $hasil = mysqli_query($koneksi, $sql);

    $data = [];
    if ($hasil) {
        while($baris = mysqli_fetch_assoc($hasil)) {
            // Bersihkan kode_alat jika kosong
            if (empty($baris['kode_alat']) || $baris['kode_alat'] === null) {
                $baris['kode_alat'] = '-';
            }

            // TIDAK PERLU lagi membuat field 'jumlah', 'jumlah_display', 'quantity', dsb.
            // Biarkan 'jumlah_total' dan 'jumlah_tersedia' tetap sebagai angka.
            // Hapus baris-baris ini:
            // $jumlah_format = $baris['jumlah_tersedia'] . ' / ' . $baris['jumlah_total'];
            // $baris['jumlah'] = $jumlah_format;
            // $baris['jumlah_display'] = $jumlah_format;
            // $baris['quantity'] = $jumlah_format;
            // $baris['qty'] = $jumlah_format;
            // $baris['stok'] = $jumlah_format;

            // Tambahkan flag has_quantity_system
            // Ini akan memberi tahu frontend bahwa sistem kuantitas sedang digunakan
            $baris['has_quantity_system'] = true;

            $data[] = $baris;
        }
    }

    // Periksa apakah query berhasil
    if (!$hasil) {
        throw new Exception("Error saat mengambil data: " . mysqli_error($koneksi));
    }

    // Format output JSON
    // Jika ada error dalam try-catch, kita akan mengembalikan array kosong sebagai 'data'
    echo json_encode($data);

} catch (Exception $e) {
    // Tangani error dan kembalikan respons JSON yang sesuai
    http_response_code(500); // Mengatur kode status HTTP menjadi 500 (Internal Server Error)
    echo json_encode([
        'error' => true,
        'message' => 'Gagal memuat data inventaris: ' . $e->getMessage(),
        'data' => [] // Pastikan data selalu array, meskipun kosong saat error
    ]);
} finally {
    mysqli_close($koneksi); // Pastikan koneksi ditutup
}
?>