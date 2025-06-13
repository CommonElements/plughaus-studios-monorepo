<?php
/**
 * Shortcodes for Knot4
 * 
 * @package Knot4
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Knot4_Shortcodes {
    
    /**
     * Initialize shortcodes
     */
    public static function init() {
        // Donation shortcodes
        add_shortcode('knot4_donation_form', array(__CLASS__, 'donation_form'));
        add_shortcode('knot4_donation_button', array(__CLASS__, 'donation_button'));
        add_shortcode('knot4_donation_total', array(__CLASS__, 'donation_total'));
        add_shortcode('knot4_donation_goal', array(__CLASS__, 'donation_goal'));
        
        // Campaign shortcodes
        add_shortcode('knot4_campaign_form', array(__CLASS__, 'campaign_form'));
        add_shortcode('knot4_campaign_progress', array(__CLASS__, 'campaign_progress'));
        add_shortcode('knot4_campaigns', array(__CLASS__, 'campaigns_list'));
        
        // Event shortcodes
        add_shortcode('knot4_events', array(__CLASS__, 'events_list'));
        add_shortcode('knot4_event_registration', array(__CLASS__, 'event_registration'));
        add_shortcode('knot4_event_calendar', array(__CLASS__, 'event_calendar'));
        
        // Donor/CRM shortcodes
        add_shortcode('knot4_donor_portal', array(__CLASS__, 'donor_portal'));
        add_shortcode('knot4_donor_login', array(__CLASS__, 'donor_login'));
        add_shortcode('knot4_donor_count', array(__CLASS__, 'donor_count'));
        add_shortcode('knot4_top_donors', array(__CLASS__, 'top_donors'));
        
        // Form shortcodes
        add_shortcode('knot4_volunteer_form', array(__CLASS__, 'volunteer_form'));
        add_shortcode('knot4_contact_form', array(__CLASS__, 'contact_form'));
        add_shortcode('knot4_newsletter_signup', array(__CLASS__, 'newsletter_signup'));
        
        // Display shortcodes
        add_shortcode('knot4_organization_info', array(__CLASS__, 'organization_info'));
        add_shortcode('knot4_stats', array(__CLASS__, 'stats_display'));
    }
    
    /**
     * Donation form shortcode
     */
    public static function donation_form($atts) {
        $atts = shortcode_atts(array(
            'title' => __('Make a Donation', 'knot4'),
            'suggested_amounts' => '25,50,100,250',
            'allow_custom' => 'yes',
            'min_amount' => '5',
            'max_amount' => '',
            'currency' => 'USD',
            'show_frequency' => 'yes',
            'show_dedication' => 'yes',
            'show_address' => 'no',
            'show_phone' => 'no',
            'show_cover_fees' => 'yes',
            'show_newsletter' => 'yes',
            'campaign_id' => '',
            'fund_designation' => '',
            'style' => 'default',
            'button_text' => __('Donate Now', 'knot4'),
            'thank_you_page' => '',
            'form_id' => '',
        ), $atts, 'knot4_donation_form');
        
        // Generate unique form ID if not provided
        if (empty($atts['form_id'])) {
            $atts['form_id'] = 'knot4-donation-' . uniqid();
        }
        
        $suggested_amounts = array_map('trim', explode(',', $atts['suggested_amounts']));
        
        ob_start();
        ?>
        <div class="knot4-donation-form-container knot4-style-<?php echo esc_attr($atts['style']); ?>">
            <form id="<?php echo esc_attr($atts['form_id']); ?>" class="knot4-donation-form" method="post">
                <?php wp_nonce_field('knot4_public_nonce', 'nonce'); ?>
                
                <?php if (!empty($atts['title'])): ?>
                <h3 class="knot4-form-title"><?php echo esc_html($atts['title']); ?></h3>
                <?php endif; ?>
                
                <!-- Amount Selection -->
                <div class="knot4-form-section knot4-amount-section">
                    <label class="knot4-label"><?php _e('Donation Amount', 'knot4'); ?> <span class="required">*</span></label>
                    
                    <div class="knot4-amount-buttons">
                        <?php foreach ($suggested_amounts as $amount): ?>
                            <button type="button" class="knot4-amount-btn" data-amount="<?php echo esc_attr($amount); ?>">
                                <?php echo Knot4_Utilities::format_currency($amount, $atts['currency']); ?>
                            </button>
                        <?php endforeach; ?>
                        
                        <?php if ($atts['allow_custom'] === 'yes'): ?>
                        <button type="button" class="knot4-amount-btn knot4-custom-btn" data-amount="custom">
                            <?php _e('Other', 'knot4'); ?>
                        </button>
                        <?php endif; ?>
                    </div>
                    
                    <div class="knot4-custom-amount" style="display: none;">
                        <label for="custom_amount"><?php _e('Enter Amount', 'knot4'); ?></label>
                        <input type="number" id="custom_amount" name="custom_amount" 
                               min="<?php echo esc_attr($atts['min_amount']); ?>" 
                               <?php echo !empty($atts['max_amount']) ? 'max="' . esc_attr($atts['max_amount']) . '"' : ''; ?>
                               step="0.01" placeholder="0.00">
                    </div>
                    
                    <input type="hidden" id="donation_amount" name="amount" required>
                </div>
                
                <!-- Frequency Selection -->
                <?php if ($atts['show_frequency'] === 'yes'): ?>
                <div class="knot4-form-section knot4-frequency-section">
                    <label class="knot4-label"><?php _e('Frequency', 'knot4'); ?></label>
                    <div class="knot4-frequency-options">
                        <label class="knot4-radio-label">
                            <input type="radio" name="frequency" value="once" checked>
                            <span><?php _e('One-time', 'knot4'); ?></span>
                        </label>
                        <label class="knot4-radio-label">
                            <input type="radio" name="frequency" value="monthly">
                            <span><?php _e('Monthly', 'knot4'); ?></span>
                        </label>
                        <?php if (Knot4_Utilities::is_pro()): ?>
                        <label class="knot4-radio-label">
                            <input type="radio" name="frequency" value="quarterly">
                            <span><?php _e('Quarterly', 'knot4'); ?></span>
                        </label>
                        <label class="knot4-radio-label">
                            <input type="radio" name="frequency" value="annually">
                            <span><?php _e('Annually', 'knot4'); ?></span>
                        </label>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Donor Information -->
                <div class="knot4-form-section knot4-donor-section">
                    <h4><?php _e('Your Information', 'knot4'); ?></h4>
                    
                    <div class="knot4-form-row">
                        <div class="knot4-form-col">
                            <label for="donor_first_name"><?php _e('First Name', 'knot4'); ?> <span class="required">*</span></label>
                            <input type="text" id="donor_first_name" name="donor_first_name" required>
                        </div>
                        <div class="knot4-form-col">
                            <label for="donor_last_name"><?php _e('Last Name', 'knot4'); ?> <span class="required">*</span></label>
                            <input type="text" id="donor_last_name" name="donor_last_name" required>
                        </div>
                    </div>
                    
                    <div class="knot4-form-row">
                        <div class="knot4-form-col">
                            <label for="donor_email"><?php _e('Email Address', 'knot4'); ?> <span class="required">*</span></label>
                            <input type="email" id="donor_email" name="donor_email" required>
                        </div>
                        <?php if ($atts['show_phone'] === 'yes'): ?>
                        <div class="knot4-form-col">
                            <label for="donor_phone"><?php _e('Phone Number', 'knot4'); ?></label>
                            <input type="tel" id="donor_phone" name="donor_phone">
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($atts['show_address'] === 'yes'): ?>
                    <div class="knot4-form-row">
                        <div class="knot4-form-col knot4-full-width">
                            <label for="donor_address"><?php _e('Address', 'knot4'); ?></label>
                            <textarea id="donor_address" name="donor_address" rows="3"></textarea>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Dedication Section -->
                <?php if ($atts['show_dedication'] === 'yes'): ?>
                <div class="knot4-form-section knot4-dedication-section">
                    <h4><?php _e('Dedication (Optional)', 'knot4'); ?></h4>
                    
                    <label class="knot4-checkbox-label">
                        <input type="checkbox" id="show_dedication_fields" name="show_dedication">
                        <span><?php _e('Make this donation in honor or memory of someone', 'knot4'); ?></span>
                    </label>
                    
                    <div class="knot4-dedication-fields" style="display: none;">
                        <div class="knot4-form-row">
                            <div class="knot4-form-col">
                                <label><?php _e('Dedication Type', 'knot4'); ?></label>
                                <select name="dedication_type">
                                    <option value=""><?php _e('Select Type', 'knot4'); ?></option>
                                    <option value="honor"><?php _e('In Honor Of', 'knot4'); ?></option>
                                    <option value="memory"><?php _e('In Memory Of', 'knot4'); ?></option>
                                </select>
                            </div>
                            <div class="knot4-form-col">
                                <label for="dedication_name"><?php _e('Name', 'knot4'); ?></label>
                                <input type="text" id="dedication_name" name="dedication_name">
                            </div>
                        </div>
                        
                        <div class="knot4-form-row">
                            <div class="knot4-form-col knot4-full-width">
                                <label for="dedication_message"><?php _e('Message (Optional)', 'knot4'); ?></label>
                                <textarea id="dedication_message" name="dedication_message" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Additional Options -->
                <div class="knot4-form-section knot4-options-section">
                    <?php if ($atts['show_cover_fees'] === 'yes'): ?>
                    <label class="knot4-checkbox-label">
                        <input type="checkbox" name="cover_fees" id="cover_fees">
                        <span><?php _e('Cover processing fees so 100% of my donation goes to the cause', 'knot4'); ?></span>
                        <small class="fee-amount" style="display: none;"></small>
                    </label>
                    <?php endif; ?>
                    
                    <?php if ($atts['show_newsletter'] === 'yes'): ?>
                    <label class="knot4-checkbox-label">
                        <input type="checkbox" name="opt_in_newsletter" id="opt_in_newsletter">
                        <span><?php _e('Sign me up for your newsletter', 'knot4'); ?></span>
                    </label>
                    <?php endif; ?>
                    
                    <label class="knot4-checkbox-label">
                        <input type="checkbox" name="is_anonymous" id="is_anonymous">
                        <span><?php _e('Make this donation anonymous', 'knot4'); ?></span>
                    </label>
                </div>
                
                <!-- Hidden Fields -->
                <input type="hidden" name="action" value="knot4_submit_donation">
                <input type="hidden" name="form_id" value="<?php echo esc_attr($atts['form_id']); ?>">
                <input type="hidden" name="campaign_id" value="<?php echo esc_attr($atts['campaign_id']); ?>">
                <input type="hidden" name="fund_designation" value="<?php echo esc_attr($atts['fund_designation']); ?>">
                <input type="hidden" name="payment_method" value="stripe">
                
                <!-- Submit Button -->
                <div class="knot4-form-section knot4-submit-section">
                    <button type="submit" class="knot4-submit-btn knot4-btn-primary">
                        <span class="btn-text"><?php echo esc_html($atts['button_text']); ?></span>
                        <span class="btn-spinner" style="display: none;">
                            <span class="spinner"></span>
                            <?php _e('Processing...', 'knot4'); ?>
                        </span>
                    </button>
                </div>
                
                <div class="knot4-form-messages"></div>
            </form>
        </div>
        
        <?php if (!Knot4_Utilities::is_pro()): ?>
        <div class="knot4-powered-by">
            <small><?php printf(__('Powered by %s', 'knot4'), '<a href="https://plughausstudios.com/knot4/" target="_blank">Knot4</a>'); ?></small>
        </div>
        <?php endif; ?>
        <?php
        
        return ob_get_clean();
    }
    
    /**
     * Simple donation button shortcode
     */
    public static function donation_button($atts) {
        $atts = shortcode_atts(array(
            'text' => __('Donate Now', 'knot4'),
            'amount' => '',
            'url' => '',
            'style' => 'primary',
            'size' => 'medium',
            'new_window' => 'no',
        ), $atts, 'knot4_donation_button');
        
        $url = !empty($atts['url']) ? $atts['url'] : home_url('/donate/');
        $target = $atts['new_window'] === 'yes' ? '_blank' : '_self';
        
        return sprintf(
            '<a href="%s" target="%s" class="knot4-donate-btn knot4-btn-%s knot4-size-%s">%s</a>',
            esc_url($url),
            esc_attr($target),
            esc_attr($atts['style']),
            esc_attr($atts['size']),
            esc_html($atts['text'])
        );
    }
    
    /**
     * Donation total display shortcode
     */
    public static function donation_total($atts) {
        $atts = shortcode_atts(array(
            'period' => 'all', // all, year, month
            'campaign_id' => '',
            'format' => 'currency', // currency, number
            'show_label' => 'yes',
            'label' => __('Total Raised', 'knot4'),
        ), $atts, 'knot4_donation_total');
        
        global $wpdb;
        
        $sql = "SELECT SUM(amount) FROM {$wpdb->prefix}knot4_donations WHERE status = 'completed'";
        $params = array();
        
        if (!empty($atts['campaign_id'])) {
            $sql .= " AND campaign_id = %d";
            $params[] = intval($atts['campaign_id']);
        }
        
        switch ($atts['period']) {
            case 'year':
                $sql .= " AND YEAR(created_at) = YEAR(NOW())";
                break;
            case 'month':
                $sql .= " AND YEAR(created_at) = YEAR(NOW()) AND MONTH(created_at) = MONTH(NOW())";
                break;
        }
        
        if (!empty($params)) {
            $total = $wpdb->get_var($wpdb->prepare($sql, $params));
        } else {
            $total = $wpdb->get_var($sql);
        }
        
        $total = floatval($total);
        
        if ($atts['format'] === 'currency') {
            $formatted_total = Knot4_Utilities::format_currency($total);
        } else {
            $formatted_total = number_format($total, 2);
        }
        
        $output = '<span class="knot4-donation-total" data-amount="' . esc_attr($total) . '">';
        
        if ($atts['show_label'] === 'yes') {
            $output .= '<span class="knot4-total-label">' . esc_html($atts['label']) . ': </span>';
        }
        
        $output .= '<span class="knot4-total-amount">' . $formatted_total . '</span>';
        $output .= '</span>';
        
        return $output;
    }
    
    /**
     * Events list shortcode
     */
    public static function events_list($atts) {
        $atts = shortcode_atts(array(
            'limit' => '6',
            'category' => '',
            'show_past' => 'no',
            'show_featured' => 'all', // all, featured, non-featured
            'layout' => 'grid', // grid, list, calendar
            'show_excerpt' => 'yes',
            'show_date' => 'yes',
            'show_location' => 'yes',
            'show_price' => 'yes',
            'show_registration' => 'yes',
            'orderby' => 'event_date',
            'order' => 'ASC',
        ), $atts, 'knot4_events');
        
        $query_args = array(
            'post_type' => 'knot4_event',
            'post_status' => 'publish',
            'posts_per_page' => intval($atts['limit']),
            'orderby' => 'meta_value',
            'meta_key' => '_knot4_event_date',
            'order' => $atts['order'],
        );
        
        // Category filter
        if (!empty($atts['category'])) {
            $query_args['tax_query'] = array(
                array(
                    'taxonomy' => 'knot4_event_category',
                    'field' => 'slug',
                    'terms' => $atts['category'],
                )
            );
        }
        
        // Date filter
        if ($atts['show_past'] === 'no') {
            $query_args['meta_query'] = array(
                array(
                    'key' => '_knot4_event_date',
                    'value' => date('Y-m-d'),
                    'compare' => '>=',
                    'type' => 'DATE',
                )
            );
        }
        
        // Featured filter
        if ($atts['show_featured'] === 'featured') {
            $query_args['meta_query'][] = array(
                'key' => '_knot4_event_featured',
                'value' => '1',
                'compare' => '=',
            );
        } elseif ($atts['show_featured'] === 'non-featured') {
            $query_args['meta_query'][] = array(
                'key' => '_knot4_event_featured',
                'value' => '1',
                'compare' => '!=',
            );
        }
        
        $events = new WP_Query($query_args);
        
        if (!$events->have_posts()) {
            return '<p class="knot4-no-events">' . __('No upcoming events found.', 'knot4') . '</p>';
        }
        
        ob_start();
        ?>
        <div class="knot4-events-container knot4-layout-<?php echo esc_attr($atts['layout']); ?>">
            <?php if ($atts['layout'] === 'grid'): ?>
            <div class="knot4-events-grid">
                <?php while ($events->have_posts()): $events->the_post(); ?>
                    <?php echo self::render_event_card($atts); ?>
                <?php endwhile; ?>
            </div>
            
            <?php elseif ($atts['layout'] === 'list'): ?>
            <div class="knot4-events-list">
                <?php while ($events->have_posts()): $events->the_post(); ?>
                    <?php echo self::render_event_list_item($atts); ?>
                <?php endwhile; ?>
            </div>
            
            <?php elseif ($atts['layout'] === 'calendar'): ?>
                <?php echo self::render_event_calendar($events, $atts); ?>
            <?php endif; ?>
        </div>
        <?php
        
        wp_reset_postdata();
        return ob_get_clean();
    }
    
    /**
     * Donor portal shortcode
     */
    public static function donor_portal($atts) {
        $atts = shortcode_atts(array(
            'show_login' => 'yes',
            'show_registration' => 'yes',
            'redirect_after_login' => '',
        ), $atts, 'knot4_donor_portal');
        
        if (!is_user_logged_in()) {
            return self::donor_login($atts);
        }
        
        $current_user = wp_get_current_user();
        
        // Check if user is a donor
        $donor_posts = get_posts(array(
            'post_type' => 'knot4_donor',
            'meta_key' => '_knot4_donor_email',
            'meta_value' => $current_user->user_email,
            'posts_per_page' => 1,
        ));
        
        if (empty($donor_posts)) {
            return '<div class="knot4-portal-error">' . 
                   __('No donor record found for your account. Please contact us for assistance.', 'knot4') . 
                   '</div>';
        }
        
        $donor_id = $donor_posts[0]->ID;
        
        ob_start();
        ?>
        <div class="knot4-donor-portal">
            <div class="knot4-portal-header">
                <h2><?php printf(__('Welcome, %s!', 'knot4'), esc_html($current_user->display_name)); ?></h2>
                <p><?php _e('Manage your donations, view your giving history, and update your information.', 'knot4'); ?></p>
            </div>
            
            <div class="knot4-portal-tabs">
                <nav class="knot4-tab-nav">
                    <button class="knot4-tab-btn active" data-tab="overview"><?php _e('Overview', 'knot4'); ?></button>
                    <button class="knot4-tab-btn" data-tab="donations"><?php _e('Donations', 'knot4'); ?></button>
                    <button class="knot4-tab-btn" data-tab="events"><?php _e('Events', 'knot4'); ?></button>
                    <button class="knot4-tab-btn" data-tab="profile"><?php _e('Profile', 'knot4'); ?></button>
                    <?php if (Knot4_Utilities::is_pro()): ?>
                    <button class="knot4-tab-btn" data-tab="recurring"><?php _e('Recurring', 'knot4'); ?></button>
                    <button class="knot4-tab-btn" data-tab="documents"><?php _e('Documents', 'knot4'); ?></button>
                    <?php endif; ?>
                </nav>
                
                <div class="knot4-tab-content">
                    <!-- Overview Tab -->
                    <div class="knot4-tab-panel active" id="overview">
                        <?php echo self::render_donor_overview($donor_id); ?>
                    </div>
                    
                    <!-- Donations Tab -->
                    <div class="knot4-tab-panel" id="donations">
                        <?php echo self::render_donor_donations($donor_id); ?>
                    </div>
                    
                    <!-- Events Tab -->
                    <div class="knot4-tab-panel" id="events">
                        <?php echo self::render_donor_events($current_user->user_email); ?>
                    </div>
                    
                    <!-- Profile Tab -->
                    <div class="knot4-tab-panel" id="profile">
                        <?php echo self::render_donor_profile($donor_id); ?>
                    </div>
                    
                    <?php if (Knot4_Utilities::is_pro()): ?>
                    <!-- Recurring Donations Tab -->
                    <div class="knot4-tab-panel" id="recurring">
                        <?php echo self::render_donor_recurring($donor_id); ?>
                    </div>
                    
                    <!-- Documents Tab -->
                    <div class="knot4-tab-panel" id="documents">
                        <?php echo self::render_donor_documents($donor_id); ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="knot4-portal-footer">
                <p><a href="<?php echo wp_logout_url(); ?>"><?php _e('Logout', 'knot4'); ?></a></p>
            </div>
        </div>
        <?php
        
        return ob_get_clean();
    }
    
    /**
     * Donor login form shortcode
     */
    public static function donor_login($atts) {
        $atts = shortcode_atts(array(
            'redirect' => '',
            'show_register' => 'yes',
            'title' => __('Donor Login', 'knot4'),
        ), $atts, 'knot4_donor_login');
        
        if (is_user_logged_in()) {
            return '<p>' . __('You are already logged in.', 'knot4') . '</p>';
        }
        
        $redirect_url = !empty($atts['redirect']) ? $atts['redirect'] : get_permalink();
        
        ob_start();
        ?>
        <div class="knot4-login-form-container">
            <?php if (!empty($atts['title'])): ?>
            <h3><?php echo esc_html($atts['title']); ?></h3>
            <?php endif; ?>
            
            <?php wp_login_form(array(
                'redirect' => $redirect_url,
                'id_username' => 'knot4_username',
                'id_password' => 'knot4_password',
                'label_username' => __('Email or Username', 'knot4'),
                'label_password' => __('Password', 'knot4'),
                'label_remember' => __('Remember Me', 'knot4'),
                'label_log_in' => __('Login', 'knot4'),
            )); ?>
            
            <?php if ($atts['show_register'] === 'yes'): ?>
            <p class="knot4-register-link">
                <?php _e("Don't have an account?", 'knot4'); ?>
                <a href="<?php echo wp_registration_url(); ?>"><?php _e('Register here', 'knot4'); ?></a>
            </p>
            <?php endif; ?>
            
            <p class="knot4-lost-password">
                <a href="<?php echo wp_lostpassword_url(); ?>"><?php _e('Lost your password?', 'knot4'); ?></a>
            </p>
        </div>
        <?php
        
        return ob_get_clean();
    }
    
    /**
     * Volunteer form shortcode
     */
    public static function volunteer_form($atts) {
        $atts = shortcode_atts(array(
            'title' => __('Volunteer With Us', 'knot4'),
            'show_interests' => 'yes',
            'show_availability' => 'yes',
            'show_experience' => 'yes',
            'button_text' => __('Submit Application', 'knot4'),
            'success_message' => __('Thank you for your interest in volunteering! We will be in touch soon.', 'knot4'),
        ), $atts, 'knot4_volunteer_form');
        
        $form_id = 'knot4-volunteer-' . uniqid();
        
        ob_start();
        ?>
        <div class="knot4-volunteer-form-container">
            <form id="<?php echo esc_attr($form_id); ?>" class="knot4-volunteer-form" method="post">
                <?php wp_nonce_field('knot4_public_nonce', 'nonce'); ?>
                
                <?php if (!empty($atts['title'])): ?>
                <h3 class="knot4-form-title"><?php echo esc_html($atts['title']); ?></h3>
                <?php endif; ?>
                
                <!-- Personal Information -->
                <div class="knot4-form-section">
                    <h4><?php _e('Personal Information', 'knot4'); ?></h4>
                    
                    <div class="knot4-form-row">
                        <div class="knot4-form-col">
                            <label for="volunteer_first_name"><?php _e('First Name', 'knot4'); ?> <span class="required">*</span></label>
                            <input type="text" id="volunteer_first_name" name="volunteer_first_name" required>
                        </div>
                        <div class="knot4-form-col">
                            <label for="volunteer_last_name"><?php _e('Last Name', 'knot4'); ?> <span class="required">*</span></label>
                            <input type="text" id="volunteer_last_name" name="volunteer_last_name" required>
                        </div>
                    </div>
                    
                    <div class="knot4-form-row">
                        <div class="knot4-form-col">
                            <label for="volunteer_email"><?php _e('Email Address', 'knot4'); ?> <span class="required">*</span></label>
                            <input type="email" id="volunteer_email" name="volunteer_email" required>
                        </div>
                        <div class="knot4-form-col">
                            <label for="volunteer_phone"><?php _e('Phone Number', 'knot4'); ?></label>
                            <input type="tel" id="volunteer_phone" name="volunteer_phone">
                        </div>
                    </div>
                </div>
                
                <!-- Volunteer Interests -->
                <?php if ($atts['show_interests'] === 'yes'): ?>
                <div class="knot4-form-section">
                    <h4><?php _e('Areas of Interest', 'knot4'); ?></h4>
                    <div class="knot4-checkbox-group">
                        <label class="knot4-checkbox-label">
                            <input type="checkbox" name="volunteer_interests[]" value="events">
                            <span><?php _e('Event Assistance', 'knot4'); ?></span>
                        </label>
                        <label class="knot4-checkbox-label">
                            <input type="checkbox" name="volunteer_interests[]" value="fundraising">
                            <span><?php _e('Fundraising', 'knot4'); ?></span>
                        </label>
                        <label class="knot4-checkbox-label">
                            <input type="checkbox" name="volunteer_interests[]" value="admin">
                            <span><?php _e('Administrative Support', 'knot4'); ?></span>
                        </label>
                        <label class="knot4-checkbox-label">
                            <input type="checkbox" name="volunteer_interests[]" value="marketing">
                            <span><?php _e('Marketing & Communications', 'knot4'); ?></span>
                        </label>
                        <label class="knot4-checkbox-label">
                            <input type="checkbox" name="volunteer_interests[]" value="other">
                            <span><?php _e('Other', 'knot4'); ?></span>
                        </label>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Availability -->
                <?php if ($atts['show_availability'] === 'yes'): ?>
                <div class="knot4-form-section">
                    <label for="volunteer_availability"><?php _e('Availability', 'knot4'); ?></label>
                    <textarea id="volunteer_availability" name="volunteer_availability" rows="3" 
                              placeholder="<?php _e('Please describe your availability (days, times, frequency)', 'knot4'); ?>"></textarea>
                </div>
                <?php endif; ?>
                
                <!-- Experience -->
                <?php if ($atts['show_experience'] === 'yes'): ?>
                <div class="knot4-form-section">
                    <label for="volunteer_experience"><?php _e('Relevant Experience', 'knot4'); ?></label>
                    <textarea id="volunteer_experience" name="volunteer_experience" rows="3" 
                              placeholder="<?php _e('Please describe any relevant volunteer experience or skills', 'knot4'); ?>"></textarea>
                </div>
                <?php endif; ?>
                
                <!-- Additional Message -->
                <div class="knot4-form-section">
                    <label for="volunteer_message"><?php _e('Additional Information', 'knot4'); ?></label>
                    <textarea id="volunteer_message" name="volunteer_message" rows="4" 
                              placeholder="<?php _e('Anything else you would like us to know?', 'knot4'); ?>"></textarea>
                </div>
                
                <!-- Hidden Fields -->
                <input type="hidden" name="action" value="knot4_submit_volunteer">
                
                <!-- Submit Button -->
                <div class="knot4-form-section knot4-submit-section">
                    <button type="submit" class="knot4-submit-btn knot4-btn-primary">
                        <span class="btn-text"><?php echo esc_html($atts['button_text']); ?></span>
                        <span class="btn-spinner" style="display: none;">
                            <span class="spinner"></span>
                            <?php _e('Submitting...', 'knot4'); ?>
                        </span>
                    </button>
                </div>
                
                <div class="knot4-form-messages"></div>
            </form>
        </div>
        <?php
        
        return ob_get_clean();
    }
    
    /**
     * Organization info shortcode
     */
    public static function organization_info($atts) {
        $atts = shortcode_atts(array(
            'show' => 'name,email,phone,address', // comma-separated list
            'format' => 'list', // list, inline, card
        ), $atts, 'knot4_organization_info');
        
        $org_settings = Knot4_Utilities::get_organization_settings();
        $show_items = array_map('trim', explode(',', $atts['show']));
        
        ob_start();
        ?>
        <div class="knot4-organization-info knot4-format-<?php echo esc_attr($atts['format']); ?>">
            <?php if ($atts['format'] === 'list'): ?>
            <ul class="knot4-org-list">
                <?php foreach ($show_items as $item): ?>
                    <?php echo self::render_org_info_item($item, $org_settings, 'list'); ?>
                <?php endforeach; ?>
            </ul>
            
            <?php elseif ($atts['format'] === 'inline'): ?>
            <div class="knot4-org-inline">
                <?php foreach ($show_items as $item): ?>
                    <?php echo self::render_org_info_item($item, $org_settings, 'inline'); ?>
                <?php endforeach; ?>
            </div>
            
            <?php else: // card format ?>
            <div class="knot4-org-card">
                <?php foreach ($show_items as $item): ?>
                    <?php echo self::render_org_info_item($item, $org_settings, 'card'); ?>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php
        
        return ob_get_clean();
    }
    
    /**
     * Helper method to render organization info item
     */
    private static function render_org_info_item($item, $org_settings, $format) {
        $output = '';
        
        switch ($item) {
            case 'name':
                if (!empty($org_settings['organization_name'])) {
                    $label = __('Organization', 'knot4');
                    $value = $org_settings['organization_name'];
                }
                break;
            case 'email':
                if (!empty($org_settings['organization_email'])) {
                    $label = __('Email', 'knot4');
                    $value = '<a href="mailto:' . esc_attr($org_settings['organization_email']) . '">' . 
                            esc_html($org_settings['organization_email']) . '</a>';
                }
                break;
            case 'phone':
                if (!empty($org_settings['organization_phone'])) {
                    $label = __('Phone', 'knot4');
                    $value = '<a href="tel:' . esc_attr($org_settings['organization_phone']) . '">' . 
                            esc_html(Knot4_Utilities::format_phone($org_settings['organization_phone'])) . '</a>';
                }
                break;
            case 'address':
                if (!empty($org_settings['organization_address'])) {
                    $label = __('Address', 'knot4');
                    $value = nl2br(esc_html($org_settings['organization_address']));
                }
                break;
        }
        
        if (!empty($value)) {
            if ($format === 'list') {
                $output = '<li class="knot4-org-item knot4-org-' . esc_attr($item) . '">';
                $output .= '<strong>' . esc_html($label) . ':</strong> ' . $value;
                $output .= '</li>';
            } elseif ($format === 'inline') {
                $output = '<span class="knot4-org-item knot4-org-' . esc_attr($item) . '">';
                $output .= $value;
                $output .= '</span>';
            } else { // card
                $output = '<div class="knot4-org-item knot4-org-' . esc_attr($item) . '">';
                $output .= '<div class="knot4-org-label">' . esc_html($label) . '</div>';
                $output .= '<div class="knot4-org-value">' . $value . '</div>';
                $output .= '</div>';
            }
        }
        
        return $output;
    }
    
    /**
     * Helper methods for donor portal tabs
     */
    private static function render_donor_overview($donor_id) {
        global $wpdb;
        
        // Get donor stats
        $total_donated = $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(amount) FROM {$wpdb->prefix}knot4_donations WHERE donor_id = %d AND status = 'completed'",
            $donor_id
        )) ?: 0;
        
        $donation_count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}knot4_donations WHERE donor_id = %d AND status = 'completed'",
            $donor_id
        )) ?: 0;
        
        $first_donation = $wpdb->get_var($wpdb->prepare(
            "SELECT created_at FROM {$wpdb->prefix}knot4_donations WHERE donor_id = %d AND status = 'completed' ORDER BY created_at ASC LIMIT 1",
            $donor_id
        ));
        
        $last_donation = $wpdb->get_var($wpdb->prepare(
            "SELECT created_at FROM {$wpdb->prefix}knot4_donations WHERE donor_id = %d AND status = 'completed' ORDER BY created_at DESC LIMIT 1",
            $donor_id
        ));
        
        ob_start();
        ?>
        <div class="knot4-donor-overview">
            <div class="knot4-overview-stats">
                <div class="knot4-stat-item">
                    <div class="stat-value"><?php echo Knot4_Utilities::format_currency($total_donated); ?></div>
                    <div class="stat-label"><?php _e('Total Donated', 'knot4'); ?></div>
                </div>
                <div class="knot4-stat-item">
                    <div class="stat-value"><?php echo number_format($donation_count); ?></div>
                    <div class="stat-label"><?php _e('Donations Made', 'knot4'); ?></div>
                </div>
                <div class="knot4-stat-item">
                    <div class="stat-value"><?php echo $first_donation ? date_i18n(get_option('date_format'), strtotime($first_donation)) : '-'; ?></div>
                    <div class="stat-label"><?php _e('First Donation', 'knot4'); ?></div>
                </div>
                <div class="knot4-stat-item">
                    <div class="stat-value"><?php echo $last_donation ? date_i18n(get_option('date_format'), strtotime($last_donation)) : '-'; ?></div>
                    <div class="stat-label"><?php _e('Latest Donation', 'knot4'); ?></div>
                </div>
            </div>
            
            <div class="knot4-recent-donations">
                <h4><?php _e('Recent Donations', 'knot4'); ?></h4>
                <?php
                $recent_donations = $wpdb->get_results($wpdb->prepare(
                    "SELECT * FROM {$wpdb->prefix}knot4_donations WHERE donor_id = %d AND status = 'completed' ORDER BY created_at DESC LIMIT 5",
                    $donor_id
                ));
                
                if (empty($recent_donations)): ?>
                    <p><?php _e('No donations found.', 'knot4'); ?></p>
                <?php else: ?>
                    <div class="knot4-donations-list">
                        <?php foreach ($recent_donations as $donation): ?>
                        <div class="knot4-donation-item">
                            <div class="donation-amount"><?php echo Knot4_Utilities::format_currency($donation->amount); ?></div>
                            <div class="donation-details">
                                <div class="donation-date"><?php echo date_i18n(get_option('date_format'), strtotime($donation->created_at)); ?></div>
                                <?php if (!empty($donation->fund_designation)): ?>
                                <div class="donation-fund"><?php echo esc_html($donation->fund_designation); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        
        return ob_get_clean();
    }
    
    private static function render_donor_donations($donor_id) {
        // Implementation for donations history tab
        return '<p>' . __('Donation history will be displayed here.', 'knot4') . '</p>';
    }
    
    private static function render_donor_events($email) {
        // Implementation for events tab
        return '<p>' . __('Event registrations will be displayed here.', 'knot4') . '</p>';
    }
    
    private static function render_donor_profile($donor_id) {
        // Implementation for profile management tab
        return '<p>' . __('Profile management will be displayed here.', 'knot4') . '</p>';
    }
    
    private static function render_donor_recurring($donor_id) {
        // Implementation for recurring donations tab (Pro)
        return '<p>' . __('Recurring donations management will be displayed here.', 'knot4') . '</p>';
    }
    
    private static function render_donor_documents($donor_id) {
        // Implementation for documents tab (Pro)
        return '<p>' . __('Tax receipts and documents will be displayed here.', 'knot4') . '</p>';
    }
    
    private static function render_event_card($atts) {
        // Implementation for event card rendering
        return '<div class="knot4-event-card">Event card content</div>';
    }
    
    private static function render_event_list_item($atts) {
        // Implementation for event list item rendering
        return '<div class="knot4-event-list-item">Event list item content</div>';
    }
    
    private static function render_event_calendar($events, $atts) {
        // Implementation for calendar view
        return '<div class="knot4-event-calendar">Calendar view</div>';
    }
}