<?php
/**
 * User Capabilities for Knot4
 * 
 * @package Knot4
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Knot4_Capabilities {
    
    /**
     * Initialize capabilities
     */
    public static function init() {
        add_action('init', array(__CLASS__, 'add_capabilities'));
    }
    
    /**
     * Add custom capabilities
     */
    public static function add_capabilities() {
        $capabilities = array(
            // View capabilities
            'view_knot4_nonprofit' => __('View Nonprofit Data', 'knot4'),
            'view_knot4_donations' => __('View Donations', 'knot4'),
            'view_knot4_donors' => __('View Donors', 'knot4'),
            'view_knot4_events' => __('View Events', 'knot4'),
            'view_knot4_reports' => __('View Reports', 'knot4'),
            
            // Manage capabilities
            'manage_knot4_nonprofit' => __('Manage Nonprofit', 'knot4'),
            'manage_knot4_donations' => __('Manage Donations', 'knot4'),
            'manage_knot4_donors' => __('Manage Donors', 'knot4'),
            'manage_knot4_events' => __('Manage Events', 'knot4'),
            'manage_knot4_settings' => __('Manage Settings', 'knot4'),
            
            // Edit capabilities
            'edit_knot4_donations' => __('Edit Donations', 'knot4'),
            'edit_knot4_donors' => __('Edit Donors', 'knot4'),
            'edit_knot4_events' => __('Edit Events', 'knot4'),
            
            // Delete capabilities
            'delete_knot4_donations' => __('Delete Donations', 'knot4'),
            'delete_knot4_donors' => __('Delete Donors', 'knot4'),
            'delete_knot4_events' => __('Delete Events', 'knot4'),
            
            // Export capabilities
            'export_knot4_data' => __('Export Data', 'knot4'),
            'import_knot4_data' => __('Import Data', 'knot4'),
        );
        
        // Add capabilities to admin role
        $admin_role = get_role('administrator');
        if ($admin_role) {
            foreach (array_keys($capabilities) as $capability) {
                $admin_role->add_cap($capability);
            }
        }
        
        // Add basic capabilities to editor role
        $editor_role = get_role('editor');
        if ($editor_role) {
            $editor_capabilities = array(
                'view_knot4_nonprofit',
                'view_knot4_donations',
                'view_knot4_donors',
                'view_knot4_events',
                'view_knot4_reports',
                'edit_knot4_donations',
                'edit_knot4_donors',
                'edit_knot4_events',
            );
            
            foreach ($editor_capabilities as $capability) {
                $editor_role->add_cap($capability);
            }
        }
        
        // Create custom role for nonprofit volunteers
        if (!get_role('knot4_volunteer')) {
            add_role(
                'knot4_volunteer',
                __('Nonprofit Volunteer', 'knot4'),
                array(
                    'read' => true,
                    'view_knot4_nonprofit' => true,
                    'view_knot4_donations' => true,
                    'view_knot4_donors' => true,
                    'view_knot4_events' => true,
                )
            );
        }
        
        // Create custom role for donors
        if (!get_role('knot4_donor')) {
            add_role(
                'knot4_donor',
                __('Donor', 'knot4'),
                array(
                    'read' => true,
                )
            );
        }
    }
    
    /**
     * Remove capabilities (for uninstall)
     */
    public static function remove_capabilities() {
        $capabilities = array(
            'view_knot4_nonprofit',
            'view_knot4_donations',
            'view_knot4_donors',
            'view_knot4_events',
            'view_knot4_reports',
            'manage_knot4_nonprofit',
            'manage_knot4_donations',
            'manage_knot4_donors',
            'manage_knot4_events',
            'manage_knot4_settings',
            'edit_knot4_donations',
            'edit_knot4_donors',
            'edit_knot4_events',
            'delete_knot4_donations',
            'delete_knot4_donors',
            'delete_knot4_events',
            'export_knot4_data',
            'import_knot4_data',
        );
        
        // Remove from all roles
        global $wp_roles;
        
        foreach ($wp_roles->roles as $role_name => $role_data) {
            $role = get_role($role_name);
            if ($role) {
                foreach ($capabilities as $capability) {
                    $role->remove_cap($capability);
                }
            }
        }
        
        // Remove custom roles
        remove_role('knot4_volunteer');
        remove_role('knot4_donor');
    }
}