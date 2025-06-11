<?php
/**
 * Simple Frontend Test
 * Get the actual HTML output to see what's happening
 */

// Load WordPress
$wp_root = dirname(dirname(dirname(__FILE__))) . '/app/public';
require_once($wp_root . '/wp-config.php');
require_once($wp_root . '/wp-load.php');

echo "=== SIMPLE FRONTEND TEST ===\n";
echo "Home URL: " . get_option('home') . "\n";

// Get the homepage content
$home_url = get_option('home');

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $home_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36');

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Status: $http_code\n";

if ($error) {
    echo "cURL Error: $error\n";
    exit(1);
}

if (!$response) {
    echo "No response received\n";
    exit(1);
}

echo "Response length: " . strlen($response) . " bytes\n";

// Extract key information
if (preg_match('/<title>(.*?)<\/title>/i', $response, $matches)) {
    echo "Page title: " . trim($matches[1]) . "\n";
} else {
    echo "❌ No title found\n";
}

// Check for errors in the HTML
if (strpos($response, 'Fatal error') !== false) {
    echo "❌ Fatal error found in response\n";
    if (preg_match('/Fatal error:.*?<\/b>/i', $response, $matches)) {
        echo "Error: " . strip_tags($matches[0]) . "\n";
    }
}

if (strpos($response, 'Warning:') !== false) {
    echo "⚠️  PHP warnings found\n";
}

if (strpos($response, 'Notice:') !== false) {
    echo "⚠️  PHP notices found\n";
}

// Check if theme is loading
if (strpos($response, 'vireo-designs') !== false) {
    echo "✅ Vireo theme detected\n";
} else {
    echo "❌ Vireo theme not detected\n";
}

// Check for CSS
if (preg_match_all('/<link[^>]*stylesheet[^>]*>/i', $response, $matches)) {
    echo "CSS files: " . count($matches[0]) . "\n";
    foreach ($matches[0] as $css) {
        if (strpos($css, 'vireo') !== false || strpos($css, 'main.css') !== false) {
            echo "  ✅ Vireo CSS found\n";
            break;
        }
    }
} else {
    echo "❌ No CSS found\n";
}

// Check for JavaScript
if (preg_match_all('/<script[^>]*>/i', $response, $matches)) {
    echo "Script tags: " . count($matches[0]) . "\n";
} else {
    echo "❌ No JavaScript found\n";
}

// Check if content is showing
if (strpos($response, 'container') !== false && strpos($response, 'site-main') !== false) {
    echo "✅ Main content structure found\n";
} else {
    echo "❌ Main content structure not found\n";
}

// Check for WooCommerce
if (strpos($response, 'woocommerce') !== false) {
    echo "✅ WooCommerce detected\n";
} else {
    echo "ℹ️  WooCommerce not detected on homepage\n";
}

// Save first 2000 chars to file for inspection
$sample_file = __DIR__ . '/frontend-sample.html';
file_put_contents($sample_file, substr($response, 0, 2000));
echo "\nFirst 2000 chars saved to: $sample_file\n";

echo "\n=== TEST COMPLETE ===\n";
?>