<?php
/**
 * GymFlow Admin Class
 *
 * Handles all admin interface functionality including menus, pages, and dashboard
 *
 * @package GymFlow
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * GF_Admin Class
 *
 * Core admin interface functionality
 */
class GF_Admin {

    /**
     * Admin pages
     * @var array
     */
    private $admin_pages = array();

    /**
     * Initialize admin
     */
    public function init() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'admin_init'));
        add_action('admin_notices', array($this, 'admin_notices'));
        add_action('wp_ajax_gf_quick_stats', array($this, 'ajax_quick_stats'));
        add_action('wp_ajax_gf_recent_activity', array($this, 'ajax_recent_activity'));
        
        // Handle bulk actions
        add_action('admin_init', array($this, 'handle_bulk_actions'));
        
        // Custom columns for post types
        add_filter('manage_gf_member_posts_columns', array($this, 'member_columns'));
        add_action('manage_gf_member_posts_custom_column', array($this, 'member_column_content'), 10, 2);
        add_filter('manage_gf_class_posts_columns', array($this, 'class_columns'));
        add_action('manage_gf_class_posts_custom_column', array($this, 'class_column_content'), 10, 2);
        add_filter('manage_gf_trainer_posts_columns', array($this, 'trainer_columns'));
        add_action('manage_gf_trainer_posts_custom_column', array($this, 'trainer_column_content'), 10, 2);
        add_filter('manage_gf_booking_posts_columns', array($this, 'booking_columns'));
        add_action('manage_gf_booking_posts_custom_column', array($this, 'booking_column_content'), 10, 2);
        add_filter('manage_gf_equipment_posts_columns', array($this, 'equipment_columns'));
        add_action('manage_gf_equipment_posts_custom_column', array($this, 'equipment_column_content'), 10, 2);
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        // Main menu
        add_menu_page(
            __('GymFlow', 'gymflow'),
            __('GymFlow', 'gymflow'),
            'manage_gymflow',
            'gymflow-dashboard',
            array($this, 'dashboard_page'),
            'dashicons-heart',
            25
        );

        // Dashboard
        add_submenu_page(
            'gymflow-dashboard',
            __('Dashboard', 'gymflow'),
            __('Dashboard', 'gymflow'),
            'manage_gymflow',
            'gymflow-dashboard',
            array($this, 'dashboard_page')
        );

        // Members
        add_submenu_page(
            'gymflow-dashboard',
            __('Members', 'gymflow'),
            __('Members', 'gymflow'),
            'manage_gymflow_members',
            'gymflow-members',
            array($this, 'members_page')
        );

        // Classes
        add_submenu_page(
            'gymflow-dashboard',
            __('Classes', 'gymflow'),
            __('Classes', 'gymflow'),
            'manage_gymflow_classes',
            'gymflow-classes',
            array($this, 'classes_page')
        );

        // Trainers
        add_submenu_page(
            'gymflow-dashboard',
            __('Trainers', 'gymflow'),
            __('Trainers', 'gymflow'),
            'manage_gymflow_trainers',
            'gymflow-trainers',
            array($this, 'trainers_page')
        );

        // Equipment
        add_submenu_page(
            'gymflow-dashboard',
            __('Equipment', 'gymflow'),
            __('Equipment', 'gymflow'),
            'manage_gymflow_equipment',
            'gymflow-equipment',
            array($this, 'equipment_page')
        );

        // Bookings
        add_submenu_page(
            'gymflow-dashboard',
            __('Bookings', 'gymflow'),
            __('Bookings', 'gymflow'),
            'manage_gymflow_bookings',
            'gymflow-bookings',
            array($this, 'bookings_page')
        );

        // Reports (Pro feature)
        if (GF_Capabilities::current_user_can_view_reports()) {
            add_submenu_page(
                'gymflow-dashboard',
                __('Reports', 'gymflow'),
                __('Reports', 'gymflow'),
                'view_gymflow_reports',
                'gymflow-reports',
                array($this, 'reports_page')
            );
        }

        // Settings
        add_submenu_page(
            'gymflow-dashboard',
            __('Settings', 'gymflow'),
            __('Settings', 'gymflow'),
            'manage_gymflow',
            'gymflow-settings',
            array($this, 'settings_page')
        );
    }

    /**
     * Admin initialization
     */
    public function admin_init() {
        // Register settings
        $this->register_settings();
    }

    /**
     * Register plugin settings
     */
    private function register_settings() {
        $setting_groups = array(
            'gymflow_general_settings',
            'gymflow_booking_settings',
            'gymflow_membership_settings',
            'gymflow_class_settings',
            'gymflow_equipment_settings',
            'gymflow_notification_settings',
            'gymflow_payment_settings'
        );

        foreach ($setting_groups as $group) {
            register_setting('gymflow_settings', $group);
        }
    }

    /**
     * Dashboard page
     */
    public function dashboard_page() {
        $stats = $this->get_dashboard_stats();
        $recent_activity = $this->get_recent_activity();
        $upcoming_classes = $this->get_upcoming_classes();
        
        include GYMFLOW_PLUGIN_PATH . 'core/includes/admin/views/dashboard.php';
    }

    /**
     * Members page
     */
    public function members_page() {
        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'list';
        $member_id = isset($_GET['member_id']) ? intval($_GET['member_id']) : 0;

        switch ($action) {
            case 'add':
                include GYMFLOW_PLUGIN_PATH . 'core/includes/admin/views/member-edit.php';
                break;
            case 'edit':
                if ($member_id > 0) {
                    $member = new GF_Member($member_id);
                    include GYMFLOW_PLUGIN_PATH . 'core/includes/admin/views/member-edit.php';
                } else {
                    wp_redirect(admin_url('admin.php?page=gymflow-members'));
                    exit;
                }
                break;
            case 'view':
                if ($member_id > 0) {
                    $member = new GF_Member($member_id);
                    include GYMFLOW_PLUGIN_PATH . 'core/includes/admin/views/member-view.php';
                } else {
                    wp_redirect(admin_url('admin.php?page=gymflow-members'));
                    exit;
                }
                break;
            default:
                $this->members_list_page();
                break;
        }
    }

    /**
     * Classes page
     */
    public function classes_page() {
        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'list';
        $class_id = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;

        switch ($action) {
            case 'add':
                include GYMFLOW_PLUGIN_PATH . 'core/includes/admin/views/class-edit.php';
                break;
            case 'edit':
                if ($class_id > 0) {
                    $class = new GF_Class($class_id);
                    include GYMFLOW_PLUGIN_PATH . 'core/includes/admin/views/class-edit.php';
                } else {
                    wp_redirect(admin_url('admin.php?page=gymflow-classes'));
                    exit;
                }
                break;
            case 'schedule':
                if ($class_id > 0) {
                    $class = new GF_Class($class_id);
                    include GYMFLOW_PLUGIN_PATH . 'core/includes/admin/views/class-schedule.php';
                } else {
                    wp_redirect(admin_url('admin.php?page=gymflow-classes'));
                    exit;
                }
                break;
            default:
                $this->classes_list_page();
                break;
        }
    }

    /**
     * Trainers page
     */
    public function trainers_page() {
        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'list';
        $trainer_id = isset($_GET['trainer_id']) ? intval($_GET['trainer_id']) : 0;

        switch ($action) {
            case 'add':
                include GYMFLOW_PLUGIN_PATH . 'core/includes/admin/views/trainer-edit.php';
                break;
            case 'edit':
                if ($trainer_id > 0) {
                    $trainer = new GF_Trainer($trainer_id);
                    include GYMFLOW_PLUGIN_PATH . 'core/includes/admin/views/trainer-edit.php';
                } else {
                    wp_redirect(admin_url('admin.php?page=gymflow-trainers'));
                    exit;
                }
                break;
            case 'view':
                if ($trainer_id > 0) {
                    $trainer = new GF_Trainer($trainer_id);
                    include GYMFLOW_PLUGIN_PATH . 'core/includes/admin/views/trainer-view.php';
                } else {
                    wp_redirect(admin_url('admin.php?page=gymflow-trainers'));
                    exit;
                }
                break;
            default:
                $this->trainers_list_page();
                break;
        }
    }

    /**
     * Equipment page
     */
    public function equipment_page() {
        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'list';
        $equipment_id = isset($_GET['equipment_id']) ? intval($_GET['equipment_id']) : 0;

        switch ($action) {
            case 'add':
                include GYMFLOW_PLUGIN_PATH . 'core/includes/admin/views/equipment-edit.php';
                break;
            case 'edit':
                if ($equipment_id > 0) {
                    include GYMFLOW_PLUGIN_PATH . 'core/includes/admin/views/equipment-edit.php';
                } else {
                    wp_redirect(admin_url('admin.php?page=gymflow-equipment'));
                    exit;
                }
                break;
            default:
                $this->equipment_list_page();
                break;
        }
    }

    /**
     * Bookings page
     */
    public function bookings_page() {
        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'list';
        $booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;

        switch ($action) {
            case 'add':
                include GYMFLOW_PLUGIN_PATH . 'core/includes/admin/views/booking-edit.php';
                break;
            case 'edit':
                if ($booking_id > 0) {
                    $booking = new GF_Booking($booking_id);
                    include GYMFLOW_PLUGIN_PATH . 'core/includes/admin/views/booking-edit.php';
                } else {
                    wp_redirect(admin_url('admin.php?page=gymflow-bookings'));
                    exit;
                }
                break;
            case 'view':
                if ($booking_id > 0) {
                    $booking = new GF_Booking($booking_id);
                    include GYMFLOW_PLUGIN_PATH . 'core/includes/admin/views/booking-view.php';
                } else {
                    wp_redirect(admin_url('admin.php?page=gymflow-bookings'));
                    exit;
                }
                break;
            default:
                $this->bookings_list_page();
                break;
        }
    }

    /**
     * Reports page (Pro feature)
     */
    public function reports_page() {
        if (!GF_Capabilities::current_user_can_view_reports()) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'gymflow'));
        }

        $report_type = isset($_GET['report']) ? sanitize_text_field($_GET['report']) : 'overview';
        
        include GYMFLOW_PLUGIN_PATH . 'core/includes/admin/views/reports.php';
    }

    /**
     * Settings page
     */
    public function settings_page() {
        $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';
        
        // Handle form submissions
        if (isset($_POST['submit']) && wp_verify_nonce($_POST['_wpnonce'], 'gymflow_settings')) {
            $this->save_settings($active_tab);
            add_settings_error('gymflow_settings', 'settings_updated', __('Settings saved.', 'gymflow'), 'updated');
        }

        include GYMFLOW_PLUGIN_PATH . 'core/includes/admin/views/settings.php';
    }

    /**
     * Get dashboard statistics
     */
    private function get_dashboard_stats() {
        return array(
            'total_members' => GF_Member::get_count(),
            'active_members' => GF_Member::get_count('active'),
            'total_classes' => GF_Class::get_count(),
            'total_trainers' => GF_Trainer::get_count(),
            'todays_bookings' => GF_Booking::get_count(array(
                'date_from' => date('Y-m-d'),
                'date_to' => date('Y-m-d')
            )),
            'pending_bookings' => GF_Booking::get_count(array('status' => 'pending')),
            'monthly_revenue' => $this->get_monthly_revenue(),
            'attendance_rate' => $this->get_attendance_rate()
        );
    }

    /**
     * Get recent activity
     */
    private function get_recent_activity() {
        global $wpdb;

        $activities = array();

        // Recent member registrations
        $members_table = $wpdb->prefix . 'gf_members';
        $recent_members = $wpdb->get_results(
            "SELECT first_name, last_name, created_at 
             FROM {$members_table} 
             ORDER BY created_at DESC 
             LIMIT 5"
        );

        foreach ($recent_members as $member) {
            $activities[] = array(
                'type' => 'member_registration',
                'message' => sprintf(__('%s %s joined', 'gymflow'), $member->first_name, $member->last_name),
                'time' => $member->created_at
            );
        }

        // Recent bookings
        $bookings_table = $wpdb->prefix . 'gf_bookings';
        $recent_bookings = $wpdb->get_results(
            "SELECT b.booking_type, b.created_at, m.first_name, m.last_name
             FROM {$bookings_table} b
             INNER JOIN {$members_table} m ON b.member_id = m.id
             ORDER BY b.created_at DESC 
             LIMIT 5"
        );

        foreach ($recent_bookings as $booking) {
            $activities[] = array(
                'type' => 'booking',
                'message' => sprintf(__('%s %s made a %s booking', 'gymflow'), 
                    $booking->first_name, $booking->last_name, $booking->booking_type),
                'time' => $booking->created_at
            );
        }

        // Sort by time
        usort($activities, function($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });

        return array_slice($activities, 0, 10);
    }

    /**
     * Get upcoming classes
     */
    private function get_upcoming_classes() {
        global $wpdb;

        $schedules_table = $wpdb->prefix . 'gf_class_schedules';
        $classes_table = $wpdb->prefix . 'gf_classes';

        return $wpdb->get_results(
            "SELECT s.*, c.name as class_name
             FROM {$schedules_table} s
             INNER JOIN {$classes_table} c ON s.class_id = c.id
             WHERE s.date >= CURDATE() AND s.status = 'scheduled'
             ORDER BY s.date ASC, s.start_time ASC
             LIMIT 10"
        );
    }

    /**
     * Get monthly revenue
     */
    private function get_monthly_revenue() {
        global $wpdb;

        $payments_table = $wpdb->prefix . 'gf_payments';
        
        return $wpdb->get_var(
            "SELECT SUM(amount) 
             FROM {$payments_table} 
             WHERE status = 'completed' 
             AND MONTH(payment_date) = MONTH(CURDATE()) 
             AND YEAR(payment_date) = YEAR(CURDATE())"
        ) ?: 0;
    }

    /**
     * Get attendance rate
     */
    private function get_attendance_rate() {
        global $wpdb;

        $bookings_table = $wpdb->prefix . 'gf_bookings';
        
        $total_bookings = $wpdb->get_var(
            "SELECT COUNT(*) 
             FROM {$bookings_table} 
             WHERE booking_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
             AND status IN ('completed', 'no_show')"
        );

        $completed_bookings = $wpdb->get_var(
            "SELECT COUNT(*) 
             FROM {$bookings_table} 
             WHERE booking_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
             AND status = 'completed'"
        );

        if ($total_bookings > 0) {
            return round(($completed_bookings / $total_bookings) * 100, 1);
        }

        return 0;
    }

    /**
     * Members list page
     */
    private function members_list_page() {
        $search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
        $status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : 'all';
        
        $args = array(
            'limit' => 20,
            'search' => $search,
            'status' => $status
        );

        $members = GF_Member::get_all($args);
        $total_members = GF_Member::get_count();
        
        include GYMFLOW_PLUGIN_PATH . 'core/includes/admin/views/members-list.php';
    }

    /**
     * Classes list page
     */
    private function classes_list_page() {
        $search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
        $category = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : '';
        
        $args = array(
            'limit' => 20,
            'search' => $search,
            'category' => $category
        );

        $classes = GF_Class::get_all($args);
        $total_classes = GF_Class::get_count();
        
        include GYMFLOW_PLUGIN_PATH . 'core/includes/admin/views/classes-list.php';
    }

    /**
     * Trainers list page
     */
    private function trainers_list_page() {
        $search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
        $specialty = isset($_GET['specialty']) ? sanitize_text_field($_GET['specialty']) : '';
        
        $args = array(
            'limit' => 20,
            'search' => $search,
            'specialty' => $specialty
        );

        $trainers = GF_Trainer::get_all($args);
        $total_trainers = GF_Trainer::get_count();
        
        include GYMFLOW_PLUGIN_PATH . 'core/includes/admin/views/trainers-list.php';
    }

    /**
     * Equipment list page
     */
    private function equipment_list_page() {
        global $wpdb;
        
        $search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
        $category = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : '';
        $status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
        
        $equipment_table = $wpdb->prefix . 'gf_equipment';
        $where = array('1=1');
        $params = array();

        if (!empty($search)) {
            $search_term = '%' . $wpdb->esc_like($search) . '%';
            $where[] = "(name LIKE %s OR description LIKE %s)";
            $params[] = $search_term;
            $params[] = $search_term;
        }

        if (!empty($category)) {
            $where[] = "category = %s";
            $params[] = $category;
        }

        if (!empty($status)) {
            $where[] = "status = %s";
            $params[] = $status;
        }

        $sql = "SELECT * FROM {$equipment_table} WHERE " . implode(' AND ', $where) . " ORDER BY name ASC LIMIT 20";
        $equipment = $wpdb->get_results($wpdb->prepare($sql, $params));
        
        include GYMFLOW_PLUGIN_PATH . 'core/includes/admin/views/equipment-list.php';
    }

    /**
     * Bookings list page
     */
    private function bookings_list_page() {
        $search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
        $status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
        $booking_type = isset($_GET['booking_type']) ? sanitize_text_field($_GET['booking_type']) : '';
        $date_from = isset($_GET['date_from']) ? sanitize_text_field($_GET['date_from']) : '';
        $date_to = isset($_GET['date_to']) ? sanitize_text_field($_GET['date_to']) : '';
        
        $args = array(
            'limit' => 20,
            'status' => $status,
            'booking_type' => $booking_type,
            'date_from' => $date_from,
            'date_to' => $date_to
        );

        $bookings = GF_Booking::get_all($args);
        $total_bookings = GF_Booking::get_count();
        
        include GYMFLOW_PLUGIN_PATH . 'core/includes/admin/views/bookings-list.php';
    }

    /**
     * Save settings
     */
    private function save_settings($tab) {
        $setting_groups = array(
            'general' => 'gymflow_general_settings',
            'booking' => 'gymflow_booking_settings',
            'membership' => 'gymflow_membership_settings',
            'class' => 'gymflow_class_settings',
            'equipment' => 'gymflow_equipment_settings',
            'notification' => 'gymflow_notification_settings',
            'payment' => 'gymflow_payment_settings'
        );

        if (isset($setting_groups[$tab]) && isset($_POST[$setting_groups[$tab]])) {
            $settings = $_POST[$setting_groups[$tab]];
            
            // Sanitize settings based on type
            $sanitized_settings = $this->sanitize_settings($settings, $tab);
            
            update_option($setting_groups[$tab], $sanitized_settings);
        }
    }

    /**
     * Sanitize settings
     */
    private function sanitize_settings($settings, $tab) {
        $sanitized = array();

        foreach ($settings as $key => $value) {
            switch ($key) {
                case 'studio_email':
                case 'admin_notification_email':
                    $sanitized[$key] = sanitize_email($value);
                    break;
                case 'studio_phone':
                    $sanitized[$key] = sanitize_text_field($value);
                    break;
                case 'studio_address':
                case 'studio_name':
                    $sanitized[$key] = sanitize_text_field($value);
                    break;
                case 'advance_booking_days':
                case 'cancellation_hours':
                case 'default_class_duration':
                case 'default_class_capacity':
                    $sanitized[$key] = intval($value);
                    break;
                case 'allow_waitlist':
                case 'auto_confirm_bookings':
                case 'send_confirmation_emails':
                case 'enable_email_notifications':
                    $sanitized[$key] = (bool) $value;
                    break;
                default:
                    $sanitized[$key] = sanitize_text_field($value);
                    break;
            }
        }

        return $sanitized;
    }

    /**
     * Handle bulk actions
     */
    public function handle_bulk_actions() {
        if (!isset($_POST['action']) || !isset($_POST['bulk_items']) || !wp_verify_nonce($_POST['_wpnonce'], 'bulk_action_nonce')) {
            return;
        }

        $action = sanitize_text_field($_POST['action']);
        $items = array_map('intval', $_POST['bulk_items']);

        switch ($action) {
            case 'delete_members':
                if (current_user_can('delete_gf_members')) {
                    foreach ($items as $member_id) {
                        $member = new GF_Member($member_id);
                        $member->delete();
                    }
                    wp_redirect(admin_url('admin.php?page=gymflow-members&bulk_deleted=' . count($items)));
                    exit;
                }
                break;

            case 'activate_members':
                if (current_user_can('edit_gf_members')) {
                    global $wpdb;
                    $members_table = $wpdb->prefix . 'gf_members';
                    foreach ($items as $member_id) {
                        $wpdb->update($members_table, array('membership_status' => 'active'), array('id' => $member_id));
                    }
                    wp_redirect(admin_url('admin.php?page=gymflow-members&bulk_activated=' . count($items)));
                    exit;
                }
                break;

            case 'confirm_bookings':
                if (current_user_can('edit_gf_bookings')) {
                    foreach ($items as $booking_id) {
                        $booking = new GF_Booking($booking_id);
                        $booking->confirm();
                    }
                    wp_redirect(admin_url('admin.php?page=gymflow-bookings&bulk_confirmed=' . count($items)));
                    exit;
                }
                break;

            case 'cancel_bookings':
                if (current_user_can('edit_gf_bookings')) {
                    foreach ($items as $booking_id) {
                        $booking = new GF_Booking($booking_id);
                        $booking->cancel(__('Bulk cancellation', 'gymflow'));
                    }
                    wp_redirect(admin_url('admin.php?page=gymflow-bookings&bulk_cancelled=' . count($items)));
                    exit;
                }
                break;
        }
    }

    /**
     * Admin notices
     */
    public function admin_notices() {
        // Bulk action success messages
        if (isset($_GET['bulk_deleted'])) {
            printf(
                '<div class="notice notice-success is-dismissible"><p>%s</p></div>',
                sprintf(__('%d items deleted successfully.', 'gymflow'), intval($_GET['bulk_deleted']))
            );
        }

        if (isset($_GET['bulk_activated'])) {
            printf(
                '<div class="notice notice-success is-dismissible"><p>%s</p></div>',
                sprintf(__('%d members activated successfully.', 'gymflow'), intval($_GET['bulk_activated']))
            );
        }

        if (isset($_GET['bulk_confirmed'])) {
            printf(
                '<div class="notice notice-success is-dismissible"><p>%s</p></div>',
                sprintf(__('%d bookings confirmed successfully.', 'gymflow'), intval($_GET['bulk_confirmed']))
            );
        }

        if (isset($_GET['bulk_cancelled'])) {
            printf(
                '<div class="notice notice-success is-dismissible"><p>%s</p></div>',
                sprintf(__('%d bookings cancelled successfully.', 'gymflow'), intval($_GET['bulk_cancelled']))
            );
        }

        // Show deactivation notice if set
        $deactivation_notice = get_option('gymflow_deactivation_notice');
        if ($deactivation_notice && isset($deactivation_notice['expires']) && time() < $deactivation_notice['expires']) {
            printf(
                '<div class="notice notice-%s %s"><p>%s</p></div>',
                esc_attr($deactivation_notice['type']),
                $deactivation_notice['dismissible'] ? 'is-dismissible' : '',
                wp_kses_post($deactivation_notice['message'])
            );
        }
    }

    /**
     * AJAX: Get quick stats
     */
    public function ajax_quick_stats() {
        check_ajax_referer('gymflow_admin', 'nonce');

        if (!current_user_can('manage_gymflow')) {
            wp_die(__('Unauthorized', 'gymflow'));
        }

        $stats = $this->get_dashboard_stats();
        wp_send_json_success($stats);
    }

    /**
     * AJAX: Get recent activity
     */
    public function ajax_recent_activity() {
        check_ajax_referer('gymflow_admin', 'nonce');

        if (!current_user_can('manage_gymflow')) {
            wp_die(__('Unauthorized', 'gymflow'));
        }

        $activity = $this->get_recent_activity();
        wp_send_json_success($activity);
    }

    /**
     * Custom columns for member posts
     */
    public function member_columns($columns) {
        return array(
            'cb' => $columns['cb'],
            'title' => __('Name', 'gymflow'),
            'member_number' => __('Member #', 'gymflow'),
            'email' => __('Email', 'gymflow'),
            'membership_status' => __('Status', 'gymflow'),
            'membership_type' => __('Type', 'gymflow'),
            'date' => __('Joined', 'gymflow')
        );
    }

    /**
     * Custom column content for member posts
     */
    public function member_column_content($column, $post_id) {
        // This would retrieve member data by post meta or custom table
        // Implementation depends on how member data is stored
    }

    /**
     * Custom columns for class posts
     */
    public function class_columns($columns) {
        return array(
            'cb' => $columns['cb'],
            'title' => __('Class Name', 'gymflow'),
            'category' => __('Category', 'gymflow'),
            'difficulty' => __('Difficulty', 'gymflow'),
            'duration' => __('Duration', 'gymflow'),
            'capacity' => __('Capacity', 'gymflow'),
            'instructor' => __('Instructor', 'gymflow'),
            'date' => __('Created', 'gymflow')
        );
    }

    /**
     * Custom column content for class posts
     */
    public function class_column_content($column, $post_id) {
        // Implementation for class column content
    }

    /**
     * Custom columns for trainer posts
     */
    public function trainer_columns($columns) {
        return array(
            'cb' => $columns['cb'],
            'title' => __('Name', 'gymflow'),
            'trainer_number' => __('Trainer #', 'gymflow'),
            'email' => __('Email', 'gymflow'),
            'specialties' => __('Specialties', 'gymflow'),
            'status' => __('Status', 'gymflow'),
            'date' => __('Hired', 'gymflow')
        );
    }

    /**
     * Custom column content for trainer posts
     */
    public function trainer_column_content($column, $post_id) {
        // Implementation for trainer column content
    }

    /**
     * Custom columns for booking posts
     */
    public function booking_columns($columns) {
        return array(
            'cb' => $columns['cb'],
            'title' => __('Booking', 'gymflow'),
            'member' => __('Member', 'gymflow'),
            'booking_type' => __('Type', 'gymflow'),
            'booking_date' => __('Date', 'gymflow'),
            'status' => __('Status', 'gymflow'),
            'payment_status' => __('Payment', 'gymflow'),
            'date' => __('Created', 'gymflow')
        );
    }

    /**
     * Custom column content for booking posts
     */
    public function booking_column_content($column, $post_id) {
        // Implementation for booking column content
    }

    /**
     * Custom columns for equipment posts
     */
    public function equipment_columns($columns) {
        return array(
            'cb' => $columns['cb'],
            'title' => __('Equipment', 'gymflow'),
            'category' => __('Category', 'gymflow'),
            'status' => __('Status', 'gymflow'),
            'condition' => __('Condition', 'gymflow'),
            'location' => __('Location', 'gymflow'),
            'next_maintenance' => __('Next Maintenance', 'gymflow'),
            'date' => __('Added', 'gymflow')
        );
    }

    /**
     * Custom column content for equipment posts
     */
    public function equipment_column_content($column, $post_id) {
        // Implementation for equipment column content
    }
}