<?php
// File: api/add_dosen.php (VERSI PERBAIKAN FINAL)

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
    $nama_dosen = trim($_POST['nama_dosen'] ?? '');
    $gelar_depan = trim($_POST['gelar_depan'] ?? '');
    $gelar_belakang = trim($_POST['gelar_belakang'] ?? '');
    $nidn = trim($_POST['nidn'] ?? '');
    $nip = trim($_POST['nip'] ?? '');
    $homebase_prodi = trim($_POST['homebase_prodi'] ?? '');
    
    // Validasi dasar
    if (empty($nama_dosen) || empty($nidn)) {
        throw new Exception('Nama Dosen dan NIDN wajib diisi.');
    }

    // Handle upload foto jika ada
    $foto_name = null; // Defaultnya NULL jika tidak ada foto
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/foto_dosen/';
        if (!is_dir($upload_dir)) {
             mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $foto_name = 'dosen_' . time() . '.' . $file_extension;
        $upload_path = $upload_dir . $foto_name;
        
        if (!move_uploaded_file($_FILES['foto']['tmp_name'], $upload_path)) {
            throw new Exception('Gagal mengupload foto.');
        }
    }

    // Hanya insert ke tabel 'dosen'
    $stmt = $koneksi->prepare(
        "INSERT INTO dosen (nama_dosen, gelar_depan, gelar_belakang, nidn, nip, homebase_prodi, foto) 
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("sssssss", $nama_dosen, $gelar_depan, $gelar_belakang, $nidn, $nip, $homebase_prodi, $foto_name);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Dosen baru berhasil ditambahkan!']);
    } else {
        // Cek jika NIDN duplikat
        if ($koneksi->errno == 1062) {
             throw new Exception('NIDN sudah terdaftar. Gunakan NIDN yang lain.');
        }
        throw new Exception('Gagal menyimpan data: ' . $stmt->error);
    }
    $stmt->close();

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$koneksi->close();
?>