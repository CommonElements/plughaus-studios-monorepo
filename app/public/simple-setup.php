<?php
/**
 * Simple Setup Script - Step by Step
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load WordPress
require_once dirname(__FILE__) . '/wp-load.php';

// Check authentication
if (!current_user_can('manage_options')) {
    echo '<h1>Please Login First</h1>';
    echo '<p><a href="' . wp_login_url($_SERVER['REQUEST_URI']) . '">Login as Administrator</a></p>';
    exit;
}

echo '<h1>PlugHaus Studios Simple Setup</h1>';

// Get the action
$action = isset($_GET['action']) ? $_GET['action'] : 'start';

switch ($action) {
    case 'start':
        echo '<h2>Setup Menu</h2>';
        echo '<p>Choose what you want to set up:</p>';
        echo '<ul>';
        echo '<li><a href="?action=post_types">1. Register Custom Post Types & Taxonomies</a></li>';
        echo '<li><a href="?action=sample_content">2. Create Sample Content</a></li>';
        echo '<li><a href="?action=pages">3. Create Pages</a></li>';
        echo '<li><a href="?action=menus">4. Create Navigation Menu</a></li>';
        echo '<li><a href="?action=settings">5. Configure Settings</a></li>';
        echo '<li><a href="?action=all">🚀 Do Everything</a></li>';
        echo '</ul>';
        break;
        
    case 'post_types':
        echo '<h2>Registering Post Types & Taxonomies</h2>';
        
        // Load theme functions if not loaded
        $functions_path = get_stylesheet_directory() . '/functions.php';
        if (!function_exists('plughaus_studios_register_post_types')) {
            include_once $functions_path;
        }
        
        // Register post types
        if (function_exists('plughaus_studios_register_post_types')) {
            plughaus_studios_register_post_types();
            echo '<p style="color: green;">✓ Post types registered</p>';
        } else {
            echo '<p style="color: red;">✗ Could not register post types</p>';
        }
        
        // Register taxonomies
        if (function_exists('plughaus_studios_register_taxonomies')) {
            plughaus_studios_register_taxonomies();
            echo '<p style="color: green;">✓ Taxonomies registered</p>';
        } else {
            echo '<p style="color: red;">✗ Could not register taxonomies</p>';
        }
        
        // Create categories
        $categories = array(
            'Property Management' => 'Plugins for managing properties, tenants, and leases',
            'Payment Processing' => 'Payment and financial transaction plugins',
            'Document Management' => 'Document generation and management tools',
            'Analytics' => 'Analytics and reporting plugins'
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
        
        echo '<p><a href="?action=sample_content">Next: Create Sample Content →</a></p>';
        break;
        
    case 'sample_content':
        echo '<h2>Creating Sample Content</h2>';
        
        $plugin_data = array(
            'post_title' => 'PlugHaus Property Management',
            'post_content' => 'A comprehensive property management solution for WordPress. Manage properties, tenants, leases, and maintenance requests with ease. Perfect for landlords, property managers, and real estate professionals.

<h3>Key Features</h3>
<ul>
<li>Property Management - Track multiple properties with detailed information</li>
<li>Tenant Management - Manage tenant information and contact details</li>
<li>Lease Tracking - Monitor lease terms, dates, and rental amounts</li>
<li>Unit Management - Organize properties by individual units</li>
<li>Dashboard Overview - Quick stats and recent activity</li>
<li>WordPress Integration - Seamlessly integrates with WordPress admin</li>
</ul>

<h3>Pro Features</h3>
<ul>
<li>Advanced analytics and reporting</li>
<li>Payment automation and tracking</li>
<li>Document management and storage</li>
<li>Email automation and templates</li>
<li>Custom fields and property types</li>
<li>Multi-property portfolio management</li>
</ul>',
            'post_excerpt' => 'Complete property management solution with tenant tracking, lease management, and maintenance requests.',
            'post_status' => 'publish',
            'post_type' => 'phstudios_plugin',
            'post_author' => get_current_user_id()
        );
        
        $plugin_id = wp_insert_post($plugin_data);
        
        if ($plugin_id && !is_wp_error($plugin_id)) {
            echo '<p style="color: green;">✓ Created sample plugin: ' . esc_html($plugin_data['post_title']) . '</p>';
            
            // Add meta data
            update_post_meta($plugin_id, '_plugin_status', 'available');
            update_post_meta($plugin_id, '_plugin_version', '1.0.0');
            update_post_meta($plugin_id, '_wordpress_url', 'https://wordpress.org/plugins/plughaus-property-management/');
            update_post_meta($plugin_id, '_github_url', 'https://github.com/CommonElements/plughaus-studios-monorepo');
            update_post_meta($plugin_id, '_price_free', 'Free');
            update_post_meta($plugin_id, '_price_pro', '$99/year');
            update_post_meta($plugin_id, '_download_count', '1250');
            update_post_meta($plugin_id, '_rating', '4.8');
            update_post_meta($plugin_id, '_tested_wp_version', '6.4');
            update_post_meta($plugin_id, '_min_php_version', '7.4');
            update_post_meta($plugin_id, '_plugin_features', "Property Management\nTenant Tracking\nLease Management\nMaintenance Requests\nDashboard Overview\nImport/Export Data");
            update_post_meta($plugin_id, '_pro_features', "Advanced Analytics\nPayment Automation\nDocument Management\nEmail Templates\nCustom Fields\nPriority Support");
            update_post_meta($plugin_id, '_featured_plugin', '1');
            
            echo '<p style="color: green;">✓ Added meta data</p>';
            
            // Set category
            $term = get_term_by('name', 'Property Management', 'plugin_category');
            if ($term) {
                wp_set_post_terms($plugin_id, array($term->term_id), 'plugin_category');
                echo '<p style="color: green;">✓ Set category</p>';
            }
            
            // Set tags
            wp_set_post_terms($plugin_id, array('property', 'management', 'real-estate', 'tenant', 'lease'), 'plugin_tag');
            echo '<p style="color: green;">✓ Set tags</p>';
            
        } else {
            echo '<p style="color: red;">✗ Failed to create sample plugin</p>';
            if (is_wp_error($plugin_id)) {
                echo '<p style="color: red;">Error: ' . $plugin_id->get_error_message() . '</p>';
            }
        }
        
        echo '<p><a href="?action=pages">Next: Create Pages →</a></p>';
        break;
        
    case 'pages':
        echo '<h2>Creating Pages</h2>';
        
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
                'content' => 'We\'re a focused WordPress plugin development studio specializing in business automation and property management solutions.',
            ),
            'contact' => array(
                'title' => 'Contact',
                'content' => '[contact_form title="Get In Touch"]',
            ),
            'support' => array(
                'title' => 'Support',
                'content' => 'Get help with our plugins through our comprehensive support resources.',
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
                    'post_author' => get_current_user_id()
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
                        echo '<p style="color: green;">✓ Set as homepage</p>';
                    }
                } else {
                    echo '<p style="color: red;">✗ Failed to create page: ' . esc_html($page['title']) . '</p>';
                }
            } else {
                echo '<p style="color: orange;">⚠ Page already exists: ' . esc_html($page['title']) . '</p>';
            }
        }
        
        echo '<p><a href="?action=menus">Next: Create Menu →</a></p>';
        break;
        
    case 'menus':
        echo '<h2>Creating Navigation Menu</h2>';
        
        $menu_name = 'Primary Menu';
        $menu_id = wp_create_nav_menu($menu_name);
        
        if (!is_wp_error($menu_id)) {
            echo '<p style="color: green;">✓ Created navigation menu</p>';
            
            // Add menu items
            $pages = array(
                'Home' => 'home',
                'Plugins' => 'plugins',
                'About' => 'about',
                'Support' => 'support',
                'Contact' => 'contact'
            );
            
            foreach ($pages as $title => $slug) {
                $page = get_page_by_path($slug);
                if ($page) {
                    wp_update_nav_menu_item($menu_id, 0, array(
                        'menu-item-title' => $title,
                        'menu-item-object' => 'page',
                        'menu-item-object-id' => $page->ID,
                        'menu-item-type' => 'post_type',
                        'menu-item-status' => 'publish'
                    ));
                    echo '<p style="color: green;">✓ Added menu item: ' . $title . '</p>';
                }
            }
            
            // Assign menu to location
            $locations = get_theme_mod('nav_menu_locations');
            $locations['primary'] = $menu_id;
            set_theme_mod('nav_menu_locations', $locations);
            
            echo '<p style="color: green;">✓ Assigned menu to primary location</p>';
        } else {
            echo '<p style="color: red;">✗ Failed to create menu</p>';
        }
        
        echo '<p><a href="?action=settings">Next: Configure Settings →</a></p>';
        break;
        
    case 'settings':
        echo '<h2>Configuring Settings</h2>';
        
        // Update site settings
        update_option('blogname', 'PlugHaus Studios');
        update_option('blogdescription', 'Professional WordPress Plugin Development');
        echo '<p style="color: green;">✓ Updated site title and tagline</p>';
        
        // Set theme customizer options
        set_theme_mod('hero_title', 'Professional <span class="gradient-text">WordPress Plugins</span><br>Built for Business');
        set_theme_mod('hero_description', 'We create powerful, scalable WordPress plugins that solve real business problems.');
        set_theme_mod('contact_email', 'hello@plughausstudios.com');
        set_theme_mod('support_email', 'support@plughausstudios.com');
        echo '<p style="color: green;">✓ Set theme customizer options</p>';
        
        // Set permalink structure
        global $wp_rewrite;
        $wp_rewrite->set_permalink_structure('/%postname%/');
        $wp_rewrite->flush_rules();
        echo '<p style="color: green;">✓ Updated permalink structure</p>';
        
        // Flush rewrite rules
        flush_rewrite_rules();
        echo '<p style="color: green;">✓ Flushed rewrite rules</p>';
        
        echo '<p style="color: green; font-size: 18px; font-weight: bold;">✓ Setup Complete!</p>';
        echo '<p><a href="' . home_url() . '" target="_blank" style="background: #2271b1; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">View Your Site</a></p>';
        break;
        
    case 'all':
        echo '<h2>Running Complete Setup</h2>';
        echo '<script>
        setTimeout(function() { window.location.href = "?action=post_types&auto=1"; }, 1000);
        </script>';
        echo '<p>Starting complete setup process...</p>';
        break;
}

// Auto-continue for complete setup
if (isset($_GET['auto'])) {
    $next_actions = array(
        'post_types' => 'sample_content',
        'sample_content' => 'pages',
        'pages' => 'menus',
        'menus' => 'settings'
    );
    
    if (isset($next_actions[$action])) {
        echo '<script>
        setTimeout(function() { window.location.href = "?action=' . $next_actions[$action] . '&auto=1"; }, 2000);
        </script>';
        echo '<p>Continuing to next step...</p>';
    }
}

echo '<style>
body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
h1, h2 { color: #2271b1; }
p { margin: 8px 0; }
ul { margin-left: 20px; }
a { color: #2271b1; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>';
?>