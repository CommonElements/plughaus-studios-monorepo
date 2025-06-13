<?php
/**
 * PlugHaus Studios Design Upgrade
 * Modern UI/UX improvements with advanced components
 * Run this by visiting: http://plughaus-studios-the-beginning-is-finished.local/upgrade-design.php
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
    <title>PlugHaus Studios Design Upgrade</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1000px; margin: 0 auto; padding: 20px; }
        h1, h2 { color: #2563eb; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { color: #0066cc; font-style: italic; }
        .btn { background: #2563eb; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; margin: 10px 5px 10px 0; }
        .section { background: #f8f9fa; padding: 20px; margin: 20px 0; border-radius: 8px; }
    </style>
</head>
<body>
    <h1>PlugHaus Studios Design Upgrade</h1>
    
    <?php
    $template_dir = get_template_directory();
    
    // 1. Create modern CSS framework
    echo '<div class="section">';
    echo '<h2>1. Creating Modern CSS Framework</h2>';
    
    $modern_css = $template_dir . '/assets/css/modern.css';
    $css_dir = dirname($modern_css);
    
    if (!is_dir($css_dir)) {
        mkdir($css_dir, 0755, true);
    }
    
    $modern_css_content = '/* PlugHaus Studios Modern Design System */

:root {
    /* Color Palette */
    --primary-50: #eff6ff;
    --primary-100: #dbeafe;
    --primary-200: #bfdbfe;
    --primary-300: #93c5fd;
    --primary-400: #60a5fa;
    --primary-500: #3b82f6;
    --primary-600: #2563eb;
    --primary-700: #1d4ed8;
    --primary-800: #1e40af;
    --primary-900: #1e3a8a;
    
    --secondary-50: #f0f9ff;
    --secondary-100: #e0f2fe;
    --secondary-200: #bae6fd;
    --secondary-300: #7dd3fc;
    --secondary-400: #38bdf8;
    --secondary-500: #0ea5e9;
    --secondary-600: #0284c7;
    --secondary-700: #0369a1;
    --secondary-800: #075985;
    --secondary-900: #0c4a6e;
    
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
    
    --success-500: #10b981;
    --warning-500: #f59e0b;
    --error-500: #ef4444;
    
    /* Spacing Scale */
    --space-1: 0.25rem;
    --space-2: 0.5rem;
    --space-3: 0.75rem;
    --space-4: 1rem;
    --space-5: 1.25rem;
    --space-6: 1.5rem;
    --space-8: 2rem;
    --space-10: 2.5rem;
    --space-12: 3rem;
    --space-16: 4rem;
    --space-20: 5rem;
    --space-24: 6rem;
    
    /* Typography */
    --font-sans: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    --font-mono: "JetBrains Mono", "Fira Code", monospace;
    
    --text-xs: 0.75rem;
    --text-sm: 0.875rem;
    --text-base: 1rem;
    --text-lg: 1.125rem;
    --text-xl: 1.25rem;
    --text-2xl: 1.5rem;
    --text-3xl: 1.875rem;
    --text-4xl: 2.25rem;
    --text-5xl: 3rem;
    --text-6xl: 3.75rem;
    --text-7xl: 4.5rem;
    
    /* Border Radius */
    --rounded-sm: 0.125rem;
    --rounded: 0.25rem;
    --rounded-md: 0.375rem;
    --rounded-lg: 0.5rem;
    --rounded-xl: 0.75rem;
    --rounded-2xl: 1rem;
    --rounded-3xl: 1.5rem;
    --rounded-full: 9999px;
    
    /* Shadows */
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
    --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
    --shadow-2xl: 0 25px 50px -12px rgb(0 0 0 / 0.25);
    
    /* Transitions */
    --transition-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1);
    --transition-base: 300ms cubic-bezier(0.4, 0, 0.2, 1);
    --transition-slow: 500ms cubic-bezier(0.4, 0, 0.2, 1);
}

/* Reset and Base Styles */
* {
    box-sizing: border-box;
}

body {
    font-family: var(--font-sans);
    line-height: 1.6;
    color: var(--gray-900);
    background: var(--gray-50);
    margin: 0;
    padding: 0;
}

/* Modern Container System */
.container {
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 var(--space-6);
}

.container-wide {
    max-width: 1400px;
}

.container-narrow {
    max-width: 800px;
}

/* Modern Grid System */
.grid {
    display: grid;
    gap: var(--space-6);
}

.grid-cols-1 { grid-template-columns: repeat(1, 1fr); }
.grid-cols-2 { grid-template-columns: repeat(2, 1fr); }
.grid-cols-3 { grid-template-columns: repeat(3, 1fr); }
.grid-cols-4 { grid-template-columns: repeat(4, 1fr); }

.grid-auto-fit {
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
}

.grid-auto-fill {
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
}

/* Flexbox Utilities */
.flex { display: flex; }
.flex-col { flex-direction: column; }
.flex-wrap { flex-wrap: wrap; }
.items-center { align-items: center; }
.items-start { align-items: flex-start; }
.items-end { align-items: flex-end; }
.justify-center { justify-content: center; }
.justify-between { justify-content: space-between; }
.justify-start { justify-content: flex-start; }
.justify-end { justify-content: flex-end; }
.gap-1 { gap: var(--space-1); }
.gap-2 { gap: var(--space-2); }
.gap-3 { gap: var(--space-3); }
.gap-4 { gap: var(--space-4); }
.gap-6 { gap: var(--space-6); }
.gap-8 { gap: var(--space-8); }

/* Modern Typography */
h1, h2, h3, h4, h5, h6 {
    font-weight: 700;
    line-height: 1.2;
    margin: 0 0 var(--space-4) 0;
    color: var(--gray-900);
}

h1 { font-size: var(--text-5xl); }
h2 { font-size: var(--text-4xl); }
h3 { font-size: var(--text-3xl); }
h4 { font-size: var(--text-2xl); }
h5 { font-size: var(--text-xl); }
h6 { font-size: var(--text-lg); }

.text-xs { font-size: var(--text-xs); }
.text-sm { font-size: var(--text-sm); }
.text-base { font-size: var(--text-base); }
.text-lg { font-size: var(--text-lg); }
.text-xl { font-size: var(--text-xl); }
.text-2xl { font-size: var(--text-2xl); }
.text-3xl { font-size: var(--text-3xl); }
.text-4xl { font-size: var(--text-4xl); }
.text-5xl { font-size: var(--text-5xl); }
.text-6xl { font-size: var(--text-6xl); }
.text-7xl { font-size: var(--text-7xl); }

.font-light { font-weight: 300; }
.font-normal { font-weight: 400; }
.font-medium { font-weight: 500; }
.font-semibold { font-weight: 600; }
.font-bold { font-weight: 700; }

.text-center { text-align: center; }
.text-left { text-align: left; }
.text-right { text-align: right; }

/* Color Utilities */
.text-gray-500 { color: var(--gray-500); }
.text-gray-600 { color: var(--gray-600); }
.text-gray-700 { color: var(--gray-700); }
.text-gray-900 { color: var(--gray-900); }
.text-primary-600 { color: var(--primary-600); }
.text-primary-700 { color: var(--primary-700); }

/* Modern Card Components */
.card {
    background: white;
    border-radius: var(--rounded-xl);
    box-shadow: var(--shadow);
    overflow: hidden;
    transition: all var(--transition-base);
}

.card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-2px);
}

.card-header {
    padding: var(--space-6);
    border-bottom: 1px solid var(--gray-200);
}

.card-body {
    padding: var(--space-6);
}

.card-footer {
    padding: var(--space-6);
    border-top: 1px solid var(--gray-200);
    background: var(--gray-50);
}

/* Modern Button System */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: var(--space-2);
    padding: var(--space-3) var(--space-6);
    font-size: var(--text-base);
    font-weight: 600;
    text-decoration: none;
    border: none;
    border-radius: var(--rounded-lg);
    cursor: pointer;
    transition: all var(--transition-fast);
    position: relative;
    overflow: hidden;
}

.btn::before {
    content: "";
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left var(--transition-base);
}

.btn:hover::before {
    left: 100%;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-600), var(--primary-700));
    color: white;
}

.btn-primary:hover {
    background: linear-gradient(135deg, var(--primary-700), var(--primary-800));
    transform: translateY(-1px);
    box-shadow: var(--shadow-lg);
}

.btn-secondary {
    background: linear-gradient(135deg, var(--secondary-600), var(--secondary-700));
    color: white;
}

.btn-outline {
    background: transparent;
    color: var(--primary-600);
    border: 2px solid var(--primary-600);
}

.btn-outline:hover {
    background: var(--primary-600);
    color: white;
}

.btn-ghost {
    background: transparent;
    color: var(--gray-700);
}

.btn-ghost:hover {
    background: var(--gray-100);
}

.btn-sm {
    padding: var(--space-2) var(--space-4);
    font-size: var(--text-sm);
}

.btn-lg {
    padding: var(--space-4) var(--space-8);
    font-size: var(--text-lg);
}

.btn-xl {
    padding: var(--space-5) var(--space-10);
    font-size: var(--text-xl);
}

/* Modern Badge System */
.badge {
    display: inline-flex;
    align-items: center;
    gap: var(--space-1);
    padding: var(--space-1) var(--space-3);
    font-size: var(--text-xs);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-radius: var(--rounded-full);
}

.badge-primary {
    background: var(--primary-100);
    color: var(--primary-800);
}

.badge-success {
    background: #d1fae5;
    color: #065f46;
}

.badge-warning {
    background: #fef3c7;
    color: #92400e;
}

.badge-error {
    background: #fee2e2;
    color: #991b1b;
}

/* Modern Form Elements */
.form-group {
    margin-bottom: var(--space-6);
}

.form-label {
    display: block;
    font-size: var(--text-sm);
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: var(--space-2);
}

.form-input,
.form-select,
.form-textarea {
    width: 100%;
    padding: var(--space-3) var(--space-4);
    font-size: var(--text-base);
    border: 2px solid var(--gray-300);
    border-radius: var(--rounded-lg);
    background: white;
    transition: all var(--transition-fast);
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
    outline: none;
    border-color: var(--primary-500);
    box-shadow: 0 0 0 3px var(--primary-100);
}

/* Hero Section Component */
.hero {
    position: relative;
    padding: var(--space-24) 0;
    background: linear-gradient(135deg, var(--primary-900) 0%, var(--primary-700) 50%, var(--secondary-600) 100%);
    color: white;
    overflow: hidden;
}

.hero::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url("data:image/svg+xml,<svg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"><g fill=\"none\" fill-rule=\"evenodd\"><g fill=\"%23ffffff\" fill-opacity=\"0.05\"><circle cx=\"60\" cy=\"12\" r=\"4\"/><circle cx=\"12\" cy=\"48\" r=\"4\"/></g></svg>") repeat;
    pointer-events: none;
}

.hero-content {
    position: relative;
    z-index: 1;
}

.hero-title {
    font-size: var(--text-6xl);
    font-weight: 800;
    line-height: 1.1;
    margin-bottom: var(--space-6);
    background: linear-gradient(135deg, #ffffff 0%, rgba(255,255,255,0.8) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.hero-subtitle {
    font-size: var(--text-xl);
    font-weight: 400;
    opacity: 0.9;
    margin-bottom: var(--space-8);
    max-width: 600px;
}

/* Feature Grid Component */
.feature-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: var(--space-8);
    margin: var(--space-16) 0;
}

.feature-card {
    position: relative;
    padding: var(--space-8);
    background: white;
    border-radius: var(--rounded-2xl);
    box-shadow: var(--shadow);
    border: 1px solid var(--gray-200);
    transition: all var(--transition-base);
}

.feature-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-xl);
    border-color: var(--primary-300);
}

.feature-icon {
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, var(--primary-500), var(--primary-600));
    border-radius: var(--rounded-xl);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: var(--text-2xl);
    color: white;
    margin-bottom: var(--space-6);
}

.feature-title {
    font-size: var(--text-xl);
    font-weight: 700;
    margin-bottom: var(--space-3);
    color: var(--gray-900);
}

.feature-description {
    color: var(--gray-600);
    line-height: 1.6;
}

/* Pricing Component */
.pricing-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: var(--space-8);
    margin: var(--space-16) 0;
}

.pricing-card {
    position: relative;
    background: white;
    border: 2px solid var(--gray-200);
    border-radius: var(--rounded-2xl);
    padding: var(--space-8);
    text-align: center;
    transition: all var(--transition-base);
}

.pricing-card.featured {
    border-color: var(--primary-500);
    transform: scale(1.05);
    box-shadow: var(--shadow-xl);
}

.pricing-card.featured::before {
    content: "Most Popular";
    position: absolute;
    top: -12px;
    left: 50%;
    transform: translateX(-50%);
    background: linear-gradient(135deg, var(--primary-500), var(--primary-600));
    color: white;
    padding: var(--space-2) var(--space-6);
    border-radius: var(--rounded-full);
    font-size: var(--text-sm);
    font-weight: 600;
}

.pricing-title {
    font-size: var(--text-2xl);
    font-weight: 700;
    margin-bottom: var(--space-4);
}

.pricing-price {
    font-size: var(--text-5xl);
    font-weight: 800;
    color: var(--primary-600);
    margin-bottom: var(--space-2);
    line-height: 1;
}

.pricing-period {
    color: var(--gray-500);
    margin-bottom: var(--space-8);
}

.pricing-features {
    list-style: none;
    padding: 0;
    margin: 0 0 var(--space-8) 0;
    text-align: left;
}

.pricing-features li {
    padding: var(--space-3) 0;
    border-bottom: 1px solid var(--gray-100);
    position: relative;
    padding-left: var(--space-8);
}

.pricing-features li::before {
    content: "✓";
    position: absolute;
    left: 0;
    color: var(--success-500);
    font-weight: bold;
}

/* Modern Navigation */
.site-header {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid var(--gray-200);
    position: sticky;
    top: 0;
    z-index: 50;
}

.site-nav {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: var(--space-4) 0;
}

.site-logo {
    display: flex;
    align-items: center;
    gap: var(--space-3);
    font-size: var(--text-xl);
    font-weight: 700;
    color: var(--primary-600);
    text-decoration: none;
}

.nav-menu {
    display: flex;
    align-items: center;
    gap: var(--space-8);
    list-style: none;
    margin: 0;
    padding: 0;
}

.nav-menu a {
    color: var(--gray-700);
    text-decoration: none;
    font-weight: 500;
    transition: color var(--transition-fast);
    position: relative;
}

.nav-menu a:hover {
    color: var(--primary-600);
}

.nav-menu a::after {
    content: "";
    position: absolute;
    bottom: -4px;
    left: 0;
    width: 0;
    height: 2px;
    background: var(--primary-600);
    transition: width var(--transition-fast);
}

.nav-menu a:hover::after {
    width: 100%;
}

/* Responsive Design */
@media (max-width: 768px) {
    .container {
        padding: 0 var(--space-4);
    }
    
    .hero-title {
        font-size: var(--text-4xl);
    }
    
    .hero {
        padding: var(--space-16) 0;
    }
    
    .grid-cols-2,
    .grid-cols-3,
    .grid-cols-4 {
        grid-template-columns: 1fr;
    }
    
    .pricing-card.featured {
        transform: none;
    }
    
    .nav-menu {
        display: none;
    }
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInLeft {
    from {
        opacity: 0;
        transform: translateX(-30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes fadeInRight {
    from {
        opacity: 0;
        transform: translateX(30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.animate-fade-in-up {
    animation: fadeInUp 0.6s ease-out;
}

.animate-fade-in-left {
    animation: fadeInLeft 0.6s ease-out;
}

.animate-fade-in-right {
    animation: fadeInRight 0.6s ease-out;
}

/* Utility Classes */
.relative { position: relative; }
.absolute { position: absolute; }
.fixed { position: fixed; }
.sticky { position: sticky; }

.top-0 { top: 0; }
.right-0 { right: 0; }
.bottom-0 { bottom: 0; }
.left-0 { left: 0; }

.z-10 { z-index: 10; }
.z-20 { z-index: 20; }
.z-50 { z-index: 50; }

.w-full { width: 100%; }
.h-full { height: 100%; }

.m-0 { margin: 0; }
.mt-4 { margin-top: var(--space-4); }
.mb-4 { margin-bottom: var(--space-4); }
.mb-8 { margin-bottom: var(--space-8); }

.p-0 { padding: 0; }
.p-4 { padding: var(--space-4); }
.p-6 { padding: var(--space-6); }
.p-8 { padding: var(--space-8); }

.rounded { border-radius: var(--rounded); }
.rounded-lg { border-radius: var(--rounded-lg); }
.rounded-xl { border-radius: var(--rounded-xl); }
.rounded-2xl { border-radius: var(--rounded-2xl); }
.rounded-full { border-radius: var(--rounded-full); }

.shadow { box-shadow: var(--shadow); }
.shadow-lg { box-shadow: var(--shadow-lg); }
.shadow-xl { box-shadow: var(--shadow-xl); }

.bg-white { background-color: white; }
.bg-gray-50 { background-color: var(--gray-50); }
.bg-gray-100 { background-color: var(--gray-100); }
.bg-primary-600 { background-color: var(--primary-600); }

.border { border: 1px solid var(--gray-200); }
.border-gray-300 { border-color: var(--gray-300); }
.border-primary-500 { border-color: var(--primary-500); }

.overflow-hidden { overflow: hidden; }
.overflow-auto { overflow: auto; }

.cursor-pointer { cursor: pointer; }

.select-none { user-select: none; }

.transition { transition: all var(--transition-base); }
.transition-fast { transition: all var(--transition-fast); }
.transition-slow { transition: all var(--transition-slow); }';

    file_put_contents($modern_css, $modern_css_content);
    echo '<p class="success">✓ Created modern CSS framework with design system</p>';
    
    // 2. Update the main homepage template
    echo '<h2>2. Upgrading Homepage Template</h2>';
    
    $homepage_template = $template_dir . '/page-home.php';
    if (file_exists($homepage_template)) {
        $new_homepage = '<?php
/**
 * Template Name: Modern Homepage
 * Modern, beautiful homepage with advanced UI components
 */

get_header(); ?>

<main id="primary" class="site-main">
    
    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content text-center animate-fade-in-up">
                <h1 class="hero-title">
                    Professional WordPress Plugins<br>
                    <span class="text-secondary-400">Built for Modern Business</span>
                </h1>
                <p class="hero-subtitle">
                    We create powerful, scalable WordPress plugins that solve real business problems. 
                    From property management to business automation, our solutions are trusted by thousands 
                    of users worldwide.
                </p>
                <div class="flex gap-4 justify-center flex-wrap">
                    <a href="/plugins/" class="btn btn-primary btn-xl">
                        <i class="fas fa-rocket"></i>
                        Explore Plugins
                    </a>
                    <a href="/pricing/" class="btn btn-outline btn-xl">
                        <i class="fas fa-eye"></i>
                        View Pricing
                    </a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Stats Section -->
    <section class="py-16 bg-white">
        <div class="container">
            <div class="grid grid-cols-4 gap-8 text-center">
                <div class="animate-fade-in-left">
                    <div class="text-4xl font-bold text-primary-600 mb-2">10,000+</div>
                    <div class="text-gray-600">Active Users</div>
                </div>
                <div class="animate-fade-in-up">
                    <div class="text-4xl font-bold text-primary-600 mb-2">5</div>
                    <div class="text-gray-600">Premium Plugins</div>
                </div>
                <div class="animate-fade-in-up">
                    <div class="text-4xl font-bold text-primary-600 mb-2">99.9%</div>
                    <div class="text-gray-600">Uptime SLA</div>
                </div>
                <div class="animate-fade-in-right">
                    <div class="text-4xl font-bold text-primary-600 mb-2">24/7</div>
                    <div class="text-gray-600">Pro Support</div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Featured Plugins -->
    <section class="py-24 bg-gray-50">
        <div class="container">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4">Featured WordPress Plugins</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Powerful solutions designed to streamline your business operations and boost productivity.
                </p>
            </div>
            
            <div class="feature-grid">
                <?php
                $featured_plugins = get_posts(array(
                    \'post_type\' => \'phstudios_plugin\',
                    \'posts_per_page\' => 3,
                    \'meta_query\' => array(
                        array(
                            \'key\' => \'_featured_plugin\',
                            \'value\' => \'1\',
                            \'compare\' => \'=\'
                        )
                    )
                ));
                
                $icons = [\'home\', \'credit-card\', \'file-alt\', \'chart-line\', \'cogs\'];
                $colors = [\'primary\', \'secondary\', \'success\', \'warning\', \'error\'];
                
                foreach ($featured_plugins as $index => $plugin) :
                    setup_postdata($plugin);
                    $icon = $icons[$index % count($icons)];
                    $color = $colors[$index % count($colors)];
                ?>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-<?php echo $icon; ?>"></i>
                        </div>
                        <h3 class="feature-title"><?php echo esc_html($plugin->post_title); ?></h3>
                        <p class="feature-description"><?php echo esc_html($plugin->post_excerpt); ?></p>
                        <div class="mt-6">
                            <a href="<?php echo get_permalink($plugin->ID); ?>" class="btn btn-outline">
                                Learn More <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; wp_reset_postdata(); ?>
            </div>
        </div>
    </section>
    
    <!-- Why Choose PlugHaus -->
    <section class="py-24 bg-white">
        <div class="container">
            <div class="grid grid-cols-2 gap-16 items-center">
                <div>
                    <h2 class="text-4xl font-bold mb-6">Why Choose PlugHaus Studios?</h2>
                    <div class="space-y-6">
                        <div class="flex gap-4">
                            <div class="w-8 h-8 rounded-lg bg-primary-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-shield-alt text-primary-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold mb-2">Enterprise-Grade Security</h4>
                                <p class="text-gray-600">All plugins follow WordPress coding standards with regular security audits and updates.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-8 h-8 rounded-lg bg-success-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-rocket text-success-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold mb-2">Performance Optimized</h4>
                                <p class="text-gray-600">Built for speed with efficient code, caching, and database optimization.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-8 h-8 rounded-lg bg-warning-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-headset text-warning-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold mb-2">24/7 Pro Support</h4>
                                <p class="text-gray-600">Get help when you need it with our dedicated support team and comprehensive documentation.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-8 h-8 rounded-lg bg-secondary-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-sync-alt text-secondary-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold mb-2">Lifetime Updates</h4>
                                <p class="text-gray-600">Stay current with automatic updates and new feature releases.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <div class="bg-gradient-to-br from-primary-500 to-secondary-600 rounded-2xl p-8 text-white">
                        <div class="mb-6">
                            <div class="w-16 h-16 bg-white bg-opacity-20 rounded-xl flex items-center justify-center mb-4">
                                <i class="fas fa-code text-2xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold mb-2">Developer Friendly</h3>
                            <p class="opacity-90">Clean, well-documented code with hooks, filters, and extensive API documentation.</p>
                        </div>
                        <div class="bg-white bg-opacity-10 rounded-lg p-4 font-mono text-sm">
                            <div class="text-secondary-200">// Easy to extend</div>
                            <div>add_action(\'phpm_after_save\', \'custom_function\');</div>
                            <div class="text-secondary-200">// Full API access</div>
                            <div>$properties = PHPM_API::get_properties();</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Pricing Preview -->
    <section class="py-24 bg-gray-50">
        <div class="container">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4">Simple, Transparent Pricing</h2>
                <p class="text-xl text-gray-600">Choose the plan that fits your needs. Upgrade or downgrade at any time.</p>
            </div>
            
            <div class="pricing-grid max-w-4xl mx-auto">
                <div class="pricing-card">
                    <h3 class="pricing-title">Free</h3>
                    <div class="pricing-price">$0</div>
                    <div class="pricing-period">Forever</div>
                    <ul class="pricing-features">
                        <li>Basic plugin features</li>
                        <li>Community support</li>
                        <li>WordPress.org updates</li>
                        <li>Documentation access</li>
                    </ul>
                    <a href="/plugins/" class="btn btn-outline w-full">Download Free</a>
                </div>
                
                <div class="pricing-card featured">
                    <h3 class="pricing-title">Pro</h3>
                    <div class="pricing-price">$99</div>
                    <div class="pricing-period">per year</div>
                    <ul class="pricing-features">
                        <li>All advanced features</li>
                        <li>Priority support</li>
                        <li>Automatic updates</li>
                        <li>Premium documentation</li>
                        <li>White-label options</li>
                    </ul>
                    <a href="/pricing/" class="btn btn-primary w-full">Get Pro</a>
                </div>
                
                <div class="pricing-card">
                    <h3 class="pricing-title">Bundle</h3>
                    <div class="pricing-price">$299</div>
                    <div class="pricing-period">lifetime</div>
                    <ul class="pricing-features">
                        <li>All current & future plugins</li>
                        <li>Lifetime updates</li>
                        <li>VIP support</li>
                        <li>Agency license</li>
                        <li>Custom development</li>
                    </ul>
                    <a href="/pricing/" class="btn btn-secondary w-full">Get Bundle</a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- CTA Section -->
    <section class="py-24 bg-gradient-to-r from-primary-600 to-secondary-600 text-white">
        <div class="container text-center">
            <h2 class="text-4xl font-bold mb-4">Ready to Transform Your Business?</h2>
            <p class="text-xl opacity-90 mb-8 max-w-2xl mx-auto">
                Join thousands of satisfied customers who trust PlugHaus Studios for their WordPress plugin needs.
            </p>
            <div class="flex gap-4 justify-center flex-wrap">
                <a href="/plugins/" class="btn bg-white text-primary-600 btn-lg hover:bg-gray-100">
                    <i class="fas fa-download"></i>
                    Start Free Today
                </a>
                <a href="/contact/" class="btn btn-outline border-white text-white btn-lg hover:bg-white hover:text-primary-600">
                    <i class="fas fa-comments"></i>
                    Talk to Sales
                </a>
            </div>
        </div>
    </section>
    
</main>

<?php get_footer(); ?>';

        file_put_contents($homepage_template, $new_homepage);
        echo '<p class="success">✓ Created modern homepage template with advanced components</p>';
    }
    
    // 3. Update functions.php to load modern CSS
    echo '<h2>3. Adding Modern CSS to Theme</h2>';
    
    $functions_file = $template_dir . '/functions.php';
    $functions_content = file_get_contents($functions_file);
    
    if (strpos($functions_content, 'plughaus-studios-modern') === false) {
        $css_enqueue = "\n\n// Enqueue modern design system\nwp_enqueue_style('plughaus-studios-modern', get_template_directory_uri() . '/assets/css/modern.css', array('plughaus-studios-style'), '1.0.0');";
        
        $functions_content = str_replace(
            "wp_enqueue_style('plughaus-studios-style', get_stylesheet_uri(), array(), '1.0.0');",
            "wp_enqueue_style('plughaus-studios-style', get_stylesheet_uri(), array(), '1.0.0');" . $css_enqueue,
            $functions_content
        );
        
        file_put_contents($functions_file, $functions_content);
        echo '<p class="success">✓ Added modern CSS framework to theme functions</p>';
    }
    
    echo '</div>';
    
    echo '<div class="section">';
    echo '<h2>🎨 Design Upgrade Complete!</h2>';
    echo '<p class="success" style="font-size: 18px;">✓ Your site now has a modern, professional design system!</p>';
    
    echo '<h3>New Features Added:</h3>';
    echo '<ul>';
    echo '<li>✅ Complete modern CSS design system with utilities</li>';
    echo '<li>✅ Beautiful hero section with gradient backgrounds</li>';
    echo '<li>✅ Feature cards with hover animations</li>';
    echo '<li>✅ Modern pricing components</li>';
    echo '<li>✅ Professional typography and spacing</li>';
    echo '<li>✅ Responsive grid system</li>';
    echo '<li>✅ Advanced button and badge components</li>';
    echo '<li>✅ Smooth animations and transitions</li>';
    echo '</ul>';
    
    echo '<a href="' . home_url() . '" class="btn" style="background: #28a745; font-size: 1.2em; padding: 15px 30px;">🚀 View New Design</a>';
    echo '</div>';
    ?>
    
</body>
</html>