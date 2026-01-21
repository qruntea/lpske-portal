<?php
// database-asisten.php - File mandiri dengan koneksi database sendiri

// Konfigurasi database
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'lpske-pkl';

// Inisialisasi variabel
$asisten_data = array();
$total_asisten = 0;
$connection_status = 'disconnected';
$error_message = '';

try {
    // Membuat koneksi database
    $koneksi = new mysqli($host, $username, $password, $database);
    
    // Set charset ke utf8
    $koneksi->set_charset("utf8");
    
    // Cek koneksi
    if ($koneksi->connect_error) {
        throw new Exception("Koneksi gagal: " . $koneksi->connect_error);
    }
    
    $connection_status = 'connected';
    
    // Query untuk mengambil data asisten (sesuai dengan struktur tabel di phpMyAdmin)
    $query = "SELECT nim, nama, angkatan, status FROM asisten ORDER BY nama ASC";
    $result = $koneksi->query($query);
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $asisten_data[] = array(
                'nim' => $row['nim'],
                'nama' => $row['nama'],
                'angkatan' => $row['angkatan'],
                'status' => $row['status']
            );
        }
        $total_asisten = count($asisten_data);
    } else {
        throw new Exception("Query gagal: " . $koneksi->error);
    }
    
} catch (Exception $e) {
    $error_message = $e->getMessage();
    error_log("Database Asisten Error: " . $error_message);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Asisten - Portal LSTARS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .hero-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="hero-gradient shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user-graduate text-white text-2xl"></i>
                    </div>
                    <div class="text-white">
                        <h1 class="text-2xl font-bold">Database Asisten</h1>
                        <p class="text-purple-200">Portal LSTARS - Teknik Industri UNS</p>
                    </div>
                </div>
                <a href="guest-dashboard.php" class="bg-white text-purple-600 px-6 py-2 rounded-lg font-medium hover:bg-purple-50 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali ke Beranda
                </a>
            </div>
        </div>
    </header>

    <!-- Debug Info (hapus di production) -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="bg-gray-100 rounded-lg p-4 text-sm">
            <strong>Debug Info:</strong>
            Status Koneksi: <span class="<?php echo $connection_status === 'connected' ? 'text-green-600' : 'text-red-600'; ?>">
                <?php echo $connection_status === 'connected' ? '✅ Terhubung' : '❌ Tidak Terhubung'; ?>
            </span>
            <?php if ($error_message): ?>
                | Error: <span class="text-red-600"><?php echo htmlspecialchars($error_message); ?></span>
            <?php endif; ?>
            | Total Data: <?php echo $total_asisten; ?>
        </div>
    </div>

    <!-- Main Content -->
    <div class="min-h-screen py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Page Description -->
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Database Asisten LSTARS</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Akses informasi lengkap profil asisten laboratorium dan mahasiswa
                </p>
            </div>

            <!-- Search Bar -->
            <?php if ($total_asisten > 0): ?>
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
                    <div class="w-full sm:w-auto flex-1 max-w-md">
                        <div class="relative">
                            <input type="text" id="searchInput" placeholder="Cari nama, NIM, atau angkatan..." 
                                   class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Data Table -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <!-- Table Header -->
                <div class="px-6 py-4 bg-green-50 border-b border-green-100">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-green-800">
                            <i class="fas fa-user-graduate mr-2"></i>
                            Daftar Asisten Laboratorium LSTARS
                        </h3>
                        <span class="text-sm text-green-600">Total: <span id="totalCount"><?php echo $total_asisten; ?></span> asisten</span>
                    </div>
                </div>
                
                <!-- Table Content -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIM</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Angkatan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="asistenTableBody">
                            <?php if ($connection_status === 'disconnected'): ?>
                            <!-- Error State -->
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-red-500">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-exclamation-triangle text-4xl text-red-300 mb-4"></i>
                                        <p class="text-lg font-medium mb-2">Koneksi Database Gagal</p>
                                        <p class="text-sm"><?php echo htmlspecialchars($error_message); ?></p>
                                    </div>
                                </td>
                            </tr>
                            <?php elseif (empty($asisten_data)): ?>
                            <!-- Empty State -->
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-database text-4xl text-gray-300 mb-4"></i>
                                        <p class="text-lg font-medium mb-2">Belum ada data asisten</p>
                                        <p class="text-sm">Silakan tambahkan data asisten ke database</p>
                                    </div>
                                </td>
                            </tr>
                            <?php else: ?>
                                <!-- Data Rows -->
                                <?php foreach ($asisten_data as $index => $asisten): ?>
                                    <?php
                                    // Tentukan warna status badge
                                    $statusClass = 'bg-green-100 text-green-800';
                                    if ($asisten['status'] === 'Tidak Aktif' || $asisten['status'] === 'Alumni') {
                                        $statusClass = 'bg-red-100 text-red-800';
                                    } elseif ($asisten['status'] === 'Cuti') {
                                        $statusClass = 'bg-yellow-100 text-yellow-800';
                                    }
                                    ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $index + 1; ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($asisten['nim']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                                    <i class="fas fa-user-graduate text-green-600"></i>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($asisten['nama']); ?></div>
                                                    <div class="text-sm text-gray-500">Teknik Industri</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <?php echo htmlspecialchars($asisten['angkatan']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $statusClass; ?>">
                                                <?php echo htmlspecialchars($asisten['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($total_asisten > 0): ?>
                <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <button class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50" disabled>
                            Previous
                        </button>
                        <button class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50" disabled>
                            Next
                        </button>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Menampilkan <span class="font-medium">1</span> sampai <span class="font-medium"><?php echo $total_asisten; ?></span> dari 
                                <span class="font-medium"><?php echo $total_asisten; ?></span> asisten
                            </p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Status Message -->
            <div class="mt-8 text-center">
                <?php if ($connection_status === 'connected' && $total_asisten > 0): ?>
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 max-w-md mx-auto">
                    <i class="fas fa-check-circle text-green-600 text-lg mb-2"></i>
                    <p class="text-green-800 font-medium text-sm">Database terhubung dengan sukses!</p>
                    <p class="text-green-600 text-xs mt-1">Menampilkan <?php echo $total_asisten; ?> data asisten</p>
                </div>
                <?php elseif ($connection_status === 'connected' && $total_asisten === 0): ?>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 max-w-md mx-auto">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-lg mb-2"></i>
                    <p class="text-yellow-800 font-medium text-sm">Database terhubung, tapi belum ada data</p>
                    <p class="text-yellow-600 text-xs mt-1">Silakan tambahkan data asisten ke database</p>
                </div>
                <?php else: ?>
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 max-w-md mx-auto">
                    <i class="fas fa-times-circle text-red-600 text-lg mb-2"></i>
                    <p class="text-red-800 font-medium text-sm">Gagal terhubung ke database</p>
                    <p class="text-red-600 text-xs mt-1">Periksa konfigurasi database</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-gray-300">
                © 2024 Portal LSTARS - Universitas Sebelas Maret. All rights reserved.
            </p>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        // Search functionality
        document.getElementById('searchInput')?.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('#asistenTableBody tr');
            let visibleCount = 0;
            
            tableRows.forEach(row => {
                // Skip placeholder rows
                if (row.querySelector('td[colspan]')) {
                    return;
                }
                
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Database Asisten page loaded');
            console.log('Connection status: <?php echo $connection_status; ?>');
            console.log('Total data: <?php echo $total_asisten; ?>');
        });
    </script>
</body>
</html>

<?php
// Tutup koneksi jika ada
if (isset($koneksi)) {
    $koneksi->close();
}
?>