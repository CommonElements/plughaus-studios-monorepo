<?php
/**
 * Booking Form for StudioSnap
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class SS_Booking_Form {
    
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_shortcode('studiosnap_booking_form', array($this, 'render_booking_form'));
    }
    
    public function enqueue_scripts() {
        if (is_singular() && has_shortcode(get_post()->post_content, 'studiosnap_booking_form')) {
            wp_enqueue_script('jquery');
            
            // Localize script
            wp_localize_script('jquery', 'ss_booking_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('ss_booking_nonce'),
                'messages' => array(
                    'loading' => __('Processing...', 'studiosnap'),
                    'success' => __('Booking submitted successfully!', 'studiosnap'),
                    'error' => __('There was an error. Please try again.', 'studiosnap')
                )
            ));
        }
    }
    
    public static function render_booking_form($atts = array()) {
        $atts = shortcode_atts(array(
            'title' => __('Book Your Session', 'studiosnap'),
            'show_packages' => 'yes',
            'default_type' => 'portrait'
        ), $atts);
        
        // Get session packages if Booking System exists
        $packages = array();
        if (class_exists('SS_Booking_System')) {
            $packages = SS_Booking_System::get_session_packages();
        } else {
            // Fallback packages
            $packages = array(
                'portrait' => array(
                    'name' => __('Portrait Session', 'studiosnap'),
                    'duration' => 2,
                    'price' => 200,
                    'description' => __('Individual or couple portraits', 'studiosnap')
                ),
                'family' => array(
                    'name' => __('Family Session', 'studiosnap'),
                    'duration' => 3,
                    'price' => 300,
                    'description' => __('Family portraits with up to 6 people', 'studiosnap')
                )
            );
        }
        
        ob_start();
        ?>
        <div class="ss-booking-form-container">
            <?php if ($atts['title']): ?>
                <h3 class="ss-booking-title"><?php echo esc_html($atts['title']); ?></h3>
            <?php endif; ?>
            
            <?php if ($atts['show_packages'] === 'yes'): ?>
                <div class="ss-packages-preview">
                    <h4><?php _e('Our Photography Packages', 'studiosnap'); ?></h4>
                    <div class="ss-packages-grid">
                        <?php foreach ($packages as $type => $package): ?>
                            <div class="ss-package-card" data-type="<?php echo esc_attr($type); ?>">
                                <h5><?php echo esc_html($package['name']); ?></h5>
                                <p class="ss-package-price">$<?php echo number_format($package['price']); ?></p>
                                <p class="ss-package-duration"><?php echo $package['duration']; ?> <?php _e('hours', 'studiosnap'); ?></p>
                                <p class="ss-package-description"><?php echo esc_html($package['description']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <form id="ss-booking-form" class="ss-booking-form">
                <div class="ss-form-row">
                    <div class="ss-form-field">
                        <label for="ss_client_name"><?php _e('Full Name', 'studiosnap'); ?> *</label>
                        <input type="text" id="ss_client_name" name="client_name" required>
                    </div>
                    
                    <div class="ss-form-field">
                        <label for="ss_client_email"><?php _e('Email Address', 'studiosnap'); ?> *</label>
                        <input type="email" id="ss_client_email" name="client_email" required>
                    </div>
                </div>
                
                <div class="ss-form-row">
                    <div class="ss-form-field">
                        <label for="ss_client_phone"><?php _e('Phone Number', 'studiosnap'); ?></label>
                        <input type="tel" id="ss_client_phone" name="client_phone">
                    </div>
                    
                    <div class="ss-form-field">
                        <label for="ss_session_type"><?php _e('Session Type', 'studiosnap'); ?> *</label>
                        <select id="ss_session_type" name="session_type" required>
                            <?php foreach ($packages as $type => $package): ?>
                                <option value="<?php echo esc_attr($type); ?>" <?php selected($type, $atts['default_type']); ?>>
                                    <?php echo esc_html($package['name']); ?> - $<?php echo number_format($package['price']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="ss-form-row">
                    <div class="ss-form-field">
                        <label for="ss_session_date"><?php _e('Preferred Date', 'studiosnap'); ?> *</label>
                        <input type="date" id="ss_session_date" name="session_date" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                    </div>
                    
                    <div class="ss-form-field">
                        <label for="ss_session_time"><?php _e('Preferred Time', 'studiosnap'); ?> *</label>
                        <select id="ss_session_time" name="session_time" required>
                            <option value=""><?php _e('Select a time', 'studiosnap'); ?></option>
                            <option value="09:00">9:00 AM</option>
                            <option value="10:00">10:00 AM</option>
                            <option value="11:00">11:00 AM</option>
                            <option value="13:00">1:00 PM</option>
                            <option value="14:00">2:00 PM</option>
                            <option value="15:00">3:00 PM</option>
                            <option value="16:00">4:00 PM</option>
                        </select>
                    </div>
                </div>
                
                <div class="ss-form-row">
                    <div class="ss-form-field">
                        <label for="ss_session_location"><?php _e('Session Location', 'studiosnap'); ?></label>
                        <select id="ss_session_location" name="session_location">
                            <option value="studio"><?php _e('Studio', 'studiosnap'); ?></option>
                            <option value="on_location"><?php _e('On Location (+$50)', 'studiosnap'); ?></option>
                        </select>
                    </div>
                </div>
                
                <div class="ss-form-row">
                    <div class="ss-form-field">
                        <label for="ss_special_requests"><?php _e('Special Requests or Notes', 'studiosnap'); ?></label>
                        <textarea id="ss_special_requests" name="special_requests" rows="4" placeholder="<?php esc_attr_e('Tell us about your vision, special requirements, or any questions you have...', 'studiosnap'); ?>"></textarea>
                    </div>
                </div>
                
                <div class="ss-form-row">
                    <div class="ss-form-field">
                        <button type="submit" class="ss-submit-button">
                            <span class="ss-button-text"><?php _e('Submit Booking Request', 'studiosnap'); ?></span>
                            <span class="ss-button-loading" style="display: none;"><?php _e('Processing...', 'studiosnap'); ?></span>
                        </button>
                    </div>
                </div>
                
                <div id="ss-booking-messages" class="ss-messages"></div>
            </form>
        </div>
        
        <style>
        .ss-booking-form-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
        }
        
        .ss-booking-title {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        
        .ss-packages-preview {
            margin-bottom: 30px;
        }
        
        .ss-packages-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .ss-package-card {
            background: white;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            border: 2px solid transparent;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .ss-package-card:hover,
        .ss-package-card.selected {
            border-color: #0073aa;
            box-shadow: 0 2px 8px rgba(0,115,170,0.1);
        }
        
        .ss-package-card h5 {
            margin: 0 0 10px 0;
            color: #333;
        }
        
        .ss-package-price {
            font-size: 18px;
            font-weight: bold;
            color: #0073aa;
            margin: 5px 0;
        }
        
        .ss-package-duration {
            color: #666;
            font-size: 14px;
            margin: 5px 0;
        }
        
        .ss-package-description {
            font-size: 13px;
            color: #777;
            margin: 10px 0 0 0;
        }
        
        .ss-booking-form {
            background: white;
            padding: 30px;
            border-radius: 5px;
        }
        
        .ss-form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .ss-form-row:last-child {
            grid-template-columns: 1fr;
        }
        
        .ss-form-field {
            display: flex;
            flex-direction: column;
        }
        
        .ss-form-field label {
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }
        
        .ss-form-field input,
        .ss-form-field select,
        .ss-form-field textarea {
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        
        .ss-form-field input:focus,
        .ss-form-field select:focus,
        .ss-form-field textarea:focus {
            outline: none;
            border-color: #0073aa;
            box-shadow: 0 0 0 2px rgba(0,115,170,0.1);
        }
        
        .ss-submit-button {
            background: #0073aa;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .ss-submit-button:hover {
            background: #005a87;
        }
        
        .ss-submit-button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        
        .ss-messages {
            margin-top: 20px;
            padding: 15px;
            border-radius: 4px;
            display: none;
        }
        
        .ss-messages.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .ss-messages.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        @media (max-width: 768px) {
            .ss-form-row {
                grid-template-columns: 1fr;
            }
            
            .ss-packages-grid {
                grid-template-columns: 1fr;
            }
        }
        </style>
        <?php
        return ob_get_clean();
    }
}

// Initialize if not in admin
if (!is_admin()) {
    new SS_Booking_Form();
}