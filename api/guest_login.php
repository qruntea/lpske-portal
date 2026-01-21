<?php
// api/guest_login.php
// API untuk menangani login sebagai tamu

// Setup error handling
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../error_log.txt');
error_reporting(E_ALL);

// Start session
session_start();

// Set header JSON
header('Content-Type: application/json');

try {
    // Cek method POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method tidak diizinkan');
    }

    // Ambil input JSON
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);
    
    // Log untuk debugging
    error_log("Guest login attempt - Raw input: " . $rawInput);
    
    if (!$input || !isset($input['action']) || $input['action'] !== 'guest_login') {
        throw new Exception('Parameter tidak valid');
    }

    // Hapus session yang ada jika ada
    session_unset();
    
    // Set session untuk guest user
    $_SESSION['user_id'] = 'guest';
    $_SESSION['username'] = 'guest';
    $_SESSION['nama_lengkap'] = 'Tamu LSTARS';
    $_SESSION['email'] = 'tamu@lpske.ac.id';
    $_SESSION['role'] = 'guest';
    $_SESSION['is_guest'] = true;
    $_SESSION['login_time'] = time();
    $_SESSION['guest_session_start'] = date('Y-m-d H:i:s');

    // Log guest login
    error_log("Guest login successful at " . date('Y-m-d H:i:s'));

    // Response sukses
    echo json_encode([
        'success' => true,
        'message' => 'Login sebagai tamu berhasil',
        'role' => 'guest',
        'redirect' => 'guest-dashboard.php',
        'user_data' => [
            'id' => 'guest',
            'nama' => 'Tamu LPSKE',
            'email' => 'tamu@lpske.ac.id',
            'role' => 'guest'
        ]
    ]);

} catch (Exception $e) {
    error_log("Guest login error: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>