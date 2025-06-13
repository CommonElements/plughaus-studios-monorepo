<?php
/**
 * Database schema and management for Vireo Sports League
 * 
 * @package Vireo_Sports_League
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class VSL_Database {
    
    /**
     * Initialize database hooks
     */
    public static function init() {
        add_action('init', array(__CLASS__, 'maybe_create_tables'));
    }
    
    /**
     * Create database tables if they don't exist
     */
    public static function maybe_create_tables() {
        $current_version = get_option('vsl_db_version', '0.0.0');
        
        if (version_compare($current_version, VIREO_LEAGUE_VERSION, '<')) {
            self::create_tables();
            update_option('vsl_db_version', VIREO_LEAGUE_VERSION);
        }
    }
    
    /**
     * Create all database tables
     */
    public static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Player statistics table
        $stats_table = $wpdb->prefix . 'vsl_player_stats';
        $stats_sql = "CREATE TABLE $stats_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            player_id bigint(20) unsigned NOT NULL,
            match_id bigint(20) unsigned NOT NULL,
            season_id bigint(20) unsigned NOT NULL,
            team_id bigint(20) unsigned NOT NULL,
            stat_type varchar(50) NOT NULL,
            stat_value varchar(255) NOT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY player_id (player_id),
            KEY match_id (match_id),
            KEY season_id (season_id),
            KEY team_id (team_id),
            KEY stat_type (stat_type),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        // Team statistics table
        $team_stats_table = $wpdb->prefix . 'vsl_team_stats';
        $team_stats_sql = "CREATE TABLE $team_stats_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            team_id bigint(20) unsigned NOT NULL,
            season_id bigint(20) unsigned NOT NULL,
            league_id bigint(20) unsigned NOT NULL,
            matches_played int(11) NOT NULL DEFAULT 0,
            wins int(11) NOT NULL DEFAULT 0,
            draws int(11) NOT NULL DEFAULT 0,
            losses int(11) NOT NULL DEFAULT 0,
            goals_for int(11) NOT NULL DEFAULT 0,
            goals_against int(11) NOT NULL DEFAULT 0,
            goal_difference int(11) NOT NULL DEFAULT 0,
            points int(11) NOT NULL DEFAULT 0,
            position int(11) NOT NULL DEFAULT 0,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY team_season (team_id, season_id),
            KEY team_id (team_id),
            KEY season_id (season_id),
            KEY league_id (league_id),
            KEY points (points),
            KEY position (position)
        ) $charset_collate;";
        
        // Match events table (goals, cards, substitutions, etc.)
        $events_table = $wpdb->prefix . 'vsl_match_events';
        $events_sql = "CREATE TABLE $events_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            match_id bigint(20) unsigned NOT NULL,
            player_id bigint(20) unsigned DEFAULT NULL,
            team_id bigint(20) unsigned NOT NULL,
            event_type varchar(50) NOT NULL,
            event_time int(11) NOT NULL DEFAULT 0,
            event_data text DEFAULT NULL,
            notes text DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY match_id (match_id),
            KEY player_id (player_id),
            KEY team_id (team_id),
            KEY event_type (event_type),
            KEY event_time (event_time)
        ) $charset_collate;";
        
        // Activity log table
        $activity_table = $wpdb->prefix . 'vsl_activity_log';
        $activity_sql = "CREATE TABLE $activity_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            type varchar(50) NOT NULL,
            message text NOT NULL,
            object_id bigint(20) unsigned DEFAULT 0,
            object_type varchar(50) DEFAULT NULL,
            user_id bigint(20) unsigned DEFAULT 0,
            ip_address varchar(45) DEFAULT NULL,
            user_agent text DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY type (type),
            KEY object_id (object_id),
            KEY user_id (user_id),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        // League settings table
        $settings_table = $wpdb->prefix . 'vsl_league_settings';
        $settings_sql = "CREATE TABLE $settings_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            league_id bigint(20) unsigned NOT NULL,
            setting_key varchar(100) NOT NULL,
            setting_value longtext DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY league_setting (league_id, setting_key),
            KEY league_id (league_id),
            KEY setting_key (setting_key)
        ) $charset_collate;";
        
        // Standings cache table (for performance)
        $standings_table = $wpdb->prefix . 'vsl_standings_cache';
        $standings_sql = "CREATE TABLE $standings_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            league_id bigint(20) unsigned NOT NULL,
            season_id bigint(20) unsigned NOT NULL,
            team_id bigint(20) unsigned NOT NULL,
            position int(11) NOT NULL DEFAULT 0,
            played int(11) NOT NULL DEFAULT 0,
            wins int(11) NOT NULL DEFAULT 0,
            draws int(11) NOT NULL DEFAULT 0,
            losses int(11) NOT NULL DEFAULT 0,
            goals_for int(11) NOT NULL DEFAULT 0,
            goals_against int(11) NOT NULL DEFAULT 0,
            goal_difference int(11) NOT NULL DEFAULT 0,
            points int(11) NOT NULL DEFAULT 0,
            form varchar(20) DEFAULT NULL,
            last_updated datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY league_season_team (league_id, season_id, team_id),
            KEY league_season (league_id, season_id),
            KEY position (position),
            KEY points (points)
        ) $charset_collate;";
        
        // Player season stats summary table
        $player_season_table = $wpdb->prefix . 'vsl_player_season_stats';
        $player_season_sql = "CREATE TABLE $player_season_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            player_id bigint(20) unsigned NOT NULL,
            team_id bigint(20) unsigned NOT NULL,
            season_id bigint(20) unsigned NOT NULL,
            league_id bigint(20) unsigned NOT NULL,
            appearances int(11) NOT NULL DEFAULT 0,
            goals int(11) NOT NULL DEFAULT 0,
            assists int(11) NOT NULL DEFAULT 0,
            yellow_cards int(11) NOT NULL DEFAULT 0,
            red_cards int(11) NOT NULL DEFAULT 0,
            minutes_played int(11) NOT NULL DEFAULT 0,
            custom_stats longtext DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY player_season (player_id, season_id),
            KEY player_id (player_id),
            KEY team_id (team_id),
            KEY season_id (season_id),
            KEY league_id (league_id),
            KEY goals (goals),
            KEY assists (assists)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        dbDelta($stats_sql);
        dbDelta($team_stats_sql);
        dbDelta($events_sql);
        dbDelta($activity_sql);
        dbDelta($settings_sql);
        dbDelta($standings_sql);
        dbDelta($player_season_sql);
        
        // Log activity
        VSL_Utilities::log_activity('system', 'Database tables created/updated', 0, 0);
    }
    
    /**
     * Get player statistics for a season
     */
    public static function get_player_season_stats($player_id, $season_id) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'vsl_player_season_stats';
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE player_id = %d AND season_id = %d",
            $player_id,
            $season_id
        ));
    }
    
    /**
     * Update player season statistics
     */
    public static function update_player_season_stats($player_id, $team_id, $season_id, $league_id, $stats) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'vsl_player_season_stats';
        
        $existing = self::get_player_season_stats($player_id, $season_id);
        
        $data = array(
            'player_id' => $player_id,
            'team_id' => $team_id,
            'season_id' => $season_id,
            'league_id' => $league_id,
            'appearances' => isset($stats['appearances']) ? $stats['appearances'] : 0,
            'goals' => isset($stats['goals']) ? $stats['goals'] : 0,
            'assists' => isset($stats['assists']) ? $stats['assists'] : 0,
            'yellow_cards' => isset($stats['yellow_cards']) ? $stats['yellow_cards'] : 0,
            'red_cards' => isset($stats['red_cards']) ? $stats['red_cards'] : 0,
            'minutes_played' => isset($stats['minutes_played']) ? $stats['minutes_played'] : 0,
            'custom_stats' => isset($stats['custom_stats']) ? maybe_serialize($stats['custom_stats']) : null,
        );
        
        if ($existing) {
            $wpdb->update($table, $data, array('player_id' => $player_id, 'season_id' => $season_id));
        } else {
            $wpdb->insert($table, $data);
        }
    }
    
    /**
     * Record match event
     */
    public static function record_match_event($match_id, $team_id, $event_type, $event_time, $player_id = null, $event_data = null, $notes = null) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'vsl_match_events';
        
        return $wpdb->insert($table, array(
            'match_id' => $match_id,
            'team_id' => $team_id,
            'player_id' => $player_id,
            'event_type' => $event_type,
            'event_time' => $event_time,
            'event_data' => $event_data,
            'notes' => $notes,
        ));
    }
    
    /**
     * Get match events
     */
    public static function get_match_events($match_id, $event_type = null) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'vsl_match_events';
        
        $sql = "SELECT * FROM $table WHERE match_id = %d";
        $params = array($match_id);
        
        if ($event_type) {
            $sql .= " AND event_type = %s";
            $params[] = $event_type;
        }
        
        $sql .= " ORDER BY event_time ASC";
        
        return $wpdb->get_results($wpdb->prepare($sql, $params));
    }
    
    /**
     * Update standings cache
     */
    public static function update_standings_cache($league_id, $season_id) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'vsl_standings_cache';
        
        // Clear existing cache for this league/season
        $wpdb->delete($table, array(
            'league_id' => $league_id,
            'season_id' => $season_id
        ));
        
        // Calculate fresh standings
        $standings = VSL_Utilities::calculate_standings($league_id, $season_id);
        
        // Insert new cache
        foreach ($standings as $position => $team_data) {
            $wpdb->insert($table, array(
                'league_id' => $league_id,
                'season_id' => $season_id,
                'team_id' => $team_data['team_id'],
                'position' => $position + 1,
                'played' => $team_data['played'],
                'wins' => $team_data['wins'],
                'draws' => $team_data['draws'],
                'losses' => $team_data['losses'],
                'goals_for' => $team_data['goals_for'],
                'goals_against' => $team_data['goals_against'],
                'goal_difference' => $team_data['goal_difference'],
                'points' => $team_data['points'],
            ));
        }
    }
    
    /**
     * Get cached standings
     */
    public static function get_cached_standings($league_id, $season_id) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'vsl_standings_cache';
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT sc.*, p.post_title as team_name 
             FROM $table sc
             LEFT JOIN {$wpdb->posts} p ON sc.team_id = p.ID
             WHERE sc.league_id = %d AND sc.season_id = %d
             ORDER BY sc.position ASC",
            $league_id,
            $season_id
        ));
    }
    
    /**
     * Drop all plugin tables (for uninstall)
     */
    public static function drop_tables() {
        global $wpdb;
        
        $tables = array(
            'vsl_player_stats',
            'vsl_team_stats',
            'vsl_match_events',
            'vsl_activity_log',
            'vsl_league_settings',
            'vsl_standings_cache',
            'vsl_player_season_stats',
        );
        
        foreach ($tables as $table) {
            $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}{$table}");
        }
    }
}