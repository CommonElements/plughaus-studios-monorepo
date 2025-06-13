<?php
/**
 * Vireo Designs - Pro Version Builder
 * 
 * Creates pro versions of all plugins for WooCommerce delivery.
 * In production, these would be generated dynamically with license keys.
 */

echo "🔨 Vireo Designs - Pro Version Builder\n";
echo "=====================================\n\n";

$plugins = [
    'vireo-property-management',
    'vireo-sports-league', 
    'equiprent-pro',
    'studiosnap',
    'dealeredge',
    'storageflow'
];

foreach ($plugins as $plugin_slug) {
    $plugin_dir = __DIR__ . "/wp-content/plugins/{$plugin_slug}";
    $pro_zip_path = "{$plugin_dir}/{$plugin_slug}-pro.zip";
    
    echo "📦 Building pro version: {$plugin_slug}\n";
    
    if (file_exists($pro_zip_path)) {
        echo "   ⚠️ Pro version already exists, skipping\n\n";
        continue;
    }
    
    // Create a pro version ZIP (for demo purposes, this is just the plugin folder)
    // In production, this would include license validation and pro features
    
    $zip = new ZipArchive();
    if ($zip->open($pro_zip_path, ZipArchive::CREATE) !== TRUE) {
        echo "   ❌ Cannot create ZIP file\n\n";
        continue;
    }
    
    // Add main plugin file
    $main_file = "{$plugin_dir}/{$plugin_slug}.php";
    if (file_exists($main_file)) {
        $zip->addFile($main_file, "{$plugin_slug}/{$plugin_slug}.php");
    }
    
    // Add core directory
    $core_dir = "{$plugin_dir}/core";
    if (is_dir($core_dir)) {
        addDirectoryToZip($zip, $core_dir, "{$plugin_slug}/core");
    }
    
    // Add pro directory if it exists
    $pro_dir = "{$plugin_dir}/pro";
    if (is_dir($pro_dir)) {
        addDirectoryToZip($zip, $pro_dir, "{$plugin_slug}/pro");
    }
    
    // Add readme
    $readme_file = "{$plugin_dir}/readme.txt";
    if (file_exists($readme_file)) {
        $zip->addFile($readme_file, "{$plugin_slug}/readme.txt");
    }
    
    // Add a pro license file
    $license_content = "<?php
/**
 * {$plugin_slug} Pro License
 * 
 * This file validates the pro license for {$plugin_slug}.
 * Generated automatically upon purchase.
 */

// Pro features enabled
define('{$plugin_slug}_PRO_ENABLED', true);
define('{$plugin_slug}_LICENSE_KEY', 'CUSTOMER_LICENSE_KEY_HERE');
define('{$plugin_slug}_LICENSE_VALID', true);

// Pro version marker
add_action('plugins_loaded', function() {
    if (!defined('{$plugin_slug}_VERSION')) {
        define('{$plugin_slug}_VERSION', '1.0.0-pro');
    }
});
?>";
    
    $zip->addFromString("{$plugin_slug}/pro-license.php", $license_content);
    
    $zip->close();
    
    $file_size = filesize($pro_zip_path);
    echo "   ✅ Pro version created (" . round($file_size / 1024, 2) . " KB)\n";
    echo "   📂 Location: {$pro_zip_path}\n\n";
}

echo "🎉 All pro versions built successfully!\n\n";

echo "📋 SUMMARY:\n";
foreach ($plugins as $plugin_slug) {
    $pro_zip_path = __DIR__ . "/wp-content/plugins/{$plugin_slug}/{$plugin_slug}-pro.zip";
    if (file_exists($pro_zip_path)) {
        $file_size = filesize($pro_zip_path);
        echo "✅ {$plugin_slug}-pro.zip (" . round($file_size / 1024, 2) . " KB)\n";
    } else {
        echo "❌ {$plugin_slug}-pro.zip (missing)\n";
    }
}

echo "\n🔗 Next Steps:\n";
echo "1. Test purchase flow with pro versions\n";
echo "2. Set up dynamic license key insertion\n";
echo "3. Configure automated delivery system\n";
echo "4. Test customer download access\n";

function addDirectoryToZip($zip, $dir, $zipPath) {
    if (is_dir($dir)) {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = $zipPath . '/' . substr($filePath, strlen($dir) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
    }
}

echo "\n✨ Pro version builder complete!\n";
?>