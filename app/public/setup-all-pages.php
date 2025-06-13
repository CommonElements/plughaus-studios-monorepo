<?php
/**
 * Setup All Professional Pages
 * Creates all pages with proper templates and content
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
    <title>Setup All Professional Pages</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .success { color: green; font-weight: bold; }
        .info { color: blue; }
        .warning { color: orange; }
    </style>
</head>
<body>
    <h1>Setting Up All Professional Pages</h1>
    
    <?php
    // Define all pages to create
    $pages_to_create = array(
        'features' => array(
            'title' => 'Features',
            'content' => 'Discover the powerful features that make our plugins stand out.',
            'template' => 'page-features.php'
        ),
        'plugins' => array(
            'title' => 'Our Plugins',
            'content' => '[plugin_showcase count="-1"]',
            'template' => 'page-plugins.php'
        ),
        'pricing' => array(
            'title' => 'Pricing',
            'content' => 'Simple, transparent pricing for every business size.',
            'template' => 'page-pricing.php'
        ),
        'about' => array(
            'title' => 'About Us',
            'content' => 'We\'re a WordPress plugin development studio focused on creating professional solutions for modern businesses.',
            'template' => 'page-about.php'
        ),
        'contact' => array(
            'title' => 'Contact',
            'content' => '[contact_form]',
            'template' => 'page-contact.php'
        ),
        'support' => array(
            'title' => 'Support',
            'content' => 'Get help with our plugins and find answers to common questions.',
            'template' => 'page-support.php'
        ),
        'blog' => array(
            'title' => 'Blog',
            'content' => 'Stay updated with the latest news, tutorials, and insights.',
            'template' => 'page-blog.php'
        )
    );

    echo '<h2>Creating Pages...</h2>';

    foreach ($pages_to_create as $slug => $page_data) {
        // Check if page already exists
        $existing_page = get_page_by_path($slug);
        
        if (!$existing_page) {
            // Create the page
            $page_args = array(
                'post_title' => $page_data['title'],
                'post_content' => $page_data['content'],
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_name' => $slug,
                'post_author' => get_current_user_id()
            );
            
            $page_id = wp_insert_post($page_args);
            
            if ($page_id && !is_wp_error($page_id)) {
                // Set the page template
                if (isset($page_data['template'])) {
                    update_post_meta($page_id, '_wp_page_template', $page_data['template']);
                }
                
                echo '<p class="success">✓ Created: ' . $page_data['title'] . ' (ID: ' . $page_id . ') with template: ' . $page_data['template'] . '</p>';
            } else {
                echo '<p style="color: red;">✗ Failed to create: ' . $page_data['title'] . '</p>';
            }
        } else {
            // Page exists, just update the template
            if (isset($page_data['template'])) {
                update_post_meta($existing_page->ID, '_wp_page_template', $page_data['template']);
            }
            echo '<p class="info">→ Updated existing page: ' . $page_data['title'] . ' (ID: ' . $existing_page->ID . ') with template: ' . $page_data['template'] . '</p>';
        }
    }

    // Create navigation menu
    echo '<h2>Setting Up Navigation Menu...</h2>';

    // Check if primary menu exists
    $menu_name = 'Primary Menu';
    $menu_exists = wp_get_nav_menu_object($menu_name);

    if (!$menu_exists) {
        $menu_id = wp_create_nav_menu($menu_name);
        echo '<p class="success">✓ Created navigation menu: ' . $menu_name . '</p>';
    } else {
        $menu_id = $menu_exists->term_id;
        echo '<p class="info">→ Using existing menu: ' . $menu_name . '</p>';
    }

    // Add pages to menu
    $menu_items = array(
        array('title' => 'Home', 'url' => home_url('/')),
        array('title' => 'Features', 'url' => home_url('/features/')),
        array('title' => 'Plugins', 'url' => home_url('/plugins/')),
        array('title' => 'Pricing', 'url' => home_url('/pricing/')),
        array('title' => 'Blog', 'url' => home_url('/blog/')),
        array('title' => 'About', 'url' => home_url('/about/')),
        array('title' => 'Contact', 'url' => home_url('/contact/'))
    );

    // Clear existing menu items
    $existing_items = wp_get_nav_menu_items($menu_id);
    if ($existing_items) {
        foreach ($existing_items as $item) {
            wp_delete_post($item->ID, true);
        }
    }

    foreach ($menu_items as $index => $item) {
        wp_update_nav_menu_item($menu_id, 0, array(
            'menu-item-title' => $item['title'],
            'menu-item-url' => $item['url'],
            'menu-item-status' => 'publish',
            'menu-item-position' => $index + 1
        ));
    }

    // Assign menu to theme location
    $locations = get_theme_mod('nav_menu_locations');
    $locations['primary'] = $menu_id;
    set_theme_mod('nav_menu_locations', $locations);

    echo '<p class="success">✓ Added menu items and assigned to primary location</p>';

    // Flush rewrite rules
    flush_rewrite_rules();
    echo '<p class="success">✓ Flushed rewrite rules</p>';

    echo '<hr>';
    echo '<h2>🎉 All Professional Pages Created!</h2>';
    echo '<p><strong>Your professional pages are now available:</strong></p>';
    echo '<ul>';
    foreach ($pages_to_create as $slug => $page_data) {
        $url = home_url('/' . $slug . '/');
        echo '<li><a href="' . $url . '" target="_blank">' . $page_data['title'] . '</a></li>';
    }
    echo '</ul>';

    echo '<p class="warning"><strong>Next:</strong> Visit each page to see the professional design in action!</p>';
    echo '<p style="color: red;"><strong>Security:</strong> Please delete this file after use: ' . __FILE__ . '</p>';
    ?>

</body>
</html>