<?php
// File: api/update_dosen.php (VERSI PERBAIKAN FINAL)

header('Content-Type: application/json');
require 'koneksi.php';
session_start();

// Validasi admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
    exit;
}

try {
    // Ambil data dari form
    $id = $_POST['dosen_id'] ?? 0;
    $nama_dosen = trim($_POST['nama_dosen'] ?? '');
    $gelar_depan = trim($_POST['gelar_depan'] ?? '');
    $gelar_belakang = trim($_POST['gelar_belakang'] ?? '');
    $nidn = trim($_POST['nidn'] ?? '');
    $nip = trim($_POST['nip'] ?? '');
    $homebase_prodi = trim($_POST['homebase_prodi'] ?? '');
    
    if (empty($id) || empty($nama_dosen) || empty($nidn)) {
        throw new Exception('ID, Nama Dosen, dan NIDN wajib diisi.');
    }

    // Ambil nama foto lama dari DB
    $stmt_get_foto = $koneksi->prepare("SELECT foto FROM dosen WHERE id = ?");
    $stmt_get_foto->bind_param("i", $id);
    $stmt_get_foto->execute();
    $foto_lama = $stmt_get_foto->get_result()->fetch_assoc()['foto'] ?? null;
    $stmt_get_foto->close();

    $foto_name = $foto_lama; // Defaultnya adalah foto lama
    
    // Jika ada foto baru diupload
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/foto_dosen/';
        // Hapus foto lama jika ada
        if ($foto_lama && file_exists($upload_dir . $foto_lama)) {
            unlink($upload_dir . $foto_lama);
        }

        $file_extension = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $foto_name = 'dosen_' . time() . '.' . $file_extension;
        $upload_path = $upload_dir . $foto_name;
        
        if (!move_uploaded_file($_FILES['foto']['tmp_name'], $upload_path)) {
            throw new Exception('Gagal mengupload foto baru.');
        }
    }

    // Update hanya tabel 'dosen'
    $stmt = $koneksi->prepare(
        "UPDATE dosen SET nama_dosen=?, gelar_depan=?, gelar_belakang=?, nidn=?, nip=?, homebase_prodi=?, foto=? 
         WHERE id=?"
    );
    $stmt->bind_param("sssssssi", $nama_dosen, $gelar_depan, $gelar_belakang, $nidn, $nip, $homebase_prodi, $foto_name, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Data dosen berhasil diperbarui!']);
    } else {
        if ($koneksi->errno == 1062) {
             throw new Exception('NIDN sudah digunakan oleh dosen lain.');
        }
        throw new Exception('Gagal memperbarui data: ' . $stmt->error);
    }
    $stmt->close();

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
$koneksi->close();
?>