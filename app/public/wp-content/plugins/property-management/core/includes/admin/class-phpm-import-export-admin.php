<?php
/**
 * Import/Export Admin Interface for PlugHaus Property Management
 */

if (!defined('ABSPATH')) {
    exit;
}

class PHPM_Import_Export_Admin {
    
    /**
     * Initialize admin hooks
     */
    public static function init() {
        add_action('admin_menu', array(__CLASS__, 'add_admin_menu'), 20);
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_scripts'));
    }
    
    /**
     * Add admin menu item
     */
    public static function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=phpm_property',
            __('Import/Export', 'plughaus-property'),
            __('Import/Export', 'plughaus-property'),
            'manage_options',
            'phpm-import-export',
            array(__CLASS__, 'admin_page')
        );
    }
    
    /**
     * Enqueue admin scripts
     */
    public static function enqueue_scripts($hook) {
        if ('phpm_property_page_phpm-import-export' !== $hook) {
            return;
        }
        
        wp_enqueue_script(
            'phpm-import-export-admin',
            PM_PLUGIN_URL . 'core/assets/js/import-export-admin.js',
            array('jquery'),
            PM_VERSION,
            true
        );
        
        wp_localize_script('phpm-import-export-admin', 'phpmImportExport', array(
            'nonce' => wp_create_nonce('phpm_admin_nonce'),
            'ajax_url' => admin_url('admin-ajax.php'),
            'strings' => array(
                'exporting' => __('Exporting data...', 'plughaus-property'),
                'importing' => __('Importing data...', 'plughaus-property'),
                'downloading' => __('Downloading template...', 'plughaus-property'),
                'select_file' => __('Please select a file to import.', 'plughaus-property'),
                'confirm_import' => __('Are you sure you want to import this data? This action cannot be undone.', 'plughaus-property'),
                'import_success' => __('Import completed successfully!', 'plughaus-property'),
                'export_success' => __('Export completed successfully!', 'plughaus-property'),
                'error' => __('An error occurred. Please try again.', 'plughaus-property')
            )
        ));
        
        wp_enqueue_style(
            'phpm-import-export-admin',
            PM_PLUGIN_URL . 'core/assets/css/import-export-admin.css',
            array(),
            PM_VERSION
        );
    }
    
    /**
     * Display admin page
     */
    public static function admin_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Import/Export Data', 'plughaus-property'); ?></h1>
            
            <div class="phpm-import-export-container">
                
                <!-- Export Section -->
                <div class="card phpm-export-section">
                    <h2><span class="dashicons dashicons-download"></span> <?php _e('Export Data', 'plughaus-property'); ?></h2>
                    <p><?php _e('Export your property management data to CSV or JSON format for backup or migration purposes.', 'plughaus-property'); ?></p>
                    
                    <form id="export-form" class="phpm-export-form">
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="export-data-type"><?php _e('Data Type', 'plughaus-property'); ?></label>
                                </th>
                                <td>
                                    <select id="export-data-type" name="data_type" required>
                                        <option value=""><?php _e('Select data type', 'plughaus-property'); ?></option>
                                        <option value="properties"><?php _e('Properties', 'plughaus-property'); ?></option>
                                        <option value="units"><?php _e('Units', 'plughaus-property'); ?></option>
                                        <option value="tenants"><?php _e('Tenants', 'plughaus-property'); ?></option>
                                        <option value="leases"><?php _e('Leases', 'plughaus-property'); ?></option>
                                        <option value="maintenance"><?php _e('Maintenance Requests', 'plughaus-property'); ?></option>
                                        <option value="all"><?php _e('All Data (Complete Backup)', 'plughaus-property'); ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="export-format"><?php _e('Format', 'plughaus-property'); ?></label>
                                </th>
                                <td>
                                    <select id="export-format" name="format" required>
                                        <option value="csv"><?php _e('CSV (Comma Separated Values)', 'plughaus-property'); ?></option>
                                        <option value="json"><?php _e('JSON (JavaScript Object Notation)', 'plughaus-property'); ?></option>
                                    </select>
                                    <p class="description">
                                        <?php _e('CSV is recommended for spreadsheet applications. JSON preserves all data relationships.', 'plughaus-property'); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr class="export-filters-row" style="display: none;">
                                <th scope="row">
                                    <label><?php _e('Filters', 'plughaus-property'); ?></label>
                                </th>
                                <td>
                                    <div class="export-filters">
                                        <label>
                                            <?php _e('Date From:', 'plughaus-property'); ?>
                                            <input type="date" name="date_from" class="filter-input">
                                        </label>
                                        <label>
                                            <?php _e('Date To:', 'plughaus-property'); ?>
                                            <input type="date" name="date_to" class="filter-input">
                                        </label>
                                        <div class="status-filters" style="display: none;">
                                            <label>
                                                <?php _e('Status:', 'plughaus-property'); ?>
                                                <select name="status" class="filter-input">
                                                    <option value=""><?php _e('All Statuses', 'plughaus-property'); ?></option>
                                                    <option value="active"><?php _e('Active', 'plughaus-property'); ?></option>
                                                    <option value="inactive"><?php _e('Inactive', 'plughaus-property'); ?></option>
                                                </select>
                                            </label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        
                        <p class="submit">
                            <button type="submit" class="button button-primary button-large">
                                <span class="dashicons dashicons-download"></span>
                                <?php _e('Export Data', 'plughaus-property'); ?>
                            </button>
                        </p>
                    </form>
                </div>
                
                <!-- Import Section -->
                <div class="card phpm-import-section">
                    <h2><span class="dashicons dashicons-upload"></span> <?php _e('Import Data', 'plughaus-property'); ?></h2>
                    <p><?php _e('Import property management data from CSV or JSON files. Use templates for proper formatting.', 'plughaus-property'); ?></p>
                    
                    <div class="import-templates">
                        <h3><?php _e('Download Templates', 'plughaus-property'); ?></h3>
                        <p><?php _e('Download CSV templates with the correct column headers for importing data.', 'plughaus-property'); ?></p>
                        
                        <div class="template-buttons">
                            <button type="button" class="button download-template" data-type="properties">
                                <span class="dashicons dashicons-building"></span>
                                <?php _e('Properties Template', 'plughaus-property'); ?>
                            </button>
                            <button type="button" class="button download-template" data-type="units">
                                <span class="dashicons dashicons-admin-home"></span>
                                <?php _e('Units Template', 'plughaus-property'); ?>
                            </button>
                            <button type="button" class="button download-template" data-type="tenants">
                                <span class="dashicons dashicons-groups"></span>
                                <?php _e('Tenants Template', 'plughaus-property'); ?>
                            </button>
                            <button type="button" class="button download-template" data-type="leases">
                                <span class="dashicons dashicons-media-document"></span>
                                <?php _e('Leases Template', 'plughaus-property'); ?>
                            </button>
                            <button type="button" class="button download-template" data-type="maintenance">
                                <span class="dashicons dashicons-admin-tools"></span>
                                <?php _e('Maintenance Template', 'plughaus-property'); ?>
                            </button>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <form id="import-form" class="phpm-import-form" enctype="multipart/form-data">
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="import-data-type"><?php _e('Data Type', 'plughaus-property'); ?></label>
                                </th>
                                <td>
                                    <select id="import-data-type" name="data_type" required>
                                        <option value=""><?php _e('Select data type', 'plughaus-property'); ?></option>
                                        <option value="properties"><?php _e('Properties', 'plughaus-property'); ?></option>
                                        <option value="units"><?php _e('Units', 'plughaus-property'); ?></option>
                                        <option value="tenants"><?php _e('Tenants', 'plughaus-property'); ?></option>
                                        <option value="leases"><?php _e('Leases', 'plughaus-property'); ?></option>
                                        <option value="maintenance"><?php _e('Maintenance Requests', 'plughaus-property'); ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="import-file"><?php _e('Import File', 'plughaus-property'); ?></label>
                                </th>
                                <td>
                                    <input type="file" id="import-file" name="import_file" accept=".csv,.json" required>
                                    <p class="description">
                                        <?php _e('Accepted formats: CSV (.csv) and JSON (.json). Maximum file size: 10MB.', 'plughaus-property'); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label><?php _e('Import Options', 'plughaus-property'); ?></label>
                                </th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="update_existing" value="true">
                                        <?php _e('Update existing records', 'plughaus-property'); ?>
                                    </label>
                                    <p class="description">
                                        <?php _e('If checked, existing records will be updated. Otherwise, duplicates will be skipped.', 'plughaus-property'); ?>
                                    </p>
                                </td>
                            </tr>
                        </table>
                        
                        <p class="submit">
                            <button type="submit" class="button button-primary button-large">
                                <span class="dashicons dashicons-upload"></span>
                                <?php _e('Import Data', 'plughaus-property'); ?>
                            </button>
                        </p>
                    </form>
                </div>
                
                <!-- Import Guidelines -->
                <div class="card phpm-guidelines">
                    <h2><?php _e('Import Guidelines', 'plughaus-property'); ?></h2>
                    
                    <div class="guidelines-grid">
                        <div class="guideline-item">
                            <h3><span class="dashicons dashicons-info"></span> <?php _e('Data Order', 'plughaus-property'); ?></h3>
                            <p><?php _e('Import data in this order: Properties → Units → Tenants → Leases → Maintenance', 'plughaus-property'); ?></p>
                        </div>
                        
                        <div class="guideline-item">
                            <h3><span class="dashicons dashicons-warning"></span> <?php _e('Required Fields', 'plughaus-property'); ?></h3>
                            <p><?php _e('Ensure all required fields are filled. Check templates for field requirements.', 'plughaus-property'); ?></p>
                        </div>
                        
                        <div class="guideline-item">
                            <h3><span class="dashicons dashicons-admin-links"></span> <?php _e('Relationships', 'plughaus-property'); ?></h3>
                            <p><?php _e('Units must reference existing properties. Leases must reference existing units and tenants.', 'plughaus-property'); ?></p>
                        </div>
                        
                        <div class="guideline-item">
                            <h3><span class="dashicons dashicons-backup"></span> <?php _e('Backup First', 'plughaus-property'); ?></h3>
                            <p><?php _e('Always export your existing data before importing new data as a backup.', 'plughaus-property'); ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- CSV Format Examples -->
                <div class="card phpm-examples">
                    <h2><?php _e('CSV Format Examples', 'plughaus-property'); ?></h2>
                    
                    <div class="format-examples">
                        <div class="example-section">
                            <h3><?php _e('Properties CSV Example', 'plughaus-property'); ?></h3>
                            <code class="csv-example">
Name,Description,Address,City,State,ZIP,Units,Property Type<br>
"Sunset Apartments","Modern apartment complex","123 Main St","Los Angeles","CA","90028",12,"apartment"<br>
"Downtown Lofts","Historic building","456 1st Ave","Portland","OR","97205",8,"loft"
                            </code>
                        </div>
                        
                        <div class="example-section">
                            <h3><?php _e('Units CSV Example', 'plughaus-property'); ?></h3>
                            <code class="csv-example">
Unit Number,Property Name,Bedrooms,Bathrooms,Square Feet,Rent Amount,Status<br>
"101","Sunset Apartments",1,1,650,1500,"available"<br>
"102","Sunset Apartments",2,1,900,2000,"occupied"
                            </code>
                        </div>
                        
                        <div class="example-section">
                            <h3><?php _e('Date Format', 'plughaus-property'); ?></h3>
                            <p><?php _e('Use YYYY-MM-DD format for all dates (e.g., 2024-01-15).', 'plughaus-property'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Progress and Results -->
            <div id="import-export-progress" class="notice notice-info" style="display: none;">
                <p>
                    <span class="spinner is-active" style="float: none; margin: 0 5px 0 0;"></span>
                    <span id="progress-message"><?php _e('Processing...', 'plughaus-property'); ?></span>
                </p>
            </div>
            
            <div id="import-export-results" class="phpm-results" style="display: none;">
                <div class="card">
                    <h2 id="results-title"><?php _e('Results', 'plughaus-property'); ?></h2>
                    <div id="results-content"></div>
                </div>
            </div>
        </div>
        <?php
    }
}

// Initialize import/export admin
PHPM_Import_Export_Admin::init();