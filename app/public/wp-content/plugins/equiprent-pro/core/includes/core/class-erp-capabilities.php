<?php
/**
 * Define custom capabilities for EquipRent Pro
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * The capabilities management class
 */
class ERP_Capabilities {

    /**
     * Initialize capabilities
     */
    public static function init() {
        add_action('admin_init', array(__CLASS__, 'add_capabilities'));
    }

    /**
     * Add custom capabilities to roles
     */
    public static function add_capabilities() {
        // Only run this once
        if (get_option('erp_capabilities_added') === 'yes') {
            return;
        }

        $capabilities = self::get_capabilities();

        // Add capabilities to administrator role
        $admin = get_role('administrator');
        if ($admin) {
            foreach ($capabilities as $cap) {
                $admin->add_cap($cap);
            }
        }

        // Create or update equipment manager role
        $equipment_manager_caps = array(
            'read' => true,
            'upload_files' => true,
            'manage_equipment' => true,
            'edit_equipment' => true,
            'view_equipment' => true,
            'delete_equipment' => true,
            'manage_bookings' => true,
            'edit_bookings' => true,
            'view_bookings' => true,
            'delete_bookings' => true,
            'manage_customers' => true,
            'edit_customers' => true,
            'view_customers' => true,
            'delete_customers' => true,
            'manage_maintenance' => true,
            'view_reports' => true,
        );

        remove_role('equipment_manager');
        add_role('equipment_manager', __('Equipment Manager', 'equiprent-pro'), $equipment_manager_caps);

        // Create or update rental agent role
        $rental_agent_caps = array(
            'read' => true,
            'upload_files' => true,
            'view_equipment' => true,
            'manage_bookings' => true,
            'edit_bookings' => true,
            'view_bookings' => true,
            'manage_customers' => true,
            'edit_customers' => true,
            'view_customers' => true,
            'view_reports' => true,
        );

        remove_role('rental_agent');
        add_role('rental_agent', __('Rental Agent', 'equiprent-pro'), $rental_agent_caps);

        // Mark as completed
        update_option('erp_capabilities_added', 'yes');
    }

    /**
     * Get all custom capabilities
     */
    public static function get_capabilities() {
        return array(
            // Equipment capabilities
            'manage_equipment',
            'edit_equipment',
            'delete_equipment',
            'view_equipment',
            
            // Booking capabilities
            'manage_bookings',
            'edit_bookings',
            'delete_bookings',
            'view_bookings',
            
            // Customer capabilities
            'manage_customers',
            'edit_customers',
            'delete_customers',
            'view_customers',
            
            // Maintenance capabilities
            'manage_maintenance',
            'edit_maintenance',
            'delete_maintenance',
            'view_maintenance',
            
            // Reporting capabilities
            'view_reports',
            'export_reports',
            
            // Analytics capabilities (Pro)
            'view_analytics',
            'manage_analytics',
            
            // Route management capabilities (Pro)
            'manage_routes',
            'edit_routes',
            'view_routes',
            
            // Advanced settings capabilities
            'manage_erp_settings',
            'view_erp_settings',
        );
    }

    /**
     * Check if current user has capability
     */
    public static function current_user_can($capability) {
        return current_user_can($capability);
    }

    /**
     * Remove capabilities on plugin deactivation
     */
    public static function remove_capabilities() {
        $capabilities = self::get_capabilities();

        // Remove from all roles
        global $wp_roles;
        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }

        foreach ($wp_roles->roles as $role_name => $role_info) {
            $role = get_role($role_name);
            if ($role) {
                foreach ($capabilities as $cap) {
                    $role->remove_cap($cap);
                }
            }
        }

        // Remove custom roles
        remove_role('equipment_manager');
        remove_role('rental_agent');

        // Reset the flag
        delete_option('erp_capabilities_added');
    }
}