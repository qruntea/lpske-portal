<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Story - Portal LSTARS</title>
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
            transform: translateY(-8px);
            box-shadow: 0 20px 40px -5px rgba(0, 0, 0, 0.15), 0 10px 20px -5px rgba(0, 0, 0, 0.1);
        }
        .story-card {
            background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid rgba(226, 232, 240, 0.5);
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
                        <i class="fas fa-graduation-cap text-white text-2xl"></i>
                    </div>
                    <div class="text-white">
                        <h1 class="text-2xl font-bold">Alumni Story</h1>
                        <p class="text-purple-200">Portal LSTARS - Teknik Industri UNS</p>
                    </div>
                </div>
                <a href="guest-dashboard.php" class="bg-white text-purple-600 px-6 py-2 rounded-lg font-medium hover:bg-purple-50 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali ke Beranda
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="min-h-screen py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Page Description -->
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Alumni Story LSTARS</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Cerita inspiratif perjalanan karir alumni laboratorium LSTARS di berbagai industri
                </p>
            </div>

            <!-- Search Bar -->
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
                    <div class="w-full sm:w-auto flex-1 max-w-md">
                        <div class="relative">
                            <input type="text" id="searchInput" placeholder="Cari nama alumni atau perusahaan..." 
                                   class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alumni Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="alumniGrid">
                <!-- Alumni Card 1 -->
                <div class="story-card rounded-xl p-6 card-hover">
                    <div class="flex items-center mb-4">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-user text-blue-600 text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Ahmad Rizki Pratama</h3>
                            <p class="text-blue-600 font-medium">Production Manager</p>
                        </div>
                    </div>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-envelope mr-2 text-blue-500"></i>
                            ahmad.rizki@company.com
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-building mr-2 text-green-500"></i>
                            PT Astra International
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-briefcase mr-2 text-purple-500"></i>
                            Manufacturing & Operations
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-calendar mr-2 text-orange-500"></i>
                            Angkatan 2019
                        </div>
                    </div>

                    <button onclick="viewStory(1)" class="w-full bg-blue-600 text-white py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors">
                        <i class="fas fa-eye mr-2"></i>Lihat Story
                    </button>
                </div>

                <!-- Alumni Card 2 -->
                <div class="story-card rounded-xl p-6 card-hover">
                    <div class="flex items-center mb-4">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-user text-green-600 text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Sari Dewi Lestari</h3>
                            <p class="text-green-600 font-medium">Quality Engineer</p>
                        </div>
                    </div>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-envelope mr-2 text-blue-500"></i>
                            sari.dewi@toyota.co.id
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-building mr-2 text-green-500"></i>
                            Toyota Motor Indonesia
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-briefcase mr-2 text-purple-500"></i>
                            Quality Control & Assurance
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-calendar mr-2 text-orange-500"></i>
                            Angkatan 2018
                        </div>
                    </div>

                    <button onclick="viewStory(2)" class="w-full bg-green-600 text-white py-3 rounded-lg font-medium hover:bg-green-700 transition-colors">
                        <i class="fas fa-eye mr-2"></i>Lihat Story
                    </button>
                </div>

                <!-- Alumni Card 3 -->
                <div class="story-card rounded-xl p-6 card-hover">
                    <div class="flex items-center mb-4">
                        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-user text-purple-600 text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Budi Santoso</h3>
                            <p class="text-purple-600 font-medium">Supply Chain Analyst</p>
                        </div>
                    </div>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-envelope mr-2 text-blue-500"></i>
                            budi.santoso@unilever.com
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-building mr-2 text-green-500"></i>
                            Unilever Indonesia
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-briefcase mr-2 text-purple-500"></i>
                            Supply Chain Management
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-calendar mr-2 text-orange-500"></i>
                            Angkatan 2020
                        </div>
                    </div>

                    <button onclick="viewStory(3)" class="w-full bg-purple-600 text-white py-3 rounded-lg font-medium hover:bg-purple-700 transition-colors">
                        <i class="fas fa-eye mr-2"></i>Lihat Story
                    </button>
                </div>

                <!-- Alumni Card 4 -->
                <div class="story-card rounded-xl p-6 card-hover">
                    <div class="flex items-center mb-4">
                        <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-user text-yellow-600 text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Rina Fitriana</h3>
                            <p class="text-yellow-600 font-medium">Project Manager</p>
                        </div>
                    </div>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-envelope mr-2 text-blue-500"></i>
                            rina.fitriana@telkom.co.id
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-building mr-2 text-green-500"></i>
                            Telkom Indonesia
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-briefcase mr-2 text-purple-500"></i>
                            Digital Transformation
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-calendar mr-2 text-orange-500"></i>
                            Angkatan 2017
                        </div>
                    </div>

                    <button onclick="viewStory(4)" class="w-full bg-yellow-600 text-white py-3 rounded-lg font-medium hover:bg-yellow-700 transition-colors">
                        <i class="fas fa-eye mr-2"></i>Lihat Story
                    </button>
                </div>

                <!-- Alumni Card 5 -->
                <div class="story-card rounded-xl p-6 card-hover">
                    <div class="flex items-center mb-4">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-user text-red-600 text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Andi Prasetyo</h3>
                            <p class="text-red-600 font-medium">Business Analyst</p>
                        </div>
                    </div>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-envelope mr-2 text-blue-500"></i>
                            andi.prasetyo@gojek.com
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-building mr-2 text-green-500"></i>
                            GoTo Group (Gojek)
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-briefcase mr-2 text-purple-500"></i>
                            Product & Strategy
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-calendar mr-2 text-orange-500"></i>
                            Angkatan 2021
                        </div>
                    </div>

                    <button onclick="viewStory(5)" class="w-full bg-red-600 text-white py-3 rounded-lg font-medium hover:bg-red-700 transition-colors">
                        <i class="fas fa-eye mr-2"></i>Lihat Story
                    </button>
                </div>

                <!-- Alumni Card 6 -->
                <div class="story-card rounded-xl p-6 card-hover">
                    <div class="flex items-center mb-4">
                        <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-user text-indigo-600 text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Maya Sari Putri</h3>
                            <p class="text-indigo-600 font-medium">Data Scientist</p>
                        </div>
                    </div>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-envelope mr-2 text-blue-500"></i>
                            maya.sari@shopee.co.id
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-building mr-2 text-green-500"></i>
                            Shopee Indonesia
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-briefcase mr-2 text-purple-500"></i>
                            Data Analytics & ML
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-calendar mr-2 text-orange-500"></i>
                            Angkatan 2019
                        </div>
                    </div>

                    <button onclick="viewStory(6)" class="w-full bg-indigo-600 text-white py-3 rounded-lg font-medium hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-eye mr-2"></i>Lihat Story
                    </button>
                </div>
            </div>

            <!-- Load More Button -->
            <div class="text-center mt-12">
                <button onclick="loadMoreStories()" class="bg-purple-600 text-white px-8 py-3 rounded-lg font-medium hover:bg-purple-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Muat Lebih Banyak Stories
                </button>
            </div>

            <!-- Status Message -->
            <div class="mt-8 text-center">
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 max-w-md mx-auto">
                    <i class="fas fa-info-circle text-purple-600 text-lg mb-2"></i>
                    <p class="text-purple-800 font-medium text-sm">Siap untuk integrasi dengan database phpMyAdmin</p>
                    <p class="text-purple-600 text-xs mt-1">Template alumni story telah disiapkan</p>
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
        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const cards = document.querySelectorAll('.story-card');
            
            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                const cardParent = card.parentElement;
                cardParent.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // View story function
        function viewStory(id) {
            alert(`Membuka cerita alumni ID: ${id}\n\nFungsi ini akan menampilkan detail lengkap perjalanan karir alumni, pencapaian, dan tips untuk mahasiswa.`);
            // Redirect to story detail page
            // window.location.href = `alumni-story-detail.php?id=${id}`;
        }

        // Load more stories function
        function loadMoreStories() {
            alert('Memuat lebih banyak alumni stories...\n\nFungsi ini akan memuat data alumni tambahan dari database.');
            // Implement load more functionality
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Alumni Story page loaded - Ready for database integration');
        });

        // Function untuk menampilkan data ketika sudah terhubung database
        function loadAlumniData() {
            // Fungsi ini akan dipanggil ketika data berhasil diambil dari database
            // fetch('get_alumni_data.php')
            //     .then(response => response.json())
            //     .then(data => {
            //         displayAlumniData(data);
            //     });
        }

        function displayAlumniData(data) {
            // Fungsi untuk menampilkan data alumni dalam card format
            const grid = document.getElementById('alumniGrid');
            
            if (data.length === 0) {
                grid.innerHTML = `
                    <div class="col-span-full text-center py-12">
                        <i class="fas fa-graduation-cap text-4xl text-gray-300 mb-4"></i>
                        <p class="text-lg font-medium text-gray-500 mb-2">Belum ada alumni story</p>
                        <p class="text-sm text-gray-400">Data akan tampil setelah terhubung dengan database</p>
                    </div>
                `;
                return;
            }

            // Render alumni cards
            let html = '';
            const colors = ['blue', 'green', 'purple', 'yellow', 'red', 'indigo'];
            
            data.forEach((alumni, index) => {
                const color = colors[index % colors.length];
                
                html += `
                    <div class="story-card rounded-xl p-6 card-hover">
                        <div class="flex items-center mb-4">
                            <div class="w-16 h-16 bg-${color}-100 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-user text-${color}-600 text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">${alumni.nama}</h3>
                                <p class="text-${color}-600 font-medium">${alumni.posisi}</p>
                            </div>
                        </div>
                        
                        <div class="space-y-3 mb-6">
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-envelope mr-2 text-blue-500"></i>
                                ${alumni.email}
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-building mr-2 text-green-500"></i>
                                ${alumni.perusahaan}
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-briefcase mr-2 text-purple-500"></i>
                                ${alumni.bidang}
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-calendar mr-2 text-orange-500"></i>
                                Angkatan ${alumni.angkatan}
                            </div>
                        </div>

                        <button onclick="viewStory(${alumni.id})" class="w-full bg-${color}-600 text-white py-3 rounded-lg font-medium hover:bg-${color}-700 transition-colors">
                            <i class="fas fa-eye mr-2"></i>Lihat Story
                        </button>
                    </div>
                `;
            });
            
            grid.innerHTML = html;
        }
    </script>
</body>
</html>