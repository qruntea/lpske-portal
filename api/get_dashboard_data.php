<?php
// api/get_dashboard_data.php - FIXED VERSION
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    require 'koneksi.php';
    
    if (!$koneksi) {
        throw new Exception('Koneksi database gagal');
    }
    
    $user_id = $_SESSION['user_id'];
    
    // 1. Ambil info user
    $user_sql = "SELECT * FROM users WHERE id = ?";
    $user_stmt = mysqli_prepare($koneksi, $user_sql);
    mysqli_stmt_bind_param($user_stmt, "i", $user_id);
    mysqli_stmt_execute($user_stmt);
    $user_result = mysqli_stmt_get_result($user_stmt);
    $user_data = mysqli_fetch_assoc($user_result);
    mysqli_stmt_close($user_stmt);
    
    // 2. Hitung statistik - FIXED dengan nama kolom yang benar
    
    // Total izin penelitian (pakai mahasiswa_user_id)
    $my_permissions_sql = "SELECT COUNT(*) as count FROM izin_penelitian WHERE mahasiswa_user_id = ?";
    $my_permissions_stmt = mysqli_prepare($koneksi, $my_permissions_sql);
    mysqli_stmt_bind_param($my_permissions_stmt, "i", $user_id);
    mysqli_stmt_execute($my_permissions_stmt);
    $my_permissions_result = mysqli_stmt_get_result($my_permissions_stmt);
    $my_permissions = mysqli_fetch_assoc($my_permissions_result)['count'];
    mysqli_stmt_close($my_permissions_stmt);
    
    // Izin yang disetujui
    $approved_sql = "SELECT COUNT(*) as count FROM izin_penelitian WHERE mahasiswa_user_id = ? AND status = 'Disetujui'";
    $approved_stmt = mysqli_prepare($koneksi, $approved_sql);
    mysqli_stmt_bind_param($approved_stmt, "i", $user_id);
    mysqli_stmt_execute($approved_stmt);
    $approved_result = mysqli_stmt_get_result($approved_stmt);
    $approved_count = mysqli_fetch_assoc($approved_result)['count'];
    mysqli_stmt_close($approved_stmt);
    
    // Izin yang pending (Diajukan)
    $pending_sql = "SELECT COUNT(*) as count FROM izin_penelitian WHERE mahasiswa_user_id = ? AND status = 'Diajukan'";
    $pending_stmt = mysqli_prepare($koneksi, $pending_sql);
    mysqli_stmt_bind_param($pending_stmt, "i", $user_id);
    mysqli_stmt_execute($pending_stmt);
    $pending_result = mysqli_stmt_get_result($pending_stmt);
    $pending_count = mysqli_fetch_assoc($pending_result)['count'];
    mysqli_stmt_close($pending_stmt);
    
    // Equipment tersedia (pakai sistem quantity)
    $equipment_sql = "SELECT SUM(jumlah_tersedia) as count FROM inventory WHERE jumlah_tersedia > 0";
    $equipment_result = mysqli_query($koneksi, $equipment_sql);
    $equipment_count = $equipment_result ? mysqli_fetch_assoc($equipment_result)['count'] : 0;
    
    // 3. Aktivitas terbaru (pakai mahasiswa_user_id)
    $activities = [];
    $activities_sql = "SELECT judul_penelitian, status, tgl_pengajuan 
                      FROM izin_penelitian 
                      WHERE mahasiswa_user_id = ? 
                      ORDER BY tgl_pengajuan DESC 
                      LIMIT 5";
    $activities_stmt = mysqli_prepare($koneksi, $activities_sql);
    mysqli_stmt_bind_param($activities_stmt, "i", $user_id);
    mysqli_stmt_execute($activities_stmt);
    $activities_result = mysqli_stmt_get_result($activities_stmt);
    
    while ($activity = mysqli_fetch_assoc($activities_result)) {
        $icon = 'fa-file-alt';
        $color = 'text-blue-500';
        
        if ($activity['status'] == 'Disetujui') {
            $icon = 'fa-check-circle';
            $color = 'text-green-500';
        } elseif ($activity['status'] == 'Ditolak') {
            $icon = 'fa-times-circle';
            $color = 'text-red-500';
        }
        
        $activities[] = [
            'icon' => $icon,
            'color' => $color,
            'description' => "Perizinan \"{$activity['judul_penelitian']}\" - {$activity['status']}",
            'time' => date('d M Y', strtotime($activity['tgl_pengajuan']))
        ];
    }
    mysqli_stmt_close($activities_stmt);
    
    // Response
    echo json_encode([
        'success' => true,
        'data' => [
            'user_info' => [
                'nama_lengkap' => $user_data['nama_lengkap'] ?? $user_data['username'] ?? 'User',
                'role' => $user_data['role'] ?? 'user'
            ],
            'statistics' => [
                'my_permissions' => (int)$my_permissions,
                'approved_count' => (int)$approved_count,
                'pending_count' => (int)$pending_count,
                'equipment_count' => (int)$equipment_count
            ],
            'activities' => $activities
        ],
        'debug' => [
            'user_id' => $user_id,
            'session_data' => $_SESSION,
            'queries_executed' => [
                'my_permissions' => $my_permissions,
                'approved_count' => $approved_count,
                'pending_count' => $pending_count,
                'equipment_count' => $equipment_count
            ],
            'timestamp' => date('Y-m-d H:i:s')
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error_line' => $e->getLine(),
        'data' => [
            'user_info' => ['nama_lengkap' => 'Error User'],
            'statistics' => [
                'my_permissions' => 0,
                'approved_count' => 0,
                'pending_count' => 0,
                'equipment_count' => 0
            ],
            'activities' => []
        ]
    ]);
} finally {
    if (isset($koneksi)) {
        mysqli_close($koneksi);
    }
}
?>