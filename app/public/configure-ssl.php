<?php
/**
 * Configure SSL for Local by Flywheel
 */

require_once('./wp-config.php');
require_once('./wp-load.php');

echo '<h1>🔒 SSL Configuration for Local by Flywheel</h1>';
echo '<p><strong>Date:</strong> ' . date('Y-m-d H:i:s') . '</p>';

echo '<h2>📋 Current Configuration</h2>';

$site_url = get_site_url();
$home_url = get_home_url();
$is_ssl = is_ssl();

echo '<p><strong>Site URL:</strong> ' . $site_url . '</p>';
echo '<p><strong>Home URL:</strong> ' . $home_url . '</p>';
echo '<p><strong>Current Request SSL:</strong> ' . ($is_ssl ? '✅ Yes' : '❌ No') . '</p>';

echo '<h2>🔧 SSL Configuration Steps</h2>';

// Step 1: Update WordPress URLs to HTTPS
echo '<h3>1. Update WordPress URLs</h3>';

$new_site_url = str_replace('http://', 'https://', $site_url);
$new_home_url = str_replace('http://', 'https://', $home_url);

if ($site_url !== $new_site_url) {
    update_option('siteurl', $new_site_url);
    echo '<p style="color: green;">✅ Updated Site URL to: ' . $new_site_url . '</p>';
} else {
    echo '<p style="color: blue;">ℹ️ Site URL already uses HTTPS</p>';
}

if ($home_url !== $new_home_url) {
    update_option('home', $new_home_url);
    echo '<p style="color: green;">✅ Updated Home URL to: ' . $new_home_url . '</p>';
} else {
    echo '<p style="color: blue;">ℹ️ Home URL already uses HTTPS</p>';
}

// Step 2: Force SSL for admin and logins
echo '<h3>2. Force SSL for Admin</h3>';

$wp_config_path = ABSPATH . 'wp-config.php';
$wp_config_content = file_get_contents($wp_config_path);

$ssl_constants = array(
    'FORCE_SSL_ADMIN' => 'true',
    'FORCE_SSL_LOGIN' => 'true'
);

$changes_made = false;
foreach ($ssl_constants as $constant => $value) {
    if (strpos($wp_config_content, $constant) === false) {
        $ssl_config = "\ndefine('{$constant}', {$value});\n";
        $wp_config_content = str_replace(
            "/* That's all, stop editing!",
            $ssl_config . "/* That's all, stop editing!",
            $wp_config_content
        );
        $changes_made = true;
        echo '<p style="color: green;">✅ Added ' . $constant . ' to wp-config.php</p>';
    } else {
        echo '<p style="color: blue;">ℹ️ ' . $constant . ' already configured</p>';
    }
}

if ($changes_made) {
    file_put_contents($wp_config_path, $wp_config_content);
}

// Step 3: Update WooCommerce settings for HTTPS
echo '<h3>3. Update WooCommerce for HTTPS</h3>';

if (class_exists('WooCommerce')) {
    // Force secure checkout
    update_option('woocommerce_force_ssl_checkout', 'yes');
    echo '<p style="color: green;">✅ Enabled WooCommerce Force SSL Checkout</p>';
    
    // Update store address if needed
    $store_address = get_option('woocommerce_store_address');
    echo '<p><strong>Store Address:</strong> ' . $store_address . '</p>';
} else {
    echo '<p style="color: orange;">⚠️ WooCommerce not active</p>';
}

// Step 4: Check for mixed content
echo '<h3>4. Mixed Content Check</h3>';

$uploads_url = wp_upload_dir()['baseurl'];
if (strpos($uploads_url, 'http://') !== false) {
    echo '<p style="color: orange;">⚠️ Uploads URL still uses HTTP: ' . $uploads_url . '</p>';
    echo '<p>This will be automatically updated when you visit the site via HTTPS</p>';
} else {
    echo '<p style="color: green;">✅ Uploads URL uses HTTPS: ' . $uploads_url . '</p>';
}

echo '<h2>📋 Local by Flywheel SSL Instructions</h2>';
echo '<div style="background: #f0f0f0; padding: 15px; border-left: 4px solid #0073aa;">';
echo '<h4>To enable SSL in Local by Flywheel:</h4>';
echo '<ol>';
echo '<li><strong>Open Local by Flywheel app</strong></li>';
echo '<li><strong>Select your "Vireo" site</strong></li>';
echo '<li><strong>Click "SSL" tab</strong> in the site settings</li>';
echo '<li><strong>Click "Trust Certificate"</strong> to install the SSL certificate</li>';
echo '<li><strong>Toggle "SSL" to ON</strong></li>';
echo '<li><strong>Your site will now be available at:</strong> https://vireo.local</li>';
echo '</ol>';
echo '</div>';

echo '<h2>🔄 After Enabling SSL</h2>';
echo '<ol>';
echo '<li><strong>Clear browser cache</strong> and reload the site</li>';
echo '<li><strong>Update site URLs in Local</strong> if needed</li>';
echo '<li><strong>Test Stripe checkout</strong> to ensure secure payment processing</li>';
echo '<li><strong>Check for mixed content warnings</strong> in browser console</li>';
echo '</ol>';

echo '<h2>✅ WordPress Configuration Complete</h2>';
echo '<p>WordPress is now configured for HTTPS. Complete the SSL setup in Local by Flywheel app.</p>';

echo '<p><em>SSL configuration completed at ' . date('Y-m-d H:i:s') . '</em></p>';
?>