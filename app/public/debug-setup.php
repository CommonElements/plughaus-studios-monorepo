<?php
/**
 * Debug Setup Script
 * This will help us identify what's wrong with the setup
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo '<h1>PlugHaus Studios Debug Setup</h1>';

// Check if wp-load.php exists
$wp_load_path = dirname(__FILE__) . '/wp-load.php';
echo '<h2>File System Check</h2>';
echo '<p>WP Load Path: ' . $wp_load_path . '</p>';
echo '<p>WP Load Exists: ' . (file_exists($wp_load_path) ? 'YES' : 'NO') . '</p>';

if (!file_exists($wp_load_path)) {
    echo '<p style="color: red;">wp-load.php not found! Make sure this script is in the WordPress root directory.</p>';
    exit;
}

// Try to load WordPress
echo '<p>Attempting to load WordPress...</p>';
try {
    require_once $wp_load_path;
    echo '<p style="color: green;">✓ WordPress loaded successfully!</p>';
} catch (Exception $e) {
    echo '<p style="color: red;">✗ Error loading WordPress: ' . $e->getMessage() . '</p>';
    exit;
}

// Check if we're logged in
echo '<h2>User Authentication Check</h2>';
if (is_user_logged_in()) {
    $current_user = wp_get_current_user();
    echo '<p style="color: green;">✓ User is logged in: ' . $current_user->user_login . '</p>';
    
    if (current_user_can('manage_options')) {
        echo '<p style="color: green;">✓ User has admin permissions</p>';
    } else {
        echo '<p style="color: red;">✗ User does not have admin permissions</p>';
        echo '<p>Please log in as an administrator first: <a href="' . wp_login_url() . '">Login</a></p>';
        exit;
    }
} else {
    echo '<p style="color: red;">✗ User is not logged in</p>';
    echo '<p>Please log in as an administrator first: <a href="' . wp_login_url() . '">Login</a></p>';
    exit;
}

// Check if theme exists
echo '<h2>Theme Check</h2>';
$theme = wp_get_theme('plughaus-studios');
if ($theme->exists()) {
    echo '<p style="color: green;">✓ PlugHaus Studios theme found</p>';
    echo '<p>Theme Name: ' . $theme->get('Name') . '</p>';
    echo '<p>Theme Version: ' . $theme->get('Version') . '</p>';
    echo '<p>Theme Directory: ' . $theme->get_stylesheet_directory() . '</p>';
} else {
    echo '<p style="color: red;">✗ PlugHaus Studios theme not found</p>';
    
    // List available themes
    $themes = wp_get_themes();
    echo '<p>Available themes:</p><ul>';
    foreach ($themes as $theme_slug => $theme_obj) {
        echo '<li>' . $theme_slug . ' - ' . $theme_obj->get('Name') . '</li>';
    }
    echo '</ul>';
    exit;
}

// Check if custom post types are registered
echo '<h2>Custom Post Type Check</h2>';
if (post_type_exists('phstudios_plugin')) {
    echo '<p style="color: green;">✓ Plugin post type is registered</p>';
} else {
    echo '<p style="color: orange;">⚠ Plugin post type not registered (this is expected if theme just activated)</p>';
}

// Check if taxonomies are registered
echo '<h2>Custom Taxonomy Check</h2>';
if (taxonomy_exists('plugin_category')) {
    echo '<p style="color: green;">✓ Plugin category taxonomy is registered</p>';
} else {
    echo '<p style="color: orange;">⚠ Plugin category taxonomy not registered (this is expected if theme just activated)</p>';
}

// Now let's try to run the actual setup
echo '<h2>Running Setup Process</h2>';

// Force theme functions to load
if (function_exists('plughaus_studios_register_post_types')) {
    echo '<p style="color: green;">✓ Theme functions are loaded</p>';
} else {
    echo '<p style="color: orange;">⚠ Theme functions not loaded, attempting to load...</p>';
    
    // Try to include functions.php
    $functions_path = get_stylesheet_directory() . '/functions.php';
    if (file_exists($functions_path)) {
        include_once $functions_path;
        echo '<p style="color: green;">✓ Functions.php loaded manually</p>';
    } else {
        echo '<p style="color: red;">✗ Functions.php not found at: ' . $functions_path . '</p>';
    }
}

// Register post types manually if needed
if (!post_type_exists('phstudios_plugin')) {
    echo '<p>Registering post types...</p>';
    if (function_exists('plughaus_studios_register_post_types')) {
        plughaus_studios_register_post_types();
        echo '<p style="color: green;">✓ Post types registered</p>';
    } else {
        echo '<p style="color: red;">✗ Could not register post types</p>';
    }
}

// Register taxonomies manually if needed
if (!taxonomy_exists('plugin_category')) {
    echo '<p>Registering taxonomies...</p>';
    if (function_exists('plughaus_studios_register_taxonomies')) {
        plughaus_studios_register_taxonomies();
        echo '<p style="color: green;">✓ Taxonomies registered</p>';
    } else {
        echo '<p style="color: red;">✗ Could not register taxonomies</p>';
    }
}

// Create one test plugin to see if it works
echo '<h2>Creating Test Plugin</h2>';

$test_plugin = array(
    'post_title' => 'Test Plugin',
    'post_content' => 'This is a test plugin to verify the setup is working.',
    'post_excerpt' => 'Test plugin excerpt',
    'post_status' => 'publish',
    'post_type' => 'phstudios_plugin',
    'post_author' => get_current_user_id()
);

$plugin_id = wp_insert_post($test_plugin);

if ($plugin_id && !is_wp_error($plugin_id)) {
    echo '<p style="color: green;">✓ Successfully created test plugin with ID: ' . $plugin_id . '</p>';
    
    // Add some meta data
    update_post_meta($plugin_id, '_plugin_status', 'available');
    update_post_meta($plugin_id, '_plugin_version', '1.0.0');
    echo '<p style="color: green;">✓ Added meta data to test plugin</p>';
    
} else {
    if (is_wp_error($plugin_id)) {
        echo '<p style="color: red;">✗ Error creating test plugin: ' . $plugin_id->get_error_message() . '</p>';
    } else {
        echo '<p style="color: red;">✗ Failed to create test plugin (unknown error)</p>';
    }
}

// Test shortcode
echo '<h2>Testing Shortcode</h2>';
if (shortcode_exists('plugin_showcase')) {
    echo '<p style="color: green;">✓ Plugin showcase shortcode is registered</p>';
    
    // Try to render it
    $shortcode_output = do_shortcode('[plugin_showcase count="1"]');
    if (!empty($shortcode_output)) {
        echo '<p style="color: green;">✓ Shortcode rendered successfully</p>';
        echo '<div style="border: 1px solid #ccc; padding: 10px; margin: 10px 0;"><strong>Shortcode Output:</strong><br>' . $shortcode_output . '</div>';
    } else {
        echo '<p style="color: orange;">⚠ Shortcode rendered but output is empty</p>';
    }
} else {
    echo '<p style="color: red;">✗ Plugin showcase shortcode not registered</p>';
}

echo '<h2>Database Check</h2>';
global $wpdb;
echo '<p>WordPress Database Prefix: ' . $wpdb->prefix . '</p>';

// Check if we can query posts
$posts_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'phstudios_plugin'");
echo '<p>Plugin posts in database: ' . $posts_count . '</p>';

echo '<h2>Next Steps</h2>';
echo '<p>If everything above looks good, you can now run the full setup script:</p>';
echo '<p><a href="setup-theme.php" style="background: #2271b1; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">Run Full Setup</a></p>';

echo '<style>
body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
h1, h2 { color: #2271b1; }
p { margin: 8px 0; }
ul { margin-left: 20px; }
</style>';
?>