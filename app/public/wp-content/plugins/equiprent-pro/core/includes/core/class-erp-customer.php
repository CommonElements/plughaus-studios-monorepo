<?php
/**
 * Customer data model and management
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Customer management class
 */
class ERP_Customer {

    /**
     * Customer ID
     */
    private $id;

    /**
     * Customer data
     */
    private $data = array();

    /**
     * Constructor
     */
    public function __construct($customer_id = 0) {
        if ($customer_id > 0) {
            $this->id = $customer_id;
            $this->load_data();
        }
    }

    /**
     * Load customer data from database
     */
    private function load_data() {
        global $wpdb;
        
        $table = $wpdb->prefix . 'erp_customers';
        $customer = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE id = %d",
            $this->id
        ));

        if ($customer) {
            $this->data = (array) $customer;
        }
    }

    /**
     * Get customer property
     */
    public function get($property) {
        return isset($this->data[$property]) ? $this->data[$property] : null;
    }

    /**
     * Set customer property
     */
    public function set($property, $value) {
        $this->data[$property] = $value;
    }

    /**
     * Save customer to database
     */
    public function save() {
        global $wpdb;
        
        $table = $wpdb->prefix . 'erp_customers';
        
        if ($this->id > 0) {
            // Update existing customer
            $result = $wpdb->update(
                $table,
                $this->data,
                array('id' => $this->id),
                $this->get_format_array(),
                array('%d')
            );
        } else {
            // Create new customer
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
     * Delete customer
     */
    public function delete() {
        if ($this->id <= 0) {
            return false;
        }

        global $wpdb;
        $table = $wpdb->prefix . 'erp_customers';
        
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
            '%d', // user_id
            '%s', // company_name
            '%s', // contact_first_name
            '%s', // contact_last_name
            '%s', // email
            '%s', // phone
            '%s', // mobile
            '%s', // address_line_1
            '%s', // address_line_2
            '%s', // city
            '%s', // state
            '%s', // postal_code
            '%s', // country
            '%s', // delivery_instructions
            '%s', // customer_type
            '%f', // credit_limit
            '%f', // current_balance
            '%s', // payment_terms
            '%d', // tax_exempt
            '%s', // tax_id
            '%s', // license_number
            '%s', // insurance_certificate
            '%s', // emergency_contact_name
            '%s', // emergency_contact_phone
            '%s', // notes
            '%s', // status
            '%s', // created_at
            '%s'  // updated_at
        );
    }

    /**
     * Get customer ID
     */
    public function get_id() {
        return $this->id;
    }

    /**
     * Get customer full name
     */
    public function get_full_name() {
        return trim($this->get('contact_first_name') . ' ' . $this->get('contact_last_name'));
    }

    /**
     * Get customer display name (company or full name)
     */
    public function get_display_name() {
        $company = $this->get('company_name');
        return !empty($company) ? $company : $this->get_full_name();
    }

    /**
     * Get customer full address
     */
    public function get_full_address() {
        $address_parts = array_filter(array(
            $this->get('address_line_1'),
            $this->get('address_line_2'),
            $this->get('city'),
            $this->get('state'),
            $this->get('postal_code'),
            $this->get('country')
        ));
        
        return implode(', ', $address_parts);
    }

    /**
     * Get customer rental history
     */
    public function get_rental_history($limit = 10) {
        global $wpdb;
        
        $bookings_table = $wpdb->prefix . 'erp_bookings';
        
        return $wpdb->get_results($wpdb->prepare("
            SELECT * FROM $bookings_table 
            WHERE customer_id = %d 
            ORDER BY created_at DESC 
            LIMIT %d
        ", $this->id, $limit));
    }

    /**
     * Get customer total bookings count
     */
    public function get_total_bookings() {
        global $wpdb;
        
        $bookings_table = $wpdb->prefix . 'erp_bookings';
        
        return $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*) FROM $bookings_table 
            WHERE customer_id = %d
        ", $this->id));
    }

    /**
     * Get customer total spent
     */
    public function get_total_spent() {
        global $wpdb;
        
        $bookings_table = $wpdb->prefix . 'erp_bookings';
        
        return $wpdb->get_var($wpdb->prepare("
            SELECT SUM(total_amount) FROM $bookings_table 
            WHERE customer_id = %d 
            AND payment_status = 'paid'
        ", $this->id));
    }

    /**
     * Get customer lifetime value
     */
    public function get_lifetime_value() {
        return $this->get_total_spent();
    }

    /**
     * Check if customer has overdue balance
     */
    public function has_overdue_balance() {
        return $this->get('current_balance') > 0;
    }

    /**
     * Update customer balance
     */
    public function update_balance($amount) {
        $current_balance = floatval($this->get('current_balance'));
        $new_balance = $current_balance + $amount;
        $this->set('current_balance', $new_balance);
        return $this->save();
    }

    /**
     * Static methods for customer management
     */

    /**
     * Get all customers
     */
    public static function get_all($args = array()) {
        global $wpdb;
        
        $defaults = array(
            'status' => '',
            'customer_type' => '',
            'limit' => 20,
            'offset' => 0,
            'orderby' => 'contact_last_name',
            'order' => 'ASC'
        );
        
        $args = wp_parse_args($args, $defaults);
        $table = $wpdb->prefix . 'erp_customers';
        
        $where = array('1=1');
        $where_values = array();
        
        if (!empty($args['status'])) {
            $where[] = 'status = %s';
            $where_values[] = $args['status'];
        }
        
        if (!empty($args['customer_type'])) {
            $where[] = 'customer_type = %s';
            $where_values[] = $args['customer_type'];
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
     * Get customer by email
     */
    public static function get_by_email($email) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'erp_customers';
        $customer_id = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table WHERE email = %s",
            $email
        ));
        
        return $customer_id ? new self($customer_id) : null;
    }

    /**
     * Get customer by WordPress user ID
     */
    public static function get_by_user_id($user_id) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'erp_customers';
        $customer_id = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table WHERE user_id = %d",
            $user_id
        ));
        
        return $customer_id ? new self($customer_id) : null;
    }

    /**
     * Search customers
     */
    public static function search($search_term, $args = array()) {
        global $wpdb;
        
        $defaults = array(
            'limit' => 20,
            'offset' => 0
        );
        
        $args = wp_parse_args($args, $defaults);
        $table = $wpdb->prefix . 'erp_customers';
        
        $query = $wpdb->prepare("
            SELECT * FROM $table 
            WHERE (contact_first_name LIKE %s 
                OR contact_last_name LIKE %s 
                OR company_name LIKE %s 
                OR email LIKE %s 
                OR phone LIKE %s 
                OR mobile LIKE %s)
            ORDER BY contact_last_name ASC
            LIMIT %d OFFSET %d
        ", 
            '%' . $wpdb->esc_like($search_term) . '%',
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
     * Get customer statistics
     */
    public static function get_statistics() {
        global $wpdb;
        
        $table = $wpdb->prefix . 'erp_customers';
        
        $stats = array();
        
        // Total customers count
        $stats['total'] = $wpdb->get_var("SELECT COUNT(*) FROM $table");
        
        // Active customers count
        $stats['active'] = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE status = %s", 'active'));
        
        // Individual customers count
        $stats['individual'] = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE customer_type = %s", 'individual'));
        
        // Business customers count
        $stats['business'] = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE customer_type = %s", 'business'));
        
        // Customers with overdue balance
        $stats['overdue'] = $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE current_balance > 0");
        
        return $stats;
    }

    /**
     * Get top customers by revenue
     */
    public static function get_top_customers($limit = 10) {
        global $wpdb;
        
        $customers_table = $wpdb->prefix . 'erp_customers';
        $bookings_table = $wpdb->prefix . 'erp_bookings';
        
        return $wpdb->get_results($wpdb->prepare("
            SELECT c.*, SUM(b.total_amount) as total_revenue
            FROM $customers_table c
            LEFT JOIN $bookings_table b ON c.id = b.customer_id
            WHERE b.payment_status = 'paid'
            GROUP BY c.id
            ORDER BY total_revenue DESC
            LIMIT %d
        ", $limit));
    }

    /**
     * Create customer from WordPress user
     */
    public static function create_from_wp_user($user_id) {
        $user = get_user_by('id', $user_id);
        if (!$user) {
            return false;
        }
        
        $customer = new self();
        $customer->set('user_id', $user_id);
        $customer->set('contact_first_name', $user->first_name ?: $user->display_name);
        $customer->set('contact_last_name', $user->last_name);
        $customer->set('email', $user->user_email);
        $customer->set('status', 'active');
        $customer->set('customer_type', 'individual');
        
        if ($customer->save()) {
            return $customer;
        }
        
        return false;
    }
}