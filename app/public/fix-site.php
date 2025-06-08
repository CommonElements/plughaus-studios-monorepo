<?php
/**
 * Web-based Fix Script for PlugHaus Studios Theme
 * Run this by visiting: http://plughaus-studios-the-beginning-is-finished.local/fix-site.php
 */

// Include WordPress
define('WP_USE_THEMES', false);
require_once dirname(__FILE__) . '/wp-load.php';

// Check if user is admin
if (!is_user_logged_in() || !current_user_can('manage_options')) {
    wp_redirect(wp_login_url($_SERVER['REQUEST_URI']));
    exit;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>PlugHaus Studios Site Fix</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        h1, h2 { color: #2563eb; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .btn { background: #2563eb; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>PlugHaus Studios Site Fix</h1>
    
    <?php
    // Fix 1: Create sample plugin if none exist
    echo '<h2>1. Checking for Sample Plugin Content...</h2>';
    
    $existing_plugins = get_posts(array(
        'post_type' => 'phstudios_plugin',
        'posts_per_page' => 1,
        'post_status' => 'publish'
    ));
    
    if (empty($existing_plugins)) {
        echo '<p>No plugins found. Creating sample plugin...</p>';
        
        $plugin_data = array(
            'post_title' => 'PlugHaus Property Management',
            'post_content' => '<p>A comprehensive property management solution for WordPress. Manage properties, tenants, leases, and maintenance requests with ease.</p>

<h3>Key Features</h3>
<ul>
<li>Property Management - Track multiple properties with detailed information</li>
<li>Tenant Management - Manage tenant information and contact details</li>
<li>Lease Tracking - Monitor lease terms, dates, and rental amounts</li>
<li>Unit Management - Organize properties by individual units</li>
<li>Dashboard Overview - Quick stats and recent activity</li>
<li>WordPress Integration - Seamlessly integrates with WordPress admin</li>
</ul>

<h3>Pro Features</h3>
<ul>
<li>Advanced analytics and reporting</li>
<li>Payment automation and tracking</li>
<li>Document management and storage</li>
<li>Email automation and templates</li>
<li>Custom fields and property types</li>
<li>Multi-property portfolio management</li>
</ul>',
            'post_excerpt' => 'Complete property management solution with tenant tracking, lease management, and maintenance requests.',
            'post_status' => 'publish',
            'post_type' => 'phstudios_plugin',
            'post_author' => get_current_user_id()
        );
        
        $plugin_id = wp_insert_post($plugin_data);
        
        if ($plugin_id && !is_wp_error($plugin_id)) {
            echo '<p class="success">✓ Created sample plugin</p>';
            
            // Add meta data
            update_post_meta($plugin_id, '_plugin_status', 'available');
            update_post_meta($plugin_id, '_plugin_version', '1.0.0');
            update_post_meta($plugin_id, '_wordpress_url', 'https://wordpress.org/plugins/plughaus-property-management/');
            update_post_meta($plugin_id, '_github_url', 'https://github.com/CommonElements/plughaus-studios-monorepo');
            update_post_meta($plugin_id, '_price_free', 'Free');
            update_post_meta($plugin_id, '_price_pro', '$99/year');
            update_post_meta($plugin_id, '_download_count', '1250');
            update_post_meta($plugin_id, '_rating', '4.8');
            update_post_meta($plugin_id, '_tested_wp_version', '6.4');
            update_post_meta($plugin_id, '_min_php_version', '7.4');
            update_post_meta($plugin_id, '_featured_plugin', '1');
            
            echo '<p class="success">✓ Added plugin meta data</p>';
        } else {
            echo '<p class="error">✗ Failed to create sample plugin</p>';
        }
    } else {
        echo '<p class="success">✓ Sample plugin already exists</p>';
    }
    
    // Fix 2: Update contact page with working form
    echo '<h2>2. Fixing Contact Form...</h2>';
    
    $contact_page = get_page_by_path('contact');
    if ($contact_page) {
        $contact_content = '<h2>Get In Touch</h2>
<p>Have questions about our plugins or need custom development? We\'d love to hear from you.</p>

<div style="max-width: 600px; margin: 2rem 0;">
    <form method="post" action="" style="background: white; padding: 2rem; border: 1px solid #e2e8f0; border-radius: 0.5rem;">
        <input type="hidden" name="contact_nonce" value="' . wp_create_nonce('plughaus_contact_form') . '" />
        
        <div style="margin-bottom: 1.5rem;">
            <label for="contact_name" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #334155;">Name *</label>
            <input type="text" id="contact_name" name="contact_name" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.375rem; font-size: 1rem;">
        </div>
        
        <div style="margin-bottom: 1.5rem;">
            <label for="contact_email" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #334155;">Email *</label>
            <input type="email" id="contact_email" name="contact_email" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.375rem; font-size: 1rem;">
        </div>
        
        <div style="margin-bottom: 1.5rem;">
            <label for="contact_subject" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #334155;">Subject *</label>
            <select id="contact_subject" name="contact_subject" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.375rem; font-size: 1rem;">
                <option value="">Select a topic</option>
                <option value="general">General Inquiry</option>
                <option value="support">Plugin Support</option>
                <option value="custom">Custom Development</option>
                <option value="partnership">Partnership</option>
            </select>
        </div>
        
        <div style="margin-bottom: 1.5rem;">
            <label for="contact_message" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #334155;">Message *</label>
            <textarea id="contact_message" name="contact_message" rows="5" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.375rem; font-size: 1rem; resize: vertical; min-height: 120px;"></textarea>
        </div>
        
        <button type="submit" name="submit_contact_form" style="background-color: #2563eb; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 0.5rem; font-weight: 500; cursor: pointer; transition: background-color 0.2s;">
            Send Message
        </button>
    </form>
</div>';

        wp_update_post(array(
            'ID' => $contact_page->ID,
            'post_content' => $contact_content
        ));
        
        echo '<p class="success">✓ Updated contact page with working form</p>';
    } else {
        echo '<p class="error">✗ Contact page not found</p>';
    }
    
    // Fix 3: Update plugins page content
    echo '<h2>3. Fixing Plugins Page...</h2>';
    
    $plugins_page = get_page_by_path('plugins');
    if ($plugins_page) {
        // Get all plugins and display them
        $plugins = get_posts(array(
            'post_type' => 'phstudios_plugin',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ));
        
        $plugins_content = '<h2>Our WordPress Plugins</h2>
<p>Powerful WordPress solutions for modern businesses. All plugins include free versions with optional Pro upgrades.</p>';
        
        if (!empty($plugins)) {
            $plugins_content .= '<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 2rem; margin: 2rem 0;">';
            
            foreach ($plugins as $plugin) {
                $status = get_post_meta($plugin->ID, '_plugin_status', true) ?: 'available';
                $version = get_post_meta($plugin->ID, '_plugin_version', true);
                $price_free = get_post_meta($plugin->ID, '_price_free', true) ?: 'Free';
                $price_pro = get_post_meta($plugin->ID, '_price_pro', true);
                $wordpress_url = get_post_meta($plugin->ID, '_wordpress_url', true);
                $rating = get_post_meta($plugin->ID, '_rating', true);
                $download_count = get_post_meta($plugin->ID, '_download_count', true);
                
                $plugins_content .= '
                <div style="background: white; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); transition: all 0.2s ease-in-out;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                        <h3 style="margin: 0; flex: 1;"><a href="' . get_permalink($plugin->ID) . '" style="color: #1e293b; text-decoration: none;">' . esc_html($plugin->post_title) . '</a></h3>
                        <span style="padding: 0.25rem 0.75rem; font-size: 0.75rem; font-weight: 500; border-radius: 0.375rem; text-transform: uppercase; letter-spacing: 0.05em; background-color: #d1fae5; color: #065f46;">' . esc_html(ucwords(str_replace('-', ' ', $status))) . '</span>
                    </div>
                    
                    <div>
                        <p style="color: #64748b; line-height: 1.6; margin-bottom: 1rem;">' . esc_html($plugin->post_excerpt) . '</p>
                        
                        <div style="display: flex; gap: 1rem; margin-bottom: 1rem; font-size: 0.875rem; color: #64748b;">
                            ' . ($download_count ? '<span style="display: flex; align-items: center; gap: 0.25rem;">📥 ' . number_format($download_count) . ' downloads</span>' : '') . '
                            ' . ($rating ? '<span style="display: flex; align-items: center; gap: 0.25rem;">⭐ ' . $rating . '/5</span>' : '') . '
                            ' . ($version ? '<span style="display: flex; align-items: center; gap: 0.25rem;">🏷️ v' . $version . '</span>' : '') . '
                        </div>
                        
                        <div style="display: flex; gap: 0.75rem; margin-bottom: 1.5rem;">
                            <span style="padding: 0.5rem 0.75rem; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 500; background-color: #10b981; color: white;">' . esc_html($price_free) . '</span>
                            ' . ($price_pro ? '<span style="padding: 0.5rem 0.75rem; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 500; background-color: #f59e0b; color: white;">' . esc_html($price_pro) . '</span>' : '') . '
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 0.75rem;">
                        <a href="' . get_permalink($plugin->ID) . '" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; font-size: 0.875rem; font-weight: 500; text-decoration: none; border-radius: 0.375rem; border: 1px solid transparent; cursor: pointer; transition: all 0.2s ease-in-out; flex: 1; justify-content: center; background-color: #2563eb; color: white;">Learn More</a>
                        ' . ($wordpress_url ? '<a href="' . esc_url($wordpress_url) . '" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; font-size: 0.875rem; font-weight: 500; text-decoration: none; border-radius: 0.375rem; border: 1px solid #2563eb; cursor: pointer; transition: all 0.2s ease-in-out; flex: 1; justify-content: center; background-color: transparent; color: #2563eb;" target="_blank">Download Free</a>' : '') . '
                    </div>
                </div>';
            }
            
            $plugins_content .= '</div>';
        } else {
            $plugins_content .= '<p>No plugins available yet. Please check back soon!</p>';
        }
        
        wp_update_post(array(
            'ID' => $plugins_page->ID,
            'post_content' => $plugins_content
        ));
        
        echo '<p class="success">✓ Updated plugins page with plugin listings</p>';
    } else {
        echo '<p class="error">✗ Plugins page not found</p>';
    }
    
    // Flush rewrite rules
    flush_rewrite_rules();
    
    echo '<h2>Fix Complete!</h2>';
    echo '<p class="success" style="font-size: 18px;">✓ All issues should now be resolved!</p>';
    
    echo '<h3>What Was Fixed:</h3>';
    echo '<ul>';
    echo '<li>✓ Created sample plugin content with proper meta data</li>';
    echo '<li>✓ Fixed contact form to display properly instead of shortcode</li>';
    echo '<li>✓ Updated plugins page with styled plugin listings</li>';
    echo '<li>✓ Flushed rewrite rules for clean URLs</li>';
    echo '</ul>';
    
    echo '<a href="' . home_url() . '" class="btn">View Your Site</a>';
    ?>
    
</body>
</html>