<?php
/**
 * Vireo Designs Theme Functions
 * 
 * @package Vireo_Designs
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Load environment variables (development only)
if (defined('WP_DEBUG') && WP_DEBUG) {
    if (file_exists(get_template_directory() . '/includes/env-loader.php')) {
        require_once get_template_directory() . '/includes/env-loader.php';
    }
}

// Load secure Stripe configuration
require_once get_template_directory() . '/stripe-config.php';

// Load Stripe checkout functionality
require_once get_template_directory() . '/includes/stripe-checkout.php';

// Load download handler
require_once get_template_directory() . '/includes/download-handler.php';

// Load Vireo License Integration
require_once get_template_directory() . '/includes/vireo-license-integration.php';

// Load debug functions (temporary)
require_once get_template_directory() . '/functions-debug.php';

// Load Stripe Setup
require_once get_template_directory() . '/includes/stripe-setup.php';

// Load Stripe Webhook Handler
require_once get_template_directory() . '/includes/stripe-webhook-handler.php';

// Load WooCommerce Checkout Enhancements
require_once get_template_directory() . '/includes/woocommerce-checkout-enhancements.php';

// Load Secure Download System
require_once get_template_directory() . '/includes/secure-download-system.php';

// DISABLED: Purchase Flow Tester causes redirect loop in development
// The auto_redirect_to_test() function redirects homepage to /purchase-test/
// if (defined('WP_DEBUG') && WP_DEBUG) {
//     require_once get_template_directory() . '/includes/purchase-flow-tester.php';
// }

// Load Package Delivery System
require_once get_template_directory() . '/includes/vireo-package-delivery.php';

/**
 * Theme Setup
 */
function vireo_designs_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ));
    add_theme_support('automatic-feed-links');
    add_theme_support('customize-selective-refresh-widgets');

    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'vireo-designs'),
        'footer' => __('Footer Menu', 'vireo-designs'),
    ));

    // Add custom image sizes
    add_image_size('plugin-featured', 600, 400, true);
    add_image_size('plugin-thumbnail', 300, 200, true);
}
add_action('after_setup_theme', 'vireo_designs_setup');

/**
 * Enqueue Scripts and Styles
 */
function vireo_designs_scripts() {
    // Check if we're in development mode
    $is_dev_mode = defined('WP_DEBUG') && WP_DEBUG;
    $asset_version = $is_dev_mode ? time() : '1.0.0';
    
    // Main compiled stylesheet
    if (file_exists(get_template_directory() . '/assets/dist/css/main.css')) {
        wp_enqueue_style('vireo-main', get_template_directory_uri() . '/assets/dist/css/main.css', array(), $asset_version);
    } else {
        // Fallback to original stylesheet if compiled version doesn't exist
        wp_enqueue_style('vireo-fallback', get_stylesheet_uri(), array(), $asset_version);
    }
    
    // Admin compiled stylesheet
    if (is_admin() && file_exists(get_template_directory() . '/assets/dist/css/admin.css')) {
        wp_enqueue_style('vireo-admin', get_template_directory_uri() . '/assets/dist/css/admin.css', array(), $asset_version);
    }
    
    // Main compiled JavaScript
    if (file_exists(get_template_directory() . '/assets/dist/js/main.js')) {
        wp_enqueue_script('vireo-main', get_template_directory_uri() . '/assets/dist/js/main.js', array('jquery'), $asset_version, true);
    }
    
    // Admin compiled JavaScript
    if (is_admin() && file_exists(get_template_directory() . '/assets/dist/js/admin.js')) {
        wp_enqueue_script('vireo-admin-js', get_template_directory_uri() . '/assets/dist/js/admin.js', array('jquery'), $asset_version, true);
    }
    
    // Legacy assets for backwards compatibility (only if compiled versions don't exist)
    if (!file_exists(get_template_directory() . '/assets/dist/css/main.css')) {
        // Enqueue enhanced professional design
        wp_enqueue_style('vireo-designs-enhanced', get_template_directory_uri() . '/assets/css/enhanced-professional.css', array('vireo-fallback'), '1.0.0');
        
        // Enqueue navigation dropdown styles (high priority)
        wp_enqueue_style('vireo-navigation-dropdown', get_template_directory_uri() . '/assets/css/navigation-dropdown.css', array('vireo-designs-enhanced'), '1.0.0');
        
        // Enqueue enhanced header & footer
        wp_enqueue_style('vireo-header-footer', get_template_directory_uri() . '/assets/css/header-footer-enhanced.css', array('vireo-navigation-dropdown'), '1.0.0');
        
        // Enqueue component library
        wp_enqueue_style('vireo-components', get_template_directory_uri() . '/assets/css/components.css', array('vireo-designs-enhanced'), '1.0.0');
        
        // Enqueue refined plugin cards (high priority for plugin directory)
        wp_enqueue_style('vireo-plugin-cards-refined', get_template_directory_uri() . '/assets/css/plugin-cards-refined.css', array('vireo-components'), '1.0.2');
        
        // Enqueue plugin directory enhanced styles
        if (is_page_template('page-plugins.php') || is_page('plugins') || is_page('plugin-directory')) {
            wp_enqueue_style('vireo-plugin-directory-enhanced', get_template_directory_uri() . '/assets/css/plugin-directory-enhanced.css', array('vireo-plugin-cards-refined'), '1.0.1');
            wp_enqueue_script('vireo-plugin-cards-interactions', get_template_directory_uri() . '/assets/js/plugin-cards-interactions.js', array('jquery'), '1.0.1', true);
        }
        
        // Enqueue color overrides to reduce excessive green (highest priority)
        wp_enqueue_style('vireo-color-overrides', get_template_directory_uri() . '/assets/css/color-overrides.css', array('vireo-plugin-cards-refined'), '1.0.0');
        
        // Enqueue UI/UX enhancements
        wp_enqueue_style('vireo-ui-enhancements', get_template_directory_uri() . '/assets/css/ui-ux-enhancements.css', array('vireo-designs-enhanced'), '1.0.0');
        wp_enqueue_script('vireo-ui-enhancements', get_template_directory_uri() . '/assets/js/ui-ux-enhancements.js', array('jquery'), '1.0.0', true);
        
        // Enqueue bird elements
        wp_enqueue_style('vireo-bird-elements', get_template_directory_uri() . '/assets/css/vireo-bird-elements.css', array('vireo-ui-enhancements'), '1.0.0');
        wp_enqueue_script('vireo-bird-elements', get_template_directory_uri() . '/assets/js/vireo-bird-elements.js', array('vireo-ui-enhancements'), '1.0.0', true);
        
        // Enqueue homepage enhancements
        if (is_page_template('page-home.php') || is_page_template('page-home-enhanced.php') || is_front_page()) {
            wp_enqueue_style('vireo-homepage-enhanced', get_template_directory_uri() . '/assets/css/homepage-enhanced.css', array('vireo-designs-enhanced'), '1.0.0');
            wp_enqueue_style('vireo-homepage-redesigned', get_template_directory_uri() . '/assets/css/homepage-redesigned.css', array('vireo-homepage-enhanced'), '1.1.0');
        }
        
        // Theme JavaScript
        wp_enqueue_script('vireo-designs-script', get_template_directory_uri() . '/assets/js/theme.js', array('jquery'), '1.0.0', true);
        
        // Navigation dropdown JavaScript
        wp_enqueue_script('vireo-navigation-dropdown', get_template_directory_uri() . '/assets/js/navigation-dropdown.js', array('jquery'), '1.0.0', true);
        
        // Header & Footer JavaScript
        wp_enqueue_script('vireo-header-footer', get_template_directory_uri() . '/assets/js/header-footer.js', array(), '1.0.0', true);
    }
    
    // Google Fonts
    wp_enqueue_style('vireo-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap', array(), null);
    
    // Font Awesome
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css', array(), '6.0.0');
    
    // Localize script for AJAX
    $script_handle = file_exists(get_template_directory() . '/assets/dist/js/main.js') ? 'vireo-main' : 'vireo-designs-script';
    wp_localize_script($script_handle, 'vireo_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('vireo_nonce'),
        'api_url' => home_url('/wp-json/'),
    ));
}
add_action('wp_enqueue_scripts', 'vireo_designs_scripts');

/**
 * Register Custom Post Types
 */
function vireo_designs_register_post_types() {
    
    // Register Plugin Post Type
    register_post_type('phstudios_plugin', array(
        'labels' => array(
            'name' => __('Plugins', 'vireo-designs'),
            'singular_name' => __('Plugin', 'vireo-designs'),
            'menu_name' => __('Plugins', 'vireo-designs'),
            'add_new' => __('Add New Plugin', 'vireo-designs'),
            'add_new_item' => __('Add New Plugin', 'vireo-designs'),
            'edit_item' => __('Edit Plugin', 'vireo-designs'),
            'new_item' => __('New Plugin', 'vireo-designs'),
            'view_item' => __('View Plugin', 'vireo-designs'),
            'search_items' => __('Search Plugins', 'vireo-designs'),
            'not_found' => __('No plugins found', 'vireo-designs'),
            'not_found_in_trash' => __('No plugins found in trash', 'vireo-designs'),
        ),
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'menu_icon' => 'dashicons-plugins-checked',
        'rewrite' => array('slug' => 'plugin-catalog'),
        'show_in_nav_menus' => true,
    ));
    
    // Register Testimonial Post Type
    register_post_type('phstudios_testimonial', array(
        'labels' => array(
            'name' => __('Testimonials', 'vireo-designs'),
            'singular_name' => __('Testimonial', 'vireo-designs'),
            'menu_name' => __('Testimonials', 'vireo-designs'),
            'add_new' => __('Add New Testimonial', 'vireo-designs'),
            'add_new_item' => __('Add New Testimonial', 'vireo-designs'),
            'edit_item' => __('Edit Testimonial', 'vireo-designs'),
        ),
        'public' => true,
        'show_in_rest' => true,
        'supports' => array('title', 'editor', 'thumbnail'),
        'menu_icon' => 'dashicons-testimonial',
        'show_in_nav_menus' => false,
    ));
}
add_action('init', 'vireo_designs_register_post_types');

/**
 * Register Custom Taxonomies
 */
function vireo_designs_register_taxonomies() {
    
    // Plugin Categories
    register_taxonomy('plugin_category', 'phstudios_plugin', array(
        'labels' => array(
            'name' => __('Plugin Categories', 'vireo-designs'),
            'singular_name' => __('Plugin Category', 'vireo-designs'),
            'menu_name' => __('Categories', 'vireo-designs'),
            'all_items' => __('All Categories', 'vireo-designs'),
            'edit_item' => __('Edit Category', 'vireo-designs'),
            'view_item' => __('View Category', 'vireo-designs'),
            'update_item' => __('Update Category', 'vireo-designs'),
            'add_new_item' => __('Add New Category', 'vireo-designs'),
            'new_item_name' => __('New Category Name', 'vireo-designs'),
        ),
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'plugin-category'),
    ));
    
    // Plugin Tags
    register_taxonomy('plugin_tag', 'phstudios_plugin', array(
        'labels' => array(
            'name' => __('Plugin Tags', 'vireo-designs'),
            'singular_name' => __('Plugin Tag', 'vireo-designs'),
            'menu_name' => __('Tags', 'vireo-designs'),
            'all_items' => __('All Tags', 'vireo-designs'),
            'edit_item' => __('Edit Tag', 'vireo-designs'),
            'view_item' => __('View Tag', 'vireo-designs'),
            'update_item' => __('Update Tag', 'vireo-designs'),
            'add_new_item' => __('Add New Tag', 'vireo-designs'),
            'new_item_name' => __('New Tag Name', 'vireo-designs'),
        ),
        'hierarchical' => false,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'plugin-tag'),
    ));
}
add_action('init', 'vireo_designs_register_taxonomies');

/**
 * Add Custom Meta Boxes
 */
function vireo_designs_add_meta_boxes() {
    add_meta_box(
        'plugin_details',
        __('Plugin Details', 'vireo-designs'),
        'vireo_designs_plugin_details_callback',
        'phstudios_plugin',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'vireo_designs_add_meta_boxes');

/**
 * Plugin Details Meta Box Callback
 */
function vireo_designs_plugin_details_callback($post) {
    wp_nonce_field('vireo_designs_save_plugin_details', 'vireo_designs_plugin_details_nonce');
    
    $version = get_post_meta($post->ID, '_plugin_version', true);
    $status = get_post_meta($post->ID, '_plugin_status', true);
    $wordpress_url = get_post_meta($post->ID, '_wordpress_url', true);
    $github_url = get_post_meta($post->ID, '_github_url', true);
    $demo_url = get_post_meta($post->ID, '_demo_url', true);
    $price_free = get_post_meta($post->ID, '_price_free', true);
    $price_pro = get_post_meta($post->ID, '_price_pro', true);
    $download_count = get_post_meta($post->ID, '_download_count', true);
    $rating = get_post_meta($post->ID, '_rating', true);
    $tested_wp_version = get_post_meta($post->ID, '_tested_wp_version', true);
    $min_php_version = get_post_meta($post->ID, '_min_php_version', true);
    $features = get_post_meta($post->ID, '_plugin_features', true);
    $pro_features = get_post_meta($post->ID, '_pro_features', true);
    
    ?>
    <style>
        .plugin-meta-table { width: 100%; border-collapse: collapse; }
        .plugin-meta-table th, .plugin-meta-table td { padding: 12px; border-bottom: 1px solid #ddd; }
        .plugin-meta-table th { background: #f9f9f9; font-weight: 600; width: 200px; }
        .plugin-meta-table input, .plugin-meta-table select, .plugin-meta-table textarea { width: 100%; padding: 8px; }
        .plugin-meta-table textarea { height: 80px; resize: vertical; }
    </style>
    
    <table class="plugin-meta-table">
        <tr>
            <th><label for="plugin_version"><?php _e('Version', 'vireo-designs'); ?></label></th>
            <td><input type="text" id="plugin_version" name="plugin_version" value="<?php echo esc_attr($version); ?>" placeholder="1.0.0" /></td>
        </tr>
        <tr>
            <th><label for="plugin_status"><?php _e('Status', 'vireo-designs'); ?></label></th>
            <td>
                <select id="plugin_status" name="plugin_status">
                    <option value="available" <?php selected($status, 'available'); ?>><?php _e('Available', 'vireo-designs'); ?></option>
                    <option value="coming-soon" <?php selected($status, 'coming-soon'); ?>><?php _e('Coming Soon', 'vireo-designs'); ?></option>
                    <option value="in-development" <?php selected($status, 'in-development'); ?>><?php _e('In Development', 'vireo-designs'); ?></option>
                    <option value="beta" <?php selected($status, 'beta'); ?>><?php _e('Beta', 'vireo-designs'); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="wordpress_url"><?php _e('WordPress.org URL', 'vireo-designs'); ?></label></th>
            <td><input type="url" id="wordpress_url" name="wordpress_url" value="<?php echo esc_attr($wordpress_url); ?>" placeholder="https://wordpress.org/plugins/plugin-name/" /></td>
        </tr>
        <tr>
            <th><label for="github_url"><?php _e('GitHub URL', 'vireo-designs'); ?></label></th>
            <td><input type="url" id="github_url" name="github_url" value="<?php echo esc_attr($github_url); ?>" placeholder="https://github.com/username/plugin-name" /></td>
        </tr>
        <tr>
            <th><label for="demo_url"><?php _e('Demo URL', 'vireo-designs'); ?></label></th>
            <td><input type="url" id="demo_url" name="demo_url" value="<?php echo esc_attr($demo_url); ?>" placeholder="https://demo.vireodesigns.com/plugin-name" /></td>
        </tr>
        <tr>
            <th><label for="price_free"><?php _e('Free Version Price', 'vireo-designs'); ?></label></th>
            <td><input type="text" id="price_free" name="price_free" value="<?php echo esc_attr($price_free); ?>" placeholder="Free" /></td>
        </tr>
        <tr>
            <th><label for="price_pro"><?php _e('Pro Version Price', 'vireo-designs'); ?></label></th>
            <td><input type="text" id="price_pro" name="price_pro" value="<?php echo esc_attr($price_pro); ?>" placeholder="$99/year" /></td>
        </tr>
        <tr>
            <th><label for="download_count"><?php _e('Download Count', 'vireo-designs'); ?></label></th>
            <td><input type="number" id="download_count" name="download_count" value="<?php echo esc_attr($download_count); ?>" placeholder="1000" /></td>
        </tr>
        <tr>
            <th><label for="rating"><?php _e('Rating', 'vireo-designs'); ?></label></th>
            <td><input type="number" id="rating" name="rating" value="<?php echo esc_attr($rating); ?>" step="0.1" min="0" max="5" placeholder="4.8" /></td>
        </tr>
        <tr>
            <th><label for="tested_wp_version"><?php _e('Tested WP Version', 'vireo-designs'); ?></label></th>
            <td><input type="text" id="tested_wp_version" name="tested_wp_version" value="<?php echo esc_attr($tested_wp_version); ?>" placeholder="6.4" /></td>
        </tr>
        <tr>
            <th><label for="min_php_version"><?php _e('Minimum PHP Version', 'vireo-designs'); ?></label></th>
            <td><input type="text" id="min_php_version" name="min_php_version" value="<?php echo esc_attr($min_php_version); ?>" placeholder="7.4" /></td>
        </tr>
        <tr>
            <th><label for="plugin_features"><?php _e('Free Features (one per line)', 'vireo-designs'); ?></label></th>
            <td><textarea id="plugin_features" name="plugin_features" placeholder="Property Management&#10;Tenant Tracking&#10;Lease Management"><?php echo esc_textarea($features); ?></textarea></td>
        </tr>
        <tr>
            <th><label for="pro_features"><?php _e('Pro Features (one per line)', 'vireo-designs'); ?></label></th>
            <td><textarea id="pro_features" name="pro_features" placeholder="Advanced Analytics&#10;Payment Automation&#10;Custom Fields"><?php echo esc_textarea($pro_features); ?></textarea></td>
        </tr>
    </table>
    <?php
}

/**
 * Save Plugin Details Meta Data
 */
function vireo_designs_save_plugin_details($post_id) {
    if (!isset($_POST['vireo_designs_plugin_details_nonce'])) {
        return;
    }
    
    if (!wp_verify_nonce($_POST['vireo_designs_plugin_details_nonce'], 'vireo_designs_save_plugin_details')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    $fields = array(
        'plugin_version', 'plugin_status', 'wordpress_url', 'github_url', 'demo_url',
        'price_free', 'price_pro', 'download_count', 'rating', 'tested_wp_version',
        'min_php_version', 'plugin_features', 'pro_features'
    );
    
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
        }
    }
}
add_action('save_post', 'vireo_designs_save_plugin_details');

/**
 * Plugin Showcase Shortcode
 */
function vireo_designs_plugin_showcase_shortcode($atts) {
    $atts = shortcode_atts(array(
        'count' => 6,
        'category' => '',
        'status' => '',
        'featured' => false,
        'layout' => 'grid', // grid, list, carousel
    ), $atts, 'plugin_showcase');
    
    $args = array(
        'post_type' => 'phstudios_plugin',
        'posts_per_page' => intval($atts['count']),
        'post_status' => 'publish',
    );
    
    $meta_query = array();
    
    if (!empty($atts['status'])) {
        $meta_query[] = array(
            'key' => '_plugin_status',
            'value' => $atts['status'],
            'compare' => '='
        );
    }
    
    if ($atts['featured']) {
        $meta_query[] = array(
            'key' => '_featured_plugin',
            'value' => '1',
            'compare' => '='
        );
    }
    
    if (!empty($meta_query)) {
        $args['meta_query'] = $meta_query;
    }
    
    if (!empty($atts['category'])) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'plugin_category',
                'field' => 'slug',
                'terms' => $atts['category']
            )
        );
    }
    
    $plugins = new WP_Query($args);
    
    if (!$plugins->have_posts()) {
        return '<p>' . __('No plugins found.', 'vireo-designs') . '</p>';
    }
    
    ob_start();
    ?>
    <div class="plugins-showcase plugins-showcase-<?php echo esc_attr($atts['layout']); ?>">
        <?php while ($plugins->have_posts()) : $plugins->the_post(); ?>
            <?php get_template_part('template-parts/plugin-card'); ?>
        <?php endwhile; ?>
    </div>
    <?php
    wp_reset_postdata();
    
    return ob_get_clean();
}
add_shortcode('plugin_showcase', 'vireo_designs_plugin_showcase_shortcode');

/**
 * Plugin Stats Shortcode
 */
function vireo_designs_plugin_stats_shortcode($atts) {
    $atts = shortcode_atts(array(
        'plugin_id' => '',
        'show' => 'downloads,rating,version', // comma-separated list
    ), $atts, 'plugin_stats');
    
    if (empty($atts['plugin_id'])) {
        return '<p>' . __('Plugin ID required.', 'vireo-designs') . '</p>';
    }
    
    $plugin = get_post($atts['plugin_id']);
    if (!$plugin || $plugin->post_type !== 'phstudios_plugin') {
        return '<p>' . __('Plugin not found.', 'vireo-designs') . '</p>';
    }
    
    $show_items = array_map('trim', explode(',', $atts['show']));
    $stats = array();
    
    foreach ($show_items as $item) {
        switch ($item) {
            case 'downloads':
                $downloads = get_post_meta($plugin->ID, '_download_count', true);
                if ($downloads) {
                    $stats[] = '<div class="plugin-stat"><span class="stat-number">' . number_format($downloads) . '</span><span class="stat-label">' . __('Downloads', 'vireo-designs') . '</span></div>';
                }
                break;
                
            case 'rating':
                $rating = get_post_meta($plugin->ID, '_rating', true);
                if ($rating) {
                    $stats[] = '<div class="plugin-stat"><span class="stat-number">' . $rating . '★</span><span class="stat-label">' . __('Rating', 'vireo-designs') . '</span></div>';
                }
                break;
                
            case 'version':
                $version = get_post_meta($plugin->ID, '_plugin_version', true);
                if ($version) {
                    $stats[] = '<div class="plugin-stat"><span class="stat-number">v' . $version . '</span><span class="stat-label">' . __('Version', 'vireo-designs') . '</span></div>';
                }
                break;
        }
    }
    
    if (empty($stats)) {
        return '<p>' . __('No stats available.', 'vireo-designs') . '</p>';
    }
    
    return '<div class="plugin-stats">' . implode('', $stats) . '</div>';
}
add_shortcode('plugin_stats', 'vireo_designs_plugin_stats_shortcode');

/**
 * Contact Form Shortcode
 */
function vireo_designs_contact_form_shortcode($atts) {
    $atts = shortcode_atts(array(
        'title' => __('Get In Touch', 'vireo-designs'),
        'email' => get_option('admin_email'),
    ), $atts, 'contact_form');
    
    ob_start();
    ?>
    <div class="contact-form-container">
        <?php if (!empty($atts['title'])) : ?>
            <h3><?php echo esc_html($atts['title']); ?></h3>
        <?php endif; ?>
        
        <form class="vireo-contact-form" method="post" action="">
            <?php wp_nonce_field('vireo_contact_form', 'contact_nonce'); ?>
            
            <div class="form-group">
                <label for="contact_name"><?php _e('Name', 'vireo-designs'); ?> *</label>
                <input type="text" id="contact_name" name="contact_name" required>
            </div>
            
            <div class="form-group">
                <label for="contact_email"><?php _e('Email', 'vireo-designs'); ?> *</label>
                <input type="email" id="contact_email" name="contact_email" required>
            </div>
            
            <div class="form-group">
                <label for="contact_subject"><?php _e('Subject', 'vireo-designs'); ?> *</label>
                <select id="contact_subject" name="contact_subject" required>
                    <option value=""><?php _e('Select a topic', 'vireo-designs'); ?></option>
                    <option value="general"><?php _e('General Inquiry', 'vireo-designs'); ?></option>
                    <option value="support"><?php _e('Plugin Support', 'vireo-designs'); ?></option>
                    <option value="custom"><?php _e('Custom Development', 'vireo-designs'); ?></option>
                    <option value="partnership"><?php _e('Partnership', 'vireo-designs'); ?></option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="contact_message"><?php _e('Message', 'vireo-designs'); ?> *</label>
                <textarea id="contact_message" name="contact_message" rows="5" required></textarea>
            </div>
            
            <button type="submit" name="submit_contact_form" class="btn btn-primary">
                <i class="fas fa-paper-plane"></i>
                <?php _e('Send Message', 'vireo-designs'); ?>
            </button>
        </form>
    </div>
    <?php
    
    return ob_get_clean();
}
add_shortcode('contact_form', 'vireo_designs_contact_form_shortcode');

/**
 * Handle Contact Form Submission
 */
function vireo_designs_handle_contact_form() {
    if (!isset($_POST['submit_contact_form']) || !wp_verify_nonce($_POST['contact_nonce'], 'vireo_contact_form')) {
        return;
    }
    
    $name = sanitize_text_field($_POST['contact_name']);
    $email = sanitize_email($_POST['contact_email']);
    $subject = sanitize_text_field($_POST['contact_subject']);
    $message = sanitize_textarea_field($_POST['contact_message']);
    
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        wp_die(__('Please fill in all required fields.', 'vireo-designs'));
    }
    
    $to = get_option('admin_email');
    $email_subject = sprintf(__('[%s] Contact Form: %s', 'vireo-designs'), get_bloginfo('name'), $subject);
    $email_message = sprintf(
        __("Name: %s\nEmail: %s\nSubject: %s\n\nMessage:\n%s", 'vireo-designs'),
        $name,
        $email,
        $subject,
        $message
    );
    
    $headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        'From: ' . $name . ' <' . $email . '>',
        'Reply-To: ' . $email,
    );
    
    if (wp_mail($to, $email_subject, $email_message, $headers)) {
        wp_redirect(add_query_arg('contact', 'success', wp_get_referer()));
    } else {
        wp_redirect(add_query_arg('contact', 'error', wp_get_referer()));
    }
    exit;
}
add_action('init', 'vireo_designs_handle_contact_form');

/**
 * Add Theme Customizer Options
 */
function vireo_designs_customize_register($wp_customize) {
    // Hero Section
    $wp_customize->add_section('hero_section', array(
        'title' => __('Hero Section', 'vireo-designs'),
        'priority' => 30,
    ));
    
    $wp_customize->add_setting('hero_title', array(
        'default' => __('Professional WordPress Plugins Built for Business', 'vireo-designs'),
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('hero_title', array(
        'label' => __('Hero Title', 'vireo-designs'),
        'section' => 'hero_section',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('hero_description', array(
        'default' => __('We create powerful, scalable WordPress plugins that solve real business problems.', 'vireo-designs'),
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    
    $wp_customize->add_control('hero_description', array(
        'label' => __('Hero Description', 'vireo-designs'),
        'section' => 'hero_section',
        'type' => 'textarea',
    ));
    
    // Contact Information
    $wp_customize->add_section('contact_info', array(
        'title' => __('Contact Information', 'vireo-designs'),
        'priority' => 40,
    ));
    
    $wp_customize->add_setting('contact_email', array(
        'default' => 'hello@vireodesigns.com',
        'sanitize_callback' => 'sanitize_email',
    ));
    
    $wp_customize->add_control('contact_email', array(
        'label' => __('Contact Email', 'vireo-designs'),
        'section' => 'contact_info',
        'type' => 'email',
    ));
    
    $wp_customize->add_setting('support_email', array(
        'default' => 'support@vireodesigns.com',
        'sanitize_callback' => 'sanitize_email',
    ));
    
    $wp_customize->add_control('support_email', array(
        'label' => __('Support Email', 'vireo-designs'),
        'section' => 'contact_info',
        'type' => 'email',
    ));
}
add_action('customize_register', 'vireo_designs_customize_register');

/**
 * Add Admin Styles
 */
function vireo_designs_admin_styles() {
    wp_enqueue_style('vireo-designs-admin', get_template_directory_uri() . '/assets/css/admin.css', array(), '1.0.0');
}
add_action('admin_enqueue_scripts', 'vireo_designs_admin_styles');

/**
 * Create Default Pages on Theme Activation
 */
function vireo_designs_create_default_pages() {
    $pages = array(
        'features' => array(
            'title' => 'Features',
            'content' => 'Discover the powerful features that make our plugins stand out.',
            'template' => 'page-features.php'
        ),
        'plugins' => array(
            'title' => 'Our Plugins',
            'content' => '[plugin_showcase count="-1"]',
            'template' => 'page-plugins.php'
        ),
        'pricing' => array(
            'title' => 'Pricing',
            'content' => 'Simple, transparent pricing for every business size.',
            'template' => 'page-pricing.php'
        ),
        'about' => array(
            'title' => 'About Us',
            'content' => 'We\'re a WordPress plugin development studio focused on creating professional solutions for modern businesses.',
            'template' => 'page-about.php'
        ),
        'contact' => array(
            'title' => 'Contact',
            'content' => '[contact_form]',
            'template' => 'page-contact.php'
        ),
        'support' => array(
            'title' => 'Support',
            'content' => 'Get help with our plugins and find answers to common questions.',
            'template' => 'page-support.php'
        ),
        'blog' => array(
            'title' => 'Blog',
            'content' => 'Stay updated with the latest news, tutorials, and insights.',
            'template' => 'page-blog.php'
        ),
    );
    
    foreach ($pages as $slug => $page) {
        if (!get_page_by_path($slug)) {
            $page_data = array(
                'post_title' => $page['title'],
                'post_content' => $page['content'],
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_name' => $slug,
            );
            
            $page_id = wp_insert_post($page_data);
            
            if ($page_id && isset($page['template'])) {
                update_post_meta($page_id, '_wp_page_template', $page['template']);
            }
            
            // Auto-assign navigation menu
            $menu_name = 'Primary Menu';
            $menu = wp_get_nav_menu_object($menu_name);
            if (!$menu) {
                $menu_id = wp_create_nav_menu($menu_name);
                
                // Create menu items
                $menu_items = array(
                    array('title' => 'Home', 'url' => home_url('/')),
                    array('title' => 'Features', 'url' => home_url('/features/')),
                    array('title' => 'Plugins', 'url' => home_url('/plugins/')),
                    array('title' => 'Pricing', 'url' => home_url('/pricing/')),
                    array('title' => 'Blog', 'url' => home_url('/blog/')),
                    array('title' => 'About', 'url' => home_url('/about/')),
                    array('title' => 'Contact', 'url' => home_url('/contact/'))
                );
                
                foreach ($menu_items as $index => $item) {
                    wp_update_nav_menu_item($menu_id, 0, array(
                        'menu-item-title' => $item['title'],
                        'menu-item-url' => $item['url'],
                        'menu-item-status' => 'publish',
                        'menu-item-position' => $index + 1
                    ));
                }
                
                // Assign menu to theme location
                $locations = get_theme_mod('nav_menu_locations');
                $locations['primary'] = $menu_id;
                set_theme_mod('nav_menu_locations', $locations);
            }
        }
    }
}
add_action('after_switch_theme', 'vireo_designs_create_default_pages');

// Flush rewrite rules on theme activation
function vireo_designs_flush_rewrites() {
    vireo_designs_register_post_types();
    vireo_designs_register_taxonomies();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'vireo_designs_flush_rewrites');
// WooCommerce integration functions
function vireo_add_woocommerce_support() {
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'vireo_add_woocommerce_support');

// Add Buy Pro button to plugin cards
function vireo_get_plugin_pro_button($plugin_id) {
    $product_id = get_post_meta($plugin_id, '_linked_product', true);
    if ($product_id) {
        $product = wc_get_product($product_id);
        if ($product) {
            $price = $product->get_price_html();
            $url = $product->get_permalink();
            return '<a href="' . $url . '" class="btn btn-pro">Get Pro ' . $price . '</a>';
        }
    }
    return '';
}

// Display license info in My Account
function vireo_display_customer_licenses() {
    if (function_exists('lmfwc_get_customer_licenses')) {
        $customer_id = get_current_user_id();
        $licenses = lmfwc_get_customer_licenses($customer_id);
        
        if ($licenses) {
            echo '<h3>Your Plugin Licenses</h3>';
            echo '<table class="shop_table">';
            echo '<thead><tr><th>Plugin</th><th>License Key</th><th>Status</th><th>Expires</th></tr></thead>';
            echo '<tbody>';
            
            foreach ($licenses as $license) {
                echo '<tr>';
                echo '<td>' . $license->product_name . '</td>';
                echo '<td><code>' . $license->license_key . '</code></td>';
                echo '<td>' . $license->status . '</td>';
                echo '<td>' . ($license->expires_at ? $license->expires_at : 'Never') . '</td>';
                echo '</tr>';
            }
            
            echo '</tbody></table>';
        }
    }
}
add_action('woocommerce_account_dashboard', 'vireo_display_customer_licenses');

// Custom product fields for license management
function vireo_add_license_fields() {
    global $post;
    if ($post->post_type !== 'product') return;
    
    echo '<div class="options_group">';
    
    woocommerce_wp_checkbox(array(
        'id' => '_enable_license_management',
        'label' => 'Enable License Management',
        'description' => 'Generate license keys for this product'
    ));
    
    woocommerce_wp_text_input(array(
        'id' => '_license_activations_limit',
        'label' => 'Activation Limit',
        'description' => 'Maximum number of sites for license',
        'type' => 'number',
        'custom_attributes' => array('min' => '1', 'max' => '100')
    ));
    
    echo '</div>';
}
add_action('woocommerce_product_options_general_product_data', 'vireo_add_license_fields');

function vireo_save_license_fields($post_id) {
    $enable_license = isset($_POST['_enable_license_management']) ? 'yes' : 'no';
    update_post_meta($post_id, '_enable_license_management', $enable_license);
    
    if (isset($_POST['_license_activations_limit'])) {
        update_post_meta($post_id, '_license_activations_limit', sanitize_text_field($_POST['_license_activations_limit']));
    }
}
add_action('woocommerce_process_product_meta', 'vireo_save_license_fields');

/**
 * Professional Menu Fallback
 */
function vireo_professional_menu() {
    echo '<ul class="nav-menu">';
    echo '<li><a href="' . home_url('/') . '">Home</a></li>';
    echo '<li><a href="' . home_url('/plugins/') . '">Plugins</a></li>';
    echo '<li><a href="' . home_url('/plugin-directory/') . '">Browse All</a></li>';
    echo '<li><a href="' . home_url('/features/') . '">Features</a></li>';
    echo '<li><a href="' . home_url('/pricing/') . '">Pricing</a></li>';
    echo '<li><a href="' . home_url('/about/') . '">About</a></li>';
    echo '<li><a href="' . home_url('/blog/') . '">Blog</a></li>';
    echo '</ul>';
}