<?php
/**
 * WordPress Configuration Check
 */

// Load WordPress
$wp_root = dirname(dirname(dirname(__FILE__))) . '/app/public';
require_once($wp_root . '/wp-config.php');
require_once($wp_root . '/wp-load.php');

echo "=== WORDPRESS CONFIGURATION CHECK ===\n";

// Basic WordPress settings
echo "Site URL: " . get_option('siteurl') . "\n";
echo "Home URL: " . get_option('home') . "\n";
echo "WordPress URL: " . wp_guess_url() . "\n";
echo "Active Theme: " . get_template() . "\n";
echo "Child Theme: " . (get_template() !== get_stylesheet() ? get_stylesheet() : 'None') . "\n";

// Check if front page is set
$show_on_front = get_option('show_on_front');
$page_on_front = get_option('page_on_front');
$page_for_posts = get_option('page_for_posts');

echo "\nFront Page Settings:\n";
echo "Show on front: $show_on_front\n";
if ($show_on_front === 'page') {
    echo "Front page ID: $page_on_front\n";
    if ($page_on_front) {
        $front_page = get_post($page_on_front);
        echo "Front page title: " . ($front_page ? $front_page->post_title : 'Not found') . "\n";
        echo "Front page status: " . ($front_page ? $front_page->post_status : 'N/A') . "\n";
    }
}

// Check for pages
echo "\nPages Check:\n";
$pages = get_pages(['post_status' => 'publish']);
echo "Total published pages: " . count($pages) . "\n";

if (empty($pages)) {
    echo "⚠️  No published pages found\n";
} else {
    echo "Sample pages:\n";
    foreach (array_slice($pages, 0, 5) as $page) {
        echo "  - {$page->post_title} (ID: {$page->ID})\n";
    }
}

// Check for posts
echo "\nPosts Check:\n";
$posts = get_posts(['post_status' => 'publish', 'numberposts' => 5]);
echo "Recent published posts: " . count($posts) . "\n";

if (empty($posts)) {
    echo "⚠️  No published posts found\n";
} else {
    foreach ($posts as $post) {
        echo "  - {$post->post_title} (ID: {$post->ID})\n";
    }
}

// Check permalink structure
echo "\nPermalink Structure:\n";
$permalink_structure = get_option('permalink_structure');
echo "Permalink structure: " . ($permalink_structure ? $permalink_structure : 'Default') . "\n";

// Check if .htaccess is writable
$htaccess_path = ABSPATH . '.htaccess';
if (file_exists($htaccess_path)) {
    echo ".htaccess exists: ✅\n";
    echo ".htaccess writable: " . (is_writable($htaccess_path) ? "✅" : "❌") . "\n";
} else {
    echo ".htaccess missing: ⚠️\n";
}

// Check for maintenance mode
if (file_exists(ABSPATH . '.maintenance')) {
    echo "⚠️  MAINTENANCE MODE ACTIVE\n";
}

// Check debug settings
echo "\nDebug Settings:\n";
echo "WP_DEBUG: " . (defined('WP_DEBUG') && WP_DEBUG ? 'ON' : 'OFF') . "\n";
echo "WP_DEBUG_LOG: " . (defined('WP_DEBUG_LOG') && WP_DEBUG_LOG ? 'ON' : 'OFF') . "\n";
echo "WP_DEBUG_DISPLAY: " . (defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY ? 'ON' : 'OFF') . "\n";

// Check plugin conflicts
echo "\nPlugin Status:\n";
$active_plugins = get_option('active_plugins', []);
echo "Active plugins: " . count($active_plugins) . "\n";

// Check for known problematic plugins
$problematic_plugins = ['jetpack/jetpack.php'];
foreach ($problematic_plugins as $plugin) {
    if (in_array($plugin, $active_plugins)) {
        echo "⚠️  Potentially problematic plugin active: $plugin\n";
    }
}

// Memory and timeout
echo "\nSystem Resources:\n";
echo "Memory limit: " . ini_get('memory_limit') . "\n";
echo "Max execution time: " . ini_get('max_execution_time') . "s\n";
echo "Current memory usage: " . round(memory_get_usage() / 1024 / 1024, 2) . "MB\n";

echo "\n=== CONFIGURATION CHECK COMPLETE ===\n";
?>