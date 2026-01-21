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
    
    $stats = [];
    
    // 1. Perizinan Pending (dari tabel izin_penelitian dengan status 'Diajukan')
    $pending_sql = "SELECT COUNT(*) as count FROM izin_penelitian WHERE status = 'Diajukan'";
    $pending_result = mysqli_query($koneksi, $pending_sql);
    $stats['perizinan_pending'] = $pending_result ? mysqli_fetch_assoc($pending_result)['count'] : 0;
    
    // 2. Total Dosen (dari tabel dosen)
    $dosen_sql = "SELECT COUNT(*) as count FROM dosen";
    $dosen_result = mysqli_query($koneksi, $dosen_sql);
    $stats['total_dosen'] = $dosen_result ? mysqli_fetch_assoc($dosen_result)['count'] : 0;
    
    // 3. Total Asisten (dari tabel asisten)
    $asisten_sql = "SELECT COUNT(*) as count FROM asisten";
    $asisten_result = mysqli_query($koneksi, $asisten_sql);
    $stats['total_asisten'] = $asisten_result ? mysqli_fetch_assoc($asisten_result)['count'] : 0;
    
    // 4. Total Users (dosen + asisten)
    $stats['total_users'] = $stats['total_dosen'] + $stats['total_asisten'];
    
    // 5. Equipment/Inventory (dari tabel inventory)
    $equipment_sql = "SELECT COUNT(*) as count FROM inventory";
    $equipment_result = mysqli_query($koneksi, $equipment_sql);
    $stats['total_equipment'] = $equipment_result ? mysqli_fetch_assoc($equipment_result)['count'] : 0;
    
    // 6. Asisten Aktif (status = 'Aktif' atau status = 1)
    $asisten_aktif_sql = "SELECT COUNT(*) as count FROM asisten WHERE status = 'Aktif' OR status = 1";
    $asisten_aktif_result = mysqli_query($koneksi, $asisten_aktif_sql);
    $stats['asisten_aktif'] = $asisten_aktif_result ? mysqli_fetch_assoc($asisten_aktif_result)['count'] : 0;
    
    // 7. Presensi hari ini (dari tabel presensi)
    $today = date('Y-m-d');
    $presensi_today_sql = "SELECT COUNT(DISTINCT user_id) as count FROM presensi WHERE DATE(tanggal) = ?";
    $presensi_stmt = mysqli_prepare($koneksi, $presensi_today_sql);
    if ($presensi_stmt) {
        mysqli_stmt_bind_param($presensi_stmt, "s", $today);
        mysqli_stmt_execute($presensi_stmt);
        $presensi_result = mysqli_stmt_get_result($presensi_stmt);
        $stats['presensi_hari_ini'] = $presensi_result ? mysqli_fetch_assoc($presensi_result)['count'] : 0;
        mysqli_stmt_close($presensi_stmt);
    } else {
        $stats['presensi_hari_ini'] = 0;
    }
    
    // 8. Equipment Tersedia (jika ada sistem quantity)
    $equipment_tersedia_sql = "SELECT COUNT(*) as count FROM inventory WHERE status = 'Tersedia'";
    $equipment_tersedia_result = mysqli_query($koneksi, $equipment_tersedia_sql);
    $stats['equipment_tersedia'] = $equipment_tersedia_result ? mysqli_fetch_assoc($equipment_tersedia_result)['count'] : $stats['total_equipment'];
    
    echo json_encode([
        'success' => true,
        'data' => $stats
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage(),
        'data' => [
            'perizinan_pending' => 0,
            'total_users' => 0,
            'total_dosen' => 0,
            'total_asisten' => 0,
            'asisten_aktif' => 0,
            'total_equipment' => 0,
            'equipment_tersedia' => 0,
            'presensi_hari_ini' => 0
        ]
    ]);
} finally {
    if (isset($koneksi)) {
        mysqli_close($koneksi);
    }
}
?>