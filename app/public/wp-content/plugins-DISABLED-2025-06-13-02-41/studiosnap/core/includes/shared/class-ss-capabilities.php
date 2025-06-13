<?php
/**
 * StudioSnap Capabilities - User roles and permissions management
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class SS_Capabilities {
    
    public static function init() {
        add_action('admin_init', array(__CLASS__, 'add_capabilities'));
        register_activation_hook(SS_PLUGIN_FILE, array(__CLASS__, 'add_roles_and_caps'));
        register_deactivation_hook(SS_PLUGIN_FILE, array(__CLASS__, 'remove_roles_and_caps'));
    }
    
    /**
     * Add custom roles and capabilities on plugin activation
     */
    public static function add_roles_and_caps() {
        self::add_custom_roles();
        self::add_capabilities();
    }
    
    /**
     * Remove custom roles and capabilities on plugin deactivation
     */
    public static function remove_roles_and_caps() {
        self::remove_custom_roles();
        self::remove_capabilities();
    }
    
    /**
     * Add custom user roles
     */
    public static function add_custom_roles() {
        // Studio Manager - Full access to all studio functions
        add_role('studio_manager', __('Studio Manager', 'studiosnap'), array(
            'read' => true,
            'edit_posts' => true,
            'delete_posts' => true,
            'publish_posts' => true,
            'upload_files' => true,
            'edit_pages' => true,
            'edit_published_pages' => true,
            'delete_pages' => true,
            'delete_published_pages' => true,
            'edit_others_pages' => true,
            'delete_others_pages' => true,
            'manage_categories' => true,
            'manage_links' => true,
            'moderate_comments' => true,
            'edit_others_posts' => true,
            'edit_published_posts' => true,
            'delete_others_posts' => true,
            'delete_published_posts' => true,
            'delete_private_posts' => true,
            'edit_private_posts' => true,
            'read_private_posts' => true,
            
            // Studio-specific capabilities
            'manage_studio' => true,
            'view_studio_dashboard' => true,
            'manage_ss_sessions' => true,
            'manage_ss_clients' => true,
            'manage_ss_galleries' => true,
            'manage_ss_packages' => true,
            'manage_ss_invoices' => true,
            'manage_ss_contracts' => true,
            'manage_studio_settings' => true,
            'view_studio_reports' => true,
            'manage_studio_staff' => true
        ));
        
        // Photographer - Can manage sessions, clients, and galleries
        add_role('photographer', __('Photographer', 'studiosnap'), array(
            'read' => true,
            'upload_files' => true,
            
            // Studio capabilities
            'view_studio_dashboard' => true,
            'edit_ss_sessions' => true,
            'read_ss_sessions' => true,
            'delete_ss_sessions' => true,
            'edit_published_ss_sessions' => true,
            'edit_others_ss_sessions' => true,
            'read_private_ss_sessions' => true,
            
            'edit_ss_clients' => true,
            'read_ss_clients' => true,
            'delete_ss_clients' => true,
            'edit_published_ss_clients' => true,
            'edit_others_ss_clients' => true,
            'read_private_ss_clients' => true,
            
            'edit_ss_galleries' => true,
            'read_ss_galleries' => true,
            'delete_ss_galleries' => true,
            'publish_ss_galleries' => true,
            'edit_published_ss_galleries' => true,
            'edit_others_ss_galleries' => true,
            'read_private_ss_galleries' => true,
            
            'read_ss_packages' => true,
            'read_ss_invoices' => true,
            'read_ss_contracts' => true
        ));
        
        // Studio Assistant - Limited access for administrative tasks
        add_role('studio_assistant', __('Studio Assistant', 'studiosnap'), array(
            'read' => true,
            
            // Studio capabilities
            'view_studio_dashboard' => true,
            'edit_ss_sessions' => true,
            'read_ss_sessions' => true,
            'edit_published_ss_sessions' => true,
            
            'edit_ss_clients' => true,
            'read_ss_clients' => true,
            'edit_published_ss_clients' => true,
            
            'read_ss_galleries' => true,
            'read_ss_packages' => true,
            
            'edit_ss_invoices' => true,
            'read_ss_invoices' => true,
            'edit_published_ss_invoices' => true,
            
            'read_ss_contracts' => true
        ));
        
        // Client - Very limited access for viewing their own content
        add_role('studio_client', __('Studio Client', 'studiosnap'), array(
            'read' => true,
            'view_own_ss_sessions' => true,
            'view_own_ss_galleries' => true,
            'view_own_ss_invoices' => true,
            'view_own_ss_contracts' => true
        ));
    }
    
    /**
     * Add capabilities to existing and custom roles
     */
    public static function add_capabilities() {
        // Get roles
        $admin = get_role('administrator');
        $editor = get_role('editor');
        $studio_manager = get_role('studio_manager');
        $photographer = get_role('photographer');
        $studio_assistant = get_role('studio_assistant');
        
        // Session capabilities
        $session_caps = array(
            'edit_ss_session',
            'read_ss_session',
            'delete_ss_session',
            'edit_ss_sessions',
            'edit_others_ss_sessions',
            'publish_ss_sessions',
            'read_private_ss_sessions',
            'delete_ss_sessions',
            'delete_private_ss_sessions',
            'delete_published_ss_sessions',
            'delete_others_ss_sessions',
            'edit_private_ss_sessions',
            'edit_published_ss_sessions'
        );
        
        // Client capabilities
        $client_caps = array(
            'edit_ss_client',
            'read_ss_client',
            'delete_ss_client',
            'edit_ss_clients',
            'edit_others_ss_clients',
            'publish_ss_clients',
            'read_private_ss_clients',
            'delete_ss_clients',
            'delete_private_ss_clients',
            'delete_published_ss_clients',
            'delete_others_ss_clients',
            'edit_private_ss_clients',
            'edit_published_ss_clients'
        );
        
        // Gallery capabilities
        $gallery_caps = array(
            'edit_ss_gallery',
            'read_ss_gallery',
            'delete_ss_gallery',
            'edit_ss_galleries',
            'edit_others_ss_galleries',
            'publish_ss_galleries',
            'read_private_ss_galleries',
            'delete_ss_galleries',
            'delete_private_ss_galleries',
            'delete_published_ss_galleries',
            'delete_others_ss_galleries',
            'edit_private_ss_galleries',
            'edit_published_ss_galleries'
        );
        
        // Package capabilities
        $package_caps = array(
            'edit_ss_package',
            'read_ss_package',
            'delete_ss_package',
            'edit_ss_packages',
            'edit_others_ss_packages',
            'publish_ss_packages',
            'read_private_ss_packages',
            'delete_ss_packages',
            'delete_private_ss_packages',
            'delete_published_ss_packages',
            'delete_others_ss_packages',
            'edit_private_ss_packages',
            'edit_published_ss_packages'
        );
        
        // Invoice capabilities
        $invoice_caps = array(
            'edit_ss_invoice',
            'read_ss_invoice',
            'delete_ss_invoice',
            'edit_ss_invoices',
            'edit_others_ss_invoices',
            'publish_ss_invoices',
            'read_private_ss_invoices',
            'delete_ss_invoices',
            'delete_private_ss_invoices',
            'delete_published_ss_invoices',
            'delete_others_ss_invoices',
            'edit_private_ss_invoices',
            'edit_published_ss_invoices'
        );
        
        // Contract capabilities
        $contract_caps = array(
            'edit_ss_contract',
            'read_ss_contract',
            'delete_ss_contract',
            'edit_ss_contracts',
            'edit_others_ss_contracts',
            'publish_ss_contracts',
            'read_private_ss_contracts',
            'delete_ss_contracts',
            'delete_private_ss_contracts',
            'delete_published_ss_contracts',
            'delete_others_ss_contracts',
            'edit_private_ss_contracts',
            'edit_published_ss_contracts'
        );
        
        // Studio management capabilities
        $management_caps = array(
            'manage_studio',
            'view_studio_dashboard',
            'manage_studio_settings',
            'view_studio_reports',
            'manage_studio_staff'
        );
        
        // Add all capabilities to administrator
        if ($admin) {
            foreach (array_merge($session_caps, $client_caps, $gallery_caps, $package_caps, $invoice_caps, $contract_caps, $management_caps) as $cap) {
                $admin->add_cap($cap);
            }
        }
        
        // Add capabilities to editor (limited studio access)
        if ($editor) {
            foreach (array_merge($session_caps, $client_caps, $gallery_caps, $package_caps) as $cap) {
                $editor->add_cap($cap);
            }
            $editor->add_cap('view_studio_dashboard');
        }
        
        // Studio Manager gets all capabilities
        if ($studio_manager) {
            foreach (array_merge($session_caps, $client_caps, $gallery_caps, $package_caps, $invoice_caps, $contract_caps, $management_caps) as $cap) {
                $studio_manager->add_cap($cap);
            }
        }
        
        // Photographer capabilities
        if ($photographer) {
            foreach (array_merge($session_caps, $client_caps, $gallery_caps) as $cap) {
                $photographer->add_cap($cap);
            }
            // Read-only access to packages, invoices, contracts
            $photographer->add_cap('read_ss_package');
            $photographer->add_cap('read_ss_packages');
            $photographer->add_cap('read_ss_invoice');
            $photographer->add_cap('read_ss_invoices');
            $photographer->add_cap('read_ss_contract');
            $photographer->add_cap('read_ss_contracts');
        }
        
        // Studio Assistant capabilities (limited editing)
        if ($studio_assistant) {
            // Limited session capabilities
            $studio_assistant->add_cap('edit_ss_session');
            $studio_assistant->add_cap('read_ss_session');
            $studio_assistant->add_cap('edit_ss_sessions');
            $studio_assistant->add_cap('edit_published_ss_sessions');
            $studio_assistant->add_cap('read_private_ss_sessions');
            
            // Limited client capabilities
            $studio_assistant->add_cap('edit_ss_client');
            $studio_assistant->add_cap('read_ss_client');
            $studio_assistant->add_cap('edit_ss_clients');
            $studio_assistant->add_cap('edit_published_ss_clients');
            $studio_assistant->add_cap('read_private_ss_clients');
            
            // Read-only gallery access
            $studio_assistant->add_cap('read_ss_gallery');
            $studio_assistant->add_cap('read_ss_galleries');
            $studio_assistant->add_cap('read_private_ss_galleries');
            
            // Limited invoice capabilities
            $studio_assistant->add_cap('edit_ss_invoice');
            $studio_assistant->add_cap('read_ss_invoice');
            $studio_assistant->add_cap('edit_ss_invoices');
            $studio_assistant->add_cap('edit_published_ss_invoices');
            $studio_assistant->add_cap('read_private_ss_invoices');
            
            // Read-only package and contract access
            $studio_assistant->add_cap('read_ss_package');
            $studio_assistant->add_cap('read_ss_packages');
            $studio_assistant->add_cap('read_ss_contract');
            $studio_assistant->add_cap('read_ss_contracts');
        }
    }
    
    /**
     * Remove custom user roles
     */
    public static function remove_custom_roles() {
        remove_role('studio_manager');
        remove_role('photographer');
        remove_role('studio_assistant');
        remove_role('studio_client');
    }
    
    /**
     * Remove capabilities from existing roles
     */
    public static function remove_capabilities() {
        $admin = get_role('administrator');
        $editor = get_role('editor');
        
        $all_caps = array(
            // Session caps
            'edit_ss_session', 'read_ss_session', 'delete_ss_session', 'edit_ss_sessions',
            'edit_others_ss_sessions', 'publish_ss_sessions', 'read_private_ss_sessions',
            'delete_ss_sessions', 'delete_private_ss_sessions', 'delete_published_ss_sessions',
            'delete_others_ss_sessions', 'edit_private_ss_sessions', 'edit_published_ss_sessions',
            
            // Client caps
            'edit_ss_client', 'read_ss_client', 'delete_ss_client', 'edit_ss_clients',
            'edit_others_ss_clients', 'publish_ss_clients', 'read_private_ss_clients',
            'delete_ss_clients', 'delete_private_ss_clients', 'delete_published_ss_clients',
            'delete_others_ss_clients', 'edit_private_ss_clients', 'edit_published_ss_clients',
            
            // Gallery caps
            'edit_ss_gallery', 'read_ss_gallery', 'delete_ss_gallery', 'edit_ss_galleries',
            'edit_others_ss_galleries', 'publish_ss_galleries', 'read_private_ss_galleries',
            'delete_ss_galleries', 'delete_private_ss_galleries', 'delete_published_ss_galleries',
            'delete_others_ss_galleries', 'edit_private_ss_galleries', 'edit_published_ss_galleries',
            
            // Package caps
            'edit_ss_package', 'read_ss_package', 'delete_ss_package', 'edit_ss_packages',
            'edit_others_ss_packages', 'publish_ss_packages', 'read_private_ss_packages',
            'delete_ss_packages', 'delete_private_ss_packages', 'delete_published_ss_packages',
            'delete_others_ss_packages', 'edit_private_ss_packages', 'edit_published_ss_packages',
            
            // Invoice caps
            'edit_ss_invoice', 'read_ss_invoice', 'delete_ss_invoice', 'edit_ss_invoices',
            'edit_others_ss_invoices', 'publish_ss_invoices', 'read_private_ss_invoices',
            'delete_ss_invoices', 'delete_private_ss_invoices', 'delete_published_ss_invoices',
            'delete_others_ss_invoices', 'edit_private_ss_invoices', 'edit_published_ss_invoices',
            
            // Contract caps
            'edit_ss_contract', 'read_ss_contract', 'delete_ss_contract', 'edit_ss_contracts',
            'edit_others_ss_contracts', 'publish_ss_contracts', 'read_private_ss_contracts',
            'delete_ss_contracts', 'delete_private_ss_contracts', 'delete_published_ss_contracts',
            'delete_others_ss_contracts', 'edit_private_ss_contracts', 'edit_published_ss_contracts',
            
            // Management caps
            'manage_studio', 'view_studio_dashboard', 'manage_studio_settings',
            'view_studio_reports', 'manage_studio_staff'
        );
        
        // Remove from administrator
        if ($admin) {
            foreach ($all_caps as $cap) {
                $admin->remove_cap($cap);
            }
        }
        
        // Remove from editor
        if ($editor) {
            foreach ($all_caps as $cap) {
                $editor->remove_cap($cap);
            }
        }
    }
    
    /**
     * Check if current user can manage studio
     */
    public static function current_user_can_manage_studio() {
        return current_user_can('manage_studio') || current_user_can('administrator');
    }
    
    /**
     * Check if current user can view studio dashboard
     */
    public static function current_user_can_view_dashboard() {
        return current_user_can('view_studio_dashboard') || current_user_can('administrator');
    }
    
    /**
     * Check if current user can manage specific post type
     */
    public static function current_user_can_manage($post_type) {
        switch ($post_type) {
            case 'ss_session':
                return current_user_can('edit_ss_sessions');
            case 'ss_client':
                return current_user_can('edit_ss_clients');
            case 'ss_gallery':
                return current_user_can('edit_ss_galleries');
            case 'ss_package':
                return current_user_can('edit_ss_packages');
            case 'ss_invoice':
                return current_user_can('edit_ss_invoices');
            case 'ss_contract':
                return current_user_can('edit_ss_contracts');
            default:
                return false;
        }
    }
    
    /**
     * Get available user roles for studio
     */
    public static function get_studio_roles() {
        return array(
            'studio_manager' => __('Studio Manager', 'studiosnap'),
            'photographer' => __('Photographer', 'studiosnap'),
            'studio_assistant' => __('Studio Assistant', 'studiosnap'),
            'studio_client' => __('Studio Client', 'studiosnap')
        );
    }
    
    /**
     * Get capability description
     */
    public static function get_capability_description($capability) {
        $descriptions = array(
            'manage_studio' => __('Full studio management access', 'studiosnap'),
            'view_studio_dashboard' => __('Access to studio dashboard', 'studiosnap'),
            'manage_studio_settings' => __('Manage studio settings and configuration', 'studiosnap'),
            'view_studio_reports' => __('View studio reports and analytics', 'studiosnap'),
            'manage_studio_staff' => __('Manage studio staff and roles', 'studiosnap'),
            'edit_ss_sessions' => __('Create and edit photography sessions', 'studiosnap'),
            'edit_ss_clients' => __('Create and edit client records', 'studiosnap'),
            'edit_ss_galleries' => __('Create and edit photo galleries', 'studiosnap'),
            'edit_ss_packages' => __('Create and edit photography packages', 'studiosnap'),
            'edit_ss_invoices' => __('Create and edit invoices', 'studiosnap'),
            'edit_ss_contracts' => __('Create and edit contracts', 'studiosnap')
        );
        
        return isset($descriptions[$capability]) ? $descriptions[$capability] : $capability;
    }
}