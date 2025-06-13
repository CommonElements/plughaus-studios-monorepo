<?php
/**
 * GymFlow Capabilities Class
 *
 * Manages user capabilities and permissions for the fitness studio management system
 *
 * @package GymFlow
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * GF_Capabilities Class
 *
 * Handles user capabilities and role management
 */
class GF_Capabilities {

    /**
     * Initialize capabilities
     */
    public function init() {
        add_action('init', array($this, 'add_capabilities_to_roles'));
        add_filter('map_meta_cap', array($this, 'map_meta_capabilities'), 10, 4);
    }

    /**
     * Add GymFlow capabilities to appropriate roles
     */
    public function add_capabilities_to_roles() {
        $this->add_admin_capabilities();
        $this->add_manager_capabilities();
        $this->add_staff_capabilities();
        $this->add_trainer_capabilities();
    }

    /**
     * Add capabilities to Administrator role
     */
    private function add_admin_capabilities() {
        $admin_role = get_role('administrator');
        
        if ($admin_role) {
            // Core management capabilities
            $admin_role->add_cap('manage_gymflow');
            $admin_role->add_cap('edit_gymflow');
            $admin_role->add_cap('view_gymflow_reports');
            $admin_role->add_cap('manage_gymflow_settings');
            
            // Member capabilities
            $admin_role->add_cap('manage_gymflow_members');
            $admin_role->add_cap('edit_gf_members');
            $admin_role->add_cap('edit_others_gf_members');
            $admin_role->add_cap('publish_gf_members');
            $admin_role->add_cap('read_private_gf_members');
            $admin_role->add_cap('delete_gf_members');
            $admin_role->add_cap('delete_private_gf_members');
            $admin_role->add_cap('delete_published_gf_members');
            $admin_role->add_cap('delete_others_gf_members');
            $admin_role->add_cap('edit_private_gf_members');
            $admin_role->add_cap('edit_published_gf_members');
            
            // Class capabilities
            $admin_role->add_cap('manage_gymflow_classes');
            $admin_role->add_cap('edit_gf_classes');
            $admin_role->add_cap('edit_others_gf_classes');
            $admin_role->add_cap('publish_gf_classes');
            $admin_role->add_cap('read_private_gf_classes');
            $admin_role->add_cap('delete_gf_classes');
            $admin_role->add_cap('delete_private_gf_classes');
            $admin_role->add_cap('delete_published_gf_classes');
            $admin_role->add_cap('delete_others_gf_classes');
            $admin_role->add_cap('edit_private_gf_classes');
            $admin_role->add_cap('edit_published_gf_classes');
            
            // Trainer capabilities
            $admin_role->add_cap('manage_gymflow_trainers');
            $admin_role->add_cap('edit_gf_trainers');
            $admin_role->add_cap('edit_others_gf_trainers');
            $admin_role->add_cap('publish_gf_trainers');
            $admin_role->add_cap('read_private_gf_trainers');
            $admin_role->add_cap('delete_gf_trainers');
            $admin_role->add_cap('delete_private_gf_trainers');
            $admin_role->add_cap('delete_published_gf_trainers');
            $admin_role->add_cap('delete_others_gf_trainers');
            $admin_role->add_cap('edit_private_gf_trainers');
            $admin_role->add_cap('edit_published_gf_trainers');
            
            // Equipment capabilities
            $admin_role->add_cap('manage_gymflow_equipment');
            $admin_role->add_cap('edit_gf_equipment');
            $admin_role->add_cap('edit_others_gf_equipment');
            $admin_role->add_cap('publish_gf_equipment');
            $admin_role->add_cap('read_private_gf_equipment');
            $admin_role->add_cap('delete_gf_equipment');
            $admin_role->add_cap('delete_private_gf_equipment');
            $admin_role->add_cap('delete_published_gf_equipment');
            $admin_role->add_cap('delete_others_gf_equipment');
            $admin_role->add_cap('edit_private_gf_equipment');
            $admin_role->add_cap('edit_published_gf_equipment');
            
            // Booking capabilities
            $admin_role->add_cap('manage_gymflow_bookings');
            $admin_role->add_cap('edit_gf_bookings');
            $admin_role->add_cap('edit_others_gf_bookings');
            $admin_role->add_cap('publish_gf_bookings');
            $admin_role->add_cap('read_private_gf_bookings');
            $admin_role->add_cap('delete_gf_bookings');
            $admin_role->add_cap('delete_private_gf_bookings');
            $admin_role->add_cap('delete_published_gf_bookings');
            $admin_role->add_cap('delete_others_gf_bookings');
            $admin_role->add_cap('edit_private_gf_bookings');
            $admin_role->add_cap('edit_published_gf_bookings');
            
            // Payment capabilities
            $admin_role->add_cap('process_gymflow_payments');
            $admin_role->add_cap('view_gymflow_financial_reports');
            $admin_role->add_cap('manage_gymflow_memberships');
        }
    }

    /**
     * Add capabilities to GymFlow Manager role
     */
    private function add_manager_capabilities() {
        $manager_role = get_role('gymflow_manager');
        
        if ($manager_role) {
            // Core management capabilities
            $manager_role->add_cap('manage_gymflow');
            $manager_role->add_cap('edit_gymflow');
            $manager_role->add_cap('view_gymflow_reports');
            
            // Member capabilities
            $manager_role->add_cap('manage_gymflow_members');
            $manager_role->add_cap('edit_gf_members');
            $manager_role->add_cap('edit_others_gf_members');
            $manager_role->add_cap('publish_gf_members');
            $manager_role->add_cap('read_private_gf_members');
            $manager_role->add_cap('delete_gf_members');
            $manager_role->add_cap('edit_private_gf_members');
            $manager_role->add_cap('edit_published_gf_members');
            
            // Class capabilities
            $manager_role->add_cap('manage_gymflow_classes');
            $manager_role->add_cap('edit_gf_classes');
            $manager_role->add_cap('edit_others_gf_classes');
            $manager_role->add_cap('publish_gf_classes');
            $manager_role->add_cap('read_private_gf_classes');
            $manager_role->add_cap('edit_private_gf_classes');
            $manager_role->add_cap('edit_published_gf_classes');
            
            // Trainer capabilities
            $manager_role->add_cap('manage_gymflow_trainers');
            $manager_role->add_cap('edit_gf_trainers');
            $manager_role->add_cap('edit_others_gf_trainers');
            $manager_role->add_cap('publish_gf_trainers');
            $manager_role->add_cap('read_private_gf_trainers');
            $manager_role->add_cap('edit_private_gf_trainers');
            $manager_role->add_cap('edit_published_gf_trainers');
            
            // Equipment capabilities
            $manager_role->add_cap('manage_gymflow_equipment');
            $manager_role->add_cap('edit_gf_equipment');
            $manager_role->add_cap('edit_others_gf_equipment');
            $manager_role->add_cap('publish_gf_equipment');
            $manager_role->add_cap('read_private_gf_equipment');
            $manager_role->add_cap('edit_private_gf_equipment');
            $manager_role->add_cap('edit_published_gf_equipment');
            
            // Booking capabilities
            $manager_role->add_cap('manage_gymflow_bookings');
            $manager_role->add_cap('edit_gf_bookings');
            $manager_role->add_cap('edit_others_gf_bookings');
            $manager_role->add_cap('publish_gf_bookings');
            $manager_role->add_cap('read_private_gf_bookings');
            $manager_role->add_cap('edit_private_gf_bookings');
            $manager_role->add_cap('edit_published_gf_bookings');
            
            // Payment capabilities
            $manager_role->add_cap('process_gymflow_payments');
            $manager_role->add_cap('view_gymflow_financial_reports');
            $manager_role->add_cap('manage_gymflow_memberships');
        }
    }

    /**
     * Add capabilities to GymFlow Staff role
     */
    private function add_staff_capabilities() {
        $staff_role = get_role('gymflow_staff');
        
        if ($staff_role) {
            // Basic editing capabilities
            $staff_role->add_cap('edit_gymflow');
            
            // Member capabilities (limited)
            $staff_role->add_cap('manage_gymflow_members');
            $staff_role->add_cap('edit_gf_members');
            $staff_role->add_cap('publish_gf_members');
            $staff_role->add_cap('edit_published_gf_members');
            
            // Booking capabilities
            $staff_role->add_cap('manage_gymflow_bookings');
            $staff_role->add_cap('edit_gf_bookings');
            $staff_role->add_cap('publish_gf_bookings');
            $staff_role->add_cap('edit_published_gf_bookings');
            
            // View classes and trainers
            $staff_role->add_cap('read_gf_classes');
            $staff_role->add_cap('read_gf_trainers');
            $staff_role->add_cap('read_gf_equipment');
        }
    }

    /**
     * Add capabilities to GymFlow Trainer role
     */
    private function add_trainer_capabilities() {
        $trainer_role = get_role('gymflow_trainer');
        
        if ($trainer_role) {
            // View capabilities
            $trainer_role->add_cap('view_gymflow_classes');
            $trainer_role->add_cap('read_gf_classes');
            $trainer_role->add_cap('read_gf_members');
            
            // Limited booking capabilities
            $trainer_role->add_cap('manage_gymflow_bookings');
            $trainer_role->add_cap('edit_gf_bookings');
            $trainer_role->add_cap('read_gf_bookings');
            
            // Edit own trainer profile
            $trainer_role->add_cap('edit_own_gf_trainer');
        }
    }

    /**
     * Map meta capabilities for custom post types
     */
    public function map_meta_capabilities($caps, $cap, $user_id, $args) {
        // Map capabilities for members
        if (strpos($cap, 'gf_member') !== false) {
            $caps = $this->map_member_capabilities($caps, $cap, $user_id, $args);
        }
        
        // Map capabilities for classes
        if (strpos($cap, 'gf_class') !== false) {
            $caps = $this->map_class_capabilities($caps, $cap, $user_id, $args);
        }
        
        // Map capabilities for trainers
        if (strpos($cap, 'gf_trainer') !== false) {
            $caps = $this->map_trainer_capabilities($caps, $cap, $user_id, $args);
        }
        
        // Map capabilities for equipment
        if (strpos($cap, 'gf_equipment') !== false) {
            $caps = $this->map_equipment_capabilities($caps, $cap, $user_id, $args);
        }
        
        // Map capabilities for bookings
        if (strpos($cap, 'gf_booking') !== false) {
            $caps = $this->map_booking_capabilities($caps, $cap, $user_id, $args);
        }

        return $caps;
    }

    /**
     * Map member capabilities
     */
    private function map_member_capabilities($caps, $cap, $user_id, $args) {
        switch ($cap) {
            case 'edit_gf_member':
                $caps = array('edit_gf_members');
                break;
            case 'read_gf_member':
                $caps = array('read');
                break;
            case 'delete_gf_member':
                $caps = array('delete_gf_members');
                break;
        }

        return $caps;
    }

    /**
     * Map class capabilities
     */
    private function map_class_capabilities($caps, $cap, $user_id, $args) {
        switch ($cap) {
            case 'edit_gf_class':
                $caps = array('edit_gf_classes');
                break;
            case 'read_gf_class':
                $caps = array('read');
                break;
            case 'delete_gf_class':
                $caps = array('delete_gf_classes');
                break;
        }

        return $caps;
    }

    /**
     * Map trainer capabilities
     */
    private function map_trainer_capabilities($caps, $cap, $user_id, $args) {
        switch ($cap) {
            case 'edit_gf_trainer':
                // Check if user is editing their own trainer profile
                if (isset($args[0]) && $this->is_users_trainer_profile($user_id, $args[0])) {
                    $caps = array('edit_own_gf_trainer');
                } else {
                    $caps = array('edit_gf_trainers');
                }
                break;
            case 'read_gf_trainer':
                $caps = array('read');
                break;
            case 'delete_gf_trainer':
                $caps = array('delete_gf_trainers');
                break;
        }

        return $caps;
    }

    /**
     * Map equipment capabilities
     */
    private function map_equipment_capabilities($caps, $cap, $user_id, $args) {
        switch ($cap) {
            case 'edit_gf_equipment':
                $caps = array('edit_gf_equipment');
                break;
            case 'read_gf_equipment':
                $caps = array('read');
                break;
            case 'delete_gf_equipment':
                $caps = array('delete_gf_equipment');
                break;
        }

        return $caps;
    }

    /**
     * Map booking capabilities
     */
    private function map_booking_capabilities($caps, $cap, $user_id, $args) {
        switch ($cap) {
            case 'edit_gf_booking':
                $caps = array('edit_gf_bookings');
                break;
            case 'read_gf_booking':
                $caps = array('read');
                break;
            case 'delete_gf_booking':
                $caps = array('delete_gf_bookings');
                break;
        }

        return $caps;
    }

    /**
     * Check if user is editing their own trainer profile
     */
    private function is_users_trainer_profile($user_id, $post_id) {
        $trainer_user_id = get_post_meta($post_id, '_trainer_user_id', true);
        return $trainer_user_id == $user_id;
    }

    /**
     * Check if current user can manage GymFlow
     */
    public static function current_user_can_manage() {
        return current_user_can('manage_gymflow') || current_user_can('manage_options');
    }

    /**
     * Check if current user can edit GymFlow content
     */
    public static function current_user_can_edit() {
        return current_user_can('edit_gymflow') || 
               current_user_can('manage_gymflow') || 
               current_user_can('manage_options');
    }

    /**
     * Check if current user can view reports
     */
    public static function current_user_can_view_reports() {
        return current_user_can('view_gymflow_reports') || 
               current_user_can('manage_gymflow') || 
               current_user_can('manage_options');
    }

    /**
     * Check if current user can process payments
     */
    public static function current_user_can_process_payments() {
        return current_user_can('process_gymflow_payments') || 
               current_user_can('manage_gymflow') || 
               current_user_can('manage_options');
    }

    /**
     * Check if current user can manage specific post type
     */
    public static function current_user_can_manage_post_type($post_type) {
        $capability_map = array(
            'gf_member' => 'manage_gymflow_members',
            'gf_class' => 'manage_gymflow_classes',
            'gf_trainer' => 'manage_gymflow_trainers',
            'gf_equipment' => 'manage_gymflow_equipment',
            'gf_booking' => 'manage_gymflow_bookings'
        );

        $capability = isset($capability_map[$post_type]) ? $capability_map[$post_type] : 'manage_gymflow';
        
        return current_user_can($capability) || 
               current_user_can('manage_gymflow') || 
               current_user_can('manage_options');
    }

    /**
     * Get all GymFlow capabilities
     */
    public static function get_all_capabilities() {
        return array(
            // Core capabilities
            'manage_gymflow',
            'edit_gymflow',
            'view_gymflow_reports',
            'manage_gymflow_settings',
            
            // Member capabilities
            'manage_gymflow_members',
            'edit_gf_members',
            'edit_others_gf_members',
            'publish_gf_members',
            'read_private_gf_members',
            'delete_gf_members',
            'delete_private_gf_members',
            'delete_published_gf_members',
            'delete_others_gf_members',
            'edit_private_gf_members',
            'edit_published_gf_members',
            
            // Class capabilities
            'manage_gymflow_classes',
            'edit_gf_classes',
            'edit_others_gf_classes',
            'publish_gf_classes',
            'read_private_gf_classes',
            'delete_gf_classes',
            'delete_private_gf_classes',
            'delete_published_gf_classes',
            'delete_others_gf_classes',
            'edit_private_gf_classes',
            'edit_published_gf_classes',
            
            // Trainer capabilities
            'manage_gymflow_trainers',
            'edit_gf_trainers',
            'edit_others_gf_trainers',
            'publish_gf_trainers',
            'read_private_gf_trainers',
            'delete_gf_trainers',
            'delete_private_gf_trainers',
            'delete_published_gf_trainers',
            'delete_others_gf_trainers',
            'edit_private_gf_trainers',
            'edit_published_gf_trainers',
            'edit_own_gf_trainer',
            
            // Equipment capabilities
            'manage_gymflow_equipment',
            'edit_gf_equipment',
            'edit_others_gf_equipment',
            'publish_gf_equipment',
            'read_private_gf_equipment',
            'delete_gf_equipment',
            'delete_private_gf_equipment',
            'delete_published_gf_equipment',
            'delete_others_gf_equipment',
            'edit_private_gf_equipment',
            'edit_published_gf_equipment',
            
            // Booking capabilities
            'manage_gymflow_bookings',
            'edit_gf_bookings',
            'edit_others_gf_bookings',
            'publish_gf_bookings',
            'read_private_gf_bookings',
            'delete_gf_bookings',
            'delete_private_gf_bookings',
            'delete_published_gf_bookings',
            'delete_others_gf_bookings',
            'edit_private_gf_bookings',
            'edit_published_gf_bookings',
            
            // Payment capabilities
            'process_gymflow_payments',
            'view_gymflow_financial_reports',
            'manage_gymflow_memberships'
        );
    }

    /**
     * Remove all GymFlow capabilities from all roles
     */
    public static function remove_all_capabilities() {
        $capabilities = self::get_all_capabilities();
        
        // Remove from all roles
        global $wp_roles;
        $roles = $wp_roles->roles;
        
        foreach ($roles as $role_name => $role_data) {
            $role = get_role($role_name);
            if ($role) {
                foreach ($capabilities as $capability) {
                    $role->remove_cap($capability);
                }
            }
        }
    }
}