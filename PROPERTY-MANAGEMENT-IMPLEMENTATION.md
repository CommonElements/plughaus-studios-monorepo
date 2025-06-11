# Property Management Ecosystem - Technical Implementation Plan

## 🎯 Implementation Overview

This document outlines the specific technical implementation for the Property Management ecosystem, including database design, API structure, and add-on integration patterns.

## 🗄️ Database Schema

### Core Tables

```sql
-- Properties
CREATE TABLE phpm_properties (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    name varchar(255) NOT NULL,
    address text,
    city varchar(100),
    state varchar(50),
    zip varchar(20),
    country varchar(50) DEFAULT 'US',
    property_type varchar(50), -- 'single_family', 'multi_family', 'commercial', 'condo'
    units_count int(11) DEFAULT 1,
    purchase_date date,
    purchase_price decimal(12,2),
    current_value decimal(12,2),
    status varchar(20) DEFAULT 'active', -- 'active', 'inactive', 'sold'
    notes text,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY property_type (property_type),
    KEY status (status),
    KEY city_state (city, state)
);

-- Units (for multi-unit properties)
CREATE TABLE phpm_units (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    property_id bigint(20) unsigned NOT NULL,
    unit_number varchar(50),
    bedrooms int(11),
    bathrooms decimal(3,1),
    square_feet int(11),
    rent_amount decimal(10,2),
    deposit_amount decimal(10,2),
    status varchar(20) DEFAULT 'vacant', -- 'occupied', 'vacant', 'maintenance'
    description text,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY property_id (property_id),
    KEY status (status),
    FOREIGN KEY (property_id) REFERENCES phpm_properties(id) ON DELETE CASCADE
);

-- Tenants
CREATE TABLE phpm_tenants (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    first_name varchar(100) NOT NULL,
    last_name varchar(100) NOT NULL,
    email varchar(255),
    phone varchar(20),
    emergency_contact_name varchar(200),
    emergency_contact_phone varchar(20),
    move_in_date date,
    move_out_date date,
    status varchar(20) DEFAULT 'active', -- 'active', 'inactive', 'evicted'
    notes text,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY email (email),
    KEY status (status),
    KEY name (last_name, first_name)
);

-- Leases
CREATE TABLE phpm_leases (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    property_id bigint(20) unsigned NOT NULL,
    unit_id bigint(20) unsigned,
    tenant_id bigint(20) unsigned NOT NULL,
    lease_start date NOT NULL,
    lease_end date,
    rent_amount decimal(10,2) NOT NULL,
    deposit_amount decimal(10,2),
    lease_type varchar(20) DEFAULT 'fixed', -- 'fixed', 'month_to_month'
    payment_due_day int(11) DEFAULT 1,
    late_fee_amount decimal(10,2),
    late_fee_grace_days int(11) DEFAULT 5,
    status varchar(20) DEFAULT 'active', -- 'active', 'terminated', 'expired'
    lease_document_url varchar(500),
    notes text,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY property_id (property_id),
    KEY unit_id (unit_id),
    KEY tenant_id (tenant_id),
    KEY status (status),
    KEY lease_dates (lease_start, lease_end),
    FOREIGN KEY (property_id) REFERENCES phpm_properties(id) ON DELETE CASCADE,
    FOREIGN KEY (unit_id) REFERENCES phpm_units(id) ON DELETE SET NULL,
    FOREIGN KEY (tenant_id) REFERENCES phpm_tenants(id) ON DELETE CASCADE
);

-- Maintenance Requests
CREATE TABLE phpm_maintenance (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    property_id bigint(20) unsigned NOT NULL,
    unit_id bigint(20) unsigned,
    tenant_id bigint(20) unsigned,
    title varchar(255) NOT NULL,
    description text,
    priority varchar(20) DEFAULT 'medium', -- 'low', 'medium', 'high', 'emergency'
    status varchar(20) DEFAULT 'open', -- 'open', 'in_progress', 'completed', 'cancelled'
    category varchar(50), -- 'plumbing', 'electrical', 'hvac', 'general'
    estimated_cost decimal(10,2),
    actual_cost decimal(10,2),
    vendor_name varchar(200),
    vendor_contact varchar(200),
    scheduled_date datetime,
    completed_date datetime,
    notes text,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY property_id (property_id),
    KEY unit_id (unit_id),
    KEY tenant_id (tenant_id),
    KEY status (status),
    KEY priority (priority),
    KEY category (category),
    FOREIGN KEY (property_id) REFERENCES phpm_properties(id) ON DELETE CASCADE,
    FOREIGN KEY (unit_id) REFERENCES phpm_units(id) ON DELETE SET NULL,
    FOREIGN KEY (tenant_id) REFERENCES phpm_tenants(id) ON DELETE SET NULL
);
```

### Pro Feature Tables

```sql
-- Payments (Pro Feature)
CREATE TABLE phpm_payments (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    lease_id bigint(20) unsigned NOT NULL,
    amount decimal(10,2) NOT NULL,
    payment_date date NOT NULL,
    due_date date NOT NULL,
    payment_method varchar(50), -- 'cash', 'check', 'online', 'bank_transfer'
    payment_reference varchar(100),
    payment_type varchar(50) DEFAULT 'rent', -- 'rent', 'deposit', 'late_fee', 'other'
    status varchar(20) DEFAULT 'pending', -- 'pending', 'completed', 'failed', 'refunded'
    notes text,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY lease_id (lease_id),
    KEY payment_date (payment_date),
    KEY due_date (due_date),
    KEY status (status),
    FOREIGN KEY (lease_id) REFERENCES phpm_leases(id) ON DELETE CASCADE
);

-- Expenses (Pro Feature)
CREATE TABLE phpm_expenses (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    property_id bigint(20) unsigned NOT NULL,
    unit_id bigint(20) unsigned,
    maintenance_id bigint(20) unsigned,
    amount decimal(10,2) NOT NULL,
    expense_date date NOT NULL,
    category varchar(50), -- 'maintenance', 'utilities', 'insurance', 'taxes', 'management'
    vendor_name varchar(200),
    description text,
    receipt_url varchar(500),
    tax_deductible boolean DEFAULT true,
    notes text,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY property_id (property_id),
    KEY unit_id (unit_id),
    KEY maintenance_id (maintenance_id),
    KEY expense_date (expense_date),
    KEY category (category),
    FOREIGN KEY (property_id) REFERENCES phpm_properties(id) ON DELETE CASCADE,
    FOREIGN KEY (unit_id) REFERENCES phpm_units(id) ON DELETE SET NULL,
    FOREIGN KEY (maintenance_id) REFERENCES phpm_maintenance(id) ON DELETE SET NULL
);
```

### Add-on Extension Tables

```sql
-- Custom Fields (Add-on Framework)
CREATE TABLE phpm_custom_fields (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    entity_type varchar(50) NOT NULL, -- 'property', 'tenant', 'lease', 'maintenance'
    entity_id bigint(20) unsigned NOT NULL,
    field_name varchar(100) NOT NULL,
    field_value longtext,
    addon_slug varchar(50),
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY entity_lookup (entity_type, entity_id),
    KEY field_name (field_name),
    KEY addon_slug (addon_slug)
);

-- Documents (Document Management Add-on)
CREATE TABLE phpm_documents (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    entity_type varchar(50) NOT NULL,
    entity_id bigint(20) unsigned NOT NULL,
    document_name varchar(255) NOT NULL,
    document_type varchar(50), -- 'lease', 'application', 'inspection', 'invoice'
    file_path varchar(500) NOT NULL,
    file_size bigint(20),
    mime_type varchar(100),
    version int(11) DEFAULT 1,
    is_template boolean DEFAULT false,
    access_level varchar(20) DEFAULT 'private', -- 'private', 'tenant', 'public'
    uploaded_by bigint(20) unsigned,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY entity_lookup (entity_type, entity_id),
    KEY document_type (document_type),
    KEY access_level (access_level)
);
```

## 🔌 API Structure

### REST API Endpoints

```php
// Core API Endpoints
/wp-json/phpm/v1/properties/
/wp-json/phpm/v1/properties/{id}
/wp-json/phpm/v1/properties/{id}/units
/wp-json/phpm/v1/units/
/wp-json/phpm/v1/units/{id}
/wp-json/phpm/v1/tenants/
/wp-json/phpm/v1/tenants/{id}
/wp-json/phpm/v1/leases/
/wp-json/phpm/v1/leases/{id}
/wp-json/phpm/v1/maintenance/
/wp-json/phpm/v1/maintenance/{id}

// Pro API Endpoints
/wp-json/phpm/v1/payments/
/wp-json/phpm/v1/expenses/
/wp-json/phpm/v1/reports/
/wp-json/phpm/v1/analytics/

// Add-on API Extension Points
/wp-json/phpm/v1/addons/{addon-slug}/
```

### API Authentication

```php
// API Key Authentication for External Apps
add_filter('determine_current_user', 'phpm_api_authentication', 20);

function phpm_api_authentication($user_id) {
    if (!empty($user_id)) {
        return $user_id;
    }
    
    $api_key = $_SERVER['HTTP_X_PHPM_API_KEY'] ?? '';
    if ($api_key) {
        return phpm_validate_api_key($api_key);
    }
    
    return $user_id;
}
```

## 🏗️ Core Plugin Architecture

### Main Plugin Class

```php
class Vireo_Property_Management {
    
    private static $instance = null;
    private $addons = array();
    private $is_pro = false;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->check_pro_license();
        $this->load_dependencies();
        $this->init_hooks();
    }
    
    private function check_pro_license() {
        // License validation logic
        $this->is_pro = $this->validate_license();
    }
    
    public function is_pro() {
        return $this->is_pro;
    }
    
    public function register_addon($addon_data) {
        $this->addons[$addon_data['slug']] = $addon_data;
        do_action('phpm_addon_registered', $addon_data);
    }
    
    public function get_addons() {
        return $this->addons;
    }
}
```

### Data Model Classes

```php
// Property Model
class PHPM_Property {
    
    private $id;
    private $data = array();
    
    public function __construct($property_id = 0) {
        if ($property_id) {
            $this->load_property($property_id);
        }
    }
    
    public function get_id() {
        return $this->id;
    }
    
    public function get_name() {
        return $this->data['name'] ?? '';
    }
    
    public function set_name($name) {
        $this->data['name'] = sanitize_text_field($name);
    }
    
    public function get_units() {
        global $wpdb;
        return $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM {$wpdb->prefix}phpm_units WHERE property_id = %d", $this->id)
        );
    }
    
    public function save() {
        global $wpdb;
        
        $data = array(
            'name' => $this->data['name'],
            'address' => $this->data['address'] ?? '',
            'city' => $this->data['city'] ?? '',
            'state' => $this->data['state'] ?? '',
            'zip' => $this->data['zip'] ?? '',
            'property_type' => $this->data['property_type'] ?? 'single_family',
            'units_count' => $this->data['units_count'] ?? 1,
            'status' => $this->data['status'] ?? 'active'
        );
        
        if ($this->id) {
            $result = $wpdb->update(
                $wpdb->prefix . 'phpm_properties',
                $data,
                array('id' => $this->id),
                array('%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s'),
                array('%d')
            );
        } else {
            $result = $wpdb->insert(
                $wpdb->prefix . 'phpm_properties',
                $data,
                array('%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s')
            );
            $this->id = $wpdb->insert_id;
        }
        
        if ($result !== false) {
            do_action('phpm_property_saved', $this->id, $this);
            return true;
        }
        
        return false;
    }
}
```

## 🔗 Add-on Integration Framework

### Add-on Base Class

```php
abstract class PHPM_Addon_Base {
    
    protected $addon_slug;
    protected $addon_name;
    protected $version;
    protected $requires_pro;
    
    public function __construct() {
        $this->init();
        $this->register_addon();
    }
    
    abstract protected function init();
    
    protected function register_addon() {
        $phpm = Vireo_Property_Management::get_instance();
        $phpm->register_addon(array(
            'slug' => $this->addon_slug,
            'name' => $this->addon_name,
            'version' => $this->version,
            'requires_pro' => $this->requires_pro,
            'instance' => $this
        ));
    }
    
    protected function add_admin_menu($menu_title, $capability, $callback) {
        add_action('admin_menu', function() use ($menu_title, $capability, $callback) {
            add_submenu_page(
                'phpm-dashboard',
                $menu_title,
                $menu_title,
                $capability,
                $this->addon_slug,
                $callback
            );
        });
    }
    
    protected function add_api_endpoint($endpoint, $callback) {
        add_action('rest_api_init', function() use ($endpoint, $callback) {
            register_rest_route('phpm/v1', $endpoint, array(
                'methods' => 'GET,POST,PUT,DELETE',
                'callback' => $callback,
                'permission_callback' => array($this, 'check_permissions')
            ));
        });
    }
    
    abstract public function check_permissions($request);
}
```

### Sample Add-on Implementation

```php
// Tenant Portal Add-on
class PHPM_Tenant_Portal extends PHPM_Addon_Base {
    
    protected $addon_slug = 'tenant-portal';
    protected $addon_name = 'Tenant Portal';
    protected $version = '1.0.0';
    protected $requires_pro = false;
    
    protected function init() {
        add_action('init', array($this, 'create_tenant_pages'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_shortcode('tenant_portal', array($this, 'tenant_portal_shortcode'));
        
        // API endpoints
        $this->add_api_endpoint('tenant-portal/login', array($this, 'handle_tenant_login'));
        $this->add_api_endpoint('tenant-portal/payments', array($this, 'handle_tenant_payments'));
        $this->add_api_endpoint('tenant-portal/maintenance', array($this, 'handle_maintenance_requests'));
    }
    
    public function create_tenant_pages() {
        // Create tenant portal pages if they don't exist
        if (!get_page_by_path('tenant-portal')) {
            wp_insert_post(array(
                'post_title' => 'Tenant Portal',
                'post_content' => '[tenant_portal]',
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_name' => 'tenant-portal'
            ));
        }
    }
    
    public function tenant_portal_shortcode($atts) {
        if (!is_user_logged_in()) {
            return $this->render_login_form();
        }
        
        $tenant = $this->get_current_tenant();
        if (!$tenant) {
            return '<p>Access denied. You are not registered as a tenant.</p>';
        }
        
        return $this->render_tenant_dashboard($tenant);
    }
    
    private function render_tenant_dashboard($tenant) {
        ob_start();
        include plugin_dir_path(__FILE__) . 'templates/tenant-dashboard.php';
        return ob_get_clean();
    }
    
    public function check_permissions($request) {
        return is_user_logged_in();
    }
}

// Initialize the add-on
new PHPM_Tenant_Portal();
```

## 📊 Reporting System

### Report Base Class

```php
abstract class PHPM_Report_Base {
    
    protected $report_slug;
    protected $report_name;
    protected $requires_pro;
    
    abstract public function generate_data($params = array());
    abstract public function render_html($data);
    
    public function register_report() {
        add_filter('phpm_available_reports', function($reports) {
            $reports[$this->report_slug] = array(
                'name' => $this->report_name,
                'requires_pro' => $this->requires_pro,
                'class' => get_class($this)
            );
            return $reports;
        });
    }
    
    public function export_csv($data) {
        // CSV export functionality
    }
    
    public function export_pdf($data) {
        // PDF export functionality (Pro feature)
    }
}

// Sample Report Implementation
class PHPM_Occupancy_Report extends PHPM_Report_Base {
    
    protected $report_slug = 'occupancy';
    protected $report_name = 'Occupancy Report';
    protected $requires_pro = false;
    
    public function generate_data($params = array()) {
        global $wpdb;
        
        $date_from = $params['date_from'] ?? date('Y-m-01');
        $date_to = $params['date_to'] ?? date('Y-m-t');
        
        $query = "
            SELECT p.name as property_name,
                   COUNT(u.id) as total_units,
                   COUNT(l.id) as occupied_units,
                   (COUNT(l.id) / COUNT(u.id) * 100) as occupancy_rate
            FROM {$wpdb->prefix}phpm_properties p
            LEFT JOIN {$wpdb->prefix}phpm_units u ON p.id = u.property_id
            LEFT JOIN {$wpdb->prefix}phpm_leases l ON u.id = l.unit_id 
                AND l.status = 'active'
                AND l.lease_start <= %s
                AND (l.lease_end IS NULL OR l.lease_end >= %s)
            WHERE p.status = 'active'
            GROUP BY p.id
        ";
        
        return $wpdb->get_results($wpdb->prepare($query, $date_to, $date_from));
    }
    
    public function render_html($data) {
        ob_start();
        ?>
        <div class="phpm-report occupancy-report">
            <h3>Property Occupancy Report</h3>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Property</th>
                        <th>Total Units</th>
                        <th>Occupied Units</th>
                        <th>Occupancy Rate</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $row): ?>
                    <tr>
                        <td><?php echo esc_html($row->property_name); ?></td>
                        <td><?php echo intval($row->total_units); ?></td>
                        <td><?php echo intval($row->occupied_units); ?></td>
                        <td><?php echo number_format($row->occupancy_rate, 1); ?>%</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
        return ob_get_clean();
    }
}
```

## 🔧 Installation & Activation Flow

### Plugin Activation

```php
register_activation_hook(__FILE__, 'phpm_activate_plugin');

function phpm_activate_plugin() {
    // Create database tables
    phpm_create_tables();
    
    // Create default pages
    phpm_create_default_pages();
    
    // Set default options
    phpm_set_default_options();
    
    // Flush rewrite rules
    flush_rewrite_rules();
    
    // Trigger activation hook for add-ons
    do_action('phpm_plugin_activated');
}

function phpm_create_tables() {
    global $wpdb;
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    
    // Execute table creation SQL
    $sql_files = array(
        'properties.sql',
        'units.sql',
        'tenants.sql',
        'leases.sql',
        'maintenance.sql'
    );
    
    foreach ($sql_files as $file) {
        $sql = file_get_contents(plugin_dir_path(__FILE__) . 'sql/' . $file);
        dbDelta($sql);
    }
}
```

## 🚀 Performance Optimization

### Database Optimization

```php
// Implement database indexing
function phpm_optimize_database() {
    global $wpdb;
    
    // Add composite indexes for common queries
    $indexes = array(
        "CREATE INDEX idx_property_status ON {$wpdb->prefix}phpm_properties (status, property_type)",
        "CREATE INDEX idx_lease_dates ON {$wpdb->prefix}phpm_leases (lease_start, lease_end, status)",
        "CREATE INDEX idx_payment_dates ON {$wpdb->prefix}phpm_payments (payment_date, due_date, status)"
    );
    
    foreach ($indexes as $index_sql) {
        $wpdb->query($index_sql);
    }
}

// Implement query caching
function phpm_get_cached_property_data($property_id) {
    $cache_key = "phpm_property_data_{$property_id}";
    $data = wp_cache_get($cache_key);
    
    if (false === $data) {
        $data = phpm_fetch_property_data($property_id);
        wp_cache_set($cache_key, $data, '', HOUR_IN_SECONDS);
    }
    
    return $data;
}
```

This implementation plan provides a solid foundation for building the Property Management ecosystem with proper scalability, extensibility, and performance considerations.