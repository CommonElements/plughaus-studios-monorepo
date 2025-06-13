<?php
/**
 * Plugin Download Handler
 * Handles free plugin downloads and license validation
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Vireo_Download_Handler {
    
    public function __construct() {
        add_action('init', array($this, 'handle_download_request'));
        add_action('wp_ajax_request_trial', array($this, 'handle_trial_request'));
        add_action('wp_ajax_nopriv_request_trial', array($this, 'handle_trial_request'));
        add_action('wp_ajax_track_download', array($this, 'track_download'));
        add_action('wp_ajax_nopriv_track_download', array($this, 'track_download'));
    }
    
    /**
     * Handle plugin download requests
     */
    public function handle_download_request() {
        if (isset($_GET['download']) && isset($_GET['plugin'])) {
            $plugin = sanitize_text_field($_GET['plugin']);
            $version = isset($_GET['version']) ? sanitize_text_field($_GET['version']) : 'free';
            
            // Security nonce check
            if (!isset($_GET['nonce']) || !wp_verify_nonce($_GET['nonce'], 'download_' . $plugin)) {
                wp_die('Security check failed');
            }
            
            $this->process_download($plugin, $version);
        }
    }
    
    /**
     * Process the download
     */
    private function process_download($plugin, $version) {
        $downloads_dir = WP_CONTENT_DIR . '/uploads/plugin-downloads/';
        
        // Define available plugins and their files
        $available_plugins = array(
            'property-management' => array(
                'free' => 'vireo-property-management-free.zip',
                'name' => 'Property Management',
                'size' => '2.1MB'
            ),
            'community-management' => array(
                'free' => 'vireo-community-management-free.zip',
                'name' => 'Community Management',
                'size' => '1.8MB'
            )
        );
        
        if (!isset($available_plugins[$plugin])) {
            wp_die('Plugin not found');
        }
        
        $plugin_info = $available_plugins[$plugin];
        
        // For free versions, just serve the file
        if ($version === 'free') {
            $file_path = $downloads_dir . $plugin_info['free'];
            
            // Create dummy file if it doesn't exist (for demo purposes)
            if (!file_exists($file_path)) {
                $this->create_demo_plugin_file($file_path, $plugin_info['name']);
            }
            
            // Track the download
            $this->increment_download_count($plugin);
            
            // Serve the file
            $this->serve_file($file_path, $plugin_info['free']);
        }
        
        // For pro versions, redirect to purchase
        else {
            wp_redirect('/checkout/?product=' . $plugin . '-pro');
            exit;
        }
    }
    
    /**
     * Create demo plugin file for demonstration
     */
    private function create_demo_plugin_file($file_path, $plugin_name) {
        $upload_dir = dirname($file_path);
        
        if (!file_exists($upload_dir)) {
            wp_mkdir_p($upload_dir);
        }
        
        // Create a simple demo zip file
        $zip = new ZipArchive();
        
        if ($zip->open($file_path, ZipArchive::CREATE) === TRUE) {
            // Add main plugin file
            $main_file_content = $this->get_demo_plugin_content($plugin_name);
            $zip->addFromString(strtolower(str_replace(' ', '-', $plugin_name)) . '.php', $main_file_content);
            
            // Add readme
            $readme_content = $this->get_demo_readme_content($plugin_name);
            $zip->addFromString('readme.txt', $readme_content);
            
            $zip->close();
        }
    }
    
    /**
     * Get demo plugin content
     */
    private function get_demo_plugin_content($plugin_name) {
        $plugin_slug = strtolower(str_replace(' ', '-', $plugin_name));
        
        return "<?php
/**
 * Plugin Name: Vireo {$plugin_name}
 * Plugin URI: https://vireodesigns.com/plugins/{$plugin_slug}/
 * Description: Professional {$plugin_name} plugin for WordPress. This is the free version with core features.
 * Version: 1.0.0
 * Author: Vireo Designs
 * Author URI: https://vireodesigns.com
 * License: GPL v2 or later
 * Text Domain: vireo-{$plugin_slug}
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Plugin constants
define('VIREO_" . strtoupper(str_replace('-', '_', $plugin_slug)) . "_VERSION', '1.0.0');
define('VIREO_" . strtoupper(str_replace('-', '_', $plugin_slug)) . "_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('VIREO_" . strtoupper(str_replace('-', '_', $plugin_slug)) . "_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Main Plugin Class
 */
class Vireo_" . str_replace('-', '_', ucwords($plugin_slug, '-')) . " {
    
    public function __construct() {
        add_action('init', array(\$this, 'init'));
        add_action('admin_menu', array(\$this, 'add_admin_menu'));
        register_activation_hook(__FILE__, array(\$this, 'activate'));
        register_deactivation_hook(__FILE__, array(\$this, 'deactivate'));
    }
    
    public function init() {
        // Initialize plugin functionality
        load_plugin_textdomain('vireo-{$plugin_slug}', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    public function add_admin_menu() {
        add_menu_page(
            '{$plugin_name}',
            '{$plugin_name}',
            'manage_options',
            'vireo-{$plugin_slug}',
            array(\$this, 'admin_page'),
            'dashicons-building',
            30
        );
    }
    
    public function admin_page() {
        ?>
        <div class=\"wrap\">
            <h1>Vireo {$plugin_name}</h1>
            <div class=\"notice notice-info\">
                <p><strong>Welcome to Vireo {$plugin_name}!</strong></p>
                <p>This is the free version with core features. Upgrade to Pro for advanced functionality:</p>
                <ul>
                    <li>✓ Advanced analytics and reporting</li>
                    <li>✓ Automation workflows</li>
                    <li>✓ Priority support</li>
                    <li>✓ Custom integrations</li>
                </ul>
                <p><a href=\"https://vireodesigns.com/plugins/{$plugin_slug}/\" class=\"button button-primary\" target=\"_blank\">Learn More About Pro</a></p>
            </div>
            
            <div class=\"card\">
                <h2>Getting Started</h2>
                <p>Thank you for installing Vireo {$plugin_name}! Here's how to get started:</p>
                <ol>
                    <li>Configure your settings using the options below</li>
                    <li>Import your existing data (if applicable)</li>
                    <li>Customize the display options</li>
                    <li>Consider upgrading to Pro for advanced features</li>
                </ol>
            </div>
            
            <div class=\"card\">
                <h2>Core Features (Free)</h2>
                <p>The free version includes essential features to get you started:</p>
                <ul>
                    <li>Basic {$plugin_name} management</li>
                    <li>Simple reporting</li>
                    <li>WordPress admin integration</li>
                    <li>Community support</li>
                </ul>
            </div>
            
            <div class=\"card\">
                <h2>Need Help?</h2>
                <p>Get support and find resources:</p>
                <ul>
                    <li><a href=\"https://vireodesigns.com/support/\" target=\"_blank\">Documentation & Support</a></li>
                    <li><a href=\"https://vireodesigns.com/contact/\" target=\"_blank\">Contact Us</a></li>
                    <li><a href=\"https://wordpress.org/support/plugin/vireo-{$plugin_slug}/\" target=\"_blank\">Community Forums</a></li>
                </ul>
            </div>
        </div>
        <?php
    }
    
    public function activate() {
        // Create necessary database tables or options
        add_option('vireo_{$plugin_slug}_version', VIREO_" . strtoupper(str_replace('-', '_', $plugin_slug)) . "_VERSION);
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    public function deactivate() {
        // Clean up if necessary
        flush_rewrite_rules();
    }
}

// Initialize the plugin
new Vireo_" . str_replace('-', '_', ucwords($plugin_slug, '-')) . "();

/**
 * Check for Pro version
 */
function vireo_{$plugin_slug}_check_pro() {
    // This would check for Pro license in the actual plugin
    return false; // Free version always returns false
}

/**
 * Pro upgrade notice
 */
function vireo_{$plugin_slug}_pro_notice() {
    if (!vireo_{$plugin_slug}_check_pro()) {
        echo '<div class=\"notice notice-info is-dismissible\">';
        echo '<p><strong>Vireo {$plugin_name} Pro</strong> - Unlock advanced features with our Pro version!</p>';
        echo '<p><a href=\"https://vireodesigns.com/plugins/{$plugin_slug}/\" class=\"button button-primary\" target=\"_blank\">Upgrade to Pro</a></p>';
        echo '</div>';
    }
}
add_action('admin_notices', 'vireo_{$plugin_slug}_pro_notice');
";
    }
    
    /**
     * Get demo readme content
     */
    private function get_demo_readme_content($plugin_name) {
        $plugin_slug = strtolower(str_replace(' ', '-', $plugin_name));
        
        return "=== Vireo {$plugin_name} ===
Contributors: vireostudios
Tags: {$plugin_slug}, business, management, professional
Requires at least: 5.8
Tested up to: 6.4
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Professional {$plugin_name} plugin for WordPress with powerful features and intuitive design.

== Description ==

Vireo {$plugin_name} is a comprehensive solution for managing your business processes directly within WordPress. 

**Free Features:**
* Core {$plugin_name} functionality
* WordPress admin integration
* Basic reporting
* Community support

**Pro Features:**
* Advanced analytics and charts
* Automation workflows
* Custom integrations
* Priority support
* White-label options
* API access

[Learn more about Pro features](https://vireodesigns.com/plugins/{$plugin_slug}/)

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/vireo-{$plugin_slug}/`
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the {$plugin_name} menu item to configure the plugin

== Frequently Asked Questions ==

= Is this plugin free? =

Yes! The core features are completely free. Pro features are available with a paid license.

= Do you offer support? =

Free users receive community support. Pro users get priority email support.

= Can I upgrade to Pro later? =

Absolutely! You can upgrade at any time without losing any data.

== Screenshots ==

1. Main dashboard
2. Management interface
3. Reporting features
4. Settings panel

== Changelog ==

= 1.0.0 =
* Initial release
* Core functionality
* WordPress admin integration

== Upgrade Notice ==

= 1.0.0 =
Initial release of Vireo {$plugin_name}.
";
    }
    
    /**
     * Serve file for download
     */
    private function serve_file($file_path, $filename) {
        if (!file_exists($file_path)) {
            wp_die('File not found');
        }
        
        // Set headers for download
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($file_path));
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: 0');
        
        // Output file
        readfile($file_path);
        exit;
    }
    
    /**
     * Track download count
     */
    private function increment_download_count($plugin) {
        $count_key = 'vireo_downloads_' . $plugin;
        $current_count = get_option($count_key, 0);
        update_option($count_key, $current_count + 1);
        
        // Also track daily downloads
        $today = date('Y-m-d');
        $daily_key = $count_key . '_' . $today;
        $daily_count = get_option($daily_key, 0);
        update_option($daily_key, $daily_count + 1);
    }
    
    /**
     * Handle trial requests (AJAX)
     */
    public function handle_trial_request() {
        check_ajax_referer('vireo_trial', 'nonce');
        
        $plugin = sanitize_text_field($_POST['plugin']);
        $email = sanitize_email($_POST['email']);
        $name = sanitize_text_field($_POST['name']);
        
        if (empty($email) || empty($plugin)) {
            wp_send_json_error('Email and plugin are required');
        }
        
        // Store trial request
        $trial_data = array(
            'plugin' => $plugin,
            'email' => $email,
            'name' => $name,
            'requested_at' => current_time('mysql'),
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT']
        );
        
        // Save to database or send email to admin
        $this->save_trial_request($trial_data);
        
        // Send confirmation email to user
        $this->send_trial_confirmation_email($email, $name, $plugin);
        
        wp_send_json_success('Trial request submitted successfully');
    }
    
    /**
     * Save trial request
     */
    private function save_trial_request($data) {
        // Here you could save to database or send to CRM
        // For now, just email the admin
        $admin_email = get_option('admin_email');
        $subject = 'New Plugin Trial Request: ' . $data['plugin'];
        $message = sprintf(
            "New trial request received:\n\nPlugin: %s\nName: %s\nEmail: %s\nRequested: %s\nIP: %s",
            $data['plugin'],
            $data['name'],
            $data['email'],
            $data['requested_at'],
            $data['ip_address']
        );
        
        wp_mail($admin_email, $subject, $message);
    }
    
    /**
     * Send trial confirmation email
     */
    private function send_trial_confirmation_email($email, $name, $plugin) {
        $subject = 'Your Vireo Designs Trial Request';
        $message = sprintf(
            "Hi %s,\n\nThank you for your interest in our %s plugin!\n\nWe've received your trial request and will be in touch within 24 hours with your trial access details.\n\nIn the meantime, feel free to:\n- Browse our documentation: https://vireodesigns.com/docs/\n- Check out our other plugins: https://vireodesigns.com/plugins/\n- Contact us with questions: https://vireodesigns.com/contact/\n\nBest regards,\nThe Vireo Designs Team",
            $name,
            ucwords(str_replace('-', ' ', $plugin))
        );
        
        wp_mail($email, $subject, $message);
    }
    
    /**
     * Track download (AJAX)
     */
    public function track_download() {
        check_ajax_referer('vireo_track', 'nonce');
        
        $plugin = sanitize_text_field($_POST['plugin']);
        $version = sanitize_text_field($_POST['version']);
        
        // Track the download
        $this->increment_download_count($plugin);
        
        // Return download stats
        $total_downloads = get_option('vireo_downloads_' . $plugin, 0);
        
        wp_send_json_success(array(
            'total_downloads' => $total_downloads,
            'message' => 'Download tracked successfully'
        ));
    }
    
    /**
     * Get download statistics
     */
    public static function get_download_stats($plugin) {
        return array(
            'total' => get_option('vireo_downloads_' . $plugin, 0),
            'today' => get_option('vireo_downloads_' . $plugin . '_' . date('Y-m-d'), 0),
            'this_week' => self::get_weekly_downloads($plugin),
            'this_month' => self::get_monthly_downloads($plugin)
        );
    }
    
    /**
     * Get weekly downloads
     */
    private static function get_weekly_downloads($plugin) {
        $total = 0;
        for ($i = 0; $i < 7; $i++) {
            $date = date('Y-m-d', strtotime('-' . $i . ' days'));
            $total += get_option('vireo_downloads_' . $plugin . '_' . $date, 0);
        }
        return $total;
    }
    
    /**
     * Get monthly downloads
     */
    private static function get_monthly_downloads($plugin) {
        $total = 0;
        for ($i = 0; $i < 30; $i++) {
            $date = date('Y-m-d', strtotime('-' . $i . ' days'));
            $total += get_option('vireo_downloads_' . $plugin . '_' . $date, 0);
        }
        return $total;
    }
}

// Initialize the download handler
new Vireo_Download_Handler();

/**
 * Helper function to generate download URL
 */
function vireo_get_download_url($plugin, $version = 'free') {
    return add_query_arg(array(
        'download' => '1',
        'plugin' => $plugin,
        'version' => $version,
        'nonce' => wp_create_nonce('download_' . $plugin)
    ), home_url('/'));
}

/**
 * Helper function to get download button HTML
 */
function vireo_get_download_button($plugin, $version = 'free', $text = '', $class = 'btn btn-primary') {
    if (empty($text)) {
        $text = $version === 'free' ? 'Download Free' : 'Get Pro';
    }
    
    $url = vireo_get_download_url($plugin, $version);
    $icon = $version === 'free' ? 'fas fa-download' : 'fas fa-crown';
    
    return sprintf(
        '<a href="%s" class="%s" data-plugin="%s" data-version="%s"><i class="%s"></i> %s</a>',
        esc_url($url),
        esc_attr($class),
        esc_attr($plugin),
        esc_attr($version),
        esc_attr($icon),
        esc_html($text)
    );
}