<?php
/**
 * Gutenberg Blocks for Knot4
 * 
 * @package Knot4
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Knot4_Gutenberg_Blocks {
    
    /**
     * Initialize Gutenberg blocks
     */
    public static function init() {
        // Register blocks on init
        add_action('init', array(__CLASS__, 'register_blocks'));
        
        // Enqueue block editor assets
        add_action('enqueue_block_editor_assets', array(__CLASS__, 'enqueue_block_editor_assets'));
        
        // Add block categories
        add_filter('block_categories_all', array(__CLASS__, 'add_block_categories'), 10, 2);
        
        // Register REST API endpoints for block data
        add_action('rest_api_init', array(__CLASS__, 'register_rest_routes'));
    }
    
    /**
     * Register all Knot4 blocks
     */
    public static function register_blocks() {
        // Donation Form Block
        register_block_type('knot4/donation-form', array(
            'render_callback' => array(__CLASS__, 'render_donation_form_block'),
            'attributes' => array(
                'title' => array(
                    'type' => 'string',
                    'default' => __('Make a Donation', 'knot4')
                ),
                'suggestedAmounts' => array(
                    'type' => 'string',
                    'default' => '25,50,100,250'
                ),
                'allowCustom' => array(
                    'type' => 'boolean',
                    'default' => true
                ),
                'showFrequency' => array(
                    'type' => 'boolean',
                    'default' => true
                ),
                'showDedication' => array(
                    'type' => 'boolean',
                    'default' => true
                ),
                'showAddress' => array(
                    'type' => 'boolean',
                    'default' => false
                ),
                'showPhone' => array(
                    'type' => 'boolean',
                    'default' => false
                ),
                'campaignId' => array(
                    'type' => 'string',
                    'default' => ''
                ),
                'fundDesignation' => array(
                    'type' => 'string',
                    'default' => ''
                ),
                'buttonText' => array(
                    'type' => 'string',
                    'default' => __('Donate Now', 'knot4')
                ),
                'style' => array(
                    'type' => 'string',
                    'default' => 'default'
                )
            )
        ));
        
        // Donation Button Block
        register_block_type('knot4/donation-button', array(
            'render_callback' => array(__CLASS__, 'render_donation_button_block'),
            'attributes' => array(
                'text' => array(
                    'type' => 'string',
                    'default' => __('Donate Now', 'knot4')
                ),
                'url' => array(
                    'type' => 'string',
                    'default' => ''
                ),
                'amount' => array(
                    'type' => 'string',
                    'default' => ''
                ),
                'style' => array(
                    'type' => 'string',
                    'default' => 'primary'
                ),
                'size' => array(
                    'type' => 'string',
                    'default' => 'medium'
                ),
                'newWindow' => array(
                    'type' => 'boolean',
                    'default' => false
                )
            )
        ));
        
        // Donation Total Block
        register_block_type('knot4/donation-total', array(
            'render_callback' => array(__CLASS__, 'render_donation_total_block'),
            'attributes' => array(
                'period' => array(
                    'type' => 'string',
                    'default' => 'all'
                ),
                'campaignId' => array(
                    'type' => 'string',
                    'default' => ''
                ),
                'format' => array(
                    'type' => 'string',
                    'default' => 'currency'
                ),
                'showLabel' => array(
                    'type' => 'boolean',
                    'default' => true
                ),
                'label' => array(
                    'type' => 'string',
                    'default' => __('Total Raised', 'knot4')
                ),
                'fontSize' => array(
                    'type' => 'string',
                    'default' => 'large'
                ),
                'textAlign' => array(
                    'type' => 'string',
                    'default' => 'left'
                ),
                'backgroundColor' => array(
                    'type' => 'string',
                    'default' => ''
                ),
                'textColor' => array(
                    'type' => 'string',
                    'default' => ''
                )
            )
        ));
        
        // Events List Block
        register_block_type('knot4/events-list', array(
            'render_callback' => array(__CLASS__, 'render_events_list_block'),
            'attributes' => array(
                'limit' => array(
                    'type' => 'number',
                    'default' => 6
                ),
                'category' => array(
                    'type' => 'string',
                    'default' => ''
                ),
                'showPast' => array(
                    'type' => 'boolean',
                    'default' => false
                ),
                'layout' => array(
                    'type' => 'string',
                    'default' => 'grid'
                ),
                'showExcerpt' => array(
                    'type' => 'boolean',
                    'default' => true
                ),
                'showDate' => array(
                    'type' => 'boolean',
                    'default' => true
                ),
                'showLocation' => array(
                    'type' => 'boolean',
                    'default' => true
                ),
                'showPrice' => array(
                    'type' => 'boolean',
                    'default' => true
                ),
                'showRegistration' => array(
                    'type' => 'boolean',
                    'default' => true
                ),
                'columns' => array(
                    'type' => 'number',
                    'default' => 3
                )
            )
        ));
        
        // Event Registration Block
        register_block_type('knot4/event-registration', array(
            'render_callback' => array(__CLASS__, 'render_event_registration_block'),
            'attributes' => array(
                'eventId' => array(
                    'type' => 'number',
                    'default' => 0
                ),
                'title' => array(
                    'type' => 'string',
                    'default' => __('Event Registration', 'knot4')
                ),
                'buttonText' => array(
                    'type' => 'string',
                    'default' => __('Register Now', 'knot4')
                ),
                'showEventDetails' => array(
                    'type' => 'boolean',
                    'default' => true
                )
            )
        ));
        
        // Volunteer Form Block
        register_block_type('knot4/volunteer-form', array(
            'render_callback' => array(__CLASS__, 'render_volunteer_form_block'),
            'attributes' => array(
                'title' => array(
                    'type' => 'string',
                    'default' => __('Volunteer With Us', 'knot4')
                ),
                'showInterests' => array(
                    'type' => 'boolean',
                    'default' => true
                ),
                'showAvailability' => array(
                    'type' => 'boolean',
                    'default' => true
                ),
                'showExperience' => array(
                    'type' => 'boolean',
                    'default' => true
                ),
                'buttonText' => array(
                    'type' => 'string',
                    'default' => __('Submit Application', 'knot4')
                )
            )
        ));
        
        // Newsletter Signup Block
        register_block_type('knot4/newsletter-signup', array(
            'render_callback' => array(__CLASS__, 'render_newsletter_signup_block'),
            'attributes' => array(
                'title' => array(
                    'type' => 'string',
                    'default' => __('Stay Updated', 'knot4')
                ),
                'description' => array(
                    'type' => 'string',
                    'default' => __('Subscribe to our newsletter for the latest updates.', 'knot4')
                ),
                'buttonText' => array(
                    'type' => 'string',
                    'default' => __('Subscribe', 'knot4')
                ),
                'layout' => array(
                    'type' => 'string',
                    'default' => 'horizontal'
                ),
                'showName' => array(
                    'type' => 'boolean',
                    'default' => false
                ),
                'backgroundColor' => array(
                    'type' => 'string',
                    'default' => ''
                ),
                'textColor' => array(
                    'type' => 'string',
                    'default' => ''
                )
            )
        ));
        
        // Organization Info Block
        register_block_type('knot4/organization-info', array(
            'render_callback' => array(__CLASS__, 'render_organization_info_block'),
            'attributes' => array(
                'show' => array(
                    'type' => 'array',
                    'default' => array('name', 'email', 'phone', 'address')
                ),
                'format' => array(
                    'type' => 'string',
                    'default' => 'list'
                ),
                'showIcons' => array(
                    'type' => 'boolean',
                    'default' => true
                ),
                'alignment' => array(
                    'type' => 'string',
                    'default' => 'left'
                )
            )
        ));
        
        // Stats Display Block
        register_block_type('knot4/stats-display', array(
            'render_callback' => array(__CLASS__, 'render_stats_display_block'),
            'attributes' => array(
                'stats' => array(
                    'type' => 'array',
                    'default' => array('donations', 'donors', 'events')
                ),
                'layout' => array(
                    'type' => 'string',
                    'default' => 'grid'
                ),
                'columns' => array(
                    'type' => 'number',
                    'default' => 3
                ),
                'showIcons' => array(
                    'type' => 'boolean',
                    'default' => true
                ),
                'animateNumbers' => array(
                    'type' => 'boolean',
                    'default' => true
                )
            )
        ));
        
        // Donor Portal Block
        register_block_type('knot4/donor-portal', array(
            'render_callback' => array(__CLASS__, 'render_donor_portal_block'),
            'attributes' => array(
                'showLogin' => array(
                    'type' => 'boolean',
                    'default' => true
                ),
                'showRegistration' => array(
                    'type' => 'boolean',
                    'default' => true
                ),
                'redirectAfterLogin' => array(
                    'type' => 'string',
                    'default' => ''
                )
            )
        ));
        
        // Campaign Progress Block (Pro Feature)
        if (Knot4_Utilities::is_pro()) {
            register_block_type('knot4/campaign-progress', array(
                'render_callback' => array(__CLASS__, 'render_campaign_progress_block'),
                'attributes' => array(
                    'campaignId' => array(
                        'type' => 'number',
                        'default' => 0
                    ),
                    'showGoal' => array(
                        'type' => 'boolean',
                        'default' => true
                    ),
                    'showPercentage' => array(
                        'type' => 'boolean',
                        'default' => true
                    ),
                    'showRaised' => array(
                        'type' => 'boolean',
                        'default' => true
                    ),
                    'layout' => array(
                        'type' => 'string',
                        'default' => 'horizontal'
                    )
                )
            ));
        }
    }
    
    /**
     * Enqueue block editor assets
     */
    public static function enqueue_block_editor_assets() {
        // Enqueue block editor JavaScript
        wp_enqueue_script(
            'knot4-blocks',
            KNOT4_PLUGIN_URL . 'assets/js/blocks.js',
            array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n'),
            KNOT4_VERSION
        );
        
        // Enqueue block editor styles
        wp_enqueue_style(
            'knot4-blocks-editor',
            KNOT4_PLUGIN_URL . 'assets/css/blocks-editor.css',
            array('wp-edit-blocks'),
            KNOT4_VERSION
        );
        
        // Localize script with data
        wp_localize_script('knot4-blocks', 'knot4Blocks', array(
            'apiUrl' => rest_url('knot4/v1/'),
            'nonce' => wp_create_nonce('wp_rest'),
            'isPro' => Knot4_Utilities::is_pro(),
            'currency' => Knot4_Utilities::get_organization_settings()['currency'],
            'strings' => array(
                'donationForm' => __('Donation Form', 'knot4'),
                'donationButton' => __('Donation Button', 'knot4'),
                'donationTotal' => __('Donation Total', 'knot4'),
                'eventsList' => __('Events List', 'knot4'),
                'eventRegistration' => __('Event Registration', 'knot4'),
                'volunteerForm' => __('Volunteer Form', 'knot4'),
                'newsletterSignup' => __('Newsletter Signup', 'knot4'),
                'organizationInfo' => __('Organization Info', 'knot4'),
                'statsDisplay' => __('Stats Display', 'knot4'),
                'donorPortal' => __('Donor Portal', 'knot4'),
                'campaignProgress' => __('Campaign Progress', 'knot4'),
            )
        ));
    }
    
    /**
     * Add custom block category
     */
    public static function add_block_categories($categories, $post) {
        return array_merge(
            $categories,
            array(
                array(
                    'slug' => 'knot4',
                    'title' => __('Knot4 Nonprofit', 'knot4'),
                    'icon' => 'heart',
                ),
            )
        );
    }
    
    /**
     * Register REST API routes for block data
     */
    public static function register_rest_routes() {
        register_rest_route('knot4/v1', '/campaigns', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_campaigns_for_blocks'),
            'permission_callback' => array(__CLASS__, 'check_editor_permissions'),
        ));
        
        register_rest_route('knot4/v1', '/events', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_events_for_blocks'),
            'permission_callback' => array(__CLASS__, 'check_editor_permissions'),
        ));
        
        register_rest_route('knot4/v1', '/donation-stats', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_donation_stats_for_blocks'),
            'permission_callback' => array(__CLASS__, 'check_editor_permissions'),
        ));
    }
    
    /**
     * Check if user can edit posts (for block editor API access)
     */
    public static function check_editor_permissions() {
        return current_user_can('edit_posts');
    }
    
    /**
     * Get campaigns for block editor
     */
    public static function get_campaigns_for_blocks($request) {
        $campaigns = get_posts(array(
            'post_type' => 'knot4_campaign',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'fields' => 'ids'
        ));
        
        $campaign_options = array();
        foreach ($campaigns as $campaign_id) {
            $campaign_options[] = array(
                'value' => $campaign_id,
                'label' => get_the_title($campaign_id)
            );
        }
        
        return rest_ensure_response($campaign_options);
    }
    
    /**
     * Get events for block editor
     */
    public static function get_events_for_blocks($request) {
        $events = get_posts(array(
            'post_type' => 'knot4_event',
            'post_status' => 'publish',
            'posts_per_page' => 50,
            'fields' => 'ids'
        ));
        
        $event_options = array();
        foreach ($events as $event_id) {
            $event_date = get_post_meta($event_id, '_knot4_event_date', true);
            $formatted_date = $event_date ? date_i18n(get_option('date_format'), strtotime($event_date)) : '';
            
            $event_options[] = array(
                'value' => $event_id,
                'label' => get_the_title($event_id) . ($formatted_date ? ' (' . $formatted_date . ')' : '')
            );
        }
        
        return rest_ensure_response($event_options);
    }
    
    /**
     * Get donation statistics for blocks
     */
    public static function get_donation_stats_for_blocks($request) {
        global $wpdb;
        
        $stats = array(
            'total_donations' => $wpdb->get_var("SELECT SUM(amount) FROM {$wpdb->prefix}knot4_donations WHERE status = 'completed'") ?: 0,
            'donation_count' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}knot4_donations WHERE status = 'completed'") ?: 0,
            'donor_count' => $wpdb->get_var("SELECT COUNT(DISTINCT donor_id) FROM {$wpdb->prefix}knot4_donations WHERE status = 'completed'") ?: 0,
            'event_count' => wp_count_posts('knot4_event')->publish ?: 0,
        );
        
        return rest_ensure_response($stats);
    }
    
    /**
     * Block render callbacks
     */
    public static function render_donation_form_block($attributes) {
        $shortcode_atts = array(
            'title' => $attributes['title'],
            'suggested_amounts' => $attributes['suggestedAmounts'],
            'allow_custom' => $attributes['allowCustom'] ? 'yes' : 'no',
            'show_frequency' => $attributes['showFrequency'] ? 'yes' : 'no',
            'show_dedication' => $attributes['showDedication'] ? 'yes' : 'no',
            'show_address' => $attributes['showAddress'] ? 'yes' : 'no',
            'show_phone' => $attributes['showPhone'] ? 'yes' : 'no',
            'campaign_id' => $attributes['campaignId'],
            'fund_designation' => $attributes['fundDesignation'],
            'button_text' => $attributes['buttonText'],
            'style' => $attributes['style'],
        );
        
        return Knot4_Shortcodes::donation_form($shortcode_atts);
    }
    
    public static function render_donation_button_block($attributes) {
        $shortcode_atts = array(
            'text' => $attributes['text'],
            'url' => $attributes['url'],
            'amount' => $attributes['amount'],
            'style' => $attributes['style'],
            'size' => $attributes['size'],
            'new_window' => $attributes['newWindow'] ? 'yes' : 'no',
        );
        
        return Knot4_Shortcodes::donation_button($shortcode_atts);
    }
    
    public static function render_donation_total_block($attributes) {
        $shortcode_atts = array(
            'period' => $attributes['period'],
            'campaign_id' => $attributes['campaignId'],
            'format' => $attributes['format'],
            'show_label' => $attributes['showLabel'] ? 'yes' : 'no',
            'label' => $attributes['label'],
        );
        
        $output = Knot4_Shortcodes::donation_total($shortcode_atts);
        
        // Add block-specific styling
        $styles = array();
        if (!empty($attributes['fontSize'])) {
            $styles[] = 'font-size: ' . esc_attr($attributes['fontSize']);
        }
        if (!empty($attributes['textAlign'])) {
            $styles[] = 'text-align: ' . esc_attr($attributes['textAlign']);
        }
        if (!empty($attributes['backgroundColor'])) {
            $styles[] = 'background-color: ' . esc_attr($attributes['backgroundColor']);
        }
        if (!empty($attributes['textColor'])) {
            $styles[] = 'color: ' . esc_attr($attributes['textColor']);
        }
        
        if (!empty($styles)) {
            $style_attr = 'style="' . implode('; ', $styles) . '"';
            $output = '<div class="knot4-donation-total-block" ' . $style_attr . '>' . $output . '</div>';
        }
        
        return $output;
    }
    
    public static function render_events_list_block($attributes) {
        $shortcode_atts = array(
            'limit' => $attributes['limit'],
            'category' => $attributes['category'],
            'show_past' => $attributes['showPast'] ? 'yes' : 'no',
            'layout' => $attributes['layout'],
            'show_excerpt' => $attributes['showExcerpt'] ? 'yes' : 'no',
            'show_date' => $attributes['showDate'] ? 'yes' : 'no',
            'show_location' => $attributes['showLocation'] ? 'yes' : 'no',
            'show_price' => $attributes['showPrice'] ? 'yes' : 'no',
            'show_registration' => $attributes['showRegistration'] ? 'yes' : 'no',
        );
        
        $output = Knot4_Shortcodes::events_list($shortcode_atts);
        
        // Add columns class for grid layout
        if ($attributes['layout'] === 'grid' && !empty($attributes['columns'])) {
            $output = str_replace(
                'class="knot4-events-grid"',
                'class="knot4-events-grid knot4-columns-' . intval($attributes['columns']) . '"',
                $output
            );
        }
        
        return $output;
    }
    
    public static function render_event_registration_block($attributes) {
        if (empty($attributes['eventId'])) {
            return '<div class="knot4-block-placeholder">' . __('Please select an event in the block settings.', 'knot4') . '</div>';
        }
        
        $shortcode_atts = array(
            'event_id' => $attributes['eventId'],
            'title' => $attributes['title'],
            'button_text' => $attributes['buttonText'],
            'show_event_details' => $attributes['showEventDetails'] ? 'yes' : 'no',
        );
        
        return Knot4_Shortcodes::event_registration($shortcode_atts);
    }
    
    public static function render_volunteer_form_block($attributes) {
        $shortcode_atts = array(
            'title' => $attributes['title'],
            'show_interests' => $attributes['showInterests'] ? 'yes' : 'no',
            'show_availability' => $attributes['showAvailability'] ? 'yes' : 'no',
            'show_experience' => $attributes['showExperience'] ? 'yes' : 'no',
            'button_text' => $attributes['buttonText'],
        );
        
        return Knot4_Shortcodes::volunteer_form($shortcode_atts);
    }
    
    public static function render_newsletter_signup_block($attributes) {
        $form_id = 'knot4-newsletter-' . uniqid();
        
        ob_start();
        ?>
        <div class="knot4-newsletter-signup-block knot4-layout-<?php echo esc_attr($attributes['layout']); ?>"
             <?php if (!empty($attributes['backgroundColor']) || !empty($attributes['textColor'])): ?>
             style="<?php 
                 if (!empty($attributes['backgroundColor'])) echo 'background-color: ' . esc_attr($attributes['backgroundColor']) . '; ';
                 if (!empty($attributes['textColor'])) echo 'color: ' . esc_attr($attributes['textColor']) . ';';
             ?>"
             <?php endif; ?>>
            
            <?php if (!empty($attributes['title'])): ?>
            <h3 class="knot4-newsletter-title"><?php echo esc_html($attributes['title']); ?></h3>
            <?php endif; ?>
            
            <?php if (!empty($attributes['description'])): ?>
            <p class="knot4-newsletter-description"><?php echo esc_html($attributes['description']); ?></p>
            <?php endif; ?>
            
            <form id="<?php echo esc_attr($form_id); ?>" class="knot4-newsletter-form" method="post">
                <?php wp_nonce_field('knot4_public_nonce', 'nonce'); ?>
                
                <div class="knot4-newsletter-fields">
                    <?php if ($attributes['showName']): ?>
                    <div class="knot4-field-group">
                        <input type="text" name="newsletter_name" placeholder="<?php _e('Your Name', 'knot4'); ?>" required>
                    </div>
                    <?php endif; ?>
                    
                    <div class="knot4-field-group">
                        <input type="email" name="newsletter_email" placeholder="<?php _e('Your Email', 'knot4'); ?>" required>
                    </div>
                    
                    <div class="knot4-field-group">
                        <button type="submit" class="knot4-newsletter-btn">
                            <?php echo esc_html($attributes['buttonText']); ?>
                        </button>
                    </div>
                </div>
                
                <input type="hidden" name="action" value="knot4_subscribe_newsletter">
            </form>
        </div>
        <?php
        
        return ob_get_clean();
    }
    
    public static function render_organization_info_block($attributes) {
        $shortcode_atts = array(
            'show' => implode(',', $attributes['show']),
            'format' => $attributes['format'],
        );
        
        $output = Knot4_Shortcodes::organization_info($shortcode_atts);
        
        // Add block-specific classes
        $classes = array('knot4-organization-info-block');
        if (!empty($attributes['alignment'])) {
            $classes[] = 'knot4-align-' . $attributes['alignment'];
        }
        if ($attributes['showIcons']) {
            $classes[] = 'knot4-show-icons';
        }
        
        return '<div class="' . implode(' ', $classes) . '">' . $output . '</div>';
    }
    
    public static function render_stats_display_block($attributes) {
        global $wpdb;
        
        $stats_data = array();
        
        foreach ($attributes['stats'] as $stat) {
            switch ($stat) {
                case 'donations':
                    $value = $wpdb->get_var("SELECT SUM(amount) FROM {$wpdb->prefix}knot4_donations WHERE status = 'completed'") ?: 0;
                    $stats_data[] = array(
                        'label' => __('Total Donated', 'knot4'),
                        'value' => Knot4_Utilities::format_currency($value),
                        'icon' => 'heart',
                        'raw_value' => $value
                    );
                    break;
                    
                case 'donors':
                    $value = $wpdb->get_var("SELECT COUNT(DISTINCT donor_id) FROM {$wpdb->prefix}knot4_donations WHERE status = 'completed'") ?: 0;
                    $stats_data[] = array(
                        'label' => __('Donors', 'knot4'),
                        'value' => number_format($value),
                        'icon' => 'groups',
                        'raw_value' => $value
                    );
                    break;
                    
                case 'events':
                    $value = wp_count_posts('knot4_event')->publish ?: 0;
                    $stats_data[] = array(
                        'label' => __('Events', 'knot4'),
                        'value' => number_format($value),
                        'icon' => 'calendar-alt',
                        'raw_value' => $value
                    );
                    break;
            }
        }
        
        if (empty($stats_data)) {
            return '<div class="knot4-block-placeholder">' . __('No statistics to display.', 'knot4') . '</div>';
        }
        
        ob_start();
        ?>
        <div class="knot4-stats-display-block knot4-layout-<?php echo esc_attr($attributes['layout']); ?> knot4-columns-<?php echo intval($attributes['columns']); ?>">
            <div class="knot4-stats-grid">
                <?php foreach ($stats_data as $stat): ?>
                <div class="knot4-stat-item">
                    <?php if ($attributes['showIcons']): ?>
                    <div class="knot4-stat-icon">
                        <span class="dashicons dashicons-<?php echo esc_attr($stat['icon']); ?>"></span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="knot4-stat-content">
                        <div class="knot4-stat-value" 
                             <?php if ($attributes['animateNumbers']): ?>
                             data-animate="true" data-target="<?php echo esc_attr($stat['raw_value']); ?>"
                             <?php endif; ?>>
                            <?php echo esc_html($stat['value']); ?>
                        </div>
                        <div class="knot4-stat-label"><?php echo esc_html($stat['label']); ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        
        return ob_get_clean();
    }
    
    public static function render_donor_portal_block($attributes) {
        $shortcode_atts = array(
            'show_login' => $attributes['showLogin'] ? 'yes' : 'no',
            'show_registration' => $attributes['showRegistration'] ? 'yes' : 'no',
            'redirect_after_login' => $attributes['redirectAfterLogin'],
        );
        
        return Knot4_Shortcodes::donor_portal($shortcode_atts);
    }
    
    public static function render_campaign_progress_block($attributes) {
        if (!Knot4_Utilities::is_pro()) {
            return '<div class="knot4-pro-feature">' . __('Campaign Progress is a Pro feature.', 'knot4') . '</div>';
        }
        
        if (empty($attributes['campaignId'])) {
            return '<div class="knot4-block-placeholder">' . __('Please select a campaign in the block settings.', 'knot4') . '</div>';
        }
        
        // This would be implemented in the pro version
        return '<div class="knot4-campaign-progress-block">' . __('Campaign progress will be displayed here.', 'knot4') . '</div>';
    }
}