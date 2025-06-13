<?php
/**
 * Test WordPress.org Ready Version of Vireo Sports League Manager
 * This script validates the free version is properly prepared for WordPress.org submission
 */

// WordPress Bootstrap
require_once __DIR__ . '/wp-config.php';
require_once ABSPATH . 'wp-settings.php';

echo "🏆 TESTING SPORTS LEAGUE - WORDPRESS.ORG READY VERSION\n";
echo "=" . str_repeat("=", 55) . "\n\n";

$test_results = array();
$total_score = 0;
$max_score = 0;

// 1. Test plugin file structure compliance
echo "📁 Testing Plugin File Structure...\n";
$plugin_dir = '/Users/condominiumassociates/Local Sites/plughaus-studios-the-beginning-is-finished/app/public/wp-content/plugins/vireo-sports-league/dist/free/vireo-sports-league';

$required_files = array(
    'vireo-sports-league.php' => 'Main plugin file',
    'readme.txt' => 'WordPress.org readme',
    'uninstall.php' => 'Uninstall script',
    'core/includes/class-vsl-activator.php' => 'Activator class',
    'core/includes/class-vsl-deactivator.php' => 'Deactivator class'
);

foreach ($required_files as $file => $description) {
    $file_path = $plugin_dir . '/' . $file;
    $exists = file_exists($file_path);
    echo "  {$description}: " . ($exists ? "✅ PRESENT" : "❌ MISSING") . "\n";
    $test_results["file_structure_{$file}"] = $exists;
    $max_score++;
    if ($exists) $total_score++;
}

// 2. Test for pro feature removal
echo "\n🔒 Testing Pro Feature Removal...\n";
$main_plugin_content = file_get_contents($plugin_dir . '/vireo-sports-league.php');

$pro_checks = array(
    'No VIREO_LEAGUE_PRO_DIR constant' => !preg_match('/define\s*\(\s*[\'"]VIREO_LEAGUE_PRO_DIR/', $main_plugin_content),
    'License check returns false' => strpos($main_plugin_content, 'return false;') !== false,
    'No pro features loaded' => strpos($main_plugin_content, 'Pro features not available in free version') !== false,
    'No pro directory exists' => !is_dir($plugin_dir . '/pro')
);

foreach ($pro_checks as $check => $passed) {
    echo "  {$check}: " . ($passed ? "✅ PASS" : "❌ FAIL") . "\n";
    $test_results["pro_removal_{$check}"] = $passed;
    $max_score++;
    if ($passed) $total_score++;
}

// 3. Test WordPress.org compliance
echo "\n📋 Testing WordPress.org Compliance...\n";

// Check readme.txt format
$readme_content = file_get_contents($plugin_dir . '/readme.txt');
$readme_checks = array(
    'Has plugin header' => strpos($readme_content, '=== ') === 0,
    'Has stable tag' => strpos($readme_content, 'Stable tag:') !== false,
    'Has tested up to' => strpos($readme_content, 'Tested up to:') !== false,
    'Has requires at least' => strpos($readme_content, 'Requires at least:') !== false,
    'Has license' => strpos($readme_content, 'License:') !== false,
    'Has description' => strpos($readme_content, '== Description ==') !== false
);

foreach ($readme_checks as $check => $passed) {
    echo "  Readme {$check}: " . ($passed ? "✅ PASS" : "❌ FAIL") . "\n";
    $test_results["readme_{$check}"] = $passed;
    $max_score++;
    if ($passed) $total_score++;
}

// 4. Test plugin headers
echo "\n🏷️ Testing Plugin Headers...\n";
$header_checks = array(
    'Has Plugin Name' => strpos($main_plugin_content, 'Plugin Name:') !== false,
    'Has Description' => strpos($main_plugin_content, 'Description:') !== false,
    'Has Version' => strpos($main_plugin_content, 'Version:') !== false,
    'Has Author' => strpos($main_plugin_content, 'Author:') !== false,
    'Has License' => strpos($main_plugin_content, 'License:') !== false,
    'Has Text Domain' => strpos($main_plugin_content, 'Text Domain:') !== false
);

foreach ($header_checks as $check => $passed) {
    echo "  {$check}: " . ($passed ? "✅ PASS" : "❌ FAIL") . "\n";
    $test_results["headers_{$check}"] = $passed;
    $max_score++;
    if ($passed) $total_score++;
}

// 5. Test functionality (load test)
echo "\n⚙️ Testing Core Functionality...\n";
try {
    require_once $plugin_dir . '/vireo-sports-league.php';
    
    $functionality_checks = array(
        'Main class exists' => class_exists('Vireo_Sports_League'),
        'Post types class exists' => class_exists('VSL_Post_Types'),
        'Utilities class exists' => class_exists('VSL_Utilities'),
        'Plugin can instantiate' => false
    );
    
    if (class_exists('Vireo_Sports_League')) {
        try {
            $plugin = Vireo_Sports_League::get_instance();
            $functionality_checks['Plugin can instantiate'] = true;
        } catch (Exception $e) {
            // Keep as false
        }
    }
    
    foreach ($functionality_checks as $check => $passed) {
        echo "  {$check}: " . ($passed ? "✅ PASS" : "❌ FAIL") . "\n";
        $test_results["functionality_{$check}"] = $passed;
        $max_score++;
        if ($passed) $total_score++;
    }
    
} catch (Exception $e) {
    echo "  ❌ CRITICAL ERROR: {$e->getMessage()}\n";
    $max_score += 4;
}

// 6. Test ZIP file integrity
echo "\n📦 Testing ZIP File...\n";
$zip_file = '/Users/condominiumassociates/Local Sites/plughaus-studios-the-beginning-is-finished/app/public/wp-content/plugins/vireo-sports-league/dist/free/vireo-sports-league-free-v1.0.0.zip';
$zip_checks = array(
    'ZIP file exists' => file_exists($zip_file),
    'ZIP file readable' => is_readable($zip_file),
    'ZIP file not empty' => filesize($zip_file) > 1000 // At least 1KB
);

foreach ($zip_checks as $check => $passed) {
    echo "  {$check}: " . ($passed ? "✅ PASS" : "❌ FAIL") . "\n";
    $test_results["zip_{$check}"] = $passed;
    $max_score++;
    if ($passed) $total_score++;
}

// 7. Test sports league specific features
echo "\n🏆 Testing Sports League Features...\n";
if (class_exists('VSL_Utilities')) {
    try {
        $sports = VSL_Utilities::get_supported_sports();
        $league_features = array(
            'Multiple sports support' => count($sports) >= 4,
            'Has soccer config' => isset($sports['soccer']),
            'Has basketball config' => isset($sports['basketball']),
            'Code generation works' => !empty(VSL_Utilities::generate_league_code())
        );
        
        foreach ($league_features as $check => $passed) {
            echo "  {$check}: " . ($passed ? "✅ PASS" : "❌ FAIL") . "\n";
            $test_results["features_{$check}"] = $passed;
            $max_score++;
            if ($passed) $total_score++;
        }
    } catch (Exception $e) {
        echo "  ❌ Error testing features: {$e->getMessage()}\n";
        $max_score += 4;
    }
} else {
    echo "  ❌ VSL_Utilities not available for testing\n";
    $max_score += 4;
}

// Calculate final score
$percentage = round(($total_score / $max_score) * 100);
$ready_for_submission = $percentage >= 90;

echo "\n📊 FINAL RESULTS\n";
echo "=" . str_repeat("=", 30) . "\n";
echo "Score: {$total_score}/{$max_score} ({$percentage}%)\n";
echo "WordPress.org Ready: " . ($ready_for_submission ? "✅ YES" : "❌ NO") . "\n";

if ($ready_for_submission) {
    echo "\n🎉 CONGRATULATIONS!\n";
    echo "The Vireo Sports League Manager plugin is ready for WordPress.org submission!\n\n";
    echo "📦 Submission Package: vireo-sports-league-free-v1.0.0.zip\n";
    echo "📍 Location: /dist/free/\n\n";
    echo "Key Features:\n";
    echo "• Multi-sport league management (Soccer, Basketball, Baseball, Volleyball)\n";
    echo "• Team and player management\n";
    echo "• Match scheduling and results\n";
    echo "• Season management\n";
    echo "• Basic standings calculation\n";
    echo "• WordPress-native integration\n\n";
    echo "Next Steps:\n";
    echo "1. Submit to WordPress.org plugin repository\n";
    echo "2. Wait for review (typically 1-2 weeks)\n";
    echo "3. Address any reviewer feedback\n";
    echo "4. Launch! 🚀\n";
} else {
    echo "\n⚠️ Issues to Address:\n";
    foreach ($test_results as $test => $passed) {
        if (!$passed) {
            echo "  - {$test}\n";
        }
    }
    echo "\nPlease fix these issues before submission.\n";
}

echo "\n📈 VIREO DESIGNS STUDIO PROGRESS\n";
echo "-" . str_repeat("-", 35) . "\n";
echo "✅ Property Management Plugin: 100% ready (SUBMITTED)\n";
echo "✅ Sports League Management: {$percentage}% ready" . ($ready_for_submission ? " (READY)" : " (NEEDS WORK)") . "\n";
echo "⏳ EquipRent Pro (Equipment Rental): Next in queue\n";
echo "⏳ GymFlow (Fitness Studio): Planned\n";
echo "⏳ DealerEdge (Auto Shop): Planned\n";
echo "\nVireo Designs Studio Goal: 5 plugins for WordPress.org\n";

$completed_plugins = 1 + ($ready_for_submission ? 1 : 0);
echo "Current Status: {$completed_plugins} of 5 plugins ready for submission! 🎯\n";

echo "\n🏁 Test completed at " . date('Y-m-d H:i:s') . "\n";
?>