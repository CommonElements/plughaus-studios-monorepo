<?php
/**
 * GymFlow Trainer Class
 *
 * Handles all trainer-related functionality including CRUD operations and scheduling
 *
 * @package GymFlow
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * GF_Trainer Class
 *
 * Core trainer management functionality
 */
class GF_Trainer {

    /**
     * Trainer ID
     * @var int
     */
    public $id;

    /**
     * WordPress user ID (if linked)
     * @var int
     */
    public $user_id;

    /**
     * Unique trainer number
     * @var string
     */
    public $trainer_number;

    /**
     * Trainer's first name
     * @var string
     */
    public $first_name;

    /**
     * Trainer's last name
     * @var string
     */
    public $last_name;

    /**
     * Trainer's email address
     * @var string
     */
    public $email;

    /**
     * Trainer's phone number
     * @var string
     */
    public $phone;

    /**
     * Trainer's bio
     * @var string
     */
    public $bio;

    /**
     * Trainer's specialties
     * @var string
     */
    public $specialties;

    /**
     * Trainer's certifications
     * @var string
     */
    public $certifications;

    /**
     * Hire date
     * @var string
     */
    public $hire_date;

    /**
     * Hourly rate
     * @var float
     */
    public $hourly_rate;

    /**
     * Commission rate
     * @var float
     */
    public $commission_rate;

    /**
     * Profile photo URL
     * @var string
     */
    public $profile_photo_url;

    /**
     * Whether trainer is active
     * @var bool
     */
    public $is_active;

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
     * @param int $trainer_id Trainer ID to load
     */
    public function __construct($trainer_id = 0) {
        if ($trainer_id > 0) {
            $this->load($trainer_id);
        }
    }

    /**
     * Load trainer data from database
     *
     * @param int $trainer_id Trainer ID
     * @return bool True if loaded successfully
     */
    public function load($trainer_id) {
        global $wpdb;

        $table = $wpdb->prefix . 'gf_trainers';
        $trainer = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $trainer_id));

        if (!$trainer) {
            return false;
        }

        // Populate object properties
        $this->id = $trainer->id;
        $this->user_id = $trainer->user_id;
        $this->trainer_number = $trainer->trainer_number;
        $this->first_name = $trainer->first_name;
        $this->last_name = $trainer->last_name;
        $this->email = $trainer->email;
        $this->phone = $trainer->phone;
        $this->bio = $trainer->bio;
        $this->specialties = $trainer->specialties;
        $this->certifications = $trainer->certifications;
        $this->hire_date = $trainer->hire_date;
        $this->hourly_rate = $trainer->hourly_rate;
        $this->commission_rate = $trainer->commission_rate;
        $this->profile_photo_url = $trainer->profile_photo_url;
        $this->is_active = $trainer->is_active;
        $this->created_at = $trainer->created_at;
        $this->updated_at = $trainer->updated_at;

        return true;
    }

    /**
     * Save trainer to database
     *
     * @return int|false Trainer ID on success, false on failure
     */
    public function save() {
        global $wpdb;

        $table = $wpdb->prefix . 'gf_trainers';

        // Validate required fields
        if (empty($this->first_name) || empty($this->last_name) || empty($this->email)) {
            return false;
        }

        // Generate trainer number if not set
        if (empty($this->trainer_number)) {
            $this->trainer_number = $this->generate_trainer_number();
        }

        $data = array(
            'user_id' => $this->user_id,
            'trainer_number' => $this->trainer_number,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'bio' => $this->bio,
            'specialties' => $this->specialties,
            'certifications' => $this->certifications,
            'hire_date' => $this->hire_date,
            'hourly_rate' => $this->hourly_rate,
            'commission_rate' => $this->commission_rate,
            'profile_photo_url' => $this->profile_photo_url,
            'is_active' => $this->is_active ? 1 : 0
        );

        $format = array(
            '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%f', '%f', '%s', '%d'
        );

        if ($this->id > 0) {
            // Update existing trainer
            $result = $wpdb->update($table, $data, array('id' => $this->id), $format, array('%d'));
            return $result !== false ? $this->id : false;
        } else {
            // Create new trainer
            $result = $wpdb->insert($table, $data, $format);
            if ($result !== false) {
                $this->id = $wpdb->insert_id;
                return $this->id;
            }
            return false;
        }
    }

    /**
     * Delete trainer from database
     *
     * @return bool True on success, false on failure
     */
    public function delete() {
        if ($this->id <= 0) {
            return false;
        }

        global $wpdb;

        $table = $wpdb->prefix . 'gf_trainers';
        $result = $wpdb->delete($table, array('id' => $this->id), array('%d'));

        if ($result !== false) {
            // Update related records to remove trainer assignment
            $this->cleanup_related_data();
            return true;
        }

        return false;
    }

    /**
     * Clean up related trainer data (set trainer_id to NULL)
     */
    private function cleanup_related_data() {
        global $wpdb;

        // Update classes to remove trainer assignment
        $classes_table = $wpdb->prefix . 'gf_classes';
        $wpdb->update($classes_table, array('instructor_id' => null), array('instructor_id' => $this->id), array('%s'), array('%d'));

        // Update class schedules to remove trainer assignment
        $schedules_table = $wpdb->prefix . 'gf_class_schedules';
        $wpdb->update($schedules_table, array('instructor_id' => null), array('instructor_id' => $this->id), array('%s'), array('%d'));

        // Update bookings to remove trainer assignment
        $bookings_table = $wpdb->prefix . 'gf_bookings';
        $wpdb->update($bookings_table, array('trainer_id' => null), array('trainer_id' => $this->id), array('%s'), array('%d'));
    }

    /**
     * Generate unique trainer number
     *
     * @return string Trainer number
     */
    private function generate_trainer_number() {
        global $wpdb;

        $table = $wpdb->prefix . 'gf_trainers';
        $prefix = 'GT'; // GymFlow Trainer
        $year = date('Y');
        
        // Get the highest number for this year
        $last_number = $wpdb->get_var($wpdb->prepare(
            "SELECT trainer_number FROM {$table} WHERE trainer_number LIKE %s ORDER BY trainer_number DESC LIMIT 1",
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
     * Get trainer's full name
     *
     * @return string Full name
     */
    public function get_full_name() {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Get trainer's initials
     *
     * @return string Initials
     */
    public function get_initials() {
        return GF_Utilities::get_initials($this->first_name, $this->last_name);
    }

    /**
     * Get years of experience
     *
     * @return int Years since hire date
     */
    public function get_years_experience() {
        if (empty($this->hire_date)) {
            return 0;
        }

        $hire_date = new DateTime($this->hire_date);
        $today = new DateTime();
        
        return $today->diff($hire_date)->y;
    }

    /**
     * Get trainer's classes
     *
     * @param bool $active_only Get only active classes
     * @return array Classes
     */
    public function get_classes($active_only = true) {
        global $wpdb;

        $table = $wpdb->prefix . 'gf_classes';
        $where = "instructor_id = %d";
        $params = array($this->id);

        if ($active_only) {
            $where .= " AND is_active = 1";
        }

        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} WHERE {$where} ORDER BY name ASC",
            ...$params
        ));
    }

    /**
     * Get trainer's schedule
     *
     * @param array $args Query arguments
     * @return array Schedule entries
     */
    public function get_schedule($args = array()) {
        global $wpdb;

        $defaults = array(
            'date_from' => date('Y-m-d'),
            'date_to' => date('Y-m-d', strtotime('+30 days')),
            'status' => 'scheduled',
            'limit' => 100
        );

        $args = wp_parse_args($args, $defaults);

        $schedules_table = $wpdb->prefix . 'gf_class_schedules';
        $classes_table = $wpdb->prefix . 'gf_classes';
        
        $where = array("s.instructor_id = %d");
        $params = array($this->id);

        if (!empty($args['date_from'])) {
            $where[] = "s.date >= %s";
            $params[] = $args['date_from'];
        }

        if (!empty($args['date_to'])) {
            $where[] = "s.date <= %s";
            $params[] = $args['date_to'];
        }

        if ($args['status'] !== 'all') {
            $where[] = "s.status = %s";
            $params[] = $args['status'];
        }

        $sql = "
            SELECT s.*, c.name as class_name, c.duration
            FROM {$schedules_table} s
            INNER JOIN {$classes_table} c ON s.class_id = c.id
            WHERE " . implode(' AND ', $where) . "
            ORDER BY s.date ASC, s.start_time ASC
        ";
        
        if ($args['limit'] > 0) {
            $sql .= " LIMIT " . intval($args['limit']);
        }

        return $wpdb->get_results($wpdb->prepare($sql, $params));
    }

    /**
     * Get trainer's personal training bookings
     *
     * @param array $args Query arguments
     * @return array Bookings
     */
    public function get_personal_training_bookings($args = array()) {
        global $wpdb;

        $defaults = array(
            'date_from' => '',
            'date_to' => '',
            'status' => 'all',
            'limit' => 50
        );

        $args = wp_parse_args($args, $defaults);

        $bookings_table = $wpdb->prefix . 'gf_bookings';
        $members_table = $wpdb->prefix . 'gf_members';
        
        $where = array("b.trainer_id = %d", "b.booking_type = 'personal_training'");
        $params = array($this->id);

        if (!empty($args['date_from'])) {
            $where[] = "b.booking_date >= %s";
            $params[] = $args['date_from'];
        }

        if (!empty($args['date_to'])) {
            $where[] = "b.booking_date <= %s";
            $params[] = $args['date_to'];
        }

        if ($args['status'] !== 'all') {
            $where[] = "b.status = %s";
            $params[] = $args['status'];
        }

        $sql = "
            SELECT b.*, CONCAT(m.first_name, ' ', m.last_name) as member_name, m.email as member_email
            FROM {$bookings_table} b
            INNER JOIN {$members_table} m ON b.member_id = m.id
            WHERE " . implode(' AND ', $where) . "
            ORDER BY b.booking_date DESC, b.start_time DESC
        ";
        
        if ($args['limit'] > 0) {
            $sql .= " LIMIT " . intval($args['limit']);
        }

        return $wpdb->get_results($wpdb->prepare($sql, $params));
    }

    /**
     * Check trainer availability
     *
     * @param string $date Date to check
     * @param string $start_time Start time
     * @param string $end_time End time
     * @param int $exclude_booking_id Booking to exclude from check
     * @return bool True if available
     */
    public function is_available($date, $start_time, $end_time, $exclude_booking_id = 0) {
        global $wpdb;

        // Check class schedules
        $schedules_table = $wpdb->prefix . 'gf_class_schedules';
        $schedule_conflict = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$schedules_table}
             WHERE instructor_id = %d AND date = %s AND status = 'scheduled'
             AND (
                 (start_time <= %s AND end_time > %s) OR
                 (start_time < %s AND end_time >= %s) OR
                 (start_time >= %s AND end_time <= %s)
             )",
            $this->id, $date,
            $start_time, $start_time,
            $end_time, $end_time,
            $start_time, $end_time
        ));

        if ($schedule_conflict > 0) {
            return false;
        }

        // Check personal training bookings
        $bookings_table = $wpdb->prefix . 'gf_bookings';
        $booking_conflict = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$bookings_table}
             WHERE trainer_id = %d AND booking_date = %s AND id != %d
             AND status IN ('confirmed', 'pending')
             AND (
                 (start_time <= %s AND end_time > %s) OR
                 (start_time < %s AND end_time >= %s) OR
                 (start_time >= %s AND end_time <= %s)
             )",
            $this->id, $date, $exclude_booking_id,
            $start_time, $start_time,
            $end_time, $end_time,
            $start_time, $end_time
        ));

        return $booking_conflict == 0;
    }

    /**
     * Get trainer statistics
     *
     * @param string $period Time period ('week', 'month', 'year')
     * @return array Statistics
     */
    public function get_statistics($period = 'month') {
        global $wpdb;

        $date_condition = '';
        switch ($period) {
            case 'week':
                $date_condition = "AND booking_date >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)";
                break;
            case 'month':
                $date_condition = "AND booking_date >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
                break;
            case 'year':
                $date_condition = "AND booking_date >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
                break;
        }

        $bookings_table = $wpdb->prefix . 'gf_bookings';
        $schedules_table = $wpdb->prefix . 'gf_class_schedules';

        $stats = array();

        // Personal training sessions
        $stats['personal_training_sessions'] = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$bookings_table}
             WHERE trainer_id = %d AND booking_type = 'personal_training'
             AND status = 'completed' {$date_condition}",
            $this->id
        ));

        // Class sessions taught
        $stats['classes_taught'] = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$schedules_table}
             WHERE instructor_id = %d AND status = 'completed' {$date_condition}",
            $this->id
        ));

        // Revenue from personal training
        $stats['personal_training_revenue'] = $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(amount) FROM {$bookings_table}
             WHERE trainer_id = %d AND booking_type = 'personal_training'
             AND payment_status = 'paid' {$date_condition}",
            $this->id
        ));

        // Calculate commission earned
        $stats['commission_earned'] = ($stats['personal_training_revenue'] * $this->commission_rate) / 100;

        return $stats;
    }

    /**
     * Get specialties as array
     *
     * @return array Specialties
     */
    public function get_specialties_array() {
        if (empty($this->specialties)) {
            return array();
        }

        return array_map('trim', explode(',', $this->specialties));
    }

    /**
     * Get certifications as array
     *
     * @return array Certifications
     */
    public function get_certifications_array() {
        if (empty($this->certifications)) {
            return array();
        }

        return array_map('trim', explode(',', $this->certifications));
    }

    /**
     * Get formatted hourly rate
     *
     * @return string Formatted rate
     */
    public function get_formatted_hourly_rate() {
        return GF_Utilities::format_currency($this->hourly_rate);
    }

    /**
     * Static method to get all trainers
     *
     * @param array $args Query arguments
     * @return array Trainers
     */
    public static function get_all($args = array()) {
        global $wpdb;

        $defaults = array(
            'limit' => 50,
            'offset' => 0,
            'active_only' => true,
            'search' => '',
            'specialty' => '',
            'order_by' => 'last_name',
            'order' => 'ASC'
        );

        $args = wp_parse_args($args, $defaults);

        $table = $wpdb->prefix . 'gf_trainers';
        $where = array();
        $params = array();

        // Active filter
        if ($args['active_only']) {
            $where[] = "is_active = 1";
        }

        // Search filter
        if (!empty($args['search'])) {
            $search = '%' . $wpdb->esc_like($args['search']) . '%';
            $where[] = "(first_name LIKE %s OR last_name LIKE %s OR email LIKE %s OR trainer_number LIKE %s)";
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        // Specialty filter
        if (!empty($args['specialty'])) {
            $where[] = "specialties LIKE %s";
            $params[] = '%' . $wpdb->esc_like($args['specialty']) . '%';
        }

        $where_clause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $sql = "SELECT * FROM {$table} {$where_clause}";
        $sql .= " ORDER BY " . sanitize_sql_orderby($args['order_by'] . ' ' . $args['order']);
        
        if ($args['limit'] > 0) {
            $sql .= " LIMIT " . intval($args['offset']) . ", " . intval($args['limit']);
        }

        return $wpdb->get_results($wpdb->prepare($sql, $params));
    }

    /**
     * Get trainer count
     *
     * @param bool $active_only Count only active trainers
     * @return int Count
     */
    public static function get_count($active_only = true) {
        global $wpdb;

        $table = $wpdb->prefix . 'gf_trainers';
        
        if ($active_only) {
            return $wpdb->get_var("SELECT COUNT(*) FROM {$table} WHERE is_active = 1");
        } else {
            return $wpdb->get_var("SELECT COUNT(*) FROM {$table}");
        }
    }

    /**
     * Find trainer by email
     *
     * @param string $email Email address
     * @return GF_Trainer|false Trainer object or false if not found
     */
    public static function find_by_email($email) {
        global $wpdb;

        $table = $wpdb->prefix . 'gf_trainers';
        $trainer_id = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$table} WHERE email = %s",
            $email
        ));

        if ($trainer_id) {
            return new self($trainer_id);
        }

        return false;
    }

    /**
     * Find trainer by trainer number
     *
     * @param string $trainer_number Trainer number
     * @return GF_Trainer|false Trainer object or false if not found
     */
    public static function find_by_trainer_number($trainer_number) {
        global $wpdb;

        $table = $wpdb->prefix . 'gf_trainers';
        $trainer_id = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$table} WHERE trainer_number = %s",
            $trainer_number
        ));

        if ($trainer_id) {
            return new self($trainer_id);
        }

        return false;
    }

    /**
     * Search trainers
     *
     * @param string $query Search query
     * @param int $limit Number of results
     * @return array Search results
     */
    public static function search($query, $limit = 20) {
        global $wpdb;

        $table = $wpdb->prefix . 'gf_trainers';
        $search = '%' . $wpdb->esc_like($query) . '%';

        return $wpdb->get_results($wpdb->prepare(
            "SELECT id, first_name, last_name, email, specialties 
             FROM {$table} 
             WHERE is_active = 1 
             AND (first_name LIKE %s OR last_name LIKE %s OR email LIKE %s OR specialties LIKE %s)
             ORDER BY last_name ASC, first_name ASC 
             LIMIT %d",
            $search, $search, $search, $search, $limit
        ));
    }

    /**
     * Get available trainers for a specific time slot
     *
     * @param string $date Date
     * @param string $start_time Start time
     * @param string $end_time End time
     * @return array Available trainers
     */
    public static function get_available_trainers($date, $start_time, $end_time) {
        $all_trainers = self::get_all();
        $available_trainers = array();

        foreach ($all_trainers as $trainer_data) {
            $trainer = new self($trainer_data->id);
            if ($trainer->is_available($date, $start_time, $end_time)) {
                $available_trainers[] = $trainer_data;
            }
        }

        return $available_trainers;
    }
}