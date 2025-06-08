<?php
/**
 * PlugHaus Studios Header Template
 * Professional WordPress Plugin Development Company
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
    
    <!-- Top Announcement Bar -->
    <div class="announcement-bar">
        <div class="container">
            <div class="announcement-content">
                <span class="announcement-text">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    <strong>New Release:</strong> Property Management Pro v2.0 now available with advanced analytics
                </span>
                <button class="announcement-close" aria-label="Close announcement">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Main Header -->
    <header id="masthead" class="site-header">
        <div class="container">
            <nav class="main-navigation">
                
                <!-- Logo/Brand -->
                <div class="site-branding">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo" rel="home">
                        <div class="logo-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M13 9V3.5L23.5 12 13 20.5V15c-5.5 0-10 1.5-10 7 0-5 1.5-10 10-12z"/>
                            </svg>
                        </div>
                        <div class="logo-text">
                            <span class="logo-name">PlugHaus Studios</span>
                            <span class="logo-tagline">WordPress Plugins</span>
                        </div>
                    </a>
                </div>
                
                <!-- Navigation Menu -->
                <div class="nav-menu-container">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'menu_class'     => 'nav-menu',
                        'container'      => false,
                        'fallback_cb'    => 'plughaus_professional_menu',
                    ));
                    ?>
                </div>
                
                <!-- Header Actions -->
                <div class="header-actions">
                    <a href="/pricing/" class="nav-link">Pricing</a>
                    <a href="/support/" class="nav-link">Support</a>
                    <a href="/contact/" class="nav-link">Contact</a>
                    <a href="/pricing/" class="btn btn-primary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M13 9V3.5L23.5 12 13 20.5V15c-5.5 0-10 1.5-10 7 0-5 1.5-10 10-12z"/>
                        </svg>
                        Get Started
                    </a>
                </div>
                
                <!-- Mobile Menu Toggle -->
                <button class="mobile-menu-toggle" aria-expanded="false" aria-label="Toggle mobile menu">
                    <span class="hamburger"></span>
                    <span class="hamburger"></span>
                    <span class="hamburger"></span>
                </button>
                
            </nav>
        </div>
    </header>