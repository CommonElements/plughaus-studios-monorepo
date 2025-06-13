<?php
/**
 * EquipRent Pro Shortcodes
 *
 * @package EquipRent_Pro
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handles shortcodes for equipment rental
 */
class ER_Shortcodes {

    /**
     * Initialize the class
     */
    public static function init() {
        add_action('init', array(__CLASS__, 'register_shortcodes'));
    }

    /**
     * Register shortcodes
     */
    public static function register_shortcodes() {
        add_shortcode('equiprent_catalog', array(__CLASS__, 'equipment_catalog'));
        add_shortcode('equiprent_booking_form', array(__CLASS__, 'booking_form'));
        add_shortcode('equiprent_availability_calendar', array(__CLASS__, 'availability_calendar'));
        add_shortcode('equiprent_customer_bookings', array(__CLASS__, 'customer_bookings'));
        add_shortcode('equiprent_equipment_search', array(__CLASS__, 'equipment_search'));
    }

    /**
     * Equipment catalog shortcode
     */
    public static function equipment_catalog($atts) {
        $atts = shortcode_atts(array(
            'category' => '',
            'limit' => 12,
            'columns' => 3,
            'show_price' => 'yes',
            'show_availability' => 'yes',
            'orderby' => 'title',
            'order' => 'ASC'
        ), $atts, 'equiprent_catalog');

        $args = array(
            'post_type' => 'equipment',
            'post_status' => 'publish',
            'posts_per_page' => intval($atts['limit']),
            'orderby' => $atts['orderby'],
            'order' => $atts['order']
        );

        if (!empty($atts['category'])) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'equipment_category',
                    'field' => 'slug',
                    'terms' => $atts['category']
                )
            );
        }

        $equipment = get_posts($args);

        if (empty($equipment)) {
            return '<p>' . __('No equipment found.', 'equiprent-pro') . '</p>';
        }

        ob_start();
        ?>
        <div class="equiprent-catalog columns-<?php echo esc_attr($atts['columns']); ?>">
            <?php foreach ($equipment as $item): ?>
                <div class="equipment-item">
                    <div class="equipment-image">
                        <?php if (has_post_thumbnail($item->ID)): ?>
                            <?php echo get_the_post_thumbnail($item->ID, 'medium'); ?>
                        <?php else: ?>
                            <div class="no-image-placeholder">
                                <span class="dashicons dashicons-format-image"></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="equipment-details">
                        <h3 class="equipment-title">
                            <a href="<?php echo get_permalink($item->ID); ?>">
                                <?php echo esc_html($item->post_title); ?>
                            </a>
                        </h3>
                        
                        <?php if ($atts['show_price'] === 'yes'): ?>
                            <?php $daily_rate = get_post_meta($item->ID, '_equipment_daily_rate', true); ?>
                            <?php if ($daily_rate): ?>
                                <div class="equipment-price">
                                    <?php echo ER_Utilities::format_currency($daily_rate); ?>/day
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php if ($atts['show_availability'] === 'yes'): ?>
                            <?php $stock = ER_Utilities::get_equipment_stock($item->ID); ?>
                            <div class="equipment-availability">
                                <?php if ($stock['available'] > 0): ?>
                                    <span class="available"><?php _e('Available', 'equiprent-pro'); ?></span>
                                <?php else: ?>
                                    <span class="unavailable"><?php _e('Not Available', 'equiprent-pro'); ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="equipment-excerpt">
                            <?php echo wp_trim_words($item->post_excerpt ?: $item->post_content, 20); ?>
                        </div>
                        
                        <div class="equipment-actions">
                            <a href="<?php echo get_permalink($item->ID); ?>" class="button view-details">
                                <?php _e('View Details', 'equiprent-pro'); ?>
                            </a>
                            <a href="#" class="button book-now" data-equipment-id="<?php echo $item->ID; ?>">
                                <?php _e('Book Now', 'equiprent-pro'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Booking form shortcode
     */
    public static function booking_form($atts) {
        $atts = shortcode_atts(array(
            'equipment_id' => '',
            'redirect_url' => '',
            'show_equipment_selection' => 'yes'
        ), $atts, 'equiprent_booking_form');

        // Check if user is logged in for customer bookings
        if (!is_user_logged_in()) {
            return '<p>' . __('Please log in to make a booking.', 'equiprent-pro') . 
                   ' <a href="' . wp_login_url(get_permalink()) . '">' . __('Login', 'equiprent-pro') . '</a></p>';
        }

        ob_start();
        ?>
        <form id="equiprent-booking-form" class="equiprent-booking-form" method="post">
            <?php wp_nonce_field('equiprent_booking_form', 'equiprent_booking_nonce'); ?>
            
            <?php if ($atts['show_equipment_selection'] === 'yes' && empty($atts['equipment_id'])): ?>
                <div class="form-group">
                    <label for="equipment_selection"><?php _e('Select Equipment', 'equiprent-pro'); ?></label>
                    <select name="equipment_items[]" id="equipment_selection" required>
                        <option value=""><?php _e('Choose equipment...', 'equiprent-pro'); ?></option>
                        <?php
                        $equipment = get_posts(array(
                            'post_type' => 'equipment',
                            'post_status' => 'publish',
                            'numberposts' => -1
                        ));
                        foreach ($equipment as $item):
                            $daily_rate = get_post_meta($item->ID, '_equipment_daily_rate', true);
                        ?>
                            <option value="<?php echo $item->ID; ?>" data-rate="<?php echo $daily_rate; ?>">
                                <?php echo esc_html($item->post_title); ?>
                                <?php if ($daily_rate): ?>
                                    (<?php echo ER_Utilities::format_currency($daily_rate); ?>/day)
                                <?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($atts['equipment_id'])): ?>
                <input type="hidden" name="equipment_items[]" value="<?php echo esc_attr($atts['equipment_id']); ?>">
            <?php endif; ?>
            
            <div class="form-row">
                <div class="form-group half-width">
                    <label for="start_date"><?php _e('Start Date', 'equiprent-pro'); ?></label>
                    <input type="date" name="start_date" id="start_date" required 
                           min="<?php echo date('Y-m-d'); ?>">
                </div>
                
                <div class="form-group half-width">
                    <label for="end_date"><?php _e('End Date', 'equiprent-pro'); ?></label>
                    <input type="date" name="end_date" id="end_date" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="delivery_required">
                    <input type="checkbox" name="delivery_required" id="delivery_required" value="1">
                    <?php _e('Delivery Required', 'equiprent-pro'); ?>
                </label>
            </div>
            
            <div class="form-group delivery-address" style="display: none;">
                <label for="delivery_address"><?php _e('Delivery Address', 'equiprent-pro'); ?></label>
                <textarea name="delivery_address" id="delivery_address" rows="3"></textarea>
            </div>
            
            <div class="form-group">
                <label for="special_requests"><?php _e('Special Requests', 'equiprent-pro'); ?></label>
                <textarea name="special_requests" id="special_requests" rows="3"></textarea>
            </div>
            
            <div class="booking-summary">
                <h4><?php _e('Booking Summary', 'equiprent-pro'); ?></h4>
                <div id="booking-calculation">
                    <p><?php _e('Please select dates to see pricing.', 'equiprent-pro'); ?></p>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" name="submit_booking" class="button button-primary">
                    <?php _e('Submit Booking Request', 'equiprent-pro'); ?>
                </button>
            </div>
        </form>
        
        <script>
        jQuery(document).ready(function($) {
            // Toggle delivery address field
            $('#delivery_required').change(function() {
                $('.delivery-address').toggle(this.checked);
            });
            
            // Calculate pricing when dates change
            $('#start_date, #end_date').change(function() {
                calculateBooking();
            });
            
            function calculateBooking() {
                var startDate = $('#start_date').val();
                var endDate = $('#end_date').val();
                var equipmentSelect = $('#equipment_selection');
                
                if (startDate && endDate && equipmentSelect.length && equipmentSelect.val()) {
                    var rate = equipmentSelect.find(':selected').data('rate');
                    var days = Math.ceil((new Date(endDate) - new Date(startDate)) / (1000 * 60 * 60 * 24));
                    
                    if (days > 0 && rate) {
                        var total = days * rate;
                        $('#booking-calculation').html(
                            '<p><strong>' + days + ' days @ ' + rate + '/day = $' + total.toFixed(2) + '</strong></p>'
                        );
                    }
                }
            }
        });
        </script>
        <?php
        return ob_get_clean();
    }

    /**
     * Availability calendar shortcode
     */
    public static function availability_calendar($atts) {
        $atts = shortcode_atts(array(
            'equipment_id' => '',
            'view' => 'month' // month, week
        ), $atts, 'equiprent_availability_calendar');

        ob_start();
        ?>
        <div class="equiprent-calendar-container">
            <div id="equiprent-availability-calendar" 
                 data-equipment-id="<?php echo esc_attr($atts['equipment_id']); ?>"
                 data-view="<?php echo esc_attr($atts['view']); ?>">
                <p><?php _e('Loading calendar...', 'equiprent-pro'); ?></p>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Customer bookings shortcode
     */
    public static function customer_bookings($atts) {
        if (!is_user_logged_in()) {
            return '<p>' . __('Please log in to view your bookings.', 'equiprent-pro') . '</p>';
        }

        $atts = shortcode_atts(array(
            'limit' => 10,
            'show_past' => 'yes'
        ), $atts, 'equiprent_customer_bookings');

        $user_id = get_current_user_id();
        
        // Get customer ID from user meta or customer table
        global $wpdb;
        $customers_table = $wpdb->prefix . 'er_customers';
        $customer = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$customers_table} WHERE user_id = %d",
            $user_id
        ));

        if (!$customer) {
            return '<p>' . __('No customer record found.', 'equiprent-pro') . '</p>';
        }

        // Get bookings
        $bookings_table = $wpdb->prefix . 'er_bookings';
        $query = "SELECT * FROM {$bookings_table} WHERE customer_id = %d";
        
        if ($atts['show_past'] !== 'yes') {
            $query .= " AND end_date >= CURDATE()";
        }
        
        $query .= " ORDER BY start_date DESC LIMIT %d";
        
        $bookings = $wpdb->get_results($wpdb->prepare($query, $customer->id, intval($atts['limit'])));

        if (empty($bookings)) {
            return '<p>' . __('No bookings found.', 'equiprent-pro') . '</p>';
        }

        ob_start();
        ?>
        <div class="equiprent-customer-bookings">
            <table class="bookings-table">
                <thead>
                    <tr>
                        <th><?php _e('Booking #', 'equiprent-pro'); ?></th>
                        <th><?php _e('Dates', 'equiprent-pro'); ?></th>
                        <th><?php _e('Equipment', 'equiprent-pro'); ?></th>
                        <th><?php _e('Status', 'equiprent-pro'); ?></th>
                        <th><?php _e('Total', 'equiprent-pro'); ?></th>
                        <th><?php _e('Actions', 'equiprent-pro'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><strong><?php echo esc_html($booking->booking_number); ?></strong></td>
                            <td>
                                <?php echo ER_Utilities::format_date($booking->start_date); ?> - 
                                <?php echo ER_Utilities::format_date($booking->end_date); ?>
                            </td>
                            <td>
                                <?php
                                // Get booking items
                                $items_table = $wpdb->prefix . 'er_booking_items';
                                $items = $wpdb->get_results($wpdb->prepare(
                                    "SELECT * FROM {$items_table} WHERE booking_id = %d",
                                    $booking->id
                                ));
                                
                                $equipment_names = array();
                                foreach ($items as $item) {
                                    $equipment = get_post($item->equipment_id);
                                    if ($equipment) {
                                        $equipment_names[] = $equipment->post_title;
                                    }
                                }
                                echo esc_html(implode(', ', $equipment_names));
                                ?>
                            </td>
                            <td>
                                <span class="booking-status status-<?php echo esc_attr($booking->status); ?>">
                                    <?php
                                    $statuses = ER_Post_Types::get_booking_statuses();
                                    echo esc_html($statuses[$booking->status] ?? $booking->status);
                                    ?>
                                </span>
                            </td>
                            <td><?php echo ER_Utilities::format_currency($booking->total_amount); ?></td>
                            <td>
                                <a href="#" class="view-booking" data-booking-id="<?php echo $booking->id; ?>">
                                    <?php _e('View', 'equiprent-pro'); ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Equipment search shortcode
     */
    public static function equipment_search($atts) {
        $atts = shortcode_atts(array(
            'show_filters' => 'yes',
            'show_sorting' => 'yes'
        ), $atts, 'equiprent_equipment_search');

        ob_start();
        ?>
        <div class="equiprent-search-form">
            <form method="get" action="">
                <div class="search-fields">
                    <div class="search-field">
                        <input type="text" name="equipment_search" 
                               placeholder="<?php _e('Search equipment...', 'equiprent-pro'); ?>"
                               value="<?php echo esc_attr($_GET['equipment_search'] ?? ''); ?>">
                    </div>
                    
                    <?php if ($atts['show_filters'] === 'yes'): ?>
                        <div class="search-field">
                            <select name="equipment_category">
                                <option value=""><?php _e('All Categories', 'equiprent-pro'); ?></option>
                                <?php
                                $categories = get_terms(array(
                                    'taxonomy' => 'equipment_category',
                                    'hide_empty' => false
                                ));
                                foreach ($categories as $category):
                                ?>
                                    <option value="<?php echo $category->slug; ?>" 
                                            <?php selected($_GET['equipment_category'] ?? '', $category->slug); ?>>
                                        <?php echo esc_html($category->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                    
                    <div class="search-field">
                        <button type="submit" class="button">
                            <?php _e('Search', 'equiprent-pro'); ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <?php
        // Display search results if there's a search query
        if (!empty($_GET['equipment_search']) || !empty($_GET['equipment_category'])) {
            echo self::display_search_results();
        }
        ?>
        <?php
        return ob_get_clean();
    }

    /**
     * Display search results
     */
    private static function display_search_results() {
        $args = array(
            'post_type' => 'equipment',
            'post_status' => 'publish',
            'posts_per_page' => 20
        );

        if (!empty($_GET['equipment_search'])) {
            $args['s'] = sanitize_text_field($_GET['equipment_search']);
        }

        if (!empty($_GET['equipment_category'])) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'equipment_category',
                    'field' => 'slug',
                    'terms' => sanitize_text_field($_GET['equipment_category'])
                )
            );
        }

        $equipment = get_posts($args);

        if (empty($equipment)) {
            return '<p>' . __('No equipment found matching your search.', 'equiprent-pro') . '</p>';
        }

        ob_start();
        ?>
        <div class="equiprent-search-results">
            <h3><?php printf(__('Found %d results', 'equiprent-pro'), count($equipment)); ?></h3>
            <?php echo self::equipment_catalog(array('limit' => -1)); ?>
        </div>
        <?php
        return ob_get_clean();
    }
}