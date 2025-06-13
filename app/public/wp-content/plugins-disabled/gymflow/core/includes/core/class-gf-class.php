<?php
/**
 * GymFlow Class Management Class
 *
 * Handles all fitness class-related functionality including CRUD operations and scheduling
 *
 * @package GymFlow
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * GF_Class Class
 *
 * Core fitness class management functionality
 */
class GF_Class {

    /**
     * Class ID
     * @var int
     */
    public $id;

    /**
     * Class name
     * @var string
     */
    public $name;

    /**
     * Class description
     * @var string
     */
    public $description;

    /**
     * Class category
     * @var string
     */
    public $category;

    /**
     * Duration in minutes
     * @var int
     */
    public $duration;

    /**
     * Maximum capacity
     * @var int
     */
    public $capacity;

    /**
     * Difficulty level
     * @var string
     */
    public $difficulty_level;

    /**
     * Equipment required
     * @var string
     */
    public $equipment_required;

    /**
     * Default instructor ID
     * @var int
     */
    public $instructor_id;

    /**
     * Regular price
     * @var float
     */
    public $price;

    /**
     * Drop-in price
     * @var float
     */
    public $drop_in_price;

    /**
     * Whether class is active
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
     * @param int $class_id Class ID to load
     */
    public function __construct($class_id = 0) {
        if ($class_id > 0) {
            $this->load($class_id);
        }
    }

    /**
     * Load class data from database
     *
     * @param int $class_id Class ID
     * @return bool True if loaded successfully
     */
    public function load($class_id) {
        global $wpdb;

        $table = $wpdb->prefix . 'gf_classes';
        $class = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $class_id));

        if (!$class) {
            return false;
        }

        // Populate object properties
        $this->id = $class->id;
        $this->name = $class->name;
        $this->description = $class->description;
        $this->category = $class->category;
        $this->duration = $class->duration;
        $this->capacity = $class->capacity;
        $this->difficulty_level = $class->difficulty_level;
        $this->equipment_required = $class->equipment_required;
        $this->instructor_id = $class->instructor_id;
        $this->price = $class->price;
        $this->drop_in_price = $class->drop_in_price;
        $this->is_active = $class->is_active;
        $this->created_at = $class->created_at;
        $this->updated_at = $class->updated_at;

        return true;
    }

    /**
     * Save class to database
     *
     * @return int|false Class ID on success, false on failure
     */
    public function save() {
        global $wpdb;

        $table = $wpdb->prefix . 'gf_classes';

        // Validate required fields
        if (empty($this->name)) {
            return false;
        }

        $data = array(
            'name' => $this->name,
            'description' => $this->description,
            'category' => $this->category,
            'duration' => $this->duration,
            'capacity' => $this->capacity,
            'difficulty_level' => $this->difficulty_level,
            'equipment_required' => $this->equipment_required,
            'instructor_id' => $this->instructor_id,
            'price' => $this->price,
            'drop_in_price' => $this->drop_in_price,
            'is_active' => $this->is_active ? 1 : 0
        );

        $format = array('%s', '%s', '%s', '%d', '%d', '%s', '%s', '%d', '%f', '%f', '%d');

        if ($this->id > 0) {
            // Update existing class
            $result = $wpdb->update($table, $data, array('id' => $this->id), $format, array('%d'));
            return $result !== false ? $this->id : false;
        } else {
            // Create new class
            $result = $wpdb->insert($table, $data, $format);
            if ($result !== false) {
                $this->id = $wpdb->insert_id;
                return $this->id;
            }
            return false;
        }
    }

    /**
     * Delete class from database
     *
     * @return bool True on success, false on failure
     */
    public function delete() {
        if ($this->id <= 0) {
            return false;
        }

        global $wpdb;

        $table = $wpdb->prefix . 'gf_classes';
        $result = $wpdb->delete($table, array('id' => $this->id), array('%d'));

        if ($result !== false) {
            // Also delete related schedules and bookings
            $this->delete_related_data();
            return true;
        }

        return false;
    }

    /**
     * Delete related class data
     */
    private function delete_related_data() {
        global $wpdb;

        // Delete class schedules
        $schedules_table = $wpdb->prefix . 'gf_class_schedules';
        $wpdb->delete($schedules_table, array('class_id' => $this->id), array('%d'));

        // Delete related bookings
        $bookings_table = $wpdb->prefix . 'gf_bookings';
        $schedule_ids = $wpdb->get_col($wpdb->prepare(
            "SELECT id FROM {$schedules_table} WHERE class_id = %d",
            $this->id
        ));

        if (!empty($schedule_ids)) {
            $placeholders = implode(',', array_fill(0, count($schedule_ids), '%d'));
            $wpdb->query($wpdb->prepare(
                "DELETE FROM {$bookings_table} WHERE class_schedule_id IN ({$placeholders})",
                ...$schedule_ids
            ));
        }
    }

    /**
     * Get class instructor
     *
     * @return GF_Trainer|false Trainer object or false if not found
     */
    public function get_instructor() {
        if (empty($this->instructor_id)) {
            return false;
        }

        return new GF_Trainer($this->instructor_id);
    }

    /**
     * Get class schedules
     *
     * @param array $args Query arguments
     * @return array Class schedules
     */
    public function get_schedules($args = array()) {
        global $wpdb;

        $defaults = array(
            'date_from' => '',
            'date_to' => '',
            'status' => 'all',
            'limit' => 50,
            'order' => 'ASC'
        );

        $args = wp_parse_args($args, $defaults);

        $table = $wpdb->prefix . 'gf_class_schedules';
        $where = array("class_id = %d");
        $params = array($this->id);

        if (!empty($args['date_from'])) {
            $where[] = "date >= %s";
            $params[] = $args['date_from'];
        }

        if (!empty($args['date_to'])) {
            $where[] = "date <= %s";
            $params[] = $args['date_to'];
        }

        if ($args['status'] !== 'all') {
            $where[] = "status = %s";
            $params[] = $args['status'];
        }

        $sql = "SELECT * FROM {$table} WHERE " . implode(' AND ', $where);
        $sql .= " ORDER BY date " . $args['order'] . ", start_time " . $args['order'];
        
        if ($args['limit'] > 0) {
            $sql .= " LIMIT " . intval($args['limit']);
        }

        return $wpdb->get_results($wpdb->prepare($sql, $params));
    }

    /**
     * Create class schedule
     *
     * @param array $schedule_data Schedule data
     * @return int|false Schedule ID on success, false on failure
     */
    public function create_schedule($schedule_data) {
        global $wpdb;

        $defaults = array(
            'instructor_id' => $this->instructor_id,
            'date' => '',
            'start_time' => '',
            'end_time' => '',
            'room' => '',
            'max_capacity' => $this->capacity,
            'status' => 'scheduled',
            'notes' => ''
        );

        $schedule_data = wp_parse_args($schedule_data, $defaults);
        $schedule_data['class_id'] = $this->id;

        // Validate required fields
        if (empty($schedule_data['date']) || empty($schedule_data['start_time']) || empty($schedule_data['end_time'])) {
            return false;
        }

        // Calculate end time if not provided
        if (empty($schedule_data['end_time']) && !empty($schedule_data['start_time'])) {
            $start_time = strtotime($schedule_data['start_time']);
            $end_time = $start_time + ($this->duration * 60);
            $schedule_data['end_time'] = date('H:i:s', $end_time);
        }

        $table = $wpdb->prefix . 'gf_class_schedules';
        $result = $wpdb->insert($table, $schedule_data, array(
            '%d', '%d', '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s'
        ));

        return $result !== false ? $wpdb->insert_id : false;
    }

    /**
     * Get upcoming class instances
     *
     * @param int $limit Number of instances to retrieve
     * @return array Upcoming schedules
     */
    public function get_upcoming_instances($limit = 10) {
        global $wpdb;

        $table = $wpdb->prefix . 'gf_class_schedules';
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} 
             WHERE class_id = %d AND date >= CURDATE() AND status = 'scheduled'
             ORDER BY date ASC, start_time ASC 
             LIMIT %d",
            $this->id,
            $limit
        ));
    }

    /**
     * Check available spots for a specific schedule
     *
     * @param int $schedule_id Schedule ID
     * @return int Available spots
     */
    public static function get_available_spots($schedule_id) {
        global $wpdb;

        $schedules_table = $wpdb->prefix . 'gf_class_schedules';
        $bookings_table = $wpdb->prefix . 'gf_bookings';

        $schedule = $wpdb->get_row($wpdb->prepare(
            "SELECT max_capacity FROM {$schedules_table} WHERE id = %d",
            $schedule_id
        ));

        if (!$schedule) {
            return 0;
        }

        $booked_spots = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$bookings_table} 
             WHERE class_schedule_id = %d AND status IN ('confirmed', 'pending')",
            $schedule_id
        ));

        return max(0, $schedule->max_capacity - $booked_spots);
    }

    /**
     * Get class statistics
     *
     * @return array Statistics
     */
    public function get_statistics() {
        global $wpdb;

        $schedules_table = $wpdb->prefix . 'gf_class_schedules';
        $bookings_table = $wpdb->prefix . 'gf_bookings';

        $stats = array();

        // Total schedules
        $stats['total_schedules'] = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$schedules_table} WHERE class_id = %d",
            $this->id
        ));

        // Total bookings
        $stats['total_bookings'] = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$bookings_table} b
             INNER JOIN {$schedules_table} s ON b.class_schedule_id = s.id
             WHERE s.class_id = %d",
            $this->id
        ));

        // Average attendance rate
        $avg_attendance = $wpdb->get_var($wpdb->prepare(
            "SELECT AVG(current_bookings / max_capacity * 100) 
             FROM {$schedules_table} 
             WHERE class_id = %d AND max_capacity > 0 AND status = 'completed'",
            $this->id
        ));
        
        $stats['avg_attendance_rate'] = round($avg_attendance, 1);

        // Revenue generated
        $stats['total_revenue'] = $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(b.amount) FROM {$bookings_table} b
             INNER JOIN {$schedules_table} s ON b.class_schedule_id = s.id
             WHERE s.class_id = %d AND b.payment_status = 'paid'",
            $this->id
        ));

        return $stats;
    }

    /**
     * Get difficulty level label
     *
     * @return string Difficulty label
     */
    public function get_difficulty_label() {
        $levels = array(
            'beginner' => __('Beginner', 'gymflow'),
            'intermediate' => __('Intermediate', 'gymflow'),
            'advanced' => __('Advanced', 'gymflow'),
            'all_levels' => __('All Levels', 'gymflow')
        );

        return isset($levels[$this->difficulty_level]) ? $levels[$this->difficulty_level] : $this->difficulty_level;
    }

    /**
     * Get formatted duration
     *
     * @return string Formatted duration
     */
    public function get_formatted_duration() {
        if ($this->duration < 60) {
            return $this->duration . ' ' . __('minutes', 'gymflow');
        } else {
            $hours = floor($this->duration / 60);
            $minutes = $this->duration % 60;
            
            if ($minutes > 0) {
                return $hours . 'h ' . $minutes . 'm';
            } else {
                return $hours . ' ' . ($hours == 1 ? __('hour', 'gymflow') : __('hours', 'gymflow'));
            }
        }
    }

    /**
     * Get formatted price
     *
     * @param string $type Price type ('regular' or 'drop_in')
     * @return string Formatted price
     */
    public function get_formatted_price($type = 'regular') {
        $price = ($type === 'drop_in') ? $this->drop_in_price : $this->price;
        return GF_Utilities::format_currency($price);
    }

    /**
     * Static method to get all classes
     *
     * @param array $args Query arguments
     * @return array Classes
     */
    public static function get_all($args = array()) {
        global $wpdb;

        $defaults = array(
            'limit' => 50,
            'offset' => 0,
            'category' => '',
            'difficulty' => '',
            'active_only' => true,
            'search' => '',
            'order_by' => 'name',
            'order' => 'ASC'
        );

        $args = wp_parse_args($args, $defaults);

        $table = $wpdb->prefix . 'gf_classes';
        $where = array();
        $params = array();

        // Active filter
        if ($args['active_only']) {
            $where[] = "is_active = 1";
        }

        // Category filter
        if (!empty($args['category'])) {
            $where[] = "category = %s";
            $params[] = $args['category'];
        }

        // Difficulty filter
        if (!empty($args['difficulty'])) {
            $where[] = "difficulty_level = %s";
            $params[] = $args['difficulty'];
        }

        // Search filter
        if (!empty($args['search'])) {
            $search = '%' . $wpdb->esc_like($args['search']) . '%';
            $where[] = "(name LIKE %s OR description LIKE %s OR category LIKE %s)";
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
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
     * Get class count
     *
     * @param bool $active_only Count only active classes
     * @return int Count
     */
    public static function get_count($active_only = true) {
        global $wpdb;

        $table = $wpdb->prefix . 'gf_classes';
        
        if ($active_only) {
            return $wpdb->get_var("SELECT COUNT(*) FROM {$table} WHERE is_active = 1");
        } else {
            return $wpdb->get_var("SELECT COUNT(*) FROM {$table}");
        }
    }

    /**
     * Get popular classes
     *
     * @param int $limit Number of classes to retrieve
     * @param string $period Time period ('week', 'month', 'year')
     * @return array Popular classes
     */
    public static function get_popular_classes($limit = 10, $period = 'month') {
        global $wpdb;

        $classes_table = $wpdb->prefix . 'gf_classes';
        $schedules_table = $wpdb->prefix . 'gf_class_schedules';
        $bookings_table = $wpdb->prefix . 'gf_bookings';

        $date_condition = '';
        switch ($period) {
            case 'week':
                $date_condition = "AND b.booking_date >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)";
                break;
            case 'month':
                $date_condition = "AND b.booking_date >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
                break;
            case 'year':
                $date_condition = "AND b.booking_date >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
                break;
        }

        $sql = "
            SELECT c.*, COUNT(b.id) as booking_count
            FROM {$classes_table} c
            INNER JOIN {$schedules_table} s ON c.id = s.class_id
            INNER JOIN {$bookings_table} b ON s.id = b.class_schedule_id
            WHERE c.is_active = 1 
            AND b.status IN ('confirmed', 'completed')
            {$date_condition}
            GROUP BY c.id
            ORDER BY booking_count DESC
            LIMIT %d
        ";

        return $wpdb->get_results($wpdb->prepare($sql, $limit));
    }

    /**
     * Search classes
     *
     * @param string $query Search query
     * @param int $limit Number of results
     * @return array Search results
     */
    public static function search($query, $limit = 20) {
        global $wpdb;

        $table = $wpdb->prefix . 'gf_classes';
        $search = '%' . $wpdb->esc_like($query) . '%';

        return $wpdb->get_results($wpdb->prepare(
            "SELECT id, name, category, difficulty_level 
             FROM {$table} 
             WHERE is_active = 1 
             AND (name LIKE %s OR description LIKE %s OR category LIKE %s)
             ORDER BY name ASC 
             LIMIT %d",
            $search, $search, $search, $limit
        ));
    }

    /**
     * Get classes by category
     *
     * @param string $category Category slug
     * @return array Classes
     */
    public static function get_by_category($category) {
        global $wpdb;

        $table = $wpdb->prefix . 'gf_classes';
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} WHERE category = %s AND is_active = 1 ORDER BY name ASC",
            $category
        ));
    }

    /**
     * Get classes by instructor
     *
     * @param int $instructor_id Instructor ID
     * @return array Classes
     */
    public static function get_by_instructor($instructor_id) {
        global $wpdb;

        $table = $wpdb->prefix . 'gf_classes';
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} WHERE instructor_id = %d AND is_active = 1 ORDER BY name ASC",
            $instructor_id
        ));
    }
}