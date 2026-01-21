// Fungsi Logout
async function logout() {
    try {
        await fetch('api/logout.php', { method: 'POST' });
        window.location.href = 'login.html';
    } catch (error) {
        console.error('Logout error:', error);
        window.location.href = 'login.html';
    }
}

// ===================================================================
// UTILITY FUNCTIONS
// ===================================================================
function showLoading(element) {
    if (element && element.closest('table')) {
        const colspan = element.closest('table').querySelector('th').parentElement.children.length;
        element.innerHTML = `<tr><td colspan="${colspan}" class="text-center py-4">Memuat data...</td></tr>`;
    }
}

function showError(element, message = 'Gagal memuat data.') {
    if (element && element.closest('table')) {
        const colspan = element.closest('table').querySelector('th').parentElement.children.length;
        element.innerHTML = `<tr><td colspan="${colspan}" class="text-center py-4 text-red-500">${message}</td></tr>`;
    }
}


// ===================================================================
// REAL-TIME CLOCK FUNCTIONS
// ===================================================================

function updateClock() {
    const now = new Date();
    
    // Format waktu (HH:MM:SS)
    const time = now.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false
    });
    
    // Format tanggal (DD MMMM YYYY)
    const date = now.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
    
    // Format hari
    const day = now.toLocaleDateString('id-ID', {
        weekday: 'long'
    });
    
    // Update semua elemen jam
    const timeElements = [
        document.getElementById('currentTime'),
        document.getElementById('timeWidget')
    ];
    
    const dateElements = [
        document.getElementById('currentDate'),
        document.getElementById('dateWidget')
    ];
    
    const dayElement = document.getElementById('currentDay');
    
    timeElements.forEach(el => {
        if (el) {
            el.textContent = time;
            // Tambahkan animasi subtle
            el.style.transform = 'scale(1.02)';
            setTimeout(() => {
                el.style.transform = 'scale(1)';
            }, 100);
        }
    });
    
    dateElements.forEach(el => {
        if (el) el.textContent = date;
    });
    
    if (dayElement) {
        dayElement.textContent = day;
    }
}

function initializeClock() {
    // Update jam setiap detik
    updateClock(); // Initial call
    setInterval(updateClock, 1000);
    
    console.log('🕐 Real-time clock initialized');
}



// ===================================================================
// DASHBOARD FUNCTIONS
// ===================================================================
async function loadDashboardData() {
    try {
        console.log('🔄 Loading dashboard statistics...');
        
        // Initialize clock first
        initializeClock();
        
        const response = await fetch('api/get_dashboard_stats.php');
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const result = await response.json();
        console.log('📊 Dashboard stats loaded:', result);
        
        const stats = result.data || {};
        
        // Update statistics cards dengan animasi
        const updateCardWithAnimation = (elementId, value) => {
            const el = document.getElementById(elementId);
            if (el) {
                el.style.transform = 'scale(0.8)';
                el.style.opacity = '0.5';
                
                setTimeout(() => {
                    el.textContent = value || '0';
                    el.style.transform = 'scale(1)';
                    el.style.opacity = '1';
                    el.style.transition = 'all 0.3s ease';
                }, 150);
            }
        };
        
        updateCardWithAnimation('pendingCount', stats.perizinan_pending);
        updateCardWithAnimation('usersCount', stats.total_users);
        updateCardWithAnimation('activeLabCount', stats.asisten_aktif);
        updateCardWithAnimation('equipmentCount', stats.total_equipment);
        
        // Load recent activities
        await loadRecentActivities();
        
        console.log('✅ Dashboard loaded successfully!');
        
    } catch (error) {
        console.error('❌ Error loading dashboard:', error);
        
        // Initialize clock even if stats fail
        initializeClock();
        
        // Fallback to default values
        const elements = ['pendingCount', 'usersCount', 'activeLabCount', 'equipmentCount'];
        elements.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.textContent = '-';
        });
    }
}

async function loadRecentActivities() {
    const recentActivitiesEl = document.getElementById('recentActivities');
    if (!recentActivitiesEl) return;
    
    recentActivitiesEl.innerHTML = 'Memuat aktivitas terbaru...';
    
    try {
        const response = await fetch('api/get_recent_activities.php');
        const result = await response.json();
        
        if (result.success && result.data.length > 0) {
            const activities = result.data.slice(0, 5); // Ambil 5 terbaru
            recentActivitiesEl.innerHTML = `
                <div class="space-y-3">
                    ${activities.map(activity => `
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                            <i class="fas ${activity.icon} ${activity.color} text-lg"></i>
                            <div class="flex-1">
                                <p class="text-sm text-gray-700">${activity.description}</p>
                                <p class="text-xs text-gray-500">${activity.time}</p>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;
        } else {
            recentActivitiesEl.innerHTML = `
                <div class="text-center py-4 text-gray-500">
                    <i class="fas fa-info-circle mb-2"></i>
                    <p>Belum ada aktivitas terbaru</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error loading recent activities:', error);
        recentActivitiesEl.innerHTML = `
            <div class="text-center py-4 text-gray-500">
                <i class="fas fa-exclamation-triangle mb-2"></i>
                <p>Gagal memuat aktivitas terbaru</p>
            </div>
        `;
    }
}

// ===================================================================
// INVENTORY FUNCTIONS - FIXED VERSION (No Categories, No Filters)
// ===================================================================

// Global variable untuk menyimpan data inventory
let inventoryData = [];

// Function untuk menampilkan modal inventory
function showModalInventory(isEdit = false, data = {}) {
    console.log('🔄 Opening inventory modal...', { isEdit, data });
    
    const modal = document.getElementById('modal-inventory');
    const modalTitle = document.getElementById('modal-title-inventory');
    const form = document.getElementById('form-inventory');
   
    // Debug: Check if elements exist
    console.log('Modal elements check:', {
        modal: !!modal,
        modalTitle: !!modalTitle,
        form: !!form
    });
   
    if (!modal || !modalTitle || !form) {
        console.error('❌ Modal inventory elements tidak ditemukan!');
        alert('Error: Modal inventory tidak ditemukan. Pastikan HTML modal sudah ditambahkan ke halaman.');
        return;
    }

    // Set modal title
    modalTitle.textContent = isEdit ? 'Edit Item Inventory' : 'Tambah Item Inventory';
   
    if (isEdit && data && data.id) {
        // Mode Edit: Isi form dengan data yang ada
        console.log('📝 Filling form for edit mode:', data);
        
        document.getElementById('inventory-id').value = data.id || '';
        document.getElementById('nama-alat').value = data.nama_alat || '';
        document.getElementById('kode-alat').value = data.kode_alat || '';
        document.getElementById('jumlah-total').value = data.jumlah_total || 1;
        document.getElementById('status-inventory').value = data.status || 'Tersedia';
        document.getElementById('lokasi').value = data.lokasi || '';
        
    } else {
        // Mode Tambah: Reset form
        console.log('🆕 Resetting form for add mode');
        form.reset();
        document.getElementById('inventory-id').value = '';
        document.getElementById('jumlah-total').value = 1;
        document.getElementById('status-inventory').value = 'Tersedia';
    }
   
    // Show modal
    modal.classList.remove('hidden');
    console.log('✅ Modal inventory opened successfully');
    
    // Focus pada field pertama
    setTimeout(() => {
        const firstInput = document.getElementById('nama-alat');
        if (firstInput) firstInput.focus();
    }, 100);
}

// Function untuk menyembunyikan modal inventory
function hideModalInventory() {
    const modal = document.getElementById('modal-inventory');
    if (modal) {
        modal.classList.add('hidden');
        console.log('❌ Modal inventory closed');
    }
}

// Function untuk menyimpan data inventory - ENHANCED DEBUGGING
async function saveInventory(formData) {
    console.log('💾 Saving inventory data...');
    
    try {
        const inventoryId = formData.get('inventory_id');
        const isEdit = inventoryId && inventoryId.trim() !== '' && inventoryId !== '0';
        
        console.log('📋 Form data entries:');
        for (let [key, value] of formData.entries()) {
            console.log(`  ${key}: "${value}"`);
        }
        console.log('🔄 Operation type:', isEdit ? 'UPDATE' : 'CREATE');
        console.log('🆔 Inventory ID:', inventoryId, '(isEdit:', isEdit, ')');
       
        const endpoint = isEdit ? 'api/update_inventory.php' : 'api/add_inventory.php';
        console.log('🎯 Endpoint:', endpoint);
        
        // Show loading state
        const submitBtn = document.querySelector('#form-inventory button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
        
        const response = await fetch(endpoint, {
            method: 'POST',
            body: formData
        });
       
        console.log('📡 Response status:', response.status);
        console.log('📡 Response ok:', response.ok);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        // Get response text first for debugging
        const responseText = await response.text();
        console.log('📝 Raw response:', responseText);
        
        let result;
        try {
            result = JSON.parse(responseText);
        } catch (parseError) {
            console.error('❌ JSON Parse Error:', parseError);
            console.error('❌ Response was:', responseText);
            throw new Error('Server returned invalid JSON: ' + responseText.substring(0, 100));
        }
        
        console.log('📤 Parsed result:', result);
       
        if (result.success) {
            alert(result.message || 'Data inventory berhasil disimpan!');
            hideModalInventory();
            
            // Reload inventory data
            await loadInventoryData();
        } else {
            console.error('❌ Server error details:', result);
            let errorMsg = result.message || 'Gagal menyimpan data inventory';
            if (result.debug) {
                console.error('❌ Debug info:', result.debug);
                errorMsg += '\nDebug: ' + JSON.stringify(result.debug);
            }
            alert('Error: ' + errorMsg);
        }
        
        // Reset button
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
        
    } catch (error) {
        console.error('❌ Error saving inventory:', error);
        alert('Terjadi kesalahan saat menyimpan data:\n' + error.message);
        
        // Reset button
        const submitBtn = document.querySelector('#form-inventory button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i>Simpan';
        }
    }
}

// Function untuk menghapus data inventory
async function deleteInventory(inventoryId, namaAlat) {
    if (!confirm(`Apakah Anda yakin ingin menghapus item "${namaAlat}"?\n\nTindakan ini tidak dapat dibatalkan.`)) {
        return;
    }
    
    console.log('🗑️ Deleting inventory:', inventoryId, namaAlat);
    
    try {
        const formData = new FormData();
        formData.append('inventory_id', inventoryId);
        
        const response = await fetch('api/delete_inventory.php', {
            method: 'POST',
            body: formData
        });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const result = await response.json();
        
        if (result.success) {
            alert(result.message || 'Item berhasil dihapus!');
            // Reload inventory data
            await loadInventoryData();
        } else {
            alert('Error: ' + (result.message || 'Gagal menghapus data'));
        }
        
    } catch (error) {
        console.error('❌ Error deleting inventory:', error);
        alert('Terjadi kesalahan saat menghapus data: ' + error.message);
    }
}

// Function untuk memuat data inventory dari API - FIXED VERSION
async function loadInventoryData() {
    console.log('🔄 Loading inventory data from API...');
    const tbody = document.getElementById('tabel-inventory-body');
   
    if (!tbody) {
        console.error('❌ Table body element not found!');
        return;
    }
   
    // Show loading - Updated colspan to 6
    tbody.innerHTML = `
        <tr>
            <td colspan="6" class="py-8 text-center">
                <div class="flex justify-center items-center">
                    <i class="fas fa-spinner fa-spin text-2xl text-blue-500 mr-3"></i>
                    <span class="text-gray-600">Memuat data inventory...</span>
                </div>
            </td>
        </tr>
    `;
   
    try {
        console.log('📡 Fetching from: api/get_inventory.php');
        
        const response = await fetch('api/get_inventory.php', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
       
        console.log('📡 Response status:', response.status);
        console.log('📡 Response ok:', response.ok);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        // Get raw response first
        const responseText = await response.text();
        console.log('📝 Raw response length:', responseText.length);
        console.log('📝 Raw response preview:', responseText.substring(0, 200));
        
        let data;
        try {
            data = JSON.parse(responseText);
        } catch (parseError) {
            console.error('❌ JSON Parse Error:', parseError);
            console.error('❌ Raw response:', responseText);
            throw new Error('Server returned invalid JSON');
        }
        
        console.log('📦 Parsed data:', data);
        console.log('📦 Data type:', typeof data);
        console.log('📦 Is array:', Array.isArray(data));
        console.log('📦 Data length:', data.length);
        
        // Store data globally
        inventoryData = Array.isArray(data) ? data : [];
       
        // Render table
        renderInventoryTable(inventoryData);
        
        console.log('✅ Inventory table rendered successfully with', inventoryData.length, 'items');

    } catch (error) {
        console.error('❌ Error loading inventory data:', error);
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="py-8 text-center">
                    <div class="flex flex-col justify-center items-center text-red-600">
                        <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                        <span class="font-medium">Gagal memuat data inventory</span>
                        <span class="text-sm text-gray-500 mt-1">${error.message}</span>
                        <button onclick="loadInventoryData()" class="mt-3 bg-blue-500 text-white px-4 py-2 rounded text-sm hover:bg-blue-600">
                            <i class="fas fa-refresh mr-1"></i>Coba Lagi
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }
}

// Function untuk render tabel inventory - NO KATEGORI COLUMN & NO BLUE HEADERS
function renderInventoryTable(data) {
    const tbody = document.getElementById('tabel-inventory-body');
    
    if (!data || data.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="py-8 text-center">
                    <div class="flex justify-center items-center text-gray-600">
                        <i class="fas fa-info-circle text-2xl mr-3"></i>
                        <span>Belum ada data inventory. Klik "Tambah Item" untuk menambahkan data baru.</span>
                    </div>
                </td>
            </tr>
        `;
        return;
    }

    // Clear table
    tbody.innerHTML = '';

    // Render data tanpa header kategori biru dan tanpa kolom kategori
    data.forEach(item => {
        const row = createInventoryRowNoKategori(item);
        tbody.innerHTML += row;
    });

    // Attach event listeners to new buttons
    attachInventoryButtonListeners();
}

// Function untuk membuat row tabel inventory - NO KATEGORI COLUMN
function createInventoryRowNoKategori(item) {
    // Determine status
    let statusClass, statusText;
    const tersedia = parseInt(item.jumlah_tersedia) || 0;
    const total = parseInt(item.jumlah_total) || 1;
    
    if (item.has_quantity_system) {
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
            case 'Maintenance':
                statusClass = 'bg-orange-200 text-orange-800'; 
                statusText = 'Maintenance'; 
                break;
            default: 
                statusClass = 'bg-gray-200 text-gray-800'; 
                statusText = item.status || 'Tidak Diketahui';
        }
    }

    // Format quantity
    const jumlahDisplay = item.has_quantity_system ? 
        `${tersedia} / ${total}` : 
        total.toString();

    // Escape HTML
    const escapeHtml = (text) => {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    };

    return `
        <tr class="hover:bg-gray-50 transition-colors">
            <td class="py-3 px-4 font-medium">${escapeHtml(item.nama_alat || '-')}</td>
            <td class="py-3 px-4 text-gray-600">${escapeHtml(item.kode_alat || '-')}</td>
            <td class="py-3 px-4 text-center">
                <span class="font-medium text-blue-600">${jumlahDisplay}</span>
            </td>
            <td class="py-3 px-4">
                <span class="${statusClass} py-1 px-3 rounded-full text-xs font-medium">
                    ${statusText}
                </span>
            </td>
            <td class="py-3 px-4 text-gray-600">${escapeHtml(item.lokasi || '-')}</td>
            <td class="py-3 px-4 text-center">
                <div class="flex justify-center space-x-2">
                    <button class="btn-edit-inventory bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded text-xs transition-colors"
                            data-id="${item.id}" 
                            data-nama-alat="${escapeHtml(item.nama_alat || '')}"
                            data-kode-alat="${escapeHtml(item.kode_alat || '')}"
                            data-jumlah-total="${item.jumlah_total || 1}"
                            data-jumlah-tersedia="${item.jumlah_tersedia || 1}"
                            data-status="${escapeHtml(item.status || '')}"
                            data-lokasi="${escapeHtml(item.lokasi || '')}"
                            title="Edit item ini">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn-delete-inventory bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs transition-colors"
                            data-id="${item.id}" 
                            data-nama="${escapeHtml(item.nama_alat || '')}"
                            title="Hapus item ini">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </div>
            </td>
        </tr>
    `;
}

// Function untuk attach event listeners pada button
function attachInventoryButtonListeners() {
    console.log('🔧 Attaching inventory button listeners...');
    
    // Edit buttons
    document.querySelectorAll('.btn-edit-inventory').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const data = {
                id: this.dataset.id,
                nama_alat: this.dataset.namaAlat,
                kode_alat: this.dataset.kodeAlat,
                jumlah_total: this.dataset.jumlahTotal,
                jumlah_tersedia: this.dataset.jumlahTersedia,
                status: this.dataset.status,
                lokasi: this.dataset.lokasi
            };
            
            console.log('✏️ Edit button clicked:', data);
            showModalInventory(true, data);
        });
    });
    
    // Delete buttons
    document.querySelectorAll('.btn-delete-inventory').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const inventoryId = this.dataset.id;
            const namaAlat = this.dataset.nama;
            
            console.log('🗑️ Delete button clicked:', { inventoryId, namaAlat });
            deleteInventory(inventoryId, namaAlat);
        });
    });
    
    console.log('✅ Button listeners attached');
}

// Function untuk initialize inventory system
function initializeInventorySystem() {
    console.log('🚀 Initializing inventory system...');
    
    // Check if we're on inventory page
    const inventoryPage = document.getElementById('page-inventory');
    if (!inventoryPage) {
        console.log('⚠️ Not on inventory page, skipping initialization');
        return;
    }
    
    // Load data if on inventory page and page is visible
    if (!inventoryPage.classList.contains('hidden')) {
        loadInventoryData();
    }
    
    // Add button event listener
    const btnTambah = document.getElementById('btn-tambah-inventory');
    if (btnTambah) {
        // Remove existing listeners
        btnTambah.replaceWith(btnTambah.cloneNode(true));
        const newBtnTambah = document.getElementById('btn-tambah-inventory');
        
        newBtnTambah.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('➕ Add inventory button clicked');
            showModalInventory(false);
        });
        
        console.log('✅ Add button listener attached');
    }
    
    // Add form event listener
    const form = document.getElementById('form-inventory');
    if (form) {
        // Remove existing listeners
        form.replaceWith(form.cloneNode(true));
        const newForm = document.getElementById('form-inventory');
        
        newForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            console.log('📝 Form submitted');
            
            const formData = new FormData(this);
            await saveInventory(formData);
        });
        
        console.log('✅ Form submit listener attached');
    }
    
    // Add modal close listeners
    const btnClose = document.getElementById('btn-close-modal-inventory');
    if (btnClose) {
        btnClose.addEventListener('click', hideModalInventory);
    }
    
    const btnCancel = document.getElementById('btn-cancel-inventory');
    if (btnCancel) {
        btnCancel.addEventListener('click', hideModalInventory);
    }
    
    // Add modal overlay listener
    const modal = document.getElementById('modal-inventory');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                hideModalInventory();
            }
        });
    }
    
    console.log('✅ Inventory system initialized');
}

// Function yang dipanggil ketika page inventory dibuka
function tampilkanDataInventory() {
    console.log('📄 Opening inventory page...');
    loadInventoryData();
}

// ===================================================================
// INITIALIZATION
// ===================================================================

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('🌐 DOM loaded, initializing inventory...');
    
    // Initialize with delay to ensure all elements are ready
    setTimeout(() => {
        initializeInventorySystem();
    }, 500);
});

// Initialize when page is shown (for SPA behavior)
document.addEventListener('pageshow', function() {
    const inventoryPage = document.getElementById('page-inventory');
    if (inventoryPage && !inventoryPage.classList.contains('hidden')) {
        console.log('📄 Inventory page shown, loading data...');
        loadInventoryData();
    }
});

// ===================================================================
// LOGBOOK ADMIN FUNCTIONS
// ===================================================================
async function tampilkanLogbookAdmin() {
    console.log('📖 Memuat semua logbook untuk admin...');
    const tbody = document.getElementById('tabel-logbook-admin-body');
    
    // Periksa apakah elemen tabel ada sebelum melanjutkan
    if (!tbody) {
        console.error('Elemen #tabel-logbook-admin-body tidak ditemukan!');
        return;
    }

    // Tampilkan pesan "Memuat data..."
    tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4">Memuat data...</td></tr>';

    try {
        const response = await fetch('api/get_admin_logbook.php');
        
        if (!response.ok) {
             throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();

        tbody.innerHTML = ''; // Kosongkan tabel sebelum diisi

        if (result.success && result.data.length > 0) {
            console.log('✅ Data logbook diterima:', result.data);
            result.data.forEach(log => {
                const row = `
                    <tr class="hover:bg-gray-50 border-b">
                        <td class="py-3 px-4">${log.nama_pengisi || '-'}</td>
                        <td class="py-3 px-4">${log.nim_pengisi || '-'}</td>
                        <td class="py-3 px-4">${log.tanggal_kegiatan || '-'}</td>
                        <td class="py-3 px-4 font-medium text-gray-800">${log.judul || '-'}</td>
                        <td class="py-3 px-4 text-sm text-gray-600">${log.deskripsi || '-'}</td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        } else if (!result.success) {
            // Jika API mengembalikan error
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-red-500">Error: ${result.message}</td></tr>`;
        } else {
            // Jika data berhasil diambil tapi kosong
            tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4">Belum ada data logbook yang dibuat oleh user.</td></tr>';
        }

    } catch (error) {
        console.error('Error saat memuat logbook admin:', error);
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-red-500">Gagal terhubung ke server. Periksa koneksi atau lihat console (F12).</td></tr>';
    }
}

// ===================================================================
// IZIN PENELITIAN FUNCTIONS
// ===================================================================
async function tampilkanRiwayatIzin() {
    const tbody = document.getElementById('tabel-riwayat-izin-body');
    if (!tbody) return;
    
    tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4">Memuat data...</td></tr>';
    
    try {
        // FIXED: Admin harus pakai get_admin_izin.php untuk lihat SEMUA data
        const response = await fetch('api/get_admin_izin.php'); 
        const result = await response.json();
        
        tbody.innerHTML = '';
        
        // FIXED: Handle new response structure
        if (!result.success) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-red-500">${result.message || 'Gagal memuat data'}</td></tr>`;
            return;
        }
        
        const data = result.data; // Ambil array dari property 'data'
        
        if (!data || data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4">Tidak ada riwayat pengajuan izin.</td></tr>';
            return;
        }

        data.forEach(p => {
            let statusClass, statusText;
            switch(p.status) {
                case 'Diajukan': 
                    statusClass = 'bg-yellow-200 text-yellow-800'; 
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

            let tombolAksi = '';
            if (p.status === 'Diajukan') {
                tombolAksi = `
                    <button class="btn-setujui bg-green-500 text-white py-1 px-3 rounded-md hover:bg-green-600 text-xs mr-2" 
                            data-id="${p.id}" data-action="setujui">
                        Setujui
                    </button>
                    <button class="btn-tolak bg-red-500 text-white py-1 px-3 rounded-md hover:bg-red-600 text-xs" 
                            data-id="${p.id}" data-action="tolak">
                        Tolak
                    </button>
                `;
            } else {
                tombolAksi = `<span class="text-gray-500 text-xs">Tindakan Selesai</span>`;
            }

            tbody.innerHTML += `
                <tr>
                    <td class="py-3 px-4">${p.judul_penelitian}</td>
                    <td class="py-3 px-4"><strong>${p.nama_mahasiswa}</strong><br><small class="text-gray-500">${p.nim || ''}</small></td>
                    <td class="py-3 px-4">${p.nama_dosen}</td>
                    <td class="py-3 px-4">${p.tgl_pengajuan}</td>
                    <td class="py-3 px-4"><span class="${statusClass} py-1 px-3 rounded-full text-xs">${statusText}</span></td>
                    <td class="py-3 px-4 text-center">${tombolAksi}</td>
                </tr>`;
        });
        
        // Optional: Log untuk debugging
        console.log('Admin data loaded:', result.debug);
        
    } catch (error) { 
        console.error('Error Riwayat Izin:', error); 
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-red-500">Gagal memuat data.</td></tr>';
    }
}

async function prosesAksiIzin(izinId, action) {
    const adminId = document.body.dataset.adminId;
    if (!adminId) {
        alert('Error: Admin ID tidak ditemukan. Sesi mungkin telah berakhir.');
        return;
    }
    const actionText = action === 'setujui' ? 'menyetujui' : 'menolak';
    if (!window.confirm(`Anda yakin ingin ${actionText} pengajuan izin ini?`)) return;

    const button = document.querySelector(`button[data-id='${izinId}'][data-action='${action}']`);
    if (button && button.disabled) return;
    
    if (button) {
        const originalText = button.textContent;
        button.textContent = 'Memproses...';
        button.disabled = true;
    }

    const formData = new FormData();
    formData.append('izin_id', izinId);
    formData.append('action', action);
    formData.append('user_id', adminId); 

    try {
        const response = await fetch('api/proses_aksi_izin.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        alert(result.message);
        if (result.success) {
            tampilkanRiwayatIzin();
        } else if (button) {
            button.textContent = originalText;
            button.disabled = false;
        }
    } catch (error) {
        console.error('Error Aksi Izin:', error);
        alert('Terjadi kesalahan koneksi.');
        if (button) {
            button.textContent = originalText;
            button.disabled = false;
        }
    }
}

// ===================================================================
// DOSEN CRUD FUNCTIONS - TAMBAHAN
// ===================================================================

function showFormDosen(isEdit = false, data = {}) {
    const listView = document.getElementById('dosen-list-view');
    const formView = document.getElementById('dosen-form-view');
    const detailView = document.getElementById('dosen-detail-view');
    const formTitle = document.getElementById('form-dosen-title');
    const passwordHelp = document.getElementById('password-help');
    
    if (!listView || !formView || !formTitle) return;
    
    // Hide other views
    listView.classList.add('hidden');
    if (detailView) detailView.classList.add('hidden');
    formView.classList.remove('hidden');
    
    // Update form title and password help text
    if (isEdit) {
        formTitle.textContent = 'Edit Data Dosen';
        if (passwordHelp) passwordHelp.textContent = 'Kosongkan jika tidak ingin mengubah password';
        document.getElementById('password').removeAttribute('required');
    } else {
        formTitle.textContent = 'Tambah Dosen Baru';
        if (passwordHelp) passwordHelp.textContent = 'Password wajib diisi untuk dosen baru';
        document.getElementById('password').setAttribute('required', 'required');
    }
    
    // Populate form if editing
    if (isEdit && data) {
        document.getElementById('dosen-id').value = data.id || '';
        document.getElementById('nama_dosen').value = data.nama_dosen || '';
        document.getElementById('gelar_depan').value = data.gelar_depan || '';
        document.getElementById('gelar_belakang').value = data.gelar_belakang || '';
        document.getElementById('nidn').value = data.nidn || '';
        document.getElementById('nip').value = data.nip || '';
        document.getElementById('email').value = data.email || '';
        document.getElementById('homebase_prodi').value = data.homebase_prodi || '';
        document.getElementById('username').value = data.username || '';
        // Password field tetap kosong untuk keamanan
        document.getElementById('password').value = '';
    } else {
        // Reset form for new entry
        document.getElementById('form-dosen').reset();
        document.getElementById('dosen-id').value = '';
    }
}

// GANTI FUNGSI LAMA saveDosen DENGAN VERSI BARU INI
async function saveDosen(formData) {
    const submitButton = document.getElementById('btn-submit-dosen');
    const originalButtonHTML = submitButton.innerHTML;

    // Tampilkan status loading pada tombol
    submitButton.disabled = true;
    submitButton.innerHTML = `<div class="loading-spinner mr-2"></div><span>Menyimpan...</span>`;

    try {
        const dosenId = formData.get('dosen_id');
        const isEdit = dosenId && dosenId.trim() !== '';
        
        // Tentukan endpoint PHP yang benar
        const endpoint = isEdit ? 'api/update_dosen.php' : 'api/add_dosen.php';

        const response = await fetch(endpoint, {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            // Coba baca pesan error dari server jika ada
            const errorData = await response.json().catch(() => null);
            throw new Error(errorData?.message || `HTTP error! Status: ${response.status}`);
        }
        
        const result = await response.json();
        
        alert(result.message || 'Proses selesai.');
        
        if (result.success) {
            // Kembali ke halaman daftar dan muat ulang data
            document.getElementById('dosen-form-view').classList.add('hidden');
            document.getElementById('dosen-list-view').classList.remove('hidden');
            await tampilkanDataDosen();
        }

    } catch (error) {
        console.error('Error saat menyimpan data dosen:', error);
        alert('Terjadi kesalahan: ' + error.message);
    } finally {
        // Kembalikan tombol ke kondisi normal
        submitButton.disabled = false;
        submitButton.innerHTML = originalButtonHTML;
    }
}

async function deleteDosen(dosenId, namaDosen) {
    if (!confirm(`Apakah Anda yakin ingin menghapus dosen ${namaDosen}?\n\nPerhatian: User account yang terkait juga akan dihapus!`)) {
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('dosen_id', dosenId);
        
        const response = await fetch('api/delete_dosen.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert(result.message);
            tampilkanDataDosen(); // Reload data
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Error deleting dosen:', error);
        alert('Terjadi kesalahan saat menghapus data.');
    }
}


// ===================================================================
// DATA DOSEN FUNCTIONS
// ===================================================================
async function tampilkanDataDosen() {
    const tbody = document.getElementById('tabel-dosen-body');
    if (!tbody) return;

    tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4">Memuat data dosen...</td></tr>';
    try {
        const response = await fetch('api/get_dosen.php');
        const data = await response.json();
        tbody.innerHTML = '';
        if (data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4">Tidak ada data dosen.</td></tr>';
            return;
        }

        data.forEach(d => {
            let namaLengkap = [d.gelar_depan, d.nama_dosen, d.gelar_belakang].filter(Boolean).join(' ');
            tbody.innerHTML += `
                <tr>
                    <td class="py-3 px-4">${namaLengkap}</td>
                    <td class="py-3 px-4">${d.nip || '-'}</td>
                    <td class="py-3 px-4">${d.nidn || '-'}</td>
                    <td class="py-3 px-4">${d.homebase_prodi || '-'}</td>
                    <td class="py-3 px-4 text-center">
                        <div class="flex justify-center space-x-2">
                            <button class="btn-edit-dosen bg-yellow-500 text-white py-1 px-3 rounded hover:bg-yellow-600 text-xs flex items-center space-x-1" 
                                    data-id="${d.id}"
                                    data-nama="${d.nama_dosen || ''}"
                                    data-gelar-depan="${d.gelar_depan || ''}"
                                    data-gelar-belakang="${d.gelar_belakang || ''}"
                                    data-nidn="${d.nidn || ''}"
                                    data-nip="${d.nip || ''}"
                                    data-email="${d.email || ''}"
                                    data-homebase="${d.homebase_prodi || ''}"
                                    data-username="${d.username || ''}">
                                <i class="fas fa-cog"></i>
                                <span>Edit</span>
                            </button>
                            <button class="btn-delete-dosen bg-red-500 text-white py-1 px-3 rounded hover:bg-red-600 text-xs flex items-center space-x-1" 
                                    data-id="${d.id}" 
                                    data-nama="${namaLengkap}">
                                <i class="fas fa-times-circle"></i>
                                <span>Hapus</span>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
    } catch (error) {
        console.error('Error Dosen:', error);
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-red-500">Gagal memuat data dosen.</td></tr>';
    }
}

// ===================================================================
// ASISTEN FUNCTIONS
// ===================================================================

async function tampilkanDataAsisten() {
    console.log('🔄 Loading asisten data...');
    const tbody = document.getElementById('tabel-asisten-body');
   
    if (!tbody) {
        console.error('❌ Element tabel-asisten-body tidak ditemukan!');
        return;
    }
   
    showLoading(tbody);
   
    try {
        const response = await fetch('api/get_asisten.php');
       
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
       
        const data = await response.json();
        console.log('👥 Data asisten berhasil dimuat:', data);
       
        tbody.innerHTML = '';
       
        if (!Array.isArray(data) || data.length === 0) {
            return showError(tbody, 'Tidak ada data asisten.');
        }

        // Kelompokkan data berdasarkan angkatan
        const groupedData = {};
        data.forEach(asisten => {
            const angkatan = asisten.angkatan || 'Tidak Diketahui';
            if (!groupedData[angkatan]) {
                groupedData[angkatan] = [];
            }
            groupedData[angkatan].push(asisten);
        });

        // Urutkan angkatan dari terbaru ke terlama
        const sortedAngkatan = Object.keys(groupedData).sort((a, b) => {
            if (a === 'Tidak Diketahui') return 1;
            if (b === 'Tidak Diketahui') return -1;
            return b - a; // Descending order
        });

        // Tampilkan data yang sudah dikelompokkan
        sortedAngkatan.forEach(angkatan => {
            // Header untuk setiap angkatan
            tbody.innerHTML += `
                <tr class="bg-blue-100">
                    <td colspan="5" class="py-3 px-4 font-bold text-blue-800">
                        Angkatan ${angkatan} (${groupedData[angkatan].length} orang)
                    </td>
                </tr>
            `;

            // Data asisten untuk angkatan ini
            groupedData[angkatan].forEach(asisten => {
                const statusClass = asisten.status === 'Aktif' || asisten.status == 1
                    ? 'bg-green-200 text-green-800'
                    : 'bg-red-200 text-red-800';
               
                const statusText = asisten.status == 1 ? 'Aktif' :
                                 asisten.status == 0 ? 'Tidak Aktif' : asisten.status;

                // ✅ PERBAIKAN: Gunakan field 'nama' yang benar
                tbody.innerHTML += `
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-3 px-4 pl-8 font-medium">${asisten.nama || '-'}</td>
                        <td class="py-3 px-4 text-gray-600">${asisten.nim || '-'}</td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-md text-xs">
                                ${asisten.jabatan || '-'}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="${statusClass} py-1 px-3 rounded-full text-xs font-medium">
                                ${statusText}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <div class="flex justify-center space-x-2">
                                <button class="btn-edit-asisten bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded text-xs"
                                        data-id="${asisten.id}" 
                                        data-user-id="${asisten.user_id || ''}"
                                        data-nama="${asisten.nama || ''}"
                                        data-nim="${asisten.nim || ''}"
                                        data-jabatan="${asisten.jabatan || ''}" 
                                        data-angkatan="${asisten.angkatan || ''}"
                                        data-status="${asisten.status}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-delete-asisten bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs"
                                        data-id="${asisten.id}" 
                                        data-nama="${asisten.nama || ''}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
        });

        console.log('✅ Tabel asisten berhasil dimuat!');

    } catch (error) {
        console.error('❌ Error loading asisten:', error);
        showError(tbody, `Gagal memuat data asisten: ${error.message}`);
    }
}

async function loadUsersForSelect() {
    try {
        const response = await fetch('api/get_users_for_asisten.php');
        const users = await response.json();
        const userSelect = document.getElementById('user-select');
        
        userSelect.innerHTML = '<option value="">Pilih User...</option>';
        
        users.forEach(user => {
            userSelect.innerHTML += `
                <option value="${user.id}">${user.nama} (${user.nomor_induk})</option>
            `;
        });
    } catch (error) {
        console.error('Error loading users:', error);
    }
}

function showModalAsisten(isEdit = false, data = {}) {
    const modal = document.getElementById('modal-asisten');
    const modalTitle = document.getElementById('modal-title');
    const form = document.getElementById('form-asisten');
   
    if (!modal || !modalTitle || !form) {
        console.error('❌ Modal elements tidak ditemukan!');
        return;
    }

    modalTitle.textContent = isEdit ? 'Edit Asisten' : 'Tambah Asisten';
   
    if (isEdit && data) {
        // Mode Edit: Isi semua field dengan data yang ada
        console.log('📝 Editing asisten dengan data:', data);
        
        document.getElementById('asisten-id').value = data.id || '';
        document.getElementById('nama-user').value = data.nama || '';
        document.getElementById('nim-user').value = data.nim || '';
        document.getElementById('jabatan').value = data.jabatan || '';
        document.getElementById('angkatan').value = data.angkatan || '';
        
        // ✅ PERBAIKAN: Handle status dengan benar
        let statusValue = '';
        if (data.status == 1 || data.status === 'Aktif') {
            statusValue = 'Aktif';
        } else if (data.status == 0 || data.status === 'Tidak Aktif') {
            statusValue = 'Tidak Aktif';
        } else {
            statusValue = data.status || '';
        }
        document.getElementById('status-input').value = statusValue;
        
    } else {
        // Mode Tambah: Reset semua field
        form.reset();
        document.getElementById('asisten-id').value = '';
    }
   
    modal.classList.remove('hidden');
}



function hideModalAsisten() {
    const modal = document.getElementById('modal-asisten');
    if (modal) {
        modal.classList.add('hidden');
    }
}

async function saveAsisten(formData) {
    try {
        const asistenId = formData.get('asisten_id');
        const isEdit = asistenId !== '' && asistenId !== null;
        
        console.log('💾 Saving asisten:', isEdit ? 'UPDATE' : 'CREATE');
        console.log('📋 Form data:', Object.fromEntries(formData));
       
        const endpoint = isEdit ? 'api/update_asisten.php' : 'api/add_asisten.php';
        
        const response = await fetch(endpoint, {
            method: 'POST',
            body: formData
        });
       
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const result = await response.json();
        console.log('📤 Server response:', result);
       
        if (result.success) {
            alert(result.message || 'Data berhasil disimpan!');
            hideModalAsisten();
            await tampilkanDataAsisten(); // Reload data
        } else {
            alert('Error: ' + (result.message || 'Gagal menyimpan data'));
            console.error('❌ Server error:', result);
        }
    } catch (error) {
        console.error('❌ Error saving asisten:', error);
        alert('Terjadi kesalahan saat menyimpan data: ' + error.message);
    }
}

async function deleteAsisten(asistenId, namaAsisten) {
    if (!confirm(`Apakah Anda yakin ingin menghapus asisten ${namaAsisten}?`)) {
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('asisten_id', asistenId);
        
        const response = await fetch('api/delete_asisten.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert(result.message);
            tampilkanDataAsisten();
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Error deleting asisten:', error);
        alert('Terjadi kesalahan saat menghapus data.');
    }
}

// ===================================================================
// PRESENSI PRAKTIKUM ADMIN FUNCTIONS
// ===================================================================

async function tampilkanDataPresensiPraktikum() {
    console.log('🔄 Loading presensi praktikum data for admin...');
    const tbody = document.getElementById('tabel-presensi-praktikum-admin-body');
    const tanggalFilter = document.getElementById('filterTanggalPraktikum').value;

    if (!tbody) {
        console.error('❌ Element tabel-presensi-praktikum-admin-body tidak ditemukan!');
        return;
    }

    showLoading(tbody);

    try {
        let url = './api/get_all_presensi_praktikum.php';
        
        if (tanggalFilter) {
            url += `?tanggal=${tanggalFilter}`;
        }
        
        console.log('📡 Fetching data from URL:', url);
        
        const response = await fetch(url);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const data = await response.json();
        console.log('📋 Data presensi praktikum diterima:', data);
        
        tbody.innerHTML = '';
        
        if (!Array.isArray(data) || data.length === 0) {
            return showError(tbody, 'Tidak ada data presensi praktikum yang ditemukan.');
        }

        data.forEach(presensi => {
            let statusClass, statusText;
            if (presensi.waktu_keluar_formatted) {
                statusClass = 'bg-green-200 text-green-800';
                statusText = 'Selesai';
            } else {
                statusClass = 'bg-yellow-200 text-yellow-800';
                statusText = 'Sedang Praktikum';
            }

            const row = `
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="py-3 px-4">${presensi.nama_mahasiswa || '-'}</td>
                    <td class="py-3 px-4">${presensi.nim_mahasiswa || '-'}</td>
                    <td class="py-3 px-4">${presensi.mata_praktikum || '-'}</td>
                    <td class="py-3 px-4">${presensi.tanggal || '-'}</td>
                    <td class="py-3 px-4 text-center">${presensi.waktu_masuk_formatted || '-'}</td>
                    <td class="py-3 px-4 text-center">${presensi.waktu_keluar_formatted || '-'}</td>
                    <td class="py-3 px-4 text-center">${presensi.durasi || '-'}</td>
                    <td class="py-3 px-4 text-center">
                        <span class="${statusClass} py-1 px-3 rounded-full text-xs font-medium">
                            ${statusText}
                        </span>
                    </td>
                </tr>
            `;
            tbody.innerHTML += row;
        });

        console.log('✅ Tabel presensi praktikum berhasil dimuat!');

    } catch (error) {
        console.error('❌ Error loading presensi praktikum:', error);
        showError(tbody, `Gagal memuat data presensi praktikum: ${error.message}`);
    }
}

function filterPresensiPraktikum() {
    console.log('🔍 Menerapkan filter presensi praktikum...');
    tampilkanDataPresensiPraktikum();
}

function resetFilterPresensiPraktikum() {
    console.log('🔄 Mereset filter presensi praktikum...');
    const tanggalInput = document.getElementById('filterTanggalPraktikum');
    if (tanggalInput) {
        tanggalInput.value = '';
    }
    tampilkanDataPresensiPraktikum();
}

// ===================================================================
// PRESENSI ADMIN FUNCTIONS - Tambahkan ke scriptadmin.js
// ===================================================================

async function tampilkanDataPresensi() {
    console.log('🔄 Loading presensi data...');
    
    try {
        // Load statistics
        await loadPresensiStatistics();
        
        // Load presensi hari ini
        await tampilkanPresensiHariIni();
        
        // Load riwayat presensi
        await tampilkanSemuaPresensi();
        
        console.log('✅ Data presensi berhasil dimuat!');
        
    } catch (error) {
        console.error('❌ Error loading presensi data:', error);
    }
}

// Fungsi untuk format status yang benar
function formatPresensiStatus(status) {
    switch (status) {
        case 'sedang piket':
            return {
                class: 'bg-green-200 text-green-800',
                text: 'Sedang Piket'
            };
        case 'selesai':
            return {
                class: 'bg-blue-200 text-blue-800',
                text: 'Selesai'
            };
        default:
            return {
                class: 'bg-gray-200 text-gray-800',
                text: status || 'Unknown'
            };
    }
}


// Perbaiki fungsi loadPresensiStatistics untuk handle response yang baru
async function loadPresensiStatistics() {
    try {
        const response = await fetch('./api/get_presensi_statistics.php');
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const data = await response.json();
        
        // Debug info
        console.log('📊 Statistics response:', data);
        if (data.debug_users) {
            console.log('👥 Available users:', data.debug_users);
        }
        
        // Update elements jika ada
        const hariIniEl = document.getElementById('presensiHariIni');
        const sedangPiketEl = document.getElementById('sedangPiket');
        const bulanIniEl = document.getElementById('totalBulanIni');
        const rataRataEl = document.getElementById('rataRata');
        
        if (hariIniEl) hariIniEl.textContent = data.hari_ini || '0';
        if (sedangPiketEl) sedangPiketEl.textContent = data.sedang_piket || '0';
        if (bulanIniEl) bulanIniEl.textContent = data.bulan_ini || '0';
        if (rataRataEl) rataRataEl.textContent = data.rata_rata || '0h';
        
    } catch (error) {
        console.error('Error loading statistics:', error);
    }
}

// Ganti fungsi lama di scriptadmin.js dengan yang ini
async function tampilkanPresensiHariIni() {
    const tbody = document.getElementById('tabel-presensi-hari-ini-body');
    if (!tbody) {
        console.log('⚠️ Element tabel-presensi-hari-ini-body tidak ditemukan');
        return;
    }
    
    showLoading(tbody);
    
    try {
        // Ganti ke API yang sama dengan halaman user untuk konsistensi
        const response = await fetch('./api/get_riwayat_presensi.php'); 
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const data = await response.json();
        console.log('📋 Presensi hari ini (dari sumber data user):', data);
        
        tbody.innerHTML = '';
        
        if (!Array.isArray(data) || data.length === 0) {
            return showError(tbody, 'Belum ada presensi hari ini.');
        }
        
        data.forEach(presensi => {
            // Kita tentukan status secara manual karena API ini tidak mengirim status
            let status = presensi.waktu_keluar_formatted ? 'selesai' : 'sedang piket';
            const statusFormat = formatPresensiStatus(status);
            
            const row = `
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="py-3 px-4 font-medium">
                        ${presensi.nama_asisten || 'Nama Tidak Ditemukan'}
                        ${presensi.nim_asisten ? `<br><small class="text-gray-500">${presensi.nim_asisten}</small>` : ''}
                    </td>
                    <td class="py-3 px-4 text-center">${presensi.waktu_masuk_formatted || '-'}</td>
                    <td class="py-3 px-4 text-center">${presensi.waktu_keluar_formatted || '-'}</td>
                    <td class="py-3 px-4 text-center">${presensi.durasi || '-'}</td>
                    <td class="py-3 px-4 text-center">
                        <span class="${statusFormat.class} py-1 px-3 rounded-full text-xs font-medium">
                            ${statusFormat.text}
                        </span>
                    </td>
                </tr>
            `;
            
            tbody.innerHTML += row;
        });
        
        console.log('✅ Presensi hari ini berhasil dimuat dari sumber data user:', data.length, 'records');
        
    } catch (error) {
        console.error('Error loading presensi hari ini:', error);
        showError(tbody, 'Gagal memuat data presensi hari ini.');
    }
}

// Perbaiki fungsi tampilkanSemuaPresensi
async function tampilkanSemuaPresensi() {
    const tbody = document.getElementById('tabel-semua-presensi-body');
    if (!tbody) {
        console.log('⚠️ Element tabel-semua-presensi-body tidak ditemukan');
        return;
    }
    
    showLoading(tbody);
    
    try {
        const response = await fetch('./api/get_all_presensi.php');
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const data = await response.json();
        
        tbody.innerHTML = '';
        
        if (!Array.isArray(data) || data.length === 0) {
            return showError(tbody, 'Belum ada data presensi.');
        }
        
        data.forEach(presensi => {
            const statusFormat = formatPresensiStatus(presensi.status);
            
            const row = `
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="py-3 px-4">${new Date(presensi.tanggal).toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'long',
                        year: 'numeric'
                    })}</td>
                    <td class="py-3 px-4 font-medium">
                        ${presensi.nama_asisten || 'Unknown User'}
                        ${presensi.nomor_induk ? `<br><small class="text-gray-500">${presensi.nomor_induk}</small>` : ''}
                    </td>
                    <td class="py-3 px-4 text-center">${presensi.waktu_masuk_formatted || '-'}</td>
                    <td class="py-3 px-4 text-center">${presensi.waktu_keluar_formatted || '-'}</td>
                    <td class="py-3 px-4 text-center">${presensi.durasi || '-'}</td>
                    <td class="py-3 px-4 text-center">
                        <span class="${statusFormat.class} py-1 px-3 rounded-full text-xs font-medium">
                            ${statusFormat.text}
                        </span>
                    </td>
                </tr>
            `;
            
            tbody.innerHTML += row;
        });
        
        console.log('✅ All presensi loaded:', data.length, 'records');
        
    } catch (error) {
        console.error('Error loading all presensi:', error);
        showError(tbody, 'Gagal memuat data presensi.');
    }
}

// Perbaiki fungsi filterPresensi juga
async function filterPresensi() {
    console.log('🔍 Filtering presensi...');
    
    // PERBAIKAN: Gunakan ID yang unik untuk presensi
    const tanggal = document.getElementById('filterTanggalPresensi')?.value;
    const status = document.getElementById('filterStatusPresensi')?.value;
    
    console.log('Filter presensi values:', { tanggal, status });
    
    const tbody = document.getElementById('tabel-semua-presensi-body');
    if (!tbody) {
        console.error('❌ Element tabel-semua-presensi-body tidak ditemukan!');
        return;
    }
    
    showLoading(tbody);
    
    try {
        let url = 'api/get_all_presensi.php';
        const params = new URLSearchParams();
        
        if (tanggal) {
            params.append('tanggal', tanggal);
            console.log('📅 Filter tanggal:', tanggal);
        }
        
        if (status && status !== '') {
            params.append('status', status);
            console.log('🏷️ Filter status:', status);
        }
        
        if (params.toString()) {
            url += '?' + params.toString();
        }
        
        console.log('🔍 Filtering presensi with URL:', url);
        
        const response = await fetch(url);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const data = await response.json();
        console.log('📋 Filtered presensi data:', data);
        
        tbody.innerHTML = '';
        
        if (!Array.isArray(data) || data.length === 0) {
            return showError(tbody, 'Tidak ada data presensi sesuai filter.');
        }
        
        data.forEach(presensi => {
            const statusFormat = formatPresensiStatus(presensi.status);
            
            const row = `
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="py-3 px-4">${new Date(presensi.tanggal).toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'long',
                        year: 'numeric'
                    })}</td>
                    <td class="py-3 px-4 font-medium">
                        ${presensi.nama_asisten || 'Unknown User'}
                        ${presensi.nomor_induk ? `<br><small class="text-gray-500">${presensi.nomor_induk}</small>` : ''}
                    </td>
                    <td class="py-3 px-4 text-center">${presensi.waktu_masuk_formatted || '-'}</td>
                    <td class="py-3 px-4 text-center">${presensi.waktu_keluar_formatted || '-'}</td>
                    <td class="py-3 px-4 text-center">${presensi.durasi || '-'}</td>
                    <td class="py-3 px-4 text-center">
                        <span class="${statusFormat.class} py-1 px-3 rounded-full text-xs font-medium">
                            ${statusFormat.text}
                        </span>
                    </td>
                </tr>
            `;
            
            tbody.innerHTML += row;
        });
        
        console.log('✅ Filtered presensi loaded:', data.length, 'records');
        
    } catch (error) {
        console.error('❌ Error filtering presensi:', error);
        showError(tbody, 'Gagal memfilter data presensi: ' + error.message);
    }
}

function resetFilterPresensi() {
    console.log('🔄 Resetting presensi filters...');
    
    // Reset semua input filter presensi dengan ID yang baru
    const filterTanggal = document.getElementById('filterTanggalPresensi');
    const filterStatus = document.getElementById('filterStatusPresensi');
    
    if (filterTanggal) {
        filterTanggal.value = '';
        console.log('📅 Reset filter tanggal');
    }
    
    if (filterStatus) {
        filterStatus.value = '';
        console.log('🏷️ Reset filter status');
    }
    
    // Reload semua data presensi
    tampilkanSemuaPresensi();
}

function initPresensiFilterListeners() {
    const filterTanggal = document.getElementById('filterTanggalPresensi');
    const filterStatus = document.getElementById('filterStatusPresensi');
    
    if (filterTanggal) {
        filterTanggal.addEventListener('change', function() {
            console.log('📅 Tanggal changed, auto filtering...');
            filterPresensi();
        });
    }
    
    if (filterStatus) {
        filterStatus.addEventListener('change', function() {
            console.log('🏷️ Status changed, auto filtering...');
            filterPresensi();
        });
    }
    
    console.log('✅ Presensi filter listeners initialized');
}

async function tampilkanDataPresensiWithFilter() {
    console.log('🔄 Loading presensi data with filter support...');
    
    try {
        // Load statistics
        await loadPresensiStatistics();
        
        // Load presensi hari ini
        await tampilkanPresensiHariIni();
        
        // Load riwayat presensi
        await tampilkanSemuaPresensi();
        
        // Initialize filter listeners setelah data dimuat
        setTimeout(() => {
            initPresensiFilterListeners();
        }, 500);
        
        console.log('✅ Data presensi dengan filter berhasil dimuat!');
        
    } catch (error) {
        console.error('❌ Error loading presensi data:', error);
    }
}


// Update fungsi showPage untuk include presensi
// Tambahkan kondisi ini di dalam fungsi showPage yang sudah ada:
/*
else if (pageId === 'presensi') {
    tampilkanDataPresensi();
}
*/
let allPeminjamanData = [];


async function tampilkanDataPeminjamanWithFilter() {
    console.log('🔄 Loading peminjaman data...');
    const tbody = document.getElementById('tabel-peminjaman-body');
    
    if (!tbody) {
        console.error('❌ Element tabel-peminjaman-body tidak ditemukan!');
        return;
    }
    
    showLoading(tbody);
    
    try {
        const response = await fetch('api/get_peminjaman_admin.php');
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const result = await response.json();
        console.log('📋 Data peminjaman response:', result);
        
        if (!result.success) {
            throw new Error(result.message || 'Gagal memuat data');
        }
        
        // Simpan data asli ke variabel global
        allPeminjamanData = result.data || [];
        const stats = result.stats || {};
        
        // Update statistik di dashboard
        updatePeminjamanStats(stats);
        
        // Tampilkan semua data
        displayFilteredData(allPeminjamanData);
        
        console.log('✅ Data peminjaman berhasil dimuat!', allPeminjamanData.length, 'records');
        
    } catch (error) {
        console.error('❌ Error loading peminjaman:', error);
        showError(tbody, `Gagal memuat data peminjaman: ${error.message}`);
    }
}

function displayFilteredData(data) {
    const tbody = document.getElementById('tabel-peminjaman-body');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    if (data.length === 0) {
        return showError(tbody, 'Tidak ada data yang sesuai dengan filter.');
    }
    
    // Group data berdasarkan status untuk tampilan yang lebih terorganisir
    const groupedData = {
        'Dipinjam': [],
        'Terlambat': [],
        'Dikembalikan': []
    };
    
    data.forEach(item => {
        const status = item.status_display || item.status;
        if (groupedData[status]) {
            groupedData[status].push(item);
        } else {
            groupedData['Dikembalikan'].push(item); // Default ke dikembalikan
        }
    });
    
    // Tampilkan data berdasarkan prioritas: Terlambat -> Dipinjam -> Dikembalikan
    const statusOrder = ['Terlambat', 'Dipinjam', 'Dikembalikan'];
    
    statusOrder.forEach(status => {
        if (groupedData[status] && groupedData[status].length > 0) {
            // Header untuk setiap grup status
            if (status !== 'Dikembalikan') { // Skip header untuk yang sudah dikembalikan
                tbody.innerHTML += `
                    <tr class="bg-gradient-to-r ${getStatusHeaderClass(status)}">
                        <td colspan="8" class="py-3 px-4 font-bold text-white">
                            <i class="fas ${getStatusIcon(status)} mr-2"></i>
                            ${status.toUpperCase()} (${groupedData[status].length} item)
                        </td>
                    </tr>
                `;
            }
            
            // Data untuk setiap grup
            groupedData[status].forEach(peminjaman => {
                tbody.innerHTML += createPeminjamanRow(peminjaman);
            });
        }
    });
}

function applyPeminjamanFilter() {
    console.log('🔍 Applying filters...');
    
    // Ambil nilai filter
    const statusFilter = document.getElementById('filterStatus')?.value || '';
    const roleFilter = document.getElementById('filterRole')?.value || '';
    const tanggalFilter = document.getElementById('filterTanggal')?.value || '';
    
    console.log('Filter values:', { statusFilter, roleFilter, tanggalFilter });
    
    // Mulai dengan semua data
    let filteredData = [...allPeminjamanData];
    
    // Filter berdasarkan status
    if (statusFilter) {
        filteredData = filteredData.filter(item => {
            const itemStatus = item.status_display || item.status;
            return itemStatus === statusFilter;
        });
        console.log(`After status filter (${statusFilter}):`, filteredData.length, 'items');
    }
    
    // Filter berdasarkan role peminjam
    if (roleFilter) {
        filteredData = filteredData.filter(item => {
            return item.role_peminjam === roleFilter;
        });
        console.log(`After role filter (${roleFilter}):`, filteredData.length, 'items');
    }
    
    // Filter berdasarkan tanggal
    if (tanggalFilter) {
        filteredData = filteredData.filter(item => {
            // Konversi tanggal untuk perbandingan
            const itemDate = new Date(item.tgl_pinjam);
            const filterDate = new Date(tanggalFilter);
            
            // Bandingkan tanggal (tanpa mempertimbangkan waktu)
            return itemDate.toDateString() === filterDate.toDateString();
        });
        console.log(`After date filter (${tanggalFilter}):`, filteredData.length, 'items');
    }
    
    // Tampilkan data yang sudah difilter
    displayFilteredData(filteredData);
    
    // Update statistik untuk data yang difilter
    updateFilteredStats(filteredData);
}

// Fungsi untuk update statistik data yang difilter
function updateFilteredStats(data) {
    const stats = {
        total_peminjaman: data.length,
        sedang_dipinjam: data.filter(item => (item.status_display || item.status) === 'Dipinjam').length,
        terlambat: data.filter(item => (item.status_display || item.status) === 'Terlambat').length,
        dikembalikan: data.filter(item => (item.status_display || item.status) === 'Dikembalikan').length
    };
    
    updatePeminjamanStats(stats);
}

// Fungsi untuk reset filter
function resetPeminjamanFilter() {
    console.log('🔄 Resetting filters...');
    
    // Reset semua input filter
    const filterStatus = document.getElementById('filterStatus');
    const filterRole = document.getElementById('filterRole');
    const filterTanggal = document.getElementById('filterTanggal');
    
    if (filterStatus) filterStatus.value = '';
    if (filterRole) filterRole.value = '';
    if (filterTanggal) filterTanggal.value = '';
    
    // Tampilkan semua data
    displayFilteredData(allPeminjamanData);
    
    // Update statistik dengan data asli
    updateFilteredStats(allPeminjamanData);
}

// Fungsi untuk export data (placeholder)
function exportPeminjamanData() {
    if (allPeminjamanData.length === 0) {
        alert('Tidak ada data untuk di-export!');
        return;
    }
    
    // Ambil data yang sedang ditampilkan (setelah filter)
    const currentlyDisplayedData = getCurrentlyDisplayedData();
    
    // Konversi ke CSV
    const csvData = convertToCSV(currentlyDisplayedData);
    
    // Download file
    downloadCSV(csvData, 'data_peminjaman_' + new Date().toISOString().split('T')[0] + '.csv');
}

// Helper function untuk mendapatkan data yang sedang ditampilkan
function getCurrentlyDisplayedData() {
    const statusFilter = document.getElementById('filterStatus')?.value || '';
    const roleFilter = document.getElementById('filterRole')?.value || '';
    const tanggalFilter = document.getElementById('filterTanggal')?.value || '';
    
    let data = [...allPeminjamanData];
    
    if (statusFilter) {
        data = data.filter(item => (item.status_display || item.status) === statusFilter);
    }
    
    if (roleFilter) {
        data = data.filter(item => item.role_peminjam === roleFilter);
    }
    
    if (tanggalFilter) {
        data = data.filter(item => {
            const itemDate = new Date(item.tgl_pinjam);
            const filterDate = new Date(tanggalFilter);
            return itemDate.toDateString() === filterDate.toDateString();
        });
    }
    
    return data;
}

// Helper function untuk konversi ke CSV
function convertToCSV(data) {
    const headers = [
        'Nama Peminjam', 'Nomor Induk', 'Role', 'Nama Alat', 'Kode Alat', 
        'Kategori', 'Tanggal Pinjam', 'Rencana Kembali', 'Aktual Kembali', 
        'Durasi (Hari)', 'Status', 'Lokasi'
    ];
    
    const csvRows = [headers.join(',')];
    
    data.forEach(item => {
        const row = [
            `"${item.nama_peminjam || ''}"`,
            `"${item.nomor_induk_peminjam || ''}"`,
            `"${item.role_peminjam || ''}"`,
            `"${item.nama_alat || ''}"`,
            `"${item.kode_alat || ''}"`,
            `"${item.kategori || ''}"`,
            `"${item.tgl_pinjam_formatted || ''}"`,
            `"${item.tgl_rencana_kembali_formatted || ''}"`,
            `"${item.tgl_aktual_kembali_formatted || ''}"`,
            `"${item.durasi_hari || 0}"`,
            `"${item.status_display || item.status || ''}"`,
            `"${item.lokasi || ''}"`
        ];
        csvRows.push(row.join(','));
    });
    
    return csvRows.join('\n');
}

// Helper function untuk download CSV
function downloadCSV(csvData, filename) {
    const blob = new Blob([csvData], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    
    if (link.download !== undefined) {
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', filename);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    } else {
        alert('Browser Anda tidak mendukung download otomatis. Silakan copy data dari console.');
        console.log('CSV Data:', csvData);
    }
}

// Event listeners untuk filter real-time
document.addEventListener('DOMContentLoaded', function() {
    // Tambahkan event listener setelah DOM loaded
    setTimeout(() => {
        const filterStatus = document.getElementById('filterStatus');
        const filterRole = document.getElementById('filterRole');
        const filterTanggal = document.getElementById('filterTanggal');
        
        if (filterStatus) {
            filterStatus.addEventListener('change', applyPeminjamanFilter);
        }
        
        if (filterRole) {
            filterRole.addEventListener('change', applyPeminjamanFilter);
        }
        
        if (filterTanggal) {
            filterTanggal.addEventListener('change', applyPeminjamanFilter);
        }
    }, 1000);
});



async function tampilkanDataPeminjaman() {
    console.log('🔄 Loading peminjaman data...');
    const tbody = document.getElementById('tabel-peminjaman-body');
    
    if (!tbody) {
        console.error('❌ Element tabel-peminjaman-body tidak ditemukan!');
        return;
    }
    
    showLoading(tbody);
    
    try {
        const response = await fetch('api/get_peminjaman_admin.php');
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const result = await response.json();
        console.log('📋 Data peminjaman response:', result);
        
        if (!result.success) {
            throw new Error(result.message || 'Gagal memuat data');
        }
        
        const data = result.data || [];
        const stats = result.stats || {};
        
        // Update statistik di dashboard jika ada
        updatePeminjamanStats(stats);
        
        tbody.innerHTML = '';
        
        if (data.length === 0) {
            return showError(tbody, 'Belum ada data peminjaman.');
        }
        
        // Group data berdasarkan status untuk tampilan yang lebih terorganisir
        const groupedData = {
            'Dipinjam': [],
            'Terlambat': [],
            'Dikembalikan': []
        };
        
        data.forEach(item => {
            const status = item.status_display || item.status;
            if (groupedData[status]) {
                groupedData[status].push(item);
            } else {
                groupedData['Dikembalikan'].push(item); // Default ke dikembalikan
            }
        });
        
        // Tampilkan data berdasarkan prioritas: Terlambat -> Dipinjam -> Dikembalikan
        const statusOrder = ['Terlambat', 'Dipinjam', 'Dikembalikan'];
        
        statusOrder.forEach(status => {
            if (groupedData[status] && groupedData[status].length > 0) {
                // Header untuk setiap grup status
                if (status !== 'Dikembalikan') { // Skip header untuk yang sudah dikembalikan
                    tbody.innerHTML += `
                        <tr class="bg-gradient-to-r ${getStatusHeaderClass(status)}">
                            <td colspan="8" class="py-3 px-4 font-bold text-white">
                                <i class="fas ${getStatusIcon(status)} mr-2"></i>
                                ${status.toUpperCase()} (${groupedData[status].length} item)
                            </td>
                        </tr>
                    `;
                }
                
                // Data untuk setiap grup
                groupedData[status].forEach(peminjaman => {
                    tbody.innerHTML += createPeminjamanRow(peminjaman);
                });
            }
        });
        
        console.log('✅ Data peminjaman berhasil dimuat!', data.length, 'records');
        
    } catch (error) {
        console.error('❌ Error loading peminjaman:', error);
        showError(tbody, `Gagal memuat data peminjaman: ${error.message}`);
    }
}

// GANTI FUNGSI LAMA DENGAN VERSI BARU INI (UNTUK HALAMAN ADMIN)

function createPeminjamanRow(peminjaman) {
    const statusInfo = getStatusStyle(peminjaman.status_display || peminjaman.status);
    const durasiInfo = getDurasiInfo(peminjaman.durasi_hari, peminjaman.status_display);
    
    // Perbaikan: Cek jika role peminjam ada sebelum membuat badge
    let roleBadge = '';
    if (peminjaman.role_peminjam) {
        roleBadge = peminjaman.role_peminjam === 'dosen' 
            ? `<span class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded-full">${peminjaman.role_peminjam}</span>` 
            : `<span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">${peminjaman.role_peminjam}</span>`;
    }

    let actionButtons = '';
    const statusSekarang = peminjaman.status_display || peminjaman.status;

    switch(statusSekarang) {
        case 'Diajukan':
            actionButtons = `
                <div class="flex justify-center gap-2">
                    <button onclick="prosesAksiPeminjaman('${peminjaman.peminjaman_id}', 'Disetujui')" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs font-medium" title="Setujui Peminjaman">Setuju</button>
                    <button onclick="prosesAksiPeminjaman('${peminjaman.peminjaman_id}', 'Ditolak')" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs font-medium" title="Tolak Peminjaman">Tolak</button>
                </div>`;
            break;
        case 'Dipinjam':
        case 'Terlambat':
            actionButtons = `
                <button onclick="prosesAksiPeminjaman('${peminjaman.peminjaman_id}', 'Dikembalikan')" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs font-medium" title="Tandai sebagai sudah dikembalikan">
                    Kembalikan
                </button>`;
            break;
        case 'Dikembalikan':
        case 'Ditolak':
            actionButtons = `<span class="text-xs text-gray-500 italic">Tindakan Selesai</span>`;
            break;
        default:
            actionButtons = `<span class="text-xs text-gray-400">N/A</span>`;
    }

    return `
        <tr class="hover:bg-gray-50 ${statusSekarang === 'Terlambat' ? 'border-l-4 border-red-500' : ''}">
            <td class="py-3 px-4">
                <div class="font-medium">${peminjaman.nama_peminjam || 'Nama tidak ada'}</div>
                
                <div class="text-sm text-gray-500">${peminjaman.nomor_induk_peminjam || ''} ${roleBadge}</div>
                
            </td>
            <td class="py-3 px-4">
                <div class="font-medium text-blue-600">${peminjaman.nama_alat}</div>
                <div class="text-xs text-gray-500">${peminjaman.kode_alat}</div>
            </td>
            <td class="py-3 px-4 text-center text-sm">${peminjaman.tgl_pinjam_formatted || '-'}</td>
            <td class="py-3 px-4 text-center text-sm">${peminjaman.tgl_rencana_kembali_formatted || '-'}</td>
            <td class="py-3 px-4 text-center text-sm">${peminjaman.tgl_aktual_kembali_formatted || '-'}</td>
            <td class="py-3 px-4 text-center"><span class="${durasiInfo.class}">${durasiInfo.text}</span></td>
            <td class="py-3 px-4 text-center"><span class="${statusInfo.class} py-1 px-3 rounded-full text-xs font-medium">${statusInfo.text}</span></td>
            <td class="py-3 px-4 text-center">${actionButtons}</td>
        </tr>`;
}

function getStatusStyle(status) {
    switch (status) {
        case 'Dipinjam':
            return {
                class: 'bg-yellow-200 text-yellow-800',
                text: 'Dipinjam'
            };
        case 'Terlambat':
            return {
                class: 'bg-red-200 text-red-800',
                text: 'Terlambat'
            };
        case 'Dikembalikan':
            return {
                class: 'bg-green-200 text-green-800',
                text: 'Dikembalikan'
            };
        default:
            return {
                class: 'bg-gray-200 text-gray-800',
                text: status || 'Unknown'
            };
    }
}

function getStatusHeaderClass(status) {
    switch (status) {
        case 'Terlambat':
            return 'from-red-500 to-red-600';
        case 'Dipinjam':
            return 'from-yellow-500 to-yellow-600';
        case 'Dikembalikan':
            return 'from-green-500 to-green-600';
        default:
            return 'from-gray-500 to-gray-600';
    }
}

function getStatusIcon(status) {
    switch (status) {
        case 'Terlambat':
            return 'fa-exclamation-triangle';
        case 'Dipinjam':
            return 'fa-clock';
        case 'Dikembalikan':
            return 'fa-check-circle';
        default:
            return 'fa-info-circle';
    }
}

function getDurasiInfo(durasi, status) {
    const hari = parseInt(durasi) || 0;
    
    if (status === 'Terlambat') {
        return {
            class: 'text-red-600 font-bold',
            text: `${hari} hari ⚠️`
        };
    } else if (hari > 7) {
        return {
            class: 'text-orange-600 font-medium',
            text: `${hari} hari`
        };
    } else {
        return {
            class: 'text-gray-600',
            text: `${hari} hari`
        };
    }
}

function updatePeminjamanStats(stats) {
    // Update statistik di card dashboard jika ada
    const elements = {
        'totalPeminjaman': stats.total_peminjaman,
        'sedangDipinjam': stats.sedang_dipinjam,
        'terlambat': stats.terlambat,
        'dikembalikan': stats.dikembalikan
    };
    
    Object.keys(elements).forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = elements[id] || '0';
        }
    });
}

// GANTI FUNGSI LAMA DENGAN VERSI BARU INI (UNTUK HALAMAN ADMIN)
async function prosesAksiPeminjaman(peminjamanId, newStatus) {
    const statusMap = {
        'Disetujui': 'menyetujui',
        'Ditolak': 'menolak',
        'Dikembalikan': 'menandai lunas (dikembalikan)'
    };
    const actionText = statusMap[newStatus] || 'memperbarui status';

    if (!confirm(`Anda yakin ingin ${actionText} peminjaman ini?`)) {
        return;
    }

    const formData = new FormData();
    formData.append('peminjaman_id', peminjamanId);
    formData.append('status', newStatus);

    try {
        const response = await fetch('api/update_status_peminjaman.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        if (result.success) {
            alert(result.message);
            tampilkanDataPeminjamanWithFilter(); // Refresh tabel
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Error saat update status:', error);
        alert('Terjadi kesalahan koneksi.');
    }
}

// Fungsi untuk menandai peminjaman sebagai dikembalikan
async function kembalikanAlat(peminjamanId, namaAlat) {
    if (!confirm(`Apakah Anda yakin ingin menandai "${namaAlat}" sebagai dikembalikan?`)) {
        return;
    }
    
    const button = document.querySelector(`button[data-id='${peminjamanId}']`);
    let originalText = 'Kembalikan'; // Default text jika button tidak ditemukan
    
    if (button) {
        originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Proses...';
        button.disabled = true;
    }
    
    try {
        const formData = new FormData();
        formData.append('peminjaman_id', peminjamanId);
        
        const response = await fetch('api/proses_pengembalian.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert(result.message || 'Alat berhasil dikembalikan!');
            
            // Reload data peminjaman
            if (typeof tampilkanDataPeminjamanWithFilter === 'function') {
                tampilkanDataPeminjamanWithFilter();
            } else if (typeof tampilkanDataPeminjaman === 'function') {
                tampilkanDataPeminjaman();
            }
        } else {
            alert('Error: ' + (result.message || 'Gagal memproses pengembalian'));
            
            // Restore button jika gagal
            if (button) {
                button.innerHTML = originalText;
                button.disabled = false;
            }
        }
    } catch (error) {
        console.error('Error kembalikan alat:', error);
        alert('Terjadi kesalahan saat memproses pengembalian.');
        
        // Restore button jika error
        if (button) {
            button.innerHTML = originalText;
            button.disabled = false;
        }
    }
}

async function sendLateNotifications() {
    console.log('📧 Mengirim notifikasi peminjaman terlambat...');
    
    // Konfirmasi dari admin
    const confirmation = confirm(
        'Apakah Anda yakin ingin mengirim notifikasi pengingat ke semua peminjam yang terlambat?\n\n' +
        'Notifikasi akan dikirim melalui email dan WhatsApp (jika tersedia).'
    );
    
    if (!confirmation) {
        return;
    }
    
    // Show loading state
    const button = document.getElementById('btn-send-notifications');
    if (button) {
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengirim...';
        button.disabled = true;
        
        // Restore button setelah selesai
        setTimeout(() => {
            button.innerHTML = originalText;
            button.disabled = false;
        }, 10000); // 10 detik timeout
    }
    
    try {
        const response = await fetch('api/send_late_notifications.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const result = await response.json();
        console.log('📧 Notification result:', result);
        
        if (result.success) {
            // Show success message with details
            showNotificationResult(result);
            
            // Refresh peminjaman data
            if (typeof tampilkanDataPeminjamanWithFilter === 'function') {
                await tampilkanDataPeminjamanWithFilter();
            } else if (typeof tampilkanDataPeminjaman === 'function') {
                await tampilkanDataPeminjaman();
            }
        } else {
            alert('Error: ' + (result.message || 'Gagal mengirim notifikasi'));
        }
        
    } catch (error) {
        console.error('❌ Error sending notifications:', error);
        alert('Terjadi kesalahan saat mengirim notifikasi: ' + error.message);
    }
}

function showNotificationResult(result) {
    const modalHtml = `
        <div id="notification-result-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-96 overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-green-800">
                            <i class="fas fa-check-circle mr-2"></i>Notifikasi Berhasil Dikirim
                        </h3>
                        <button onclick="closeNotificationModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="grid grid-cols-3 gap-4 text-center">
                            <div>
                                <div class="text-2xl font-bold text-green-600">${result.sent_count || 0}</div>
                                <div class="text-sm text-gray-600">Total Notifikasi</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-blue-600">${result.email_count || 0}</div>
                                <div class="text-sm text-gray-600">Email Terkirim</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-purple-600">${result.whatsapp_count || 0}</div>
                                <div class="text-sm text-gray-600">WhatsApp Terkirim</div>
                            </div>
                        </div>
                    </div>
                    
                    ${result.notifications && result.notifications.length > 0 ? `
                        <div class="mb-4">
                            <h4 class="font-medium text-gray-800 mb-2">Detail Pengiriman:</h4>
                            <div class="max-h-48 overflow-y-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="text-left p-2">Peminjam</th>
                                            <th class="text-left p-2">Alat</th>
                                            <th class="text-center p-2">Terlambat</th>
                                            <th class="text-center p-2">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${result.notifications.map(notif => `
                                            <tr class="border-b">
                                                <td class="p-2">${notif.nama_peminjam}</td>
                                                <td class="p-2">${notif.nama_alat}</td>
                                                <td class="text-center p-2">${notif.hari_terlambat} hari</td>
                                                <td class="text-center p-2">
                                                    ${notif.email_sent ? '<i class="fas fa-envelope text-green-500" title="Email terkirim"></i>' : ''}
                                                    ${notif.whatsapp_sent ? '<i class="fas fa-whatsapp text-green-500 ml-1" title="WhatsApp terkirim"></i>' : ''}
                                                </td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    ` : ''}
                    
                    <div class="flex justify-end space-x-3">
                        <button onclick="viewNotificationLogs()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm">
                            <i class="fas fa-history mr-2"></i>Lihat Log
                        </button>
                        <button onclick="closeNotificationModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded text-sm">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHtml);
}

function closeNotificationModal() {
    const modal = document.getElementById('notification-result-modal');
    if (modal) {
        modal.remove();
    }
}

async function getLateLoansPreview() {
    try {
        const response = await fetch('api/get_late_loans_preview.php');
        const result = await response.json();
        
        if (result.success && result.data.length > 0) {
            showLateLoansPreview(result.data);
        } else {
            alert('Tidak ada peminjaman yang terlambat saat ini.');
        }
    } catch (error) {
        console.error('Error getting late loans preview:', error);
        alert('Gagal memuat preview peminjaman terlambat.');
    }
}

function showLateLoansPreview(lateLoans) {
    const modalHtml = `
        <div id="late-loans-preview-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-96 overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-red-800">
                            <i class="fas fa-exclamation-triangle mr-2"></i>Preview Peminjaman Terlambat (${lateLoans.length})
                        </h3>
                        <button onclick="closeLateLoansPreview()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    
                    <div class="overflow-x-auto mb-4">
                        <table class="w-full text-sm">
                            <thead class="bg-red-100">
                                <tr>
                                    <th class="text-left p-2">Peminjam</th>
                                    <th class="text-left p-2">NIM/NIP</th>
                                    <th class="text-left p-2">Alat</th>
                                    <th class="text-center p-2">Terlambat</th>
                                    <th class="text-center p-2">Kontak</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${lateLoans.map(loan => `
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="p-2 font-medium">${loan.nama_peminjam}</td>
                                        <td class="p-2">${loan.nomor_induk}</td>
                                        <td class="p-2">
                                            <div>${loan.nama_alat}</div>
                                            <div class="text-xs text-gray-500">${loan.kode_alat}</div>
                                        </td>
                                        <td class="text-center p-2">
                                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-medium">
                                                ${loan.hari_terlambat} hari
                                            </span>
                                        </td>
                                        <td class="text-center p-2">
                                            ${loan.email ? '<i class="fas fa-envelope text-blue-500" title="Ada email"></i>' : ''}
                                            ${loan.nomor_induk ? '<i class="fas fa-phone text-green-500 ml-1" title="Ada nomor"></i>' : ''}
                                        </td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-600">
                            <i class="fas fa-info-circle mr-1"></i>
                            Notifikasi akan dikirim ke semua peminjam yang terlambat di atas
                        </div>
                        <div class="flex space-x-3">
                            <button onclick="closeLateLoansPreview()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded text-sm">
                                Batal
                            </button>
                            <button onclick="closeLateLoansPreview(); sendLateNotifications();" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm">
                                <i class="fas fa-paper-plane mr-2"></i>Kirim Notifikasi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
}

function closeLateLoansPreview() {
    const modal = document.getElementById('late-loans-preview-modal');
    if (modal) {
        modal.remove();
    }
}

async function viewNotificationLogs() {
    try {
        const response = await fetch('api/get_notification_logs.php');
        const result = await response.json();
        
        if (result.success) {
            showNotificationLogs(result.data);
        } else {
            alert('Gagal memuat log notifikasi.');
        }
    } catch (error) {
        console.error('Error loading notification logs:', error);
        alert('Gagal memuat log notifikasi.');
    }
}

function showNotificationLogs(logs) {
    const modalHtml = `
        <div id="notification-logs-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-6xl w-full max-h-[90vh] overflow-hidden">
                <div class="p-6 border-b">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-history mr-2"></i>Log Notifikasi Sistem
                        </h3>
                        <button onclick="closeNotificationLogs()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <div class="p-6 overflow-y-auto max-h-[60vh]">
                    ${logs && logs.length > 0 ? `
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="text-left p-3">Waktu</th>
                                        <th class="text-left p-3">Penerima</th>
                                        <th class="text-left p-3">Alat</th>
                                        <th class="text-left p-3">Status</th>
                                        <th class="text-left p-3">Preview</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${logs.map(log => `
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="p-3">
                                                <div>${log.sent_at_formatted}</div>
                                            </td>
                                            <td class="p-3">
                                                <div class="font-medium">${log.recipient.name}</div>
                                                <div class="text-gray-500 text-xs">${log.recipient.id}</div>
                                            </td>
                                            <td class="p-3">
                                                ${log.peminjaman.nama_alat ? `
                                                    <div>${log.peminjaman.nama_alat}</div>
                                                    <div class="text-gray-500 text-xs">${log.peminjaman.kode_alat}</div>
                                                ` : '-'}
                                            </td>
                                            <td class="p-3">
                                                <span class="px-2 py-1 rounded text-xs bg-green-100 text-green-800">
                                                    Terkirim
                                                </span>
                                            </td>
                                            <td class="p-3">
                                                <button onclick="alert('${log.message_preview}...')" class="text-blue-600 hover:text-blue-800 text-xs">
                                                    <i class="fas fa-eye mr-1"></i>Lihat
                                                </button>
                                            </td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    ` : `
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-4"></i>
                            <p>Belum ada log notifikasi</p>
                        </div>
                    `}
                </div>
                
                <div class="p-6 border-t bg-gray-50">
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-600">
                            Total: ${logs ? logs.length : 0} notifikasi
                        </div>
                        <button onclick="closeNotificationLogs()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded text-sm">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
}

function closeNotificationLogs() {
    const modal = document.getElementById('notification-logs-modal');
    if (modal) {
        modal.remove();
    }
}

// Update notification statistics
async function updateNotificationStats() {
    try {
        // Update late loans count
        const lateResponse = await fetch('api/get_late_loans_preview.php');
        const lateResult = await lateResponse.json();
        
        const lateCountEl = document.getElementById('lateLoansCount');
        if (lateCountEl) {
            lateCountEl.textContent = lateResult.success ? lateResult.count : '0';
        }
        
        // Update sent notifications count (today)
        const today = new Date().toISOString().split('T')[0];
        const logsResponse = await fetch(`api/get_notification_logs.php?date_from=${today}`);
        const logsResult = await logsResponse.json();
        
        const sentCountEl = document.getElementById('sentNotificationsCount');
        if (sentCountEl) {
            sentCountEl.textContent = logsResult.success ? logsResult.data.length : '0';
        }
        
    } catch (error) {
        console.error('Error updating notification stats:', error);
    }
}

// Initialize notification features when page loads
function initializeNotificationFeatures() {
    // Update stats setiap 30 detik
    updateNotificationStats();
    setInterval(updateNotificationStats, 30000);
    
    console.log('✅ Notification features initialized');
}


// ===================================================================
// NAVIGATION AND INITIALIZATION
// ===================================================================
// Opsi: Versi fungsi showPage yang lebih rapi dan konsisten
function showPage(pageId) {
    // 1. Sembunyikan semua halaman
    document.querySelectorAll('.page').forEach(page => {
        page.classList.remove('active');
    });

    // 2. Tampilkan halaman yang dituju
    const activePage = document.getElementById(`page-${pageId}`);
    if (activePage) {
        activePage.classList.add('active');
    } else {
        console.error(`Error: Halaman dengan id 'page-${pageId}' tidak ditemukan!`);
        return; // Hentikan fungsi jika halaman tidak ada
    }

    // 3. Panggil fungsi untuk memuat data berdasarkan pageId
    switch (pageId) {
        case 'dashboard':
            loadDashboardData();
            break;
        case 'izin':
            tampilkanRiwayatIzin();
            break;
        case 'logbook_admin':
            tampilkanLogbookAdmin();
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
            tampilkanDataPeminjamanWithFilter();
            break;
        case 'presensi':
            tampilkanDataPresensi();
            break;
        // Tambahkan case baru ini
        case 'presensi_praktikum_admin':
            tampilkanDataPresensiPraktikum();
            break;
        default:
            console.log(`Tidak ada fungsi pemuatan data untuk pageId: ${pageId}`);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Check admin ID
    const adminId = document.body.dataset.adminId;
    if (!adminId) {
        console.error('Admin ID not found');
        alert('Session error: Admin ID tidak ditemukan. Silakan login ulang.');
        window.location.href = 'login.html';
        return;
    }
    
    // Load dashboard data on initial load
    loadDashboardData();
    
    // Navigation
    const navLinks = document.querySelectorAll('.nav-link');
    const pageTitle = document.getElementById('page-title');
    const menuButton = document.getElementById('menu-button');
    const sidebar = document.getElementById('sidebar');
    const contentArea = document.getElementById('content-area');

    if (navLinks) {
        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const pageId = this.dataset.page;
                if (pageTitle) pageTitle.textContent = this.querySelector('span').textContent;
                navLinks.forEach(nav => nav.classList.remove('active'));
                this.classList.add('active');
                showPage(pageId);
                if (window.innerWidth < 768 && sidebar) {
                    sidebar.classList.add('-translate-x-full');
                }
            });
        });
    }

    if (menuButton && sidebar) {
        menuButton.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
        });
    }

    // Content handlers
    if (contentArea) {
        contentArea.addEventListener('click', async function(e) {
            const target = e.target.closest('button');
            if (!target) return;
            
            if (target.classList.contains('btn-setujui') || target.classList.contains('btn-tolak')) {
                prosesAksiIzin(target.dataset.id, target.dataset.action);
            }
            
            if (target.classList.contains('btn-detail-dosen')) {
                tampilkanDetailDosen(target.dataset.id);
            }
            
            if (target.id === 'btn-kembali-dosen') {
                const formView = document.getElementById('dosen-form-view');
                const detailView = document.getElementById('dosen-detail-view');
                const listView = document.getElementById('dosen-list-view');
                
                if (formView) formView.classList.add('hidden');
                if (detailView) detailView.classList.add('hidden');
                if (listView) listView.classList.remove('hidden');
            }

            // Asisten CRUD handlers
           if (target.id === 'btn-tambah-asisten') {
                showModalAsisten(false);
            }
            if (target.classList.contains('btn-edit-asisten')) {
                const data = {
                    id: target.dataset.id,
                    userId: target.dataset.userId,
                    nim: target.dataset.nim, // ✅ ini yang kurang
                    nama: target.dataset.nama, 
                    jabatan: target.dataset.jabatan,
                    angkatan: target.dataset.angkatan,
                    status: target.dataset.status
                };
                 console.log('✏️ Edit button clicked dengan data:', data);
                showModalAsisten(true, data);
            }

             if (target.classList.contains('btn-delete-asisten')) {
                deleteAsisten(target.dataset.id, target.dataset.nama);
            }

            if (target.classList.contains('btn-kembalikan')) {
            kembalikanAlat(target.dataset.id, target.dataset.alat);
            }

            if (target.id === 'btn-close-modal' || target.id === 'btn-cancel') {
                hideModalAsisten();
            }

            // Dosen CRUD handlers - TAMBAHKAN INI
if (target.id === 'btn-tambah-dosen') {
    showFormDosen(false);
}

if (target.classList.contains('btn-edit-dosen')) {
    const data = {
        id: target.dataset.id,
        nama_dosen: target.dataset.nama,
        gelar_depan: target.dataset.gelarDepan,
        gelar_belakang: target.dataset.gelarBelakang,
        nidn: target.dataset.nidn,
        nip: target.dataset.nip,
        email: target.dataset.email,
        homebase_prodi: target.dataset.homebase,
        username: target.dataset.username
    };
    showFormDosen(true, data);
}

if (target.classList.contains('btn-delete-dosen')) {
    deleteDosen(target.dataset.id, target.dataset.nama);
}

if (target.id === 'btn-batal-form') {
    document.getElementById('dosen-form-view').classList.add('hidden');
    document.getElementById('dosen-list-view').classList.remove('hidden');
}
        });
    
    }

    // Modal form submission
    const formAsisten = document.getElementById('form-asisten');
    if (formAsisten) {
        formAsisten.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validasi form
            const namaUser = document.getElementById('nama-user').value.trim();
            const nimUser = document.getElementById('nim-user').value.trim();
            const jabatan = document.getElementById('jabatan').value.trim();
            const angkatan = document.getElementById('angkatan').value.trim();
            const status = document.getElementById('status-input').value.trim();
            
            if (!namaUser || !nimUser || !jabatan || !angkatan || !status) {
                alert('Semua field harus diisi!');
                return;
            }
            
            const formData = new FormData(this);
            console.log('📨 Submitting form dengan data:', Object.fromEntries(formData));
            saveAsisten(formData);
        });
        
    }
    // Form dosen submission - TAMBAHKAN INI
    const formDosen = document.getElementById('form-dosen');
    if (formDosen) {
        formDosen.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            console.log('📨 Submitting dosen form');
            saveDosen(formData);
        });
    }
    setTimeout(() => {
        initializeNotificationFeatures();
    }, 1000);
});