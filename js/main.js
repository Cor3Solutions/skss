// Main JavaScript for Oriental Muayboran Academy

document.addEventListener('DOMContentLoaded', function() {
    
    // ====================================
    // Mobile Menu Toggle
    // ====================================
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const mainNav = document.getElementById('mainNav');
    
    if (mobileMenuToggle && mainNav) {
        mobileMenuToggle.addEventListener('click', function() {
            mainNav.classList.toggle('active');
            mobileMenuToggle.classList.toggle('active');
        });
    }
    
    // ====================================
    // Dropdown Menu for Mobile
    // ====================================
    const dropdownParents = document.querySelectorAll('.has-dropdown');
    
    dropdownParents.forEach(parent => {
        const link = parent.querySelector('.nav-link');
        
        if (window.innerWidth <= 768 && link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                parent.classList.toggle('active');
            });
        }
    });
    
    // ====================================
    // Header Scroll Effect
    // ====================================
    const header = document.querySelector('.site-header');
    let lastScroll = 0;
    
    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset;
        
        if (currentScroll > 100) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
        
        lastScroll = currentScroll;
    });
    
    // ====================================
    // Smooth Scroll for Anchor Links
    // ====================================
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            if (href !== '#' && href !== '#!') {
                const target = document.querySelector(href);
                
                if (target) {
                    e.preventDefault();
                    
                    const headerHeight = header ? header.offsetHeight : 0;
                    const targetPosition = target.offsetTop - headerHeight;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                    
                    // Close mobile menu if open
                    if (mainNav) {
                        mainNav.classList.remove('active');
                    }
                    if (mobileMenuToggle) {
                        mobileMenuToggle.classList.remove('active');
                    }
                }
            }
        });
    });
    
    // ====================================
    // Active Navigation Link
    // ====================================
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll(".nav-link");
    
    navLinks.forEach(link => {
        try {
            const linkPath = new URL(link.href, window.location.origin).pathname;
            
            // Check for exact match or if current path ends with the link path
            if (linkPath === currentPath || currentPath.endsWith(linkPath.split("/").pop())) {
                link.classList.add("active");
                
                // Also highlight parent dropdown if this is a dropdown item
                const dropdownItem = link.closest("ul.dropdown");
                if (dropdownItem) {
                    const parentDropdown = dropdownItem.closest(".has-dropdown");
                    if (parentDropdown) {
                        const parentLink = parentDropdown.querySelector(":scope > .nav-link");
                        if (parentLink) {
                            parentLink.classList.add("active");
                        }
                    }
                }
            }
        } catch (e) {
            console.log("Error processing nav link:", e);
        }
    });

    
    // ====================================
    // Form Validation
    // ====================================
    const forms = document.querySelectorAll('form[data-validate]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
            
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.classList.add('error');
                    
                    // Show error message
                    let errorMsg = input.nextElementSibling;
                    if (!errorMsg || !errorMsg.classList.contains('error-message')) {
                        errorMsg = document.createElement('span');
                        errorMsg.className = 'error-message';
                        errorMsg.textContent = 'This field is required';
                        errorMsg.style.color = 'var(--color-primary)';
                        errorMsg.style.fontSize = '0.875rem';
                        errorMsg.style.marginTop = '0.25rem';
                        errorMsg.style.display = 'block';
                        input.parentNode.appendChild(errorMsg);
                    }
                } else {
                    input.classList.remove('error');
                    const errorMsg = input.parentNode.querySelector('.error-message');
                    if (errorMsg) {
                        errorMsg.remove();
                    }
                }
            });
            
            // Validate email
            const emailInputs = form.querySelectorAll('input[type="email"]');
            emailInputs.forEach(input => {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (input.value && !emailRegex.test(input.value)) {
                    isValid = false;
                    input.classList.add('error');
                    
                    let errorMsg = input.nextElementSibling;
                    if (!errorMsg || !errorMsg.classList.contains('error-message')) {
                        errorMsg = document.createElement('span');
                        errorMsg.className = 'error-message';
                        errorMsg.textContent = 'Please enter a valid email address';
                        errorMsg.style.color = 'var(--color-primary)';
                        errorMsg.style.fontSize = '0.875rem';
                        errorMsg.style.marginTop = '0.25rem';
                        errorMsg.style.display = 'block';
                        input.parentNode.appendChild(errorMsg);
                    }
                }
            });
            
            if (!isValid) {
                e.preventDefault();
            }
        });
        
        // Remove error on input
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                this.classList.remove('error');
                const errorMsg = this.parentNode.querySelector('.error-message');
                if (errorMsg) {
                    errorMsg.remove();
                }
            });
        });
    });
    
    // ====================================
    // Fade-in Animation on Scroll
    // ====================================
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in-up');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    const animatedElements = document.querySelectorAll('.card, .section-header, .form-group');
    animatedElements.forEach(el => observer.observe(el));
    
    // ====================================
    // Close mobile menu when clicking outside
    // ====================================
    document.addEventListener('click', function(e) {
        if (mainNav && mainNav.classList.contains('active')) {
            if (!mainNav.contains(e.target) && !mobileMenuToggle.contains(e.target)) {
                mainNav.classList.remove('active');
                mobileMenuToggle.classList.remove('active');
            }
        }
    });
    
    // ====================================
    // Password Toggle Visibility
    // ====================================
    const passwordToggles = document.querySelectorAll('.password-toggle');
    
    passwordToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const input = this.previousElementSibling;
            
            if (input && input.type === 'password') {
                input.type = 'text';
                this.textContent = '👁️';
            } else if (input) {
                input.type = 'password';
                this.textContent = '👁️';
            }
        });
    });
    
    // ====================================
    // Auto-hide alerts after 5 seconds
    // ====================================
    const alerts = document.querySelectorAll('.alert');
    
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
    
});

// ====================================
// Utility Functions
// ====================================

// Show loading state
function showLoading(button) {
    if (button) {
        button.disabled = true;
        button.dataset.originalText = button.textContent;
        button.textContent = 'Loading...';
    }
}

// Hide loading state
function hideLoading(button) {
    if (button && button.dataset.originalText) {
        button.disabled = false;
        button.textContent = button.dataset.originalText;
    }
}

// Show notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        padding: 1rem 2rem;
        background: white;
        border-left: 4px solid var(--color-${type === 'error' ? 'primary' : 'secondary'});
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        z-index: 9999;
        animation: slideInRight 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}