<?php
/**
 * The template for displaying the footer
 *
 * @package Vireo_Designs
 */
?>

    <!-- Clean Footer -->
    <footer id="colophon" class="site-footer">
        <div class="container">
            <div class="footer-content">
                
                <!-- Footer Brand -->
                <div class="footer-brand">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="footer-logo" rel="home">
                        <?php 
                        $logo_url = get_template_directory_uri() . '/assets/images/vireo.png';
                        $logo_path = str_replace(get_template_directory_uri(), get_template_directory(), $logo_url);
                        if (file_exists($logo_path)) {
                            echo '<img src="' . $logo_url . '" alt="Vireo Logo" width="24" height="24" class="logo-image" />';
                        }
                        ?>
                        <span class="logo-text">Vireo</span>
                    </a>
                    <p class="footer-description">
                        Professional WordPress plugins for growing businesses.
                    </p>
                </div>
                
                <!-- Quick Links -->
                <nav class="footer-nav">
                    <a href="<?php echo esc_url(home_url('/industries/')); ?>">Industries</a>
                    <a href="<?php echo esc_url(home_url('/shop/')); ?>">Pricing</a>
                    <a href="<?php echo esc_url(home_url('/support/')); ?>">Support</a>
                    <a href="<?php echo esc_url(home_url('/contact/')); ?>">Contact</a>
                </nav>
                
                <!-- Social Links -->
                <div class="footer-social">
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" aria-label="GitHub"><i class="fab fa-github"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
                </div>
                
            </div>
            
            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Vireo. All rights reserved.</p>
                <div class="legal-links">
                    <a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>">Privacy</a>
                    <a href="<?php echo esc_url(home_url('/terms-of-service/')); ?>">Terms</a>
                </div>
            </div>
            
        </div>
    </footer>

</div><!-- #page -->

<!-- Back to Top Button -->
<button id="back-to-top" class="back-to-top" aria-label="<?php _e('Back to top', 'vireo-designs'); ?>">
    <i class="fas fa-chevron-up"></i>
</button>

<?php wp_footer(); ?>

<script>
// Back to top functionality
document.addEventListener('DOMContentLoaded', function() {
    const backToTopButton = document.getElementById('back-to-top');
    
    if (backToTopButton) {
        // Show/hide button based on scroll position
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopButton.classList.add('visible');
            } else {
                backToTopButton.classList.remove('visible');
            }
        });
        
        // Smooth scroll to top
        backToTopButton.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
    
    // Search toggle functionality
    const searchToggle = document.querySelector('.search-toggle');
    const searchClose = document.querySelector('.search-close');
    const headerSearch = document.getElementById('header-search');
    
    if (searchToggle && headerSearch) {
        searchToggle.addEventListener('click', function() {
            headerSearch.classList.add('active');
            setTimeout(() => {
                const searchInput = headerSearch.querySelector('input[type="search"]');
                if (searchInput) {
                    searchInput.focus();
                }
            }, 300);
        });
    }
    
    if (searchClose && headerSearch) {
        searchClose.addEventListener('click', function() {
            headerSearch.classList.remove('active');
        });
    }
    
    // Close search on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && headerSearch && headerSearch.classList.contains('active')) {
            headerSearch.classList.remove('active');
        }
    });
    
    // Mobile menu toggle
    const menuToggle = document.querySelector('.menu-toggle');
    const primaryMenu = document.getElementById('primary-menu');
    
    if (menuToggle && primaryMenu) {
        menuToggle.addEventListener('click', function() {
            const expanded = menuToggle.getAttribute('aria-expanded') === 'true';
            menuToggle.setAttribute('aria-expanded', !expanded);
            primaryMenu.classList.toggle('active');
            
            // Change icon
            const icon = menuToggle.querySelector('i');
            if (primaryMenu.classList.contains('active')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
    }
    
    // Auto-hide notifications
    const notifications = document.querySelectorAll('.notification');
    notifications.forEach(function(notification) {
        setTimeout(function() {
            notification.style.opacity = '0';
            setTimeout(function() {
                notification.remove();
            }, 300);
        }, 5000);
    });
});
</script>

<style>
/* Additional CSS for dynamic elements */
.back-to-top {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 50px;
    height: 50px;
    background: var(--primary-color);
    color: white;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    opacity: 0;
    visibility: hidden;
    transform: translateY(20px);
    transition: all 0.3s ease;
    z-index: 1000;
    box-shadow: var(--shadow-lg);
}

.back-to-top.visible {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.back-to-top:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
}

.header-search {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border-top: 1px solid var(--gray-200);
    box-shadow: var(--shadow-lg);
    transform: translateY(-100%);
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    z-index: 999;
}

.header-search.active {
    transform: translateY(0);
    opacity: 1;
    visibility: visible;
}

.header-search .container {
    display: flex;
    align-items: center;
    gap: var(--spacing-4);
    padding: var(--spacing-4) var(--spacing-6);
}

.header-search .search-form {
    flex: 1;
}

.header-search input {
    width: 100%;
    padding: var(--spacing-3);
    border: 1px solid var(--gray-300);
    border-radius: var(--radius);
    font-size: var(--font-size-base);
}

.search-close {
    background: none;
    border: none;
    font-size: var(--font-size-lg);
    color: var(--gray-500);
    cursor: pointer;
    padding: var(--spacing-2);
}

.notification {
    background: var(--success-color);
    color: white;
    padding: var(--spacing-4);
    margin-bottom: var(--spacing-4);
    transition: opacity 0.3s ease;
}

.notification-error {
    background: var(--danger-color);
}

.notification .container {
    display: flex;
    align-items: center;
    gap: var(--spacing-2);
}

@media (max-width: 768px) {
    .header-actions {
        gap: var(--spacing-2);
    }
    
    .header-actions .btn {
        padding: var(--spacing-2) var(--spacing-3);
        font-size: var(--font-size-sm);
    }
    
    .back-to-top {
        bottom: 15px;
        right: 15px;
        width: 45px;
        height: 45px;
    }
}
</style>

</body>
</html>