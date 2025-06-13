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
                            echo '<li><a href="' . esc_url(home_url('/industries/property-management/')) . '">Property Management</a></li>';
                            echo '<li><a href="' . esc_url(home_url('/industries/sports-leagues/')) . '">Sports Leagues</a></li>';
                            echo '<li><a href="' . esc_url(home_url('/industries/fantasy-sports/')) . '">Fantasy Sports</a></li>';
                            echo '<li><a href="' . esc_url(home_url('/industries/gym-fitness/')) . '">Gym & Fitness</a></li>';
                            echo '<li><a href="' . esc_url(home_url('/industries/equipment-rental/')) . '">Equipment Rental</a></li>';
                            echo '<li><a href="' . esc_url(home_url('/industries/marina-rv-resorts/')) . '">Marina & RV Resorts</a></li>';
                            echo '<li><a href="' . esc_url(home_url('/industries/self-storage/')) . '">Self Storage</a></li>';
                            echo '<li><a href="' . esc_url(home_url('/industries/nonprofits/')) . '">Nonprofits</a></li>';
                            echo '</ul>';
                            echo '</li>';
                            echo '<li class="dropdown">';
                            echo '<a href="' . esc_url(home_url('/plugins/')) . '" class="dropdown-toggle">Plugins <i class="fas fa-chevron-down"></i></a>';
                            echo '<ul class="dropdown-menu">';
                            echo '<li><a href="' . esc_url(home_url('/plugins/')) . '">All Plugins</a></li>';
                            echo '<li><a href="' . esc_url(home_url('/plugin-directory/')) . '">Plugin Directory</a></li>';
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
                    <a href="<?php echo esc_url(home_url('/support/')); ?>" class="nav-link">Support</a>
                    <a href="<?php echo esc_url(home_url('/shop/')); ?>" class="btn btn-primary">Get Started</a>
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