<?php
/**
 * Admin settings for PlugHaus Sports League
 * 
 * @package PlugHaus_Sports_League
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class PSL_Admin_Settings {
    
    /**
     * Initialize settings
     */
    public function __construct() {
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_menu', array($this, 'add_settings_page'));
    }
    
    /**
     * Register all settings
     */
    public function register_settings() {
        // General settings
        register_setting('psl_general_settings', 'psl_general_settings', array($this, 'sanitize_general_settings'));
        
        // League settings
        register_setting('psl_league_settings', 'psl_league_settings', array($this, 'sanitize_league_settings'));
        
        // Display settings
        register_setting('psl_display_settings', 'psl_display_settings', array($this, 'sanitize_display_settings'));
        
        // Notification settings
        register_setting('psl_notification_settings', 'psl_notification_settings', array($this, 'sanitize_notification_settings'));
        
        $this->add_settings_sections();
    }
    
    /**
     * Add settings sections and fields
     */
    private function add_settings_sections() {
        // General Settings Section
        add_settings_section(
            'psl_general_section',
            __('General Settings', 'plughaus-league'),
            array($this, 'general_section_callback'),
            'psl_general_settings'
        );
        
        add_settings_field(
            'default_sport',
            __('Default Sport', 'plughaus-league'),
            array($this, 'default_sport_callback'),
            'psl_general_settings',
            'psl_general_section'
        );
        
        add_settings_field(
            'date_format',
            __('Date Format', 'plughaus-league'),
            array($this, 'date_format_callback'),
            'psl_general_settings',
            'psl_general_section'
        );
        
        add_settings_field(
            'time_format',
            __('Time Format', 'plughaus-league'),
            array($this, 'time_format_callback'),
            'psl_general_settings',
            'psl_general_section'
        );
        
        add_settings_field(
            'timezone',
            __('Timezone', 'plughaus-league'),
            array($this, 'timezone_callback'),
            'psl_general_settings',
            'psl_general_section'
        );
        
        // League Settings Section
        add_settings_section(
            'psl_league_section',
            __('League Settings', 'plughaus-league'),
            array($this, 'league_section_callback'),
            'psl_league_settings'
        );
        
        add_settings_field(
            'points_win',
            __('Points for Win', 'plughaus-league'),
            array($this, 'points_win_callback'),
            'psl_league_settings',
            'psl_league_section'
        );
        
        add_settings_field(
            'points_draw',
            __('Points for Draw', 'plughaus-league'),
            array($this, 'points_draw_callback'),
            'psl_league_settings',
            'psl_league_section'
        );
        
        add_settings_field(
            'points_loss',
            __('Points for Loss', 'plughaus-league'),
            array($this, 'points_loss_callback'),
            'psl_league_settings',
            'psl_league_section'
        );
        
        add_settings_field(
            'allow_draws',
            __('Allow Draws', 'plughaus-league'),
            array($this, 'allow_draws_callback'),
            'psl_league_settings',
            'psl_league_section'
        );
        
        add_settings_field(
            'standings_tiebreaker',
            __('Standings Tiebreaker', 'plughaus-league'),
            array($this, 'standings_tiebreaker_callback'),
            'psl_league_settings',
            'psl_league_section'
        );
        
        // Display Settings Section
        add_settings_section(
            'psl_display_section',
            __('Display Settings', 'plughaus-league'),
            array($this, 'display_section_callback'),
            'psl_display_settings'
        );
        
        add_settings_field(
            'theme_color',
            __('Theme Color', 'plughaus-league'),
            array($this, 'theme_color_callback'),
            'psl_display_settings',
            'psl_display_section'
        );
        
        add_settings_field(
            'show_logos',
            __('Show Team Logos', 'plughaus-league'),
            array($this, 'show_logos_callback'),
            'psl_display_settings',
            'psl_display_section'
        );
        
        add_settings_field(
            'show_player_photos',
            __('Show Player Photos', 'plughaus-league'),
            array($this, 'show_player_photos_callback'),
            'psl_display_settings',
            'psl_display_section'
        );
        
        add_settings_field(
            'items_per_page',
            __('Items Per Page', 'plughaus-league'),
            array($this, 'items_per_page_callback'),
            'psl_display_settings',
            'psl_display_section'
        );
    }
    
    /**
     * Add settings page to admin menu
     */
    public function add_settings_page() {
        // This is already handled by PSL_Admin class
    }
    
    /**
     * Section callbacks
     */
    public function general_section_callback() {
        echo '<p>' . __('Configure general plugin settings.', 'plughaus-league') . '</p>';
    }
    
    public function league_section_callback() {
        echo '<p>' . __('Configure league-specific settings like scoring and standings.', 'plughaus-league') . '</p>';
    }
    
    public function display_section_callback() {
        echo '<p>' . __('Customize how league information is displayed on your website.', 'plughaus-league') . '</p>';
    }
    
    /**
     * Field callbacks
     */
    public function default_sport_callback() {
        $options = get_option('psl_general_settings');
        $value = isset($options['default_sport']) ? $options['default_sport'] : 'soccer';
        $sports = PSL_Utilities::get_supported_sports();
        
        echo '<select name="psl_general_settings[default_sport]">';
        foreach ($sports as $key => $sport) {
            printf('<option value="%s" %s>%s</option>', 
                esc_attr($key), 
                selected($value, $key, false), 
                esc_html($sport['name'])
            );
        }
        echo '</select>';
        echo '<p class="description">' . __('Default sport for new leagues.', 'plughaus-league') . '</p>';
    }
    
    public function date_format_callback() {
        $options = get_option('psl_general_settings');
        $value = isset($options['date_format']) ? $options['date_format'] : 'Y-m-d';
        
        echo '<select name="psl_general_settings[date_format]">';
        $formats = array(
            'Y-m-d' => '2024-03-15',
            'm/d/Y' => '03/15/2024',
            'd/m/Y' => '15/03/2024',
            'F j, Y' => 'March 15, 2024',
            'j F Y' => '15 March 2024',
        );
        
        foreach ($formats as $format => $example) {
            printf('<option value="%s" %s>%s (%s)</option>', 
                esc_attr($format), 
                selected($value, $format, false), 
                esc_html($example),
                esc_html($format)
            );
        }
        echo '</select>';
    }
    
    public function time_format_callback() {
        $options = get_option('psl_general_settings');
        $value = isset($options['time_format']) ? $options['time_format'] : 'H:i';
        
        echo '<select name="psl_general_settings[time_format]">';
        $formats = array(
            'H:i' => '14:30 (24-hour)',
            'g:i A' => '2:30 PM (12-hour)',
        );
        
        foreach ($formats as $format => $example) {
            printf('<option value="%s" %s>%s</option>', 
                esc_attr($format), 
                selected($value, $format, false), 
                esc_html($example)
            );
        }
        echo '</select>';
    }
    
    public function timezone_callback() {
        $options = get_option('psl_general_settings');
        $value = isset($options['timezone']) ? $options['timezone'] : get_option('timezone_string', 'America/New_York');
        
        echo '<select name="psl_general_settings[timezone]">';
        $timezones = timezone_identifiers_list();
        foreach ($timezones as $timezone) {
            printf('<option value="%s" %s>%s</option>', 
                esc_attr($timezone), 
                selected($value, $timezone, false), 
                esc_html($timezone)
            );
        }
        echo '</select>';
    }
    
    public function points_win_callback() {
        $options = get_option('psl_league_settings');
        $value = isset($options['points_win']) ? $options['points_win'] : 3;
        
        printf('<input type="number" name="psl_league_settings[points_win]" value="%d" min="0" max="10" />', 
            esc_attr($value)
        );
    }
    
    public function points_draw_callback() {
        $options = get_option('psl_league_settings');
        $value = isset($options['points_draw']) ? $options['points_draw'] : 1;
        
        printf('<input type="number" name="psl_league_settings[points_draw]" value="%d" min="0" max="10" />', 
            esc_attr($value)
        );
    }
    
    public function points_loss_callback() {
        $options = get_option('psl_league_settings');
        $value = isset($options['points_loss']) ? $options['points_loss'] : 0;
        
        printf('<input type="number" name="psl_league_settings[points_loss]" value="%d" min="0" max="10" />', 
            esc_attr($value)
        );
    }
    
    public function allow_draws_callback() {
        $options = get_option('psl_league_settings');
        $value = isset($options['allow_draws']) ? $options['allow_draws'] : true;
        
        printf('<input type="checkbox" name="psl_league_settings[allow_draws]" value="1" %s />', 
            checked($value, true, false)
        );
        echo '<label for="psl_league_settings[allow_draws]">' . __('Allow matches to end in a draw', 'plughaus-league') . '</label>';
    }
    
    public function standings_tiebreaker_callback() {
        $options = get_option('psl_league_settings');
        $value = isset($options['standings_tiebreaker']) ? $options['standings_tiebreaker'] : 'goal_difference';
        
        echo '<select name="psl_league_settings[standings_tiebreaker]">';
        $tiebreakers = array(
            'goal_difference' => __('Goal Difference', 'plughaus-league'),
            'goals_for' => __('Goals For', 'plughaus-league'),
            'head_to_head' => __('Head-to-Head Record', 'plughaus-league'),
            'alphabetical' => __('Alphabetical', 'plughaus-league'),
        );
        
        foreach ($tiebreakers as $key => $label) {
            printf('<option value="%s" %s>%s</option>', 
                esc_attr($key), 
                selected($value, $key, false), 
                esc_html($label)
            );
        }
        echo '</select>';
    }
    
    public function theme_color_callback() {
        $options = get_option('psl_display_settings');
        $value = isset($options['theme_color']) ? $options['theme_color'] : '#007cba';
        
        printf('<input type="color" name="psl_display_settings[theme_color]" value="%s" />', 
            esc_attr($value)
        );
    }
    
    public function show_logos_callback() {
        $options = get_option('psl_display_settings');
        $value = isset($options['show_logos']) ? $options['show_logos'] : true;
        
        printf('<input type="checkbox" name="psl_display_settings[show_logos]" value="1" %s />', 
            checked($value, true, false)
        );
        echo '<label for="psl_display_settings[show_logos]">' . __('Display team logos in tables and lists', 'plughaus-league') . '</label>';
    }
    
    public function show_player_photos_callback() {
        $options = get_option('psl_display_settings');
        $value = isset($options['show_player_photos']) ? $options['show_player_photos'] : true;
        
        printf('<input type="checkbox" name="psl_display_settings[show_player_photos]" value="1" %s />', 
            checked($value, true, false)
        );
        echo '<label for="psl_display_settings[show_player_photos]">' . __('Display player photos in rosters', 'plughaus-league') . '</label>';
    }
    
    public function items_per_page_callback() {
        $options = get_option('psl_display_settings');
        $value = isset($options['items_per_page']) ? $options['items_per_page'] : 20;
        
        printf('<input type="number" name="psl_display_settings[items_per_page]" value="%d" min="5" max="100" />', 
            esc_attr($value)
        );
        echo '<p class="description">' . __('Number of items to show per page in tables.', 'plughaus-league') . '</p>';
    }
    
    /**
     * Sanitize settings
     */
    public function sanitize_general_settings($input) {
        $sanitized = array();
        
        if (isset($input['default_sport'])) {
            $sanitized['default_sport'] = sanitize_text_field($input['default_sport']);
        }
        
        if (isset($input['date_format'])) {
            $sanitized['date_format'] = sanitize_text_field($input['date_format']);
        }
        
        if (isset($input['time_format'])) {
            $sanitized['time_format'] = sanitize_text_field($input['time_format']);
        }
        
        if (isset($input['timezone'])) {
            $sanitized['timezone'] = sanitize_text_field($input['timezone']);
        }
        
        return $sanitized;
    }
    
    public function sanitize_league_settings($input) {
        $sanitized = array();
        
        if (isset($input['points_win'])) {
            $sanitized['points_win'] = intval($input['points_win']);
        }
        
        if (isset($input['points_draw'])) {
            $sanitized['points_draw'] = intval($input['points_draw']);
        }
        
        if (isset($input['points_loss'])) {
            $sanitized['points_loss'] = intval($input['points_loss']);
        }
        
        if (isset($input['allow_draws'])) {
            $sanitized['allow_draws'] = true;
        } else {
            $sanitized['allow_draws'] = false;
        }
        
        if (isset($input['standings_tiebreaker'])) {
            $sanitized['standings_tiebreaker'] = sanitize_text_field($input['standings_tiebreaker']);
        }
        
        return $sanitized;
    }
    
    public function sanitize_display_settings($input) {
        $sanitized = array();
        
        if (isset($input['theme_color'])) {
            $sanitized['theme_color'] = sanitize_hex_color($input['theme_color']);
        }
        
        if (isset($input['show_logos'])) {
            $sanitized['show_logos'] = true;
        } else {
            $sanitized['show_logos'] = false;
        }
        
        if (isset($input['show_player_photos'])) {
            $sanitized['show_player_photos'] = true;
        } else {
            $sanitized['show_player_photos'] = false;
        }
        
        if (isset($input['items_per_page'])) {
            $sanitized['items_per_page'] = intval($input['items_per_page']);
        }
        
        return $sanitized;
    }
    
    public function sanitize_notification_settings($input) {
        $sanitized = array();
        
        if (isset($input['enable_email_notifications'])) {
            $sanitized['enable_email_notifications'] = true;
        } else {
            $sanitized['enable_email_notifications'] = false;
        }
        
        if (isset($input['admin_email'])) {
            $sanitized['admin_email'] = sanitize_email($input['admin_email']);
        }
        
        return $sanitized;
    }
}