<?php
/**
 * PlugHaus Studios Theme Setup Script
 * 
 * Run this file once to activate the theme and create sample content
 * Delete this file after running it.
 */

// Load WordPress
require_once dirname(__FILE__) . '/wp-load.php';

// Check if user is admin
if (!current_user_can('manage_options')) {
    wp_die('You do not have permission to run this script.');
}

echo '<h1>PlugHaus Studios Theme Setup</h1>';

// Activate the theme
echo '<h2>Activating Theme...</h2>';
$theme = wp_get_theme('plughaus-studios');
if ($theme->exists()) {
    switch_theme('plughaus-studios');
    echo '<p style="color: green;">✓ PlugHaus Studios theme activated successfully!</p>';
} else {
    echo '<p style="color: red;">✗ PlugHaus Studios theme not found. Make sure it\'s in wp-content/themes/</p>';
    exit;
}

// Create plugin categories
echo '<h2>Creating Plugin Categories...</h2>';
$categories = array(
    'Property Management' => 'Plugins for managing properties, tenants, and leases',
    'Payment Processing' => 'Payment and financial transaction plugins',
    'Document Management' => 'Document generation and management tools',
    'Analytics' => 'Analytics and reporting plugins',
    'Automation' => 'Business automation and workflow plugins'
);

foreach ($categories as $name => $description) {
    $term = wp_insert_term($name, 'plugin_category', array(
        'description' => $description,
        'slug' => sanitize_title($name)
    ));
    
    if (!is_wp_error($term)) {
        echo '<p style="color: green;">✓ Created category: ' . esc_html($name) . '</p>';
    } else {
        echo '<p style="color: orange;">⚠ Category might already exist: ' . esc_html($name) . '</p>';
    }
}

// Create plugin tags
echo '<h2>Creating Plugin Tags...</h2>';
$tags = array('wordpress', 'property', 'management', 'real-estate', 'tenant', 'lease', 'payment', 'automation', 'business', 'freemium');

foreach ($tags as $tag) {
    $term = wp_insert_term($tag, 'plugin_tag', array(
        'slug' => sanitize_title($tag)
    ));
    
    if (!is_wp_error($term)) {
        echo '<p style="color: green;">✓ Created tag: ' . esc_html($tag) . '</p>';
    }
}

// Create sample plugins
echo '<h2>Creating Sample Plugins...</h2>';

$plugins = array(
    array(
        'title' => 'PlugHaus Property Management',
        'content' => 'A comprehensive property management solution for WordPress. Manage properties, tenants, leases, and maintenance requests with ease. Perfect for landlords, property managers, and real estate professionals.',
        'excerpt' => 'Complete property management solution with tenant tracking, lease management, and maintenance requests.',
        'status' => 'available',
        'version' => '1.0.0',
        'wordpress_url' => 'https://wordpress.org/plugins/plughaus-property-management/',
        'github_url' => 'https://github.com/CommonElements/plughaus-studios-monorepo',
        'price_free' => 'Free',
        'price_pro' => '$99/year',
        'download_count' => '1250',
        'rating' => '4.8',
        'tested_wp_version' => '6.4',
        'min_php_version' => '7.4',
        'features' => "Property Management\nTenant Tracking\nLease Management\nMaintenance Requests\nDashboard Overview\nImport/Export Data",
        'pro_features' => "Advanced Analytics\nPayment Automation\nDocument Management\nEmail Templates\nCustom Fields\nPriority Support",
        'featured' => true,
        'categories' => array('Property Management'),
        'tags' => array('property', 'management', 'real-estate', 'tenant', 'lease')
    ),
    array(
        'title' => 'PlugHaus Payment Gateway',
        'content' => 'Integrated payment processing solution with automated rent collection, late fees, and comprehensive financial reporting. Seamlessly connects with your property management system.',
        'excerpt' => 'Automated payment processing with rent collection, late fees, and financial reporting.',
        'status' => 'coming-soon',
        'version' => '0.9.0',
        'price_pro' => '$149/year',
        'tested_wp_version' => '6.4',
        'min_php_version' => '7.4',
        'pro_features' => "Stripe Integration\nRecurring Payments\nLate Fee Automation\nPayment Tracking\nFinancial Reporting\nTenant Payment Portal",
        'categories' => array('Payment Processing', 'Property Management'),
        'tags' => array('payment', 'stripe', 'automation', 'business')
    ),
    array(
        'title' => 'PlugHaus Document Automator',
        'content' => 'Generate leases, notices, and legal documents automatically with customizable templates. Features digital signatures and secure document storage.',
        'excerpt' => 'Automated document generation with customizable templates and digital signatures.',
        'status' => 'in-development',
        'version' => '0.5.0',
        'price_pro' => '$79/year',
        'tested_wp_version' => '6.4',
        'min_php_version' => '7.4',
        'pro_features' => "Template Engine\nDigital Signatures\nDocument Vault\nAutomated Generation\nLegal Compliance\nCustom Branding",
        'categories' => array('Document Management', 'Property Management'),
        'tags' => array('documents', 'automation', 'templates', 'legal')
    ),
    array(
        'title' => 'PlugHaus Analytics Framework',
        'content' => 'Advanced analytics and reporting with interactive dashboards and performance insights. Get deep insights into your property portfolio performance.',
        'excerpt' => 'Advanced analytics and reporting with interactive dashboards and insights.',
        'status' => 'beta',
        'version' => '0.8.0',
        'price_pro' => '$59/year',
        'tested_wp_version' => '6.4',
        'min_php_version' => '7.4',
        'pro_features' => "Custom Dashboards\nAutomated Reports\nData Visualization\nPerformance Metrics\nExport Options\nScheduled Reports",
        'categories' => array('Analytics', 'Property Management'),
        'tags' => array('analytics', 'reporting', 'dashboard', 'insights')
    )
);

foreach ($plugins as $plugin_data) {
    // Create the plugin post
    $post_data = array(
        'post_title' => $plugin_data['title'],
        'post_content' => $plugin_data['content'],
        'post_excerpt' => $plugin_data['excerpt'],
        'post_status' => 'publish',
        'post_type' => 'phstudios_plugin',
        'post_author' => 1
    );
    
    $plugin_id = wp_insert_post($post_data);
    
    if ($plugin_id && !is_wp_error($plugin_id)) {
        echo '<p style="color: green;">✓ Created plugin: ' . esc_html($plugin_data['title']) . '</p>';
        
        // Add meta data
        $meta_fields = array(
            '_plugin_status', '_plugin_version', '_wordpress_url', '_github_url', '_demo_url',
            '_price_free', '_price_pro', '_download_count', '_rating', '_tested_wp_version',
            '_min_php_version', '_plugin_features', '_pro_features'
        );
        
        foreach ($meta_fields as $field) {
            $key = str_replace('_', '', $field);
            if (isset($plugin_data[$key])) {
                update_post_meta($plugin_id, $field, $plugin_data[$key]);
            }
        }
        
        // Set featured plugin
        if (isset($plugin_data['featured']) && $plugin_data['featured']) {
            update_post_meta($plugin_id, '_featured_plugin', '1');
        }
        
        // Set categories
        if (isset($plugin_data['categories'])) {
            $category_ids = array();
            foreach ($plugin_data['categories'] as $cat_name) {
                $term = get_term_by('name', $cat_name, 'plugin_category');
                if ($term) {
                    $category_ids[] = $term->term_id;
                }
            }
            if (!empty($category_ids)) {
                wp_set_post_terms($plugin_id, $category_ids, 'plugin_category');
            }
        }
        
        // Set tags
        if (isset($plugin_data['tags'])) {
            wp_set_post_terms($plugin_id, $plugin_data['tags'], 'plugin_tag');
        }
    } else {
        echo '<p style="color: red;">✗ Failed to create plugin: ' . esc_html($plugin_data['title']) . '</p>';
    }
}

// Create pages
echo '<h2>Creating Pages...</h2>';

$pages = array(
    'home' => array(
        'title' => 'Home',
        'content' => '',
        'template' => 'page-home.php'
    ),
    'plugins' => array(
        'title' => 'Our Plugins',
        'content' => '[plugin_showcase count="-1"]',
    ),
    'about' => array(
        'title' => 'About Us',
        'content' => 'We\'re a focused WordPress plugin development studio specializing in business automation and property management solutions. Our team has years of experience building scalable, secure plugins that follow WordPress best practices.',
    ),
    'contact' => array(
        'title' => 'Contact',
        'content' => '[contact_form title="Get In Touch"]',
    ),
    'support' => array(
        'title' => 'Support',
        'content' => 'Get help with our plugins through our comprehensive support resources. We offer community support through WordPress.org forums and priority support for Pro customers.',
    ),
    'privacy-policy' => array(
        'title' => 'Privacy Policy',
        'content' => 'This privacy policy explains how PlugHaus Studios collects, uses, and protects your information when you use our plugins and services.',
    ),
    'terms-of-service' => array(
        'title' => 'Terms of Service',
        'content' => 'These terms of service govern your use of PlugHaus Studios plugins and services.',
    )
);

foreach ($pages as $slug => $page) {
    if (!get_page_by_path($slug)) {
        $page_data = array(
            'post_title' => $page['title'],
            'post_content' => $page['content'],
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_name' => $slug,
            'post_author' => 1
        );
        
        $page_id = wp_insert_post($page_data);
        
        if ($page_id && !is_wp_error($page_id)) {
            echo '<p style="color: green;">✓ Created page: ' . esc_html($page['title']) . '</p>';
            
            if (isset($page['template'])) {
                update_post_meta($page_id, '_wp_page_template', $page['template']);
            }
            
            // Set front page
            if ($slug === 'home') {
                update_option('show_on_front', 'page');
                update_option('page_on_front', $page_id);
                echo '<p style="color: green;">✓ Set homepage</p>';
            }
        } else {
            echo '<p style="color: red;">✗ Failed to create page: ' . esc_html($page['title']) . '</p>';
        }
    } else {
        echo '<p style="color: orange;">⚠ Page already exists: ' . esc_html($page['title']) . '</p>';
    }
}

// Create menus
echo '<h2>Creating Navigation Menu...</h2>';

$menu_name = 'Primary Menu';
$menu_id = wp_create_nav_menu($menu_name);

if (!is_wp_error($menu_id)) {
    echo '<p style="color: green;">✓ Created navigation menu</p>';
    
    // Add menu items
    $menu_items = array(
        array('title' => 'Home', 'url' => home_url('/'), 'type' => 'custom'),
        array('title' => 'Plugins', 'slug' => 'plugins', 'type' => 'page'),
        array('title' => 'About', 'slug' => 'about', 'type' => 'page'),
        array('title' => 'Support', 'slug' => 'support', 'type' => 'page'),
        array('title' => 'Contact', 'slug' => 'contact', 'type' => 'page')
    );
    
    foreach ($menu_items as $item) {
        if ($item['type'] === 'page') {
            $page = get_page_by_path($item['slug']);
            if ($page) {
                wp_update_nav_menu_item($menu_id, 0, array(
                    'menu-item-title' => $item['title'],
                    'menu-item-object' => 'page',
                    'menu-item-object-id' => $page->ID,
                    'menu-item-type' => 'post_type',
                    'menu-item-status' => 'publish'
                ));
            }
        } else {
            wp_update_nav_menu_item($menu_id, 0, array(
                'menu-item-title' => $item['title'],
                'menu-item-url' => $item['url'],
                'menu-item-type' => 'custom',
                'menu-item-status' => 'publish'
            ));
        }
    }
    
    // Assign menu to location
    $locations = get_theme_mod('nav_menu_locations');
    $locations['primary'] = $menu_id;
    set_theme_mod('nav_menu_locations', $locations);
    
    echo '<p style="color: green;">✓ Added menu items and assigned to primary location</p>';
}

// Set theme customizer options
echo '<h2>Setting Theme Options...</h2>';

set_theme_mod('hero_title', 'Professional <span class="gradient-text">WordPress Plugins</span><br>Built for Business');
set_theme_mod('hero_description', 'We create powerful, scalable WordPress plugins that solve real business problems. From property management to business automation, our solutions are trusted by thousands of users worldwide.');
set_theme_mod('contact_email', 'hello@plughausstudios.com');
set_theme_mod('support_email', 'support@plughausstudios.com');

echo '<p style="color: green;">✓ Set theme customizer options</p>';

// Update site settings
echo '<h2>Updating Site Settings...</h2>';

update_option('blogname', 'PlugHaus Studios');
update_option('blogdescription', 'Professional WordPress Plugin Development');
update_option('users_can_register', 0);
update_option('default_comment_status', 'closed');
update_option('default_ping_status', 'closed');

// Set permalink structure
global $wp_rewrite;
$wp_rewrite->set_permalink_structure('/%postname%/');
$wp_rewrite->flush_rules();

echo '<p style="color: green;">✓ Updated site settings and permalink structure</p>';

// Flush rewrite rules
flush_rewrite_rules();

echo '<h2>Setup Complete!</h2>';
echo '<p style="color: green; font-size: 18px; font-weight: bold;">✓ PlugHaus Studios theme setup completed successfully!</p>';
echo '<p><a href="' . home_url() . '" target="_blank" style="background: #2271b1; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">View Your Site</a></p>';
echo '<p><a href="' . admin_url() . '" style="background: #50575e; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">Go to Admin</a></p>';

echo '<h3>Next Steps:</h3>';
echo '<ul>';
echo '<li>Delete this setup file (setup-theme.php) for security</li>';
echo '<li>Add your own content and customize the theme</li>';
echo '<li>Upload plugin screenshots to the media library</li>';
echo '<li>Configure contact forms and email settings</li>';
echo '<li>Set up analytics and tracking</li>';
echo '</ul>';

echo '<style>
body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
h1, h2 { color: #2271b1; }
p { margin: 8px 0; }
ul { margin-left: 20px; }
</style>';
?>