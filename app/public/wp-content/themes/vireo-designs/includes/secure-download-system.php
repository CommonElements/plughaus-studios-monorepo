<?php
/**
 * Secure Download Delivery System for Vireo Designs
 * Handles secure plugin downloads with tracking and access control
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Vireo_Download_System {
    
    public function __construct() {
        // Always wait for WordPress to be fully loaded
        add_action('wp_loaded', array($this, 'init'));
        add_action('woocommerce_order_status_completed', array($this, 'generate_download_tokens'));
    }
    
    public function init() {
        // Register download endpoint
        add_rewrite_rule('^vireo-download/([^/]+)/?$', 'index.php?vireo_download=$matches[1]', 'top');
        add_query_var('vireo_download');
        add_action('template_redirect', array($this, 'handle_download_request'));
        
        // Flush rewrite rules if needed
        if (get_option('vireo_download_rules_flushed') !== 'yes') {
            flush_rewrite_rules();
            update_option('vireo_download_rules_flushed', 'yes');
        }
        
        // AJAX handlers for download management
        add_action('wp_ajax_regenerate_download_link', array($this, 'regenerate_download_link'));
        add_action('wp_ajax_nopriv_track_download', array($this, 'track_download'));
        
        // Add download links to order emails
        add_action('woocommerce_email_order_details', array($this, 'add_download_links_to_email'), 10, 4);
        
        // Add download management to my account
        add_action('woocommerce_account_dashboard', array($this, 'display_customer_downloads'));
        
        // Clean up expired download tokens
        add_action('vireo_cleanup_expired_downloads', array($this, 'cleanup_expired_downloads'));
        
        // Schedule cleanup if not already scheduled
        if (!wp_next_scheduled('vireo_cleanup_expired_downloads')) {
            wp_schedule_event(time(), 'daily', 'vireo_cleanup_expired_downloads');
        }
    }
    
    public function generate_download_tokens($order_id) {
        $order = wc_get_order($order_id);
        if (!$order) return;
        
        foreach ($order->get_items() as $item) {
            $product = $item->get_product();
            if ($product && has_term('plugins', 'product_cat', $product->get_id())) {
                $this->create_secure_download_link($order, $product);
            }
        }
    }
    
    public function create_secure_download_link($order, $product) {
        // Generate cryptographically secure unique download token
        $token = bin2hex(random_bytes(16)); // 32 character hex string
        
        // Get download files from product
        $downloads = $product->get_downloads();
        
        if (empty($downloads)) {
            error_log('Vireo Download: No download files found for product ' . $product->get_id());
            return false;
        }
        
        // Store download data
        $download_data = array(
            'token' => $token,
            'order_id' => $order->get_id(),
            'product_id' => $product->get_id(),
            'customer_email' => $order->get_billing_email(),
            'created' => current_time('timestamp'),
            'expires' => current_time('timestamp') + (30 * DAY_IN_SECONDS), // 30 days
            'download_count' => 0,
            'download_limit' => 10, // Allow 10 downloads
            'ip_addresses' => array(),
            'files' => $downloads
        );
        
        // Store in database
        global $wpdb;
        $wpdb->insert(
            $wpdb->prefix . 'vireo_downloads',
            array(
                'token' => $token,
                'order_id' => $order->get_id(),
                'product_id' => $product->get_id(),
                'customer_email' => $order->get_billing_email(),
                'download_data' => wp_json_encode($download_data),
                'created_at' => current_time('mysql'),
                'expires_at' => date('Y-m-d H:i:s', $download_data['expires'])
            ),
            array('%s', '%d', '%d', '%s', '%s', '%s', '%s')
        );
        
        // Store token in order meta for easy access
        $order->add_meta_data('_vireo_download_token_' . $product->get_id(), $token);
        $order->save();
        
        return $token;
    }
    
    public function handle_download_request() {
        $token = get_query_var('vireo_download');
        
        if (empty($token)) {
            return;
        }
        
        // Sanitize and validate token format
        $token = sanitize_text_field($token);
        if (!preg_match('/^[a-zA-Z0-9]{32}$/', $token)) {
            wp_die(__('Invalid download token format.', 'vireo-designs'), 'Invalid Token', array('response' => 400));
        }
        
        // Rate limiting for download requests
        $ip = $this->get_client_ip();
        $rate_limit_key = 'vireo_download_attempts_' . md5($ip);
        $current_attempts = get_transient($rate_limit_key);
        
        if ($current_attempts && $current_attempts > 50) { // Max 50 download attempts per hour per IP
            wp_die(__('Too many download attempts. Please try again later.', 'vireo-designs'), 'Rate Limited', array('response' => 429));
        }
        
        set_transient($rate_limit_key, ($current_attempts ? $current_attempts + 1 : 1), HOUR_IN_SECONDS);
        
        // Validate and process download
        $download_data = $this->get_download_data($token);
        
        if (!$download_data) {
            // Log suspicious activity
            error_log("Vireo Security: Invalid download token attempted from IP: {$ip}");
            wp_die(__('Invalid or expired download link.', 'vireo-designs'), 'Download Error', array('response' => 404));
        }
        
        // Check download limits
        if ($download_data['download_count'] >= $download_data['download_limit']) {
            wp_die(__('Download limit exceeded. Please contact support if you need additional downloads.', 'vireo-designs'), 'Download Limit Exceeded', array('response' => 403));
        }
        
        // Check expiration
        if (current_time('timestamp') > $download_data['expires']) {
            wp_die(__('Download link has expired. Please contact support for a new link.', 'vireo-designs'), 'Download Expired', array('response' => 410));
        }
        
        // IP validation - limit to reasonable number of IPs per token
        $max_ips_per_token = 3;
        if (!in_array($ip, $download_data['ip_addresses'])) {
            if (count($download_data['ip_addresses']) >= $max_ips_per_token) {
                error_log("Vireo Security: Token {$token} used from too many IPs. Current IP: {$ip}");
                wp_die(__('This download has been accessed from too many different locations. Please contact support.', 'vireo-designs'), 'Security Check Failed', array('response' => 403));
            }
            $download_data['ip_addresses'][] = $ip;
        }
        
        // Increment download count
        $download_data['download_count']++;
        
        // Update download data
        $this->update_download_data($token, $download_data);
        
        // Log successful download start
        error_log("Vireo Download: Token " . substr($token, 0, 8) . "... downloaded by IP {$ip}");
        
        // Serve the file
        $this->serve_download_file($download_data);
    }
    
    public function serve_download_file($download_data) {
        // Get the first available file (you can extend this to handle multiple files)
        $files = $download_data['files'];
        if (empty($files)) {
            wp_die(__('No download files available.', 'vireo-designs'), 'No Files', array('response' => 404));
        }
        
        $file_data = reset($files);
        $file_path = $file_data['file'];
        
        // Handle different file storage methods
        if (strpos($file_path, 'http') === 0) {
            // External URL - validate and redirect
            if (!filter_var($file_path, FILTER_VALIDATE_URL)) {
                wp_die(__('Invalid download URL.', 'vireo-designs'), 'Invalid URL', array('response' => 400));
            }
            
            // Security: Only allow redirects to trusted domains
            $allowed_domains = apply_filters('vireo_download_allowed_domains', array(
                'vireodesigns.com',
                'downloads.vireodesigns.com',
                parse_url(home_url(), PHP_URL_HOST)
            ));
            
            $url_host = parse_url($file_path, PHP_URL_HOST);
            if (!in_array($url_host, $allowed_domains, true)) {
                wp_die(__('Download not available from external source.', 'vireo-designs'), 'External Download Blocked', array('response' => 403));
            }
            
            wp_safe_redirect($file_path);
            exit;
        } else {
            // Local file - serve securely with path validation
            $file_path = $this->validate_and_sanitize_file_path($file_path);
            
            if (!$file_path || !file_exists($file_path)) {
                wp_die(__('Download file not found.', 'vireo-designs'), 'File Not Found', array('response' => 404));
            }
            
            // Additional security checks
            if (!$this->is_file_allowed($file_path)) {
                wp_die(__('File type not allowed for download.', 'vireo-designs'), 'File Type Blocked', array('response' => 403));
            }
            
            // Rate limiting check
            if (!$this->check_download_rate_limit($download_data)) {
                wp_die(__('Download rate limit exceeded. Please try again later.', 'vireo-designs'), 'Rate Limited', array('response' => 429));
            }
            
            // Set secure headers for download
            $filename = sanitize_file_name(basename($file_path));
            $mime_type = $this->get_safe_mime_type($file_path);
            
            // Clear any previous output
            if (ob_get_level()) {
                ob_end_clean();
            }
            
            // Security headers
            header('X-Content-Type-Options: nosniff');
            header('X-Frame-Options: DENY');
            header('X-XSS-Protection: 1; mode=block');
            header('Referrer-Policy: no-referrer');
            
            // Download headers
            header('Content-Type: ' . $mime_type);
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . filesize($file_path));
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            
            // Serve file in chunks for large files
            $this->readfile_chunked($file_path);
            exit;
        }
    }
    
    public function get_download_data($token) {
        global $wpdb;
        
        $result = $wpdb->get_row($wpdb->prepare(
            "SELECT download_data FROM {$wpdb->prefix}vireo_downloads WHERE token = %s AND expires_at > NOW()",
            $token
        ));
        
        if (!$result) {
            return false;
        }
        
        return json_decode($result->download_data, true);
    }
    
    public function update_download_data($token, $data) {
        global $wpdb;
        
        $wpdb->update(
            $wpdb->prefix . 'vireo_downloads',
            array('download_data' => wp_json_encode($data)),
            array('token' => $token),
            array('%s'),
            array('%s')
        );
    }
    
    public function regenerate_download_link() {
        check_ajax_referer('vireo_download_nonce', 'nonce');
        
        $order_id = intval($_POST['order_id']);
        $product_id = intval($_POST['product_id']);
        
        // Verify user owns this order
        $order = wc_get_order($order_id);
        if (!$order || $order->get_user_id() !== get_current_user_id()) {
            wp_die(__('Access denied.', 'vireo-designs'));
        }
        
        $product = wc_get_product($product_id);
        if (!$product) {
            wp_die(__('Product not found.', 'vireo-designs'));
        }
        
        // Invalidate old token
        $old_token = $order->get_meta('_vireo_download_token_' . $product_id);
        if ($old_token) {
            $this->invalidate_download_token($old_token);
        }
        
        // Generate new token
        $new_token = $this->create_secure_download_link($order, $product);
        
        wp_send_json_success(array(
            'download_url' => home_url('/vireo-download/' . $new_token),
            'message' => __('New download link generated successfully.', 'vireo-designs')
        ));
    }
    
    public function invalidate_download_token($token) {
        global $wpdb;
        
        $wpdb->update(
            $wpdb->prefix . 'vireo_downloads',
            array('expires_at' => current_time('mysql')),
            array('token' => $token),
            array('%s'),
            array('%s')
        );
    }
    
    public function track_download() {
        // Security: Verify nonce for tracking requests
        if (!check_ajax_referer('vireo_download_track', 'nonce', false)) {
            wp_send_json_error('Security check failed');
        }
        
        $token = sanitize_text_field($_POST['token'] ?? '');
        $event = sanitize_text_field($_POST['event'] ?? '');
        
        if (empty($token) || empty($event)) {
            wp_send_json_error('Invalid parameters');
        }
        
        // Validate event type
        $allowed_events = array('download_started', 'download_completed', 'download_failed');
        if (!in_array($event, $allowed_events, true)) {
            wp_send_json_error('Invalid event type');
        }
        
        // Rate limiting for tracking calls
        $ip = $this->get_client_ip();
        $rate_limit_key = 'vireo_track_rate_' . md5($ip);
        $current_count = get_transient($rate_limit_key);
        
        if ($current_count && $current_count > 100) { // Max 100 tracking calls per hour per IP
            wp_send_json_error('Rate limit exceeded');
        }
        
        set_transient($rate_limit_key, ($current_count ? $current_count + 1 : 1), HOUR_IN_SECONDS);
        
        // Log download tracking event (sanitized)
        error_log("Vireo Download Tracking: " . sanitize_text_field($event) . " for token " . substr($token, 0, 8) . "...");
        
        wp_send_json_success();
    }
    
    public function add_download_links_to_email($order, $sent_to_admin, $plain_text, $email) {
        if ($sent_to_admin || $plain_text) return;
        
        $download_links = array();
        foreach ($order->get_items() as $item) {
            $product = $item->get_product();
            if ($product && has_term('plugins', 'product_cat', $product->get_id())) {
                $token = $order->get_meta('_vireo_download_token_' . $product->get_id());
                if ($token) {
                    $download_links[] = array(
                        'name' => $product->get_name(),
                        'url' => home_url('/vireo-download/' . $token)
                    );
                }
            }
        }
        
        if (!empty($download_links)) {
            echo '<div class="download-links-section">';
            echo '<h3>Download Your Plugins</h3>';
            foreach ($download_links as $link) {
                echo '<p><a href="' . esc_url($link['url']) . '" style="color: #059669; text-decoration: none; font-weight: bold;">';
                echo '📦 Download ' . esc_html($link['name']);
                echo '</a></p>';
            }
            echo '<p><small>Download links are valid for 30 days and limited to 10 downloads per plugin.</small></p>';
            echo '</div>';
        }
    }
    
    public function display_customer_downloads() {
        if (!is_user_logged_in()) return;
        
        $user_id = get_current_user_id();
        $orders = wc_get_orders(array(
            'customer_id' => $user_id,
            'status' => array('completed'),
            'limit' => -1
        ));
        
        $downloads = array();
        foreach ($orders as $order) {
            foreach ($order->get_items() as $item) {
                $product = $item->get_product();
                if ($product && has_term('plugins', 'product_cat', $product->get_id())) {
                    $token = $order->get_meta('_vireo_download_token_' . $product->get_id());
                    if ($token) {
                        $downloads[] = array(
                            'order_id' => $order->get_id(),
                            'product_id' => $product->get_id(),
                            'product_name' => $product->get_name(),
                            'token' => $token,
                            'order_date' => $order->get_date_created()
                        );
                    }
                }
            }
        }
        
        if (!empty($downloads)) {
            echo '<div class="customer-downloads-section">';
            echo '<h3>Your Plugin Downloads</h3>';
            echo '<div class="downloads-grid">';
            
            foreach ($downloads as $download) {
                echo '<div class="download-item">';
                echo '<h4>' . esc_html($download['product_name']) . '</h4>';
                echo '<p>Purchased: ' . $download['order_date']->format('M j, Y') . '</p>';
                echo '<div class="download-actions">';
                echo '<a href="' . home_url('/vireo-download/' . $download['token']) . '" class="button">Download</a>';
                echo '<button class="button regenerate-link" data-order="' . $download['order_id'] . '" data-product="' . $download['product_id'] . '">Regenerate Link</button>';
                echo '</div>';
                echo '</div>';
            }
            
            echo '</div>';
            echo '</div>';
            
            // Add JavaScript for regenerate functionality
            $this->add_download_management_script();
        }
    }
    
    public function cleanup_expired_downloads() {
        global $wpdb;
        
        $deleted = $wpdb->query(
            "DELETE FROM {$wpdb->prefix}vireo_downloads WHERE expires_at < NOW()"
        );
        
        if ($deleted > 0) {
            error_log("Vireo Downloads: Cleaned up {$deleted} expired download tokens");
        }
    }
    
    private function get_client_ip() {
        $ip_keys = array('HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR');
        
        foreach ($ip_keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }
    
    /**
     * Validate and sanitize file path to prevent directory traversal attacks
     */
    private function validate_and_sanitize_file_path($file_path) {
        // Remove any URL encoding
        $file_path = urldecode($file_path);
        
        // Convert to absolute path safely
        if (strpos($file_path, site_url()) === 0) {
            $file_path = str_replace(site_url('/'), ABSPATH, $file_path);
        } elseif (strpos($file_path, '/') !== 0) {
            // Relative path - make it absolute from wp-content/uploads
            $upload_dir = wp_upload_dir();
            $file_path = $upload_dir['basedir'] . '/' . ltrim($file_path, '/');
        }
        
        // Resolve path to prevent directory traversal
        $real_path = realpath($file_path);
        if (!$real_path) {
            return false;
        }
        
        // Ensure file is within allowed directories
        $allowed_dirs = array(
            realpath(ABSPATH . 'wp-content/uploads'),
            realpath(WP_CONTENT_DIR . '/plugins'),
            realpath(WP_CONTENT_DIR . '/themes')
        );
        
        $is_allowed = false;
        foreach ($allowed_dirs as $allowed_dir) {
            if ($allowed_dir && strpos($real_path, $allowed_dir) === 0) {
                $is_allowed = true;
                break;
            }
        }
        
        return $is_allowed ? $real_path : false;
    }
    
    /**
     * Check if file type is allowed for download
     */
    private function is_file_allowed($file_path) {
        $allowed_extensions = apply_filters('vireo_download_allowed_extensions', array(
            'zip', 'tar', 'gz', 'pdf', 'doc', 'docx', 'txt'
        ));
        
        $file_extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
        
        // Block dangerous file types
        $blocked_extensions = array(
            'php', 'php3', 'php4', 'php5', 'phtml', 'exe', 'bat', 'cmd', 
            'com', 'scr', 'vbs', 'js', 'jar', 'sh', 'pl', 'py'
        );
        
        if (in_array($file_extension, $blocked_extensions, true)) {
            return false;
        }
        
        return in_array($file_extension, $allowed_extensions, true);
    }
    
    /**
     * Get safe MIME type for file
     */
    private function get_safe_mime_type($file_path) {
        $file_extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
        
        $safe_mime_types = array(
            'zip' => 'application/zip',
            'tar' => 'application/x-tar',
            'gz' => 'application/gzip',
            'pdf' => 'application/pdf',
            'txt' => 'text/plain',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        );
        
        return $safe_mime_types[$file_extension] ?? 'application/octet-stream';
    }
    
    /**
     * Check download rate limiting
     */
    private function check_download_rate_limit($download_data) {
        $ip = $this->get_client_ip();
        $rate_limit_key = 'vireo_download_rate_' . md5($ip);
        $current_count = get_transient($rate_limit_key);
        
        // Allow max 10 downloads per hour per IP
        if ($current_count && $current_count >= 10) {
            return false;
        }
        
        set_transient($rate_limit_key, ($current_count ? $current_count + 1 : 1), HOUR_IN_SECONDS);
        return true;
    }
    
    /**
     * Read file in chunks to handle large files efficiently
     */
    private function readfile_chunked($file_path) {
        $chunk_size = 8192; // 8KB chunks
        $handle = fopen($file_path, 'rb');
        
        if ($handle === false) {
            return false;
        }
        
        while (!feof($handle)) {
            $chunk = fread($handle, $chunk_size);
            if ($chunk === false) {
                break;
            }
            echo $chunk;
            
            // Flush output to browser
            if (ob_get_level()) {
                ob_flush();
            }
            flush();
        }
        
        fclose($handle);
        return true;
    }
    
    private function add_download_management_script() {
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const regenerateButtons = document.querySelectorAll('.regenerate-link');
            
            regenerateButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const orderId = this.dataset.order;
                    const productId = this.dataset.product;
                    
                    if (confirm('Generate a new download link? This will invalidate the current link.')) {
                        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: new URLSearchParams({
                                action: 'regenerate_download_link',
                                order_id: orderId,
                                product_id: productId,
                                nonce: '<?php echo wp_create_nonce('vireo_download_nonce'); ?>'
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert(data.data.message);
                                location.reload();
                            } else {
                                alert('Error generating new link. Please try again.');
                            }
                        })
                        .catch(error => {
                            alert('Network error. Please try again.');
                        });
                    }
                });
            });
        });
        </script>
        
        <style>
        .customer-downloads-section {
            margin: 2rem 0;
            padding: 1.5rem;
            background: #f9f9f9;
            border-radius: 8px;
        }
        .downloads-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        .download-item {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .download-item h4 {
            margin: 0 0 0.5rem 0;
            color: #059669;
        }
        .download-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        .download-actions .button {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
        }
        </style>
        <?php
    }
}

// Initialize download system after WordPress is loaded
add_action('plugins_loaded', function() {
    new Vireo_Download_System();
});

// Create downloads table on activation
register_activation_hook(__FILE__, function() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'vireo_downloads';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        token varchar(64) NOT NULL,
        order_id mediumint(9) NOT NULL,
        product_id mediumint(9) NOT NULL,
        customer_email varchar(255) NOT NULL,
        download_data longtext NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        expires_at datetime NOT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY token (token),
        KEY order_id (order_id),
        KEY expires_at (expires_at)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
});
?>