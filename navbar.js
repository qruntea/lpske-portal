// LSTARS Portal - Complete Navbar JavaScript (Fixed Navigation)
// This file handles navbar functionality without breaking existing script.js

// Navbar JavaScript Functions
function toggleDropdown(dropdownId) {
    const dropdown = document.getElementById(dropdownId);
    const allDropdowns = document.querySelectorAll('.nav-dropdown, .profile-dropdown');
    
    // Close all other dropdowns
    allDropdowns.forEach(d => {
        if (d.id !== dropdownId) {
            d.classList.remove('show');
            // Reset chevron rotation
            const chevron = d.previousElementSibling?.querySelector('i[id$="-chevron"]');
            if (chevron) chevron.style.transform = 'rotate(0deg)';
        }
    });
    
    // Toggle current dropdown
    dropdown.classList.toggle('show');
    
    // Rotate chevron
    const chevron = document.getElementById(dropdownId.replace('-dropdown', '-chevron'));
    if (chevron) {
        chevron.style.transform = dropdown.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';
    }
}

// Enhanced Mobile menu toggle - KEEP ORIGINAL LOGIC
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Navbar JavaScript Loaded');
    
    // Mobile menu functionality
    const menuButton = document.getElementById('menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (menuButton && mobileMenu) {
        menuButton.addEventListener('click', function() {
            const menuIcon = document.getElementById('mobile-menu-icon');
            
            mobileMenu.classList.toggle('show');
            
            if (mobileMenu.classList.contains('show')) {
                menuIcon.classList.remove('fa-bars');
                menuIcon.classList.add('fa-times');
            } else {
                menuIcon.classList.remove('fa-times');
                menuIcon.classList.add('fa-bars');
            }
        });
    }

    // IMPORTANT: Re-enable navigation for dropdown items
    // This ensures dropdown links work with the existing navigation system
    setTimeout(function() {
        console.log('🔧 Re-initializing navigation listeners...');
        
        // Find main navigation container
        const mainNav = document.getElementById('main-nav');
        if (mainNav) {
            // Remove existing click listeners to avoid conflicts
            mainNav.removeEventListener('click', handleNavClick);
            
            // Add our enhanced click handler
            mainNav.addEventListener('click', handleNavClick);
            console.log('✅ Navigation listeners attached to main-nav');
        }

        // Also handle mobile navigation
        const mobileNavLinks = document.querySelectorAll('#mobile-menu .nav-link[data-page]');
        mobileNavLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const pageId = this.dataset.page;
                if (pageId) {
                    // Trigger the same navigation as main nav
                    navigateToPage(pageId, this);
                    
                    // Close mobile menu
                    mobileMenu.classList.remove('show');
                    const menuIcon = document.getElementById('mobile-menu-icon');
                    if (menuIcon) {
                        menuIcon.classList.remove('fa-times');
                        menuIcon.classList.add('fa-bars');
                    }
                }
            });
        });
        console.log('✅ Mobile navigation listeners attached');

        // Handle footer navigation links
        const footerNavLinks = document.querySelectorAll('footer .nav-link[data-page]');
        footerNavLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const pageId = this.dataset.page;
                if (pageId) {
                    navigateToPage(pageId, this);
                }
            });
        });
        console.log('✅ Footer navigation listeners attached');

    }, 500); // Wait for main script.js to load
});

// Enhanced navigation click handler
function handleNavClick(e) {
    // Find the clicked nav-link
    const link = e.target.closest('.nav-link[data-page]');
    if (!link) return;
    
    e.preventDefault();
    e.stopPropagation();
    
    const pageId = link.dataset.page;
    if (pageId) {
        console.log(`🔄 Navigating to: ${pageId}`);
        navigateToPage(pageId, link);
        
        // Close any open dropdowns
        document.querySelectorAll('.nav-dropdown').forEach(dropdown => {
            dropdown.classList.remove('show');
        });
    }
}

// Centralized navigation function
function navigateToPage(pageId, linkElement) {
    console.log(`📄 Switching to page: ${pageId}`);
    
    // Update active states
    document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
    if (linkElement) {
        linkElement.classList.add('active');
        
        // Also update corresponding mobile/footer links
        document.querySelectorAll(`.nav-link[data-page="${pageId}"]`).forEach(l => {
            l.classList.add('active');
        });
    }
    
    // Update page title
    const pageTitle = document.getElementById('page-title');
    const spanText = linkElement?.querySelector('span');
    if (pageTitle && spanText) {
        pageTitle.textContent = spanText.textContent;
    }
    
    // Show selected page
    document.querySelectorAll('.page').forEach(p => {
        if (p.id === `page-${pageId}`) {
            p.classList.remove('hidden');
        } else {
            p.classList.add('hidden');
        }
    });

    // Page-specific initialization (same as original script.js)
    switch(pageId) {
        case 'dashboard':
            setTimeout(() => {
                if (window.loadUserDashboardData) {
                    window.loadUserDashboardData();
                }
                if (window.initializeClock) {
                    window.initializeClock();
                }
            }, 100);
            break;
        case 'dosen':
            if (window.tampilkanDataDosen) {
                window.tampilkanDataDosen();
            }
            break;
        case 'asisten':
            if (window.tampilkanDataAsisten) {
                window.tampilkanDataAsisten();
            }
            break;
        case 'inventory':
            if (window.tampilkanDataInventory) {
                window.tampilkanDataInventory();
            }
            break;
        case 'peminjaman':
            if (window.muatOpsiPeminjaman) {
                window.muatOpsiPeminjaman();
            }
            if (window.tampilkanRiwayatPeminjaman) {
                window.tampilkanRiwayatPeminjaman();
            }
            break;
        case 'izin':
            if (window.muatOpsiDosen) {
                window.muatOpsiDosen();
            }
            if (window.tampilkanRiwayatIzin) {
                window.tampilkanRiwayatIzin();
            }
            // Refresh statistik karena mungkin ada perubahan izin
            setTimeout(() => {
                if (window.refreshDashboardStatistics) {
                    window.refreshDashboardStatistics();
                }
            }, 500);
            break;
        case 'presensi':
            if (window.muatHalamanPresensi) {
                window.muatHalamanPresensi();
            }
            break;
        case 'dokumentasi':
            if (window.tampilkanDokumentasi) {
                window.tampilkanDokumentasi();
            }
            break;
        case 'LogbookKegiatanLab':
            console.log('📖 LogBook page loaded');
            break;
    }
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    const dropdowns = document.querySelectorAll('.nav-dropdown, .profile-dropdown');
    const buttons = document.querySelectorAll('[onclick*="toggleDropdown"]');
    
    let clickedOnButton = false;
    buttons.forEach(button => {
        if (button.contains(event.target)) {
            clickedOnButton = true;
        }
    });
    
    if (!clickedOnButton) {
        dropdowns.forEach(dropdown => {
            if (!dropdown.contains(event.target)) {
                dropdown.classList.remove('show');
                // Reset chevron rotation
                const chevron = dropdown.previousElementSibling?.querySelector('i[id$="-chevron"]');
                if (chevron) chevron.style.transform = 'rotate(0deg)';
            }
        });
    }
});

// Update navbar time
function updateNavbarTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        timeZone: 'Asia/Jakarta'
    });
    
    const timeElement = document.getElementById('current-navbar-time');
    if (timeElement) {
        timeElement.textContent = timeString;
    }
}

// Initialize navbar time update
document.addEventListener('DOMContentLoaded', function() {
    updateNavbarTime();
    setInterval(updateNavbarTime, 1000);
});

// Global function to manually trigger navigation (for debugging)
window.triggerNavigation = function(pageId) {
    const link = document.querySelector(`.nav-link[data-page="${pageId}"]`);
    if (link) {
        navigateToPage(pageId, link);
    }
};

console.log('✅ LSTARS Navbar JavaScript fully loaded and ready!');