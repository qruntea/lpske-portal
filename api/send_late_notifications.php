<?php
// ===================================================================
// File: send_late_notifications.php
// Lokasi: htdocs/lstars-portal/api/send_late_notifications.php
// Fungsi: Kirim notifikasi ke peminjam yang terlambat
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
    $email_sent_count = 0;
    $whatsapp_sent_count = 0;
    
    foreach ($late_loans as $loan) {
        // Format data untuk notifikasi
        $notification_data = [
            'nama_peminjam' => $loan['nama_peminjam'],
            'nomor_induk' => $loan['nomor_induk'],
            'email' => $loan['email'],
            'nama_alat' => $loan['nama_alat'],
            'kode_alat' => $loan['kode_alat'],
            'tgl_pinjam' => date('d/m/Y', strtotime($loan['tgl_pinjam'])),
            'tgl_rencana_kembali' => date('d/m/Y', strtotime($loan['tgl_rencana_kembali'])),
            'hari_terlambat' => $loan['hari_terlambat'],
            'kategori' => $loan['kategori'],
            'lokasi' => $loan['lokasi']
        ];
        
        // Generate pesan notifikasi
        $subject = "🚨 REMINDER: Pengembalian Alat Laboratorium Terlambat";
        $message = generateNotificationMessage($notification_data);
        
        // Kirim email (untuk testing awal, return true)
        $email_result = sendEmailNotification($loan['email'], $subject, $message);
        
        // Kirim WhatsApp (untuk testing awal, return true)
        $whatsapp_result = sendWhatsAppNotification($loan['nomor_induk'], $message);
        
        // Log notifikasi ke database
        $log_result = logNotification($koneksi, $loan['peminjaman_id'], $loan['peminjam_user_id'], $message);
        
        $notifications_sent[] = [
            'peminjaman_id' => $loan['peminjaman_id'],
            'nama_peminjam' => $loan['nama_peminjam'],
            'nama_alat' => $loan['nama_alat'],
            'hari_terlambat' => $loan['hari_terlambat'],
            'email_sent' => $email_result,
            'whatsapp_sent' => $whatsapp_result,
            'logged' => $log_result
        ];
        
        if ($email_result) $email_sent_count++;
        if ($whatsapp_result) $whatsapp_sent_count++;
    }
    
    echo json_encode([
        'success' => true,
        'message' => "Notifikasi berhasil dikirim ke {$email_sent_count} email dan {$whatsapp_sent_count} WhatsApp",
        'sent_count' => count($late_loans),
        'email_count' => $email_sent_count,
        'whatsapp_count' => $whatsapp_sent_count,
        'notifications' => $notifications_sent
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
// HELPER FUNCTIONS
// ===================================================================

function generateNotificationMessage($data) {
    return "
🚨 PENGINGAT PENGEMBALIAN ALAT LABORATORIUM

Yth. {$data['nama_peminjam']} ({$data['nomor_induk']})

Kami ingatkan bahwa Anda memiliki peminjaman alat laboratorium yang TERLAMBAT:

📋 DETAIL PEMINJAMAN:
- Nama Alat: {$data['nama_alat']}
- Kode Alat: {$data['kode_alat']}
- Kategori: {$data['kategori']}
- Lokasi: {$data['lokasi']}

📅 INFORMASI WAKTU:
- Tanggal Pinjam: {$data['tgl_pinjam']}
- Batas Kembali: {$data['tgl_rencana_kembali']}
- Terlambat: {$data['hari_terlambat']} hari

⚠️ TINDAKAN DIPERLUKAN:
Harap segera mengembalikan alat ke laboratorium untuk menghindari sanksi lebih lanjut.

Terima kasih atas perhatian dan kerjasamanya.

---
Laboratorium Sistem Komputer & Elektronika
Portal LSTARS - Sistem Manajemen Lab
";
}

function sendEmailNotification($email, $subject, $message) {
    // UNTUK TESTING AWAL: return true (simulasi berhasil)
    return true;
    
    // UNTUK PRODUKSI NANTI: uncomment baris di bawah
    /*
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $headers = "From: noreply@lstars.lab\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        return mail($email, $subject, $message, $headers);
    }
    return false;
    */
}

function sendWhatsAppNotification($phone, $message) {
    // UNTUK TESTING AWAL: return true (simulasi berhasil)
    return true;
    
    // UNTUK PRODUKSI NANTI: implementasi API WhatsApp
}

function logNotification($koneksi, $peminjaman_id, $user_id, $message) {
    try {
        $stmt = $koneksi->prepare("
            INSERT INTO notification_logs 
            (peminjaman_id, user_id, message, sent_at, notification_type) 
            VALUES (?, ?, ?, NOW(), 'late_reminder')
        ");
        
        $stmt->bind_param("iis", $peminjaman_id, $user_id, $message);
        return $stmt->execute();
    } catch (Exception $e) {
        return false;
    }
}
?>