<?php
// ===================================================================
// File: proses_pengembalian.php (VERSI BARU DENGAN KUANTITAS)
// Lokasi: htdocs/lstars-portal/api/proses_pengembalian.php
// ===================================================================

require 'koneksi.php';

$response = ['success' => false, 'message' => 'ID tidak ditemukan.'];

if (isset($_POST['peminjaman_id'])) {
    $peminjaman_id = $_POST['peminjaman_id'];

    mysqli_begin_transaction($koneksi);
    try {
        // Ambil dulu ID inventory dari peminjaman yang akan diproses
        $inventory_id = null;
        $stmt_get_inv = $koneksi->prepare("SELECT inventory_id FROM peminjaman WHERE id = ?");
        $stmt_get_inv->bind_param("i", $peminjaman_id);
        $stmt_get_inv->execute();
        $result_inv = $stmt_get_inv->get_result();
        if ($row_inv = $result_inv->fetch_assoc()) {
            $inventory_id = $row_inv['inventory_id'];
        }
        $stmt_get_inv->close();

        if ($inventory_id) {
            // 1. Update status di tabel peminjaman
            $stmt1 = $koneksi->prepare("UPDATE peminjaman SET status = 'Dikembalikan', tgl_aktual_kembali = NOW() WHERE id = ? AND status = 'Dipinjam'");
            $stmt1->bind_param("i", $peminjaman_id);
            $stmt1->execute();

            if ($stmt1->affected_rows > 0) {
                // 2. Tambah kembali jumlah stok yang tersedia
                $stmt2 = $koneksi->prepare("UPDATE inventory SET jumlah_tersedia = jumlah_tersedia + 1 WHERE id = ?");
                $stmt2->bind_param("i", $inventory_id);
                $stmt2->execute();

                mysqli_commit($koneksi);
                $response = ['success' => true, 'message' => 'Alat berhasil dikembalikan!'];
            } else {
                mysqli_rollback($koneksi);
                $response['message'] = 'Gagal, barang mungkin sudah dikembalikan sebelumnya.';
            }
        } else {
            mysqli_rollback($koneksi);
            $response['message'] = 'Gagal menemukan ID inventory terkait.';
        }
    } catch (mysqli_sql_exception $exception) {
        mysqli_rollback($koneksi);
        $response['message'] = 'Gagal memperbarui database karena ada kesalahan teknis.';
    }
}

echo json_encode($response);
mysqli_close($koneksi);
?>