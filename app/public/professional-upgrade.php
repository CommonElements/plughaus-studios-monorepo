<?php
/**
 * Professional WordPress Plugin Site Upgrade
 * Inspired by Gravity Forms, GravityKit, and GeoDirectory best practices
 * Run this by visiting: http://plughaus-studios-the-beginning-is-finished.local/professional-upgrade.php
 */

// Include WordPress
define('WP_USE_THEMES', false);
require_once dirname(__FILE__) . '/wp-load.php';

// Check if user is admin
if (!is_user_logged_in() || !current_user_can('manage_options')) {
    wp_redirect(wp_login_url($_SERVER['REQUEST_URI']));
    exit;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>PlugHaus Studios Professional Upgrade</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1000px; margin: 0 auto; padding: 20px; }
        h1, h2 { color: #2563eb; }
        .success { color: green; font-weight: bold; }
        .info { color: #0066cc; font-style: italic; }
        .btn { background: #2563eb; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; margin: 10px 5px 10px 0; }
        .section { background: #f8f9fa; padding: 20px; margin: 20px 0; border-radius: 8px; }
    </style>
</head>
<body>
    <h1>PlugHaus Studios Professional Upgrade</h1>
    <p class="info">Incorporating best practices from leading WordPress plugin sites...</p>
    
    <?php
    $template_dir = get_template_directory();
    
    // 1. Create professional header template
    echo '<div class="section">';
    echo '<h2>1. Creating Professional Header</h2>';
    
    $header_file = $template_dir . '/header.php';
    $professional_header = '<?php
/**
 * Professional Header Template
 * Inspired by leading WordPress plugin sites
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo(\'charset\'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
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
                    <i class="fas fa-gift"></i>
                    <strong>Limited Time:</strong> Get 30% off all Pro plugins - Use code LAUNCH30
                </span>
                <button class="announcement-close" aria-label="Close">×</button>
            </div>
        </div>
    </div>
    
    <!-- Main Header -->
    <header id="masthead" class="site-header">
        <div class="container">
            <nav class="main-navigation">
                
                <!-- Logo/Brand -->
                <div class="site-branding">
                    <a href="<?php echo esc_url(home_url(\'/\')); ?>" class="site-logo" rel="home">
                        <div class="logo-icon">
                            <i class="fas fa-plug"></i>
                        </div>
                        <div class="logo-text">
                            <span class="logo-name"><?php bloginfo(\'name\'); ?></span>
                            <span class="logo-tagline">WordPress Plugins</span>
                        </div>
                    </a>
                </div>
                
                <!-- Navigation Menu -->
                <div class="nav-menu-container">
                    <?php
                    wp_nav_menu(array(
                        \'theme_location\' => \'primary\',
                        \'menu_class\'     => \'nav-menu\',
                        \'container\'      => false,
                        \'fallback_cb\'    => \'plughaus_professional_menu\',
                    ));
                    ?>
                </div>
                
                <!-- Header Actions -->
                <div class="header-actions">
                    <a href="/pricing/" class="nav-link">Pricing</a>
                    <a href="/support/" class="nav-link">Support</a>
                    <a href="/shop/" class="btn btn-primary">
                        <i class="fas fa-download"></i>
                        Get Started
                    </a>
                </div>
                
                <!-- Mobile Menu Toggle -->
                <button class="mobile-menu-toggle" aria-expanded="false">
                    <span class="hamburger"></span>
                    <span class="hamburger"></span>
                    <span class="hamburger"></span>
                </button>
                
            </nav>
        </div>
    </header>

<?php
// Professional menu fallback
function plughaus_professional_menu() {
    echo \'<ul class="nav-menu">\';
    echo \'<li class="menu-item dropdown">
            <a href="#" class="nav-link">Plugins <i class="fas fa-chevron-down"></i></a>
            <ul class="dropdown-menu">
                <li><a href="/plugins/">All Plugins</a></li>
                <li><a href="/plugins/?filter=property-management">Property Management</a></li>
                <li><a href="/plugins/?filter=payment-gateways">Payment Gateways</a></li>
                <li><a href="/plugins/?filter=business-tools">Business Tools</a></li>
            </ul>
          </li>\';
    echo \'<li class="menu-item"><a href="/features/" class="nav-link">Features</a></li>\';
    echo \'<li class="menu-item"><a href="/add-ons/" class="nav-link">Add-Ons</a></li>\';
    echo \'<li class="menu-item"><a href="/blog/" class="nav-link">Blog</a></li>\';
    echo \'<li class="menu-item"><a href="/about/" class="nav-link">About</a></li>\';
    echo \'</ul>\';
}
?>';

    file_put_contents($header_file, $professional_header);
    echo '<p class="success">✓ Created professional header with navigation dropdowns</p>';
    
    // 2. Create professional homepage
    echo '<h2>2. Creating Professional Homepage</h2>';
    
    $homepage_file = $template_dir . '/page-home.php';
    $professional_homepage = '<?php
/**
 * Professional Homepage Template
 * Inspired by top WordPress plugin sites
 */

get_header(); ?>

<main id="primary" class="site-main">
    
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-background">
            <div class="hero-pattern"></div>
        </div>
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <div class="hero-badge">
                        <i class="fas fa-star"></i>
                        Trusted by 10,000+ WordPress Sites
                    </div>
                    <h1 class="hero-title">
                        Powerful WordPress Plugins<br>
                        <span class="text-gradient">Built for Modern Business</span>
                    </h1>
                    <p class="hero-description">
                        Transform your WordPress site into a powerful business platform. 
                        Our plugins help you manage properties, process payments, automate workflows, 
                        and scale your operations with confidence.
                    </p>
                    <div class="hero-actions">
                        <a href="/shop/" class="btn btn-primary btn-lg">
                            <i class="fas fa-rocket"></i>
                            Get Started Free
                        </a>
                        <a href="/demo/" class="btn btn-outline btn-lg">
                            <i class="fas fa-play"></i>
                            View Demo
                        </a>
                    </div>
                    <div class="hero-trust">
                        <span class="trust-text">Trusted by leading companies:</span>
                        <div class="trust-logos">
                            <div class="trust-logo">Company A</div>
                            <div class="trust-logo">Company B</div>
                            <div class="trust-logo">Company C</div>
                            <div class="trust-logo">Company D</div>
                        </div>
                    </div>
                </div>
                <div class="hero-visual">
                    <div class="hero-demo">
                        <div class="demo-window">
                            <div class="demo-header">
                                <div class="demo-dots">
                                    <span></span><span></span><span></span>
                                </div>
                                <div class="demo-title">PlugHaus Dashboard</div>
                            </div>
                            <div class="demo-content">
                                <div class="demo-stats">
                                    <div class="stat-card">
                                        <div class="stat-number">247</div>
                                        <div class="stat-label">Properties</div>
                                    </div>
                                    <div class="stat-card">
                                        <div class="stat-number">$84k</div>
                                        <div class="stat-label">Revenue</div>
                                    </div>
                                    <div class="stat-card">
                                        <div class="stat-number">98%</div>
                                        <div class="stat-label">Occupancy</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Features Overview -->
    <section class="features-overview">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Everything You Need to Succeed</h2>
                <p class="section-description">
                    Comprehensive WordPress plugins designed to streamline your business operations
                </p>
            </div>
            
            <div class="features-grid">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3 class="feature-title">Lightning Fast</h3>
                    <p class="feature-description">
                        Optimized for performance with efficient code and smart caching
                    </p>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="feature-title">Enterprise Security</h3>
                    <p class="feature-description">
                        Bank-grade security with regular audits and WordPress standards compliance
                    </p>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3 class="feature-title">Mobile Ready</h3>
                    <p class="feature-description">
                        Responsive design that works perfectly on all devices and screen sizes
                    </p>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-puzzle-piece"></i>
                    </div>
                    <h3 class="feature-title">Easy Integration</h3>
                    <p class="feature-description">
                        Seamlessly integrates with popular WordPress plugins and themes
                    </p>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3 class="feature-title">24/7 Support</h3>
                    <p class="feature-description">
                        Get help when you need it with our dedicated support team
                    </p>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-sync-alt"></i>
                    </div>
                    <h3 class="feature-title">Automatic Updates</h3>
                    <p class="feature-description">
                        Stay current with automatic updates and new feature releases
                    </p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Plugin Showcase -->
    <section class="plugin-showcase">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Our WordPress Plugins</h2>
                <p class="section-description">
                    Professional solutions for every business need
                </p>
            </div>
            
            <div class="plugin-grid">
                <?php
                $featured_plugins = get_posts(array(
                    \'post_type\' => \'phstudios_plugin\',
                    \'posts_per_page\' => 4,
                    \'post_status\' => \'publish\'
                ));
                
                $plugin_icons = [\'home\', \'credit-card\', \'file-alt\', \'chart-line\'];
                
                foreach ($featured_plugins as $index => $plugin) :
                    setup_postdata($plugin);
                    $icon = $plugin_icons[$index % count($plugin_icons)];
                    $status = get_post_meta($plugin->ID, \'_plugin_status\', true) ?: \'available\';
                    $downloads = get_post_meta($plugin->ID, \'_download_count\', true) ?: \'1000\';
                    $rating = get_post_meta($plugin->ID, \'_rating\', true) ?: \'4.8\';
                ?>
                    <div class="plugin-card">
                        <div class="plugin-header">
                            <div class="plugin-icon">
                                <i class="fas fa-<?php echo $icon; ?>"></i>
                            </div>
                            <div class="plugin-status status-<?php echo $status; ?>">
                                <?php echo ucwords(str_replace(\'-\', \' \', $status)); ?>
                            </div>
                        </div>
                        <div class="plugin-content">
                            <h3 class="plugin-title"><?php the_title(); ?></h3>
                            <p class="plugin-excerpt"><?php echo esc_html(get_the_excerpt()); ?></p>
                            <div class="plugin-stats">
                                <div class="stat">
                                    <i class="fas fa-download"></i>
                                    <?php echo number_format($downloads); ?>+
                                </div>
                                <div class="stat">
                                    <i class="fas fa-star"></i>
                                    <?php echo $rating; ?>/5
                                </div>
                            </div>
                        </div>
                        <div class="plugin-actions">
                            <a href="<?php the_permalink(); ?>" class="btn btn-outline">Learn More</a>
                            <a href="/shop/" class="btn btn-primary">Get Plugin</a>
                        </div>
                    </div>
                <?php endforeach; wp_reset_postdata(); ?>
            </div>
        </div>
    </section>
    
    <!-- Social Proof -->
    <section class="social-proof">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Loved by WordPress Professionals</h2>
            </div>
            
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"PlugHaus Property Management has transformed how we handle our rental portfolio. The automation features alone save us 20+ hours per week."</p>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="author-info">
                            <div class="author-name">Sarah Johnson</div>
                            <div class="author-title">Property Manager, Johnson Real Estate</div>
                        </div>
                    </div>
                    <div class="testimonial-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"The payment processing integration is seamless. Our clients love the smooth checkout experience and we love the detailed analytics."</p>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="author-info">
                            <div class="author-name">Mike Chen</div>
                            <div class="author-title">CEO, Tech Solutions LLC</div>
                        </div>
                    </div>
                    <div class="testimonial-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"Outstanding support team and rock-solid plugins. We\'ve built our entire business platform on PlugHaus solutions."</p>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="author-info">
                            <div class="author-name">Emily Rodriguez</div>
                            <div class="author-title">CTO, Digital Agency Co</div>
                        </div>
                    </div>
                    <div class="testimonial-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
            </div>
            
            <div class="social-proof-stats">
                <div class="proof-stat">
                    <div class="stat-number">10,000+</div>
                    <div class="stat-label">Active Sites</div>
                </div>
                <div class="proof-stat">
                    <div class="stat-number">99.9%</div>
                    <div class="stat-label">Uptime</div>
                </div>
                <div class="proof-stat">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Support</div>
                </div>
                <div class="proof-stat">
                    <div class="stat-number">4.9/5</div>
                    <div class="stat-label">Rating</div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">Ready to Transform Your WordPress Site?</h2>
                <p class="cta-description">
                    Join thousands of satisfied customers who trust PlugHaus Studios for their business needs.
                </p>
                <div class="cta-actions">
                    <a href="/shop/" class="btn btn-primary btn-xl">
                        <i class="fas fa-rocket"></i>
                        Start Free Today
                    </a>
                    <a href="/contact/" class="btn btn-outline btn-xl">
                        <i class="fas fa-comments"></i>
                        Talk to Sales
                    </a>
                </div>
                <div class="cta-guarantee">
                    <i class="fas fa-shield-alt"></i>
                    30-day money-back guarantee
                </div>
            </div>
        </div>
    </section>
    
</main>

<?php get_footer(); ?>';

    file_put_contents($homepage_file, $professional_homepage);
    echo '<p class="success">✓ Created professional homepage with industry-standard sections</p>';
    
    // 3. Create professional CSS
    echo '<h2>3. Creating Professional Stylesheet</h2>';
    
    $professional_css = $template_dir . '/assets/css/professional.css';
    $css_content = '/* Professional WordPress Plugin Site Styles */
/* Inspired by Gravity Forms, GravityKit, and GeoDirectory */

/* Variables */
:root {
    --primary-color: #1e40af;
    --primary-light: #3b82f6;
    --primary-dark: #1e3a8a;
    --secondary-color: #f59e0b;
    --success-color: #10b981;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-400: #9ca3af;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-800: #1f2937;
    --gray-900: #111827;
    --font-sans: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
}

/* Reset */
* {
    box-sizing: border-box;
}

body {
    font-family: var(--font-sans);
    line-height: 1.6;
    color: var(--gray-900);
    margin: 0;
    padding: 0;
    background: #ffffff;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1.5rem;
}

/* Announcement Bar */
.announcement-bar {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
    color: white;
    padding: 0.75rem 0;
    font-size: 0.875rem;
    position: relative;
}

.announcement-content {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
}

.announcement-close {
    background: none;
    border: none;
    color: white;
    font-size: 1.25rem;
    cursor: pointer;
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
}

/* Header */
.site-header {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid var(--gray-200);
    position: sticky;
    top: 0;
    z-index: 1000;
    padding: 1rem 0;
}

.main-navigation {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.site-logo {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    text-decoration: none;
    color: var(--gray-900);
}

.logo-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
}

.logo-name {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--gray-900);
    display: block;
}

.logo-tagline {
    font-size: 0.75rem;
    color: var(--gray-500);
    display: block;
    line-height: 1;
}

/* Navigation */
.nav-menu {
    display: flex;
    align-items: center;
    gap: 2rem;
    list-style: none;
    margin: 0;
    padding: 0;
}

.nav-link {
    color: var(--gray-700);
    text-decoration: none;
    font-weight: 500;
    font-size: 0.95rem;
    position: relative;
    transition: color 0.2s;
}

.nav-link:hover {
    color: var(--primary-color);
}

.dropdown {
    position: relative;
}

.dropdown-menu {
    position: absolute;
    top: 100%;
    left: 0;
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: 8px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    padding: 0.5rem 0;
    min-width: 200px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.2s;
    z-index: 1000;
}

.dropdown:hover .dropdown-menu {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.dropdown-menu li {
    padding: 0;
}

.dropdown-menu a {
    display: block;
    padding: 0.75rem 1rem;
    color: var(--gray-700);
    text-decoration: none;
    font-size: 0.875rem;
    transition: background-color 0.2s;
}

.dropdown-menu a:hover {
    background: var(--gray-50);
    color: var(--primary-color);
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.mobile-menu-toggle {
    display: none;
    flex-direction: column;
    gap: 4px;
    background: none;
    border: none;
    cursor: pointer;
}

.hamburger {
    width: 25px;
    height: 2px;
    background: var(--gray-700);
    transition: all 0.3s;
}

/* Hero Section */
.hero-section {
    position: relative;
    padding: 6rem 0 8rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    overflow: hidden;
}

.hero-background {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    opacity: 0.1;
}

.hero-pattern {
    width: 100%;
    height: 100%;
    background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.3) 1px, transparent 0);
    background-size: 20px 20px;
}

.hero-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
    align-items: center;
    position: relative;
    z-index: 1;
}

.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255, 255, 255, 0.2);
    padding: 0.5rem 1rem;
    border-radius: 50px;
    font-size: 0.875rem;
    font-weight: 500;
    margin-bottom: 1.5rem;
    backdrop-filter: blur(10px);
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 800;
    line-height: 1.1;
    margin-bottom: 1.5rem;
}

.text-gradient {
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.hero-description {
    font-size: 1.25rem;
    opacity: 0.9;
    margin-bottom: 2rem;
    line-height: 1.6;
}

.hero-actions {
    display: flex;
    gap: 1rem;
    margin-bottom: 3rem;
}

.hero-trust {
    opacity: 0.8;
}

.trust-text {
    display: block;
    font-size: 0.875rem;
    margin-bottom: 1rem;
}

.trust-logos {
    display: flex;
    gap: 2rem;
    opacity: 0.6;
}

.trust-logo {
    padding: 0.5rem 1rem;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 4px;
    font-size: 0.875rem;
}

/* Hero Demo */
.hero-demo {
    perspective: 1000px;
}

.demo-window {
    background: white;
    border-radius: 12px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transform: rotateY(-5deg) rotateX(5deg);
    transition: transform 0.3s;
}

.demo-window:hover {
    transform: rotateY(0deg) rotateX(0deg);
}

.demo-header {
    background: var(--gray-100);
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    border-bottom: 1px solid var(--gray-200);
}

.demo-dots {
    display: flex;
    gap: 0.5rem;
}

.demo-dots span {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: var(--gray-300);
}

.demo-dots span:nth-child(1) { background: #ff5f56; }
.demo-dots span:nth-child(2) { background: #ffbd2e; }
.demo-dots span:nth-child(3) { background: #27ca3f; }

.demo-title {
    font-weight: 600;
    color: var(--gray-700);
    font-size: 0.875rem;
}

.demo-content {
    padding: 2rem;
    background: white;
}

.demo-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}

.stat-card {
    text-align: center;
    padding: 1.5rem 1rem;
    background: var(--gray-50);
    border-radius: 8px;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 0.875rem;
    color: var(--gray-600);
}

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    font-size: 0.95rem;
    font-weight: 600;
    text-decoration: none;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
    position: relative;
    overflow: hidden;
}

.btn-primary {
    background: var(--primary-color);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(30, 64, 175, 0.4);
}

.btn-outline {
    background: transparent;
    color: var(--primary-color);
    border: 2px solid var(--primary-color);
}

.btn-outline:hover {
    background: var(--primary-color);
    color: white;
}

.btn-lg {
    padding: 1rem 2rem;
    font-size: 1rem;
}

.btn-xl {
    padding: 1.25rem 2.5rem;
    font-size: 1.125rem;
}

/* Sections */
.features-overview,
.plugin-showcase,
.social-proof,
.cta-section {
    padding: 6rem 0;
}

.features-overview {
    background: var(--gray-50);
}

.section-header {
    text-align: center;
    margin-bottom: 4rem;
}

.section-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
    color: var(--gray-900);
}

.section-description {
    font-size: 1.25rem;
    color: var(--gray-600);
    max-width: 600px;
    margin: 0 auto;
}

/* Features Grid */
.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}

.feature-item {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    transition: all 0.3s;
}

.feature-item:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

.feature-icon {
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    margin: 0 auto 1.5rem;
}

.feature-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: var(--gray-900);
}

.feature-description {
    color: var(--gray-600);
    line-height: 1.6;
}

/* Plugin Grid */
.plugin-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}

.plugin-card {
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: 12px;
    padding: 1.5rem;
    transition: all 0.3s;
    display: flex;
    flex-direction: column;
}

.plugin-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    border-color: var(--primary-color);
}

.plugin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.plugin-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
}

.plugin-status {
    padding: 0.25rem 0.75rem;
    font-size: 0.75rem;
    font-weight: 600;
    border-radius: 50px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.status-available {
    background: #d1fae5;
    color: #065f46;
}

.status-coming-soon {
    background: #fef3c7;
    color: #92400e;
}

.plugin-content {
    flex: 1;
    margin-bottom: 1.5rem;
}

.plugin-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.75rem;
    color: var(--gray-900);
}

.plugin-excerpt {
    color: var(--gray-600);
    line-height: 1.6;
    margin-bottom: 1rem;
}

.plugin-stats {
    display: flex;
    gap: 1rem;
    font-size: 0.875rem;
    color: var(--gray-500);
}

.plugin-stats .stat {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.plugin-stats i {
    color: var(--primary-color);
}

.plugin-actions {
    display: flex;
    gap: 0.75rem;
}

.plugin-actions .btn {
    flex: 1;
    text-align: center;
    justify-content: center;
}

/* Testimonials */
.testimonials-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
    margin-bottom: 4rem;
}

.testimonial-card {
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: 12px;
    padding: 2rem;
    transition: all 0.3s;
}

.testimonial-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.testimonial-content {
    margin-bottom: 1.5rem;
}

.testimonial-content p {
    font-size: 1.125rem;
    line-height: 1.6;
    color: var(--gray-700);
    font-style: italic;
    margin: 0;
}

.testimonial-author {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.author-avatar {
    width: 48px;
    height: 48px;
    background: var(--gray-200);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gray-500);
}

.author-name {
    font-weight: 600;
    color: var(--gray-900);
}

.author-title {
    font-size: 0.875rem;
    color: var(--gray-600);
}

.testimonial-rating {
    color: var(--secondary-color);
}

/* Social Proof Stats */
.social-proof-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 2rem;
    text-align: center;
}

.proof-stat {
    padding: 1.5rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.proof-stat .stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 0.5rem;
}

.proof-stat .stat-label {
    color: var(--gray-600);
    font-weight: 500;
}

/* CTA Section */
.cta-section {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
    color: white;
}

.cta-content {
    text-align: center;
    max-width: 600px;
    margin: 0 auto;
}

.cta-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.cta-description {
    font-size: 1.25rem;
    opacity: 0.9;
    margin-bottom: 2rem;
}

.cta-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-bottom: 2rem;
}

.cta-actions .btn-primary {
    background: white;
    color: var(--primary-color);
}

.cta-actions .btn-primary:hover {
    background: var(--gray-100);
}

.cta-actions .btn-outline {
    border-color: white;
    color: white;
}

.cta-actions .btn-outline:hover {
    background: white;
    color: var(--primary-color);
}

.cta-guarantee {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    opacity: 0.8;
}

/* Responsive Design */
@media (max-width: 768px) {
    .mobile-menu-toggle {
        display: flex;
    }
    
    .nav-menu,
    .header-actions {
        display: none;
    }
    
    .hero-content {
        grid-template-columns: 1fr;
        gap: 2rem;
        text-align: center;
    }
    
    .hero-title {
        font-size: 2.5rem;
    }
    
    .hero-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .features-grid,
    .plugin-grid,
    .testimonials-grid {
        grid-template-columns: 1fr;
    }
    
    .social-proof-stats {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .cta-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .section-title {
        font-size: 2rem;
    }
    
    .container {
        padding: 0 1rem;
    }
}

@media (max-width: 480px) {
    .social-proof-stats {
        grid-template-columns: 1fr;
    }
    
    .demo-stats {
        grid-template-columns: 1fr;
    }
    
    .hero-title {
        font-size: 2rem;
    }
    
    .section-title {
        font-size: 1.75rem;
    }
}';

    file_put_contents($professional_css, $css_content);
    echo '<p class="success">✓ Created professional stylesheet inspired by industry leaders</p>';
    
    // 4. Update functions.php to include professional CSS
    echo '<h2>4. Adding Professional CSS to Theme</h2>';
    
    $functions_file = $template_dir . '/functions.php';
    $functions_content = file_get_contents($functions_file);
    
    if (strpos($functions_content, 'plughaus-studios-professional') === false) {
        $css_enqueue = "\n\n// Enqueue professional design\nwp_enqueue_style('plughaus-studios-professional', get_template_directory_uri() . '/assets/css/professional.css', array('plughaus-studios-style'), '1.0.0');";
        
        $functions_content = str_replace(
            "wp_enqueue_style('plughaus-studios-style', get_stylesheet_uri(), array(), '1.0.0');",
            "wp_enqueue_style('plughaus-studios-style', get_stylesheet_uri(), array(), '1.0.0');" . $css_enqueue,
            $functions_content
        );
        
        file_put_contents($functions_file, $functions_content);
        echo '<p class="success">✓ Added professional CSS to theme functions</p>';
    }
    
    echo '</div>';
    
    echo '<div class="section">';
    echo '<h2>🌟 Professional Upgrade Complete!</h2>';
    echo '<p class="success" style="font-size: 18px;">✓ Your site now matches industry-leading WordPress plugin sites!</p>';
    
    echo '<h3>Professional Features Added:</h3>';
    echo '<ul>';
    echo '<li>✅ Announcement bar with promotional messaging</li>';
    echo '<li>✅ Professional navigation with dropdowns</li>';
    echo '<li>✅ Industry-standard hero section with demo visual</li>';
    echo '<li>✅ Trust signals and social proof sections</li>';
    echo '<li>✅ Professional testimonials with ratings</li>';
    echo '<li>✅ Feature showcase inspired by top sites</li>';
    echo '<li>✅ Call-to-action with guarantee messaging</li>';
    echo '<li>✅ Modern responsive design</li>';
    echo '</ul>';
    
    echo '<h3>Design Inspiration Sources:</h3>';
    echo '<ul>';
    echo '<li>📊 <strong>Gravity Forms</strong> - Professional layout, trust signals, clean design</li>';
    echo '<li>🎨 <strong>GravityKit</strong> - Color scheme, button styles, responsive grid</li>';
    echo '<li>🌟 <strong>GeoDirectory</strong> - Feature presentation, testimonials, CTA design</li>';
    echo '</ul>';
    
    echo '<a href="' . home_url() . '" class="btn" style="background: #28a745; font-size: 1.2em; padding: 15px 30px;">🚀 View Professional Site</a>';
    echo '</div>';
    ?>
    
    <script>
    // Add announcement bar close functionality
    document.addEventListener('DOMContentLoaded', function() {
        const closeBtn = document.querySelector('.announcement-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                document.querySelector('.announcement-bar').style.display = 'none';
            });
        }
    });
    </script>
    
</body>
</html>