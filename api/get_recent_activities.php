<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    require 'koneksi.php';
    
    if (!$koneksi) {
        throw new Exception('Koneksi database gagal');
    }
    
    $activities = [];
    
    // 1. Perizinan terbaru (5 terakhir)
    $izin_sql = "SELECT judul_penelitian, nama_dosen, status, created_at 
                 FROM izin_penelitian 
                 ORDER BY created_at DESC 
                 LIMIT 5";
    $izin_result = mysqli_query($koneksi, $izin_sql);
    
    if ($izin_result) {
        while ($row = mysqli_fetch_assoc($izin_result)) {
            $icon = 'fa-file-alt';
            $color = 'text-blue-500';
            
            if ($row['status'] == 'Disetujui') {
                $icon = 'fa-check-circle';
                $color = 'text-green-500';
            } elseif ($row['status'] == 'Ditolak') {
                $icon = 'fa-times-circle';
                $color = 'text-red-500';
            }
            
            $activities[] = [
                'icon' => $icon,
                'color' => $color,
                'description' => "Perizinan penelitian \"{$row['judul_penelitian']}\" dari {$row['nama_dosen']} - {$row['status']}",
                'time' => date('d M Y H:i', strtotime($row['created_at'])),
                'timestamp' => strtotime($row['created_at'])
            ];
        }
    }
    
    // 2. Dosen baru terdaftar (3 terakhir)
    $dosen_sql = "SELECT nama_dosen, u.created_at 
                  FROM dosen d 
                  LEFT JOIN users u ON d.user_id = u.id 
                  ORDER BY u.created_at DESC 
                  LIMIT 3";
    $dosen_result = mysqli_query($koneksi, $dosen_sql);
    
    if ($dosen_result) {
        while ($row = mysqli_fetch_assoc($dosen_result)) {
            $activities[] = [
                'icon' => 'fa-user-plus',
                'color' => 'text-green-500',
                'description' => "Dosen baru \"{$row['nama_dosen']}\" telah terdaftar",
                'time' => date('d M Y H:i', strtotime($row['created_at'])),
                'timestamp' => strtotime($row['created_at'])
            ];
        }
    }
    
    // 3. Asisten baru terdaftar (3 terakhir)
    $asisten_sql = "SELECT nama_lengkap, created_at 
                    FROM asisten 
                    ORDER BY created_at DESC 
                    LIMIT 3";
    $asisten_result = mysqli_query($koneksi, $asisten_sql);
    
    if ($asisten_result) {
        while ($row = mysqli_fetch_assoc($asisten_result)) {
            $activities[] = [
                'icon' => 'fa-graduation-cap',
                'color' => 'text-blue-500',
                'description' => "Asisten baru \"{$row['nama_lengkap']}\" telah terdaftar",
                'time' => date('d M Y H:i', strtotime($row['created_at'])),
                'timestamp' => strtotime($row['created_at'])
            ];
        }
    }
    
    // 4. Presensi hari ini (3 terakhir)
    $today = date('Y-m-d');
    $presensi_sql = "SELECT a.nama_lengkap, p.waktu_masuk 
                     FROM presensi p 
                     LEFT JOIN asisten a ON p.user_id = a.user_id 
                     WHERE DATE(p.tanggal) = ? 
                     ORDER BY p.waktu_masuk DESC 
                     LIMIT 3";
    $presensi_stmt = mysqli_prepare($koneksi, $presensi_sql);
    
    if ($presensi_stmt) {
        mysqli_stmt_bind_param($presensi_stmt, "s", $today);
        mysqli_stmt_execute($presensi_stmt);
        $presensi_result = mysqli_stmt_get_result($presensi_stmt);
        
        while ($row = mysqli_fetch_assoc($presensi_result)) {
            $activities[] = [
                'icon' => 'fa-clock',
                'color' => 'text-green-500',
                'description' => "Asisten \"{$row['nama_lengkap']}\" melakukan presensi",
                'time' => date('d M Y H:i', strtotime($row['waktu_masuk'])),
                'timestamp' => strtotime($row['waktu_masuk'])
            ];
        }
        mysqli_stmt_close($presensi_stmt);
    }
    
    // Sort by timestamp (newest first)
    usort($activities, function($a, $b) {
        return $b['timestamp'] - $a['timestamp'];
    });
    
    // Take only 8 most recent
    $activities = array_slice($activities, 0, 8);
    
    echo json_encode([
        'success' => true,
        'data' => $activities
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'data' => []
    ]);
} finally {
    if (isset($koneksi)) {
        mysqli_close($koneksi);
    }
}
?>