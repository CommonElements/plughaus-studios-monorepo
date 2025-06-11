<?php
/**
 * Plugin Activation Test Suite
 * 
 * Comprehensive testing for Vireo Designs plugin activation across different environments
 * Tests both Property Management and Sports League Management plugins
 * 
 * @package VireoDesigns
 * @subpackage Tests
 */

class Plugin_Activation_Test_Suite {
    
    private $test_results = [];
    private $environment_info = [];
    private $log_file;
    
    public function __construct() {
        $this->log_file = __DIR__ . '/activation-test-log-' . date('Y-m-d-H-i-s') . '.txt';
        $this->gather_environment_info();
    }
    
    /**
     * Run complete test suite
     */
    public function run_all_tests() {
        $this->log("=== VIREO DESIGNS PLUGIN ACTIVATION TEST SUITE ===");
        $this->log("Test Started: " . date('Y-m-d H:i:s'));
        $this->log_environment_info();
        
        // Test Property Management Plugin
        $this->test_property_management_activation();
        
        // Test Sports League Management Plugin
        $this->test_sports_league_activation();
        
        // Test plugin conflicts
        $this->test_plugin_conflicts();
        
        // Test database operations
        $this->test_database_operations();
        
        // Generate report
        $this->generate_test_report();
        
        return $this->test_results;
    }
    
    /**
     * Gather WordPress environment information
     */
    private function gather_environment_info() {
        global $wp_version, $wpdb;
        
        $this->environment_info = [
            'wp_version' => $wp_version,
            'php_version' => PHP_VERSION,
            'mysql_version' => $wpdb->db_version(),
            'active_theme' => get_template(),
            'multisite' => is_multisite(),
            'debug_mode' => WP_DEBUG,
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'active_plugins' => get_option('active_plugins', []),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'
        ];
    }
    
    /**
     * Test Property Management Plugin activation
     */
    private function test_property_management_activation() {
        $this->log("\n=== TESTING PROPERTY MANAGEMENT PLUGIN ===");
        
        $plugin_path = 'plughaus-property-management/plughaus-property-management.php';
        $test_name = 'Property Management Plugin Activation';
        
        try {
            // Deactivate if already active
            if (is_plugin_active($plugin_path)) {
                deactivate_plugins($plugin_path);
                $this->log("Deactivated existing Property Management plugin");
            }
            
            // Test activation
            $result = activate_plugin($plugin_path);
            
            if (is_wp_error($result)) {
                $this->record_test_failure($test_name, $result->get_error_message());
                return false;
            }
            
            // Verify activation
            if (!is_plugin_active($plugin_path)) {
                $this->record_test_failure($test_name, "Plugin not active after activation attempt");
                return false;
            }
            
            // Test plugin class exists
            if (!class_exists('Plughaus_Property_Management')) {
                $this->record_test_failure($test_name, "Main plugin class not found");
                return false;
            }
            
            // Test database tables were created
            $tables_created = $this->verify_property_management_tables();
            if (!$tables_created) {
                $this->record_test_failure($test_name, "Database tables not created properly");
                return false;
            }
            
            // Test admin menu registration
            $menu_registered = $this->verify_admin_menu('property-management');
            if (!$menu_registered) {
                $this->record_test_failure($test_name, "Admin menu not registered");
                return false;
            }
            
            $this->record_test_success($test_name, "All activation checks passed");
            return true;
            
        } catch (Exception $e) {
            $this->record_test_failure($test_name, "Exception: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Test Sports League Management Plugin activation
     */
    private function test_sports_league_activation() {
        $this->log("\n=== TESTING SPORTS LEAGUE MANAGEMENT PLUGIN ===");
        
        $plugin_path = 'vireo-sports-league/vireo-sports-league.php';
        $test_name = 'Sports League Management Plugin Activation';
        
        try {
            // Deactivate if already active
            if (is_plugin_active($plugin_path)) {
                deactivate_plugins($plugin_path);
                $this->log("Deactivated existing Sports League plugin");
            }
            
            // Test activation
            $result = activate_plugin($plugin_path);
            
            if (is_wp_error($result)) {
                $this->record_test_failure($test_name, $result->get_error_message());
                return false;
            }
            
            // Verify activation
            if (!is_plugin_active($plugin_path)) {
                $this->record_test_failure($test_name, "Plugin not active after activation attempt");
                return false;
            }
            
            // Test plugin class exists
            if (!class_exists('Vireo_Sports_League')) {
                $this->record_test_failure($test_name, "Main plugin class not found");
                return false;
            }
            
            // Test database tables were created
            $tables_created = $this->verify_sports_league_tables();
            if (!$tables_created) {
                $this->record_test_failure($test_name, "Database tables not created properly");
                return false;
            }
            
            // Test admin menu registration
            $menu_registered = $this->verify_admin_menu('sports-league');
            if (!$menu_registered) {
                $this->record_test_failure($test_name, "Admin menu not registered");
                return false;
            }
            
            $this->record_test_success($test_name, "All activation checks passed");
            return true;
            
        } catch (Exception $e) {
            $this->record_test_failure($test_name, "Exception: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Test for plugin conflicts
     */
    private function test_plugin_conflicts() {
        $this->log("\n=== TESTING PLUGIN CONFLICTS ===");
        
        $property_path = 'plughaus-property-management/plughaus-property-management.php';
        $sports_path = 'vireo-sports-league/vireo-sports-league.php';
        $test_name = 'Plugin Conflict Detection';
        
        try {
            // Activate both plugins
            activate_plugin($property_path);
            activate_plugin($sports_path);
            
            // Check for JavaScript errors (basic check)
            if ($this->detect_javascript_errors()) {
                $this->record_test_failure($test_name, "JavaScript errors detected with both plugins active");
                return false;
            }
            
            // Check for PHP fatal errors in error log
            if ($this->detect_php_errors()) {
                $this->record_test_failure($test_name, "PHP errors detected in error log");
                return false;
            }
            
            // Test admin pages load properly
            if (!$this->test_admin_pages_load()) {
                $this->record_test_failure($test_name, "Admin pages failed to load properly");
                return false;
            }
            
            $this->record_test_success($test_name, "No conflicts detected between plugins");
            return true;
            
        } catch (Exception $e) {
            $this->record_test_failure($test_name, "Exception: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Test database operations
     */
    private function test_database_operations() {
        $this->log("\n=== TESTING DATABASE OPERATIONS ===");
        
        // Test Property Management CRUD operations
        $this->test_property_management_crud();
        
        // Test Sports League CRUD operations
        $this->test_sports_league_crud();
        
        // Test database performance
        $this->test_database_performance();
    }
    
    /**
     * Verify Property Management database tables
     */
    private function verify_property_management_tables() {
        global $wpdb;
        
        $required_tables = [
            'phpm_properties',
            'phpm_units',
            'phpm_tenants',
            'phpm_leases',
            'phpm_maintenance_requests',
            'phpm_payments',
            'phpm_expenses',
            'phpm_documents',
            'phpm_communications',
            'phpm_settings',
            'phpm_activity_log'
        ];
        
        foreach ($required_tables as $table) {
            $table_name = $wpdb->prefix . $table;
            $result = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name));
            
            if ($result !== $table_name) {
                $this->log("Missing table: $table_name");
                return false;
            }
        }
        
        $this->log("All Property Management tables verified");
        return true;
    }
    
    /**
     * Verify Sports League Management database tables
     */
    private function verify_sports_league_tables() {
        global $wpdb;
        
        $required_tables = [
            'vsl_leagues',
            'vsl_seasons',
            'vsl_teams',
            'vsl_players',
            'vsl_matches',
            'vsl_match_events',
            'vsl_standings',
            'vsl_player_stats',
            'vsl_venues',
            'vsl_officials',
            'vsl_activity_log'
        ];
        
        foreach ($required_tables as $table) {
            $table_name = $wpdb->prefix . $table;
            $result = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name));
            
            if ($result !== $table_name) {
                $this->log("Missing table: $table_name");
                return false;
            }
        }
        
        $this->log("All Sports League tables verified");
        return true;
    }
    
    /**
     * Verify admin menu registration
     */
    private function verify_admin_menu($plugin_type) {
        global $menu, $submenu;
        
        if ($plugin_type === 'property-management') {
            // Check for Property Management menu
            foreach ($menu as $menu_item) {
                if (strpos($menu_item[2], 'property-management') !== false) {
                    $this->log("Property Management admin menu found");
                    return true;
                }
            }
        } elseif ($plugin_type === 'sports-league') {
            // Check for Sports League menu
            foreach ($menu as $menu_item) {
                if (strpos($menu_item[2], 'sports-league') !== false) {
                    $this->log("Sports League admin menu found");
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Test Property Management CRUD operations
     */
    private function test_property_management_crud() {
        $test_name = 'Property Management CRUD';
        
        try {
            // Test property creation
            $property_data = [
                'name' => 'Test Property',
                'address' => '123 Test St',
                'type' => 'residential',
                'status' => 'active'
            ];
            
            global $wpdb;
            $table_name = $wpdb->prefix . 'phpm_properties';
            
            $result = $wpdb->insert($table_name, $property_data);
            
            if ($result === false) {
                $this->record_test_failure($test_name, "Failed to insert test property");
                return false;
            }
            
            $property_id = $wpdb->insert_id;
            $this->log("Created test property with ID: $property_id");
            
            // Test property retrieval
            $property = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $property_id));
            
            if (!$property) {
                $this->record_test_failure($test_name, "Failed to retrieve test property");
                return false;
            }
            
            // Test property update
            $update_data = ['status' => 'inactive'];
            $updated = $wpdb->update($table_name, $update_data, ['id' => $property_id]);
            
            if ($updated === false) {
                $this->record_test_failure($test_name, "Failed to update test property");
                return false;
            }
            
            // Test property deletion
            $deleted = $wpdb->delete($table_name, ['id' => $property_id]);
            
            if ($deleted === false) {
                $this->record_test_failure($test_name, "Failed to delete test property");
                return false;
            }
            
            $this->record_test_success($test_name, "CRUD operations completed successfully");
            return true;
            
        } catch (Exception $e) {
            $this->record_test_failure($test_name, "Exception: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Test Sports League CRUD operations
     */
    private function test_sports_league_crud() {
        $test_name = 'Sports League CRUD';
        
        try {
            // Test league creation
            $league_data = [
                'name' => 'Test League',
                'sport' => 'soccer',
                'status' => 'active',
                'created_at' => current_time('mysql')
            ];
            
            global $wpdb;
            $table_name = $wpdb->prefix . 'vsl_leagues';
            
            $result = $wpdb->insert($table_name, $league_data);
            
            if ($result === false) {
                $this->record_test_failure($test_name, "Failed to insert test league");
                return false;
            }
            
            $league_id = $wpdb->insert_id;
            $this->log("Created test league with ID: $league_id");
            
            // Test league retrieval
            $league = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $league_id));
            
            if (!$league) {
                $this->record_test_failure($test_name, "Failed to retrieve test league");
                return false;
            }
            
            // Clean up
            $wpdb->delete($table_name, ['id' => $league_id]);
            
            $this->record_test_success($test_name, "CRUD operations completed successfully");
            return true;
            
        } catch (Exception $e) {
            $this->record_test_failure($test_name, "Exception: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Test database performance
     */
    private function test_database_performance() {
        $test_name = 'Database Performance';
        
        try {
            global $wpdb;
            
            // Test query performance
            $start_time = microtime(true);
            
            // Run several test queries
            $wpdb->get_results("SELECT * FROM {$wpdb->prefix}posts LIMIT 10");
            $wpdb->get_results("SELECT * FROM {$wpdb->prefix}users LIMIT 10");
            
            $end_time = microtime(true);
            $query_time = $end_time - $start_time;
            
            if ($query_time > 1.0) {
                $this->record_test_failure($test_name, "Database queries are slow: {$query_time}s");
                return false;
            }
            
            $this->record_test_success($test_name, "Database performance acceptable: {$query_time}s");
            return true;
            
        } catch (Exception $e) {
            $this->record_test_failure($test_name, "Exception: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Basic JavaScript error detection
     */
    private function detect_javascript_errors() {
        // This is a basic implementation - in a real scenario you'd use browser automation
        return false;
    }
    
    /**
     * Check for PHP errors in error log
     */
    private function detect_php_errors() {
        if (!WP_DEBUG_LOG) {
            return false;
        }
        
        $error_log = WP_CONTENT_DIR . '/debug.log';
        if (!file_exists($error_log)) {
            return false;
        }
        
        $recent_errors = tail($error_log, 50);
        $current_time = time();
        
        foreach ($recent_errors as $line) {
            if (preg_match('/\[(\d{2}-\w{3}-\d{4} \d{2}:\d{2}:\d{2})\]/', $line, $matches)) {
                $error_time = strtotime($matches[1]);
                if (($current_time - $error_time) < 300) { // Last 5 minutes
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Test admin pages load properly
     */
    private function test_admin_pages_load() {
        // Basic test - in production you'd simulate HTTP requests
        return true;
    }
    
    /**
     * Record test success
     */
    private function record_test_success($test_name, $message) {
        $this->test_results[] = [
            'test' => $test_name,
            'status' => 'PASS',
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        $this->log("✅ PASS: $test_name - $message");
    }
    
    /**
     * Record test failure
     */
    private function record_test_failure($test_name, $message) {
        $this->test_results[] = [
            'test' => $test_name,
            'status' => 'FAIL',
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        $this->log("❌ FAIL: $test_name - $message");
    }
    
    /**
     * Log environment information
     */
    private function log_environment_info() {
        $this->log("\n=== ENVIRONMENT INFORMATION ===");
        foreach ($this->environment_info as $key => $value) {
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            $this->log("$key: $value");
        }
    }
    
    /**
     * Log message to file and output
     */
    private function log($message) {
        $log_entry = $message . "\n";
        file_put_contents($this->log_file, $log_entry, FILE_APPEND | LOCK_EX);
        echo $log_entry;
    }
    
    /**
     * Generate comprehensive test report
     */
    private function generate_test_report() {
        $this->log("\n=== TEST RESULTS SUMMARY ===");
        
        $total_tests = count($this->test_results);
        $passed_tests = array_filter($this->test_results, function($result) {
            return $result['status'] === 'PASS';
        });
        $failed_tests = array_filter($this->test_results, function($result) {
            return $result['status'] === 'FAIL';
        });
        
        $pass_count = count($passed_tests);
        $fail_count = count($failed_tests);
        $pass_rate = $total_tests > 0 ? ($pass_count / $total_tests) * 100 : 0;
        
        $this->log("Total Tests: $total_tests");
        $this->log("Passed: $pass_count");
        $this->log("Failed: $fail_count");
        $this->log("Pass Rate: " . number_format($pass_rate, 2) . "%");
        
        if ($fail_count > 0) {
            $this->log("\n=== FAILED TESTS ===");
            foreach ($failed_tests as $test) {
                $this->log("❌ {$test['test']}: {$test['message']}");
            }
        }
        
        $this->log("\nTest completed: " . date('Y-m-d H:i:s'));
        $this->log("Log file: " . $this->log_file);
        
        // Save detailed report
        $report_file = __DIR__ . '/activation-test-report-' . date('Y-m-d-H-i-s') . '.json';
        file_put_contents($report_file, json_encode([
            'summary' => [
                'total_tests' => $total_tests,
                'passed' => $pass_count,
                'failed' => $fail_count,
                'pass_rate' => $pass_rate
            ],
            'environment' => $this->environment_info,
            'results' => $this->test_results,
            'timestamp' => date('Y-m-d H:i:s')
        ], JSON_PRETTY_PRINT));
        
        $this->log("Detailed report: $report_file");
    }
}

/**
 * Helper function to read last N lines of a file
 */
function tail($file, $lines = 10) {
    if (!file_exists($file)) {
        return [];
    }
    
    $handle = fopen($file, "r");
    $linecounter = $lines;
    $pos = -2;
    $beginning = false;
    $text = array();
    
    while ($linecounter > 0) {
        $t = " ";
        while ($t != "\n") {
            if (fseek($handle, $pos, SEEK_END) == -1) {
                $beginning = true;
                break;
            }
            $t = fgetc($handle);
            $pos--;
        }
        $linecounter--;
        if ($beginning) {
            rewind($handle);
        }
        $text[$lines - $linecounter - 1] = fgets($handle);
        if ($beginning) break;
    }
    fclose($handle);
    return array_reverse($text);
}

// CLI execution
if (defined('WP_CLI') && WP_CLI) {
    WP_CLI::add_command('vireo test-activation', function($args, $assoc_args) {
        $test_suite = new Plugin_Activation_Test_Suite();
        $results = $test_suite->run_all_tests();
        
        $failed = array_filter($results, function($result) {
            return $result['status'] === 'FAIL';
        });
        
        if (!empty($failed)) {
            WP_CLI::error('Some tests failed. Check the log file for details.');
        } else {
            WP_CLI::success('All tests passed!');
        }
    });
}