<?php
/**
 * Enhanced Site Setup Script for PlugHaus Studios
 * Run this by visiting: http://plughaus-studios-the-beginning-is-finished.local/enhance-site.php
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
    <title>PlugHaus Studios Site Enhancement</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 0 auto; padding: 20px; }
        h1, h2 { color: #2563eb; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { color: #0066cc; font-style: italic; }
        .btn { background: #2563eb; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; margin: 10px 0; }
        .section { background: #f8f9fa; padding: 15px; margin: 20px 0; border-radius: 8px; }
    </style>
</head>
<body>
    <h1>PlugHaus Studios Site Enhancement</h1>
    
    <?php
    // 1. Create navigation menu
    echo '<div class="section">';
    echo '<h2>1. Setting Up Navigation Menu</h2>';
    
    $menu_name = 'Primary Navigation';
    $menu_exists = wp_get_nav_menu_object($menu_name);
    
    if (!$menu_exists) {
        $menu_id = wp_create_nav_menu($menu_name);
        echo '<p class="success">✓ Created primary navigation menu</p>';
        
        // Add menu items
        $pages = array('home', 'plugins', 'about', 'support', 'contact');
        $menu_order = 1;
        
        foreach ($pages as $page_slug) {
            $page = get_page_by_path($page_slug);
            if ($page) {
                wp_update_nav_menu_item($menu_id, 0, array(
                    'menu-item-title' => ucfirst($page_slug),
                    'menu-item-object' => 'page',
                    'menu-item-object-id' => $page->ID,
                    'menu-item-type' => 'post_type',
                    'menu-item-status' => 'publish',
                    'menu-item-position' => $menu_order++
                ));
                echo '<p class="info">→ Added ' . ucfirst($page_slug) . ' to menu</p>';
            }
        }
        
        // Set menu location
        $locations = get_theme_mod('nav_menu_locations');
        $locations['primary'] = $menu_id;
        set_theme_mod('nav_menu_locations', $locations);
        
        echo '<p class="success">✓ Assigned menu to primary location</p>';
    } else {
        echo '<p class="success">✓ Primary navigation menu already exists</p>';
    }
    echo '</div>';
    
    // 2. Create additional plugins and content
    echo '<div class="section">';
    echo '<h2>2. Adding More Plugin Content</h2>';
    
    $additional_plugins = array(
        array(
            'title' => 'Payment Gateway Pro',
            'content' => '<p>Seamless payment processing for WordPress sites. Accept credit cards, PayPal, Stripe, and more with advanced fraud protection and recurring billing support.</p>
            
<h3>Features</h3>
<ul>
<li>Multiple Payment Gateways - Stripe, PayPal, Square, Authorize.net</li>
<li>Recurring Billing - Subscriptions and payment plans</li>
<li>Advanced Security - PCI compliance and fraud detection</li>
<li>Custom Checkout - Branded payment forms</li>
<li>Analytics Dashboard - Payment tracking and reporting</li>
<li>Mobile Optimized - Responsive payment forms</li>
</ul>',
            'excerpt' => 'Complete payment processing solution with multiple gateways, recurring billing, and advanced security features.',
            'status' => 'coming-soon',
            'version' => '2.1.0',
            'price_free' => 'Free',
            'price_pro' => '$149/year',
            'rating' => '4.9',
            'downloads' => '2800'
        ),
        array(
            'title' => 'Document Automator',
            'content' => '<p>Automate document generation and management for WordPress. Create PDFs, contracts, invoices, and reports automatically from your WordPress data.</p>
            
<h3>Features</h3>
<ul>
<li>PDF Generation - Create professional documents</li>
<li>Template Engine - Customizable document templates</li>
<li>Data Integration - Pull from WordPress posts, users, custom fields</li>
<li>Digital Signatures - E-signature integration</li>
<li>Bulk Operations - Generate multiple documents at once</li>
<li>Cloud Storage - Integration with Google Drive, Dropbox</li>
</ul>',
            'excerpt' => 'Powerful document automation with PDF generation, templates, and digital signature capabilities.',
            'status' => 'coming-soon',
            'version' => '1.5.0',
            'price_free' => 'Free',
            'price_pro' => '$199/year',
            'rating' => '4.7',
            'downloads' => '950'
        ),
        array(
            'title' => 'SEO Analytics Suite',
            'content' => '<p>Comprehensive SEO analytics and optimization toolkit for WordPress. Track rankings, analyze competitors, and optimize your content for search engines.</p>
            
<h3>Features</h3>
<ul>
<li>Rank Tracking - Monitor keyword positions</li>
<li>Competitor Analysis - Track competitor performance</li>
<li>Content Optimization - AI-powered SEO suggestions</li>
<li>Technical SEO - Site health and performance monitoring</li>
<li>Backlink Analysis - Monitor and analyze backlinks</li>
<li>Reporting - Automated SEO reports</li>
</ul>',
            'excerpt' => 'Advanced SEO analytics with rank tracking, competitor analysis, and content optimization tools.',
            'status' => 'available',
            'version' => '3.2.1',
            'price_free' => 'Free',
            'price_pro' => '$99/year',
            'rating' => '4.6',
            'downloads' => '5200'
        )
    );
    
    foreach ($additional_plugins as $plugin_data) {
        // Check if plugin already exists
        $existing = get_posts(array(
            'post_type' => 'phstudios_plugin',
            'title' => $plugin_data['title'],
            'posts_per_page' => 1
        ));
        
        if (empty($existing)) {
            $plugin_post = array(
                'post_title' => $plugin_data['title'],
                'post_content' => $plugin_data['content'],
                'post_excerpt' => $plugin_data['excerpt'],
                'post_status' => 'publish',
                'post_type' => 'phstudios_plugin',
                'post_author' => get_current_user_id()
            );
            
            $plugin_id = wp_insert_post($plugin_post);
            
            if ($plugin_id && !is_wp_error($plugin_id)) {
                echo '<p class="success">✓ Created plugin: ' . $plugin_data['title'] . '</p>';
                
                // Add meta data
                update_post_meta($plugin_id, '_plugin_status', $plugin_data['status']);
                update_post_meta($plugin_id, '_plugin_version', $plugin_data['version']);
                update_post_meta($plugin_id, '_price_free', $plugin_data['price_free']);
                update_post_meta($plugin_id, '_price_pro', $plugin_data['price_pro']);
                update_post_meta($plugin_id, '_rating', $plugin_data['rating']);
                update_post_meta($plugin_id, '_download_count', $plugin_data['downloads']);
                update_post_meta($plugin_id, '_tested_wp_version', '6.4');
                update_post_meta($plugin_id, '_min_php_version', '7.4');
                
                if ($plugin_data['status'] === 'available') {
                    update_post_meta($plugin_id, '_featured_plugin', '1');
                }
            }
        } else {
            echo '<p class="info">→ Plugin already exists: ' . $plugin_data['title'] . '</p>';
        }
    }
    echo '</div>';
    
    // 3. Enhance existing pages
    echo '<div class="section">';
    echo '<h2>3. Enhancing Page Content</h2>';
    
    // Update About page
    $about_page = get_page_by_path('about');
    if ($about_page) {
        $about_content = '
<h2>About PlugHaus Studios</h2>

<p>We\'re a focused WordPress plugin development studio specializing in business automation and property management solutions. Our mission is to create powerful, scalable plugins that solve real business problems.</p>

<h3>Our Story</h3>
<p>Founded in 2024, PlugHaus Studios emerged from the need for better WordPress business tools. We saw too many businesses struggling with disconnected systems and manual processes, so we decided to build something better.</p>

<h3>What We Do</h3>
<ul>
<li><strong>Property Management Solutions</strong> - Complete systems for landlords and property managers</li>
<li><strong>Payment Processing</strong> - Secure, reliable payment gateways and billing systems</li>
<li><strong>Document Automation</strong> - Streamline paperwork and document generation</li>
<li><strong>Business Analytics</strong> - Data-driven insights for better decision making</li>
</ul>

<h3>Our Values</h3>
<ul>
<li><strong>Quality First</strong> - Every plugin is thoroughly tested and documented</li>
<li><strong>User-Focused</strong> - We build for real people solving real problems</li>
<li><strong>Open Source</strong> - Contributing back to the WordPress community</li>
<li><strong>Continuous Innovation</strong> - Always improving and adding new features</li>
</ul>

<h3>Get in Touch</h3>
<p>Have questions or need custom development? <a href="' . home_url('/contact/') . '">Contact us</a> - we\'d love to hear from you!</p>';

        wp_update_post(array(
            'ID' => $about_page->ID,
            'post_content' => $about_content
        ));
        
        echo '<p class="success">✓ Enhanced About page content</p>';
    }
    
    // Update Support page
    $support_page = get_page_by_path('support');
    if ($support_page) {
        $support_content = '
<h2>Plugin Support & Documentation</h2>

<p>Get help with our plugins through our comprehensive support resources. We\'re here to help you succeed!</p>

<h3>Documentation</h3>
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin: 2rem 0;">
    <div style="background: white; padding: 1.5rem; border: 1px solid #e2e8f0; border-radius: 0.5rem;">
        <h4>🏠 Property Management</h4>
        <p>Complete guide to managing properties, tenants, and leases.</p>
        <a href="#" style="color: #2563eb; text-decoration: none;">View Documentation →</a>
    </div>
    
    <div style="background: white; padding: 1.5rem; border: 1px solid #e2e8f0; border-radius: 0.5rem;">
        <h4>💳 Payment Gateway</h4>
        <p>Setup guides for payment processing and billing automation.</p>
        <a href="#" style="color: #2563eb; text-decoration: none;">View Documentation →</a>
    </div>
    
    <div style="background: white; padding: 1.5rem; border: 1px solid #e2e8f0; border-radius: 0.5rem;">
        <h4>📄 Document Automator</h4>
        <p>Learn how to automate document generation and management.</p>
        <a href="#" style="color: #2563eb; text-decoration: none;">View Documentation →</a>
    </div>
</div>

<h3>Support Options</h3>
<ul>
<li><strong>Community Forums</strong> - Connect with other users and get community support</li>
<li><strong>Email Support</strong> - Direct support for pro users (24-48 hour response)</li>
<li><strong>Priority Support</strong> - Premium support with faster response times</li>
<li><strong>Custom Development</strong> - Need something specific? We can build it!</li>
</ul>

<h3>Contact Support</h3>
<p>Still need help? <a href="' . home_url('/contact/') . '">Contact our support team</a> and we\'ll get back to you soon.</p>';

        wp_update_post(array(
            'ID' => $support_page->ID,
            'post_content' => $support_content
        ));
        
        echo '<p class="success">✓ Enhanced Support page content</p>';
    }
    echo '</div>';
    
    // 4. Create plugin categories
    echo '<div class="section">';
    echo '<h2>4. Setting Up Plugin Categories</h2>';
    
    $categories = array(
        'Property Management' => 'Plugins for managing properties, tenants, and real estate.',
        'Payment Processing' => 'E-commerce and payment gateway solutions.',
        'Document Automation' => 'Tools for generating and managing documents.',
        'Analytics & SEO' => 'Analytics, SEO, and performance optimization tools.',
        'Business Tools' => 'General business automation and productivity plugins.'
    );
    
    foreach ($categories as $cat_name => $cat_description) {
        if (!term_exists($cat_name, 'plugin_category')) {
            $term = wp_insert_term($cat_name, 'plugin_category', array(
                'description' => $cat_description
            ));
            if (!is_wp_error($term)) {
                echo '<p class="success">✓ Created category: ' . $cat_name . '</p>';
            }
        } else {
            echo '<p class="info">→ Category already exists: ' . $cat_name . '</p>';
        }
    }
    echo '</div>';
    
    // 5. Set theme customizer defaults
    echo '<div class="section">';
    echo '<h2>5. Setting Theme Defaults</h2>';
    
    set_theme_mod('hero_title', 'Professional <span class="gradient-text">WordPress Plugins</span><br>Built for Business');
    set_theme_mod('hero_description', 'We create powerful, scalable WordPress plugins that solve real business problems. From property management to business automation, our solutions are trusted by thousands of users worldwide.');
    set_theme_mod('contact_email', 'hello@plughausstudios.com');
    set_theme_mod('support_email', 'support@plughausstudios.com');
    
    echo '<p class="success">✓ Set theme customizer defaults</p>';
    echo '</div>';
    
    // Final cleanup
    flush_rewrite_rules();
    
    echo '<div class="section">';
    echo '<h2>Enhancement Complete! 🚀</h2>';
    echo '<p class="success" style="font-size: 18px;">✓ Your site has been significantly enhanced!</p>';
    
    echo '<h3>What Was Added/Updated:</h3>';
    echo '<ul>';
    echo '<li>✓ Set up proper navigation menu with all pages</li>';
    echo '<li>✓ Added 3 additional plugin entries with full content</li>';
    echo '<li>✓ Enhanced About and Support pages with detailed content</li>';
    echo '<li>✓ Created plugin categories for better organization</li>';
    echo '<li>✓ Set theme customizer defaults for hero section</li>';
    echo '<li>✓ Flushed rewrite rules for clean URLs</li>';
    echo '</ul>';
    
    echo '<a href="' . home_url() . '" class="btn">View Your Enhanced Site</a>';
    echo '<a href="' . admin_url('nav-menus.php') . '" class="btn" style="background: #28a745; margin-left: 10px;">Manage Menus</a>';
    echo '</div>';
    ?>
    
</body>
</html>