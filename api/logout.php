<?php
session_start();

// Log logout activity (optional)
if (isset($_SESSION['user_id'])) {
    require '../koneksi.php';
    
    $user_id = $_SESSION['user_id'];
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    
    $stmt = $koneksi->prepare("INSERT INTO log_aktivitas (user_id, aksi, detail, ip_address) VALUES (?, 'logout', 'User logout', ?)");
    if ($stmt) {
        $stmt->bind_param("is", $user_id, $ip_address);
        $stmt->execute();
    }
    
    $koneksi->close();
}

// Destroy session
session_destroy();

// Clear session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

header('Content-Type: application/json');
echo json_encode(['success' => true, 'message' => 'Logout berhasil']);
?>