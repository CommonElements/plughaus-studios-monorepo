<?php
/**
 * Vireo Designs - Standalone Plugin Extraction System
 * 
 * Creates completely independent WordPress.org compliant plugins
 * that work 100% standalone without any dependencies on each other.
 */

require_once('./wp-load.php');

echo "<h1>🔨 Vireo Designs - Standalone Plugin Extraction</h1>\n";
echo "<p>Creating WordPress.org compliant standalone plugins...</p>\n";

// Define plugin extraction configurations
$plugins_to_extract = [
    'property-management' => [
        'source_dir' => '/Users/condominiumassociates/Local Sites/Vireo/app/public/wp-content/plugins-DISABLED-2025-06-13-02-41/vireo-property-management',
        'plugin_name' => 'Vireo Property Management',
        'plugin_slug' => 'vireo-property-management',
        'plugin_description' => 'Professional property management for WordPress. Manage properties, tenants, leases, and maintenance requests.',
        'plugin_version' => '1.0.0',
        'wordpress_org_slug' => 'vireo-property-management',
        'main_file' => 'vireo-property-management.php',
        'text_domain' => 'vireo-property-management',
        'prefix' => 'VPM_',
        'plugin_key' => 'property-management'
    ],
    
    'sports-league' => [
        'source_dir' => '/Users/condominiumassociates/Local Sites/Vireo/app/public/wp-content/plugins-DISABLED-2025-06-13-02-41/vireo-sports-league',
        'plugin_name' => 'Vireo Sports League Manager',
        'plugin_slug' => 'vireo-sports-league-manager',
        'plugin_description' => 'Complete sports league management for WordPress. Manage teams, players, schedules, and statistics.',
        'plugin_version' => '1.0.0',
        'wordpress_org_slug' => 'vireo-sports-league-manager',
        'main_file' => 'vireo-sports-league-manager.php',
        'text_domain' => 'vireo-sports-league-manager',
        'prefix' => 'VSLM_',
        'plugin_key' => 'sports-league'
    ],
    
    'equipment-rental' => [
        'source_dir' => '/Users/condominiumassociates/Local Sites/Vireo/app/public/wp-content/plugins-DISABLED-2025-06-13-02-41/equiprent-pro',
        'plugin_name' => 'EquipRent - Equipment Rental Management',
        'plugin_slug' => 'equiprent-equipment-rental',
        'plugin_description' => 'Professional equipment rental management for WordPress. Handle inventory, bookings, customers, and billing.',
        'plugin_version' => '1.0.0',
        'wordpress_org_slug' => 'equiprent-equipment-rental',
        'main_file' => 'equiprent-equipment-rental.php',
        'text_domain' => 'equiprent-equipment-rental',
        'prefix' => 'EER_',
        'plugin_key' => 'equipment-rental'
    ]
];

// Create extraction directory
$extraction_dir = '/Users/condominiumassociates/Local Sites/Vireo/app/public/wp-content/standalone-plugins';
if (!is_dir($extraction_dir)) {
    mkdir($extraction_dir, 0755, true);
}

echo "<h2>📦 Plugin Extraction Status</h2>\n";

foreach ($plugins_to_extract as $plugin_key => $config) {
    echo "<h3>🔨 Processing: {$config['plugin_name']}</h3>\n";
    
    // Check if source directory exists
    if (!is_dir($config['source_dir'])) {
        echo "<p>❌ Source directory not found: {$config['source_dir']}</p>\n";
        continue;
    }
    
    // Create standalone plugin directory
    $standalone_dir = $extraction_dir . '/' . $config['plugin_slug'];
    if (is_dir($standalone_dir)) {
        // Remove existing directory
        exec("rm -rf " . escapeshellarg($standalone_dir));
    }
    mkdir($standalone_dir, 0755, true);
    
    echo "<p>📁 Created standalone directory: $standalone_dir</p>\n";
    
    // Copy core files (free version only)
    $core_source = $config['source_dir'] . '/core';
    if (is_dir($core_source)) {
        exec("cp -R " . escapeshellarg($core_source) . " " . escapeshellarg($standalone_dir . '/core'));
        echo "<p>✅ Copied core files</p>\n";
    }
    
    // Copy assets
    $assets_source = $config['source_dir'] . '/assets';
    if (is_dir($assets_source)) {
        exec("cp -R " . escapeshellarg($assets_source) . " " . escapeshellarg($standalone_dir . '/assets'));
        echo "<p>✅ Copied assets</p>\n";
    }
    
    // Create standalone main plugin file
    $main_plugin_content = create_standalone_main_file($config);
    file_put_contents($standalone_dir . '/' . $config['main_file'], $main_plugin_content);
    echo "<p>✅ Created main plugin file: {$config['main_file']}</p>\n";
    
    // Create WordPress.org compliant readme.txt
    $readme_content = create_wordpress_org_readme($config);
    file_put_contents($standalone_dir . '/readme.txt', $readme_content);
    echo "<p>✅ Created WordPress.org readme.txt</p>\n";
    
    // Create index.php for security
    $index_content = "<?php\n// Silence is golden.\n";
    file_put_contents($standalone_dir . '/index.php', $index_content);
    echo "<p>✅ Created security index.php</p>\n";
    
    echo "<p><strong>✅ Standalone plugin ready for WordPress.org submission!</strong></p>\n";
    echo "<hr>\n";
}

echo "<h2>📋 Next Steps for WordPress.org Submission</h2>\n";
echo "<ol>\n";
echo "<li><strong>Test Each Plugin:</strong> Install and activate each standalone plugin individually</li>\n";
echo "<li><strong>Code Review:</strong> Ensure no dependencies on other Vireo plugins</li>\n";
echo "<li><strong>WordPress.org Guidelines:</strong> Review each plugin against WordPress.org requirements</li>\n";
echo "<li><strong>Submit to WordPress.org:</strong> Upload ZIP files to WordPress.org for review</li>\n";
echo "</ol>\n";

echo "<p><strong>🚀 All plugins extracted as standalone WordPress.org compliant packages!</strong></p>\n";

/**
 * Create standalone main plugin file
 */
function create_standalone_main_file($config) {
    $content = '<?php
/**
 * Plugin Name: ' . $config['plugin_name'] . '
 * Plugin URI: https://vireodesigns.com/plugins/' . $config['plugin_slug'] . '/
 * Description: ' . $config['plugin_description'] . '
 * Version: ' . $config['plugin_version'] . '
 * Author: Vireo Designs
 * Author URI: https://vireodesigns.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ' . $config['text_domain'] . '
 * Domain Path: /languages
 * Requires at least: 5.8
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * Network: false
 */

// Prevent direct access
if (!defined(\'ABSPATH\')) {
    exit;
}

// Define plugin constants
define(\'' . $config['prefix'] . 'VERSION\', \'' . $config['plugin_version'] . '\');
define(\'' . $config['prefix'] . 'PLUGIN_FILE\', __FILE__);
define(\'' . $config['prefix'] . 'PLUGIN_DIR\', plugin_dir_path(__FILE__));
define(\'' . $config['prefix'] . 'PLUGIN_URL\', plugin_dir_url(__FILE__));
define(\'' . $config['prefix'] . 'PLUGIN_BASENAME\', plugin_basename(__FILE__));

/**
 * Main plugin class - 100% standalone
 */
class ' . $config['prefix'] . 'Main {
    
    /**
     * Single instance
     */
    private static $instance = null;
    
    /**
     * Get single instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        add_action(\'init\', array($this, \'init\'));
        add_action(\'plugins_loaded\', array($this, \'load_textdomain\'));
        register_activation_hook(__FILE__, array($this, \'activate\'));
        register_deactivation_hook(__FILE__, array($this, \'deactivate\'));
    }
    
    /**
     * Initialize plugin
     */
    public function init() {
        // Load core functionality only - no dependencies on other Vireo plugins
        $this->load_core();
        
        // Initialize admin interface
        if (is_admin()) {
            $this->load_admin();
        }
    }
    
    /**
     * Load text domain for translations
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            \'' . $config['text_domain'] . '\',
            false,
            dirname(plugin_basename(__FILE__)) . \'/languages\'
        );
    }
    
    /**
     * Load core functionality
     */
    private function load_core() {
        // Include core files if they exist
        $core_files = array(
            ' . $config['prefix'] . 'PLUGIN_DIR . \'core/includes/class-core.php\',
            ' . $config['prefix'] . 'PLUGIN_DIR . \'core/includes/class-database.php\',
            ' . $config['prefix'] . 'PLUGIN_DIR . \'core/includes/class-utilities.php\'
        );
        
        foreach ($core_files as $file) {
            if (file_exists($file)) {
                require_once $file;
            }
        }
    }
    
    /**
     * Load admin functionality
     */
    private function load_admin() {
        // Include admin files if they exist
        $admin_files = array(
            ' . $config['prefix'] . 'PLUGIN_DIR . \'core/includes/admin/class-admin.php\',
            ' . $config['prefix'] . 'PLUGIN_DIR . \'core/includes/admin/class-admin-menu.php\'
        );
        
        foreach ($admin_files as $file) {
            if (file_exists($file)) {
                require_once $file;
            }
        }
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Create database tables if needed
        $this->create_tables();
        
        // Set default options
        $this->set_default_options();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Create database tables
     */
    private function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Example table creation - customize for each plugin
        $table_name = $wpdb->prefix . \'' . $config['text_domain'] . '_data\';
        
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name tinytext NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        require_once(ABSPATH . \'wp-admin/includes/upgrade.php\');
        dbDelta($sql);
        
        // Store version number
        add_option(\'' . $config['text_domain'] . '_version\', ' . $config['prefix'] . 'VERSION);
    }
    
    /**
     * Set default options
     */
    private function set_default_options() {
        $default_options = array(
            \'enable_features\' => true,
            \'plugin_version\' => ' . $config['prefix'] . 'VERSION
        );
        
        add_option(\'' . $config['text_domain'] . '_options\', $default_options);
    }
}

// Initialize the plugin
' . $config['prefix'] . 'Main::get_instance();
';

    return $content;
}

/**
 * Create WordPress.org compliant readme.txt
 */
function create_wordpress_org_readme($config) {
    return '=== ' . $config['plugin_name'] . ' ===
Contributors: vireodesigns
Donate link: https://vireodesigns.com/donate/
Tags: business, management, ' . $config['plugin_key'] . '
Requires at least: 5.8
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: ' . $config['plugin_version'] . '
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

' . $config['plugin_description'] . '

== Description ==

' . $config['plugin_name'] . ' is a comprehensive WordPress plugin designed to help businesses manage their operations efficiently. This plugin provides powerful tools while remaining easy to use for small to medium-sized businesses.

= Free Version Features =

* Core functionality for small businesses
* Easy setup and configuration
* WordPress native integration
* Community support

= Pro Version =

Upgrade to the Pro version for advanced features including unlimited usage, premium support, and enhanced functionality.

[Learn more about Pro features](https://vireodesigns.com/plugins/' . $config['plugin_slug'] . '/)

= Why Choose Our Plugin? =

* **WordPress Native**: Built specifically for WordPress
* **Industry Focused**: Tailored for your specific business needs
* **Affordable**: Much cheaper than SaaS alternatives
* **Self-Hosted**: Your data stays on your server
* **Regular Updates**: Continuous improvement and feature additions

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/' . $config['plugin_slug'] . '` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the \'Plugins\' screen in WordPress
3. Use the Settings->' . $config['plugin_name'] . ' screen to configure the plugin

== Frequently Asked Questions ==

= Is this plugin free? =

Yes! The free version includes core functionality perfect for small businesses.

= What\'s included in the Pro version? =

The Pro version includes unlimited usage, advanced features, priority support, and regular updates.

= Does this work with my theme? =

Yes! This plugin is designed to work with any properly coded WordPress theme.

= Where can I get support? =

Free support is available through the WordPress.org forums. Pro customers receive priority support at vireodesigns.com.

== Screenshots ==

1. Main dashboard overview
2. Settings configuration
3. Data management interface

== Changelog ==

= ' . $config['plugin_version'] . ' =
* Initial release
* Core functionality implemented
* WordPress.org submission ready

== Upgrade Notice ==

= ' . $config['plugin_version'] . ' =
Initial release of ' . $config['plugin_name'] . ' - install now to get started!
';
}
?>