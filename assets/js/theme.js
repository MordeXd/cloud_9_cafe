/**
 * Cloud 9 Cafe - Theme JavaScript
 * UI Enhancements and Interactions
 */

// ============================================
// TOAST NOTIFICATION SYSTEM
// ============================================
const Toast = {
    container: null,
    
    init() {
        this.container = document.getElementById('toastContainer');
        if (!this.container) {
            this.container = document.createElement('div');
            this.container.className = 'toast-container';
            this.container.id = 'toastContainer';
            document.body.appendChild(this.container);
        }
    },
    
    show(message, type = 'info', duration = 3000) {
        if (!this.container) this.init();
        
        const icons = {
            success: 'fa-check-circle',
            error: 'fa-times-circle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle'
        };
        
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <div class="toast-icon">
                <i class="fas ${icons[type]}"></i>
            </div>
            <div class="toast-content">
                <p class="mb-0 fw-medium">${message}</p>
            </div>
        `;
        
        this.container.appendChild(toast);
        
        // Auto remove
        setTimeout(() => {
            toast.classList.add('hide');
            setTimeout(() => toast.remove(), 300);
        }, duration);
    },
    
    success(message, duration) { this.show(message, 'success', duration); },
    error(message, duration) { this.show(message, 'error', duration); },
    warning(message, duration) { this.show(message, 'warning', duration); },
    info(message, duration) { this.show(message, 'info', duration); }
};

// ============================================
// SIDEBAR MOBILE TOGGLE
// ============================================
const Sidebar = {
    init() {
        const sidebar = document.querySelector('.sidebar');
        const toggleBtn = document.querySelector('.sidebar-toggle');
        const overlay = document.querySelector('.sidebar-overlay');
        
        if (!sidebar || !toggleBtn) return;
        
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('show');
            if (overlay) overlay.classList.toggle('show');
        });
        
        if (overlay) {
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            });
        }
        
        // Close sidebar when clicking on nav links (mobile)
        const navLinks = sidebar.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 992) {
                    sidebar.classList.remove('show');
                    if (overlay) overlay.classList.remove('show');
                }
            });
        });
    }
};

// ============================================
// NAVBAR SCROLL EFFECT
// ============================================
const Navbar = {
    init() {
        const navbar = document.querySelector('.navbar');
        if (!navbar) return;
        
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }
};

// ============================================
// ANIMATION ON SCROLL
// ============================================
const ScrollAnimations = {
    init() {
        const animatedElements = document.querySelectorAll('.animate-on-scroll');
        
        if (animatedElements.length === 0) return;
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in-up');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        
        animatedElements.forEach(el => observer.observe(el));
    }
};

// ============================================
// FORM VALIDATION ENHANCEMENTS
// ============================================
const FormEnhancements = {
    init() {
        // Password toggle visibility
        const togglePasswordBtns = document.querySelectorAll('.toggle-password');
        togglePasswordBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const input = document.querySelector(btn.dataset.target);
                const icon = btn.querySelector('i');
                
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });
        
        // Input focus effects
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('focus', () => {
                input.closest('.form-group')?.classList.add('focused');
            });
            
            input.addEventListener('blur', () => {
                input.closest('.form-group')?.classList.remove('focused');
            });
        });
    }
};

// ============================================
// QUANTITY INPUT SPINNER
// ============================================
const QuantitySpinner = {
    init() {
        const spinners = document.querySelectorAll('.quantity-spinner');
        
        spinners.forEach(spinner => {
            const input = spinner.querySelector('input');
            const btnUp = spinner.querySelector('.btn-up');
            const btnDown = spinner.querySelector('.btn-down');
            
            if (btnUp) {
                btnUp.addEventListener('click', () => {
                    input.value = parseInt(input.value || 0) + 1;
                    input.dispatchEvent(new Event('change'));
                });
            }
            
            if (btnDown) {
                btnDown.addEventListener('click', () => {
                    const val = parseInt(input.value || 0);
                    if (val > 1) {
                        input.value = val - 1;
                        input.dispatchEvent(new Event('change'));
                    }
                });
            }
        });
    }
};

// ============================================
// TABLE ROW ACTIONS
// ============================================
const TableActions = {
    init() {
        // Confirm delete
        const deleteBtns = document.querySelectorAll('[data-confirm]');
        deleteBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const message = btn.dataset.confirm || 'Are you sure?';
                if (!confirm(message)) {
                    e.preventDefault();
                }
            });
        });
    }
};

// ============================================
// IMAGE PREVIEW FOR FILE INPUTS
// ============================================
const ImagePreview = {
    init() {
        const fileInputs = document.querySelectorAll('[data-preview]');
        
        fileInputs.forEach(input => {
            const previewId = input.dataset.preview;
            const preview = document.getElementById(previewId);
            
            if (!preview) return;
            
            input.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        preview.src = e.target.result;
                        preview.classList.remove('d-none');
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    }
};

// ============================================
// DROPDOWN STATUS UPDATE
// ============================================
const StatusDropdown = {
    init() {
        const statusSelects = document.querySelectorAll('.status-update');
        
        statusSelects.forEach(select => {
            select.addEventListener('change', () => {
                const form = select.closest('form');
                if (form) {
                    form.submit();
                }
            });
        });
    }
};

// ============================================
// LOADING SPINNER
// ============================================
const LoadingSpinner = {
    show(message = 'Loading...') {
        const spinner = document.createElement('div');
        spinner.id = 'globalLoadingSpinner';
        spinner.innerHTML = `
            <div style="
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(255,255,255,0.9);
                z-index: 9999;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            ">
                <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-primary fw-medium">${message}</p>
            </div>
        `;
        document.body.appendChild(spinner);
    },
    
    hide() {
        const spinner = document.getElementById('globalLoadingSpinner');
        if (spinner) spinner.remove();
    }
};

// ============================================
// CART COUNTER
// ============================================
const CartCounter = {
    update(count) {
        const badges = document.querySelectorAll('.cart-badge');
        badges.forEach(badge => {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'flex' : 'none';
            
            // Pulse animation
            badge.classList.add('pulse');
            setTimeout(() => badge.classList.remove('pulse'), 500);
        });
    }
};

// ============================================
// SMOOTH SCROLL
// ============================================
const SmoothScroll = {
    init() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }
};

// ============================================
// TOOLTIP INITIALIZATION
// ============================================
const Tooltips = {
    init() {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        if (tooltipTriggerList.length > 0 && typeof bootstrap !== 'undefined') {
            tooltipTriggerList.forEach(el => new bootstrap.Tooltip(el));
        }
    }
};

// ============================================
// MODAL AUTO-FOCUS
// ============================================
const ModalFocus = {
    init() {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            modal.addEventListener('shown.bs.modal', () => {
                const input = modal.querySelector('input:not([type="hidden"])');
                if (input) input.focus();
            });
        });
    }
};

// ============================================
// SEARCH INPUT CLEAR
// ============================================
const SearchInput = {
    init() {
        const searchInputs = document.querySelectorAll('.search-input');
        
        searchInputs.forEach(input => {
            const clearBtn = input.parentElement.querySelector('.search-clear');
            
            if (clearBtn) {
                input.addEventListener('input', () => {
                    clearBtn.style.display = input.value ? 'block' : 'none';
                });
                
                clearBtn.addEventListener('click', () => {
                    input.value = '';
                    input.focus();
                    clearBtn.style.display = 'none';
                });
            }
        });
    }
};

// ============================================
// PRINT HANDLER
// ============================================
const PrintHandler = {
    init() {
        const printBtns = document.querySelectorAll('[data-print]');
        printBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                window.print();
            });
        });
    }
};

// ============================================
// CONFIRMATION DIALOG
// ============================================
const ConfirmDialog = {
    show(message, onConfirm, onCancel) {
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-body text-center p-4">
                        <div class="mb-3">
                            <i class="fas fa-question-circle fa-3x text-primary"></i>
                        </div>
                        <h5 class="mb-3">${message}</h5>
                        <div class="d-flex justify-content-center gap-2">
                            <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button class="btn btn-primary" id="confirmBtn">Confirm</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
        
        modal.querySelector('#confirmBtn').addEventListener('click', () => {
            if (onConfirm) onConfirm();
            bsModal.hide();
            modal.addEventListener('hidden.bs.modal', () => modal.remove());
        });
        
        modal.addEventListener('hidden.bs.modal', () => {
            if (onCancel) onCancel();
            modal.remove();
        });
    }
};

// ============================================
// DARK MODE SUPPORT
// ============================================
const DarkMode = {
    init() {
        const toggle = document.querySelector('[data-darkmode-toggle]');
        if (!toggle) return;
        
        const savedMode = localStorage.getItem('darkMode');
        if (savedMode === 'true') {
            document.body.classList.add('dark-mode');
        }
        
        toggle.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            const isDark = document.body.classList.contains('dark-mode');
            localStorage.setItem('darkMode', isDark);
        });
    }
};

// ============================================
// INITIALIZE ALL ON DOM READY
// ============================================
document.addEventListener('DOMContentLoaded', () => {
    Toast.init();
    Sidebar.init();
    Navbar.init();
    ScrollAnimations.init();
    FormEnhancements.init();
    QuantitySpinner.init();
    TableActions.init();
    ImagePreview.init();
    StatusDropdown.init();
    SmoothScroll.init();
    Tooltips.init();
    ModalFocus.init();
    SearchInput.init();
    PrintHandler.init();
    DarkMode.init();
});

// Export for global access
window.Cloud9Theme = {
    Toast,
    LoadingSpinner,
    CartCounter,
    ConfirmDialog
};
