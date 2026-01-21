<?php
// jadwal-praktikum.php - Minimal Design
require_once 'config/database.php';
require_once 'includes/header.php';

// Ambil data untuk dropdown filter
try {
    $stmt_semester = $pdo->query("SELECT DISTINCT semester FROM jadwal_praktikum ORDER BY semester");
    $semesters = $stmt_semester->fetchAll(PDO::FETCH_COLUMN);
    
    $stmt_kelas = $pdo->query("SELECT DISTINCT kelas FROM jadwal_praktikum ORDER BY kelas");
    $kelas_list = $stmt_kelas->fetchAll(PDO::FETCH_COLUMN);
    
    $hari_list = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];
    
} catch(PDOException $e) {
    $error_message = "Error: " . $e->getMessage();
    $semesters = [];
    $kelas_list = [];
    $hari_list = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Praktikum - LSTARS Portal</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .loading { display: none; }
        .loading.show { display: block; }
        .table-row:hover { background-color: #f8fafc; }
    </style>
</head>
<body class="bg-gray-50">

<!-- Header Simple -->
<nav class="bg-gradient-to-r from-blue-600 to-purple-700 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center">
                <i class="fas fa-calendar-alt text-2xl mr-3"></i>
                <div>
                    <h1 class="text-xl font-bold">Jadwal Praktikum</h1>
                    <p class="text-sm opacity-90">Portal LSTARS - Teknik Industri UNS</p>
                </div>
            </div>
            <button onclick="window.location.href='guest-dashboard.php'" class="bg-white text-blue-600 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Beranda
            </button>
        </div>
    </div>
</nav>

<!-- Main Content -->
<section class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Error Message -->
        <?php if (isset($error_message)): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
        <?php endif; ?>
        
        <!-- Filter Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter Jadwal</h3>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <select id="semesterFilter" class="border border-gray-300 rounded-lg px-4 py-2 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Semester</option>
                    <?php foreach($semesters as $semester): ?>
                        <option value="<?php echo $semester; ?>">Semester <?php echo $semester; ?></option>
                    <?php endforeach; ?>
                </select>
                
                <select id="kelasFilter" class="border border-gray-300 rounded-lg px-4 py-2 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Kelas</option>
                    <?php foreach($kelas_list as $kelas): ?>
                        <option value="<?php echo $kelas; ?>">Kelas <?php echo $kelas; ?></option>
                    <?php endforeach; ?>
                </select>
                
                <select id="hariFilter" class="border border-gray-300 rounded-lg px-4 py-2 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Hari</option>
                    <?php foreach($hari_list as $hari): ?>
                        <option value="<?php echo $hari; ?>"><?php echo ucfirst($hari); ?></option>
                    <?php endforeach; ?>
                </select>
                
                <input type="text" id="searchFilter" placeholder="Cari mata kuliah..." 
                       class="border border-gray-300 rounded-lg px-4 py-2 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                
                <button id="resetFilter" class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-undo mr-2"></i>Reset
                </button>
            </div>
        </div>

        <!-- Stats -->
        <div class="flex justify-between items-center mb-6">
            <div class="text-gray-600">
                <i class="fas fa-info-circle mr-2"></i>
                Total: <span id="totalJadwal" class="font-semibold text-gray-900">0</span> jadwal praktikum
            </div>
            <div class="flex space-x-2">
                <button id="downloadExcel" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors text-sm">
                    <i class="fas fa-file-excel mr-2"></i>Export Excel
                </button>
                <button id="downloadPDF" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors text-sm">
                    <i class="fas fa-file-pdf mr-2"></i>Export PDF
                </button>
            </div>
        </div>

        <!-- Loading Indicator -->
        <div id="loadingIndicator" class="loading text-center py-8">
            <i class="fas fa-spinner fa-spin text-3xl text-gray-400"></i>
            <p class="mt-2 text-gray-500">Memuat jadwal praktikum...</p>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Mata Kuliah
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Semester/Kelas
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jadwal
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ruangan
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Dosen
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Mahasiswa
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody id="jadwalTableBody" class="bg-white divide-y divide-gray-200">
                        <!-- Data akan dimuat di sini -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- No Data Message -->
        <div id="noDataMessage" class="hidden text-center py-12">
            <i class="fas fa-calendar-times text-4xl text-gray-400 mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-600 mb-2">Tidak ada jadwal ditemukan</h3>
            <p class="text-gray-500">Coba ubah filter atau reset pencarian Anda</p>
        </div>

    </div>
</section>

<!-- Footer -->
<footer class="bg-white border-t border-gray-200 py-8 mt-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center text-gray-500">
            <p>&copy; 2024 LSTARS - Laboratorium Sistem Teknik & Analisis Riset Simulasi</p>
        </div>
    </div>
</footer>

<script>
// Global variables
let currentData = [];

// Load jadwal dari database
async function loadJadwal() {
    document.getElementById('loadingIndicator').classList.add('show');
    
    try {
        const filters = {
            semester: document.getElementById('semesterFilter').value,
            kelas: document.getElementById('kelasFilter').value,
            hari: document.getElementById('hariFilter').value,
            search: document.getElementById('searchFilter').value
        };
        
        const queryParams = new URLSearchParams();
        Object.entries(filters).forEach(([key, value]) => {
            if (value) queryParams.append(key, value);
        });
        
        const response = await fetch(`api/jadwal-praktikum.php?${queryParams}`);
        const result = await response.json();
        
        if (result.success) {
            currentData = result.data;
            updateDisplay();
        } else {
            showError('Gagal memuat data jadwal: ' + (result.message || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error:', error);
        showError('Terjadi kesalahan saat memuat data: ' + error.message);
    } finally {
        document.getElementById('loadingIndicator').classList.remove('show');
    }
}

// Update tampilan tabel
function updateDisplay() {
    document.getElementById('totalJadwal').textContent = currentData.length;
    
    if (currentData.length === 0) {
        document.getElementById('noDataMessage').classList.remove('hidden');
        document.querySelector('.bg-white.rounded-lg.shadow-sm.border').classList.add('hidden');
    } else {
        document.getElementById('noDataMessage').classList.add('hidden');
        document.querySelector('.bg-white.rounded-lg.shadow-sm.border').classList.remove('hidden');
        renderTable();
    }
}

// Render tabel
function renderTable() {
    const tbody = document.getElementById('jadwalTableBody');
    tbody.innerHTML = '';
    
    currentData.forEach((jadwal, index) => {
        const row = document.createElement('tr');
        row.className = 'table-row';
        
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">${jadwal.mata_kuliah}</div>
                <div class="text-sm text-gray-500">${jadwal.kode_mk || '-'}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">Semester ${jadwal.semester}</div>
                <div class="text-sm text-gray-500">Kelas ${jadwal.kelas}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">${ucfirst(jadwal.hari)}</div>
                <div class="text-sm text-gray-500">${jadwal.jam_praktikum}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">${jadwal.ruangan}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">${jadwal.nama_dosen || 'Belum ditentukan'}</div>
                <div class="text-sm text-gray-500">${jadwal.nama_asisten || 'Belum ditentukan'}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">${jadwal.jumlah_mahasiswa}/${jadwal.kapasitas}</div>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: ${(jadwal.jumlah_mahasiswa/jadwal.kapasitas)*100}%"></div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${jadwal.status === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}">
                    ${ucfirst(jadwal.status)}
                </span>
            </td>
        `;
        
        tbody.appendChild(row);
    });
}

// Helper functions
function ucfirst(str) {
    return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
}

function showError(message) {
    alert(message);
}

// Event listeners
document.getElementById('semesterFilter').addEventListener('change', loadJadwal);
document.getElementById('kelasFilter').addEventListener('change', loadJadwal);
document.getElementById('hariFilter').addEventListener('change', loadJadwal);

let searchTimeout;
document.getElementById('searchFilter').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(loadJadwal, 500);
});

document.getElementById('resetFilter').addEventListener('click', function() {
    document.getElementById('semesterFilter').value = '';
    document.getElementById('kelasFilter').value = '';
    document.getElementById('hariFilter').value = '';
    document.getElementById('searchFilter').value = '';
    loadJadwal();
});

// Download functions
document.getElementById('downloadPDF').addEventListener('click', function() {
    window.open('exports/jadwal-csv.php', '_blank');
});

document.getElementById('downloadExcel').addEventListener('click', function() {
    window.open('exports/jadwal-excel.php', '_blank');
});

// Load initial data
document.addEventListener('DOMContentLoaded', function() {
    loadJadwal();
});
</script>

</body>
</html>