<?php
// includes/header.php
session_start();

// Set timezone Indonesia
date_default_timezone_set('Asia/Jakarta');

// Base URL configuration
$base_url = 'http://localhost/LSTARS1/'; // Sesuaikan dengan path project Anda

// Site configuration
$site_name = 'LSTARS Portal';
$site_description = 'Laboratorium Sistem Teknik & Analisis Riset Simulasi - Teknik Industri UNS';

// Function untuk menampilkan pesan flash
function showFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $type = $_SESSION['flash_type'] ?? 'info';
        $message = $_SESSION['flash_message'];
        
        $color_class = [
            'success' => 'bg-green-100 border-green-400 text-green-700',
            'error' => 'bg-red-100 border-red-400 text-red-700',
            'warning' => 'bg-yellow-100 border-yellow-400 text-yellow-700',
            'info' => 'bg-blue-100 border-blue-400 text-blue-700'
        ];
        
        echo '<div class="' . ($color_class[$type] ?? $color_class['info']) . ' px-4 py-3 rounded mb-4">';
        echo htmlspecialchars($message);
        echo '</div>';
        
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
    }
}

// Function untuk set flash message
function setFlashMessage($message, $type = 'info') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

// Function untuk format hari dalam bahasa Indonesia
function formatHari($hari) {
    $hari_indo = [
        'senin' => 'Senin',
        'selasa' => 'Selasa', 
        'rabu' => 'Rabu',
        'kamis' => 'Kamis',
        'jumat' => 'Jumat',
        'sabtu' => 'Sabtu',
        'minggu' => 'Minggu'
    ];
    
    return $hari_indo[strtolower($hari)] ?? ucfirst($hari);
}

// Function untuk format waktu
function formatWaktu($waktu) {
    return date('H:i', strtotime($waktu));
}

// Function untuk format tanggal Indonesia
function formatTanggal($tanggal) {
    $bulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    
    $timestamp = strtotime($tanggal);
    $hari = date('d', $timestamp);
    $bulan_nama = $bulan[(int)date('m', $timestamp)];
    $tahun = date('Y', $timestamp);
    
    return $hari . ' ' . $bulan_nama . ' ' . $tahun;
}

// Function untuk sanitize input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function untuk generate random string
function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}
?>