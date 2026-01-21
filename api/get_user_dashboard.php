<?php
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
    $user_role = $_SESSION['user_role'];
    
    $data = [
        'user_info' => [],
        'statistics' => [],
        'activities' => []
    ];
    
    // 1. Ambil info user
    $user_sql = "SELECT * FROM users WHERE id = ?";
    $user_stmt = mysqli_prepare($koneksi, $user_sql);
    mysqli_stmt_bind_param($user_stmt, "i", $user_id);
    mysqli_stmt_execute($user_stmt);
    $user_result = mysqli_stmt_get_result($user_stmt);
    
    if ($user_row = mysqli_fetch_assoc($user_result)) {
        $data['user_info'] = [
            'id' => $user_row['id'],
            'username' => $user_row['username'],
            'email' => $user_row['email'],
            'role' => $user_row['role'],
            'nama_lengkap' => $user_row['nama_lengkap'] ?? $user_row['username']
        ];
        
        // Ambil data tambahan berdasarkan role
        if ($user_role === 'dosen') {
            $dosen_sql = "SELECT * FROM dosen WHERE user_id = ?";
            $dosen_stmt = mysqli_prepare($koneksi, $dosen_sql);
            mysqli_stmt_bind_param($dosen_stmt, "i", $user_id);
            mysqli_stmt_execute($dosen_stmt);
            $dosen_result = mysqli_stmt_get_result($dosen_stmt);
            
            if ($dosen_row = mysqli_fetch_assoc($dosen_result)) {
                $data['user_info']['nama_lengkap'] = trim($dosen_row['gelar_depan'] . ' ' . $dosen_row['nama_dosen'] . ' ' . $dosen_row['gelar_belakang']);
                $data['user_info']['nidn'] = $dosen_row['nidn'];
                $data['user_info']['homebase_prodi'] = $dosen_row['homebase_prodi'];
            }
            mysqli_stmt_close($dosen_stmt);
        }
    }
    mysqli_stmt_close($user_stmt);
    
    // 2. Statistik berdasarkan role
    if ($user_role === 'dosen') {
        // Statistik untuk dosen
        $my_permissions_sql = "SELECT COUNT(*) as count FROM izin_penelitian WHERE user_id = ?";
        $my_permissions_stmt = mysqli_prepare($koneksi, $my_permissions_sql);
        mysqli_stmt_bind_param($my_permissions_stmt, "i", $user_id);
        mysqli_stmt_execute($my_permissions_stmt);
        $my_permissions_result = mysqli_stmt_get_result($my_permissions_stmt);
        $data['statistics']['my_permissions'] = mysqli_fetch_assoc($my_permissions_result)['count'];
        mysqli_stmt_close($my_permissions_stmt);
        
        $approved_sql = "SELECT COUNT(*) as count FROM izin_penelitian WHERE user_id = ? AND status = 'Disetujui'";
        $approved_stmt = mysqli_prepare($koneksi, $approved_sql);
        mysqli_stmt_bind_param($approved_stmt, "i", $user_id);
        mysqli_stmt_execute($approved_stmt);
        $approved_result = mysqli_stmt_get_result($approved_stmt);
        $data['statistics']['approved_count'] = mysqli_fetch_assoc($approved_result)['count'];
        mysqli_stmt_close($approved_stmt);
        
        $pending_sql = "SELECT COUNT(*) as count FROM izin_penelitian WHERE user_id = ? AND status = 'Diajukan'";
        $pending_stmt = mysqli_prepare($koneksi, $pending_sql);
        mysqli_stmt_bind_param($pending_stmt, "i", $user_id);
        mysqli_stmt_execute($pending_stmt);
        $pending_result = mysqli_stmt_get_result($pending_stmt);
        $data['statistics']['pending_count'] = mysqli_fetch_assoc($pending_result)['count'];
        mysqli_stmt_close($pending_stmt);
    }
    
    // Equipment tersedia (untuk semua role)
    $equipment_sql = "SELECT COUNT(*) as count FROM inventory WHERE status = 'Tersedia'";
    $equipment_result = mysqli_query($koneksi, $equipment_sql);
    $data['statistics']['equipment_count'] = $equipment_result ? mysqli_fetch_assoc($equipment_result)['count'] : 0;
    
    // 3. Aktivitas terbaru user
    if ($user_role === 'dosen') {
        $activities_sql = "SELECT judul_penelitian, status, created_at 
                          FROM izin_penelitian 
                          WHERE user_id = ? 
                          ORDER BY created_at DESC 
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
            
            $data['activities'][] = [
                'icon' => $icon,
                'color' => $color,
                'description' => "Perizinan \"{$activity['judul_penelitian']}\" - {$activity['status']}",
                'time' => date('d M Y H:i', strtotime($activity['created_at']))
            ];
        }
        mysqli_stmt_close($activities_stmt);
    }
    
    echo json_encode([
        'success' => true,
        'data' => $data
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} finally {
    if (isset($koneksi)) {
        mysqli_close($koneksi);
    }
}
?>