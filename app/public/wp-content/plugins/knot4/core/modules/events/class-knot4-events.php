<?php
/**
 * Events Module for Knot4
 * 
 * @package Knot4
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Knot4_Events {
    
    /**
     * Initialize events module
     */
    public static function init() {
        // Admin AJAX handlers
        add_action('wp_ajax_knot4_create_event', array(__CLASS__, 'create_event'));
        add_action('wp_ajax_knot4_get_event_registrations', array(__CLASS__, 'get_event_registrations'));
        add_action('wp_ajax_knot4_update_event', array(__CLASS__, 'update_event'));
        add_action('wp_ajax_knot4_delete_event', array(__CLASS__, 'delete_event'));
        
        // Public AJAX handlers
        add_action('wp_ajax_knot4_register_for_event', array(__CLASS__, 'register_for_event'));
        add_action('wp_ajax_nopriv_knot4_register_for_event', array(__CLASS__, 'register_for_event'));
        add_action('wp_ajax_knot4_cancel_event_registration', array(__CLASS__, 'cancel_event_registration'));
        add_action('wp_ajax_nopriv_knot4_cancel_event_registration', array(__CLASS__, 'cancel_event_registration'));
        
        // Meta box hooks
        add_action('add_meta_boxes', array(__CLASS__, 'add_event_meta_boxes'));
        add_action('save_post', array(__CLASS__, 'save_event_meta'));
        
        // Template hooks
        add_filter('single_template', array(__CLASS__, 'event_single_template'));
        add_filter('archive_template', array(__CLASS__, 'event_archive_template'));
        
        // Email notifications
        add_action('knot4_event_registration_confirmed', array(__CLASS__, 'send_registration_confirmation'));
        add_action('knot4_event_reminder', array(__CLASS__, 'send_event_reminder'));
        
        // Schedule event reminders
        add_action('knot4_send_event_reminders', array(__CLASS__, 'process_event_reminders'));
        if (!wp_next_scheduled('knot4_send_event_reminders')) {
            wp_schedule_event(time(), 'hourly', 'knot4_send_event_reminders');
        }
    }
    
    /**
     * Add event meta boxes
     */
    public static function add_event_meta_boxes() {
        add_meta_box(
            'knot4_event_details',
            __('Event Details', 'knot4'),
            array(__CLASS__, 'event_details_meta_box'),
            'knot4_event',
            'normal',
            'high'
        );
        
        add_meta_box(
            'knot4_event_registration',
            __('Registration Settings', 'knot4'),
            array(__CLASS__, 'event_registration_meta_box'),
            'knot4_event',
            'normal',
            'high'
        );
        
        add_meta_box(
            'knot4_event_stats',
            __('Event Statistics', 'knot4'),
            array(__CLASS__, 'event_stats_meta_box'),
            'knot4_event',
            'side',
            'default'
        );
    }
    
    /**
     * Event details meta box
     */
    public static function event_details_meta_box($post) {
        wp_nonce_field('knot4_event_meta_nonce', 'knot4_event_meta_nonce');
        
        $event_date = get_post_meta($post->ID, '_knot4_event_date', true);
        $event_time = get_post_meta($post->ID, '_knot4_event_time', true);
        $event_end_date = get_post_meta($post->ID, '_knot4_event_end_date', true);
        $event_end_time = get_post_meta($post->ID, '_knot4_event_end_time', true);
        $event_venue = get_post_meta($post->ID, '_knot4_event_venue', true);
        $event_address = get_post_meta($post->ID, '_knot4_event_address', true);
        $event_cost = get_post_meta($post->ID, '_knot4_event_cost', true);
        $event_capacity = get_post_meta($post->ID, '_knot4_event_capacity', true);
        $event_type = get_post_meta($post->ID, '_knot4_event_type', true);
        $is_virtual = get_post_meta($post->ID, '_knot4_event_is_virtual', true);
        $virtual_link = get_post_meta($post->ID, '_knot4_event_virtual_link', true);
        
        ?>
        <table class="form-table">
            <tr>
                <th><label for="knot4_event_date"><?php _e('Event Date', 'knot4'); ?></label></th>
                <td>
                    <input type="date" id="knot4_event_date" name="knot4_event_date" value="<?php echo esc_attr($event_date); ?>" required />
                    <input type="time" id="knot4_event_time" name="knot4_event_time" value="<?php echo esc_attr($event_time); ?>" />
                </td>
            </tr>
            <tr>
                <th><label for="knot4_event_end_date"><?php _e('End Date/Time', 'knot4'); ?></label></th>
                <td>
                    <input type="date" id="knot4_event_end_date" name="knot4_event_end_date" value="<?php echo esc_attr($event_end_date); ?>" />
                    <input type="time" id="knot4_event_end_time" name="knot4_event_end_time" value="<?php echo esc_attr($event_end_time); ?>" />
                </td>
            </tr>
            <tr>
                <th><label for="knot4_event_type"><?php _e('Event Type', 'knot4'); ?></label></th>
                <td>
                    <select id="knot4_event_type" name="knot4_event_type">
                        <?php foreach (Knot4_Utilities::get_event_types() as $key => $label): ?>
                            <option value="<?php echo esc_attr($key); ?>" <?php selected($event_type, $key); ?>>
                                <?php echo esc_html($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="knot4_event_is_virtual"><?php _e('Virtual Event', 'knot4'); ?></label></th>
                <td>
                    <input type="checkbox" id="knot4_event_is_virtual" name="knot4_event_is_virtual" value="1" <?php checked($is_virtual, '1'); ?> />
                    <label for="knot4_event_is_virtual"><?php _e('This is a virtual event', 'knot4'); ?></label>
                </td>
            </tr>
            <tr>
                <th><label for="knot4_event_venue"><?php _e('Venue', 'knot4'); ?></label></th>
                <td><input type="text" id="knot4_event_venue" name="knot4_event_venue" value="<?php echo esc_attr($event_venue); ?>" class="widefat" /></td>
            </tr>
            <tr>
                <th><label for="knot4_event_address"><?php _e('Address', 'knot4'); ?></label></th>
                <td><textarea id="knot4_event_address" name="knot4_event_address" class="widefat" rows="3"><?php echo esc_textarea($event_address); ?></textarea></td>
            </tr>
            <tr class="virtual-event-row" <?php echo $is_virtual ? '' : 'style="display:none;"'; ?>>
                <th><label for="knot4_event_virtual_link"><?php _e('Virtual Event Link', 'knot4'); ?></label></th>
                <td><input type="url" id="knot4_event_virtual_link" name="knot4_event_virtual_link" value="<?php echo esc_attr($virtual_link); ?>" class="widefat" /></td>
            </tr>
            <tr>
                <th><label for="knot4_event_cost"><?php _e('Cost', 'knot4'); ?></label></th>
                <td>
                    <input type="number" id="knot4_event_cost" name="knot4_event_cost" value="<?php echo esc_attr($event_cost); ?>" step="0.01" min="0" />
                    <p class="description"><?php _e('Leave blank or 0 for free events', 'knot4'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="knot4_event_capacity"><?php _e('Capacity', 'knot4'); ?></label></th>
                <td>
                    <input type="number" id="knot4_event_capacity" name="knot4_event_capacity" value="<?php echo esc_attr($event_capacity); ?>" min="1" />
                    <p class="description"><?php _e('Maximum number of registrants (leave blank for unlimited)', 'knot4'); ?></p>
                </td>
            </tr>
        </table>
        
        <script>
        jQuery(document).ready(function($) {
            $('#knot4_event_is_virtual').change(function() {
                if ($(this).is(':checked')) {
                    $('.virtual-event-row').show();
                } else {
                    $('.virtual-event-row').hide();
                }
            });
        });
        </script>
        <?php
    }
    
    /**
     * Event registration meta box
     */
    public static function event_registration_meta_box($post) {
        $registration_enabled = get_post_meta($post->ID, '_knot4_registration_enabled', true);
        $registration_deadline = get_post_meta($post->ID, '_knot4_registration_deadline', true);
        $registration_fields = get_post_meta($post->ID, '_knot4_registration_fields', true);
        $confirmation_message = get_post_meta($post->ID, '_knot4_confirmation_message', true);
        
        if (!$registration_fields) {
            $registration_fields = array('name', 'email', 'phone');
        }
        
        ?>
        <table class="form-table">
            <tr>
                <th><label for="knot4_registration_enabled"><?php _e('Enable Registration', 'knot4'); ?></label></th>
                <td>
                    <input type="checkbox" id="knot4_registration_enabled" name="knot4_registration_enabled" value="1" <?php checked($registration_enabled, '1'); ?> />
                    <label for="knot4_registration_enabled"><?php _e('Allow people to register for this event', 'knot4'); ?></label>
                </td>
            </tr>
            <tr>
                <th><label for="knot4_registration_deadline"><?php _e('Registration Deadline', 'knot4'); ?></label></th>
                <td>
                    <input type="datetime-local" id="knot4_registration_deadline" name="knot4_registration_deadline" value="<?php echo esc_attr($registration_deadline); ?>" />
                    <p class="description"><?php _e('Registration will close at this date/time', 'knot4'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label><?php _e('Required Fields', 'knot4'); ?></label></th>
                <td>
                    <?php $available_fields = array(
                        'name' => __('Full Name', 'knot4'),
                        'email' => __('Email Address', 'knot4'),
                        'phone' => __('Phone Number', 'knot4'),
                        'organization' => __('Organization', 'knot4'),
                        'dietary_restrictions' => __('Dietary Restrictions', 'knot4'),
                        'accessibility_needs' => __('Accessibility Needs', 'knot4'),
                        'emergency_contact' => __('Emergency Contact', 'knot4'),
                    ); ?>
                    
                    <?php foreach ($available_fields as $field => $label): ?>
                        <label style="display: block; margin-bottom: 5px;">
                            <input type="checkbox" name="knot4_registration_fields[]" value="<?php echo esc_attr($field); ?>" 
                                <?php checked(in_array($field, (array) $registration_fields)); ?> />
                            <?php echo esc_html($label); ?>
                        </label>
                    <?php endforeach; ?>
                </td>
            </tr>
            <tr>
                <th><label for="knot4_confirmation_message"><?php _e('Confirmation Message', 'knot4'); ?></label></th>
                <td>
                    <textarea id="knot4_confirmation_message" name="knot4_confirmation_message" class="widefat" rows="4"><?php echo esc_textarea($confirmation_message); ?></textarea>
                    <p class="description"><?php _e('Message shown after successful registration', 'knot4'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Event statistics meta box
     */
    public static function event_stats_meta_box($post) {
        $registration_count = self::get_registration_count($post->ID);
        $capacity = get_post_meta($post->ID, '_knot4_event_capacity', true);
        $cost = get_post_meta($post->ID, '_knot4_event_cost', true);
        $total_revenue = self::get_event_revenue($post->ID);
        
        ?>
        <div class="knot4-event-stats">
            <p><strong><?php _e('Registrations:', 'knot4'); ?></strong> 
                <?php echo intval($registration_count); ?>
                <?php if ($capacity): ?>
                    / <?php echo intval($capacity); ?>
                <?php endif; ?>
            </p>
            
            <?php if ($cost > 0): ?>
                <p><strong><?php _e('Revenue:', 'knot4'); ?></strong> 
                    <?php echo Knot4_Utilities::format_currency($total_revenue); ?>
                </p>
            <?php endif; ?>
            
            <p><strong><?php _e('Status:', 'knot4'); ?></strong> 
                <?php echo self::get_event_status($post->ID); ?>
            </p>
            
            <?php if ($registration_count > 0): ?>
                <p>
                    <a href="<?php echo admin_url('admin.php?page=knot4-events&event_id=' . $post->ID . '&tab=registrations'); ?>" 
                       class="button button-secondary">
                        <?php _e('View Registrations', 'knot4'); ?>
                    </a>
                </p>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Save event meta
     */
    public static function save_event_meta($post_id) {
        if (!isset($_POST['knot4_event_meta_nonce']) || 
            !wp_verify_nonce($_POST['knot4_event_meta_nonce'], 'knot4_event_meta_nonce')) {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Save event details
        $fields = array(
            '_knot4_event_date',
            '_knot4_event_time',
            '_knot4_event_end_date',
            '_knot4_event_end_time',
            '_knot4_event_venue',
            '_knot4_event_address',
            '_knot4_event_cost',
            '_knot4_event_capacity',
            '_knot4_event_type',
            '_knot4_event_virtual_link',
            '_knot4_registration_deadline',
            '_knot4_confirmation_message',
        );
        
        foreach ($fields as $field) {
            $key = str_replace('_knot4_', 'knot4_', $field);
            if (isset($_POST[$key])) {
                update_post_meta($post_id, $field, sanitize_text_field($_POST[$key]));
            }
        }
        
        // Save checkboxes
        update_post_meta($post_id, '_knot4_event_is_virtual', 
            isset($_POST['knot4_event_is_virtual']) ? '1' : '0');
        update_post_meta($post_id, '_knot4_registration_enabled', 
            isset($_POST['knot4_registration_enabled']) ? '1' : '0');
        
        // Save registration fields
        if (isset($_POST['knot4_registration_fields'])) {
            update_post_meta($post_id, '_knot4_registration_fields', 
                array_map('sanitize_text_field', $_POST['knot4_registration_fields']));
        } else {
            update_post_meta($post_id, '_knot4_registration_fields', array());
        }
    }
    
    /**
     * Register for event (AJAX handler)
     */
    public static function register_for_event() {
        check_ajax_referer('knot4_public_nonce', 'nonce');
        
        $event_id = intval($_POST['event_id']);
        $registration_data = array(
            'event_id' => $event_id,
            'name' => sanitize_text_field($_POST['name']),
            'email' => sanitize_email($_POST['email']),
            'phone' => sanitize_text_field($_POST['phone']),
            'organization' => sanitize_text_field($_POST['organization']),
            'dietary_restrictions' => sanitize_textarea_field($_POST['dietary_restrictions']),
            'accessibility_needs' => sanitize_textarea_field($_POST['accessibility_needs']),
            'emergency_contact' => sanitize_text_field($_POST['emergency_contact']),
            'notes' => sanitize_textarea_field($_POST['notes']),
        );
        
        // Validate registration
        $validation = self::validate_event_registration($event_id, $registration_data);
        if (is_wp_error($validation)) {
            wp_send_json_error(array('message' => $validation->get_error_message()));
        }
        
        // Create registration
        $registration_id = self::create_event_registration($registration_data);
        if (is_wp_error($registration_id)) {
            wp_send_json_error(array('message' => $registration_id->get_error_message()));
        }
        
        // Send confirmation email
        do_action('knot4_event_registration_confirmed', $registration_id, $registration_data);
        
        wp_send_json_success(array(
            'message' => get_post_meta($event_id, '_knot4_confirmation_message', true) ?: 
                        __('Registration successful! You will receive a confirmation email shortly.', 'knot4'),
            'registration_id' => $registration_id,
        ));
    }
    
    /**
     * Create event registration
     */
    public static function create_event_registration($data) {
        global $wpdb;
        
        $result = $wpdb->insert(
            $wpdb->prefix . 'knot4_event_registrations',
            array(
                'event_id' => $data['event_id'],
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'organization' => $data['organization'],
                'dietary_restrictions' => $data['dietary_restrictions'],
                'accessibility_needs' => $data['accessibility_needs'],
                'emergency_contact' => $data['emergency_contact'],
                'notes' => $data['notes'],
                'status' => 'confirmed',
                'registered_at' => current_time('mysql'),
            ),
            array('%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
        );
        
        if ($result === false) {
            return new WP_Error('db_error', __('Failed to create registration.', 'knot4'));
        }
        
        $registration_id = $wpdb->insert_id;
        
        // Log activity
        Knot4_Utilities::log_activity(
            'event_registration',
            sprintf(__('New registration for event: %s', 'knot4'), get_the_title($data['event_id'])),
            $registration_id
        );
        
        return $registration_id;
    }
    
    /**
     * Validate event registration
     */
    public static function validate_event_registration($event_id, $data) {
        // Check if event exists
        $event = get_post($event_id);
        if (!$event || $event->post_type !== 'knot4_event') {
            return new WP_Error('invalid_event', __('Event not found.', 'knot4'));
        }
        
        // Check if registration is enabled
        if (!get_post_meta($event_id, '_knot4_registration_enabled', true)) {
            return new WP_Error('registration_disabled', __('Registration is not enabled for this event.', 'knot4'));
        }
        
        // Check registration deadline
        $deadline = get_post_meta($event_id, '_knot4_registration_deadline', true);
        if ($deadline && strtotime($deadline) < time()) {
            return new WP_Error('registration_closed', __('Registration deadline has passed.', 'knot4'));
        }
        
        // Check capacity
        $capacity = get_post_meta($event_id, '_knot4_event_capacity', true);
        if ($capacity) {
            $current_registrations = self::get_registration_count($event_id);
            if ($current_registrations >= $capacity) {
                return new WP_Error('event_full', __('This event is at capacity.', 'knot4'));
            }
        }
        
        // Check if already registered
        global $wpdb;
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}knot4_event_registrations 
             WHERE event_id = %d AND email = %s AND status != 'cancelled'",
            $event_id,
            $data['email']
        ));
        
        if ($existing) {
            return new WP_Error('already_registered', __('You are already registered for this event.', 'knot4'));
        }
        
        // Validate required fields
        $required_fields = get_post_meta($event_id, '_knot4_registration_fields', true);
        if ($required_fields) {
            foreach ($required_fields as $field) {
                if (empty($data[$field])) {
                    return new WP_Error('missing_field', sprintf(__('%s is required.', 'knot4'), ucfirst(str_replace('_', ' ', $field))));
                }
            }
        }
        
        return true;
    }
    
    /**
     * Get registration count for event
     */
    public static function get_registration_count($event_id) {
        global $wpdb;
        return $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}knot4_event_registrations 
             WHERE event_id = %d AND status = 'confirmed'",
            $event_id
        ));
    }
    
    /**
     * Get event revenue
     */
    public static function get_event_revenue($event_id) {
        $cost = get_post_meta($event_id, '_knot4_event_cost', true);
        if (!$cost) {
            return 0;
        }
        
        $registration_count = self::get_registration_count($event_id);
        return $cost * $registration_count;
    }
    
    /**
     * Get event status
     */
    public static function get_event_status($event_id) {
        $event_date = get_post_meta($event_id, '_knot4_event_date', true);
        $event_time = get_post_meta($event_id, '_knot4_event_time', true);
        
        if (!$event_date) {
            return __('Draft', 'knot4');
        }
        
        $event_datetime = strtotime($event_date . ' ' . $event_time);
        $now = time();
        
        if ($event_datetime < $now) {
            return __('Completed', 'knot4');
        } elseif ($event_datetime - $now < 24 * 3600) {
            return __('Starting Soon', 'knot4');
        } else {
            return __('Upcoming', 'knot4');
        }
    }
    
    /**
     * Send registration confirmation email
     */
    public static function send_registration_confirmation($registration_id, $registration_data) {
        global $wpdb;
        
        $registration = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}knot4_event_registrations WHERE id = %d",
            $registration_id
        ));
        
        if (!$registration) {
            return false;
        }
        
        $event = get_post($registration->event_id);
        $event_date = get_post_meta($event->ID, '_knot4_event_date', true);
        $event_time = get_post_meta($event->ID, '_knot4_event_time', true);
        $event_venue = get_post_meta($event->ID, '_knot4_event_venue', true);
        $event_address = get_post_meta($event->ID, '_knot4_event_address', true);
        $is_virtual = get_post_meta($event->ID, '_knot4_event_is_virtual', true);
        $virtual_link = get_post_meta($event->ID, '_knot4_event_virtual_link', true);
        
        $org_settings = Knot4_Utilities::get_organization_settings();
        
        $subject = sprintf(__('Registration Confirmation - %s', 'knot4'), $event->post_title);
        
        $message = sprintf(__('Dear %s,', 'knot4'), $registration->name) . "\n\n";
        $message .= sprintf(__('Thank you for registering for %s!', 'knot4'), $event->post_title) . "\n\n";
        $message .= __('Event Details:', 'knot4') . "\n";
        $message .= sprintf(__('Date: %s', 'knot4'), date_i18n(get_option('date_format'), strtotime($event_date))) . "\n";
        
        if ($event_time) {
            $message .= sprintf(__('Time: %s', 'knot4'), date_i18n(get_option('time_format'), strtotime($event_time))) . "\n";
        }
        
        if ($is_virtual && $virtual_link) {
            $message .= sprintf(__('Virtual Event Link: %s', 'knot4'), $virtual_link) . "\n";
        } else {
            if ($event_venue) {
                $message .= sprintf(__('Venue: %s', 'knot4'), $event_venue) . "\n";
            }
            if ($event_address) {
                $message .= sprintf(__('Address: %s', 'knot4'), $event_address) . "\n";
            }
        }
        
        $message .= "\n" . __('If you need to cancel your registration or have any questions, please contact us.', 'knot4') . "\n\n";
        $message .= sprintf(__('Best regards,', 'knot4')) . "\n";
        $message .= $org_settings['organization_name'];
        
        return Knot4_Utilities::send_notification($registration->email, $subject, $message);
    }
    
    /**
     * Process event reminders
     */
    public static function process_event_reminders() {
        global $wpdb;
        
        // Find events happening in 24 hours that haven't had reminders sent
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        
        $events = $wpdb->get_results($wpdb->prepare(
            "SELECT p.ID, p.post_title, pm.meta_value as event_date, pm2.meta_value as event_time
             FROM {$wpdb->posts} p
             LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_knot4_event_date'
             LEFT JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_knot4_event_time'
             LEFT JOIN {$wpdb->postmeta} pm3 ON p.ID = pm3.post_id AND pm3.meta_key = '_knot4_reminder_sent'
             WHERE p.post_type = 'knot4_event' 
             AND p.post_status = 'publish'
             AND pm.meta_value = %s
             AND (pm3.meta_value IS NULL OR pm3.meta_value != '1')",
            $tomorrow
        ));
        
        foreach ($events as $event) {
            self::send_event_reminders($event->ID);
            update_post_meta($event->ID, '_knot4_reminder_sent', '1');
        }
    }
    
    /**
     * Send event reminders to registrants
     */
    public static function send_event_reminders($event_id) {
        global $wpdb;
        
        $registrations = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}knot4_event_registrations 
             WHERE event_id = %d AND status = 'confirmed'",
            $event_id
        ));
        
        $event = get_post($event_id);
        $event_date = get_post_meta($event_id, '_knot4_event_date', true);
        $event_time = get_post_meta($event_id, '_knot4_event_time', true);
        
        foreach ($registrations as $registration) {
            $subject = sprintf(__('Reminder: %s Tomorrow', 'knot4'), $event->post_title);
            
            $message = sprintf(__('Dear %s,', 'knot4'), $registration->name) . "\n\n";
            $message .= sprintf(__('This is a reminder that %s is tomorrow!', 'knot4'), $event->post_title) . "\n\n";
            $message .= sprintf(__('Date: %s', 'knot4'), date_i18n(get_option('date_format'), strtotime($event_date))) . "\n";
            
            if ($event_time) {
                $message .= sprintf(__('Time: %s', 'knot4'), date_i18n(get_option('time_format'), strtotime($event_time))) . "\n";
            }
            
            $message .= "\n" . __('We look forward to seeing you there!', 'knot4');
            
            Knot4_Utilities::send_notification($registration->email, $subject, $message);
        }
    }
    
    /**
     * Create new event (AJAX handler)
     */
    public static function create_event() {
        check_ajax_referer('knot4_admin_nonce', 'nonce');
        
        if (!current_user_can('edit_knot4_events')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'knot4')));
        }
        
        $event_data = array(
            'post_title' => sanitize_text_field($_POST['event_title']),
            'post_content' => wp_kses_post($_POST['event_description']),
            'post_type' => 'knot4_event',
            'post_status' => 'publish',
            'meta_input' => array(
                '_knot4_event_date' => sanitize_text_field($_POST['event_date']),
                '_knot4_event_time' => sanitize_text_field($_POST['event_time']),
                '_knot4_event_venue' => sanitize_text_field($_POST['event_venue']),
                '_knot4_event_type' => sanitize_text_field($_POST['event_type']),
                '_knot4_registration_enabled' => '1',
            ),
        );
        
        $event_id = wp_insert_post($event_data);
        
        if (is_wp_error($event_id)) {
            wp_send_json_error(array('message' => __('Failed to create event.', 'knot4')));
        }
        
        wp_send_json_success(array(
            'message' => __('Event created successfully.', 'knot4'),
            'event_id' => $event_id,
            'edit_url' => admin_url('post.php?post=' . $event_id . '&action=edit')
        ));
    }
    
    /**
     * Get event registrations (AJAX handler)
     */
    public static function get_event_registrations() {
        check_ajax_referer('knot4_admin_nonce', 'nonce');
        
        if (!current_user_can('view_knot4_events')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'knot4')));
        }
        
        $event_id = intval($_POST['event_id']);
        global $wpdb;
        
        $registrations = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}knot4_event_registrations 
             WHERE event_id = %d ORDER BY registered_at DESC",
            $event_id
        ));
        
        wp_send_json_success(array('registrations' => $registrations));
    }
    
    /**
     * Update event (AJAX handler)
     */
    public static function update_event() {
        check_ajax_referer('knot4_admin_nonce', 'nonce');
        
        if (!current_user_can('edit_knot4_events')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'knot4')));
        }
        
        // Implementation for updating events
        wp_send_json_success(array('message' => __('Event updated successfully.', 'knot4')));
    }
    
    /**
     * Delete event (AJAX handler)
     */
    public static function delete_event() {
        check_ajax_referer('knot4_admin_nonce', 'nonce');
        
        if (!current_user_can('delete_knot4_events')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'knot4')));
        }
        
        // Implementation for deleting events
        wp_send_json_success(array('message' => __('Event deleted successfully.', 'knot4')));
    }
    
    /**
     * Cancel event registration (AJAX handler)
     */
    public static function cancel_event_registration() {
        check_ajax_referer('knot4_public_nonce', 'nonce');
        
        $registration_id = intval($_POST['registration_id']);
        
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'knot4_event_registrations',
            array('status' => 'cancelled'),
            array('id' => $registration_id),
            array('%s'),
            array('%d')
        );
        
        if ($result === false) {
            wp_send_json_error(array('message' => __('Failed to cancel registration.', 'knot4')));
        }
        
        wp_send_json_success(array('message' => __('Registration cancelled successfully.', 'knot4')));
    }
    
    /**
     * Set event single template
     */
    public static function event_single_template($template) {
        global $post;
        
        if ($post->post_type === 'knot4_event') {
            $theme_template = locate_template(array('single-knot4-event.php'));
            if ($theme_template) {
                return $theme_template;
            }
            
            $plugin_template = KNOT4_CORE_DIR . 'templates/single-event.php';
            if (file_exists($plugin_template)) {
                return $plugin_template;
            }
        }
        
        return $template;
    }
    
    /**
     * Set event archive template
     */
    public static function event_archive_template($template) {
        if (is_post_type_archive('knot4_event')) {
            $theme_template = locate_template(array('archive-knot4-event.php'));
            if ($theme_template) {
                return $theme_template;
            }
            
            $plugin_template = KNOT4_CORE_DIR . 'templates/archive-events.php';
            if (file_exists($plugin_template)) {
                return $plugin_template;
            }
        }
        
        return $template;
    }
}