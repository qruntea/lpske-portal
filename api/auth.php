<?php
// Buat file baru: api/auth.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function checkLogin() {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Anda harus login terlebih dahulu']);
        exit();
    }
    return $_SESSION['user_id'];
}

function getCurrentUserId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

function getCurrentUser() {
    return [
        'user_id' => $_SESSION['user_id'] ?? null,
        'nama' => $_SESSION['user_name'] ?? null,
        'email' => $_SESSION['user_email'] ?? null,
        'role' => $_SESSION['user_role'] ?? null
    ];
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_name']);
}
?>