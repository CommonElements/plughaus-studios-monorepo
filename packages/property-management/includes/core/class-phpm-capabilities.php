<?php
/**
 * Capabilities management for Vireo Property Management
 *
 * @package VireoPropertyManagement
 * @since 1.0.0
 */

namespace Vireo\PropertyManagement\Core;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handles user capabilities and roles
 */
class Capabilities {
    
    /**
     * Initialize capabilities
     */
    public static function init() {
        // Add capabilities on plugin activation
        register_activation_hook(PHPM_PLUGIN_BASE, array(__CLASS__, 'add_capabilities'));
        
        // Remove capabilities on plugin deactivation
        register_deactivation_hook(PHPM_PLUGIN_BASE, array(__CLASS__, 'remove_capabilities'));
    }
    
    /**
     * Add custom capabilities
     */
    public static function add_capabilities() {
        $roles = array('administrator');
        
        // Property management capabilities
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
        
        foreach ($roles as $role_name) {
            $role = get_role($role_name);
            
            if ($role) {
                foreach ($capabilities as $cap) {
                    $role->add_cap($cap);
                }
            }
        }
        
        // Create custom roles
        self::create_custom_roles();
    }
    
    /**
     * Remove custom capabilities
     */
    public static function remove_capabilities() {
        $roles = array('administrator', 'phpm_property_manager', 'phpm_tenant');
        
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
        
        foreach ($roles as $role_name) {
            $role = get_role($role_name);
            
            if ($role) {
                foreach ($capabilities as $cap) {
                    $role->remove_cap($cap);
                }
            }
        }
        
        // Remove custom roles
        remove_role('phpm_property_manager');
        remove_role('phpm_tenant');
    }
    
    /**
     * Create custom roles
     */
    private static function create_custom_roles() {
        // Property Manager Role
        add_role('phpm_property_manager', __('Property Manager', 'vireo-property'), array(
            'read' => true,
            
            // Properties - full access
            'edit_phpm_property' => true,
            'read_phpm_property' => true,
            'delete_phpm_property' => true,
            'edit_phpm_properties' => true,
            'edit_others_phpm_properties' => true,
            'publish_phpm_properties' => true,
            'read_private_phpm_properties' => true,
            'delete_phpm_properties' => true,
            'delete_private_phpm_properties' => true,
            'delete_published_phpm_properties' => true,
            'delete_others_phpm_properties' => true,
            'edit_private_phpm_properties' => true,
            'edit_published_phpm_properties' => true,
            
            // Units - full access
            'edit_phpm_unit' => true,
            'read_phpm_unit' => true,
            'delete_phpm_unit' => true,
            'edit_phpm_units' => true,
            'edit_others_phpm_units' => true,
            'publish_phpm_units' => true,
            'read_private_phpm_units' => true,
            'delete_phpm_units' => true,
            'delete_private_phpm_units' => true,
            'delete_published_phpm_units' => true,
            'delete_others_phpm_units' => true,
            'edit_private_phpm_units' => true,
            'edit_published_phpm_units' => true,
            
            // Tenants - full access
            'edit_phpm_tenant' => true,
            'read_phpm_tenant' => true,
            'delete_phpm_tenant' => true,
            'edit_phpm_tenants' => true,
            'edit_others_phpm_tenants' => true,
            'publish_phpm_tenants' => true,
            'read_private_phpm_tenants' => true,
            'delete_phpm_tenants' => true,
            'delete_private_phpm_tenants' => true,
            'delete_published_phpm_tenants' => true,
            'delete_others_phpm_tenants' => true,
            'edit_private_phpm_tenants' => true,
            'edit_published_phpm_tenants' => true,
            
            // Leases - full access
            'edit_phpm_lease' => true,
            'read_phpm_lease' => true,
            'delete_phpm_lease' => true,
            'edit_phpm_leases' => true,
            'edit_others_phpm_leases' => true,
            'publish_phpm_leases' => true,
            'read_private_phpm_leases' => true,
            'delete_phpm_leases' => true,
            'delete_private_phpm_leases' => true,
            'delete_published_phpm_leases' => true,
            'delete_others_phpm_leases' => true,
            'edit_private_phpm_leases' => true,
            'edit_published_phpm_leases' => true,
            
            // Maintenance - full access
            'edit_phpm_maintenance' => true,
            'read_phpm_maintenance' => true,
            'delete_phpm_maintenance' => true,
            'edit_phpm_maintenances' => true,
            'edit_others_phpm_maintenances' => true,
            'publish_phpm_maintenances' => true,
            'read_private_phpm_maintenances' => true,
            'delete_phpm_maintenances' => true,
            'delete_private_phpm_maintenances' => true,
            'delete_published_phpm_maintenances' => true,
            'delete_others_phpm_maintenances' => true,
            'edit_private_phpm_maintenances' => true,
            'edit_published_phpm_maintenances' => true,
        ));
        
        // Tenant Role
        add_role('phpm_tenant', __('Tenant', 'vireo-property'), array(
            'read' => true,
            
            // Limited read access
            'read_phpm_property' => true,
            'read_phpm_unit' => true,
            
            // Can create maintenance requests
            'edit_phpm_maintenance' => true,
            'publish_phpm_maintenances' => true,
        ));
    }
}

// Initialize capabilities
PHPM_Capabilities::init();