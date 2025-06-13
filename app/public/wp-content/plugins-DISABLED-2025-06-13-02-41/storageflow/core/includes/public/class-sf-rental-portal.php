<?php
/**
 * StorageFlow Rental Portal - Public rental application interface
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class SF_Rental_Portal {
    
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_portal_scripts'));
        add_shortcode('storage_rental_form', array($this, 'render_rental_form'));
        add_shortcode('storage_unit_sizes', array($this, 'render_unit_sizes'));
    }
    
    public function enqueue_portal_scripts() {
        if (is_page() || has_shortcode(get_post()->post_content ?? '', 'storage_rental_form')) {
            wp_enqueue_script('jquery-ui-datepicker');
            wp_enqueue_style('jquery-ui-datepicker-style', 'https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css');
            
            wp_enqueue_script(
                'storageflow-rental',
                SF_PLUGIN_URL . 'core/assets/js/rental-portal.js',
                array('jquery', 'jquery-ui-datepicker'),
                SF_VERSION,
                true
            );
            
            wp_localize_script('storageflow-rental', 'storageflow_rental', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('sf_public_nonce'),
                'messages' => array(
                    'checking_availability' => __('Checking unit availability...', 'storageflow'),
                    'no_units_available' => __('No units available for the selected criteria.', 'storageflow'),
                    'submitting_application' => __('Submitting your application...', 'storageflow'),
                    'application_success' => __('Application submitted successfully!', 'storageflow'),
                    'application_error' => __('There was an error submitting your application. Please try again.', 'storageflow'),
                    'required_field' => __('This field is required.', 'storageflow'),
                    'invalid_email' => __('Please enter a valid email address.', 'storageflow'),
                    'invalid_phone' => __('Please enter a valid phone number.', 'storageflow'),
                    'invalid_date' => __('Please select a valid move-in date.', 'storageflow')
                )
            ));
            
            wp_enqueue_style(
                'storageflow-rental-portal',
                SF_PLUGIN_URL . 'core/assets/css/rental-portal.css',
                array(),
                SF_VERSION
            );
        }
    }
    
    /**
     * Render rental form shortcode
     */
    public function render_rental_form($atts) {
        $atts = shortcode_atts(array(
            'style' => 'default',
            'show_rates' => 'true',
            'show_unit_grid' => 'true',
            'title' => __('Rent Your Storage Unit', 'storageflow')
        ), $atts);
        
        $unit_sizes = SF_Unit_Manager::get_units_by_size(false);
        
        ob_start();
        ?>
        <div class="storageflow-rental-container" data-style="<?php echo esc_attr($atts['style']); ?>">
            <div class="sf-rental-header">
                <h2><?php echo esc_html($atts['title']); ?></h2>
                <p class="sf-rental-subtitle"><?php _e('Find the perfect storage unit for your needs. Complete the application below and we\'ll have you moving in within 24 hours.', 'storageflow'); ?></p>
            </div>
            
            <?php if ($atts['show_unit_grid'] === 'true') : ?>
            <div class="sf-unit-sizes-display">
                <h3><?php _e('Available Unit Sizes', 'storageflow'); ?></h3>
                <div class="sf-unit-sizes-grid">
                    <?php echo $this->render_unit_size_cards($unit_sizes); ?>
                </div>
            </div>
            <?php endif; ?>
            
            <form id="storageflow-rental-form" class="sf-rental-form" novalidate>
                <?php wp_nonce_field('sf_public_nonce', 'sf_rental_nonce'); ?>
                
                <div class="sf-form-section sf-unit-selection">
                    <h3><?php _e('Unit Selection', 'storageflow'); ?></h3>
                    
                    <div class="sf-form-row">
                        <div class="sf-form-field sf-field-half">
                            <label for="sf_unit_size"><?php _e('Unit Size', 'storageflow'); ?> <span class="required">*</span></label>
                            <select id="sf_unit_size" name="unit_size" required>
                                <option value=""><?php _e('Select unit size...', 'storageflow'); ?></option>
                                <?php foreach ($unit_sizes as $size => $info) : ?>
                                    <?php if ($info['available_units'] > 0) : ?>
                                    <option value="<?php echo esc_attr($size); ?>" data-rate="<?php echo esc_attr($info['lowest_rate']); ?>">
                                        <?php echo esc_html($size); ?> - <?php echo SF_Utilities::format_currency($info['lowest_rate']); ?>/month
                                        (<?php echo $info['available_units']; ?> available)
                                    </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                            <div class="sf-field-error"></div>
                        </div>
                        
                        <div class="sf-form-field sf-field-half">
                            <label for="sf_move_in_date"><?php _e('Desired Move-In Date', 'storageflow'); ?> <span class="required">*</span></label>
                            <input type="text" id="sf_move_in_date" name="move_in_date" readonly required>
                            <div class="sf-field-error"></div>
                        </div>
                    </div>
                    
                    <div class="sf-form-field">
                        <label><?php _e('Desired Features', 'storageflow'); ?></label>
                        <div class="sf-features-checkboxes">
                            <label class="sf-checkbox-label">
                                <input type="checkbox" name="features[]" value="climate_controlled">
                                <?php _e('Climate Controlled', 'storageflow'); ?>
                            </label>
                            <label class="sf-checkbox-label">
                                <input type="checkbox" name="features[]" value="drive_up">
                                <?php _e('Drive-Up Access', 'storageflow'); ?>
                            </label>
                            <label class="sf-checkbox-label">
                                <input type="checkbox" name="features[]" value="ground_floor">
                                <?php _e('Ground Floor', 'storageflow'); ?>
                            </label>
                            <label class="sf-checkbox-label">
                                <input type="checkbox" name="features[]" value="indoor_access">
                                <?php _e('Indoor Access', 'storageflow'); ?>
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="sf-available-units" style="display: none;">
                    <h3><?php _e('Available Units', 'storageflow'); ?></h3>
                    <div class="sf-units-loading" style="display: none;">
                        <span class="spinner"></span> <?php _e('Finding available units...', 'storageflow'); ?>
                    </div>
                    <div class="sf-units-grid"></div>
                </div>
                
                <div class="sf-form-section sf-personal-info">
                    <h3><?php _e('Personal Information', 'storageflow'); ?></h3>
                    
                    <div class="sf-form-row">
                        <div class="sf-form-field sf-field-half">
                            <label for="sf_tenant_name"><?php _e('Full Name', 'storageflow'); ?> <span class="required">*</span></label>
                            <input type="text" id="sf_tenant_name" name="tenant_name" required>
                            <div class="sf-field-error"></div>
                        </div>
                        
                        <div class="sf-form-field sf-field-half">
                            <label for="sf_tenant_email"><?php _e('Email Address', 'storageflow'); ?> <span class="required">*</span></label>
                            <input type="email" id="sf_tenant_email" name="tenant_email" required>
                            <div class="sf-field-error"></div>
                        </div>
                    </div>
                    
                    <div class="sf-form-field">
                        <label for="sf_tenant_phone"><?php _e('Phone Number', 'storageflow'); ?> <span class="required">*</span></label>
                        <input type="tel" id="sf_tenant_phone" name="tenant_phone" required>
                        <div class="sf-field-error"></div>
                    </div>
                    
                    <div class="sf-form-field">
                        <label for="sf_tenant_address"><?php _e('Current Address', 'storageflow'); ?> <span class="required">*</span></label>
                        <textarea id="sf_tenant_address" name="tenant_address" rows="2" required></textarea>
                        <div class="sf-field-error"></div>
                    </div>
                    
                    <div class="sf-form-row">
                        <div class="sf-form-field sf-field-third">
                            <label for="sf_tenant_city"><?php _e('City', 'storageflow'); ?> <span class="required">*</span></label>
                            <input type="text" id="sf_tenant_city" name="tenant_city" required>
                            <div class="sf-field-error"></div>
                        </div>
                        
                        <div class="sf-form-field sf-field-third">
                            <label for="sf_tenant_state"><?php _e('State', 'storageflow'); ?> <span class="required">*</span></label>
                            <select id="sf_tenant_state" name="tenant_state" required>
                                <option value=""><?php _e('Select state...', 'storageflow'); ?></option>
                                <?php echo $this->render_state_options(); ?>
                            </select>
                            <div class="sf-field-error"></div>
                        </div>
                        
                        <div class="sf-form-field sf-field-third">
                            <label for="sf_tenant_zip"><?php _e('ZIP Code', 'storageflow'); ?> <span class="required">*</span></label>
                            <input type="text" id="sf_tenant_zip" name="tenant_zip" required>
                            <div class="sf-field-error"></div>
                        </div>
                    </div>
                </div>
                
                <div class="sf-form-section sf-rental-details">
                    <h3><?php _e('Rental Information', 'storageflow'); ?></h3>
                    
                    <div class="sf-form-row">
                        <div class="sf-form-field sf-field-half">
                            <label for="sf_rental_term"><?php _e('Rental Term', 'storageflow'); ?></label>
                            <select id="sf_rental_term" name="rental_term">
                                <option value="month_to_month"><?php _e('Month-to-Month', 'storageflow'); ?></option>
                                <option value="3_month"><?php _e('3 Month Minimum', 'storageflow'); ?></option>
                                <option value="6_month"><?php _e('6 Month Minimum', 'storageflow'); ?></option>
                                <option value="12_month"><?php _e('12 Month Contract', 'storageflow'); ?></option>
                            </select>
                        </div>
                        
                        <div class="sf-form-field sf-field-half">
                            <label for="sf_unit_use"><?php _e('What will you store?', 'storageflow'); ?></label>
                            <select id="sf_unit_use" name="unit_use">
                                <option value="personal"><?php _e('Personal Items', 'storageflow'); ?></option>
                                <option value="business"><?php _e('Business Items', 'storageflow'); ?></option>
                                <option value="vehicle"><?php _e('Vehicle Storage', 'storageflow'); ?></option>
                                <option value="furniture"><?php _e('Furniture', 'storageflow'); ?></option>
                                <option value="documents"><?php _e('Documents/Records', 'storageflow'); ?></option>
                                <option value="other"><?php _e('Other', 'storageflow'); ?></option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="sf-form-section sf-emergency-contact">
                    <h3><?php _e('Emergency Contact', 'storageflow'); ?></h3>
                    
                    <div class="sf-form-row">
                        <div class="sf-form-field sf-field-half">
                            <label for="sf_emergency_contact_name"><?php _e('Emergency Contact Name', 'storageflow'); ?></label>
                            <input type="text" id="sf_emergency_contact_name" name="emergency_contact_name">
                        </div>
                        
                        <div class="sf-form-field sf-field-half">
                            <label for="sf_emergency_contact_phone"><?php _e('Emergency Contact Phone', 'storageflow'); ?></label>
                            <input type="tel" id="sf_emergency_contact_phone" name="emergency_contact_phone">
                        </div>
                    </div>
                </div>
                
                <div class="sf-form-section sf-additional-info">
                    <h3><?php _e('Additional Information', 'storageflow'); ?></h3>
                    
                    <div class="sf-form-field">
                        <label for="sf_referral_source"><?php _e('How did you hear about us?', 'storageflow'); ?></label>
                        <select id="sf_referral_source" name="referral_source">
                            <option value=""><?php _e('Select source...', 'storageflow'); ?></option>
                            <option value="google"><?php _e('Google Search', 'storageflow'); ?></option>
                            <option value="facebook"><?php _e('Facebook', 'storageflow'); ?></option>
                            <option value="friend"><?php _e('Friend/Family', 'storageflow'); ?></option>
                            <option value="drive_by"><?php _e('Drove by facility', 'storageflow'); ?></option>
                            <option value="yellow_pages"><?php _e('Yellow Pages', 'storageflow'); ?></option>
                            <option value="other"><?php _e('Other', 'storageflow'); ?></option>
                        </select>
                    </div>
                    
                    <div class="sf-form-field">
                        <label for="sf_special_requests"><?php _e('Special Requests or Questions', 'storageflow'); ?></label>
                        <textarea id="sf_special_requests" name="special_requests" rows="4" placeholder="<?php _e('Any special requirements, questions, or requests...', 'storageflow'); ?>"></textarea>
                    </div>
                </div>
                
                <div class="sf-rental-summary" style="display: none;">
                    <h3><?php _e('Application Summary', 'storageflow'); ?></h3>
                    <div class="sf-summary-content">
                        <div class="sf-summary-item">
                            <span class="sf-summary-label"><?php _e('Selected Unit:', 'storageflow'); ?></span>
                            <span class="sf-summary-value" id="summary-unit"></span>
                        </div>
                        <div class="sf-summary-item">
                            <span class="sf-summary-label"><?php _e('Move-In Date:', 'storageflow'); ?></span>
                            <span class="sf-summary-value" id="summary-date"></span>
                        </div>
                        <div class="sf-summary-item">
                            <span class="sf-summary-label"><?php _e('Monthly Rent:', 'storageflow'); ?></span>
                            <span class="sf-summary-value" id="summary-rent"></span>
                        </div>
                        <div class="sf-summary-item">
                            <span class="sf-summary-label"><?php _e('Security Deposit:', 'storageflow'); ?></span>
                            <span class="sf-summary-value" id="summary-deposit"></span>
                        </div>
                        <div class="sf-summary-item">
                            <span class="sf-summary-label"><?php _e('Admin Fee:', 'storageflow'); ?></span>
                            <span class="sf-summary-value" id="summary-admin-fee"></span>
                        </div>
                        <div class="sf-summary-item sf-summary-total">
                            <span class="sf-summary-label"><?php _e('Total Move-In Cost:', 'storageflow'); ?></span>
                            <span class="sf-summary-value" id="summary-total"></span>
                        </div>
                        <div class="sf-summary-note">
                            <small><?php _e('* This is an application. Final pricing and unit assignment will be confirmed upon approval.', 'storageflow'); ?></small>
                        </div>
                    </div>
                </div>
                
                <div class="sf-form-actions">
                    <button type="submit" class="sf-submit-btn">
                        <span class="sf-btn-text"><?php _e('Submit Rental Application', 'storageflow'); ?></span>
                        <span class="sf-btn-loading" style="display: none;">
                            <span class="spinner"></span> <?php _e('Submitting...', 'storageflow'); ?>
                        </span>
                    </button>
                    
                    <div class="sf-form-notes">
                        <p><?php _e('* By submitting this application, you agree to our terms and conditions. We will review your application and contact you within 24 hours.', 'storageflow'); ?></p>
                    </div>
                </div>
                
                <div class="sf-form-messages">
                    <div class="sf-success-message" style="display: none;"></div>
                    <div class="sf-error-message" style="display: none;"></div>
                </div>
            </form>
        </div>
        
        <style>
        .storageflow-rental-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        
        .sf-rental-header {
            text-align: center;
            margin-bottom: 30px;
            padding: 30px;
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: white;
            border-radius: 12px;
        }
        
        .sf-rental-header h2 {
            margin-bottom: 10px;
            color: white;
        }
        
        .sf-rental-subtitle {
            color: rgba(255,255,255,0.9);
            font-size: 16px;
        }
        
        .sf-unit-sizes-display {
            background: #f0fdf4;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .sf-unit-sizes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        
        .sf-unit-size-card {
            background: white;
            padding: 20px;
            border-radius: 6px;
            border-left: 4px solid #059669;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .sf-unit-size-card:hover,
        .sf-unit-size-card.selected {
            border-color: #047857;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.15);
        }
        
        .sf-size-title {
            font-weight: 600;
            color: #047857;
            margin-bottom: 5px;
            font-size: 18px;
        }
        
        .sf-size-rate {
            font-size: 16px;
            font-weight: bold;
            color: #059669;
            margin-bottom: 10px;
        }
        
        .sf-size-details {
            font-size: 14px;
            color: #6b7280;
        }
        
        .sf-form-section {
            margin-bottom: 30px;
            padding: 25px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .sf-form-section h3 {
            margin: 0 0 20px 0;
            color: #1f2937;
            border-bottom: 2px solid #059669;
            padding-bottom: 10px;
        }
        
        .sf-form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .sf-form-field {
            margin-bottom: 20px;
        }
        
        .sf-field-half { flex: 1; }
        .sf-field-third { flex: 1; }
        
        .sf-form-field label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #374151;
        }
        
        .required { color: #dc2626; }
        
        .sf-form-field input,
        .sf-form-field select,
        .sf-form-field textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        
        .sf-form-field input:focus,
        .sf-form-field select:focus,
        .sf-form-field textarea:focus {
            outline: none;
            border-color: #059669;
            box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
        }
        
        .sf-field-error {
            color: #dc2626;
            font-size: 14px;
            margin-top: 5px;
            display: none;
        }
        
        .sf-features-checkboxes {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin-top: 5px;
        }
        
        .sf-checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: normal !important;
            margin-bottom: 0 !important;
        }
        
        .sf-checkbox-label input[type="checkbox"] {
            width: auto;
            margin: 0;
        }
        
        .sf-available-units {
            background: #eff6ff;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border: 1px solid #3b82f6;
        }
        
        .sf-units-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        
        .sf-unit-card {
            background: white;
            padding: 20px;
            border-radius: 6px;
            border: 2px solid #e5e7eb;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .sf-unit-card:hover,
        .sf-unit-card.selected {
            border-color: #059669;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.15);
        }
        
        .sf-unit-number {
            font-weight: bold;
            color: #047857;
            font-size: 18px;
            margin-bottom: 10px;
        }
        
        .sf-unit-details {
            margin: 10px 0;
            color: #6b7280;
        }
        
        .sf-unit-rate {
            font-weight: bold;
            color: #059669;
            font-size: 16px;
        }
        
        .sf-unit-features {
            margin-top: 10px;
        }
        
        .sf-feature-tag {
            display: inline-block;
            background: #d1fae5;
            color: #047857;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            margin: 2px;
        }
        
        .sf-rental-summary {
            background: #f0fdf4;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border: 1px solid #10b981;
        }
        
        .sf-summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .sf-summary-total {
            font-weight: bold;
            font-size: 18px;
            border-top: 2px solid #10b981;
            padding-top: 10px;
            margin-top: 10px;
        }
        
        .sf-summary-note {
            margin-top: 10px;
            padding: 10px;
            background: rgba(16, 185, 129, 0.1);
            border-radius: 4px;
        }
        
        .sf-form-actions {
            text-align: center;
            margin: 30px 0;
        }
        
        .sf-submit-btn {
            background: #059669;
            color: white;
            border: none;
            padding: 15px 40px;
            font-size: 18px;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s ease;
            min-width: 250px;
        }
        
        .sf-submit-btn:hover:not(:disabled) {
            background: #047857;
        }
        
        .sf-submit-btn:disabled {
            background: #9ca3af;
            cursor: not-allowed;
        }
        
        .sf-form-notes {
            margin-top: 15px;
            font-size: 14px;
            color: #6b7280;
        }
        
        .sf-success-message,
        .sf-error-message {
            padding: 15px;
            border-radius: 6px;
            margin-top: 20px;
        }
        
        .sf-success-message {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        
        .sf-error-message {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        
        .spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #f3f4f6;
            border-top: 2px solid #059669;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @media (max-width: 768px) {
            .sf-form-row {
                flex-direction: column;
                gap: 0;
            }
            
            .sf-unit-sizes-grid {
                grid-template-columns: 1fr;
            }
            
            .sf-units-grid {
                grid-template-columns: 1fr;
            }
            
            .sf-features-checkboxes {
                grid-template-columns: 1fr;
            }
        }
        </style>
        
        <?php
        return ob_get_clean();
    }
    
    /**
     * Render unit size cards
     */
    private function render_unit_size_cards($unit_sizes) {
        ob_start();
        
        foreach ($unit_sizes as $size => $info) {
            if ($info['available_units'] > 0) {
                ?>
                <div class="sf-unit-size-card" data-size="<?php echo esc_attr($size); ?>" data-rate="<?php echo esc_attr($info['lowest_rate']); ?>">
                    <div class="sf-size-title"><?php echo esc_html($size); ?></div>
                    <div class="sf-size-rate"><?php echo SF_Utilities::format_currency($info['lowest_rate']); ?>/month</div>
                    <div class="sf-size-details">
                        <?php echo $info['available_units']; ?> <?php _e('available', 'storageflow'); ?>
                        <?php if ($info['lowest_rate'] !== $info['highest_rate']) : ?>
                            <br><?php echo SF_Utilities::format_currency($info['lowest_rate']); ?> - <?php echo SF_Utilities::format_currency($info['highest_rate']); ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
            }
        }
        
        return ob_get_clean();
    }
    
    /**
     * Render state options
     */
    private function render_state_options() {
        $states = array(
            'AL' => 'Alabama', 'AK' => 'Alaska', 'AZ' => 'Arizona', 'AR' => 'Arkansas',
            'CA' => 'California', 'CO' => 'Colorado', 'CT' => 'Connecticut', 'DE' => 'Delaware',
            'FL' => 'Florida', 'GA' => 'Georgia', 'HI' => 'Hawaii', 'ID' => 'Idaho',
            'IL' => 'Illinois', 'IN' => 'Indiana', 'IA' => 'Iowa', 'KS' => 'Kansas',
            'KY' => 'Kentucky', 'LA' => 'Louisiana', 'ME' => 'Maine', 'MD' => 'Maryland',
            'MA' => 'Massachusetts', 'MI' => 'Michigan', 'MN' => 'Minnesota', 'MS' => 'Mississippi',
            'MO' => 'Missouri', 'MT' => 'Montana', 'NE' => 'Nebraska', 'NV' => 'Nevada',
            'NH' => 'New Hampshire', 'NJ' => 'New Jersey', 'NM' => 'New Mexico', 'NY' => 'New York',
            'NC' => 'North Carolina', 'ND' => 'North Dakota', 'OH' => 'Ohio', 'OK' => 'Oklahoma',
            'OR' => 'Oregon', 'PA' => 'Pennsylvania', 'RI' => 'Rhode Island', 'SC' => 'South Carolina',
            'SD' => 'South Dakota', 'TN' => 'Tennessee', 'TX' => 'Texas', 'UT' => 'Utah',
            'VT' => 'Vermont', 'VA' => 'Virginia', 'WA' => 'Washington', 'WV' => 'West Virginia',
            'WI' => 'Wisconsin', 'WY' => 'Wyoming'
        );
        
        $output = '';
        foreach ($states as $code => $name) {
            $output .= sprintf('<option value="%s">%s</option>', esc_attr($code), esc_html($name));
        }
        
        return $output;
    }
    
    /**
     * Render unit sizes showcase
     */
    public function render_unit_sizes($atts) {
        $atts = shortcode_atts(array(
            'style' => 'grid',
            'show_availability' => 'true',
            'show_features' => 'true'
        ), $atts);
        
        $unit_sizes = SF_Unit_Manager::get_units_by_size(true);
        
        ob_start();
        ?>
        <div class="sf-unit-sizes-showcase">
            <div class="sf-sizes-header">
                <h3><?php _e('Storage Unit Sizes & Rates', 'storageflow'); ?></h3>
                <p><?php _e('Find the perfect size for your storage needs.', 'storageflow'); ?></p>
            </div>
            
            <div class="sf-sizes-grid" data-style="<?php echo esc_attr($atts['style']); ?>">
                <?php foreach ($unit_sizes as $size => $info) : ?>
                <div class="sf-size-showcase-card">
                    <div class="sf-size-header">
                        <h4><?php echo esc_html($size); ?></h4>
                        <div class="sf-size-price">
                            <?php if ($info['lowest_rate'] === $info['highest_rate']) : ?>
                                <?php echo SF_Utilities::format_currency($info['lowest_rate']); ?>/month
                            <?php else : ?>
                                <?php echo SF_Utilities::format_currency($info['lowest_rate']); ?> - <?php echo SF_Utilities::format_currency($info['highest_rate']); ?>/month
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if ($atts['show_availability'] === 'true') : ?>
                    <div class="sf-size-availability">
                        <div class="sf-availability-stats">
                            <span class="sf-stat">
                                <strong><?php echo $info['total_units']; ?></strong> <?php _e('Total Units', 'storageflow'); ?>
                            </span>
                            <span class="sf-stat">
                                <strong><?php echo $info['available_units']; ?></strong> <?php _e('Available', 'storageflow'); ?>
                            </span>
                            <span class="sf-stat">
                                <strong><?php echo $info['rented_units']; ?></strong> <?php _e('Rented', 'storageflow'); ?>
                            </span>
                        </div>
                        
                        <div class="sf-occupancy-bar">
                            <?php 
                            $occupancy_rate = $info['total_units'] > 0 ? ($info['rented_units'] / $info['total_units']) * 100 : 0;
                            ?>
                            <div class="sf-occupancy-fill" style="width: <?php echo $occupancy_rate; ?>%;"></div>
                        </div>
                        <div class="sf-occupancy-text">
                            <?php echo round($occupancy_rate, 1); ?>% <?php _e('Occupied', 'storageflow'); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="sf-size-description">
                        <?php echo $this->get_size_description($size); ?>
                    </div>
                    
                    <div class="sf-size-actions">
                        <a href="#rental-form" class="sf-rent-now-btn">
                            <?php _e('Rent Now', 'storageflow'); ?>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <style>
        .sf-unit-sizes-showcase {
            margin: 20px 0;
        }
        
        .sf-sizes-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .sf-sizes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .sf-size-showcase-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .sf-size-showcase-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .sf-size-header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 2px solid #059669;
        }
        
        .sf-size-header h4 {
            margin: 0 0 5px 0;
            color: #1f2937;
        }
        
        .sf-size-price {
            font-size: 18px;
            font-weight: bold;
            color: #059669;
        }
        
        .sf-size-availability {
            margin: 15px 0;
        }
        
        .sf-availability-stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .sf-stat {
            text-align: center;
            font-size: 14px;
            color: #6b7280;
        }
        
        .sf-occupancy-bar {
            height: 8px;
            background: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 5px;
        }
        
        .sf-occupancy-fill {
            height: 100%;
            background: linear-gradient(90deg, #10b981, #059669);
            transition: width 0.3s ease;
        }
        
        .sf-occupancy-text {
            text-align: center;
            font-size: 12px;
            color: #6b7280;
        }
        
        .sf-size-description {
            margin: 15px 0;
            color: #6b7280;
            line-height: 1.5;
        }
        
        .sf-size-actions {
            text-align: center;
            margin-top: 20px;
        }
        
        .sf-rent-now-btn {
            background: #059669;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.3s ease;
        }
        
        .sf-rent-now-btn:hover {
            background: #047857;
            color: white;
        }
        </style>
        
        <?php
        return ob_get_clean();
    }
    
    /**
     * Get size description based on unit size
     */
    private function get_size_description($size) {
        $descriptions = array(
            '5x5' => __('Perfect for small items, seasonal decorations, or a few boxes. About the size of a large closet.', 'storageflow'),
            '5x10' => __('Great for a small apartment or office. Can fit a sofa, dresser, or small appliances.', 'storageflow'),
            '10x10' => __('Ideal for a 1-2 bedroom apartment or small house. Can store furniture and appliances.', 'storageflow'),
            '10x15' => __('Perfect for a 2-3 bedroom house, including major appliances and furniture.', 'storageflow'),
            '10x20' => __('Suitable for a 3-4 bedroom house or vehicle storage. Large furniture and multiple rooms of items.', 'storageflow'),
            '10x30' => __('Ideal for large homes or commercial storage. Can accommodate multiple vehicles or extensive inventory.', 'storageflow')
        );
        
        return isset($descriptions[$size]) ? $descriptions[$size] : __('Contact us for details about this unit size.', 'storageflow');
    }
}