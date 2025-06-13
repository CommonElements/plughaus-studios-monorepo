<?php
/**
 * Shortcodes for EquipRent Pro
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Shortcodes class
 */
class ERP_Shortcodes {

    /**
     * Initialize shortcodes
     */
    public static function init() {
        add_shortcode('erp_equipment_list', array(__CLASS__, 'equipment_list'));
        add_shortcode('erp_equipment_grid', array(__CLASS__, 'equipment_grid'));
        add_shortcode('erp_booking_form', array(__CLASS__, 'booking_form'));
        add_shortcode('erp_availability_calendar', array(__CLASS__, 'availability_calendar'));
        add_shortcode('erp_equipment_search', array(__CLASS__, 'equipment_search'));
    }

    /**
     * Equipment list shortcode
     */
    public static function equipment_list($atts) {
        $atts = shortcode_atts(array(
            'category' => '',
            'location' => '',
            'status' => 'available',
            'limit' => 10,
            'orderby' => 'name',
            'order' => 'ASC',
            'show_images' => 'yes',
            'show_price' => 'yes',
            'show_description' => 'yes'
        ), $atts);

        $args = array(
            'limit' => intval($atts['limit']),
            'orderby' => sanitize_text_field($atts['orderby']),
            'order' => sanitize_text_field($atts['order'])
        );

        if (!empty($atts['category'])) {
            $args['category'] = sanitize_text_field($atts['category']);
        }

        if (!empty($atts['location'])) {
            $args['location'] = sanitize_text_field($atts['location']);
        }

        if (!empty($atts['status'])) {
            $args['status'] = sanitize_text_field($atts['status']);
        }

        $equipment_list = ERP_Equipment::get_all($args);

        if (empty($equipment_list)) {
            return '<p class="erp-no-equipment">' . __('No equipment found.', 'equiprent-pro') . '</p>';
        }

        ob_start();
        ?>
        <div class="erp-equipment-list">
            <?php foreach ($equipment_list as $equipment): ?>
            <div class="erp-equipment-item" data-equipment-id="<?php echo esc_attr($equipment->id); ?>">
                <?php if ($atts['show_images'] === 'yes'): ?>
                <div class="erp-equipment-image">
                    <?php
                    $image_gallery = json_decode($equipment->image_gallery, true);
                    $image_url = !empty($image_gallery) && is_array($image_gallery) ? $image_gallery[0] : '';
                    ?>
                    <?php if ($image_url): ?>
                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($equipment->name); ?>">
                    <?php else: ?>
                    <div class="erp-no-image">
                        <span class="dashicons dashicons-hammer"></span>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div class="erp-equipment-content">
                    <h3 class="erp-equipment-title"><?php echo esc_html($equipment->name); ?></h3>
                    
                    <?php if ($atts['show_description'] === 'yes' && !empty($equipment->description)): ?>
                    <p class="erp-equipment-description"><?php echo esc_html(wp_trim_words($equipment->description, 20)); ?></p>
                    <?php endif; ?>

                    <div class="erp-equipment-meta">
                        <?php if (!empty($equipment->category)): ?>
                        <span class="erp-equipment-category"><?php echo esc_html($equipment->category); ?></span>
                        <?php endif; ?>
                        
                        <?php if (!empty($equipment->location)): ?>
                        <span class="erp-equipment-location"><?php echo esc_html($equipment->location); ?></span>
                        <?php endif; ?>
                    </div>

                    <?php if ($atts['show_price'] === 'yes'): ?>
                    <div class="erp-equipment-pricing">
                        <?php if (!empty($equipment->daily_rate)): ?>
                        <span class="erp-daily-rate">
                            <?php echo ERP_Utilities::format_currency($equipment->daily_rate); ?>
                            <small><?php _e('per day', 'equiprent-pro'); ?></small>
                        </span>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <div class="erp-equipment-actions">
                        <button class="erp-btn erp-btn-primary erp-book-equipment" data-equipment-id="<?php echo esc_attr($equipment->id); ?>">
                            <?php _e('Book Now', 'equiprent-pro'); ?>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Equipment grid shortcode
     */
    public static function equipment_grid($atts) {
        $atts = shortcode_atts(array(
            'columns' => 3,
            'category' => '',
            'location' => '',
            'status' => 'available',
            'limit' => 12,
            'orderby' => 'name',
            'order' => 'ASC'
        ), $atts);

        $list_atts = $atts;
        $list_atts['show_images'] = 'yes';
        $list_atts['show_price'] = 'yes';
        $list_atts['show_description'] = 'yes';

        $equipment_html = self::equipment_list($list_atts);
        
        $columns = max(1, min(6, intval($atts['columns'])));
        
        return '<div class="erp-equipment-grid erp-columns-' . $columns . '">' . $equipment_html . '</div>';
    }

    /**
     * Booking form shortcode
     */
    public static function booking_form($atts) {
        $atts = shortcode_atts(array(
            'equipment_id' => '',
            'show_equipment_selector' => 'yes',
            'redirect_url' => ''
        ), $atts);

        ob_start();
        ?>
        <div class="erp-booking-form-container">
            <form class="erp-booking-form" method="post" action="">
                <?php wp_nonce_field('erp_booking_form', 'erp_booking_nonce'); ?>
                
                <div class="erp-form-section">
                    <h3><?php _e('Rental Details', 'equiprent-pro'); ?></h3>
                    
                    <?php if ($atts['show_equipment_selector'] === 'yes'): ?>
                    <div class="erp-form-group">
                        <label for="equipment_id"><?php _e('Select Equipment', 'equiprent-pro'); ?></label>
                        <select name="equipment_id" id="equipment_id" required>
                            <option value=""><?php _e('Choose equipment...', 'equiprent-pro'); ?></option>
                            <?php
                            $available_equipment = ERP_Equipment::get_by_status('available');
                            foreach ($available_equipment as $equipment):
                            ?>
                            <option value="<?php echo esc_attr($equipment->id); ?>" <?php selected($atts['equipment_id'], $equipment->id); ?>>
                                <?php echo esc_html($equipment->name . ' - ' . ERP_Utilities::format_currency($equipment->daily_rate) . '/day'); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php else: ?>
                    <input type="hidden" name="equipment_id" value="<?php echo esc_attr($atts['equipment_id']); ?>">
                    <?php endif; ?>

                    <div class="erp-form-row">
                        <div class="erp-form-group">
                            <label for="start_date"><?php _e('Start Date', 'equiprent-pro'); ?></label>
                            <input type="date" name="start_date" id="start_date" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="erp-form-group">
                            <label for="end_date"><?php _e('End Date', 'equiprent-pro'); ?></label>
                            <input type="date" name="end_date" id="end_date" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>

                    <div class="erp-availability-check">
                        <button type="button" class="erp-btn erp-btn-secondary" id="check-availability">
                            <?php _e('Check Availability', 'equiprent-pro'); ?>
                        </button>
                        <div class="erp-availability-result"></div>
                    </div>
                </div>

                <div class="erp-form-section">
                    <h3><?php _e('Contact Information', 'equiprent-pro'); ?></h3>
                    
                    <div class="erp-form-row">
                        <div class="erp-form-group">
                            <label for="first_name"><?php _e('First Name', 'equiprent-pro'); ?></label>
                            <input type="text" name="first_name" id="first_name" required>
                        </div>
                        <div class="erp-form-group">
                            <label for="last_name"><?php _e('Last Name', 'equiprent-pro'); ?></label>
                            <input type="text" name="last_name" id="last_name" required>
                        </div>
                    </div>

                    <div class="erp-form-row">
                        <div class="erp-form-group">
                            <label for="email"><?php _e('Email Address', 'equiprent-pro'); ?></label>
                            <input type="email" name="email" id="email" required>
                        </div>
                        <div class="erp-form-group">
                            <label for="phone"><?php _e('Phone Number', 'equiprent-pro'); ?></label>
                            <input type="tel" name="phone" id="phone" required>
                        </div>
                    </div>

                    <div class="erp-form-group">
                        <label for="company_name"><?php _e('Company Name (Optional)', 'equiprent-pro'); ?></label>
                        <input type="text" name="company_name" id="company_name">
                    </div>
                </div>

                <div class="erp-form-section">
                    <h3><?php _e('Delivery Information', 'equiprent-pro'); ?></h3>
                    
                    <div class="erp-form-group">
                        <label><?php _e('Pickup Method', 'equiprent-pro'); ?></label>
                        <div class="erp-radio-group">
                            <label>
                                <input type="radio" name="pickup_method" value="customer_pickup" checked>
                                <?php _e('I will pick up', 'equiprent-pro'); ?>
                            </label>
                            <label>
                                <input type="radio" name="pickup_method" value="delivery">
                                <?php _e('Please deliver', 'equiprent-pro'); ?>
                            </label>
                        </div>
                    </div>

                    <div class="erp-delivery-fields" style="display: none;">
                        <div class="erp-form-group">
                            <label for="delivery_address"><?php _e('Delivery Address', 'equiprent-pro'); ?></label>
                            <textarea name="delivery_address" id="delivery_address" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="erp-form-group">
                        <label for="special_instructions"><?php _e('Special Instructions', 'equiprent-pro'); ?></label>
                        <textarea name="special_instructions" id="special_instructions" rows="3"></textarea>
                    </div>
                </div>

                <div class="erp-form-actions">
                    <button type="submit" class="erp-btn erp-btn-primary erp-btn-large">
                        <?php _e('Submit Booking Request', 'equiprent-pro'); ?>
                    </button>
                </div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Availability calendar shortcode
     */
    public static function availability_calendar($atts) {
        $atts = shortcode_atts(array(
            'equipment_id' => '',
            'view' => 'month' // month, week, list
        ), $atts);

        if (empty($atts['equipment_id'])) {
            return '<p class="erp-error">' . __('Equipment ID is required for availability calendar.', 'equiprent-pro') . '</p>';
        }

        $equipment = new ERP_Equipment(intval($atts['equipment_id']));
        if (!$equipment->get_id()) {
            return '<p class="erp-error">' . __('Equipment not found.', 'equiprent-pro') . '</p>';
        }

        // Get bookings for next 3 months
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d', strtotime('+3 months'));
        $bookings = ERP_Booking::get_calendar_bookings($start_date, $end_date);

        // Filter bookings for this equipment
        $equipment_bookings = array();
        foreach ($bookings as $booking) {
            global $wpdb;
            $has_equipment = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}erp_booking_items WHERE booking_id = %d AND equipment_id = %d",
                $booking->id,
                $equipment->get_id()
            ));
            
            if ($has_equipment > 0) {
                $equipment_bookings[] = $booking;
            }
        }

        ob_start();
        ?>
        <div class="erp-availability-calendar" data-equipment-id="<?php echo esc_attr($equipment->get_id()); ?>">
            <div class="erp-calendar-header">
                <h3><?php echo esc_html($equipment->get('name')); ?> - <?php _e('Availability', 'equiprent-pro'); ?></h3>
            </div>
            
            <div class="erp-calendar-legend">
                <span class="erp-available"><?php _e('Available', 'equiprent-pro'); ?></span>
                <span class="erp-booked"><?php _e('Booked', 'equiprent-pro'); ?></span>
            </div>

            <div class="erp-calendar-grid">
                <!-- Calendar implementation would go here -->
                <!-- This is a simplified version - full calendar would need JavaScript -->
                <?php if (!empty($equipment_bookings)): ?>
                <div class="erp-upcoming-bookings">
                    <h4><?php _e('Upcoming Bookings', 'equiprent-pro'); ?></h4>
                    <ul>
                        <?php foreach ($equipment_bookings as $booking): ?>
                        <li>
                            <?php echo ERP_Utilities::format_date($booking->start_date); ?> - 
                            <?php echo ERP_Utilities::format_date($booking->end_date); ?>
                            (<?php echo esc_html($booking->booking_number); ?>)
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php else: ?>
                <p class="erp-no-bookings"><?php _e('No upcoming bookings for this equipment.', 'equiprent-pro'); ?></p>
                <?php endif; ?>
            </div>
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
            'show_results' => 'yes',
            'results_per_page' => 12
        ), $atts);

        ob_start();
        ?>
        <div class="erp-equipment-search">
            <?php if ($atts['show_filters'] === 'yes'): ?>
            <div class="erp-search-filters">
                <form class="erp-search-form" method="get">
                    <div class="erp-filter-row">
                        <div class="erp-filter-group">
                            <label for="search_term"><?php _e('Search', 'equiprent-pro'); ?></label>
                            <input type="text" name="search_term" id="search_term" placeholder="<?php _e('Equipment name, brand, model...', 'equiprent-pro'); ?>" value="<?php echo esc_attr(isset($_GET['search_term']) ? $_GET['search_term'] : ''); ?>">
                        </div>
                        
                        <div class="erp-filter-group">
                            <label for="category"><?php _e('Category', 'equiprent-pro'); ?></label>
                            <select name="category" id="category">
                                <option value=""><?php _e('All Categories', 'equiprent-pro'); ?></option>
                                <?php
                                $categories = ERP_Utilities::get_equipment_categories();
                                $selected_category = isset($_GET['category']) ? $_GET['category'] : '';
                                foreach ($categories as $category):
                                ?>
                                <option value="<?php echo esc_attr($category); ?>" <?php selected($selected_category, $category); ?>>
                                    <?php echo esc_html($category); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="erp-filter-group">
                            <label for="location"><?php _e('Location', 'equiprent-pro'); ?></label>
                            <select name="location" id="location">
                                <option value=""><?php _e('All Locations', 'equiprent-pro'); ?></option>
                                <?php
                                $locations = ERP_Utilities::get_equipment_locations();
                                $selected_location = isset($_GET['location']) ? $_GET['location'] : '';
                                foreach ($locations as $location):
                                ?>
                                <option value="<?php echo esc_attr($location); ?>" <?php selected($selected_location, $location); ?>>
                                    <?php echo esc_html($location); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="erp-filter-group">
                            <button type="submit" class="erp-btn erp-btn-primary">
                                <?php _e('Search', 'equiprent-pro'); ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <?php endif; ?>

            <?php if ($atts['show_results'] === 'yes'): ?>
            <div class="erp-search-results">
                <?php
                // Handle search
                $search_results = array();
                if (!empty($_GET['search_term']) || !empty($_GET['category']) || !empty($_GET['location'])) {
                    $search_args = array(
                        'status' => 'available',
                        'limit' => intval($atts['results_per_page'])
                    );

                    if (!empty($_GET['category'])) {
                        $search_args['category'] = sanitize_text_field($_GET['category']);
                    }

                    if (!empty($_GET['location'])) {
                        $search_args['location'] = sanitize_text_field($_GET['location']);
                    }

                    if (!empty($_GET['search_term'])) {
                        $search_results = ERP_Equipment::search(sanitize_text_field($_GET['search_term']), $search_args);
                    } else {
                        $search_results = ERP_Equipment::get_all($search_args);
                    }

                    if (!empty($search_results)) {
                        echo '<div class="erp-search-count">';
                        printf(_n('%d equipment found', '%d equipment found', count($search_results), 'equiprent-pro'), count($search_results));
                        echo '</div>';

                        echo self::equipment_grid(array(
                            'columns' => 3,
                            'limit' => count($search_results)
                        ));
                    } else {
                        echo '<p class="erp-no-results">' . __('No equipment found matching your criteria.', 'equiprent-pro') . '</p>';
                    }
                } else {
                    echo '<p class="erp-search-prompt">' . __('Use the search form above to find equipment.', 'equiprent-pro') . '</p>';
                }
                ?>
            </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}