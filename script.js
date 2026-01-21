// ===================================================================
// LSTARS PORTAL - INTEGRATED MAIN JAVASCRIPT FILE
// Menggabungkan fungsi dashboard dan semua fitur bisnis
// UPDATED WITH REAL DATA INTEGRATION
// ===================================================================

console.log('🚀 LSTARS Portal System Loading...');

// ===================================================================
// GLOBAL VARIABLES
// ===================================================================
let clockInterval = null;
let isUserDataLoaded = false;
let dokumentasiData = [];
let currentDokumentasiData = [];
let currentDokumentasiFilter = 'semua';
let formEventListenersAdded = false;

// ===================================================================
// SESSION & AUTH FUNCTIONS
// ===================================================================
async function logout() {
    try {
        await fetch('api/logout.php', { method: 'POST' });
        window.location.href = 'login.html';
    } catch (error) {
        console.error('Logout error:', error);
        window.location.href = 'login.html';
    }
}

async function cekSession() {
    try {
        const res = await fetch('api/check_session.php');
        const data = await res.json();
        const span = document.getElementById('welcome-message');

        if (data.logged_in) {
            span.textContent = `Selamat datang, ${data.user_data.nama}!`;
            return data.user_data;
        } else {
            span.textContent = 'Silakan login terlebih dahulu.';
            return null;
        }
    } catch (error) {
        console.error('Gagal memeriksa sesi:', error);
        document.getElementById('welcome-message').textContent = 'Terjadi kesalahan.';
        return null;
    }
}

// ===================================================================
// ENHANCED CLOCK FUNCTIONS
// ===================================================================
// GANTI FUNGSI updateClock LAMA DENGAN VERSI INI
function updateClock() {
    const now = new Date();
    
    // Format untuk real-time clock di dashboard
    const time = now.toLocaleTimeString('id-ID', {
        hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false, timeZone: 'Asia/Jakarta'
    });
    const date = now.toLocaleDateString('id-ID', {
        day: 'numeric', month: 'long', year: 'numeric', timeZone: 'Asia/Jakarta'
    });
    const day = now.toLocaleDateString('id-ID', {
        weekday: 'long', timeZone: 'Asia/Jakarta'
    });

    // Format lengkap untuk halaman presensi (piket & praktikum)
    const fullDateTime = now.toLocaleDateString('id-ID', { 
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', 
        hour: '2-digit', minute: '2-digit', second: '2-digit',
        timeZone: 'Asia/Jakarta'
    });
    
    // Update elemen dashboard
    const timeEl = document.getElementById('currentTime');
    const dateEl = document.getElementById('currentDate');
    const dayEl = document.getElementById('currentDay');
    if (timeEl) timeEl.textContent = time;
    if (dateEl) dateEl.textContent = date;
    if (dayEl) dayEl.textContent = day;

    // --- PENAMBAHAN BAGIAN INI ---
    // Update elemen jam di halaman Presensi Piket
    const presensiPiketTimeEl = document.getElementById('current-time-date-presensi');
    if (presensiPiketTimeEl) {
        presensiPiketTimeEl.textContent = fullDateTime;
    }
    // --- AKHIR PENAMBAHAN ---

    // Update elemen jam di halaman Presensi Praktikum
    const presensiPraktikumTimeEl = document.getElementById('current-time-date-praktikum');
    if (presensiPraktikumTimeEl) {
        presensiPraktikumTimeEl.textContent = fullDateTime;
    }
}

function initializeClock() {
    if (clockInterval) {
        clearInterval(clockInterval);
    }
    
    updateClock(); // Update immediately
    clockInterval = setInterval(updateClock, 1000);
    
    console.log('⏰ Real-time clock started');
}

// ===================================================================
// USER DATA & DASHBOARD FUNCTIONS - UPDATED WITH REAL API
// ===================================================================
function getUserNameFromHeader() {
    const welcomeEl = document.getElementById('welcome-message');
    if (welcomeEl && welcomeEl.textContent) {
        const text = welcomeEl.textContent.trim();
        if (text.includes('Selamat datang')) {
            const name = text.replace('Selamat datang,', '').replace('!', '').trim();
            if (name && name !== 'Selamat datang') {
                return name;
            }
        }
    }
            return 'Pengguna LSTARS';
}

async function loadUserDashboardData() {
    console.log('📋 Loading user dashboard data from API...');
    
    try {
        // Panggil API dashboard yang sudah ada
        const response = await fetch('api/get_dashboard_data.php');
        const result = await response.json();
        
        console.log('📊 Dashboard API response:', result);
        
        if (result.success && result.data) {
            const { user_info, statistics, activities } = result.data;
            
            // Update user profile dengan data real
            updateUserProfile(user_info);
            
            // Update statistics dengan data real
            updateRealStatistics(statistics);
            
            // Update activities dengan data real
            updateRealActivities(activities);
            
            console.log('✅ Real dashboard data loaded successfully');
        } else {
            console.error('❌ Dashboard API error:', result.message);
            // Fallback ke data default jika API gagal
            updateDefaultDashboard();
        }
        
    } catch (error) {
        console.error('❌ Error loading dashboard data:', error);
        // Fallback ke data default jika ada error
        updateDefaultDashboard();
    }
    
    isUserDataLoaded = true;
}

function updateUserProfile(userInfo) {
    const userNameEl = document.getElementById('userName');
    const userRoleEl = document.getElementById('userRole');
    const userInfoEl = document.getElementById('userInfo');
    
    console.log('👤 Updating user profile with real data:', userInfo);
    
    if (userNameEl && userInfo) {
        userNameEl.textContent = userInfo.nama_lengkap || userInfo.username || 'Pengguna LSTARS';
    }
    
    if (userRoleEl && userInfo) {
        // Capitalize role
        const role = userInfo.role ? 
            userInfo.role.charAt(0).toUpperCase() + userInfo.role.slice(1) : 'Pengguna';
        userRoleEl.textContent = role;
    }
    
    if (userInfoEl) {
        let infoText = 'Portal LSTARS • Universitas Sebelas Maret';
        
        // Tambahkan info khusus jika dosen
        if (userInfo && userInfo.homebase_prodi) {
            infoText = `${userInfo.homebase_prodi} • ${infoText}`;
        }
        
        userInfoEl.textContent = infoText;
    }
    
    console.log('✅ User profile updated with real data');
}

function updateRealStatistics(stats) {
    console.log('📊 Updating statistics with real data:', stats);
    
    // Mapping dari API response ke element ID
    const statsMapping = {
        'myPermissions': stats?.my_permissions || 0,
        'approvedCount': stats?.approved_count || 0,
        'pendingCount': stats?.pending_count || 0,
        'equipmentCount': stats?.equipment_count || 0
    };
    
    // Update setiap statistik dengan animasi
    Object.entries(statsMapping).forEach(([elementId, value]) => {
        const element = document.getElementById(elementId);
        if (element) {
            // Animasi loading
            element.style.transform = 'scale(0.8)';
            element.style.opacity = '0.5';
            
            setTimeout(() => {
                element.textContent = value;
                element.style.transform = 'scale(1)';
                element.style.opacity = '1';
                element.style.transition = 'all 0.3s ease';
                
                // Tambahkan efek highlight jika nilai > 0
                if (value > 0) {
                    element.style.color = '#059669'; // Green color
                    setTimeout(() => {
                        element.style.color = ''; // Reset color
                    }, 1500);
                }
            }, 100);
        }
    });
    
    console.log('✅ Real statistics updated successfully');
}

function updateRealActivities(activities) {
    const activitiesEl = document.getElementById('recentActivities');
    if (!activitiesEl) return;
    
    console.log('📋 Updating activities with real data:', activities);
    
    const currentDate = new Date().toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long', 
        year: 'numeric'
    });
    
    const currentTime = new Date().toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit'
    });
    
    let activitiesHTML = `
        <div class="space-y-3">
            <!-- Login activity -->
            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <i class="fas fa-sign-in-alt text-green-500 text-lg"></i>
                <div class="flex-1">
                    <p class="text-sm text-gray-700 font-medium">Berhasil masuk ke Portal LSTARS</p>
                    <p class="text-xs text-gray-500">${currentDate} • ${currentTime}</p>
                </div>
            </div>
    `;
    
    // Tambahkan aktivitas real dari API jika ada
    if (activities && Array.isArray(activities) && activities.length > 0) {
        activities.forEach(activity => {
            activitiesHTML += `
                <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="fas ${activity.icon || 'fa-file-alt'} ${activity.color || 'text-blue-500'} text-lg"></i>
                    <div class="flex-1">
                        <p class="text-sm text-gray-700">${activity.description}</p>
                        <p class="text-xs text-gray-500">${activity.time}</p>
                    </div>
                </div>
            `;
        });
    } else {
        // Aktivitas default jika tidak ada data dari API
        activitiesHTML += `
            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <i class="fas fa-shield-alt text-blue-500 text-lg"></i>
                <div class="flex-1">
                    <p class="text-sm text-gray-700">Sesi login aktif dan aman</p>
                    <p class="text-xs text-gray-500">${currentDate} • ${currentTime}</p>
                </div>
            </div>
            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <i class="fas fa-database text-purple-500 text-lg"></i>
                <div class="flex-1">
                    <p class="text-sm text-gray-700">Koneksi database stabil</p>
                    <p class="text-xs text-gray-500">${currentDate} • ${currentTime}</p>
                </div>
            </div>
        `;
    }
    
    activitiesHTML += `</div>`;
    activitiesEl.innerHTML = activitiesHTML;
    
    console.log('✅ Real activities updated successfully');
}

// Fallback function jika API gagal
function updateDefaultDashboard() {
    console.log('⚠️ Using default dashboard data');
    
    // Update user profile default
    const userNameEl = document.getElementById('userName');
    const userRoleEl = document.getElementById('userRole');
    const userInfoEl = document.getElementById('userInfo');
    
    if (userNameEl) userNameEl.textContent = getUserNameFromHeader();
    if (userRoleEl) userRoleEl.textContent = 'Pengguna';
    if (userInfoEl) userInfoEl.textContent = 'Portal LSTARS • Universitas Sebelas Maret';
    
    // Update statistics default
    const defaultStats = {
        'myPermissions': '-',
        'approvedCount': '-',
        'pendingCount': '-',
        'equipmentCount': '-'
    };
    
    Object.entries(defaultStats).forEach(([id, value]) => {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = value;
        }
    });
    
    // Update activities default
    updateActivitiesDefault(getUserNameFromHeader());
}

function updateActivitiesDefault(userName) {
    const activitiesEl = document.getElementById('recentActivities');
    if (!activitiesEl) return;
    
    const currentDate = new Date().toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long', 
        year: 'numeric'
    });
    
    const currentTime = new Date().toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit'
    });
    
    activitiesEl.innerHTML = `
        <div class="space-y-3">
            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <i class="fas fa-sign-in-alt text-green-500 text-lg"></i>
                <div class="flex-1">
                    <p class="text-sm text-gray-700 font-medium">Berhasil masuk ke Portal LSTARS</p>
                    <p class="text-xs text-gray-500">${currentDate} • ${currentTime}</p>
                </div>
            </div>
            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <i class="fas fa-shield-alt text-blue-500 text-lg"></i>
                <div class="flex-1">
                    <p class="text-sm text-gray-700">Sesi login aktif dan aman</p>
                    <p class="text-xs text-gray-500">${currentDate} • ${currentTime}</p>
                </div>
            </div>
            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <i class="fas fa-database text-purple-500 text-lg"></i>
                <div class="flex-1">
                    <p class="text-sm text-gray-700">Koneksi database stabil</p>
                    <p class="text-xs text-gray-500">${currentDate} • ${currentTime}</p>
                </div>
            </div>
            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <i class="fas fa-user-check text-indigo-500 text-lg"></i>
                <div class="flex-1">
                    <p class="text-sm text-gray-700">Profil ${userName} dimuat</p>
                    <p class="text-xs text-gray-500">${currentDate} • ${currentTime}</p>
                </div>
            </div>
        </div>
    `;
}

// ===================================================================
// FUNGSI UNTUK REFRESH DASHBOARD STATISTICS SAJA
// ===================================================================
async function refreshDashboardStatistics() {
    console.log('🔄 Refreshing dashboard statistics...');
    
    try {
        const response = await fetch('api/get_dashboard_data.php');
        const result = await response.json();
        
        if (result.success && result.data && result.data.statistics) {
            updateRealStatistics(result.data.statistics);
            console.log('✅ Dashboard statistics refreshed');
        }
    } catch (error) {
        console.error('❌ Error refreshing statistics:', error);
    }
}

// ===================================================================
// UTILITY FUNCTIONS
// ===================================================================
const showLoading = (tbody) => {
    const colspan = tbody.parentElement.querySelector('thead tr').childElementCount;
    tbody.innerHTML = `<tr><td colspan="${colspan}" class="text-center py-4">Memuat data...</td></tr>`;
};

const showError = (tbody, message = 'Gagal memuat data.') => {
    const colspan = tbody.parentElement.querySelector('thead tr').childElementCount;
    tbody.innerHTML = `<tr><td colspan="${colspan}" class="text-center py-4 text-red-500">${message}</td></tr>`;
};

// ===================================================================
// PRESENSI PRAKTIKUM FUNCTIONS (NEW)
// ===================================================================

async function handlePraktikumAction(action) {
    const namaInput = document.getElementById('presensi-praktikum-nama');
    const nimInput = document.getElementById('presensi-praktikum-nim');
    const praktikumInput = document.getElementById('mata-praktikum');
    
    const clockInBtn = document.getElementById('btn-clock-in-praktikum');
    const clockOutBtn = document.getElementById('btn-clock-out-praktikum');

    const nama = namaInput.value.trim();
    const nim = nimInput.value.trim();
    const praktikum = praktikumInput.value.trim();

    if (!nama || !nim || !praktikum) {
        alert('Nama Mahasiswa, NIM, dan Mata Praktikum wajib diisi.');
        return;
    }

    clockInBtn.disabled = true;
    clockOutBtn.disabled = true;
    const originalBtnText = action === 'clock_in' ? clockInBtn.innerHTML : clockOutBtn.innerHTML;
    const targetBtn = action === 'clock_in' ? clockInBtn : clockOutBtn;
    targetBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';

    const formData = new FormData();
    formData.append('nama_mahasiswa', nama);
    formData.append('nim_mahasiswa', nim);
    formData.append('mata_praktikum', praktikum);
    formData.append('action', action);

    try {
        const response = await fetch('api/proses_presensi_praktikum.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (result.success) {
            alert(result.message);
            namaInput.value = '';
            nimInput.value = '';
            praktikumInput.value = '';
            await tampilkanRiwayatPraktikum();
        } else {
            alert(result.message);
        }
    } catch (error) {
        console.error('Praktikum action error:', error);
        alert('Terjadi kesalahan koneksi.');
    } finally {
        targetBtn.innerHTML = originalBtnText;
        clockInBtn.disabled = false;
        clockOutBtn.disabled = false;
    }
}

async function tampilkanRiwayatPraktikum() {
    const tbody = document.getElementById('tabel-riwayat-presensi-praktikum-body');
    if (!tbody) return;

    tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4">Memuat riwayat...</td></tr>`;

    try {
        const response = await fetch('api/get_riwayat_praktikum.php');
        const data = await response.json();

        if (!Array.isArray(data) || data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-gray-500">Belum ada presensi praktikum hari ini.</td></tr>`;
            return;
        }

        tbody.innerHTML = '';
        data.forEach(item => {
            const row = `
                <tr class="hover:bg-gray-50">
                    <td class="py-3 px-4">${item.nama_mahasiswa || '-'}</td>
                    <td class="py-3 px-4">${item.nim_mahasiswa || '-'}</td>
                    <td class="py-3 px-4">${item.mata_praktikum || '-'}</td>
                    <td class="py-3 px-4 text-center text-green-600 font-medium">${item.waktu_masuk_formatted || '-'}</td>
                    <td class="py-3 px-4 text-center text-red-600 font-medium">${item.waktu_keluar_formatted || '<i class="text-gray-500">Belum Clock Out</i>'}</td>
                    <td class="py-3 px-4 text-center font-semibold">${item.durasi || '-'}</td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
    } catch (error) {
        console.error('Error fetching praktikum history:', error);
        tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-red-500">Gagal memuat riwayat.</td></tr>`;
    }
}

function initializePraktikumPage() {
    console.log('Initializing Presensi Praktikum Page...');
    // Pembaruan jam sekarang ditangani oleh fungsi global updateClock()
    tampilkanRiwayatPraktikum();

    const clockInBtn = document.getElementById('btn-clock-in-praktikum');
    const clockOutBtn = document.getElementById('btn-clock-out-praktikum');

    if (clockInBtn && !clockInBtn.hasAttribute('data-listener-added')) {
        clockInBtn.addEventListener('click', () => handlePraktikumAction('clock_in'));
        clockInBtn.setAttribute('data-listener-added', 'true');
    }
    
    if (clockOutBtn && !clockOutBtn.hasAttribute('data-listener-added')) {
        clockOutBtn.addEventListener('click', () => handlePraktikumAction('clock_out'));
        clockOutBtn.setAttribute('data-listener-added', 'true');
    }
}

// ===================================================================
// DATA DOSEN FUNCTIONS - COMPLETE FIXED VERSION
// ===================================================================
async function tampilkanDataDosen() {
    const tbody = document.getElementById('tabel-dosen-body');
    if (!tbody) {
        console.error('Elemen #tabel-dosen-body tidak ditemukan!');
        return;
    }
    
    showLoading(tbody); 
    
    try {
        const response = await fetch('api/get_dosen.php');
        const data = await response.json();

        if (!Array.isArray(data) || data.length === 0) {
            return showError(tbody, 'Tidak ada data dosen untuk ditampilkan.');
        }

        const rowsHtml = data.map((dosen, index) => {
            // Gabungkan nama lengkap dengan gelar
            const namaLengkap = [dosen.gelar_depan, dosen.nama_dosen, dosen.gelar_belakang]
                .filter(Boolean) // Hapus bagian yang null atau kosong
                .join(' ')
                .replace(/ ,/, ','); // Perbaiki koma jika ada

            return `
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="py-3 px-4">
                        <div class="font-medium text-gray-800">${namaLengkap}</div>
                        <div class="text-sm text-gray-500">${dosen.homebase_prodi || 'Prodi Tidak Diketahui'}</div>
                    </td>
                    <td class="py-3 px-4 text-sm text-gray-500">${dosen.nip || '-'}</td>
                    <td class="py-3 px-4 text-sm text-gray-500">${dosen.nidn || '-'}</td>
                    <td class="py-3 px-4 text-sm text-gray-500">${dosen.homebase_prodi || '-'}</td>
                    <td class="py-3 px-4 text-center">
                        <button class="btn-detail-dosen bg-blue-500 hover:bg-blue-600 text-white py-1 px-3 rounded-md text-sm transition-colors"
                                data-dosen-id="${dosen.id}"
                                data-dosen-nama="${namaLengkap}"
                                data-dosen-nip="${dosen.nip || ''}"
                                data-dosen-nidn="${dosen.nidn || ''}"
                                data-dosen-prodi="${dosen.homebase_prodi || ''}"
                                data-dosen-gelar-depan="${dosen.gelar_depan || ''}"
                                data-dosen-gelar-belakang="${dosen.gelar_belakang || ''}"
                                data-dosen-foto="${dosen.foto || 'default.jpg'}"
                                data-dosen-email="${dosen.email || ''}"
                                data-dosen-fakultas="${dosen.fakultas || ''}">
                            Lihat Detail
                        </button>
                    </td>
                </tr>
            `;
        }).join('');

        tbody.innerHTML = rowsHtml;

        // Tambahkan event listener untuk tombol detail
        addDosenDetailEventListeners();

    } catch (error) {
        console.error('Error saat menampilkan data dosen:', error);
        showError(tbody, 'Gagal memuat data dosen.');
    }
}

// ===================================================================
// FINAL WORKING VERSION - Asisten Grouping
// ===================================================================

async function tampilkanDataAsisten() {
    console.log('🔄 Loading asisten data with grouping...');
    const tbody = document.getElementById('tabel-asisten-body');
    
    if (!tbody) {
        console.error('❌ Element tabel-asisten-body tidak ditemukan!');
        return;
    }
    
    // Show loading with correct colspan
    tbody.innerHTML = `
        <tr>
            <td colspan="4" class="py-8 text-center">
                <div class="flex justify-center items-center">
                    <i class="fas fa-spinner fa-spin text-2xl text-blue-500 mr-3"></i>
                    <span class="text-gray-600">Memuat data asisten...</span>
                </div>
            </td>
        </tr>
    `;
    
    try {
        const response = await fetch('api/get_asisten.php');
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const data = await response.json();
        console.log('👥 Raw asisten data:', data);
        
        // Clear table body
        tbody.innerHTML = '';
        
        if (!Array.isArray(data) || data.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center py-4">Tidak ada data asisten.</td>
                </tr>
            `;
            return;
        }

        // Group data by angkatan
        const groupedData = {};
        data.forEach(asisten => {
            // Pastikan angkatan ada dan valid
            let angkatan = asisten.angkatan;
            
            // Jika angkatan kosong, null, atau undefined
            if (!angkatan || angkatan === '' || angkatan === null) {
                angkatan = 'Tidak Diketahui';
            }
            
            // Convert to string untuk konsistensi
            angkatan = String(angkatan);
            
            if (!groupedData[angkatan]) {
                groupedData[angkatan] = [];
            }
            groupedData[angkatan].push(asisten);
        });

        console.log('📊 Grouped by angkatan:', groupedData);

        // Sort angkatan - terbaru dulu, lalu "Tidak Diketahui" di akhir
        const sortedAngkatan = Object.keys(groupedData).sort((a, b) => {
            if (a === 'Tidak Diketahui') return 1;
            if (b === 'Tidak Diketahui') return -1;
            
            // Convert to number for proper sorting
            const numA = parseInt(a) || 0;
            const numB = parseInt(b) || 0;
            
            return numB - numA; // Descending order (terbaru dulu)
        });

        console.log('📋 Sorted angkatan order:', sortedAngkatan);

        // Render grouped data
        sortedAngkatan.forEach(angkatan => {
            const asistenList = groupedData[angkatan];
            const asistenCount = asistenList.length;
            
            // Header row for each angkatan - EXACT match with screenshot
            tbody.innerHTML += `
                <tr class="bg-blue-100">
                    <td colspan="4" class="py-3 px-4 font-bold text-blue-800">
                        Angkatan ${angkatan} (${asistenCount} orang)
                    </td>
                </tr>
            `;

            // Data rows for this angkatan
            asistenList.forEach((asisten) => {
                // Determine status styling
                let statusClass, statusText;
                
                if (asisten.status === 'Aktif' || asisten.status == 1 || asisten.status === '1') {
                    statusClass = 'bg-green-200 text-green-800';
                    statusText = 'Aktif';
                } else {
                    statusClass = 'bg-red-200 text-red-800';
                    statusText = 'Tidak Aktif';
                }
                
                // Data row with proper indentation
                tbody.innerHTML += `
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-3 px-4 pl-8">${asisten.nama || '-'}</td>
                        <td class="py-3 px-4">${asisten.nim || '-'}</td>
                        <td class="py-3 px-4">${asisten.jabatan || 'Asisten Lab'}</td>
                        <td class="py-3 px-4">
                            <span class="${statusClass} py-1 px-3 rounded-full text-xs font-medium">
                                ${statusText}
                            </span>
                        </td>
                    </tr>
                `;
            });
        });

        console.log('✅ Asisten data grouped successfully!');
        console.log(`📊 Total groups: ${sortedAngkatan.length}`);
        console.log(`👥 Total asisten: ${data.length}`);

    } catch (error) {
        console.error('❌ Error loading asisten data:', error);
        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="text-center py-4 text-red-500">
                    Gagal memuat data asisten: ${error.message}
                </td>
            </tr>
        `;
    }
}

// Debug function untuk testing
function debugAsissenData() {
    console.log('🔍 Debug: Testing asisten API...');
    
    fetch('api/get_asisten.php')
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('📋 Raw API data:', data);
            console.log('📊 Data type:', typeof data);
            console.log('📊 Is array:', Array.isArray(data));
            console.log('📊 Length:', data.length);
            
            if (data.length > 0) {
                console.log('📋 First item:', data[0]);
                console.log('📋 Available fields:', Object.keys(data[0]));
                
                // Check angkatan values
                const angkatanValues = data.map(item => item.angkatan).filter((v, i, a) => a.indexOf(v) === i);
                console.log('📊 Unique angkatan values:', angkatanValues);
            }
        })
        .catch(error => {
            console.error('❌ Debug error:', error);
        });
}

// Manual refresh function
function refreshAsistenData() {
    console.log('🔄 Manual refresh asisten data...');
    tampilkanDataAsisten();
}

// Export for global access
window.debugAsissenData = debugAsissenData;
window.refreshAsistenData = refreshAsistenData;

// ===================================================================
// FUNGSI UNTUK MODAL DETAIL DOSEN
// ===================================================================
function addDosenDetailEventListeners() {
    const detailButtons = document.querySelectorAll('.btn-detail-dosen');
    detailButtons.forEach(button => {
        button.addEventListener('click', function() {
            const dosenData = {
                id: this.dataset.dosenId,
                nama: this.dataset.dosenNama,
                nip: this.dataset.dosenNip,
                nidn: this.dataset.dosenNidn,
                prodi: this.dataset.dosenProdi,
                gelarDepan: this.dataset.dosenGelarDepan,
                gelarBelakang: this.dataset.dosenGelarBelakang,
                foto: this.dataset.dosenFoto,
                email: this.dataset.dosenEmail,
                fakultas: this.dataset.dosenFakultas
            };
            
            showDosenDetailModal(dosenData);
        });
    });
}

function showDosenDetailModal(dosenData) {
    // --- BAGIAN PENTING DIMULAI DI SINI ---
    // Logika untuk menentukan URL foto yang akan ditampilkan
    let fotoUrl;
    const namaEncoded = encodeURIComponent(dosenData.nama);
    const fallbackAvatar = `https://ui-avatars.com/api/?name=${namaEncoded}&background=4F46E5&color=fff&size=256&font-size=0.33`;

    // 1. Cek jika ada nama file foto yang valid di database
    if (dosenData.foto && dosenData.foto !== 'default.jpg' && dosenData.foto.trim() !== '') {
        // 2. Buat path/URL lengkap ke file foto di server Anda
        // Pastikan path 'uploads/foto_dosen/' ini sudah benar
        fotoUrl = `uploads/foto_dosen/${dosenData.foto}`;
    } else {
        // 3. Jika tidak ada foto di DB, gunakan avatar inisial
        fotoUrl = fallbackAvatar;
    }
    // --- AKHIR BAGIAN PENTING ---

    // Buat modal HTML yang lebih lengkap
    const modalHTML = `
        <div id="modal-detail-dosen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg max-w-2xl w-full mx-4 relative max-h-[90vh] overflow-y-auto">
                <!-- Header Modal -->
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white p-6 rounded-t-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <i class="fas fa-user-tie text-2xl"></i>
                            <h2 class="text-xl font-bold">Detail Dosen</h2>
                        </div>
                        <button id="close-modal-dosen" class="text-white hover:text-gray-300 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Content Modal -->
                <div class="p-6">
                    <!-- Profile Section -->
                    <div class="flex items-center space-x-6 mb-6 pb-6 border-b border-gray-200">
                        <img src="${fotoUrl}" alt="Foto ${dosenData.nama}" 
                             class="w-24 h-24 rounded-full object-cover border-4 border-blue-100 shadow-lg"
                             onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(dosenData.nama)}&background=random&size=200'">
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold text-gray-800 mb-2">${dosenData.nama}</h3>
                            <div class="space-y-1">
                                <p class="text-gray-600"><i class="fas fa-building mr-2 text-blue-500"></i>${dosenData.prodi || 'Prodi tidak tersedia'}</p>
                                <p class="text-gray-600"><i class="fas fa-university mr-2 text-purple-500"></i>${dosenData.fakultas || 'Fakultas Teknik'}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Biodata Section -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-id-card mr-2 text-blue-500"></i>
                            Informasi Biodata
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <label class="text-sm font-medium text-gray-600">Nama Lengkap:</label>
                                <p class="text-gray-800 font-medium">${dosenData.nama}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <label class="text-sm font-medium text-gray-600">NIDN:</label>
                                <p class="text-gray-800 font-medium">${dosenData.nidn || '-'}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <label class="text-sm font-medium text-gray-600">NIP:</label>
                                <p class="text-gray-800 font-medium">${dosenData.nip || '-'}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <label class="text-sm font-medium text-gray-600">Homebase:</label>
                                <p class="text-gray-800 font-medium">${dosenData.prodi || '-'}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <label class="text-sm font-medium text-gray-600">Gelar Depan:</label>
                                <p class="text-gray-800 font-medium">${dosenData.gelarDepan || '-'}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <label class="text-sm font-medium text-gray-600">Gelar Belakang:</label>
                                <p class="text-gray-800 font-medium">${dosenData.gelarBelakang || '-'}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Navigation -->
                    <div class="mb-6">
                        <div class="border-b border-gray-200">
                            <nav class="-mb-px flex space-x-8">
                                <button class="tab-btn active border-b-2 border-blue-500 py-2 px-1 text-sm font-medium text-blue-600" data-tab="biodata">
                                    <i class="fas fa-user mr-2"></i>Biodata
                                </button>
                                <button class="tab-btn border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="keahlian">
                                    <i class="fas fa-graduation-cap mr-2"></i>Keahlian
                                </button>
                                <button class="tab-btn border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="penelitian">
                                    <i class="fas fa-flask mr-2"></i>Penelitian
                                </button>
                                <button class="tab-btn border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="kontak">
                                    <i class="fas fa-envelope mr-2"></i>Kontak
                                </button>
                            </nav>
                        </div>
                    </div>

                    <!-- Tab Content -->
                    <div id="tab-content">
                        <!-- Biodata Tab -->
                        <div id="tab-biodata" class="tab-content">
                            <div class="text-center py-8">
                                <i class="fas fa-user-check text-green-500 text-4xl mb-4"></i>
                                <p class="text-gray-600">Data biodata telah ditampilkan di atas.</p>
                            </div>
                        </div>
                        
                        <!-- Keahlian Tab -->
                        <div id="tab-keahlian" class="tab-content hidden">
                            <div class="space-y-4">
                                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
                                    <h5 class="font-medium text-blue-800 mb-2">Bidang Keahlian</h5>
                                    <p class="text-blue-700">Sistem Informasi, Teknologi Pendidikan, Computational Learning</p>
                                </div>
                                <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg">
                                    <h5 class="font-medium text-green-800 mb-2">Sertifikasi</h5>
                                    <p class="text-green-700">Sertifikat Dosen Profesional, Oracle Certified Professional</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Penelitian Tab -->
                        <div id="tab-penelitian" class="tab-content hidden">
                            <div class="space-y-4">
                                <div class="bg-purple-50 border-l-4 border-purple-500 p-4 rounded-r-lg">
                                    <h5 class="font-medium text-purple-800 mb-2">Penelitian Terbaru</h5>
                                    <p class="text-purple-700">Pengembangan Sistem E-Learning Adaptif untuk Pendidikan Tinggi</p>
                                </div>
                                <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 rounded-r-lg">
                                    <h5 class="font-medium text-indigo-800 mb-2">Publikasi</h5>
                                    <p class="text-indigo-700">15+ publikasi di jurnal nasional dan internasional</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Kontak Tab -->
                        <div id="tab-kontak" class="tab-content hidden">
                            <div class="space-y-4">
                                <div class="bg-teal-50 border-l-4 border-teal-500 p-4 rounded-r-lg">
                                    <h5 class="font-medium text-teal-800 mb-2">Email</h5>
                                    <p class="text-teal-700">${dosenData.email || 'Email belum tersedia'}</p>
                                </div>
                                <div class="bg-cyan-50 border-l-4 border-cyan-500 p-4 rounded-r-lg">
                                    <h5 class="font-medium text-cyan-800 mb-2">Ruang Kerja</h5>
                                    <p class="text-cyan-700">Laboratorium LSTARS, Gedung Teknik Industri</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Modal -->
                <div class="bg-gray-50 px-6 py-4 rounded-b-lg">
                    <div class="flex space-x-3">
                        <button class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-md transition-colors">
                            <i class="fas fa-envelope mr-2"></i>Hubungi Dosen
                        </button>
                        <button id="tutup-modal-dosen" class="bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 px-4 rounded-md transition-colors">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Hapus modal yang ada jika ada
    const existingModal = document.getElementById('modal-detail-dosen');
    if (existingModal) {
        existingModal.remove();
    }

    // Tambahkan modal ke body
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    // Inisialisasi tab functionality
    initializeModalTabs();

    // Tambahkan event listener untuk menutup modal
    const closeBtn = document.getElementById('close-modal-dosen');
    const tutupBtn = document.getElementById('tutup-modal-dosen');
    const modal = document.getElementById('modal-detail-dosen');

    const closeModal = () => {
        modal.remove();
        document.body.style.overflow = 'auto';
    };

    closeBtn.addEventListener('click', closeModal);
    tutupBtn.addEventListener('click', closeModal);
    
    // Tutup modal jika klik di luar modal
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });

    // Tutup modal dengan tombol ESC
    const escapeHandler = function(e) {
        if (e.key === 'Escape') {
            closeModal();
            document.removeEventListener('keydown', escapeHandler);
        }
    };
    document.addEventListener('keydown', escapeHandler);

    // Prevent body scroll
    document.body.style.overflow = 'hidden';
}

// ===================================================================
// TAB FUNCTIONALITY FOR MODAL
// ===================================================================
function initializeModalTabs() {
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.dataset.tab;

            // Remove active class from all buttons
            tabButtons.forEach(btn => {
                btn.classList.remove('active', 'border-blue-500', 'text-blue-600');
                btn.classList.add('border-transparent', 'text-gray-500');
            });

            // Add active class to clicked button
            this.classList.add('active', 'border-blue-500', 'text-blue-600');
            this.classList.remove('border-transparent', 'text-gray-500');

            // Hide all tab contents
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });

            // Show target tab content
            const targetContent = document.getElementById(`tab-${targetTab}`);
            if (targetContent) {
                targetContent.classList.remove('hidden');
            }
        });
    });
}

// ===================================================================
// EXPORT FUNCTIONS FOR GLOBAL ACCESS
// ===================================================================
window.showDosenDetailModal = showDosenDetailModal;
window.addDosenDetailEventListeners = addDosenDetailEventListeners;
window.initializeModalTabs = initializeModalTabs;

// ===================================================================
// INVENTORY FUNCTIONS
// ===================================================================
async function tampilkanDataInventory() {
    console.log('🔄 Loading inventory data...');
    const tbody = document.getElementById('tabel-inventory-body');
    
    if (!tbody) {
        console.error('❌ Element tabel-inventory-body tidak ditemukan!');
        return;
    }
    
    showLoading(tbody);
    
    try {
        const response = await fetch('api/get_inventory.php');
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const data = await response.json();
        console.log('📦 Data inventory berhasil dimuat:', data);
        
        tbody.innerHTML = '';
        
        if (!Array.isArray(data) || data.length === 0) {
            return showError(tbody, 'Tidak ada data inventory.');
        }
        
        const hasQuantitySystem = data[0]?.hasOwnProperty('jumlah_total') && 
                                 data[0]?.hasOwnProperty('jumlah_tersedia') && 
                                 data[0]?.has_quantity_system !== false;
        
        console.log('💡 Sistem quantity aktif:', hasQuantitySystem);
        
        data.forEach((item, index) => {
            try {
                let statusClass, statusText, quantityColumn = '';
                
                if (hasQuantitySystem) {
                    const tersedia = parseInt(item.jumlah_tersedia) || 0;
                    const total = parseInt(item.jumlah_total) || 1;
                    
                    if (tersedia <= 0) {
                        statusClass = 'bg-red-200 text-red-800';
                        statusText = 'Habis';
                    } else if (tersedia < total) {
                        statusClass = 'bg-yellow-200 text-yellow-800';
                        statusText = 'Sebagian Dipinjam';
                    } else {
                        statusClass = 'bg-green-200 text-green-800';
                        statusText = 'Tersedia';
                    }
                    
                    quantityColumn = `
                        <td class="py-3 px-4 text-center">
                            <span class="font-medium text-blue-600">${tersedia}</span>
                            <span class="text-gray-500"> / ${total}</span>
                        </td>
                    `;
                } else {
                    switch(item.status) {
                        case 'Tersedia': 
                            statusClass = 'bg-green-200 text-green-800'; 
                            statusText = 'Tersedia'; 
                            break;
                        case 'Dipinjam': 
                            statusClass = 'bg-yellow-200 text-yellow-800'; 
                            statusText = 'Dipinjam'; 
                            break;
                        case 'Rusak': 
                            statusClass = 'bg-red-200 text-red-800'; 
                            statusText = 'Rusak'; 
                            break;
                        default: 
                            statusClass = 'bg-gray-200 text-gray-800'; 
                            statusText = item.status || 'Tidak Diketahui';
                    }
                    
                    quantityColumn = '';
                }
                
                const row = `
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-3 px-4 font-medium">${item.nama_alat || '-'}</td>
                        <td class="py-3 px-4 text-gray-600">${item.kode_alat || '-'}</td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-md text-xs">
                                ${item.kategori || '-'}
                            </span>
                        </td>
                        ${quantityColumn}
                        <td class="py-3 px-4">
                            <span class="${statusClass} py-1 px-3 rounded-full text-xs font-medium">
                                ${statusText}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-gray-600">${item.lokasi || '-'}</td>
                    </tr>
                `;
                
                tbody.innerHTML += row;
                
            } catch (itemError) {
                console.error(`❌ Error memproses item ${index}:`, itemError, item);
            }
        });
        
        console.log('✅ Tabel inventory berhasil dimuat!');
        
    } catch (error) { 
        console.error('❌ Error loading inventory:', error); 
        showError(tbody, `Gagal memuat data inventory: ${error.message}`); 
    }
}

// ===================================================================
// PEMINJAMAN FUNCTIONS - PRODUCTION READY
// Deskripsi: Script untuk mengelola form peminjaman dan menampilkan
// riwayat peminjaman pengguna. Versi ini sudah dibersihkan dari
// semua log dan fungsi debugging.
// ===================================================================

/**
 * Memuat opsi alat/inventaris yang tersedia ke dalam dropdown.
 */
async function muatOpsiPeminjaman() {
    const selectAlat = document.getElementById('select-alat');
    if (!selectAlat) return;

    selectAlat.innerHTML = '<option value="">Memuat...</option>';
    selectAlat.disabled = true;

    try {
        const response = await fetch('api/get_inventory.php?available_only=true');
        if (!response.ok) {
            throw new Error('Gagal mengambil data inventaris.');
        }

        const data = await response.json();
        
        selectAlat.innerHTML = '<option value="">-- Pilih Alat/Ruang --</option>';

        if (Array.isArray(data) && data.length > 0) {
            data.forEach(item => {
                const hasQuantity = item.jumlah_total && parseInt(item.jumlah_total) > 1;
                const tersedia = item.jumlah_tersedia || 1;
                
                let optionText;
                if (hasQuantity) {
                    optionText = `${item.nama_alat} (${item.kode_alat}) - Tersedia: ${tersedia}`;
                } else {
                    optionText = `${item.nama_alat} (${item.kode_alat})`;
                }
                
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = optionText;
                selectAlat.appendChild(option);
            });
        } else {
            selectAlat.innerHTML += '<option value="" disabled>Tidak ada alat yang tersedia saat ini</option>';
        }

    } catch (error) {
        selectAlat.innerHTML = '<option value="">Gagal memuat data</option>';
    } finally {
        selectAlat.disabled = false;
    }
}

/**
 * Menampilkan riwayat peminjaman untuk pengguna yang sedang login.
 */
// GANTI FUNGSI LAMA DENGAN VERSI BARU INI (UNTUK HALAMAN USER)
async function tampilkanRiwayatPeminjaman() {
    const tbody = document.getElementById('tabel-peminjaman-body');
    if (!tbody) return;

    tbody.innerHTML = `<tr><td colspan="6" class="text-center py-8">Memuat riwayat...</td></tr>`;

    try {
        const response = await fetch('api/get_peminjaman.php');
        const result = await response.json();
        if (!result.success) throw new Error(result.message);
        
        const data = result.data || [];
        if (data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4">Anda belum memiliki riwayat peminjaman.</td></tr>`;
            return;
        }

        // Fungsi format tanggal yang sudah diperbaiki
        const formatDate = (dateStr) => {
            if (!dateStr || dateStr === '0000-00-00') return '-';
            const dateObj = new Date(dateStr.replace(' ', 'T'));
            if (isNaN(dateObj.getTime())) return 'Invalid Date';
            return dateObj.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
        };

        const rowsHtml = data.map(p => {
            let statusClass, statusText;
            const rencanaKembali = new Date(p.tgl_rencana_kembali.replace(' ', 'T'));
            const hariIni = new Date();
            hariIni.setHours(0,0,0,0);

            // Menentukan status berdasarkan data dari database
            switch (p.status) {
                case 'Diajukan':
                    statusClass = 'bg-blue-100 text-blue-800';
                    statusText = 'Menunggu Persetujuan';
                    break;
                case 'Dipinjam':
                    // Cek apakah terlambat
                    if (rencanaKembali < hariIni) {
                        statusClass = 'bg-red-100 text-red-800';
                        statusText = 'Terlambat';
                    } else {
                        statusClass = 'bg-yellow-100 text-yellow-800';
                        statusText = 'Sedang Dipinjam';
                    }
                    break;
                case 'Ditolak':
                    statusClass = 'bg-red-100 text-red-800';
                    statusText = 'Ditolak';
                    break;
                case 'Dikembalikan':
                    statusClass = 'bg-green-100 text-green-800';
                    statusText = 'Selesai';
                    break;
                default:
                    statusClass = 'bg-gray-100 text-gray-800';
                    statusText = p.status;
            }

            return `
                <tr class="hover:bg-gray-50">
                    <td class="py-3 px-4">${p.nama_peminjam || '-'}</td>
                    <td class="py-3 px-4">${p.nim_peminjam || '-'}</td>
                    <td class="py-3 px-4">${p.nama_alat || '-'}</td>
                    <td class="py-3 px-4 text-sm">${formatDate(p.tgl_pinjam)}</td>
                    <td class="py-3 px-4 text-sm">${formatDate(p.tgl_rencana_kembali)}</td>
                    <td class="py-3 px-4">
                        <span class="${statusClass} py-1 px-3 rounded-full text-xs font-medium">${statusText}</span>
                    </td>
                </tr>
            `;
        }).join('');
        tbody.innerHTML = rowsHtml;
    } catch (error) {
        tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-red-500">Gagal memuat riwayat: ${error.message}</td></tr>`;
    }
}

/**
 * Fungsi untuk melakukan refresh data riwayat secara manual.
 */
function refreshRiwayat() {
    tampilkanRiwayatPeminjaman();
}

// ===================================================================
// EXPORT FUNCTIONS TO BE ACCESSIBLE FROM HTML
// ===================================================================
window.muatOpsiPeminjaman = muatOpsiPeminjaman;
window.tampilkanRiwayatPeminjaman = tampilkanRiwayatPeminjaman;
window.refreshRiwayat = refreshRiwayat;


// ===================================================================
// IZIN PENELITIAN FUNCTIONS
// ===================================================================
async function muatOpsiDosen() {
    const selectDosen = document.getElementById('select-dosen');
    selectDosen.innerHTML = '<option value="">Memuat...</option>';

    try {
        const response = await fetch('api/get_dosen_list.php');
        
        // Cek kalau response tidak OK
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        // Reset pilihan awal
        selectDosen.innerHTML = '<option value="">-- Pilih Dosen Pembimbing --</option>';

        if (Array.isArray(data) && data.length > 0) {
            data.forEach(d => {
                if (d.id && d.nama_lengkap) { // Pastikan datanya valid
                    selectDosen.innerHTML += `<option value="${d.id}">${d.nama_lengkap}</option>`;
                }
            });
        } else {
            selectDosen.innerHTML += '<option value="">(Tidak ada data dosen)</option>';
        }

    } catch (error) { 
        console.error('Error Opsi Dosen:', error); 
        selectDosen.innerHTML = '<option value="">Gagal memuat data</option>'; 
    }
}


async function tampilkanRiwayatIzin() {
    const tbody = document.getElementById('tabel-riwayat-izin-body');
    if (!tbody) return;

    showLoading(tbody);
    
    try {
        const response = await fetch('api/get_riwayat_izin.php');
        const result = await response.json();
        
        if (!result.success) {
            showError(tbody, result.message || 'Gagal memuat data riwayat izin.');
            return;
        }
        
        const data = result.data;
        tbody.innerHTML = '';
        
        if (!data || data.length === 0) {
            return showError(tbody, 'Anda tidak memiliki riwayat pengajuan izin.');
        }
        
        data.forEach(p => {
            let statusClass, statusText;
            switch(p.status) {
                case 'Diajukan':
                    statusClass = 'bg-blue-200 text-blue-800';
                    statusText = 'Diajukan';
                    break;
                case 'Disetujui':
                    statusClass = 'bg-green-200 text-green-800';
                    statusText = 'Disetujui';
                    break;
                case 'Ditolak':
                    statusClass = 'bg-red-200 text-red-800';
                    statusText = 'Ditolak';
                    break;
                default:
                    statusClass = 'bg-gray-200 text-gray-800';
                    statusText = p.status;
            }
            
            tbody.innerHTML += `
                <tr>
                    <td class="py-3 px-4">${p.nama_mahasiswa}</td>
                    <td class="py-3 px-4">${p.nim}</td>
                    <td class="py-3 px-4">${p.judul_penelitian}</td>
                    <td class="py-3 px-4">${p.nama_dosen}</td>
                    <td class="py-3 px-4">${p.tgl_pengajuan}</td>
                    <td class="py-3 px-4">
                        <span class="${statusClass} py-1 px-3 rounded-full text-xs">${statusText}</span>
                    </td>
                </tr>`;
        });
        
    } catch (error) {
        console.error('Error Riwayat Izin:', error);
        showError(tbody, 'Gagal memuat data riwayat izin. Coba periksa console.');
    }
}


// ===================================================================
// DOKUMENTASI FUNCTIONS
// ===================================================================
async function loadDokumentasiData() {
    try {
        const response = await fetch('api/get_dokumentasi.php');
        const result = await response.json();
        
        if (result.success) {
            dokumentasiData = result.data;
            currentDokumentasiData = dokumentasiData;
            renderDokumentasiGrid(currentDokumentasiData);
            updateDokumentasiCount();
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        console.error('Error loading dokumentasi:', error);
        showDokumentasiError('Gagal memuat dokumentasi: ' + error.message);
    }
}

function showDokumentasiError(message) {
    const container = document.getElementById('dokumentasi-container');
    if (container) {
        container.innerHTML = `
            <div class="col-span-full text-center py-16">
                <i class="fas fa-exclamation-triangle text-red-400 text-6xl mb-4"></i>
                <p class="text-red-600 text-lg font-medium">Terjadi Kesalahan</p>
                <p class="text-gray-600 text-sm mt-2">${message}</p>
                <button onclick="loadDokumentasiData()" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors">
                    <i class="fas fa-refresh mr-2"></i>Coba Lagi
                </button>
            </div>
        `;
    }
}

function formatTanggalDokumentasi(tanggal) {
    const options = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    };
    return new Date(tanggal).toLocaleDateString('id-ID', options);
}

function renderDokumentasiGrid(data) {
    const container = document.getElementById('dokumentasi-container');
    const loadingState = document.getElementById('loading-state');
    
    if (!container) {
        console.error('Dokumentasi container not found');
        return;
    }
    
    if (loadingState) {
        loadingState.remove();
    }
    
    container.innerHTML = '';
    
    if (data.length === 0) {
        container.innerHTML = `
            <div class="col-span-full text-center py-16">
                <i class="fas fa-search text-gray-400 text-6xl mb-4"></i>
                <p class="text-gray-600 text-lg">Tidak ada dokumentasi ditemukan</p>
                <p class="text-gray-500 text-sm mt-2">Coba ubah filter atau kata kunci pencarian</p>
            </div>
        `;
        return;
    }

    data.forEach(item => {
        const card = document.createElement('div');
        card.className = 'foto-card bg-white rounded-lg shadow-md overflow-hidden cursor-pointer transform hover:-translate-y-1 transition-transform duration-300';
        card.onclick = () => showDokumentasiModal(item);
        
        card.innerHTML = `
            <div class="aspect-w-16 aspect-h-12">
                <img src="${item.url_gambar}" alt="${item.judul_kegiatan}" 
                     class="w-full h-48 object-cover" 
                    onerror="this.src='/lstars-portal/assets/images/no-image.png'"
            </div>
            <div class="p-4">
                <h3 class="font-semibold text-gray-800 mb-2 line-clamp-2">${item.judul_kegiatan}</h3>
                <p class="text-gray-600 text-sm mb-3 line-clamp-3">${item.deskripsi}</p>
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        ${item.kategori}
                    </span>
                    <span class="text-xs text-gray-500">
                        ${item.tanggal_formatted || formatTanggalDokumentasi(item.tanggal_kegiatan)}
                    </span>
                </div>
            </div>
        `;
        
        container.appendChild(card);
    });
}

function showDokumentasiModal(item) {
    const modal = document.getElementById('modal-detail-dokumentasi');
    if (!modal) {
        console.error('Modal dokumentasi not found');
        return;
    }
    
    document.getElementById('detail-judul').textContent = item.judul_kegiatan;
    document.getElementById('detail-gambar').src = item.url_gambar;
    document.getElementById('detail-gambar').alt = item.judul_kegiatan;
    document.getElementById('detail-tanggal').textContent = item.tanggal_formatted || formatTanggalDokumentasi(item.tanggal_kegiatan);
    document.getElementById('detail-kategori').textContent = item.kategori;
    document.getElementById('detail-uploader').textContent = item.uploader_nama || item.uploader || 'Admin LSTARS';
    document.getElementById('detail-deskripsi').textContent = item.deskripsi;
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeDokumentasiModal() {
    const modal = document.getElementById('modal-detail-dokumentasi');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

function filterDokumentasiData(kategori) {
    currentDokumentasiFilter = kategori;
    
    if (kategori === 'semua') {
        currentDokumentasiData = dokumentasiData;
    } else {
        currentDokumentasiData = dokumentasiData.filter(item => item.kategori === kategori);
    }
    
    renderDokumentasiGrid(currentDokumentasiData);
    updateDokumentasiCount();
}

function updateDokumentasiCount() {
    const countElement = document.getElementById('dokumentasi-count');
    if (countElement) {
        countElement.textContent = `${currentDokumentasiData.length}`;
    }
}

function initDokumentasiPage() {
    console.log('Initializing dokumentasi page...');
    
    currentDokumentasiFilter = 'semua';
    
    const filterButtons = document.querySelectorAll('.filter-kategori');
    filterButtons.forEach(btn => {
        btn.classList.remove('active', 'bg-blue-500', 'text-white');
        btn.classList.add('bg-white', 'border-gray-300', 'text-gray-700');
    });
    
    const semuaBtn = document.querySelector('.filter-kategori[data-kategori="semua"]');
    if (semuaBtn) {
        semuaBtn.classList.add('active', 'bg-blue-500', 'text-white');
        semuaBtn.classList.remove('bg-white', 'border-gray-300', 'text-gray-700');
    }
    
    loadDokumentasiData();
}

function addDokumentasiEventListeners() {
    const filterButtons = document.querySelectorAll('.filter-kategori');
    filterButtons.forEach(button => {
        button.removeEventListener('click', handleFilterClick);
        button.addEventListener('click', handleFilterClick);
    });

    const closeBtn = document.getElementById('close-modal-dokumentasi');
    if (closeBtn) {
        closeBtn.removeEventListener('click', closeDokumentasiModal);
        closeBtn.addEventListener('click', closeDokumentasiModal);
    }
    
    const modal = document.getElementById('modal-detail-dokumentasi');
    if (modal) {
        modal.removeEventListener('click', handleModalOutsideClick);
        modal.addEventListener('click', handleModalOutsideClick);
    }
}

function handleFilterClick() {
    document.querySelectorAll('.filter-kategori').forEach(btn => {
        btn.classList.remove('active', 'bg-blue-500', 'text-white');
        btn.classList.add('bg-white', 'border-gray-300', 'text-gray-700');
    });
    
    this.classList.add('active', 'bg-blue-500', 'text-white');
    this.classList.remove('bg-white', 'border-gray-300', 'text-gray-700');
    
    filterDokumentasiData(this.dataset.kategori);
}

function handleModalOutsideClick(e) {
    if (e.target === this) {
        closeDokumentasiModal();
    }
}

function tampilkanDokumentasi() {
    console.log('Menampilkan halaman dokumentasi...');
    initDokumentasiPage();
    addDokumentasiEventListeners();
}

// ===================================================================
// FORM EVENT LISTENERS - UPDATED WITH LOGBOOK FORM
// ===================================================================
function addFormEventListeners() {
    if (formEventListenersAdded) return;

    const formPeminjaman = document.getElementById('form-peminjaman');
    if(formPeminjaman) {
        formPeminjaman.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(formPeminjaman);
            const button = formPeminjaman.querySelector('button[type="submit"]');
            const originalText = button.textContent;
            
            if (button.disabled) return;
            
            button.textContent = 'Mengirim...';
            button.disabled = true;
            
            try {
                const response = await fetch('api/proses_peminjaman.php', { method: 'POST', body: formData });
                const result = await response.json();
                
                alert(result.message); 
                
                if (result.success) {
                    formPeminjaman.reset();
                    tampilkanRiwayatPeminjaman();
                    muatOpsiPeminjaman();
                }
            } catch (error) {
                console.error('Error Submit Pinjam:', error);
                alert('Terjadi kesalahan koneksi.');
            } finally {
                button.textContent = originalText;
                button.disabled = false;
            }
        });
    }

    const formIzin = document.getElementById('form-izin');
    if(formIzin) {
        formIzin.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(formIzin);
            const button = formIzin.querySelector('button[type="submit"]');
            const originalText = button.textContent;
            
            if (button.disabled) return;
            
            button.textContent = 'Mengirim...';
            button.disabled = true;
            
            try {
                const response = await fetch('api/proses_izin.php', { 
                    method: 'POST', 
                    body: formData 
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert(result.message);
                    formIzin.reset();
                    await tampilkanRiwayatIzin();
                    setTimeout(refreshDashboardStatistics, 1000);
                } else {
                    alert(result.message || 'Terjadi kesalahan saat mengirim pengajuan.');
                }
                
            } catch (error) {
                console.error('Error Submit Izin:', error);
                alert('Terjadi kesalahan koneksi.');
            } finally {
                button.textContent = originalText;
                button.disabled = false;
            }
        });
    }

    const formLogbook = document.getElementById('form-logbook');
    if (formLogbook) {
        formLogbook.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const button = formLogbook.querySelector('button[type="submit"]');
            const originalText = button.textContent;
            
            if (button.disabled) return;
            
            button.textContent = 'Menyimpan...';
            button.disabled = true;
            
            const formData = new FormData(formLogbook);
            
            try {
                const response = await fetch('api/proses_logbook.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert(result.message);
                    formLogbook.reset();
                    // Panggil fungsi untuk memuat ulang riwayat logbook
                    tampilkanRiwayatLogbook(); 
                } else {
                    alert(result.message || 'Terjadi kesalahan saat menyimpan logbook.');
                }
            } catch (error) {
                console.error('Error saat submit logbook:', error);
                alert('Terjadi kesalahan koneksi.');
            } finally {
                button.textContent = originalText;
                button.disabled = false;
            }
        });
    }

    formEventListenersAdded = true;
}

// Tambahkan fungsi ini di file JavaScript Anda
async function tampilkanRiwayatLogbook() {
    const tbody = document.getElementById('tabel-logbook-body');
    if (!tbody) return;

    // Tampilkan pesan loading
    tbody.innerHTML = `<tr><td colspan="5" class="text-center p-8 text-gray-500">Memuat data logbook...</td></tr>`;

    try {
        const response = await fetch('api/get_riwayat_logbook.php');
        const data = await response.json();

        tbody.innerHTML = ''; // Kosongkan tabel
        
        if (data.success && data.data.length > 0) {
            data.data.forEach(log => {
                const row = `
                    <tr>
                        <td class="py-3 px-4">${log.nama_pengisi || '-'}</td>
                        <td class="py-3 px-4">${log.nim_pengisi || '-'}</td>
                        <td class="py-3 px-4">${log.tanggal_kegiatan || '-'}</td>
                        <td class="py-3 px-4">${log.judul || '-'}</td>
                        <td class="py-3 px-4">${log.deskripsi || '-'}</td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        } else {
            // Jika data kosong atau ada error
            tbody.innerHTML = `<tr><td colspan="5" class="text-center p-8 text-gray-500">Tidak ada riwayat logbook.</td></tr>`;
        }

    } catch (error) {
        console.error('Error saat memuat riwayat logbook:', error);
        tbody.innerHTML = `<tr><td colspan="5" class="text-center p-8 text-red-500">Gagal memuat data.</td></tr>`;
    }
}

// ===================================================================
// PRESENSI PIKET FUNCTIONS (NEW & FIXED)
// ===================================================================

/**
 * Menangani aksi Clock In atau Clock Out.
 * @param {string} action - Aksi yang akan dilakukan ('clock_in' atau 'clock_out').
 */
// GANTI FUNGSI LAMA handlePresensiAction DENGAN VERSI BARU INI

async function handlePresensiAction(action) {
    const namaInput = document.getElementById('presensi-piket-nama');
    const nimInput = document.getElementById('presensi-piket-nim');
    const clockInBtn = document.getElementById('btn-clock-in');
    const clockOutBtn = document.getElementById('btn-clock-out');

    const nama = namaInput.value.trim();
    const nim = nimInput.value.trim();

    if (!nama || !nim) {
        alert('Nama Asisten dan NIM wajib diisi!');
        return;
    }

    clockInBtn.disabled = true;
    clockOutBtn.disabled = true;
    const targetBtn = action === 'clock_in' ? clockInBtn : clockOutBtn;
    const originalBtnText = targetBtn.innerHTML;
    targetBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';

    const formData = new FormData();
    formData.append('nama_asisten', nama);
    formData.append('nim_asisten', nim);
    formData.append('action', action);

    try {
        const response = await fetch('api/proses_presensi.php', {
            method: 'POST',
            body: formData
        });

        // **BAGIAN PERBAIKAN ERROR HANDLING**
        if (!response.ok) {
            // Jika status HTTP bukan 200 OK (misal: 500 Internal Server Error)
            const errorText = await response.text();
            throw new Error(`Server error (HTTP ${response.status}): ${errorText}`);
        }

        const result = await response.json();
        // **AKHIR PERBAIKAN**

        alert(result.message);

        if (result.success) {
            namaInput.value = '';
            nimInput.value = '';
            await tampilkanRiwayatPresensiHariIni();
        }
    } catch (error) {
        // **Pesan error yang lebih informatif di console**
        console.error('Terjadi kesalahan saat proses presensi:', error);
        alert('Terjadi kesalahan koneksi atau respons server tidak valid. Silakan coba lagi dan periksa console (F12) untuk detail.');
    } finally {
        targetBtn.innerHTML = originalBtnText;
        clockInBtn.disabled = false;
        clockOutBtn.disabled = false;
    }
}

/**
 * Memuat dan menampilkan riwayat presensi untuk hari ini.
 */
async function tampilkanRiwayatPresensiHariIni() {
    const tbody = document.getElementById('tabel-riwayat-presensi-body');
    if (!tbody) return;

    tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4">Memuat riwayat...</td></tr>`;

    try {
        // Menggunakan API yang sudah ada untuk mengambil data hari ini
        const response = await fetch('api/get_riwayat_presensi.php'); // Pastikan file ini mengambil data dari DB, bukan session
        const data = await response.json();

        if (!Array.isArray(data) || data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-gray-500">Belum ada riwayat presensi hari ini.</td></tr>`;
            return;
        }

        tbody.innerHTML = ''; // Kosongkan tabel
        data.forEach(item => {
            const row = `
                <tr class="hover:bg-gray-50">
                    <td class="py-3 px-4">${item.nama_asisten || '-'}</td>
                    <td class="py-3 px-4">${item.nim_asisten || '-'}</td>
                    <td class="py-3 px-4">${item.tanggal_formatted || '-'}</td>
                    <td class="py-3 px-4 text-center text-green-600 font-medium">${item.waktu_masuk_formatted || '-'}</td>
                    <td class="py-3 px-4 text-center text-red-600 font-medium">${item.waktu_keluar_formatted || '<i class="text-gray-500">Belum Clock Out</i>'}</td>
                    <td class="py-3 px-4 text-center font-semibold">${item.durasi || '-'}</td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
    } catch (error) {
        console.error('Error fetching presensi history:', error);
        tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-red-500">Gagal memuat riwayat.</td></tr>`;
    }
}

/**
 * Fungsi utama untuk inisialisasi halaman presensi piket.
 */
function initializePresensiPage() {
    console.log('Initializing Presensi Piket Page...');
    
    // Panggil fungsi untuk menampilkan riwayat saat halaman dibuka
    tampilkanRiwayatPresensiHariIni();

    const clockInBtn = document.getElementById('btn-clock-in');
    const clockOutBtn = document.getElementById('btn-clock-out');

    // Tambahkan event listener hanya jika belum ada untuk menghindari duplikasi
    if (clockInBtn && !clockInBtn.hasAttribute('data-listener-added')) {
        clockInBtn.addEventListener('click', () => handlePresensiAction('clock_in'));
        clockInBtn.setAttribute('data-listener-added', 'true');
    }
    
    if (clockOutBtn && !clockOutBtn.hasAttribute('data-listener-added')) {
        clockOutBtn.addEventListener('click', () => handlePresensiAction('clock_out'));
        clockOutBtn.setAttribute('data-listener-added', 'true');
    }
}

// ===================================================================
// PRESENSI PRAKTIKUM FUNCTIONS (NEW)
// ===================================================================

async function handlePraktikumAction(action) {
    const namaInput = document.getElementById('presensi-praktikum-nama');
    const nimInput = document.getElementById('presensi-praktikum-nim');
    const praktikumInput = document.getElementById('mata-praktikum');
    
    const clockInBtn = document.getElementById('btn-clock-in-praktikum');
    const clockOutBtn = document.getElementById('btn-clock-out-praktikum');

    const nama = namaInput.value.trim();
    const nim = nimInput.value.trim();
    const praktikum = praktikumInput.value.trim();

    if (!nama || !nim || !praktikum) {
        alert('Nama Mahasiswa, NIM, dan Mata Praktikum wajib diisi.');
        return;
    }

    clockInBtn.disabled = true;
    clockOutBtn.disabled = true;
    const originalBtnText = action === 'clock_in' ? clockInBtn.innerHTML : clockOutBtn.innerHTML;
    const targetBtn = action === 'clock_in' ? clockInBtn : clockOutBtn;
    targetBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';

    const formData = new FormData();
    formData.append('nama_mahasiswa', nama);
    formData.append('nim_mahasiswa', nim);
    formData.append('mata_praktikum', praktikum);
    formData.append('action', action);

    try {
        const response = await fetch('api/proses_presensi_praktikum.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            alert(result.message);
            namaInput.value = '';
            nimInput.value = '';
            praktikumInput.value = '';
            await tampilkanRiwayatPraktikum();
        } else {
            alert(result.message);
        }
    } catch (error) {
        console.error('Praktikum action error:', error);
        alert('Terjadi kesalahan koneksi.');
    } finally {
        targetBtn.innerHTML = originalBtnText;
        clockInBtn.disabled = false;
        clockOutBtn.disabled = false;
    }
}

async function tampilkanRiwayatPraktikum() {
    const tbody = document.getElementById('tabel-riwayat-presensi-praktikum-body');
    if (!tbody) return;

    tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4">Memuat riwayat...</td></tr>`;

    try {
        const response = await fetch('api/get_riwayat_praktikum.php');
        const data = await response.json();

        if (!Array.isArray(data) || data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-gray-500">Belum ada presensi praktikum hari ini.</td></tr>`;
            return;
        }

        tbody.innerHTML = '';
        data.forEach(item => {
            const row = `
                <tr class="hover:bg-gray-50">
                    <td class="py-3 px-4">${item.nama_mahasiswa || '-'}</td>
                    <td class="py-3 px-4">${item.nim_mahasiswa || '-'}</td>
                    <td class="py-3 px-4">${item.mata_praktikum || '-'}</td>
                    <td class="py-3 px-4 text-center text-green-600 font-medium">${item.waktu_masuk_formatted || '-'}</td>
                    <td class="py-3 px-4 text-center text-red-600 font-medium">${item.waktu_keluar_formatted || '<i class="text-gray-500">Belum Clock Out</i>'}</td>
                    <td class="py-3 px-4 text-center font-semibold">${item.durasi || '-'}</td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
    } catch (error) {
        console.error('Error fetching praktikum history:', error);
        tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-red-500">Gagal memuat riwayat.</td></tr>`;
    }
}

function initializePraktikumPage() {
    console.log('Initializing Presensi Praktikum Page...');
    const clockEl = document.getElementById('current-time-date-praktikum');
    if (clockEl) {
        const now = new Date();
        clockEl.textContent = now.toLocaleDateString('id-ID', {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
            hour: '2-digit', minute: '2-digit', second: '2-digit'
        });
    }

    tampilkanRiwayatPraktikum();

    const clockInBtn = document.getElementById('btn-clock-in-praktikum');
    const clockOutBtn = document.getElementById('btn-clock-out-praktikum');

    if (clockInBtn && !clockInBtn.hasAttribute('data-listener-added')) {
        clockInBtn.addEventListener('click', () => handlePraktikumAction('clock_in'));
        clockInBtn.setAttribute('data-listener-added', 'true');
    }
    
    if (clockOutBtn && !clockOutBtn.hasAttribute('data-listener-added')) {
        clockOutBtn.addEventListener('click', () => handlePraktikumAction('clock_out'));
        clockOutBtn.setAttribute('data-listener-added', 'true');
    }
}

// ===================================================================
// ENHANCED NAVIGATION LOGIC - UPDATED WITH DASHBOARD REFRESH
// ===================================================================
function initializeNavigation() {
    const sidebar = document.getElementById('sidebar');
    const menuButton = document.getElementById('menu-button');
    const mainNav = document.getElementById('main-nav');
    const contentArea = document.getElementById('content-area');
    const pageTitle = document.getElementById('page-title');
    const pages = contentArea?.querySelectorAll('.page') || [];
    const navLinks = mainNav?.querySelectorAll('.nav-link') || [];

    console.log('🧭 Initializing enhanced navigation...');

    // Mobile menu toggle
    if (menuButton && sidebar) {
        menuButton.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
            console.log('📱 Mobile menu toggled');
        });
    }

    // Navigation click handler
    if (mainNav) {
        mainNav.addEventListener('click', function(e) {
            const link = e.target.closest('.nav-link');
            if (!link) return;
            e.preventDefault();
            
            const pageId = link.dataset.page;
            console.log(`🔄 Navigating to: ${pageId}`);
            
            // Update active navigation
            navLinks.forEach(l => l.classList.remove('active'));
            link.classList.add('active');
            
            // Update page title
            if (pageTitle) {
                const spanText = link.querySelector('span');
                if (spanText) {
                    pageTitle.textContent = spanText.textContent;
                }
            }
            
            // Show selected page
            pages.forEach(p => {
                if (p.id === `page-${pageId}`) {
                    p.classList.remove('hidden');
                } else {
                    p.classList.add('hidden');
                }
            });

            // Page-specific initialization
            switch(pageId) {
                case 'dashboard':
                    setTimeout(() => {
                        loadUserDashboardData(); // ← Panggil fungsi baru yang real
                        initializeClock();
                    }, 100);
                    break;
                case 'dosen':
                    tampilkanDataDosen();
                    break;
                case 'asisten':
                    tampilkanDataAsisten();
                    break;
                case 'inventory':
                    tampilkanDataInventory();
                    break;
                case 'peminjaman':
                    muatOpsiPeminjaman();
                    tampilkanRiwayatPeminjaman();
                    break;
                case 'izin':
                    muatOpsiDosen();
                    tampilkanRiwayatIzin();
                    // Refresh statistik karena mungkin ada perubahan izin
                    setTimeout(refreshDashboardStatistics, 500);
                    break;
                case 'presensi':
                    initializePresensiPage(); // Ganti dengan fungsi baru ini
                    break;
                case 'presensi_praktikum':
                    initializePraktikumPage(); // Pastikan memanggil fungsi ini
                    break;
                case 'LogbookKegiatanLab':
                    if (typeof tampilkanRiwayatLogbook === 'function') {
                        tampilkanRiwayatLogbook();
                    }
                    break;
                case 'dokumentasi':
                    tampilkanDokumentasi();
                    break;
                }

            // Close mobile menu
            if (window.innerWidth < 768 && sidebar) {
                sidebar.classList.add('-translate-x-full');
            }
        });
    }

    console.log('✅ Enhanced navigation initialized');
}

// ===================================================================
// EVENT LISTENER UNTUK TOMBOL AKSI
// ===================================================================
function initializeActionButtons() {
    const contentArea = document.getElementById('content-area');
    if (!contentArea) return;

    contentArea.addEventListener('click', async function(e) {
        // Handle tombol kembalikan peminjaman
        if (e.target.classList.contains('btn-kembalikan')) {
            const button = e.target;
            const peminjamanId = button.dataset.peminjamanId;
            const inventoryId = button.dataset.inventoryId;

            if (!window.confirm('Anda yakin ingin mengembalikan barang ini?')) return;
            
            const originalText = button.textContent;
            
            if (button.disabled) return;
            
            button.textContent = 'Memproses...';
            button.disabled = true;

            const formData = new FormData();
            formData.append('peminjaman_id', peminjamanId);
            formData.append('inventory_id', inventoryId);

            try {
                const response = await fetch('api/proses_pengembalian.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                alert(result.message);

                if (result.success) {
                    tampilkanRiwayatPeminjaman();
                    muatOpsiPeminjaman();
                    // Refresh dashboard statistics
                    refreshDashboardStatistics();
                } else {
                    button.textContent = originalText;
                    button.disabled = false;
                }
            } catch (error) {
                console.error('Error Pengembalian:', error);
                alert('Terjadi kesalahan koneksi.');
                button.textContent = originalText;
                button.disabled = false;
            }
        }

        // Handle tombol setujui/tolak izin penelitian
        if (e.target.classList.contains('btn-setujui') || e.target.classList.contains('btn-tolak')) {
            const button = e.target;
            const izinId = button.dataset.id;
            const action = button.dataset.action;
            const actionText = action === 'setujui' ? 'menyetujui' : 'menolak';
            
            if (!window.confirm(`Anda yakin ingin ${actionText} pengajuan izin ini?`)) return;
            
            const originalText = button.textContent;
            
            if (button.disabled) return;
            
            button.textContent = 'Memproses...';
            button.disabled = true;

            const formData = new FormData();
            formData.append('izin_id', izinId);
            formData.append('action', action);
            formData.append('user_id', 1);

            try {
                const response = await fetch('api/proses_aksi_izin.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                alert(result.message);

                if (result.success) {
                    tampilkanRiwayatIzin();
                    // Refresh dashboard statistics setelah approve/reject
                    refreshDashboardStatistics();
                } else {
                    button.textContent = originalText;
                    button.disabled = false;
                }
            } catch (error) {
                console.error('Error Aksi Izin:', error);
                alert('Terjadi kesalahan koneksi.');
                button.textContent = originalText;
                button.disabled = false;
            }
        }

        // Handle tombol presensi (Clock In/Clock Out)
        if (e.target && e.target.id === 'btn-presensi') {
            const button = e.target;
            const action = button.dataset.action;
            const presensiId = button.dataset.id;

            button.textContent = 'Memproses...';
            button.disabled = true;

            const formData = new FormData();
            formData.append('action', action);
            if (presensiId) {
                formData.append('presensi_id', presensiId);
            }

            try {
                const response = await fetch('api/proses_presensi.php', { method: 'POST', body: formData });
                const result = await response.json();
                alert(result.message);
                muatHalamanPresensi();
            } catch (error) {
                console.error('Error Presensi:', error);
                alert('Terjadi kesalahan koneksi.');
                muatHalamanPresensi();
            }
        }
    });
}

// ===================================================================
// ENHANCED INITIALIZATION - MAIN ENTRY POINT
// ===================================================================
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 LSTARS Portal System Starting...');
    
    // Run session check first
    cekSession();
    
    // Initialize components with proper sequencing
    setTimeout(() => {
        console.log('⏰ Starting enhanced clock...');
        initializeClock();
        
        console.log('👤 Loading user data...');
        loadUserDashboardData(); // ← Menggunakan fungsi real API
        
        console.log('🧭 Setting up enhanced navigation...');
        initializeNavigation();
        
        console.log('⚡ Initializing action buttons...');
        initializeActionButtons();
        
        console.log('📝 Adding form event listeners...');
        addFormEventListeners();
        
        console.log('✅ LSTARS Portal System Ready!');
        
    }, 300);
    
    // Additional check after 1 second
    setTimeout(() => {
        if (!isUserDataLoaded) {
            console.log('⚠️ Retrying user data load...');
            loadUserDashboardData();
        }
    }, 1000);
});

// ===================================================================
// GLOBAL EVENT LISTENERS
// ===================================================================

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDokumentasiModal();
    }
});

// ===================================================================
// GLOBAL FUNCTIONS FOR DEBUGGING AND TESTING
// ===================================================================
window.loadDokumentasiData = loadDokumentasiData;
window.closeDokumentasiModal = closeDokumentasiModal;
window.filterDokumentasiData = filterDokumentasiData;

// Enhanced dashboard functions
window.refreshDashboardStatistics = refreshDashboardStatistics;
window.loadUserDashboardData = loadUserDashboardData;

window.reloadDashboard = function() {
    console.log('🔄 Manually reloading dashboard...');
    loadUserDashboardData();
    initializeClock();
};

window.testDashboardAPI = async function() {
    console.log('🧪 Testing Dashboard API...');
    try {
        const response = await fetch('api/get_dashboard_data.php');
        const result = await response.json();
        console.log('📊 API Response:', result);
        return result;
    } catch (error) {
        console.error('❌ API Test Error:', error);
        return null;
    }
};

window.debugInfo = function() {
    console.log('🔍 Debug Info:');
    console.log('- Clock running:', !!clockInterval);
    console.log('- User data loaded:', isUserDataLoaded);
    console.log('- User name found:', getUserNameFromHeader());
    console.log('- Current time element:', document.getElementById('currentTime')?.textContent);
    console.log('- Form listeners added:', formEventListenersAdded);
};

// ===================================================================
// ERROR HANDLING
// ===================================================================
window.addEventListener('error', function(e) {
    console.error('❌ JavaScript Error:', e.error);
});

console.log('📦 LSTARS Portal - Complete Integrated Script with Real Data Loaded Successfully!');