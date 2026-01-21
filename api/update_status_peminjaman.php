<?php
// ===================================================================
// File: api/update_status_peminjaman.php (VERSI FINAL)
// Deskripsi: Mengelola SEMUA perubahan status peminjaman (Setuju, Tolak, Kembalikan)
// ===================================================================

header('Content-Type: application/json');
require 'koneksi.php'; // Menggunakan nama file koneksi Anda

// --- Validasi Input Awal ---
if (!isset($_POST['peminjaman_id']) || !isset($_POST['status'])) {
    echo json_encode(['success' => false, 'message' => 'Aksi gagal: Parameter tidak lengkap.']);
    exit;
}

$peminjamanId = intval($_POST['peminjaman_id']);
$action = $_POST['status']; // Aksi dari admin: "Disetujui", "Ditolak", "Dikembalikan"
$dbStatus = '';

// --- Tentukan status yang akan disimpan di database berdasarkan aksi ---
switch ($action) {
    case 'Disetujui':
        $dbStatus = 'Dipinjam'; // Jika disetujui, status langsung menjadi 'Dipinjam'
        break;
    case 'Ditolak':
        $dbStatus = 'Ditolak';
        break;
    case 'Dikembalikan':
        $dbStatus = 'Dikembalikan';
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Aksi tidak valid.']);
        exit;
}

// --- Ambil data penting dari peminjaman yang akan diubah ---
$stmt_get = $koneksi->prepare("SELECT inventory_id, status FROM peminjaman WHERE id = ?");
$stmt_get->bind_param("i", $peminjamanId);
$stmt_get->execute();
$result = $stmt_get->get_result();
$peminjaman = $result->fetch_assoc();
$stmt_get->close();

if (!$peminjaman) {
    echo json_encode(['success' => false, 'message' => 'Data peminjaman tidak ditemukan.']);
    exit;
}

$inventoryId = $peminjaman['inventory_id'];
$currentStatus = $peminjaman['status'];

// --- Mulai Transaksi Database untuk Keamanan Data ---
$koneksi->begin_transaction();

try {
    // --- Langkah 1: Update Tabel Peminjaman ---
    if ($dbStatus === 'Dikembalikan') {
        // Jika dikembalikan, set juga tanggal aktual kembali menjadi hari ini
        $stmt_update = $koneksi->prepare("UPDATE peminjaman SET status = ?, tgl_aktual_kembali = CURDATE() WHERE id = ?");
    } else {
        // Untuk status lain, hanya update statusnya
        $stmt_update = $koneksi->prepare("UPDATE peminjaman SET status = ? WHERE id = ?");
    }
    $stmt_update->bind_param("si", $dbStatus, $peminjamanId);
    $stmt_update->execute();
    $stmt_update->close();

    // --- Langkah 2: Update Stok di Tabel Inventory (Logika Kunci) ---
    if ($action === 'Disetujui' && $currentStatus === 'Diajukan') {
        // Kurangi stok karena barang dipinjam
        $koneksi->query("UPDATE inventory SET jumlah_tersedia = jumlah_tersedia - 1 WHERE id = $inventoryId AND jumlah_tersedia > 0");
    
    } elseif ($action === 'Dikembalikan' && ($currentStatus === 'Dipinjam' || $currentStatus === 'Terlambat')) {
        // Tambah stok karena barang dikembalikan (FIX: bisa dari status 'Dipinjam' atau 'Terlambat')
        $koneksi->query("UPDATE inventory SET jumlah_tersedia = jumlah_tersedia + 1 WHERE id = $inventoryId");
    }
    
    // Jika semua berhasil, simpan perubahan
    $koneksi->commit();
    echo json_encode(['success' => true, 'message' => 'Status peminjaman berhasil diperbarui menjadi "' . $action . '"']);

} catch (mysqli_sql_exception $exception) {
    // Jika ada error, batalkan semua perubahan
    $koneksi->rollback();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $exception->getMessage()]);
}

$koneksi->close();
?>