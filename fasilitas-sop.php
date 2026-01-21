<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fasilitas & SOP - LSTARS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .card-hover {
            transition: all 0.3s ease;
            transform: translateY(0);
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .tab-button.active {
            background: linear-gradient(135deg, #3B82F6, #1D4ED8);
            color: white;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-gradient-to-r from-blue-500 to-purple-500 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-clipboard-list text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold">Fasilitas & SOP</h1>
                        <p class="text-blue-100 opacity-90">Portal LSTARS - Teknik Industri UNS</p>
                    </div>
                </div>
                <a href="guest-dashboard.php" class="bg-white hover:bg-gray-50 text-purple-600 font-medium px-6 py-2 rounded-lg flex items-center transition-all shadow-lg">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Tab Navigation -->
        <div class="mb-8">
            <div class="flex flex-wrap gap-2 bg-white p-2 rounded-lg shadow-sm">
                <button onclick="showTab('fasilitas')" class="tab-button active px-6 py-3 rounded-lg font-medium transition-all">
                    <i class="fas fa-tools mr-2"></i>Fasilitas
                </button>
                <button onclick="showTab('sop')" class="tab-button px-6 py-3 rounded-lg font-medium transition-all">
                    <i class="fas fa-clipboard-list mr-2"></i>SOP
                </button>
            </div>
        </div>

        <!-- Fasilitas Tab -->
        <div id="fasilitas" class="tab-content active">
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Fasilitas Laboratorium</h2>
                <p class="text-lg text-gray-600 mb-8">LSTARS dilengkapi dengan fasilitas modern untuk mendukung kegiatan penelitian dan praktikum</p>
            </div>

            <!-- Fasilitas Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                <!-- Komputer & Software -->
                <div class="card-hover bg-white rounded-xl p-6 shadow-lg">
                    <div class="w-16 h-16 bg-blue-500 rounded-xl flex items-center justify-center mb-4">
                        <i class="fas fa-desktop text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Komputer & Software</h3>
                    <ul class="text-gray-600 space-y-2">
                        <li>• 30 Unit PC High Performance</li>
                        <li>• Software MATLAB</li>
                        <li>• AutoCAD</li>
                        <li>• Arena Simulation</li>
                        <li>• Microsoft Office Suite</li>
                        <li>• SPSS Statistical Software</li>
                    </ul>
                </div>

                <!-- Peralatan Pengukuran -->
                <div class="card-hover bg-white rounded-xl p-6 shadow-lg">
                    <div class="w-16 h-16 bg-green-500 rounded-xl flex items-center justify-center mb-4">
                        <i class="fas fa-ruler-combined text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Peralatan Pengukuran</h3>
                    <ul class="text-gray-600 space-y-2">
                        <li>• Stopwatch Digital (50 unit)</li>
                        <li>• Meteran & Mistar</li>
                        <li>• Timbangan Digital</li>
                        <li>• Caliper Digital</li>
                        <li>• Thermometer Digital</li>
                        <li>• Sound Level Meter</li>
                    </ul>
                </div>

                <!-- Ergonomi -->
                <div class="card-hover bg-white rounded-xl p-6 shadow-lg">
                    <div class="w-16 h-16 bg-purple-500 rounded-xl flex items-center justify-center mb-4">
                        <i class="fas fa-user-check text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Peralatan Ergonomi</h3>
                    <ul class="text-gray-600 space-y-2">
                        <li>• Anthropometer Kit</li>
                        <li>• Kursi Kerja Adjustable</li>
                        <li>• Meja Kerja Ergonomis</li>
                        <li>• Eye Tracker</li>
                        <li>• EMG (Electromyography)</li>
                        <li>• Force Gauge</li>
                    </ul>
                </div>

                <!-- Sistem Produksi -->
                <div class="card-hover bg-white rounded-xl p-6 shadow-lg">
                    <div class="w-16 h-16 bg-orange-500 rounded-xl flex items-center justify-center mb-4">
                        <i class="fas fa-cogs text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Sistem Produksi</h3>
                    <ul class="text-gray-600 space-y-2">
                        <li>• Mini Assembly Line</li>
                        <li>• Conveyor Belt System</li>
                        <li>• Quality Control Station</li>
                        <li>• Material Handling Equipment</li>
                        <li>• Warehouse Simulation Kit</li>
                        <li>• RFID System</li>
                    </ul>
                </div>

                <!-- Audio Visual -->
                <div class="card-hover bg-white rounded-xl p-6 shadow-lg">
                    <div class="w-16 h-16 bg-red-500 rounded-xl flex items-center justify-center mb-4">
                        <i class="fas fa-video text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Audio Visual</h3>
                    <ul class="text-gray-600 space-y-2">
                        <li>• Projector HD (3 unit)</li>
                        <li>• Smart Board Interactive</li>
                        <li>• Sound System</li>
                        <li>• Video Camera</li>
                        <li>• Wireless Microphone</li>
                        <li>• Lighting System</li>
                    </ul>
                </div>

                <!-- Peralatan Keselamatan -->
                <div class="card-hover bg-white rounded-xl p-6 shadow-lg">
                    <div class="w-16 h-16 bg-yellow-500 rounded-xl flex items-center justify-center mb-4">
                        <i class="fas fa-shield-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Keselamatan</h3>
                    <ul class="text-gray-600 space-y-2">
                        <li>• Fire Extinguisher</li>
                        <li>• First Aid Kit</li>
                        <li>• Emergency Exit Signs</li>
                        <li>• Safety Helmet</li>
                        <li>• Safety Vest</li>
                        <li>• Eye Protection</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- SOP Tab -->
        <div id="sop" class="tab-content">
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Standard Operating Procedures</h2>
                <p class="text-lg text-gray-600 mb-8">Panduan prosedur standar untuk memastikan keamanan dan efektivitas penggunaan laboratorium</p>
            </div>

            <!-- SOP Sections -->
            <div class="space-y-8">
                <!-- Prosedur Umum -->
                <div class="bg-white rounded-xl p-8 shadow-lg">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-list-check text-white text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-semibold">Prosedur Umum Laboratorium</h3>
                    </div>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-semibold text-lg mb-3 text-green-600">Sebelum Masuk Lab:</h4>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700">
                                <li>Registrasi di buku tamu</li>
                                <li>Mengenakan pakaian yang sesuai</li>
                                <li>Mematikan handphone atau mode silent</li>
                                <li>Mencuci tangan</li>
                                <li>Menggunakan APD jika diperlukan</li>
                            </ol>
                        </div>
                        <div>
                            <h4 class="font-semibold text-lg mb-3 text-red-600">Setelah Selesai:</h4>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700">
                                <li>Mematikan semua peralatan</li>
                                <li>Membersihkan area kerja</li>
                                <li>Mengembalikan peralatan ke tempatnya</li>
                                <li>Mengisi logbook kegiatan</li>
                                <li>Keluar dengan tertib</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Penggunaan Komputer -->
                <div class="bg-white rounded-xl p-8 shadow-lg">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-computer text-white text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-semibold">SOP Penggunaan Komputer</h3>
                    </div>
                    <div class="space-y-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-semibold mb-2">Sebelum Menggunakan:</h4>
                            <ul class="list-disc list-inside space-y-1 text-gray-700">
                                <li>Periksa kondisi fisik komputer</li>
                                <li>Pastikan keyboard dan mouse berfungsi</li>
                                <li>Login dengan akun yang telah diberikan</li>
                                <li>Jangan mengubah setting sistem</li>
                            </ul>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-semibold mb-2">Selama Penggunaan:</h4>
                            <ul class="list-disc list-inside space-y-1 text-gray-700">
                                <li>Simpan pekerjaan secara berkala</li>
                                <li>Jangan install software tanpa izin</li>
                                <li>Hindari membuka situs yang tidak relevan</li>
                                <li>Laporkan jika ada masalah teknis</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Keselamatan Kerja -->
                <div class="bg-white rounded-xl p-8 shadow-lg">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-red-500 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-hard-hat text-white text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-semibold">SOP Keselamatan Kerja</h3>
                    </div>
                    <div class="grid md:grid-cols-3 gap-6">
                        <div class="bg-red-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-red-700 mb-3">Larangan:</h4>
                            <ul class="list-disc list-inside space-y-1 text-red-600">
                                <li>Makan dan minum di lab</li>
                                <li>Merokok di area lab</li>
                                <li>Berlari di dalam lab</li>
                                <li>Menggunakan peralatan tanpa supervisi</li>
                            </ul>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-yellow-700 mb-3">Wajib:</h4>
                            <ul class="list-disc list-inside space-y-1 text-yellow-600">
                                <li>Menggunakan APD</li>
                                <li>Mengikuti instruksi asisten</li>
                                <li>Melaporkan kecelakaan</li>
                                <li>Menjaga kebersihan</li>
                            </ul>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-green-700 mb-3">Darurat:</h4>
                            <ul class="list-disc list-inside space-y-1 text-green-600">
                                <li>Hubungi petugas lab</li>
                                <li>Gunakan jalur evakuasi</li>
                                <li>Titik kumpul di parking area</li>
                                <li>Ikuti instruksi petugas</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Peminjaman Alat -->
                <div class="bg-white rounded-xl p-8 shadow-lg">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-hand-holding text-white text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-semibold">SOP Peminjaman Peralatan</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border p-3 text-left font-semibold">Langkah</th>
                                    <th class="border p-3 text-left font-semibold">Prosedur</th>
                                    <th class="border p-3 text-left font-semibold">Dokumen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="border p-3 font-medium">1. Pengajuan</td>
                                    <td class="border p-3">Isi form peminjaman dengan lengkap</td>
                                    <td class="border p-3">Form peminjaman + KTM</td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <td class="border p-3 font-medium">2. Verifikasi</td>
                                    <td class="border p-3">Petugas memeriksa ketersediaan alat</td>
                                    <td class="border p-3">Kartu inventaris</td>
                                </tr>
                                <tr>
                                    <td class="border p-3 font-medium">3. Serah Terima</td>
                                    <td class="border p-3">Peminjam menerima dan memeriksa kondisi alat</td>
                                    <td class="border p-3">Berita acara serah terima</td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <td class="border p-3 font-medium">4. Pengembalian</td>
                                    <td class="border p-3">Kembalikan dalam kondisi baik sesuai jadwal</td>
                                    <td class="border p-3">Konfirmasi pengembalian</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Kontak Darurat -->
                <div class="bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl p-8">
                    <div class="text-center">
                        <i class="fas fa-phone-alt text-4xl mb-4"></i>
                        <h3 class="text-2xl font-bold mb-4">Kontak Darurat</h3>
                        <div class="grid md:grid-cols-3 gap-6 text-center">
                            <div>
                                <h4 class="font-semibold mb-2">Keamanan Kampus</h4>
                                <p class="text-xl">Ext. 2222</p>
                            </div>
                            <div>
                                <h4 class="font-semibold mb-2">Petugas Lab</h4>
                                <p class="text-xl">Ext. 3456</p>
                            </div>
                            <div>
                                <h4 class="font-semibold mb-2">UKS/Medis</h4>
                                <p class="text-xl">Ext. 1111</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p>&copy; 2024 LSTARS - Laboratorium Sistem Teknik dan Analisis Rekayasa Sistem. All rights reserved.</p>
        </div>
    </footer>

    <script>
        function showTab(tabName) {
            // Hide all tab contents
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => {
                content.classList.remove('active');
            });

            // Remove active class from all buttons
            const tabButtons = document.querySelectorAll('.tab-button');
            tabButtons.forEach(button => {
                button.classList.remove('active');
            });

            // Show selected tab content
            document.getElementById(tabName).classList.add('active');

            // Add active class to clicked button
            event.target.closest('.tab-button').classList.add('active');
        }
    </script>
</body>
</html>