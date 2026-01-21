<?php
// ===================================================================
// File: get_notification_logs.php
// Lokasi: htdocs/lpske-portal/api/get_notification_logs.php
// Fungsi: Melihat log notifikasi yang sudah dikirim
// ===================================================================

session_start();

// Cek apakah user adalah admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak. Hanya admin yang dapat mengakses.']);
    exit();
}

require 'koneksi.php';
header('Content-Type: application/json');

try {
    // Parameters untuk pagination dan filter
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    $notification_type = isset($_GET['type']) ? $_GET['type'] : '';
    $date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
    $date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
    
    // Build query dengan filter
    $where_clauses = [];
    $params = [];
    $param_types = '';
    
    if (!empty($notification_type)) {
        $where_clauses[] = "nl.notification_type = ?";
        $params[] = $notification_type;
        $param_types .= 's';
    }
    
    if (!empty($date_from)) {
        $where_clauses[] = "DATE(nl.sent_at) >= ?";
        $params[] = $date_from;
        $param_types .= 's';
    }
    
    if (!empty($date_to)) {
        $where_clauses[] = "DATE(nl.sent_at) <= ?";
        $params[] = $date_to;
        $param_types .= 's';
    }
    
    $where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';
    
    // Query untuk mendapatkan log notifikasi
    $sql = "
        SELECT 
            nl.id,
            nl.notification_type,
            nl.sent_at,
            nl.is_read,
            nl.delivery_status,
            LEFT(nl.message, 200) as message_preview,
            
            u.nama_lengkap as recipient_name,
            u.nomor_induk as recipient_id,
            u.email as recipient_email,
            u.role as recipient_role,
            
            p.id as peminjaman_id,
            i.nama_alat,
            i.kode_alat
            
        FROM notification_logs nl
        LEFT JOIN users u ON nl.user_id = u.id
        LEFT JOIN peminjaman p ON nl.peminjaman_id = p.id
        LEFT JOIN inventory i ON p.inventory_id = i.id
        {$where_sql}
        ORDER BY nl.sent_at DESC
        LIMIT ? OFFSET ?
    ";
    
    // Tambah parameter limit dan offset
    $params[] = $limit;
    $params[] = $offset;
    $param_types .= 'ii';
    
    $stmt = $koneksi->prepare($sql);
    
    if (!empty($params)) {
        $stmt->bind_param($param_types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $logs = [];
    while ($row = $result->fetch_assoc()) {
        $logs[] = [
            'id' => $row['id'],
            'notification_type' => $row['notification_type'],
            'sent_at' => $row['sent_at'],
            'sent_at_formatted' => date('d/m/Y H:i', strtotime($row['sent_at'])),
            'is_read' => (bool)$row['is_read'],
            'delivery_status' => $row['delivery_status'],
            'message_preview' => $row['message_preview'],
            'recipient' => [
                'name' => $row['recipient_name'],
                'id' => $row['recipient_id'],
                'email' => $row['recipient_email'],
                'role' => $row['recipient_role']
            ],
            'peminjaman' => [
                'id' => $row['peminjaman_id'],
                'nama_alat' => $row['nama_alat'],
                'kode_alat' => $row['kode_alat']
            ]
        ];
    }
    
    // Query untuk total count
    $count_sql = "
        SELECT COUNT(*) as total
        FROM notification_logs nl
        LEFT JOIN users u ON nl.user_id = u.id
        LEFT JOIN peminjaman p ON nl.peminjaman_id = p.id
        {$where_sql}
    ";
    
    if (!empty($where_clauses)) {
        $count_stmt = $koneksi->prepare($count_sql);
        $count_param_types = substr($param_types, 0, -2); // Remove limit/offset types
        $count_params = array_slice($params, 0, -2); // Remove limit/offset values
        
        if (!empty($count_params)) {
            $count_stmt->bind_param($count_param_types, ...$count_params);
        }
        $count_stmt->execute();
        $count_result = $count_stmt->get_result();
    } else {
        $count_result = $koneksi->query($count_sql);
    }
    
    $total_count = $count_result->fetch_assoc()['total'];
    
    echo json_encode([
        'success' => true,
        'data' => $logs,
        'pagination' => [
            'total' => (int)$total_count,
            'limit' => $limit,
            'offset' => $offset,
            'has_more' => ($offset + $limit) < $total_count
        ],
        'query_info' => [
            'executed_at' => date('Y-m-d H:i:s'),
            'admin_id' => $_SESSION['user_id']
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
    if (isset($count_stmt)) {
        $count_stmt->close();
    }
    $koneksi->close();
}
?>