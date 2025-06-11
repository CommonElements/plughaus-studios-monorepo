<?php
/**
 * PlugHaus Property Management Uninstall Script
 * 
 * This file is executed when the plugin is deleted via the WordPress admin.
 * It removes all plugin data including:
 * - Database tables
 * - Options and transients
 * - User capabilities
 * - Scheduled events
 * - Plugin pages
 * - Uploaded files
 */

// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Define plugin constants for uninstall
if (!defined('PHPM_PLUGIN_DIR')) {
    define('PHPM_PLUGIN_DIR', plugin_dir_path(__FILE__));
}

// Load utilities class for logging
if (file_exists(PHPM_PLUGIN_DIR . 'core/includes/shared/class-phpm-utilities.php')) {
    require_once PHPM_PLUGIN_DIR . 'core/includes/shared/class-phpm-utilities.php';
}

/**
 * Main uninstall class
 */
class PHPM_Uninstaller {
    
    /**
     * Run the uninstall process
     */
    public static function uninstall() {
        // Check if user has permission to delete plugins
        if (!current_user_can('delete_plugins')) {
            return;
        }
        
        // Get plugin settings to check if data should be preserved
        $settings = get_option('phpm_settings', array());
        $preserve_data = isset($settings['preserve_data_on_uninstall']) ? $settings['preserve_data_on_uninstall'] : false;
        
        // If preserve data is enabled, only remove plugin-specific settings
        if ($preserve_data) {
            self::preserve_data_uninstall();
        } else {
            self::complete_uninstall();
        }
        
        // Always remove plugin-specific options
        self::remove_plugin_options();
        
        // Log uninstall
        if (class_exists('PHPM_Utilities')) {
            PHPM_Utilities::log('Plugin uninstalled successfully', 'info');
        }
    }
    
    /**
     * Complete uninstall - removes all data
     */
    private static function complete_uninstall() {
        // Remove custom database tables
        self::remove_database_tables();
        
        // Remove all posts and meta data
        self::remove_posts_and_meta();
        
        // Remove taxonomies and terms
        self::remove_taxonomies();
        
        // Remove user capabilities
        self::remove_capabilities();
        
        // Remove uploaded files
        self::remove_uploaded_files();
        
        // Remove plugin pages
        self::remove_plugin_pages();
        
        // Clear scheduled events
        self::clear_scheduled_events();
        
        // Remove all transients
        self::remove_transients();
    }
    
    /**
     * Preserve data uninstall - keeps core data but removes plugin settings
     */
    private static function preserve_data_uninstall() {
        // Only remove plugin-specific options and transients
        self::remove_transients();
        self::clear_scheduled_events();
        
        // Keep posts, database tables, and user data
        // Only remove plugin configuration
    }
    
    /**
     * Remove custom database tables
     */
    private static function remove_database_tables() {
        global $wpdb;
        
        $tables = array(
            $wpdb->prefix . 'phpm_property_views',
            $wpdb->prefix . 'phpm_maintenance_log',
            $wpdb->prefix . 'phpm_payments',
            $wpdb->prefix . 'phpm_lease_history',
            $wpdb->prefix . 'phpm_documents'
        );
        
        foreach ($tables as $table) {
            $wpdb->query("DROP TABLE IF EXISTS $table");
        }
    }
    
    /**
     * Remove all plugin posts and metadata
     */
    private static function remove_posts_and_meta() {
        global $wpdb;
        
        $post_types = array(
            'phpm_property',
            'phpm_unit', 
            'phpm_tenant',
            'phpm_lease',
            'phpm_maintenance'
        );
        
        foreach ($post_types as $post_type) {
            // Get all posts of this type
            $posts = get_posts(array(
                'post_type' => $post_type,
                'numberposts' => -1,
                'post_status' => 'any',
                'fields' => 'ids'
            ));
            
            // Delete each post and its metadata
            foreach ($posts as $post_id) {
                wp_delete_post($post_id, true);
            }
        }
        
        // Remove any orphaned meta data
        $wpdb->query("DELETE FROM $wpdb->postmeta WHERE meta_key LIKE '_phpm_%'");
        
        // Remove post type entries from posts table (cleanup)
        $post_types_string = "'" . implode("','", $post_types) . "'";
        $wpdb->query("DELETE FROM $wpdb->posts WHERE post_type IN ($post_types_string)");
    }
    
    /**
     * Remove taxonomies and terms
     */
    private static function remove_taxonomies() {
        $taxonomies = array(
            'phpm_property_type',
            'phpm_amenities',
            'phpm_location'
        );
        
        foreach ($taxonomies as $taxonomy) {
            // Get all terms
            $terms = get_terms(array(
                'taxonomy' => $taxonomy,
                'hide_empty' => false,
                'fields' => 'ids'
            ));
            
            // Delete each term
            if (!is_wp_error($terms)) {
                foreach ($terms as $term_id) {
                    wp_delete_term($term_id, $taxonomy);
                }
            }
        }
    }
    
    /**
     * Remove user capabilities
     */
    private static function remove_capabilities() {
        $capabilities = array(
            // Properties
            'edit_phpm_property',
            'read_phpm_property',
            'delete_phpm_property',
            'edit_phpm_properties',
            'edit_others_phpm_properties',
            'publish_phpm_properties',
            'read_private_phpm_properties',
            'delete_phpm_properties',
            'delete_private_phpm_properties',
            'delete_published_phpm_properties',
            'delete_others_phpm_properties',
            'edit_private_phpm_properties',
            'edit_published_phpm_properties',
            
            // Units
            'edit_phpm_unit',
            'read_phpm_unit',
            'delete_phpm_unit',
            'edit_phpm_units',
            'edit_others_phpm_units',
            'publish_phpm_units',
            'read_private_phpm_units',
            'delete_phpm_units',
            'delete_private_phpm_units',
            'delete_published_phpm_units',
            'delete_others_phpm_units',
            'edit_private_phpm_units',
            'edit_published_phpm_units',
            
            // Tenants
            'edit_phpm_tenant',
            'read_phpm_tenant',
            'delete_phpm_tenant',
            'edit_phpm_tenants',
            'edit_others_phpm_tenants',
            'publish_phpm_tenants',
            'read_private_phpm_tenants',
            'delete_phpm_tenants',
            'delete_private_phpm_tenants',
            'delete_published_phpm_tenants',
            'delete_others_phpm_tenants',
            'edit_private_phpm_tenants',
            'edit_published_phpm_tenants',
            
            // Leases
            'edit_phpm_lease',
            'read_phpm_lease',
            'delete_phpm_lease',
            'edit_phpm_leases',
            'edit_others_phpm_leases',
            'publish_phpm_leases',
            'read_private_phpm_leases',
            'delete_phpm_leases',
            'delete_private_phpm_leases',
            'delete_published_phpm_leases',
            'delete_others_phpm_leases',
            'edit_private_phpm_leases',
            'edit_published_phpm_leases',
            
            // Maintenance
            'edit_phpm_maintenance',
            'read_phpm_maintenance',
            'delete_phpm_maintenance',
            'edit_phpm_maintenances',
            'edit_others_phpm_maintenances',
            'publish_phpm_maintenances',
            'read_private_phpm_maintenances',
            'delete_phpm_maintenances',
            'delete_private_phpm_maintenances',
            'delete_published_phpm_maintenances',
            'delete_others_phpm_maintenances',
            'edit_private_phpm_maintenances',
            'edit_published_phpm_maintenances',
        );
        
        // Remove capabilities from all roles
        global $wp_roles;
        
        if (class_exists('WP_Roles') && isset($wp_roles)) {
            foreach ($wp_roles->roles as $role_name => $role_info) {
                $role = get_role($role_name);
                if ($role) {
                    foreach ($capabilities as $cap) {
                        $role->remove_cap($cap);
                    }
                }
            }
        }
        
        // Remove custom roles
        remove_role('phpm_property_manager');
        remove_role('phpm_tenant');
    }
    
    /**
     * Remove uploaded files
     */
    private static function remove_uploaded_files() {
        $upload_dir = wp_upload_dir();
        $phpm_upload_dir = $upload_dir['basedir'] . '/phpm-files/';
        
        if (is_dir($phpm_upload_dir)) {
            self::delete_directory($phpm_upload_dir);
        }
        
        // Remove any files stored in the documents table locations
        global $wpdb;
        $table_name = $wpdb->prefix . 'phpm_documents';
        
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
            $documents = $wpdb->get_results("SELECT file_path FROM $table_name");
            
            foreach ($documents as $document) {
                if (file_exists($document->file_path)) {
                    wp_delete_file($document->file_path);
                }
            }
        }
    }
    
    /**
     * Remove plugin-created pages
     */
    private static function remove_plugin_pages() {
        $page_options = array(
            'phpm_properties_page_id',
            'phpm_tenant_portal_page_id',
            'phpm_maintenance_page_id'
        );
        
        foreach ($page_options as $option) {
            $page_id = get_option($option);
            if ($page_id) {
                wp_delete_post($page_id, true);
                delete_option($option);
            }
        }
        
        // Also remove pages by slug
        $page_slugs = array(
            'tenant-portal',
            'maintenance-request',
            'property-listings'
        );
        
        foreach ($page_slugs as $slug) {
            $page = get_page_by_path($slug);
            if ($page) {
                wp_delete_post($page->ID, true);
            }
        }
    }
    
    /**
     * Clear scheduled events
     */
    private static function clear_scheduled_events() {
        $events = array(
            'phpm_daily_lease_check',
            'phpm_weekly_cleanup',
            'phpm_monthly_reports',
            'phpm_weekly_reports'
        );
        
        foreach ($events as $event) {
            $timestamp = wp_next_scheduled($event);
            if ($timestamp) {
                wp_unschedule_event($timestamp, $event);
            }
        }
    }
    
    /**
     * Remove plugin options
     */
    private static function remove_plugin_options() {
        $options = array(
            'phpm_settings',
            'phpm_version',
            'phpm_activation_date',
            'phpm_error_logs',
            'phpm_properties_page_id',
            'phpm_tenant_portal_page_id',
            'phpm_maintenance_page_id'
        );
        
        foreach ($options as $option) {
            delete_option($option);
        }
        
        // Remove any options that start with phpm_
        global $wpdb;
        $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE 'phpm_%'");
    }
    
    /**
     * Remove all plugin transients
     */
    private static function remove_transients() {
        global $wpdb;
        
        // Remove all transients with our prefix
        $wpdb->query(
            "DELETE FROM $wpdb->options 
            WHERE option_name LIKE '_transient_phpm_%' 
            OR option_name LIKE '_transient_timeout_phpm_%'"
        );
    }
    
    /**
     * Recursively delete a directory and its contents
     */
    private static function delete_directory($dir) {
        if (!is_dir($dir)) {
            return false;
        }
        
        $files = array_diff(scandir($dir), array('.', '..'));
        
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            
            if (is_dir($path)) {
                self::delete_directory($path);
            } else {
                unlink($path);
            }
        }
        
        return rmdir($dir);
    }
    
    /**
     * Get confirmation from user before proceeding
     */
    public static function confirm_uninstall() {
        $settings = get_option('phpm_settings', array());
        $preserve_data = isset($settings['preserve_data_on_uninstall']) ? $settings['preserve_data_on_uninstall'] : false;
        
        if ($preserve_data) {
            return true; // Skip confirmation if data is being preserved
        }
        
        // In a real-world scenario, this would be handled by JavaScript
        // For now, we'll proceed with uninstall
        return true;
    }
}

// Run the uninstall
if (PHPM_Uninstaller::confirm_uninstall()) {
    PHPM_Uninstaller::uninstall();
}