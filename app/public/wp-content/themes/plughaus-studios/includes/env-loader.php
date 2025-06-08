<?php
/**
 * Environment Variable Loader
 * Loads variables from .env.local file for local development
 */

if (!defined('ABSPATH')) {
    exit;
}

function plughaus_load_env() {
    $env_file = ABSPATH . '../../../.env.local';
    
    if (!file_exists($env_file)) {
        return;
    }
    
    $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Parse key=value pairs
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Define as constant if not already defined
            if (!defined($key) && !empty($key)) {
                define($key, $value);
            }
        }
    }
}

// Load environment variables
plughaus_load_env();
?>