<?php
/**
 * GymFlow Deactivator Class
 *
 * Handles plugin deactivation tasks including cleanup and data preservation
 *
 * @package GymFlow
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * GF_Deactivator Class
 *
 * Fired during plugin deactivation
 */
class GF_Deactivator {

    /**
     * Plugin deactivation handler
     *
     * Performs cleanup tasks while preserving user data
     */
    public static function deactivate() {
        // Clear scheduled events
        self::clear_scheduled_events();

        // Flush rewrite rules
        flush_rewrite_rules();

        // Clear transients
        self::clear_transients();

        // Set deactivation flag
        update_option('gymflow_deactivation_time', current_time('timestamp'));

        // Log deactivation
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('GymFlow plugin deactivated');
        }

        // Show deactivation notice
        self::set_deactivation_notice();
    }

    /**
     * Clear all scheduled events
     */
    private static function clear_scheduled_events() {
        $events = array(
            'gymflow_daily_cleanup',
            'gymflow_membership_check',
            'gymflow_weekly_reports',
            'gymflow_monthly_reports',
            'gymflow_backup_database',
            'gymflow_send_reminders',
            'gymflow_process_renewals',
            'gymflow_clean_expired_sessions'
        );

        foreach ($events as $event) {
            $timestamp = wp_next_scheduled($event);
            if ($timestamp) {
                wp_unschedule_event($timestamp, $event);
            }
            
            // Clear all instances of recurring events
            wp_clear_scheduled_hook($event);
        }
    }

    /**
     * Clear plugin transients
     */
    private static function clear_transients() {
        global $wpdb;

        // Delete transients starting with gymflow_
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_gymflow_%'");
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_gymflow_%'");
        
        // Delete site transients
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_site_transient_gymflow_%'");
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_site_transient_timeout_gymflow_%'");

        // Clear specific known transients
        $transients = array(
            'gymflow_member_count',
            'gymflow_class_count',
            'gymflow_trainer_count',
            'gymflow_equipment_count',
            'gymflow_revenue_stats',
            'gymflow_attendance_stats',
            'gymflow_popular_classes',
            'gymflow_membership_stats',
            'gymflow_equipment_usage',
            'gymflow_system_status'
        );

        foreach ($transients as $transient) {
            delete_transient($transient);
            delete_site_transient($transient);
        }
    }

    /**
     * Set deactivation notice for admin
     */
    private static function set_deactivation_notice() {
        $notice = array(
            'type' => 'info',
            'message' => sprintf(
                __('GymFlow has been deactivated. Your data has been preserved and will be available when you reactivate the plugin. Need help? <a href="%s" target="_blank">Contact Support</a>', 'gymflow'),
                'https://vireodesigns.com/support'
            ),
            'dismissible' => true,
            'expires' => time() + (7 * DAY_IN_SECONDS) // Show for 7 days
        );

        update_option('gymflow_deactivation_notice', $notice);
    }

    /**
     * Clean up temporary files (optional - only on complete removal)
     */
    public static function cleanup_temp_files() {
        $upload_dir = wp_upload_dir();
        $temp_dir = $upload_dir['basedir'] . '/gymflow/temp';

        if (is_dir($temp_dir)) {
            self::delete_directory_recursive($temp_dir);
        }

        // Clean up any cached files
        $cache_dir = $upload_dir['basedir'] . '/gymflow/cache';
        if (is_dir($cache_dir)) {
            self::delete_directory_recursive($cache_dir);
        }
    }

    /**
     * Remove user capabilities (optional - only on complete removal)
     */
    public static function remove_capabilities() {
        // Remove capabilities from administrator
        $admin_role = get_role('administrator');
        if ($admin_role) {
            $admin_role->remove_cap('manage_gymflow');
            $admin_role->remove_cap('edit_gymflow');
            $admin_role->remove_cap('view_gymflow_reports');
            $admin_role->remove_cap('manage_gymflow_members');
            $admin_role->remove_cap('manage_gymflow_classes');
            $admin_role->remove_cap('manage_gymflow_trainers');
            $admin_role->remove_cap('manage_gymflow_equipment');
            $admin_role->remove_cap('manage_gymflow_bookings');
            $admin_role->remove_cap('process_gymflow_payments');
        }

        // Remove custom roles
        remove_role('gymflow_manager');
        remove_role('gymflow_staff');
        remove_role('gymflow_trainer');
    }

    /**
     * Create data export before deactivation (Pro feature)
     */
    public static function create_data_export() {
        // This would be a Pro feature to automatically export all data
        // before deactivation for backup purposes
        
        if (!self::is_pro_version()) {
            return false;
        }

        $export_data = array(
            'members' => self::export_members_data(),
            'classes' => self::export_classes_data(),
            'trainers' => self::export_trainers_data(),
            'equipment' => self::export_equipment_data(),
            'bookings' => self::export_bookings_data(),
            'payments' => self::export_payments_data(),
            'settings' => self::export_settings_data()
        );

        $upload_dir = wp_upload_dir();
        $export_file = $upload_dir['basedir'] . '/gymflow/exports/deactivation_backup_' . date('Y-m-d_H-i-s') . '.json';

        if (!file_exists(dirname($export_file))) {
            wp_mkdir_p(dirname($export_file));
        }

        file_put_contents($export_file, json_encode($export_data, JSON_PRETTY_PRINT));

        return $export_file;
    }

    /**
     * Check if this is the pro version
     */
    private static function is_pro_version() {
        return function_exists('gymflow_check_pro_license') && gymflow_check_pro_license();
    }

    /**
     * Export members data
     */
    private static function export_members_data() {
        global $wpdb;
        
        $members_table = $wpdb->prefix . 'gf_members';
        return $wpdb->get_results("SELECT * FROM {$members_table}", ARRAY_A);
    }

    /**
     * Export classes data
     */
    private static function export_classes_data() {
        global $wpdb;
        
        $classes_table = $wpdb->prefix . 'gf_classes';
        $schedules_table = $wpdb->prefix . 'gf_class_schedules';
        
        return array(
            'classes' => $wpdb->get_results("SELECT * FROM {$classes_table}", ARRAY_A),
            'schedules' => $wpdb->get_results("SELECT * FROM {$schedules_table}", ARRAY_A)
        );
    }

    /**
     * Export trainers data
     */
    private static function export_trainers_data() {
        global $wpdb;
        
        $trainers_table = $wpdb->prefix . 'gf_trainers';
        return $wpdb->get_results("SELECT * FROM {$trainers_table}", ARRAY_A);
    }

    /**
     * Export equipment data
     */
    private static function export_equipment_data() {
        global $wpdb;
        
        $equipment_table = $wpdb->prefix . 'gf_equipment';
        return $wpdb->get_results("SELECT * FROM {$equipment_table}", ARRAY_A);
    }

    /**
     * Export bookings data
     */
    private static function export_bookings_data() {
        global $wpdb;
        
        $bookings_table = $wpdb->prefix . 'gf_bookings';
        return $wpdb->get_results("SELECT * FROM {$bookings_table}", ARRAY_A);
    }

    /**
     * Export payments data
     */
    private static function export_payments_data() {
        global $wpdb;
        
        $payments_table = $wpdb->prefix . 'gf_payments';
        return $wpdb->get_results("SELECT * FROM {$payments_table}", ARRAY_A);
    }

    /**
     * Export settings data
     */
    private static function export_settings_data() {
        global $wpdb;
        
        $options = $wpdb->get_results(
            "SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE 'gymflow_%'",
            ARRAY_A
        );

        $settings = array();
        foreach ($options as $option) {
            $settings[$option['option_name']] = maybe_unserialize($option['option_value']);
        }

        return $settings;
    }

    /**
     * Recursively delete directory
     */
    private static function delete_directory_recursive($dir) {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), array('.', '..'));
        
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                self::delete_directory_recursive($path);
            } else {
                unlink($path);
            }
        }
        
        rmdir($dir);
    }

    /**
     * Send deactivation feedback (optional)
     */
    public static function send_deactivation_feedback($reason = '', $details = '') {
        if (empty($reason)) {
            return;
        }

        $site_url = get_site_url();
        $admin_email = get_option('admin_email');
        
        $feedback_data = array(
            'site_url' => $site_url,
            'admin_email' => $admin_email,
            'reason' => sanitize_text_field($reason),
            'details' => sanitize_textarea_field($details),
            'plugin_version' => defined('GYMFLOW_VERSION') ? GYMFLOW_VERSION : 'unknown',
            'wp_version' => get_bloginfo('version'),
            'php_version' => PHP_VERSION,
            'deactivation_date' => current_time('mysql')
        );

        // This would send feedback to our servers (if user opts in)
        // For now, just log it locally
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('GymFlow Deactivation Feedback: ' . json_encode($feedback_data));
        }
    }

    /**
     * Show feedback modal on deactivation (Pro feature)
     */
    public static function show_deactivation_feedback_modal() {
        if (!self::is_pro_version()) {
            return;
        }

        // This would show a modal asking for deactivation feedback
        // Implementation would be in JavaScript
        add_action('admin_footer', array(__CLASS__, 'render_feedback_modal'));
    }

    /**
     * Render feedback modal HTML
     */
    public static function render_feedback_modal() {
        ?>
        <div id="gymflow-deactivation-modal" style="display: none;">
            <div class="gymflow-modal-content">
                <h3><?php _e('Quick Feedback', 'gymflow'); ?></h3>
                <p><?php _e('We\'re sorry to see you go! Could you tell us why you\'re deactivating GymFlow?', 'gymflow'); ?></p>
                
                <form id="gymflow-deactivation-form">
                    <label>
                        <input type="radio" name="reason" value="temporary">
                        <?php _e('It\'s only temporary', 'gymflow'); ?>
                    </label>
                    
                    <label>
                        <input type="radio" name="reason" value="missing_features">
                        <?php _e('Missing features I need', 'gymflow'); ?>
                    </label>
                    
                    <label>
                        <input type="radio" name="reason" value="found_better">
                        <?php _e('Found a better plugin', 'gymflow'); ?>
                    </label>
                    
                    <label>
                        <input type="radio" name="reason" value="too_complex">
                        <?php _e('Too complex to set up', 'gymflow'); ?>
                    </label>
                    
                    <label>
                        <input type="radio" name="reason" value="other">
                        <?php _e('Other', 'gymflow'); ?>
                    </label>
                    
                    <textarea name="details" placeholder="<?php _e('Additional details (optional)', 'gymflow'); ?>"></textarea>
                    
                    <div class="gymflow-modal-actions">
                        <button type="submit" class="button button-primary">
                            <?php _e('Submit & Deactivate', 'gymflow'); ?>
                        </button>
                        <button type="button" class="button gymflow-skip-feedback">
                            <?php _e('Skip & Deactivate', 'gymflow'); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <?php
    }
}