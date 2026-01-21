<?php
// ===================================================================
// File: send_internal_late_notifications.php
// Lokasi: htdocs/lstars-portal/api/send_internal_late_notifications.php
// Fungsi: Kirim notifikasi internal ke dashboard user untuk peminjaman terlambat
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
    // Query untuk mendapatkan peminjaman yang terlambat
    $sql = "
        SELECT 
            p.id AS peminjaman_id,
            p.peminjam_user_id,
            p.tgl_pinjam,
            p.tgl_rencana_kembali,
            DATEDIFF(CURDATE(), p.tgl_rencana_kembali) as hari_terlambat,
            
            -- Data Peminjam
            u.nama_lengkap AS nama_peminjam,
            u.nomor_induk,
            u.email,
            u.role,
            
            -- Data Alat
            i.nama_alat,
            i.kode_alat,
            i.kategori,
            i.lokasi
            
        FROM peminjaman p
        LEFT JOIN users u ON p.peminjam_user_id = u.id
        LEFT JOIN inventory i ON p.inventory_id = i.id
        WHERE p.tgl_aktual_kembali IS NULL 
        AND p.tgl_rencana_kembali < CURDATE()
        ORDER BY hari_terlambat DESC
    ";
    
    $result = $koneksi->query($sql);
    
    if (!$result) {
        throw new Exception("Query error: " . $koneksi->error);
    }
    
    $late_loans = [];
    while ($row = $result->fetch_assoc()) {
        $late_loans[] = $row;
    }
    
    if (empty($late_loans)) {
        echo json_encode([
            'success' => true,
            'message' => 'Tidak ada peminjaman yang terlambat',
            'sent_count' => 0,
            'notifications' => []
        ]);
        exit();
    }
    
    $notifications_sent = [];
    $success_count = 0;
    $admin_name = $_SESSION['user_name'] ?? 'Admin';
    
    // Siapkan statement untuk insert notifikasi internal
    $stmt = $koneksi->prepare("
        INSERT INTO internal_notifications 
        (user_id, peminjaman_id, type, title, message, icon, color, priority, created_at) 
        VALUES (?, ?, 'late_reminder', ?, ?, 'fas fa-exclamation-triangle', 'red', 'high', NOW())
    ");
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $koneksi->error);
    }
    
    foreach ($late_loans as $loan) {
        // Cek apakah sudah ada notifikasi terlambat untuk peminjaman ini hari ini
        $check_sql = "SELECT id FROM internal_notifications 
                      WHERE user_id = ? AND peminjaman_id = ? 
                      AND type = 'late_reminder' 
                      AND DATE(created_at) = CURDATE()";
        $check_stmt = $koneksi->prepare($check_sql);
        $check_stmt->bind_param("ii", $loan['peminjam_user_id'], $loan['peminjaman_id']);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            // Skip jika sudah ada notifikasi hari ini
            $check_stmt->close();
            continue;
        }
        $check_stmt->close();
        
        // Generate title dan message untuk notifikasi
        $title = "⏰ PENGINGAT: Pengembalian Alat Terlambat";
        $message = sprintf(
            "Halo %s! Anda memiliki peminjaman alat \"%s\" (%s) yang sudah terlambat %d hari dari tanggal yang dijadwalkan (%s). Harap segera mengembalikan alat ke laboratorium untuk menghindari sanksi lebih lanjut.",
            $loan['nama_peminjam'],
            $loan['nama_alat'],
            $loan['kode_alat'],
            $loan['hari_terlambat'],
            date('d/m/Y', strtotime($loan['tgl_rencana_kembali']))
        );
        
        // Insert notifikasi internal
        $stmt->bind_param("iiss", 
            $loan['peminjam_user_id'], 
            $loan['peminjaman_id'], 
            $title, 
            $message
        );
        
        if ($stmt->execute()) {
            $success_count++;
            
            // Log ke notification_logs juga (untuk tracking admin)
            logNotificationToSystem($koneksi, $loan, $admin_name);
            
            $notifications_sent[] = [
                'user_id' => $loan['peminjam_user_id'],
                'peminjaman_id' => $loan['peminjaman_id'],
                'nama_peminjam' => $loan['nama_peminjam'],
                'nama_alat' => $loan['nama_alat'],
                'kode_alat' => $loan['kode_alat'],
                'hari_terlambat' => $loan['hari_terlambat'],
                'status' => 'sent_to_dashboard'
            ];
        }
    }
    
    $stmt->close();
    
    echo json_encode([
        'success' => true,
        'message' => "Berhasil mengirim {$success_count} notifikasi ke dashboard user",
        'sent_count' => $success_count,
        'total_late' => count($late_loans),
        'notifications' => $notifications_sent,
        'admin_info' => [
            'sent_by' => $admin_name,
            'sent_at' => date('Y-m-d H:i:s')
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
} finally {
    if (isset($result)) {
        $result->free();
    }
    $koneksi->close();
}

// ===================================================================
// HELPER FUNCTION
// ===================================================================
function logNotificationToSystem($koneksi, $loan, $admin_name) {
    try {
        $log_message = "🚨 PENGINGAT PENGEMBALIAN ALAT LABORATORIUM (DASHBOARD INTERNAL)\n\n" .
                      "Notifikasi dikirim ke dashboard user: {$loan['nama_peminjam']} ({$loan['nomor_induk']})\n\n" .
                      "📋 DETAIL PEMINJAMAN:\n" .
                      "- Nama Alat: {$loan['nama_alat']}\n" .
                      "- Kode Alat: {$loan['kode_alat']}\n" .
                      "- Kategori: {$loan['kategori']}\n" .
                      "- Lokasi: {$loan['lokasi']}\n\n" .
                      "📅 INFORMASI WAKTU:\n" .
                      "- Tanggal Pinjam: " . date('d/m/Y', strtotime($loan['tgl_pinjam'])) . "\n" .
                      "- Batas Kembali: " . date('d/m/Y', strtotime($loan['tgl_rencana_kembali'])) . "\n" .
                      "- Terlambat: {$loan['hari_terlambat']} hari\n\n" .
                      "✅ Notifikasi berhasil dikirim ke dashboard internal user\n" .
                      "Notifikasi dikirim oleh: {$admin_name}\n" .
                      "---\n" .
                      "Portal LSTARS - Sistem Manajemen Lab";
        
        $stmt = $koneksi->prepare("
            INSERT INTO notification_logs 
            (peminjaman_id, user_id, message, notification_type, sent_at, delivery_status, delivery_method) 
            VALUES (?, ?, ?, 'late_reminder', NOW(), 'sent', 'internal_dashboard')
        ");
        
        if ($stmt) {
            $stmt->bind_param("iis", $loan['peminjaman_id'], $loan['peminjam_user_id'], $log_message);
            $stmt->execute();
            $stmt->close();
        }
    } catch (Exception $e) {
        // Silent fail untuk logging, tidak mengganggu proses utama
        error_log("Failed to log notification: " . $e->getMessage());
    }
}
?>