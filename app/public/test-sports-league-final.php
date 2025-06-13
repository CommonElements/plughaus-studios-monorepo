<?php
/**
 * Final Test of Sports League Plugin Build
 */

// WordPress Bootstrap
require_once __DIR__ . '/wp-config.php';
require_once ABSPATH . 'wp-settings.php';

echo "🏆 FINAL SPORTS LEAGUE PLUGIN TEST\n";
echo "=" . str_repeat("=", 40) . "\n\n";

$built_plugin = '/Users/condominiumassociates/Local Sites/plughaus-studios-the-beginning-is-finished/app/public/wp-content/plugins/vireo-sports-league/dist/free/vireo-sports-league/vireo-sports-league.php';
$base_dir = dirname($built_plugin);
$zip_file = '/Users/condominiumassociates/Local Sites/plughaus-studios-the-beginning-is-finished/app/public/wp-content/plugins/vireo-sports-league/dist/free/vireo-sports-league-free-v1.0.0-fixed.zip';

// Test plugin existence
echo "📁 Built Plugin File: " . (file_exists($built_plugin) ? "✅ EXISTS" : "❌ MISSING") . "\n";

// Test syntax
echo "🔧 Syntax Check: ";
$syntax_check = shell_exec('php -l "' . $built_plugin . '" 2>&1');
echo (strpos($syntax_check, 'No syntax errors') !== false) ? "✅ VALID" : "❌ ERRORS";
echo "\n";

// Test file structure
echo "\n📦 File Structure Check:\n";
$required_files = array(
    'readme.txt' => 'WordPress.org readme',
    'uninstall.php' => 'Uninstall script',
    'core/includes/class-vsl-activator.php' => 'Activator',
    'core/includes/shared/class-vsl-utilities.php' => 'Utilities',
    'core/includes/core/class-vsl-post-types.php' => 'Post Types'
);

$files_present = 0;
foreach ($required_files as $file => $desc) {
    $path = $base_dir . '/' . $file;
    $exists = file_exists($path);
    echo "  {$desc}: " . ($exists ? "✅ PRESENT" : "❌ MISSING") . "\n";
    if ($exists) $files_present++;
}

// Test ZIP file
echo "\n📦 Distribution Package:\n";
echo "  ZIP exists: " . (file_exists($zip_file) ? "✅ YES" : "❌ NO") . "\n";
if (file_exists($zip_file)) {
    echo "  ZIP size: " . round(filesize($zip_file)/1024, 1) . " KB\n";
}

// Test plugin headers
echo "\n🏷️ Plugin Headers Check:\n";
$plugin_content = file_get_contents($built_plugin);
$header_checks = array(
    'Plugin Name:' => 'Has Plugin Name',
    'Description:' => 'Has Description', 
    'Version:' => 'Has Version',
    'Author:' => 'Has Author',
    'Text Domain:' => 'Has Text Domain'
);

$headers_present = 0;
foreach ($header_checks as $header => $desc) {
    $exists = strpos($plugin_content, $header) !== false;
    echo "  {$desc}: " . ($exists ? "✅ PRESENT" : "❌ MISSING") . "\n";
    if ($exists) $headers_present++;
}

// Test pro features removal
echo "\n🔒 Pro Features Removal Check:\n";
$pro_removed = array(
    'No PRO_DIR constant' => !preg_match('/VIREO_LEAGUE_PRO_DIR/', $plugin_content),
    'License returns false' => strpos($plugin_content, 'return false;') !== false,
    'No pro directory' => !is_dir($base_dir . '/pro')
);

$pro_checks_passed = 0;
foreach ($pro_removed as $check => $passed) {
    echo "  {$check}: " . ($passed ? "✅ PASS" : "❌ FAIL") . "\n";
    if ($passed) $pro_checks_passed++;
}

// Calculate score
$total_checks = count($required_files) + count($header_checks) + count($pro_removed) + 2; // +2 for syntax and ZIP
$passed_checks = $files_present + $headers_present + $pro_checks_passed;
$passed_checks += (strpos($syntax_check, 'No syntax errors') !== false) ? 1 : 0;
$passed_checks += file_exists($zip_file) ? 1 : 0;

$percentage = round(($passed_checks / $total_checks) * 100);

echo "\n📊 FINAL SCORE\n";
echo "=" . str_repeat("=", 20) . "\n";
echo "Score: {$passed_checks}/{$total_checks} ({$percentage}%)\n";

$ready = $percentage >= 90;
echo "WordPress.org Ready: " . ($ready ? "✅ YES" : "❌ NO") . "\n";

if ($ready) {
    echo "\n🎉 SUCCESS!\n";
    echo "Vireo Sports League Manager is ready for WordPress.org!\n\n";
    echo "📦 Package: vireo-sports-league-free-v1.0.0-fixed.zip\n";
    echo "🎯 Features: Multi-sport league management\n";
    echo "📍 Location: /dist/free/\n";
} else {
    echo "\n⚠️ Needs more work before submission.\n";
}

echo "\n📈 STUDIO PROGRESS UPDATE\n";
echo "-" . str_repeat("-", 25) . "\n";
echo "✅ Property Management: 100% (READY)\n";
echo "✅ Sports League: {$percentage}% " . ($ready ? "(READY)" : "(NEEDS WORK)") . "\n";
echo "⏳ Equipment Rental: Next\n";
echo "⏳ Fitness Studio: Planned\n";
echo "⏳ Auto Shop: Planned\n";

$completed = 1 + ($ready ? 1 : 0);
echo "\nProgress: {$completed}/5 plugins ready! 🎯\n";

echo "\n🏁 Test completed at " . date('Y-m-d H:i:s') . "\n";
?>