<?php
// Matikan display error di production
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');
error_reporting(E_ALL);

// Start session
session_start();

// Set header JSON
header('Content-Type: application/json');

// reCAPTCHA verification removed
// This endpoint no longer requires or validates reCAPTCHA tokens.

// Cek method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);
    exit;
}

try {
    // Include koneksi database
    require_once 'koneksi.php';
    
    // Ambil data JSON dari request
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
        exit;
    }
    
    $email = trim($input['email'] ?? '');
    $password = trim($input['password'] ?? '');
    // reCAPTCHA removed: no token expected
    
    // Validasi input
    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Email dan password harus diisi']);
        exit;
    }
    
    // Validasi format email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Format email tidak valid']);
        exit;
    }
    
    // reCAPTCHA removed: proceed without verification
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
    
    // Cek koneksi database
    if (!isset($koneksi) || $koneksi->connect_error) {
        throw new Exception("Koneksi database gagal: " . ($koneksi->connect_error ?? 'Unknown error'));
    }
    
    // Query untuk cek user berdasarkan email
    $stmt = $koneksi->prepare("SELECT id, username, nama_lengkap, email, password, role FROM users WHERE email = ? LIMIT 1");
    
    if (!$stmt) {
        throw new Exception("Prepare statement gagal: " . $koneksi->error);
    }
    
    $stmt->bind_param("s", $email);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute statement gagal: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Email tidak ditemukan']);
        $stmt->close();
        exit;
    }
    
    $user = $result->fetch_assoc();
    $stmt->close();
    
    // Verifikasi password
    $passwordValid = false;
    
    // Jika password di database sudah di-hash dengan password_hash()
    if (password_verify($password, $user['password'])) {
        $passwordValid = true;
    } 
    // Jika password masih plain text (untuk development/testing)
    elseif ($password === $user['password']) {
        $passwordValid = true;
    }
    
    if (!$passwordValid) {
        echo json_encode(['success' => false, 'message' => 'Password salah']);
        exit;
    }
    
    // Login berhasil - set session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['nama_lengkap'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['login_time'] = time();
    $_SESSION['last_activity'] = time();
    
    // Generate session token untuk security
    $session_token = bin2hex(random_bytes(16));
    $_SESSION['session_token'] = $session_token;
    
    // Log aktivitas login (opsional)
    try {
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        // Cek apakah tabel log_aktivitas ada
        $check_table = $koneksi->query("SHOW TABLES LIKE 'log_aktivitas'");
        if ($check_table && $check_table->num_rows > 0) {
            $log_stmt = $koneksi->prepare("INSERT INTO log_aktivitas (user_id, aksi, detail, ip_address) VALUES (?, 'login', ?, ?)");
            if ($log_stmt) {
                $detail = "User login berhasil";
                $log_stmt->bind_param("iss", $user['id'], $detail, $ip_address);
                $log_stmt->execute();
                $log_stmt->close();
            }
        }
    } catch (Exception $log_error) {
        // Log error tapi jangan ganggu proses login
        error_log("Log aktivitas error: " . $log_error->getMessage());
    }
    
    // Response sukses
    echo json_encode([
        'success' => true,
        'message' => 'Login berhasil',
        'role' => $user['role'],
        'session_token' => $session_token,
        'user_data' => [
            'id' => $user['id'],
            'nama' => $user['nama_lengkap'],
            'email' => $user['email'],
            'role' => $user['role']
        ]
    ]);
    
} catch (Exception $e) {
    // Log error detail
    error_log("Login error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
    
    // Response error yang user-friendly
    echo json_encode([
        'success' => false, 
        'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
    ]);
} finally {
    // Tutup koneksi jika ada
    if (isset($koneksi) && $koneksi instanceof mysqli) {
        $koneksi->close();
    }
}
?>