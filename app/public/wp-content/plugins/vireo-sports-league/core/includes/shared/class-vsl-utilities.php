<?php
/**
 * Shared utilities for Vireo Sports League
 * 
 * @package Vireo_Sports_League
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class VSL_Utilities {
    
    /**
     * Check if pro features are available
     */
    public static function is_pro() {
        $league = Vireo_Sports_League::get_instance();
        return $league->is_pro();
    }
    
    /**
     * Get supported sports and their configurations
     */
    public static function get_supported_sports() {
        return array(
            'soccer' => array(
                'name' => __('Soccer', 'vireo-league'),
                'icon' => 'soccer',
                'positions' => array(
                    'goalkeeper' => __('Goalkeeper', 'vireo-league'),
                    'defender' => __('Defender', 'vireo-league'),
                    'midfielder' => __('Midfielder', 'vireo-league'),
                    'forward' => __('Forward', 'vireo-league'),
                ),
                'stats' => array(
                    'goals' => __('Goals', 'vireo-league'),
                    'assists' => __('Assists', 'vireo-league'),
                    'yellow_cards' => __('Yellow Cards', 'vireo-league'),
                    'red_cards' => __('Red Cards', 'vireo-league'),
                    'saves' => __('Saves', 'vireo-league'),
                    'minutes_played' => __('Minutes Played', 'vireo-league'),
                ),
                'match_stats' => array(
                    'possession' => __('Possession %', 'vireo-league'),
                    'shots' => __('Shots', 'vireo-league'),
                    'shots_on_target' => __('Shots on Target', 'vireo-league'),
                    'corners' => __('Corner Kicks', 'vireo-league'),
                    'fouls' => __('Fouls', 'vireo-league'),
                ),
                'points_win' => 3,
                'points_draw' => 1,
                'points_loss' => 0,
            ),
            'basketball' => array(
                'name' => __('Basketball', 'vireo-league'),
                'icon' => 'basketball',
                'positions' => array(
                    'point_guard' => __('Point Guard', 'vireo-league'),
                    'shooting_guard' => __('Shooting Guard', 'vireo-league'),
                    'small_forward' => __('Small Forward', 'vireo-league'),
                    'power_forward' => __('Power Forward', 'vireo-league'),
                    'center' => __('Center', 'vireo-league'),
                ),
                'stats' => array(
                    'points' => __('Points', 'vireo-league'),
                    'rebounds' => __('Rebounds', 'vireo-league'),
                    'assists' => __('Assists', 'vireo-league'),
                    'steals' => __('Steals', 'vireo-league'),
                    'blocks' => __('Blocks', 'vireo-league'),
                    'turnovers' => __('Turnovers', 'vireo-league'),
                    'fouls' => __('Fouls', 'vireo-league'),
                    'minutes_played' => __('Minutes Played', 'vireo-league'),
                ),
                'match_stats' => array(
                    'field_goals' => __('Field Goals Made/Attempted', 'vireo-league'),
                    'three_pointers' => __('3-Pointers Made/Attempted', 'vireo-league'),
                    'free_throws' => __('Free Throws Made/Attempted', 'vireo-league'),
                ),
                'points_win' => 2,
                'points_draw' => 0,
                'points_loss' => 0,
            ),
            'baseball' => array(
                'name' => __('Baseball', 'vireo-league'),
                'icon' => 'baseball',
                'positions' => array(
                    'pitcher' => __('Pitcher', 'vireo-league'),
                    'catcher' => __('Catcher', 'vireo-league'),
                    'first_base' => __('First Base', 'vireo-league'),
                    'second_base' => __('Second Base', 'vireo-league'),
                    'third_base' => __('Third Base', 'vireo-league'),
                    'shortstop' => __('Shortstop', 'vireo-league'),
                    'left_field' => __('Left Field', 'vireo-league'),
                    'center_field' => __('Center Field', 'vireo-league'),
                    'right_field' => __('Right Field', 'vireo-league'),
                ),
                'stats' => array(
                    'batting_average' => __('Batting Average', 'vireo-league'),
                    'home_runs' => __('Home Runs', 'vireo-league'),
                    'rbi' => __('RBI', 'vireo-league'),
                    'runs' => __('Runs', 'vireo-league'),
                    'hits' => __('Hits', 'vireo-league'),
                    'strikeouts' => __('Strikeouts', 'vireo-league'),
                    'walks' => __('Walks', 'vireo-league'),
                    'era' => __('ERA', 'vireo-league'),
                ),
                'match_stats' => array(
                    'innings_pitched' => __('Innings Pitched', 'vireo-league'),
                    'hits_allowed' => __('Hits Allowed', 'vireo-league'),
                    'earned_runs' => __('Earned Runs', 'vireo-league'),
                ),
                'points_win' => 2,
                'points_draw' => 0,
                'points_loss' => 0,
            ),
            'volleyball' => array(
                'name' => __('Volleyball', 'vireo-league'),
                'icon' => 'volleyball',
                'positions' => array(
                    'setter' => __('Setter', 'vireo-league'),
                    'outside_hitter' => __('Outside Hitter', 'vireo-league'),
                    'middle_blocker' => __('Middle Blocker', 'vireo-league'),
                    'opposite' => __('Opposite', 'vireo-league'),
                    'libero' => __('Libero', 'vireo-league'),
                ),
                'stats' => array(
                    'kills' => __('Kills', 'vireo-league'),
                    'assists' => __('Assists', 'vireo-league'),
                    'blocks' => __('Blocks', 'vireo-league'),
                    'digs' => __('Digs', 'vireo-league'),
                    'aces' => __('Aces', 'vireo-league'),
                    'errors' => __('Errors', 'vireo-league'),
                ),
                'match_stats' => array(
                    'attack_percentage' => __('Attack %', 'vireo-league'),
                    'serving_percentage' => __('Serving %', 'vireo-league'),
                ),
                'points_win' => 3,
                'points_draw' => 0,
                'points_loss' => 0,
            ),
        );
    }
    
    /**
     * Get age divisions
     */
    public static function get_age_divisions() {
        return array(
            'u8' => __('Under 8', 'vireo-league'),
            'u10' => __('Under 10', 'vireo-league'),
            'u12' => __('Under 12', 'vireo-league'),
            'u14' => __('Under 14', 'vireo-league'),
            'u16' => __('Under 16', 'vireo-league'),
            'u18' => __('Under 18', 'vireo-league'),
            'adult' => __('Adult', 'vireo-league'),
            'senior' => __('Senior (35+)', 'vireo-league'),
        );
    }
    
    /**
     * Get match statuses
     */
    public static function get_match_statuses() {
        return array(
            'scheduled' => __('Scheduled', 'vireo-league'),
            'in_progress' => __('In Progress', 'vireo-league'),
            'completed' => __('Completed', 'vireo-league'),
            'postponed' => __('Postponed', 'vireo-league'),
            'cancelled' => __('Cancelled', 'vireo-league'),
        );
    }
    
    /**
     * Get player statuses
     */
    public static function get_player_statuses() {
        return array(
            'active' => __('Active', 'vireo-league'),
            'inactive' => __('Inactive', 'vireo-league'),
            'injured' => __('Injured', 'vireo-league'),
            'suspended' => __('Suspended', 'vireo-league'),
            'retired' => __('Retired', 'vireo-league'),
        );
    }
    
    /**
     * Get team statuses
     */
    public static function get_team_statuses() {
        return array(
            'active' => __('Active', 'vireo-league'),
            'inactive' => __('Inactive', 'vireo-league'),
            'suspended' => __('Suspended', 'vireo-league'),
            'disbanded' => __('Disbanded', 'vireo-league'),
        );
    }
    
    /**
     * Format score display
     */
    public static function format_score($home_score, $away_score, $sport = 'soccer') {
        return sprintf('%d - %d', $home_score, $away_score);
    }
    
    /**
     * Calculate standings for teams
     */
    public static function calculate_standings($league_id, $season_id = null) {
        global $wpdb;
        
        $season_condition = $season_id ? $wpdb->prepare(" AND season_id = %d", $season_id) : "";
        
        // Get all matches for the league
        $matches = $wpdb->get_results($wpdb->prepare(
            "SELECT m.*, 
                    ht.post_title as home_team_name,
                    at.post_title as away_team_name
             FROM {$wpdb->prefix}vsl_matches m
             LEFT JOIN {$wpdb->posts} ht ON m.home_team_id = ht.ID
             LEFT JOIN {$wpdb->posts} at ON m.away_team_id = at.ID
             WHERE m.league_id = %d 
             AND m.status = 'completed'
             {$season_condition}
             ORDER BY m.match_date ASC",
            $league_id
        ));
        
        $standings = array();
        $sport_config = self::get_sport_config($league_id);
        
        // Initialize all teams
        $teams = self::get_league_teams($league_id);
        foreach ($teams as $team) {
            $standings[$team->ID] = array(
                'team_id' => $team->ID,
                'team_name' => $team->post_title,
                'played' => 0,
                'wins' => 0,
                'draws' => 0,
                'losses' => 0,
                'goals_for' => 0,
                'goals_against' => 0,
                'goal_difference' => 0,
                'points' => 0,
            );
        }
        
        // Process matches
        foreach ($matches as $match) {
            $home_id = $match->home_team_id;
            $away_id = $match->away_team_id;
            $home_score = intval($match->home_score);
            $away_score = intval($match->away_score);
            
            if (!isset($standings[$home_id]) || !isset($standings[$away_id])) {
                continue;
            }
            
            // Update played count
            $standings[$home_id]['played']++;
            $standings[$away_id]['played']++;
            
            // Update goals
            $standings[$home_id]['goals_for'] += $home_score;
            $standings[$home_id]['goals_against'] += $away_score;
            $standings[$away_id]['goals_for'] += $away_score;
            $standings[$away_id]['goals_against'] += $home_score;
            
            // Determine result and assign points
            if ($home_score > $away_score) {
                // Home team wins
                $standings[$home_id]['wins']++;
                $standings[$home_id]['points'] += $sport_config['points_win'];
                $standings[$away_id]['losses']++;
                $standings[$away_id]['points'] += $sport_config['points_loss'];
            } elseif ($away_score > $home_score) {
                // Away team wins
                $standings[$away_id]['wins']++;
                $standings[$away_id]['points'] += $sport_config['points_win'];
                $standings[$home_id]['losses']++;
                $standings[$home_id]['points'] += $sport_config['points_loss'];
            } else {
                // Draw
                $standings[$home_id]['draws']++;
                $standings[$home_id]['points'] += $sport_config['points_draw'];
                $standings[$away_id]['draws']++;
                $standings[$away_id]['points'] += $sport_config['points_draw'];
            }
        }
        
        // Calculate goal difference
        foreach ($standings as &$team) {
            $team['goal_difference'] = $team['goals_for'] - $team['goals_against'];
        }
        
        // Sort standings
        usort($standings, function($a, $b) {
            // Primary: Points (descending)
            if ($a['points'] !== $b['points']) {
                return $b['points'] - $a['points'];
            }
            
            // Secondary: Goal difference (descending)
            if ($a['goal_difference'] !== $b['goal_difference']) {
                return $b['goal_difference'] - $a['goal_difference'];
            }
            
            // Tertiary: Goals for (descending)
            if ($a['goals_for'] !== $b['goals_for']) {
                return $b['goals_for'] - $a['goals_for'];
            }
            
            // Quaternary: Alphabetical by team name
            return strcmp($a['team_name'], $b['team_name']);
        });
        
        return $standings;
    }
    
    /**
     * Get sport configuration for a league
     */
    public static function get_sport_config($league_id) {
        $sport = get_post_meta($league_id, '_vsl_sport', true);
        $sports = self::get_supported_sports();
        
        return isset($sports[$sport]) ? $sports[$sport] : $sports['soccer'];
    }
    
    /**
     * Get teams in a league
     */
    public static function get_league_teams($league_id) {
        return get_posts(array(
            'post_type' => 'vsl_team',
            'meta_query' => array(
                array(
                    'key' => '_vsl_league_id',
                    'value' => $league_id,
                    'compare' => '='
                )
            ),
            'posts_per_page' => -1,
            'post_status' => 'publish',
        ));
    }
    
    /**
     * Get players in a team
     */
    public static function get_team_players($team_id) {
        return get_posts(array(
            'post_type' => 'vsl_player',
            'meta_query' => array(
                array(
                    'key' => '_vsl_team_id',
                    'value' => $team_id,
                    'compare' => '='
                )
            ),
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'meta_key' => '_vsl_jersey_number',
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
        ));
    }
    
    /**
     * Generate unique reference codes
     */
    public static function generate_league_code() {
        return 'LG-' . strtoupper(wp_generate_password(8, false));
    }
    
    public static function generate_team_code() {
        return 'TM-' . strtoupper(wp_generate_password(6, false));
    }
    
    public static function generate_match_code() {
        return 'MT-' . date('Y') . '-' . strtoupper(wp_generate_password(6, false));
    }
    
    /**
     * Format date for display
     */
    public static function format_match_date($date, $include_time = true) {
        if (!$date) return '';
        
        $format = get_option('date_format');
        if ($include_time) {
            $format .= ' ' . get_option('time_format');
        }
        
        return date_i18n($format, strtotime($date));
    }
    
    /**
     * Check user capabilities for league management
     */
    public static function current_user_can_manage_leagues() {
        return current_user_can('manage_vsl_leagues') || current_user_can('manage_options');
    }
    
    /**
     * Check user capabilities for viewing league data
     */
    public static function current_user_can_view_leagues() {
        return current_user_can('view_vsl_leagues') || self::current_user_can_manage_leagues();
    }
    
    /**
     * Check if user can edit specific team
     */
    public static function current_user_can_edit_team($team_id) {
        if (self::current_user_can_manage_leagues()) {
            return true;
        }
        
        // Check if user is assigned as coach for this team
        $coach_id = get_post_meta($team_id, '_vsl_coach_id', true);
        return $coach_id && $coach_id == get_current_user_id();
    }
    
    /**
     * Log activity
     */
    public static function log_activity($type, $message, $object_id = 0, $user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        global $wpdb;
        
        $wpdb->insert(
            $wpdb->prefix . 'vsl_activity_log',
            array(
                'type' => $type,
                'message' => $message,
                'object_id' => $object_id,
                'user_id' => $user_id,
                'created_at' => current_time('mysql'),
            ),
            array('%s', '%s', '%d', '%d', '%s')
        );
    }
    
    /**
     * Get league settings
     */
    public static function get_league_settings() {
        $defaults = array(
            'default_sport' => 'soccer',
            'season_format' => 'calendar_year',
            'standings_tiebreaker' => 'goal_difference',
            'match_duration' => 90,
            'allow_draws' => true,
            'points_win' => 3,
            'points_draw' => 1,
            'points_loss' => 0,
        );
        
        $settings = get_option('vsl_league_settings', array());
        return wp_parse_args($settings, $defaults);
    }
    
    /**
     * Get display settings
     */
    public static function get_display_settings() {
        $defaults = array(
            'theme_color' => '#007cba',
            'show_logos' => true,
            'show_stats' => true,
            'date_format' => 'default',
            'timezone' => get_option('timezone_string', 'America/New_York'),
        );
        
        $settings = get_option('vsl_display_settings', array());
        return wp_parse_args($settings, $defaults);
    }
}