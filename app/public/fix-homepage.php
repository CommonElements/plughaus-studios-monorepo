<?php
/**
 * Fix Homepage Display
 * Run this to set the proper homepage template
 */

// Include WordPress
define('WP_USE_THEMES', false);
require_once dirname(__FILE__) . '/wp-load.php';

// Check if user is admin
if (!is_user_logged_in() || !current_user_can('manage_options')) {
    wp_redirect(wp_login_url($_SERVER['REQUEST_URI']));
    exit;
}

echo '<h1>Fixing Homepage Display</h1>';

// 1. Check current front page settings
$show_on_front = get_option('show_on_front');
$page_on_front = get_option('page_on_front');

echo '<h2>Current Settings:</h2>';
echo '<p>Show on front: ' . $show_on_front . '</p>';
echo '<p>Page on front ID: ' . $page_on_front . '</p>';

// 2. Find or create the home page
$home_page = get_page_by_path('home');
if (!$home_page) {
    // Create home page
    $page_data = array(
        'post_title' => 'Home',
        'post_content' => '[plugin_showcase count="6" featured="true"]',
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_name' => 'home',
    );
    
    $page_id = wp_insert_post($page_data);
    
    if ($page_id) {
        // Set the page template
        update_post_meta($page_id, '_wp_page_template', 'page-home.php');
        echo '<p style="color: green;">✓ Created Home page with ID: ' . $page_id . '</p>';
        $home_page_id = $page_id;
    } else {
        echo '<p style="color: red;">✗ Failed to create Home page</p>';
    }
} else {
    $home_page_id = $home_page->ID;
    // Make sure the template is set
    update_post_meta($home_page_id, '_wp_page_template', 'page-home.php');
    echo '<p style="color: blue;">→ Found existing Home page with ID: ' . $home_page_id . '</p>';
}

// 3. Set WordPress to show pages on front
update_option('show_on_front', 'page');
update_option('page_on_front', $home_page_id);

echo '<h2>Updated Settings:</h2>';
echo '<p style="color: green;">✓ Set front page to show pages</p>';
echo '<p style="color: green;">✓ Set Home page (ID: ' . $home_page_id . ') as front page</p>';
echo '<p style="color: green;">✓ Assigned page-home.php template</p>';

// 4. Check template file exists
$template_file = get_template_directory() . '/page-home.php';
if (file_exists($template_file)) {
    echo '<p style="color: green;">✓ Template file exists: page-home.php</p>';
} else {
    echo '<p style="color: red;">✗ Template file missing: page-home.php</p>';
}

// 5. Flush rewrite rules
flush_rewrite_rules();
echo '<p style="color: green;">✓ Flushed rewrite rules</p>';

echo '<hr>';
echo '<h2>🎉 Homepage Fix Complete!</h2>';
echo '<p><strong>Your professional homepage should now be displaying at: <a href="' . home_url() . '" target="_blank">' . home_url() . '</a></strong></p>';
echo '<p>If you still see "Nothing here", try refreshing the page or clearing any caching.</p>';

// Clean up - delete this file after use
echo '<hr>';
echo '<p style="color: orange;"><strong>Security Note:</strong> Please delete this file after use: ' . __FILE__ . '</p>';
?>