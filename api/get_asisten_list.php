<?php
// ===================================================================
// Tugas: Mengambil daftar ID, nama, dan NIM dari asisten yang
//        statusnya 'aktif' untuk ditampilkan di form.
// ===================================================================

// Mulai sesi untuk validasi login
session_start();

// Panggil file koneksi database Anda
require 'koneksi.php'; 

// Atur header agar outputnya berupa JSON, penting untuk JavaScript
header('Content-Type: application/json');

// Keamanan: Cek apakah ada sesi login yang aktif. Jika tidak, hentikan.
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Kode error "Unauthorized"
    echo json_encode([]); // Kirim array kosong sebagai respons
    exit;
}

// Query SQL untuk mengambil data asisten yang aktif.
// Asumsi:
// - Nama tabel Anda adalah 'users'.
// - Ada kolom 'role' untuk membedakan 'asisten', 'admin', dll.
// - Ada kolom 'status' yang berisi 'aktif' atau 'tidak aktif'.
// - Kolom yang menyimpan nama lengkap adalah 'nama_lengkap'.
// --> Sesuaikan nama tabel dan kolom jika berbeda dengan database Anda.
$sql = "SELECT id, nama_lengkap, nim FROM users WHERE role = 'asisten' AND status = 'aktif' ORDER BY nama_lengkap ASC";

$result = $koneksi->query($sql);

$asisten_list = []; // Siapkan array kosong untuk menampung data

if ($result && $result->num_rows > 0) {
    // Looping untuk mengambil setiap baris data
    while($row = $result->fetch_assoc()) {
        $asisten_list[] = $row; // Masukkan data ke dalam array
    }
}

// Ubah array PHP menjadi format JSON dan kirimkan sebagai output
echo json_encode($asisten_list);

// Tutup koneksi database
$koneksi->close();
?>