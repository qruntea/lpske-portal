<?php
// ===================================================================
// FILE 1: upload_foto_dosen.php
// Lokasi: htdocs/lstars-portal/api/upload_foto_dosen.php
// Tugas: Handle upload foto profile dosen
// ===================================================================

require 'koneksi.php';

$response = ['success' => false, 'message' => 'Terjadi kesalahan.'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Method tidak diizinkan';
    echo json_encode($response);
    exit;
}

$dosen_id = isset($_POST['dosen_id']) ? (int)$_POST['dosen_id'] : 0;

if ($dosen_id <= 0) {
    $response['message'] = 'ID dosen tidak valid';
    echo json_encode($response);
    exit;
}

if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
    $response['message'] = 'File foto tidak valid atau tidak ditemukan';
    echo json_encode($response);
    exit;
}

try {
    $file = $_FILES['foto'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];
    
    // Validasi ukuran file (max 5MB)
    if ($fileSize > 5 * 1024 * 1024) {
        throw new Exception('Ukuran file terlalu besar. Maksimal 5MB.');
    }
    
    // Validasi tipe file
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $fileTmpName);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        throw new Exception('Tipe file tidak diizinkan. Hanya JPG, PNG, GIF yang diperbolehkan.');
    }
    
    // Buat folder uploads jika belum ada
    $uploadDir = '../uploads/foto_dosen/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Generate nama file unik
    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
    $newFileName = 'dosen_' . $dosen_id . '_' . time() . '.' . $fileExtension;
    $uploadPath = $uploadDir . $newFileName;
    
    // Hapus foto lama jika ada
    $stmt = $koneksi->prepare("SELECT foto FROM dosen WHERE id = ?");
    $stmt->bind_param("i", $dosen_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $oldData = $result->fetch_assoc();
    
    if ($oldData && $oldData['foto'] && file_exists('../' . $oldData['foto'])) {
        unlink('../' . $oldData['foto']);
    }
    
    // Upload file baru
    if (move_uploaded_file($fileTmpName, $uploadPath)) {
        // Update database
        $fotoPath = 'uploads/foto_dosen/' . $newFileName;
        $stmt = $koneksi->prepare("UPDATE dosen SET foto = ? WHERE id = ?");
        $stmt->bind_param("si", $fotoPath, $dosen_id);
        
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Foto berhasil diupload';
            $response['foto_url'] = $fotoPath;
        } else {
            throw new Exception('Gagal menyimpan path foto ke database');
        }
    } else {
        throw new Exception('Gagal mengupload file');
    }
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
mysqli_close($koneksi);
?>