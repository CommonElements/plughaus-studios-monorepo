<?php
/**
 * Emergency script to disable custom plugins and restore site functionality
 */

// Temporarily move our custom plugins to disable them
$custom_plugins = [
    'studiosnap',
    'dearedge', 
    'marina-manager',
    'storageflow',
    'vireo-property-management',
    'vireo-sports-league'
];

$plugins_dir = __DIR__ . '/plugins/';
$disabled_dir = __DIR__ . '/plugins-disabled/';

// Create disabled directory if it doesn't exist
if (!file_exists($disabled_dir)) {
    mkdir($disabled_dir, 0755, true);
}

foreach ($custom_plugins as $plugin) {
    $source = $plugins_dir . $plugin;
    $destination = $disabled_dir . $plugin;
    
    if (file_exists($source) && is_dir($source)) {
        if (rename($source, $destination)) {
            echo "Disabled: $plugin\n";
        } else {
            echo "Failed to disable: $plugin\n";
        }
    }
}

echo "Custom plugins have been disabled. Please check if the site loads now.\n";
echo "Re-enable plugins one by one to identify the problematic one.\n";