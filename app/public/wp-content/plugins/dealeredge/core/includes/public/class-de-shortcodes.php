<?php
/**
 * DealerEdge Shortcodes
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class DE_Shortcodes {
    
    public static function init() {
        add_shortcode('dealeredge_inventory', array(__CLASS__, 'display_inventory'));
        add_shortcode('dealeredge_services', array(__CLASS__, 'display_services'));
    }
    
    public static function display_inventory($atts) {
        $atts = shortcode_atts(array(
            'limit' => 10,
            'status' => 'available',
            'type' => 'all'
        ), $atts);
        
        // Query vehicles
        $args = array(
            'post_type' => 'de_vehicle',
            'posts_per_page' => intval($atts['limit']),
            'post_status' => 'publish',
            'meta_query' => array()
        );
        
        if ($atts['status'] !== 'all') {
            $args['meta_query'][] = array(
                'key' => '_de_vehicle_status',
                'value' => $atts['status'],
                'compare' => '='
            );
        }
        
        $vehicles = get_posts($args);
        
        if (empty($vehicles)) {
            return '<p>' . __('No vehicles available.', 'dealeredge') . '</p>';
        }
        
        ob_start();
        ?>
        <div class="dealeredge-inventory">
            <?php foreach ($vehicles as $vehicle) : ?>
                <div class="vehicle-item">
                    <h3><?php echo DE_Utilities::get_vehicle_display_name($vehicle->ID); ?></h3>
                    
                    <?php if (has_post_thumbnail($vehicle->ID)) : ?>
                        <div class="vehicle-image">
                            <?php echo get_the_post_thumbnail($vehicle->ID, 'medium'); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="vehicle-details">
                        <?php
                        $mileage = get_post_meta($vehicle->ID, '_de_vehicle_mileage', true);
                        $condition = get_post_meta($vehicle->ID, '_de_vehicle_condition', true);
                        $price = get_post_meta($vehicle->ID, '_de_vehicle_sale_price', true);
                        ?>
                        
                        <?php if ($mileage) : ?>
                            <p><strong><?php _e('Mileage:', 'dealeredge'); ?></strong> <?php echo number_format($mileage); ?> miles</p>
                        <?php endif; ?>
                        
                        <?php if ($condition) : ?>
                            <p><strong><?php _e('Condition:', 'dealeredge'); ?></strong> <?php echo esc_html(DE_Utilities::get_vehicle_conditions()[$condition] ?? $condition); ?></p>
                        <?php endif; ?>
                        
                        <?php if ($price) : ?>
                            <p class="vehicle-price"><strong><?php echo DE_Utilities::format_currency($price); ?></strong></p>
                        <?php endif; ?>
                        
                        <?php if ($vehicle->post_content) : ?>
                            <div class="vehicle-description">
                                <?php echo wpautop($vehicle->post_content); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <style>
        .dealeredge-inventory {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        
        .vehicle-item {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 20px;
            background: #fff;
        }
        
        .vehicle-item h3 {
            margin: 0 0 15px 0;
            color: #333;
        }
        
        .vehicle-image {
            margin: 0 0 15px 0;
        }
        
        .vehicle-image img {
            width: 100%;
            height: auto;
            border-radius: 4px;
        }
        
        .vehicle-details p {
            margin: 5px 0;
        }
        
        .vehicle-price {
            font-size: 1.2em;
            color: #007cba;
        }
        
        .vehicle-description {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        </style>
        
        <?php
        return ob_get_clean();
    }
    
    public static function display_services($atts) {
        $atts = shortcode_atts(array(
            'category' => 'all',
            'limit' => -1
        ), $atts);
        
        $args = array(
            'taxonomy' => 'de_service_category',
            'hide_empty' => false,
            'number' => $atts['limit']
        );
        
        if ($atts['category'] !== 'all') {
            $args['slug'] = $atts['category'];
        }
        
        $services = get_terms($args);
        
        if (empty($services) || is_wp_error($services)) {
            return '<p>' . __('No services available.', 'dealeredge') . '</p>';
        }
        
        ob_start();
        ?>
        <div class="dealeredge-services">
            <?php foreach ($services as $service) : ?>
                <div class="service-item">
                    <h3><?php echo esc_html($service->name); ?></h3>
                    <?php if ($service->description) : ?>
                        <p><?php echo esc_html($service->description); ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        
        <style>
        .dealeredge-services {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        
        .service-item {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 20px;
            background: #fff;
        }
        
        .service-item h3 {
            margin: 0 0 10px 0;
            color: #333;
        }
        </style>
        
        <?php
        return ob_get_clean();
    }
}