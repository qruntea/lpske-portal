<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan - SMAKADUTA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }

        .hero-gradient {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(16, 185, 129, 0.3);
        }

        .book-card {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(52, 211, 153, 0.1));
            border: 2px solid rgba(16, 185, 129, 0.2);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .book-card:hover {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(52, 211, 153, 0.2));
            border-color: rgba(16, 185, 129, 0.5);
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.2);
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s ease;
        }

        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: 1.5rem;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .schedule-item {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.05), rgba(52, 211, 153, 0.05));
            border-left: 4px solid #10b981;
        }

        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(16, 185, 129, 0.1);
            border-radius: 50%;
            animation: particleFloat 15s infinite linear;
        }

        @keyframes particleFloat {
            0% {
                opacity: 0;
                transform: translateY(100vh) scale(0);
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                opacity: 0;
                transform: translateY(-100vh) scale(1);
            }
        }

        .content-wrapper {
            position: relative;
            z-index: 10;
        }

        body {
            background: linear-gradient(135deg, #e8f5f1 0%, #f0fdf9 100%);
            min-height: 100vh;
        }
    </style>
</head>
<body>
    <!-- Animated Particles Background -->
    <div class="particles" id="particles"></div>

    <div class="content-wrapper">
        <!-- Navigation -->
        <nav class="hero-gradient shadow-lg sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <!-- Logo -->
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-book text-white text-2xl"></i>
                        </div>
                        <div class="text-white">
                            <h1 class="text-xl font-bold">Perpustakaan SMAKADUTA</h1>
                            <p class="text-xs text-emerald-100">Portal Digital Perpustakaan</p>
                        </div>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden md:flex items-center space-x-6">
                        <a href="index.html" class="text-white hover:text-emerald-100 transition-colors">Beranda</a>
                        <a href="#koleksi" class="text-white hover:text-emerald-100 transition-colors">Koleksi Buku</a>
                        <a href="login.html" class="bg-white text-emerald-600 px-4 py-2 rounded-lg font-medium hover:bg-emerald-50 transition-colors">
                            <i class="fas fa-sign-in-alt 
                            
                            mr-2"></i>Login
                        </a>
                    </div>

                    <!-- Mobile Menu Button -->
                    <button id="mobile-menu-btn" class="md:hidden text-white">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>

                <!-- Mobile Menu -->
                <div id="mobile-menu" class="md:hidden hidden bg-white bg-opacity-10 backdrop-blur-sm rounded-lg mt-2 mb-4">
                    <div class="px-4 py-3 space-y-2">
                        <a href="index.html" class="block text-white hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded">Beranda</a>
                        <a href="#koleksi" class="block text-white hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded">Koleksi Buku</a>
                        <a href="login.html" class="block bg-white text-emerald-600 px-3 py-2 rounded font-medium">Login</a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section id="beranda" class="hero-gradient text-white py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="mb-8">
                    <h1 class="text-4xl md:text-6xl font-bold mb-4">
                        Perpustakaan Digital SMAKADUTA
                    </h1>
                    <p class="text-xl text-emerald-100 max-w-3xl mx-auto mb-8">
                        Jelajahi koleksi lengkap buku-buku berkualitas kami. Temukan pengetahuan baru dan tingkatkan wawasan Anda dengan membaca
                    </p>
                </div>
            </div>
        </section>

        <!-- Jadwal Section -->
        <section id="jadwal" class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        <i class="fas fa-clock text-emerald-600 mr-3"></i>Jadwal Buka-Tutup Perpustakaan
                    </h2>
                    <p class="text-xl text-gray-600">Jam operasional perpustakaan kami</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
                    <!-- Senin -->
                    <div class="schedule-item p-6 rounded-xl">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-calendar-day text-emerald-600 text-xl mr-3"></i>
                            <h3 class="font-bold text-lg text-gray-900">Senin</h3>
                        </div>
                        <p class="text-2xl font-bold text-emerald-600">07:00 - 15:30</p>
                        <p class="text-sm text-gray-600 mt-2">Jam kerja normal</p>
                    </div>

                    <!-- Selasa -->
                    <div class="schedule-item p-6 rounded-xl">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-calendar-day text-emerald-600 text-xl mr-3"></i>
                            <h3 class="font-bold text-lg text-gray-900">Selasa</h3>
                        </div>
                        <p class="text-2xl font-bold text-emerald-600">07:00 - 15:30</p>
                        <p class="text-sm text-gray-600 mt-2">Jam kerja normal</p>
                    </div>

                    <!-- Rabu -->
                    <div class="schedule-item p-6 rounded-xl">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-calendar-day text-emerald-600 text-xl mr-3"></i>
                            <h3 class="font-bold text-lg text-gray-900">Rabu</h3>
                        </div>
                        <p class="text-2xl font-bold text-emerald-600">07:00 - 15:30</p>
                        <p class="text-sm text-gray-600 mt-2">Jam kerja normal</p>
                    </div>

                    <!-- Kamis -->
                    <div class="schedule-item p-6 rounded-xl">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-calendar-day text-emerald-600 text-xl mr-3"></i>
                            <h3 class="font-bold text-lg text-gray-900">Kamis</h3>
                        </div>
                        <p class="text-2xl font-bold text-emerald-600">07:00 - 15:30</p>
                        <p class="text-sm text-gray-600 mt-2">Jam kerja normal</p>
                    </div>

                    <!-- Jumat -->
                    <div class="schedule-item p-6 rounded-xl">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-calendar-day text-emerald-600 text-xl mr-3"></i>
                            <h3 class="font-bold text-lg text-gray-900">Jumat</h3>
                        </div>
                        <p class="text-2xl font-bold text-emerald-600">07:00 - 15:00</p>
                        <p class="text-sm text-gray-600 mt-2">Tutup lebih awal</p>
                    </div>
                </div>

                <div class="bg-emerald-50 border-l-4 border-emerald-600 p-4 rounded-r-lg">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-emerald-600 text-lg mr-3"></i>
                        <p class="text-emerald-800">
                            <strong>Catatan:</strong> Perpustakaan tutup pada hari Sabtu, Minggu, dan hari libur nasional. Untuk informasi lebih lanjut, silakan hubungi petugas perpustakaan.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Koleksi Buku Section -->
        <section id="koleksi" class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        <i class="fas fa-book-open text-emerald-600 mr-3"></i>Koleksi Buku Perpustakaan
                    </h2>
                    <p class="text-xl text-gray-600">Klik pada buku untuk melihat sinopsis lengkap</p>
                </div>

                <!-- Grid Koleksi Buku -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Buku 1 -->
                    <div class="book-card p-6 rounded-xl" onclick="showSynopsis('buku1')">
                        <div class="mb-4">
                            <i class="fas fa-book text-emerald-600 text-4xl"></i>
                        </div>
                        <h3 class="font-bold text-lg text-gray-900 mb-2">Laskar Pelangi</h3>
                        <p class="text-gray-700 mb-3">
                            <strong>Pengarang:</strong> Andrea Hirata
                        </p>
                        <p class="text-sm text-gray-600">Novel fiksi yang menginspirasi tentang perjuangan pendidikan di daerah terpencil.</p>
                        <button class="mt-4 w-full bg-emerald-600 text-white py-2 rounded-lg hover:bg-emerald-700 transition-colors font-medium">
                            Baca Sinopsis
                        </button>
                    </div>

                    <!-- Buku 2 -->
                    <div class="book-card p-6 rounded-xl" onclick="showSynopsis('buku2')">
                        <div class="mb-4">
                            <i class="fas fa-book text-emerald-600 text-4xl"></i>
                        </div>
                        <h3 class="font-bold text-lg text-gray-900 mb-2">Satria Baratayudha</h3>
                        <p class="text-gray-700 mb-3">
                            <strong>Pengarang:</strong> Mpu Sedah
                        </p>
                        <p class="text-sm text-gray-600">Karya klasik sastra jawa yang menceritakan perang besar antara dua saudara.</p>
                        <button class="mt-4 w-full bg-emerald-600 text-white py-2 rounded-lg hover:bg-emerald-700 transition-colors font-medium">
                            Baca Sinopsis
                        </button>
                    </div>

                    <!-- Buku 3 -->
                    <div class="book-card p-6 rounded-xl" onclick="showSynopsis('buku3')">
                        <div class="mb-4">
                            <i class="fas fa-book text-emerald-600 text-4xl"></i>
                        </div>
                        <h3 class="font-bold text-lg text-gray-900 mb-2">Sang Pemimpi</h3>
                        <p class="text-gray-700 mb-3">
                            <strong>Pengarang:</strong> Andrea Hirata
                        </p>
                        <p class="text-sm text-gray-600">Lanjutan dari Laskar Pelangi yang menceritakan mimpi dan usaha meraihnya.</p>
                        <button class="mt-4 w-full bg-emerald-600 text-white py-2 rounded-lg hover:bg-emerald-700 transition-colors font-medium">
                            Baca Sinopsis
                        </button>
                    </div>

                    <!-- Buku 4 -->
                    <div class="book-card p-6 rounded-xl" onclick="showSynopsis('buku4')">
                        <div class="mb-4">
                            <i class="fas fa-book text-emerald-600 text-4xl"></i>
                        </div>
                        <h3 class="font-bold text-lg text-gray-900 mb-2">Bumi Manusia</h3>
                        <p class="text-gray-700 mb-3">
                            <strong>Pengarang:</strong> Pramoedya Ananta Toer
                        </p>
                        <p class="text-sm text-gray-600">Novel pertama dari Tetralogi yang menceritakan perjuangan kemerdekaan Indonesia.</p>
                        <button class="mt-4 w-full bg-emerald-600 text-white py-2 rounded-lg hover:bg-emerald-700 transition-colors font-medium">
                            Baca Sinopsis
                        </button>
                    </div>

                    <!-- Buku 5 -->
                    <div class="book-card p-6 rounded-xl" onclick="showSynopsis('buku5')">
                        <div class="mb-4">
                            <i class="fas fa-book text-emerald-600 text-4xl"></i>
                        </div>
                        <h3 class="font-bold text-lg text-gray-900 mb-2">Anak Semua Bangsa</h3>
                        <p class="text-gray-700 mb-3">
                            <strong>Pengarang:</strong> Pramoedya Ananta Toer
                        </p>
                        <p class="text-sm text-gray-600">Novel lanjutan yang menggali lebih dalam tentang kemerdekaan dan cinta sejati.</p>
                        <button class="mt-4 w-full bg-emerald-600 text-white py-2 rounded-lg hover:bg-emerald-700 transition-colors font-medium">
                            Baca Sinopsis
                        </button>
                    </div>

                    <!-- Buku 6 -->
                    <div class="book-card p-6 rounded-xl" onclick="showSynopsis('buku6')">
                        <div class="mb-4">
                            <i class="fas fa-book text-emerald-600 text-4xl"></i>
                        </div>
                        <h3 class="font-bold text-lg text-gray-900 mb-2">Habibie & Ainun</h3>
                        <p class="text-gray-700 mb-3">
                            <strong>Pengarang:</strong> B. J. Habibie
                        </p>
                        <p class="text-sm text-gray-600">Kisah nyata tentang cinta dan pengorbanan dalam mengejar mimpi besar.</p>
                        <button class="mt-4 w-full bg-emerald-600 text-white py-2 rounded-lg hover:bg-emerald-700 transition-colors font-medium">
                            Baca Sinopsis
                        </button>
                    </div>

                    <!-- Buku 7 -->
                    <div class="book-card p-6 rounded-xl" onclick="showSynopsis('buku7')">
                        <div class="mb-4">
                            <i class="fas fa-book text-emerald-600 text-4xl"></i>
                        </div>
                        <h3 class="font-bold text-lg text-gray-900 mb-2">Negeri 5 Menara</h3>
                        <p class="text-gray-700 mb-3">
                            <strong>Pengarang:</strong> Ahmad Fuadi
                        </p>
                        <p class="text-sm text-gray-600">Novel tentang persahabatan dan pembelajaran di pesantren yang mencerahkan.</p>
                        <button class="mt-4 w-full bg-emerald-600 text-white py-2 rounded-lg hover:bg-emerald-700 transition-colors font-medium">
                            Baca Sinopsis
                        </button>
                    </div>

                    <!-- Buku 8 -->
                    <div class="book-card p-6 rounded-xl" onclick="showSynopsis('buku8')">
                        <div class="mb-4">
                            <i class="fas fa-book text-emerald-600 text-4xl"></i>
                        </div>
                        <h3 class="font-bold text-lg text-gray-900 mb-2">Ranah 3 Warna</h3>
                        <p class="text-gray-700 mb-3">
                            <strong>Pengarang:</strong> Ahmad Fuadi
                        </p>
                        <p class="text-sm text-gray-600">Lanjutan dari Negeri 5 Menara dengan petualangan baru yang menantang.</p>
                        <button class="mt-4 w-full bg-emerald-600 text-white py-2 rounded-lg hover:bg-emerald-700 transition-colors font-medium">
                            Baca Sinopsis
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="hero-gradient text-white py-12 mt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                    <div>
                        <h3 class="font-bold text-lg mb-4">Perpustakaan SMAKADUTA</h3>
                        <p class="text-emerald-100">Portal digital perpustakaan yang menyediakan akses ke koleksi buku berkualitas.</p>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg mb-4">Kontak</h3>
                        <p class="text-emerald-100">
                            <i class="fas fa-phone mr-2"></i>(021) 1234-5678
                        </p>
                        <p class="text-emerald-100">
                            <i class="fas fa-envelope mr-2"></i>perpustakaan@smakaduta.sch.id
                        </p>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg mb-4">Jam Operasional</h3>
                        <p class="text-emerald-100">Senin - Jumat: 07:00 - 15:30</p>
                        <p class="text-emerald-100">Sabtu - Minggu: Tutup</p>
                    </div>
                </div>
                <div class="border-t border-emerald-400 pt-8 text-center text-emerald-100">
                    <p>&copy; 2026 Perpustakaan SMAKADUTA. Semua hak dilindungi.</p>
                </div>
            </div>
        </footer>
    </div>

    <!-- Modal Sinopsis -->
    <div id="synopsisModal" class="modal" onclick="closeSynopsis(event)">
        <div class="modal-content" onclick="event.stopPropagation()">
            <div class="flex justify-between items-center mb-4">
                <h2 id="synopsisTitle" class="text-2xl font-bold text-gray-900"></h2>
                <button onclick="closeSynopsis()" class="text-gray-600 hover:text-gray-900 text-2xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="synopsisContent" class="space-y-4 text-gray-700"></div>
        </div>
    </div>

    <script>
        // Data Sinopsis Buku
        const bukuData = {
            buku1: {
                judul: 'Laskar Pelangi',
                pengarang: 'Andrea Hirata',
                tahun: '2005',
                genre: 'Fiksi',
                sinopsis: 'Laskar Pelangi adalah sebuah novel yang menceritakan perjuangan seorang guru dan sepuluh orang muridnya di sebuah sekolah kecil di Belitung. Mereka berjuang melawan keterbatasan ekonomi dan sumber daya untuk mengejar pendidikan berkualitas. Novel ini menginspirasi ribuan pembaca dengan pesan tentang harapan, persahabatan, dan dedikasi dalam mengejar impian. Karya Andrea Hirata ini telah diterjemahkan ke berbagai bahasa dan mendapat pujian internasional.'
            },
            buku2: {
                judul: 'Satria Baratayudha',
                pengarang: 'Mpu Sedah',
                tahun: 'Abad ke-14',
                genre: 'Klasik Sastra Jawa',
                sinopsis: 'Satria Baratayudha adalah karya sastra klasik Jawa yang menceritakan kisah perang besar antara dua saudara, Harjuna dan Duryodhana. Cerita ini didasarkan pada epos Mahabharata dan menceritakan tentang perjuangan, kehormatan, dan takdir. Karya ini mencerminkan nilai-nilai budaya Jawa kuno dan masih relevan hingga saat ini. Mpu Sedah menghadirkan narasi yang mendalam tentang konflik moral dan spiritual yang dihadapi para tokoh.'
            },
            buku3: {
                judul: 'Sang Pemimpi',
                pengarang: 'Andrea Hirata',
                tahun: '2006',
                genre: 'Fiksi',
                sinopsis: 'Sang Pemimpi adalah kelanjutan dari Laskar Pelangi yang menceritakan perjalanan dua karakter utama dalam meraih impian mereka. Novel ini mengikuti mereka ke Paris untuk mengejar pendidikan lanjutan sambil berjuang melawan kemiskinan. Dengan storytelling yang menarik, Andrea Hirata menunjukkan bahwa mimpi bisa menjadi kenyataan jika didukung dengan kerja keras dan determinasi. Buku ini menjadi inspirasi bagi banyak pembaca untuk tidak menyerah pada keterbatasan.'
            },
            buku4: {
                judul: 'Bumi Manusia',
                pengarang: 'Pramoedya Ananta Toer',
                tahun: '1980',
                genre: 'Fiksi Sejarah',
                sinopsis: 'Bumi Manusia adalah novel pertama dari Tetralogi Buru karya Pramoedya Ananta Toer. Novel ini menceritakan kisah seorang pemuda Jawa bernama Minke yang membangun kesadaran dan perlawanan terhadap kolonialisme Belanda. Melalui perjalanan emosional Minke dan relasi cintanya, Pramoedya mengungkap kompleksitas sosial dan politique pada masa akhir penjajahan. Karya ini dianggap sebagai salah satu novel terpenting dalam sastra Indonesia modern.'
            },
            buku5: {
                judul: 'Anak Semua Bangsa',
                pengarang: 'Pramoedya Ananta Toer',
                tahun: '1980',
                genre: 'Fiksi Sejarah',
                sinopsis: 'Anak Semua Bangsa adalah novel kedua dari Tetralogi Buru yang meneruskan kisah Minke dan perjuangannya. Novel ini menggali lebih dalam tentang keterlibatan Minke dalam gerakan nasionalis dan tantangan yang dia hadapi. Pramoedya menghadirkan narasi yang kaya tentang cinta, pengkhianatan, dan dedikasi untuk kemerdekaan. Melalui karakter-karakter yang kompleks, novel ini menunjukkan bagaimana individu dapat membuat perbedaan dalam gerakan besar.'
            },
            buku6: {
                judul: 'Habibie & Ainun',
                pengarang: 'B. J. Habibie',
                tahun: '2007',
                genre: 'Biografi Fiksi',
                sinopsis: 'Habibie & Ainun adalah kisah nyata yang ditulis oleh mantan Presiden B. J. Habibie tentang cinta dan pengorbanan dalam hidupnya. Novel ini menceritakan hubungannya dengan istri tercinta, Ainun, dan bagaimana dukungan Ainun membuatnya mampu mencapai mimpi besar dalam dunia teknologi dan penerbangan. Dengan gaya penulisan yang romantis namun tulus, Habibie menunjukkan bahwa kesuksesan sejati datang dari dukungan orang terkasih.'
            },
            buku7: {
                judul: 'Negeri 5 Menara',
                pengarang: 'Ahmad Fuadi',
                tahun: '2009',
                genre: 'Fiksi',
                sinopsis: 'Negeri 5 Menara menceritakan kisah lima sahabat yang belajar di sebuah pesantren modern di Spanyol. Novel ini menggabungkan elemen petualangan, persahabatan, dan pencarian identitas diri. Ahmad Fuadi membawa pembaca dalam perjalanan spiritual dan intelektual para tokoh yang mencoba menyeimbangkan modernitas dengan tradisi. Buku ini telah menjadi bestseller dan menginspirasi banyak generasi muda untuk mengejar pendidikan berkualitas.'
            },
            buku8: {
                judul: 'Ranah 3 Warna',
                pengarang: 'Ahmad Fuadi',
                tahun: '2012',
                genre: 'Fiksi',
                sinopsis: 'Ranah 3 Warna adalah lanjutan dari Negeri 5 Menara yang mengikuti perjalanan tokoh-tokoh dalam menghadapi kehidupan dewasa. Novel ini menceritakan tentang karir, cinta, dan peran mereka dalam masyarakat. Ahmad Fuadi menghadirkan cerita yang lebih matang dan reflektif tentang pilihan hidup dan tanggung jawab. Melalui ketiga perspektif warna (merah, kuning, biru) dalam judul, novel ini menawarkan pandangan multi-dimensi tentang kehidupan manusia.'
            }
        };

        // Fungsi untuk menampilkan sinopsis
        function showSynopsis(bukuId) {
            const buku = bukuData[bukuId];
            if (buku) {
                document.getElementById('synopsisTitle').textContent = buku.judul;
                document.getElementById('synopsisContent').innerHTML = `
                    <p><strong>Pengarang:</strong> ${buku.pengarang}</p>
                    <p><strong>Tahun Terbit:</strong> ${buku.tahun}</p>
                    <p><strong>Genre:</strong> ${buku.genre}</p>
                    <hr class="my-4 border-emerald-200">
                    <div class="bg-emerald-50 p-4 rounded-lg">
                        <h3 class="font-bold text-emerald-900 mb-2">Sinopsis:</h3>
                        <p class="text-gray-700">${buku.sinopsis}</p>
                    </div>
                `;
                document.getElementById('synopsisModal').classList.add('active');
            }
        }

        // Fungsi untuk menutup sinopsis
        function closeSynopsis(event) {
            if (event && event.target.id !== 'synopsisModal') {
                return;
            }
            document.getElementById('synopsisModal').classList.remove('active');
        }

        // Mobile menu toggle
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });

        // Particle animation
        function createParticles() {
            const container = document.getElementById('particles');
            for (let i = 0; i < 30; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.top = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 5 + 's';
                particle.style.animationDuration = (Math.random() * 10 + 15) + 's';
                container.appendChild(particle);
            }
        }

        // Close modal when pressing Escape
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeSynopsis();
            }
        });

        // Initialize particles on page load
        window.addEventListener('load', createParticles);
    </script>
</body>
</html>