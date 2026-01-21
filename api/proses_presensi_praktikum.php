<?php
// File: api/proses_presensi_praktikum.php

session_start();
header('Content-Type: application/json');
require 'koneksi.php'; // Pastikan timezone sudah di-set di sini

// Ambil input dari form
$nama_mahasiswa = trim($_POST['nama_mahasiswa'] ?? '');
$nim_mahasiswa = trim($_POST['nim_mahasiswa'] ?? '');
$mata_praktikum = trim($_POST['mata_praktikum'] ?? '');
$action = trim($_POST['action'] ?? ''); // 'clock_in' or 'clock_out'

$tanggal_sekarang = date('Y-m-d');
$waktu_sekarang = date('H:i:s');

// Validasi input wajib
if (empty($nama_mahasiswa) || empty($nim_mahasiswa) || empty($mata_praktikum)) {
    echo json_encode(['success' => false, 'message' => 'Nama, NIM, dan Mata Praktikum wajib diisi.']);
    exit;
}
if (empty($action) || !in_array($action, ['clock_in', 'clock_out'])) {
    echo json_encode(['success' => false, 'message' => 'Aksi tidak valid.']);
    exit;
}

if ($action === 'clock_in') {
    // Cek apakah mahasiswa ini sudah clock in untuk matkul yang sama hari ini
    $stmt_check = $koneksi->prepare("SELECT id FROM presensi_praktikum WHERE nim_mahasiswa = ? AND mata_praktikum = ? AND tanggal = ?");
    $stmt_check->bind_param("sss", $nim_mahasiswa, $mata_praktikum, $tanggal_sekarang);
    $stmt_check->execute();
    
    if ($stmt_check->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => "{$nama_mahasiswa} ({$nim_mahasiswa}) sudah melakukan presensi untuk praktikum {$mata_praktikum} hari ini."]);
    } else {
        $stmt_insert = $koneksi->prepare("INSERT INTO presensi_praktikum (nama_mahasiswa, nim_mahasiswa, mata_praktikum, tanggal, waktu_masuk, status) VALUES (?, ?, ?, ?, ?, 'sedang praktikum')");
        $stmt_insert->bind_param("sssss", $nama_mahasiswa, $nim_mahasiswa, $mata_praktikum, $tanggal_sekarang, $waktu_sekarang);
        
        if ($stmt_insert->execute()) {
            echo json_encode(['success' => true, 'message' => "{$nama_mahasiswa}, berhasil Clock In untuk praktikum {$mata_praktikum}."]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal melakukan Clock In.']);
        }
        $stmt_insert->close();
    }
    $stmt_check->close();

} elseif ($action === 'clock_out') {
    // Cari sesi praktikum yang aktif (belum clock out) untuk mahasiswa & matkul ini
    $stmt_check = $koneksi->prepare("SELECT id, waktu_masuk FROM presensi_praktikum WHERE nim_mahasiswa = ? AND mata_praktikum = ? AND tanggal = ? AND status = 'sedang praktikum'");
    $stmt_check->bind_param("sss", $nim_mahasiswa, $mata_praktikum, $tanggal_sekarang);
    $stmt_check->execute();
    $presensi_aktif = $stmt_check->get_result()->fetch_assoc();
    
    if ($presensi_aktif) {
        $presensi_id = $presensi_aktif['id'];
        $waktu_masuk_db = $presensi_aktif['waktu_masuk'];
        
        $start_time = new DateTime($waktu_masuk_db);
        $end_time = new DateTime($waktu_sekarang);
        $interval = $start_time->diff($end_time);
        $durasi = $interval->format('%h jam %i menit %s detik');

        $stmt_update = $koneksi->prepare("UPDATE presensi_praktikum SET waktu_keluar = ?, durasi = ?, status = 'selesai' WHERE id = ?");
        $stmt_update->bind_param("ssi", $waktu_sekarang, $durasi, $presensi_id);
        
        if ($stmt_update->execute()) {
            echo json_encode(['success' => true, 'message' => "{$nama_mahasiswa}, berhasil Clock Out dari praktikum {$mata_praktikum}."]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal melakukan Clock Out.']);
        }
        $stmt_update->close();
    } else {
        echo json_encode(['success' => false, 'message' => "Tidak bisa Clock Out. {$nama_mahasiswa} belum Clock In untuk praktikum {$mata_praktikum} hari ini."]);
    }
    $stmt_check->close();
}

$koneksi->close();
?>