<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal LSTARS - Laboratorium Pengembangan Sistem dan Komputasi Edukatif</title>
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
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="hero-gradient shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-flask text-white text-2xl"></i>
                    </div>
                    <div class="text-white">
                        <h1 class="text-xl font-bold">LSTARS Portal</h1>
                        <p class="text-xs text-purple-200">Teknik Industri UNS</p>
                    </div>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="#beranda" class="text-white hover:text-purple-200 transition-colors">Beranda</a>
                    <a href="#tentang" class="text-white hover:text-purple-200 transition-colors">Tentang</a>
                    <a href="#dokumentasi" class="text-white hover:text-purple-200 transition-colors">Dokumentasi</a>
                    <a href="#kontak" class="text-white hover:text-purple-200 transition-colors">Kontak</a>
                    <a href="login.html" class="bg-white text-purple-600 px-4 py-2 rounded-lg font-medium hover:bg-purple-50 transition-colors">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
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
                    <a href="#beranda" class="block text-white hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded">Beranda</a>
                    <a href="#tentang" class="block text-white hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded">Tentang</a>
                    <a href="#dokumentasi" class="block text-white hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded">Dokumentasi</a>
                    <a href="#kontak" class="block text-white hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded">Kontak</a>
                    <a href="login.html" class="block bg-white text-purple-600 px-3 py-2 rounded font-medium">Login</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="beranda" class="hero-gradient text-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="mb-8">
                <h1 class="text-4xl md:text-6xl font-bold mb-4">
                    Portal LSTARS
                </h1>
                <h2 class="text-2xl md:text-3xl font-light mb-6">
                    Laboratorium Pengembangan Sistem dan Komputasi Edukatif
                </h2>
                <p class="text-xl text-purple-100 max-w-3xl mx-auto mb-8">
                    Pusat inovasi teknologi industri 4.0, penelitian, dan pengembangan sistem komputasi untuk pendidikan teknik industri yang unggul
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="#dokumentasi" class="bg-white text-purple-600 px-8 py-3 rounded-lg font-semibold hover:bg-purple-50 transition-all transform hover:scale-105">
                        <i class="fas fa-camera mr-2"></i>Lihat Dokumentasi
                    </a>
                    <a href="login.html" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-purple-600 transition-all">
                        <i class="fas fa-sign-in-alt mr-2"></i>Akses Portal
                    </a>
                </div>
            </div>
        </div>
    </section>

    <?php
// Bagian atas file tetap sama, hanya update section "Tentang LSTARS"
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LSTARS - About Section</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<!-- About Section -->
<section id="tentang" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Tentang LSTARS</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Laboratorium modern yang mengintegrasikan teknologi terdepan dengan pendidikan teknik industri
            </p>
        </div>
        
        <!-- Grid Layout: 3 kolom di atas, 3 kolom di bawah -->
        <div class="space-y-8">
            <!-- Baris pertama: 3 tombol -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Database Dosen -->
                <div class="text-center card-hover bg-gradient-to-br from-blue-50 to-indigo-100 p-8 rounded-xl cursor-pointer" onclick="window.location.href='database-dosen.php'">
                    <div class="w-16 h-16 bg-blue-500 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user-tie text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Database Dosen</h3>
                    <p class="text-gray-600">Akses informasi lengkap profil dosen, bidang keahlian, penelitian, dan publikasi ilmiah</p>
                </div>

                <!-- Database Asisten -->
                <div class="text-center card-hover bg-gradient-to-br from-green-50 to-emerald-100 p-8 rounded-xl cursor-pointer" onclick="window.location.href='database-asisten.php'">
                    <div class="w-16 h-16 bg-green-500 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Database Asisten</h3>
                    <p class="text-gray-600">Data nama asisten, angkatan, dan NIM laboratorium LSTARS</p>
                </div>

                <!-- Data Inventory -->
                <div class="text-center card-hover bg-gradient-to-br from-purple-50 to-violet-100 p-8 rounded-xl cursor-pointer" onclick="window.location.href='data-inventory.php'">
                    <div class="w-16 h-16 bg-purple-500 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-boxes text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Data Inventory</h3>
                    <p class="text-gray-600">Data nama barang, kondisi, dan jumlah ketersediaan laboratorium LSTARS</p>
                </div>
            </div>

            <!-- Baris kedua: 3 tombol -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Alumni Story -->
                <div class="text-center card-hover bg-gradient-to-br from-orange-50 to-red-100 p-8 rounded-xl cursor-pointer" onclick="window.location.href='alumni-story.php'">
                    <div class="w-16 h-16 bg-orange-500 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-graduation-cap text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Alumni Story</h3>
                    <p class="text-gray-600">Cerita inspiratif dan perjalanan karir alumni laboratorium LSTARS</p>
                </div>

                <!-- Jadwal Praktikum -->
                <div class="text-center card-hover bg-gradient-to-br from-rose-50 to-pink-100 p-8 rounded-xl cursor-pointer" onclick="window.location.href='jadwal-praktikum.php'">
                    <div class="w-16 h-16 bg-rose-500 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-calendar-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Jadwal Praktikum</h3>
                    <p class="text-gray-600">Jadwal praktikum, sesi laboratorium, dan kalender kegiatan akademik LSTARS</p>
                </div>

                <!-- Fasilitas & SOP -->
                <div class="text-center card-hover bg-gradient-to-br from-teal-50 to-cyan-100 p-8 rounded-xl cursor-pointer" onclick="window.location.href='fasilitas-sop.php'">
                    <div class="w-16 h-16 bg-teal-500 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-clipboard-list text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Fasilitas & SOP</h3>
                    <p class="text-gray-600">Informasi fasilitas laboratorium dan standar operasional prosedur</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Dokumentasi Section -->
<section id="dokumentasi" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Dokumentasi Kegiatan</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Lihat berbagai kegiatan, penelitian, dan prestasi yang telah dicapai LSTARS
            </p>
        </div>

        <!-- Info Counter -->
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg mb-8">
            <div class="flex items-center">
                <i class="fas fa-info-circle text-blue-500 text-lg mr-3"></i>
                <p class="text-blue-700">
                    <span id="total-dokumentasi">16</span> dokumentasi kegiatan tersedia. Klik pada foto untuk melihat detail lengkap.
                </p>
            </div>
        </div>

        <!-- Grid Dokumentasi -->
        <div id="dokumentasi-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-8">
            <!-- Sample documentation cards -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow cursor-pointer">
                <img src="https://picsum.photos/300/200?random=1" alt="Dokumentasi" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h4 class="font-semibold text-gray-900">Praktikum Sistem Produksi</h4>
                    <p class="text-sm text-gray-600">15 Januari 2024</p>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow cursor-pointer">
                <img src="https://picsum.photos/300/200?random=2" alt="Dokumentasi" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h4 class="font-semibold text-gray-900">Workshop IoT</h4>
                    <p class="text-sm text-gray-600">20 Februari 2024</p>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow cursor-pointer">
                <img src="https://picsum.photos/300/200?random=3" alt="Dokumentasi" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h4 class="font-semibold text-gray-900">Seminar Industri 4.0</h4>
                    <p class="text-sm text-gray-600">10 Maret 2024</p>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow cursor-pointer">
                <img src="https://picsum.photos/300/200?random=4" alt="Dokumentasi" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h4 class="font-semibold text-gray-900">Penelitian Mahasiswa</h4>
                    <p class="text-sm text-gray-600">25 Maret 2024</p>
                </div>
            </div>
        </div>

        <!-- Load More Button -->
        <div class="text-center">
            <button id="load-more-btn" class="bg-purple-600 text-white px-8 py-3 rounded-lg hover:bg-purple-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>Muat Lebih Banyak
            </button>
        </div>
    </div>
</section>

<style>
.card-hover {
    transition: all 0.3s ease;
    transform: translateY(0);
}

.card-hover:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}
</style>

<script>
// Simple interaction for load more button
document.getElementById('load-more-btn').addEventListener('click', function() {
    alert('Fitur "Muat Lebih Banyak" akan segera tersedia!');
});
</script>

</body>
</html>

<style>
.card-hover {
    transition: all 0.3s ease;
    transform: translateY(0);
}

.card-hover:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}
</style>

    
<section id="kontak" class="py-20 bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Hubungi & Kolaborasi</h2>
            <p class="text-xl text-gray-600">Tertarik untuk berkolaborasi atau ingin tahu lebih lanjut?</p>
        </div>

        <div class="rounded-xl shadow-2xl overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-2">

                <div class="p-8 sm:p-12 bg-white">
                    <h3 class="text-2xl font-semibold mb-8 text-gray-900">Informasi Kontak</h3>
                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                                <i class="fas fa-map-marker-alt text-purple-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold">Alamat</p>
                                <p class="text-gray-600">Teknik Industri, Universitas Sebelas Maret<br>Surakarta, Jawa Tengah</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                                <i class="fas fa-envelope text-purple-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold">Email</p>
                                <p class="text-gray-600">lstars@mipa.uns.ac.id</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                                <i class="fas fa-phone text-purple-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold">Telepon</p>
                                <p class="text-gray-600">+62 271 646994</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-10 pt-8 border-t border-gray-200">
                        <h4 class="text-lg font-semibold mb-4">Ikuti Kami</h4>
                        <div class="flex space-x-4">
                            <a href="#" class="w-10 h-10 bg-purple-600 text-white rounded-lg flex items-center justify-center hover:bg-purple-700 transition-colors">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="w-10 h-10 bg-purple-600 text-white rounded-lg flex items-center justify-center hover:bg-purple-700 transition-colors">
                                <i class="fab fa-youtube"></i>
                            </a>
                            <a href="#" class="w-10 h-10 bg-purple-600 text-white rounded-lg flex items-center justify-center hover:bg-purple-700 transition-colors">
                                <i class="fab fa-linkedin"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="p-8 sm:p-12 bg-gray-50">
                <h3 class="text-2xl font-semibold mb-4 text-gray-900">Kolaborasi Projek</h3>
                <p class="text-gray-600 mb-8">Kami bekerja sama dengan berbagai mitra dan ahli untuk mencapai hasil yang luar biasa.</p>
                
                <div class="mb-8">
                    <div class="flex items-center justify-start gap-5">
                        <img class="h-24 w-24 rounded-full object-cover ring-4 ring-white shadow-lg transform hover:scale-110 transition-transform duration-300 ease-in-out" src="https://media.licdn.com/dms/image/D560BAQFtfZFFbT0zRA/company-logo_200_200/0/1692255639163?e=2147483647&v=beta&t=TWmQZCnGTge8CO1jxgxlupfQRePFqH3wuLKWW0g3INE" alt="Foto Mitra 1" title="Dr. Andi Wijaya">
                        <img class="h-24 w-24 rounded-full object-cover ring-4 ring-white shadow-lg transform hover:scale-110 transition-transform duration-300 ease-in-out" src="https://tse2.mm.bing.net/th/id/OIP.Vxk7bRukf9Ne6ks_Q59zlwAAAA?pid=Api&P=0&h=180" alt="Foto Mitra 2" title="Siti Aisyah, M.Sc.">
                        <img class="h-24 w-24 rounded-full object-cover ring-4 ring-white shadow-lg transform hover:scale-110 transition-transform duration-300 ease-in-out" src="https://tse2.mm.bing.net/th/id/OIP.OPp1YIUFZobDV89LyG7j2AAAAA?pid=Api&P=0&h=180" alt="Foto Mitra 3" title="Budi Santoso">
                        <img class="h-24 w-24 rounded-full object-cover ring-4 ring-white shadow-lg transform hover:scale-110 transition-transform duration-300 ease-in-out" src="https://i.pravatar.cc/150?img=10" alt="Foto Mitra 4" title="Rina Hartati, Ph.D.">
                    </div>
                </div>

                <div class="mt-12">
                    <a href="profil.html" class="inline-block bg-purple-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-purple-700 transition-all duration-300 ease-in-out shadow-lg shadow-purple-600/50 hover:shadow-xl hover:shadow-purple-700/40 transform hover:-translate-y-1">
                        See More
                    </a>
                </div>
            </div>
                </div>
            </div>
            </div>
        </div>

    </div>
</section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-flask text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold">LSTARS Portal</h3>
                    </div>
                    <p class="text-gray-300 mb-4">
                        Laboratorium Pengembangan Sistem dan Komputasi Edukatif - Universitas Sebelas Maret
                    </p>
                </div>

                <div>
                    <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="#beranda" class="text-gray-300 hover:text-white transition-colors">Beranda</a></li>
                        <li><a href="#tentang" class="text-gray-300 hover:text-white transition-colors">Tentang</a></li>
                        <li><a href="#dokumentasi" class="text-gray-300 hover:text-white transition-colors">Dokumentasi</a></li>
                        <li><a href="login.html" class="text-gray-300 hover:text-white transition-colors">Login Portal</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-lg font-semibold mb-4">Jam Operasional</h4>
                    <div class="text-gray-300 space-y-1">
                        <p>Senin - Jumat: 08:00 - 16:00</p>
                        <p>Sabtu: 08:00 - 12:00</p>
                        <p>Minggu: Tutup</p>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-700 mt-8 pt-8 text-center">
                <p class="text-gray-300">
                    © 2024 Portal LSTARS - Universitas Sebelas Maret. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <!-- Modal Detail Dokumentasi -->
    <div id="modal-dokumentasi" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl max-w-4xl w-full max-h-[95vh] overflow-hidden shadow-2xl">
            <!-- Modal Header -->
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <h2 id="modal-title" class="text-2xl font-bold text-gray-800"></h2>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-gray-100 rounded-full">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="overflow-y-auto max-h-[calc(95vh-120px)]">
                <div class="p-6">
                    <!-- Gambar -->
                    <div class="mb-6">
                        <img id="modal-image" src="" alt="" class="w-full h-auto rounded-lg shadow-lg max-h-96 object-cover">
                    </div>

                    
    <script>
        // Data dokumentasi kegiatan LSTARS
        const dokumentasiData = [
            {
                id: 1,
                judul_kegiatan: "Workshop Simulasi Ergonomi dan K3",
                deskripsi: "Kegiatan workshop praktik simulasi ergonomi dan keselamatan kerja untuk mahasiswa Teknik Industri. Peserta belajar mengidentifikasi risiko ergonomi dan menerapkan prinsip-prinsip K3 di tempat kerja.",
                kategori: "Pelatihan",
                tanggal_kegiatan: "2024-12-15",
                uploader: "Tim LSTARS",
                url_gambar: "https://images.unsplash.com/photo-1556761175-4b46a572b786?w=500&h=300&fit=crop"
            },
            {
                id: 2,
                judul_kegiatan: "Praktikum Sistem Informasi Manufaktur",
                deskripsi: "Sesi praktikum mahasiswa dalam menggunakan software simulasi manufaktur dan sistem informasi terintegrasi untuk optimasi proses produksi.",
                kategori: "Kegiatan",
                tanggal_kegiatan: "2024-12-10",
                uploader: "Dosen Pengampu",
                url_gambar: "https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=500&h=300&fit=crop"
            },
            {
                id: 3,
                judul_kegiatan: "Fasilitas Lab Komputasi LSTARS",
                deskripsi: "Dokumentasi fasilitas laboratorium komputasi yang dilengkapi dengan komputer high-end untuk simulasi dan analisis data industri.",
                kategori: "Fasilitas",
                tanggal_kegiatan: "2024-12-08",
                uploader: "Admin Lab",
                url_gambar: "https://images.unsplash.com/photo-1531482615713-2afd69097998?w=500&h=300&fit=crop"
            },
            {
                id: 4,
                judul_kegiatan: "Seminar Nasional Industri 4.0",
                deskripsi: "Seminar nasional tentang implementasi teknologi Industri 4.0 di Indonesia dengan pembicara dari akademisi dan praktisi industri terkemuka.",
                kategori: "Event",
                tanggal_kegiatan: "2024-12-05",
                uploader: "Panitia Seminar",
                url_gambar: "https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=500&h=300&fit=crop"
            },
            {
                id: 5,
                judul_kegiatan: "Penelitian IoT untuk Smart Factory",
                deskripsi: "Dokumentasi penelitian implementasi Internet of Things (IoT) untuk menciptakan konsep smart factory di laboratorium LSTARS.",
                kategori: "Penelitian",
                tanggal_kegiatan: "2024-12-03",
                uploader: "Tim Peneliti",
                url_gambar: "https://images.unsplash.com/photo-1518709268805-4e9042af2176?w=500&h=300&fit=crop"
            },
            {
                id: 6,
                judul_kegiatan: "Workshop Data Analytics dengan Python",
                deskripsi: "Pelatihan analisis data menggunakan Python dan R untuk optimasi proses industri dan pengambilan keputusan berbasis data.",
                kategori: "Pelatihan",
                tanggal_kegiatan: "2024-11-28",
                uploader: "Instruktur",
                url_gambar: "https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=500&h=300&fit=crop"
            },
            {
                id: 7,
                judul_kegiatan: "Kunjungan Industri PT. Astra Honda Motor",
                deskripsi: "Kunjungan mahasiswa ke PT. Astra Honda Motor untuk melihat langsung implementasi sistem manufaktur modern dan lean production.",
                kategori: "Kegiatan",
                tanggal_kegiatan: "2024-11-25",
                uploader: "Dosen Pembimbing",
                url_gambar: "https://images.unsplash.com/photo-1565728744382-61accd4aa148?w=500&h=300&fit=crop"
            },
            {
                id: 8,
                judul_kegiatan: "Lab Ergonomi dan Antropometri",
                deskripsi: "Fasilitas laboratorium ergonomi yang dilengkapi dengan peralatan pengukuran antropometri dan analisis postur kerja modern.",
                kategori: "Fasilitas",
                tanggal_kegiatan: "2024-11-20",
                uploader: "Admin Lab",
                url_gambar: "https://images.unsplash.com/photo-1559757148-5c350d0d3c56?w=500&h=300&fit=crop"
            },
            {
                id: 9,
                judul_kegiatan: "Kompetisi Desain Produk Inovatif",
                deskripsi: "Dokumentasi kompetisi desain produk inovatif antar mahasiswa dengan tema sustainable manufacturing dan green technology.",
                kategori: "Event",
                tanggal_kegiatan: "2024-11-15",
                uploader: "Panitia Kompetisi",
                url_gambar: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=500&h=300&fit=crop"
            },
            {
                id: 10,
                judul_kegiatan: "Pelatihan Six Sigma Green Belt",
                deskripsi: "Program pelatihan sertifikasi Six Sigma Green Belt untuk mahasiswa dan dosen guna meningkatkan kemampuan quality improvement.",
                kategori: "Pelatihan",
                tanggal_kegiatan: "2024-11-10",
                uploader: "Certified Trainer",
                url_gambar: "https://images.unsplash.com/photo-1552664730-d307ca884978?w=500&h=300&fit=crop"
            },
            {
                id: 11,
                judul_kegiatan: "Laboratorium Sistem Produksi",
                deskripsi: "Fasilitas laboratorium sistem produksi dengan miniatur lini produksi otomatis dan sistem kontrol berbasis PLC.",
                kategori: "Fasilitas",
                tanggal_kegiatan: "2024-11-05",
                uploader: "Admin Lab",
                url_gambar: "https://images.unsplash.com/photo-1581092160562-40aa08e78837?w=500&h=300&fit=crop"
            },
            {
                id: 12,
                judul_kegiatan: "Penelitian AI dalam Quality Control",
                deskripsi: "Dokumentasi penelitian penerapan Artificial Intelligence untuk sistem quality control otomatis dalam industri manufaktur.",
                kategori: "Penelitian",
                tanggal_kegiatan: "2024-10-30",
                uploader: "Tim Peneliti AI",
                url_gambar: "https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=500&h=300&fit=crop"
            },
            {
                id: 13,
                judul_kegiatan: "Penghargaan Lab Terbaik Nasional",
                deskripsi: "LSTARS meraih penghargaan sebagai laboratorium terbaik tingkat nasional dalam kategori inovasi teknologi industri.",
                kategori: "Penghargaan",
                tanggal_kegiatan: "2024-10-25",
                uploader: "Humas LSTARS",
                url_gambar: "https://images.unsplash.com/photo-1567427017947-545c5f8d16ad?w=500&h=300&fit=crop"
            },
            {
                id: 14,
                judul_kegiatan: "Workshop Lean Manufacturing",
                deskripsi: "Pelatihan implementasi lean manufacturing dan waste elimination untuk meningkatkan efisiensi proses produksi.",
                kategori: "Pelatihan",
                tanggal_kegiatan: "2024-10-20",
                uploader: "Lean Expert",
                url_gambar: "https://images.unsplash.com/photo-1556740738-b6a63e27c4df?w=500&h=300&fit=crop"
            },
            {
                id: 15,
                judul_kegiatan: "Kolaborasi dengan Industri Otomotif",
                deskripsi: "Dokumentasi kerjasama penelitian dan pengembangan dengan industri otomotif untuk proyek inovasi teknologi manufacturing.",
                kategori: "Kegiatan",
                tanggal_kegiatan: "2024-10-15",
                uploader: "Tim Kerjasama",
                url_gambar: "https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?w=500&h=300&fit=crop"
            },
            {
                id: 16,
                judul_kegiatan: "Laboratorium Virtual Reality",
                deskripsi: "Fasilitas laboratorium VR untuk simulasi dan pelatihan virtual dalam bidang ergonomi, safety, dan desain produk.",
                kategori: "Fasilitas",
                tanggal_kegiatan: "2024-10-10",
                uploader: "Admin Lab VR",
                url_gambar: "https://images.unsplash.com/photo-1592478411213-6153e4ebc696?w=500&h=300&fit=crop"
            }
        ];

        let displayedCount = 8; // Jumlah foto yang ditampilkan awalnya

        // Format tanggal
        function formatTanggal(tanggal) {
            const options = { 
                day: 'numeric', 
                month: 'short', 
                year: 'numeric' 
            };
            return new Date(tanggal).toLocaleDateString('id-ID', options);
        }

        // Render dokumentasi ke grid
        function renderDokumentasi(count = displayedCount) {
            const container = document.getElementById('dokumentasi-grid');
            container.innerHTML = '';

            const dataToShow = dokumentasiData.slice(0, count);

            dataToShow.forEach(item => {
                const card = document.createElement('div');
                card.className = 'bg-white rounded-lg shadow-md overflow-hidden cursor-pointer card-hover';
                card.onclick = () => showModal(item);
                
                card.innerHTML = `
                    <div class="relative">
                        <img src="${item.url_gambar}" alt="${item.judul_kegiatan}" 
                             class="w-full h-48 object-cover" 
                             onerror="this.src='https://via.placeholder.com/500x300/e2e8f0/64748b?text=No+Image'"
                        />
                        <div class="absolute top-2 right-2">
                            <span class="bg-black bg-opacity-70 text-white px-2 py-1 rounded-full text-xs">
                                ${item.kategori}
                            </span>
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-800 mb-2 text-sm leading-tight">${item.judul_kegiatan}</h3>
                        <p class="text-gray-600 text-xs mb-3 line-clamp-3">${item.deskripsi.substring(0, 100)}...</p>
                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <span>
                                <i class="fas fa-calendar mr-1"></i>
                                ${formatTanggal(item.tanggal_kegiatan)}
                            </span>
                            <span>
                                <i class="fas fa-user mr-1"></i>
                                ${item.uploader}
                            </span>
                        </div>
                    </div>
                `;
                
                container.appendChild(card);
            });

            // Show/hide Load More button
            const loadMoreBtn = document.getElementById('load-more-btn');
            if (count < dokumentasiData.length) {
                loadMoreBtn.classList.remove('hidden');
            } else {
                loadMoreBtn.classList.add('hidden');
            }
        }

        // Show modal
        function showModal(item) {
            document.getElementById('modal-title').textContent = item.judul_kegiatan;
            document.getElementById('modal-image').src = item.url_gambar;
            document.getElementById('modal-image').alt = item.judul_kegiatan;
            document.getElementById('modal-tanggal').textContent = formatTanggal(item.tanggal_kegiatan);
            document.getElementById('modal-kategori').textContent = item.kategori;
            document.getElementById('modal-uploader').textContent = item.uploader;
            document.getElementById('modal-deskripsi').textContent = item.deskripsi;
            
            document.getElementById('modal-dokumentasi').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        // Close modal
        function closeModal() {
            document.getElementById('modal-dokumentasi').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Mobile menu toggle
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        }

        // Smooth scrolling for navigation links
        function setupSmoothScrolling() {
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                    // Close mobile menu if open
                    document.getElementById('mobile-menu').classList.add('hidden');
                });
            });
        }

        // Initialize everything when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Render initial dokumentasi
            renderDokumentasi();
            
            // Setup smooth scrolling
            setupSmoothScrolling();

            // Mobile menu button
            document.getElementById('mobile-menu-btn').addEventListener('click', toggleMobileMenu);

            // Load more button
            document.getElementById('load-more-btn').addEventListener('click', function() {
                displayedCount += 4;
                renderDokumentasi(displayedCount);
            });
            // Close modal when clicking outside
            document.getElementById('modal-dokumentasi').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal();
                }
            });

            // Close modal with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeModal();
                }
            });

            // Form submission (you can connect this to your backend)
            document.querySelector('form').addEventListener('submit', function(e) {
                e.preventDefault();
                alert('Terima kasih! Pesan Anda telah dikirim. Kami akan segera menghubungi Anda.');
                this.reset();
            });
        });
            // 1. Cari elemen tombol berdasarkan ID yang sudah kita buat
            const tombolProfil = document.getElementById('tombol-profil');

            // 2. Tambahkan "pendengar acara" (event listener) yang akan berjalan saat tombol di-klik
            tombolProfil.addEventListener('click', function(event) {
            
            // 3. Mencegah perilaku default dari link (agar tidak terjadi apa-apa sebelum script berjalan)
            event.preventDefault(); 
            
            // 4. Perintahkan browser untuk pindah ke halaman 'profil.html'
            window.location.href = 'profil.html';
        });
    </script>
</body>
</html>