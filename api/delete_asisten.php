<?php
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $asisten_id = $_POST['asisten_id'];
    
    $sql = "DELETE FROM asisten WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "i", $asisten_id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'message' => 'Asisten berhasil dihapus!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menghapus asisten!']);
    }
    
    mysqli_stmt_close($stmt);
}

mysqli_close($koneksi);
?>