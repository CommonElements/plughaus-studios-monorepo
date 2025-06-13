<?php
/**
 * Sample Data Admin Page for PlugHaus Property Management
 */

if (!defined('ABSPATH')) {
    exit;
}

class PHPM_Sample_Data_Admin {
    
    /**
     * Initialize admin hooks
     */
    public static function init() {
        add_action('admin_menu', array(__CLASS__, 'add_admin_menu'), 15);
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_scripts'));
        add_action('admin_notices', array(__CLASS__, 'show_admin_notices'));
        add_action('admin_bar_menu', array(__CLASS__, 'add_admin_bar_menu'), 100);
        add_action('wp_dashboard_setup', array(__CLASS__, 'add_dashboard_widget'));
    }
    
    /**
     * Add admin menu item
     */
    public static function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=phpm_property',
            __('Sample Data', 'plughaus-property'),
            __('Sample Data', 'plughaus-property'),
            'manage_options',
            'phpm-sample-data',
            array(__CLASS__, 'admin_page')
        );
    }
    
    /**
     * Enqueue admin scripts
     */
    public static function enqueue_scripts($hook) {
        if ('phpm_property_page_phpm-sample-data' !== $hook) {
            return;
        }
        
        wp_enqueue_script(
            'phpm-sample-data-admin',
            PHPM_PLUGIN_URL . 'core/assets/js/sample-data-admin.js',
            array('jquery'),
            PHPM_VERSION,
            true
        );
        
        wp_localize_script('phpm-sample-data-admin', 'phpmSampleData', array(
            'nonce' => wp_create_nonce('phpm_admin_nonce'),
            'ajax_url' => admin_url('admin-ajax.php'),
            'strings' => array(
                'installing' => __('Installing sample data...', 'plughaus-property'),
                'removing' => __('Removing sample data...', 'plughaus-property'),
                'confirm_remove' => __('Are you sure you want to remove all sample data? This action cannot be undone.', 'plughaus-property'),
                'error' => __('An error occurred. Please try again.', 'plughaus-property')
            )
        ));
        
        wp_enqueue_style(
            'phpm-sample-data-admin',
            PHPM_PLUGIN_URL . 'core/assets/css/sample-data-admin.css',
            array(),
            PHPM_VERSION
        );
    }
    
    /**
     * Display admin page
     */
    public static function admin_page() {
        $sample_data_exists = PHPM_Sample_Data::sample_data_exists();
        $install_timestamp = get_option('phpm_sample_data_timestamp', null);
        
        ?>
        <div class="wrap">
            <h1><?php _e('Sample Data Management', 'plughaus-property'); ?></h1>
            
            <div class="phpm-sample-data-container">
                
                <?php if (!$sample_data_exists): ?>
                    <div class="card">
                        <h2><?php _e('Install Sample Data', 'plughaus-property'); ?></h2>
                        <p><?php _e('Get started quickly by installing sample data that demonstrates all plugin features. This includes properties, units, tenants, leases, and maintenance requests.', 'plughaus-property'); ?></p>
                        
                        <div class="phpm-sample-preview">
                            <h3><?php _e('What will be created:', 'plughaus-property'); ?></h3>
                            <ul>
                                <li><strong>4 Properties</strong> - Different types (apartments, lofts, townhomes, condos)</li>
                                <li><strong>41 Units</strong> - Various bedroom/bathroom configurations</li>
                                <li><strong>10 Tenants</strong> - With realistic contact information</li>
                                <li><strong>~30 Leases</strong> - Linking tenants to occupied units</li>
                                <li><strong>6 Maintenance Requests</strong> - Different priorities and statuses</li>
                            </ul>
                            
                            <p class="description">
                                <?php _e('Sample data is clearly marked and can be removed at any time without affecting your real data.', 'plughaus-property'); ?>
                            </p>
                        </div>
                        
                        <p>
                            <button type="button" class="button button-primary button-large" id="install-sample-data">
                                <span class="dashicons dashicons-download"></span>
                                <?php _e('Install Sample Data', 'plughaus-property'); ?>
                            </button>
                        </p>
                    </div>
                    
                <?php else: ?>
                    <div class="card">
                        <h2><?php _e('Sample Data Installed', 'plughaus-property'); ?></h2>
                        
                        <?php if ($install_timestamp): ?>
                            <p>
                                <?php 
                                printf(
                                    __('Sample data was installed on %s.', 'plughaus-property'), 
                                    '<strong>' . date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $install_timestamp) . '</strong>'
                                ); 
                                ?>
                            </p>
                        <?php endif; ?>
                        
                        <p><?php _e('Sample data is currently installed in your system. You can explore the features or remove the sample data when you\'re ready to add your own.', 'plughaus-property'); ?></p>
                        
                        <div class="phpm-sample-actions">
                            <p>
                                <a href="<?php echo admin_url('edit.php?post_type=phpm_property'); ?>" class="button button-secondary">
                                    <span class="dashicons dashicons-building"></span>
                                    <?php _e('View Properties', 'plughaus-property'); ?>
                                </a>
                                
                                <a href="<?php echo admin_url('edit.php?post_type=phpm_unit'); ?>" class="button button-secondary">
                                    <span class="dashicons dashicons-admin-home"></span>
                                    <?php _e('View Units', 'plughaus-property'); ?>
                                </a>
                                
                                <a href="<?php echo admin_url('edit.php?post_type=phpm_tenant'); ?>" class="button button-secondary">
                                    <span class="dashicons dashicons-groups"></span>
                                    <?php _e('View Tenants', 'plughaus-property'); ?>
                                </a>
                                
                                <a href="<?php echo admin_url('edit.php?post_type=phpm_lease'); ?>" class="button button-secondary">
                                    <span class="dashicons dashicons-media-document"></span>
                                    <?php _e('View Leases', 'plughaus-property'); ?>
                                </a>
                            </p>
                            
                            <hr>
                            
                            <div class="remove-sample-data-section">
                                <h3><?php _e('Ready to Use Your Own Data?', 'plughaus-property'); ?></h3>
                                <p><?php _e('When you\'re ready to start with your own data, you can safely remove all sample data. This will only remove items marked as sample data - your real data will not be affected.', 'plughaus-property'); ?></p>
                                
                                <div class="removal-options">
                                    <button type="button" class="button button-primary button-large" id="remove-sample-data" style="background: #d63638; border-color: #d63638;">
                                        <span class="dashicons dashicons-trash"></span>
                                        <?php _e('Remove All Sample Data', 'plughaus-property'); ?>
                                    </button>
                                    
                                    <p class="description" style="margin-top: 10px;">
                                        <strong><?php _e('Tip:', 'plughaus-property'); ?></strong> 
                                        <?php _e('You can also export the sample data first to use as templates for your own data.', 'plughaus-property'); ?>
                                        <a href="<?php echo admin_url('edit.php?post_type=phpm_property&page=phpm-import-export'); ?>" class="button button-small button-secondary" style="margin-left: 10px;">
                                            <span class="dashicons dashicons-download"></span>
                                            <?php _e('Export Sample Data', 'plughaus-property'); ?>
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="card">
                    <h2><?php _e('Sample Data Features', 'plughaus-property'); ?></h2>
                    <p><?php _e('The sample data demonstrates these key plugin features:', 'plughaus-property'); ?></p>
                    
                    <div class="phpm-features-grid">
                        <div class="feature-item">
                            <h4><span class="dashicons dashicons-building"></span> <?php _e('Property Management', 'plughaus-property'); ?></h4>
                            <p><?php _e('Multiple property types with addresses, amenities, and unit counts.', 'plughaus-property'); ?></p>
                        </div>
                        
                        <div class="feature-item">
                            <h4><span class="dashicons dashicons-admin-home"></span> <?php _e('Unit Tracking', 'plughaus-property'); ?></h4>
                            <p><?php _e('Individual units with bedroom/bathroom counts, square footage, and rent amounts.', 'plughaus-property'); ?></p>
                        </div>
                        
                        <div class="feature-item">
                            <h4><span class="dashicons dashicons-groups"></span> <?php _e('Tenant Records', 'plughaus-property'); ?></h4>
                            <p><?php _e('Tenant profiles with contact information and emergency contacts.', 'plughaus-property'); ?></p>
                        </div>
                        
                        <div class="feature-item">
                            <h4><span class="dashicons dashicons-media-document"></span> <?php _e('Lease Management', 'plughaus-property'); ?></h4>
                            <p><?php _e('Active leases linking tenants to units with dates and rent amounts.', 'plughaus-property'); ?></p>
                        </div>
                        
                        <div class="feature-item">
                            <h4><span class="dashicons dashicons-admin-tools"></span> <?php _e('Maintenance Tracking', 'plughaus-property'); ?></h4>
                            <p><?php _e('Maintenance requests with different priorities, categories, and statuses.', 'plughaus-property'); ?></p>
                        </div>
                        
                        <div class="feature-item">
                            <h4><span class="dashicons dashicons-chart-bar"></span> <?php _e('Reporting Data', 'plughaus-property'); ?></h4>
                            <p><?php _e('Realistic data for testing occupancy rates and financial reports.', 'plughaus-property'); ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <h2><?php _e('Tips for Using Sample Data', 'plughaus-property'); ?></h2>
                    <ul>
                        <li><?php _e('<strong>Explore the Admin:</strong> Visit each section (Properties, Units, Tenants, Leases) to see how the plugin works.', 'plughaus-property'); ?></li>
                        <li><?php _e('<strong>Test Features:</strong> Try creating new records, editing existing ones, and using the search functionality.', 'plughaus-property'); ?></li>
                        <li><?php _e('<strong>Check Relationships:</strong> Notice how units are linked to properties, and leases link tenants to units.', 'plughaus-property'); ?></li>
                        <li><?php _e('<strong>View Reports:</strong> Check the dashboard and reports to see occupancy and financial data.', 'plughaus-property'); ?></li>
                        <li><?php _e('<strong>Try the Frontend:</strong> If you have tenant portal pages set up, test the tenant-facing features.', 'plughaus-property'); ?></li>
                    </ul>
                </div>
            </div>
            
            <div id="sample-data-loading" class="notice notice-info" style="display: none;">
                <p>
                    <span class="spinner is-active" style="float: none; margin: 0 5px 0 0;"></span>
                    <span id="loading-message"><?php _e('Processing...', 'plughaus-property'); ?></span>
                </p>
            </div>
        </div>
        <?php
    }
    
    /**
     * Show admin notices
     */
    public static function show_admin_notices() {
        $screen = get_current_screen();
        
        // Show sample data installation notice on main admin pages
        if (!PHPM_Sample_Data::sample_data_exists() && 
            $screen && 
            strpos($screen->id, 'phpm_') !== false &&
            get_transient('phpm_sample_data_notice_dismissed') === false) {
            
            ?>
            <div class="notice notice-info is-dismissible" data-notice="phpm-sample-data">
                <p>
                    <strong><?php _e('PlugHaus Property Management', 'plughaus-property'); ?></strong> - 
                    <?php _e('Want to see how the plugin works?', 'plughaus-property'); ?>
                    <a href="<?php echo admin_url('edit.php?post_type=phpm_property&page=phpm-sample-data'); ?>" class="button button-small">
                        <?php _e('Install Sample Data', 'plughaus-property'); ?>
                    </a>
                </p>
            </div>
            
            <script>
            jQuery(document).ready(function($) {
                $(document).on('click', '.notice[data-notice="phpm-sample-data"] .notice-dismiss', function() {
                    $.post(ajaxurl, {
                        action: 'phpm_dismiss_sample_data_notice',
                        nonce: '<?php echo wp_create_nonce("phpm_admin_nonce"); ?>'
                    });
                });
            });
            </script>
            <?php
        }
        
        // Show sample data removal notice on list pages when sample data exists
        if (PHPM_Sample_Data::sample_data_exists() && 
            $screen && 
            in_array($screen->id, array('edit-phpm_property', 'edit-phpm_unit', 'edit-phpm_tenant', 'edit-phpm_lease', 'edit-phpm_maintenance')) &&
            get_transient('phpm_sample_removal_notice_dismissed') === false) {
            
            ?>
            <div class="notice notice-warning is-dismissible" data-notice="phpm-sample-removal" style="border-left-color: #ff9800;">
                <p>
                    <span class="dashicons dashicons-database" style="color: #ff9800; margin-right: 5px;"></span>
                    <strong><?php _e('Sample Data Active', 'plughaus-property'); ?></strong> - 
                    <?php _e('You\'re viewing sample data. Ready to use your own?', 'plughaus-property'); ?>
                    <a href="<?php echo admin_url('edit.php?post_type=phpm_property&page=phpm-sample-data'); ?>" class="button button-small">
                        <?php _e('Manage Sample Data', 'plughaus-property'); ?>
                    </a>
                    <button type="button" class="button button-small button-link-delete" onclick="phpmQuickRemoveSampleData()" style="color: #d63638; margin-left: 5px;">
                        <?php _e('Remove Sample Data', 'plughaus-property'); ?>
                    </button>
                </p>
            </div>
            
            <script>
            jQuery(document).ready(function($) {
                $(document).on('click', '.notice[data-notice="phpm-sample-removal"] .notice-dismiss', function() {
                    $.post(ajaxurl, {
                        action: 'phpm_dismiss_sample_removal_notice',
                        nonce: '<?php echo wp_create_nonce("phpm_admin_nonce"); ?>'
                    });
                });
            });
            
            // Global function for quick removal
            window.phpmQuickRemoveSampleData = function() {
                if (!confirm('<?php echo esc_js(__('Are you sure you want to remove all sample data? This action cannot be undone.', 'plughaus-property')); ?>')) {
                    return;
                }
                
                var button = event.target;
                var originalText = button.innerHTML;
                button.disabled = true;
                button.innerHTML = '<?php echo esc_js(__('Removing...', 'plughaus-property')); ?>';
                
                jQuery.post(ajaxurl, {
                    action: 'phpm_remove_sample_data',
                    nonce: '<?php echo wp_create_nonce("phpm_admin_nonce"); ?>'
                }, function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('<?php echo esc_js(__('Error removing sample data. Please try again.', 'plughaus-property')); ?>');
                        button.disabled = false;
                        button.innerHTML = originalText;
                    }
                }).fail(function() {
                    alert('<?php echo esc_js(__('Error removing sample data. Please try again.', 'plughaus-property')); ?>');
                    button.disabled = false;
                    button.innerHTML = originalText;
                });
            };
            </script>
            <?php
        }
    }
    
    /**
     * Add admin bar menu for quick sample data removal
     */
    public static function add_admin_bar_menu($wp_admin_bar) {
        if (!current_user_can('manage_options') || !PHPM_Sample_Data::sample_data_exists()) {
            return;
        }
        
        $wp_admin_bar->add_node(array(
            'id' => 'phpm-sample-data',
            'title' => '<span class="dashicons dashicons-database" style="margin-right: 5px;"></span>' . __('Sample Data Active', 'plughaus-property'),
            'href' => admin_url('edit.php?post_type=phpm_property&page=phpm-sample-data'),
            'meta' => array(
                'title' => __('Click to manage sample data', 'plughaus-property')
            )
        ));
        
        $wp_admin_bar->add_node(array(
            'parent' => 'phpm-sample-data',
            'id' => 'phpm-sample-data-manage',
            'title' => __('Manage Sample Data', 'plughaus-property'),
            'href' => admin_url('edit.php?post_type=phpm_property&page=phpm-sample-data')
        ));
        
        $wp_admin_bar->add_node(array(
            'parent' => 'phpm-sample-data',
            'id' => 'phpm-sample-data-remove-quick',
            'title' => '<span style="color: #dc3545;">' . __('Remove Sample Data', 'plughaus-property') . '</span>',
            'href' => '#',
            'meta' => array(
                'onclick' => 'if(confirm("' . esc_js(__('Are you sure you want to remove all sample data? This action cannot be undone.', 'plughaus-property')) . '")) { phpmQuickRemoveSampleData(); } return false;'
            )
        ));
    }
    
    /**
     * Add dashboard widget for sample data management
     */
    public static function add_dashboard_widget() {
        if (!current_user_can('manage_options') || !PHPM_Sample_Data::sample_data_exists()) {
            return;
        }
        
        wp_add_dashboard_widget(
            'phpm_sample_data_widget',
            __('PlugHaus Property Management - Sample Data', 'plughaus-property'),
            array(__CLASS__, 'dashboard_widget_content')
        );
    }
    
    /**
     * Dashboard widget content
     */
    public static function dashboard_widget_content() {
        $install_timestamp = get_option('phpm_sample_data_timestamp', null);
        $sample_data_ids = get_option('phpm_sample_data_ids', array());
        
        // Count sample data items
        $counts = array();
        foreach ($sample_data_ids as $type => $ids) {
            if (is_array($ids)) {
                $counts[$type] = count($ids);
            }
        }
        
        ?>
        <div class="phpm-dashboard-widget">
            <p>
                <strong><?php _e('Sample data is currently active', 'plughaus-property'); ?></strong>
                <?php if ($install_timestamp): ?>
                    <br><small><?php printf(__('Installed: %s', 'plughaus-property'), date_i18n(get_option('date_format'), $install_timestamp)); ?></small>
                <?php endif; ?>
            </p>
            
            <?php if (!empty($counts)): ?>
                <div class="sample-data-summary">
                    <ul style="margin: 10px 0; list-style: none; padding: 0;">
                        <?php foreach ($counts as $type => $count): ?>
                            <li style="margin: 2px 0;">
                                <span class="dashicons dashicons-yes-alt" style="color: #00a32a; font-size: 14px;"></span>
                                <?php printf(__('%d %s', 'plughaus-property'), $count, ucfirst($type)); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <div class="widget-actions" style="margin-top: 15px;">
                <a href="<?php echo admin_url('edit.php?post_type=phpm_property'); ?>" class="button button-small">
                    <?php _e('View Properties', 'plughaus-property'); ?>
                </a>
                <a href="<?php echo admin_url('edit.php?post_type=phpm_property&page=phpm-sample-data'); ?>" class="button button-small">
                    <?php _e('Manage Sample Data', 'plughaus-property'); ?>
                </a>
            </div>
            
            <div class="quick-remove-section" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e2e4e7;">
                <p style="margin: 0 0 10px 0; color: #646970; font-size: 12px;">
                    <?php _e('Ready to use your own data?', 'plughaus-property'); ?>
                </p>
                <button type="button" class="button button-link-delete button-small" onclick="phpmQuickRemoveSampleData()" style="color: #d63638;">
                    <span class="dashicons dashicons-trash" style="font-size: 14px; margin-right: 3px;"></span>
                    <?php _e('Remove Sample Data', 'plughaus-property'); ?>
                </button>
            </div>
        </div>
        
        <script>
        function phpmQuickRemoveSampleData() {
            if (!confirm('<?php echo esc_js(__('Are you sure you want to remove all sample data? This action cannot be undone.', 'plughaus-property')); ?>')) {
                return;
            }
            
            var button = document.querySelector('.quick-remove-section button');
            if (button) {
                button.disabled = true;
                button.innerHTML = '<span class="spinner is-active" style="float: none; margin: 0 5px 0 0;"></span><?php echo esc_js(__('Removing...', 'plughaus-property')); ?>';
            }
            
            jQuery.post(ajaxurl, {
                action: 'phpm_remove_sample_data',
                nonce: '<?php echo wp_create_nonce("phpm_admin_nonce"); ?>'
            }, function(response) {
                if (response.success) {
                    // Show success message and reload
                    alert('<?php echo esc_js(__('Sample data removed successfully!', 'plughaus-property')); ?>');
                    location.reload();
                } else {
                    alert('<?php echo esc_js(__('Error removing sample data. Please try again.', 'plughaus-property')); ?>');
                    if (button) {
                        button.disabled = false;
                        button.innerHTML = '<span class="dashicons dashicons-trash"></span><?php echo esc_js(__('Remove Sample Data', 'plughaus-property')); ?>';
                    }
                }
            }).fail(function() {
                alert('<?php echo esc_js(__('Error removing sample data. Please try again.', 'plughaus-property')); ?>');
                if (button) {
                    button.disabled = false;
                    button.innerHTML = '<span class="dashicons dashicons-trash"></span><?php echo esc_js(__('Remove Sample Data', 'plughaus-property')); ?>';
                }
            });
        }
        </script>
        <?php
    }
}

// Initialize sample data admin
PHPM_Sample_Data_Admin::init();