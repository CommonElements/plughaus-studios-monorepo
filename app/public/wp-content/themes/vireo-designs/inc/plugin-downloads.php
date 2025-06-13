<?php
/**
 * Plugin Downloads Handler for Vireo Designs
 * 
 * Secure download system for FREE standalone plugins
 * Direct distribution from Vireo site
 */

if (!defined('ABSPATH')) {
    exit;
}

class Vireo_Plugin_Downloads {
    
    public function __construct() {
        add_action('init', array($this, 'init_download_system'));
        add_action('wp_ajax_download_plugin', array($this, 'handle_plugin_download'));
        add_action('wp_ajax_nopriv_download_plugin', array($this, 'handle_plugin_download'));
        add_filter('query_vars', array($this, 'add_query_vars'));
        add_action('template_redirect', array($this, 'handle_download_redirect'));
    }
    
    /**
     * Initialize download system
     */
    public function init_download_system() {
        // Add rewrite rule for clean download URLs
        add_rewrite_rule(
            '^download/([^/]*)/([^/]*)/?', 
            'index.php?download_plugin=$matches[1]&download_token=$matches[2]', 
            'top'
        );
        
        // Create downloads directory if it doesn't exist
        $this->create_downloads_directory();
        
        // Generate plugin ZIP files if they don't exist
        $this->generate_plugin_zips();
    }
    
    /**
     * Add query vars for download system
     */
    public function add_query_vars($vars) {
        $vars[] = 'download_plugin';
        $vars[] = 'download_token';
        return $vars;
    }
    
    /**
     * Handle download redirects
     */
    public function handle_download_redirect() {
        $plugin_slug = get_query_var('download_plugin');
        $token = get_query_var('download_token');
        
        if ($plugin_slug && $token) {
            $this->process_secure_download($plugin_slug, $token);
        }
    }
    
    /**
     * Create secure downloads directory
     */
    private function create_downloads_directory() {
        $uploads_dir = wp_upload_dir();
        $downloads_dir = $uploads_dir['basedir'] . '/plugin-downloads';
        
        if (!file_exists($downloads_dir)) {
            wp_mkdir_p($downloads_dir);
            
            // Create .htaccess to protect direct access
            $htaccess_content = "Order deny,allow\nDeny from all\n";
            file_put_contents($downloads_dir . '/.htaccess', $htaccess_content);
            
            // Create index.php for security
            file_put_contents($downloads_dir . '/index.php', "<?php\n// Silence is golden.\n");
        }
        
        return $downloads_dir;
    }
    
    /**
     * Generate ZIP files for all standalone plugins
     */
    private function generate_plugin_zips() {
        $standalone_plugins_dir = ABSPATH . 'wp-content/standalone-plugins';
        $downloads_dir = $this->create_downloads_directory();
        
        if (!is_dir($standalone_plugins_dir)) {
            return;
        }
        
        $plugins = array(
            'vireo-property-management' => 'Vireo Property Management',
            'vireo-sports-league-manager' => 'Vireo Sports League Manager', 
            'equiprent-equipment-rental' => 'EquipRent Equipment Rental'
        );
        
        foreach ($plugins as $slug => $name) {
            $plugin_dir = $standalone_plugins_dir . '/' . $slug;
            $zip_file = $downloads_dir . '/' . $slug . '.zip';
            
            if (is_dir($plugin_dir) && !file_exists($zip_file)) {
                $this->create_plugin_zip($plugin_dir, $zip_file, $slug);
                error_log("Generated ZIP: $zip_file");
            }
        }
    }
    
    /**
     * Create ZIP file for a plugin
     */
    private function create_plugin_zip($source_dir, $zip_file, $plugin_slug) {
        if (!class_exists('ZipArchive')) {
            error_log('ZipArchive not available for plugin downloads');
            return false;
        }
        
        $zip = new ZipArchive();
        if ($zip->open($zip_file, ZipArchive::CREATE) !== TRUE) {
            error_log("Cannot create ZIP file: $zip_file");
            return false;
        }
        
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source_dir),
            RecursiveIteratorIterator::LEAVES_ONLY
        );
        
        foreach ($files as $file) {
            if (!$file->isDir()) {
                $file_path = $file->getRealPath();
                $relative_path = $plugin_slug . '/' . substr($file_path, strlen($source_dir) + 1);
                
                // Skip hidden files and system files
                if (strpos(basename($file_path), '.') !== 0) {
                    $zip->addFile($file_path, $relative_path);
                }
            }
        }
        
        $result = $zip->close();
        
        if ($result) {
            // Set appropriate file permissions
            chmod($zip_file, 0644);
            return true;
        }
        
        return false;
    }
    
    /**
     * Generate secure download token
     */
    public function generate_download_token($plugin_slug, $expiry_hours = 24) {
        $expiry = time() + ($expiry_hours * HOUR_IN_SECONDS);
        $token_data = array(
            'plugin' => $plugin_slug,
            'expiry' => $expiry,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        );
        
        $token = base64_encode(wp_json_encode($token_data));
        $hash = wp_hash($token . $plugin_slug);
        
        // Store token in database for validation
        $this->store_download_token($hash, $token_data);
        
        return $hash;
    }
    
    /**
     * Store download token
     */
    private function store_download_token($hash, $token_data) {
        $tokens = get_option('vireo_download_tokens', array());
        $tokens[$hash] = $token_data;
        
        // Clean up expired tokens
        $tokens = array_filter($tokens, function($data) {
            return $data['expiry'] > time();
        });
        
        update_option('vireo_download_tokens', $tokens);
    }
    
    /**
     * Validate download token
     */
    private function validate_download_token($plugin_slug, $token) {
        $tokens = get_option('vireo_download_tokens', array());
        
        if (!isset($tokens[$token])) {
            return false;
        }
        
        $token_data = $tokens[$token];
        
        // Check expiry
        if ($token_data['expiry'] <= time()) {
            unset($tokens[$token]);
            update_option('vireo_download_tokens', $tokens);
            return false;
        }
        
        // Check plugin match
        if ($token_data['plugin'] !== $plugin_slug) {
            return false;
        }
        
        // Basic security checks
        $current_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        if ($token_data['ip'] !== $current_ip && $token_data['ip'] !== 'unknown') {
            error_log("IP mismatch for download token: {$token_data['ip']} vs $current_ip");
        }
        
        return true;
    }
    
    /**
     * Process secure download
     */
    private function process_secure_download($plugin_slug, $token) {
        // Validate token
        if (!$this->validate_download_token($plugin_slug, $token)) {
            wp_die('Invalid or expired download link.', 'Download Error', array('response' => 403));
        }
        
        // Get download file
        $downloads_dir = $this->create_downloads_directory();
        $zip_file = $downloads_dir . '/' . $plugin_slug . '.zip';
        
        if (!file_exists($zip_file)) {
            wp_die('Download file not found.', 'Download Error', array('response' => 404));
        }
        
        // Log download
        $this->log_download($plugin_slug, $token);
        
        // Serve file
        $this->serve_download_file($zip_file, $plugin_slug . '.zip');
    }
    
    /**
     * Log download for analytics
     */
    private function log_download($plugin_slug, $token) {
        $download_logs = get_option('vireo_download_logs', array());
        
        $log_entry = array(
            'plugin' => $plugin_slug,
            'token' => substr($token, 0, 8) . '...', // Partial token for privacy
            'timestamp' => current_time('mysql'),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'referer' => $_SERVER['HTTP_REFERER'] ?? 'direct'
        );
        
        $download_logs[] = $log_entry;
        
        // Keep only last 1000 downloads
        if (count($download_logs) > 1000) {
            $download_logs = array_slice($download_logs, -1000);
        }
        
        update_option('vireo_download_logs', $download_logs);
        
        // Update download counter for plugin
        $download_counts = get_option('vireo_download_counts', array());
        $download_counts[$plugin_slug] = ($download_counts[$plugin_slug] ?? 0) + 1;
        update_option('vireo_download_counts', $download_counts);
    }
    
    /**
     * Serve download file
     */
    private function serve_download_file($file_path, $filename) {
        // Clear any output buffers
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        // Set headers
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($file_path));
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: 0');
        
        // Read and output file
        readfile($file_path);
        exit;
    }
    
    /**
     * Get download URL for a plugin
     */
    public function get_download_url($plugin_slug) {
        $token = $this->generate_download_token($plugin_slug);
        return home_url("download/$plugin_slug/$token/");
    }
    
    /**
     * AJAX handler for generating download links
     */
    public function handle_plugin_download() {
        check_ajax_referer('vireo_download_nonce', 'nonce');
        
        $plugin_slug = sanitize_text_field($_POST['plugin_slug'] ?? '');
        
        if (empty($plugin_slug)) {
            wp_send_json_error('Invalid plugin specified.');
        }
        
        $allowed_plugins = array(
            'vireo-property-management',
            'vireo-sports-league-manager', 
            'equiprent-equipment-rental'
        );
        
        if (!in_array($plugin_slug, $allowed_plugins)) {
            wp_send_json_error('Plugin not available for download.');
        }
        
        $download_url = $this->get_download_url($plugin_slug);
        
        wp_send_json_success(array(
            'download_url' => $download_url,
            'message' => 'Download link generated successfully.'
        ));
    }
    
    /**
     * Get download statistics
     */
    public function get_download_stats($plugin_slug = null) {
        $download_counts = get_option('vireo_download_counts', array());
        
        if ($plugin_slug) {
            return $download_counts[$plugin_slug] ?? 0;
        }
        
        return $download_counts;
    }
}

// Initialize download system
new Vireo_Plugin_Downloads();

/**
 * Helper function to get download URL
 */
function vireo_get_plugin_download_url($plugin_slug) {
    $downloads = new Vireo_Plugin_Downloads();
    return $downloads->get_download_url($plugin_slug);
}

/**
 * Helper function to get download count
 */
function vireo_get_plugin_download_count($plugin_slug) {
    $downloads = new Vireo_Plugin_Downloads();
    return $downloads->get_download_stats($plugin_slug);
}