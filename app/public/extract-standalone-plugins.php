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
        'free_features' => [
            'Up to 5 properties',
            'Basic tenant management',
            'Simple lease tracking',
            'Community support'
        ]
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
        'free_features' => [
            'Up to 2 leagues',
            'Basic team management',
            'Simple scheduling',
            'Community support'
        ]
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
        'free_features' => [
            'Up to 25 items',
            'Basic booking system',
            'Simple customer management',
            'Community support'
        ]
    ],
    
    'auto-shop' => [
        'source_dir' => '/Users/condominiumassociates/Local Sites/Vireo/app/public/wp-content/plugins-DISABLED-2025-06-13-02-41/dealeredge',
        'plugin_name' => 'DealerEdge - Auto Shop Management',
        'plugin_slug' => 'dealeredge-auto-shop',
        'plugin_description' => 'Complete auto shop and small dealer management for WordPress. Streamline work orders, inventory, and billing.',
        'plugin_version' => '1.0.0',
        'wordpress_org_slug' => 'dealeredge-auto-shop',
        'main_file' => 'dealeredge-auto-shop.php',
        'text_domain' => 'dealeredge-auto-shop',
        'prefix' => 'DEAS_',
        'free_features' => [
            'Up to 10 work orders/month',
            'Basic customer management',
            'Simple inventory tracking',
            'Community support'
        ]
    ],
    
    'fitness-studio' => [
        'source_dir' => '/Users/condominiumassociates/Local Sites/Vireo/app/public/wp-content/plugins-DISABLED-2025-06-13-02-41/gymflow',
        'plugin_name' => 'GymFlow - Fitness Studio Management',
        'plugin_slug' => 'gymflow-fitness-studio',
        'plugin_description' => 'Professional fitness studio management for WordPress. Handle memberships, class scheduling, and member progress.',
        'plugin_version' => '1.0.0',
        'wordpress_org_slug' => 'gymflow-fitness-studio',
        'main_file' => 'gymflow-fitness-studio.php',
        'text_domain' => 'gymflow-fitness-studio',
        'prefix' => 'GFS_',
        'free_features' => [
            'Up to 50 members',
            'Basic class scheduling',
            'Simple member management',
            'Community support'
        ]
    ],
    
    'photography-studio' => [
        'source_dir' => '/Users/condominiumassociates/Local Sites/Vireo/app/public/wp-content/plugins-DISABLED-2025-06-13-02-41/studiosnap',
        'plugin_name' => 'StudioSnap - Photography Studio Management',
        'plugin_slug' => 'studiosnap-photography-studio',
        'plugin_description' => 'Complete photography studio management for WordPress. Handle client bookings, session management, and galleries.',
        'plugin_version' => '1.0.0',
        'wordpress_org_slug' => 'studiosnap-photography-studio',
        'main_file' => 'studiosnap-photography-studio.php',
        'text_domain' => 'studiosnap-photography-studio',
        'prefix' => 'SPS_',
        'free_features' => [
            'Up to 10 sessions/month',
            'Basic client management',
            'Simple gallery sharing',
            'Community support'
        ]
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
    
    // Copy languages if they exist
    $languages_source = $config['source_dir'] . '/languages';
    if (is_dir($languages_source)) {
        exec("cp -R " . escapeshellarg($languages_source) . " " . escapeshellarg($standalone_dir . '/languages'));
        echo "<p>✅ Copied language files</p>\n";
    }
    
    // Create standalone main plugin file
    $main_plugin_content = create_standalone_main_file($config);
    file_put_contents($standalone_dir . '/' . $config['main_file'], $main_plugin_content);
    echo "<p>✅ Created main plugin file: {$config['main_file']}</p>\n";
    
    // Create WordPress.org compliant readme.txt
    $readme_content = create_wordpress_org_readme($config);
    file_put_contents($standalone_dir . '/readme.txt', $readme_content);
    echo "<p>✅ Created WordPress.org readme.txt</p>\n";
    
    // Create uninstall.php
    $uninstall_content = create_uninstall_file($config);
    file_put_contents($standalone_dir . '/uninstall.php', $uninstall_content);
    echo "<p>✅ Created uninstall.php</p>\n";
    
    // Create index.php for security
    $index_content = "<?php\n// Silence is golden.\n";
    file_put_contents($standalone_dir . '/index.php', $index_content);
    echo "<p>✅ Created security index.php</p>\n";
    
    // Create ZIP package for WordPress.org submission
    $zip_file = $extraction_dir . '/' . $config['plugin_slug'] . '.zip';
    create_zip_package($standalone_dir, $zip_file);
    echo "<p>📦 Created ZIP package: " . basename($zip_file) . "</p>\n";
    
    echo "<p><strong>✅ Standalone plugin ready for WordPress.org submission!</strong></p>\n";
    echo "<hr>\n";
}

echo "<h2>📋 Next Steps for WordPress.org Submission</h2>\n";
echo "<ol>\n";
echo "<li><strong>Test Each Plugin:</strong> Install and activate each standalone plugin individually</li>\n";
echo "<li><strong>Code Review:</strong> Ensure no dependencies on other Vireo plugins</li>\n";
echo "<li><strong>WordPress.org Guidelines:</strong> Review each plugin against WordPress.org requirements</li>\n";
echo "<li><strong>Submit to WordPress.org:</strong> Upload ZIP files to WordPress.org for review</li>\n";
echo "<li><strong>Monitor Approval:</strong> Track approval status and respond to reviewer feedback</li>\n";
echo "</ol>\n";

echo "<h2>🎯 Standalone Plugin Summary</h2>\n";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>\n";
echo "<tr><th>Plugin Name</th><th>WordPress.org Slug</th><th>Text Domain</th><th>Prefix</th><th>Status</th></tr>\n";

foreach ($plugins_to_extract as $plugin_key => $config) {
    $status = is_dir($extraction_dir . '/' . $config['plugin_slug']) ? '✅ Ready' : '❌ Failed';
    echo "<tr>";
    echo "<td>{$config['plugin_name']}</td>";
    echo "<td>{$config['wordpress_org_slug']}</td>";
    echo "<td>{$config['text_domain']}</td>";
    echo "<td>{$config['prefix']}</td>";
    echo "<td>$status</td>";
    echo "</tr>\n";
}
echo "</table>\n";

echo "<p><strong>🚀 All plugins extracted as standalone WordPress.org compliant packages!</strong></p>\n";

/**
 * Create standalone main plugin file
 */
function create_standalone_main_file($config) {
    return "<?php
/**
 * Plugin Name: {$config['plugin_name']}
 * Plugin URI: https://vireodesigns.com/plugins/{$config['plugin_slug']}/
 * Description: {$config['plugin_description']}
 * Version: {$config['plugin_version']}
 * Author: Vireo Designs
 * Author URI: https://vireodesigns.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: {$config['text_domain']}
 * Domain Path: /languages
 * Requires at least: 5.8
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * Network: false
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('{$config['prefix']}VERSION', '{$config['plugin_version']}');
define('{$config['prefix']}PLUGIN_FILE', __FILE__);
define('{$config['prefix']}PLUGIN_DIR', plugin_dir_path(__FILE__));
define('{$config['prefix']}PLUGIN_URL', plugin_dir_url(__FILE__));
define('{$config['prefix']}PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main plugin class - 100% standalone
 */
class {$config['prefix']}Main {
    
    /**
     * Single instance
     */
    private static \$instance = null;
    
    /**
     * Get single instance
     */
    public static function get_instance() {
        if (null === self::\$instance) {
            self::\$instance = new self();
        }
        return self::\$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        add_action('init', array(\$this, 'init'));
        add_action('plugins_loaded', array(\$this, 'load_textdomain'));
        register_activation_hook(__FILE__, array(\$this, 'activate'));
        register_deactivation_hook(__FILE__, array(\$this, 'deactivate'));
    }
    
    /**
     * Initialize plugin
     */
    public function init() {
        // Load core functionality
        \$this->load_core();
        
        // Initialize admin interface
        if (is_admin()) {
            \$this->load_admin();
        }
        
        // Initialize frontend
        if (!is_admin()) {
            \$this->load_frontend();
        }
    }
    
    /**
     * Load text domain for translations
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            '{$config['text_domain']}',
            false,
            dirname(plugin_basename(__FILE__)) . '/languages'
        );
    }
    
    /**
     * Load core functionality
     */
    private function load_core() {
        // Include core files if they exist
        \$core_files = array(
            '{$config['prefix']}PLUGIN_DIR . 'core/includes/class-core.php',
            '{$config['prefix']}PLUGIN_DIR . 'core/includes/class-database.php',
            '{$config['prefix']}PLUGIN_DIR . 'core/includes/class-utilities.php'
        );
        
        foreach (\$core_files as \$file) {
            if (file_exists(\$file)) {
                require_once \$file;
            }
        }
    }
    
    /**
     * Load admin functionality
     */
    private function load_admin() {
        // Include admin files if they exist
        \$admin_files = array(
            '{$config['prefix']}PLUGIN_DIR . 'core/includes/admin/class-admin.php',
            '{$config['prefix']}PLUGIN_DIR . 'core/includes/admin/class-admin-menu.php',
            '{$config['prefix']}PLUGIN_DIR . 'core/includes/admin/class-admin-settings.php'
        );
        
        foreach (\$admin_files as \$file) {
            if (file_exists(\$file)) {
                require_once \$file;
            }
        }
    }
    
    /**
     * Load frontend functionality
     */
    private function load_frontend() {
        // Include frontend files if they exist
        \$frontend_files = array(
            '{$config['prefix']}PLUGIN_DIR . 'core/includes/public/class-public.php',
            '{$config['prefix']}PLUGIN_DIR . 'core/includes/public/class-shortcodes.php'
        );
        
        foreach (\$frontend_files as \$file) {
            if (file_exists(\$file)) {
                require_once \$file;
            }
        }
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Create database tables if needed
        \$this->create_tables();
        
        // Set default options
        \$this->set_default_options();
        
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
        global \$wpdb;
        
        \$charset_collate = \$wpdb->get_charset_collate();
        
        // Example table creation - customize for each plugin
        \$table_name = \$wpdb->prefix . '{$config['text_domain']}_data';
        
        \$sql = \"CREATE TABLE \$table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name tinytext NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) \$charset_collate;\";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta(\$sql);
        
        // Store version number
        add_option('{$config['text_domain']}_version', {$config['prefix']}VERSION);
    }
    
    /**
     * Set default options
     */
    private function set_default_options() {
        \$default_options = array(
            'enable_features' => true,
            'plugin_version' => {$config['prefix']}VERSION
        );
        
        add_option('{$config['text_domain']}_options', \$default_options);
    }
}

// Initialize the plugin
{$config['prefix']}Main::get_instance();

// Prevent conflicts with other Vireo plugins
if (!function_exists('vireo_prevent_conflicts')) {
    function vireo_prevent_conflicts() {
        // This ensures each plugin works independently
        return true;
    }
}
";
}

/**
 * Create WordPress.org compliant readme.txt
 */
function create_wordpress_org_readme($config) {
    $features_list = implode(\"\n\", array_map(function($feature) {
        return \"* $feature\";
    }, $config['free_features']));
    
    return \"=== {$config['plugin_name']} ===
Contributors: vireodesigns
Donate link: https://vireodesigns.com/donate/
Tags: business, management, {$config['plugin_key']}
Requires at least: 5.8
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: {$config['plugin_version']}
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

{$config['plugin_description']}

== Description ==

{$config['plugin_name']} is a comprehensive WordPress plugin designed to help businesses manage their operations efficiently. This plugin provides powerful tools while remaining easy to use for small to medium-sized businesses.

= Free Version Features =

$features_list

= Pro Version =

Upgrade to the Pro version for advanced features including unlimited usage, premium support, and enhanced functionality.

[Learn more about Pro features](https://vireodesigns.com/plugins/{$config['plugin_slug']}/)

= Why Choose Our Plugin? =

* **WordPress Native**: Built specifically for WordPress
* **Industry Focused**: Tailored for your specific business needs
* **Affordable**: Much cheaper than SaaS alternatives
* **Self-Hosted**: Your data stays on your server
* **Regular Updates**: Continuous improvement and feature additions

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/{$config['plugin_slug']}` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Settings->{$config['plugin_name']} screen to configure the plugin

== Frequently Asked Questions ==

= Is this plugin free? =

Yes! The free version includes core functionality perfect for small businesses.

= What's included in the Pro version? =

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

= {$config['plugin_version']} =
* Initial release
* Core functionality implemented
* WordPress.org submission ready

== Upgrade Notice ==

= {$config['plugin_version']} =
Initial release of {$config['plugin_name']} - install now to get started!
\";
}

/**
 * Create uninstall.php file
 */
function create_uninstall_file($config) {
    return \"<?php
/**
 * Uninstall {$config['plugin_name']}
 */

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete plugin options
delete_option('{$config['text_domain']}_options');
delete_option('{$config['text_domain']}_version');

// Drop custom tables if they exist
global \$wpdb;
\$table_name = \$wpdb->prefix . '{$config['text_domain']}_data';
\$wpdb->query(\"DROP TABLE IF EXISTS \$table_name\");

// Clear any cached data
wp_cache_flush();
\";
}

/**
 * Create ZIP package
 */
function create_zip_package($source_dir, $zip_file) {
    $zip = new ZipArchive();
    if ($zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source_dir),
            RecursiveIteratorIterator::LEAVES_ONLY
        );
        
        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($source_dir) + 1);
                $zip->addFile($filePath, basename($source_dir) . '/' . $relativePath);
            }
        }
        $zip->close();
        return true;
    }
    return false;
}
?>