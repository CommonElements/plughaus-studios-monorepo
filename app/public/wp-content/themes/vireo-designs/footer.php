<?php
/**
 * The template for displaying the footer
 *
 * @package Vireo_Designs
 */
?>

    <!-- Professional Footer -->
    <footer id="colophon" class="site-footer">
        <div class="container">
            
            <!-- Main Footer Content -->
            <div class="footer-main">
                
                <!-- Company Info -->
                <div class="footer-section footer-brand">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="footer-logo" rel="home">
                        <?php 
                        $logo_url = get_template_directory_uri() . '/assets/images/vireo.png';
                        $logo_path = str_replace(get_template_directory_uri(), get_template_directory(), $logo_url);
                        if (file_exists($logo_path)) {
                            echo '<img src="' . $logo_url . '" alt="Vireo Logo" width="32" height="32" class="logo-image" />';
                        }
                        ?>
                        <span class="logo-text">Vireo Designs</span>
                    </a>
                    <p class="footer-description">
                        Professional WordPress plugins designed for growing businesses. Industry-specific solutions that save time and increase productivity.
                    </p>
                    <div class="footer-social">
                        <a href="https://twitter.com/vireodesigns" aria-label="Follow us on Twitter" target="_blank"><i class="fab fa-twitter"></i></a>
                        <a href="https://github.com/vireodesigns" aria-label="View our GitHub" target="_blank"><i class="fab fa-github"></i></a>
                        <a href="https://linkedin.com/company/vireodesigns" aria-label="Connect on LinkedIn" target="_blank"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                
                <!-- Products -->
                <div class="footer-section">
                    <h4>Our Plugins</h4>
                    <nav class="footer-links">
                        <a href="<?php echo esc_url(home_url('/plugins/')); ?>">All Plugins</a>
                        <a href="<?php echo esc_url(home_url('/industry-property-management/')); ?>">Property Management</a>
                        <a href="<?php echo esc_url(home_url('/industry-sports-leagues/')); ?>">Sports Leagues</a>
                        <a href="<?php echo esc_url(home_url('/industry-equipment-rental/')); ?>">Equipment Rental</a>
                        <a href="<?php echo esc_url(home_url('/industry-automotive/')); ?>">Automotive</a>
                        <a href="<?php echo esc_url(home_url('/shop/')); ?>">Pro Versions</a>
                    </nav>
                </div>
                
                <!-- Resources -->
                <div class="footer-section">
                    <h4>Resources</h4>
                    <nav class="footer-links">
                        <a href="<?php echo esc_url(home_url('/support/')); ?>">Documentation</a>
                        <a href="<?php echo esc_url(home_url('/support/tutorials/')); ?>">Tutorials</a>
                        <a href="<?php echo esc_url(home_url('/support/api/')); ?>">API Reference</a>
                        <a href="<?php echo esc_url(home_url('/blog/')); ?>">Blog</a>
                        <a href="<?php echo esc_url(home_url('/changelog/')); ?>">Changelog</a>
                        <a href="<?php echo esc_url(home_url('/roadmap/')); ?>">Roadmap</a>
                    </nav>
                </div>
                
                <!-- Company -->
                <div class="footer-section">
                    <h4>Company</h4>
                    <nav class="footer-links">
                        <a href="<?php echo esc_url(home_url('/about/')); ?>">About Us</a>
                        <a href="<?php echo esc_url(home_url('/contact/')); ?>">Contact</a>
                        <a href="<?php echo esc_url(home_url('/careers/')); ?>">Careers</a>
                        <a href="<?php echo esc_url(home_url('/partners/')); ?>">Partners</a>
                        <a href="<?php echo esc_url(home_url('/affiliate-program/')); ?>">Affiliate Program</a>
                        <a href="<?php echo esc_url(home_url('/press/')); ?>">Press Kit</a>
                    </nav>
                </div>
                
            </div>
            
            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <div class="footer-bottom-left">
                    <p>&copy; <?php echo date('Y'); ?> Vireo Designs. All rights reserved.</p>
                    <p class="footer-tagline">Empowering businesses with WordPress-native solutions.</p>
                </div>
                <nav class="legal-links">
                    <a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>">Privacy Policy</a>
                    <a href="<?php echo esc_url(home_url('/terms-of-service/')); ?>">Terms of Service</a>
                    <a href="<?php echo esc_url(home_url('/cookie-policy/')); ?>">Cookie Policy</a>
                    <a href="<?php echo esc_url(home_url('/refund-policy/')); ?>">Refund Policy</a>
                </nav>
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
/* Enhanced Header & Footer Styling */

/* Header Actions */
.header-actions {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.cart-wrapper,
.account-wrapper {
    display: flex;
    align-items: center;
}

.cart-link,
.account-link,
.login-link,
.support-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-color);
    text-decoration: none;
    padding: 0.5rem;
    border-radius: 6px;
    transition: all 0.2s ease;
    font-weight: 500;
}

.cart-link:hover,
.account-link:hover,
.login-link:hover,
.support-link:hover {
    background: var(--gray-100);
    color: var(--primary-color);
}

.cart-count {
    background: var(--primary-color);
    color: white;
    border-radius: 50%;
    padding: 0.125rem 0.375rem;
    font-size: 0.75rem;
    font-weight: 600;
    min-width: 1.25rem;
    text-align: center;
}

.cart-total {
    font-weight: 600;
    color: var(--primary-color);
}

/* Footer Styling */
.site-footer {
    background: #1e293b;
    color: #e2e8f0;
    padding: 3rem 0 1rem;
}

.footer-main {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr;
    gap: 3rem;
    margin-bottom: 3rem;
}

.footer-section h4 {
    color: white;
    font-size: 1.125rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
}

.footer-brand .footer-logo {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    color: white;
    text-decoration: none;
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.footer-description {
    color: #94a3b8;
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.footer-social {
    display: flex;
    gap: 1rem;
}

.footer-social a {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2.5rem;
    height: 2.5rem;
    background: #334155;
    color: #e2e8f0;
    border-radius: 6px;
    text-decoration: none;
    transition: all 0.2s ease;
}

.footer-social a:hover {
    background: var(--primary-color);
    color: white;
    transform: translateY(-2px);
}

.footer-links {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.footer-links a {
    color: #94a3b8;
    text-decoration: none;
    transition: color 0.2s ease;
}

.footer-links a:hover {
    color: white;
}

.footer-bottom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 2rem;
    border-top: 1px solid #334155;
    gap: 2rem;
}

.footer-bottom-left p {
    margin: 0;
    color: #94a3b8;
    font-size: 0.875rem;
}

.footer-tagline {
    font-style: italic;
    opacity: 0.8;
}

.legal-links {
    display: flex;
    gap: 1.5rem;
}

.legal-links a {
    color: #94a3b8;
    text-decoration: none;
    font-size: 0.875rem;
    transition: color 0.2s ease;
}

.legal-links a:hover {
    color: white;
}

/* Responsive Footer */
@media (max-width: 1024px) {
    .footer-main {
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
    }
}

@media (max-width: 768px) {
    .header-actions {
        gap: 1rem;
    }
    
    .header-actions span {
        display: none;
    }
    
    .footer-main {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .footer-bottom {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
    
    .legal-links {
        flex-wrap: wrap;
        justify-content: center;
        gap: 1rem;
    }
}

/* Back to Top Button */
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