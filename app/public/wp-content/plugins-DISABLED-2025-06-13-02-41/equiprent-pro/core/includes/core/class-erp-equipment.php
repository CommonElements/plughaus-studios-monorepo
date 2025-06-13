<?php
/**
 * Equipment data model and management
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Equipment management class
 */
class ERP_Equipment {

    /**
     * Equipment ID
     */
    private $id;

    /**
     * Equipment data
     */
    private $data = array();

    /**
     * Constructor
     */
    public function __construct($equipment_id = 0) {
        if ($equipment_id > 0) {
            $this->id = $equipment_id;
            $this->load_data();
        }
    }

    /**
     * Load equipment data from database
     */
    private function load_data() {
        global $wpdb;
        
        $table = $wpdb->prefix . 'erp_equipment';
        $equipment = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE id = %d",
            $this->id
        ));

        if ($equipment) {
            $this->data = (array) $equipment;
        }
    }

    /**
     * Get equipment property
     */
    public function get($property) {
        return isset($this->data[$property]) ? $this->data[$property] : null;
    }

    /**
     * Set equipment property
     */
    public function set($property, $value) {
        $this->data[$property] = $value;
    }

    /**
     * Save equipment to database
     */
    public function save() {
        global $wpdb;
        
        $table = $wpdb->prefix . 'erp_equipment';
        
        if ($this->id > 0) {
            // Update existing equipment
            $result = $wpdb->update(
                $table,
                $this->data,
                array('id' => $this->id),
                $this->get_format_array(),
                array('%d')
            );
        } else {
            // Create new equipment
            $this->data['created_at'] = current_time('mysql');
            $result = $wpdb->insert(
                $table,
                $this->data,
                $this->get_format_array()
            );
            
            if ($result) {
                $this->id = $wpdb->insert_id;
                $this->data['id'] = $this->id;
            }
        }

        return $result !== false;
    }

    /**
     * Delete equipment
     */
    public function delete() {
        if ($this->id <= 0) {
            return false;
        }

        global $wpdb;
        $table = $wpdb->prefix . 'erp_equipment';
        
        return $wpdb->delete(
            $table,
            array('id' => $this->id),
            array('%d')
        ) !== false;
    }

    /**
     * Get format array for database operations
     */
    private function get_format_array() {
        return array(
            '%s', // name
            '%s', // description
            '%s', // category
            '%s', // brand
            '%s', // model
            '%s', // serial_number
            '%s', // qr_code
            '%s', // status
            '%s', // condition_status
            '%f', // daily_rate
            '%f', // weekly_rate
            '%f', // monthly_rate
            '%f', // deposit_amount
            '%s', // location
            '%f', // weight
            '%s', // dimensions
            '%s', // power_requirements
            '%s', // accessories
            '%s', // maintenance_schedule
            '%s', // last_maintenance_date
            '%s', // next_maintenance_date
            '%s', // purchase_date
            '%f', // purchase_price
            '%f', // depreciation_rate
            '%f', // insurance_value
            '%s', // image_gallery
            '%s', // created_at
            '%s'  // updated_at
        );
    }

    /**
     * Get equipment ID
     */
    public function get_id() {
        return $this->id;
    }

    /**
     * Check if equipment is available for given dates
     */
    public function is_available($start_date, $end_date) {
        global $wpdb;
        
        $bookings_table = $wpdb->prefix . 'erp_bookings';
        $booking_items_table = $wpdb->prefix . 'erp_booking_items';
        
        $conflicts = $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*)
            FROM $bookings_table b
            INNER JOIN $booking_items_table bi ON b.id = bi.booking_id
            WHERE bi.equipment_id = %d
            AND b.status NOT IN ('cancelled', 'completed')
            AND (
                (b.start_date <= %s AND b.end_date >= %s)
                OR (b.start_date <= %s AND b.end_date >= %s)
                OR (b.start_date >= %s AND b.end_date <= %s)
            )
        ", $this->id, $start_date, $start_date, $end_date, $end_date, $start_date, $end_date));

        return $conflicts == 0;
    }

    /**
     * Get equipment utilization rate
     */
    public function get_utilization_rate($days = 30) {
        global $wpdb;
        
        $bookings_table = $wpdb->prefix . 'erp_bookings';
        $booking_items_table = $wpdb->prefix . 'erp_booking_items';
        
        $start_date = date('Y-m-d', strtotime("-{$days} days"));
        $end_date = date('Y-m-d');
        
        $booked_days = $wpdb->get_var($wpdb->prepare("
            SELECT SUM(DATEDIFF(LEAST(b.end_date, %s), GREATEST(b.start_date, %s)) + 1)
            FROM $bookings_table b
            INNER JOIN $booking_items_table bi ON b.id = bi.booking_id
            WHERE bi.equipment_id = %d
            AND b.status IN ('confirmed', 'in_progress', 'completed')
            AND b.start_date <= %s
            AND b.end_date >= %s
        ", $end_date, $start_date, $this->id, $end_date, $start_date));

        return $booked_days ? ($booked_days / $days) * 100 : 0;
    }

    /**
     * Static methods for equipment management
     */

    /**
     * Get all equipment
     */
    public static function get_all($args = array()) {
        global $wpdb;
        
        $defaults = array(
            'status' => '',
            'category' => '',
            'location' => '',
            'limit' => 20,
            'offset' => 0,
            'orderby' => 'name',
            'order' => 'ASC'
        );
        
        $args = wp_parse_args($args, $defaults);
        $table = $wpdb->prefix . 'erp_equipment';
        
        $where = array('1=1');
        $where_values = array();
        
        if (!empty($args['status'])) {
            $where[] = 'status = %s';
            $where_values[] = $args['status'];
        }
        
        if (!empty($args['category'])) {
            $where[] = 'category = %s';
            $where_values[] = $args['category'];
        }
        
        if (!empty($args['location'])) {
            $where[] = 'location = %s';
            $where_values[] = $args['location'];
        }
        
        $where_clause = implode(' AND ', $where);
        $order_clause = sprintf('ORDER BY %s %s', $args['orderby'], $args['order']);
        $limit_clause = sprintf('LIMIT %d OFFSET %d', $args['limit'], $args['offset']);
        
        $query = "SELECT * FROM $table WHERE $where_clause $order_clause $limit_clause";
        
        if (!empty($where_values)) {
            $results = $wpdb->get_results($wpdb->prepare($query, ...$where_values));
        } else {
            $results = $wpdb->get_results($query);
        }
        
        return $results ? $results : array();
    }

    /**
     * Get equipment by status
     */
    public static function get_by_status($status) {
        return self::get_all(array('status' => $status));
    }

    /**
     * Get equipment by category
     */
    public static function get_by_category($category) {
        return self::get_all(array('category' => $category));
    }

    /**
     * Search equipment
     */
    public static function search($search_term, $args = array()) {
        global $wpdb;
        
        $defaults = array(
            'limit' => 20,
            'offset' => 0
        );
        
        $args = wp_parse_args($args, $defaults);
        $table = $wpdb->prefix . 'erp_equipment';
        
        $query = $wpdb->prepare("
            SELECT * FROM $table 
            WHERE (name LIKE %s 
                OR description LIKE %s 
                OR brand LIKE %s 
                OR model LIKE %s 
                OR serial_number LIKE %s)
            ORDER BY name ASC
            LIMIT %d OFFSET %d
        ", 
            '%' . $wpdb->esc_like($search_term) . '%',
            '%' . $wpdb->esc_like($search_term) . '%',
            '%' . $wpdb->esc_like($search_term) . '%',
            '%' . $wpdb->esc_like($search_term) . '%',
            '%' . $wpdb->esc_like($search_term) . '%',
            $args['limit'],
            $args['offset']
        );
        
        $results = $wpdb->get_results($query);
        return $results ? $results : array();
    }

    /**
     * Get equipment statistics
     */
    public static function get_statistics() {
        global $wpdb;
        
        $table = $wpdb->prefix . 'erp_equipment';
        
        $stats = array();
        
        // Total equipment count
        $stats['total'] = $wpdb->get_var("SELECT COUNT(*) FROM $table");
        
        // Available equipment count
        $stats['available'] = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE status = %s", 'available'));
        
        // Rented equipment count
        $stats['rented'] = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE status = %s", 'rented'));
        
        // Maintenance equipment count
        $stats['maintenance'] = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE status = %s", 'maintenance'));
        
        // Total value
        $stats['total_value'] = $wpdb->get_var("SELECT SUM(purchase_price) FROM $table WHERE purchase_price IS NOT NULL");
        
        return $stats;
    }

    /**
     * Generate QR code for equipment
     */
    public function generate_qr_code() {
        if ($this->id <= 0) {
            return false;
        }
        
        $qr_code = 'ERP-' . str_pad($this->id, 6, '0', STR_PAD_LEFT) . '-' . wp_generate_password(4, false);
        $this->set('qr_code', $qr_code);
        
        return $this->save();
    }

    /**
     * Get equipment by QR code
     */
    public static function get_by_qr_code($qr_code) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'erp_equipment';
        $equipment_id = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table WHERE qr_code = %s",
            $qr_code
        ));
        
        return $equipment_id ? new self($equipment_id) : null;
    }
}