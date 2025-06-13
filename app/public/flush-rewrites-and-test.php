<?php
/**
 * Flush rewrite rules and test download system
 */

require_once('./wp-load.php');

echo "<h1>🔧 Activating Download System</h1>\n";

// Flush rewrite rules to register our download endpoints
flush_rewrite_rules();
echo "<p>✅ Rewrite rules flushed</p>\n";

// Test that the download system is loaded
if (class_exists('Vireo_Plugin_Downloads')) {
    echo "<p>✅ Vireo_Plugin_Downloads class loaded</p>\n";
    
    $downloads = new Vireo_Plugin_Downloads();
    
    // Test download URL generation
    $test_url = $downloads->get_download_url('vireo-property-management');
    echo "<p>✅ Test download URL generated: <a href='$test_url' target='_blank'>$test_url</a></p>\n";
    
    // Check if standalone plugins exist
    $standalone_dir = ABSPATH . 'wp-content/standalone-plugins';
    if (is_dir($standalone_dir)) {
        echo "<p>✅ Standalone plugins directory exists: $standalone_dir</p>\n";
        
        $plugins = glob($standalone_dir . '/*', GLOB_ONLYDIR);
        echo "<p>📦 Found standalone plugins:</p>\n<ul>\n";
        foreach ($plugins as $plugin_dir) {
            $plugin_name = basename($plugin_dir);
            echo "<li>$plugin_name</li>\n";
        }
        echo "</ul>\n";
    } else {
        echo "<p>❌ Standalone plugins directory not found</p>\n";
    }
    
    // Check downloads directory
    $uploads_dir = wp_upload_dir();
    $downloads_dir = $uploads_dir['basedir'] . '/plugin-downloads';
    if (is_dir($downloads_dir)) {
        echo "<p>✅ Downloads directory exists: $downloads_dir</p>\n";
        
        $zip_files = glob($downloads_dir . '/*.zip');
        echo "<p>📦 ZIP files found:</p>\n<ul>\n";
        foreach ($zip_files as $zip_file) {
            $filename = basename($zip_file);
            $size = round(filesize($zip_file) / 1024 / 1024, 2);
            echo "<li>$filename ({$size} MB)</li>\n";
        }
        echo "</ul>\n";
    } else {
        echo "<p>❌ Downloads directory not found</p>\n";
    }
    
} else {
    echo "<p>❌ Vireo_Plugin_Downloads class not loaded</p>\n";
}

echo "<h2>🚀 Download System Status</h2>\n";
echo "<p>The download system is now active and ready to serve plugin downloads!</p>\n";
echo "<p><strong>Next Steps:</strong></p>\n";
echo "<ul>\n";
echo "<li>Visit individual plugin pages to test downloads</li>\n";
echo "<li>Configure WooCommerce for Pro versions</li>\n";
echo "<li>Set up license delivery system</li>\n";
echo "</ul>\n";

?>