<?php
/**
 * Frontend Fix Script
 */

// Load WordPress
$wp_root = dirname(dirname(dirname(__FILE__))) . '/app/public';
require_once($wp_root . '/wp-config.php');
require_once($wp_root . '/wp-load.php');

echo "=== FRONTEND FIX SCRIPT ===\n";

// Check current homepage setup
$show_on_front = get_option('show_on_front');
$page_on_front = get_option('page_on_front');

echo "Current front page setting: $show_on_front\n";
echo "Front page ID: $page_on_front\n";

if ($page_on_front) {
    $front_page = get_post($page_on_front);
    if ($front_page) {
        echo "Front page: {$front_page->post_title}\n";
        echo "Front page content length: " . strlen($front_page->post_content) . " chars\n";
        
        // Show first 200 chars of content
        echo "Content preview: " . substr(strip_tags($front_page->post_content), 0, 200) . "...\n";
    } else {
        echo "❌ Front page post not found!\n";
        echo "Setting homepage to show latest posts instead...\n";
        update_option('show_on_front', 'posts');
        echo "✅ Changed to show latest posts\n";
    }
}

// Check for template file
echo "\nTemplate Check:\n";
$template_dir = get_template_directory();

if ($show_on_front === 'page' && $page_on_front) {
    // Check for page template
    $page_template = get_page_template_slug($page_on_front);
    echo "Page template: " . ($page_template ? $page_template : 'default') . "\n";
    
    // Check if page-home.php exists
    $home_template = $template_dir . '/page-home.php';
    if (file_exists($home_template)) {
        echo "✅ page-home.php template exists\n";
    } else {
        echo "⚠️  page-home.php template not found\n";
    }
    
    // Check if front-page.php exists
    $front_page_template = $template_dir . '/front-page.php';
    if (file_exists($front_page_template)) {
        echo "✅ front-page.php template exists\n";
    } else {
        echo "⚠️  front-page.php template not found\n";
    }
} else {
    // Check for index.php
    $index_template = $template_dir . '/index.php';
    if (file_exists($index_template)) {
        echo "✅ index.php template exists\n";
    } else {
        echo "❌ index.php template not found!\n";
    }
}

// Temporarily disable problematic plugins
echo "\nPlugin Management:\n";
$active_plugins = get_option('active_plugins', []);
$problematic_plugins = [
    'jetpack/jetpack.php',
    'google-listings-and-ads/google-listings-and-ads.php'
];

$plugins_to_disable = [];
foreach ($problematic_plugins as $plugin) {
    if (in_array($plugin, $active_plugins)) {
        $plugins_to_disable[] = $plugin;
        echo "Found problematic plugin: $plugin\n";
    }
}

if (!empty($plugins_to_disable)) {
    echo "Temporarily disabling problematic plugins...\n";
    $remaining_plugins = array_diff($active_plugins, $plugins_to_disable);
    update_option('active_plugins', $remaining_plugins);
    echo "✅ Disabled " . count($plugins_to_disable) . " plugins\n";
    
    // Store disabled plugins for later re-enabling
    update_option('vd_disabled_plugins', $plugins_to_disable);
} else {
    echo "No problematic plugins found\n";
}

// Check WordPress URLs
echo "\nURL Configuration:\n";
$site_url = get_option('siteurl');
$home_url = get_option('home');

echo "Site URL: $site_url\n";
echo "Home URL: $home_url\n";

// Force a simple homepage for testing
echo "\nTesting Simple Homepage:\n";
echo "Setting homepage to show latest posts...\n";
update_option('show_on_front', 'posts');
echo "✅ Homepage set to show posts\n";

// Clear any caches
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
    echo "✅ Cache cleared\n";
}

// Test the frontend again
echo "\nTesting Frontend Response:\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $home_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: $http_code\n";
echo "Response length: " . strlen($response) . " bytes\n";

if (strpos($response, 'Fatal error') !== false) {
    echo "❌ Fatal error still present\n";
} else {
    echo "✅ No fatal errors detected\n";
}

if (strpos($response, 'site-main') !== false) {
    echo "✅ Main content structure found\n";
} else {
    echo "❌ Main content structure missing\n";
}

echo "\n=== FIX COMPLETE ===\n";
echo "Try accessing the frontend now: $home_url\n";

if (!empty($plugins_to_disable)) {
    echo "\nNote: Disabled plugins temporarily. To re-enable:\n";
    foreach ($plugins_to_disable as $plugin) {
        echo "  - $plugin\n";
    }
}
?>