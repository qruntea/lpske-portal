<?php
// database-dosen.php - File mandiri dengan koneksi database sendiri

// Konfigurasi database
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'lpske-pkl';

// Inisialisasi variabel
$dosen_data = array();
$total_dosen = 0;
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
    
    // Query untuk mengambil data dosen (sesuai dengan struktur tabel yang ada)
    $query = "SELECT nidn, nama_dosen, gelar_depan, gelar_belakang, nip, homebase_prodi, foto FROM dosen ORDER BY nama_dosen ASC";
    $result = $koneksi->query($query);
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            // Gabungkan nama lengkap dengan gelar
            $nama_lengkap = trim(($row['gelar_depan'] ? $row['gelar_depan'] . ' ' : '') . 
                                $row['nama_dosen'] . 
                                ($row['gelar_belakang'] ? ', ' . $row['gelar_belakang'] : ''));
            
            $dosen_data[] = array(
                'nidn' => $row['nidn'],
                'nama' => $nama_lengkap,
                'nama_dosen' => $row['nama_dosen'],
                'gelar_depan' => $row['gelar_depan'],
                'gelar_belakang' => $row['gelar_belakang'],
                'nip' => $row['nip'],
                'homebase_prodi' => $row['homebase_prodi'],
                'foto' => $row['foto']
            );
        }
        $total_dosen = count($dosen_data);
    } else {
        throw new Exception("Query gagal: " . $koneksi->error);
    }
    
} catch (Exception $e) {
    $error_message = $e->getMessage();
    error_log("Database Dosen Error: " . $error_message);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Dosen - Portal LSTARS</title>
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
        .modal-backdrop {
            backdrop-filter: blur(4px);
        }
        .modal-content {
            animation: modalSlideIn 0.3s ease-out;
        }
        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: scale(0.9) translateY(-10px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }
        .dosen-name-link {
            cursor: pointer;
            transition: color 0.2s ease;
        }
        .dosen-name-link:hover {
            color: #2563eb;
            text-decoration: underline;
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
                        <i class="fas fa-user-tie text-white text-2xl"></i>
                    </div>
                    <div class="text-white">
                        <h1 class="text-2xl font-bold">Database Dosen</h1>
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
            | Total Data: <?php echo $total_dosen; ?>
        </div>
    </div>

    <!-- Main Content -->
    <div class="min-h-screen py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Page Description -->
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Database Dosen LSTARS</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Akses informasi lengkap profil dosen dan bidang keahlian. <span class="text-blue-600 font-medium">Klik nama dosen</span> untuk melihat detail lengkap.
                </p>
            </div>

            <!-- Search Bar -->
            <?php if ($total_dosen > 0): ?>
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
                    <div class="w-full sm:w-auto flex-1 max-w-md">
                        <div class="relative">
                            <input type="text" id="searchInput" placeholder="Cari nama dosen, NIDN, atau NIP..." 
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
                <div class="px-6 py-4 bg-blue-50 border-b border-blue-100">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-blue-800">
                            <i class="fas fa-user-tie mr-2"></i>
                            Daftar Dosen Laboratorium LSTARS
                        </h3>
                        <span class="text-sm text-blue-600">Total: <span id="totalCount"><?php echo $total_dosen; ?></span> dosen</span>
                    </div>
                </div>
                
                <!-- Table Content -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIDN</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Dosen</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIP</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Homebase Prodi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Foto</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="dosenTableBody">
                            <?php if ($connection_status === 'disconnected'): ?>
                            <!-- Error State -->
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-red-500">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-exclamation-triangle text-4xl text-red-300 mb-4"></i>
                                        <p class="text-lg font-medium mb-2">Koneksi Database Gagal</p>
                                        <p class="text-sm"><?php echo htmlspecialchars($error_message); ?></p>
                                    </div>
                                </td>
                            </tr>
                            <?php elseif (empty($dosen_data)): ?>
                            <!-- Empty State -->
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-database text-4xl text-gray-300 mb-4"></i>
                                        <p class="text-lg font-medium mb-2">Belum ada data dosen</p>
                                        <p class="text-sm">Silakan tambahkan data dosen ke database</p>
                                    </div>
                                </td>
                            </tr>
                            <?php else: ?>
                                <!-- Data Rows -->
                                <?php foreach ($dosen_data as $index => $dosen): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $index + 1; ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($dosen['nidn'] ?: '-'); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                                    <?php if ($dosen['foto']): ?>
                                                        <img src="<?php echo htmlspecialchars($dosen['foto']); ?>" alt="Foto" class="w-10 h-10 rounded-full object-cover">
                                                    <?php else: ?>
                                                        <i class="fas fa-user text-blue-600"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900 dosen-name-link" 
                                                         onclick="showDosenDetail(<?php echo htmlspecialchars(json_encode($dosen)); ?>)">
                                                        <?php echo htmlspecialchars($dosen['nama']); ?>
                                                        <i class="fas fa-external-link-alt text-xs ml-1 text-blue-500"></i>
                                                    </div>
                                                    <div class="text-sm text-gray-500">Teknik Industri</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($dosen['nip'] ?: '-'); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <?php echo htmlspecialchars($dosen['homebase_prodi'] ?: 'Tidak Diketahui'); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php if ($dosen['foto']): ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i class="fas fa-check mr-1"></i>Ada
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    <i class="fas fa-times mr-1"></i>Tidak Ada
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($total_dosen > 0): ?>
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
                                Menampilkan <span class="font-medium">1</span> sampai <span class="font-medium"><?php echo $total_dosen; ?></span> dari 
                                <span class="font-medium"><?php echo $total_dosen; ?></span> dosen
                            </p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Status Message -->
            <div class="mt-8 text-center">
                <?php if ($connection_status === 'connected' && $total_dosen > 0): ?>
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 max-w-md mx-auto">
                    <i class="fas fa-check-circle text-green-600 text-lg mb-2"></i>
                    <p class="text-green-800 font-medium text-sm">Database terhubung dengan sukses!</p>
                    <p class="text-green-600 text-xs mt-1">Menampilkan <?php echo $total_dosen; ?> data dosen</p>
                </div>
                <?php elseif ($connection_status === 'connected' && $total_dosen === 0): ?>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 max-w-md mx-auto">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-lg mb-2"></i>
                    <p class="text-yellow-800 font-medium text-sm">Database terhubung, tapi belum ada data</p>
                    <p class="text-yellow-600 text-xs mt-1">Silakan tambahkan data dosen ke database</p>
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

    <!-- Modal Detail Dosen -->
    <div id="dosenModal" class="fixed inset-0 z-50 hidden overflow-y-auto modal-backdrop bg-black bg-opacity-50">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity" onclick="closeDosenModal()"></div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-6 modal-content">
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900">
                        <i class="fas fa-user-tie text-blue-600 mr-2"></i>
                        Detail Dosen
                    </h3>
                    <button onclick="closeDosenModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Modal Content -->
                <div class="mt-6">
                    <!-- Profile Section -->
                    <div class="flex items-start space-x-6 mb-6">
                        <div class="flex-shrink-0">
                            <div id="modalFoto" class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-blue-600 text-2xl"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h4 id="modalNama" class="text-2xl font-bold text-gray-900 mb-2">-</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-id-card w-5 mr-2"></i>
                                    <span class="font-medium">NIDN:</span>
                                    <span id="modalNIDN" class="ml-2">-</span>
                                </div>
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-id-badge w-5 mr-2"></i>
                                    <span class="font-medium">NIP:</span>
                                    <span id="modalNIP" class="ml-2">-</span>
                                </div>
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-building w-5 mr-2"></i>
                                    <span class="font-medium">Homebase:</span>
                                    <span id="modalHomebase" class="ml-2">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Info Tabs -->
                    <div class="border-t border-gray-200 pt-6">
                        <div class="flex space-x-1 mb-4">
                            <button onclick="switchTab('biodata')" class="tab-button px-4 py-2 text-sm font-medium rounded-lg bg-blue-100 text-blue-700" data-tab="biodata">
                                <i class="fas fa-user mr-1"></i>Biodata
                            </button>
                            <button onclick="switchTab('keahlian')" class="tab-button px-4 py-2 text-sm font-medium rounded-lg text-gray-500 hover:text-gray-700" data-tab="keahlian">
                                <i class="fas fa-cogs mr-1"></i>Keahlian
                            </button>
                            <button onclick="switchTab('penelitian')" class="tab-button px-4 py-2 text-sm font-medium rounded-lg text-gray-500 hover:text-gray-700" data-tab="penelitian">
                                <i class="fas fa-flask mr-1"></i>Penelitian
                            </button>
                            <button onclick="switchTab('kontak')" class="tab-button px-4 py-2 text-sm font-medium rounded-lg text-gray-500 hover:text-gray-700" data-tab="kontak">
                                <i class="fas fa-envelope mr-1"></i>Kontak
                            </button>
                        </div>

                        <!-- Tab Content -->
                        <div id="biodata-content" class="tab-content">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h5 class="font-semibold text-gray-900 mb-3">Informasi Biodata</h5>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Nama Lengkap:</span>
                                        <span id="biodataNama" class="font-medium">-</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Gelar Depan:</span>
                                        <span id="biodataGelarDepan" class="font-medium">-</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Gelar Belakang:</span>
                                        <span id="biodataGelarBelakang" class="font-medium">-</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Program Studi:</span>
                                        <span class="font-medium">Teknik Industri</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Fakultas:</span>
                                        <span class="font-medium">Teknik</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="keahlian-content" class="tab-content hidden">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h5 class="font-semibold text-gray-900 mb-3">Bidang Keahlian</h5>
                                <div class="space-y-2">
                                    <div class="flex flex-wrap gap-2">
                                        <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">Sistem Informasi Manajemen</span>
                                        <span class="px-3 py-1 bg-green-100 text-green-800 text-xs rounded-full">Optimasi Sistem</span>
                                        <span class="px-3 py-1 bg-purple-100 text-purple-800 text-xs rounded-full">Manajemen Kualitas</span>
                                        <span class="px-3 py-1 bg-orange-100 text-orange-800 text-xs rounded-full">Ergonomi</span>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-3">
                                        Memiliki keahlian dalam pengembangan sistem informasi terintegrasi, optimasi proses industri, 
                                        dan implementasi standar kualitas internasional.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div id="penelitian-content" class="tab-content hidden">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h5 class="font-semibold text-gray-900 mb-3">Penelitian Terkini</h5>
                                <div class="space-y-3">
                                    <div class="border-l-4 border-blue-500 pl-3">
                                        <h6 class="font-medium text-gray-900">Optimasi Supply Chain Management</h6>
                                        <p class="text-sm text-gray-600">Penelitian tentang efisiensi rantai pasok menggunakan algoritma genetika</p>
                                        <span class="text-xs text-blue-600">2024 - Ongoing</span>
                                    </div>
                                    <div class="border-l-4 border-green-500 pl-3">
                                        <h6 class="font-medium text-gray-900">Digital Transformation in Manufacturing</h6>
                                        <p class="text-sm text-gray-600">Implementasi Industry 4.0 pada industri manufaktur Indonesia</p>
                                        <span class="text-xs text-green-600">2023 - 2024</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="kontak-content" class="tab-content hidden">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h5 class="font-semibold text-gray-900 mb-3">Informasi Kontak</h5>
                                <div class="space-y-3">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-envelope text-blue-600"></i>
                                        <span class="text-sm">nama.dosen@staff.uns.ac.id</span>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-phone text-green-600"></i>
                                        <span class="text-sm">+62 271 632110</span>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-map-marker-alt text-red-600"></i>
                                        <span class="text-sm">Gedung Teknik Industri, Fakultas Teknik UNS</span>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-clock text-orange-600"></i>
                                        <span class="text-sm">Senin - Jumat: 08:00 - 16:00 WIB</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="mt-6 flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <button onclick="closeDosenModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                        Tutup
                    </button>
                    <button class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-envelope mr-2"></i>Hubungi Dosen
                    </button>
                </div>
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
        // Global variables
        let currentDosenData = null;

        // Search functionality
        document.getElementById('searchInput')?.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('#dosenTableBody tr');
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

        // Modal functions
        function showDosenDetail(dosenData) {
            currentDosenData = dosenData;
            
            // Update modal content
            document.getElementById('modalNama').textContent = dosenData.nama || '-';
            document.getElementById('modalNIDN').textContent = dosenData.nidn || '-';
            document.getElementById('modalNIP').textContent = dosenData.nip || '-';
            document.getElementById('modalHomebase').textContent = dosenData.homebase_prodi || 'Tidak Diketahui';
            
            // Update biodata tab
            document.getElementById('biodataNama').textContent = dosenData.nama_dosen || '-';
            document.getElementById('biodataGelarDepan').textContent = dosenData.gelar_depan || '-';
            document.getElementById('biodataGelarBelakang').textContent = dosenData.gelar_belakang || '-';
            
            // Update foto
            const modalFoto = document.getElementById('modalFoto');
            if (dosenData.foto) {
                modalFoto.innerHTML = `<img src="${dosenData.foto}" alt="Foto Dosen" class="w-24 h-24 rounded-full object-cover">`;
            } else {
                modalFoto.innerHTML = '<i class="fas fa-user text-blue-600 text-2xl"></i>';
            }
            
            // Reset to first tab
            switchTab('biodata');
            
            // Show modal
            document.getElementById('dosenModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeDosenModal() {
            document.getElementById('dosenModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            currentDosenData = null;
        }

        function switchTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Remove active class from all tab buttons
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('bg-blue-100', 'text-blue-700');
                button.classList.add('text-gray-500', 'hover:text-gray-700');
            });
            
            // Show selected tab content
            document.getElementById(tabName + '-content').classList.remove('hidden');
            
            // Add active class to selected tab button
            const activeButton = document.querySelector(`[data-tab="${tabName}"]`);
            activeButton.classList.add('bg-blue-100', 'text-blue-700');
            activeButton.classList.remove('text-gray-500', 'hover:text-gray-700');
        }

        // Close modal when clicking outside
        document.getElementById('dosenModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDosenModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !document.getElementById('dosenModal').classList.contains('hidden')) {
                closeDosenModal();
            }
        });

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Database Dosen page loaded');
            console.log('Connection status: <?php echo $connection_status; ?>');
            console.log('Total data: <?php echo $total_dosen; ?>');
            
            // Add click handlers for dosen names
            document.querySelectorAll('.dosen-name-link').forEach(link => {
                link.style.cursor = 'pointer';
            });
        });

        // Prevent modal content click from closing modal
        document.querySelector('.modal-content').addEventListener('click', function(e) {
            e.stopPropagation();
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