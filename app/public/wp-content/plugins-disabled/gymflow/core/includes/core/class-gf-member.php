<?php
/**
 * GymFlow Member Class
 *
 * Handles all member-related functionality including CRUD operations and business logic
 *
 * @package GymFlow
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * GF_Member Class
 *
 * Core member management functionality
 */
class GF_Member {

    /**
     * Member ID
     * @var int
     */
    public $id;

    /**
     * WordPress user ID (if linked)
     * @var int
     */
    public $user_id;

    /**
     * Unique member number
     * @var string
     */
    public $member_number;

    /**
     * Member's first name
     * @var string
     */
    public $first_name;

    /**
     * Member's last name
     * @var string
     */
    public $last_name;

    /**
     * Member's email address
     * @var string
     */
    public $email;

    /**
     * Member's phone number
     * @var string
     */
    public $phone;

    /**
     * Member's date of birth
     * @var string
     */
    public $date_of_birth;

    /**
     * Member's gender
     * @var string
     */
    public $gender;

    /**
     * Emergency contact information
     * @var array
     */
    public $emergency_contact;

    /**
     * Health conditions and notes
     * @var string
     */
    public $health_conditions;

    /**
     * Membership type
     * @var string
     */
    public $membership_type;

    /**
     * Membership status
     * @var string
     */
    public $membership_status;

    /**
     * Membership start date
     * @var string
     */
    public $membership_start_date;

    /**
     * Membership end date
     * @var string
     */
    public $membership_end_date;

    /**
     * Member notes
     * @var string
     */
    public $notes;

    /**
     * Profile photo URL
     * @var string
     */
    public $profile_photo_url;

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
     * @param int $member_id Member ID to load
     */
    public function __construct($member_id = 0) {
        if ($member_id > 0) {
            $this->load($member_id);
        }
    }

    /**
     * Load member data from database
     *
     * @param int $member_id Member ID
     * @return bool True if loaded successfully
     */
    public function load($member_id) {
        global $wpdb;

        $table = $wpdb->prefix . 'gf_members';
        $member = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $member_id));

        if (!$member) {
            return false;
        }

        // Populate object properties
        $this->id = $member->id;
        $this->user_id = $member->user_id;
        $this->member_number = $member->member_number;
        $this->first_name = $member->first_name;
        $this->last_name = $member->last_name;
        $this->email = $member->email;
        $this->phone = $member->phone;
        $this->date_of_birth = $member->date_of_birth;
        $this->gender = $member->gender;
        $this->health_conditions = $member->health_conditions;
        $this->membership_type = $member->membership_type;
        $this->membership_status = $member->membership_status;
        $this->membership_start_date = $member->membership_start_date;
        $this->membership_end_date = $member->membership_end_date;
        $this->notes = $member->notes;
        $this->profile_photo_url = $member->profile_photo_url;
        $this->created_at = $member->created_at;
        $this->updated_at = $member->updated_at;

        // Parse emergency contact JSON
        $this->emergency_contact = array(
            'name' => $member->emergency_contact_name,
            'phone' => $member->emergency_contact_phone
        );

        return true;
    }

    /**
     * Save member to database
     *
     * @return int|false Member ID on success, false on failure
     */
    public function save() {
        global $wpdb;

        $table = $wpdb->prefix . 'gf_members';

        // Validate required fields
        if (empty($this->first_name) || empty($this->last_name) || empty($this->email)) {
            return false;
        }

        // Generate member number if not set
        if (empty($this->member_number)) {
            $this->member_number = $this->generate_member_number();
        }

        $data = array(
            'user_id' => $this->user_id,
            'member_number' => $this->member_number,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'emergency_contact_name' => isset($this->emergency_contact['name']) ? $this->emergency_contact['name'] : '',
            'emergency_contact_phone' => isset($this->emergency_contact['phone']) ? $this->emergency_contact['phone'] : '',
            'health_conditions' => $this->health_conditions,
            'membership_type' => $this->membership_type,
            'membership_status' => $this->membership_status,
            'membership_start_date' => $this->membership_start_date,
            'membership_end_date' => $this->membership_end_date,
            'notes' => $this->notes,
            'profile_photo_url' => $this->profile_photo_url
        );

        $format = array(
            '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', 
            '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'
        );

        if ($this->id > 0) {
            // Update existing member
            $result = $wpdb->update($table, $data, array('id' => $this->id), $format, array('%d'));
            return $result !== false ? $this->id : false;
        } else {
            // Create new member
            $result = $wpdb->insert($table, $data, $format);
            if ($result !== false) {
                $this->id = $wpdb->insert_id;
                return $this->id;
            }
            return false;
        }
    }

    /**
     * Delete member from database
     *
     * @return bool True on success, false on failure
     */
    public function delete() {
        if ($this->id <= 0) {
            return false;
        }

        global $wpdb;

        $table = $wpdb->prefix . 'gf_members';
        $result = $wpdb->delete($table, array('id' => $this->id), array('%d'));

        if ($result !== false) {
            // Also delete related bookings, check-ins, etc.
            $this->delete_related_data();
            return true;
        }

        return false;
    }

    /**
     * Delete related member data
     */
    private function delete_related_data() {
        global $wpdb;

        // Delete bookings
        $bookings_table = $wpdb->prefix . 'gf_bookings';
        $wpdb->delete($bookings_table, array('member_id' => $this->id), array('%d'));

        // Delete check-ins
        $checkins_table = $wpdb->prefix . 'gf_check_ins';
        $wpdb->delete($checkins_table, array('member_id' => $this->id), array('%d'));

        // Delete payments
        $payments_table = $wpdb->prefix . 'gf_payments';
        $wpdb->delete($payments_table, array('member_id' => $this->id), array('%d'));
    }

    /**
     * Generate unique member number
     *
     * @return string Member number
     */
    private function generate_member_number() {
        global $wpdb;

        $table = $wpdb->prefix . 'gf_members';
        $prefix = 'GM'; // GymFlow Member
        $year = date('Y');
        
        // Get the highest number for this year
        $last_number = $wpdb->get_var($wpdb->prepare(
            "SELECT member_number FROM {$table} WHERE member_number LIKE %s ORDER BY member_number DESC LIMIT 1",
            $prefix . $year . '%'
        ));

        if ($last_number) {
            $number = intval(substr($last_number, -4)) + 1;
        } else {
            $number = 1;
        }

        return $prefix . $year . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get member's full name
     *
     * @return string Full name
     */
    public function get_full_name() {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Get member's initials
     *
     * @return string Initials
     */
    public function get_initials() {
        return GF_Utilities::get_initials($this->first_name, $this->last_name);
    }

    /**
     * Get member's age
     *
     * @return int Age in years
     */
    public function get_age() {
        return GF_Utilities::calculate_age($this->date_of_birth);
    }

    /**
     * Check if membership is active
     *
     * @return bool True if active
     */
    public function is_membership_active() {
        if ($this->membership_status !== 'active') {
            return false;
        }

        if (empty($this->membership_end_date)) {
            return true; // No end date means active
        }

        return strtotime($this->membership_end_date) >= strtotime('today');
    }

    /**
     * Check if membership is expired
     *
     * @return bool True if expired
     */
    public function is_membership_expired() {
        if (empty($this->membership_end_date)) {
            return false;
        }

        return strtotime($this->membership_end_date) < strtotime('today');
    }

    /**
     * Get days until membership expires
     *
     * @return int Days until expiration (negative if already expired)
     */
    public function days_until_expiration() {
        if (empty($this->membership_end_date)) {
            return 999; // No expiration
        }

        $today = strtotime('today');
        $expiry = strtotime($this->membership_end_date);
        
        return floor(($expiry - $today) / DAY_IN_SECONDS);
    }

    /**
     * Get member's bookings
     *
     * @param array $args Query arguments
     * @return array Bookings
     */
    public function get_bookings($args = array()) {
        global $wpdb;

        $defaults = array(
            'limit' => 50,
            'status' => 'all',
            'date_from' => '',
            'date_to' => '',
            'order' => 'DESC'
        );

        $args = wp_parse_args($args, $defaults);

        $table = $wpdb->prefix . 'gf_bookings';
        $where = array("member_id = %d");
        $params = array($this->id);

        if ($args['status'] !== 'all') {
            $where[] = "status = %s";
            $params[] = $args['status'];
        }

        if (!empty($args['date_from'])) {
            $where[] = "booking_date >= %s";
            $params[] = $args['date_from'];
        }

        if (!empty($args['date_to'])) {
            $where[] = "booking_date <= %s";
            $params[] = $args['date_to'];
        }

        $sql = "SELECT * FROM {$table} WHERE " . implode(' AND ', $where);
        $sql .= " ORDER BY booking_date " . $args['order'];
        
        if ($args['limit'] > 0) {
            $sql .= " LIMIT " . intval($args['limit']);
        }

        return $wpdb->get_results($wpdb->prepare($sql, $params));
    }

    /**
     * Get member's check-in history
     *
     * @param int $limit Number of records to retrieve
     * @return array Check-ins
     */
    public function get_checkin_history($limit = 30) {
        global $wpdb;

        $table = $wpdb->prefix . 'gf_check_ins';
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} WHERE member_id = %d ORDER BY check_in_time DESC LIMIT %d",
            $this->id,
            $limit
        ));
    }

    /**
     * Get member's payment history
     *
     * @param int $limit Number of records to retrieve
     * @return array Payments
     */
    public function get_payment_history($limit = 50) {
        global $wpdb;

        $table = $wpdb->prefix . 'gf_payments';
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} WHERE member_id = %d ORDER BY payment_date DESC LIMIT %d",
            $this->id,
            $limit
        ));
    }

    /**
     * Record member check-in
     *
     * @param array $data Check-in data
     * @return int|false Check-in ID on success, false on failure
     */
    public function record_checkin($data = array()) {
        global $wpdb;

        $defaults = array(
            'check_in_time' => current_time('mysql'),
            'check_in_method' => 'front_desk',
            'location' => '',
            'staff_id' => get_current_user_id(),
            'notes' => ''
        );

        $data = wp_parse_args($data, $defaults);
        $data['member_id'] = $this->id;

        $table = $wpdb->prefix . 'gf_check_ins';
        $result = $wpdb->insert($table, $data, array('%d', '%s', '%s', '%s', '%d', '%s'));

        return $result !== false ? $wpdb->insert_id : false;
    }

    /**
     * Record member check-out
     *
     * @param int $checkin_id Check-in ID to update
     * @return bool True on success, false on failure
     */
    public function record_checkout($checkin_id) {
        global $wpdb;

        $table = $wpdb->prefix . 'gf_check_ins';
        
        $result = $wpdb->update(
            $table,
            array('check_out_time' => current_time('mysql')),
            array('id' => $checkin_id, 'member_id' => $this->id),
            array('%s'),
            array('%d', '%d')
        );

        return $result !== false;
    }

    /**
     * Get membership status badge HTML
     *
     * @return string HTML badge
     */
    public function get_status_badge() {
        return GF_Utilities::get_status_badge($this->membership_status);
    }

    /**
     * Static method to get all members
     *
     * @param array $args Query arguments
     * @return array Members
     */
    public static function get_all($args = array()) {
        global $wpdb;

        $defaults = array(
            'limit' => 50,
            'offset' => 0,
            'status' => 'all',
            'search' => '',
            'order_by' => 'last_name',
            'order' => 'ASC'
        );

        $args = wp_parse_args($args, $defaults);

        $table = $wpdb->prefix . 'gf_members';
        $where = array('1=1');
        $params = array();

        // Status filter
        if ($args['status'] !== 'all') {
            $where[] = "membership_status = %s";
            $params[] = $args['status'];
        }

        // Search filter
        if (!empty($args['search'])) {
            $search = '%' . $wpdb->esc_like($args['search']) . '%';
            $where[] = "(first_name LIKE %s OR last_name LIKE %s OR email LIKE %s OR member_number LIKE %s)";
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        $sql = "SELECT * FROM {$table} WHERE " . implode(' AND ', $where);
        $sql .= " ORDER BY " . sanitize_sql_orderby($args['order_by'] . ' ' . $args['order']);
        
        if ($args['limit'] > 0) {
            $sql .= " LIMIT " . intval($args['offset']) . ", " . intval($args['limit']);
        }

        return $wpdb->get_results($wpdb->prepare($sql, $params));
    }

    /**
     * Get member count by status
     *
     * @param string $status Membership status
     * @return int Count
     */
    public static function get_count($status = 'all') {
        global $wpdb;

        $table = $wpdb->prefix . 'gf_members';
        
        if ($status === 'all') {
            return $wpdb->get_var("SELECT COUNT(*) FROM {$table}");
        } else {
            return $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$table} WHERE membership_status = %s",
                $status
            ));
        }
    }

    /**
     * Find member by email
     *
     * @param string $email Email address
     * @return GF_Member|false Member object or false if not found
     */
    public static function find_by_email($email) {
        global $wpdb;

        $table = $wpdb->prefix . 'gf_members';
        $member_id = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$table} WHERE email = %s",
            $email
        ));

        if ($member_id) {
            return new self($member_id);
        }

        return false;
    }

    /**
     * Find member by member number
     *
     * @param string $member_number Member number
     * @return GF_Member|false Member object or false if not found
     */
    public static function find_by_member_number($member_number) {
        global $wpdb;

        $table = $wpdb->prefix . 'gf_members';
        $member_id = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$table} WHERE member_number = %s",
            $member_number
        ));

        if ($member_id) {
            return new self($member_id);
        }

        return false;
    }
}