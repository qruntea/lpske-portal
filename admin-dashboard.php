<?php

session_start();

// Cek jika user tidak login atau rolenya bukan admin, arahkan ke login
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.html");
    exit();
}

// Ambil data admin dari session
$nama_admin = $_SESSION['user_name'] ?? 'Admin';
$admin_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Portal LSTARS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* Anda bisa menambahkan style kustom di sini jika diperlukan */
        body { font-family: 'Inter', sans-serif; }
        .nav-link.active { background-color: rgb(31, 41, 55); } /* Warna aktif untuk menu sidebar */
        .page { display: none; } /* Semua halaman disembunyikan secara default */
        .page.active { display: block; } /* Hanya halaman aktif yang ditampilkan */

        /* Clock animations */
        #currentTime, #timeWidget {
            transition: transform 0.1s ease;
            font-family: 'Courier New', monospace;
        }

        #currentDate, #dateWidget {
            transition: opacity 0.3s ease;
        }

        /* Card hover effects */
        .bg-gradient-to-br {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        }

        .bg-gradient-to-br:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            transform: translateY(-2px);
            transition: all 0.3s ease;
        }

        /* Pulse animation untuk jam */
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }

        .pulse-subtle {
            animation: pulse 2s infinite;
        }

        /* Form input styling yang konsisten */
        .form-input {
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-input.error {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        /* Loading spinner */
        .loading-spinner {
            border: 2px solid #f3f3f3;
            border-top: 2px solid #3498db;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>

<body class="bg-gray-100" data-admin-id="<?php echo htmlspecialchars($admin_id, ENT_QUOTES, 'UTF-8'); ?>">
    <div class="flex h-screen bg-gray-200">
        <!-- Sidebar -->
        <div class="fixed inset-y-0 left-0 w-64 bg-gray-900 text-white p-4 space-y-6 transform md:relative md:translate-x-0 -translate-x-full transition-transform duration-200 ease-in-out z-30" id="sidebar">
            <a href="#" class="flex items-center space-x-2 px-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M12 6V3m0 18v-3M5.636 5.636l-1.414-1.414m15.152 15.152l-1.414-1.414M18.364 5.636l-1.414 1.414m-11.314 11.314l-1.414 1.414"/></svg>
                <span class="text-2xl font-bold">Admin Portal</span>
            </a>
            <nav class="space-y-2" id="main-nav">
                <a href="#" data-page="dashboard" class="nav-link active flex items-center space-x-3 py-2 px-4 rounded-lg transition-colors hover:bg-gray-700">
                    <i class="fas fa-home fa-fw"></i>
                    <span>Dashboard</span>
                </a>

                <a href="#" data-page="izin" class="nav-link flex items-center space-x-3 py-2 px-4 rounded-lg transition-colors hover:bg-gray-700">
                    <i class="fas fa-file-contract fa-fw"></i>
                    <span>Izin Kerja/Penelitian</span>
                </a>

                <a href="#" data-page="logbook_admin" class="nav-link flex items-center space-x-3 py-2 px-4 rounded-lg transition-colors hover:bg-gray-700">
                    <i class="fas fa-book fa-fw"></i>
                    <span>Logbook Kegiatan</span>
                </a>

                <h3 class="px-4 pt-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Database</h3>
                <a href="#" data-page="dosen" class="nav-link flex items-center space-x-3 py-2 px-4 rounded-lg transition-colors hover:bg-gray-700">
                    <i class="fas fa-users fa-fw"></i>
                    <span>Database Dosen</span>
                </a>
                <a href="#" data-page="asisten" class="nav-link flex items-center space-x-3 py-2 px-4 rounded-lg transition-colors hover:bg-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 18a1 1 0 0 0 1 1h18a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v2z"></path><path d="M10 10V5a2 2 0 1 1 4 0v5"></path><path d="M4 15h16"></path><path d="M2 11a1 1 0 0 1 1-1h18a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1v-1z"></path></svg>
                    <span>Database Asisten</span>
                </a>
                <a href="#" data-page="inventory" class="nav-link flex items-center space-x-3 py-2 px-4 rounded-lg transition-colors hover:bg-gray-700">
                    <i class="fas fa-boxes fa-fw"></i>
                    <span>Database Inventory</span>
                </a>
                <a href="#" data-page="peminjaman" class="nav-link flex items-center space-x-3 py-2 px-4 rounded-lg transition-colors hover:bg-gray-700">
                    <i class="fas fa-hand-holding fa-fw"></i>
                    <span>Data Peminjaman</span>
                </a>
                <a href="#" data-page="presensi" class="nav-link flex items-center space-x-3 py-2 px-4 rounded-lg transition-colors hover:bg-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    <span>Data Presensi</span>
                </a>
                <a href="#" data-page="presensi_praktikum_admin" class="nav-link flex items-center space-x-3 py-2 px-4 rounded-lg transition-colors hover:bg-gray-700">
                    <i class="fas fa-user-check fa-fw"></i>
                    <span>Presensi Praktikum</span>
                </a>
            </nav>
        </div>

        <!-- Main content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="flex justify-between items-center p-4 bg-white border-b-2 border-gray-200">
                <button id="menu-button" class="md:hidden text-gray-500 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <h1 class="text-2xl font-semibold text-gray-800" id="page-title">Dashboard</h1>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600 font-medium">Selamat datang, <?php echo htmlspecialchars($nama_admin); ?>!</span>
                    <button onclick="logout()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-medium">Logout</button>
                </div>
            </header>
            
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6 md:p-8" id="content-area">
                
                <!-- Dashboard Page -->
                <div id="page-dashboard" class="page active">
                    <h2 class="text-3xl font-bold text-gray-800">Selamat Datang di Panel Admin LSTARS</h2>
                    <p class="mt-2 text-gray-600">Gunakan menu di sebelah kiri untuk mengelola data perizinan dan database laboratorium.</p>
                    
                    <!-- WIDGET JAM REAL-TIME -->
                    <div class="bg-white p-4 rounded-lg shadow-lg border-l-4 border-blue-500 mt-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600" id="currentTime">00:00:00</div>
                            <div class="text-sm text-gray-600" id="currentDate">Loading...</div>
                            <div class="text-xs text-gray-500 mt-1" id="currentDay">Loading...</div>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8 mt-8">
                        <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow duration-300">
                            <div class="flex items-center">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-700">Perizinan Pending</h3>
                                    <p class="text-3xl font-bold text-yellow-600" id="pendingCount">-</p>
                                    <p class="text-sm text-gray-500">menunggu persetujuan</p>
                                </div>
                                <div class="ml-4">
                                    <i class="fas fa-clock text-yellow-500 text-2xl"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow duration-300">
                            <div class="flex items-center">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-700">Total Users</h3>
                                    <p class="text-3xl font-bold text-blue-600" id="usersCount">-</p>
                                    <p class="text-sm text-gray-500">dosen & asisten</p>
                                </div>
                                <div class="ml-4">
                                    <i class="fas fa-users text-blue-500 text-2xl"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow duration-300">
                            <div class="flex items-center">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-700">Asisten Aktif</h3>
                                    <p class="text-3xl font-bold text-green-600" id="activeLabCount">-</p>
                                    <p class="text-sm text-gray-500">status aktif</p>
                                </div>
                                <div class="ml-4">
                                    <i class="fas fa-user-check text-green-500 text-2xl"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow duration-300">
                            <div class="flex items-center">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-700">Equipment</h3>
                                    <p class="text-3xl font-bold text-purple-600" id="equipmentCount">-</p>
                                    <p class="text-sm text-gray-500">total inventory</p>
                                </div>
                                <div class="ml-4">
                                    <i class="fas fa-boxes text-purple-500 text-2xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activities -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Aktivitas Terbaru</h3>
                        <div id="recentActivities">Loading...</div>
                    </div>
                </div>

                <!-- Izin Kerja/Penelitian Page -->
                <div id="page-izin" class="page">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">Izin Kerja/Penelitian</h2>
                        <p class="text-gray-600 mt-2">Kelola pengajuan izin kerja dan penelitian dari dosen</p>
                    </div>
                    
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Pengajuan Izin</h3>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white admin-table">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th class="py-3 px-4 uppercase font-semibold text-sm text-left w-1/4">Judul</th>
                                        <th class="py-3 px-4 uppercase font-semibold text-sm text-left w-1/5">Nama Mahasiswa</th>
                                        <th class="py-3 px-4 uppercase font-semibold text-sm text-left w-1/5">Dosen Pembimbing</th>
                                        <th class="py-3 px-4 uppercase font-semibold text-sm text-left w-1/6">Tgl Pengajuan</th>
                                        <th class="py-3 px-4 uppercase font-semibold text-sm text-left w-1/8">Status</th>
                                        <th class="py-3 px-4 uppercase font-semibold text-sm text-center w-1/8">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tabel-riwayat-izin-body" class="text-gray-700">
                                    <!-- Data akan dimuat oleh JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Logbook -->
                <div id="page-logbook_admin" class="page hidden">
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Logbook Kegiatan</h2>

                        <p class="text-gray-600 mb-6">
                            Lihat dan pantau semua logbook kegiatan yang telah diisi oleh asisten.
                        </p>

                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white admin-table">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Nama Pengisi</th>
                                        <th class="py-3 px-4 uppercase font-semibold text-sm text-left">NIM</th>
                                        <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Tanggal</th>
                                        <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Judul Kegiatan</th>
                                        <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Deskripsi</th>
                                    </tr>
                                </thead>
                                <tbody id="tabel-logbook-admin-body" class="text-gray-700">
                                    </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Database Dosen Page (DIPERBAIKI LENGKAP) -->
                <div id="page-dosen" class="page hidden">
                    <!-- Tampilan Daftar Dosen (Awal) -->
                    <div id="dosen-list-view">
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-xl font-semibold text-gray-700">Database Dosen LSTARS</h2>
                                <button id="btn-tambah-dosen" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                                    <i class="fas fa-plus mr-2"></i>Tambah Dosen
                                </button>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white">
                                    <thead class="bg-gray-200">
                                        <tr>
                                            <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Nama Lengkap</th>
                                            <th class="py-3 px-4 uppercase font-semibold text-sm text-left">NIP</th>
                                            <th class="py-3 px-4 uppercase font-semibold text-sm text-left">NIDN</th>
                                            <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Homebase Prodi</th>
                                            <th class="py-3 px-4 uppercase font-semibold text-sm text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabel-dosen-body" class="text-gray-700"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Tampilan Detail Dosen (Awalnya tersembunyi) - DIHAPUS -->
                    <!-- Detail view sudah tidak digunakan lagi -->

                    <!-- Tampilan Form Tambah/Edit Dosen (DIPERBAIKI LENGKAP) -->
                    <div id="dosen-form-view" class="hidden">
                        <h2 id="form-dosen-title" class="text-2xl font-bold text-gray-800 mb-6">Tambah Dosen Baru</h2>
                        <div class="bg-white p-8 rounded-lg shadow-md">
                            <form id="form-dosen" enctype="multipart/form-data">
                                <input type="hidden" id="dosen-id" name="dosen_id" value="">
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Nama Dosen -->
                                    <div>
                                        <label for="nama_dosen" class="block text-sm font-medium text-gray-700 mb-1">
                                            Nama Dosen (Tanpa Gelar) <span class="text-red-500">*</span>
                                        </label>
                                        <input 
                                            type="text" 
                                            name="nama_dosen" 
                                            id="nama_dosen" 
                                            class="form-input mt-1 block w-full" 
                                            placeholder="Contoh: Ahmad Budi Santoso"
                                            required>
                                    </div>

                                    <!-- Gelar Depan dan Belakang -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label for="gelar_depan" class="block text-sm font-medium text-gray-700 mb-1">Gelar Depan</label>
                                            <input 
                                                type="text" 
                                                name="gelar_depan" 
                                                id="gelar_depan" 
                                                class="form-input mt-1 block w-full"
                                                placeholder="Dr., Prof., Ir.">
                                        </div>
                                        <div>
                                            <label for="gelar_belakang" class="block text-sm font-medium text-gray-700 mb-1">Gelar Belakang</label>
                                            <input 
                                                type="text" 
                                                name="gelar_belakang" 
                                                id="gelar_belakang" 
                                                class="form-input mt-1 block w-full"
                                                placeholder="M.Kom., Ph.D., S.T.">
                                        </div>
                                    </div>

                                    <!-- NIDN -->
                                    <div>
                                        <label for="nidn" class="block text-sm font-medium text-gray-700 mb-1">
                                            NIDN <span class="text-red-500">*</span>
                                        </label>
                                        <input 
                                            type="text" 
                                            name="nidn" 
                                            id="nidn" 
                                            class="form-input mt-1 block w-full"
                                            placeholder="Nomor Induk Dosen Nasional"
                                            required>
                                        <p class="text-xs text-gray-500 mt-1">Nomor Induk Dosen Nasional (NIDN)</p>
                                    </div>

                                    <!-- NIP -->
                                    <div>
                                        <label for="nip" class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
                                        <input 
                                            type="text" 
                                            name="nip" 
                                            id="nip" 
                                            class="form-input mt-1 block w-full"
                                            placeholder="Nomor Induk Pegawai (opsional)">
                                        <p class="text-xs text-gray-500 mt-1">Opsional - Nomor Induk Pegawai</p>
                                    </div>

                                    <!-- Email -->
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                            Email <span class="text-red-500">*</span>
                                        </label>
                                        <input 
                                            type="email" 
                                            name="email" 
                                            id="email" 
                                            class="form-input mt-1 block w-full"
                                            placeholder="email@domain.com"
                                            required>
                                    </div>

                                    <!-- Homebase Prodi -->
                                    <div>
                                        <label for="homebase_prodi" class="block text-sm font-medium text-gray-700 mb-1">Homebase Prodi</label>
                                        <input 
                                            type="text" 
                                            name="homebase_prodi" 
                                            id="homebase_prodi" 
                                            class="form-input mt-1 block w-full"
                                            placeholder="Contoh: Teknik Informatika">
                                    </div>

                                    <!-- Username -->
                                    <div>
                                        <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                                        <input 
                                            type="text" 
                                            name="username" 
                                            id="username" 
                                            class="form-input mt-1 block w-full"
                                            placeholder="Username untuk login">
                                        <p class="text-xs text-gray-500 mt-1">Kosongkan untuk menggunakan NIDN sebagai username</p>
                                    </div>

                                    <!-- Password -->
                                    <div>
                                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                        <input 
                                            type="password" 
                                            name="password" 
                                            id="password" 
                                            class="form-input mt-1 block w-full"
                                            placeholder="Password untuk login">
                                        <p class="text-xs text-gray-500 mt-1" id="password-help">Password wajib diisi untuk dosen baru</p>
                                    </div>

                                    <!-- Foto Upload -->
                                    <div class="md:col-span-2">
                                        <label for="foto" class="block text-sm font-medium text-gray-700 mb-1">Foto Dosen</label>
                                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors">
                                            <div class="space-y-1 text-center">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <div class="flex text-sm text-gray-600">
                                                    <label for="foto" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                        <span>Upload foto</span>
                                                        <input 
                                                            id="foto" 
                                                            name="foto" 
                                                            type="file" 
                                                            class="sr-only" 
                                                            accept="image/jpeg,image/jpg,image/png,image/gif"
                                                            onchange="previewFoto(this)">
                                                    </label>
                                                    <p class="pl-1">atau drag and drop</p>
                                                </div>
                                                <p class="text-xs text-gray-500">PNG, JPG, GIF hingga 2MB</p>
                                            </div>
                                        </div>
                                        <!-- Preview foto -->
                                        <div id="foto-preview" class="mt-4 hidden">
                                            <img id="preview-image" src="" alt="Preview" class="h-32 w-32 object-cover rounded-lg border border-gray-300">
                                        </div>
                                        <p class="text-xs text-gray-500 mt-2">Kosongkan jika tidak ingin mengubah foto.</p>
                                    </div>
                                </div>

                                <!-- Form Actions -->
                                <div class="mt-8 flex justify-end space-x-4">
                                    <button 
                                        type="button" 
                                        id="btn-batal-form" 
                                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-6 rounded-lg transition-colors duration-200">
                                        <i class="fas fa-times mr-2"></i>Batal
                                    </button>
                                    <button 
                                        type="submit" 
                                        id="btn-submit-dosen"
                                        class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-6 rounded-lg transition-colors duration-200 flex items-center"
                                        disabled>
                                        <i class="fas fa-save mr-2"></i>
                                        <span id="submit-text">Simpan</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Database Asisten Page -->
                <div id="page-asisten" class="page">
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-semibold text-gray-700">Database Asisten Laboratorium</h2>
                            <button id="btn-tambah-asisten" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium">
                                <i class="fas fa-plus mr-2"></i>Tambah Asisten
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Nama</th>
                                        <th class="py-3 px-4 uppercase font-semibold text-sm text-left">NIM</th>
                                        <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Jabatan</th>
                                        <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Status</th>
                                        <th class="py-3 px-4 uppercase font-semibold text-sm text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tabel-asisten-body" class="text-gray-700"></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Modal Form Asisten -->
                    <div id="modal-asisten" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
                        <div class="flex justify-center items-center min-h-screen p-4">
                            <div class="bg-white rounded-lg shadow-lg w-full max-w-md">
                                <div class="flex justify-between items-center p-6 border-b">
                                    <h3 id="modal-title" class="text-lg font-semibold text-gray-900">Tambah Asisten</h3>
                                    <button id="btn-close-modal" class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                                <form id="form-asisten" class="p-6">
                                    <input type="hidden" id="asisten-id" name="asisten_id">
                                    <div class="space-y-4">
                                        <div>
                                            <label for="nama-user" class="block text-sm font-medium text-gray-700">Nama User</label>
                                            <input type="text" id="nama-user" name="nama" placeholder="Ketik nama lengkap user" class="form-input mt-1 block w-full" required>
                                        </div>
                                        <div>
                                            <label for="nim-user" class="block text-sm font-medium text-gray-700">NIM</label>
                                            <input type="text" id="nim-user" name="nim" placeholder="Ketik NIM user" class="form-input mt-1 block w-full" required>
                                        </div>
                                        <div>
                                            <label for="jabatan" class="block text-sm font-medium text-gray-700">Jabatan</label>
                                            <input type="text" id="jabatan" name="jabatan" placeholder="Contoh: Asisten Dosen, Koordinator Lab, dll" class="form-input mt-1 block w-full" required>
                                        </div>
                                        <div>
                                            <label for="angkatan" class="block text-sm font-medium text-gray-700">Angkatan</label>
                                            <input type="text" id="angkatan" name="angkatan" placeholder="Contoh: 2023" class="form-input mt-1 block w-full" required>
                                        </div>
                                        <div>
                                            <label for="status-input" class="block text-sm font-medium text-gray-700">Status</label>
                                            <input type="text" id="status-input" name="status" placeholder="Contoh: Aktif, Tidak Aktif" class="form-input mt-1 block w-full" required>
                                        </div>
                                    </div>
                                    <div class="flex justify-end space-x-3 mt-6">
                                        <button type="button" id="btn-cancel" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-gray-300 rounded-md hover:bg-gray-300">
                                            Batal
                                        </button>
                                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                                            Simpan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

<!-- Database Inventory Page - FIXED VERSION -->
<div id="page-inventory" class="page hidden">
    <!-- LIST VIEW -->
    <div id="inventory-list-view">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <!-- Header dengan tombol tambah -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-700">Database Inventory</h2>
                <button id="btn-tambah-inventory" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors shadow-md">
                    <i class="fas fa-plus mr-2"></i>Tambah Item
                </button>
            </div>

            <!-- Tabel Inventory -->
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Nama Alat</th>
                            <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Kode</th>
                            <th class="py-3 px-4 uppercase font-semibold text-sm text-center">Jumlah</th>
                            <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Status</th>
                            <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Lokasi</th>
                            <th class="py-3 px-4 uppercase font-semibold text-sm text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tabel-inventory-body" class="text-gray-700">
                        <!-- Data akan dimuat oleh JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Inventory - Add before </body> -->
<div id="modal-inventory" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-lg w-full max-h-[90vh] overflow-hidden shadow-2xl">
        <!-- Modal Header -->
        <div class="flex justify-between items-center p-6 border-b border-gray-200">
            <h2 id="modal-title-inventory" class="text-xl font-semibold text-gray-800">Tambah Item Inventory</h2>
            <button id="btn-close-modal-inventory" class="text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-gray-100 rounded-full">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <!-- Modal Content -->
        <div class="overflow-y-auto max-h-[calc(90vh-120px)]">
            <div class="p-6">
                <form id="form-inventory" class="space-y-4">
                    <!-- Hidden field untuk ID (untuk edit) -->
                    <input type="hidden" id="inventory-id" name="inventory_id">

                    <!-- Nama Alat -->
                    <div>
                        <label for="nama-alat" class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Alat <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nama-alat" name="nama_alat" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Contoh: Jangka Sorong">
                    </div>

                    <!-- Kode Alat -->
                    <div>
                        <label for="kode-alat" class="block text-sm font-medium text-gray-700 mb-1">
                            Kode Alat <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="kode-alat" name="kode_alat" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Contoh: AA01, AA02, dll">
                        <p class="text-xs text-gray-500 mt-1">Format: AA + nomor urut (contoh: AA01, AA02)</p>
                    </div>



                    <!-- Jumlah Total -->
                    <div>
                        <label for="jumlah-total" class="block text-sm font-medium text-gray-700 mb-1">
                            Jumlah Total <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="jumlah-total" name="jumlah_total" min="1" value="1" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Masukkan jumlah total">
                        <p class="text-xs text-gray-500 mt-1">Total keseluruhan item yang dimiliki</p>
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status-inventory" class="block text-sm font-medium text-gray-700 mb-1">
                            Status
                        </label>
                        <select id="status-inventory" name="status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="Tersedia">Tersedia</option>
                            <option value="Dipinjam">Dipinjam</option>
                            <option value="Rusak">Rusak</option>
                            <option value="Maintenance">Maintenance</option>
                        </select>
                    </div>

                    <!-- Lokasi -->
                    <div>
                        <label for="lokasi" class="block text-sm font-medium text-gray-700 mb-1">
                            Lokasi
                        </label>
                        <input type="text" id="lokasi" name="lokasi"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Contoh: Rak A1, Lemari B2, dll">
                    </div>



                    <!-- Submit Button -->
                    <div class="flex gap-3 pt-4">
                        <button type="submit" 
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-save mr-2"></i>Simpan
                        </button>
                        <button type="button" id="btn-cancel-inventory"
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

                <!-- Data Peminjaman Page dengan Sistem Notifikasi -->
                <div id="page-peminjaman" class="page">
                    <!-- Header dengan Statistik -->
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">Data Peminjaman Laboratorium</h2>
                        <p class="text-gray-600 mt-2">Monitor dan kelola semua aktivitas peminjaman alat laboratorium</p>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                        <!-- Statistik Peminjaman -->
                        <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow duration-300">
                            <div class="flex items-center">
                                <div class="flex-1">
                                    <h3 class="text-sm font-semibold text-gray-700">Total Peminjaman</h3>
                                    <p class="text-2xl font-bold text-blue-600" id="totalPeminjaman">-</p>
                                    <p class="text-xs text-gray-500">seluruh waktu</p>
                                </div>
                                <div class="ml-2">
                                    <i class="fas fa-clipboard-list text-blue-500 text-xl"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow duration-300">
                            <div class="flex items-center">
                                <div class="flex-1">
                                    <h3 class="text-sm font-semibold text-gray-700">Sedang Dipinjam</h3>
                                    <p class="text-2xl font-bold text-yellow-600" id="sedangDipinjam">-</p>
                                    <p class="text-xs text-gray-500">alat aktif</p>
                                </div>
                                <div class="ml-2">
                                    <i class="fas fa-clock text-yellow-500 text-xl"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow duration-300">
                            <div class="flex items-center">
                                <div class="flex-1">
                                    <h3 class="text-sm font-semibold text-gray-700">Terlambat</h3>
                                    <p class="text-2xl font-bold text-red-600" id="terlambat">-</p>
                                    <p class="text-xs text-gray-500">perlu tindakan</p>
                                </div>
                                <div class="ml-2">
                                    <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow duration-300">
                            <div class="flex items-center">
                                <div class="flex-1">
                                    <h3 class="text-sm font-semibold text-gray-700">Dikembalikan</h3>
                                    <p class="text-2xl font-bold text-green-600" id="dikembalikan">-</p>
                                    <p class="text-xs text-gray-500">selesai</p>
                                </div>
                                <div class="ml-2">
                                    <i class="fas fa-check-circle text-green-500 text-xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter dan Tools -->
                    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                        <div class="flex flex-wrap gap-4 items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-700">
                                <i class="fas fa-filter mr-2"></i>Filter & Tools
                            </h3>
                            <div class="flex gap-4 flex-wrap">
                                <select id="filterStatus" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                                    <option value="">Semua Status</option>
                                    <option value="Dipinjam">Sedang Dipinjam</option>
                                    <option value="Terlambat">Terlambat</option>
                                    <option value="Dikembalikan">Dikembalikan</option>
                                </select>
                                
                                <select id="filterRole" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                                    <option value="">Semua Peminjam</option>
                                    <option value="dosen">Dosen</option>
                                    <option value="asisten">Asisten</option>
                                </select>
                                
                                <input type="date" id="filterTanggal" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                                
                                <button onclick="applyPeminjamanFilter()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm">
                                    <i class="fas fa-filter mr-2"></i>Filter
                                </button>
                                
                                <button onclick="resetPeminjamanFilter()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm">
                                    <i class="fas fa-undo mr-2"></i>Reset
                                </button>
                                
                                <button onclick="tampilkanDataPeminjamanWithFilter()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md text-sm">
                                    <i class="fas fa-sync-alt mr-2"></i>Refresh
                                </button>
                                
                                <button onclick="exportPeminjamanData()" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-md text-sm">
                                    <i class="fas fa-download mr-2"></i>Export CSV
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Data Peminjaman -->
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-700">
                                <i class="fas fa-table mr-2"></i>Daftar Peminjaman
                            </h3>
                            <div class="text-sm text-gray-500">
                                Data diurutkan berdasarkan prioritas: Terlambat → Dipinjam → Dikembalikan
                            </div>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th class="py-3 px-4 uppercase font-semibold text-sm text-left">
                                            <i class="fas fa-user mr-1"></i>Peminjam
                                        </th>
                                        <th class="py-3 px-4 uppercase font-semibold text-sm text-left">
                                            <i class="fas fa-tools mr-1"></i>Alat
                                        </th>
                                        <th class="py-3 px-4 uppercase font-semibold text-sm text-center">
                                            <i class="fas fa-calendar-alt mr-1"></i>Tgl Pinjam
                                        </th>
                                        <th class="py-3 px-4 uppercase font-semibold text-sm text-center">
                                            <i class="fas fa-calendar-check mr-1"></i>Rencana Kembali
                                        </th>
                                        <th class="py-3 px-4 uppercase font-semibold text-sm text-center">
                                            <i class="fas fa-calendar-times mr-1"></i>Aktual Kembali
                                        </th>
                                        <th class="py-3 px-4 uppercase font-semibold text-sm text-center">
                                            <i class="fas fa-hourglass-half mr-1"></i>Durasi
                                        </th>
                                        <th class="py-3 px-4 uppercase font-semibold text-sm text-center">
                                            <i class="fas fa-info-circle mr-1"></i>Status
                                        </th>
                                        <th class="py-3 px-4 uppercase font-semibold text-sm text-center">
                                            <i class="fas fa-cog mr-1"></i>Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="tabel-peminjaman-body" class="text-gray-700">
                                    <!-- Data akan dimuat oleh JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Info Panel -->
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Legend -->
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <h4 class="font-semibold text-blue-800 mb-3">
                                <i class="fas fa-info-circle mr-2"></i>Keterangan Status
                            </h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex items-center space-x-2">
                                    <span class="bg-green-200 text-green-800 py-1 px-3 rounded-full text-xs font-medium">Dikembalikan</span>
                                    <span class="text-gray-600">Alat sudah dikembalikan</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="bg-yellow-200 text-yellow-800 py-1 px-3 rounded-full text-xs font-medium">Dipinjam</span>
                                    <span class="text-gray-600">Alat sedang dipinjam (normal)</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="bg-red-200 text-red-800 py-1 px-3 rounded-full text-xs font-medium">Terlambat</span>
                                    <span class="text-gray-600">Melewati batas waktu kembali</span>
                                </div>
                            </div>
                        </div>     
                    </div>
                </div>

                <!-- Data Presensi Page -->
                <div id="page-presensi" class="page">
                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                        <div class="bg-white p-6 rounded-lg shadow">
                            <h3 class="text-lg font-semibold text-gray-700">Hari Ini</h3>
                            <p class="text-3xl font-bold text-blue-600" id="presensiHariIni">-</p>
                            <p class="text-sm text-gray-500">yang hadir</p>
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow">
                            <h3 class="text-lg font-semibold text-gray-700">Sedang Piket</h3>
                            <p class="text-3xl font-bold text-green-600" id="sedangPiket">-</p>
                            <p class="text-sm text-gray-500">asisten aktif</p>
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow">
                            <h3 class="text-lg font-semibold text-gray-700">Bulan Ini</h3>
                            <p class="text-3xl font-bold text-purple-600" id="totalBulanIni">-</p>
                            <p class="text-sm text-gray-500">total presensi</p>
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow">
                            <h3 class="text-lg font-semibold text-gray-700">Rata-rata</h3>
                            <p class="text-3xl font-bold text-orange-600" id="rataRata">-</p>
                            <p class="text-sm text-gray-500">jam per hari</p>
                        </div>
                    </div>

                    <!-- Filter dan Search -->
                    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                        <div class="flex flex-wrap gap-4 items-center justify-between">
                            <h2 class="text-xl font-semibold text-gray-700">Data Presensi Asisten</h2>
                            <div class="flex gap-4">
                                <input type="date" id="filterTanggalPresensi" class="px-3 py-2 border border-gray-300 rounded-md" placeholder="Pilih tanggal">
                                
                                <select id="filterStatusPresensi" class="px-3 py-2 border border-gray-300 rounded-md">
                                    <option value="">Semua Status</option>
                                    <option value="sedang piket">Sedang Piket</option>
                                    <option value="selesai">Selesai</option>
                                </select>
                                
                                <button onclick="filterPresensi()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                                    <i class="fas fa-filter mr-2"></i>Filter
                                </button>
                                
                                <button onclick="resetFilterPresensi()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
                                    <i class="fas fa-undo mr-2"></i>Reset
                                </button>
                                
                                <button onclick="tampilkanDataPresensi()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md">
                                    <i class="fas fa-sync-alt mr-2"></i>Refresh
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Presensi Hari Ini -->
                    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Presensi Hari Ini</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Nama Asisten</th>
                                        <th class="py-3 px-4 uppercase font-semibold text-sm text-center">Clock In</th>
                                        <th class="py-3 px-4 uppercase font-semibold text-sm text-center">Clock Out</th>
                                        <th class="py-3 px-4 uppercase font-semibold text-sm text-center">Durasi</th>
                                        <th class="py-3 px-4 uppercase font-semibold text-sm text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="tabel-presensi-hari-ini-body" class="text-gray-700"></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tabel Semua Presensi -->
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Riwayat Presensi (30 Hari Terakhir)</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Tanggal</th>
                                        <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Nama Asisten</th>
                                        <th class="py-3 px-4 uppercase font-semibold text-sm text-center">Clock In</th>
                                        <th class="py-3 px-4 uppercase font-semibold text-sm text-center">Clock Out</th>
                                        <th class="py-3 px-4 uppercase font-semibold text-sm text-center">Durasi</th>
                                        <th class="py-3 px-4 uppercase font-semibold text-sm text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="tabel-semua-presensi-body" class="text-gray-700"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Presensi Praktikum -->
                <div id="page-presensi_praktikum_admin" class="page hidden">
                    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">Data Presensi Praktikum Mahasiswa</h2>
                        <p class="text-gray-600 mt-2">Monitor dan lihat riwayat presensi praktikum mahasiswa.</p>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                        <div class="flex flex-wrap gap-4 items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-700">
                                <i class="fas fa-filter mr-2"></i>Filter & Tools
                            </h3>
                            <div class="flex gap-4 flex-wrap">
                                <input type="date" id="filterTanggalPraktikum" class="px-3 py-2 border border-gray-300 rounded-md" placeholder="Pilih tanggal">
                                
                                <button onclick="filterPresensiPraktikum()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm">
                                    <i class="fas fa-filter mr-2"></i>Filter
                                </button>
                                
                                <button onclick="resetFilterPresensiPraktikum()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm">
                                    <i class="fas fa-undo mr-2"></i>Reset
                                </button>
                                
                                <button onclick="tampilkanDataPresensiPraktikum()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md text-sm">
                                    <i class="fas fa-sync-alt mr-2"></i>Refresh
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Nama</th>
                                        <th class="py-3 px-4 uppercase font-semibold text-sm text-left">NIM</th>
                                        <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Praktikum</th>
                                        <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Tanggal</th> <th class="py-3 px-4 uppercase font-semibold text-sm text-center">Clock In</th>
                                        <th class="py-3 px-4 uppercase font-semibold text-sm text-center">Clock Out</th>
                                        <th class="py-3 px-4 uppercase font-semibold text-sm text-center">Durasi</th>
                                        <th class="py-3 px-4 uppercase font-semibold text-sm text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="tabel-presensi-praktikum-admin-body" class="text-gray-700">
                                    </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- JavaScript Functions -->
    <script>
        // ✅ FUNGSI PREVIEW FOTO
        function previewFoto(input) {
            const preview = document.getElementById('foto-preview');
            const previewImage = document.getElementById('preview-image');
            
            if (input.files && input.files[0]) {
                const file = input.files[0];
                
                // Validasi ukuran file (2MB = 2 * 1024 * 1024 bytes)
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran file terlalu besar! Maksimal 2MB.');
                    input.value = '';
                    preview.classList.add('hidden');
                    return;
                }
                
                // Validasi tipe file
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Tipe file tidak didukung! Gunakan JPG, JPEG, PNG, atau GIF.');
                    input.value = '';
                    preview.classList.add('hidden');
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                preview.classList.add('hidden');
            }
        }

        // ✅ FUNGSI VALIDASI FORM DOSEN
        function validateDosenForm() {
            const requiredFields = [
                { id: 'nama_dosen', name: 'Nama Dosen' },
                { id: 'nidn', name: 'NIDN' },
                { id: 'email', name: 'Email' }
            ];
            
            let isValid = true;
            let errorMessages = [];
            
            // Reset error styling
            document.querySelectorAll('.form-input').forEach(input => {
                input.classList.remove('error');
            });
            
            // Check required fields
            requiredFields.forEach(field => {
                const element = document.getElementById(field.id);
                const value = element.value.trim();
                
                if (!value) {
                    isValid = false;
                    errorMessages.push(`${field.name} wajib diisi`);
                    element.classList.add('error');
                } else {
                    element.classList.remove('error');
                }
            });
            
            // Validate email format only
            const email = document.getElementById('email').value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email && !emailRegex.test(email)) {
                isValid = false;
                errorMessages.push('Format email tidak valid');
                document.getElementById('email').classList.add('error');
            }
            
            // Show validation errors
            if (!isValid) {
                alert('Validation Error:\n\n' + errorMessages.join('\n'));
            }
            
            return isValid;
        }

        // ✅ FUNGSI LOGOUT
        async function logout() {
            try {
                await fetch('api/logout.php', { method: 'POST' });
                window.location.href = 'login.html';
            } catch (error) {
                console.error('Logout error:', error);
                window.location.href = 'login.html';
            }
        }

        // ✅ INITIALIZATION SETELAH DOM LOADED
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Admin Dashboard loaded');
            
            // Check admin ID
            const adminId = document.body.dataset.adminId;
            if (!adminId) {
                console.error('Admin ID not found');
                alert('Session error: Admin ID tidak ditemukan. Silakan login ulang.');
                window.location.href = 'login.html';
                return;
            }
            
            // Enable submit button after validation
            const formDosen = document.getElementById('form-dosen');
            const submitButton = document.getElementById('btn-submit-dosen');
            
            if (submitButton) {
                submitButton.disabled = false; // Enable button
            }
            
            // Form dosen submission handler
            if (formDosen) {
                formDosen.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Validate form
                    if (!validateDosenForm()) {
                        return;
                    }
                    
                    // Show loading state
                    const submitButton = document.getElementById('btn-submit-dosen');
                    const submitText = document.getElementById('submit-text');
                    
                    if (submitButton) {
                        submitButton.disabled = true;
                        submitButton.innerHTML = '<div class="loading-spinner mr-2"></div><span>Menyimpan...</span>';
                    }
                    
                    const formData = new FormData(this);
                    
                    // Log form data for debugging
                    console.log('📨 Form data yang akan dikirim:');
                    for (let [key, value] of formData.entries()) {
                        if (key !== 'password' || value !== '') {
                            console.log(`  ${key}: ${value}`);
                        }
                    }
                    
                    // Call the save function from scriptadmin.js
                    if (typeof saveDosen === 'function') {
                        saveDosen(formData).finally(() => {
                            // Reset button state
                            if (submitButton) {
                                submitButton.disabled = false;
                                submitButton.innerHTML = '<i class="fas fa-save mr-2"></i><span>Simpan</span>';
                            }
                        });
                    } else {
                        console.error('saveDosen function not found');
                        alert('Error: Function saveDosen tidak ditemukan. Pastikan scriptadmin.js dimuat dengan benar.');
                        
                        // Reset button state
                        if (submitButton) {
                            submitButton.disabled = false;
                            submitButton.innerHTML = '<i class="fas fa-save mr-2"></i><span>Simpan</span>';
                        }
                    }
                });
            }
            
            // Real-time input validation
            const inputs = document.querySelectorAll('.form-input');
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (this.hasAttribute('required') && !this.value.trim()) {
                        this.classList.add('error');
                    } else {
                        this.classList.remove('error');
                    }
                    
                    // Special validation for email only
                    if (this.id === 'email' && this.value.trim()) {
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailRegex.test(this.value.trim())) {
                            this.classList.add('error');
                        } else {
                            this.classList.remove('error');
                        }
                    }
                });
                
                input.addEventListener('input', function() {
                    // Remove error styling when user starts typing
                    if (this.classList.contains('error')) {
                        this.classList.remove('error');
                    }
                });
            });
            
            console.log('✅ Form validation and handlers initialized');
        });
    </script>

    <!-- Load Main JavaScript File -->
    <script src="scriptadmin.js" defer></script>
</body>
</html>