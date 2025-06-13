<?php
/**
 * REST API functionality for Vireo Sports League
 */

if (!defined('ABSPATH')) {
    exit;
}

class VSL_REST_API {
    
    /**
     * API namespace
     */
    const NAMESPACE = 'vsl/v1';
    
    /**
     * Register REST API routes
     */
    public static function register_routes() {
        // Teams endpoints
        register_rest_route(self::NAMESPACE, '/teams', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array(__CLASS__, 'get_teams'),
                'permission_callback' => '__return_true',
            ),
            array(
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => array(__CLASS__, 'create_team'),
                'permission_callback' => array(__CLASS__, 'create_item_permissions_check'),
            ),
        ));
        
        register_rest_route(self::NAMESPACE, '/teams/(?P<id>\d+)', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array(__CLASS__, 'get_team'),
                'permission_callback' => '__return_true',
            ),
            array(
                'methods' => WP_REST_Server::EDITABLE,
                'callback' => array(__CLASS__, 'update_team'),
                'permission_callback' => array(__CLASS__, 'update_item_permissions_check'),
            ),
            array(
                'methods' => WP_REST_Server::DELETABLE,
                'callback' => array(__CLASS__, 'delete_team'),
                'permission_callback' => array(__CLASS__, 'delete_item_permissions_check'),
            ),
        ));
        
        // Players endpoints
        register_rest_route(self::NAMESPACE, '/players', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array(__CLASS__, 'get_players'),
                'permission_callback' => '__return_true',
            ),
            array(
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => array(__CLASS__, 'create_player'),
                'permission_callback' => array(__CLASS__, 'create_item_permissions_check'),
            ),
        ));
        
        // Matches endpoints
        register_rest_route(self::NAMESPACE, '/matches', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array(__CLASS__, 'get_matches'),
                'permission_callback' => '__return_true',
            ),
            array(
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => array(__CLASS__, 'create_match'),
                'permission_callback' => array(__CLASS__, 'create_item_permissions_check'),
            ),
        ));
        
        // Standings endpoint
        register_rest_route(self::NAMESPACE, '/standings', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array(__CLASS__, 'get_standings'),
            'permission_callback' => '__return_true',
        ));
    }
    
    /**
     * Get all teams
     */
    public static function get_teams($request) {
        $args = array(
            'post_type' => 'vsl_team',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        );
        
        $teams = get_posts($args);
        $data = array();
        
        foreach ($teams as $team) {
            $data[] = self::prepare_team_for_response($team);
        }
        
        return rest_ensure_response($data);
    }
    
    /**
     * Get single team
     */
    public static function get_team($request) {
        $id = (int) $request['id'];
        $team = get_post($id);
        
        if (empty($team) || $team->post_type !== 'vsl_team') {
            return new WP_Error('vsl_team_not_found', 'Team not found', array('status' => 404));
        }
        
        $data = self::prepare_team_for_response($team);
        return rest_ensure_response($data);
    }
    
    /**
     * Create a team
     */
    public static function create_team($request) {
        $team_data = array(
            'post_title' => sanitize_text_field($request['name']),
            'post_type' => 'vsl_team',
            'post_status' => 'publish',
        );
        
        $team_id = wp_insert_post($team_data);
        
        if (is_wp_error($team_id)) {
            return $team_id;
        }
        
        // Update meta fields
        if (isset($request['coach'])) {
            update_post_meta($team_id, '_vsl_coach', sanitize_text_field($request['coach']));
        }
        
        if (isset($request['home_venue'])) {
            update_post_meta($team_id, '_vsl_home_venue', sanitize_text_field($request['home_venue']));
        }
        
        $team = get_post($team_id);
        $data = self::prepare_team_for_response($team);
        
        return rest_ensure_response($data);
    }
    
    /**
     * Update a team
     */
    public static function update_team($request) {
        $id = (int) $request['id'];
        
        $team_data = array(
            'ID' => $id,
            'post_title' => sanitize_text_field($request['name']),
        );
        
        $updated = wp_update_post($team_data);
        
        if (is_wp_error($updated)) {
            return $updated;
        }
        
        // Update meta fields
        if (isset($request['coach'])) {
            update_post_meta($id, '_vsl_coach', sanitize_text_field($request['coach']));
        }
        
        if (isset($request['home_venue'])) {
            update_post_meta($id, '_vsl_home_venue', sanitize_text_field($request['home_venue']));
        }
        
        $team = get_post($id);
        $data = self::prepare_team_for_response($team);
        
        return rest_ensure_response($data);
    }
    
    /**
     * Delete a team
     */
    public static function delete_team($request) {
        $id = (int) $request['id'];
        $deleted = wp_delete_post($id, true);
        
        if (!$deleted) {
            return new WP_Error('vsl_delete_failed', 'Failed to delete team', array('status' => 500));
        }
        
        return rest_ensure_response(array('deleted' => true));
    }
    
    /**
     * Get all players
     */
    public static function get_players($request) {
        $args = array(
            'post_type' => 'vsl_player',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        );
        
        if (isset($request['team'])) {
            $args['meta_query'] = array(
                array(
                    'key' => '_vsl_team',
                    'value' => (int) $request['team'],
                ),
            );
        }
        
        $players = get_posts($args);
        $data = array();
        
        foreach ($players as $player) {
            $data[] = self::prepare_player_for_response($player);
        }
        
        return rest_ensure_response($data);
    }
    
    /**
     * Create a player
     */
    public static function create_player($request) {
        $player_data = array(
            'post_title' => sanitize_text_field($request['name']),
            'post_type' => 'vsl_player',
            'post_status' => 'publish',
        );
        
        $player_id = wp_insert_post($player_data);
        
        if (is_wp_error($player_id)) {
            return $player_id;
        }
        
        // Update meta fields
        if (isset($request['team'])) {
            update_post_meta($player_id, '_vsl_team', (int) $request['team']);
        }
        
        if (isset($request['jersey_number'])) {
            update_post_meta($player_id, '_vsl_jersey_number', sanitize_text_field($request['jersey_number']));
        }
        
        if (isset($request['position'])) {
            update_post_meta($player_id, '_vsl_position', sanitize_text_field($request['position']));
        }
        
        $player = get_post($player_id);
        $data = self::prepare_player_for_response($player);
        
        return rest_ensure_response($data);
    }
    
    /**
     * Get matches
     */
    public static function get_matches($request) {
        $args = array(
            'post_type' => 'vsl_match',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby' => 'meta_value',
            'meta_key' => '_vsl_match_date',
            'order' => 'ASC',
        );
        
        $matches = get_posts($args);
        $data = array();
        
        foreach ($matches as $match) {
            $data[] = self::prepare_match_for_response($match);
        }
        
        return rest_ensure_response($data);
    }
    
    /**
     * Create a match
     */
    public static function create_match($request) {
        $match_data = array(
            'post_title' => sanitize_text_field($request['title']),
            'post_type' => 'vsl_match',
            'post_status' => 'publish',
        );
        
        $match_id = wp_insert_post($match_data);
        
        if (is_wp_error($match_id)) {
            return $match_id;
        }
        
        // Update meta fields
        update_post_meta($match_id, '_vsl_home_team', (int) $request['home_team']);
        update_post_meta($match_id, '_vsl_away_team', (int) $request['away_team']);
        update_post_meta($match_id, '_vsl_match_date', sanitize_text_field($request['match_date']));
        update_post_meta($match_id, '_vsl_venue', sanitize_text_field($request['venue']));
        
        if (isset($request['home_score'])) {
            update_post_meta($match_id, '_vsl_home_score', (int) $request['home_score']);
        }
        
        if (isset($request['away_score'])) {
            update_post_meta($match_id, '_vsl_away_score', (int) $request['away_score']);
        }
        
        $match = get_post($match_id);
        $data = self::prepare_match_for_response($match);
        
        return rest_ensure_response($data);
    }
    
    /**
     * Get standings
     */
    public static function get_standings($request) {
        // This would calculate standings based on match results
        // For now, return sample data
        $standings = array(
            array(
                'team' => 'Team A',
                'played' => 10,
                'won' => 7,
                'drawn' => 2,
                'lost' => 1,
                'points' => 23,
            ),
            array(
                'team' => 'Team B',
                'played' => 10,
                'won' => 6,
                'drawn' => 3,
                'lost' => 1,
                'points' => 21,
            ),
        );
        
        return rest_ensure_response($standings);
    }
    
    /**
     * Prepare team for response
     */
    private static function prepare_team_for_response($team) {
        return array(
            'id' => $team->ID,
            'name' => $team->post_title,
            'slug' => $team->post_name,
            'coach' => get_post_meta($team->ID, '_vsl_coach', true),
            'home_venue' => get_post_meta($team->ID, '_vsl_home_venue', true),
        );
    }
    
    /**
     * Prepare player for response
     */
    private static function prepare_player_for_response($player) {
        return array(
            'id' => $player->ID,
            'name' => $player->post_title,
            'team' => (int) get_post_meta($player->ID, '_vsl_team', true),
            'jersey_number' => get_post_meta($player->ID, '_vsl_jersey_number', true),
            'position' => get_post_meta($player->ID, '_vsl_position', true),
        );
    }
    
    /**
     * Prepare match for response
     */
    private static function prepare_match_for_response($match) {
        return array(
            'id' => $match->ID,
            'title' => $match->post_title,
            'home_team' => (int) get_post_meta($match->ID, '_vsl_home_team', true),
            'away_team' => (int) get_post_meta($match->ID, '_vsl_away_team', true),
            'match_date' => get_post_meta($match->ID, '_vsl_match_date', true),
            'venue' => get_post_meta($match->ID, '_vsl_venue', true),
            'home_score' => get_post_meta($match->ID, '_vsl_home_score', true),
            'away_score' => get_post_meta($match->ID, '_vsl_away_score', true),
        );
    }
    
    /**
     * Check permissions for creating items
     */
    public static function create_item_permissions_check($request) {
        return current_user_can('edit_posts');
    }
    
    /**
     * Check permissions for updating items
     */
    public static function update_item_permissions_check($request) {
        return current_user_can('edit_posts');
    }
    
    /**
     * Check permissions for deleting items
     */
    public static function delete_item_permissions_check($request) {
        return current_user_can('delete_posts');
    }
}