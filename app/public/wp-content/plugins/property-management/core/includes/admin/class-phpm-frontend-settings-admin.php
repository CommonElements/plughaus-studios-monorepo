<?php
/**
 * Frontend Page Settings Admin for PlugHaus Property Management
 * Allows users to designate which pages display property management content
 *
 * @package PlugHausPropertyManagement
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class PHPM_Frontend_Settings_Admin {
    
    /**
     * Initialize admin hooks
     */
    public static function init() {
        add_action('admin_menu', array(__CLASS__, 'add_admin_menu'), 25);
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_scripts'));
        add_action('wp_ajax_phpm_save_frontend_settings', array(__CLASS__, 'ajax_save_settings'));
        add_action('wp_ajax_phpm_create_frontend_page', array(__CLASS__, 'ajax_create_page'));
    }
    
    /**
     * Add admin menu item
     */
    public static function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=phpm_property',
            __('Frontend Pages', 'plughaus-property'),
            __('Frontend Pages', 'plughaus-property'),
            'manage_options',
            'phpm-frontend-settings',
            array(__CLASS__, 'admin_page')
        );
    }
    
    /**
     * Enqueue admin scripts
     */
    public static function enqueue_scripts($hook) {
        if ('phpm_property_page_phpm-frontend-settings' !== $hook) {
            return;
        }
        
        wp_enqueue_script(
            'phpm-frontend-settings-admin',
            PM_PLUGIN_URL . 'core/assets/js/frontend-settings-admin.js',
            array('jquery'),
            PM_VERSION,
            true
        );
        
        wp_localize_script('phpm-frontend-settings-admin', 'phpmFrontendSettings', array(
            'nonce' => wp_create_nonce('phpm_admin_nonce'),
            'ajax_url' => admin_url('admin-ajax.php'),
            'strings' => array(
                'saving' => __('Saving...', 'plughaus-property'),
                'saved' => __('Settings saved!', 'plughaus-property'),
                'error' => __('Error saving settings.', 'plughaus-property'),
                'creating_page' => __('Creating page...', 'plughaus-property'),
                'page_created' => __('Page created successfully!', 'plughaus-property'),
                'page_error' => __('Error creating page.', 'plughaus-property'),
                'confirm_create' => __('This will create a new page with the selected content. Continue?', 'plughaus-property')
            )
        ));
        
        wp_enqueue_style(
            'phpm-frontend-settings-admin',
            PM_PLUGIN_URL . 'core/assets/css/frontend-settings-admin.css',
            array(),
            PM_VERSION
        );
    }
    
    /**
     * Get frontend page settings
     */
    public static function get_frontend_settings() {
        $defaults = array(
            'property_listing_page' => 0,
            'property_detail_page' => 0,
            'tenant_portal_page' => 0,
            'maintenance_request_page' => 0,
            'tenant_dashboard_page' => 0,
            'property_search_page' => 0,
            'contact_page' => 0,
            'application_page' => 0,
            'enable_property_listings' => true,
            'enable_tenant_portal' => true,
            'enable_maintenance_requests' => true,
            'enable_public_search' => false,
            'require_login_for_portal' => true,
            'allow_online_applications' => false,
            'properties_per_page' => 12,
            'show_available_only' => true,
            'show_property_images' => true,
            'show_property_map' => true,
            'default_map_zoom' => 14
        );
        
        $settings = get_option('phpm_frontend_settings', array());
        return wp_parse_args($settings, $defaults);
    }
    
    /**
     * Display admin page
     */
    public static function admin_page() {
        $settings = self::get_frontend_settings();
        $pages = get_pages(array('post_status' => 'publish,private,draft'));
        
        ?>
        <div class="wrap">
            <h1><?php _e('Frontend Page Settings', 'plughaus-property'); ?></h1>
            <p class="description"><?php _e('Configure which WordPress pages will display property management content and features.', 'plughaus-property'); ?></p>
            
            <div class="phpm-frontend-settings-container">
                
                <!-- Page Assignments -->
                <div class="card">
                    <h2><span class="dashicons dashicons-admin-page"></span> <?php _e('Page Assignments', 'plughaus-property'); ?></h2>
                    <p><?php _e('Assign WordPress pages to display different types of property management content. You can select existing pages or create new ones.', 'plughaus-property'); ?></p>
                    
                    <table class="form-table page-assignments">
                        
                        <!-- Property Listing Page -->
                        <tr>
                            <th scope="row">
                                <label for="property_listing_page"><?php _e('Property Listings Page', 'plughaus-property'); ?></label>
                            </th>
                            <td>
                                <select id="property_listing_page" name="property_listing_page" class="page-select">
                                    <option value="0"><?php _e('— Select Page —', 'plughaus-property'); ?></option>
                                    <?php foreach ($pages as $page): ?>
                                        <option value="<?php echo $page->ID; ?>" <?php selected($settings['property_listing_page'], $page->ID); ?>>
                                            <?php echo esc_html($page->post_title); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" class="button create-page-btn" data-page-type="property_listing">
                                    <?php _e('Create New Page', 'plughaus-property'); ?>
                                </button>
                                <p class="description">
                                    <?php _e('Displays a grid of available properties. Use shortcode:', 'plughaus-property'); ?>
                                    <code>[phpm_property_listings]</code>
                                </p>
                            </td>
                        </tr>
                        
                        <!-- Property Detail Page -->
                        <tr>
                            <th scope="row">
                                <label for="property_detail_page"><?php _e('Property Details Page', 'plughaus-property'); ?></label>
                            </th>
                            <td>
                                <select id="property_detail_page" name="property_detail_page" class="page-select">
                                    <option value="0"><?php _e('— Select Page —', 'plughaus-property'); ?></option>
                                    <?php foreach ($pages as $page): ?>
                                        <option value="<?php echo $page->ID; ?>" <?php selected($settings['property_detail_page'], $page->ID); ?>>
                                            <?php echo esc_html($page->post_title); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" class="button create-page-btn" data-page-type="property_detail">
                                    <?php _e('Create New Page', 'plughaus-property'); ?>
                                </button>
                                <p class="description">
                                    <?php _e('Shows detailed information for individual properties. Use shortcode:', 'plughaus-property'); ?>
                                    <code>[phpm_property_detail]</code>
                                </p>
                            </td>
                        </tr>
                        
                        <!-- Tenant Portal Page -->
                        <tr>
                            <th scope="row">
                                <label for="tenant_portal_page"><?php _e('Tenant Portal Page', 'plughaus-property'); ?></label>
                            </th>
                            <td>
                                <select id="tenant_portal_page" name="tenant_portal_page" class="page-select">
                                    <option value="0"><?php _e('— Select Page —', 'plughaus-property'); ?></option>
                                    <?php foreach ($pages as $page): ?>
                                        <option value="<?php echo $page->ID; ?>" <?php selected($settings['tenant_portal_page'], $page->ID); ?>>
                                            <?php echo esc_html($page->post_title); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" class="button create-page-btn" data-page-type="tenant_portal">
                                    <?php _e('Create New Page', 'plughaus-property'); ?>
                                </button>
                                <p class="description">
                                    <?php _e('Main tenant portal with lease info and quick actions. Use shortcode:', 'plughaus-property'); ?>
                                    <code>[phpm_tenant_portal]</code>
                                </p>
                            </td>
                        </tr>
                        
                        <!-- Tenant Dashboard Page -->
                        <tr>
                            <th scope="row">
                                <label for="tenant_dashboard_page"><?php _e('Tenant Dashboard Page', 'plughaus-property'); ?></label>
                            </th>
                            <td>
                                <select id="tenant_dashboard_page" name="tenant_dashboard_page" class="page-select">
                                    <option value="0"><?php _e('— Select Page —', 'plughaus-property'); ?></option>
                                    <?php foreach ($pages as $page): ?>
                                        <option value="<?php echo $page->ID; ?>" <?php selected($settings['tenant_dashboard_page'], $page->ID); ?>>
                                            <?php echo esc_html($page->post_title); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" class="button create-page-btn" data-page-type="tenant_dashboard">
                                    <?php _e('Create New Page', 'plughaus-property'); ?>
                                </button>
                                <p class="description">
                                    <?php _e('Comprehensive tenant dashboard with payments, documents, etc. Use shortcode:', 'plughaus-property'); ?>
                                    <code>[phpm_tenant_dashboard]</code>
                                </p>
                            </td>
                        </tr>
                        
                        <!-- Maintenance Request Page -->
                        <tr>
                            <th scope="row">
                                <label for="maintenance_request_page"><?php _e('Maintenance Request Page', 'plughaus-property'); ?></label>
                            </th>
                            <td>
                                <select id="maintenance_request_page" name="maintenance_request_page" class="page-select">
                                    <option value="0"><?php _e('— Select Page —', 'plughaus-property'); ?></option>
                                    <?php foreach ($pages as $page): ?>
                                        <option value="<?php echo $page->ID; ?>" <?php selected($settings['maintenance_request_page'], $page->ID); ?>>
                                            <?php echo esc_html($page->post_title); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" class="button create-page-btn" data-page-type="maintenance_request">
                                    <?php _e('Create New Page', 'plughaus-property'); ?>
                                </button>
                                <p class="description">
                                    <?php _e('Form for tenants to submit maintenance requests. Use shortcode:', 'plughaus-property'); ?>
                                    <code>[phpm_maintenance_request_form]</code>
                                </p>
                            </td>
                        </tr>
                        
                        <!-- Property Search Page -->
                        <tr>
                            <th scope="row">
                                <label for="property_search_page"><?php _e('Property Search Page', 'plughaus-property'); ?></label>
                            </th>
                            <td>
                                <select id="property_search_page" name="property_search_page" class="page-select">
                                    <option value="0"><?php _e('— Select Page —', 'plughaus-property'); ?></option>
                                    <?php foreach ($pages as $page): ?>
                                        <option value="<?php echo $page->ID; ?>" <?php selected($settings['property_search_page'], $page->ID); ?>>
                                            <?php echo esc_html($page->post_title); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" class="button create-page-btn" data-page-type="property_search">
                                    <?php _e('Create New Page', 'plughaus-property'); ?>
                                </button>
                                <p class="description">
                                    <?php _e('Advanced property search with filters. Use shortcode:', 'plughaus-property'); ?>
                                    <code>[phpm_property_search]</code>
                                </p>
                            </td>
                        </tr>
                        
                        <!-- Application Page -->
                        <tr>
                            <th scope="row">
                                <label for="application_page"><?php _e('Rental Application Page', 'plughaus-property'); ?></label>
                            </th>
                            <td>
                                <select id="application_page" name="application_page" class="page-select">
                                    <option value="0"><?php _e('— Select Page —', 'plughaus-property'); ?></option>
                                    <?php foreach ($pages as $page): ?>
                                        <option value="<?php echo $page->ID; ?>" <?php selected($settings['application_page'], $page->ID); ?>>
                                            <?php echo esc_html($page->post_title); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" class="button create-page-btn" data-page-type="application">
                                    <?php _e('Create New Page', 'plughaus-property'); ?>
                                </button>
                                <p class="description">
                                    <?php _e('Online rental application form. Use shortcode:', 'plughaus-property'); ?>
                                    <code>[phpm_rental_application]</code>
                                </p>
                            </td>
                        </tr>
                        
                        <!-- Contact Page -->
                        <tr>
                            <th scope="row">
                                <label for="contact_page"><?php _e('Contact/Inquiry Page', 'plughaus-property'); ?></label>
                            </th>
                            <td>
                                <select id="contact_page" name="contact_page" class="page-select">
                                    <option value="0"><?php _e('— Select Page —', 'plughaus-property'); ?></option>
                                    <?php foreach ($pages as $page): ?>
                                        <option value="<?php echo $page->ID; ?>" <?php selected($settings['contact_page'], $page->ID); ?>>
                                            <?php echo esc_html($page->post_title); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" class="button create-page-btn" data-page-type="contact">
                                    <?php _e('Create New Page', 'plughaus-property'); ?>
                                </button>
                                <p class="description">
                                    <?php _e('Contact form for property inquiries. Use shortcode:', 'plughaus-property'); ?>
                                    <code>[phpm_contact_form]</code>
                                </p>
                            </td>
                        </tr>
                        
                    </table>
                </div>
                
                <!-- Display Settings -->
                <div class="card">
                    <h2><span class="dashicons dashicons-admin-appearance"></span> <?php _e('Display Settings', 'plughaus-property'); ?></h2>
                    
                    <table class="form-table">
                        
                        <!-- Property Listings Settings -->
                        <tr>
                            <th scope="row"><?php _e('Property Listings', 'plughaus-property'); ?></th>
                            <td>
                                <fieldset>
                                    <legend class="screen-reader-text"><?php _e('Property listing options', 'plughaus-property'); ?></legend>
                                    
                                    <label>
                                        <input type="checkbox" name="enable_property_listings" value="1" <?php checked($settings['enable_property_listings']); ?> />
                                        <?php _e('Enable public property listings', 'plughaus-property'); ?>
                                    </label><br>
                                    
                                    <label>
                                        <input type="checkbox" name="show_available_only" value="1" <?php checked($settings['show_available_only']); ?> />
                                        <?php _e('Show only available properties', 'plughaus-property'); ?>
                                    </label><br>
                                    
                                    <label>
                                        <input type="checkbox" name="show_property_images" value="1" <?php checked($settings['show_property_images']); ?> />
                                        <?php _e('Show property images', 'plughaus-property'); ?>
                                    </label><br>
                                    
                                    <label>
                                        <?php _e('Properties per page:', 'plughaus-property'); ?>
                                        <input type="number" name="properties_per_page" value="<?php echo esc_attr($settings['properties_per_page']); ?>" min="1" max="50" style="width: 60px;" />
                                    </label>
                                </fieldset>
                            </td>
                        </tr>
                        
                        <!-- Tenant Portal Settings -->
                        <tr>
                            <th scope="row"><?php _e('Tenant Portal', 'plughaus-property'); ?></th>
                            <td>
                                <fieldset>
                                    <legend class="screen-reader-text"><?php _e('Tenant portal options', 'plughaus-property'); ?></legend>
                                    
                                    <label>
                                        <input type="checkbox" name="enable_tenant_portal" value="1" <?php checked($settings['enable_tenant_portal']); ?> />
                                        <?php _e('Enable tenant portal', 'plughaus-property'); ?>
                                    </label><br>
                                    
                                    <label>
                                        <input type="checkbox" name="require_login_for_portal" value="1" <?php checked($settings['require_login_for_portal']); ?> />
                                        <?php _e('Require login to access tenant portal', 'plughaus-property'); ?>
                                    </label><br>
                                    
                                    <label>
                                        <input type="checkbox" name="enable_maintenance_requests" value="1" <?php checked($settings['enable_maintenance_requests']); ?> />
                                        <?php _e('Allow tenants to submit maintenance requests', 'plughaus-property'); ?>
                                    </label>
                                </fieldset>
                            </td>
                        </tr>
                        
                        <!-- Map Settings -->
                        <tr>
                            <th scope="row"><?php _e('Map Display', 'plughaus-property'); ?></th>
                            <td>
                                <fieldset>
                                    <legend class="screen-reader-text"><?php _e('Map display options', 'plughaus-property'); ?></legend>
                                    
                                    <label>
                                        <input type="checkbox" name="show_property_map" value="1" <?php checked($settings['show_property_map']); ?> />
                                        <?php _e('Show property locations on map', 'plughaus-property'); ?>
                                    </label><br>
                                    
                                    <label>
                                        <?php _e('Default map zoom level:', 'plughaus-property'); ?>
                                        <input type="number" name="default_map_zoom" value="<?php echo esc_attr($settings['default_map_zoom']); ?>" min="1" max="20" style="width: 60px;" />
                                    </label>
                                </fieldset>
                            </td>
                        </tr>
                        
                        <!-- Advanced Settings -->
                        <tr>
                            <th scope="row"><?php _e('Advanced Features', 'plughaus-property'); ?></th>
                            <td>
                                <fieldset>
                                    <legend class="screen-reader-text"><?php _e('Advanced feature options', 'plughaus-property'); ?></legend>
                                    
                                    <label>
                                        <input type="checkbox" name="enable_public_search" value="1" <?php checked($settings['enable_public_search']); ?> />
                                        <?php _e('Enable public property search', 'plughaus-property'); ?>
                                    </label><br>
                                    
                                    <label>
                                        <input type="checkbox" name="allow_online_applications" value="1" <?php checked($settings['allow_online_applications']); ?> />
                                        <?php _e('Allow online rental applications', 'plughaus-property'); ?>
                                    </label>
                                </fieldset>
                            </td>
                        </tr>
                        
                    </table>
                </div>
                
                <!-- Page Templates Guide -->
                <div class="card">
                    <h2><span class="dashicons dashicons-media-code"></span> <?php _e('Shortcode Reference', 'plughaus-property'); ?></h2>
                    <p><?php _e('Use these shortcodes to display property management content on any page or post:', 'plughaus-property'); ?></p>
                    
                    <div class="shortcode-grid">
                        
                        <div class="shortcode-item">
                            <h4><?php _e('Property Listings', 'plughaus-property'); ?></h4>
                            <code>[phpm_property_listings]</code>
                            <p><?php _e('Displays a grid of available properties with filters', 'plughaus-property'); ?></p>
                            <details>
                                <summary><?php _e('Available attributes', 'plughaus-property'); ?></summary>
                                <ul>
                                    <li><code>type="apartment"</code> - <?php _e('Filter by property type', 'plughaus-property'); ?></li>
                                    <li><code>limit="6"</code> - <?php _e('Number of properties to show', 'plughaus-property'); ?></li>
                                    <li><code>show_search="true"</code> - <?php _e('Include search form', 'plughaus-property'); ?></li>
                                    <li><code>show_filters="true"</code> - <?php _e('Include filter options', 'plughaus-property'); ?></li>
                                </ul>
                            </details>
                        </div>
                        
                        <div class="shortcode-item">
                            <h4><?php _e('Property Details', 'plughaus-property'); ?></h4>
                            <code>[phpm_property_detail id="123"]</code>
                            <p><?php _e('Shows detailed information for a specific property', 'plughaus-property'); ?></p>
                            <details>
                                <summary><?php _e('Available attributes', 'plughaus-property'); ?></summary>
                                <ul>
                                    <li><code>id="123"</code> - <?php _e('Property ID (required)', 'plughaus-property'); ?></li>
                                    <li><code>show_map="true"</code> - <?php _e('Include location map', 'plughaus-property'); ?></li>
                                    <li><code>show_units="true"</code> - <?php _e('Show available units', 'plughaus-property'); ?></li>
                                </ul>
                            </details>
                        </div>
                        
                        <div class="shortcode-item">
                            <h4><?php _e('Tenant Portal', 'plughaus-property'); ?></h4>
                            <code>[phpm_tenant_portal]</code>
                            <p><?php _e('Main tenant portal with lease info and quick actions', 'plughaus-property'); ?></p>
                            <details>
                                <summary><?php _e('Available attributes', 'plughaus-property'); ?></summary>
                                <ul>
                                    <li><code>show_payments="true"</code> - <?php _e('Show payment history', 'plughaus-property'); ?></li>
                                    <li><code>show_documents="true"</code> - <?php _e('Show document library', 'plughaus-property'); ?></li>
                                    <li><code>show_maintenance="true"</code> - <?php _e('Show maintenance requests', 'plughaus-property'); ?></li>
                                </ul>
                            </details>
                        </div>
                        
                        <div class="shortcode-item">
                            <h4><?php _e('Maintenance Request Form', 'plughaus-property'); ?></h4>
                            <code>[phpm_maintenance_request_form]</code>
                            <p><?php _e('Form for tenants to submit maintenance requests', 'plughaus-property'); ?></p>
                            <details>
                                <summary><?php _e('Available attributes', 'plughaus-property'); ?></summary>
                                <ul>
                                    <li><code>show_history="true"</code> - <?php _e('Include request history', 'plughaus-property'); ?></li>
                                    <li><code>allow_images="true"</code> - <?php _e('Allow image uploads', 'plughaus-property'); ?></li>
                                </ul>
                            </details>
                        </div>
                        
                        <div class="shortcode-item">
                            <h4><?php _e('Property Search', 'plughaus-property'); ?></h4>
                            <code>[phpm_property_search]</code>
                            <p><?php _e('Advanced search form with multiple filters', 'plughaus-property'); ?></p>
                            <details>
                                <summary><?php _e('Available attributes', 'plughaus-property'); ?></summary>
                                <ul>
                                    <li><code>style="horizontal"</code> - <?php _e('Search form layout', 'plughaus-property'); ?></li>
                                    <li><code>show_results="true"</code> - <?php _e('Show results below form', 'plughaus-property'); ?></li>
                                </ul>
                            </details>
                        </div>
                        
                        <div class="shortcode-item">
                            <h4><?php _e('Contact Form', 'plughaus-property'); ?></h4>
                            <code>[phpm_contact_form]</code>
                            <p><?php _e('Contact form for property inquiries', 'plughaus-property'); ?></p>
                            <details>
                                <summary><?php _e('Available attributes', 'plughaus-property'); ?></summary>
                                <ul>
                                    <li><code>property_id="123"</code> - <?php _e('Pre-fill specific property', 'plughaus-property'); ?></li>
                                    <li><code>show_property_select="true"</code> - <?php _e('Include property dropdown', 'plughaus-property'); ?></li>
                                </ul>
                            </details>
                        </div>
                        
                    </div>
                </div>
                
                <!-- Save Button -->
                <div class="submit-section">
                    <p class="submit">
                        <button type="button" id="save-frontend-settings" class="button button-primary button-large">
                            <span class="dashicons dashicons-yes"></span>
                            <?php _e('Save Frontend Settings', 'plughaus-property'); ?>
                        </button>
                    </p>
                </div>
                
            </div>
            
            <div id="frontend-settings-feedback" class="notice" style="display: none;">
                <p><span id="feedback-message"></span></p>
            </div>
        </div>
        <?php
    }
    
    /**
     * Get page template content for different page types
     */
    public static function get_page_template_content($page_type) {
        $templates = array(
            'property_listing' => array(
                'title' => __('Properties', 'plughaus-property'),
                'content' => '<h2>' . __('Available Properties', 'plughaus-property') . '</h2>
<p>' . __('Browse our available rental properties below.', 'plughaus-property') . '</p>

[phpm_property_listings show_search="true" show_filters="true"]

<h3>' . __('Need Help?', 'plughaus-property') . '</h3>
<p>' . __('Contact us if you have any questions about our properties or the rental process.', 'plughaus-property') . '</p>'
            ),
            
            'property_detail' => array(
                'title' => __('Property Details', 'plughaus-property'),
                'content' => '<h2>' . __('Property Information', 'plughaus-property') . '</h2>

[phpm_property_detail show_map="true" show_units="true"]

<p><a href="' . get_permalink(self::get_frontend_settings()['property_listing_page']) . '">' . __('← Back to Properties', 'plughaus-property') . '</a></p>'
            ),
            
            'tenant_portal' => array(
                'title' => __('Tenant Portal', 'plughaus-property'),
                'content' => '<h2>' . __('Welcome to Your Tenant Portal', 'plughaus-property') . '</h2>
<p>' . __('Access your lease information, submit maintenance requests, and manage your tenancy.', 'plughaus-property') . '</p>

[phpm_tenant_portal show_payments="true" show_documents="true" show_maintenance="true"]'
            ),
            
            'tenant_dashboard' => array(
                'title' => __('Tenant Dashboard', 'plughaus-property'),
                'content' => '<h2>' . __('Your Dashboard', 'plughaus-property') . '</h2>

[phpm_tenant_dashboard]'
            ),
            
            'maintenance_request' => array(
                'title' => __('Maintenance Request', 'plughaus-property'),
                'content' => '<h2>' . __('Submit a Maintenance Request', 'plughaus-property') . '</h2>
<p>' . __('Please provide as much detail as possible about the maintenance issue you\'re experiencing.', 'plughaus-property') . '</p>

[phpm_maintenance_request_form show_history="true" allow_images="true"]'
            ),
            
            'property_search' => array(
                'title' => __('Property Search', 'plughaus-property'),
                'content' => '<h2>' . __('Find Your Perfect Property', 'plughaus-property') . '</h2>
<p>' . __('Use the filters below to search for properties that meet your needs.', 'plughaus-property') . '</p>

[phpm_property_search style="horizontal" show_results="true"]'
            ),
            
            'application' => array(
                'title' => __('Rental Application', 'plughaus-property'),
                'content' => '<h2>' . __('Rental Application', 'plughaus-property') . '</h2>
<p>' . __('Please complete the application form below. All fields marked with an asterisk (*) are required.', 'plughaus-property') . '</p>

[phpm_rental_application]

<h3>' . __('Application Process', 'plughaus-property') . '</h3>
<ol>
<li>' . __('Complete the application form', 'plughaus-property') . '</li>
<li>' . __('Submit required documents', 'plughaus-property') . '</li>
<li>' . __('Application review (24-48 hours)', 'plughaus-property') . '</li>
<li>' . __('Approval notification', 'plughaus-property') . '</li>
</ol>'
            ),
            
            'contact' => array(
                'title' => __('Contact Us', 'plughaus-property'),
                'content' => '<h2>' . __('Get in Touch', 'plughaus-property') . '</h2>
<p>' . __('Have questions about our properties or services? We\'re here to help!', 'plughaus-property') . '</p>

[phpm_contact_form show_property_select="true"]

<h3>' . __('Other Ways to Reach Us', 'plughaus-property') . '</h3>
<p>' . __('Phone: [Your Phone Number]', 'plughaus-property') . '<br>
' . __('Email: [Your Email Address]', 'plughaus-property') . '<br>
' . __('Office Hours: Monday - Friday, 9AM - 5PM', 'plughaus-property') . '</p>'
            )
        );
        
        return isset($templates[$page_type]) ? $templates[$page_type] : array('title' => '', 'content' => '');
    }
    
    /**
     * AJAX handler for saving frontend settings
     */
    public static function ajax_save_settings() {
        check_ajax_referer('phpm_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions.', 'plughaus-property'));
        }
        
        // Sanitize and prepare settings
        $settings = array();
        
        // Page assignments
        $page_fields = array(
            'property_listing_page', 'property_detail_page', 'tenant_portal_page',
            'maintenance_request_page', 'tenant_dashboard_page', 'property_search_page',
            'contact_page', 'application_page'
        );
        
        foreach ($page_fields as $field) {
            $settings[$field] = absint($_POST[$field]);
        }
        
        // Display settings
        $settings['enable_property_listings'] = isset($_POST['enable_property_listings']);
        $settings['enable_tenant_portal'] = isset($_POST['enable_tenant_portal']);
        $settings['enable_maintenance_requests'] = isset($_POST['enable_maintenance_requests']);
        $settings['enable_public_search'] = isset($_POST['enable_public_search']);
        $settings['require_login_for_portal'] = isset($_POST['require_login_for_portal']);
        $settings['allow_online_applications'] = isset($_POST['allow_online_applications']);
        $settings['show_available_only'] = isset($_POST['show_available_only']);
        $settings['show_property_images'] = isset($_POST['show_property_images']);
        $settings['show_property_map'] = isset($_POST['show_property_map']);
        
        // Numeric settings
        $settings['properties_per_page'] = absint($_POST['properties_per_page']);
        $settings['default_map_zoom'] = absint($_POST['default_map_zoom']);
        
        // Update settings
        $updated = update_option('phpm_frontend_settings', $settings);
        
        if ($updated) {
            wp_send_json_success(__('Frontend settings saved successfully!', 'plughaus-property'));
        } else {
            wp_send_json_error(__('Failed to save frontend settings.', 'plughaus-property'));
        }
    }
    
    /**
     * AJAX handler for creating frontend pages
     */
    public static function ajax_create_page() {
        check_ajax_referer('phpm_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions.', 'plughaus-property'));
        }
        
        $page_type = sanitize_text_field($_POST['page_type']);
        $template = self::get_page_template_content($page_type);
        
        if (empty($template['title'])) {
            wp_send_json_error(__('Invalid page type.', 'plughaus-property'));
        }
        
        // Create the page
        $page_data = array(
            'post_title' => $template['title'],
            'post_content' => $template['content'],
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_author' => get_current_user_id(),
            'meta_input' => array(
                '_phpm_page_type' => $page_type,
                '_phpm_created_by_plugin' => true
            )
        );
        
        $page_id = wp_insert_post($page_data);
        
        if (is_wp_error($page_id)) {
            wp_send_json_error(__('Failed to create page.', 'plughaus-property'));
        }
        
        // Auto-assign the page to the setting
        $settings = self::get_frontend_settings();
        $settings[$page_type . '_page'] = $page_id;
        update_option('phpm_frontend_settings', $settings);
        
        wp_send_json_success(array(
            'page_id' => $page_id,
            'page_title' => $template['title'],
            'edit_url' => admin_url('post.php?post=' . $page_id . '&action=edit'),
            'view_url' => get_permalink($page_id)
        ));
    }
}

// Initialize frontend settings admin
PHPM_Frontend_Settings_Admin::init();