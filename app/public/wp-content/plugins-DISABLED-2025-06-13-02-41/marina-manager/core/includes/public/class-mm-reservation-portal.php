<?php
/**
 * Marina Manager Reservation Portal - Public booking interface
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class MM_Reservation_Portal {
    
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_portal_scripts'));
        add_shortcode('marina_reservation_form', array($this, 'render_reservation_form'));
        add_shortcode('marina_slip_availability', array($this, 'render_slip_availability'));
    }
    
    public function enqueue_portal_scripts() {
        if (is_page() || has_shortcode(get_post()->post_content ?? '', 'marina_reservation_form')) {
            wp_enqueue_script('jquery-ui-datepicker');
            wp_enqueue_style('jquery-ui-datepicker-style', 'https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css');
            
            wp_enqueue_script(
                'marina-reservation',
                MM_PLUGIN_URL . 'core/assets/js/reservation-portal.js',
                array('jquery', 'jquery-ui-datepicker'),
                MM_VERSION,
                true
            );
            
            wp_localize_script('marina-reservation', 'marina_reservation', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('mm_public_nonce'),
                'messages' => array(
                    'checking_availability' => __('Checking slip availability...', 'marina-manager'),
                    'no_slips_available' => __('No suitable slips available for your dates and boat size.', 'marina-manager'),
                    'submitting_reservation' => __('Submitting your reservation...', 'marina-manager'),
                    'reservation_success' => __('Reservation submitted successfully!', 'marina-manager'),
                    'reservation_error' => __('There was an error submitting your reservation. Please try again.', 'marina-manager'),
                    'required_field' => __('This field is required.', 'marina-manager'),
                    'invalid_email' => __('Please enter a valid email address.', 'marina-manager'),
                    'invalid_phone' => __('Please enter a valid phone number.', 'marina-manager'),
                    'invalid_dates' => __('Please select valid arrival and departure dates.', 'marina-manager')
                )
            ));
            
            wp_enqueue_style(
                'marina-reservation-portal',
                MM_PLUGIN_URL . 'core/assets/css/reservation-portal.css',
                array(),
                MM_VERSION
            );
        }
    }
    
    /**
     * Render reservation form shortcode
     */
    public function render_reservation_form($atts) {
        $atts = shortcode_atts(array(
            'style' => 'default',
            'show_rates' => 'true',
            'show_slip_types' => 'true',
            'title' => __('Reserve Your Slip', 'marina-manager')
        ), $atts);
        
        ob_start();
        ?>
        <div class="marina-reservation-container" data-style="<?php echo esc_attr($atts['style']); ?>">
            <div class="mm-reservation-header">
                <h2><?php echo esc_html($atts['title']); ?></h2>
                <p class="mm-reservation-subtitle"><?php _e('Book your perfect slip at our marina. Fill out the form below and we\'ll confirm your reservation within 24 hours.', 'marina-manager'); ?></p>
            </div>
            
            <?php if ($atts['show_rates'] === 'true') : ?>
            <div class="mm-rates-display">
                <h3><?php _e('Current Rates', 'marina-manager'); ?></h3>
                <div class="mm-rates-grid">
                    <?php echo $this->render_rate_cards(); ?>
                </div>
            </div>
            <?php endif; ?>
            
            <form id="marina-reservation-form" class="mm-reservation-form" novalidate>
                <?php wp_nonce_field('mm_public_nonce', 'mm_reservation_nonce'); ?>
                
                <div class="mm-form-section mm-boat-info">
                    <h3><?php _e('Boat Information', 'marina-manager'); ?></h3>
                    
                    <div class="mm-form-row">
                        <div class="mm-form-field mm-field-half">
                            <label for="mm_boat_name"><?php _e('Boat Name', 'marina-manager'); ?> <span class="required">*</span></label>
                            <input type="text" id="mm_boat_name" name="boat_name" required>
                            <div class="mm-field-error"></div>
                        </div>
                        
                        <div class="mm-form-field mm-field-half">
                            <label for="mm_boat_type"><?php _e('Boat Type', 'marina-manager'); ?> <span class="required">*</span></label>
                            <select id="mm_boat_type" name="boat_type" required>
                                <option value=""><?php _e('Select boat type...', 'marina-manager'); ?></option>
                                <option value="sailboat"><?php _e('Sailboat', 'marina-manager'); ?></option>
                                <option value="motorboat"><?php _e('Motorboat', 'marina-manager'); ?></option>
                                <option value="catamaran"><?php _e('Catamaran', 'marina-manager'); ?></option>
                                <option value="yacht"><?php _e('Yacht', 'marina-manager'); ?></option>
                                <option value="fishing_boat"><?php _e('Fishing Boat', 'marina-manager'); ?></option>
                                <option value="pontoon"><?php _e('Pontoon Boat', 'marina-manager'); ?></option>
                                <option value="other"><?php _e('Other', 'marina-manager'); ?></option>
                            </select>
                            <div class="mm-field-error"></div>
                        </div>
                    </div>
                    
                    <div class="mm-form-row">
                        <div class="mm-form-field mm-field-third">
                            <label for="mm_boat_length"><?php _e('Length (feet)', 'marina-manager'); ?> <span class="required">*</span></label>
                            <input type="number" id="mm_boat_length" name="boat_length" min="10" max="200" step="0.1" required>
                            <div class="mm-field-error"></div>
                        </div>
                        
                        <div class="mm-form-field mm-field-third">
                            <label for="mm_boat_beam"><?php _e('Beam (feet)', 'marina-manager'); ?></label>
                            <input type="number" id="mm_boat_beam" name="boat_beam" min="3" max="50" step="0.1">
                        </div>
                        
                        <div class="mm-form-field mm-field-third">
                            <label for="mm_boat_draft"><?php _e('Draft (feet)', 'marina-manager'); ?></label>
                            <input type="number" id="mm_boat_draft" name="boat_draft" min="0.5" max="20" step="0.1">
                        </div>
                    </div>
                    
                    <div class="mm-form-row">
                        <div class="mm-form-field mm-field-half">
                            <label for="mm_boat_make"><?php _e('Make/Manufacturer', 'marina-manager'); ?></label>
                            <input type="text" id="mm_boat_make" name="boat_make">
                        </div>
                        
                        <div class="mm-form-field mm-field-half">
                            <label for="mm_boat_year"><?php _e('Year', 'marina-manager'); ?></label>
                            <input type="number" id="mm_boat_year" name="boat_year" min="1950" max="<?php echo date('Y') + 1; ?>">
                        </div>
                    </div>
                </div>
                
                <div class="mm-form-section mm-owner-info">
                    <h3><?php _e('Contact Information', 'marina-manager'); ?></h3>
                    
                    <div class="mm-form-row">
                        <div class="mm-form-field mm-field-half">
                            <label for="mm_owner_name"><?php _e('Full Name', 'marina-manager'); ?> <span class="required">*</span></label>
                            <input type="text" id="mm_owner_name" name="owner_name" required>
                            <div class="mm-field-error"></div>
                        </div>
                        
                        <div class="mm-form-field mm-field-half">
                            <label for="mm_owner_email"><?php _e('Email Address', 'marina-manager'); ?> <span class="required">*</span></label>
                            <input type="email" id="mm_owner_email" name="owner_email" required>
                            <div class="mm-field-error"></div>
                        </div>
                    </div>
                    
                    <div class="mm-form-field">
                        <label for="mm_owner_phone"><?php _e('Phone Number', 'marina-manager'); ?> <span class="required">*</span></label>
                        <input type="tel" id="mm_owner_phone" name="owner_phone" required>
                        <div class="mm-field-error"></div>
                    </div>
                </div>
                
                <div class="mm-form-section mm-reservation-details">
                    <h3><?php _e('Reservation Details', 'marina-manager'); ?></h3>
                    
                    <div class="mm-form-row">
                        <div class="mm-form-field mm-field-half">
                            <label for="mm_start_date"><?php _e('Arrival Date', 'marina-manager'); ?> <span class="required">*</span></label>
                            <input type="text" id="mm_start_date" name="start_date" readonly required>
                            <div class="mm-field-error"></div>
                        </div>
                        
                        <div class="mm-form-field mm-field-half">
                            <label for="mm_end_date"><?php _e('Departure Date', 'marina-manager'); ?> <span class="required">*</span></label>
                            <input type="text" id="mm_end_date" name="end_date" readonly required>
                            <div class="mm-field-error"></div>
                        </div>
                    </div>
                    
                    <div class="mm-form-row">
                        <div class="mm-form-field mm-field-half">
                            <label for="mm_reservation_type"><?php _e('Reservation Type', 'marina-manager'); ?></label>
                            <select id="mm_reservation_type" name="reservation_type">
                                <option value="transient"><?php _e('Transient (Short-term)', 'marina-manager'); ?></option>
                                <option value="seasonal"><?php _e('Seasonal (3-6 months)', 'marina-manager'); ?></option>
                                <option value="annual"><?php _e('Annual (12 months)', 'marina-manager'); ?></option>
                            </select>
                        </div>
                        
                        <div class="mm-form-field mm-field-half">
                            <label for="mm_guest_count"><?php _e('Number of Guests', 'marina-manager'); ?></label>
                            <select id="mm_guest_count" name="guest_count">
                                <option value="1">1 Guest</option>
                                <option value="2">2 Guests</option>
                                <option value="3">3 Guests</option>
                                <option value="4">4 Guests</option>
                                <option value="5">5 Guests</option>
                                <option value="6">6+ Guests</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mm-form-field">
                        <label for="mm_power_requirements"><?php _e('Power Requirements', 'marina-manager'); ?></label>
                        <select id="mm_power_requirements" name="power_requirements">
                            <option value="30amp"><?php _e('30 Amp', 'marina-manager'); ?></option>
                            <option value="50amp"><?php _e('50 Amp', 'marina-manager'); ?></option>
                            <option value="100amp"><?php _e('100 Amp', 'marina-manager'); ?></option>
                            <option value="none"><?php _e('No Power Needed', 'marina-manager'); ?></option>
                        </select>
                    </div>
                </div>
                
                <div class="mm-availability-display" style="display: none;">
                    <h3><?php _e('Available Slips', 'marina-manager'); ?></h3>
                    <div class="mm-availability-loading" style="display: none;">
                        <span class="spinner"></span> <?php _e('Checking availability...', 'marina-manager'); ?>
                    </div>
                    <div class="mm-slips-grid"></div>
                </div>
                
                <div class="mm-form-section mm-additional-info">
                    <h3><?php _e('Additional Information', 'marina-manager'); ?></h3>
                    
                    <div class="mm-form-field">
                        <label for="mm_special_requests"><?php _e('Special Requests or Requirements', 'marina-manager'); ?></label>
                        <textarea id="mm_special_requests" name="special_requests" rows="4" placeholder="<?php _e('Tell us about any special requirements, accessibility needs, or other requests...', 'marina-manager'); ?>"></textarea>
                    </div>
                </div>
                
                <div class="mm-reservation-summary" style="display: none;">
                    <h3><?php _e('Reservation Summary', 'marina-manager'); ?></h3>
                    <div class="mm-summary-content">
                        <div class="mm-summary-item">
                            <span class="mm-summary-label"><?php _e('Boat:', 'marina-manager'); ?></span>
                            <span class="mm-summary-value" id="summary-boat"></span>
                        </div>
                        <div class="mm-summary-item">
                            <span class="mm-summary-label"><?php _e('Dates:', 'marina-manager'); ?></span>
                            <span class="mm-summary-value" id="summary-dates"></span>
                        </div>
                        <div class="mm-summary-item">
                            <span class="mm-summary-label"><?php _e('Selected Slip:', 'marina-manager'); ?></span>
                            <span class="mm-summary-value" id="summary-slip"></span>
                        </div>
                        <div class="mm-summary-item mm-summary-total">
                            <span class="mm-summary-label"><?php _e('Estimated Total:', 'marina-manager'); ?></span>
                            <span class="mm-summary-value" id="summary-total"></span>
                        </div>
                        <div class="mm-summary-note">
                            <small><?php _e('* Final pricing will be confirmed upon approval. A 30% deposit is required to secure your reservation.', 'marina-manager'); ?></small>
                        </div>
                    </div>
                </div>
                
                <div class="mm-form-actions">
                    <button type="submit" class="mm-submit-btn">
                        <span class="mm-btn-text"><?php _e('Submit Reservation Request', 'marina-manager'); ?></span>
                        <span class="mm-btn-loading" style="display: none;">
                            <span class="spinner"></span> <?php _e('Submitting...', 'marina-manager'); ?>
                        </span>
                    </button>
                    
                    <div class="mm-form-notes">
                        <p><?php _e('* This is a reservation request, not a confirmed booking. We will review your request and contact you within 24 hours to confirm availability and payment details.', 'marina-manager'); ?></p>
                    </div>
                </div>
                
                <div class="mm-form-messages">
                    <div class="mm-success-message" style="display: none;"></div>
                    <div class="mm-error-message" style="display: none;"></div>
                </div>
            </form>
        </div>
        
        <style>
        .marina-reservation-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        
        .mm-reservation-header {
            text-align: center;
            margin-bottom: 30px;
            padding: 30px;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            border-radius: 12px;
        }
        
        .mm-reservation-header h2 {
            margin-bottom: 10px;
            color: white;
        }
        
        .mm-reservation-subtitle {
            color: rgba(255,255,255,0.9);
            font-size: 16px;
        }
        
        .mm-rates-display {
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .mm-rates-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        
        .mm-rate-card {
            background: white;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #3b82f6;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .mm-rate-title {
            font-weight: 600;
            color: #1e40af;
            margin-bottom: 5px;
        }
        
        .mm-rate-price {
            font-size: 18px;
            font-weight: bold;
            color: #059669;
        }
        
        .mm-form-section {
            margin-bottom: 30px;
            padding: 25px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .mm-form-section h3 {
            margin: 0 0 20px 0;
            color: #1f2937;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 10px;
        }
        
        .mm-form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .mm-form-field {
            margin-bottom: 20px;
        }
        
        .mm-field-half { flex: 1; }
        .mm-field-third { flex: 1; }
        
        .mm-form-field label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #374151;
        }
        
        .required { color: #dc2626; }
        
        .mm-form-field input,
        .mm-form-field select,
        .mm-form-field textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        
        .mm-form-field input:focus,
        .mm-form-field select:focus,
        .mm-form-field textarea:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .mm-field-error {
            color: #dc2626;
            font-size: 14px;
            margin-top: 5px;
            display: none;
        }
        
        .mm-availability-display {
            background: #f0f9ff;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border: 1px solid #0ea5e9;
        }
        
        .mm-slips-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        
        .mm-slip-card {
            background: white;
            padding: 15px;
            border-radius: 6px;
            border: 2px solid #e5e7eb;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .mm-slip-card:hover,
        .mm-slip-card.selected {
            border-color: #3b82f6;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
        }
        
        .mm-slip-number {
            font-weight: bold;
            color: #1e40af;
            font-size: 18px;
        }
        
        .mm-slip-details {
            margin: 10px 0;
            color: #6b7280;
        }
        
        .mm-slip-rate {
            font-weight: bold;
            color: #059669;
            font-size: 16px;
        }
        
        .mm-reservation-summary {
            background: #ecfdf5;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border: 1px solid #10b981;
        }
        
        .mm-summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .mm-summary-total {
            font-weight: bold;
            font-size: 18px;
            border-top: 2px solid #10b981;
            padding-top: 10px;
            margin-top: 10px;
        }
        
        .mm-summary-note {
            margin-top: 10px;
            padding: 10px;
            background: rgba(16, 185, 129, 0.1);
            border-radius: 4px;
        }
        
        .mm-form-actions {
            text-align: center;
            margin: 30px 0;
        }
        
        .mm-submit-btn {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 15px 40px;
            font-size: 18px;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s ease;
            min-width: 250px;
        }
        
        .mm-submit-btn:hover:not(:disabled) {
            background: #1d4ed8;
        }
        
        .mm-submit-btn:disabled {
            background: #9ca3af;
            cursor: not-allowed;
        }
        
        .mm-form-notes {
            margin-top: 15px;
            font-size: 14px;
            color: #6b7280;
        }
        
        .mm-success-message,
        .mm-error-message {
            padding: 15px;
            border-radius: 6px;
            margin-top: 20px;
        }
        
        .mm-success-message {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        
        .mm-error-message {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        
        .spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #f3f4f6;
            border-top: 2px solid #3b82f6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @media (max-width: 768px) {
            .mm-form-row {
                flex-direction: column;
                gap: 0;
            }
            
            .mm-rates-grid {
                grid-template-columns: 1fr;
            }
            
            .mm-slips-grid {
                grid-template-columns: 1fr;
            }
        }
        </style>
        
        <?php
        return ob_get_clean();
    }
    
    /**
     * Render rate cards
     */
    private function render_rate_cards() {
        // Get rate information from settings or defaults
        $rates = array(
            'transient_daily' => get_option('mm_transient_daily_rate', 2.50),
            'seasonal_monthly' => get_option('mm_seasonal_monthly_rate', 12.00),
            'annual_monthly' => get_option('mm_annual_monthly_rate', 10.00)
        );
        
        ob_start();
        ?>
        <div class="mm-rate-card">
            <div class="mm-rate-title"><?php _e('Daily Rate', 'marina-manager'); ?></div>
            <div class="mm-rate-price">$<?php echo number_format($rates['transient_daily'], 2); ?>/ft/day</div>
        </div>
        
        <div class="mm-rate-card">
            <div class="mm-rate-title"><?php _e('Seasonal Rate', 'marina-manager'); ?></div>
            <div class="mm-rate-price">$<?php echo number_format($rates['seasonal_monthly'], 2); ?>/ft/month</div>
        </div>
        
        <div class="mm-rate-card">
            <div class="mm-rate-title"><?php _e('Annual Rate', 'marina-manager'); ?></div>
            <div class="mm-rate-price">$<?php echo number_format($rates['annual_monthly'], 2); ?>/ft/month</div>
        </div>
        
        <div class="mm-rate-card">
            <div class="mm-rate-title"><?php _e('Utilities', 'marina-manager'); ?></div>
            <div class="mm-rate-price">$<?php echo number_format(get_option('mm_utility_fee', 5.00), 2); ?>/day</div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Render slip availability calendar
     */
    public function render_slip_availability($atts) {
        $atts = shortcode_atts(array(
            'month' => date('n'),
            'year' => date('Y'),
            'show_legend' => 'true'
        ), $atts);
        
        $calendar_data = MM_Slip_Manager::get_availability_calendar($atts['month'], $atts['year']);
        
        ob_start();
        ?>
        <div class="mm-availability-calendar">
            <div class="mm-calendar-header">
                <h3><?php echo date('F Y', mktime(0, 0, 0, $atts['month'], 1, $atts['year'])); ?> <?php _e('Slip Availability', 'marina-manager'); ?></h3>
                
                <?php if ($atts['show_legend'] === 'true') : ?>
                <div class="mm-availability-legend">
                    <span class="mm-legend-item">
                        <span class="mm-status-indicator available"></span>
                        <?php _e('Available', 'marina-manager'); ?>
                    </span>
                    <span class="mm-legend-item">
                        <span class="mm-status-indicator occupied"></span>
                        <?php _e('Occupied', 'marina-manager'); ?>
                    </span>
                    <span class="mm-legend-item">
                        <span class="mm-status-indicator maintenance"></span>
                        <?php _e('Maintenance', 'marina-manager'); ?>
                    </span>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="mm-calendar-grid">
                <?php foreach ($calendar_data as $slip_number => $slip_info) : ?>
                <div class="mm-slip-availability-row">
                    <div class="mm-slip-info">
                        <strong><?php _e('Slip', 'marina-manager'); ?> <?php echo esc_html($slip_number); ?></strong>
                        <span class="mm-slip-status <?php echo esc_attr($slip_info['status']); ?>">
                            <?php echo esc_html(ucfirst($slip_info['status'])); ?>
                        </span>
                    </div>
                    
                    <div class="mm-availability-bars">
                        <?php
                        $days_in_month = date('t', mktime(0, 0, 0, $atts['month'], 1, $atts['year']));
                        for ($day = 1; $day <= $days_in_month; $day++) {
                            $current_date = sprintf('%04d-%02d-%02d', $atts['year'], $atts['month'], $day);
                            $day_status = $this->get_day_availability_status($slip_info['reservations'], $current_date);
                            echo '<span class="mm-day-indicator ' . esc_attr($day_status) . '" title="' . esc_attr($current_date) . '"></span>';
                        }
                        ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <style>
        .mm-availability-calendar {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .mm-calendar-header {
            margin-bottom: 20px;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 15px;
        }
        
        .mm-availability-legend {
            display: flex;
            gap: 20px;
            margin-top: 10px;
        }
        
        .mm-legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
        }
        
        .mm-status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }
        
        .mm-status-indicator.available { background: #10b981; }
        .mm-status-indicator.occupied { background: #dc2626; }
        .mm-status-indicator.maintenance { background: #f59e0b; }
        
        .mm-slip-availability-row {
            display: flex;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .mm-slip-info {
            width: 150px;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .mm-slip-status {
            font-size: 12px;
            padding: 2px 8px;
            border-radius: 12px;
            color: white;
        }
        
        .mm-slip-status.available { background: #10b981; }
        .mm-slip-status.occupied { background: #dc2626; }
        .mm-slip-status.maintenance { background: #f59e0b; }
        
        .mm-availability-bars {
            display: flex;
            gap: 1px;
            flex-wrap: wrap;
        }
        
        .mm-day-indicator {
            width: 8px;
            height: 20px;
            display: inline-block;
            border-radius: 2px;
        }
        
        .mm-day-indicator.available { background: #d1fae5; }
        .mm-day-indicator.occupied { background: #fee2e2; }
        .mm-day-indicator.reserved { background: #fef3c7; }
        </style>
        
        <?php
        return ob_get_clean();
    }
    
    /**
     * Get day availability status from reservations
     */
    private function get_day_availability_status($reservations, $date) {
        foreach ($reservations as $reservation) {
            $start_date = date('Y-m-d', strtotime($reservation['start_date']));
            $end_date = date('Y-m-d', strtotime($reservation['end_date']));
            
            if ($date >= $start_date && $date <= $end_date) {
                return $reservation['type'] === 'transient' ? 'reserved' : 'occupied';
            }
        }
        
        return 'available';
    }
}