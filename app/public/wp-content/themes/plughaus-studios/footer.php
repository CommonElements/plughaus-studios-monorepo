<?php
/**
 * The template for displaying the footer
 *
 * @package PlugHaus_Studios
 */
?>

    <footer id="colophon" class="site-footer">
        <div class="container">
            
            <div class="footer-content">
                
                <!-- Footer Brand -->
                <div class="footer-brand">
                    <div class="footer-logo">
                        <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M13 9V3.5L23.5 12 13 20.5V15c-5.5 0-10 1.5-10 7 0-5 1.5-10 10-12z"/>
                            </svg>
                            <span>PlugHaus Studios</span>
                        </a>
                    </div>
                    
                    <p class="footer-description">
                        <?php
                        $description = get_bloginfo('description', 'display');
                        if ($description) {
                            echo esc_html($description);
                        } else {
                            _e('Professional WordPress plugin development for modern businesses.', 'plughaus-studios');
                        }
                        ?>
                    </p>
                    
                    <!-- Social Links -->
                    <div class="social-links">
                        <a href="#" aria-label="<?php _e('Twitter', 'plughaus-studios'); ?>">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                        </a>
                        <a href="#" aria-label="<?php _e('GitHub', 'plughaus-studios'); ?>">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                            </svg>
                        </a>
                        <a href="#" aria-label="<?php _e('LinkedIn', 'plughaus-studios'); ?>">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                            </svg>
                        </a>
                    </div>
                </div>
                
                <!-- Footer Links -->
                <div class="footer-links">
                    
                    <!-- Plugins Section -->
                    <div class="footer-section">
                        <h4><?php _e('Plugins', 'plughaus-studios'); ?></h4>
                        <ul>
                            <?php
                            // Get featured plugins
                            $plugins = get_posts(array(
                                'post_type' => 'phstudios_plugin',
                                'posts_per_page' => 4,
                                'meta_query' => array(
                                    array(
                                        'key' => '_plugin_status',
                                        'value' => 'available',
                                        'compare' => '='
                                    )
                                )
                            ));
                            
                            if ($plugins) {
                                foreach ($plugins as $plugin) {
                                    echo '<li><a href="' . esc_url(get_permalink($plugin->ID)) . '">' . esc_html($plugin->post_title) . '</a></li>';
                                }
                            } else {
                                echo '<li><a href="' . esc_url(home_url('/plugins/')) . '">' . __('Property Management', 'plughaus-studios') . '</a></li>';
                                echo '<li><a href="#">' . __('Payment Gateway', 'plughaus-studios') . '</a></li>';
                                echo '<li><a href="#">' . __('Document Automator', 'plughaus-studios') . '</a></li>';
                            }
                            ?>
                            <li><a href="<?php echo esc_url(home_url('/plugins/')); ?>"><?php _e('All Plugins', 'plughaus-studios'); ?></a></li>
                        </ul>
                    </div>
                    
                    <!-- Support Section -->
                    <div class="footer-section">
                        <h4><?php _e('Support', 'plughaus-studios'); ?></h4>
                        <ul>
                            <?php
                            $support_page = get_page_by_path('support');
                            $contact_page = get_page_by_path('contact');
                            $blog_page_id = get_option('page_for_posts');
                            ?>
                            
                            <?php if ($support_page) : ?>
                                <li><a href="<?php echo esc_url(get_permalink($support_page->ID)); ?>"><?php _e('Documentation', 'plughaus-studios'); ?></a></li>
                            <?php endif; ?>
                            
                            <?php if ($contact_page) : ?>
                                <li><a href="<?php echo esc_url(get_permalink($contact_page->ID)); ?>"><?php _e('Contact Support', 'plughaus-studios'); ?></a></li>
                            <?php endif; ?>
                            
                            <?php if ($blog_page_id) : ?>
                                <li><a href="<?php echo esc_url(get_permalink($blog_page_id)); ?>"><?php _e('Blog & Tutorials', 'plughaus-studios'); ?></a></li>
                            <?php endif; ?>
                            
                            <li><a href="#"><?php _e('Community Forums', 'plughaus-studios'); ?></a></li>
                        </ul>
                    </div>
                    
                    <!-- Company Section -->
                    <div class="footer-section">
                        <h4><?php _e('Company', 'plughaus-studios'); ?></h4>
                        <ul>
                            <?php
                            $about_page = get_page_by_path('about');
                            if ($about_page) {
                                echo '<li><a href="' . esc_url(get_permalink($about_page->ID)) . '">' . __('About Us', 'plughaus-studios') . '</a></li>';
                            }
                            ?>
                            <li><a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>"><?php _e('Privacy Policy', 'plughaus-studios'); ?></a></li>
                            <li><a href="<?php echo esc_url(home_url('/terms-of-service/')); ?>"><?php _e('Terms of Service', 'plughaus-studios'); ?></a></li>
                            <li><a href="<?php echo esc_url(home_url('/refund-policy/')); ?>"><?php _e('Refund Policy', 'plughaus-studios'); ?></a></li>
                        </ul>
                    </div>
                    
                </div>
                
            </div>
            
            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <div class="footer-copyright">
                    <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. <?php _e('All rights reserved.', 'plughaus-studios'); ?></p>
                </div>
                
                <?php if (has_nav_menu('footer')) : ?>
                    <nav class="footer-navigation">
                        <?php
                        wp_nav_menu(array(
                            'theme_location' => 'footer',
                            'menu_class'     => 'footer-menu',
                            'container'      => false,
                            'depth'          => 1,
                        ));
                        ?>
                    </nav>
                <?php endif; ?>
            </div>
            
        </div>
    </footer>

</div><!-- #page -->

<!-- Back to Top Button -->
<button id="back-to-top" class="back-to-top" aria-label="<?php _e('Back to top', 'plughaus-studios'); ?>">
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