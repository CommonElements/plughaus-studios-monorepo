<?php
/**
 * Secure Stripe Configuration for Vireo Designs
 * This file loads Stripe keys from environment variables or wp-config.php
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get Stripe configuration from secure sources
 */
function vireo_get_stripe_config() {
    // Try to get from environment variables first (most secure)
    $stripe_config = array(
        'publishable_key' => getenv('STRIPE_PUBLISHABLE_KEY') ?: '',
        'secret_key' => getenv('STRIPE_SECRET_KEY') ?: '',
        'account_id' => getenv('STRIPE_ACCOUNT_ID') ?: '',
        'mode' => 'live' // Using live keys from .env.local
    );
    
    // Fallback to wp-config.php constants if environment variables not available
    if (empty($stripe_config['publishable_key']) && defined('STRIPE_PUBLISHABLE_KEY')) {
        $stripe_config['publishable_key'] = STRIPE_PUBLISHABLE_KEY;
    }
    
    if (empty($stripe_config['secret_key']) && defined('STRIPE_SECRET_KEY')) {
        $stripe_config['secret_key'] = STRIPE_SECRET_KEY;
    }
    
    if (empty($stripe_config['account_id']) && defined('STRIPE_ACCOUNT_ID')) {
        $stripe_config['account_id'] = STRIPE_ACCOUNT_ID;
    }
    
    return $stripe_config;
}

/**
 * Configure WooCommerce Stripe Gateway
 */
function vireo_configure_stripe_gateway() {
    if (!class_exists('WooCommerce')) {
        return;
    }
    
    $stripe_config = vireo_get_stripe_config();
    
    if (empty($stripe_config['publishable_key']) || empty($stripe_config['secret_key'])) {
        return; // No valid Stripe configuration
    }
    
    // Update WooCommerce Stripe settings
    $stripe_settings = get_option('woocommerce_stripe_settings', array());
    
    // Enable Stripe gateway
    $stripe_settings['enabled'] = 'yes';
    $stripe_settings['title'] = 'Credit Card';
    $stripe_settings['description'] = 'Secure payment with Stripe';
    
    // Set API keys securely
    $stripe_settings['publishable_key'] = $stripe_config['publishable_key'];
    $stripe_settings['secret_key'] = $stripe_config['secret_key'];
    
    // Enable live mode (since we're using live keys)
    $stripe_settings['testmode'] = 'no';
    
    // Enable new checkout experience
    $stripe_settings['upe_checkout_experience_enabled'] = 'yes';
    
    // Security settings
    $stripe_settings['capture'] = 'yes';
    $stripe_settings['statement_descriptor'] = 'VIREO DESIGNS';
    
    // Save settings
    update_option('woocommerce_stripe_settings', $stripe_settings);
    
    // Also configure WooPayments if active
    $woopayments_settings = get_option('woocommerce_woocommerce_payments_settings', array());
    if (!empty($woopayments_settings)) {
        $woopayments_settings['enabled'] = 'yes';
        update_option('woocommerce_woocommerce_payments_settings', $woopayments_settings);
    }
}

// Hook into WordPress initialization
add_action('init', 'vireo_configure_stripe_gateway', 20);

/**
 * Add Stripe environment variables to wp-config.php if not already set
 */
function vireo_add_stripe_to_wp_config() {
    $wp_config_path = ABSPATH . 'wp-config.php';
    
    if (!file_exists($wp_config_path)) {
        return false;
    }
    
    $wp_config_content = file_get_contents($wp_config_path);
    
    // Check if Stripe constants are already defined
    if (strpos($wp_config_content, 'STRIPE_PUBLISHABLE_KEY') !== false) {
        return true; // Already configured
    }
    
    // Read Stripe keys from .env.local if available
    $env_path = dirname(ABSPATH, 2) . '/.env.local';
    if (!file_exists($env_path)) {
        return false;
    }
    
    $env_content = file_get_contents($env_path);
    preg_match('/STRIPE_PUBLISHABLE_KEY=(.+)/', $env_content, $pub_key_match);
    preg_match('/STRIPE_SECRET_KEY=(.+)/', $env_content, $secret_key_match);
    preg_match('/STRIPE_ACCOUNT_ID=(.+)/', $env_content, $account_id_match);
    
    if (empty($pub_key_match[1]) || empty($secret_key_match[1])) {
        return false;
    }
    
    // Add Stripe configuration to wp-config.php
    $stripe_config = "\n\n// Stripe Configuration for Vireo Designs\n";
    $stripe_config .= "define('STRIPE_PUBLISHABLE_KEY', '" . trim($pub_key_match[1]) . "');\n";
    $stripe_config .= "define('STRIPE_SECRET_KEY', '" . trim($secret_key_match[1]) . "');\n";
    
    if (!empty($account_id_match[1])) {
        $stripe_config .= "define('STRIPE_ACCOUNT_ID', '" . trim($account_id_match[1]) . "');\n";
    }
    
    // Insert before the "/* That's all, stop editing!" line
    $wp_config_content = str_replace(
        "/* That's all, stop editing!",
        $stripe_config . "\n/* That's all, stop editing!",
        $wp_config_content
    );
    
    return file_put_contents($wp_config_path, $wp_config_content);
}

// Only add to wp-config on admin pages
if (is_admin()) {
    add_action('admin_init', 'vireo_add_stripe_to_wp_config');
}
?>