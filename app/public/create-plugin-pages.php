<?php
/**
 * Direct Page Creator for PlugHaus Studios
 * Visit this file in your browser to create pages
 */

// Bootstrap WordPress
require_once('wp-config.php');
require_once('wp-load.php');

if (!current_user_can('administrator')) {
    die('You must be logged in as an administrator to run this script.');
}

echo "<style>body { font-family: Arial, sans-serif; margin: 40px; } .success { color: green; } .error { color: red; } .exists { color: orange; }</style>";
echo "<h1>PlugHaus Studios Page Creator</h1>";

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
    'plugin-property-management' => array(
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

foreach ($pages_to_create as $slug => $page_data) {
    // Check if page already exists
    $existing_page = get_page_by_path($slug);
    
    if ($existing_page) {
        echo "<p class='exists'>✓ Page '{$page_data['title']}' already exists (/{$slug}/)</p>";
        
        // Update template if needed
        $current_template = get_post_meta($existing_page->ID, '_wp_page_template', true);
        if ($current_template !== $page_data['template']) {
            update_post_meta($existing_page->ID, '_wp_page_template', $page_data['template']);
            echo "<p class='success'>  → Updated template to {$page_data['template']}</p>";
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
        
        echo "<p class='success'>✓ Created page '{$page_data['title']}' (ID: $page_id) with template {$page_data['template']}</p>";
    } else {
        echo "<p class='error'>✗ Failed to create page '{$page_data['title']}'</p>";
        if (is_wp_error($page_id)) {
            echo "<p class='error'>Error: " . $page_id->get_error_message() . "</p>";
        }
    }
}

// Flush rewrite rules to ensure clean URLs work
flush_rewrite_rules();
echo "<p class='success'>✓ Flushed rewrite rules</p>";

echo "<h2>Test Your Pages:</h2>";
echo "<ul>";
foreach ($pages_to_create as $slug => $page_data) {
    $url = home_url('/' . $slug . '/');
    echo "<li><a href='$url' target='_blank'>{$page_data['title']}</a> - $url</li>";
}
echo "</ul>";

echo "<p><strong>Done!</strong> You can now visit your plugin pages. Delete this file (create-plugin-pages.php) when finished.</p>";
?>