<?php
/**
 * Booking data model and management
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Booking management class
 */
class ERP_Booking {

    /**
     * Booking ID
     */
    private $id;

    /**
     * Booking data
     */
    private $data = array();

    /**
     * Booking items
     */
    private $items = array();

    /**
     * Constructor
     */
    public function __construct($booking_id = 0) {
        if ($booking_id > 0) {
            $this->id = $booking_id;
            $this->load_data();
            $this->load_items();
        }
    }

    /**
     * Load booking data from database
     */
    private function load_data() {
        global $wpdb;
        
        $table = $wpdb->prefix . 'erp_bookings';
        $booking = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE id = %d",
            $this->id
        ));

        if ($booking) {
            $this->data = (array) $booking;
        }
    }

    /**
     * Load booking items
     */
    private function load_items() {
        global $wpdb;
        
        $table = $wpdb->prefix . 'erp_booking_items';
        $this->items = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table WHERE booking_id = %d",
            $this->id
        ));
    }

    /**
     * Get booking property
     */
    public function get($property) {
        return isset($this->data[$property]) ? $this->data[$property] : null;
    }

    /**
     * Set booking property
     */
    public function set($property, $value) {
        $this->data[$property] = $value;
    }

    /**
     * Save booking to database
     */
    public function save() {
        global $wpdb;
        
        $table = $wpdb->prefix . 'erp_bookings';
        
        if ($this->id > 0) {
            // Update existing booking
            $result = $wpdb->update(
                $table,
                $this->data,
                array('id' => $this->id),
                $this->get_format_array(),
                array('%d')
            );
        } else {
            // Generate booking number if not set
            if (empty($this->data['booking_number'])) {
                $this->data['booking_number'] = $this->generate_booking_number();
            }
            
            // Create new booking
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
     * Delete booking
     */
    public function delete() {
        if ($this->id <= 0) {
            return false;
        }

        global $wpdb;
        
        // Delete booking items first
        $items_table = $wpdb->prefix . 'erp_booking_items';
        $wpdb->delete($items_table, array('booking_id' => $this->id), array('%d'));
        
        // Delete booking
        $table = $wpdb->prefix . 'erp_bookings';
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
            '%s', // booking_number
            '%d', // customer_id
            '%s', // status
            '%s', // booking_type
            '%s', // start_date
            '%s', // end_date
            '%s', // start_time
            '%s', // end_time
            '%s', // pickup_method
            '%s', // delivery_address
            '%s', // delivery_date
            '%s', // delivery_time_slot
            '%s', // delivery_instructions
            '%s', // return_method
            '%s', // return_address
            '%s', // return_date
            '%s', // return_time_slot
            '%f', // subtotal
            '%f', // tax_amount
            '%f', // discount_amount
            '%f', // deposit_amount
            '%f', // total_amount
            '%f', // paid_amount
            '%s', // payment_status
            '%s', // payment_method
            '%s', // transaction_id
            '%s', // special_instructions
            '%d', // damage_waiver
            '%f', // damage_waiver_fee
            '%s', // notes
            '%d', // created_by
            '%s', // created_at
            '%s'  // updated_at
        );
    }

    /**
     * Get booking ID
     */
    public function get_id() {
        return $this->id;
    }

    /**
     * Get booking items
     */
    public function get_items() {
        return $this->items;
    }

    /**
     * Add item to booking
     */
    public function add_item($equipment_id, $quantity = 1, $daily_rate = null) {
        global $wpdb;
        
        if ($this->id <= 0) {
            return false;
        }
        
        // Get equipment details if rate not provided
        if ($daily_rate === null) {
            $equipment = new ERP_Equipment($equipment_id);
            $daily_rate = $equipment->get('daily_rate');
        }
        
        // Calculate total days
        $start_date = new DateTime($this->get('start_date'));
        $end_date = new DateTime($this->get('end_date'));
        $total_days = $start_date->diff($end_date)->days + 1;
        
        // Calculate line total
        $line_total = $daily_rate * $quantity * $total_days;
        
        $table = $wpdb->prefix . 'erp_booking_items';
        $result = $wpdb->insert(
            $table,
            array(
                'booking_id' => $this->id,
                'equipment_id' => $equipment_id,
                'quantity' => $quantity,
                'daily_rate' => $daily_rate,
                'total_days' => $total_days,
                'line_total' => $line_total,
                'condition_out' => 'excellent'
            ),
            array('%d', '%d', '%d', '%f', '%d', '%f', '%s')
        );
        
        if ($result) {
            $this->load_items();
            $this->calculate_totals();
        }
        
        return $result !== false;
    }

    /**
     * Remove item from booking
     */
    public function remove_item($item_id) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'erp_booking_items';
        $result = $wpdb->delete(
            $table,
            array('id' => $item_id, 'booking_id' => $this->id),
            array('%d', '%d')
        );
        
        if ($result) {
            $this->load_items();
            $this->calculate_totals();
        }
        
        return $result !== false;
    }

    /**
     * Calculate booking totals
     */
    public function calculate_totals() {
        $subtotal = 0;
        
        foreach ($this->items as $item) {
            $subtotal += floatval($item->line_total);
        }
        
        // Add damage waiver if selected
        $damage_waiver_fee = $this->get('damage_waiver') ? floatval($this->get('damage_waiver_fee')) : 0;
        $subtotal += $damage_waiver_fee;
        
        // Calculate tax (assuming a simple tax rate - can be made configurable)
        $tax_rate = 0.08; // 8% - make this configurable
        $tax_amount = $subtotal * $tax_rate;
        
        // Apply discount
        $discount_amount = floatval($this->get('discount_amount'));
        
        // Calculate total
        $total_amount = $subtotal + $tax_amount - $discount_amount;
        
        // Update booking totals
        $this->set('subtotal', $subtotal);
        $this->set('tax_amount', $tax_amount);
        $this->set('total_amount', $total_amount);
        
        return $this->save();
    }

    /**
     * Generate unique booking number
     */
    private function generate_booking_number() {
        $prefix = 'ERP';
        $year = date('Y');
        $month = date('m');
        
        global $wpdb;
        $table = $wpdb->prefix . 'erp_bookings';
        
        // Get the last booking number for this month
        $last_number = $wpdb->get_var($wpdb->prepare("
            SELECT booking_number FROM $table 
            WHERE booking_number LIKE %s 
            ORDER BY id DESC 
            LIMIT 1
        ", $prefix . $year . $month . '%'));
        
        if ($last_number) {
            $last_sequence = intval(substr($last_number, -4));
            $new_sequence = $last_sequence + 1;
        } else {
            $new_sequence = 1;
        }
        
        return $prefix . $year . $month . str_pad($new_sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get customer
     */
    public function get_customer() {
        $customer_id = $this->get('customer_id');
        return $customer_id ? new ERP_Customer($customer_id) : null;
    }

    /**
     * Check if booking dates overlap with another booking
     */
    public function has_equipment_conflicts() {
        if (empty($this->items)) {
            return false;
        }
        
        global $wpdb;
        
        $bookings_table = $wpdb->prefix . 'erp_bookings';
        $booking_items_table = $wpdb->prefix . 'erp_booking_items';
        
        foreach ($this->items as $item) {
            $conflicts = $wpdb->get_var($wpdb->prepare("
                SELECT COUNT(*)
                FROM $bookings_table b
                INNER JOIN $booking_items_table bi ON b.id = bi.booking_id
                WHERE bi.equipment_id = %d
                AND b.id != %d
                AND b.status NOT IN ('cancelled', 'completed')
                AND (
                    (b.start_date <= %s AND b.end_date >= %s)
                    OR (b.start_date <= %s AND b.end_date >= %s)
                    OR (b.start_date >= %s AND b.end_date <= %s)
                )
            ", 
                $item->equipment_id, 
                $this->id,
                $this->get('start_date'), 
                $this->get('start_date'),
                $this->get('end_date'), 
                $this->get('end_date'),
                $this->get('start_date'), 
                $this->get('end_date')
            ));
            
            if ($conflicts > 0) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Static methods for booking management
     */

    /**
     * Get all bookings
     */
    public static function get_all($args = array()) {
        global $wpdb;
        
        $defaults = array(
            'status' => '',
            'customer_id' => 0,
            'start_date' => '',
            'end_date' => '',
            'limit' => 20,
            'offset' => 0,
            'orderby' => 'created_at',
            'order' => 'DESC'
        );
        
        $args = wp_parse_args($args, $defaults);
        $table = $wpdb->prefix . 'erp_bookings';
        
        $where = array('1=1');
        $where_values = array();
        
        if (!empty($args['status'])) {
            $where[] = 'status = %s';
            $where_values[] = $args['status'];
        }
        
        if ($args['customer_id'] > 0) {
            $where[] = 'customer_id = %d';
            $where_values[] = $args['customer_id'];
        }
        
        if (!empty($args['start_date'])) {
            $where[] = 'start_date >= %s';
            $where_values[] = $args['start_date'];
        }
        
        if (!empty($args['end_date'])) {
            $where[] = 'end_date <= %s';
            $where_values[] = $args['end_date'];
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
     * Get booking by booking number
     */
    public static function get_by_booking_number($booking_number) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'erp_bookings';
        $booking_id = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table WHERE booking_number = %s",
            $booking_number
        ));
        
        return $booking_id ? new self($booking_id) : null;
    }

    /**
     * Get booking statistics
     */
    public static function get_statistics() {
        global $wpdb;
        
        $table = $wpdb->prefix . 'erp_bookings';
        
        $stats = array();
        
        // Total bookings count
        $stats['total'] = $wpdb->get_var("SELECT COUNT(*) FROM $table");
        
        // Pending bookings count
        $stats['pending'] = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE status = %s", 'pending'));
        
        // Confirmed bookings count
        $stats['confirmed'] = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE status = %s", 'confirmed'));
        
        // In progress bookings count
        $stats['in_progress'] = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE status = %s", 'in_progress'));
        
        // Total revenue
        $stats['total_revenue'] = $wpdb->get_var($wpdb->prepare("SELECT SUM(total_amount) FROM $table WHERE payment_status = %s", 'paid'));
        
        // Pending revenue
        $stats['pending_revenue'] = $wpdb->get_var($wpdb->prepare("SELECT SUM(total_amount) FROM $table WHERE payment_status = %s", 'pending'));
        
        return $stats;
    }

    /**
     * Get bookings for calendar
     */
    public static function get_calendar_bookings($start_date, $end_date) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'erp_bookings';
        
        return $wpdb->get_results($wpdb->prepare("
            SELECT id, booking_number, customer_id, status, start_date, end_date, total_amount
            FROM $table 
            WHERE (start_date <= %s AND end_date >= %s)
            AND status NOT IN ('cancelled')
            ORDER BY start_date ASC
        ", $end_date, $start_date));
    }
}