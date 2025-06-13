<?php
/**
 * Plugin activator for Vireo Sports League
 * 
 * @package Vireo_Sports_League
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class VSL_Activator {
    
    /**
     * Activate the plugin
     */
    public static function activate() {
        // Check WordPress version
        if (!self::check_wp_version()) {
            deactivate_plugins(plugin_basename(__FILE__));
            wp_die(__('Vireo Sports League requires WordPress 5.8 or higher.', 'vireo-league'));
        }
        
        // Check PHP version
        if (!self::check_php_version()) {
            deactivate_plugins(plugin_basename(__FILE__));
            wp_die(__('Vireo Sports League requires PHP 7.4 or higher.', 'vireo-league'));
        }
        
        // Create database tables
        self::create_tables();
        
        // Load dependencies
        self::load_dependencies();
        
        // Register post types and taxonomies
        if (class_exists('VSL_Post_Types')) {
            VSL_Post_Types::register_post_types();
        }
        if (class_exists('VSL_Taxonomies')) {
            VSL_Taxonomies::register_taxonomies();
        }
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Add capabilities
        if (class_exists('VSL_Capabilities')) {
            VSL_Capabilities::add_custom_roles_and_capabilities();
        }
        
        // Set default options
        self::set_default_options();
        
        // Create default content
        self::create_default_content();
        
        // Schedule events
        self::schedule_events();
        
        // Log activation
        if (class_exists('VSL_Utilities')) {
            VSL_Utilities::log_activity('system', 'Plugin activated', 0, get_current_user_id());
        }
        
        // Set activation flag
        update_option('vsl_activation_time', time());
        update_option('vsl_version', VIREO_LEAGUE_VERSION);
    }
    
    /**
     * Create custom database tables
     */
    private static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        // Leagues table - Core league information
        $table_name = $wpdb->prefix . 'vsl_leagues';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            league_code varchar(20) NOT NULL,
            name varchar(255) NOT NULL,
            description text,
            sport varchar(50) NOT NULL DEFAULT 'soccer',
            organizer varchar(255),
            contact_email varchar(255),
            website varchar(255),
            logo_url varchar(500),
            status varchar(20) DEFAULT 'active',
            is_public boolean DEFAULT true,
            allow_registration boolean DEFAULT false,
            registration_fee decimal(10,2) DEFAULT 0,
            max_teams int DEFAULT 0,
            location varchar(255),
            rules text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY league_code (league_code),
            KEY sport (sport),
            KEY status (status),
            KEY organizer (organizer)
        ) $charset_collate;";
        dbDelta($sql);
        
        // Seasons table - League seasons/competitions
        $table_name = $wpdb->prefix . 'vsl_seasons';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            league_id bigint(20) NOT NULL,
            name varchar(255) NOT NULL,
            year int NOT NULL,
            start_date date NOT NULL,
            end_date date NOT NULL,
            registration_start date,
            registration_end date,
            status varchar(20) DEFAULT 'upcoming',
            is_current boolean DEFAULT false,
            max_teams int DEFAULT 0,
            playoff_format varchar(50),
            notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY league_id (league_id),
            KEY year (year),
            KEY status (status),
            KEY is_current (is_current),
            FOREIGN KEY (league_id) REFERENCES {$wpdb->prefix}vsl_leagues(id) ON DELETE CASCADE
        ) $charset_collate;";
        dbDelta($sql);
        
        // Teams table - Team information
        $table_name = $wpdb->prefix . 'vsl_teams';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            league_id bigint(20) NOT NULL,
            team_code varchar(20) NOT NULL,
            name varchar(255) NOT NULL,
            short_name varchar(50),
            abbreviation varchar(10),
            logo_url varchar(500),
            primary_color varchar(7),
            secondary_color varchar(7),
            home_venue varchar(255),
            founded_year year,
            coach_name varchar(255),
            coach_email varchar(255),
            coach_phone varchar(20),
            manager_name varchar(255),
            manager_email varchar(255),
            manager_phone varchar(20),
            status varchar(20) DEFAULT 'active',
            registration_fee_paid boolean DEFAULT false,
            notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY team_code (team_code),
            KEY league_id (league_id),
            KEY name (name),
            KEY status (status),
            KEY coach_email (coach_email),
            FOREIGN KEY (league_id) REFERENCES {$wpdb->prefix}vsl_leagues(id) ON DELETE CASCADE
        ) $charset_collate;";
        dbDelta($sql);
        
        // Players table - Individual player information
        $table_name = $wpdb->prefix . 'vsl_players';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            player_code varchar(20) NOT NULL,
            team_id bigint(20) NOT NULL,
            first_name varchar(100) NOT NULL,
            last_name varchar(100) NOT NULL,
            email varchar(255),
            phone varchar(20),
            date_of_birth date,
            jersey_number int,
            position varchar(50),
            height varchar(10),
            weight varchar(10),
            photo_url varchar(500),
            emergency_contact_name varchar(200),
            emergency_contact_phone varchar(20),
            medical_notes text,
            registration_date date,
            status varchar(20) DEFAULT 'active',
            is_captain boolean DEFAULT false,
            notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY player_code (player_code),
            UNIQUE KEY team_jersey (team_id, jersey_number),
            KEY team_id (team_id),
            KEY name (first_name, last_name),
            KEY position (position),
            KEY status (status),
            KEY email (email),
            FOREIGN KEY (team_id) REFERENCES {$wpdb->prefix}vsl_teams(id) ON DELETE CASCADE
        ) $charset_collate;";
        dbDelta($sql);
        
        // Matches table - Game/match information
        $table_name = $wpdb->prefix . 'vsl_matches';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            season_id bigint(20) NOT NULL,
            match_number varchar(20),
            home_team_id bigint(20) NOT NULL,
            away_team_id bigint(20) NOT NULL,
            match_date datetime NOT NULL,
            venue varchar(255),
            referee varchar(255),
            assistant_referee varchar(255),
            home_score int DEFAULT 0,
            away_score int DEFAULT 0,
            status varchar(20) DEFAULT 'scheduled',
            match_type varchar(50) DEFAULT 'regular',
            round_number int,
            notes text,
            weather varchar(100),
            attendance int,
            match_duration int DEFAULT 90,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY season_id (season_id),
            KEY home_team_id (home_team_id),
            KEY away_team_id (away_team_id),
            KEY match_date (match_date),
            KEY status (status),
            KEY match_type (match_type),
            FOREIGN KEY (season_id) REFERENCES {$wpdb->prefix}vsl_seasons(id) ON DELETE CASCADE,
            FOREIGN KEY (home_team_id) REFERENCES {$wpdb->prefix}vsl_teams(id) ON DELETE CASCADE,
            FOREIGN KEY (away_team_id) REFERENCES {$wpdb->prefix}vsl_teams(id) ON DELETE CASCADE
        ) $charset_collate;";
        dbDelta($sql);
        
        // Match events table - Goals, cards, substitutions
        $table_name = $wpdb->prefix . 'vsl_match_events';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            match_id bigint(20) NOT NULL,
            team_id bigint(20) NOT NULL,
            player_id bigint(20),
            event_type varchar(50) NOT NULL,
            minute int NOT NULL,
            extra_time int DEFAULT 0,
            description text,
            assist_player_id bigint(20),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY match_id (match_id),
            KEY team_id (team_id),
            KEY player_id (player_id),
            KEY event_type (event_type),
            KEY minute (minute),
            FOREIGN KEY (match_id) REFERENCES {$wpdb->prefix}vsl_matches(id) ON DELETE CASCADE,
            FOREIGN KEY (team_id) REFERENCES {$wpdb->prefix}vsl_teams(id) ON DELETE CASCADE,
            FOREIGN KEY (player_id) REFERENCES {$wpdb->prefix}vsl_players(id) ON DELETE SET NULL,
            FOREIGN KEY (assist_player_id) REFERENCES {$wpdb->prefix}vsl_players(id) ON DELETE SET NULL
        ) $charset_collate;";
        dbDelta($sql);
        
        // Team standings table - League standings/points
        $table_name = $wpdb->prefix . 'vsl_standings';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            season_id bigint(20) NOT NULL,
            team_id bigint(20) NOT NULL,
            position int DEFAULT 0,
            matches_played int DEFAULT 0,
            wins int DEFAULT 0,
            draws int DEFAULT 0,
            losses int DEFAULT 0,
            goals_for int DEFAULT 0,
            goals_against int DEFAULT 0,
            goal_difference int DEFAULT 0,
            points int DEFAULT 0,
            form varchar(10),
            last_updated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY season_team (season_id, team_id),
            KEY season_id (season_id),
            KEY team_id (team_id),
            KEY position (position),
            KEY points (points),
            FOREIGN KEY (season_id) REFERENCES {$wpdb->prefix}vsl_seasons(id) ON DELETE CASCADE,
            FOREIGN KEY (team_id) REFERENCES {$wpdb->prefix}vsl_teams(id) ON DELETE CASCADE
        ) $charset_collate;";
        dbDelta($sql);
        
        // Player statistics table - Individual player stats
        $table_name = $wpdb->prefix . 'vsl_player_stats';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            season_id bigint(20) NOT NULL,
            player_id bigint(20) NOT NULL,
            matches_played int DEFAULT 0,
            minutes_played int DEFAULT 0,
            goals int DEFAULT 0,
            assists int DEFAULT 0,
            yellow_cards int DEFAULT 0,
            red_cards int DEFAULT 0,
            clean_sheets int DEFAULT 0,
            saves int DEFAULT 0,
            goals_conceded int DEFAULT 0,
            last_updated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY season_player (season_id, player_id),
            KEY season_id (season_id),
            KEY player_id (player_id),
            KEY goals (goals),
            KEY assists (assists),
            FOREIGN KEY (season_id) REFERENCES {$wpdb->prefix}vsl_seasons(id) ON DELETE CASCADE,
            FOREIGN KEY (player_id) REFERENCES {$wpdb->prefix}vsl_players(id) ON DELETE CASCADE
        ) $charset_collate;";
        dbDelta($sql);
        
        // Venues table - Match venues/facilities
        $table_name = $wpdb->prefix . 'vsl_venues';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            address text,
            city varchar(100),
            state varchar(50),
            zip varchar(20),
            capacity int,
            surface_type varchar(50),
            lighting boolean DEFAULT true,
            parking_info text,
            contact_name varchar(255),
            contact_phone varchar(20),
            contact_email varchar(255),
            notes text,
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY name (name),
            KEY city_state (city, state),
            KEY status (status)
        ) $charset_collate;";
        dbDelta($sql);
        
        // Officials table - Referees and linesmen
        $table_name = $wpdb->prefix . 'vsl_officials';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            first_name varchar(100) NOT NULL,
            last_name varchar(100) NOT NULL,
            email varchar(255),
            phone varchar(20),
            certification_level varchar(50),
            certification_expiry date,
            experience_years int DEFAULT 0,
            specialties text,
            availability text,
            rates decimal(10,2),
            status varchar(20) DEFAULT 'active',
            notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY name (first_name, last_name),
            KEY certification_level (certification_level),
            KEY status (status),
            KEY email (email)
        ) $charset_collate;";
        dbDelta($sql);
        
        // Activity log table - System activity tracking
        $table_name = $wpdb->prefix . 'vsl_activity_log';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20),
            action varchar(100) NOT NULL,
            object_type varchar(50),
            object_id bigint(20),
            description text,
            ip_address varchar(45),
            user_agent text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY action (action),
            KEY object_type (object_type),
            KEY object_id (object_id),
            KEY created_at (created_at)
        ) $charset_collate;";
        dbDelta($sql);
        
        // Update database version
        update_option('vsl_db_version', '1.0.0');
    }
    
    /**
     * Check WordPress version
     */
    private static function check_wp_version() {
        global $wp_version;
        return version_compare($wp_version, '5.8', '>=');
    }
    
    /**
     * Check PHP version
     */
    private static function check_php_version() {
        return version_compare(PHP_VERSION, '7.4', '>=');
    }
    
    /**
     * Load required dependencies for activation
     */
    private static function load_dependencies() {
        // Only load files that exist during activation
        $required_files = array(
            VIREO_LEAGUE_CORE_DIR . 'includes/shared/class-vsl-utilities.php',
            VIREO_LEAGUE_CORE_DIR . 'includes/core/class-vsl-capabilities.php',
            VIREO_LEAGUE_CORE_DIR . 'includes/core/class-vsl-post-types.php',
            VIREO_LEAGUE_CORE_DIR . 'includes/core/class-vsl-taxonomies.php',
        );
        
        foreach ($required_files as $file) {
            if (file_exists($file)) {
                require_once $file;
            }
        }
    }
    
    /**
     * Set default plugin options
     */
    private static function set_default_options() {
        // General settings
        $general_defaults = array(
            'default_sport' => 'soccer',
            'date_format' => 'Y-m-d',
            'time_format' => 'H:i',
            'timezone' => get_option('timezone_string', 'America/New_York'),
            'enable_public_registration' => false,
            'require_approval' => true,
        );
        
        add_option('vsl_general_settings', $general_defaults);
        
        // League settings
        $league_defaults = array(
            'season_format' => 'calendar_year',
            'standings_tiebreaker' => 'goal_difference',
            'match_duration' => 90,
            'allow_draws' => true,
            'points_win' => 3,
            'points_draw' => 1,
            'points_loss' => 0,
        );
        
        add_option('vsl_league_settings', $league_defaults);
        
        // Display settings
        $display_defaults = array(
            'theme_color' => '#059669',
            'show_logos' => true,
            'show_player_photos' => true,
            'show_stats' => true,
            'items_per_page' => 20,
            'enable_responsive_tables' => true,
        );
        
        add_option('vsl_display_settings', $display_defaults);
        
        // Notification settings
        $notification_defaults = array(
            'enable_email_notifications' => false,
            'admin_email' => get_option('admin_email'),
            'send_match_reminders' => false,
            'reminder_hours' => 24,
        );
        
        add_option('vsl_notification_settings', $notification_defaults);
    }
    
    /**
     * Create default content for demo purposes
     */
    private static function create_default_content() {
        // Only create if no leagues exist
        $existing_leagues = get_posts(array(
            'post_type' => 'vsl_league',
            'posts_per_page' => 1,
            'post_status' => 'any'
        ));
        
        if (!empty($existing_leagues)) {
            return;
        }
        
        // Create sample league
        $league_id = wp_insert_post(array(
            'post_title' => __('Sample Soccer League', 'vireo-league'),
            'post_content' => __('This is a sample soccer league created during plugin activation. You can edit or delete this league.', 'vireo-league'),
            'post_type' => 'vsl_league',
            'post_status' => 'publish',
            'post_author' => get_current_user_id(),
        ));
        
        if ($league_id && !is_wp_error($league_id)) {
            // Add league meta
            update_post_meta($league_id, '_vsl_sport', 'soccer');
            if (class_exists('VSL_Utilities')) {
                update_post_meta($league_id, '_vsl_league_code', VSL_Utilities::generate_league_code());
            } else {
                update_post_meta($league_id, '_vsl_league_code', 'SL' . date('Y') . '001');
            }
            update_post_meta($league_id, '_vsl_status', 'active');
            update_post_meta($league_id, '_vsl_organizer', get_bloginfo('name'));
            update_post_meta($league_id, '_vsl_contact_email', get_option('admin_email'));
            
            // Create sample season
            $season_id = wp_insert_post(array(
                'post_title' => __('2025 Season', 'vireo-league'),
                'post_content' => __('Sample season for the demo league.', 'vireo-league'),
                'post_type' => 'vsl_season',
                'post_status' => 'publish',
                'post_author' => get_current_user_id(),
            ));
            
            if ($season_id && !is_wp_error($season_id)) {
                update_post_meta($season_id, '_vsl_league_id', $league_id);
                update_post_meta($season_id, '_vsl_start_date', date('Y-01-01'));
                update_post_meta($season_id, '_vsl_end_date', date('Y-12-31'));
                update_post_meta($season_id, '_vsl_status', 'active');
                update_post_meta($season_id, '_vsl_is_current', '1');
            }
            
            // Create sample teams
            $teams = array(
                __('Eagles FC', 'vireo-league'),
                __('Lions United', 'vireo-league'),
                __('Sharks FC', 'vireo-league'),
                __('Tigers SC', 'vireo-league'),
            );
            
            foreach ($teams as $team_name) {
                $team_id = wp_insert_post(array(
                    'post_title' => $team_name,
                    'post_content' => sprintf(__('Sample team: %s', 'vireo-league'), $team_name),
                    'post_type' => 'vsl_team',
                    'post_status' => 'publish',
                    'post_author' => get_current_user_id(),
                ));
                
                if ($team_id && !is_wp_error($team_id)) {
                    update_post_meta($team_id, '_vsl_league_id', $league_id);
                    if (class_exists('VSL_Utilities')) {
                        update_post_meta($team_id, '_vsl_team_code', VSL_Utilities::generate_team_code());
                    } else {
                        update_post_meta($team_id, '_vsl_team_code', 'T' . str_pad(($team_id % 1000), 3, '0', STR_PAD_LEFT));
                    }
                    update_post_meta($team_id, '_vsl_status', 'active');
                    update_post_meta($team_id, '_vsl_founded_year', date('Y'));
                }
            }
        }
    }
    
    /**
     * Schedule recurring events
     */
    private static function schedule_events() {
        // Schedule standings update (runs every hour)
        if (!wp_next_scheduled('vsl_update_standings')) {
            wp_schedule_event(time(), 'hourly', 'vsl_update_standings');
        }
        
        // Schedule cleanup (runs daily)
        if (!wp_next_scheduled('vsl_daily_cleanup')) {
            wp_schedule_event(time(), 'daily', 'vsl_daily_cleanup');
        }
        
        // Schedule statistics calculation (runs daily)
        if (!wp_next_scheduled('vsl_calculate_statistics')) {
            wp_schedule_event(time(), 'daily', 'vsl_calculate_statistics');
        }
    }
}