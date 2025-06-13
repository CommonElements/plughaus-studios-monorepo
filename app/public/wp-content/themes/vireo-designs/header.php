<?php
/**
 * Vireo Designs Header Template
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
    
    
    <header id="masthead" class="site-header">
        <div class="container">
            <div class="header-content">
                
                <!-- Logo -->
                <div class="site-branding">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo" rel="home">
                        <?php 
                        $logo_url = get_template_directory_uri() . '/assets/images/vireo.png';
                        $logo_path = str_replace(get_template_directory_uri(), get_template_directory(), $logo_url);
                        if (file_exists($logo_path)) {
                            echo '<img src="' . $logo_url . '" alt="Vireo Logo" width="28" height="28" class="logo-image" />';
                        }
                        ?>
                        <span class="logo-text">Vireo</span>
                    </a>
                </div>
                
                <!-- Navigation -->
                <nav class="main-navigation">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'menu_class'     => 'nav-menu',
                        'container'      => false,
                        'fallback_cb'    => function() {
                            echo '<ul class="nav-menu">';
                            echo '<li><a href="' . esc_url(home_url('/')) . '">Home</a></li>';
                            echo '<li class="dropdown">';
                            echo '<a href="' . esc_url(home_url('/industries/')) . '" class="dropdown-toggle">Industries <i class="fas fa-chevron-down"></i></a>';
                            echo '<ul class="dropdown-menu">';
                            echo '<li><a href="' . esc_url(home_url('/industries/')) . '">All Industries</a></li>';
                            echo '<li><a href="' . esc_url(home_url('/industry-property-management/')) . '">Property Management</a></li>';
                            echo '<li><a href="' . esc_url(home_url('/industry-sports-leagues/')) . '">Sports Leagues</a></li>';
                            echo '<li><a href="' . esc_url(home_url('/industry-automotive/')) . '">Automotive</a></li>';
                            echo '<li><a href="' . esc_url(home_url('/industry-gym-fitness/')) . '">Gym & Fitness</a></li>';
                            echo '<li><a href="' . esc_url(home_url('/industry-equipment-rental/')) . '">Equipment Rental</a></li>';
                            echo '<li><a href="' . esc_url(home_url('/industry-marina-rv-resorts/')) . '">Marina & RV Resorts</a></li>';
                            echo '<li><a href="' . esc_url(home_url('/industry-self-storage/')) . '">Self Storage</a></li>';
                            echo '<li><a href="' . esc_url(home_url('/industry-nonprofits/')) . '">Nonprofits</a></li>';
                            echo '<li><a href="' . esc_url(home_url('/industry-creative-services/')) . '">Creative Services</a></li>';
                            echo '</ul>';
                            echo '</li>';
                            echo '<li class="dropdown">';
                            echo '<a href="' . esc_url(home_url('/plugins/')) . '" class="dropdown-toggle">Plugins <i class="fas fa-chevron-down"></i></a>';
                            echo '<ul class="dropdown-menu">';
                            echo '<li><a href="' . esc_url(home_url('/plugins/')) . '">All Plugins</a></li>';
                            echo '<li><a href="' . esc_url(home_url('/shop/')) . '">Shop Pro Versions</a></li>';
                            echo '<li><a href="' . esc_url(home_url('/industries/')) . '">By Industry</a></li>';
                            echo '</ul>';
                            echo '</li>';
                            echo '<li><a href="' . esc_url(home_url('/shop/')) . '">Pricing</a></li>';
                            echo '<li><a href="' . esc_url(home_url('/about/')) . '">About</a></li>';
                            echo '<li><a href="' . esc_url(home_url('/contact/')) . '">Contact</a></li>';
                            echo '</ul>';
                        },
                    ));
                    ?>
                </nav>
                
                <!-- Header Actions -->
                <div class="header-actions">
                    <div class="cart-wrapper">
                        <?php if (function_exists('WC') && WC()->cart): ?>
                            <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="cart-link">
                                <i class="fas fa-shopping-cart"></i>
                                <span class="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                                <span class="cart-total"><?php echo WC()->cart->get_cart_total(); ?></span>
                            </a>
                        <?php endif; ?>
                    </div>
                    
                    <div class="account-wrapper">
                        <?php if (is_user_logged_in()): ?>
                            <a href="<?php echo esc_url(get_permalink(get_option('woocommerce_myaccount_page_id'))); ?>" class="account-link">
                                <i class="fas fa-user"></i>
                                <span>Account</span>
                            </a>
                        <?php else: ?>
                            <a href="<?php echo esc_url(get_permalink(get_option('woocommerce_myaccount_page_id'))); ?>" class="login-link">
                                <i class="fas fa-sign-in-alt"></i>
                                <span>Login</span>
                            </a>
                        <?php endif; ?>
                    </div>
                    
                    <a href="<?php echo esc_url(home_url('/support/')); ?>" class="support-link">
                        <i class="fas fa-life-ring"></i>
                        <span>Support</span>
                    </a>
                    
                    <a href="<?php echo esc_url(home_url('/shop/')); ?>" class="btn btn-primary">
                        <i class="fas fa-rocket"></i>
                        Get Started
                    </a>
                </div>
                
                <!-- Mobile Menu Toggle -->
                <button class="mobile-menu-toggle" aria-expanded="false" aria-label="Menu">
                    <span class="hamburger"></span>
                    <span class="hamburger"></span>
                    <span class="hamburger"></span>
                </button>
                
            </div>
        </div>
    </header>