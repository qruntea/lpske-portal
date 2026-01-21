<?php
// Selama pengembangan, SANGAT PENTING untuk menampilkan error
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

// --- Validasi Sesi & Metode ---
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid method']);
    exit;
}

try {
    require 'koneksi.php';
    if (!$koneksi) {
        throw new Exception('Koneksi database gagal');
    }
    
    // PERBAIKAN 1: Gunakan nama variabel yang jelas. Ini adalah ID dari tabel 'dosen'.
    $dosen_id = trim($_POST['dosen_id'] ?? '');
    
    if (empty($dosen_id) || !is_numeric($dosen_id)) {
        echo json_encode(['success' => false, 'message' => 'ID dosen tidak valid!']);
        exit;
    }
    
    // Mulai transaksi
    mysqli_autocommit($koneksi, false);
    
    // LANGKAH 1: Ambil info `user_id` dan `foto` dari tabel dosen berdasarkan PRIMARY KEY `id`
    // PERBAIKAN 2: Gunakan `WHERE id = ?` bukan `WHERE user_id = ?`
    $get_info_sql = "SELECT user_id, foto, nama_dosen FROM dosen WHERE id = ?";
    $get_info_stmt = mysqli_prepare($koneksi, $get_info_sql);
    if (!$get_info_stmt) {
        throw new Exception('Prepare statement untuk mengambil info dosen gagal: ' . mysqli_error($koneksi));
    }
    mysqli_stmt_bind_param($get_info_stmt, "i", $dosen_id);
    mysqli_stmt_execute($get_info_stmt);
    $info_result = mysqli_stmt_get_result($get_info_stmt);
    
    if (mysqli_num_rows($info_result) === 0) {
        mysqli_stmt_close($get_info_stmt);
        throw new Exception("Dosen dengan ID {$dosen_id} tidak ditemukan!");
    }
    
    $dosen_data = mysqli_fetch_assoc($info_result);
    $user_id_to_delete = $dosen_data['user_id']; // Ini adalah ID untuk tabel 'users'
    $foto_name = $dosen_data['foto'];
    mysqli_stmt_close($get_info_stmt);
    
    // LANGKAH 2: Hapus data dari tabel 'dosen' terlebih dahulu
    // PERBAIKAN 3: Gunakan `WHERE id = ?`
    $delete_dosen_sql = "DELETE FROM dosen WHERE id = ?";
    $delete_dosen_stmt = mysqli_prepare($koneksi, $delete_dosen_sql);
    if (!$delete_dosen_stmt) {
        throw new Exception('Prepare statement untuk menghapus dosen gagal.');
    }
    mysqli_stmt_bind_param($delete_dosen_stmt, "i", $dosen_id);
    if (!mysqli_stmt_execute($delete_dosen_stmt)) {
        throw new Exception('Gagal menghapus data dosen: ' . mysqli_stmt_error($delete_dosen_stmt));
    }
    mysqli_stmt_close($delete_dosen_stmt);
    
    // LANGKAH 3: Hapus data dari tabel 'users' menggunakan user_id yang didapat dari LANGKAH 1
    if ($user_id_to_delete) {
        $delete_user_sql = "DELETE FROM users WHERE id = ?";
        $delete_user_stmt = mysqli_prepare($koneksi, $delete_user_sql);
        if (!$delete_user_stmt) {
            throw new Exception('Prepare statement untuk menghapus user gagal.');
        }
        mysqli_stmt_bind_param($delete_user_stmt, "i", $user_id_to_delete);
        if (!mysqli_stmt_execute($delete_user_stmt)) {
            throw new Exception('Gagal menghapus user account: ' . mysqli_stmt_error($delete_user_stmt));
        }
        mysqli_stmt_close($delete_user_stmt);
    }
    
    // LANGKAH 4: Hapus file foto dari server
    if ($foto_name && $foto_name !== 'default.jpg') {
        $foto_path = '../uploads/foto_dosen/' . $foto_name;
        if (file_exists($foto_path)) {
            unlink($foto_path);
        }
    }
    
    // Jika semua berhasil, commit transaksi
    mysqli_commit($koneksi);
    
    echo json_encode([
        'success' => true,
        'message' => 'Dosen dan user account terkait berhasil dihapus!'
    ]);

} catch (Exception $e) {
    if (isset($koneksi)) {
        mysqli_rollback($koneksi); // Batalkan semua perubahan jika terjadi error
    }
    // Kirim pesan error yang sebenarnya untuk debugging
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
} finally {
    if (isset($koneksi)) {
        mysqli_close($koneksi);
    }
}
?>