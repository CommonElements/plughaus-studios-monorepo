<?php
/**
 * Frontend Debug Script
 * Check what's happening with the frontend
 */

// Load WordPress
$wp_root = dirname(dirname(dirname(__FILE__))) . '/app/public';
require_once($wp_root . '/wp-config.php');
require_once($wp_root . '/wp-load.php');

echo "=== FRONTEND DEBUG ===\n";

// Basic WordPress info
echo "WordPress Version: " . get_bloginfo('version') . "\n";
echo "Site URL: " . get_option('siteurl') . "\n";
echo "Home URL: " . get_option('home') . "\n";
echo "Active Theme: " . get_template() . "\n";

// Check if theme files exist
$theme_dir = get_template_directory();
echo "\nTheme Directory: $theme_dir\n";

$theme_files = ['index.php', 'style.css', 'functions.php', 'header.php', 'footer.php'];
foreach ($theme_files as $file) {
    $file_path = $theme_dir . '/' . $file;
    echo ($file_exists = file_exists($file_path) ? "✅" : "❌") . " $file\n";
    
    if ($file === 'functions.php' && $file_exists) {
        // Check for PHP errors in functions.php
        $content = file_get_contents($file_path);
        if (strpos($content, 'Fatal error') !== false || strpos($content, 'Parse error') !== false) {
            echo "   ⚠️  Potential PHP errors detected\n";
        }
    }
}

// Check for active plugins
echo "\nActive Plugins:\n";
$active_plugins = get_option('active_plugins', []);
foreach ($active_plugins as $plugin) {
    echo "✅ $plugin\n";
}

// Check database connectivity
echo "\nDatabase Status:\n";
global $wpdb;
$result = $wpdb->get_var("SELECT 1");
if ($result === '1') {
    echo "✅ Database connection working\n";
} else {
    echo "❌ Database connection failed\n";
}

// Check for recent errors
echo "\nError Log Check:\n";
$error_log_paths = [
    WP_CONTENT_DIR . '/debug.log',
    ini_get('error_log'),
    '/var/log/php-error.log',
    '/tmp/php-errors.log'
];

$found_errors = false;
foreach ($error_log_paths as $log_path) {
    if ($log_path && file_exists($log_path)) {
        echo "Found error log: $log_path\n";
        
        // Check last 20 lines for recent errors
        $lines = file($log_path);
        if ($lines && count($lines) > 0) {
            $recent_lines = array_slice($lines, -20);
            $recent_errors = array_filter($recent_lines, function($line) {
                return (strpos($line, 'Fatal error') !== false || 
                        strpos($line, 'Parse error') !== false ||
                        strpos($line, 'Warning') !== false ||
                        strpos($line, 'Notice') !== false) &&
                       (time() - strtotime(substr($line, 1, 20))) < 3600; // Last hour
            });
            
            if (!empty($recent_errors)) {
                echo "⚠️  Recent errors found:\n";
                foreach ($recent_errors as $error) {
                    echo "   " . trim($error) . "\n";
                }
                $found_errors = true;
            }
        }
    }
}

if (!$found_errors) {
    echo "✅ No recent errors found\n";
}

// Test a simple frontend request
echo "\nFrontend Request Test:\n";
$home_url = get_option('home');
if ($home_url) {
    echo "Testing: $home_url\n";
    
    // Simple curl test
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $home_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "❌ cURL Error: $error\n";
    } else {
        echo "✅ HTTP Status: $http_code\n";
        if ($response) {
            $response_length = strlen($response);
            echo "✅ Response received: {$response_length} bytes\n";
            
            // Check for common issues
            if (strpos($response, '<title>') !== false) {
                preg_match('/<title>(.*?)<\/title>/i', $response, $matches);
                $title = isset($matches[1]) ? trim($matches[1]) : 'No title';
                echo "✅ Page title: $title\n";
            } else {
                echo "⚠️  No title tag found\n";
            }
            
            if (strpos($response, 'Fatal error') !== false) {
                echo "❌ Fatal error detected in response\n";
            }
        } else {
            echo "⚠️  Empty response\n";
        }
    }
} else {
    echo "❌ No home URL configured\n";
}

// Check .htaccess
echo "\n.htaccess Check:\n";
$htaccess_path = ABSPATH . '.htaccess';
if (file_exists($htaccess_path)) {
    echo "✅ .htaccess exists\n";
    $htaccess_content = file_get_contents($htaccess_path);
    if (strpos($htaccess_content, 'RewriteEngine') !== false) {
        echo "✅ URL rewriting enabled\n";
    } else {
        echo "⚠️  URL rewriting may not be configured\n";
    }
} else {
    echo "⚠️  .htaccess file not found\n";
}

echo "\n=== DEBUG COMPLETE ===\n";
?>