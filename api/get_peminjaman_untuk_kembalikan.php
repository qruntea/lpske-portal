<?php
// ===================================================================
// File: get_peminjaman_untuk_kembalikan.php (PERBAIKAN SEMENTARA/UMUM)
// Lokasi: htdocs/lpske-portal/api/get_peminjaman_untuk_kembalikan.php
// Tugas: Mengambil daftar peminjaman yang sedang 'Dipinjam'
//        beserta detail alatnya untuk halaman pengembalian.
// ===================================================================

require 'koneksi.php'; // Pastikan file koneksi.php sudah benar

header('Content-Type: application/json');

$sql = "SELECT p.id AS peminjaman_id, p.tgl_pinjam, p.tgl_rencana_kembali, p.status AS status_peminjaman,
               i.id AS inventory_id, i.nama_alat, i.kode_alat, i.kategori, i.status AS status_inventory, i.lokasi
        FROM peminjaman p
        JOIN inventory i ON p.inventory_id = i.id
        WHERE p.status = 'Dipinjam'
        ORDER BY p.tgl_pinjam DESC";

$result = mysqli_query($koneksi, $sql);

$peminjaman_data = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Tambahkan user_peminjam dengan nilai default jika tidak ada JOIN users
        $row['user_peminjam'] = 'N/A'; // Atau 'User Tidak Dikenal'
        $peminjaman_data[] = $row;
    }
}

echo json_encode($peminjaman_data);

mysqli_close($koneksi);
?>