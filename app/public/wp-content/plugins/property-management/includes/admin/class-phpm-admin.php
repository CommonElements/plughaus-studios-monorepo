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
            PHPM_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            PHPM_VERSION
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
            PHPM_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery', 'wp-api'),
            PHPM_VERSION,
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
        
        $address = get_post_meta($post->ID, '_phpm_property_address', true);
        $city = get_post_meta($post->ID, '_phpm_property_city', true);
        $state = get_post_meta($post->ID, '_phpm_property_state', true);
        $zip = get_post_meta($post->ID, '_phpm_property_zip', true);
        $units = get_post_meta($post->ID, '_phpm_property_units', true);
        
        ?>
        <table class="form-table">
            <tr>
                <th><label for="phpm_property_address"><?php _e('Address', 'plughaus-property'); ?></label></th>
                <td><input type="text" id="phpm_property_address" name="phpm_property_address" value="<?php echo esc_attr($address); ?>" class="large-text" /></td>
            </tr>
            <tr>
                <th><label for="phpm_property_city"><?php _e('City', 'plughaus-property'); ?></label></th>
                <td><input type="text" id="phpm_property_city" name="phpm_property_city" value="<?php echo esc_attr($city); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="phpm_property_state"><?php _e('State', 'plughaus-property'); ?></label></th>
                <td><input type="text" id="phpm_property_state" name="phpm_property_state" value="<?php echo esc_attr($state); ?>" class="small-text" /></td>
            </tr>
            <tr>
                <th><label for="phpm_property_zip"><?php _e('ZIP Code', 'plughaus-property'); ?></label></th>
                <td><input type="text" id="phpm_property_zip" name="phpm_property_zip" value="<?php echo esc_attr($zip); ?>" class="small-text" /></td>
            </tr>
            <tr>
                <th><label for="phpm_property_units"><?php _e('Number of Units', 'plughaus-property'); ?></label></th>
                <td><input type="number" id="phpm_property_units" name="phpm_property_units" value="<?php echo esc_attr($units); ?>" class="small-text" min="1" /></td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Save meta data
     */
    public function save_meta_data($post_id) {
        // Check if our nonce is set
        if (!isset($_POST['phpm_property_nonce'])) {
            return;
        }
        
        // Verify nonce
        if (!wp_verify_nonce($_POST['phpm_property_nonce'], 'phpm_save_property_details')) {
            return;
        }
        
        // Check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Check permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Save property details
        if ('phpm_property' === get_post_type($post_id)) {
            update_post_meta($post_id, '_phpm_property_address', sanitize_text_field($_POST['phpm_property_address']));
            update_post_meta($post_id, '_phpm_property_city', sanitize_text_field($_POST['phpm_property_city']));
            update_post_meta($post_id, '_phpm_property_state', sanitize_text_field($_POST['phpm_property_state']));
            update_post_meta($post_id, '_phpm_property_zip', sanitize_text_field($_POST['phpm_property_zip']));
            update_post_meta($post_id, '_phpm_property_units', intval($_POST['phpm_property_units']));
        }
    }
    
    // Additional meta box render methods would go here...
    public function render_unit_details_meta_box($post) {
        // Unit details form
    }
    
    public function render_tenant_details_meta_box($post) {
        // Tenant details form
    }
    
    public function render_lease_details_meta_box($post) {
        // Lease details form
    }
}