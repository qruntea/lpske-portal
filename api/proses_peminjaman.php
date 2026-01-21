<?php
// ===================================================================
// FILE: api/proses_peminjaman.php - SUPER FIXED VERSION
// ===================================================================

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Debug mode - set ke false untuk production
$debug_mode = true;

if ($debug_mode) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

session_start();

// Log untuk debugging
if ($debug_mode) {
    error_log("=== PEMINJAMAN DEBUG START ===");
    error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
    error_log("POST DATA: " . print_r($_POST, true));
    error_log("SESSION: " . print_r($_SESSION, true));
}

// Check session
if (!isset($_SESSION['user_id'])) {
    $response = [
        'success' => false,
        'message' => 'Sesi tidak valid. Silakan login ulang.',
        'debug_info' => $debug_mode ? [
            'session_data' => $_SESSION,
            'session_id' => session_id()
        ] : null
    ];
    echo json_encode($response);
    exit;
}

// Check method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response = [
        'success' => false,
        'message' => 'Method tidak diizinkan. Gunakan POST.',
        'debug_info' => $debug_mode ? [
            'method' => $_SERVER['REQUEST_METHOD']
        ] : null
    ];
    echo json_encode($response);
    exit;
}

try {
    // Include koneksi database
    require_once 'koneksi.php';
    
    if (!$koneksi) {
        throw new Exception('Koneksi database gagal: ' . mysqli_connect_error());
    }
    
    $user_id = $_SESSION['user_id'];
    
    // Ambil dan validasi input
    $nama_peminjam = trim($_POST['nama_peminjam'] ?? '');
    $nim_peminjam = trim($_POST['nim_peminjam'] ?? '');
    $alat_id = trim($_POST['alat_id'] ?? '');
    $tgl_pinjam = trim($_POST['tgl_pinjam'] ?? '');
    $tgl_rencana_kembali = trim($_POST['tgl_rencana_kembali'] ?? '');
    $keperluan = trim($_POST['keperluan'] ?? '');
    
    if ($debug_mode) {
        error_log("PROCESSED INPUT:");
        error_log("- nama_peminjam: '$nama_peminjam'");
        error_log("- nim_peminjam: '$nim_peminjam'");
        error_log("- alat_id: '$alat_id'");
        error_log("- tgl_pinjam: '$tgl_pinjam'");
        error_log("- tgl_rencana_kembali: '$tgl_rencana_kembali'");
    }
    
    // Validasi field wajib
    $errors = [];
    
    if (empty($nama_peminjam)) {
        $errors[] = 'Nama peminjam harus diisi';
    }
    
    if (empty($nim_peminjam)) {
        $errors[] = 'NIM/NPM harus diisi';
    }
    
    if (empty($alat_id) || !is_numeric($alat_id)) {
        $errors[] = 'Alat/ruang harus dipilih dengan benar';
    }
    
    if (empty($tgl_pinjam)) {
        $errors[] = 'Tanggal peminjaman harus diisi';
    }
    
    if (empty($tgl_rencana_kembali)) {
        $errors[] = 'Tanggal rencana pengembalian harus diisi';
    }
    
    // Jika ada error validasi
    if (!empty($errors)) {
        $response = [
            'success' => false,
            'message' => 'Data tidak lengkap: ' . implode(', ', $errors),
            'debug_info' => $debug_mode ? [
                'errors' => $errors,
                'received_data' => $_POST
            ] : null
        ];
        echo json_encode($response);
        exit;
    }
    
    // Konversi alat_id ke integer
    $alat_id = (int)$alat_id;
    
    // Validasi format tanggal
    if (!strtotime($tgl_pinjam)) {
        $response = [
            'success' => false,
            'message' => 'Format tanggal peminjaman tidak valid'
        ];
        echo json_encode($response);
        exit;
    }
    
    if (!strtotime($tgl_rencana_kembali)) {
        $response = [
            'success' => false,
            'message' => 'Format tanggal pengembalian tidak valid'
        ];
        echo json_encode($response);
        exit;
    }
    
    // Validasi logika tanggal
    if (strtotime($tgl_pinjam) >= strtotime($tgl_rencana_kembali)) {
        $response = [
            'success' => false,
            'message' => 'Tanggal rencana pengembalian harus setelah tanggal peminjaman'
        ];
        echo json_encode($response);
        exit;
    }
    
    // Cek apakah alat tersedia
    $check_query = "SELECT id, nama_alat, kode_alat, status FROM inventory WHERE id = ?";
    $check_stmt = $koneksi->prepare($check_query);
    
    if (!$check_stmt) {
        throw new Exception('Prepare failed: ' . $koneksi->error);
    }
    
    $check_stmt->bind_param("i", $alat_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    $alat_data = $check_result->fetch_assoc();
    $check_stmt->close();
    
    if (!$alat_data) {
        $response = [
            'success' => false,
            'message' => 'Alat/ruang tidak ditemukan'
        ];
        echo json_encode($response);
        exit;
    }
    
    if ($debug_mode) {
        error_log("ALAT DATA: " . print_r($alat_data, true));
    }
    
    // Insert data peminjaman
    $insert_query = "INSERT INTO peminjaman (
        inventory_id,
        peminjam_user_id,
        nama_peminjam,
        nim_peminjam,
        tgl_pinjam,
        tgl_rencana_kembali,
        status,
        catatan
    ) VALUES (?, ?, ?, ?, ?, ?, 'Diajukan', ?)";
    
    $insert_stmt = $koneksi->prepare($insert_query);
    
    if (!$insert_stmt) {
        throw new Exception('Insert prepare failed: ' . $koneksi->error);
    }
    
    $insert_stmt->bind_param(
        "iisssss",
        $alat_id,
        $user_id,
        $nama_peminjam,
        $nim_peminjam,
        $tgl_pinjam,
        $tgl_rencana_kembali,
        $keperluan
    );
    
    if ($debug_mode) {
        error_log("EXECUTING INSERT WITH:");
        error_log("- alat_id: $alat_id");
        error_log("- user_id: $user_id");
        error_log("- nama_peminjam: $nama_peminjam");
        error_log("- nim_peminjam: $nim_peminjam");
        error_log("- tgl_pinjam: $tgl_pinjam");
        error_log("- tgl_rencana_kembali: $tgl_rencana_kembali");
        error_log("- keperluan: $keperluan");
    }
    
    if ($insert_stmt->execute()) {
        $peminjaman_id = $koneksi->insert_id;
        $insert_stmt->close();
        
        if ($debug_mode) {
            error_log("INSERT SUCCESS - ID: $peminjaman_id");
        }
        
        // Response sukses
        $response = [
            'success' => true,
            'message' => 'Pengajuan peminjaman berhasil dikirim!',
            'data' => [
                'peminjaman_id' => $peminjaman_id,
                'nama_peminjam' => $nama_peminjam,
                'nim_peminjam' => $nim_peminjam,
                'nama_alat' => $alat_data['nama_alat'],
                'kode_alat' => $alat_data['kode_alat'],
                'tgl_pinjam' => $tgl_pinjam,
                'tgl_rencana_kembali' => $tgl_rencana_kembali,
                'status' => 'Diajukan'
            ],
            'debug_info' => $debug_mode ? [
                'insert_id' => $peminjaman_id,
                'timestamp' => date('Y-m-d H:i:s')
            ] : null
        ];
        
        echo json_encode($response);
        
    } else {
        throw new Exception('Insert execution failed: ' . $insert_stmt->error);
    }
    
} catch (Exception $e) {
    if ($debug_mode) {
        error_log("EXCEPTION: " . $e->getMessage());
        error_log("FILE: " . $e->getFile());
        error_log("LINE: " . $e->getLine());
    }
    
    $response = [
        'success' => false,
        'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
        'debug_info' => $debug_mode ? [
            'error_type' => get_class($e),
            'error_file' => $e->getFile(),
            'error_line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ] : null
    ];
    
    echo json_encode($response);
    
} finally {
    // Tutup koneksi
    if (isset($koneksi)) {
        $koneksi->close();
    }
    
    if ($debug_mode) {
        error_log("=== PEMINJAMAN DEBUG END ===");
    }
}

?>