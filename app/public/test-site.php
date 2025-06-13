<?php
// Simple test to see if the site is working
echo "WordPress Test Page - " . date('Y-m-d H:i:s');

// Try to load WordPress if possible
if (file_exists('./wp-config.php')) {
    echo "<br>WordPress config found";
    
    try {
        define('WP_USE_THEMES', false);
        require_once('./wp-blog-header.php');
        echo "<br>WordPress loaded successfully";
        echo "<br>Site URL: " . get_site_url();
        echo "<br>Home URL: " . get_home_url();
        
        // Check active plugins
        $active_plugins = get_option('active_plugins', array());
        echo "<br>Active plugins: " . count($active_plugins);
        foreach ($active_plugins as $plugin) {
            echo "<br>- " . $plugin;
        }
        
    } catch (Exception $e) {
        echo "<br>WordPress error: " . $e->getMessage();
    }
} else {
    echo "<br>WordPress config not found";
}
?>