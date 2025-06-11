<?php
/**
 * Admin functionality for PlugHaus Sports League
 * 
 * @package PlugHaus_Sports_League
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class VSL_Admin {
    
    /**
     * Initialize admin functionality
     */
    public function __construct() {
        add_action('admin_init', array($this, 'admin_init'));
        add_action('admin_notices', array($this, 'admin_notices'));
    }
    
    /**
     * Admin initialization
     */
    public function admin_init() {
        // Check user capabilities
        if (!current_user_can('manage_options') && !VSL_Utilities::current_user_can_view_leagues()) {
            return;
        }
    }
    
    /**
     * Add admin menu pages
     */
    public function add_admin_menus() {
        // Main menu page
        add_menu_page(
            __('Sports League', 'vireo-league'),
            __('Sports League', 'vireo-league'),
            'view_vsl_leagues',
            'vsl-dashboard',
            array($this, 'dashboard_page'),
            'dashicons-awards',
            30
        );
        
        // Dashboard submenu
        add_submenu_page(
            'vsl-dashboard',
            __('Dashboard', 'vireo-league'),
            __('Dashboard', 'vireo-league'),
            'view_vsl_leagues',
            'vsl-dashboard',
            array($this, 'dashboard_page')
        );
        
        // Leagues submenu
        add_submenu_page(
            'vsl-dashboard',
            __('Leagues', 'vireo-league'),
            __('Leagues', 'vireo-league'),
            'view_vsl_leagues',
            'edit.php?post_type=vsl_league'
        );
        
        // Teams submenu
        add_submenu_page(
            'vsl-dashboard',
            __('Teams', 'vireo-league'),
            __('Teams', 'vireo-league'),
            'view_vsl_teams',
            'edit.php?post_type=vsl_team'
        );
        
        // Players submenu
        add_submenu_page(
            'vsl-dashboard',
            __('Players', 'vireo-league'),
            __('Players', 'vireo-league'),
            'view_vsl_players',
            'edit.php?post_type=vsl_player'
        );
        
        // Matches submenu
        add_submenu_page(
            'vsl-dashboard',
            __('Matches', 'vireo-league'),
            __('Matches', 'vireo-league'),
            'view_vsl_matches',
            'edit.php?post_type=vsl_match'
        );
        
        // Seasons submenu
        add_submenu_page(
            'vsl-dashboard',
            __('Seasons', 'vireo-league'),
            __('Seasons', 'vireo-league'),
            'view_vsl_seasons',
            'edit.php?post_type=vsl_season'
        );
        
        // Standings submenu
        add_submenu_page(
            'vsl-dashboard',
            __('Standings', 'vireo-league'),
            __('Standings', 'vireo-league'),
            'view_vsl_leagues',
            'vsl-standings',
            array($this, 'standings_page')
        );
        
        // Statistics submenu
        add_submenu_page(
            'vsl-dashboard',
            __('Statistics', 'vireo-league'),
            __('Statistics', 'vireo-league'),
            'view_vsl_statistics',
            'vsl-statistics',
            array($this, 'statistics_page')
        );
        
        // Settings submenu
        add_submenu_page(
            'vsl-dashboard',
            __('Settings', 'vireo-league'),
            __('Settings', 'vireo-league'),
            'manage_vsl_settings',
            'vsl-settings',
            array($this, 'settings_page')
        );
    }
    
    /**
     * Dashboard page
     */
    public function dashboard_page() {
        include_once PLUGHAUS_LEAGUE_CORE_DIR . 'admin/views/dashboard.php';
    }
    
    /**
     * Standings page
     */
    public function standings_page() {
        include_once PLUGHAUS_LEAGUE_CORE_DIR . 'admin/views/standings.php';
    }
    
    /**
     * Statistics page
     */
    public function statistics_page() {
        include_once PLUGHAUS_LEAGUE_CORE_DIR . 'admin/views/statistics.php';
    }
    
    /**
     * Settings page
     */
    public function settings_page() {
        include_once PLUGHAUS_LEAGUE_CORE_DIR . 'admin/views/settings.php';
    }
    
    /**
     * Enqueue admin styles
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            'vsl-admin',
            PLUGHAUS_LEAGUE_PLUGIN_URL . 'core/assets/css/admin.css',
            array(),
            PLUGHAUS_LEAGUE_VERSION
        );
        
        // Enqueue additional styles for specific pages
        $screen = get_current_screen();
        if ($screen && strpos($screen->id, 'psl') !== false) {
            wp_enqueue_style(
                'vsl-admin-page',
                PLUGHAUS_LEAGUE_PLUGIN_URL . 'core/assets/css/admin-page.css',
                array('vsl-admin'),
                PLUGHAUS_LEAGUE_VERSION
            );
        }
    }
    
    /**
     * Enqueue admin scripts
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            'vsl-admin',
            PLUGHAUS_LEAGUE_PLUGIN_URL . 'core/assets/js/admin.js',
            array('jquery', 'wp-util'),
            PLUGHAUS_LEAGUE_VERSION,
            true
        );
        
        // Localize script
        wp_localize_script('vsl-admin', 'vsl_admin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('vsl_admin_nonce'),
            'strings' => array(
                'confirm_delete' => __('Are you sure you want to delete this item?', 'vireo-league'),
                'loading' => __('Loading...', 'vireo-league'),
                'error' => __('An error occurred. Please try again.', 'vireo-league'),
                'success' => __('Action completed successfully.', 'vireo-league'),
            )
        ));
        
        // Enqueue Chart.js for statistics pages
        $screen = get_current_screen();
        if ($screen && (strpos($screen->id, 'vsl-statistics') !== false || strpos($screen->id, 'vsl-dashboard') !== false)) {
            wp_enqueue_script(
                'chart-js',
                'https://cdn.jsdelivr.net/npm/chart.js',
                array(),
                '3.9.1',
                true
            );
        }
    }
    
    /**
     * Display admin notices
     */
    public function admin_notices() {
        // Check for activation notice
        if (get_option('vsl_show_activation_notice')) {
            ?>
            <div class="notice notice-success is-dismissible">
                <p>
                    <?php _e('PlugHaus Sports League has been activated successfully!', 'vireo-league'); ?>
                    <a href="<?php echo admin_url('admin.php?page=vsl-dashboard'); ?>"><?php _e('Get started', 'vireo-league'); ?></a>
                </p>
            </div>
            <?php
            delete_option('vsl_show_activation_notice');
        }
        
        // Check for pro features notice
        if (!VSL_Utilities::is_pro() && current_user_can('manage_options')) {
            $this->show_pro_notice();
        }
    }
    
    /**
     * Show pro features notice
     */
    private function show_pro_notice() {
        $screen = get_current_screen();
        if (!$screen || strpos($screen->id, 'psl') === false) {
            return;
        }
        
        ?>
        <div class="notice notice-info">
            <p>
                <strong><?php _e('PlugHaus Sports League Pro', 'vireo-league'); ?></strong> - 
                <?php _e('Unlock advanced features like tournament brackets, payment processing, advanced statistics, and more!', 'vireo-league'); ?>
                <a href="https://plughausstudios.com/plugins/sports-league-pro" target="_blank" class="button button-primary" style="margin-left: 10px;">
                    <?php _e('Upgrade to Pro', 'vireo-league'); ?>
                </a>
            </p>
        </div>
        <?php
    }
    
    /**
     * Handle admin footer text
     */
    public function admin_footer_text($footer_text) {
        $screen = get_current_screen();
        if ($screen && strpos($screen->id, 'psl') !== false) {
            return sprintf(
                __('Thank you for using %s! Please %s if you like the plugin.', 'vireo-league'),
                '<strong>PlugHaus Sports League</strong>',
                '<a href="https://wordpress.org/support/plugin/plughaus-sports-league/reviews/#new-post" target="_blank">rate us ★★★★★</a>'
            );
        }
        return $footer_text;
    }
}