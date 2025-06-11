<?php
/**
 * Simple Plugin Activation Test Runner
 * 
 * Execute this file directly to test plugin activation
 * Can be run from browser or command line
 */

// Determine WordPress root path
$wp_root = dirname(dirname(dirname(__FILE__))) . '/app/public';
if (!file_exists($wp_root . '/wp-config.php')) {
    die("WordPress installation not found at: $wp_root\n");
}

// Load WordPress
define('WP_USE_THEMES', false);
require_once($wp_root . '/wp-config.php');
require_once($wp_root . '/wp-load.php');

// Set up test environment
if (!defined('WP_ADMIN')) {
    define('WP_ADMIN', true);
}

// Load our test suite
require_once(__DIR__ . '/plugin-activation-test.php');

// HTML output for browser
$is_browser = !empty($_SERVER['HTTP_HOST']);

if ($is_browser) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Vireo Designs Plugin Activation Tests</title>
        <style>
            body { font-family: monospace; margin: 20px; background: #f5f5f5; }
            .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
            .test-pass { color: #28a745; }
            .test-fail { color: #dc3545; }
            .test-header { background: #007cba; color: white; padding: 10px; margin: -20px -20px 20px -20px; }
            pre { background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto; }
            .button { 
                background: #007cba; 
                color: white; 
                padding: 10px 20px; 
                border: none; 
                border-radius: 4px; 
                cursor: pointer; 
                text-decoration: none;
                display: inline-block;
                margin: 10px 5px 0 0;
            }
            .button:hover { background: #005a87; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="test-header">
                <h1>🧪 Vireo Designs Plugin Activation Tests</h1>
                <p>Comprehensive testing suite for plugin activation and functionality</p>
            </div>
            
            <?php if (!isset($_GET['run'])): ?>
                <p>This test suite will verify that both the Property Management and Sports League Management plugins activate correctly and function properly.</p>
                
                <h3>What will be tested:</h3>
                <ul>
                    <li>✅ Plugin activation without errors</li>
                    <li>✅ Database table creation</li>
                    <li>✅ Admin menu registration</li>
                    <li>✅ Plugin conflict detection</li>
                    <li>✅ Basic CRUD operations</li>
                    <li>✅ Performance benchmarks</li>
                </ul>
                
                <a href="?run=1" class="button">🚀 Run Tests</a>
                <a href="?run=1&verbose=1" class="button">🔍 Run Tests (Verbose)</a>
            <?php else: ?>
                <pre>
    <?php
    endif;
}

// Run tests if requested
if ($is_browser && isset($_GET['run']) || !$is_browser) {
    echo "Initializing test suite...\n";
    
    // Capture output
    ob_start();
    
    try {
        $test_suite = new Plugin_Activation_Test_Suite();
        $results = $test_suite->run_all_tests();
        
        $output = ob_get_clean();
        
        // Format output for browser
        if ($is_browser) {
            echo htmlspecialchars($output);
            echo "\n</pre>\n";
            
            // Show summary
            $total = count($results);
            $passed = count(array_filter($results, function($r) { return $r['status'] === 'PASS'; }));
            $failed = $total - $passed;
            
            echo "<div style='margin-top: 20px; padding: 15px; border-radius: 4px; ";
            echo $failed > 0 ? "background: #f8d7da; border: 1px solid #f5c6cb;'>" : "background: #d4edda; border: 1px solid #c3e6cb;'>";
            echo "<h3>Test Summary:</h3>";
            echo "<p><strong>Total Tests:</strong> $total</p>";
            echo "<p><strong>Passed:</strong> <span class='test-pass'>$passed</span></p>";
            echo "<p><strong>Failed:</strong> <span class='test-fail'>$failed</span></p>";
            echo "<p><strong>Success Rate:</strong> " . ($total > 0 ? round(($passed / $total) * 100, 2) : 0) . "%</p>";
            echo "</div>";
            
            if ($failed > 0) {
                echo "<div style='margin-top: 15px; padding: 15px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px;'>";
                echo "<h4>⚠️ Action Required:</h4>";
                echo "<p>Some tests failed. Please check the detailed log above and resolve any issues before deploying to production.</p>";
                echo "</div>";
            } else {
                echo "<div style='margin-top: 15px; padding: 15px; background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 4px;'>";
                echo "<h4>🎉 All Tests Passed!</h4>";
                echo "<p>Your plugins are ready for production deployment.</p>";
                echo "</div>";
            }
            
            echo "<a href='?' class='button'>🔄 Run Again</a>";
            echo "<a href='../unit/' class='button'>📊 Unit Tests</a>";
            
        } else {
            echo $output;
        }
        
    } catch (Exception $e) {
        $error_output = ob_get_clean();
        echo "ERROR: " . $e->getMessage() . "\n";
        echo $error_output;
        exit(1);
    }
}

if ($is_browser && isset($_GET['run'])) {
    echo "</div></body></html>";
}
?>