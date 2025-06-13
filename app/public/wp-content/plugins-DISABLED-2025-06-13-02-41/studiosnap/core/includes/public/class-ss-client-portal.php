<?php
/**
 * StudioSnap Client Portal - Secure client access to bookings, galleries, contracts
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class SS_Client_Portal {
    
    public function __construct() {
        add_action('init', array($this, 'handle_portal_requests'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_portal_scripts'));
        add_action('template_redirect', array($this, 'handle_portal_pages'));
    }
    
    public function enqueue_portal_scripts() {
        if ($this->is_portal_page()) {
            wp_enqueue_style(
                'studiosnap-client-portal',
                SS_PLUGIN_URL . 'core/assets/css/client-portal.css',
                array(),
                SS_VERSION
            );
            
            wp_enqueue_script(
                'studiosnap-client-portal',
                SS_PLUGIN_URL . 'core/assets/js/client-portal.js',
                array('jquery'),
                SS_VERSION,
                true
            );
            
            wp_localize_script('studiosnap-client-portal', 'studiosnap_portal', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('ss_portal_nonce')
            ));
        }
    }
    
    public function handle_portal_pages() {
        // Handle client portal pages
        if (isset($_GET['ss_portal'])) {
            $this->render_portal_page();
            exit;
        }
    }
    
    public function handle_portal_requests() {
        // Handle contract signing
        if (isset($_POST['ss_sign_contract'])) {
            $this->process_contract_signing();
        }
        
        // Handle payment processing
        if (isset($_POST['ss_process_payment'])) {
            $this->process_payment();
        }
    }
    
    /**
     * Render client portal shortcode
     */
    public static function render_client_portal($atts) {
        $atts = shortcode_atts(array(
            'type' => 'login', // login, booking, contract, payment, gallery
            'booking_id' => '',
            'hash' => ''
        ), $atts);
        
        // Validate access if booking_id and hash provided
        if ($atts['booking_id'] && $atts['hash']) {
            $valid_access = self::validate_portal_access($atts['booking_id'], $atts['hash']);
            if (!$valid_access) {
                return '<div class="ss-portal-error">Invalid access credentials.</div>';
            }
            
            return self::render_booking_portal($atts['booking_id'], $atts['type']);
        }
        
        // Default login form
        return self::render_portal_login();
    }
    
    /**
     * Validate portal access
     */
    private static function validate_portal_access($booking_id, $hash) {
        $booking = get_post($booking_id);
        if (!$booking || $booking->post_type !== 'ss_booking') {
            return false;
        }
        
        $booking_hash = get_post_meta($booking_id, '_ss_booking_hash', true);
        return hash_equals($booking_hash, $hash);
    }
    
    /**
     * Render portal login form
     */
    private static function render_portal_login() {
        ob_start();
        ?>
        <div class="ss-client-portal-login">
            <div class="ss-portal-header">
                <h2><?php _e('Client Portal Access', 'studiosnap'); ?></h2>
                <p><?php _e('Enter your booking information to access your session details, contract, and photos.', 'studiosnap'); ?></p>
            </div>
            
            <form id="ss-portal-login-form" class="ss-portal-form">
                <div class="ss-form-field">
                    <label for="client_email"><?php _e('Email Address', 'studiosnap'); ?></label>
                    <input type="email" id="client_email" name="client_email" required>
                </div>
                
                <div class="ss-form-field">
                    <label for="booking_reference"><?php _e('Booking Reference or Last Name', 'studiosnap'); ?></label>
                    <input type="text" id="booking_reference" name="booking_reference" required>
                </div>
                
                <button type="submit" class="ss-portal-btn">
                    <?php _e('Access Portal', 'studiosnap'); ?>
                </button>
                
                <div class="ss-portal-help">
                    <p><?php _e('Don\'t have your booking reference? Check your confirmation email or contact us.', 'studiosnap'); ?></p>
                </div>
            </form>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            $('#ss-portal-login-form').on('submit', function(e) {
                e.preventDefault();
                
                var email = $('#client_email').val();
                var reference = $('#booking_reference').val();
                
                // Simple client-side lookup
                $.post(studiosnap_portal.ajax_url, {
                    action: 'ss_portal_login',
                    nonce: studiosnap_portal.nonce,
                    email: email,
                    reference: reference
                }, function(response) {
                    if (response.success) {
                        window.location.href = response.data.portal_url;
                    } else {
                        alert(response.data || 'Invalid credentials. Please try again.');
                    }
                });
            });
        });
        </script>
        
        <?php
        return ob_get_clean();
    }
    
    /**
     * Render booking portal
     */
    private static function render_booking_portal($booking_id, $section = 'overview') {
        $booking = get_post($booking_id);
        $client_name = get_post_meta($booking_id, '_ss_client_name', true);
        $client_email = get_post_meta($booking_id, '_ss_client_email', true);
        $session_date = get_post_meta($booking_id, '_ss_session_date', true);
        $session_time = get_post_meta($booking_id, '_ss_session_start_time', true);
        $session_type = get_post_meta($booking_id, '_ss_session_type', true);
        $session_status = $booking->post_status;
        $total_price = get_post_meta($booking_id, '_ss_total_price', true);
        
        ob_start();
        ?>
        <div class="ss-client-portal">
            <div class="ss-portal-header">
                <h1><?php printf(__('Welcome, %s!', 'studiosnap'), esc_html($client_name)); ?></h1>
                <div class="ss-booking-status status-<?php echo esc_attr(str_replace('ss_', '', $session_status)); ?>">
                    <?php echo esc_html(self::get_status_label($session_status)); ?>
                </div>
            </div>
            
            <div class="ss-portal-navigation">
                <nav class="ss-portal-nav">
                    <a href="#overview" class="ss-nav-item <?php echo $section === 'overview' ? 'active' : ''; ?>">
                        <?php _e('Overview', 'studiosnap'); ?>
                    </a>
                    <a href="#contract" class="ss-nav-item <?php echo $section === 'contract' ? 'active' : ''; ?>">
                        <?php _e('Contract', 'studiosnap'); ?>
                    </a>
                    <a href="#payment" class="ss-nav-item <?php echo $section === 'payment' ? 'active' : ''; ?>">
                        <?php _e('Payment', 'studiosnap'); ?>
                    </a>
                    <?php if ($session_status === 'ss_completed') : ?>
                    <a href="#gallery" class="ss-nav-item <?php echo $section === 'gallery' ? 'active' : ''; ?>">
                        <?php _e('Photos', 'studiosnap'); ?>
                    </a>
                    <?php endif; ?>
                </nav>
            </div>
            
            <div class="ss-portal-content">
                <?php if ($section === 'overview') : ?>
                    <?php echo self::render_overview_section($booking_id); ?>
                <?php elseif ($section === 'contract') : ?>
                    <?php echo self::render_contract_section($booking_id); ?>
                <?php elseif ($section === 'payment') : ?>
                    <?php echo self::render_payment_section($booking_id); ?>
                <?php elseif ($section === 'gallery') : ?>
                    <?php echo self::render_gallery_section($booking_id); ?>
                <?php endif; ?>
            </div>
        </div>
        
        <style>
        .ss-client-portal {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        
        .ss-portal-header {
            text-align: center;
            margin-bottom: 30px;
            padding: 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
        }
        
        .ss-booking-status {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            margin-top: 10px;
        }
        
        .ss-booking-status.status-inquiry {
            background: #fff3cd;
            color: #856404;
        }
        
        .ss-booking-status.status-confirmed {
            background: #d4edda;
            color: #155724;
        }
        
        .ss-booking-status.status-completed {
            background: #cce7ff;
            color: #004085;
        }
        
        .ss-portal-navigation {
            margin-bottom: 30px;
        }
        
        .ss-portal-nav {
            display: flex;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .ss-nav-item {
            flex: 1;
            padding: 15px 20px;
            text-align: center;
            text-decoration: none;
            color: #666;
            border-right: 1px solid #eee;
            transition: all 0.3s ease;
        }
        
        .ss-nav-item:last-child {
            border-right: none;
        }
        
        .ss-nav-item:hover,
        .ss-nav-item.active {
            background: #007cba;
            color: white;
        }
        
        .ss-portal-content {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .ss-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        
        .ss-info-item {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 4px solid #007cba;
        }
        
        .ss-info-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        
        .ss-info-value {
            font-size: 16px;
            color: #333;
            font-weight: 500;
        }
        
        .ss-progress-steps {
            display: flex;
            justify-content: space-between;
            margin: 30px 0;
            position: relative;
        }
        
        .ss-progress-steps::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 2px;
            background: #eee;
            z-index: 1;
        }
        
        .ss-step {
            background: white;
            border: 2px solid #eee;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 2;
            font-weight: bold;
            color: #666;
        }
        
        .ss-step.completed {
            background: #28a745;
            border-color: #28a745;
            color: white;
        }
        
        .ss-step.current {
            background: #007cba;
            border-color: #007cba;
            color: white;
        }
        
        .ss-action-buttons {
            margin: 30px 0;
            text-align: center;
        }
        
        .ss-portal-btn {
            background: #007cba;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 0 10px;
            transition: background 0.3s ease;
        }
        
        .ss-portal-btn:hover {
            background: #005a87;
            color: white;
        }
        
        .ss-portal-btn.secondary {
            background: #6c757d;
        }
        
        .ss-portal-btn.secondary:hover {
            background: #545b62;
        }
        
        @media (max-width: 768px) {
            .ss-portal-nav {
                flex-direction: column;
            }
            
            .ss-nav-item {
                border-right: none;
                border-bottom: 1px solid #eee;
            }
            
            .ss-progress-steps {
                flex-direction: column;
                align-items: center;
            }
            
            .ss-progress-steps::before {
                display: none;
            }
        }
        </style>
        
        <?php
        return ob_get_clean();
    }
    
    /**
     * Render overview section
     */
    private static function render_overview_section($booking_id) {
        $booking = get_post($booking_id);
        $session_date = get_post_meta($booking_id, '_ss_session_date', true);
        $session_time = get_post_meta($booking_id, '_ss_session_start_time', true);
        $session_type = get_post_meta($booking_id, '_ss_session_type', true);
        $session_location = get_post_meta($booking_id, '_ss_session_location', true);
        $total_price = get_post_meta($booking_id, '_ss_total_price', true);
        $special_requests = get_post_meta($booking_id, '_ss_special_requests', true);
        
        $contract_signed = get_post_meta($booking_id, '_ss_contract_signed', true);
        $payment_completed = get_post_meta($booking_id, '_ss_payment_completed', true);
        
        ob_start();
        ?>
        <div class="ss-overview-section">
            <h2><?php _e('Session Overview', 'studiosnap'); ?></h2>
            
            <div class="ss-info-grid">
                <div class="ss-info-item">
                    <div class="ss-info-label"><?php _e('Session Type', 'studiosnap'); ?></div>
                    <div class="ss-info-value"><?php echo esc_html(ucfirst($session_type)); ?></div>
                </div>
                
                <div class="ss-info-item">
                    <div class="ss-info-label"><?php _e('Date & Time', 'studiosnap'); ?></div>
                    <div class="ss-info-value">
                        <?php echo date('F j, Y', strtotime($session_date)); ?><br>
                        <?php echo date('g:i A', strtotime($session_time)); ?>
                    </div>
                </div>
                
                <div class="ss-info-item">
                    <div class="ss-info-label"><?php _e('Location', 'studiosnap'); ?></div>
                    <div class="ss-info-value">
                        <?php echo $session_location === 'on_location' ? __('On Location', 'studiosnap') : __('Studio', 'studiosnap'); ?>
                    </div>
                </div>
                
                <div class="ss-info-item">
                    <div class="ss-info-label"><?php _e('Investment', 'studiosnap'); ?></div>
                    <div class="ss-info-value"><?php echo SS_Utilities::format_currency($total_price); ?></div>
                </div>
            </div>
            
            <?php if ($special_requests) : ?>
            <div class="ss-special-requests">
                <h3><?php _e('Special Requests', 'studiosnap'); ?></h3>
                <div class="ss-requests-content">
                    <?php echo wpautop(esc_html($special_requests)); ?>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="ss-booking-progress">
                <h3><?php _e('Booking Progress', 'studiosnap'); ?></h3>
                
                <div class="ss-progress-steps">
                    <div class="ss-step completed">1</div>
                    <div class="ss-step <?php echo $contract_signed ? 'completed' : ($booking->post_status === 'ss_confirmed' ? 'current' : ''); ?>">2</div>
                    <div class="ss-step <?php echo $payment_completed ? 'completed' : (($contract_signed && $booking->post_status === 'ss_confirmed') ? 'current' : ''); ?>">3</div>
                    <div class="ss-step <?php echo $booking->post_status === 'ss_completed' ? 'completed' : (($payment_completed && $booking->post_status === 'ss_confirmed') ? 'current' : ''); ?>">4</div>
                </div>
                
                <div class="ss-step-labels">
                    <div><?php _e('Booking Inquiry', 'studiosnap'); ?></div>
                    <div><?php _e('Contract Signed', 'studiosnap'); ?></div>
                    <div><?php _e('Payment Complete', 'studiosnap'); ?></div>
                    <div><?php _e('Session Complete', 'studiosnap'); ?></div>
                </div>
            </div>
            
            <div class="ss-next-steps">
                <h3><?php _e('Next Steps', 'studiosnap'); ?></h3>
                
                <?php if ($booking->post_status === 'ss_inquiry') : ?>
                    <p><?php _e('We\'re reviewing your booking request and will contact you within 24 hours to confirm availability.', 'studiosnap'); ?></p>
                <?php elseif ($booking->post_status === 'ss_confirmed' && !$contract_signed) : ?>
                    <p><?php _e('Your session is confirmed! Please sign your contract to secure your booking.', 'studiosnap'); ?></p>
                    <div class="ss-action-buttons">
                        <a href="#contract" class="ss-portal-btn"><?php _e('Sign Contract', 'studiosnap'); ?></a>
                    </div>
                <?php elseif ($contract_signed && !$payment_completed) : ?>
                    <p><?php _e('Contract signed! Please complete your session retainer to finalize your booking.', 'studiosnap'); ?></p>
                    <div class="ss-action-buttons">
                        <a href="#payment" class="ss-portal-btn"><?php _e('Make Payment', 'studiosnap'); ?></a>
                    </div>
                <?php elseif ($payment_completed && $booking->post_status === 'ss_confirmed') : ?>
                    <p><?php _e('All set! We\'ll send you a reminder 24 hours before your session. Looking forward to working with you!', 'studiosnap'); ?></p>
                <?php elseif ($booking->post_status === 'ss_completed') : ?>
                    <p><?php _e('Your session is complete! Your photos are being edited and will be available soon.', 'studiosnap'); ?></p>
                    <div class="ss-action-buttons">
                        <a href="#gallery" class="ss-portal-btn"><?php _e('View Photos', 'studiosnap'); ?></a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php
        return ob_get_clean();
    }
    
    /**
     * Render contract section
     */
    private static function render_contract_section($booking_id) {
        $contract_signed = get_post_meta($booking_id, '_ss_contract_signed', true);
        $contract_date = get_post_meta($booking_id, '_ss_contract_signed_date', true);
        
        ob_start();
        ?>
        <div class="ss-contract-section">
            <h2><?php _e('Photography Contract', 'studiosnap'); ?></h2>
            
            <?php if ($contract_signed) : ?>
                <div class="ss-contract-status signed">
                    <h3><?php _e('✓ Contract Signed', 'studiosnap'); ?></h3>
                    <p><?php printf(__('Signed on %s', 'studiosnap'), date('F j, Y', strtotime($contract_date))); ?></p>
                </div>
            <?php else : ?>
                <div class="ss-contract-content">
                    <div class="ss-contract-text">
                        <?php echo wpautop(self::get_contract_text($booking_id)); ?>
                    </div>
                    
                    <form id="ss-contract-form" method="post">
                        <?php wp_nonce_field('ss_sign_contract', 'contract_nonce'); ?>
                        <input type="hidden" name="booking_id" value="<?php echo esc_attr($booking_id); ?>">
                        
                        <div class="ss-signature-section">
                            <label>
                                <input type="checkbox" name="agree_terms" required>
                                <?php _e('I have read and agree to the terms and conditions above', 'studiosnap'); ?>
                            </label>
                        </div>
                        
                        <div class="ss-signature-section">
                            <label for="client_signature"><?php _e('Digital Signature (Type your full name)', 'studiosnap'); ?></label>
                            <input type="text" id="client_signature" name="client_signature" required>
                        </div>
                        
                        <div class="ss-action-buttons">
                            <button type="submit" name="ss_sign_contract" class="ss-portal-btn">
                                <?php _e('Sign Contract', 'studiosnap'); ?>
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
        
        <?php
        return ob_get_clean();
    }
    
    /**
     * Render payment section
     */
    private static function render_payment_section($booking_id) {
        $total_price = get_post_meta($booking_id, '_ss_total_price', true);
        $payment_completed = get_post_meta($booking_id, '_ss_payment_completed', true);
        $retainer_amount = $total_price * 0.5; // 50% retainer
        
        ob_start();
        ?>
        <div class="ss-payment-section">
            <h2><?php _e('Session Payment', 'studiosnap'); ?></h2>
            
            <?php if ($payment_completed) : ?>
                <div class="ss-payment-status completed">
                    <h3><?php _e('✓ Payment Complete', 'studiosnap'); ?></h3>
                    <p><?php _e('Thank you! Your session retainer has been received.', 'studiosnap'); ?></p>
                </div>
            <?php else : ?>
                <div class="ss-payment-details">
                    <div class="ss-payment-breakdown">
                        <h3><?php _e('Payment Breakdown', 'studiosnap'); ?></h3>
                        
                        <div class="ss-payment-item">
                            <span><?php _e('Session Total:', 'studiosnap'); ?></span>
                            <span><?php echo SS_Utilities::format_currency($total_price); ?></span>
                        </div>
                        
                        <div class="ss-payment-item">
                            <span><?php _e('Retainer (50%):', 'studiosnap'); ?></span>
                            <span><?php echo SS_Utilities::format_currency($retainer_amount); ?></span>
                        </div>
                        
                        <div class="ss-payment-item total">
                            <span><?php _e('Due Today:', 'studiosnap'); ?></span>
                            <span><?php echo SS_Utilities::format_currency($retainer_amount); ?></span>
                        </div>
                        
                        <div class="ss-payment-item">
                            <span><?php _e('Remaining Balance:', 'studiosnap'); ?></span>
                            <span><?php echo SS_Utilities::format_currency($total_price - $retainer_amount); ?></span>
                        </div>
                    </div>
                    
                    <div class="ss-payment-options">
                        <h3><?php _e('Payment Options', 'studiosnap'); ?></h3>
                        
                        <div class="ss-payment-method">
                            <h4><?php _e('Credit Card / PayPal', 'studiosnap'); ?></h4>
                            <p><?php _e('Secure online payment processing', 'studiosnap'); ?></p>
                            <button class="ss-portal-btn" onclick="openPaymentModal(<?php echo $booking_id; ?>, <?php echo $retainer_amount; ?>)">
                                <?php _e('Pay Online', 'studiosnap'); ?>
                            </button>
                        </div>
                        
                        <div class="ss-payment-method">
                            <h4><?php _e('Bank Transfer / Check', 'studiosnap'); ?></h4>
                            <p><?php _e('Contact us for payment instructions', 'studiosnap'); ?></p>
                            <a href="mailto:<?php echo get_option('admin_email'); ?>" class="ss-portal-btn secondary">
                                <?php _e('Contact for Instructions', 'studiosnap'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <?php
        return ob_get_clean();
    }
    
    /**
     * Render gallery section
     */
    private static function render_gallery_section($booking_id) {
        $gallery_id = get_post_meta($booking_id, '_ss_gallery_id', true);
        
        if (!$gallery_id) {
            return '<p>' . __('Your gallery is being prepared. You will receive an email when your photos are ready.', 'studiosnap') . '</p>';
        }
        
        return SS_Gallery_Display::render_client_gallery($gallery_id);
    }
    
    /**
     * Get status label
     */
    private static function get_status_label($status) {
        $labels = array(
            'ss_inquiry' => __('Inquiry Received', 'studiosnap'),
            'ss_confirmed' => __('Session Confirmed', 'studiosnap'),
            'ss_completed' => __('Session Complete', 'studiosnap'),
            'ss_cancelled' => __('Cancelled', 'studiosnap')
        );
        
        return isset($labels[$status]) ? $labels[$status] : $status;
    }
    
    /**
     * Get contract text
     */
    private static function get_contract_text($booking_id) {
        $client_name = get_post_meta($booking_id, '_ss_client_name', true);
        $session_date = get_post_meta($booking_id, '_ss_session_date', true);
        $studio_name = get_bloginfo('name');
        
        return sprintf(__('
<h3>Photography Services Agreement</h3>

<p>This agreement is between %s ("%s") and %s ("Client") for photography services scheduled for %s.</p>

<h4>1. Services</h4>
<p>%s agrees to provide professional photography services as described in the booking details. This includes the photography session and basic image editing.</p>

<h4>2. Payment Terms</h4>
<p>A 50%% retainer is required to secure the booking. The remaining balance is due on the day of the session. All payments are non-refundable except as outlined in the cancellation policy.</p>

<h4>3. Cancellation Policy</h4>
<p>Client may cancel up to 48 hours before the session for a full refund of the retainer. Cancellations within 48 hours forfeit 50%% of the retainer. No-shows forfeit the entire retainer.</p>

<h4>4. Copyright and Usage</h4>
<p>%s retains copyright to all images. Client receives a license for personal use. Commercial use requires written permission and may incur additional fees.</p>

<h4>5. Image Delivery</h4>
<p>Edited images will be delivered within 2-3 weeks via online gallery. Client has 30 days to download images before they are archived.</p>

<h4>6. Liability</h4>
<p>%s\'s liability is limited to the amount paid for services. We are not responsible for images lost due to equipment failure or other circumstances beyond our control.</p>

<p>By signing below, both parties agree to the terms and conditions outlined in this agreement.</p>
        ', 'studiosnap'), $studio_name, $studio_name, $client_name, date('F j, Y', strtotime($session_date)), $studio_name, $studio_name, $studio_name);
    }
    
    /**
     * Process contract signing
     */
    private function process_contract_signing() {
        if (!wp_verify_nonce($_POST['contract_nonce'], 'ss_sign_contract')) {
            return;
        }
        
        $booking_id = intval($_POST['booking_id']);
        $signature = sanitize_text_field($_POST['client_signature']);
        $agree_terms = isset($_POST['agree_terms']);
        
        if (!$agree_terms || !$signature) {
            wp_die(__('Please complete all required fields.', 'studiosnap'));
        }
        
        // Save contract signature
        update_post_meta($booking_id, '_ss_contract_signed', true);
        update_post_meta($booking_id, '_ss_contract_signed_date', current_time('mysql'));
        update_post_meta($booking_id, '_ss_contract_signature', $signature);
        update_post_meta($booking_id, '_ss_contract_ip', $_SERVER['REMOTE_ADDR']);
        
        // Send confirmation email
        SS_Email_Handler::send_contract_signed_confirmation($booking_id);
        
        wp_redirect(add_query_arg('contract_signed', '1', wp_get_referer()));
        exit;
    }
    
    /**
     * Check if current page is portal page
     */
    private function is_portal_page() {
        return (isset($_GET['ss_portal']) || 
                has_shortcode(get_post()->post_content ?? '', 'studiosnap_client_portal') ||
                strpos($_SERVER['REQUEST_URI'], 'client-portal') !== false);
    }
}