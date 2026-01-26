<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Ambil data dari session dengan pengecekan
$nama = isset($_SESSION['nama_lengkap']) ? htmlspecialchars($_SESSION['nama_lengkap']) : 'Nama tidak tersedia';
$email = isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : 'Email tidak tersedia';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan SMAKADUTA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Link ke file CSS eksternal -->
    <link rel="stylesheet" href="style.css">
</head>

<body class="bg-gradient-to-b from-emerald-50 to-white">
    <!-- Modern Navbar -->
    <nav class="navbar-royal shadow-xl sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo & Brand -->
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0 flex items-center space-x-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-white to-gray-100 rounded-xl flex items-center justify-center shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M12 6V3m0 18v-3M5.636 5.636l-1.414-1.414m15.152 15.152l-1.414-1.414M18.364 5.636l-1.414 1.414m-11.314 11.314l-1.414 1.414" />
                            </svg>
                        </div>
                        <div class="text-white">
                            <h1 class="text-xl font-bold">Perpustakaan SMAKADUTA</h1>
                            <p class="text-xs text-emerald-200">Portal Koleksi Buku</p>
                        </div>
                    </div>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden lg:flex items-center space-x-8" id="main-nav">
                    <!-- Dashboard -->
                    <a href="#" data-page="dashboard" class="nav-link active nav-item text-white hover:text-emerald-200 px-3 py-2 rounded-lg font-medium transition-all duration-300">
                        <i class="fas fa-home mr-2"></i>
                        <span>Dashboard</span>
                    </a>

                    <!-- Database Dropdown -->
                    <div class="relative nav-item">
                        <button class="text-white hover:text-emerald-200 px-3 py-2 rounded-lg font-medium transition-all duration-300 flex items-center" onclick="toggleDropdown('database-dropdown')">
                            <i class="fas fa-database mr-2"></i>
                            <span>Database</span>
                            <i class="fas fa-chevron-down ml-2 text-xs transition-transform duration-300" id="database-chevron"></i>
                        </button>
                        <div id="database-dropdown" class="nav-dropdown absolute top-full left-0 mt-2 w-56 bg-white rounded-xl shadow-2xl border border-gray-100 py-2">
                            <a href="#" data-page="inventory" class="nav-link dropdown-item flex items-center px-4 py-3 text-gray-700 hover:text-emerald-600">
                                <i class="fas fa-book w-5 mr-3 text-emerald-500"></i>
                                <span>Inventory Buku</span>
                            </a>
                        </div>
                    </div>

                    <!-- Layanan Dropdown -->
                    <div class="relative nav-item">
                        <button class="text-white hover:text-emerald-200 px-3 py-2 rounded-lg font-medium transition-all duration-300 flex items-center" onclick="toggleDropdown('layanan-dropdown')">
                            <i class="fas fa-cogs mr-2"></i>
                            <span>Layanan</span>
                            <i class="fas fa-chevron-down ml-2 text-xs transition-transform duration-300" id="layanan-chevron"></i>
                        </button>
                        <div id="layanan-dropdown" class="nav-dropdown absolute top-full left-0 mt-2 w-64 bg-white rounded-xl shadow-2xl border border-gray-100 py-2">
                            <a href="#" data-page="peminjaman" class="nav-link dropdown-item flex items-center px-4 py-3 text-gray-700 hover:text-emerald-600">
                                <i class="fas fa-exchange-alt w-5 mr-3 text-emerald-500"></i>
                                <span>Peminjaman & Pengembalian</span>
                            </a>
                            <a href="#" data-page="izin" class="nav-link dropdown-item flex items-center px-4 py-3 text-gray-700 hover:text-emerald-600">
                                <i class="fas fa-file-signature w-5 mr-3 text-emerald-500"></i>
                                <span>Izin Peminjaman</span>
                            </a>
                            <a href="#" data-page="presensi" class="nav-link dropdown-item flex items-center px-4 py-3 text-gray-700 hover:text-emerald-600">
                                <i class="fas fa-clock w-5 mr-3 text-emerald-500"></i>
                                <span>Presensi Piket</span>
                            </a>
                            <a href="#" data-page="presensi_praktikum" class="nav-link dropdown-item flex items-center px-4 py-3 text-gray-700 hover:text-emerald-600">
                                <i class="fas fa-user-check w-5 mr-3 text-teal-500"></i>
                                <span>Presensi Praktikum</span>
                            </a>
                            <a href="#" data-page="dokumentasi" class="nav-link dropdown-item flex items-center px-4 py-3 text-gray-700 hover:text-emerald-600">
                                <i class="fas fa-camera w-5 mr-3 text-emerald-500"></i>
                                <span>Dokumentasi Kegiatan</span>
                            </a>
                            <a href="#" data-page="LogbookKegiatanLab" class="nav-link dropdown-item flex items-center px-4 py-3 text-gray-700 hover:text-emerald-600">
                                <i class="fas fa-book w-5 mr-3 text-emerald-500"></i>
                                <span>LogBook Kegiatan</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Right Side - Profile & Notifications -->
                <div class="flex items-center space-x-4">
                    <!-- Notification Bell -->
                    <button class="relative text-white hover:text-emerald-200 transition-colors duration-300">
                        <i class="fas fa-bell text-lg"></i>
                        <span class="notification-badge absolute -top-1 -right-1 w-3 h-3 bg-emerald-500 rounded-full"></span>
                    </button>

                    <!-- Profile Info & Logout -->
                    <div class="flex items-center space-x-3">
                        <div class="hidden md:block text-right">
                            <i class="fas fa-user-circle text-lg text-white mr-2"></i>
                            <span id="welcome-message" class="text-white font-medium">Memuat data...</span>
                        </div>
                        <button onclick="logout()" class="bg-gradient-to-r from-emerald-500 to-emerald-500 hover:from-emerald-600 hover:to-emerald-600 text-white px-4 py-2 rounded-lg font-medium transition-all duration-300 shadow-lg">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </button>
                    </div>

                    <!-- Mobile Menu Button -->
                    <button id="menu-button" class="lg:hidden text-white focus:outline-none p-2">
                        <i class="fas fa-bars text-xl" id="mobile-menu-icon"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Navigation Menu -->
            <div id="mobile-menu" class="mobile-menu lg:hidden bg-white bg-opacity-10 backdrop-blur-sm rounded-xl mt-2 mb-4">
                <div class="px-4 py-3 space-y-3">
                    <!-- Mobile Dashboard -->
                    <a href="#" data-page="dashboard" class="nav-link block px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-lg transition-all duration-300">
                        <i class="fas fa-home mr-3"></i>Dashboard
                    </a>
                    
                    <!-- Mobile Database Section -->
                    <div class="space-y-2">
                        <p class="px-4 text-emerald-200 text-xs font-semibold uppercase tracking-wider">Database</p>
                        <a href="#" data-page="inventory" class="nav-link block px-6 py-2 text-white hover:bg-white hover:bg-opacity-20 rounded-lg transition-all duration-300">
                            <i class="fas fa-book mr-3"></i>Inventory Buku
                        </a>
                    </div>
                    
                    <!-- Mobile Layanan Section -->
                    <div class="space-y-2">
                        <p class="px-4 text-emerald-200 text-xs font-semibold uppercase tracking-wider">Layanan</p>
                        <a href="#" data-page="peminjaman" class="nav-link block px-6 py-2 text-white hover:bg-white hover:bg-opacity-20 rounded-lg transition-all duration-300">
                            <i class="fas fa-exchange-alt mr-3"></i>Peminjaman & Pengembalian
                        </a>
                        <a href="#" data-page="izin" class="nav-link block px-6 py-2 text-white hover:bg-white hover:bg-opacity-20 rounded-lg transition-all duration-300">
                            <i class="fas fa-file-signature mr-3"></i>Izin Peminjaman
                        </a>
                        <a href="#" data-page="presensi" class="nav-link block px-6 py-2 text-white hover:bg-white hover:bg-opacity-20 rounded-lg transition-all duration-300">
                            <i class="fas fa-clock mr-3"></i>Presensi Piket
                        </a>
                        <a href="#" data-page="presensi_praktikum" class="nav-link block px-6 py-2 text-white hover:bg-white hover:bg-opacity-20 rounded-lg transition-all duration-300">
                            <i class="fas fa-user-check mr-3"></i>Presensi Praktikum
                        </a>
                        <a href="#" data-page="dokumentasi" class="nav-link block px-6 py-2 text-white hover:bg-white hover:bg-opacity-20 rounded-lg transition-all duration-300">
                            <i class="fas fa-camera mr-3"></i>Dokumentasi Kegiatan
                        </a>
                        <a href="#" data-page="LogbookKegiatanLab" class="nav-link block px-6 py-2 text-white hover:bg-white hover:bg-opacity-20 rounded-lg transition-all duration-300">
                            <i class="fas fa-book mr-3"></i>LogBook Kegiatan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Title Bar -->
    <div class="bg-white border-b-2 border-emerald-200 px-6 py-4 shadow-sm">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-emerald-900" id="page-title">Dashboard</h1>
                <p class="text-sm text-emerald-600 mt-1">Kelola koleksi buku perpustakaan dengan mudah</p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-sm text-emerald-700 bg-emerald-50 px-4 py-2 rounded-lg">
                    <i class="fas fa-clock mr-2 text-emerald-600"></i>
                    <span id="current-navbar-time">Loading...</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <main class="max-w-7xl mx-auto px-6 py-8" id="content-area">
        
        <!-- Dashboard Page -->
        <div id="page-dashboard" class="page">
            <!-- Dashboard Header -->
            <div class="mb-8 bg-gradient-to-r from-emerald-50 to-emerald-100 rounded-xl p-8 border-l-4 border-emerald-600">
                <h2 class="text-4xl font-bold text-emerald-900">Selamat Datang di Perpustakaan SMAKADUTA</h2>
                <p class="mt-3 text-lg text-emerald-700">Silakan gunakan menu di atas untuk mengakses fitur yang tersedia.</p>
            </div>

            <!-- Real-time Clock Card -->
            <div class="bg-white p-4 rounded-lg shadow-lg border-l-4 border-emerald-500 mb-8">
                <div class="text-center">
                    <div class="text-2xl font-bold text-emerald-600" id="currentTime">00:00:00</div>
                    <div class="text-sm text-gray-600" id="currentDate">Loading...</div>
                    <div class="text-xs text-gray-500 mt-1" id="currentDay">Loading...</div>
                </div>
            </div>

            <!-- User Profile Card -->
            <div class="bg-gradient-to-r from-white to-emerald-50 p-6 rounded-lg shadow-md mb-8 border-l-4 border-emerald-500">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-full flex items-center justify-center shadow-lg">
                        <i class="fas fa-user text-white text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-emerald-900" id="userName">Loading...</h3>
                        <p class="text-emerald-700 font-medium" id="userRole">Loading...</p>
                        <p class="text-sm text-emerald-600" id="userInfo">Loading...</p>
                    </div>
                </div>
            </div>
            
            <!-- STATISTICS CARDS FOR USER -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-700">Perizinan Saya</h3>
                            <p class="text-3xl font-bold text-emerald-600" id="myPermissions">-</p>
                            <p class="text-sm text-gray-500">total pengajuan</p>
                        </div>
                        <div class="ml-4">
                            <i class="fas fa-file-alt text-emerald-500 text-2xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-700">Status Disetujui</h3>
                            <p class="text-3xl font-bold text-green-600" id="approvedCount">-</p>
                            <p class="text-sm text-gray-500">telah disetujui</p>
                        </div>
                        <div class="ml-4">
                            <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-700">Menunggu Persetujuan</h3>
                            <p class="text-3xl font-bold text-yellow-600" id="pendingCount">-</p>
                            <p class="text-sm text-gray-500">dalam proses</p>
                        </div>
                        <div class="ml-4">
                            <i class="fas fa-hourglass-half text-yellow-500 text-2xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-700">Equipment Tersedia</h3>
                            <p class="text-3xl font-bold text-emerald-600" id="equipmentCount">-</p>
                            <p class="text-sm text-gray-500">dapat dipinjam</p>
                        </div>
                        <div class="ml-4">
                            <i class="fas fa-boxes text-emerald-500 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- RECENT ACTIVITIES -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Aktivitas Terbaru</h3>
                <div id="recentActivities">Loading...</div>
            </div>
        </div>
        
        <!-- Dosen Page - HIDDEN -->
        <div id="page-dosen" class="page hidden" style="display:none;">
    <div id="dosen-list-view">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Daftar Staf</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-200">
                        <tr>
                            <!-- PERBAIKAN: Header tabel yang benar sesuai permintaan -->
                            <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Nama Lengkap</th>
                            <th class="py-3 px-4 uppercase font-semibold text-sm text-left">NIP</th>
                            <th class="py-3 px-4 uppercase font-semibold text-sm text-left">NIDN</th>
                            <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Homebase Prodi</th>
                            <th class="py-3 px-4 uppercase font-semibold text-sm text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody id="tabel-dosen-body" class="text-gray-700"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

        <!-- Asisten Page - HIDDEN -->
        <div id="page-asisten" class="page hidden" style="display:none;">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Daftar Asisten</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Nama</th>
                                <th class="py-3 px-4 uppercase font-semibold text-sm text-left">NIM</th>
                                <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Jabatan</th>
                                <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody id="tabel-asisten-body" class="text-gray-700"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Inventory Page -->
        <div id="page-inventory" class="page hidden">
            <div class="bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full bg-white">
                        <thead>
                            <tr class="bg-gradient-to-r from-emerald-600 to-emerald-700 text-white">
                                <th class="py-4 px-6 text-left font-semibold text-base">Nama Buku</th>
                                <th class="py-4 px-6 text-left font-semibold text-base">Kode Buku</th>
                                <th class="py-4 px-6 text-left font-semibold text-base">Kategori</th>
                                <th class="py-4 px-6 text-center font-semibold text-base">Jumlah</th>
                                <th class="py-4 px-6 text-center font-semibold text-base">Status</th>
                            </tr>
                        </thead>
                        <tbody id="tabel-inventory-body" class="text-gray-700 divide-y divide-gray-200 text-base"></tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Peminjaman Page - SIMPLIFIED WITHOUT KEPERLUAN -->
<div id="page-peminjaman" class="page hidden">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">
                <i class="fas fa-clipboard-list mr-2 text-emerald-500"></i>Form Peminjaman
            </h2>
            
            <form id="form-peminjaman" class="space-y-4">
                
                <!-- BAGIAN DATA PEMINJAM -->
                <div class="p-4 bg-emerald-50 rounded-lg border border-emerald-200">
                    <h3 class="text-sm font-semibold text-emerald-800 mb-3">
                        <i class="fas fa-user mr-1"></i>Data Peminjam
                    </h3>
                    
                    <div class="space-y-3">
                        <div>
                            <label for="nama_peminjam" class="block text-sm font-medium text-gray-700 mb-1">
                                Nama Lengkap <span class="text-emerald-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="nama_peminjam" 
                                name="nama_peminjam" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-sm"
                                placeholder="Masukkan nama lengkap Anda"
                                required
                                autocomplete="off"
                            >
                            <p class="text-xs text-gray-500 mt-1">Contoh: Ahmad Pratama Wijaya</p>
                        </div>
                        
                        <div>
                            <label for="nim_peminjam" class="block text-sm font-medium text-gray-700 mb-1">
                                NIM/NPM <span class="text-emerald-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="nim_peminjam" 
                                name="nim_peminjam" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-sm"
                                placeholder="Masukkan NIM/NPM Anda"
                                pattern="[A-Za-z0-9]+"
                                title="NIM hanya boleh berisi huruf dan angka"
                                required
                                autocomplete="off"
                            >
                            <p class="text-xs text-gray-500 mt-1">Contoh: I0123456 atau 202012345</p>
                        </div>
                    </div>
                </div>
                
                <!-- BAGIAN ALAT DAN TANGGAL -->
                <div class="space-y-4">
                    <div>
                        <label for="select-alat" class="block text-sm font-medium text-gray-700 mb-1">
                            Alat/Ruang yang Dipinjam <span class="text-emerald-500">*</span>
                        </label>
                        <select 
                            id="select-alat" 
                            name="alat_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-sm"
                            required
                        >
                            <option value="">-- Pilih Alat/Ruang --</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Pilih alat atau ruang yang ingin dipinjam</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label for="tgl_pinjam" class="block text-sm font-medium text-gray-700 mb-1">
                                Tanggal Peminjaman <span class="text-emerald-500">*</span>
                            </label>
                            <input 
                                type="date" 
                                id="tgl_pinjam" 
                                name="tgl_pinjam" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-sm"
                                required
                            >
                        </div>
                        
                        <div>
                            <label for="tgl_rencana_kembali" class="block text-sm font-medium text-gray-700 mb-1">
                                Rencana Tanggal Pengembalian <span class="text-emerald-500">*</span>
                            </label>
                            <input 
                                type="date" 
                                id="tgl_rencana_kembali" 
                                name="tgl_rencana_kembali" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-sm"
                                required
                            >
                        </div>
                    </div>
                </div>
                
                <!-- TOMBOL SUBMIT -->
                <button 
                    type="submit" 
                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-3 px-4 rounded-md transition-colors duration-200 flex items-center justify-center"
                    id="btn-submit-peminjaman"
                >
                    <i class="fas fa-paper-plane mr-2"></i>
                    <span>Ajukan Peminjaman</span>
                </button>
                
                <!-- DEBUG INFO (SEMENTARA) -->
                <div id="debug-info" class="p-3 bg-yellow-50 rounded-md border border-yellow-200 text-xs">
                    <p class="text-yellow-800 font-semibold mb-1">Debug Info:</p>
                    <div id="debug-content" class="text-yellow-700"></div>
                </div>
            </form>
        </div>
        
        <!-- BAGIAN RIWAYAT PEMINJAMAN -->
        <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-700">
                    <i class="fas fa-history mr-2 text-green-500"></i>Riwayat Peminjaman
                </h2>
                <button 
                    onclick="tampilkanRiwayatPeminjaman()" 
                    class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm transition-colors"
                    title="Refresh riwayat"
                >
                    <i class="fas fa-sync-alt mr-1"></i>Refresh
                </button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b">
                                <i class="fas fa-user mr-1"></i>Peminjam
                            </th>
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b">
                                <i class="fas fa-id-card mr-1"></i>NIM
                            </th>
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b">
                                <i class="fas fa-tools mr-1"></i>Item
                            </th>
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b">
                                <i class="fas fa-calendar mr-1"></i>Tgl Pinjam
                            </th>
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b">
                                <i class="fas fa-calendar-check mr-1"></i>Tgl Kembali
                            </th>
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b">
                                <i class="fas fa-info-circle mr-1"></i>Status
                            </th>
                        </tr>
                    </thead>
                    <tbody id="tabel-peminjaman-body" class="text-gray-700 divide-y divide-gray-200">
                        <!-- Data akan diisi oleh JavaScript -->
                        <tr>
                            <td colspan="7" class="text-center py-8 text-gray-500">
                                <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                                <p>Memuat riwayat peminjaman...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript Debug Enhanced -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    console.log('🚀 Form Peminjaman Simplified Loaded');
    
    // Debug display element
    const debugContent = document.getElementById('debug-content');
    
    function updateDebugInfo(message) {
        if (debugContent) {
            debugContent.innerHTML += `<div>${new Date().toLocaleTimeString()}: ${message}</div>`;
        }
        console.log('🔍 DEBUG:', message);
    }
    
    updateDebugInfo('Form initialized');
    
    // Auto-fill dari localStorage
    const savedNama = localStorage.getItem('perpustakaan_user_nama');
    const savedNim = localStorage.getItem('perpustakaan_user_nim');
    
    if (savedNama) {
        document.getElementById('nama_peminjam').value = savedNama;
        updateDebugInfo(`Auto-filled nama: ${savedNama}`);
    }
    
    if (savedNim) {
        document.getElementById('nim_peminjam').value = savedNim;
        updateDebugInfo(`Auto-filled NIM: ${savedNim}`);
    }
    
    // Set minimum dates
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('tgl_pinjam').min = today;
    document.getElementById('tgl_rencana_kembali').min = today;
    
    // Form submission handler
    const form = document.getElementById('form-peminjaman');
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        updateDebugInfo('Form submission started');
        
        const formData = new FormData(form);
        const submitBtn = document.getElementById('btn-submit-peminjaman');
        const originalContent = submitBtn.innerHTML;
        
        // Log form data
        updateDebugInfo('Form data collected:');
        for (let [key, value] of formData.entries()) {
            updateDebugInfo(`  ${key}: "${value}"`);
        }
        
        // Client validation
        const nama = formData.get('nama_peminjam')?.trim();
        const nim = formData.get('nim_peminjam')?.trim();
        const alat = formData.get('alat_id');
        const tglPinjam = formData.get('tgl_pinjam');
        const tglKembali = formData.get('tgl_rencana_kembali');
        
        if (!nama || !nim || !alat || !tglPinjam || !tglKembali) {
            updateDebugInfo('❌ Validation failed - missing fields');
            alert('Semua field wajib harus diisi!');
            return;
        }
        
        updateDebugInfo('✅ Client validation passed');
        
        // Update button
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengirim...';
        
        try {
            updateDebugInfo('📤 Sending to API...');
            
            const response = await fetch('api/proses_peminjaman.php', {
                method: 'POST',
                body: formData
            });
            
            updateDebugInfo(`📥 Response status: ${response.status}`);
            
            const text = await response.text();
            updateDebugInfo(`📄 Raw response: ${text.substring(0, 100)}...`);
            
            let result;
            try {
                result = JSON.parse(text);
                updateDebugInfo(`✅ JSON parsed successfully`);
            } catch (parseError) {
                updateDebugInfo(`❌ JSON parse error: ${parseError.message}`);
                throw new Error('Server response bukan JSON valid: ' + text.substring(0, 50));
            }
            
            updateDebugInfo(`📊 Result: ${result.success ? 'SUCCESS' : 'FAILED'}`);
            updateDebugInfo(`💬 Message: ${result.message}`);
            
            if (result.success) {
                alert(result.message || 'Pengajuan berhasil!');
                
                // Save to localStorage
                localStorage.setItem('perpustakaan_user_nama', nama);
                localStorage.setItem('perpustakaan_user_nim', nim);
                
                // Reset form
                form.reset();
                updateDebugInfo('🔄 Form reset');
                
                // Refresh riwayat
                updateDebugInfo('🔄 Refreshing riwayat...');
                if (typeof tampilkanRiwayatPeminjaman === 'function') {
                    await tampilkanRiwayatPeminjaman();
                    updateDebugInfo('✅ Riwayat refreshed');
                } else {
                    updateDebugInfo('❌ tampilkanRiwayatPeminjaman function not found');
                }
                
                // Refresh options
                if (typeof muatOpsiPeminjaman === 'function') {
                    await muatOpsiPeminjaman();
                    updateDebugInfo('✅ Options refreshed');
                }
                
            } else {
                updateDebugInfo(`❌ API Error: ${result.message}`);
                alert('Error: ' + result.message);
            }
            
        } catch (error) {
            updateDebugInfo(`❌ Network error: ${error.message}`);
            alert('Terjadi kesalahan: ' + error.message);
        } finally {
            // Reset button
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalContent;
            updateDebugInfo('🔄 Button reset');
        }
    });
    
    // Load options saat page load
    if (typeof muatOpsiPeminjaman === 'function') {
        muatOpsiPeminjaman().then(() => {
            updateDebugInfo('✅ Options loaded on page load');
        }).catch(error => {
            updateDebugInfo(`❌ Failed to load options: ${error.message}`);
        });
    }
    
    // Load riwayat saat page load
    if (typeof tampilkanRiwayatPeminjaman === 'function') {
        tampilkanRiwayatPeminjaman().then(() => {
            updateDebugInfo('✅ Riwayat loaded on page load');
        }).catch(error => {
            updateDebugInfo(`❌ Failed to load riwayat: ${error.message}`);
        });
    }
    
    updateDebugInfo('🎉 Form setup completed');
});
</script>

        <!-- Izin Page -->
        <div id="page-izin" class="page hidden">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Pengajuan Izin Peminjaman</h2>
                <form id="form-izin" class="space-y-4 mb-8">
                    <div>
                        <label for="nama-mahasiswa" class="block text-sm font-medium text-gray-700">Nama Mahasiswa / Peneliti</label>
                        <input type="text" id="nama-mahasiswa" name="nama_mahasiswa" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="Masukkan nama lengkap Anda" required>
                    </div>
                    <div>
                        <label for="nim-mahasiswa" class="block text-sm font-medium text-gray-700">NIM</label>
                        <input type="text" id="nim-mahasiswa" name="nim_mahasiswa" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="Masukkan NIM Anda" required>
                    </div>
                    <div>
                        <label for="judul_penelitian" class="block text-sm font-medium text-gray-700">Judul Kegiatan</label>
                        <input type="text" id="judul_penelitian" name="judul_penelitian" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" required>
                    </div>
                    <div>
                        <label for="select-petugas" class="block text-sm font-medium text-gray-700">Petugas Perpustakaan</label>
                        <select id="select-petugas" name="petugas_perpustakaan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" required></select>
                    </div>
                    <div>
                        <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi Singkat Kegiatan</label>
                        <textarea id="deskripsi" name="deskripsi" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" required></textarea>
                    </div>
                    <button type="submit" class="w-full md:w-auto bg-emerald-600 text-white py-2 px-4 rounded-md hover:bg-emerald-700">Ajukan Izin</button>
                </form>
                
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Riwayat Pengajuan Izin</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Mahasiswa</th>
                                <th class="py-3 px-4 uppercase font-semibold text-sm text-left">NIM</th>
                                <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Judul</th>
                                <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Dosen Pembimbing</th>
                                <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Tgl Pengajuan</th>
                                <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody id="tabel-riwayat-izin-body" class="text-gray-700"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Presensi Page -->
        <div id="page-presensi" class="page hidden">
            <div class="bg-white p-6 rounded-lg shadow-md text-center max-w-md mx-auto">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Presensi Piket Asisten</h2>
                <p class="text-gray-500 mb-6" id="current-time-date-presensi">Memuat tanggal dan waktu...</p>
                
                <div class="space-y-4 mb-6 text-left">
                    <div>
                        <label for="presensi-piket-nama" class="block text-sm font-medium text-gray-700">Nama Asisten</label>
                        <input type="text" id="presensi-piket-nama" name="nama_asisten" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="Masukkan nama lengkap Anda" required autocomplete="off">
                    </div>
                    <div>
                        <label for="presensi-piket-nim" class="block text-sm font-medium text-gray-700">NIM</label>
                        <input type="text" id="presensi-piket-nim" name="nim_asisten" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="Masukkan NIM Anda" required autocomplete="off">
                    </div>
                </div>
                
                <div class="flex items-center justify-center gap-4">
                    <button id="btn-clock-in" class="flex-1 bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded-lg text-base transition-transform transform hover:scale-105">
                        <i class="fas fa-sign-in-alt mr-2"></i>Clock In
                    </button>
                    <button id="btn-clock-out" class="flex-1 bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3 px-4 rounded-lg text-base transition-transform transform hover:scale-105">
                        <i class="fas fa-sign-out-alt mr-2"></i>Clock Out
                    </button>
                </div>
                
                <p class="text-sm text-gray-600 mt-4">Masukkan Nama dan NIM Anda, lalu pilih aksi yang sesuai.</p>
            </div>

            <div class="mt-8 bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Riwayat Presensi Piket Hari Ini</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Nama Asisten</th>
                                <th class="py-3 px-4 uppercase font-semibold text-sm text-left">NIM</th>
                                <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Tanggal</th>
                                <th class="py-3 px-4 uppercase font-semibold text-sm text-center">Clock In</th>
                                <th class="py-3 px-4 uppercase font-semibold text-sm text-center">Clock Out</th>
                                <th class="py-3 px-4 uppercase font-semibold text-sm text-center">Durasi</th>
                            </tr>
                        </thead>
                        <tbody id="tabel-riwayat-presensi-body" class="text-gray-700">
                            <tr>
                                <td colspan="6" class="text-center p-4 text-gray-500">
                                    <p>Memuat riwayat...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- HALAMAN PRESENSI PRAKTIKUM -->
        <div id="page-presensi_praktikum" class="page hidden">
            <div class="bg-white p-6 rounded-lg shadow-md text-center max-w-md mx-auto">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Presensi Praktikum</h2>
                <p class="text-gray-500 mb-6" id="current-time-date-praktikum">Memuat tanggal dan waktu...</p>
                
                <div class="space-y-4 mb-6 text-left">
                    <div>
                        <label for="presensi-praktikum-nama" class="block text-sm font-medium text-gray-700">Nama Mahasiswa</label>
                        <input type="text" id="presensi-praktikum-nama" name="nama_mahasiswa" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm" placeholder="Masukkan nama lengkap Anda" required autocomplete="off">
                    </div>
                    <div>
                        <label for="presensi-praktikum-nim" class="block text-sm font-medium text-gray-700">NIM</label>
                        <input type="text" id="presensi-praktikum-nim" name="nim_mahasiswa_praktikum" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm" placeholder="Masukkan NIM Anda" required autocomplete="off">
                    </div>
                    <div>
                        <label for="mata-praktikum" class="block text-sm font-medium text-gray-700">Mata Praktikum</label>
                        <input type="text" id="mata-praktikum" name="mata_praktikum" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm" placeholder="Contoh: Fisika Dasar" required autocomplete="off">
                    </div>
                </div>
                
                <div class="flex items-center justify-center gap-4">
                    <button id="btn-clock-in-praktikum" class="flex-1 bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded-lg text-base transition-transform transform hover:scale-105">
                        <i class="fas fa-sign-in-alt mr-2"></i>Clock In
                    </button>
                    <button id="btn-clock-out-praktikum" class="flex-1 bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3 px-4 rounded-lg text-base transition-transform transform hover:scale-105">
                        <i class="fas fa-sign-out-alt mr-2"></i>Clock Out
                    </button>
                </div>
            </div>
                
            <div class="mt-8 bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Riwayat Presensi Praktikum Hari Ini</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Nama</th>
                                <th class="py-3 px-4 uppercase font-semibold text-sm text-left">NIM</th>
                                <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Praktikum</th>
                                <th class="py-3 px-4 uppercase font-semibold text-sm text-center">Clock In</th>
                                <th class="py-3 px-4 uppercase font-semibold text-sm text-center">Clock Out</th>
                                <th class="py-3 px-4 uppercase font-semibold text-sm text-center">Durasi</th>
                            </tr>
                        </thead>
                        <tbody id="tabel-riwayat-presensi-praktikum-body" class="text-gray-700">
                            <tr><td colspan="6" class="text-center p-4 text-gray-500">Memuat riwayat...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- AKHIR HALAMAN -->

        <!-- Dokumentasi Page -->
        <div id="page-dokumentasi" class="page hidden min-h-screen py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex items-center mb-4">
                        <button onclick="goBack()" class="mr-4 text-gray-600 hover:text-gray-800 transition-colors">
                            <i class="fas fa-arrow-left text-xl"></i>
                        </button>
                        <div>
                            <h2 class="text-3xl font-bold text-gray-800 mb-2">Dokumentasi Kegiatan</h2>
                            <p class="text-gray-600 text-lg">Koleksi foto dan dokumentasi berbagai kegiatan di Perpustakaan SMAKADUTA</p>
                        </div>
                    </div>
                </div>
                
                <!-- Filter Kategori -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Filter Kategori:</h3>
                    <div class="flex flex-wrap gap-3">
                        <button class="filter-kategori bg-emerald-500 text-white px-5 py-2 rounded-lg text-sm font-medium shadow-md hover:bg-emerald-600 transition-colors active" data-kategori="semua">
                            <i class="fas fa-th-large mr-2"></i>Semua
                        </button>
                        <button class="filter-kategori bg-white border border-gray-300 text-gray-700 px-5 py-2 rounded-lg text-sm font-medium shadow-sm hover:bg-gray-50 hover:border-gray-400 transition-all" data-kategori="Fasilitas">
                            <i class="fas fa-building mr-2"></i>Fasilitas
                        </button>
                        <button class="filter-kategori bg-white border border-gray-300 text-gray-700 px-5 py-2 rounded-lg text-sm font-medium shadow-sm hover:bg-gray-50 hover:border-gray-400 transition-all" data-kategori="Kegiatan">
                            <i class="fas fa-users mr-2"></i>Kegiatan
                        </button>
                        <button class="filter-kategori bg-white border border-gray-300 text-gray-700 px-5 py-2 rounded-lg text-sm font-medium shadow-sm hover:bg-gray-50 hover:border-gray-400 transition-all" data-kategori="Pelatihan">
                            <i class="fas fa-chalkboard-teacher mr-2"></i>Pelatihan
                        </button>
                        <button class="filter-kategori bg-white border border-gray-300 text-gray-700 px-5 py-2 rounded-lg text-sm font-medium shadow-sm hover:bg-gray-50 hover:border-gray-400 transition-all" data-kategori="Kegiatan">
                            <i class="fas fa-book mr-2"></i>Kegiatan
                        </button>
                        <button class="filter-kategori bg-white border border-gray-300 text-gray-700 px-5 py-2 rounded-lg text-sm font-medium shadow-sm hover:bg-gray-50 hover:border-gray-400 transition-all" data-kategori="Event">
                            <i class="fas fa-calendar-alt mr-2"></i>Event
                        </button>
                        <button class="filter-kategori bg-white border border-gray-300 text-gray-700 px-5 py-2 rounded-lg text-sm font-medium shadow-sm hover:bg-gray-50 hover:border-gray-400 transition-all" data-kategori="Penghargaan">
                            <i class="fas fa-trophy mr-2"></i>Penghargaan
                        </button>
                    </div>
                </div>

                <!-- Info Section -->
                <div id="dokumentasi-info" class="mb-6">
                    <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-emerald-500 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-emerald-700">
                                    <span id="dokumentasi-count">Loading...</span> dokumentasi tersedia. Klik pada foto untuk melihat detail lengkap.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grid Dokumentasi -->
                <div id="dokumentasi-container" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Loading state -->
                    <div id="loading-state" class="col-span-full flex justify-center items-center py-16">
                        <div class="text-center">
                            <div class="loading-spinner mx-auto mb-4"></div>
                            <p class="text-gray-600">Memuat dokumentasi...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- LogBook Page -->
        <div id="page-LogbookKegiatanLab" class="page hidden">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Form Section -->
                <div class="lg:col-span-1 bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">
                        <i class="fas fa-plus-circle mr-2 text-emerald-500"></i>Tambah LogBook Baru
                    </h2>
                    <form id="form-logbook" class="space-y-4">
                        <div>
                            <label for="logbook-nama" class="block text-sm font-medium text-gray-700">Nama Pengisi</label>
                            <input type="text" id="logbook-nama" name="nama_pengisi" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="Masukkan nama lengkap Anda" required>
                        </div>
                        <div>
                            <label for="logbook-nim" class="block text-sm font-medium text-gray-700">NIM</label>
                            <input type="text" id="logbook-nim" name="nim_pengisi" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="Masukkan NIM Anda" required>
                        </div>
                        <div>
                            <label for="logbook-judul" class="block text-sm font-medium text-gray-700">Judul Kegiatan</label>
                            <input type="text" id="logbook-judul" name="judul" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" required>
                        </div>
                        <div>
                            <label for="logbook-tanggal" class="block text-sm font-medium text-gray-700">Tanggal Kegiatan</label>
                            <input type="date" id="logbook-tanggal" name="tanggal_kegiatan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" required>
                        </div>
                        <div>
                            <label for="logbook-deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                            <textarea id="logbook-deskripsi" name="deskripsi" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" required></textarea>
                        </div>
                        <button type="submit" class="w-full bg-emerald-600 text-white py-2 px-4 rounded-md hover:bg-emerald-700 transition-all duration-300 flex items-center justify-center">
                            <i class="fas fa-save mr-2"></i>
                            <span>Simpan Catatan</span>
                        </button>
                    </form>
                </div>
                <!-- Table Section -->
                <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">
                        <i class="fas fa-history mr-2 text-emerald-500"></i>Riwayat LogBook
                    </h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Nama Pengisi</th>
                                    <th class="py-3 px-4 uppercase font-semibold text-sm text-left">NIM</th>
                                    <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Tanggal</th>
                                    <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Judul Kegiatan</th>
                                    <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Deskripsi</th>
                                </tr>
                            </thead>
                            <tbody id="tabel-logbook-body" class="text-gray-700">
                                <!-- Data will be loaded here by JavaScript -->
                                <tr><td colspan="5" class="text-center p-8 text-gray-500">Memuat data logbook...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-gradient-to-r from-slate-800 to-slate-900 text-white mt-16">
        <div class="max-w-7xl mx-auto px-6 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- About Section -->
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-emerald-500 rounded-lg flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M12 6V3m0 18v-3M5.636 5.636l-1.414-1.414m15.152 15.152l-1.414-1.414M18.364 5.636l-1.414 1.414m-11.314 11.314l-1.414 1.414" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold">Perpustakaan SMAKADUTA</h3>
                    </div>
                    <p class="text-gray-300 mb-4">Perpustakaan SMAKADUTA - SMK 2 Surakarta</p>
                    <p class="text-sm text-gray-400">Portal manajemen koleksi buku yang modern dan efisien untuk mendukung kegiatan akademik.</p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-lg font-semibold mb-4 text-emerald-300">Menu Utama</h4>
                    <ul class="space-y-2">
                        <li><a href="#" data-page="dashboard" class="nav-link text-gray-300 hover:text-emerald-300 transition-colors text-sm"><i class="fas fa-home w-4 mr-2"></i>Dashboard</a></li>
                        <li><a href="#" data-page="inventory" class="nav-link text-gray-300 hover:text-emerald-300 transition-colors text-sm"><i class="fas fa-book w-4 mr-2"></i>Inventory Buku</a></li>
                        <li><a href="#" data-page="peminjaman" class="nav-link text-gray-300 hover:text-emerald-300 transition-colors text-sm"><i class="fas fa-exchange-alt w-4 mr-2"></i>Peminjaman</a></li>
                    </ul>
                </div>

                <!-- Contact & Social Media -->
                <div>
                    <h4 class="text-lg font-semibold mb-4 text-emerald-300">Hubungi Kami</h4>
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-map-marker-alt text-emerald-400 w-4"></i>
                            <span class="text-gray-300 text-sm">SMK 2 Surakarta</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-envelope text-emerald-400 w-4"></i>
                            <span class="text-gray-300 text-sm">perpustakaan@smakaduta.sch.id</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-phone text-emerald-400 w-4"></i>
                            <span class="text-gray-300 text-sm">+62 271 646994</span>
                        </div>
                    </div>

                    <!-- Social Media Links -->
                    <div>
                        <h5 class="text-sm font-semibold mb-3 text-emerald-300">Follow Us</h5>
                        <div class="flex space-x-4">
                            <a href="https://instagram.com/smakaduta" target="_blank" rel="noopener noreferrer" 
                                class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-lg flex items-center justify-center hover:scale-110 transition-transform duration-300 shadow-lg">
                                <i class="fab fa-instagram text-white"></i>
                            </a>
                            <a href="https://tiktok.com/@smakaduta" target="_blank" rel="noopener noreferrer" 
                                class="w-10 h-10 bg-gradient-to-br from-gray-800 to-gray-900 rounded-lg flex items-center justify-center hover:scale-110 transition-transform duration-300 shadow-lg">
                                <i class="fab fa-tiktok text-white"></i>
                            </a>
                            <a href="https://wa.me/6285123456789" target="_blank" rel="noopener noreferrer" 
                                class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center hover:scale-110 transition-transform duration-300 shadow-lg">
                                <i class="fab fa-whatsapp text-white"></i>
                            </a>
                            <a href="https://youtube.com/@smakaduta" target="_blank" rel="noopener noreferrer" 
                                class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-lg flex items-center justify-center hover:scale-110 transition-transform duration-300 shadow-lg">
                                <i class="fab fa-youtube text-white"></i>
                            </a>
                            <a href="https://twitter.com/smakaduta" target="_blank" rel="noopener noreferrer" 
                                class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-emerald-500 rounded-lg flex items-center justify-center hover:scale-110 transition-transform duration-300 shadow-lg">
                                <i class="fab fa-twitter text-white"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Section -->
            <div class="border-t border-gray-700 mt-8 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="text-center md:text-left mb-4 md:mb-0">
                        <p class="text-gray-300 text-sm">
                            © 2026 Perpustakaan SMAKADUTA. Semua hak dilindungi.
                        </p>
                        <p class="text-gray-400 text-xs mt-1">
                            Website ini dibuat dengan ❤️
                        </p>
                    </div>
                    <div class="flex items-center space-x-4 text-xs text-gray-400">
                        <a href="#" class="hover:text-emerald-300 transition-colors">Privacy Policy</a>
                        <span>•</span>
                        <a href="#" class="hover:text-emerald-300 transition-colors">Terms of Service</a>
                        <span>•</span>
                        <a href="#" class="hover:text-emerald-300 transition-colors">Support</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Decorative Bottom Line -->
        <div class="h-1 bg-gradient-to-r from-emerald-500 via-violet-500 to-emerald-500"></div>
    </footer>

    <!-- Modal Detail Dokumentasi -->
    <div id="modal-detail-dokumentasi" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50 flex items-center justify-center p-4 modal-backdrop">
        <div class="bg-white rounded-xl max-w-5xl w-full max-h-[95vh] overflow-hidden shadow-2xl">
            <!-- Modal Header -->
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-3 h-3 bg-emerald-500 rounded-full"></div>
                    <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                </div>
                <h2 id="detail-judul" class="text-2xl font-bold text-gray-800 text-center flex-1 mx-4"></h2>
                <button id="close-modal-dokumentasi" class="text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-gray-100 rounded-full">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="overflow-y-auto max-h-[calc(95vh-120px)]">
                <div class="p-6">
                    <!-- Gambar -->
                    <div class="mb-6">
                        <img id="detail-gambar" src="" alt="" class="w-full h-auto rounded-lg shadow-lg">
                    </div>

                    <!-- Informasi Detail -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-3">Informasi Kegiatan</h3>
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-alt text-emerald-500 w-5 mr-3"></i>
                                    <span class="text-gray-700">Tanggal: <span id="detail-tanggal" class="font-medium"></span></span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-tag text-green-500 w-5 mr-3"></i>
                                    <span class="text-gray-700">Kategori: <span id="detail-kategori" class="font-medium"></span></span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-user text-emerald-500 w-5 mr-3"></i>
                                    <span class="text-gray-700">Uploader: <span id="detail-uploader" class="font-medium"></span></span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-3">Deskripsi</h3>
                            <p id="detail-deskripsi" class="text-gray-700 leading-relaxed"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Link ke file JavaScript eksternal -->
    <script src="navbar.js"></script>
    <script src="script.js"></script>
</body>

</html>
