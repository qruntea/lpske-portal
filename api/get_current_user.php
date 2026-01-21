<?php
// Buat file baru: api/get_current_user.php
require 'auth.php';
header('Content-Type: application/json');

if (isLoggedIn()) {
    echo json_encode([
        'success' => true,
        'user' => getCurrentUser(),
        'login_time' => $_SESSION['login_time'] ?? null
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Belum login'
    ]);
}
?>