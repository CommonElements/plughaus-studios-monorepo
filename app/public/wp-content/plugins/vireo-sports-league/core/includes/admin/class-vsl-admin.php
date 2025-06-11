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

class PSL_Admin {
    
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
        if (!current_user_can('manage_options') && !PSL_Utilities::current_user_can_view_leagues()) {
            return;
        }
    }
    
    /**
     * Add admin menu pages
     */
    public function add_admin_menus() {
        // Main menu page
        add_menu_page(
            __('Sports League', 'plughaus-league'),
            __('Sports League', 'plughaus-league'),
            'view_psl_leagues',
            'psl-dashboard',
            array($this, 'dashboard_page'),
            'dashicons-awards',
            30
        );
        
        // Dashboard submenu
        add_submenu_page(
            'psl-dashboard',
            __('Dashboard', 'plughaus-league'),
            __('Dashboard', 'plughaus-league'),
            'view_psl_leagues',
            'psl-dashboard',
            array($this, 'dashboard_page')
        );
        
        // Leagues submenu
        add_submenu_page(
            'psl-dashboard',
            __('Leagues', 'plughaus-league'),
            __('Leagues', 'plughaus-league'),
            'view_psl_leagues',
            'edit.php?post_type=psl_league'
        );
        
        // Teams submenu
        add_submenu_page(
            'psl-dashboard',
            __('Teams', 'plughaus-league'),
            __('Teams', 'plughaus-league'),
            'view_psl_teams',
            'edit.php?post_type=psl_team'
        );
        
        // Players submenu
        add_submenu_page(
            'psl-dashboard',
            __('Players', 'plughaus-league'),
            __('Players', 'plughaus-league'),
            'view_psl_players',
            'edit.php?post_type=psl_player'
        );
        
        // Matches submenu
        add_submenu_page(
            'psl-dashboard',
            __('Matches', 'plughaus-league'),
            __('Matches', 'plughaus-league'),
            'view_psl_matches',
            'edit.php?post_type=psl_match'
        );
        
        // Seasons submenu
        add_submenu_page(
            'psl-dashboard',
            __('Seasons', 'plughaus-league'),
            __('Seasons', 'plughaus-league'),
            'view_psl_seasons',
            'edit.php?post_type=psl_season'
        );
        
        // Standings submenu
        add_submenu_page(
            'psl-dashboard',
            __('Standings', 'plughaus-league'),
            __('Standings', 'plughaus-league'),
            'view_psl_leagues',
            'psl-standings',
            array($this, 'standings_page')
        );
        
        // Statistics submenu
        add_submenu_page(
            'psl-dashboard',
            __('Statistics', 'plughaus-league'),
            __('Statistics', 'plughaus-league'),
            'view_psl_statistics',
            'psl-statistics',
            array($this, 'statistics_page')
        );
        
        // Settings submenu
        add_submenu_page(
            'psl-dashboard',
            __('Settings', 'plughaus-league'),
            __('Settings', 'plughaus-league'),
            'manage_psl_settings',
            'psl-settings',
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
            'psl-admin',
            PLUGHAUS_LEAGUE_PLUGIN_URL . 'core/assets/css/admin.css',
            array(),
            PLUGHAUS_LEAGUE_VERSION
        );
        
        // Enqueue additional styles for specific pages
        $screen = get_current_screen();
        if ($screen && strpos($screen->id, 'psl') !== false) {
            wp_enqueue_style(
                'psl-admin-page',
                PLUGHAUS_LEAGUE_PLUGIN_URL . 'core/assets/css/admin-page.css',
                array('psl-admin'),
                PLUGHAUS_LEAGUE_VERSION
            );
        }
    }
    
    /**
     * Enqueue admin scripts
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            'psl-admin',
            PLUGHAUS_LEAGUE_PLUGIN_URL . 'core/assets/js/admin.js',
            array('jquery', 'wp-util'),
            PLUGHAUS_LEAGUE_VERSION,
            true
        );
        
        // Localize script
        wp_localize_script('psl-admin', 'psl_admin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('psl_admin_nonce'),
            'strings' => array(
                'confirm_delete' => __('Are you sure you want to delete this item?', 'plughaus-league'),
                'loading' => __('Loading...', 'plughaus-league'),
                'error' => __('An error occurred. Please try again.', 'plughaus-league'),
                'success' => __('Action completed successfully.', 'plughaus-league'),
            )
        ));
        
        // Enqueue Chart.js for statistics pages
        $screen = get_current_screen();
        if ($screen && (strpos($screen->id, 'psl-statistics') !== false || strpos($screen->id, 'psl-dashboard') !== false)) {
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
        if (get_option('psl_show_activation_notice')) {
            ?>
            <div class="notice notice-success is-dismissible">
                <p>
                    <?php _e('PlugHaus Sports League has been activated successfully!', 'plughaus-league'); ?>
                    <a href="<?php echo admin_url('admin.php?page=psl-dashboard'); ?>"><?php _e('Get started', 'plughaus-league'); ?></a>
                </p>
            </div>
            <?php
            delete_option('psl_show_activation_notice');
        }
        
        // Check for pro features notice
        if (!PSL_Utilities::is_pro() && current_user_can('manage_options')) {
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
                <strong><?php _e('PlugHaus Sports League Pro', 'plughaus-league'); ?></strong> - 
                <?php _e('Unlock advanced features like tournament brackets, payment processing, advanced statistics, and more!', 'plughaus-league'); ?>
                <a href="https://plughausstudios.com/plugins/sports-league-pro" target="_blank" class="button button-primary" style="margin-left: 10px;">
                    <?php _e('Upgrade to Pro', 'plughaus-league'); ?>
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
                __('Thank you for using %s! Please %s if you like the plugin.', 'plughaus-league'),
                '<strong>PlugHaus Sports League</strong>',
                '<a href="https://wordpress.org/support/plugin/plughaus-sports-league/reviews/#new-post" target="_blank">rate us ★★★★★</a>'
            );
        }
        return $footer_text;
    }
}