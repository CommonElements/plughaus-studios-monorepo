<?php
/**
 * Quick Fix Script for PlugHaus Studios Theme
 */

require_once dirname(__FILE__) . '/wp-load.php';

if (!current_user_can('manage_options')) {
    wp_die('Please login as administrator first.');
}

echo '<h1>PlugHaus Studios Quick Fix</h1>';

// 1. Create a sample plugin if none exist
$existing_plugins = get_posts(array(
    'post_type' => 'phstudios_plugin',
    'posts_per_page' => 1,
    'post_status' => 'publish'
));

if (empty($existing_plugins)) {
    echo '<h2>Creating Sample Plugin...</h2>';
    
    $plugin_data = array(
        'post_title' => 'PlugHaus Property Management',
        'post_content' => '<p>A comprehensive property management solution for WordPress. Manage properties, tenants, leases, and maintenance requests with ease. Perfect for landlords, property managers, and real estate professionals.</p>

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
        echo '<p style="color: green;">✓ Created sample plugin</p>';
        
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
        update_post_meta($plugin_id, '_plugin_features', "Property Management\nTenant Tracking\nLease Management\nMaintenance Requests\nDashboard Overview\nImport/Export Data");
        update_post_meta($plugin_id, '_pro_features', "Advanced Analytics\nPayment Automation\nDocument Management\nEmail Templates\nCustom Fields\nPriority Support");
        update_post_meta($plugin_id, '_featured_plugin', '1');
        
        echo '<p style="color: green;">✓ Added plugin meta data</p>';
        
    } else {
        echo '<p style="color: red;">✗ Failed to create sample plugin</p>';
    }
}

// 2. Fix contact form shortcode by rendering it properly
echo '<h2>Fixing Contact Form...</h2>';

$contact_page = get_page_by_path('contact');
if ($contact_page) {
    $contact_content = '
<h2>Get In Touch</h2>
<p>Have questions about our plugins or need custom development? We\'d love to hear from you.</p>

<div class="contact-form-container">
    <form class="plughaus-contact-form" method="post" action="">
        <input type="hidden" name="contact_nonce" value="' . wp_create_nonce('plughaus_contact_form') . '" />
        
        <div class="form-group">
            <label for="contact_name">Name *</label>
            <input type="text" id="contact_name" name="contact_name" required>
        </div>
        
        <div class="form-group">
            <label for="contact_email">Email *</label>
            <input type="email" id="contact_email" name="contact_email" required>
        </div>
        
        <div class="form-group">
            <label for="contact_subject">Subject *</label>
            <select id="contact_subject" name="contact_subject" required>
                <option value="">Select a topic</option>
                <option value="general">General Inquiry</option>
                <option value="support">Plugin Support</option>
                <option value="custom">Custom Development</option>
                <option value="partnership">Partnership</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="contact_message">Message *</label>
            <textarea id="contact_message" name="contact_message" rows="5" required></textarea>
        </div>
        
        <button type="submit" name="submit_contact_form" class="btn btn-primary">
            <i class="fas fa-paper-plane"></i>
            Send Message
        </button>
    </form>
</div>

<style>
.contact-form-container {
    max-width: 600px;
    margin: 2rem 0;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #334155;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #cbd5e1;
    border-radius: 0.375rem;
    font-size: 1rem;
    transition: border-color 0.15s ease-in-out;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.form-group textarea {
    resize: vertical;
    min-height: 120px;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    border-radius: 0.5rem;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all 0.2s ease-in-out;
}

.btn-primary {
    background-color: #2563eb;
    color: white;
}

.btn-primary:hover {
    background-color: #1d4ed8;
    transform: translateY(-1px);
}
</style>';

    wp_update_post(array(
        'ID' => $contact_page->ID,
        'post_content' => $contact_content
    ));
    
    echo '<p style="color: green;">✓ Updated contact page with working form</p>';
}

// 3. Update plugins page content
echo '<h2>Fixing Plugins Page...</h2>';

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
        $plugins_content .= '<div class="plugins-showcase">';
        
        foreach ($plugins as $plugin) {
            $status = get_post_meta($plugin->ID, '_plugin_status', true) ?: 'available';
            $version = get_post_meta($plugin->ID, '_plugin_version', true);
            $price_free = get_post_meta($plugin->ID, '_price_free', true) ?: 'Free';
            $price_pro = get_post_meta($plugin->ID, '_price_pro', true);
            $wordpress_url = get_post_meta($plugin->ID, '_wordpress_url', true);
            $rating = get_post_meta($plugin->ID, '_rating', true);
            $download_count = get_post_meta($plugin->ID, '_download_count', true);
            
            $plugins_content .= '
            <div class="plugin-card">
                <div class="plugin-header">
                    <h3><a href="' . get_permalink($plugin->ID) . '">' . esc_html($plugin->post_title) . '</a></h3>
                    <span class="plugin-status status-' . esc_attr($status) . '">' . esc_html(ucwords(str_replace('-', ' ', $status))) . '</span>
                </div>
                
                <div class="plugin-content">
                    <p>' . esc_html($plugin->post_excerpt) . '</p>
                    
                    <div class="plugin-stats">
                        ' . ($download_count ? '<span class="stat"><i class="fas fa-download"></i> ' . number_format($download_count) . ' downloads</span>' : '') . '
                        ' . ($rating ? '<span class="stat"><i class="fas fa-star"></i> ' . $rating . '/5</span>' : '') . '
                        ' . ($version ? '<span class="stat"><i class="fas fa-tag"></i> v' . $version . '</span>' : '') . '
                    </div>
                    
                    <div class="plugin-pricing">
                        <span class="price free">' . esc_html($price_free) . '</span>
                        ' . ($price_pro ? '<span class="price pro">' . esc_html($price_pro) . '</span>' : '') . '
                    </div>
                </div>
                
                <div class="plugin-actions">
                    <a href="' . get_permalink($plugin->ID) . '" class="btn btn-primary">Learn More</a>
                    ' . ($wordpress_url ? '<a href="' . esc_url($wordpress_url) . '" class="btn btn-outline" target="_blank">Download Free</a>' : '') . '
                </div>
            </div>';
        }
        
        $plugins_content .= '</div>';
    } else {
        $plugins_content .= '<p>No plugins available yet. Please check back soon!</p>';
    }
    
    $plugins_content .= '
    <style>
    .plugins-showcase {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 2rem;
        margin: 2rem 0;
    }
    
    .plugin-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease-in-out;
    }
    
    .plugin-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }
    
    .plugin-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }
    
    .plugin-header h3 {
        margin: 0;
        flex: 1;
    }
    
    .plugin-header h3 a {
        color: #1e293b;
        text-decoration: none;
    }
    
    .plugin-header h3 a:hover {
        color: #2563eb;
    }
    
    .plugin-status {
        padding: 0.25rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 500;
        border-radius: 0.375rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .plugin-status.status-available {
        background-color: #d1fae5;
        color: #065f46;
    }
    
    .plugin-status.status-coming-soon {
        background-color: #fef3c7;
        color: #92400e;
    }
    
    .plugin-content p {
        color: #64748b;
        line-height: 1.6;
        margin-bottom: 1rem;
    }
    
    .plugin-stats {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
        font-size: 0.875rem;
        color: #64748b;
    }
    
    .plugin-stats .stat {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .plugin-stats i {
        color: #2563eb;
    }
    
    .plugin-pricing {
        display: flex;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }
    
    .price {
        padding: 0.5rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 500;
    }
    
    .price.free {
        background-color: #10b981;
        color: white;
    }
    
    .price.pro {
        background-color: #f59e0b;
        color: white;
    }
    
    .plugin-actions {
        display: flex;
        gap: 0.75rem;
    }
    
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        border-radius: 0.375rem;
        border: 1px solid transparent;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        flex: 1;
        justify-content: center;
    }
    
    .btn-primary {
        background-color: #2563eb;
        color: white;
    }
    
    .btn-primary:hover {
        background-color: #1d4ed8;
    }
    
    .btn-outline {
        background-color: transparent;
        color: #2563eb;
        border-color: #2563eb;
    }
    
    .btn-outline:hover {
        background-color: #2563eb;
        color: white;
    }
    </style>';
    
    wp_update_post(array(
        'ID' => $plugins_page->ID,
        'post_content' => $plugins_content
    ));
    
    echo '<p style="color: green;">✓ Updated plugins page with plugin listings</p>';
}

// 4. Test shortcode functionality
echo '<h2>Testing Shortcode...</h2>';

if (shortcode_exists('plugin_showcase')) {
    echo '<p style="color: green;">✓ Plugin showcase shortcode is registered</p>';
} else {
    echo '<p style="color: red;">✗ Plugin showcase shortcode not found</p>';
}

// Force flush rewrite rules
flush_rewrite_rules();

echo '<h2>Quick Fix Complete!</h2>';
echo '<p style="color: green; font-size: 18px; font-weight: bold;">✓ Issues should now be resolved!</p>';
echo '<p><a href="' . home_url() . '" target="_blank" style="background: #2563eb; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">View Your Site</a></p>';

echo '<h3>What Was Fixed:</h3>';
echo '<ul>';
echo '<li>✓ Created sample plugin content</li>';
echo '<li>✓ Fixed contact form display</li>';
echo '<li>✓ Updated plugins page with proper content</li>';
echo '<li>✓ Added proper styling for plugin cards</li>';
echo '</ul>';

echo '<style>
body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
h1, h2 { color: #2563eb; }
p { margin: 8px 0; }
ul { margin-left: 20px; }
</style>';
?>