<?php
// ===================================================================
// File: proses_izin.php (SOLUSI FINAL)
// Deskripsi: Memproses pengajuan izin penelitian, dengan perbaikan.
// ===================================================================

session_start();
header('Content-Type: application/json');

// Memastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'Anda belum login. Silakan login terlebih dahulu.'
    ]);
    exit;
}

require 'koneksi.php';

$response = ['success' => false, 'message' => 'Terjadi kesalahan saat memproses data.'];

// Ambil data dari form dengan pengecekan
$judul = $_POST['judul_penelitian'] ?? null;
$deskripsi = $_POST['deskripsi'] ?? null;
$dosen_id = $_POST['dosen_pembimbing'] ?? null;
$nama_mahasiswa = $_POST['nama_mahasiswa'] ?? null;
$nim_mahasiswa = $_POST['nim_mahasiswa'] ?? null;
$mahasiswa_id = $_SESSION['user_id'];

// Validasi semua field yang diperlukan terisi
if ($judul && $deskripsi && $dosen_id && $nama_mahasiswa && $nim_mahasiswa) {
    
    // Status awal pengajuan
    $status = 'Diajukan';

    // Query INSERT ke tabel izin_penelitian
    $stmt = $koneksi->prepare("INSERT INTO izin_penelitian (mahasiswa_user_id, dosen_user_id, judul_penelitian, deskripsi, status, nama_mahasiswa, nim) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt === false) {
        $response['message'] = 'Gagal menyiapkan statement: ' . $koneksi->error;
        echo json_encode($response);
        exit;
    }

    // Bind parameter: i = integer, s = string
    // Urutan: mahasiswa_user_id (i), dosen_user_id (i), judul (s), deskripsi (s), status (s), nama_mahasiswa (s), nim_mahasiswa (s)
    if ($stmt->bind_param("iississ", $mahasiswa_id, $dosen_id, $judul, $deskripsi, $status, $nama_mahasiswa, $nim_mahasiswa)) {
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Pengajuan izin berhasil dikirim!';
        } else {
            // Tampilkan error jika eksekusi gagal
            $response['message'] = 'Gagal menyimpan data pengajuan: ' . $stmt->error;
        }
    } else {
        // Tampilkan error jika bind_param gagal
        $response['message'] = 'Error bind_param: ' . $stmt->error;
    }

    $stmt->close();
} else {
    // Pesan error jika ada field yang kosong
    $response['message'] = 'Data tidak lengkap. Mohon isi semua field.';
}

$koneksi->close();
echo json_encode($response);
?>