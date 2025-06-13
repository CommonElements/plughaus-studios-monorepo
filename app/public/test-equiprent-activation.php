<?php
/**
 * EquipRent Pro Activation Test Script
 * 
 * Comprehensive test script to verify plugin activation process and functionality
 * Run this by visiting: http://your-site.local/test-equiprent-activation.php
 * 
 * DELETE THIS FILE AFTER TESTING!
 */

// Load WordPress
require_once('wp-config.php');
require_once('wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    wp_die('Access denied. Admin privileges required.');
}

// Define test configuration
$test_config = array(
    'plugin_path' => 'equiprent-pro/equiprent-pro.php',
    'plugin_name' => 'EquipRent Pro - Equipment Rental Management',
    'expected_version' => '1.0.0',
    'expected_db_version' => '1.0.0',
    'run_activation' => false // Set to true to force activation
);

// Test results storage
$test_results = array(
    'passed' => 0,
    'failed' => 0,
    'warnings' => 0,
    'tests' => array()
);

/**
 * Add test result
 */
function add_test_result($name, $status, $message, $data = null) {
    global $test_results;
    
    $test_results['tests'][] = array(
        'name' => $name,
        'status' => $status, // 'pass', 'fail', 'warning'
        'message' => $message,
        'data' => $data
    );
    
    $test_results[$status === 'pass' ? 'passed' : ($status === 'fail' ? 'failed' : 'warnings')]++;
}

/**
 * Format test output
 */
function format_test_output($test) {
    $color = $test['status'] === 'pass' ? 'green' : ($test['status'] === 'fail' ? 'red' : 'orange');
    $icon = $test['status'] === 'pass' ? '✓' : ($test['status'] === 'fail' ? '✗' : '⚠');
    
    echo '<div style="margin: 10px 0; padding: 10px; border-left: 4px solid ' . $color . '; background: #f9f9f9;">';
    echo '<strong style="color: ' . $color . ';">' . $icon . ' ' . $test['name'] . '</strong><br>';
    echo $test['message'];
    
    if ($test['data']) {
        echo '<details style="margin-top: 10px;"><summary>Details</summary>';
        echo '<pre style="background: #fff; padding: 10px; margin-top: 5px; overflow-x: auto;">';
        if (is_array($test['data']) || is_object($test['data'])) {
            print_r($test['data']);
        } else {
            echo htmlspecialchars($test['data']);
        }
        echo '</pre></details>';
    }
    
    echo '</div>';
}

// Start output
?>
<!DOCTYPE html>
<html>
<head>
    <title>EquipRent Pro Activation Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f1f1f1; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header { background: #007cba; color: white; padding: 20px; margin: -20px -20px 20px -20px; border-radius: 8px 8px 0 0; }
        .summary { background: #f8f8f8; padding: 15px; border-radius: 4px; margin: 20px 0; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f8f8; font-weight: bold; }
        .status-pass { color: green; font-weight: bold; }
        .status-fail { color: red; font-weight: bold; }
        .status-warning { color: orange; font-weight: bold; }
        .action-buttons { margin: 20px 0; }
        .button { display: inline-block; padding: 10px 20px; background: #007cba; color: white; text-decoration: none; border-radius: 4px; margin-right: 10px; }
        .button:hover { background: #005a87; }
        .button-secondary { background: #666; }
        .button-danger { background: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔧 EquipRent Pro Activation Test Suite</h1>
            <p>Comprehensive validation of plugin activation, database creation, and functionality</p>
            <p><strong>Test Time:</strong> <?php echo current_time('Y-m-d H:i:s'); ?></p>
        </div>

<?php

// Run Tests
echo '<h2>🧪 Running Tests...</h2>';

// TEST 1: WordPress Environment Check
try {
    $wp_version = get_bloginfo('version');
    $php_version = PHP_VERSION;
    $mysql_version = $GLOBALS['wpdb']->db_version();
    
    $env_data = array(
        'WordPress Version' => $wp_version,
        'PHP Version' => $php_version,
        'MySQL Version' => $mysql_version,
        'Memory Limit' => ini_get('memory_limit'),
        'Max Execution Time' => ini_get('max_execution_time'),
        'WP_DEBUG' => defined('WP_DEBUG') && WP_DEBUG ? 'ON' : 'OFF'
    );
    
    $requirements_met = true;
    $requirement_issues = array();
    
    if (version_compare($wp_version, '5.8', '<')) {
        $requirements_met = false;
        $requirement_issues[] = 'WordPress 5.8+ required';
    }
    
    if (version_compare($php_version, '7.4', '<')) {
        $requirements_met = false;
        $requirement_issues[] = 'PHP 7.4+ required';
    }
    
    if ($requirements_met) {
        add_test_result('Environment Check', 'pass', 'WordPress environment meets all requirements', $env_data);
    } else {
        add_test_result('Environment Check', 'fail', 'Environment requirements not met: ' . implode(', ', $requirement_issues), $env_data);
    }
} catch (Exception $e) {
    add_test_result('Environment Check', 'fail', 'Error checking environment: ' . $e->getMessage());
}

// TEST 2: Plugin File Structure Check
try {
    $plugin_files = array(
        'Main Plugin File' => 'wp-content/plugins/equiprent-pro/equiprent-pro.php',
        'Core Directory' => 'wp-content/plugins/equiprent-pro/core/',
        'Pro Directory' => 'wp-content/plugins/equiprent-pro/pro/',
        'Activator Class' => 'wp-content/plugins/equiprent-pro/core/includes/class-er-activator.php',
        'Deactivator Class' => 'wp-content/plugins/equiprent-pro/core/includes/class-er-deactivator.php',
        'Post Types Class' => 'wp-content/plugins/equiprent-pro/core/includes/core/class-er-post-types.php',
        'Database Class' => 'wp-content/plugins/equiprent-pro/core/includes/core/class-er-database.php',
        'Admin Class' => 'wp-content/plugins/equiprent-pro/core/includes/admin/class-er-admin.php',
        'Public Class' => 'wp-content/plugins/equiprent-pro/core/includes/public/class-er-shortcodes.php',
        'Utilities Class' => 'wp-content/plugins/equiprent-pro/core/includes/shared/class-er-utilities.php'
    );
    
    $file_check_data = array();
    $all_files_exist = true;
    
    foreach ($plugin_files as $description => $file_path) {
        $exists = file_exists($file_path);
        $file_check_data[$description] = $exists ? 'EXISTS' : 'MISSING';
        if (!$exists) {
            $all_files_exist = false;
        }
    }
    
    if ($all_files_exist) {
        add_test_result('Plugin File Structure', 'pass', 'All essential plugin files found', $file_check_data);
    } else {
        add_test_result('Plugin File Structure', 'fail', 'Some plugin files are missing', $file_check_data);
    }
} catch (Exception $e) {
    add_test_result('Plugin File Structure', 'fail', 'Error checking plugin files: ' . $e->getMessage());
}

// TEST 3: Plugin Activation Status
try {
    $active_plugins = get_option('active_plugins', array());
    $is_active = in_array($test_config['plugin_path'], $active_plugins);
    
    $activation_data = array(
        'Plugin Path' => $test_config['plugin_path'],
        'Is Active' => $is_active ? 'YES' : 'NO',
        'Total Active Plugins' => count($active_plugins)
    );
    
    if ($is_active) {
        add_test_result('Plugin Activation Status', 'pass', 'EquipRent Pro is currently active', $activation_data);
    } else {
        add_test_result('Plugin Activation Status', 'warning', 'Plugin is not active - database tables may not be created yet', $activation_data);
    }
} catch (Exception $e) {
    add_test_result('Plugin Activation Status', 'fail', 'Error checking activation status: ' . $e->getMessage());
}

// TEST 4: Database Tables Check
try {
    global $wpdb;
    
    $expected_tables = array(
        'er_equipment' => 'Equipment inventory table',
        'er_bookings' => 'Rental bookings table', 
        'er_booking_items' => 'Booking line items table',
        'er_customers' => 'Customer information table',
        'er_maintenance' => 'Equipment maintenance records',
        'er_damage_reports' => 'Damage report tracking',
        'er_payments' => 'Payment transaction records',
        'er_activity_log' => 'System activity logging',
        'er_availability' => 'Equipment availability calendar',
        'er_documents' => 'Document storage tracking'
    );
    
    $table_status = array();
    $all_tables_exist = true;
    
    foreach ($expected_tables as $table => $description) {
        $full_table_name = $wpdb->prefix . $table;
        $exists = $wpdb->get_var("SHOW TABLES LIKE '{$full_table_name}'") === $full_table_name;
        
        if ($exists) {
            $row_count = $wpdb->get_var("SELECT COUNT(*) FROM {$full_table_name}");
            $table_status[$table] = array(
                'exists' => true,
                'rows' => $row_count,
                'description' => $description
            );
        } else {
            $table_status[$table] = array(
                'exists' => false,
                'rows' => 'N/A',
                'description' => $description
            );
            $all_tables_exist = false;
        }
    }
    
    if ($all_tables_exist) {
        add_test_result('Database Tables', 'pass', 'All expected database tables exist', $table_status);
    } else {
        add_test_result('Database Tables', 'fail', 'Some database tables are missing', $table_status);
    }
} catch (Exception $e) {
    add_test_result('Database Tables', 'fail', 'Error checking database tables: ' . $e->getMessage());
}

// TEST 5: Plugin Options Check
try {
    $expected_options = array(
        'equiprent_activated' => 'Plugin activation flag',
        'equiprent_activation_date' => 'When plugin was activated',
        'equiprent_db_version' => 'Database schema version',
        'equiprent_version' => 'Plugin version',
        'equiprent_settings' => 'Main plugin settings',
        'equiprent_currency' => 'Currency setting',
        'equiprent_business_name' => 'Business name',
        'equiprent_email_templates' => 'Email template configurations'
    );
    
    $option_status = array();
    $missing_options = 0;
    
    foreach ($expected_options as $option => $description) {
        $value = get_option($option);
        $exists = $value !== false;
        
        $option_status[$option] = array(
            'exists' => $exists,
            'value' => $exists ? (is_array($value) ? 'Array (' . count($value) . ' items)' : $value) : 'NOT SET',
            'description' => $description
        );
        
        if (!$exists) {
            $missing_options++;
        }
    }
    
    if ($missing_options === 0) {
        add_test_result('Plugin Options', 'pass', 'All expected plugin options are set', $option_status);
    } else {
        add_test_result('Plugin Options', 'warning', "{$missing_options} plugin options are missing", $option_status);
    }
} catch (Exception $e) {
    add_test_result('Plugin Options', 'fail', 'Error checking plugin options: ' . $e->getMessage());
}

// TEST 6: WordPress Pages Check
try {
    $expected_pages = array(
        'equiprent_page_equipment-catalog' => 'Equipment Catalog',
        'equiprent_page_booking-form' => 'Make a Reservation',
        'equiprent_page_customer-dashboard' => 'Customer Dashboard',
        'equiprent_page_my-bookings' => 'My Bookings',
        'equiprent_page_booking-checkout' => 'Booking Checkout',
        'equiprent_page_booking-confirmation' => 'Booking Confirmation',
        'equiprent_page_terms-conditions' => 'Terms and Conditions',
        'equiprent_page_damage-waiver' => 'Damage Waiver'
    );
    
    $page_status = array();
    $missing_pages = 0;
    
    foreach ($expected_pages as $option => $page_title) {
        $page_id = get_option($option);
        $page_exists = false;
        $page_url = '';
        
        if ($page_id) {
            $page = get_post($page_id);
            if ($page && $page->post_status === 'publish') {
                $page_exists = true;
                $page_url = get_permalink($page_id);
            }
        }
        
        $page_status[$page_title] = array(
            'exists' => $page_exists,
            'page_id' => $page_id ?: 'Not Set',
            'url' => $page_url ?: 'N/A',
            'status' => $page_exists ? 'Published' : 'Missing/Draft'
        );
        
        if (!$page_exists) {
            $missing_pages++;
        }
    }
    
    if ($missing_pages === 0) {
        add_test_result('WordPress Pages', 'pass', 'All plugin pages created successfully', $page_status);
    } else {
        add_test_result('WordPress Pages', 'warning', "{$missing_pages} plugin pages are missing", $page_status);
    }
} catch (Exception $e) {
    add_test_result('WordPress Pages', 'fail', 'Error checking WordPress pages: ' . $e->getMessage());
}

// TEST 7: User Roles and Capabilities Check
try {
    $expected_roles = array(
        'rental_manager' => 'Rental Manager',
        'rental_staff' => 'Rental Staff', 
        'rental_customer' => 'Rental Customer'
    );
    
    $expected_capabilities = array(
        'manage_equipment',
        'edit_equipment',
        'manage_bookings',
        'process_bookings',
        'manage_rental_customers',
        'manage_payments',
        'manage_maintenance',
        'manage_equiprent_settings'
    );
    
    $role_status = array();
    $missing_roles = 0;
    
    foreach ($expected_roles as $role_key => $role_name) {
        $role = get_role($role_key);
        $exists = $role !== null;
        
        $capabilities = array();
        if ($exists) {
            $capabilities = array_keys($role->capabilities);
        }
        
        $role_status[$role_name] = array(
            'exists' => $exists,
            'capabilities_count' => count($capabilities),
            'sample_capabilities' => array_slice($capabilities, 0, 5)
        );
        
        if (!$exists) {
            $missing_roles++;
        }
    }
    
    // Check admin capabilities
    $admin_role = get_role('administrator');
    $admin_has_caps = true;
    $missing_caps = array();
    
    if ($admin_role) {
        foreach ($expected_capabilities as $cap) {
            if (!$admin_role->has_cap($cap)) {
                $admin_has_caps = false;
                $missing_caps[] = $cap;
            }
        }
    }
    
    $role_status['Administrator Capabilities'] = array(
        'has_all_capabilities' => $admin_has_caps,
        'missing_capabilities' => $missing_caps,
        'total_caps_checked' => count($expected_capabilities)
    );
    
    if ($missing_roles === 0 && $admin_has_caps) {
        add_test_result('User Roles & Capabilities', 'pass', 'All user roles and capabilities configured correctly', $role_status);
    } else {
        $issues = array();
        if ($missing_roles > 0) $issues[] = "{$missing_roles} roles missing";
        if (!$admin_has_caps) $issues[] = "Admin missing " . count($missing_caps) . " capabilities";
        
        add_test_result('User Roles & Capabilities', 'warning', 'Issues found: ' . implode(', ', $issues), $role_status);
    }
} catch (Exception $e) {
    add_test_result('User Roles & Capabilities', 'fail', 'Error checking roles and capabilities: ' . $e->getMessage());
}

// TEST 8: Class Loading Check
try {
    $expected_classes = array(
        'EquipRent_Pro' => 'Main plugin class',
        'ER_Activator' => 'Plugin activator',
        'ER_Deactivator' => 'Plugin deactivator',
        'ER_Post_Types' => 'Post types registration',
        'ER_Database' => 'Database operations',
        'ER_Utilities' => 'Utility functions',
        'ER_Capabilities' => 'User capabilities management'
    );
    
    $class_status = array();
    $missing_classes = 0;
    
    foreach ($expected_classes as $class_name => $description) {
        $exists = class_exists($class_name);
        
        $class_status[$class_name] = array(
            'exists' => $exists,
            'description' => $description,
            'methods' => $exists ? count(get_class_methods($class_name)) : 0
        );
        
        if (!$exists) {
            $missing_classes++;
        }
    }
    
    if ($missing_classes === 0) {
        add_test_result('Class Loading', 'pass', 'All expected classes loaded successfully', $class_status);
    } else {
        add_test_result('Class Loading', 'fail', "{$missing_classes} classes failed to load", $class_status);
    }
} catch (Exception $e) {
    add_test_result('Class Loading', 'fail', 'Error checking class loading: ' . $e->getMessage());
}

// TEST 9: Basic Functionality Test
try {
    $functionality_tests = array();
    
    // Test if main plugin instance is available
    if (class_exists('EquipRent_Pro')) {
        $plugin_instance = EquipRent_Pro::get_instance();
        $functionality_tests['Plugin Instance'] = $plugin_instance ? 'Available' : 'Failed';
        
        if ($plugin_instance) {
            $functionality_tests['Plugin Version'] = method_exists($plugin_instance, 'get_version') ? $plugin_instance->get_version() : 'Method missing';
            $functionality_tests['Is Pro'] = method_exists($plugin_instance, 'is_pro') ? ($plugin_instance->is_pro() ? 'Yes' : 'No') : 'Method missing';
            $functionality_tests['Available Modules'] = method_exists($plugin_instance, 'get_modules') ? count($plugin_instance->get_modules()) . ' modules' : 'Method missing';
        }
    } else {
        $functionality_tests['Plugin Instance'] = 'Class not found';
    }
    
    // Test database connection
    global $wpdb;
    $db_test = $wpdb->get_var("SELECT 1");
    $functionality_tests['Database Connection'] = $db_test === '1' ? 'Working' : 'Failed';
    
    // Test WordPress hooks
    $hooks_registered = 0;
    if (has_action('plugins_loaded')) $hooks_registered++;
    if (has_action('init')) $hooks_registered++;
    if (has_action('admin_menu')) $hooks_registered++;
    
    $functionality_tests['WordPress Hooks'] = "{$hooks_registered} hooks registered";
    
    $all_functional = true;
    foreach ($functionality_tests as $test => $result) {
        if (strpos(strtolower($result), 'fail') !== false || strpos(strtolower($result), 'missing') !== false) {
            $all_functional = false;
            break;
        }
    }
    
    if ($all_functional) {
        add_test_result('Basic Functionality', 'pass', 'All basic functionality tests passed', $functionality_tests);
    } else {
        add_test_result('Basic Functionality', 'warning', 'Some functionality issues detected', $functionality_tests);
    }
} catch (Exception $e) {
    add_test_result('Basic Functionality', 'fail', 'Error testing basic functionality: ' . $e->getMessage());
}

// TEST 10: Admin Interface Check (if admin)
if (is_admin() || wp_doing_ajax()) {
    try {
        $admin_tests = array();
        
        // Check if admin menus would be registered
        if (class_exists('ER_Admin')) {
            $admin_tests['Admin Class'] = 'Loaded';
            
            // Test admin styles and scripts registration
            global $wp_scripts, $wp_styles;
            $admin_tests['Script Queue Available'] = isset($wp_scripts) ? 'Yes' : 'No';
            $admin_tests['Style Queue Available'] = isset($wp_styles) ? 'Yes' : 'No';
        } else {
            $admin_tests['Admin Class'] = 'Not Loaded';
        }
        
        // Check for admin capabilities
        $current_user = wp_get_current_user();
        $admin_tests['Current User Can Manage'] = current_user_can('manage_options') ? 'Yes' : 'No';
        $admin_tests['User ID'] = $current_user->ID;
        
        add_test_result('Admin Interface', 'pass', 'Admin interface components checked', $admin_tests);
    } catch (Exception $e) {
        add_test_result('Admin Interface', 'warning', 'Error checking admin interface: ' . $e->getMessage());
    }
}

// Display Test Results Summary
echo '<div class="summary">';
echo '<h2>📊 Test Results Summary</h2>';
echo '<table>';
echo '<tr><th>Status</th><th>Count</th><th>Percentage</th></tr>';

$total_tests = $test_results['passed'] + $test_results['failed'] + $test_results['warnings'];
$pass_percentage = $total_tests > 0 ? round(($test_results['passed'] / $total_tests) * 100, 1) : 0;
$fail_percentage = $total_tests > 0 ? round(($test_results['failed'] / $total_tests) * 100, 1) : 0;
$warning_percentage = $total_tests > 0 ? round(($test_results['warnings'] / $total_tests) * 100, 1) : 0;

echo '<tr><td class="status-pass">✓ Passed</td><td>' . $test_results['passed'] . '</td><td>' . $pass_percentage . '%</td></tr>';
echo '<tr><td class="status-fail">✗ Failed</td><td>' . $test_results['failed'] . '</td><td>' . $fail_percentage . '%</td></tr>';
echo '<tr><td class="status-warning">⚠ Warnings</td><td>' . $test_results['warnings'] . '</td><td>' . $warning_percentage . '%</td></tr>';
echo '<tr><th>Total</th><th>' . $total_tests . '</th><th>100%</th></tr>';
echo '</table>';

// Overall status
if ($test_results['failed'] === 0 && $test_results['warnings'] === 0) {
    echo '<p style="color: green; font-size: 18px; font-weight: bold;">🎉 All tests passed! EquipRent Pro is properly activated and configured.</p>';
} elseif ($test_results['failed'] === 0) {
    echo '<p style="color: orange; font-size: 18px; font-weight: bold;">⚠️ Tests passed with warnings. Plugin should work but some features may not be fully configured.</p>';
} else {
    echo '<p style="color: red; font-size: 18px; font-weight: bold;">❌ Some tests failed. Plugin activation may be incomplete or there are configuration issues.</p>';
}

echo '</div>';

// Action buttons
echo '<div class="action-buttons">';
echo '<h3>🛠️ Actions</h3>';

$is_plugin_active = in_array($test_config['plugin_path'], get_option('active_plugins', array()));

if (!$is_plugin_active) {
    echo '<a href="' . admin_url('plugins.php') . '" class="button">Activate Plugin</a>';
}

echo '<a href="' . admin_url('admin.php?page=equiprent-settings') . '" class="button button-secondary">Plugin Settings</a>';
echo '<a href="' . admin_url('admin.php?page=equiprent-dashboard') . '" class="button button-secondary">Plugin Dashboard</a>';
echo '<a href="' . admin_url('plugins.php') . '" class="button button-secondary">Manage Plugins</a>';

echo '</div>';

// Detailed Test Results
echo '<h2>📋 Detailed Test Results</h2>';

foreach ($test_results['tests'] as $test) {
    format_test_output($test);
}

// Recommendations
echo '<h2>💡 Recommendations</h2>';

if ($test_results['failed'] > 0) {
    echo '<div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin: 10px 0;">';
    echo '<h4>Critical Issues:</h4>';
    echo '<ul>';
    foreach ($test_results['tests'] as $test) {
        if ($test['status'] === 'fail') {
            echo '<li><strong>' . $test['name'] . ':</strong> ' . strip_tags($test['message']) . '</li>';
        }
    }
    echo '</ul>';
    echo '<p><strong>Action Required:</strong> Fix these issues before using the plugin in production.</p>';
    echo '</div>';
}

if ($test_results['warnings'] > 0) {
    echo '<div style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 4px; margin: 10px 0;">';
    echo '<h4>Warnings:</h4>';
    echo '<ul>';
    foreach ($test_results['tests'] as $test) {
        if ($test['status'] === 'warning') {
            echo '<li><strong>' . $test['name'] . ':</strong> ' . strip_tags($test['message']) . '</li>';
        }
    }
    echo '</ul>';
    echo '<p><strong>Recommended:</strong> Address these warnings for optimal plugin performance.</p>';
    echo '</div>';
}

if ($test_results['failed'] === 0 && $test_results['warnings'] === 0) {
    echo '<div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin: 10px 0;">';
    echo '<h4>✅ Plugin Ready for Use!</h4>';
    echo '<p>EquipRent Pro has been successfully activated and all components are working correctly.</p>';
    echo '<p><strong>Next Steps:</strong></p>';
    echo '<ul>';
    echo '<li>Configure your business settings in the plugin dashboard</li>';
    echo '<li>Add your equipment inventory</li>';
    echo '<li>Customize email templates</li>';
    echo '<li>Set up your booking workflow</li>';
    echo '<li>Test the customer booking process</li>';
    echo '</ul>';
    echo '</div>';
}

// Security reminder
echo '<div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin: 20px 0;">';
echo '<h4>🔒 Security Reminder</h4>';
echo '<p><strong>IMPORTANT:</strong> Delete this test file immediately after testing!</p>';
echo '<p>This file contains sensitive information about your plugin configuration and should not be accessible in production.</p>';
echo '<code>rm ' . __FILE__ . '</code>';
echo '</div>';

?>

        <hr>
        <p style="text-align: center; color: #666; margin-top: 30px;">
            <small>
                EquipRent Pro Activation Test Suite | 
                Generated: <?php echo current_time('Y-m-d H:i:s'); ?> | 
                WordPress <?php echo get_bloginfo('version'); ?> | 
                PHP <?php echo PHP_VERSION; ?>
            </small>
        </p>
    </div>
</body>
</html>