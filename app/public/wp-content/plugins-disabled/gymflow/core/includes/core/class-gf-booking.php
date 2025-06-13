<?php
/**
 * GymFlow Booking Class
 *
 * Handles all booking-related functionality including CRUD operations and business logic
 *
 * @package GymFlow
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * GF_Booking Class
 *
 * Core booking management functionality
 */
class GF_Booking {

    /**
     * Booking ID
     * @var int
     */
    public $id;

    /**
     * Booking type
     * @var string
     */
    public $booking_type;

    /**
     * Unique booking reference
     * @var string
     */
    public $booking_reference;

    /**
     * Member ID
     * @var int
     */
    public $member_id;

    /**
     * Class schedule ID (for class bookings)
     * @var int
     */
    public $class_schedule_id;

    /**
     * Equipment ID (for equipment bookings)
     * @var int
     */
    public $equipment_id;

    /**
     * Trainer ID (for personal training)
     * @var int
     */
    public $trainer_id;

    /**
     * Booking date
     * @var string
     */
    public $booking_date;

    /**
     * Start time
     * @var string
     */
    public $start_time;

    /**
     * End time
     * @var string
     */
    public $end_time;

    /**
     * Booking status
     * @var string
     */
    public $status;

    /**
     * Payment status
     * @var string
     */
    public $payment_status;

    /**
     * Booking amount
     * @var float
     */
    public $amount;

    /**
     * Payment method
     * @var string
     */
    public $payment_method;

    /**
     * Booking notes
     * @var string
     */
    public $notes;

    /**
     * Booking source
     * @var string
     */
    public $booking_source;

    /**
     * Confirmed timestamp
     * @var string
     */
    public $confirmed_at;

    /**
     * Cancelled timestamp
     * @var string
     */
    public $cancelled_at;

    /**
     * Cancellation reason
     * @var string
     */
    public $cancellation_reason;

    /**
     * Created timestamp
     * @var string
     */
    public $created_at;

    /**
     * Updated timestamp
     * @var string
     */
    public $updated_at;

    /**
     * Constructor
     *
     * @param int $booking_id Booking ID to load
     */
    public function __construct($booking_id = 0) {
        if ($booking_id > 0) {
            $this->load($booking_id);
        }
    }

    /**
     * Load booking data from database
     *
     * @param int $booking_id Booking ID
     * @return bool True if loaded successfully
     */
    public function load($booking_id) {
        global $wpdb;

        $table = $wpdb->prefix . 'gf_bookings';
        $booking = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $booking_id));

        if (!$booking) {
            return false;
        }

        // Populate object properties
        $this->id = $booking->id;
        $this->booking_type = $booking->booking_type;
        $this->booking_reference = $booking->booking_reference;
        $this->member_id = $booking->member_id;
        $this->class_schedule_id = $booking->class_schedule_id;
        $this->equipment_id = $booking->equipment_id;
        $this->trainer_id = $booking->trainer_id;
        $this->booking_date = $booking->booking_date;
        $this->start_time = $booking->start_time;
        $this->end_time = $booking->end_time;
        $this->status = $booking->status;
        $this->payment_status = $booking->payment_status;
        $this->amount = $booking->amount;
        $this->payment_method = $booking->payment_method;
        $this->notes = $booking->notes;
        $this->booking_source = $booking->booking_source;
        $this->confirmed_at = $booking->confirmed_at;
        $this->cancelled_at = $booking->cancelled_at;
        $this->cancellation_reason = $booking->cancellation_reason;
        $this->created_at = $booking->created_at;
        $this->updated_at = $booking->updated_at;

        return true;
    }

    /**
     * Save booking to database
     *
     * @return int|false Booking ID on success, false on failure
     */
    public function save() {
        global $wpdb;

        $table = $wpdb->prefix . 'gf_bookings';

        // Validate required fields
        if (empty($this->member_id) || empty($this->booking_date) || 
            empty($this->start_time) || empty($this->end_time) || empty($this->booking_type)) {
            return false;
        }

        // Generate booking reference if not set
        if (empty($this->booking_reference)) {
            $this->booking_reference = $this->generate_booking_reference();
        }

        // Validate booking type requirements
        if (!$this->validate_booking_type_requirements()) {
            return false;
        }

        $data = array(
            'booking_type' => $this->booking_type,
            'booking_reference' => $this->booking_reference,
            'member_id' => $this->member_id,
            'class_schedule_id' => $this->class_schedule_id,
            'equipment_id' => $this->equipment_id,
            'trainer_id' => $this->trainer_id,
            'booking_date' => $this->booking_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'status' => $this->status ?: 'confirmed',
            'payment_status' => $this->payment_status ?: 'pending',
            'amount' => $this->amount ?: 0,
            'payment_method' => $this->payment_method,
            'notes' => $this->notes,
            'booking_source' => $this->booking_source ?: 'admin',
            'confirmed_at' => $this->confirmed_at,
            'cancelled_at' => $this->cancelled_at,
            'cancellation_reason' => $this->cancellation_reason
        );

        $format = array(
            '%s', '%s', '%d', '%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%f', '%s', '%s', '%s', '%s', '%s', '%s'
        );

        if ($this->id > 0) {
            // Update existing booking
            $result = $wpdb->update($table, $data, array('id' => $this->id), $format, array('%d'));
            return $result !== false ? $this->id : false;
        } else {
            // Create new booking
            $result = $wpdb->insert($table, $data, $format);
            if ($result !== false) {
                $this->id = $wpdb->insert_id;
                $this->update_related_counts();
                return $this->id;
            }
            return false;
        }
    }

    /**
     * Delete booking from database
     *
     * @return bool True on success, false on failure
     */
    public function delete() {
        if ($this->id <= 0) {
            return false;
        }

        global $wpdb;

        $table = $wpdb->prefix . 'gf_bookings';
        $result = $wpdb->delete($table, array('id' => $this->id), array('%d'));

        if ($result !== false) {
            $this->update_related_counts();
            return true;
        }

        return false;
    }

    /**
     * Validate booking type requirements
     *
     * @return bool True if valid
     */
    private function validate_booking_type_requirements() {
        switch ($this->booking_type) {
            case 'class':
                return !empty($this->class_schedule_id);
            case 'equipment':
                return !empty($this->equipment_id);
            case 'personal_training':
                return !empty($this->trainer_id);
            default:
                return false;
        }
    }

    /**
     * Generate unique booking reference
     *
     * @return string Booking reference
     */
    private function generate_booking_reference() {
        global $wpdb;

        $table = $wpdb->prefix . 'gf_bookings';
        $prefix = 'GB'; // GymFlow Booking
        $date = date('Ymd');
        
        // Get the highest number for today
        $last_number = $wpdb->get_var($wpdb->prepare(
            "SELECT booking_reference FROM {$table} WHERE booking_reference LIKE %s ORDER BY booking_reference DESC LIMIT 1",
            $prefix . $date . '%'
        ));

        if ($last_number) {
            $number = intval(substr($last_number, -4)) + 1;
        } else {
            $number = 1;
        }

        return $prefix . $date . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Update related counts (class schedules, etc.)
     */
    private function update_related_counts() {
        if ($this->booking_type === 'class' && !empty($this->class_schedule_id)) {
            $this->update_class_schedule_count();
        }
    }

    /**
     * Update class schedule booking count
     */
    private function update_class_schedule_count() {
        global $wpdb;

        $schedules_table = $wpdb->prefix . 'gf_class_schedules';
        $bookings_table = $wpdb->prefix . 'gf_bookings';

        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$bookings_table} 
             WHERE class_schedule_id = %d AND status IN ('confirmed', 'pending')",
            $this->class_schedule_id
        ));

        $wpdb->update(
            $schedules_table,
            array('current_bookings' => $count),
            array('id' => $this->class_schedule_id),
            array('%d'),
            array('%d')
        );
    }

    /**
     * Confirm booking
     *
     * @return bool True on success
     */
    public function confirm() {
        $this->status = 'confirmed';
        $this->confirmed_at = current_time('mysql');
        
        return $this->save() !== false;
    }

    /**
     * Cancel booking
     *
     * @param string $reason Cancellation reason
     * @return bool True on success
     */
    public function cancel($reason = '') {
        $this->status = 'cancelled';
        $this->cancelled_at = current_time('mysql');
        $this->cancellation_reason = $reason;
        
        $result = $this->save() !== false;
        
        if ($result) {
            $this->update_related_counts();
        }
        
        return $result;
    }

    /**
     * Mark booking as completed
     *
     * @return bool True on success
     */
    public function complete() {
        $this->status = 'completed';
        
        return $this->save() !== false;
    }

    /**
     * Mark booking as no-show
     *
     * @return bool True on success
     */
    public function mark_no_show() {
        $this->status = 'no_show';
        
        return $this->save() !== false;
    }

    /**
     * Get member object
     *
     * @return GF_Member|false Member object or false if not found
     */
    public function get_member() {
        if (empty($this->member_id)) {
            return false;
        }

        return new GF_Member($this->member_id);
    }

    /**
     * Get class object (for class bookings)
     *
     * @return GF_Class|false Class object or false if not found
     */
    public function get_class() {
        if ($this->booking_type !== 'class' || empty($this->class_schedule_id)) {
            return false;
        }

        global $wpdb;

        $schedules_table = $wpdb->prefix . 'gf_class_schedules';
        $class_id = $wpdb->get_var($wpdb->prepare(
            "SELECT class_id FROM {$schedules_table} WHERE id = %d",
            $this->class_schedule_id
        ));

        if ($class_id) {
            return new GF_Class($class_id);
        }

        return false;
    }

    /**
     * Get class schedule (for class bookings)
     *
     * @return object|false Schedule object or false if not found
     */
    public function get_class_schedule() {
        if ($this->booking_type !== 'class' || empty($this->class_schedule_id)) {
            return false;
        }

        global $wpdb;

        $table = $wpdb->prefix . 'gf_class_schedules';
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table} WHERE id = %d",
            $this->class_schedule_id
        ));
    }

    /**
     * Get trainer object (for personal training)
     *
     * @return GF_Trainer|false Trainer object or false if not found
     */
    public function get_trainer() {
        if (empty($this->trainer_id)) {
            return false;
        }

        return new GF_Trainer($this->trainer_id);
    }

    /**
     * Get equipment object (for equipment bookings)
     *
     * @return object|false Equipment object or false if not found
     */
    public function get_equipment() {
        if ($this->booking_type !== 'equipment' || empty($this->equipment_id)) {
            return false;
        }

        global $wpdb;

        $table = $wpdb->prefix . 'gf_equipment';
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table} WHERE id = %d",
            $this->equipment_id
        ));
    }

    /**
     * Get booking details with related information
     *
     * @return array Booking details
     */
    public function get_detailed_info() {
        $details = array(
            'booking' => $this,
            'member' => $this->get_member(),
            'type_specific' => null
        );

        switch ($this->booking_type) {
            case 'class':
                $details['class'] = $this->get_class();
                $details['schedule'] = $this->get_class_schedule();
                break;
            case 'equipment':
                $details['equipment'] = $this->get_equipment();
                break;
            case 'personal_training':
                $details['trainer'] = $this->get_trainer();
                break;
        }

        return $details;
    }

    /**
     * Get booking status badge HTML
     *
     * @return string HTML badge
     */
    public function get_status_badge() {
        $statuses = array(
            'confirmed' => array(
                'label' => __('Confirmed', 'gymflow'),
                'class' => 'status-confirmed'
            ),
            'pending' => array(
                'label' => __('Pending', 'gymflow'),
                'class' => 'status-pending'
            ),
            'cancelled' => array(
                'label' => __('Cancelled', 'gymflow'),
                'class' => 'status-cancelled'
            ),
            'completed' => array(
                'label' => __('Completed', 'gymflow'),
                'class' => 'status-completed'
            ),
            'no_show' => array(
                'label' => __('No Show', 'gymflow'),
                'class' => 'status-no-show'
            )
        );

        $status_info = isset($statuses[$this->status]) ? $statuses[$this->status] : $statuses['pending'];
        
        return sprintf(
            '<span class="gymflow-booking-status %s">%s</span>',
            esc_attr($status_info['class']),
            esc_html($status_info['label'])
        );
    }

    /**
     * Get payment status badge HTML
     *
     * @return string HTML badge
     */
    public function get_payment_status_badge() {
        $statuses = array(
            'paid' => array(
                'label' => __('Paid', 'gymflow'),
                'class' => 'payment-paid'
            ),
            'pending' => array(
                'label' => __('Pending', 'gymflow'),
                'class' => 'payment-pending'
            ),
            'refunded' => array(
                'label' => __('Refunded', 'gymflow'),
                'class' => 'payment-refunded'
            ),
            'partial' => array(
                'label' => __('Partial', 'gymflow'),
                'class' => 'payment-partial'
            )
        );

        $status_info = isset($statuses[$this->payment_status]) ? $statuses[$this->payment_status] : $statuses['pending'];
        
        return sprintf(
            '<span class="gymflow-payment-status %s">%s</span>',
            esc_attr($status_info['class']),
            esc_html($status_info['label'])
        );
    }

    /**
     * Get formatted amount
     *
     * @return string Formatted amount
     */
    public function get_formatted_amount() {
        return GF_Utilities::format_currency($this->amount);
    }

    /**
     * Get formatted date and time
     *
     * @return string Formatted date and time
     */
    public function get_formatted_datetime() {
        $date = GF_Utilities::format_date($this->booking_date);
        $start_time = GF_Utilities::format_time($this->start_time);
        $end_time = GF_Utilities::format_time($this->end_time);
        
        return sprintf(__('%s from %s to %s', 'gymflow'), $date, $start_time, $end_time);
    }

    /**
     * Check if booking can be cancelled
     *
     * @return bool True if can be cancelled
     */
    public function can_be_cancelled() {
        if (in_array($this->status, array('cancelled', 'completed', 'no_show'))) {
            return false;
        }

        // Check cancellation policy (e.g., 24 hours before)
        $cancellation_hours = GF_Utilities::get_option('booking_settings', array())['cancellation_hours'] ?? 24;
        $booking_datetime = strtotime($this->booking_date . ' ' . $this->start_time);
        $now = time();
        
        return ($booking_datetime - $now) > ($cancellation_hours * 3600);
    }

    /**
     * Static method to get all bookings
     *
     * @param array $args Query arguments
     * @return array Bookings
     */
    public static function get_all($args = array()) {
        global $wpdb;

        $defaults = array(
            'limit' => 50,
            'offset' => 0,
            'member_id' => 0,
            'trainer_id' => 0,
            'equipment_id' => 0,
            'booking_type' => '',
            'status' => '',
            'date_from' => '',
            'date_to' => '',
            'order_by' => 'booking_date',
            'order' => 'DESC'
        );

        $args = wp_parse_args($args, $defaults);

        $table = $wpdb->prefix . 'gf_bookings';
        $where = array('1=1');
        $params = array();

        // Member filter
        if ($args['member_id'] > 0) {
            $where[] = "member_id = %d";
            $params[] = $args['member_id'];
        }

        // Trainer filter
        if ($args['trainer_id'] > 0) {
            $where[] = "trainer_id = %d";
            $params[] = $args['trainer_id'];
        }

        // Equipment filter
        if ($args['equipment_id'] > 0) {
            $where[] = "equipment_id = %d";
            $params[] = $args['equipment_id'];
        }

        // Booking type filter
        if (!empty($args['booking_type'])) {
            $where[] = "booking_type = %s";
            $params[] = $args['booking_type'];
        }

        // Status filter
        if (!empty($args['status'])) {
            $where[] = "status = %s";
            $params[] = $args['status'];
        }

        // Date range filter
        if (!empty($args['date_from'])) {
            $where[] = "booking_date >= %s";
            $params[] = $args['date_from'];
        }

        if (!empty($args['date_to'])) {
            $where[] = "booking_date <= %s";
            $params[] = $args['date_to'];
        }

        $sql = "SELECT * FROM {$table} WHERE " . implode(' AND ', $where);
        $sql .= " ORDER BY " . sanitize_sql_orderby($args['order_by'] . ' ' . $args['order']);
        
        if ($args['limit'] > 0) {
            $sql .= " LIMIT " . intval($args['offset']) . ", " . intval($args['limit']);
        }

        return $wpdb->get_results($wpdb->prepare($sql, $params));
    }

    /**
     * Get booking count
     *
     * @param array $filters Filters to apply
     * @return int Count
     */
    public static function get_count($filters = array()) {
        global $wpdb;

        $table = $wpdb->prefix . 'gf_bookings';
        $where = array('1=1');
        $params = array();

        // Apply filters
        if (!empty($filters['status'])) {
            $where[] = "status = %s";
            $params[] = $filters['status'];
        }

        if (!empty($filters['booking_type'])) {
            $where[] = "booking_type = %s";
            $params[] = $filters['booking_type'];
        }

        if (!empty($filters['date_from'])) {
            $where[] = "booking_date >= %s";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $where[] = "booking_date <= %s";
            $params[] = $filters['date_to'];
        }

        $sql = "SELECT COUNT(*) FROM {$table} WHERE " . implode(' AND ', $where);
        
        return $wpdb->get_var($wpdb->prepare($sql, $params));
    }

    /**
     * Find booking by reference
     *
     * @param string $booking_reference Booking reference
     * @return GF_Booking|false Booking object or false if not found
     */
    public static function find_by_reference($booking_reference) {
        global $wpdb;

        $table = $wpdb->prefix . 'gf_bookings';
        $booking_id = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$table} WHERE booking_reference = %s",
            $booking_reference
        ));

        if ($booking_id) {
            return new self($booking_id);
        }

        return false;
    }

    /**
     * Get upcoming bookings
     *
     * @param int $limit Number of bookings to retrieve
     * @return array Upcoming bookings
     */
    public static function get_upcoming($limit = 20) {
        global $wpdb;

        $table = $wpdb->prefix . 'gf_bookings';
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} 
             WHERE booking_date >= CURDATE() AND status IN ('confirmed', 'pending')
             ORDER BY booking_date ASC, start_time ASC 
             LIMIT %d",
            $limit
        ));
    }

    /**
     * Get today's bookings
     *
     * @return array Today's bookings
     */
    public static function get_todays_bookings() {
        global $wpdb;

        $table = $wpdb->prefix . 'gf_bookings';
        
        return $wpdb->get_results(
            "SELECT * FROM {$table} 
             WHERE booking_date = CURDATE() AND status IN ('confirmed', 'pending')
             ORDER BY start_time ASC"
        );
    }
}