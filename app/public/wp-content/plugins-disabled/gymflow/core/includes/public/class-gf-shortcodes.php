<?php
/**
 * GymFlow Shortcodes Class
 *
 * Handles all public-facing shortcodes for member registration, class schedules, booking forms, etc.
 *
 * @package GymFlow
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * GF_Shortcodes Class
 *
 * Public shortcode functionality
 */
class GF_Shortcodes {

    /**
     * Initialize shortcodes
     */
    public function init() {
        add_action('init', array($this, 'register_shortcodes'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_gf_member_registration', array($this, 'ajax_member_registration'));
        add_action('wp_ajax_nopriv_gf_member_registration', array($this, 'ajax_member_registration'));
        add_action('wp_ajax_gf_class_booking', array($this, 'ajax_class_booking'));
        add_action('wp_ajax_nopriv_gf_class_booking', array($this, 'ajax_class_booking'));
        add_action('wp_ajax_gf_equipment_booking', array($this, 'ajax_equipment_booking'));
        add_action('wp_ajax_nopriv_gf_equipment_booking', array($this, 'ajax_equipment_booking'));
        add_action('wp_ajax_gf_get_available_slots', array($this, 'ajax_get_available_slots'));
        add_action('wp_ajax_nopriv_gf_get_available_slots', array($this, 'ajax_get_available_slots'));
    }

    /**
     * Register all shortcodes
     */
    public function register_shortcodes() {
        // Member-related shortcodes
        add_shortcode('gf_member_registration', array($this, 'member_registration_form'));
        add_shortcode('gf_member_login', array($this, 'member_login_form'));
        add_shortcode('gf_member_dashboard', array($this, 'member_dashboard'));
        add_shortcode('gf_member_profile', array($this, 'member_profile'));

        // Class-related shortcodes
        add_shortcode('gf_class_schedule', array($this, 'class_schedule'));
        add_shortcode('gf_class_list', array($this, 'class_list'));
        add_shortcode('gf_class_booking', array($this, 'class_booking_form'));
        add_shortcode('gf_class_details', array($this, 'class_details'));

        // Trainer-related shortcodes
        add_shortcode('gf_trainer_list', array($this, 'trainer_list'));
        add_shortcode('gf_trainer_profile', array($this, 'trainer_profile'));
        add_shortcode('gf_personal_training_booking', array($this, 'personal_training_booking'));

        // Equipment-related shortcodes
        add_shortcode('gf_equipment_list', array($this, 'equipment_list'));
        add_shortcode('gf_equipment_booking', array($this, 'equipment_booking_form'));

        // Booking-related shortcodes
        add_shortcode('gf_booking_confirmation', array($this, 'booking_confirmation'));
        add_shortcode('gf_my_bookings', array($this, 'my_bookings'));

        // General shortcodes
        add_shortcode('gf_opening_hours', array($this, 'opening_hours'));
        add_shortcode('gf_contact_info', array($this, 'contact_info'));
    }

    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_scripts() {
        if ($this->should_enqueue_scripts()) {
            wp_enqueue_script('jquery');
            wp_enqueue_script('jquery-ui-datepicker');
            wp_enqueue_style('jquery-ui');

            wp_enqueue_script(
                'gymflow-public',
                GYMFLOW_PLUGIN_URL . 'core/assets/js/public.js',
                array('jquery', 'jquery-ui-datepicker'),
                GYMFLOW_VERSION,
                true
            );

            wp_enqueue_style(
                'gymflow-public',
                GYMFLOW_PLUGIN_URL . 'core/assets/css/public.css',
                array(),
                GYMFLOW_VERSION
            );

            wp_localize_script('gymflow-public', 'gymflow_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('gymflow_public'),
                'strings' => array(
                    'loading' => __('Loading...', 'gymflow'),
                    'error' => __('An error occurred. Please try again.', 'gymflow'),
                    'success' => __('Success!', 'gymflow'),
                    'required_field' => __('This field is required.', 'gymflow'),
                    'invalid_email' => __('Please enter a valid email address.', 'gymflow'),
                    'confirm_booking' => __('Are you sure you want to book this slot?', 'gymflow'),
                    'cancel_booking' => __('Are you sure you want to cancel this booking?', 'gymflow')
                )
            ));
        }
    }

    /**
     * Check if we should enqueue scripts (if any GymFlow shortcode is present)
     */
    private function should_enqueue_scripts() {
        global $post;
        
        if (!$post) {
            return false;
        }

        $shortcodes = array(
            'gf_member_registration', 'gf_member_login', 'gf_member_dashboard', 'gf_member_profile',
            'gf_class_schedule', 'gf_class_list', 'gf_class_booking', 'gf_class_details',
            'gf_trainer_list', 'gf_trainer_profile', 'gf_personal_training_booking',
            'gf_equipment_list', 'gf_equipment_booking', 'gf_booking_confirmation',
            'gf_my_bookings', 'gf_opening_hours', 'gf_contact_info'
        );

        foreach ($shortcodes as $shortcode) {
            if (has_shortcode($post->post_content, $shortcode)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Member registration form shortcode
     */
    public function member_registration_form($atts) {
        $atts = shortcode_atts(array(
            'redirect_url' => '',
            'show_login_link' => 'yes',
            'membership_types' => '',
            'class' => 'gymflow-registration-form'
        ), $atts);

        ob_start();
        ?>
        <div class="<?php echo esc_attr($atts['class']); ?>">
            <form id="gymflow-member-registration" method="post">
                <?php wp_nonce_field('gf_member_registration', 'gf_member_nonce'); ?>
                
                <div class="gf-form-section">
                    <h3><?php _e('Personal Information', 'gymflow'); ?></h3>
                    
                    <div class="gf-form-row">
                        <div class="gf-form-field">
                            <label for="first_name"><?php _e('First Name', 'gymflow'); ?> *</label>
                            <input type="text" id="first_name" name="first_name" required>
                        </div>
                        <div class="gf-form-field">
                            <label for="last_name"><?php _e('Last Name', 'gymflow'); ?> *</label>
                            <input type="text" id="last_name" name="last_name" required>
                        </div>
                    </div>

                    <div class="gf-form-row">
                        <div class="gf-form-field">
                            <label for="email"><?php _e('Email Address', 'gymflow'); ?> *</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="gf-form-field">
                            <label for="phone"><?php _e('Phone Number', 'gymflow'); ?></label>
                            <input type="tel" id="phone" name="phone">
                        </div>
                    </div>

                    <div class="gf-form-row">
                        <div class="gf-form-field">
                            <label for="date_of_birth"><?php _e('Date of Birth', 'gymflow'); ?></label>
                            <input type="date" id="date_of_birth" name="date_of_birth">
                        </div>
                        <div class="gf-form-field">
                            <label for="gender"><?php _e('Gender', 'gymflow'); ?></label>
                            <select id="gender" name="gender">
                                <option value="prefer_not_to_say"><?php _e('Prefer not to say', 'gymflow'); ?></option>
                                <option value="male"><?php _e('Male', 'gymflow'); ?></option>
                                <option value="female"><?php _e('Female', 'gymflow'); ?></option>
                                <option value="other"><?php _e('Other', 'gymflow'); ?></option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="gf-form-section">
                    <h3><?php _e('Membership Information', 'gymflow'); ?></h3>
                    
                    <div class="gf-form-field">
                        <label for="membership_type"><?php _e('Membership Type', 'gymflow'); ?> *</label>
                        <select id="membership_type" name="membership_type" required>
                            <option value=""><?php _e('Select Membership Type', 'gymflow'); ?></option>
                            <?php echo $this->get_membership_type_options($atts['membership_types']); ?>
                        </select>
                    </div>
                </div>

                <div class="gf-form-section">
                    <h3><?php _e('Emergency Contact', 'gymflow'); ?></h3>
                    
                    <div class="gf-form-row">
                        <div class="gf-form-field">
                            <label for="emergency_contact_name"><?php _e('Contact Name', 'gymflow'); ?></label>
                            <input type="text" id="emergency_contact_name" name="emergency_contact_name">
                        </div>
                        <div class="gf-form-field">
                            <label for="emergency_contact_phone"><?php _e('Contact Phone', 'gymflow'); ?></label>
                            <input type="tel" id="emergency_contact_phone" name="emergency_contact_phone">
                        </div>
                    </div>
                </div>

                <div class="gf-form-section">
                    <h3><?php _e('Health Information', 'gymflow'); ?></h3>
                    
                    <div class="gf-form-field">
                        <label for="health_conditions"><?php _e('Health Conditions or Allergies', 'gymflow'); ?></label>
                        <textarea id="health_conditions" name="health_conditions" rows="3" placeholder="<?php _e('Please list any relevant health conditions, allergies, or medical information', 'gymflow'); ?>"></textarea>
                    </div>
                </div>

                <div class="gf-form-section">
                    <div class="gf-form-field">
                        <label>
                            <input type="checkbox" name="accept_terms" required>
                            <?php _e('I accept the terms and conditions and waiver', 'gymflow'); ?> *
                        </label>
                    </div>
                </div>

                <div class="gf-form-actions">
                    <button type="submit" class="gf-btn gf-btn-primary">
                        <?php _e('Register', 'gymflow'); ?>
                    </button>
                </div>

                <div class="gf-form-messages"></div>
            </form>

            <?php if ($atts['show_login_link'] === 'yes'): ?>
            <div class="gf-form-footer">
                <p><?php _e('Already a member?', 'gymflow'); ?> <a href="#" class="gf-show-login"><?php _e('Sign in here', 'gymflow'); ?></a></p>
            </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Member login form shortcode
     */
    public function member_login_form($atts) {
        $atts = shortcode_atts(array(
            'redirect_url' => '',
            'show_register_link' => 'yes',
            'class' => 'gymflow-login-form'
        ), $atts);

        ob_start();
        ?>
        <div class="<?php echo esc_attr($atts['class']); ?>">
            <form id="gymflow-member-login" method="post">
                <?php wp_nonce_field('gf_member_login', 'gf_login_nonce'); ?>
                
                <div class="gf-form-field">
                    <label for="login_email"><?php _e('Email Address', 'gymflow'); ?></label>
                    <input type="email" id="login_email" name="email" required>
                </div>

                <div class="gf-form-field">
                    <label for="login_member_number"><?php _e('Member Number', 'gymflow'); ?></label>
                    <input type="text" id="login_member_number" name="member_number" placeholder="<?php _e('Optional - or use email only', 'gymflow'); ?>">
                </div>

                <div class="gf-form-actions">
                    <button type="submit" class="gf-btn gf-btn-primary">
                        <?php _e('Sign In', 'gymflow'); ?>
                    </button>
                </div>

                <div class="gf-form-messages"></div>
            </form>

            <?php if ($atts['show_register_link'] === 'yes'): ?>
            <div class="gf-form-footer">
                <p><?php _e('Not a member yet?', 'gymflow'); ?> <a href="#" class="gf-show-registration"><?php _e('Register here', 'gymflow'); ?></a></p>
            </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Member dashboard shortcode
     */
    public function member_dashboard($atts) {
        $atts = shortcode_atts(array(
            'class' => 'gymflow-member-dashboard'
        ), $atts);

        // Check if member is logged in
        $member_id = $this->get_current_member_id();
        if (!$member_id) {
            return '<p>' . __('Please log in to view your dashboard.', 'gymflow') . '</p>';
        }

        $member = new GF_Member($member_id);
        $upcoming_bookings = GF_Booking::get_all(array(
            'member_id' => $member_id,
            'date_from' => date('Y-m-d'),
            'limit' => 5,
            'order_by' => 'booking_date',
            'order' => 'ASC'
        ));

        ob_start();
        ?>
        <div class="<?php echo esc_attr($atts['class']); ?>">
            <div class="gf-dashboard-header">
                <h2><?php printf(__('Welcome, %s!', 'gymflow'), esc_html($member->first_name)); ?></h2>
                <p class="gf-member-info">
                    <?php printf(__('Member #%s', 'gymflow'), esc_html($member->member_number)); ?> | 
                    <?php printf(__('Status: %s', 'gymflow'), '<span class="gf-status-' . esc_attr($member->membership_status) . '">' . esc_html(ucfirst($member->membership_status)) . '</span>'); ?>
                </p>
            </div>

            <div class="gf-dashboard-grid">
                <div class="gf-dashboard-card">
                    <h3><?php _e('Upcoming Bookings', 'gymflow'); ?></h3>
                    <?php if (!empty($upcoming_bookings)): ?>
                        <div class="gf-bookings-list">
                            <?php foreach ($upcoming_bookings as $booking): ?>
                                <div class="gf-booking-item">
                                    <div class="gf-booking-type"><?php echo esc_html(ucfirst($booking->booking_type)); ?></div>
                                    <div class="gf-booking-date"><?php echo esc_html(GF_Utilities::format_date($booking->booking_date)); ?></div>
                                    <div class="gf-booking-time"><?php echo esc_html(GF_Utilities::format_time($booking->start_time)); ?></div>
                                    <div class="gf-booking-status"><?php echo $this->get_booking_status_badge($booking->status); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <a href="#" class="gf-btn gf-btn-secondary gf-view-all-bookings"><?php _e('View All Bookings', 'gymflow'); ?></a>
                    <?php else: ?>
                        <p><?php _e('No upcoming bookings.', 'gymflow'); ?></p>
                        <a href="#" class="gf-btn gf-btn-primary gf-book-class"><?php _e('Book a Class', 'gymflow'); ?></a>
                    <?php endif; ?>
                </div>

                <div class="gf-dashboard-card">
                    <h3><?php _e('Membership Details', 'gymflow'); ?></h3>
                    <div class="gf-membership-info">
                        <p><strong><?php _e('Type:', 'gymflow'); ?></strong> <?php echo esc_html($member->membership_type); ?></p>
                        <p><strong><?php _e('Start Date:', 'gymflow'); ?></strong> <?php echo esc_html(GF_Utilities::format_date($member->membership_start_date)); ?></p>
                        <p><strong><?php _e('End Date:', 'gymflow'); ?></strong> <?php echo esc_html(GF_Utilities::format_date($member->membership_end_date)); ?></p>
                    </div>
                    <a href="#" class="gf-btn gf-btn-secondary gf-edit-profile"><?php _e('Edit Profile', 'gymflow'); ?></a>
                </div>

                <div class="gf-dashboard-card">
                    <h3><?php _e('Quick Actions', 'gymflow'); ?></h3>
                    <div class="gf-quick-actions">
                        <a href="#" class="gf-btn gf-btn-primary gf-book-class"><?php _e('Book a Class', 'gymflow'); ?></a>
                        <a href="#" class="gf-btn gf-btn-primary gf-book-equipment"><?php _e('Book Equipment', 'gymflow'); ?></a>
                        <a href="#" class="gf-btn gf-btn-primary gf-book-training"><?php _e('Personal Training', 'gymflow'); ?></a>
                    </div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Class schedule shortcode
     */
    public function class_schedule($atts) {
        $atts = shortcode_atts(array(
            'view' => 'week', // week, day, month
            'show_bookings' => 'yes',
            'categories' => '',
            'class' => 'gymflow-class-schedule'
        ), $atts);

        ob_start();
        ?>
        <div class="<?php echo esc_attr($atts['class']); ?>">
            <div class="gf-schedule-header">
                <div class="gf-schedule-nav">
                    <button class="gf-btn gf-btn-secondary gf-nav-prev">&larr; <?php _e('Previous', 'gymflow'); ?></button>
                    <span class="gf-current-period"></span>
                    <button class="gf-btn gf-btn-secondary gf-nav-next"><?php _e('Next', 'gymflow'); ?> &rarr;</button>
                </div>
                <div class="gf-schedule-view-toggle">
                    <button class="gf-btn gf-view-day" data-view="day"><?php _e('Day', 'gymflow'); ?></button>
                    <button class="gf-btn gf-view-week active" data-view="week"><?php _e('Week', 'gymflow'); ?></button>
                    <button class="gf-btn gf-view-month" data-view="month"><?php _e('Month', 'gymflow'); ?></button>
                </div>
            </div>

            <div class="gf-schedule-filters">
                <?php echo $this->get_class_category_filter($atts['categories']); ?>
            </div>

            <div class="gf-schedule-grid" id="gf-schedule-grid">
                <!-- Schedule content loaded via AJAX -->
                <div class="gf-loading"><?php _e('Loading schedule...', 'gymflow'); ?></div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Class list shortcode
     */
    public function class_list($atts) {
        $atts = shortcode_atts(array(
            'limit' => 10,
            'category' => '',
            'difficulty' => '',
            'show_instructor' => 'yes',
            'show_price' => 'yes',
            'show_booking_button' => 'yes',
            'class' => 'gymflow-class-list'
        ), $atts);

        $args = array(
            'limit' => intval($atts['limit']),
            'category' => $atts['category'],
            'difficulty' => $atts['difficulty'],
            'active_only' => true
        );

        $classes = GF_Class::get_all($args);

        ob_start();
        ?>
        <div class="<?php echo esc_attr($atts['class']); ?>">
            <?php if (!empty($classes)): ?>
                <div class="gf-classes-grid">
                    <?php foreach ($classes as $class_data): ?>
                        <?php $class = new GF_Class($class_data->id); ?>
                        <div class="gf-class-card">
                            <div class="gf-class-header">
                                <h3 class="gf-class-name"><?php echo esc_html($class->name); ?></h3>
                                <span class="gf-class-difficulty"><?php echo esc_html($class->get_difficulty_label()); ?></span>
                            </div>

                            <div class="gf-class-details">
                                <p class="gf-class-description"><?php echo esc_html($class->description); ?></p>
                                
                                <div class="gf-class-meta">
                                    <span class="gf-class-duration"><?php echo esc_html($class->get_formatted_duration()); ?></span>
                                    <span class="gf-class-capacity"><?php printf(__('Max %d people', 'gymflow'), $class->capacity); ?></span>
                                    
                                    <?php if ($atts['show_instructor'] === 'yes' && $class->instructor_id): ?>
                                        <?php $instructor = $class->get_instructor(); ?>
                                        <?php if ($instructor): ?>
                                            <span class="gf-class-instructor"><?php printf(__('with %s', 'gymflow'), esc_html($instructor->get_full_name())); ?></span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>

                                <?php if ($atts['show_price'] === 'yes'): ?>
                                    <div class="gf-class-pricing">
                                        <?php if ($class->price > 0): ?>
                                            <span class="gf-class-price"><?php echo esc_html($class->get_formatted_price()); ?></span>
                                        <?php endif; ?>
                                        <?php if ($class->drop_in_price > 0): ?>
                                            <span class="gf-class-drop-in"><?php printf(__('Drop-in: %s', 'gymflow'), esc_html($class->get_formatted_price('drop_in'))); ?></span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if ($atts['show_booking_button'] === 'yes'): ?>
                                <div class="gf-class-actions">
                                    <a href="#" class="gf-btn gf-btn-primary gf-book-class-btn" data-class-id="<?php echo esc_attr($class->id); ?>">
                                        <?php _e('Book Class', 'gymflow'); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p><?php _e('No classes found.', 'gymflow'); ?></p>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Class booking form shortcode
     */
    public function class_booking_form($atts) {
        $atts = shortcode_atts(array(
            'class_id' => '',
            'show_class_selection' => 'yes',
            'class' => 'gymflow-class-booking-form'
        ), $atts);

        ob_start();
        ?>
        <div class="<?php echo esc_attr($atts['class']); ?>">
            <form id="gymflow-class-booking" method="post">
                <?php wp_nonce_field('gf_class_booking', 'gf_booking_nonce'); ?>
                
                <?php if ($atts['show_class_selection'] === 'yes' && empty($atts['class_id'])): ?>
                    <div class="gf-form-field">
                        <label for="class_id"><?php _e('Select Class', 'gymflow'); ?> *</label>
                        <select id="class_id" name="class_id" required>
                            <option value=""><?php _e('Choose a class...', 'gymflow'); ?></option>
                            <?php echo $this->get_active_classes_options(); ?>
                        </select>
                    </div>
                <?php elseif (!empty($atts['class_id'])): ?>
                    <input type="hidden" name="class_id" value="<?php echo esc_attr($atts['class_id']); ?>">
                <?php endif; ?>

                <div class="gf-form-field">
                    <label for="booking_date"><?php _e('Date', 'gymflow'); ?> *</label>
                    <input type="date" id="booking_date" name="booking_date" required min="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="gf-form-field" id="available-times-container" style="display:none;">
                    <label><?php _e('Available Times', 'gymflow'); ?> *</label>
                    <div id="available-times"></div>
                </div>

                <div class="gf-member-details">
                    <h3><?php _e('Your Details', 'gymflow'); ?></h3>
                    
                    <div class="gf-form-row">
                        <div class="gf-form-field">
                            <label for="member_first_name"><?php _e('First Name', 'gymflow'); ?> *</label>
                            <input type="text" id="member_first_name" name="first_name" required>
                        </div>
                        <div class="gf-form-field">
                            <label for="member_last_name"><?php _e('Last Name', 'gymflow'); ?> *</label>
                            <input type="text" id="member_last_name" name="last_name" required>
                        </div>
                    </div>

                    <div class="gf-form-row">
                        <div class="gf-form-field">
                            <label for="member_email"><?php _e('Email Address', 'gymflow'); ?> *</label>
                            <input type="email" id="member_email" name="email" required>
                        </div>
                        <div class="gf-form-field">
                            <label for="member_phone"><?php _e('Phone Number', 'gymflow'); ?></label>
                            <input type="tel" id="member_phone" name="phone">
                        </div>
                    </div>
                </div>

                <div class="gf-form-field">
                    <label for="special_requests"><?php _e('Special Requests', 'gymflow'); ?></label>
                    <textarea id="special_requests" name="notes" rows="3" placeholder="<?php _e('Any special requirements or notes...', 'gymflow'); ?>"></textarea>
                </div>

                <div class="gf-form-actions">
                    <button type="submit" class="gf-btn gf-btn-primary" disabled>
                        <?php _e('Book Class', 'gymflow'); ?>
                    </button>
                </div>

                <div class="gf-form-messages"></div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Equipment booking form shortcode
     */
    public function equipment_booking_form($atts) {
        $atts = shortcode_atts(array(
            'equipment_id' => '',
            'show_equipment_selection' => 'yes',
            'class' => 'gymflow-equipment-booking-form'
        ), $atts);

        ob_start();
        ?>
        <div class="<?php echo esc_attr($atts['class']); ?>">
            <form id="gymflow-equipment-booking" method="post">
                <?php wp_nonce_field('gf_equipment_booking', 'gf_equipment_nonce'); ?>
                
                <?php if ($atts['show_equipment_selection'] === 'yes' && empty($atts['equipment_id'])): ?>
                    <div class="gf-form-field">
                        <label for="equipment_id"><?php _e('Select Equipment', 'gymflow'); ?> *</label>
                        <select id="equipment_id" name="equipment_id" required>
                            <option value=""><?php _e('Choose equipment...', 'gymflow'); ?></option>
                            <?php echo $this->get_bookable_equipment_options(); ?>
                        </select>
                    </div>
                <?php elseif (!empty($atts['equipment_id'])): ?>
                    <input type="hidden" name="equipment_id" value="<?php echo esc_attr($atts['equipment_id']); ?>">
                <?php endif; ?>

                <div class="gf-form-row">
                    <div class="gf-form-field">
                        <label for="booking_date"><?php _e('Date', 'gymflow'); ?> *</label>
                        <input type="date" id="booking_date" name="booking_date" required min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="gf-form-field">
                        <label for="start_time"><?php _e('Start Time', 'gymflow'); ?> *</label>
                        <input type="time" id="start_time" name="start_time" required>
                    </div>
                </div>

                <div class="gf-form-field">
                    <label for="duration"><?php _e('Duration (minutes)', 'gymflow'); ?> *</label>
                    <select id="duration" name="duration" required>
                        <option value="30">30 <?php _e('minutes', 'gymflow'); ?></option>
                        <option value="60" selected>1 <?php _e('hour', 'gymflow'); ?></option>
                        <option value="90">1.5 <?php _e('hours', 'gymflow'); ?></option>
                        <option value="120">2 <?php _e('hours', 'gymflow'); ?></option>
                    </select>
                </div>

                <div class="gf-member-details">
                    <h3><?php _e('Your Details', 'gymflow'); ?></h3>
                    
                    <div class="gf-form-row">
                        <div class="gf-form-field">
                            <label for="member_first_name"><?php _e('First Name', 'gymflow'); ?> *</label>
                            <input type="text" id="member_first_name" name="first_name" required>
                        </div>
                        <div class="gf-form-field">
                            <label for="member_last_name"><?php _e('Last Name', 'gymflow'); ?> *</label>
                            <input type="text" id="member_last_name" name="last_name" required>
                        </div>
                    </div>

                    <div class="gf-form-row">
                        <div class="gf-form-field">
                            <label for="member_email"><?php _e('Email Address', 'gymflow'); ?> *</label>
                            <input type="email" id="member_email" name="email" required>
                        </div>
                        <div class="gf-form-field">
                            <label for="member_phone"><?php _e('Phone Number', 'gymflow'); ?></label>
                            <input type="tel" id="member_phone" name="phone">
                        </div>
                    </div>
                </div>

                <div class="gf-form-actions">
                    <button type="submit" class="gf-btn gf-btn-primary">
                        <?php _e('Book Equipment', 'gymflow'); ?>
                    </button>
                </div>

                <div class="gf-form-messages"></div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Trainer list shortcode
     */
    public function trainer_list($atts) {
        $atts = shortcode_atts(array(
            'limit' => 10,
            'specialty' => '',
            'show_bio' => 'yes',
            'show_specialties' => 'yes',
            'show_book_button' => 'yes',
            'class' => 'gymflow-trainer-list'
        ), $atts);

        $args = array(
            'limit' => intval($atts['limit']),
            'specialty' => $atts['specialty'],
            'active_only' => true
        );

        $trainers = GF_Trainer::get_all($args);

        ob_start();
        ?>
        <div class="<?php echo esc_attr($atts['class']); ?>">
            <?php if (!empty($trainers)): ?>
                <div class="gf-trainers-grid">
                    <?php foreach ($trainers as $trainer_data): ?>
                        <?php $trainer = new GF_Trainer($trainer_data->id); ?>
                        <div class="gf-trainer-card">
                            <div class="gf-trainer-photo">
                                <?php if ($trainer->profile_photo_url): ?>
                                    <img src="<?php echo esc_url($trainer->profile_photo_url); ?>" alt="<?php echo esc_attr($trainer->get_full_name()); ?>">
                                <?php else: ?>
                                    <div class="gf-trainer-avatar"><?php echo esc_html($trainer->get_initials()); ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="gf-trainer-info">
                                <h3 class="gf-trainer-name"><?php echo esc_html($trainer->get_full_name()); ?></h3>
                                
                                <?php if ($atts['show_specialties'] === 'yes' && !empty($trainer->specialties)): ?>
                                    <div class="gf-trainer-specialties">
                                        <?php foreach ($trainer->get_specialties_array() as $specialty): ?>
                                            <span class="gf-specialty-tag"><?php echo esc_html($specialty); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ($atts['show_bio'] === 'yes' && !empty($trainer->bio)): ?>
                                    <p class="gf-trainer-bio"><?php echo esc_html(wp_trim_words($trainer->bio, 20)); ?></p>
                                <?php endif; ?>

                                <div class="gf-trainer-meta">
                                    <?php if ($trainer->hourly_rate > 0): ?>
                                        <span class="gf-trainer-rate"><?php printf(__('%s/hour', 'gymflow'), $trainer->get_formatted_hourly_rate()); ?></span>
                                    <?php endif; ?>
                                    <span class="gf-trainer-experience"><?php printf(__('%d years experience', 'gymflow'), $trainer->get_years_experience()); ?></span>
                                </div>

                                <?php if ($atts['show_book_button'] === 'yes'): ?>
                                    <div class="gf-trainer-actions">
                                        <a href="#" class="gf-btn gf-btn-primary gf-book-trainer-btn" data-trainer-id="<?php echo esc_attr($trainer->id); ?>">
                                            <?php _e('Book Session', 'gymflow'); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p><?php _e('No trainers found.', 'gymflow'); ?></p>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * My bookings shortcode
     */
    public function my_bookings($atts) {
        $atts = shortcode_atts(array(
            'limit' => 20,
            'status' => '',
            'show_past' => 'yes',
            'class' => 'gymflow-my-bookings'
        ), $atts);

        // Check if member is logged in
        $member_id = $this->get_current_member_id();
        if (!$member_id) {
            return '<p>' . __('Please log in to view your bookings.', 'gymflow') . '</p>';
        }

        $args = array(
            'member_id' => $member_id,
            'limit' => intval($atts['limit']),
            'order_by' => 'booking_date',
            'order' => 'DESC'
        );

        if (!empty($atts['status'])) {
            $args['status'] = $atts['status'];
        }

        if ($atts['show_past'] === 'no') {
            $args['date_from'] = date('Y-m-d');
        }

        $bookings = GF_Booking::get_all($args);

        ob_start();
        ?>
        <div class="<?php echo esc_attr($atts['class']); ?>">
            <div class="gf-bookings-header">
                <h2><?php _e('My Bookings', 'gymflow'); ?></h2>
                <div class="gf-bookings-filters">
                    <select id="gf-booking-status-filter">
                        <option value=""><?php _e('All Statuses', 'gymflow'); ?></option>
                        <option value="confirmed"><?php _e('Confirmed', 'gymflow'); ?></option>
                        <option value="pending"><?php _e('Pending', 'gymflow'); ?></option>
                        <option value="completed"><?php _e('Completed', 'gymflow'); ?></option>
                        <option value="cancelled"><?php _e('Cancelled', 'gymflow'); ?></option>
                    </select>
                </div>
            </div>

            <?php if (!empty($bookings)): ?>
                <div class="gf-bookings-table">
                    <table>
                        <thead>
                            <tr>
                                <th><?php _e('Type', 'gymflow'); ?></th>
                                <th><?php _e('Date & Time', 'gymflow'); ?></th>
                                <th><?php _e('Details', 'gymflow'); ?></th>
                                <th><?php _e('Status', 'gymflow'); ?></th>
                                <th><?php _e('Actions', 'gymflow'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookings as $booking_data): ?>
                                <?php $booking = new GF_Booking($booking_data->id); ?>
                                <tr>
                                    <td><?php echo esc_html(ucfirst($booking->booking_type)); ?></td>
                                    <td><?php echo esc_html($booking->get_formatted_datetime()); ?></td>
                                    <td>
                                        <?php
                                        switch ($booking->booking_type) {
                                            case 'class':
                                                $class = $booking->get_class();
                                                echo $class ? esc_html($class->name) : __('Class not found', 'gymflow');
                                                break;
                                            case 'equipment':
                                                $equipment = $booking->get_equipment();
                                                echo $equipment ? esc_html($equipment->name) : __('Equipment not found', 'gymflow');
                                                break;
                                            case 'personal_training':
                                                $trainer = $booking->get_trainer();
                                                echo $trainer ? esc_html($trainer->get_full_name()) : __('Trainer not found', 'gymflow');
                                                break;
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo $booking->get_status_badge(); ?></td>
                                    <td>
                                        <?php if ($booking->can_be_cancelled()): ?>
                                            <button class="gf-btn gf-btn-small gf-btn-danger gf-cancel-booking" 
                                                    data-booking-id="<?php echo esc_attr($booking->id); ?>">
                                                <?php _e('Cancel', 'gymflow'); ?>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p><?php _e('No bookings found.', 'gymflow'); ?></p>
                <a href="#" class="gf-btn gf-btn-primary"><?php _e('Book a Class', 'gymflow'); ?></a>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Opening hours shortcode
     */
    public function opening_hours($atts) {
        $atts = shortcode_atts(array(
            'show_today' => 'yes',
            'class' => 'gymflow-opening-hours'
        ), $atts);

        $opening_hours = GF_Utilities::get_option('general_settings', array())['opening_hours'] ?? array();

        ob_start();
        ?>
        <div class="<?php echo esc_attr($atts['class']); ?>">
            <h3><?php _e('Opening Hours', 'gymflow'); ?></h3>
            
            <?php if ($atts['show_today'] === 'yes'): ?>
                <div class="gf-today-hours">
                    <?php
                    $today = strtolower(date('l'));
                    $today_hours = isset($opening_hours[$today]) ? $opening_hours[$today] : '';
                    ?>
                    <p><strong><?php _e('Today:', 'gymflow'); ?></strong> 
                        <?php echo $today_hours ? esc_html($today_hours) : __('Closed', 'gymflow'); ?>
                    </p>
                </div>
            <?php endif; ?>

            <div class="gf-weekly-hours">
                <?php
                $days = array(
                    'monday' => __('Monday', 'gymflow'),
                    'tuesday' => __('Tuesday', 'gymflow'),
                    'wednesday' => __('Wednesday', 'gymflow'),
                    'thursday' => __('Thursday', 'gymflow'),
                    'friday' => __('Friday', 'gymflow'),
                    'saturday' => __('Saturday', 'gymflow'),
                    'sunday' => __('Sunday', 'gymflow')
                );

                foreach ($days as $day => $label):
                    $hours = isset($opening_hours[$day]) ? $opening_hours[$day] : '';
                ?>
                    <div class="gf-day-hours">
                        <span class="gf-day"><?php echo esc_html($label); ?>:</span>
                        <span class="gf-hours"><?php echo $hours ? esc_html($hours) : __('Closed', 'gymflow'); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Contact info shortcode
     */
    public function contact_info($atts) {
        $atts = shortcode_atts(array(
            'show_phone' => 'yes',
            'show_email' => 'yes',
            'show_address' => 'yes',
            'class' => 'gymflow-contact-info'
        ), $atts);

        $settings = GF_Utilities::get_option('general_settings', array());

        ob_start();
        ?>
        <div class="<?php echo esc_attr($atts['class']); ?>">
            <?php if ($atts['show_phone'] === 'yes' && !empty($settings['studio_phone'])): ?>
                <div class="gf-contact-item gf-phone">
                    <strong><?php _e('Phone:', 'gymflow'); ?></strong>
                    <a href="tel:<?php echo esc_attr($settings['studio_phone']); ?>"><?php echo esc_html($settings['studio_phone']); ?></a>
                </div>
            <?php endif; ?>

            <?php if ($atts['show_email'] === 'yes' && !empty($settings['studio_email'])): ?>
                <div class="gf-contact-item gf-email">
                    <strong><?php _e('Email:', 'gymflow'); ?></strong>
                    <a href="mailto:<?php echo esc_attr($settings['studio_email']); ?>"><?php echo esc_html($settings['studio_email']); ?></a>
                </div>
            <?php endif; ?>

            <?php if ($atts['show_address'] === 'yes' && !empty($settings['studio_address'])): ?>
                <div class="gf-contact-item gf-address">
                    <strong><?php _e('Address:', 'gymflow'); ?></strong>
                    <?php echo esc_html($settings['studio_address']); ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * AJAX: Handle member registration
     */
    public function ajax_member_registration() {
        check_ajax_referer('gf_member_registration', 'gf_member_nonce');

        $response = array('success' => false, 'message' => '');

        // Validate and sanitize input
        $data = $this->sanitize_member_data($_POST);
        
        if (!GF_Data_Validation::validate_member($data)) {
            $response['message'] = __('Please fill in all required fields.', 'gymflow');
            wp_send_json($response);
        }

        // Create new member
        $member = new GF_Member();
        foreach ($data as $key => $value) {
            if (property_exists($member, $key)) {
                $member->$key = $value;
            }
        }

        $member_id = $member->save();

        if ($member_id) {
            $response['success'] = true;
            $response['message'] = __('Registration successful! Welcome to our gym.', 'gymflow');
            $response['member_id'] = $member_id;
        } else {
            $response['message'] = __('Registration failed. Please try again.', 'gymflow');
        }

        wp_send_json($response);
    }

    /**
     * AJAX: Handle class booking
     */
    public function ajax_class_booking() {
        check_ajax_referer('gf_class_booking', 'gf_booking_nonce');

        $response = array('success' => false, 'message' => '');

        // Get or create member
        $member_id = $this->get_or_create_member($_POST);
        if (!$member_id) {
            $response['message'] = __('Member registration failed.', 'gymflow');
            wp_send_json($response);
        }

        // Create booking
        $booking = new GF_Booking();
        $booking->booking_type = 'class';
        $booking->member_id = $member_id;
        $booking->class_schedule_id = intval($_POST['schedule_id']);
        $booking->booking_date = sanitize_text_field($_POST['booking_date']);
        $booking->start_time = sanitize_text_field($_POST['start_time']);
        $booking->end_time = sanitize_text_field($_POST['end_time']);
        $booking->notes = sanitize_textarea_field($_POST['notes']);
        $booking->booking_source = 'website';

        $booking_id = $booking->save();

        if ($booking_id) {
            $response['success'] = true;
            $response['message'] = __('Class booked successfully!', 'gymflow');
            $response['booking_id'] = $booking_id;
        } else {
            $response['message'] = __('Booking failed. Please try again.', 'gymflow');
        }

        wp_send_json($response);
    }

    /**
     * AJAX: Handle equipment booking
     */
    public function ajax_equipment_booking() {
        check_ajax_referer('gf_equipment_booking', 'gf_equipment_nonce');

        $response = array('success' => false, 'message' => '');

        // Get or create member
        $member_id = $this->get_or_create_member($_POST);
        if (!$member_id) {
            $response['message'] = __('Member registration failed.', 'gymflow');
            wp_send_json($response);
        }

        // Calculate end time
        $start_time = sanitize_text_field($_POST['start_time']);
        $duration = intval($_POST['duration']);
        $end_time = date('H:i:s', strtotime($start_time) + ($duration * 60));

        // Create booking
        $booking = new GF_Booking();
        $booking->booking_type = 'equipment';
        $booking->member_id = $member_id;
        $booking->equipment_id = intval($_POST['equipment_id']);
        $booking->booking_date = sanitize_text_field($_POST['booking_date']);
        $booking->start_time = $start_time;
        $booking->end_time = $end_time;
        $booking->notes = sanitize_textarea_field($_POST['notes']);
        $booking->booking_source = 'website';

        $booking_id = $booking->save();

        if ($booking_id) {
            $response['success'] = true;
            $response['message'] = __('Equipment booked successfully!', 'gymflow');
            $response['booking_id'] = $booking_id;
        } else {
            $response['message'] = __('Booking failed. Please try again.', 'gymflow');
        }

        wp_send_json($response);
    }

    /**
     * AJAX: Get available time slots
     */
    public function ajax_get_available_slots() {
        check_ajax_referer('gymflow_public', 'nonce');

        $class_id = intval($_POST['class_id']);
        $date = sanitize_text_field($_POST['date']);

        $response = array('success' => false, 'slots' => array());

        if ($class_id && $date) {
            $class = new GF_Class($class_id);
            $schedules = $class->get_schedules(array(
                'date_from' => $date,
                'date_to' => $date,
                'status' => 'scheduled'
            ));

            foreach ($schedules as $schedule) {
                $available_spots = GF_Class::get_available_spots($schedule->id);
                if ($available_spots > 0) {
                    $response['slots'][] = array(
                        'id' => $schedule->id,
                        'time' => GF_Utilities::format_time($schedule->start_time),
                        'end_time' => GF_Utilities::format_time($schedule->end_time),
                        'available_spots' => $available_spots,
                        'instructor' => $schedule->instructor_id ? (new GF_Trainer($schedule->instructor_id))->get_full_name() : ''
                    );
                }
            }

            $response['success'] = true;
        }

        wp_send_json($response);
    }

    /**
     * Helper: Get membership type options
     */
    private function get_membership_type_options($types_filter = '') {
        global $wpdb;

        $table = $wpdb->prefix . 'gf_memberships';
        $where = "is_active = 1";
        $params = array();

        if (!empty($types_filter)) {
            $types = array_map('trim', explode(',', $types_filter));
            $placeholders = implode(',', array_fill(0, count($types), '%s'));
            $where .= " AND name IN ({$placeholders})";
            $params = $types;
        }

        $memberships = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} WHERE {$where} ORDER BY sort_order ASC",
            $params
        ));

        $options = '';
        foreach ($memberships as $membership) {
            $options .= sprintf(
                '<option value="%s" data-price="%s">%s - %s</option>',
                esc_attr($membership->name),
                esc_attr($membership->price),
                esc_html($membership->name),
                GF_Utilities::format_currency($membership->price)
            );
        }

        return $options;
    }

    /**
     * Helper: Get active classes options
     */
    private function get_active_classes_options() {
        $classes = GF_Class::get_all(array('active_only' => true));
        
        $options = '';
        foreach ($classes as $class) {
            $options .= sprintf(
                '<option value="%d">%s</option>',
                $class->id,
                esc_html($class->name)
            );
        }

        return $options;
    }

    /**
     * Helper: Get bookable equipment options
     */
    private function get_bookable_equipment_options() {
        global $wpdb;

        $table = $wpdb->prefix . 'gf_equipment';
        $equipment = $wpdb->get_results(
            "SELECT * FROM {$table} 
             WHERE is_bookable = 1 AND status = 'available' 
             ORDER BY name ASC"
        );

        $options = '';
        foreach ($equipment as $item) {
            $options .= sprintf(
                '<option value="%d">%s</option>',
                $item->id,
                esc_html($item->name)
            );
        }

        return $options;
    }

    /**
     * Helper: Get class category filter
     */
    private function get_class_category_filter($categories_filter = '') {
        $categories = get_terms(array(
            'taxonomy' => 'gf_class_category',
            'hide_empty' => true
        ));

        if (is_wp_error($categories) || empty($categories)) {
            return '';
        }

        $filter_categories = array();
        if (!empty($categories_filter)) {
            $filter_categories = array_map('trim', explode(',', $categories_filter));
        }

        $options = '<option value="">' . __('All Categories', 'gymflow') . '</option>';
        foreach ($categories as $category) {
            if (empty($filter_categories) || in_array($category->slug, $filter_categories)) {
                $options .= sprintf(
                    '<option value="%s">%s</option>',
                    esc_attr($category->slug),
                    esc_html($category->name)
                );
            }
        }

        return '<select id="gf-category-filter">' . $options . '</select>';
    }

    /**
     * Helper: Sanitize member data
     */
    private function sanitize_member_data($data) {
        return array(
            'first_name' => sanitize_text_field($data['first_name']),
            'last_name' => sanitize_text_field($data['last_name']),
            'email' => sanitize_email($data['email']),
            'phone' => sanitize_text_field($data['phone']),
            'date_of_birth' => sanitize_text_field($data['date_of_birth']),
            'gender' => sanitize_text_field($data['gender']),
            'membership_type' => sanitize_text_field($data['membership_type']),
            'emergency_contact_name' => sanitize_text_field($data['emergency_contact_name']),
            'emergency_contact_phone' => sanitize_text_field($data['emergency_contact_phone']),
            'health_conditions' => sanitize_textarea_field($data['health_conditions'])
        );
    }

    /**
     * Helper: Get or create member from form data
     */
    private function get_or_create_member($data) {
        // Check if member exists
        $existing_member = GF_Member::find_by_email(sanitize_email($data['email']));
        
        if ($existing_member) {
            return $existing_member->id;
        }

        // Create new member
        $member = new GF_Member();
        $member->first_name = sanitize_text_field($data['first_name']);
        $member->last_name = sanitize_text_field($data['last_name']);
        $member->email = sanitize_email($data['email']);
        $member->phone = sanitize_text_field($data['phone']);
        $member->membership_status = 'pending';

        return $member->save();
    }

    /**
     * Helper: Get current member ID (if logged in)
     */
    private function get_current_member_id() {
        // This would integrate with WordPress user system or custom member session
        // For now, return 0 (not implemented)
        return 0;
    }

    /**
     * Helper: Get booking status badge
     */
    private function get_booking_status_badge($status) {
        $statuses = array(
            'confirmed' => array('label' => __('Confirmed', 'gymflow'), 'class' => 'success'),
            'pending' => array('label' => __('Pending', 'gymflow'), 'class' => 'warning'),
            'cancelled' => array('label' => __('Cancelled', 'gymflow'), 'class' => 'danger'),
            'completed' => array('label' => __('Completed', 'gymflow'), 'class' => 'info'),
            'no_show' => array('label' => __('No Show', 'gymflow'), 'class' => 'secondary')
        );

        $status_info = isset($statuses[$status]) ? $statuses[$status] : $statuses['pending'];
        
        return sprintf(
            '<span class="gf-status-badge gf-status-%s">%s</span>',
            esc_attr($status_info['class']),
            esc_html($status_info['label'])
        );
    }
}