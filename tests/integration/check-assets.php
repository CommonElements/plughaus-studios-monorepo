<?php
/**
 * Check Frontend Assets Loading
 */

// Load WordPress
$wp_root = dirname(dirname(dirname(__FILE__))) . '/app/public';
require_once($wp_root . '/wp-config.php');
require_once($wp_root . '/wp-load.php');

echo "=== FRONTEND ASSETS CHECK ===\n";

// Get home URL
$home_url = get_option('home');
echo "Home URL: $home_url\n";

// Check if this looks like a Local by Flywheel URL
if (strpos($home_url, 'Vireo') !== false && strpos($home_url, '.local') === false) {
    echo "⚠️  URL looks incorrect for Local by Flywheel\n";
    echo "Expected format: http://site-name.local\n";
    
    // Show what the Local URL probably should be
    $site_name = basename(dirname(dirname(dirname(__FILE__))));
    $expected_url = "http://{$site_name}.local";
    echo "Try: $expected_url\n";
}

// Check theme directory and assets
$theme_dir = get_template_directory();
$theme_url = get_template_directory_uri();
echo "\nTheme Directory: $theme_dir\n";
echo "Theme URL: $theme_url\n";

// Check for compiled assets
$asset_paths = [
    'assets/dist/css/main.css',
    'assets/dist/js/theme.js',
    'assets/src/scss/main.scss',
    'style.css'
];

echo "\nAsset Files:\n";
foreach ($asset_paths as $asset) {
    $file_path = $theme_dir . '/' . $asset;
    $exists = file_exists($file_path);
    echo ($exists ? "✅" : "❌") . " $asset";
    if ($exists) {
        $size = filesize($file_path);
        echo " ({$size} bytes)";
    }
    echo "\n";
}

// Check functions.php for asset enqueuing
echo "\nChecking functions.php for asset enqueuing:\n";
$functions_file = $theme_dir . '/functions.php';
if (file_exists($functions_file)) {
    $content = file_get_contents($functions_file);
    
    if (strpos($content, 'wp_enqueue_style') !== false) {
        echo "✅ CSS enqueuing found\n";
    } else {
        echo "⚠️  No CSS enqueuing found\n";
    }
    
    if (strpos($content, 'wp_enqueue_script') !== false) {
        echo "✅ JS enqueuing found\n";
    } else {
        echo "⚠️  No JS enqueuing found\n";
    }
    
    // Check for compilation issues
    if (strpos($content, 'vireo_designs_scripts') !== false) {
        echo "✅ Vireo theme scripts function found\n";
    } else {
        echo "⚠️  Theme scripts function not found\n";
    }
}

// Test asset URLs
echo "\nTesting Asset URLs:\n";
$test_assets = [
    $theme_url . '/style.css',
    $theme_url . '/assets/dist/css/main.css',
    $theme_url . '/assets/dist/js/theme.js'
];

foreach ($test_assets as $asset_url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $asset_url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $status = ($http_code == 200) ? "✅" : "❌";
    echo "$status $asset_url (HTTP $http_code)\n";
}

// Check for JavaScript errors by looking at the page source
echo "\nChecking for JavaScript issues:\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $home_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
curl_close($ch);

if ($response) {
    // Check for script tags
    $script_count = preg_match_all('/<script[^>]*>/i', $response, $matches);
    echo "Found $script_count script tags\n";
    
    // Check for stylesheet links
    $css_count = preg_match_all('/<link[^>]*stylesheet[^>]*>/i', $response, $matches);
    echo "Found $css_count stylesheet links\n";
    
    // Check for common errors
    if (strpos($response, '404') !== false) {
        echo "⚠️  404 errors may be present\n";
    }
    
    if (strpos($response, 'console.error') !== false) {
        echo "⚠️  JavaScript console errors detected\n";
    }
    
    // Check if Vireo theme is properly loading
    if (strpos($response, 'vireo') !== false || strpos($response, 'Vireo') !== false) {
        echo "✅ Vireo theme elements detected\n";
    } else {
        echo "⚠️  Vireo theme elements not found\n";
    }
}

echo "\n=== ASSETS CHECK COMPLETE ===\n";
?>