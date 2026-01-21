// ===================================================================
// LSTARS PORTAL - LOGIN SCRIPT
// Script untuk menangani login regular dan guest login
// ===================================================================

console.log('🚀 LSTARS Login System Loading...');

// ===================================================================
// UTILITY FUNCTIONS
// ===================================================================

// Create floating particles
function createParticles() {
    const particlesContainer = document.getElementById('particles');
    if (!particlesContainer) return;
    
    const particleCount = 50;

    for (let i = 0; i < particleCount; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        particle.style.left = Math.random() * 100 + '%';
        particle.style.animationDelay = Math.random() * 15 + 's';
        particle.style.animationDuration = (Math.random() * 10 + 10) + 's';
        particlesContainer.appendChild(particle);
    }
}

// Show alert function
function showAlert(message, type) {
    const alert = document.getElementById('alert');
    const alertBox = document.getElementById('alertBox');
    const alertMessage = document.getElementById('alertMessage');
    const messageSpan = alertMessage.querySelector('span');
    const icon = alertMessage.querySelector('i');

    if (!alert || !alertBox || !alertMessage || !messageSpan || !icon) {
        console.error('Alert elements not found');
        return;
    }

    messageSpan.textContent = message;

    if (type === 'success') {
        alertBox.className = 'alert-box-elegant alert-success-elegant';
        icon.className = 'fas fa-check-circle mr-2';
    } else {
        alertBox.className = 'alert-box-elegant alert-error-elegant';
        icon.className = 'fas fa-exclamation-triangle mr-2';
    }

    alert.classList.remove('hidden');
    
    // Auto hide after 6 seconds
    setTimeout(() => {
        alert.classList.add('hidden');
    }, 6000);
}

// ===================================================================
// reCAPTCHA removed
// ===================================================================
// Client-side reCAPTCHA helper functions were removed to keep UI/ buttons
// working while disabling CAPTCHA behavior. No token is requested or sent.

// ===================================================================
// GUEST LOGIN HANDLER
// ===================================================================
// GANTIKAN FUNGSI LAMA ANDA DENGAN YANG INI
async function handleGuestLogin() {
    console.log('Memproses login tamu...');
    
    const loading = document.getElementById('loading');
    const alertDiv = document.getElementById('alert');
    
    loading.classList.remove('hidden');
    alertDiv.classList.add('hidden');

    try {
        // Langsung panggil API tamu, tidak ada CAPTCHA sama sekali
        const response = await fetch('api/guest_login.php', { 
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'guest_login' })
        });

        const result = await response.json();

        if (result.success) {
            window.location.href = result.redirect;
        } else {
            showAlert(result.message || 'Gagal masuk sebagai tamu', 'error');
        }

    } catch (error) {
        console.error('Error login tamu:', error);
        showAlert('Terjadi kesalahan koneksi.', 'error');
    } finally {
        loading.classList.add('hidden');
    }
}

// ===================================================================
// REGULAR LOGIN HANDLER
// ===================================================================
async function handleRegularLogin(e) {
    e.preventDefault();
    console.log('🔄 Processing regular login...');

    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value.trim();
    const loading = document.getElementById('loading');
    const alert = document.getElementById('alert');
    const submitBtn = document.getElementById('loginButton');

    // Validation
    if (!email || !password) {
        showAlert('Email dan password harus diisi', 'error');
        return;
    }

    if (!submitBtn || !loading) {
        console.error('Required elements not found');
        return;
    }

    try {
        // Disable button and show loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memverifikasi...';
        loading.classList.remove('hidden');
        alert.classList.add('hidden');
        
        // Simulate API call delay for better UX
        await new Promise(resolve => setTimeout(resolve, 1500));

        const response = await fetch('api/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ 
                email, 
                password
            })
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const text = await response.text();
        console.log('📥 Raw response:', text);
        
        let result;
        try {
            result = JSON.parse(text);
        } catch (parseError) {
            console.error("❌ Response bukan JSON:", text);
            throw new Error("Server mengembalikan response yang tidak valid: " + text.slice(0, 100));
        }

        console.log('🎯 Login response:', result);
        loading.classList.add('hidden');

        if (result.success) {
            // Login berhasil - redirect berdasarkan role
            showAlert('Login berhasil! Mengalihkan ke dashboard...', 'success');

            setTimeout(() => {
                if (result.role === 'admin') {
                   window.location.href = 'admin-dashboard.php';
                } else {
                    window.location.href = 'user-dashboard.php';
                }
            }, 1500);

        } else {
            showAlert(result.message || 'Login gagal', 'error');
            // reCAPTCHA removed
        }

    } catch (error) {
        console.error('❌ Login error:', error);
        loading.classList.add('hidden');
        showAlert('Terjadi kesalahan: ' + error.message, 'error');
        // reCAPTCHA removed
    } finally {
        // Re-enable button
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-sign-in-alt mr-2"></i>Masuk ke Portal';
    }
}

// ===================================================================
// PASSWORD VISIBILITY TOGGLE
// ===================================================================
function initPasswordToggle() {
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    if (!togglePassword || !passwordInput) {
        console.error('Password toggle elements not found');
        return;
    }

    togglePassword.addEventListener('click', function () {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        // Enhanced icon toggle with animation
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
        
        // Add a subtle scale animation
        this.style.transform = 'translateY(-50%) scale(0.9)';
        setTimeout(() => {
            this.style.transform = 'translateY(-50%) scale(1)';
        }, 150);
    });
}

// ===================================================================
// INPUT FOCUS ANIMATIONS
// ===================================================================
function initInputAnimations() {
    const inputs = document.querySelectorAll('input');
    
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        input.addEventListener('blur', function() {
            this.style.transform = 'translateY(0)';
        });
    });
}

// reCAPTCHA initialization removed
// No client-side loading required

// ===================================================================
// INITIALIZATION
// ===================================================================
async function initializeLogin() {
    console.log('🎯 Initializing login system...');
    
    try {
        // Create particles animation
        createParticles();
        
        // Initialize password toggle
        initPasswordToggle();
        
        // Initialize input animations
        initInputAnimations();
        
        // Add event listeners
        const loginForm = document.getElementById('loginForm');
        const guestBtn = document.getElementById('guestLoginBtn');
        
        if (loginForm) {
            loginForm.addEventListener('submit', handleRegularLogin);
            console.log('✅ Regular login form listener added');
        } else {
            console.error('❌ Login form not found');
        }
        
        if (guestBtn) {
            guestBtn.addEventListener('click', handleGuestLogin);
            console.log('✅ Guest login button listener added');
        } else {
            console.error('❌ Guest login button not found');
        }
        
        console.log('🚀 Login system initialized successfully!');
        
    } catch (error) {
        console.error('❌ Failed to initialize login system:', error);
        showAlert('Sistem keamanan gagal dimuat. Silakan refresh halaman.', 'error');
    }
}

// ===================================================================
// WINDOW LOAD EVENT
// ===================================================================
window.addEventListener('load', function() {
    console.log('🌟 Window loaded, setting up login page...');
    
    // Set body opacity to 1 after window loads
    document.body.style.opacity = '1';
    
    // Initialize login system
    initializeLogin();
});

// ===================================================================
// DOM CONTENT LOADED EVENT (Fallback)
// ===================================================================
document.addEventListener('DOMContentLoaded', function() {
    console.log('📋 DOM loaded, ensuring login system is ready...');
    
    // Double-check initialization after a short delay
    setTimeout(() => {
        const loginForm = document.getElementById('loginForm');
        const guestBtn = document.getElementById('guestLoginBtn');
        
        if (!loginForm || !guestBtn) {
            console.warn('⚠️ Some elements missing, retrying initialization...');
            initializeLogin();
        }
    }, 500);
});

// ===================================================================
// ERROR HANDLING
// ===================================================================
window.addEventListener('error', function(e) {
    console.error('❌ JavaScript Error:', e.error);
    showAlert('Terjadi kesalahan pada halaman. Silakan refresh halaman.', 'error');
});

console.log('📦 LSTARS Login Script Loaded Successfully!');