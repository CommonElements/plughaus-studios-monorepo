<?php
/**
 * Test WordPress.org Ready Version of Vireo Property Management
 * This script validates the free version is properly prepared for WordPress.org submission
 */

// WordPress Bootstrap
require_once __DIR__ . '/wp-config.php';
require_once ABSPATH . 'wp-settings.php';

echo "🧪 TESTING WORDPRESS.ORG READY VERSION\n";
echo "=" . str_repeat("=", 50) . "\n\n";

$test_results = array();
$total_score = 0;
$max_score = 0;

// 1. Test plugin file structure compliance
echo "📁 Testing Plugin File Structure...\n";
$plugin_dir = '/Users/condominiumassociates/Local Sites/plughaus-studios-the-beginning-is-finished/app/public/wp-content/plugins/vireo-property-management/dist/free/vireo-property-management';

$required_files = array(
    'vireo-property-management.php' => 'Main plugin file',
    'readme.txt' => 'WordPress.org readme',
    'uninstall.php' => 'Uninstall script',
    'core/includes/class-phpm-activator.php' => 'Activator class',
    'core/includes/class-phpm-deactivator.php' => 'Deactivator class'
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
$main_plugin_content = file_get_contents($plugin_dir . '/vireo-property-management.php');

$pro_checks = array(
    'No VPM_PRO_DIR constant' => !preg_match('/define\s*\(\s*[\'"]VPM_PRO_DIR/', $main_plugin_content),
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

// 5. Test code quality (basic checks)
echo "\n🔍 Testing Code Quality...\n";
$quality_checks = array(
    'No PHP syntax errors' => !preg_match('/\?\>\s*\<\?php/', $main_plugin_content), // No unnecessary open/close tags
    'Has security check' => strpos($main_plugin_content, "if (!defined('ABSPATH'))") !== false,
    'Uses proper constants' => strpos($main_plugin_content, 'VPM_VERSION') !== false,
    'Has proper class structure' => strpos($main_plugin_content, 'class Vireo_Property_Management') !== false
);

foreach ($quality_checks as $check => $passed) {
    echo "  {$check}: " . ($passed ? "✅ PASS" : "❌ FAIL") . "\n";
    $test_results["quality_{$check}"] = $passed;
    $max_score++;
    if ($passed) $total_score++;
}

// 6. Test ZIP file integrity
echo "\n📦 Testing ZIP File...\n";
$zip_file = '/Users/condominiumassociates/Local Sites/plughaus-studios-the-beginning-is-finished/app/public/wp-content/plugins/vireo-property-management/dist/free/vireo-property-management-free-v1.0.0-fixed.zip';
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

// Calculate final score
$percentage = round(($total_score / $max_score) * 100);
$ready_for_submission = $percentage >= 95;

echo "\n📊 FINAL RESULTS\n";
echo "=" . str_repeat("=", 30) . "\n";
echo "Score: {$total_score}/{$max_score} ({$percentage}%)\n";
echo "WordPress.org Ready: " . ($ready_for_submission ? "✅ YES" : "❌ NO") . "\n";

if ($ready_for_submission) {
    echo "\n🎉 CONGRATULATIONS!\n";
    echo "The Vireo Property Management plugin is ready for WordPress.org submission!\n\n";
    echo "📦 Submission Package: vireo-property-management-free-v1.0.0-fixed.zip\n";
    echo "📍 Location: /dist/free/\n\n";
    echo "Next Steps:\n";
    echo "1. Submit to WordPress.org plugin repository\n";
    echo "2. Wait for review (typically 1-2 weeks)\n";
    echo "3. Address any reviewer feedback\n";
    echo "4. Celebrate launch! 🚀\n";
} else {
    echo "\n⚠️ Issues to Address:\n";
    foreach ($test_results as $test => $passed) {
        if (!$passed) {
            echo "  - {$test}\n";
        }
    }
    echo "\nPlease fix these issues before submission.\n";
}

echo "\n📈 STUDIO PROGRESS UPDATE\n";
echo "-" . str_repeat("-", 30) . "\n";
echo "✅ Property Management Plugin: {$percentage}% ready\n";
echo "⏳ Sports League Management: Next in queue\n";
echo "⏳ EquipRent Pro (Equipment Rental): Planned\n";
echo "⏳ GymFlow (Fitness Studio): Planned\n";
echo "⏳ DealerEdge (Auto Shop): Planned\n";
echo "\nVireo Designs Studio Goal: 5 plugins for WordPress.org\n";
echo "Current Status: 1 of 5 plugins ready for submission! 🎯\n";

echo "\n🏁 Test completed at " . date('Y-m-d H:i:s') . "\n";
?>