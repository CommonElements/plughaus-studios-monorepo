<?php
/**
 * Admin Settings for Knot4
 * 
 * @package Knot4
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Knot4_Admin_Settings {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_init', array($this, 'register_settings'));
    }
    
    /**
     * Register plugin settings
     */
    public function register_settings() {
        // Organization Settings
        register_setting('knot4_settings_group', 'knot4_organization_settings', array(
            'sanitize_callback' => array($this, 'sanitize_organization_settings'),
        ));
        
        // Payment Settings
        register_setting('knot4_settings_group', 'knot4_payment_settings', array(
            'sanitize_callback' => array($this, 'sanitize_payment_settings'),
        ));
        
        // Email Settings
        register_setting('knot4_settings_group', 'knot4_email_settings', array(
            'sanitize_callback' => array($this, 'sanitize_email_settings'),
        ));
        
        // Page Settings
        register_setting('knot4_settings_group', 'knot4_page_settings', array(
            'sanitize_callback' => array($this, 'sanitize_page_settings'),
        ));
        
        // Add settings sections
        add_settings_section(
            'knot4_organization_section',
            __('Organization Information', 'knot4'),
            array($this, 'organization_section_callback'),
            'knot4_settings'
        );
        
        add_settings_section(
            'knot4_payment_section',
            __('Payment Settings', 'knot4'),
            array($this, 'payment_section_callback'),
            'knot4_settings'
        );
        
        add_settings_section(
            'knot4_email_section',
            __('Email Settings', 'knot4'),
            array($this, 'email_section_callback'),
            'knot4_settings'
        );
        
        add_settings_section(
            'knot4_page_section',
            __('Page Designations', 'knot4'),
            array($this, 'page_section_callback'),
            'knot4_settings'
        );
        
        // Add settings fields
        $this->add_organization_fields();
        $this->add_payment_fields();
        $this->add_email_fields();
        $this->add_page_fields();
    }
    
    /**
     * Add organization settings fields
     */
    private function add_organization_fields() {
        add_settings_field(
            'organization_name',
            __('Organization Name', 'knot4'),
            array($this, 'text_field_callback'),
            'knot4_settings',
            'knot4_organization_section',
            array(
                'label_for' => 'organization_name',
                'option_name' => 'knot4_organization_settings',
                'field_name' => 'organization_name',
                'description' => __('Your organization\'s legal name', 'knot4'),
            )
        );
        
        add_settings_field(
            'organization_email',
            __('Contact Email', 'knot4'),
            array($this, 'email_field_callback'),
            'knot4_settings',
            'knot4_organization_section',
            array(
                'label_for' => 'organization_email',
                'option_name' => 'knot4_organization_settings',
                'field_name' => 'organization_email',
                'description' => __('Primary contact email address', 'knot4'),
            )
        );
        
        add_settings_field(
            'organization_phone',
            __('Phone Number', 'knot4'),
            array($this, 'text_field_callback'),
            'knot4_settings',
            'knot4_organization_section',
            array(
                'label_for' => 'organization_phone',
                'option_name' => 'knot4_organization_settings',
                'field_name' => 'organization_phone',
                'description' => __('Primary phone number', 'knot4'),
            )
        );
        
        add_settings_field(
            'organization_address',
            __('Mailing Address', 'knot4'),
            array($this, 'textarea_field_callback'),
            'knot4_settings',
            'knot4_organization_section',
            array(
                'label_for' => 'organization_address',
                'option_name' => 'knot4_organization_settings',
                'field_name' => 'organization_address',
                'description' => __('Full mailing address', 'knot4'),
            )
        );
        
        add_settings_field(
            'tax_id',
            __('Tax ID (EIN)', 'knot4'),
            array($this, 'text_field_callback'),
            'knot4_settings',
            'knot4_organization_section',
            array(
                'label_for' => 'tax_id',
                'option_name' => 'knot4_organization_settings',
                'field_name' => 'tax_id',
                'description' => __('Tax identification number for receipts', 'knot4'),
            )
        );
        
        add_settings_field(
            'currency',
            __('Currency', 'knot4'),
            array($this, 'select_field_callback'),
            'knot4_settings',
            'knot4_organization_section',
            array(
                'label_for' => 'currency',
                'option_name' => 'knot4_organization_settings',
                'field_name' => 'currency',
                'options' => array(
                    'USD' => __('US Dollar ($)', 'knot4'),
                    'EUR' => __('Euro (€)', 'knot4'),
                    'GBP' => __('British Pound (£)', 'knot4'),
                    'CAD' => __('Canadian Dollar (C$)', 'knot4'),
                    'AUD' => __('Australian Dollar (A$)', 'knot4'),
                ),
                'description' => __('Default currency for donations', 'knot4'),
            )
        );
    }
    
    /**
     * Add payment settings fields
     */
    private function add_payment_fields() {
        add_settings_field(
            'test_mode',
            __('Test Mode', 'knot4'),
            array($this, 'checkbox_field_callback'),
            'knot4_settings',
            'knot4_payment_section',
            array(
                'label_for' => 'test_mode',
                'option_name' => 'knot4_payment_settings',
                'field_name' => 'test_mode',
                'description' => __('Enable test mode for payments (no real charges)', 'knot4'),
            )
        );
        
        add_settings_field(
            'stripe_test_publishable_key',
            __('Stripe Test Publishable Key', 'knot4'),
            array($this, 'text_field_callback'),
            'knot4_settings',
            'knot4_payment_section',
            array(
                'label_for' => 'stripe_test_publishable_key',
                'option_name' => 'knot4_payment_settings',
                'field_name' => 'stripe_test_publishable_key',
                'description' => __('Starts with pk_test_', 'knot4'),
            )
        );
        
        add_settings_field(
            'stripe_test_secret_key',
            __('Stripe Test Secret Key', 'knot4'),
            array($this, 'password_field_callback'),
            'knot4_settings',
            'knot4_payment_section',
            array(
                'label_for' => 'stripe_test_secret_key',
                'option_name' => 'knot4_payment_settings',
                'field_name' => 'stripe_test_secret_key',
                'description' => __('Starts with sk_test_', 'knot4'),
            )
        );
        
        add_settings_field(
            'stripe_live_publishable_key',
            __('Stripe Live Publishable Key', 'knot4'),
            array($this, 'text_field_callback'),
            'knot4_settings',
            'knot4_payment_section',
            array(
                'label_for' => 'stripe_live_publishable_key',
                'option_name' => 'knot4_payment_settings',
                'field_name' => 'stripe_live_publishable_key',
                'description' => __('Starts with pk_live_', 'knot4'),
            )
        );
        
        add_settings_field(
            'stripe_live_secret_key',
            __('Stripe Live Secret Key', 'knot4'),
            array($this, 'password_field_callback'),
            'knot4_settings',
            'knot4_payment_section',
            array(
                'label_for' => 'stripe_live_secret_key',
                'option_name' => 'knot4_payment_settings',
                'field_name' => 'stripe_live_secret_key',
                'description' => __('Starts with sk_live_', 'knot4'),
            )
        );
    }
    
    /**
     * Add email settings fields
     */
    private function add_email_fields() {
        add_settings_field(
            'from_name',
            __('From Name', 'knot4'),
            array($this, 'text_field_callback'),
            'knot4_settings',
            'knot4_email_section',
            array(
                'label_for' => 'from_name',
                'option_name' => 'knot4_email_settings',
                'field_name' => 'from_name',
                'description' => __('Name that appears in outgoing emails', 'knot4'),
            )
        );
        
        add_settings_field(
            'from_email',
            __('From Email', 'knot4'),
            array($this, 'email_field_callback'),
            'knot4_settings',
            'knot4_email_section',
            array(
                'label_for' => 'from_email',
                'option_name' => 'knot4_email_settings',
                'field_name' => 'from_email',
                'description' => __('Email address for outgoing emails', 'knot4'),
            )
        );
        
        add_settings_field(
            'reply_to',
            __('Reply-To Email', 'knot4'),
            array($this, 'email_field_callback'),
            'knot4_settings',
            'knot4_email_section',
            array(
                'label_for' => 'reply_to',
                'option_name' => 'knot4_email_settings',
                'field_name' => 'reply_to',
                'description' => __('Email address for replies', 'knot4'),
            )
        );
    }
    
    /**
     * Add page settings fields
     */
    private function add_page_fields() {
        add_settings_field(
            'donation_page',
            __('Donation Page', 'knot4'),
            array($this, 'page_select_field_callback'),
            'knot4_settings',
            'knot4_page_section',
            array(
                'label_for' => 'donation_page',
                'option_name' => 'knot4_page_settings',
                'field_name' => 'donation_page',
                'description' => __('Select the page where donation forms will be displayed', 'knot4'),
                'create_text' => __('Create Donation Page', 'knot4'),
            )
        );
        
        add_settings_field(
            'donor_portal_page',
            __('Donor Portal Page', 'knot4'),
            array($this, 'page_select_field_callback'),
            'knot4_settings',
            'knot4_page_section',
            array(
                'label_for' => 'donor_portal_page',
                'option_name' => 'knot4_page_settings',
                'field_name' => 'donor_portal_page',
                'description' => __('Select the page where the donor portal will be displayed', 'knot4'),
                'create_text' => __('Create Donor Portal Page', 'knot4'),
            )
        );
        
        add_settings_field(
            'events_page',
            __('Events Page', 'knot4'),
            array($this, 'page_select_field_callback'),
            'knot4_settings',
            'knot4_page_section',
            array(
                'label_for' => 'events_page',
                'option_name' => 'knot4_page_settings',
                'field_name' => 'events_page',
                'description' => __('Select the page where events will be displayed', 'knot4'),
                'create_text' => __('Create Events Page', 'knot4'),
            )
        );
        
        add_settings_field(
            'volunteer_page',
            __('Volunteer Page', 'knot4'),
            array($this, 'page_select_field_callback'),
            'knot4_settings',
            'knot4_page_section',
            array(
                'label_for' => 'volunteer_page',
                'option_name' => 'knot4_page_settings',
                'field_name' => 'volunteer_page',
                'description' => __('Select the page where volunteer forms will be displayed', 'knot4'),
                'create_text' => __('Create Volunteer Page', 'knot4'),
            )
        );
        
        add_settings_field(
            'thank_you_page',
            __('Thank You Page', 'knot4'),
            array($this, 'page_select_field_callback'),
            'knot4_settings',
            'knot4_page_section',
            array(
                'label_for' => 'thank_you_page',
                'option_name' => 'knot4_page_settings',
                'field_name' => 'thank_you_page',
                'description' => __('Select the page where users are redirected after donations', 'knot4'),
                'create_text' => __('Create Thank You Page', 'knot4'),
            )
        );
        
        add_settings_field(
            'newsletter_page',
            __('Newsletter Page', 'knot4'),
            array($this, 'page_select_field_callback'),
            'knot4_settings',
            'knot4_page_section',
            array(
                'label_for' => 'newsletter_page',
                'option_name' => 'knot4_page_settings',
                'field_name' => 'newsletter_page',
                'description' => __('Select the page for newsletter signup confirmation', 'knot4'),
                'create_text' => __('Create Newsletter Page', 'knot4'),
            )
        );
    }
    
    /**
     * Section callbacks
     */
    public function organization_section_callback() {
        echo '<p>' . __('Configure your organization\'s basic information.', 'knot4') . '</p>';
    }
    
    public function payment_section_callback() {
        echo '<p>' . __('Configure payment gateway settings.', 'knot4') . '</p>';
    }
    
    public function email_section_callback() {
        echo '<p>' . __('Configure email settings for notifications.', 'knot4') . '</p>';
    }
    
    public function page_section_callback() {
        echo '<p>' . __('Designate which pages display Knot4 content or create new pages automatically.', 'knot4') . '</p>';
    }
    
    /**
     * Field callbacks
     */
    public function text_field_callback($args) {
        $options = get_option($args['option_name'], array());
        $value = isset($options[$args['field_name']]) ? $options[$args['field_name']] : '';
        
        printf(
            '<input type="text" id="%1$s" name="%2$s[%3$s]" value="%4$s" class="regular-text" />',
            esc_attr($args['label_for']),
            esc_attr($args['option_name']),
            esc_attr($args['field_name']),
            esc_attr($value)
        );
        
        if (!empty($args['description'])) {
            printf('<p class="description">%s</p>', esc_html($args['description']));
        }
    }
    
    public function email_field_callback($args) {
        $options = get_option($args['option_name'], array());
        $value = isset($options[$args['field_name']]) ? $options[$args['field_name']] : '';
        
        printf(
            '<input type="email" id="%1$s" name="%2$s[%3$s]" value="%4$s" class="regular-text" />',
            esc_attr($args['label_for']),
            esc_attr($args['option_name']),
            esc_attr($args['field_name']),
            esc_attr($value)
        );
        
        if (!empty($args['description'])) {
            printf('<p class="description">%s</p>', esc_html($args['description']));
        }
    }
    
    public function password_field_callback($args) {
        $options = get_option($args['option_name'], array());
        $value = isset($options[$args['field_name']]) ? $options[$args['field_name']] : '';
        
        printf(
            '<input type="password" id="%1$s" name="%2$s[%3$s]" value="%4$s" class="regular-text" />',
            esc_attr($args['label_for']),
            esc_attr($args['option_name']),
            esc_attr($args['field_name']),
            esc_attr($value)
        );
        
        if (!empty($args['description'])) {
            printf('<p class="description">%s</p>', esc_html($args['description']));
        }
    }
    
    public function textarea_field_callback($args) {
        $options = get_option($args['option_name'], array());
        $value = isset($options[$args['field_name']]) ? $options[$args['field_name']] : '';
        
        printf(
            '<textarea id="%1$s" name="%2$s[%3$s]" rows="4" class="large-text">%4$s</textarea>',
            esc_attr($args['label_for']),
            esc_attr($args['option_name']),
            esc_attr($args['field_name']),
            esc_textarea($value)
        );
        
        if (!empty($args['description'])) {
            printf('<p class="description">%s</p>', esc_html($args['description']));
        }
    }
    
    public function select_field_callback($args) {
        $options = get_option($args['option_name'], array());
        $value = isset($options[$args['field_name']]) ? $options[$args['field_name']] : '';
        
        printf('<select id="%1$s" name="%2$s[%3$s]">', 
            esc_attr($args['label_for']),
            esc_attr($args['option_name']),
            esc_attr($args['field_name'])
        );
        
        foreach ($args['options'] as $option_value => $option_label) {
            printf(
                '<option value="%1$s" %2$s>%3$s</option>',
                esc_attr($option_value),
                selected($value, $option_value, false),
                esc_html($option_label)
            );
        }
        
        echo '</select>';
        
        if (!empty($args['description'])) {
            printf('<p class="description">%s</p>', esc_html($args['description']));
        }
    }
    
    public function checkbox_field_callback($args) {
        $options = get_option($args['option_name'], array());
        $value = isset($options[$args['field_name']]) ? $options[$args['field_name']] : false;
        
        printf(
            '<input type="checkbox" id="%1$s" name="%2$s[%3$s]" value="1" %4$s />',
            esc_attr($args['label_for']),
            esc_attr($args['option_name']),
            esc_attr($args['field_name']),
            checked(1, $value, false)
        );
        
        if (!empty($args['description'])) {
            printf('<label for="%1$s"> %2$s</label>', esc_attr($args['label_for']), esc_html($args['description']));
        }
    }
    
    public function page_select_field_callback($args) {
        $options = get_option($args['option_name'], array());
        $value = isset($options[$args['field_name']]) ? $options[$args['field_name']] : '';
        
        // Get all pages
        $pages = get_pages(array(
            'post_status' => 'publish',
            'sort_column' => 'post_title',
            'sort_order' => 'ASC'
        ));
        
        echo '<div class="knot4-page-select-container">';
        
        printf('<select id="%1$s" name="%2$s[%3$s]" class="regular-text">', 
            esc_attr($args['label_for']),
            esc_attr($args['option_name']),
            esc_attr($args['field_name'])
        );
        
        printf('<option value="">%s</option>', __('Select a page...', 'knot4'));
        
        foreach ($pages as $page) {
            printf(
                '<option value="%1$s" %2$s>%3$s</option>',
                esc_attr($page->ID),
                selected($value, $page->ID, false),
                esc_html($page->post_title)
            );
        }
        
        echo '</select>';
        
        // Create page button
        printf(
            '<button type="button" class="button knot4-create-page" data-page-type="%s" data-field-id="%s">%s</button>',
            esc_attr($args['field_name']),
            esc_attr($args['label_for']),
            esc_html($args['create_text'])
        );
        
        echo '</div>';
        
        if (!empty($args['description'])) {
            printf('<p class="description">%s</p>', esc_html($args['description']));
        }
        
        // Show current page link if selected
        if (!empty($value)) {
            $page_url = get_permalink($value);
            if ($page_url) {
                printf(
                    '<p class="description"><a href="%s" target="_blank">%s</a></p>',
                    esc_url($page_url),
                    sprintf(__('View: %s', 'knot4'), get_the_title($value))
                );
            }
        }
    }
    
    /**
     * Sanitization callbacks
     */
    public function sanitize_organization_settings($input) {
        $sanitized = array();
        
        if (isset($input['organization_name'])) {
            $sanitized['organization_name'] = sanitize_text_field($input['organization_name']);
        }
        
        if (isset($input['organization_email'])) {
            $sanitized['organization_email'] = sanitize_email($input['organization_email']);
        }
        
        if (isset($input['organization_phone'])) {
            $sanitized['organization_phone'] = sanitize_text_field($input['organization_phone']);
        }
        
        if (isset($input['organization_address'])) {
            $sanitized['organization_address'] = sanitize_textarea_field($input['organization_address']);
        }
        
        if (isset($input['tax_id'])) {
            $sanitized['tax_id'] = sanitize_text_field($input['tax_id']);
        }
        
        if (isset($input['currency'])) {
            $sanitized['currency'] = sanitize_text_field($input['currency']);
        }
        
        return $sanitized;
    }
    
    public function sanitize_payment_settings($input) {
        $sanitized = array();
        
        if (isset($input['test_mode'])) {
            $sanitized['test_mode'] = (bool) $input['test_mode'];
        }
        
        $stripe_keys = array(
            'stripe_test_publishable_key',
            'stripe_test_secret_key',
            'stripe_live_publishable_key',
            'stripe_live_secret_key',
        );
        
        foreach ($stripe_keys as $key) {
            if (isset($input[$key])) {
                $sanitized[$key] = sanitize_text_field($input[$key]);
            }
        }
        
        return $sanitized;
    }
    
    public function sanitize_email_settings($input) {
        $sanitized = array();
        
        if (isset($input['from_name'])) {
            $sanitized['from_name'] = sanitize_text_field($input['from_name']);
        }
        
        if (isset($input['from_email'])) {
            $sanitized['from_email'] = sanitize_email($input['from_email']);
        }
        
        if (isset($input['reply_to'])) {
            $sanitized['reply_to'] = sanitize_email($input['reply_to']);
        }
        
        return $sanitized;
    }
    
    public function sanitize_page_settings($input) {
        $sanitized = array();
        
        $page_fields = array(
            'donation_page',
            'donor_portal_page', 
            'events_page',
            'volunteer_page',
            'thank_you_page',
            'newsletter_page'
        );
        
        foreach ($page_fields as $field) {
            if (isset($input[$field])) {
                $page_id = intval($input[$field]);
                if ($page_id > 0 && get_post_status($page_id) === 'publish') {
                    $sanitized[$field] = $page_id;
                }
            }
        }
        
        return $sanitized;
    }
    
    /**
     * AJAX handler to create pages
     */
    public static function create_page_ajax_handler() {
        check_ajax_referer('knot4_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'knot4'));
        }
        
        $page_type = sanitize_text_field($_POST['page_type']);
        $page_data = self::get_page_template_data($page_type);
        
        if (!$page_data) {
            wp_send_json_error(__('Invalid page type.', 'knot4'));
        }
        
        $page_id = wp_insert_post(array(
            'post_title' => $page_data['title'],
            'post_content' => $page_data['content'],
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_author' => get_current_user_id(),
        ));
        
        if (is_wp_error($page_id)) {
            wp_send_json_error(__('Failed to create page.', 'knot4'));
        }
        
        wp_send_json_success(array(
            'page_id' => $page_id,
            'page_title' => $page_data['title'],
            'page_url' => get_permalink($page_id)
        ));
    }
    
    /**
     * Get page template data for different page types
     */
    private static function get_page_template_data($page_type) {
        switch ($page_type) {
            case 'donation_page':
                return array(
                    'title' => __('Donate', 'knot4'),
                    'content' => '<!-- wp:knot4/donation-form /-->'
                );
                
            case 'donor_portal_page':
                return array(
                    'title' => __('Donor Portal', 'knot4'),
                    'content' => '<!-- wp:knot4/donor-portal /-->'
                );
                
            case 'events_page':
                return array(
                    'title' => __('Events', 'knot4'),
                    'content' => '<!-- wp:knot4/events-list /-->'
                );
                
            case 'volunteer_page':
                return array(
                    'title' => __('Volunteer', 'knot4'),
                    'content' => '<!-- wp:knot4/volunteer-form /-->'
                );
                
            case 'thank_you_page':
                return array(
                    'title' => __('Thank You', 'knot4'),
                    'content' => '<!-- wp:heading {"textAlign":"center"} -->' . "\n" .
                               '<h2 class="wp-block-heading has-text-align-center">' . __('Thank You for Your Donation!', 'knot4') . '</h2>' . "\n" .
                               '<!-- /wp:heading -->' . "\n\n" .
                               '<!-- wp:paragraph {"align":"center"} -->' . "\n" .
                               '<p class="has-text-align-center">' . __('Your generous contribution makes a real difference. You will receive a confirmation email shortly.', 'knot4') . '</p>' . "\n" .
                               '<!-- /wp:paragraph -->' . "\n\n" .
                               '<!-- wp:knot4/donation-total /-->'
                );
                
            case 'newsletter_page':
                return array(
                    'title' => __('Newsletter Signup', 'knot4'),
                    'content' => '<!-- wp:heading {"textAlign":"center"} -->' . "\n" .
                               '<h2 class="wp-block-heading has-text-align-center">' . __('Stay Connected', 'knot4') . '</h2>' . "\n" .
                               '<!-- /wp:heading -->' . "\n\n" .
                               '<!-- wp:knot4/newsletter-signup /-->'
                );
                
            default:
                return false;
        }
    }
}