<?php
/**
 * Fix 404 Asset Issues
 */

// Load WordPress
$wp_root = dirname(dirname(dirname(__FILE__))) . '/app/public';
require_once($wp_root . '/wp-config.php');
require_once($wp_root . '/wp-load.php');

echo "=== FIXING 404 ASSET ISSUES ===\n";

// Check what assets are being enqueued
echo "Currently enqueued styles:\n";
global $wp_styles;
if ($wp_styles) {
    foreach ($wp_styles->registered as $handle => $style) {
        if (strpos($style->src, 'vireo') !== false || strpos($style->src, 'frontend') !== false || strpos($style->src, 'public') !== false) {
            echo "  $handle: {$style->src}\n";
            
            // Check if file exists
            $file_path = str_replace(get_site_url(), ABSPATH, $style->src);
            $exists = file_exists($file_path);
            echo "    File exists: " . ($exists ? "✅" : "❌") . "\n";
            if (!$exists) {
                echo "    Expected path: $file_path\n";
            }
        }
    }
}

echo "\nCurrently enqueued scripts:\n";
global $wp_scripts;
if ($wp_scripts) {
    foreach ($wp_scripts->registered as $handle => $script) {
        if (strpos($script->src, 'vireo') !== false || strpos($script->src, 'frontend') !== false || strpos($script->src, 'public') !== false) {
            echo "  $handle: {$script->src}\n";
            
            // Check if file exists
            $file_path = str_replace(get_site_url(), ABSPATH, $script->src);
            $exists = file_exists($file_path);
            echo "    File exists: " . ($exists ? "✅" : "❌") . "\n";
            if (!$exists) {
                echo "    Expected path: $file_path\n";
            }
        }
    }
}

// Check theme directory for actual assets
echo "\nChecking theme assets:\n";
$theme_dir = get_template_directory();
$theme_url = get_template_directory_uri();

$asset_files = [
    'style.css',
    'assets/dist/css/main.css',
    'assets/dist/css/components.css',
    'assets/dist/css/admin-styles.css',
    'assets/dist/js/theme.js',
    'assets/dist/js/ui-enhancements.js',
    'assets/dist/js/admin.js',
    'assets/css/frontend.css',
    'assets/css/public.css',
    'assets/js/frontend.js',
    'assets/js/public.js'
];

foreach ($asset_files as $asset) {
    $file_path = $theme_dir . '/' . $asset;
    $exists = file_exists($file_path);
    echo ($exists ? "✅" : "❌") . " $asset\n";
    
    if (!$exists && (strpos($asset, 'frontend') !== false || strpos($asset, 'public') !== false)) {
        echo "    Missing: $asset - this might be causing 404s\n";
    }
}

// Check for missing pages
echo "\nChecking for missing pages:\n";
$pages_to_check = [
    'industries',
    'plugins', 
    'pricing',
    'about',
    'contact',
    'support'
];

foreach ($pages_to_check as $page_slug) {
    $page = get_page_by_path($page_slug);
    if ($page) {
        echo "✅ /$page_slug (ID: {$page->ID})\n";
    } else {
        echo "❌ /$page_slug - missing\n";
        
        // Create the missing page
        $page_data = [
            'post_title' => ucwords(str_replace('-', ' ', $page_slug)),
            'post_content' => "This is the $page_slug page.",
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_name' => $page_slug
        ];
        
        $page_id = wp_insert_post($page_data);
        if ($page_id) {
            echo "    ✅ Created page: $page_slug (ID: $page_id)\n";
        } else {
            echo "    ❌ Failed to create page: $page_slug\n";
        }
    }
}

// Check functions.php for problematic asset enqueuing
echo "\nChecking functions.php asset enqueuing:\n";
$functions_file = $theme_dir . '/functions.php';
if (file_exists($functions_file)) {
    $content = file_get_contents($functions_file);
    
    // Look for references to missing files
    if (strpos($content, 'frontend.css') !== false) {
        echo "⚠️  functions.php references frontend.css\n";
    }
    if (strpos($content, 'public.css') !== false) {
        echo "⚠️  functions.php references public.css\n";
    }
    if (strpos($content, 'frontend.js') !== false) {
        echo "⚠️  functions.php references frontend.js\n";
    }
    if (strpos($content, 'public.js') !== false) {
        echo "⚠️  functions.php references public.js\n";
    }
}

// Check plugin asset enqueuing
echo "\nChecking plugin asset enqueuing:\n";
$active_plugins = get_option('active_plugins', []);
foreach ($active_plugins as $plugin) {
    if (strpos($plugin, 'vireo') !== false) {
        echo "Checking plugin: $plugin\n";
        
        $plugin_dir = WP_PLUGIN_DIR . '/' . dirname($plugin);
        $potential_files = [
            $plugin_dir . '/assets/css/frontend.css',
            $plugin_dir . '/assets/css/public.css',
            $plugin_dir . '/assets/js/frontend.js',
            $plugin_dir . '/assets/js/public.js'
        ];
        
        foreach ($potential_files as $file) {
            if (file_exists($file)) {
                echo "  ✅ " . basename($file) . "\n";
            } else {
                echo "  ❌ " . basename($file) . " (might be causing 404)\n";
            }
        }
    }
}

// Flush rewrite rules to fix page 404s
echo "\nFlushing rewrite rules...\n";
flush_rewrite_rules();
echo "✅ Rewrite rules flushed\n";

echo "\n=== FIX COMPLETE ===\n";
echo "The 404 errors should be reduced now.\n";
echo "Missing asset files have been identified and missing pages created.\n";
?>