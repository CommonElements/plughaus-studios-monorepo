<?php
/**
 * DealerEdge Settings Admin View
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Handle form submission
if (isset($_POST['submit'])) {
    check_admin_referer('dealeredge_settings');
    
    // Update options
    update_option('dealeredge_business_name', sanitize_text_field($_POST['dealeredge_business_name']));
    update_option('dealeredge_business_type', sanitize_text_field($_POST['dealeredge_business_type']));
    update_option('dealeredge_currency', sanitize_text_field($_POST['dealeredge_currency']));
    update_option('dealeredge_tax_rate', floatval($_POST['dealeredge_tax_rate']));
    update_option('dealeredge_labor_rate', floatval($_POST['dealeredge_labor_rate']));
    
    echo '<div class="notice notice-success"><p>' . __('Settings saved successfully!', 'dealeredge') . '</p></div>';
}

// Get current values
$business_name = get_option('dealeredge_business_name', get_bloginfo('name'));
$business_type = get_option('dealeredge_business_type', 'auto_shop');
$currency = get_option('dealeredge_currency', 'USD');
$tax_rate = get_option('dealeredge_tax_rate', '8.25');
$labor_rate = get_option('dealeredge_labor_rate', '120.00');
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <form method="post" action="">
        <?php wp_nonce_field('dealeredge_settings'); ?>
        
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="dealeredge_business_name"><?php _e('Business Name', 'dealeredge'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="dealeredge_business_name" name="dealeredge_business_name" 
                               value="<?php echo esc_attr($business_name); ?>" class="regular-text" />
                        <p class="description"><?php _e('The name of your auto shop or dealership.', 'dealeredge'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="dealeredge_business_type"><?php _e('Business Type', 'dealeredge'); ?></label>
                    </th>
                    <td>
                        <select id="dealeredge_business_type" name="dealeredge_business_type">
                            <option value="auto_shop" <?php selected($business_type, 'auto_shop'); ?>><?php _e('Auto Shop / Service Center', 'dealeredge'); ?></option>
                            <option value="dealership" <?php selected($business_type, 'dealership'); ?>><?php _e('Car Dealership', 'dealeredge'); ?></option>
                            <option value="both" <?php selected($business_type, 'both'); ?>><?php _e('Both Auto Shop & Dealership', 'dealeredge'); ?></option>
                        </select>
                        <p class="description"><?php _e('Choose your primary business type to customize available features.', 'dealeredge'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="dealeredge_currency"><?php _e('Currency', 'dealeredge'); ?></label>
                    </th>
                    <td>
                        <select id="dealeredge_currency" name="dealeredge_currency">
                            <option value="USD" <?php selected($currency, 'USD'); ?>><?php _e('US Dollar ($)', 'dealeredge'); ?></option>
                            <option value="EUR" <?php selected($currency, 'EUR'); ?>><?php _e('Euro (€)', 'dealeredge'); ?></option>
                            <option value="GBP" <?php selected($currency, 'GBP'); ?>><?php _e('British Pound (£)', 'dealeredge'); ?></option>
                            <option value="CAD" <?php selected($currency, 'CAD'); ?>><?php _e('Canadian Dollar (C$)', 'dealeredge'); ?></option>
                            <option value="AUD" <?php selected($currency, 'AUD'); ?>><?php _e('Australian Dollar (A$)', 'dealeredge'); ?></option>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="dealeredge_tax_rate"><?php _e('Tax Rate (%)', 'dealeredge'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="dealeredge_tax_rate" name="dealeredge_tax_rate" 
                               value="<?php echo esc_attr($tax_rate); ?>" step="0.01" min="0" max="100" class="small-text" />
                        <p class="description"><?php _e('Default tax rate for work orders and sales.', 'dealeredge'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="dealeredge_labor_rate"><?php _e('Default Labor Rate', 'dealeredge'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="dealeredge_labor_rate" name="dealeredge_labor_rate" 
                               value="<?php echo esc_attr($labor_rate); ?>" step="0.01" min="0" class="small-text" />
                        <p class="description"><?php _e('Default hourly labor rate for work orders.', 'dealeredge'); ?></p>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <?php submit_button(); ?>
    </form>
    
    <hr>
    
    <div class="dealeredge-settings-info">
        <h2><?php _e('Getting Started', 'dealeredge'); ?></h2>
        <p><?php _e('Welcome to DealerEdge! Here are some quick steps to get you started:', 'dealeredge'); ?></p>
        
        <ol>
            <li><strong><?php _e('Configure your settings above', 'dealeredge'); ?></strong> - <?php _e('Set your business type, currency, and rates.', 'dealeredge'); ?></li>
            <li><strong><?php _e('Add your first vehicle', 'dealeredge'); ?></strong> - <a href="<?php echo admin_url('post-new.php?post_type=de_vehicle'); ?>"><?php _e('Add Vehicle', 'dealeredge'); ?></a></li>
            <li><strong><?php _e('Add customers', 'dealeredge'); ?></strong> - <a href="<?php echo admin_url('post-new.php?post_type=de_customer'); ?>"><?php _e('Add Customer', 'dealeredge'); ?></a></li>
            <li><strong><?php _e('Create your first work order or sale', 'dealeredge'); ?></strong> - <a href="<?php echo admin_url('post-new.php?post_type=de_work_order'); ?>"><?php _e('Create Work Order', 'dealeredge'); ?></a></li>
        </ol>
        
        <div class="dealeredge-upgrade-notice" style="background: #f0f8ff; border-left: 4px solid #007cba; padding: 15px; margin: 20px 0;">
            <h3><?php _e('Upgrade to DealerEdge Pro', 'dealeredge'); ?></h3>
            <p><?php _e('Get advanced features including analytics, email automation, multi-location support, and priority support.', 'dealeredge'); ?></p>
            <p>
                <a href="https://vireodesigns.com/plugins/dealeredge" class="button button-primary" target="_blank">
                    <?php _e('Learn More About Pro', 'dealeredge'); ?>
                </a>
            </p>
        </div>
    </div>
</div>

<style>
.dealeredge-settings-info {
    max-width: 800px;
}

.dealeredge-settings-info ol {
    padding-left: 20px;
}

.dealeredge-settings-info ol li {
    margin-bottom: 10px;
}

.dealeredge-upgrade-notice {
    border-radius: 4px;
}

.dealeredge-upgrade-notice h3 {
    margin-top: 0;
    color: #007cba;
}
</style>