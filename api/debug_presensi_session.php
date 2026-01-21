<?php
// Fungsi untuk mendapatkan user_id dari session dengan aman
function getCurrentUserId() {
    // Coba berbagai kemungkinan key session
    if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
        return $_SESSION['user_id'];
    }
    if (isset($_SESSION['id']) && !empty($_SESSION['id'])) {
        return $_SESSION['id'];
    }
    if (isset($_SESSION['login_id']) && !empty($_SESSION['login_id'])) {
        return $_SESSION['login_id'];
    }
    if (isset($_SESSION['asisten_id']) && !empty($_SESSION['asisten_id'])) {
        return $_SESSION['asisten_id'];
    }
    return null;
}

// ===== FILE: get_presensi_status.php (DIPERBAIKI) =====
session_start();
require 'koneksi.php';

// Debug session
error_log('SESSION DATA: ' . json_encode($_SESSION));

$user_id = getCurrentUserId();

if (!$user_id) {
    echo json_encode([
        'success' => false, 
        'message' => 'Session tidak valid',
        'debug' => [
            'session_keys' => array_keys($_SESSION),
            'session_data' => $_SESSION
        ]
    ]);
    exit;
}

try {
    $today = date('Y-m-d');
    
    // Cek status presensi hari ini
    $sql = "SELECT 
                p.id, p.tanggal, p.waktu_masuk, p.waktu_keluar, p.status,
                u.nama_lengkap, u.nomor_induk
            FROM presensi_piket p
            JOIN users u ON p.asisten_user_id = u.id
            WHERE p.asisten_user_id = ? AND DATE(p.tanggal) = ?";
    
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "is", $user_id, $today);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode([
            'success' => true,
            'has_presensi' => true,
            'data' => $row,
            'debug' => ['user_id' => $user_id]
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'has_presensi' => false,
            'message' => 'Belum ada presensi hari ini',
            'debug' => ['user_id' => $user_id, 'today' => $today]
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage(),
        'debug' => ['user_id' => $user_id]
    ]);
}

mysqli_close($koneksi);

// ===== FILE: get_riwayat_presensi.php (DIPERBAIKI) =====
session_start();
require 'koneksi.php';

$user_id = getCurrentUserId();

if (!$user_id) {
    echo json_encode([
        'success' => false, 
        'message' => 'Session tidak valid',
        'debug_session' => $_SESSION
    ]);
    exit;
}

try {
    // Ambil riwayat presensi 30 hari terakhir
    $sql = "SELECT 
                p.id, p.tanggal, p.waktu_masuk, p.waktu_keluar, p.status,
                DATE_FORMAT(p.waktu_masuk, '%H:%i') as waktu_masuk_formatted,
                DATE_FORMAT(p.waktu_keluar, '%H:%i') as waktu_keluar_formatted,
                CASE 
                    WHEN p.waktu_keluar IS NOT NULL THEN 
                        CONCAT(
                            FLOOR(TIMESTAMPDIFF(MINUTE, p.waktu_masuk, p.waktu_keluar) / 60), 'h ',
                            TIMESTAMPDIFF(MINUTE, p.waktu_masuk, p.waktu_keluar) % 60, 'm'
                        )
                    ELSE '-'
                END as durasi
            FROM presensi_piket p
            WHERE p.asisten_user_id = ? 
            AND p.tanggal >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            ORDER BY p.tanggal DESC";
    
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $data,
        'debug' => ['user_id' => $user_id, 'count' => count($data)]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}

mysqli_close($koneksi);

// ===== FILE: proses_presensi.php (DIPERBAIKI) =====
session_start();
require 'koneksi.php';

$user_id = getCurrentUserId();

if (!$user_id) {
    echo json_encode([
        'success' => false, 
        'message' => 'Session tidak valid. Silakan login ulang.',
        'debug_session' => array_keys($_SESSION)
    ]);
    exit;
}

$action = $_POST['action'] ?? '';
$today = date('Y-m-d');
$now = date('Y-m-d H:i:s');

try {
    if ($action === 'clock_in') {
        // Cek apakah sudah clock in hari ini
        $check_sql = "SELECT id FROM presensi_piket WHERE asisten_user_id = ? AND DATE(tanggal) = ?";
        $check_stmt = mysqli_prepare($koneksi, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "is", $user_id, $today);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);
        
        if (mysqli_num_rows($check_result) > 0) {
            echo json_encode(['success' => false, 'message' => 'Anda sudah clock in hari ini']);
            exit;
        }
        
        // Insert clock in baru
        $insert_sql = "INSERT INTO presensi_piket (asisten_user_id, tanggal, waktu_masuk, status) VALUES (?, ?, ?, 'clocked_in')";
        $insert_stmt = mysqli_prepare($koneksi, $insert_sql);
        mysqli_stmt_bind_param($insert_stmt, "iss", $user_id, $today, $now);
        
        if (mysqli_stmt_execute($insert_stmt)) {
            echo json_encode([
                'success' => true, 
                'message' => 'Clock in berhasil!',
                'debug' => ['user_id' => $user_id, 'time' => $now]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal clock in: ' . mysqli_error($koneksi)]);
        }
        
    } elseif ($action === 'clock_out') {
        // Update dengan waktu keluar
        $update_sql = "UPDATE presensi_piket SET waktu_keluar = ?, status = 'completed' WHERE asisten_user_id = ? AND DATE(tanggal) = ? AND status = 'clocked_in'";
        $update_stmt = mysqli_prepare($koneksi, $update_sql);
        mysqli_stmt_bind_param($update_stmt, "sis", $now, $user_id, $today);
        
        if (mysqli_stmt_execute($update_stmt)) {
            if (mysqli_stmt_affected_rows($update_stmt) > 0) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Clock out berhasil!',
                    'debug' => ['user_id' => $user_id, 'time' => $now]
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Tidak ada data clock in yang ditemukan untuk hari ini']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal clock out: ' . mysqli_error($koneksi)]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Action tidak valid']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

mysqli_close($koneksi);
?>