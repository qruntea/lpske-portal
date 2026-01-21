<?php
session_start();
header('Content-Type: application/json');

// Kalau belum login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['logged_in' => false]);
    exit;
}

// Jika sudah login
echo json_encode([
    'logged_in' => true,
    'user_data' => [
        'id' => $_SESSION['user_id'],
        'nama' => $_SESSION['user_name'],
        'email' => $_SESSION['user_email'],
        'role' => $_SESSION['user_role']
    ]
]);