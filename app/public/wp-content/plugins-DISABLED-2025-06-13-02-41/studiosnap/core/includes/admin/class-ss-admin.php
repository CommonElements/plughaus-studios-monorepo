<?php
/**
 * Admin Interface for StudioSnap
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class SS_Admin {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menus'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }
    
    public function add_admin_menus() {
        // Main menu
        add_menu_page(
            __('StudioSnap', 'studiosnap'),
            __('StudioSnap', 'studiosnap'),
            'manage_studio',
            'studiosnap',
            array($this, 'display_dashboard'),
            'dashicons-camera',
            30
        );
        
        // Dashboard submenu
        add_submenu_page(
            'studiosnap',
            __('Dashboard', 'studiosnap'),
            __('Dashboard', 'studiosnap'),
            'manage_studio',
            'studiosnap',
            array($this, 'display_dashboard')
        );
        
        // Sessions submenu
        add_submenu_page(
            'studiosnap',
            __('Sessions', 'studiosnap'),
            __('Sessions', 'studiosnap'),
            'manage_ss_sessions',
            'edit.php?post_type=ss_session'
        );
        
        // Clients submenu
        add_submenu_page(
            'studiosnap',
            __('Clients', 'studiosnap'),
            __('Clients', 'studiosnap'),
            'manage_ss_clients',
            'edit.php?post_type=ss_client'
        );
        
        // Settings submenu
        add_submenu_page(
            'studiosnap',
            __('Settings', 'studiosnap'),
            __('Settings', 'studiosnap'),
            'manage_studio',
            'studiosnap-settings',
            array($this, 'display_settings')
        );
    }
    
    public function display_dashboard() {
        ?>
        <div class="wrap">
            <h1><?php _e('StudioSnap Dashboard', 'studiosnap'); ?></h1>
            
            <div class="ss-dashboard-grid">
                <div class="ss-stats-cards">
                    <?php
                    // Get booking stats if available
                    $stats = array(
                        'upcoming_sessions' => 0,
                        'total_clients' => 0,
                        'this_month_revenue' => 0
                    );
                    
                    if (class_exists('SS_Booking_System')) {
                        $stats = SS_Booking_System::get_booking_stats();
                    } else {
                        // Fallback to basic WordPress counts
                        $stats['total_clients'] = wp_count_posts('ss_client') ? wp_count_posts('ss_client')->publish : 0;
                        $stats['upcoming_sessions'] = wp_count_posts('ss_session') ? wp_count_posts('ss_session')->publish : 0;
                    }
                    ?>
                    
                    <div class="ss-stat-card">
                        <h3><?php _e('Upcoming Sessions', 'studiosnap'); ?></h3>
                        <p class="ss-stat-number"><?php echo number_format($stats['upcoming_sessions']); ?></p>
                    </div>
                    
                    <div class="ss-stat-card">
                        <h3><?php _e('Total Clients', 'studiosnap'); ?></h3>
                        <p class="ss-stat-number"><?php echo number_format($stats['total_clients']); ?></p>
                    </div>
                    
                    <div class="ss-stat-card">
                        <h3><?php _e('This Month Revenue', 'studiosnap'); ?></h3>
                        <p class="ss-stat-number">$<?php echo isset($stats['this_month_revenue']) ? number_format($stats['this_month_revenue']) : '0'; ?></p>
                    </div>
                </div>
                
                <div class="ss-quick-actions">
                    <h3><?php _e('Quick Actions', 'studiosnap'); ?></h3>
                    <p><a href="<?php echo admin_url('post-new.php?post_type=ss_session'); ?>" class="button button-primary"><?php _e('Add New Session', 'studiosnap'); ?></a></p>
                    <p><a href="<?php echo admin_url('post-new.php?post_type=ss_client'); ?>" class="button"><?php _e('Add New Client', 'studiosnap'); ?></a></p>
                    <p><a href="<?php echo admin_url('admin.php?page=studiosnap-settings'); ?>" class="button"><?php _e('Settings', 'studiosnap'); ?></a></p>
                </div>
            </div>
            
            <p class="ss-getting-started">
                <?php _e('Welcome to StudioSnap! This is the free version. Upgrade to Pro for advanced features like payment processing, automated workflows, and priority support.', 'studiosnap'); ?>
            </p>
        </div>
        
        <style>
        .ss-dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin: 20px 0;
        }
        
        .ss-stats-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }
        
        .ss-stat-card {
            background: #fff;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-align: center;
        }
        
        .ss-stat-card h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #666;
        }
        
        .ss-stat-number {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
            color: #333;
        }
        
        .ss-quick-actions {
            background: #fff;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .ss-quick-actions h3 {
            margin-top: 0;
        }
        
        .ss-getting-started {
            background: #f1f1f1;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        </style>
        <?php
    }
    
    public function display_settings() {
        ?>
        <div class="wrap">
            <h1><?php _e('StudioSnap Settings', 'studiosnap'); ?></h1>
            
            <form method="post" action="options.php">
                <?php
                settings_fields('studiosnap_settings');
                do_settings_sections('studiosnap_settings');
                ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Studio Name', 'studiosnap'); ?></th>
                        <td>
                            <input type="text" name="ss_studio_name" value="<?php echo esc_attr(get_option('ss_studio_name', '')); ?>" class="regular-text" />
                            <p class="description"><?php _e('Your photography studio name', 'studiosnap'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('Contact Email', 'studiosnap'); ?></th>
                        <td>
                            <input type="email" name="ss_contact_email" value="<?php echo esc_attr(get_option('ss_contact_email', '')); ?>" class="regular-text" />
                            <p class="description"><?php _e('Email for client communications', 'studiosnap'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('Currency', 'studiosnap'); ?></th>
                        <td>
                            <select name="ss_currency">
                                <option value="USD" <?php selected(get_option('ss_currency'), 'USD'); ?>><?php _e('USD ($)', 'studiosnap'); ?></option>
                                <option value="EUR" <?php selected(get_option('ss_currency'), 'EUR'); ?>><?php _e('EUR (€)', 'studiosnap'); ?></option>
                                <option value="GBP" <?php selected(get_option('ss_currency'), 'GBP'); ?>><?php _e('GBP (£)', 'studiosnap'); ?></option>
                            </select>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(); ?>
            </form>
            
            <hr>
            
            <h2><?php _e('Upgrade to Pro', 'studiosnap'); ?></h2>
            <p><?php _e('Get advanced features including payment processing, automated workflows, and priority support.', 'studiosnap'); ?></p>
            <p><a href="https://vireodesigns.com/plugins/studiosnap-pro" class="button button-primary" target="_blank"><?php _e('Upgrade to Pro', 'studiosnap'); ?></a></p>
        </div>
        <?php
    }
    
    public function enqueue_styles($hook) {
        if (strpos($hook, 'studiosnap') !== false) {
            // TODO: Enqueue admin CSS when created
        }
    }
    
    public function enqueue_scripts($hook) {
        if (strpos($hook, 'studiosnap') !== false) {
            // TODO: Enqueue admin JS when created
        }
    }
}

// Initialize admin if in admin area
if (is_admin()) {
    new SS_Admin();
}