<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Inventory - Portal LSTARS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .hero-gradient { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    </style>
</head>
<body class="bg-gray-50">
    <header class="hero-gradient shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-boxes text-white text-2xl"></i>
                    </div>
                    <div class="text-white">
                        <h1 class="text-2xl font-bold">Database Inventory</h1>
                        <p class="text-purple-200">Portal LSTARS - Teknik Industri UNS</p>
                    </div>
                </div>
                <a href="guest-dashboard.php" class="bg-white text-purple-600 px-6 py-2 rounded-lg font-medium hover:bg-purple-50 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali ke Beranda
                </a>
            </div>
        </div>
    </header>

    <main class="min-h-screen py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Database Inventory LSTARS</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Akses informasi lengkap inventaris peralatan dan barang laboratorium
                </p>
            </div>
             <div class="mb-8 max-w-md mx-auto">
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="Cari ID, nama, atau kategori..." 
                           class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                     <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-boxes-stacked mr-2 text-purple-600"></i>
                        Daftar Inventory
                    </h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">ID Barang</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Nama Inventory</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider">Jumlah</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider">Ketersediaan</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider">Kondisi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="inventoryTableBody">
                            </tbody>
                    </table>
                </div>
                 <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 text-sm text-gray-600">
                    Total Item Ditampilkan: <span id="totalCount" class="font-bold">0</span>.
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-gray-800 text-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-gray-400">&copy; 2025 Portal LSTARS - Universitas Sebelas Maret.</p>
        </div>
    </footer>

    <script>
        // Variabel global untuk menyimpan semua data dari database
        let allInventoryData = [];

        // Fungsi untuk menampilkan data di tabel
        function displayInventoryData(data) {
            const tableBody = document.getElementById('inventoryTableBody');
            const totalCount = document.getElementById('totalCount');
            
            if (!data || data.length === 0) {
                tableBody.innerHTML = `<tr><td colspan="6" class="text-center py-12 text-gray-500">Data tidak ditemukan.</td></tr>`;
                totalCount.textContent = '0';
                return;
            }

            tableBody.innerHTML = data.map((item, index) => {
                // Logika Ketersediaan (dari database)
                const tersedia = parseInt(item.jumlah_tersedia);
                let ketersediaanText, ketersediaanClass;
                if (tersedia <= 0) {
                    ketersediaanText = 'Habis';
                    ketersediaanClass = 'bg-red-100 text-red-800';
                } else {
                    ketersediaanText = `Tersedia`;
                    ketersediaanClass = 'bg-green-100 text-green-800';
                }

                // Logika Kondisi (dari database)
                let kondisiText = item.status;
                let kondisiClass = 'bg-gray-100 text-gray-800';
                if (item.status === 'Tersedia' || item.status === 'Baik') {
                    kondisiText = 'Baik';
                    kondisiClass = 'bg-green-100 text-green-800';
                } else if (item.status === 'Rusak') {
                    kondisiText = 'Rusak';
                    kondisiClass = 'bg-red-100 text-red-800';
                }

                // Logika Ikon (dari database)
                let iconClass = 'fas fa-box';
                const kategori = item.kategori ? item.kategori.toLowerCase() : '';
                if (kategori.includes('ukur')) {
                    iconClass = 'fas fa-ruler-combined';
                } else if (kategori.includes('elektronik')) {
                    iconClass = 'fas fa-bolt';
                } else if (kategori.includes('gelas')) {
                    iconClass = 'fas fa-flask';
                }

                // Menggunakan struktur HTML asli Anda untuk tampilan yang detail
                return `
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${index + 1}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${item.kode_alat}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                    <i class="${iconClass} text-orange-600"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">${item.nama_alat}</div>
                                    <div class="text-sm text-gray-500">${item.kategori || 'Lain-lain'}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                ${item.jumlah_total} unit
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${ketersediaanClass}">
                                ${ketersediaanText} (${tersedia})
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${kondisiClass}">
                                ${kondisiText}
                            </span>
                        </td>
                    </tr>
                `;
            }).join('');
            
            totalCount.textContent = data.length;
        }
        
        // [AKTIFKAN] Fungsi untuk mengambil data dari server
        async function loadInventoryData() {
            const tableBody = document.getElementById('inventoryTableBody');
            tableBody.innerHTML = `<tr><td colspan="6" class="text-center py-12 text-gray-500">Memuat data...</td></tr>`;
            try {
                // Pastikan nama file PHP ini benar dan ada di folder /api/
                const response = await fetch('api/get_inventory.php');
                const data = await response.json();
                
                if (data.error) throw new Error(data.error);

                allInventoryData = data;
                displayInventoryData(allInventoryData);
            } catch (error) {
                console.error('Gagal mengambil data:', error);
                tableBody.innerHTML = `<tr><td colspan="6" class="text-center py-12 text-red-600">Gagal memuat data. Periksa koneksi atau file API.</td></tr>`;
            }
        }

        // Fungsi untuk filter pencarian
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const filteredData = allInventoryData.filter(item => 
                item.nama_alat.toLowerCase().includes(searchTerm) ||
                item.kode_alat.toLowerCase().includes(searchTerm) ||
                (item.kategori && item.kategori.toLowerCase().includes(searchTerm))
            );
            displayInventoryData(filteredData);
        });

        // [AKTIFKAN] Memanggil fungsi utama saat halaman selesai dimuat
        document.addEventListener('DOMContentLoaded', loadInventoryData);
    </script>
</body>
</html>