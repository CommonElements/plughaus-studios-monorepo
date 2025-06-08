<?php
/**
 * Admin functionality for PlugHaus Property Management
 */

if (!defined('ABSPATH')) {
    exit;
}

class PHPM_Admin {
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->init_hooks();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Add dashboard widgets
        add_action('wp_dashboard_setup', array($this, 'add_dashboard_widgets'));
        
        // Add meta boxes
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        
        // Save meta data
        add_action('save_post', array($this, 'save_meta_data'));
    }
    
    /**
     * Enqueue admin styles
     */
    public function enqueue_styles($hook) {
        // Only load on our plugin pages
        if (strpos($hook, 'phpm') === false && get_post_type() !== 'phpm_property' && get_post_type() !== 'phpm_unit' && get_post_type() !== 'phpm_tenant' && get_post_type() !== 'phpm_lease' && get_post_type() !== 'phpm_maintenance') {
            return;
        }
        
        wp_enqueue_style(
            'phpm-admin',
            PM_PLUGIN_URL . 'core/assets/css/admin.css',
            array(),
            PM_VERSION
        );
    }
    
    /**
     * Enqueue admin scripts
     */
    public function enqueue_scripts($hook) {
        // Only load on our plugin pages
        if (strpos($hook, 'phpm') === false && get_post_type() !== 'phpm_property' && get_post_type() !== 'phpm_unit' && get_post_type() !== 'phpm_tenant' && get_post_type() !== 'phpm_lease' && get_post_type() !== 'phpm_maintenance') {
            return;
        }
        
        wp_enqueue_script(
            'phpm-admin',
            PM_PLUGIN_URL . 'core/assets/js/admin.js',
            array('jquery', 'wp-api'),
            PM_VERSION,
            true
        );
        
        // Localize script
        wp_localize_script('phpm-admin', 'phpm_admin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('phpm_admin_nonce'),
            'api_url' => home_url('/wp-json/phpm/v1/'),
            'strings' => array(
                'confirm_delete' => __('Are you sure you want to delete this?', 'plughaus-property'),
                'saving' => __('Saving...', 'plughaus-property'),
                'saved' => __('Saved!', 'plughaus-property'),
                'error' => __('An error occurred. Please try again.', 'plughaus-property'),
            )
        ));
    }
    
    /**
     * Add admin menus
     */
    public function add_admin_menus() {
        // Main menu
        add_menu_page(
            __('PlugHaus Property', 'plughaus-property'),
            __('Property Mgmt', 'plughaus-property'),
            'manage_options',
            'phpm-dashboard',
            array($this, 'render_dashboard'),
            'dashicons-building',
            25
        );
        
        // Dashboard submenu
        add_submenu_page(
            'phpm-dashboard',
            __('Dashboard', 'plughaus-property'),
            __('Dashboard', 'plughaus-property'),
            'manage_options',
            'phpm-dashboard',
            array($this, 'render_dashboard')
        );
        
        // Properties submenu - handled by post type
        
        // Reports submenu
        add_submenu_page(
            'phpm-dashboard',
            __('Reports', 'plughaus-property'),
            __('Reports', 'plughaus-property'),
            'manage_options',
            'phpm-reports',
            array($this, 'render_reports')
        );
        
        // Import/Export submenu
        add_submenu_page(
            'phpm-dashboard',
            __('Import/Export', 'plughaus-property'),
            __('Import/Export', 'plughaus-property'),
            'manage_options',
            'phpm-import-export',
            array($this, 'render_import_export')
        );
        
        // Settings submenu
        add_submenu_page(
            'phpm-dashboard',
            __('Settings', 'plughaus-property'),
            __('Settings', 'plughaus-property'),
            'manage_options',
            'phpm-settings',
            array($this, 'render_settings')
        );
    }
    
    /**
     * Render dashboard page
     */
    public function render_dashboard() {
        ?>
        <div class="wrap">
            <h1><?php _e('Property Management Dashboard', 'plughaus-property'); ?></h1>
            
            <div class="phpm-dashboard-widgets">
                <div class="phpm-widget">
                    <h3><?php _e('Quick Stats', 'plughaus-property'); ?></h3>
                    <?php $this->render_quick_stats(); ?>
                </div>
                
                <div class="phpm-widget">
                    <h3><?php _e('Recent Activity', 'plughaus-property'); ?></h3>
                    <?php $this->render_recent_activity(); ?>
                </div>
                
                <div class="phpm-widget">
                    <h3><?php _e('Upcoming Lease Expirations', 'plughaus-property'); ?></h3>
                    <?php $this->render_lease_expirations(); ?>
                </div>
                
                <div class="phpm-widget">
                    <h3><?php _e('Maintenance Requests', 'plughaus-property'); ?></h3>
                    <?php $this->render_maintenance_summary(); ?>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render quick stats widget
     */
    private function render_quick_stats() {
        $total_properties = wp_count_posts('phpm_property')->publish;
        $total_units = wp_count_posts('phpm_unit')->publish;
        $total_tenants = wp_count_posts('phpm_tenant')->publish;
        $active_leases = $this->count_active_leases();
        
        ?>
        <div class="phpm-stats-grid">
            <div class="phpm-stat">
                <div class="phpm-stat-number"><?php echo esc_html($total_properties); ?></div>
                <div class="phpm-stat-label"><?php _e('Properties', 'plughaus-property'); ?></div>
            </div>
            <div class="phpm-stat">
                <div class="phpm-stat-number"><?php echo esc_html($total_units); ?></div>
                <div class="phpm-stat-label"><?php _e('Units', 'plughaus-property'); ?></div>
            </div>
            <div class="phpm-stat">
                <div class="phpm-stat-number"><?php echo esc_html($total_tenants); ?></div>
                <div class="phpm-stat-label"><?php _e('Tenants', 'plughaus-property'); ?></div>
            </div>
            <div class="phpm-stat">
                <div class="phpm-stat-number"><?php echo esc_html($active_leases); ?></div>
                <div class="phpm-stat-label"><?php _e('Active Leases', 'plughaus-property'); ?></div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Count active leases
     */
    private function count_active_leases() {
        $args = array(
            'post_type' => 'phpm_lease',
            'post_status' => 'active',
            'posts_per_page' => -1,
            'fields' => 'ids'
        );
        
        $query = new WP_Query($args);
        return $query->found_posts;
    }
    
    /**
     * Render recent activity
     */
    private function render_recent_activity() {
        // This would show recent property additions, tenant changes, etc.
        echo '<p>' . __('No recent activity to display.', 'plughaus-property') . '</p>';
    }
    
    /**
     * Render lease expirations
     */
    private function render_lease_expirations() {
        // This would show upcoming lease expirations
        echo '<p>' . __('No upcoming lease expirations.', 'plughaus-property') . '</p>';
    }
    
    /**
     * Render maintenance summary
     */
    private function render_maintenance_summary() {
        // This would show pending maintenance requests
        echo '<p>' . __('No pending maintenance requests.', 'plughaus-property') . '</p>';
    }
    
    /**
     * Render reports page
     */
    public function render_reports() {
        ?>
        <div class="wrap">
            <h1><?php _e('Property Reports', 'plughaus-property'); ?></h1>
            <p><?php _e('Generate various reports for your properties.', 'plughaus-property'); ?></p>
            
            <div class="phpm-reports-list">
                <div class="phpm-report-card">
                    <h3><?php _e('Occupancy Report', 'plughaus-property'); ?></h3>
                    <p><?php _e('View occupancy rates across all properties.', 'plughaus-property'); ?></p>
                    <button class="button button-primary"><?php _e('Generate Report', 'plughaus-property'); ?></button>
                </div>
                
                <div class="phpm-report-card">
                    <h3><?php _e('Financial Summary', 'plughaus-property'); ?></h3>
                    <p><?php _e('Overview of rental income and expenses.', 'plughaus-property'); ?></p>
                    <button class="button button-primary"><?php _e('Generate Report', 'plughaus-property'); ?></button>
                </div>
                
                <div class="phpm-report-card">
                    <h3><?php _e('Tenant Report', 'plughaus-property'); ?></h3>
                    <p><?php _e('List of all current and past tenants.', 'plughaus-property'); ?></p>
                    <button class="button button-primary"><?php _e('Generate Report', 'plughaus-property'); ?></button>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render import/export page
     */
    public function render_import_export() {
        ?>
        <div class="wrap">
            <h1><?php _e('Import/Export Data', 'plughaus-property'); ?></h1>
            
            <div class="phpm-import-export-sections">
                <div class="phpm-section">
                    <h2><?php _e('Import Data', 'plughaus-property'); ?></h2>
                    <p><?php _e('Import properties, units, and tenants from a CSV file.', 'plughaus-property'); ?></p>
                    
                    <form method="post" enctype="multipart/form-data">
                        <?php wp_nonce_field('phpm_import_data', 'phpm_import_nonce'); ?>
                        
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="import_type"><?php _e('Import Type', 'plughaus-property'); ?></label>
                                </th>
                                <td>
                                    <select name="import_type" id="import_type">
                                        <option value="properties"><?php _e('Properties', 'plughaus-property'); ?></option>
                                        <option value="units"><?php _e('Units', 'plughaus-property'); ?></option>
                                        <option value="tenants"><?php _e('Tenants', 'plughaus-property'); ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="import_file"><?php _e('CSV File', 'plughaus-property'); ?></label>
                                </th>
                                <td>
                                    <input type="file" name="import_file" id="import_file" accept=".csv" required>
                                </td>
                            </tr>
                        </table>
                        
                        <p class="submit">
                            <input type="submit" name="phpm_import" class="button button-primary" value="<?php _e('Import Data', 'plughaus-property'); ?>">
                        </p>
                    </form>
                </div>
                
                <div class="phpm-section">
                    <h2><?php _e('Export Data', 'plughaus-property'); ?></h2>
                    <p><?php _e('Export your property data to CSV format.', 'plughaus-property'); ?></p>
                    
                    <form method="post">
                        <?php wp_nonce_field('phpm_export_data', 'phpm_export_nonce'); ?>
                        
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="export_type"><?php _e('Export Type', 'plughaus-property'); ?></label>
                                </th>
                                <td>
                                    <select name="export_type" id="export_type">
                                        <option value="all"><?php _e('All Data', 'plughaus-property'); ?></option>
                                        <option value="properties"><?php _e('Properties', 'plughaus-property'); ?></option>
                                        <option value="units"><?php _e('Units', 'plughaus-property'); ?></option>
                                        <option value="tenants"><?php _e('Tenants', 'plughaus-property'); ?></option>
                                        <option value="leases"><?php _e('Leases', 'plughaus-property'); ?></option>
                                    </select>
                                </td>
                            </tr>
                        </table>
                        
                        <p class="submit">
                            <input type="submit" name="phpm_export" class="button button-primary" value="<?php _e('Export Data', 'plughaus-property'); ?>">
                        </p>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render settings page
     */
    public function render_settings() {
        ?>
        <div class="wrap">
            <h1><?php _e('PlugHaus Property Settings', 'plughaus-property'); ?></h1>
            
            <form method="post" action="options.php">
                <?php
                settings_fields('phpm_settings_group');
                do_settings_sections('phpm_settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
    
    /**
     * Add dashboard widgets
     */
    public function add_dashboard_widgets() {
        wp_add_dashboard_widget(
            'phpm_dashboard_widget',
            __('Property Management Overview', 'plughaus-property'),
            array($this, 'render_dashboard_widget')
        );
    }
    
    /**
     * Render dashboard widget
     */
    public function render_dashboard_widget() {
        $this->render_quick_stats();
    }
    
    /**
     * Add meta boxes
     */
    public function add_meta_boxes() {
        // Property details
        add_meta_box(
            'phpm_property_details',
            __('Property Details', 'plughaus-property'),
            array($this, 'render_property_details_meta_box'),
            'phpm_property',
            'normal',
            'high'
        );
        
        // Unit details
        add_meta_box(
            'phpm_unit_details',
            __('Unit Details', 'plughaus-property'),
            array($this, 'render_unit_details_meta_box'),
            'phpm_unit',
            'normal',
            'high'
        );
        
        // Tenant details
        add_meta_box(
            'phpm_tenant_details',
            __('Tenant Details', 'plughaus-property'),
            array($this, 'render_tenant_details_meta_box'),
            'phpm_tenant',
            'normal',
            'high'
        );
        
        // Lease details
        add_meta_box(
            'phpm_lease_details',
            __('Lease Details', 'plughaus-property'),
            array($this, 'render_lease_details_meta_box'),
            'phpm_lease',
            'normal',
            'high'
        );
    }
    
    /**
     * Render property details meta box
     */
    public function render_property_details_meta_box($post) {
        wp_nonce_field('phpm_save_property_details', 'phpm_property_nonce');
        
        // Get existing data
        $address = get_post_meta($post->ID, '_phpm_property_address', true);
        $city = get_post_meta($post->ID, '_phpm_property_city', true);
        $state = get_post_meta($post->ID, '_phpm_property_state', true);
        $zip = get_post_meta($post->ID, '_phpm_property_zip', true);
        $units = get_post_meta($post->ID, '_phpm_property_units', true);
        $type = get_post_meta($post->ID, '_phpm_property_type', true);
        $property_code = get_post_meta($post->ID, '_phpm_property_code', true);
        
        // Generate property code if doesn't exist
        if (empty($property_code) && $post->ID) {
            $property_code = PHPM_Utilities::generate_property_code();
            update_post_meta($post->ID, '_phpm_property_code', $property_code);
        }
        
        ?>
        <div class="phpm-meta-box">
            <table class="form-table">
                <?php if (!empty($property_code)): ?>
                <tr>
                    <th><label><?php _e('Property Code', 'plughaus-property'); ?></label></th>
                    <td>
                        <strong><?php echo esc_html($property_code); ?></strong>
                        <p class="description"><?php _e('Unique identifier for this property', 'plughaus-property'); ?></p>
                    </td>
                </tr>
                <?php endif; ?>
                
                <tr>
                    <th><label for="phpm_property_type"><?php _e('Property Type', 'plughaus-property'); ?></label></th>
                    <td>
                        <select id="phpm_property_type" name="phpm_property_type" class="regular-text">
                            <option value=""><?php _e('Select Type', 'plughaus-property'); ?></option>
                            <?php foreach (PHPM_Utilities::get_property_types() as $key => $label): ?>
                                <option value="<?php echo esc_attr($key); ?>" <?php selected($type, $key); ?>>
                                    <?php echo esc_html($label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th><label for="phpm_property_address"><?php _e('Street Address', 'plughaus-property'); ?></label></th>
                    <td>
                        <input type="text" id="phpm_property_address" name="phpm_property_address" 
                               value="<?php echo esc_attr($address); ?>" class="large-text" 
                               placeholder="<?php _e('123 Main Street', 'plughaus-property'); ?>" />
                    </td>
                </tr>
                
                <tr>
                    <th><label for="phpm_property_city"><?php _e('City', 'plughaus-property'); ?></label></th>
                    <td>
                        <input type="text" id="phpm_property_city" name="phpm_property_city" 
                               value="<?php echo esc_attr($city); ?>" class="regular-text" 
                               placeholder="<?php _e('San Francisco', 'plughaus-property'); ?>" />
                    </td>
                </tr>
                
                <tr>
                    <th><label for="phpm_property_state"><?php _e('State/Province', 'plughaus-property'); ?></label></th>
                    <td>
                        <input type="text" id="phpm_property_state" name="phpm_property_state" 
                               value="<?php echo esc_attr($state); ?>" class="small-text" 
                               placeholder="<?php _e('CA', 'plughaus-property'); ?>" />
                    </td>
                </tr>
                
                <tr>
                    <th><label for="phpm_property_zip"><?php _e('ZIP/Postal Code', 'plughaus-property'); ?></label></th>
                    <td>
                        <input type="text" id="phpm_property_zip" name="phpm_property_zip" 
                               value="<?php echo esc_attr($zip); ?>" class="small-text" 
                               placeholder="<?php _e('94102', 'plughaus-property'); ?>" />
                    </td>
                </tr>
                
                <tr>
                    <th><label for="phpm_property_units"><?php _e('Number of Units', 'plughaus-property'); ?></label></th>
                    <td>
                        <input type="number" id="phpm_property_units" name="phpm_property_units" 
                               value="<?php echo esc_attr($units); ?>" class="small-text" min="1" max="999" 
                               placeholder="1" />
                        <p class="description"><?php _e('Total rentable units in this property', 'plughaus-property'); ?></p>
                    </td>
                </tr>
            </table>
            
            <?php if (!PHPM_Utilities::is_pro()): ?>
            <div class="phpm-pro-notice">
                <p>
                    <span class="dashicons dashicons-star-filled"></span>
                    <?php printf(
                        __('Unlock advanced property features like custom fields, financial tracking, and analytics with %s.', 'plughaus-property'),
                        '<a href="https://plughausstudios.com/property-management-pro/" target="_blank">PlugHaus Property Management Pro</a>'
                    ); ?>
                </p>
            </div>
            <?php endif; ?>
        </div>
        
        <style>
        .phpm-meta-box .form-table th {
            width: 180px;
            padding: 15px 0;
        }
        .phpm-meta-box .form-table td {
            padding: 15px 0;
        }
        .phpm-pro-notice {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 4px;
            padding: 12px 16px;
            margin-top: 20px;
            color: #856404;
        }
        .phpm-pro-notice .dashicons {
            color: #007cba;
            margin-right: 5px;
        }
        .phpm-pro-notice a {
            color: #007cba;
            font-weight: 600;
            text-decoration: none;
        }
        .phpm-pro-notice a:hover {
            text-decoration: underline;
        }
        </style>
        <?php
    }
    
    /**
     * Save meta data
     */
    public function save_meta_data($post_id) {
        // Check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Check permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        $post_type = get_post_type($post_id);
        
        // Save property details
        if ('phpm_property' === $post_type && isset($_POST['phpm_property_nonce'])) {
            if (!wp_verify_nonce($_POST['phpm_property_nonce'], 'phpm_save_property_details')) {
                return;
            }
            
            update_post_meta($post_id, '_phpm_property_type', sanitize_text_field($_POST['phpm_property_type']));
            update_post_meta($post_id, '_phpm_property_address', sanitize_text_field($_POST['phpm_property_address']));
            update_post_meta($post_id, '_phpm_property_city', sanitize_text_field($_POST['phpm_property_city']));
            update_post_meta($post_id, '_phpm_property_state', sanitize_text_field($_POST['phpm_property_state']));
            update_post_meta($post_id, '_phpm_property_zip', sanitize_text_field($_POST['phpm_property_zip']));
            update_post_meta($post_id, '_phpm_property_units', intval($_POST['phpm_property_units']));
        }
        
        // Save unit details
        if ('phpm_unit' === $post_type && isset($_POST['phpm_unit_nonce'])) {
            if (!wp_verify_nonce($_POST['phpm_unit_nonce'], 'phpm_save_unit_details')) {
                return;
            }
            
            $property_id = intval($_POST['phpm_unit_property_id']);
            $unit_number = sanitize_text_field($_POST['phpm_unit_number']);
            
            update_post_meta($post_id, '_phpm_unit_property_id', $property_id);
            update_post_meta($post_id, '_phpm_unit_number', $unit_number);
            update_post_meta($post_id, '_phpm_unit_bedrooms', intval($_POST['phpm_unit_bedrooms']));
            update_post_meta($post_id, '_phpm_unit_bathrooms', floatval($_POST['phpm_unit_bathrooms']));
            update_post_meta($post_id, '_phpm_unit_square_feet', intval($_POST['phpm_unit_square_feet']));
            update_post_meta($post_id, '_phpm_unit_rent_amount', floatval($_POST['phpm_unit_rent_amount']));
            update_post_meta($post_id, '_phpm_unit_status', sanitize_text_field($_POST['phpm_unit_status']));
            
            // Generate unit code if property and unit number are set
            if (!empty($property_id) && !empty($unit_number)) {
                $unit_code = PHPM_Utilities::generate_unit_code($property_id, $unit_number);
                update_post_meta($post_id, '_phpm_unit_code', $unit_code);
            }
        }
        
        // Save tenant details
        if ('phpm_tenant' === $post_type && isset($_POST['phpm_tenant_nonce'])) {
            if (!wp_verify_nonce($_POST['phpm_tenant_nonce'], 'phpm_save_tenant_details')) {
                return;
            }
            
            update_post_meta($post_id, '_phpm_tenant_first_name', sanitize_text_field($_POST['phpm_tenant_first_name']));
            update_post_meta($post_id, '_phpm_tenant_last_name', sanitize_text_field($_POST['phpm_tenant_last_name']));
            update_post_meta($post_id, '_phpm_tenant_email', sanitize_email($_POST['phpm_tenant_email']));
            update_post_meta($post_id, '_phpm_tenant_phone', sanitize_text_field($_POST['phpm_tenant_phone']));
            update_post_meta($post_id, '_phpm_tenant_emergency_name', sanitize_text_field($_POST['phpm_tenant_emergency_name']));
            update_post_meta($post_id, '_phpm_tenant_emergency_phone', sanitize_text_field($_POST['phpm_tenant_emergency_phone']));
            update_post_meta($post_id, '_phpm_tenant_emergency_relationship', sanitize_text_field($_POST['phpm_tenant_emergency_relationship']));
            update_post_meta($post_id, '_phpm_tenant_move_in_date', sanitize_text_field($_POST['phpm_tenant_move_in_date']));
            update_post_meta($post_id, '_phpm_tenant_notes', wp_kses_post($_POST['phpm_tenant_notes']));
        }
        
        // Save lease details
        if ('phpm_lease' === $post_type && isset($_POST['phpm_lease_nonce'])) {
            if (!wp_verify_nonce($_POST['phpm_lease_nonce'], 'phpm_save_lease_details')) {
                return;
            }
            
            update_post_meta($post_id, '_phpm_lease_property_id', intval($_POST['phpm_lease_property_id']));
            update_post_meta($post_id, '_phpm_lease_unit_id', intval($_POST['phpm_lease_unit_id']));
            update_post_meta($post_id, '_phpm_lease_tenant_id', intval($_POST['phpm_lease_tenant_id']));
            update_post_meta($post_id, '_phpm_lease_start_date', sanitize_text_field($_POST['phpm_lease_start_date']));
            update_post_meta($post_id, '_phpm_lease_end_date', sanitize_text_field($_POST['phpm_lease_end_date']));
            update_post_meta($post_id, '_phpm_lease_rent_amount', floatval($_POST['phpm_lease_rent_amount']));
            update_post_meta($post_id, '_phpm_lease_security_deposit', floatval($_POST['phpm_lease_security_deposit']));
            update_post_meta($post_id, '_phpm_lease_status', sanitize_text_field($_POST['phpm_lease_status']));
            update_post_meta($post_id, '_phpm_lease_notes', wp_kses_post($_POST['phpm_lease_notes']));
        }
    }
    
    /**
     * Render unit details meta box
     */
    public function render_unit_details_meta_box($post) {
        wp_nonce_field('phpm_save_unit_details', 'phpm_unit_nonce');
        
        // Get existing data
        $property_id = get_post_meta($post->ID, '_phpm_unit_property_id', true);
        $unit_number = get_post_meta($post->ID, '_phpm_unit_number', true);
        $bedrooms = get_post_meta($post->ID, '_phpm_unit_bedrooms', true);
        $bathrooms = get_post_meta($post->ID, '_phpm_unit_bathrooms', true);
        $square_feet = get_post_meta($post->ID, '_phpm_unit_square_feet', true);
        $rent_amount = get_post_meta($post->ID, '_phpm_unit_rent_amount', true);
        $unit_code = get_post_meta($post->ID, '_phpm_unit_code', true);
        $status = get_post_meta($post->ID, '_phpm_unit_status', true);
        
        // Generate unit code if doesn't exist
        if (empty($unit_code) && $post->ID && !empty($property_id)) {
            $unit_code = PHPM_Utilities::generate_unit_code($property_id, $unit_number);
            update_post_meta($post->ID, '_phpm_unit_code', $unit_code);
        }
        
        // Get properties for dropdown
        $properties = get_posts(array(
            'post_type' => 'phpm_property',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC'
        ));
        
        ?>
        <div class="phpm-meta-box">
            <table class="form-table">
                <?php if (!empty($unit_code)): ?>
                <tr>
                    <th><label><?php _e('Unit Code', 'plughaus-property'); ?></label></th>
                    <td>
                        <strong><?php echo esc_html($unit_code); ?></strong>
                        <p class="description"><?php _e('Unique identifier for this unit', 'plughaus-property'); ?></p>
                    </td>
                </tr>
                <?php endif; ?>
                
                <tr>
                    <th><label for="phpm_unit_property_id"><?php _e('Property', 'plughaus-property'); ?></label></th>
                    <td>
                        <select id="phpm_unit_property_id" name="phpm_unit_property_id" class="regular-text">
                            <option value=""><?php _e('Select Property', 'plughaus-property'); ?></option>
                            <?php foreach ($properties as $property): ?>
                                <option value="<?php echo esc_attr($property->ID); ?>" <?php selected($property_id, $property->ID); ?>>
                                    <?php echo esc_html($property->post_title); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description"><?php _e('Property this unit belongs to', 'plughaus-property'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th><label for="phpm_unit_number"><?php _e('Unit Number', 'plughaus-property'); ?></label></th>
                    <td>
                        <input type="text" id="phpm_unit_number" name="phpm_unit_number" 
                               value="<?php echo esc_attr($unit_number); ?>" class="regular-text" 
                               placeholder="<?php _e('101, A, 1st Floor, etc.', 'plughaus-property'); ?>" />
                    </td>
                </tr>
                
                <tr>
                    <th><label for="phpm_unit_bedrooms"><?php _e('Bedrooms', 'plughaus-property'); ?></label></th>
                    <td>
                        <input type="number" id="phpm_unit_bedrooms" name="phpm_unit_bedrooms" 
                               value="<?php echo esc_attr($bedrooms); ?>" class="small-text" min="0" max="20" step="1" />
                    </td>
                </tr>
                
                <tr>
                    <th><label for="phpm_unit_bathrooms"><?php _e('Bathrooms', 'plughaus-property'); ?></label></th>
                    <td>
                        <input type="number" id="phpm_unit_bathrooms" name="phpm_unit_bathrooms" 
                               value="<?php echo esc_attr($bathrooms); ?>" class="small-text" min="0" max="20" step="0.5" />
                    </td>
                </tr>
                
                <tr>
                    <th><label for="phpm_unit_square_feet"><?php _e('Square Feet', 'plughaus-property'); ?></label></th>
                    <td>
                        <input type="number" id="phpm_unit_square_feet" name="phpm_unit_square_feet" 
                               value="<?php echo esc_attr($square_feet); ?>" class="regular-text" min="1" />
                    </td>
                </tr>
                
                <tr>
                    <th><label for="phpm_unit_rent_amount"><?php _e('Monthly Rent', 'plughaus-property'); ?></label></th>
                    <td>
                        <input type="number" id="phpm_unit_rent_amount" name="phpm_unit_rent_amount" 
                               value="<?php echo esc_attr($rent_amount); ?>" class="regular-text" min="0" step="0.01" 
                               placeholder="<?php _e('2500.00', 'plughaus-property'); ?>" />
                        <p class="description"><?php _e('Monthly rental amount in local currency', 'plughaus-property'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th><label for="phpm_unit_status"><?php _e('Unit Status', 'plughaus-property'); ?></label></th>
                    <td>
                        <select id="phpm_unit_status" name="phpm_unit_status" class="regular-text">
                            <option value="available" <?php selected($status, 'available'); ?>><?php _e('Available', 'plughaus-property'); ?></option>
                            <option value="occupied" <?php selected($status, 'occupied'); ?>><?php _e('Occupied', 'plughaus-property'); ?></option>
                            <option value="maintenance" <?php selected($status, 'maintenance'); ?>><?php _e('Under Maintenance', 'plughaus-property'); ?></option>
                            <option value="offline" <?php selected($status, 'offline'); ?>><?php _e('Offline', 'plughaus-property'); ?></option>
                        </select>
                    </td>
                </tr>
            </table>
            
            <?php if (!PHPM_Utilities::is_pro()): ?>
            <div class="phpm-pro-notice">
                <p>
                    <span class="dashicons dashicons-star-filled"></span>
                    <?php printf(
                        __('Add custom fields, amenities, and detailed floor plans with %s.', 'plughaus-property'),
                        '<a href="https://plughausstudios.com/property-management-pro/" target="_blank">PlugHaus Property Management Pro</a>'
                    ); ?>
                </p>
            </div>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Render tenant details meta box
     */
    public function render_tenant_details_meta_box($post) {
        wp_nonce_field('phpm_save_tenant_details', 'phpm_tenant_nonce');
        
        // Get existing data
        $first_name = get_post_meta($post->ID, '_phpm_tenant_first_name', true);
        $last_name = get_post_meta($post->ID, '_phpm_tenant_last_name', true);
        $email = get_post_meta($post->ID, '_phpm_tenant_email', true);
        $phone = get_post_meta($post->ID, '_phpm_tenant_phone', true);
        $emergency_name = get_post_meta($post->ID, '_phpm_tenant_emergency_name', true);
        $emergency_phone = get_post_meta($post->ID, '_phpm_tenant_emergency_phone', true);
        $emergency_relationship = get_post_meta($post->ID, '_phpm_tenant_emergency_relationship', true);
        $move_in_date = get_post_meta($post->ID, '_phpm_tenant_move_in_date', true);
        $notes = get_post_meta($post->ID, '_phpm_tenant_notes', true);
        
        ?>
        <div class="phpm-meta-box">
            <table class="form-table">
                <tr>
                    <th><label for="phpm_tenant_first_name"><?php _e('First Name', 'plughaus-property'); ?> <span class="required">*</span></label></th>
                    <td>
                        <input type="text" id="phpm_tenant_first_name" name="phpm_tenant_first_name" 
                               value="<?php echo esc_attr($first_name); ?>" class="regular-text" required 
                               placeholder="<?php _e('John', 'plughaus-property'); ?>" />
                    </td>
                </tr>
                
                <tr>
                    <th><label for="phpm_tenant_last_name"><?php _e('Last Name', 'plughaus-property'); ?> <span class="required">*</span></label></th>
                    <td>
                        <input type="text" id="phpm_tenant_last_name" name="phpm_tenant_last_name" 
                               value="<?php echo esc_attr($last_name); ?>" class="regular-text" required 
                               placeholder="<?php _e('Smith', 'plughaus-property'); ?>" />
                    </td>
                </tr>
                
                <tr>
                    <th><label for="phpm_tenant_email"><?php _e('Email Address', 'plughaus-property'); ?> <span class="required">*</span></label></th>
                    <td>
                        <input type="email" id="phpm_tenant_email" name="phpm_tenant_email" 
                               value="<?php echo esc_attr($email); ?>" class="regular-text" required 
                               placeholder="<?php _e('john.smith@example.com', 'plughaus-property'); ?>" />
                        <?php if (!empty($email) && !PHPM_Utilities::validate_email($email)): ?>
                            <p class="description error"><?php _e('Invalid email format', 'plughaus-property'); ?></p>
                        <?php endif; ?>
                    </td>
                </tr>
                
                <tr>
                    <th><label for="phpm_tenant_phone"><?php _e('Phone Number', 'plughaus-property'); ?></label></th>
                    <td>
                        <input type="tel" id="phpm_tenant_phone" name="phpm_tenant_phone" 
                               value="<?php echo esc_attr($phone); ?>" class="regular-text" 
                               placeholder="<?php _e('(555) 123-4567', 'plughaus-property'); ?>" />
                        <?php if (!empty($phone)): ?>
                            <p class="description"><?php echo __('Formatted: ', 'plughaus-property') . PHPM_Utilities::format_phone($phone); ?></p>
                        <?php endif; ?>
                    </td>
                </tr>
                
                <tr>
                    <th><label for="phpm_tenant_move_in_date"><?php _e('Move-In Date', 'plughaus-property'); ?></label></th>
                    <td>
                        <input type="date" id="phpm_tenant_move_in_date" name="phpm_tenant_move_in_date" 
                               value="<?php echo esc_attr($move_in_date); ?>" class="regular-text" />
                    </td>
                </tr>
            </table>
            
            <h4><?php _e('Emergency Contact', 'plughaus-property'); ?></h4>
            <table class="form-table">
                <tr>
                    <th><label for="phpm_tenant_emergency_name"><?php _e('Emergency Contact Name', 'plughaus-property'); ?></label></th>
                    <td>
                        <input type="text" id="phpm_tenant_emergency_name" name="phpm_tenant_emergency_name" 
                               value="<?php echo esc_attr($emergency_name); ?>" class="regular-text" 
                               placeholder="<?php _e('Jane Smith', 'plughaus-property'); ?>" />
                    </td>
                </tr>
                
                <tr>
                    <th><label for="phpm_tenant_emergency_phone"><?php _e('Emergency Contact Phone', 'plughaus-property'); ?></label></th>
                    <td>
                        <input type="tel" id="phpm_tenant_emergency_phone" name="phpm_tenant_emergency_phone" 
                               value="<?php echo esc_attr($emergency_phone); ?>" class="regular-text" 
                               placeholder="<?php _e('(555) 987-6543', 'plughaus-property'); ?>" />
                    </td>
                </tr>
                
                <tr>
                    <th><label for="phpm_tenant_emergency_relationship"><?php _e('Relationship', 'plughaus-property'); ?></label></th>
                    <td>
                        <select id="phpm_tenant_emergency_relationship" name="phpm_tenant_emergency_relationship" class="regular-text">
                            <option value=""><?php _e('Select Relationship', 'plughaus-property'); ?></option>
                            <option value="spouse" <?php selected($emergency_relationship, 'spouse'); ?>><?php _e('Spouse', 'plughaus-property'); ?></option>
                            <option value="parent" <?php selected($emergency_relationship, 'parent'); ?>><?php _e('Parent', 'plughaus-property'); ?></option>
                            <option value="child" <?php selected($emergency_relationship, 'child'); ?>><?php _e('Child', 'plughaus-property'); ?></option>
                            <option value="sibling" <?php selected($emergency_relationship, 'sibling'); ?>><?php _e('Sibling', 'plughaus-property'); ?></option>
                            <option value="friend" <?php selected($emergency_relationship, 'friend'); ?>><?php _e('Friend', 'plughaus-property'); ?></option>
                            <option value="other" <?php selected($emergency_relationship, 'other'); ?>><?php _e('Other', 'plughaus-property'); ?></option>
                        </select>
                    </td>
                </tr>
            </table>
            
            <h4><?php _e('Additional Information', 'plughaus-property'); ?></h4>
            <table class="form-table">
                <tr>
                    <th><label for="phpm_tenant_notes"><?php _e('Notes', 'plughaus-property'); ?></label></th>
                    <td>
                        <textarea id="phpm_tenant_notes" name="phpm_tenant_notes" class="large-text" rows="4" 
                                  placeholder="<?php _e('Additional notes about this tenant...', 'plughaus-property'); ?>"><?php echo esc_textarea($notes); ?></textarea>
                        <p class="description"><?php _e('Any additional information or special notes about this tenant', 'plughaus-property'); ?></p>
                    </td>
                </tr>
            </table>
            
            <?php if (!PHPM_Utilities::is_pro()): ?>
            <div class="phpm-pro-notice">
                <p>
                    <span class="dashicons dashicons-star-filled"></span>
                    <?php printf(
                        __('Track tenant documents, credit history, and payment records with %s.', 'plughaus-property'),
                        '<a href="https://plughausstudios.com/property-management-pro/" target="_blank">PlugHaus Property Management Pro</a>'
                    ); ?>
                </p>
            </div>
            <?php endif; ?>
        </div>
        
        <style>
        .phpm-meta-box .required {
            color: #dc3232;
        }
        .phpm-meta-box .description.error {
            color: #dc3232;
            font-weight: bold;
        }
        .phpm-meta-box h4 {
            margin-top: 25px;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px solid #ddd;
        }
        </style>
        <?php
    }
    
    /**
     * Render lease details meta box
     */
    public function render_lease_details_meta_box($post) {
        wp_nonce_field('phpm_save_lease_details', 'phpm_lease_nonce');
        
        // Get existing data
        $property_id = get_post_meta($post->ID, '_phpm_lease_property_id', true);
        $unit_id = get_post_meta($post->ID, '_phpm_lease_unit_id', true);
        $tenant_id = get_post_meta($post->ID, '_phpm_lease_tenant_id', true);
        $start_date = get_post_meta($post->ID, '_phpm_lease_start_date', true);
        $end_date = get_post_meta($post->ID, '_phpm_lease_end_date', true);
        $rent_amount = get_post_meta($post->ID, '_phpm_lease_rent_amount', true);
        $security_deposit = get_post_meta($post->ID, '_phpm_lease_security_deposit', true);
        $status = get_post_meta($post->ID, '_phpm_lease_status', true);
        $notes = get_post_meta($post->ID, '_phpm_lease_notes', true);
        
        // Get properties for dropdown
        $properties = get_posts(array(
            'post_type' => 'phpm_property',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC'
        ));
        
        // Get units for selected property
        $units = array();
        if (!empty($property_id)) {
            $units = get_posts(array(
                'post_type' => 'phpm_unit',
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'meta_query' => array(
                    array(
                        'key' => '_phpm_unit_property_id',
                        'value' => $property_id,
                        'compare' => '='
                    )
                ),
                'orderby' => 'title',
                'order' => 'ASC'
            ));
        }
        
        // Get tenants for dropdown
        $tenants = get_posts(array(
            'post_type' => 'phpm_tenant',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC'
        ));
        
        // Calculate lease term if both dates are set
        $lease_term = '';
        if (!empty($start_date) && !empty($end_date)) {
            $term_months = PHPM_Utilities::calculate_lease_term($start_date, $end_date);
            $lease_term = sprintf(_n('%d month', '%d months', $term_months, 'plughaus-property'), $term_months);
        }
        
        // Calculate days until expiration
        $days_until_expiry = '';
        if (!empty($end_date)) {
            $days = PHPM_Utilities::days_until_lease_expiration($end_date);
            if ($days > 0) {
                $days_until_expiry = sprintf(_n('%d day remaining', '%d days remaining', $days, 'plughaus-property'), $days);
            } elseif ($days < 0) {
                $days_until_expiry = sprintf(__('Expired %d days ago', 'plughaus-property'), abs($days));
            } else {
                $days_until_expiry = __('Expires today', 'plughaus-property');
            }
        }
        
        ?>
        <div class="phpm-meta-box">
            <table class="form-table">
                <tr>
                    <th><label for="phpm_lease_property_id"><?php _e('Property', 'plughaus-property'); ?> <span class="required">*</span></label></th>
                    <td>
                        <select id="phpm_lease_property_id" name="phpm_lease_property_id" class="regular-text" required>
                            <option value=""><?php _e('Select Property', 'plughaus-property'); ?></option>
                            <?php foreach ($properties as $property): ?>
                                <option value="<?php echo esc_attr($property->ID); ?>" <?php selected($property_id, $property->ID); ?>>
                                    <?php echo esc_html($property->post_title); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th><label for="phpm_lease_unit_id"><?php _e('Unit', 'plughaus-property'); ?></label></th>
                    <td>
                        <select id="phpm_lease_unit_id" name="phpm_lease_unit_id" class="regular-text">
                            <option value=""><?php _e('Select Unit', 'plughaus-property'); ?></option>
                            <?php foreach ($units as $unit): ?>
                                <option value="<?php echo esc_attr($unit->ID); ?>" <?php selected($unit_id, $unit->ID); ?>>
                                    <?php echo esc_html($unit->post_title); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description"><?php _e('Select property first to see available units', 'plughaus-property'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th><label for="phpm_lease_tenant_id"><?php _e('Tenant', 'plughaus-property'); ?> <span class="required">*</span></label></th>
                    <td>
                        <select id="phpm_lease_tenant_id" name="phpm_lease_tenant_id" class="regular-text" required>
                            <option value=""><?php _e('Select Tenant', 'plughaus-property'); ?></option>
                            <?php foreach ($tenants as $tenant): ?>
                                <option value="<?php echo esc_attr($tenant->ID); ?>" <?php selected($tenant_id, $tenant->ID); ?>>
                                    <?php echo esc_html($tenant->post_title); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th><label for="phpm_lease_status"><?php _e('Lease Status', 'plughaus-property'); ?></label></th>
                    <td>
                        <select id="phpm_lease_status" name="phpm_lease_status" class="regular-text">
                            <?php foreach (PHPM_Utilities::get_lease_statuses() as $key => $label): ?>
                                <option value="<?php echo esc_attr($key); ?>" <?php selected($status, $key); ?>>
                                    <?php echo esc_html($label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            </table>
            
            <h4><?php _e('Lease Term', 'plughaus-property'); ?></h4>
            <table class="form-table">
                <tr>
                    <th><label for="phpm_lease_start_date"><?php _e('Start Date', 'plughaus-property'); ?> <span class="required">*</span></label></th>
                    <td>
                        <input type="date" id="phpm_lease_start_date" name="phpm_lease_start_date" 
                               value="<?php echo esc_attr($start_date); ?>" class="regular-text" required />
                    </td>
                </tr>
                
                <tr>
                    <th><label for="phpm_lease_end_date"><?php _e('End Date', 'plughaus-property'); ?> <span class="required">*</span></label></th>
                    <td>
                        <input type="date" id="phpm_lease_end_date" name="phpm_lease_end_date" 
                               value="<?php echo esc_attr($end_date); ?>" class="regular-text" required />
                        <?php if (!empty($lease_term)): ?>
                            <p class="description"><?php printf(__('Lease term: %s', 'plughaus-property'), $lease_term); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($days_until_expiry)): ?>
                            <p class="description"><?php echo esc_html($days_until_expiry); ?></p>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
            
            <h4><?php _e('Financial Details', 'plughaus-property'); ?></h4>
            <table class="form-table">
                <tr>
                    <th><label for="phpm_lease_rent_amount"><?php _e('Monthly Rent', 'plughaus-property'); ?> <span class="required">*</span></label></th>
                    <td>
                        <input type="number" id="phpm_lease_rent_amount" name="phpm_lease_rent_amount" 
                               value="<?php echo esc_attr($rent_amount); ?>" class="regular-text" min="0" step="0.01" required 
                               placeholder="<?php _e('2500.00', 'plughaus-property'); ?>" />
                        <?php if (!empty($rent_amount)): ?>
                            <p class="description"><?php echo PHPM_Utilities::format_currency($rent_amount); ?> <?php _e('per month', 'plughaus-property'); ?></p>
                        <?php endif; ?>
                    </td>
                </tr>
                
                <tr>
                    <th><label for="phpm_lease_security_deposit"><?php _e('Security Deposit', 'plughaus-property'); ?></label></th>
                    <td>
                        <input type="number" id="phpm_lease_security_deposit" name="phpm_lease_security_deposit" 
                               value="<?php echo esc_attr($security_deposit); ?>" class="regular-text" min="0" step="0.01" 
                               placeholder="<?php _e('2500.00', 'plughaus-property'); ?>" />
                        <?php if (!empty($security_deposit)): ?>
                            <p class="description"><?php echo PHPM_Utilities::format_currency($security_deposit); ?></p>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
            
            <h4><?php _e('Additional Information', 'plughaus-property'); ?></h4>
            <table class="form-table">
                <tr>
                    <th><label for="phpm_lease_notes"><?php _e('Lease Notes', 'plughaus-property'); ?></label></th>
                    <td>
                        <textarea id="phpm_lease_notes" name="phpm_lease_notes" class="large-text" rows="4" 
                                  placeholder="<?php _e('Special terms, conditions, or notes about this lease...', 'plughaus-property'); ?>"><?php echo esc_textarea($notes); ?></textarea>
                    </td>
                </tr>
            </table>
            
            <?php if (!PHPM_Utilities::is_pro()): ?>
            <div class="phpm-pro-notice">
                <p>
                    <span class="dashicons dashicons-star-filled"></span>
                    <?php printf(
                        __('Generate lease documents, automate renewals, and track payments with %s.', 'plughaus-property'),
                        '<a href="https://plughausstudios.com/property-management-pro/" target="_blank">PlugHaus Property Management Pro</a>'
                    ); ?>
                </p>
            </div>
            <?php endif; ?>
        </div>
        <?php
    }
}