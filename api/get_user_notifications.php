<?php
// ===================================================================
// File: get_user_notifications.php
// Lokasi: htdocs/lpske-portal/api/get_user_notifications.php
// Fungsi: Mengambil notifikasi internal untuk user yang login
// ===================================================================

session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User belum login']);
    exit();
}

require 'koneksi.php';
header('Content-Type: application/json');

try {
    $user_id = $_SESSION['user_id'];
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $include_read = isset($_GET['include_read']) ? (bool)$_GET['include_read'] : true;
    
    // Build WHERE clause
    $where_conditions = ["user_id = ?"];
    $params = [$user_id];
    $param_types = "i";
    
    if (!$include_read) {
        $where_conditions[] = "is_read = 0";
    }
    
    // Exclude dismissed notifications
    $where_conditions[] = "is_dismissed = 0";
    
    // Exclude expired notifications
    $where_conditions[] = "(expires_at IS NULL OR expires_at > NOW())";
    
    $where_sql = "WHERE " . implode(" AND ", $where_conditions);
    
    // Query untuk mengambil notifikasi
    $sql = "
        SELECT 
            id,
            type,
            title,
            message,
            icon,
            color,
            priority,
            is_read,
            action_url,
            action_text,
            created_at,
            read_at,
            peminjaman_id,
            DATE_FORMAT(created_at, '%d %b %Y • %H:%i') as created_at_formatted,
            CASE 
                WHEN created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR) THEN 'baru'
                WHEN created_at >= DATE_SUB(NOW(), INTERVAL 1 DAY) THEN 'hari_ini'
                WHEN created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 'minggu_ini'
                ELSE 'lama'
            END as time_category
        FROM internal_notifications 
        {$where_sql}
        ORDER BY 
            CASE priority 
                WHEN 'urgent' THEN 1 
                WHEN 'high' THEN 2 
                WHEN 'normal' THEN 3 
                WHEN 'low' THEN 4 
            END ASC,
            created_at DESC
        LIMIT ?
    ";
    
    $stmt = $koneksi->prepare($sql);
    $param_types .= "i";
    $params[] = $limit;
    $stmt->bind_param($param_types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        $notifications[] = [
            'id' => (int)$row['id'],
            'type' => $row['type'],
            'title' => $row['title'],
            'message' => $row['message'],
            'icon' => $row['icon'],
            'color' => $row['color'],
            'priority' => $row['priority'],
            'is_read' => (bool)$row['is_read'],
            'action_url' => $row['action_url'],
            'action_text' => $row['action_text'],
            'created_at' => $row['created_at'],
            'created_at_formatted' => $row['created_at_formatted'],
            'time_category' => $row['time_category'],
            'peminjaman_id' => $row['peminjaman_id']
        ];
    }
    
    // Hitung jumlah notifikasi yang belum dibaca
    $unread_sql = "
        SELECT COUNT(*) as unread_count 
        FROM internal_notifications 
        WHERE user_id = ? AND is_read = 0 AND is_dismissed = 0 
        AND (expires_at IS NULL OR expires_at > NOW())
    ";
    $unread_stmt = $koneksi->prepare($unread_sql);
    $unread_stmt->bind_param("i", $user_id);
    $unread_stmt->execute();
    $unread_result = $unread_stmt->get_result();
    $unread_count = $unread_result->fetch_assoc()['unread_count'];
    
    echo json_encode([
        'success' => true,
        'data' => $notifications,
        'unread_count' => (int)$unread_count,
        'total_count' => count($notifications),
        'user_id' => $user_id,
        'query_info' => [
            'limit' => $limit,
            'include_read' => $include_read,
            'executed_at' => date('Y-m-d H:i:s')
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($unread_stmt)) {
        $unread_stmt->close();
    }
    $koneksi->close();
}
?>