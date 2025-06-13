<?php
/**
 * GymFlow Uninstall Script
 *
 * Fired when the plugin is uninstalled.
 *
 * @package GymFlow
 * @version 1.0.0
 */

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

/**
 * GymFlow Uninstaller Class
 *
 * Handles clean removal of all plugin data when uninstalled.
 */
class GymFlow_Uninstaller {

    /**
     * Run the uninstall process
     */
    public static function uninstall() {
        global $wpdb;

        // Only proceed if user has confirmed data deletion
        $delete_data = get_option('gymflow_delete_data_on_uninstall', false);
        
        if (!$delete_data) {
            return;
        }

        // Remove custom post types and their data
        self::delete_posts();
        
        // Remove custom database tables
        self::delete_tables();
        
        // Remove plugin options
        self::delete_options();
        
        // Remove user meta
        self::delete_user_meta();
        
        // Clear any cached data
        self::clear_cache();
        
        // Remove uploaded files
        self::delete_uploads();
    }

    /**
     * Delete all custom post types and related data
     */
    private static function delete_posts() {
        global $wpdb;

        $post_types = array(
            'gf_member',
            'gf_class',
            'gf_trainer',
            'gf_equipment',
            'gf_booking',
            'gf_membership',
            'gf_payment'
        );

        foreach ($post_types as $post_type) {
            $posts = get_posts(array(
                'post_type' => $post_type,
                'numberposts' => -1,
                'post_status' => 'any'
            ));

            foreach ($posts as $post) {
                wp_delete_post($post->ID, true);
            }
        }

        // Clean up any orphaned meta data
        $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE post_id NOT IN (SELECT id FROM {$wpdb->posts})");
    }

    /**
     * Delete custom database tables
     */
    private static function delete_tables() {
        global $wpdb;

        $tables = array(
            $wpdb->prefix . 'gf_members',
            $wpdb->prefix . 'gf_classes',
            $wpdb->prefix . 'gf_trainers',
            $wpdb->prefix . 'gf_equipment',
            $wpdb->prefix . 'gf_bookings',
            $wpdb->prefix . 'gf_memberships',
            $wpdb->prefix . 'gf_payments',
            $wpdb->prefix . 'gf_check_ins',
            $wpdb->prefix . 'gf_progress_tracking',
            $wpdb->prefix . 'gf_class_schedules',
            $wpdb->prefix . 'gf_equipment_maintenance',
            $wpdb->prefix . 'gf_notifications'
        );

        foreach ($tables as $table) {
            $wpdb->query("DROP TABLE IF EXISTS {$table}");
        }
    }

    /**
     * Delete all plugin options
     */
    private static function delete_options() {
        global $wpdb;

        // Delete all options starting with 'gymflow_'
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'gymflow_%'");
        
        // Delete all options starting with 'gf_'
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'gf_%'");

        // Delete specific transients
        delete_transient('gymflow_member_count');
        delete_transient('gymflow_class_count');
        delete_transient('gymflow_revenue_stats');
        delete_transient('gymflow_attendance_stats');
    }

    /**
     * Delete user meta related to the plugin
     */
    private static function delete_user_meta() {
        global $wpdb;

        $meta_keys = array(
            'gf_member_id',
            'gf_membership_status',
            'gf_member_notes',
            'gf_emergency_contact',
            'gf_health_conditions',
            'gf_trainer_specialties',
            'gf_trainer_bio',
            'gf_check_in_history',
            'gf_progress_photos',
            'gf_workout_programs'
        );

        foreach ($meta_keys as $meta_key) {
            $wpdb->delete(
                $wpdb->usermeta,
                array('meta_key' => $meta_key),
                array('%s')
            );
        }
    }

    /**
     * Clear any cached data
     */
    private static function clear_cache() {
        // Clear WordPress object cache
        wp_cache_flush();
        
        // Clear any page cache if using caching plugins
        if (function_exists('w3tc_flush_all')) {
            w3tc_flush_all();
        }
        
        if (function_exists('wp_cache_clear_cache')) {
            wp_cache_clear_cache();
        }
        
        if (function_exists('rocket_clean_domain')) {
            rocket_clean_domain();
        }
    }

    /**
     * Delete uploaded files
     */
    private static function delete_uploads() {
        $upload_dir = wp_upload_dir();
        $gymflow_dir = $upload_dir['basedir'] . '/gymflow';
        
        if (is_dir($gymflow_dir)) {
            self::delete_directory($gymflow_dir);
        }
    }

    /**
     * Recursively delete a directory and its contents
     */
    private static function delete_directory($dir) {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), array('.', '..'));
        
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                self::delete_directory($path);
            } else {
                unlink($path);
            }
        }
        
        rmdir($dir);
    }
}

// Run the uninstaller
GymFlow_Uninstaller::uninstall();