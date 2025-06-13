<?php
/**
 * Vireo Package Delivery System
 * Handles delivery of self-contained plugin packages after purchase
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Vireo_Package_Delivery {
    
    private $packages_dir;
    private $temp_dir;
    
    public function __construct() {
        $this->packages_dir = WP_CONTENT_DIR . '/vireo-packages/';
        $this->temp_dir = WP_CONTENT_DIR . '/vireo-temp/';
        
        add_action('init', array($this, 'init'));
    }
    
    public function init() {
        // Ensure directories exist
        $this->ensure_directories();
        
        // Hook into WooCommerce order completion
        add_action('woocommerce_order_status_completed', array($this, 'generate_download_packages'), 10, 1);
        
        // Handle package downloads
        add_action('template_redirect', array($this, 'handle_package_download'));
        
        // Add rewrite rules for package downloads
        add_action('init', array($this, 'add_rewrite_rules'));
        
        // AJAX handlers for package management
        add_action('wp_ajax_vireo_build_packages', array($this, 'ajax_build_packages'));
        add_action('wp_ajax_vireo_check_package_status', array($this, 'ajax_check_package_status'));
        
        // Admin menu for package management
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }
    
    /**
     * Ensure required directories exist
     */
    private function ensure_directories() {
        if (!file_exists($this->packages_dir)) {
            wp_mkdir_p($this->packages_dir);
            
            // Create .htaccess to prevent direct access
            file_put_contents($this->packages_dir . '.htaccess', "Order Deny,Allow\nDeny from all\n");
        }
        
        if (!file_exists($this->temp_dir)) {
            wp_mkdir_p($this->temp_dir);
            file_put_contents($this->temp_dir . '.htaccess', "Order Deny,Allow\nDeny from all\n");
        }
        
        // Create package subdirectories
        $subdirs = ['property-management', 'sports-league', 'equipment-rental'];
        foreach ($subdirs as $subdir) {
            wp_mkdir_p($this->packages_dir . $subdir);
        }
    }
    
    /**
     * Add rewrite rules for package downloads
     */
    public function add_rewrite_rules() {
        add_rewrite_rule(
            '^vireo-package/([^/]+)/?$',
            'index.php?vireo_package_download=$matches[1]',
            'top'
        );
        
        add_filter('query_vars', function($vars) {
            $vars[] = 'vireo_package_download';
            return $vars;
        });
    }
    
    /**
     * Generate download packages when order is completed
     */
    public function generate_download_packages($order_id) {
        $order = wc_get_order($order_id);
        if (!$order) {
            return;
        }
        
        foreach ($order->get_items() as $item_id => $item) {
            $product = $item->get_product();
            if (!$product) {
                continue;
            }
            
            // Check if this is a Vireo plugin product
            $plugin_type = get_post_meta($product->get_id(), '_vireo_plugin_type', true);
            if (!$plugin_type) {
                continue;
            }
            
            // Generate package for this product
            $this->generate_product_package($order, $product, $plugin_type);
        }
    }
    
    /**
     * Generate package for specific product
     */
    private function generate_product_package($order, $product, $plugin_type) {
        $package_info = $this->get_package_info($plugin_type);
        if (!$package_info) {
            return false;
        }
        
        // Create custom package with license info
        $package_data = array(
            'order_id' => $order->get_id(),
            'customer_email' => $order->get_billing_email(),
            'product_id' => $product->get_id(),
            'product_name' => $product->get_name(),
            'plugin_type' => $plugin_type,
            'license_key' => $this->generate_license_key($order, $product),
            'download_limit' => $this->get_download_limit($product),
            'expires_at' => $this->get_expiration_date($product),
            'created_at' => current_time('mysql')
        );
        
        // Build custom package
        $package_path = $this->build_custom_package($package_data);
        
        if ($package_path) {
            // Store package information
            $this->store_package_info($package_data, $package_path);
            
            // Send download email
            $this->send_download_email($order, $package_data, $package_path);
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Build custom package with license info embedded
     */
    private function build_custom_package($package_data) {
        $plugin_type = $package_data['plugin_type'];
        $package_info = $this->get_package_info($plugin_type);
        
        if (!$package_info) {
            return false;
        }
        
        // Create temporary directory for this package
        $temp_package_dir = $this->temp_dir . 'package_' . $package_data['order_id'] . '_' . time() . '/';
        wp_mkdir_p($temp_package_dir);
        
        try {
            // Copy base package files
            $this->copy_base_package($package_info, $temp_package_dir);
            
            // Inject license information
            $this->inject_license_info($temp_package_dir, $package_data);
            
            // Create custom ZIP
            $package_filename = $this->create_package_filename($package_data);
            $package_path = $this->packages_dir . $package_filename;
            
            if ($this->create_zip_package($temp_package_dir, $package_path)) {
                // Clean up temp directory
                $this->cleanup_directory($temp_package_dir);
                return $package_path;
            }
            
        } catch (Exception $e) {
            error_log('Vireo Package Build Error: ' . $e->getMessage());
            $this->cleanup_directory($temp_package_dir);
        }
        
        return false;
    }
    
    /**
     * Copy base package files
     */
    private function copy_base_package($package_info, $temp_dir) {
        $base_package_path = $this->get_base_package_path($package_info);
        
        if (!file_exists($base_package_path)) {
            throw new Exception("Base package not found: " . $base_package_path);
        }
        
        // Extract base package to temp directory
        $zip = new ZipArchive();
        if ($zip->open($base_package_path) === TRUE) {
            $zip->extractTo($temp_dir);
            $zip->close();
        } else {
            throw new Exception("Failed to extract base package");
        }
    }
    
    /**
     * Inject license information into package
     */
    private function inject_license_info($temp_dir, $package_data) {
        // Find main plugin file
        $plugin_files = glob($temp_dir . '*/*.php');
        $main_plugin_file = null;
        
        foreach ($plugin_files as $file) {
            $content = file_get_contents($file);
            if (strpos($content, 'Plugin Name:') !== false) {
                $main_plugin_file = $file;
                break;
            }
        }
        
        if (!$main_plugin_file) {
            throw new Exception("Main plugin file not found");
        }
        
        // Inject license information
        $content = file_get_contents($main_plugin_file);
        
        // Add license constant
        $license_constant = "\n// Auto-generated license information\n";
        $license_constant .= "define('VIREO_LICENSE_KEY', '" . $package_data['license_key'] . "');\n";
        $license_constant .= "define('VIREO_LICENSE_EMAIL', '" . $package_data['customer_email'] . "');\n";
        $license_constant .= "define('VIREO_LICENSE_EXPIRES', '" . $package_data['expires_at'] . "');\n";
        
        // Insert after opening <?php tag
        $content = str_replace('<?php', '<?php' . $license_constant, $content);
        
        file_put_contents($main_plugin_file, $content);
        
        // Create license info file
        $license_info = array(
            'license_key' => $package_data['license_key'],
            'customer_email' => $package_data['customer_email'],
            'product_name' => $package_data['product_name'],
            'order_id' => $package_data['order_id'],
            'issued_at' => $package_data['created_at'],
            'expires_at' => $package_data['expires_at'],
            'download_limit' => $package_data['download_limit']
        );
        
        $license_file = dirname($main_plugin_file) . '/LICENSE-INFO.json';
        file_put_contents($license_file, json_encode($license_info, JSON_PRETTY_PRINT));
        
        // Create installation instructions
        $instructions = $this->create_installation_instructions($package_data);
        $readme_file = dirname($main_plugin_file) . '/INSTALLATION.md';
        file_put_contents($readme_file, $instructions);
    }
    
    /**
     * Create ZIP package
     */
    private function create_zip_package($source_dir, $output_path) {
        $zip = new ZipArchive();
        
        if ($zip->open($output_path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            return false;
        }
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source_dir),
            RecursiveIteratorIterator::LEAVES_ONLY
        );
        
        foreach ($iterator as $file) {
            if (!$file->isDir()) {
                $file_path = $file->getRealPath();
                $relative_path = substr($file_path, strlen($source_dir));
                $zip->addFile($file_path, $relative_path);
            }
        }
        
        return $zip->close();
    }
    
    /**
     * Handle package downloads
     */
    public function handle_package_download() {
        $download_token = get_query_var('vireo_package_download');
        
        if (!$download_token) {
            return;
        }
        
        // Validate download token
        $package_info = $this->get_package_by_token($download_token);
        
        if (!$package_info) {
            wp_die('Invalid download token.', 'Download Error', array('response' => 404));
        }
        
        // Check download limits and expiration
        if (!$this->validate_download_access($package_info)) {
            wp_die('Download limit exceeded or expired.', 'Download Error', array('response' => 403));
        }
        
        // Serve file
        $this->serve_package_file($package_info);
    }
    
    /**
     * Serve package file for download
     */
    private function serve_package_file($package_info) {
        $file_path = $package_info['package_path'];
        
        if (!file_exists($file_path)) {
            wp_die('Package file not found.', 'Download Error', array('response' => 404));
        }
        
        // Update download count
        $this->increment_download_count($package_info['id']);
        
        // Set headers for file download
        $filename = basename($file_path);
        
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
     * Get package information by type
     */
    private function get_package_info($plugin_type) {
        $packages = array(
            'property-management-core' => array(
                'name' => 'Vireo Property Management Core',
                'base_package' => 'vireo-property-management-core-v1.0.0.zip',
                'type' => 'core'
            ),
            'property-management-pro' => array(
                'name' => 'Vireo Property Management Pro',
                'base_package' => 'vireo-property-management-pro-v1.0.0.zip',
                'type' => 'pro'
            ),
            'sports-league-core' => array(
                'name' => 'Vireo Sports League Manager Core',
                'base_package' => 'vireo-sports-league-core-v1.0.0.zip',
                'type' => 'core'
            ),
            'sports-league-pro' => array(
                'name' => 'Vireo Sports League Manager Pro',
                'base_package' => 'vireo-sports-league-pro-v1.0.0.zip',
                'type' => 'pro'
            )
        );
        
        return isset($packages[$plugin_type]) ? $packages[$plugin_type] : null;
    }
    
    /**
     * Generate license key
     */
    private function generate_license_key($order, $product) {
        $prefix = 'VIREO';
        $order_id = str_pad($order->get_id(), 4, '0', STR_PAD_LEFT);
        $product_id = str_pad($product->get_id(), 3, '0', STR_PAD_LEFT);
        $random = strtoupper(substr(md5(uniqid()), 0, 8));
        
        return sprintf('%s-%s-%s-%s', $prefix, $order_id, $product_id, $random);
    }
    
    /**
     * Create installation instructions
     */
    private function create_installation_instructions($package_data) {
        return "# Installation Instructions for {$package_data['product_name']}

## License Information
- **License Key**: {$package_data['license_key']}
- **Registered Email**: {$package_data['customer_email']}
- **Order ID**: {$package_data['order_id']}

## Installation Steps

1. **Upload Plugin**
   - Log into your WordPress admin dashboard
   - Go to Plugins → Add New → Upload Plugin
   - Choose this ZIP file and click 'Install Now'
   - Activate the plugin

2. **Enter License Key**
   - Go to the plugin settings page
   - Enter your license key: `{$package_data['license_key']}`
   - Save settings to activate pro features

3. **Configuration**
   - Follow the setup wizard to configure basic settings
   - Import sample data if desired for testing

## Support

- **Documentation**: https://vireodesigns.com/docs/
- **Support Portal**: https://vireodesigns.com/support/
- **Community Forum**: https://vireodesigns.com/community/

## Important Notes

- Keep your license key secure and backed up
- Do not share your license key publicly
- Contact support if you need to transfer your license
- Download limit: {$package_data['download_limit']} times
- License expires: {$package_data['expires_at']}

---
Generated on " . date('Y-m-d H:i:s') . "
Package customized for order #{$package_data['order_id']}
";
    }
    
    /**
     * Store package information in database
     */
    private function store_package_info($package_data, $package_path) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'vireo_packages';
        
        // Create table if it doesn't exist
        $this->create_packages_table();
        
        $download_token = wp_generate_password(32, false);
        
        $wpdb->insert(
            $table_name,
            array(
                'download_token' => $download_token,
                'order_id' => $package_data['order_id'],
                'product_id' => $package_data['product_id'],
                'customer_email' => $package_data['customer_email'],
                'license_key' => $package_data['license_key'],
                'package_path' => $package_path,
                'download_count' => 0,
                'download_limit' => $package_data['download_limit'],
                'created_at' => $package_data['created_at'],
                'expires_at' => $package_data['expires_at']
            ),
            array('%s', '%d', '%d', '%s', '%s', '%s', '%d', '%d', '%s', '%s')
        );
        
        return $download_token;
    }
    
    /**
     * Create packages table
     */
    private function create_packages_table() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'vireo_packages';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            download_token varchar(64) NOT NULL,
            order_id mediumint(9) NOT NULL,
            product_id mediumint(9) NOT NULL,
            customer_email varchar(255) NOT NULL,
            license_key varchar(255) NOT NULL,
            package_path varchar(500) NOT NULL,
            download_count int(11) DEFAULT 0,
            download_limit int(11) DEFAULT 10,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            expires_at datetime NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY download_token (download_token),
            KEY order_id (order_id),
            KEY customer_email (customer_email)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Send download email to customer
     */
    private function send_download_email($order, $package_data, $package_path) {
        $download_token = $this->get_download_token_by_package($package_path);
        
        if (!$download_token) {
            return false;
        }
        
        $download_url = home_url("/vireo-package/{$download_token}");
        
        $subject = "Your {$package_data['product_name']} Download is Ready";
        
        $message = "
        <h2>Thank you for your purchase!</h2>
        
        <p>Your {$package_data['product_name']} plugin is ready for download.</p>
        
        <h3>Download Information:</h3>
        <ul>
            <li><strong>Product:</strong> {$package_data['product_name']}</li>
            <li><strong>License Key:</strong> <code>{$package_data['license_key']}</code></li>
            <li><strong>Download Limit:</strong> {$package_data['download_limit']} times</li>
            <li><strong>Expires:</strong> {$package_data['expires_at']}</li>
        </ul>
        
        <p><a href=\"{$download_url}\" style=\"background: #0073aa; color: white; padding: 12px 24px; text-decoration: none; border-radius: 4px;\">Download Plugin</a></p>
        
        <h3>Installation Instructions:</h3>
        <ol>
            <li>Download the plugin ZIP file using the link above</li>
            <li>In your WordPress admin, go to Plugins → Add New → Upload Plugin</li>
            <li>Upload the ZIP file and activate the plugin</li>
            <li>Enter your license key in the plugin settings</li>
        </ol>
        
        <p><strong>Important:</strong> Keep your license key secure. You'll need it to receive updates and access pro features.</p>
        
        <hr>
        <p><small>If you have any questions, visit our <a href=\"https://vireodesigns.com/support\">support portal</a>.</small></p>
        ";
        
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: Vireo Designs <downloads@vireodesigns.com>'
        );
        
        return wp_mail($order->get_billing_email(), $subject, $message, $headers);
    }
    
    /**
     * Admin menu for package management
     */
    public function add_admin_menu() {
        add_submenu_page(
            'tools.php',
            'Vireo Package Management',
            'Vireo Packages',
            'manage_options',
            'vireo-packages',
            array($this, 'admin_page')
        );
    }
    
    /**
     * Admin page for package management
     */
    public function admin_page() {
        ?>
        <div class="wrap">
            <h1>Vireo Package Management</h1>
            
            <div class="vireo-package-admin">
                
                <div class="postbox">
                    <h2 class="hndle">Package Builder</h2>
                    <div class="inside">
                        <p>Build self-contained plugin packages for distribution.</p>
                        <button id="build-packages" class="button button-primary">Build All Packages</button>
                        <div id="build-status"></div>
                    </div>
                </div>
                
                <div class="postbox">
                    <h2 class="hndle">Package Statistics</h2>
                    <div class="inside">
                        <?php $this->display_package_stats(); ?>
                    </div>
                </div>
                
            </div>
        </div>
        
        <script>
        document.getElementById('build-packages').addEventListener('click', function() {
            const button = this;
            const status = document.getElementById('build-status');
            
            button.disabled = true;
            button.textContent = 'Building...';
            status.innerHTML = '<p>Building packages, please wait...</p>';
            
            fetch(ajaxurl, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: new URLSearchParams({
                    action: 'vireo_build_packages',
                    nonce: '<?php echo wp_create_nonce('vireo_packages'); ?>'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    status.innerHTML = '<div class="notice notice-success"><p>' + data.data.message + '</p></div>';
                } else {
                    status.innerHTML = '<div class="notice notice-error"><p>Error: ' + data.data + '</p></div>';
                }
                
                button.disabled = false;
                button.textContent = 'Build All Packages';
            })
            .catch(error => {
                status.innerHTML = '<div class="notice notice-error"><p>Network error: ' + error + '</p></div>';
                button.disabled = false;
                button.textContent = 'Build All Packages';
            });
        });
        </script>
        
        <style>
        .vireo-package-admin .postbox {
            margin-bottom: 20px;
        }
        
        .package-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .stat-box {
            padding: 15px;
            background: #f9f9f9;
            border-radius: 4px;
            text-align: center;
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #0073aa;
        }
        
        .stat-label {
            font-size: 14px;
            color: #666;
        }
        </style>
        <?php
    }
    
    /**
     * Display package statistics
     */
    private function display_package_stats() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'vireo_packages';
        
        $total_packages = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        $total_downloads = $wpdb->get_var("SELECT SUM(download_count) FROM $table_name");
        $active_packages = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE expires_at > NOW()");
        
        ?>
        <div class="package-stats">
            <div class="stat-box">
                <div class="stat-number"><?php echo $total_packages ?: 0; ?></div>
                <div class="stat-label">Total Packages</div>
            </div>
            <div class="stat-box">
                <div class="stat-number"><?php echo $total_downloads ?: 0; ?></div>
                <div class="stat-label">Total Downloads</div>
            </div>
            <div class="stat-box">
                <div class="stat-number"><?php echo $active_packages ?: 0; ?></div>
                <div class="stat-label">Active Packages</div>
            </div>
        </div>
        <?php
    }
    
    /**
     * AJAX handler for building packages
     */
    public function ajax_build_packages() {
        check_ajax_referer('vireo_packages', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Access denied');
        }
        
        try {
            // Execute package builder script
            $script_path = get_template_directory() . '/../plugins/vireo-property-management/build-scripts/build-ecosystem-packages.js';
            
            if (!file_exists($script_path)) {
                throw new Exception('Package builder script not found');
            }
            
            // Run the build script
            $output = shell_exec("cd " . dirname($script_path) . " && node build-ecosystem-packages.js 2>&1");
            
            if (strpos($output, 'All ecosystem packages built successfully') !== false) {
                wp_send_json_success(array(
                    'message' => 'All packages built successfully!<br><pre>' . esc_html($output) . '</pre>'
                ));
            } else {
                wp_send_json_error('Build failed: ' . $output);
            }
            
        } catch (Exception $e) {
            wp_send_json_error('Build error: ' . $e->getMessage());
        }
    }
    
    // Additional utility methods...
    private function cleanup_directory($dir) {
        if (is_dir($dir)) {
            $files = array_diff(scandir($dir), array('.', '..'));
            foreach ($files as $file) {
                $path = $dir . $file;
                is_dir($path) ? $this->cleanup_directory($path . '/') : unlink($path);
            }
            rmdir($dir);
        }
    }
    
    private function get_base_package_path($package_info) {
        return $this->packages_dir . $package_info['base_package'];
    }
    
    private function create_package_filename($package_data) {
        $timestamp = date('Ymd-His');
        $order_id = $package_data['order_id'];
        return "vireo-package-{$package_data['plugin_type']}-order-{$order_id}-{$timestamp}.zip";
    }
    
    private function get_download_limit($product) {
        return get_post_meta($product->get_id(), '_download_limit', true) ?: 10;
    }
    
    private function get_expiration_date($product) {
        $days = get_post_meta($product->get_id(), '_download_expiry_days', true) ?: 365;
        return date('Y-m-d H:i:s', strtotime("+{$days} days"));
    }
    
    private function get_package_by_token($token) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'vireo_packages';
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE download_token = %s", $token), ARRAY_A);
    }
    
    private function validate_download_access($package_info) {
        // Check expiration
        if (strtotime($package_info['expires_at']) < time()) {
            return false;
        }
        
        // Check download limit
        if ($package_info['download_count'] >= $package_info['download_limit']) {
            return false;
        }
        
        return true;
    }
    
    private function increment_download_count($package_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'vireo_packages';
        $wpdb->query($wpdb->prepare("UPDATE $table_name SET download_count = download_count + 1 WHERE id = %d", $package_id));
    }
    
    private function get_download_token_by_package($package_path) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'vireo_packages';
        return $wpdb->get_var($wpdb->prepare("SELECT download_token FROM $table_name WHERE package_path = %s", $package_path));
    }
}

// Initialize the package delivery system
new Vireo_Package_Delivery();
?>