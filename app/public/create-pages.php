<?php
/**
 * Create Missing WordPress Pages
 * Run this once to create all the pages needed for PlugHaus Studios
 */

// Include WordPress
require_once 'wp-config.php';
require_once 'wp-load.php';

// Define pages to create
$pages_to_create = array(
    'plugins' => array(
        'title' => 'Our Plugins',
        'content' => 'Discover our collection of professional WordPress plugins.',
        'template' => 'page-plugins.php'
    ),
    'plugin-directory' => array(
        'title' => 'Plugin Directory',
        'content' => 'Browse all available plugins with search and filtering.',
        'template' => 'page-plugin-directory.php'
    ),
    'plugin/property-management' => array(
        'title' => 'Property Management Pro',
        'content' => 'Complete property management solution for WordPress.',
        'template' => 'page-plugin-property-management.php'
    ),
    'features' => array(
        'title' => 'Features',
        'content' => 'Discover the powerful features in our plugins.',
        'template' => 'page-features.php'
    ),
    'pricing' => array(
        'title' => 'Pricing',
        'content' => 'Simple, transparent pricing for all our plugins.',
        'template' => 'page-pricing.php'
    ),
    'about' => array(
        'title' => 'About Us',
        'content' => 'Learn about PlugHaus Studios and our mission.',
        'template' => 'page-about.php'
    ),
    'contact' => array(
        'title' => 'Contact',
        'content' => 'Get in touch with our team.',
        'template' => 'page-contact.php'
    ),
    'support' => array(
        'title' => 'Support',
        'content' => 'Get help with our plugins and services.',
        'template' => 'page-support.php'
    ),
    'blog' => array(
        'title' => 'Blog',
        'content' => 'Stay updated with the latest news and tutorials.',
        'template' => 'page-blog.php'
    )
);

echo "<h2>Creating WordPress Pages for PlugHaus Studios</h2>\n";

foreach ($pages_to_create as $slug => $page_data) {
    // Check if page already exists
    $existing_page = get_page_by_path($slug);
    
    if ($existing_page) {
        echo "<p>✓ Page '{$page_data['title']}' already exists (/{$slug}/)</p>\n";
        
        // Update template if needed
        $current_template = get_post_meta($existing_page->ID, '_wp_page_template', true);
        if ($current_template !== $page_data['template']) {
            update_post_meta($existing_page->ID, '_wp_page_template', $page_data['template']);
            echo "<p>  → Updated template to {$page_data['template']}</p>\n";
        }
        continue;
    }
    
    // Create the page
    $page_id = wp_insert_post(array(
        'post_title' => $page_data['title'],
        'post_content' => $page_data['content'],
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_name' => $slug,
        'comment_status' => 'closed',
        'ping_status' => 'closed'
    ));
    
    if ($page_id && !is_wp_error($page_id)) {
        // Set the page template
        update_post_meta($page_id, '_wp_page_template', $page_data['template']);
        
        echo "<p>✓ Created page '{$page_data['title']}' (/{$slug}/) with template {$page_data['template']}</p>\n";
    } else {
        echo "<p>✗ Failed to create page '{$page_data['title']}'</p>\n";
    }
}

echo "<h3>Navigation Menu Update</h3>\n";

// Update navigation menu if it exists
$menu_name = 'Primary Menu';
$menu = wp_get_nav_menu_object($menu_name);

if (!$menu) {
    // Create menu if it doesn't exist
    $menu_id = wp_create_nav_menu($menu_name);
    echo "<p>✓ Created navigation menu: {$menu_name}</p>\n";
    
    // Add menu items
    $menu_items = array(
        array('title' => 'Home', 'url' => home_url('/')),
        array('title' => 'Plugins', 'url' => home_url('/plugins/')),
        array('title' => 'Browse All', 'url' => home_url('/plugin-directory/')),
        array('title' => 'Features', 'url' => home_url('/features/')),
        array('title' => 'Pricing', 'url' => home_url('/pricing/')),
        array('title' => 'About', 'url' => home_url('/about/')),
        array('title' => 'Blog', 'url' => home_url('/blog/')),
        array('title' => 'Support', 'url' => home_url('/support/')),
        array('title' => 'Contact', 'url' => home_url('/contact/'))
    );
    
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
    
    echo "<p>✓ Added menu items and assigned to primary location</p>\n";
} else {
    echo "<p>✓ Navigation menu already exists</p>\n";
}

// Flush rewrite rules to ensure clean URLs work
flush_rewrite_rules();
echo "<p>✓ Flushed rewrite rules</p>\n";

echo "<h3>Setup Complete!</h3>\n";
echo "<p><strong>You can now visit:</strong></p>\n";
echo "<ul>\n";
foreach ($pages_to_create as $slug => $page_data) {
    echo "<li><a href='" . home_url('/' . $slug . '/') . "' target='_blank'>{$page_data['title']}</a></li>\n";
}
echo "</ul>\n";

echo "<p><em>After visiting the pages, you can safely delete this file (create-pages.php).</em></p>\n";
?>