<?php
/**
 * DealerEdge Capabilities Management
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class DE_Capabilities {
    
    public static function init() {
        add_action('init', array(__CLASS__, 'add_capabilities'));
    }
    
    public static function add_capabilities() {
        // Get administrator role
        $admin_role = get_role('administrator');
        
        if ($admin_role) {
            // DealerEdge general capabilities
            $admin_role->add_cap('manage_dealeredge');
            $admin_role->add_cap('view_dealeredge_dashboard');
            $admin_role->add_cap('edit_dealeredge_settings');
            
            // Vehicle capabilities
            $admin_role->add_cap('edit_de_vehicles');
            $admin_role->add_cap('edit_others_de_vehicles');
            $admin_role->add_cap('publish_de_vehicles');
            $admin_role->add_cap('read_private_de_vehicles');
            $admin_role->add_cap('delete_de_vehicles');
            $admin_role->add_cap('delete_private_de_vehicles');
            $admin_role->add_cap('delete_published_de_vehicles');
            $admin_role->add_cap('delete_others_de_vehicles');
            $admin_role->add_cap('edit_private_de_vehicles');
            $admin_role->add_cap('edit_published_de_vehicles');
            
            // Customer capabilities
            $admin_role->add_cap('edit_de_customers');
            $admin_role->add_cap('edit_others_de_customers');
            $admin_role->add_cap('publish_de_customers');
            $admin_role->add_cap('read_private_de_customers');
            $admin_role->add_cap('delete_de_customers');
            $admin_role->add_cap('delete_private_de_customers');
            $admin_role->add_cap('delete_published_de_customers');
            $admin_role->add_cap('delete_others_de_customers');
            $admin_role->add_cap('edit_private_de_customers');
            $admin_role->add_cap('edit_published_de_customers');
            
            // Work Order capabilities
            $admin_role->add_cap('edit_de_work_orders');
            $admin_role->add_cap('edit_others_de_work_orders');
            $admin_role->add_cap('publish_de_work_orders');
            $admin_role->add_cap('read_private_de_work_orders');
            $admin_role->add_cap('delete_de_work_orders');
            $admin_role->add_cap('delete_private_de_work_orders');
            $admin_role->add_cap('delete_published_de_work_orders');
            $admin_role->add_cap('delete_others_de_work_orders');
            $admin_role->add_cap('edit_private_de_work_orders');
            $admin_role->add_cap('edit_published_de_work_orders');
            
            // Sale capabilities
            $admin_role->add_cap('edit_de_sales');
            $admin_role->add_cap('edit_others_de_sales');
            $admin_role->add_cap('publish_de_sales');
            $admin_role->add_cap('read_private_de_sales');
            $admin_role->add_cap('delete_de_sales');
            $admin_role->add_cap('delete_private_de_sales');
            $admin_role->add_cap('delete_published_de_sales');
            $admin_role->add_cap('delete_others_de_sales');
            $admin_role->add_cap('edit_private_de_sales');
            $admin_role->add_cap('edit_published_de_sales');
            
            // Part capabilities
            $admin_role->add_cap('edit_de_parts');
            $admin_role->add_cap('edit_others_de_parts');
            $admin_role->add_cap('publish_de_parts');
            $admin_role->add_cap('read_private_de_parts');
            $admin_role->add_cap('delete_de_parts');
            $admin_role->add_cap('delete_private_de_parts');
            $admin_role->add_cap('delete_published_de_parts');
            $admin_role->add_cap('delete_others_de_parts');
            $admin_role->add_cap('edit_private_de_parts');
            $admin_role->add_cap('edit_published_de_parts');
        }
        
        // Add capabilities to editor role for daily operations
        $editor_role = get_role('editor');
        if ($editor_role) {
            $editor_role->add_cap('view_dealeredge_dashboard');
            $editor_role->add_cap('edit_de_vehicles');
            $editor_role->add_cap('publish_de_vehicles');
            $editor_role->add_cap('edit_de_customers');
            $editor_role->add_cap('publish_de_customers');
            $editor_role->add_cap('edit_de_work_orders');
            $editor_role->add_cap('publish_de_work_orders');
            $editor_role->add_cap('edit_de_sales');
            $editor_role->add_cap('publish_de_sales');
            $editor_role->add_cap('edit_de_parts');
            $editor_role->add_cap('publish_de_parts');
        }
    }
    
    public static function remove_capabilities() {
        $roles = array('administrator', 'editor');
        
        $capabilities = array(
            'manage_dealeredge',
            'view_dealeredge_dashboard',
            'edit_dealeredge_settings',
            'edit_de_vehicles',
            'edit_others_de_vehicles',
            'publish_de_vehicles',
            'read_private_de_vehicles',
            'delete_de_vehicles',
            'delete_private_de_vehicles',
            'delete_published_de_vehicles',
            'delete_others_de_vehicles',
            'edit_private_de_vehicles',
            'edit_published_de_vehicles',
            'edit_de_customers',
            'edit_others_de_customers',
            'publish_de_customers',
            'read_private_de_customers',
            'delete_de_customers',
            'delete_private_de_customers',
            'delete_published_de_customers',
            'delete_others_de_customers',
            'edit_private_de_customers',
            'edit_published_de_customers',
            'edit_de_work_orders',
            'edit_others_de_work_orders',
            'publish_de_work_orders',
            'read_private_de_work_orders',
            'delete_de_work_orders',
            'delete_private_de_work_orders',
            'delete_published_de_work_orders',
            'delete_others_de_work_orders',
            'edit_private_de_work_orders',
            'edit_published_de_work_orders',
            'edit_de_sales',
            'edit_others_de_sales',
            'publish_de_sales',
            'read_private_de_sales',
            'delete_de_sales',
            'delete_private_de_sales',
            'delete_published_de_sales',
            'delete_others_de_sales',
            'edit_private_de_sales',
            'edit_published_de_sales',
            'edit_de_parts',
            'edit_others_de_parts',
            'publish_de_parts',
            'read_private_de_parts',
            'delete_de_parts',
            'delete_private_de_parts',
            'delete_published_de_parts',
            'delete_others_de_parts',
            'edit_private_de_parts',
            'edit_published_de_parts',
        );
        
        foreach ($roles as $role_name) {
            $role = get_role($role_name);
            if ($role) {
                foreach ($capabilities as $cap) {
                    $role->remove_cap($cap);
                }
            }
        }
    }
}