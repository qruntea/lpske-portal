<?php
// ===================================================================
// File: manage_notifications.php
// Lokasi: htdocs/lstars-portal/api/manage_notifications.php
// Fungsi: Mark as read, dismiss, dan manage notifikasi user
// ===================================================================

session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User belum login']);
    exit();
}

require 'koneksi.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

try {
    $user_id = $_SESSION['user_id'];
    $action = $_POST['action'] ?? '';
    $notification_id = $_POST['notification_id'] ?? '';
    
    switch ($action) {
        case 'mark_as_read':
            if (empty($notification_id)) {
                throw new Exception('Notification ID required');
            }
            
            $sql = "UPDATE internal_notifications 
                    SET is_read = 1, read_at = NOW() 
                    WHERE id = ? AND user_id = ?";
            $stmt = $koneksi->prepare($sql);
            $stmt->bind_param("ii", $notification_id, $user_id);
            
            if ($stmt->execute()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Notifikasi ditandai sebagai dibaca',
                    'notification_id' => $notification_id
                ]);
            } else {
                throw new Exception('Gagal menandai notifikasi sebagai dibaca');
            }
            break;
            
        case 'mark_all_as_read':
            $sql = "UPDATE internal_notifications 
                    SET is_read = 1, read_at = NOW() 
                    WHERE user_id = ? AND is_read = 0";
            $stmt = $koneksi->prepare($sql);
            $stmt->bind_param("i", $user_id);
            
            if ($stmt->execute()) {
                $affected_rows = $stmt->affected_rows;
                echo json_encode([
                    'success' => true,
                    'message' => "Berhasil menandai {$affected_rows} notifikasi sebagai dibaca",
                    'affected_rows' => $affected_rows
                ]);
            } else {
                throw new Exception('Gagal menandai semua notifikasi sebagai dibaca');
            }
            break;
            
        case 'dismiss':
            if (empty($notification_id)) {
                throw new Exception('Notification ID required');
            }
            
            $sql = "UPDATE internal_notifications 
                    SET is_dismissed = 1 
                    WHERE id = ? AND user_id = ?";
            $stmt = $koneksi->prepare($sql);
            $stmt->bind_param("ii", $notification_id, $user_id);
            
            if ($stmt->execute()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Notifikasi berhasil dihapus',
                    'notification_id' => $notification_id
                ]);
            } else {
                throw new Exception('Gagal menghapus notifikasi');
            }
            break;
            
        case 'dismiss_all_read':
            $sql = "UPDATE internal_notifications 
                    SET is_dismissed = 1 
                    WHERE user_id = ? AND is_read = 1";
            $stmt = $koneksi->prepare($sql);
            $stmt->bind_param("i", $user_id);
            
            if ($stmt->execute()) {
                $affected_rows = $stmt->affected_rows;
                echo json_encode([
                    'success' => true,
                    'message' => "Berhasil menghapus {$affected_rows} notifikasi yang sudah dibaca",
                    'affected_rows' => $affected_rows
                ]);
            } else {
                throw new Exception('Gagal menghapus notifikasi yang sudah dibaca');
            }
            break;
            
        default:
            throw new Exception('Action tidak valid');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    $koneksi->close();
}
?>