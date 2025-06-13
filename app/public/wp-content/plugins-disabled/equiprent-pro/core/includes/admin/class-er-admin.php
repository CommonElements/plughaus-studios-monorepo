<?php
/**
 * EquipRent Pro Admin
 *
 * @package EquipRent_Pro
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handles admin functionality
 */
class ER_Admin {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menus'));
        add_action('admin_init', array($this, 'admin_init'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_notices', array($this, 'show_admin_notices'));
    }

    /**
     * Admin initialization
     */
    public function admin_init() {
        // Add custom columns to equipment list
        add_filter('manage_equipment_posts_columns', array($this, 'equipment_columns'));
        add_action('manage_equipment_posts_custom_column', array($this, 'equipment_column_content'), 10, 2);
        
        // Add custom columns to booking list
        add_filter('manage_er_booking_posts_columns', array($this, 'booking_columns'));
        add_action('manage_er_booking_posts_custom_column', array($this, 'booking_column_content'), 10, 2);
        
        // Add custom columns to customer list
        add_filter('manage_er_customer_posts_columns', array($this, 'customer_columns'));
        add_action('manage_er_customer_posts_custom_column', array($this, 'customer_column_content'), 10, 2);
    }

    /**
     * Add admin menus
     */
    public function add_admin_menus() {
        // Main menu
        add_menu_page(
            __('EquipRent Pro', 'equiprent-pro'),
            __('EquipRent Pro', 'equiprent-pro'),
            'manage_equipment',
            'equiprent-pro',
            array($this, 'dashboard_page'),
            'dashicons-hammer',
            25
        );

        // Dashboard submenu
        add_submenu_page(
            'equiprent-pro',
            __('Dashboard', 'equiprent-pro'),
            __('Dashboard', 'equiprent-pro'),
            'manage_equipment',
            'equiprent-pro',
            array($this, 'dashboard_page')
        );

        // Equipment submenu
        add_submenu_page(
            'equiprent-pro',
            __('Equipment', 'equiprent-pro'),
            __('Equipment', 'equiprent-pro'),
            'manage_equipment',
            'edit.php?post_type=equipment'
        );

        // Add New Equipment
        add_submenu_page(
            'equiprent-pro',
            __('Add Equipment', 'equiprent-pro'),
            __('Add Equipment', 'equiprent-pro'),
            'create_equipment',
            'post-new.php?post_type=equipment'
        );

        // Equipment Categories
        add_submenu_page(
            'equiprent-pro',
            __('Categories', 'equiprent-pro'),
            __('Categories', 'equiprent-pro'),
            'manage_equipment',
            'edit-tags.php?taxonomy=equipment_category&post_type=equipment'
        );

        // Bookings submenu
        add_submenu_page(
            'equiprent-pro',
            __('Bookings', 'equiprent-pro'),
            __('Bookings', 'equiprent-pro'),
            'manage_bookings',
            'edit.php?post_type=er_booking'
        );

        // Add New Booking
        add_submenu_page(
            'equiprent-pro',
            __('Add Booking', 'equiprent-pro'),
            __('Add Booking', 'equiprent-pro'),
            'create_bookings',
            'post-new.php?post_type=er_booking'
        );

        // Customers submenu
        add_submenu_page(
            'equiprent-pro',
            __('Customers', 'equiprent-pro'),
            __('Customers', 'equiprent-pro'),
            'manage_rental_customers',
            'edit.php?post_type=er_customer'
        );

        // Add New Customer
        add_submenu_page(
            'equiprent-pro',
            __('Add Customer', 'equiprent-pro'),
            __('Add Customer', 'equiprent-pro'),
            'edit_rental_customers',
            'post-new.php?post_type=er_customer'
        );

        // Calendar submenu
        add_submenu_page(
            'equiprent-pro',
            __('Calendar', 'equiprent-pro'),
            __('Calendar', 'equiprent-pro'),
            'manage_bookings',
            'equiprent-calendar',
            array($this, 'calendar_page')
        );

        // Reports submenu (Pro feature)
        if (ER_Utilities::is_pro()) {
            add_submenu_page(
                'equiprent-pro',
                __('Reports', 'equiprent-pro'),
                __('Reports', 'equiprent-pro'),
                'view_equiprent_reports',
                'equiprent-reports',
                array($this, 'reports_page')
            );
        }

        // Settings submenu
        add_submenu_page(
            'equiprent-pro',
            __('Settings', 'equiprent-pro'),
            __('Settings', 'equiprent-pro'),
            'manage_equiprent_settings',
            'equiprent-settings',
            array($this, 'settings_page')
        );
    }

    /**
     * Dashboard page
     */
    public function dashboard_page() {
        include_once EQUIPRENT_CORE_DIR . 'includes/admin/views/dashboard.php';
    }

    /**
     * Calendar page
     */
    public function calendar_page() {
        include_once EQUIPRENT_CORE_DIR . 'includes/admin/views/calendar.php';
    }

    /**
     * Reports page
     */
    public function reports_page() {
        if (ER_Utilities::is_pro()) {
            include_once EQUIPRENT_PRO_DIR . 'includes/admin/views/reports.php';
        } else {
            include_once EQUIPRENT_CORE_DIR . 'includes/admin/views/pro-upgrade.php';
        }
    }

    /**
     * Settings page
     */
    public function settings_page() {
        include_once EQUIPRENT_CORE_DIR . 'includes/admin/views/settings.php';
    }

    /**
     * Enqueue admin styles
     */
    public function enqueue_styles() {
        $screen = get_current_screen();
        
        if (strpos($screen->id, 'equiprent') !== false || 
            in_array($screen->post_type, array('equipment', 'er_booking', 'er_customer'))) {
            
            wp_enqueue_style(
                'equiprent-admin',
                EQUIPRENT_PLUGIN_URL . 'core/assets/css/admin.css',
                array(),
                EQUIPRENT_VERSION
            );
        }
    }

    /**
     * Enqueue admin scripts
     */
    public function enqueue_scripts() {
        $screen = get_current_screen();
        
        if (strpos($screen->id, 'equiprent') !== false || 
            in_array($screen->post_type, array('equipment', 'er_booking', 'er_customer'))) {
            
            wp_enqueue_script(
                'equiprent-admin',
                EQUIPRENT_PLUGIN_URL . 'core/assets/js/admin.js',
                array('jquery', 'wp-color-picker'),
                EQUIPRENT_VERSION,
                true
            );
            
            wp_localize_script('equiprent-admin', 'equiprent_admin', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('equiprent_admin_nonce'),
                'currency_symbol' => ER_Utilities::get_setting('currency_symbol', '$'),
                'date_format' => ER_Utilities::get_setting('date_format', 'Y-m-d'),
                'strings' => array(
                    'confirm_delete' => __('Are you sure you want to delete this item?', 'equiprent-pro'),
                    'loading' => __('Loading...', 'equiprent-pro'),
                    'error' => __('An error occurred. Please try again.', 'equiprent-pro'),
                )
            ));
        }
        
        // Enqueue calendar scripts on calendar page
        if (isset($_GET['page']) && $_GET['page'] === 'equiprent-calendar') {
            wp_enqueue_script(
                'equiprent-calendar',
                EQUIPRENT_PLUGIN_URL . 'core/assets/js/calendar.js',
                array('jquery'),
                EQUIPRENT_VERSION,
                true
            );
        }
    }

    /**
     * Show admin notices
     */
    public function show_admin_notices() {
        // Show setup notice for new installations
        if (get_option('equiprent_activated') && !get_option('equiprent_setup_completed')) {
            ?>
            <div class="notice notice-info is-dismissible">
                <p>
                    <strong><?php _e('Welcome to EquipRent Pro!', 'equiprent-pro'); ?></strong>
                    <?php _e('Get started by configuring your settings and adding your first equipment item.', 'equiprent-pro'); ?>
                    <a href="<?php echo admin_url('admin.php?page=equiprent-settings'); ?>" class="button button-primary">
                        <?php _e('Configure Settings', 'equiprent-pro'); ?>
                    </a>
                </p>
            </div>
            <?php
        }
    }

    /**
     * Equipment list table columns
     */
    public function equipment_columns($columns) {
        $new_columns = array();
        
        $new_columns['cb'] = $columns['cb'];
        $new_columns['title'] = $columns['title'];
        $new_columns['equipment_image'] = __('Image', 'equiprent-pro');
        $new_columns['equipment_sku'] = __('SKU', 'equiprent-pro');
        $new_columns['equipment_category'] = __('Category', 'equiprent-pro');
        $new_columns['equipment_status'] = __('Status', 'equiprent-pro');
        $new_columns['equipment_rate'] = __('Daily Rate', 'equiprent-pro');
        $new_columns['equipment_stock'] = __('Stock', 'equiprent-pro');
        $new_columns['date'] = $columns['date'];
        
        return $new_columns;
    }

    /**
     * Equipment column content
     */
    public function equipment_column_content($column, $post_id) {
        switch ($column) {
            case 'equipment_image':
                $thumbnail = get_the_post_thumbnail($post_id, array(50, 50));
                echo $thumbnail ? $thumbnail : '<span class="dashicons dashicons-format-image"></span>';
                break;
                
            case 'equipment_sku':
                $sku = get_post_meta($post_id, '_equipment_sku', true);
                echo $sku ? esc_html($sku) : '—';
                break;
                
            case 'equipment_category':
                $terms = get_the_terms($post_id, 'equipment_category');
                if ($terms && !is_wp_error($terms)) {
                    $categories = array();
                    foreach ($terms as $term) {
                        $categories[] = $term->name;
                    }
                    echo implode(', ', $categories);
                } else {
                    echo '—';
                }
                break;
                
            case 'equipment_status':
                $status = get_post_meta($post_id, '_equipment_status', true);
                if (!$status) $status = 'available';
                
                $statuses = ER_Post_Types::get_equipment_statuses();
                $status_label = isset($statuses[$status]) ? $statuses[$status] : $status;
                
                $class = 'status-' . $status;
                echo '<span class="equipment-status ' . esc_attr($class) . '">' . esc_html($status_label) . '</span>';
                break;
                
            case 'equipment_rate':
                $daily_rate = get_post_meta($post_id, '_equipment_daily_rate', true);
                echo $daily_rate ? ER_Utilities::format_currency($daily_rate) : '—';
                break;
                
            case 'equipment_stock':
                $stock = ER_Utilities::get_equipment_stock($post_id);
                echo sprintf(
                    '<span class="stock-info">%d / %d</span>',
                    $stock['available'],
                    $stock['total']
                );
                break;
        }
    }

    /**
     * Booking list table columns
     */
    public function booking_columns($columns) {
        $new_columns = array();
        
        $new_columns['cb'] = $columns['cb'];
        $new_columns['booking_number'] = __('Booking #', 'equiprent-pro');
        $new_columns['booking_customer'] = __('Customer', 'equiprent-pro');
        $new_columns['booking_dates'] = __('Rental Period', 'equiprent-pro');
        $new_columns['booking_total'] = __('Total', 'equiprent-pro');
        $new_columns['booking_status'] = __('Status', 'equiprent-pro');
        $new_columns['date'] = $columns['date'];
        
        return $new_columns;
    }

    /**
     * Booking column content
     */
    public function booking_column_content($column, $post_id) {
        switch ($column) {
            case 'booking_number':
                $booking_number = get_post_meta($post_id, '_booking_number', true);
                echo $booking_number ? '<strong>' . esc_html($booking_number) . '</strong>' : '—';
                break;
                
            case 'booking_customer':
                $customer_id = get_post_meta($post_id, '_customer_id', true);
                if ($customer_id) {
                    $customer = get_post($customer_id);
                    echo $customer ? esc_html($customer->post_title) : '—';
                } else {
                    echo '—';
                }
                break;
                
            case 'booking_dates':
                $start_date = get_post_meta($post_id, '_start_date', true);
                $end_date = get_post_meta($post_id, '_end_date', true);
                
                if ($start_date && $end_date) {
                    echo ER_Utilities::format_date($start_date) . ' - ' . ER_Utilities::format_date($end_date);
                } else {
                    echo '—';
                }
                break;
                
            case 'booking_total':
                $total = get_post_meta($post_id, '_total_amount', true);
                echo $total ? ER_Utilities::format_currency($total) : '—';
                break;
                
            case 'booking_status':
                $status = get_post_meta($post_id, '_booking_status', true);
                if (!$status) $status = 'pending';
                
                $statuses = ER_Post_Types::get_booking_statuses();
                $status_label = isset($statuses[$status]) ? $statuses[$status] : $status;
                
                $class = 'status-' . $status;
                echo '<span class="booking-status ' . esc_attr($class) . '">' . esc_html($status_label) . '</span>';
                break;
        }
    }

    /**
     * Customer list table columns
     */
    public function customer_columns($columns) {
        $new_columns = array();
        
        $new_columns['cb'] = $columns['cb'];
        $new_columns['title'] = __('Name', 'equiprent-pro');
        $new_columns['customer_type'] = __('Type', 'equiprent-pro');
        $new_columns['customer_email'] = __('Email', 'equiprent-pro');
        $new_columns['customer_phone'] = __('Phone', 'equiprent-pro');
        $new_columns['customer_bookings'] = __('Bookings', 'equiprent-pro');
        $new_columns['date'] = $columns['date'];
        
        return $new_columns;
    }

    /**
     * Customer column content
     */
    public function customer_column_content($column, $post_id) {
        switch ($column) {
            case 'customer_type':
                $type = get_post_meta($post_id, '_customer_type', true);
                echo $type === 'business' ? __('Business', 'equiprent-pro') : __('Individual', 'equiprent-pro');
                break;
                
            case 'customer_email':
                $email = get_post_meta($post_id, '_customer_email', true);
                echo $email ? '<a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a>' : '—';
                break;
                
            case 'customer_phone':
                $phone = get_post_meta($post_id, '_customer_phone', true);
                echo $phone ? '<a href="tel:' . esc_attr($phone) . '">' . esc_html($phone) . '</a>' : '—';
                break;
                
            case 'customer_bookings':
                global $wpdb;
                $bookings_table = $wpdb->prefix . 'er_bookings';
                
                $booking_count = $wpdb->get_var($wpdb->prepare("
                    SELECT COUNT(*) 
                    FROM {$bookings_table} 
                    WHERE customer_id = %d
                ", $post_id));
                
                echo (int)$booking_count;
                break;
        }
    }
}