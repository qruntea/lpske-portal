<?php
// File: api/proses_presensi.php (VERSI FINAL BERSIH)

// Menampilkan error jika terjadi (baik untuk development)
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
require 'koneksi.php'; // Memuat koneksi dari file Anda

// Langsung hentikan jika koneksi gagal
if ($koneksi->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Internal Server Error: Gagal terhubung ke database.']);
    exit;
}

date_default_timezone_set('Asia/Jakarta');

$nama = $_POST['nama_asisten'] ?? '';
$nim = $_POST['nim_asisten'] ?? '';
$action = $_POST['action'] ?? '';

if (empty($nama) || empty($nim) || empty($action)) {
    echo json_encode(['success' => false, 'message' => 'Nama, NIM, dan Aksi wajib diisi.']);
    exit;
}

$today = date('Y-m-d');
$now_datetime = new DateTime();
$now_time_str = $now_datetime->format('H:i:s');

try {
    if ($action === 'clock_in') {
        // Cek apakah user sudah pernah clock in hari ini
        $stmt_check = $koneksi->prepare("SELECT id FROM presensi_piket WHERE nim_asisten = ? AND tanggal = ?");
        $stmt_check->bind_param("ss", $nim, $today);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Anda sudah Clock In hari ini. Tidak bisa Clock In lagi.']);
        } else {
            // Lakukan insert data baru
            $stmt_insert = $koneksi->prepare("INSERT INTO presensi_piket (nama_asisten, nim_asisten, tanggal, waktu_masuk, status) VALUES (?, ?, ?, ?, 'sedang piket')");
            $stmt_insert->bind_param("ssss", $nama, $nim, $today, $now_time_str);
            $stmt_insert->execute();
            echo json_encode(['success' => true, 'message' => "Clock In berhasil pada jam {$now_time_str}."]);
        }
        $stmt_check->close();

    } elseif ($action === 'clock_out') {
        // Cari data presensi yang belum clock out
        $stmt_check = $koneksi->prepare("SELECT id, waktu_masuk, waktu_keluar FROM presensi_piket WHERE nim_asisten = ? AND tanggal = ? ORDER BY id DESC LIMIT 1");
        $stmt_check->bind_param("ss", $nim, $today);
        $stmt_check->execute();
        $result = $stmt_check->get_result();
        $presensi = $result->fetch_assoc();

        if (!$presensi) {
            echo json_encode(['success' => false, 'message' => 'Gagal Clock Out: Anda belum melakukan Clock In hari ini.']);
        } elseif ($presensi['waktu_keluar'] !== null) {
            echo json_encode(['success' => false, 'message' => 'Gagal Clock Out: Anda sudah melakukan Clock Out sebelumnya.']);
        } else {
            // Lakukan update data
            $waktu_masuk = new DateTime($presensi['waktu_masuk']);
            $durasi_diff = $waktu_masuk->diff($now_datetime);
            $durasi_str = $durasi_diff->format('%h jam %i menit %s detik');

            $stmt_update = $koneksi->prepare("UPDATE presensi_piket SET waktu_keluar = ?, status = 'selesai', durasi = ? WHERE id = ?");
            $stmt_update->bind_param("ssi", $now_time_str, $durasi_str, $presensi['id']);
            $stmt_update->execute();
            echo json_encode(['success' => true, 'message' => "Clock Out berhasil pada jam {$now_time_str}."]);
        }
        $stmt_check->close();
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan pada Server: ' . $e->getMessage()]);
}

$koneksi->close();
?>