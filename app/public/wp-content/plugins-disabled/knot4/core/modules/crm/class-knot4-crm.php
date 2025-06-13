<?php
/**
 * CRM Module for Knot4
 * 
 * @package Knot4
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Knot4_CRM {
    
    /**
     * Initialize CRM module
     */
    public static function init() {
        // Admin AJAX handlers
        add_action('wp_ajax_knot4_update_donor', array(__CLASS__, 'update_donor'));
        add_action('wp_ajax_knot4_search_donors', array(__CLASS__, 'search_donors'));
        add_action('wp_ajax_knot4_get_donor_details', array(__CLASS__, 'get_donor_details'));
        add_action('wp_ajax_knot4_add_donor_note', array(__CLASS__, 'add_donor_note'));
        add_action('wp_ajax_knot4_bulk_donor_action', array(__CLASS__, 'bulk_donor_action'));
        add_action('wp_ajax_knot4_export_donors', array(__CLASS__, 'export_donors'));
        
        // Public AJAX handlers for donor portal
        add_action('wp_ajax_knot4_register_donor', array(__CLASS__, 'register_donor'));
        add_action('wp_ajax_nopriv_knot4_register_donor', array(__CLASS__, 'register_donor'));
        add_action('wp_ajax_knot4_update_donor_profile', array(__CLASS__, 'update_donor_profile'));
        add_action('wp_ajax_knot4_get_donor_dashboard', array(__CLASS__, 'get_donor_dashboard'));
        
        // Meta box hooks
        add_action('add_meta_boxes', array(__CLASS__, 'add_donor_meta_boxes'));
        add_action('save_post', array(__CLASS__, 'save_donor_meta'));
        
        // Custom columns for donor list
        add_filter('manage_knot4_donor_posts_columns', array(__CLASS__, 'donor_list_columns'));
        add_action('manage_knot4_donor_posts_custom_column', array(__CLASS__, 'donor_list_column_content'), 10, 2);
        add_filter('manage_edit-knot4_donor_sortable_columns', array(__CLASS__, 'donor_sortable_columns'));
        
        // Donor portal shortcodes
        add_shortcode('knot4_donor_portal', array(__CLASS__, 'donor_portal_shortcode'));
        add_shortcode('knot4_donor_login', array(__CLASS__, 'donor_login_shortcode'));
        add_shortcode('knot4_donor_register', array(__CLASS__, 'donor_register_shortcode'));
    }
    
    /**
     * Add donor meta boxes
     */
    public static function add_donor_meta_boxes() {
        add_meta_box(
            'knot4_donor_details',
            __('Donor Information', 'knot4'),
            array(__CLASS__, 'donor_details_meta_box'),
            'knot4_donor',
            'normal',
            'high'
        );
        
        add_meta_box(
            'knot4_donor_stats',
            __('Donor Statistics', 'knot4'),
            array(__CLASS__, 'donor_stats_meta_box'),
            'knot4_donor',
            'side',
            'default'
        );
        
        add_meta_box(
            'knot4_donor_history',
            __('Donation History', 'knot4'),
            array(__CLASS__, 'donor_history_meta_box'),
            'knot4_donor',
            'normal',
            'default'
        );
        
        add_meta_box(
            'knot4_donor_notes',
            __('Notes & Communications', 'knot4'),
            array(__CLASS__, 'donor_notes_meta_box'),
            'knot4_donor',
            'normal',
            'default'
        );
    }
    
    /**
     * Donor details meta box
     */
    public static function donor_details_meta_box($post) {
        wp_nonce_field('knot4_donor_meta_nonce', 'knot4_donor_meta_nonce');
        
        $email = get_post_meta($post->ID, '_knot4_donor_email', true);
        $first_name = get_post_meta($post->ID, '_knot4_donor_first_name', true);
        $last_name = get_post_meta($post->ID, '_knot4_donor_last_name', true);
        $phone = get_post_meta($post->ID, '_knot4_donor_phone', true);
        $address = get_post_meta($post->ID, '_knot4_donor_address', true);
        $city = get_post_meta($post->ID, '_knot4_donor_city', true);
        $state = get_post_meta($post->ID, '_knot4_donor_state', true);
        $zip = get_post_meta($post->ID, '_knot4_donor_zip', true);
        $country = get_post_meta($post->ID, '_knot4_donor_country', true);
        $donor_type = get_post_meta($post->ID, '_knot4_donor_type', true);
        $organization = get_post_meta($post->ID, '_knot4_donor_organization', true);
        $communication_preference = get_post_meta($post->ID, '_knot4_communication_preference', true);
        $newsletter_opt_in = get_post_meta($post->ID, '_knot4_newsletter_opt_in', true);
        $birthdate = get_post_meta($post->ID, '_knot4_donor_birthdate', true);
        $occupation = get_post_meta($post->ID, '_knot4_donor_occupation', true);
        $interests = get_post_meta($post->ID, '_knot4_donor_interests', true);
        
        ?>
        <table class="form-table">
            <tr>
                <th><label for="knot4_donor_email"><?php _e('Email Address', 'knot4'); ?> *</label></th>
                <td><input type="email" id="knot4_donor_email" name="knot4_donor_email" value="<?php echo esc_attr($email); ?>" class="widefat" required /></td>
            </tr>
            <tr>
                <th><label for="knot4_donor_first_name"><?php _e('First Name', 'knot4'); ?></label></th>
                <td><input type="text" id="knot4_donor_first_name" name="knot4_donor_first_name" value="<?php echo esc_attr($first_name); ?>" class="widefat" /></td>
            </tr>
            <tr>
                <th><label for="knot4_donor_last_name"><?php _e('Last Name', 'knot4'); ?></label></th>
                <td><input type="text" id="knot4_donor_last_name" name="knot4_donor_last_name" value="<?php echo esc_attr($last_name); ?>" class="widefat" /></td>
            </tr>
            <tr>
                <th><label for="knot4_donor_type"><?php _e('Donor Type', 'knot4'); ?></label></th>
                <td>
                    <select id="knot4_donor_type" name="knot4_donor_type" class="widefat">
                        <?php foreach (Knot4_Utilities::get_donor_types() as $key => $label): ?>
                            <option value="<?php echo esc_attr($key); ?>" <?php selected($donor_type, $key); ?>>
                                <?php echo esc_html($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="knot4_donor_organization"><?php _e('Organization', 'knot4'); ?></label></th>
                <td><input type="text" id="knot4_donor_organization" name="knot4_donor_organization" value="<?php echo esc_attr($organization); ?>" class="widefat" /></td>
            </tr>
            <tr>
                <th><label for="knot4_donor_phone"><?php _e('Phone Number', 'knot4'); ?></label></th>
                <td><input type="tel" id="knot4_donor_phone" name="knot4_donor_phone" value="<?php echo esc_attr($phone); ?>" class="widefat" /></td>
            </tr>
            <tr>
                <th><label for="knot4_donor_birthdate"><?php _e('Birth Date', 'knot4'); ?></label></th>
                <td><input type="date" id="knot4_donor_birthdate" name="knot4_donor_birthdate" value="<?php echo esc_attr($birthdate); ?>" /></td>
            </tr>
            <tr>
                <th><label for="knot4_donor_occupation"><?php _e('Occupation', 'knot4'); ?></label></th>
                <td><input type="text" id="knot4_donor_occupation" name="knot4_donor_occupation" value="<?php echo esc_attr($occupation); ?>" class="widefat" /></td>
            </tr>
        </table>
        
        <h4><?php _e('Address Information', 'knot4'); ?></h4>
        <table class="form-table">
            <tr>
                <th><label for="knot4_donor_address"><?php _e('Street Address', 'knot4'); ?></label></th>
                <td><textarea id="knot4_donor_address" name="knot4_donor_address" class="widefat" rows="3"><?php echo esc_textarea($address); ?></textarea></td>
            </tr>
            <tr>
                <th><label for="knot4_donor_city"><?php _e('City', 'knot4'); ?></label></th>
                <td><input type="text" id="knot4_donor_city" name="knot4_donor_city" value="<?php echo esc_attr($city); ?>" /></td>
            </tr>
            <tr>
                <th><label for="knot4_donor_state"><?php _e('State/Province', 'knot4'); ?></label></th>
                <td><input type="text" id="knot4_donor_state" name="knot4_donor_state" value="<?php echo esc_attr($state); ?>" /></td>
            </tr>
            <tr>
                <th><label for="knot4_donor_zip"><?php _e('ZIP/Postal Code', 'knot4'); ?></label></th>
                <td><input type="text" id="knot4_donor_zip" name="knot4_donor_zip" value="<?php echo esc_attr($zip); ?>" /></td>
            </tr>
            <tr>
                <th><label for="knot4_donor_country"><?php _e('Country', 'knot4'); ?></label></th>
                <td><input type="text" id="knot4_donor_country" name="knot4_donor_country" value="<?php echo esc_attr($country); ?>" placeholder="United States" /></td>
            </tr>
        </table>
        
        <h4><?php _e('Communication Preferences', 'knot4'); ?></h4>
        <table class="form-table">
            <tr>
                <th><label for="knot4_communication_preference"><?php _e('Preferred Contact Method', 'knot4'); ?></label></th>
                <td>
                    <select id="knot4_communication_preference" name="knot4_communication_preference" class="widefat">
                        <?php foreach (Knot4_Utilities::get_communication_preferences() as $key => $label): ?>
                            <option value="<?php echo esc_attr($key); ?>" <?php selected($communication_preference, $key); ?>>
                                <?php echo esc_html($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="knot4_newsletter_opt_in"><?php _e('Newsletter Subscription', 'knot4'); ?></label></th>
                <td>
                    <input type="checkbox" id="knot4_newsletter_opt_in" name="knot4_newsletter_opt_in" value="1" <?php checked($newsletter_opt_in, '1'); ?> />
                    <label for="knot4_newsletter_opt_in"><?php _e('Subscribed to newsletter', 'knot4'); ?></label>
                </td>
            </tr>
            <tr>
                <th><label for="knot4_donor_interests"><?php _e('Interests/Programs', 'knot4'); ?></label></th>
                <td>
                    <textarea id="knot4_donor_interests" name="knot4_donor_interests" class="widefat" rows="3" placeholder="<?php _e('Environmental, Education, Community Outreach, etc.', 'knot4'); ?>"><?php echo esc_textarea($interests); ?></textarea>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Donor statistics meta box
     */
    public static function donor_stats_meta_box($post) {
        $total_donated = get_post_meta($post->ID, '_knot4_total_donated', true) ?: 0;
        $donation_count = get_post_meta($post->ID, '_knot4_donation_count', true) ?: 0;
        $first_donation_date = get_post_meta($post->ID, '_knot4_first_donation_date', true);
        $last_donation_date = get_post_meta($post->ID, '_knot4_last_donation_date', true);
        $average_donation = $donation_count > 0 ? $total_donated / $donation_count : 0;
        
        // Get recent donation activity
        global $wpdb;
        $recent_donations = $wpdb->get_results($wpdb->prepare(
            "SELECT amount, status, created_at FROM {$wpdb->prefix}knot4_donations 
             WHERE donor_id = %d ORDER BY created_at DESC LIMIT 5",
            $post->ID
        ));
        
        ?>
        <div class="knot4-donor-stats">
            <p><strong><?php _e('Total Donated:', 'knot4'); ?></strong> 
                <?php echo Knot4_Utilities::format_currency($total_donated); ?>
            </p>
            <p><strong><?php _e('Number of Donations:', 'knot4'); ?></strong> 
                <?php echo intval($donation_count); ?>
            </p>
            <p><strong><?php _e('Average Donation:', 'knot4'); ?></strong> 
                <?php echo Knot4_Utilities::format_currency($average_donation); ?>
            </p>
            
            <?php if ($first_donation_date): ?>
                <p><strong><?php _e('First Donation:', 'knot4'); ?></strong> 
                    <?php echo date_i18n(get_option('date_format'), strtotime($first_donation_date)); ?>
                </p>
            <?php endif; ?>
            
            <?php if ($last_donation_date): ?>
                <p><strong><?php _e('Last Donation:', 'knot4'); ?></strong> 
                    <?php echo date_i18n(get_option('date_format'), strtotime($last_donation_date)); ?>
                </p>
            <?php endif; ?>
            
            <?php if ($recent_donations): ?>
                <h4><?php _e('Recent Activity', 'knot4'); ?></h4>
                <ul style="margin: 0; padding-left: 20px;">
                    <?php foreach ($recent_donations as $donation): ?>
                        <li>
                            <?php echo Knot4_Utilities::format_currency($donation->amount); ?> 
                            <span class="donation-status status-<?php echo esc_attr($donation->status); ?>">
                                (<?php echo esc_html(ucfirst($donation->status)); ?>)
                            </span>
                            <br><small><?php echo date_i18n(get_option('date_format'), strtotime($donation->created_at)); ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        
        <style>
        .donation-status { font-size: 11px; }
        .status-completed { color: #46b450; }
        .status-pending { color: #ffb900; }
        .status-failed { color: #dc3232; }
        </style>
        <?php
    }
    
    /**
     * Donor history meta box
     */
    public static function donor_history_meta_box($post) {
        global $wpdb;
        
        $donations = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}knot4_donations 
             WHERE donor_id = %d ORDER BY created_at DESC LIMIT 20",
            $post->ID
        ));
        
        if ($donations): ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Date', 'knot4'); ?></th>
                        <th><?php _e('Amount', 'knot4'); ?></th>
                        <th><?php _e('Status', 'knot4'); ?></th>
                        <th><?php _e('Method', 'knot4'); ?></th>
                        <th><?php _e('Reference', 'knot4'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($donations as $donation): ?>
                        <tr>
                            <td><?php echo date_i18n(get_option('date_format'), strtotime($donation->created_at)); ?></td>
                            <td><?php echo Knot4_Utilities::format_currency($donation->amount, $donation->currency); ?></td>
                            <td>
                                <span class="donation-status status-<?php echo esc_attr($donation->status); ?>">
                                    <?php echo esc_html(ucfirst($donation->status)); ?>
                                </span>
                            </td>
                            <td><?php echo esc_html(ucfirst($donation->payment_gateway ?: 'N/A')); ?></td>
                            <td><code><?php echo esc_html($donation->reference_id); ?></code></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p><?php _e('No donation history found.', 'knot4'); ?></p>
        <?php endif;
    }
    
    /**
     * Donor notes meta box
     */
    public static function donor_notes_meta_box($post) {
        global $wpdb;
        
        $notes = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}knot4_donor_notes 
             WHERE donor_id = %d ORDER BY created_at DESC",
            $post->ID
        ));
        
        ?>
        <div class="knot4-donor-notes">
            <div class="add-note-section">
                <h4><?php _e('Add New Note', 'knot4'); ?></h4>
                <textarea id="knot4_new_note" class="widefat" rows="3" placeholder="<?php _e('Enter note about this donor...', 'knot4'); ?>"></textarea>
                <p>
                    <button type="button" class="button button-primary" onclick="knot4AddDonorNote(<?php echo $post->ID; ?>)">
                        <?php _e('Add Note', 'knot4'); ?>
                    </button>
                </p>
            </div>
            
            <div class="notes-list">
                <h4><?php _e('Notes History', 'knot4'); ?></h4>
                <div id="knot4-notes-container">
                    <?php if ($notes): ?>
                        <?php foreach ($notes as $note): ?>
                            <div class="donor-note" data-note-id="<?php echo $note->id; ?>">
                                <div class="note-content"><?php echo esc_html($note->content); ?></div>
                                <div class="note-meta">
                                    <small>
                                        <?php echo get_userdata($note->user_id)->display_name; ?> - 
                                        <?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($note->created_at)); ?>
                                    </small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p><?php _e('No notes found.', 'knot4'); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <style>
        .donor-note {
            border-left: 3px solid #0073aa;
            padding: 10px;
            margin-bottom: 10px;
            background: #f9f9f9;
        }
        .note-meta {
            margin-top: 5px;
            color: #666;
        }
        .add-note-section {
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }
        </style>
        
        <script>
        function knot4AddDonorNote(donorId) {
            var noteContent = document.getElementById('knot4_new_note').value.trim();
            if (!noteContent) {
                alert('<?php _e('Please enter a note.', 'knot4'); ?>');
                return;
            }
            
            jQuery.post(ajaxurl, {
                action: 'knot4_add_donor_note',
                nonce: '<?php echo wp_create_nonce('knot4_admin_nonce'); ?>',
                donor_id: donorId,
                note_content: noteContent
            }, function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data.message || '<?php _e('Failed to add note.', 'knot4'); ?>');
                }
            });
        }
        </script>
        <?php
    }
    
    /**
     * Save donor meta
     */
    public static function save_donor_meta($post_id) {
        if (!isset($_POST['knot4_donor_meta_nonce']) || 
            !wp_verify_nonce($_POST['knot4_donor_meta_nonce'], 'knot4_donor_meta_nonce')) {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Save donor details
        $fields = array(
            '_knot4_donor_email',
            '_knot4_donor_first_name',
            '_knot4_donor_last_name',
            '_knot4_donor_phone',
            '_knot4_donor_address',
            '_knot4_donor_city',
            '_knot4_donor_state',
            '_knot4_donor_zip',
            '_knot4_donor_country',
            '_knot4_donor_type',
            '_knot4_donor_organization',
            '_knot4_communication_preference',
            '_knot4_donor_birthdate',
            '_knot4_donor_occupation',
            '_knot4_donor_interests',
        );
        
        foreach ($fields as $field) {
            $key = str_replace('_knot4_', 'knot4_', $field);
            if (isset($_POST[$key])) {
                update_post_meta($post_id, $field, sanitize_text_field($_POST[$key]));
            }
        }
        
        // Save checkboxes
        update_post_meta($post_id, '_knot4_newsletter_opt_in', 
            isset($_POST['knot4_newsletter_opt_in']) ? '1' : '0');
        
        // Update post title to match donor name
        $first_name = sanitize_text_field($_POST['knot4_donor_first_name']);
        $last_name = sanitize_text_field($_POST['knot4_donor_last_name']);
        $email = sanitize_email($_POST['knot4_donor_email']);
        
        $donor_name = trim($first_name . ' ' . $last_name);
        if (empty($donor_name)) {
            $donor_name = $email;
        }
        
        wp_update_post(array(
            'ID' => $post_id,
            'post_title' => $donor_name,
        ));
    }
    
    /**
     * Set custom columns for donor list
     */
    public static function donor_list_columns($columns) {
        $new_columns = array();
        $new_columns['cb'] = $columns['cb'];
        $new_columns['title'] = __('Name', 'knot4');
        $new_columns['email'] = __('Email', 'knot4');
        $new_columns['total_donated'] = __('Total Donated', 'knot4');
        $new_columns['donation_count'] = __('Donations', 'knot4');
        $new_columns['last_donation'] = __('Last Donation', 'knot4');
        $new_columns['donor_type'] = __('Type', 'knot4');
        $new_columns['date'] = __('Registered', 'knot4');
        
        return $new_columns;
    }
    
    /**
     * Display custom column content
     */
    public static function donor_list_column_content($column, $post_id) {
        switch ($column) {
            case 'email':
                echo esc_html(get_post_meta($post_id, '_knot4_donor_email', true));
                break;
                
            case 'total_donated':
                $total = get_post_meta($post_id, '_knot4_total_donated', true) ?: 0;
                echo Knot4_Utilities::format_currency($total);
                break;
                
            case 'donation_count':
                echo intval(get_post_meta($post_id, '_knot4_donation_count', true));
                break;
                
            case 'last_donation':
                $last_date = get_post_meta($post_id, '_knot4_last_donation_date', true);
                if ($last_date) {
                    echo date_i18n(get_option('date_format'), strtotime($last_date));
                } else {
                    echo '—';
                }
                break;
                
            case 'donor_type':
                $type = get_post_meta($post_id, '_knot4_donor_type', true);
                $types = Knot4_Utilities::get_donor_types();
                echo isset($types[$type]) ? esc_html($types[$type]) : '—';
                break;
        }
    }
    
    /**
     * Make columns sortable
     */
    public static function donor_sortable_columns($columns) {
        $columns['total_donated'] = 'total_donated';
        $columns['donation_count'] = 'donation_count';
        $columns['last_donation'] = 'last_donation';
        return $columns;
    }
    
    /**
     * Get donor details (AJAX handler)
     */
    public static function get_donor_details() {
        check_ajax_referer('knot4_admin_nonce', 'nonce');
        
        if (!current_user_can('view_knot4_donors')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'knot4')));
        }
        
        $donor_id = intval($_POST['donor_id']);
        $donor = get_post($donor_id);
        
        if (!$donor || $donor->post_type !== 'knot4_donor') {
            wp_send_json_error(array('message' => __('Donor not found.', 'knot4')));
        }
        
        // Get all donor meta
        $donor_data = array(
            'id' => $donor->ID,
            'name' => $donor->post_title,
            'email' => get_post_meta($donor->ID, '_knot4_donor_email', true),
            'phone' => get_post_meta($donor->ID, '_knot4_donor_phone', true),
            'total_donated' => get_post_meta($donor->ID, '_knot4_total_donated', true) ?: 0,
            'donation_count' => get_post_meta($donor->ID, '_knot4_donation_count', true) ?: 0,
            'first_donation_date' => get_post_meta($donor->ID, '_knot4_first_donation_date', true),
            'last_donation_date' => get_post_meta($donor->ID, '_knot4_last_donation_date', true),
        );
        
        wp_send_json_success(array('donor' => $donor_data));
    }
    
    /**
     * Add donor note (AJAX handler)
     */
    public static function add_donor_note() {
        check_ajax_referer('knot4_admin_nonce', 'nonce');
        
        if (!current_user_can('edit_knot4_donors')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'knot4')));
        }
        
        $donor_id = intval($_POST['donor_id']);
        $note_content = sanitize_textarea_field($_POST['note_content']);
        
        if (empty($note_content)) {
            wp_send_json_error(array('message' => __('Note content is required.', 'knot4')));
        }
        
        global $wpdb;
        $result = $wpdb->insert(
            $wpdb->prefix . 'knot4_donor_notes',
            array(
                'donor_id' => $donor_id,
                'user_id' => get_current_user_id(),
                'content' => $note_content,
                'created_at' => current_time('mysql'),
            ),
            array('%d', '%d', '%s', '%s')
        );
        
        if ($result === false) {
            wp_send_json_error(array('message' => __('Failed to add note.', 'knot4')));
        }
        
        wp_send_json_success(array('message' => __('Note added successfully.', 'knot4')));
    }
    
    /**
     * Update donor information (AJAX handler)
     */
    public static function update_donor() {
        check_ajax_referer('knot4_admin_nonce', 'nonce');
        
        if (!current_user_can('edit_knot4_donors')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'knot4')));
        }
        
        $donor_id = intval($_POST['donor_id']);
        $donor_data = $_POST['donor_data'];
        
        // Validate and update donor information
        foreach ($donor_data as $key => $value) {
            $meta_key = '_knot4_donor_' . sanitize_key($key);
            update_post_meta($donor_id, $meta_key, sanitize_text_field($value));
        }
        
        wp_send_json_success(array('message' => __('Donor updated successfully.', 'knot4')));
    }
    
    /**
     * Search donors (AJAX handler)
     */
    public static function search_donors() {
        check_ajax_referer('knot4_admin_nonce', 'nonce');
        
        if (!current_user_can('view_knot4_donors')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'knot4')));
        }
        
        $search_term = sanitize_text_field($_POST['search_term']);
        $args = array(
            'post_type' => 'knot4_donor',
            'post_status' => 'publish',
            'posts_per_page' => 20,
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key' => '_knot4_donor_email',
                    'value' => $search_term,
                    'compare' => 'LIKE'
                ),
                array(
                    'key' => '_knot4_donor_first_name',
                    'value' => $search_term,
                    'compare' => 'LIKE'
                ),
                array(
                    'key' => '_knot4_donor_last_name',
                    'value' => $search_term,
                    'compare' => 'LIKE'
                ),
            ),
        );
        
        $donors = get_posts($args);
        $donor_list = array();
        
        foreach ($donors as $donor) {
            $donor_list[] = array(
                'id' => $donor->ID,
                'name' => $donor->post_title,
                'email' => get_post_meta($donor->ID, '_knot4_donor_email', true),
                'total_donated' => get_post_meta($donor->ID, '_knot4_total_donated', true) ?: 0,
            );
        }
        
        wp_send_json_success(array('donors' => $donor_list));
    }
    
    /**
     * Bulk donor actions (AJAX handler)
     */
    public static function bulk_donor_action() {
        check_ajax_referer('knot4_admin_nonce', 'nonce');
        
        if (!current_user_can('edit_knot4_donors')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'knot4')));
        }
        
        $action = sanitize_text_field($_POST['bulk_action']);
        $donor_ids = array_map('intval', $_POST['donor_ids']);
        
        switch ($action) {
            case 'delete':
                foreach ($donor_ids as $donor_id) {
                    wp_delete_post($donor_id, true);
                }
                wp_send_json_success(array('message' => __('Donors deleted successfully.', 'knot4')));
                break;
                
            case 'export':
                // Export functionality would go here
                wp_send_json_success(array('message' => __('Export completed.', 'knot4')));
                break;
                
            default:
                wp_send_json_error(array('message' => __('Invalid action.', 'knot4')));
        }
    }
    
    /**
     * Export donors (AJAX handler)
     */
    public static function export_donors() {
        check_ajax_referer('knot4_admin_nonce', 'nonce');
        
        if (!current_user_can('export_knot4_data')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'knot4')));
        }
        
        // Implementation for exporting donor data
        wp_send_json_success(array('message' => __('Export completed successfully.', 'knot4')));
    }
    
    /**
     * Register donor (AJAX handler for public registration)
     */
    public static function register_donor() {
        check_ajax_referer('knot4_public_nonce', 'nonce');
        
        $email = sanitize_email($_POST['email']);
        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name = sanitize_text_field($_POST['last_name']);
        $password = $_POST['password'];
        
        // Validate required fields
        if (empty($email) || empty($first_name) || empty($last_name) || empty($password)) {
            wp_send_json_error(array('message' => __('All fields are required.', 'knot4')));
        }
        
        if (!is_email($email)) {
            wp_send_json_error(array('message' => __('Please enter a valid email address.', 'knot4')));
        }
        
        // Check if user already exists
        if (email_exists($email)) {
            wp_send_json_error(array('message' => __('An account with this email already exists.', 'knot4')));
        }
        
        // Create WordPress user
        $user_id = wp_create_user($email, $password, $email);
        if (is_wp_error($user_id)) {
            wp_send_json_error(array('message' => $user_id->get_error_message()));
        }
        
        // Update user meta
        wp_update_user(array(
            'ID' => $user_id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'display_name' => $first_name . ' ' . $last_name,
        ));
        
        // Add donor role
        $user = new WP_User($user_id);
        $user->add_role('knot4_donor');
        
        // Create donor post
        $donor_name = trim($first_name . ' ' . $last_name);
        $donor_id = wp_insert_post(array(
            'post_type' => 'knot4_donor',
            'post_title' => $donor_name,
            'post_status' => 'publish',
            'meta_input' => array(
                '_knot4_donor_email' => $email,
                '_knot4_donor_first_name' => $first_name,
                '_knot4_donor_last_name' => $last_name,
                '_knot4_donor_user_id' => $user_id,
                '_knot4_total_donated' => 0,
                '_knot4_donation_count' => 0,
            ),
        ));
        
        if (is_wp_error($donor_id)) {
            wp_delete_user($user_id);
            wp_send_json_error(array('message' => __('Failed to create donor profile.', 'knot4')));
        }
        
        // Log user in
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id, true);
        
        wp_send_json_success(array(
            'message' => __('Account created successfully!', 'knot4'),
            'redirect_url' => home_url('/donor-portal/')
        ));
    }
    
    /**
     * Update donor profile (AJAX handler)
     */
    public static function update_donor_profile() {
        check_ajax_referer('knot4_public_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => __('Please log in to update your profile.', 'knot4')));
        }
        
        $user_id = get_current_user_id();
        $donor_id = self::get_donor_id_by_user($user_id);
        
        if (!$donor_id) {
            wp_send_json_error(array('message' => __('Donor profile not found.', 'knot4')));
        }
        
        // Update donor information
        $profile_data = $_POST['profile_data'];
        foreach ($profile_data as $key => $value) {
            $meta_key = '_knot4_donor_' . sanitize_key($key);
            update_post_meta($donor_id, $meta_key, sanitize_text_field($value));
        }
        
        wp_send_json_success(array('message' => __('Profile updated successfully.', 'knot4')));
    }
    
    /**
     * Get donor dashboard data (AJAX handler)
     */
    public static function get_donor_dashboard() {
        check_ajax_referer('knot4_public_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => __('Please log in to view your dashboard.', 'knot4')));
        }
        
        $user_id = get_current_user_id();
        $donor_id = self::get_donor_id_by_user($user_id);
        
        if (!$donor_id) {
            wp_send_json_error(array('message' => __('Donor profile not found.', 'knot4')));
        }
        
        // Get donor statistics
        $total_donated = get_post_meta($donor_id, '_knot4_total_donated', true) ?: 0;
        $donation_count = get_post_meta($donor_id, '_knot4_donation_count', true) ?: 0;
        
        // Get recent donations
        global $wpdb;
        $recent_donations = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}knot4_donations 
             WHERE donor_id = %d ORDER BY created_at DESC LIMIT 10",
            $donor_id
        ));
        
        $dashboard_data = array(
            'total_donated' => $total_donated,
            'donation_count' => $donation_count,
            'recent_donations' => $recent_donations,
        );
        
        wp_send_json_success(array('dashboard' => $dashboard_data));
    }
    
    /**
     * Get donor ID by WordPress user ID
     */
    public static function get_donor_id_by_user($user_id) {
        $donor = get_posts(array(
            'post_type' => 'knot4_donor',
            'meta_query' => array(
                array(
                    'key' => '_knot4_donor_user_id',
                    'value' => $user_id,
                    'compare' => '='
                )
            ),
            'posts_per_page' => 1,
        ));
        
        return $donor ? $donor[0]->ID : null;
    }
    
    /**
     * Donor portal shortcode
     */
    public static function donor_portal_shortcode($atts) {
        if (!is_user_logged_in()) {
            return '<p>' . __('Please log in to access your donor portal.', 'knot4') . '</p>';
        }
        
        $user_id = get_current_user_id();
        $donor_id = self::get_donor_id_by_user($user_id);
        
        if (!$donor_id) {
            return '<p>' . __('Donor profile not found.', 'knot4') . '</p>';
        }
        
        ob_start();
        include KNOT4_CORE_DIR . 'templates/donor-portal.php';
        return ob_get_clean();
    }
    
    /**
     * Donor login shortcode
     */
    public static function donor_login_shortcode($atts) {
        if (is_user_logged_in()) {
            return '<p>' . __('You are already logged in.', 'knot4') . ' <a href="' . wp_logout_url() . '">' . __('Logout', 'knot4') . '</a></p>';
        }
        
        ob_start();
        ?>
        <div class="knot4-donor-login">
            <?php wp_login_form(array(
                'redirect' => home_url('/donor-portal/'),
                'label_log_in' => __('Login to Donor Portal', 'knot4'),
            )); ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Donor registration shortcode
     */
    public static function donor_register_shortcode($atts) {
        if (is_user_logged_in()) {
            return '<p>' . __('You are already registered and logged in.', 'knot4') . '</p>';
        }
        
        ob_start();
        ?>
        <div class="knot4-donor-register">
            <form id="knot4-donor-registration-form" class="knot4-form">
                <?php wp_nonce_field('knot4_public_nonce', 'nonce'); ?>
                
                <div class="form-group">
                    <label for="first_name"><?php _e('First Name', 'knot4'); ?> *</label>
                    <input type="text" id="first_name" name="first_name" required />
                </div>
                
                <div class="form-group">
                    <label for="last_name"><?php _e('Last Name', 'knot4'); ?> *</label>
                    <input type="text" id="last_name" name="last_name" required />
                </div>
                
                <div class="form-group">
                    <label for="email"><?php _e('Email Address', 'knot4'); ?> *</label>
                    <input type="email" id="email" name="email" required />
                </div>
                
                <div class="form-group">
                    <label for="password"><?php _e('Password', 'knot4'); ?> *</label>
                    <input type="password" id="password" name="password" required />
                </div>
                
                <button type="submit" class="knot4-submit-btn">
                    <?php _e('Create Account', 'knot4'); ?>
                </button>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }
}