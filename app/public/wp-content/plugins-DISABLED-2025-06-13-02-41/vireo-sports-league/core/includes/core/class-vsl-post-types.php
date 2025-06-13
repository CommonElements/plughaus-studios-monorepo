<?php
/**
 * Custom Post Types for PlugHaus Sports League
 * 
 * @package PlugHaus_Sports_League
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class VSL_Post_Types {
    
    /**
     * Initialize post types
     */
    public static function init() {
        add_action('init', array(__CLASS__, 'register_post_types'));
        add_action('add_meta_boxes', array(__CLASS__, 'add_meta_boxes'));
        add_action('save_post', array(__CLASS__, 'save_meta_boxes'));
    }
    
    /**
     * Register all custom post types
     */
    public static function register_post_types() {
        self::register_league();
        self::register_team();
        self::register_player();
        self::register_match();
        self::register_season();
    }
    
    /**
     * Register League post type
     */
    private static function register_league() {
        $labels = array(
            'name' => _x('Leagues', 'Post Type General Name', 'vireo-league'),
            'singular_name' => _x('League', 'Post Type Singular Name', 'vireo-league'),
            'menu_name' => __('Leagues', 'vireo-league'),
            'name_admin_bar' => __('League', 'vireo-league'),
            'archives' => __('League Archives', 'vireo-league'),
            'attributes' => __('League Attributes', 'vireo-league'),
            'parent_item_colon' => __('Parent League:', 'vireo-league'),
            'all_items' => __('All Leagues', 'vireo-league'),
            'add_new_item' => __('Add New League', 'vireo-league'),
            'add_new' => __('Add New', 'vireo-league'),
            'new_item' => __('New League', 'vireo-league'),
            'edit_item' => __('Edit League', 'vireo-league'),
            'update_item' => __('Update League', 'vireo-league'),
            'view_item' => __('View League', 'vireo-league'),
            'view_items' => __('View Leagues', 'vireo-league'),
            'search_items' => __('Search Leagues', 'vireo-league'),
            'not_found' => __('Not found', 'vireo-league'),
            'not_found_in_trash' => __('Not found in Trash', 'vireo-league'),
            'featured_image' => __('League Logo', 'vireo-league'),
            'set_featured_image' => __('Set league logo', 'vireo-league'),
            'remove_featured_image' => __('Remove league logo', 'vireo-league'),
            'use_featured_image' => __('Use as league logo', 'vireo-league'),
        );
        
        $args = array(
            'label' => __('League', 'vireo-league'),
            'description' => __('Sports leagues and competitions', 'vireo-league'),
            'labels' => $labels,
            'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => false, // We'll add to custom menu
            'menu_position' => 25,
            'menu_icon' => 'dashicons-awards',
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'page',
            'show_in_rest' => true,
            'rewrite' => array('slug' => 'leagues'),
        );
        
        register_post_type('vsl_league', $args);
    }
    
    /**
     * Register Team post type
     */
    private static function register_team() {
        $labels = array(
            'name' => _x('Teams', 'Post Type General Name', 'vireo-league'),
            'singular_name' => _x('Team', 'Post Type Singular Name', 'vireo-league'),
            'menu_name' => __('Teams', 'vireo-league'),
            'name_admin_bar' => __('Team', 'vireo-league'),
            'archives' => __('Team Archives', 'vireo-league'),
            'attributes' => __('Team Attributes', 'vireo-league'),
            'parent_item_colon' => __('Parent Team:', 'vireo-league'),
            'all_items' => __('All Teams', 'vireo-league'),
            'add_new_item' => __('Add New Team', 'vireo-league'),
            'add_new' => __('Add New', 'vireo-league'),
            'new_item' => __('New Team', 'vireo-league'),
            'edit_item' => __('Edit Team', 'vireo-league'),
            'update_item' => __('Update Team', 'vireo-league'),
            'view_item' => __('View Team', 'vireo-league'),
            'view_items' => __('View Teams', 'vireo-league'),
            'search_items' => __('Search Teams', 'vireo-league'),
            'not_found' => __('Not found', 'vireo-league'),
            'not_found_in_trash' => __('Not found in Trash', 'vireo-league'),
            'featured_image' => __('Team Logo', 'vireo-league'),
            'set_featured_image' => __('Set team logo', 'vireo-league'),
            'remove_featured_image' => __('Remove team logo', 'vireo-league'),
            'use_featured_image' => __('Use as team logo', 'vireo-league'),
        );
        
        $args = array(
            'label' => __('Team', 'vireo-league'),
            'description' => __('Sports teams', 'vireo-league'),
            'labels' => $labels,
            'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => false,
            'menu_position' => 26,
            'menu_icon' => 'dashicons-groups',
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'page',
            'show_in_rest' => true,
            'rewrite' => array('slug' => 'teams'),
        );
        
        register_post_type('vsl_team', $args);
    }
    
    /**
     * Register Player post type
     */
    private static function register_player() {
        $labels = array(
            'name' => _x('Players', 'Post Type General Name', 'vireo-league'),
            'singular_name' => _x('Player', 'Post Type Singular Name', 'vireo-league'),
            'menu_name' => __('Players', 'vireo-league'),
            'name_admin_bar' => __('Player', 'vireo-league'),
            'archives' => __('Player Archives', 'vireo-league'),
            'attributes' => __('Player Attributes', 'vireo-league'),
            'parent_item_colon' => __('Parent Player:', 'vireo-league'),
            'all_items' => __('All Players', 'vireo-league'),
            'add_new_item' => __('Add New Player', 'vireo-league'),
            'add_new' => __('Add New', 'vireo-league'),
            'new_item' => __('New Player', 'vireo-league'),
            'edit_item' => __('Edit Player', 'vireo-league'),
            'update_item' => __('Update Player', 'vireo-league'),
            'view_item' => __('View Player', 'vireo-league'),
            'view_items' => __('View Players', 'vireo-league'),
            'search_items' => __('Search Players', 'vireo-league'),
            'not_found' => __('Not found', 'vireo-league'),
            'not_found_in_trash' => __('Not found in Trash', 'vireo-league'),
            'featured_image' => __('Player Photo', 'vireo-league'),
            'set_featured_image' => __('Set player photo', 'vireo-league'),
            'remove_featured_image' => __('Remove player photo', 'vireo-league'),
            'use_featured_image' => __('Use as player photo', 'vireo-league'),
        );
        
        $args = array(
            'label' => __('Player', 'vireo-league'),
            'description' => __('Sports players', 'vireo-league'),
            'labels' => $labels,
            'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => false,
            'menu_position' => 27,
            'menu_icon' => 'dashicons-admin-users',
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'page',
            'show_in_rest' => true,
            'rewrite' => array('slug' => 'players'),
        );
        
        register_post_type('vsl_player', $args);
    }
    
    /**
     * Register Match post type
     */
    private static function register_match() {
        $labels = array(
            'name' => _x('Matches', 'Post Type General Name', 'vireo-league'),
            'singular_name' => _x('Match', 'Post Type Singular Name', 'vireo-league'),
            'menu_name' => __('Matches', 'vireo-league'),
            'name_admin_bar' => __('Match', 'vireo-league'),
            'archives' => __('Match Archives', 'vireo-league'),
            'attributes' => __('Match Attributes', 'vireo-league'),
            'parent_item_colon' => __('Parent Match:', 'vireo-league'),
            'all_items' => __('All Matches', 'vireo-league'),
            'add_new_item' => __('Add New Match', 'vireo-league'),
            'add_new' => __('Add New', 'vireo-league'),
            'new_item' => __('New Match', 'vireo-league'),
            'edit_item' => __('Edit Match', 'vireo-league'),
            'update_item' => __('Update Match', 'vireo-league'),
            'view_item' => __('View Match', 'vireo-league'),
            'view_items' => __('View Matches', 'vireo-league'),
            'search_items' => __('Search Matches', 'vireo-league'),
            'not_found' => __('Not found', 'vireo-league'),
            'not_found_in_trash' => __('Not found in Trash', 'vireo-league'),
        );
        
        $args = array(
            'label' => __('Match', 'vireo-league'),
            'description' => __('Sports matches and games', 'vireo-league'),
            'labels' => $labels,
            'supports' => array('title', 'editor', 'custom-fields'),
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => false,
            'menu_position' => 28,
            'menu_icon' => 'dashicons-calendar-alt',
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'page',
            'show_in_rest' => true,
            'rewrite' => array('slug' => 'matches'),
        );
        
        register_post_type('vsl_match', $args);
    }
    
    /**
     * Register Season post type
     */
    private static function register_season() {
        $labels = array(
            'name' => _x('Seasons', 'Post Type General Name', 'vireo-league'),
            'singular_name' => _x('Season', 'Post Type Singular Name', 'vireo-league'),
            'menu_name' => __('Seasons', 'vireo-league'),
            'name_admin_bar' => __('Season', 'vireo-league'),
            'archives' => __('Season Archives', 'vireo-league'),
            'attributes' => __('Season Attributes', 'vireo-league'),
            'parent_item_colon' => __('Parent Season:', 'vireo-league'),
            'all_items' => __('All Seasons', 'vireo-league'),
            'add_new_item' => __('Add New Season', 'vireo-league'),
            'add_new' => __('Add New', 'vireo-league'),
            'new_item' => __('New Season', 'vireo-league'),
            'edit_item' => __('Edit Season', 'vireo-league'),
            'update_item' => __('Update Season', 'vireo-league'),
            'view_item' => __('View Season', 'vireo-league'),
            'view_items' => __('View Seasons', 'vireo-league'),
            'search_items' => __('Search Seasons', 'vireo-league'),
            'not_found' => __('Not found', 'vireo-league'),
            'not_found_in_trash' => __('Not found in Trash', 'vireo-league'),
        );
        
        $args = array(
            'label' => __('Season', 'vireo-league'),
            'description' => __('Sports seasons', 'vireo-league'),
            'labels' => $labels,
            'supports' => array('title', 'editor', 'custom-fields'),
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => false,
            'menu_position' => 29,
            'menu_icon' => 'dashicons-calendar',
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'page',
            'show_in_rest' => true,
            'rewrite' => array('slug' => 'seasons'),
        );
        
        register_post_type('vsl_season', $args);
    }
    
    /**
     * Add meta boxes to post types
     */
    public static function add_meta_boxes() {
        // League meta boxes
        add_meta_box(
            'vsl_league_details',
            __('League Details', 'vireo-league'),
            array(__CLASS__, 'league_details_meta_box'),
            'vsl_league',
            'normal',
            'high'
        );
        
        // Team meta boxes
        add_meta_box(
            'vsl_team_details',
            __('Team Details', 'vireo-league'),
            array(__CLASS__, 'team_details_meta_box'),
            'vsl_team',
            'normal',
            'high'
        );
        
        // Player meta boxes
        add_meta_box(
            'vsl_player_details',
            __('Player Details', 'vireo-league'),
            array(__CLASS__, 'player_details_meta_box'),
            'vsl_player',
            'normal',
            'high'
        );
        
        // Match meta boxes
        add_meta_box(
            'vsl_match_details',
            __('Match Details', 'vireo-league'),
            array(__CLASS__, 'match_details_meta_box'),
            'vsl_match',
            'normal',
            'high'
        );
        
        // Season meta boxes
        add_meta_box(
            'vsl_season_details',
            __('Season Details', 'vireo-league'),
            array(__CLASS__, 'season_details_meta_box'),
            'vsl_season',
            'normal',
            'high'
        );
    }
    
    /**
     * League details meta box
     */
    public static function league_details_meta_box($post) {
        wp_nonce_field('vsl_league_meta_nonce', 'vsl_league_meta_nonce');
        
        $sport = get_post_meta($post->ID, '_vsl_sport', true);
        $league_code = get_post_meta($post->ID, '_vsl_league_code', true);
        $status = get_post_meta($post->ID, '_vsl_status', true);
        $organizer = get_post_meta($post->ID, '_vsl_organizer', true);
        $contact_email = get_post_meta($post->ID, '_vsl_contact_email', true);
        $location = get_post_meta($post->ID, '_vsl_location', true);
        
        $sports = VSL_Utilities::get_supported_sports();
        ?>
        <table class="form-table">
            <tr>
                <th><label for="vsl_sport"><?php _e('Sport', 'vireo-league'); ?></label></th>
                <td>
                    <select id="vsl_sport" name="vsl_sport">
                        <?php foreach ($sports as $key => $sport_data): ?>
                            <option value="<?php echo esc_attr($key); ?>" <?php selected($sport, $key); ?>>
                                <?php echo esc_html($sport_data['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="vsl_league_code"><?php _e('League Code', 'vireo-league'); ?></label></th>
                <td>
                    <input type="text" id="vsl_league_code" name="vsl_league_code" 
                           value="<?php echo esc_attr($league_code); ?>" class="regular-text" readonly />
                    <p class="description"><?php _e('Unique identifier for this league (auto-generated)', 'vireo-league'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="vsl_status"><?php _e('Status', 'vireo-league'); ?></label></th>
                <td>
                    <select id="vsl_status" name="vsl_status">
                        <option value="active" <?php selected($status, 'active'); ?>><?php _e('Active', 'vireo-league'); ?></option>
                        <option value="inactive" <?php selected($status, 'inactive'); ?>><?php _e('Inactive', 'vireo-league'); ?></option>
                        <option value="completed" <?php selected($status, 'completed'); ?>><?php _e('Completed', 'vireo-league'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="vsl_organizer"><?php _e('Organizer', 'vireo-league'); ?></label></th>
                <td>
                    <input type="text" id="vsl_organizer" name="vsl_organizer" 
                           value="<?php echo esc_attr($organizer); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th><label for="vsl_contact_email"><?php _e('Contact Email', 'vireo-league'); ?></label></th>
                <td>
                    <input type="email" id="vsl_contact_email" name="vsl_contact_email" 
                           value="<?php echo esc_attr($contact_email); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th><label for="vsl_location"><?php _e('Location', 'vireo-league'); ?></label></th>
                <td>
                    <input type="text" id="vsl_location" name="vsl_location" 
                           value="<?php echo esc_attr($location); ?>" class="regular-text" />
                    <p class="description"><?php _e('City, region, or venue where matches are played', 'vireo-league'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Team details meta box
     */
    public static function team_details_meta_box($post) {
        wp_nonce_field('vsl_team_meta_nonce', 'vsl_team_meta_nonce');
        
        $league_id = get_post_meta($post->ID, '_vsl_league_id', true);
        $team_code = get_post_meta($post->ID, '_vsl_team_code', true);
        $coach_id = get_post_meta($post->ID, '_vsl_coach_id', true);
        $status = get_post_meta($post->ID, '_vsl_status', true);
        $founded_year = get_post_meta($post->ID, '_vsl_founded_year', true);
        $colors = get_post_meta($post->ID, '_vsl_colors', true);
        $venue = get_post_meta($post->ID, '_vsl_venue', true);
        
        // Get leagues for dropdown
        $leagues = get_posts(array('post_type' => 'vsl_league', 'posts_per_page' => -1));
        
        // Get coaches (users with coach capability)
        $coaches = get_users(array('capability' => 'edit_vsl_teams'));
        ?>
        <table class="form-table">
            <tr>
                <th><label for="vsl_league_id"><?php _e('League', 'vireo-league'); ?></label></th>
                <td>
                    <select id="vsl_league_id" name="vsl_league_id" required>
                        <option value=""><?php _e('Select League', 'vireo-league'); ?></option>
                        <?php foreach ($leagues as $league): ?>
                            <option value="<?php echo $league->ID; ?>" <?php selected($league_id, $league->ID); ?>>
                                <?php echo esc_html($league->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="vsl_team_code"><?php _e('Team Code', 'vireo-league'); ?></label></th>
                <td>
                    <input type="text" id="vsl_team_code" name="vsl_team_code" 
                           value="<?php echo esc_attr($team_code); ?>" class="regular-text" readonly />
                    <p class="description"><?php _e('Unique identifier for this team (auto-generated)', 'vireo-league'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="vsl_coach_id"><?php _e('Coach', 'vireo-league'); ?></label></th>
                <td>
                    <select id="vsl_coach_id" name="vsl_coach_id">
                        <option value=""><?php _e('Select Coach', 'vireo-league'); ?></option>
                        <?php foreach ($coaches as $coach): ?>
                            <option value="<?php echo $coach->ID; ?>" <?php selected($coach_id, $coach->ID); ?>>
                                <?php echo esc_html($coach->display_name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="vsl_status"><?php _e('Status', 'vireo-league'); ?></label></th>
                <td>
                    <select id="vsl_status" name="vsl_status">
                        <option value="active" <?php selected($status, 'active'); ?>><?php _e('Active', 'vireo-league'); ?></option>
                        <option value="inactive" <?php selected($status, 'inactive'); ?>><?php _e('Inactive', 'vireo-league'); ?></option>
                        <option value="suspended" <?php selected($status, 'suspended'); ?>><?php _e('Suspended', 'vireo-league'); ?></option>
                        <option value="disbanded" <?php selected($status, 'disbanded'); ?>><?php _e('Disbanded', 'vireo-league'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="vsl_founded_year"><?php _e('Founded Year', 'vireo-league'); ?></label></th>
                <td>
                    <input type="number" id="vsl_founded_year" name="vsl_founded_year" 
                           value="<?php echo esc_attr($founded_year); ?>" min="1800" max="<?php echo date('Y'); ?>" />
                </td>
            </tr>
            <tr>
                <th><label for="vsl_colors"><?php _e('Team Colors', 'vireo-league'); ?></label></th>
                <td>
                    <input type="text" id="vsl_colors" name="vsl_colors" 
                           value="<?php echo esc_attr($colors); ?>" class="regular-text" />
                    <p class="description"><?php _e('e.g., Blue and White', 'vireo-league'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="vsl_venue"><?php _e('Home Venue', 'vireo-league'); ?></label></th>
                <td>
                    <input type="text" id="vsl_venue" name="vsl_venue" 
                           value="<?php echo esc_attr($venue); ?>" class="regular-text" />
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Player details meta box
     */
    public static function player_details_meta_box($post) {
        wp_nonce_field('vsl_player_meta_nonce', 'vsl_player_meta_nonce');
        
        $team_id = get_post_meta($post->ID, '_vsl_team_id', true);
        $jersey_number = get_post_meta($post->ID, '_vsl_jersey_number', true);
        $position = get_post_meta($post->ID, '_vsl_position', true);
        $date_of_birth = get_post_meta($post->ID, '_vsl_date_of_birth', true);
        $height = get_post_meta($post->ID, '_vsl_height', true);
        $weight = get_post_meta($post->ID, '_vsl_weight', true);
        $status = get_post_meta($post->ID, '_vsl_status', true);
        
        // Get teams for dropdown
        $teams = get_posts(array('post_type' => 'vsl_team', 'posts_per_page' => -1));
        
        // Get sport-specific positions if team is selected
        $positions = array();
        if ($team_id) {
            $league_id = get_post_meta($team_id, '_vsl_league_id', true);
            if ($league_id) {
                $sport_config = VSL_Utilities::get_sport_config($league_id);
                $positions = $sport_config['positions'];
            }
        }
        ?>
        <table class="form-table">
            <tr>
                <th><label for="vsl_team_id"><?php _e('Team', 'vireo-league'); ?></label></th>
                <td>
                    <select id="vsl_team_id" name="vsl_team_id" required>
                        <option value=""><?php _e('Select Team', 'vireo-league'); ?></option>
                        <?php foreach ($teams as $team): ?>
                            <option value="<?php echo $team->ID; ?>" <?php selected($team_id, $team->ID); ?>>
                                <?php echo esc_html($team->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="vsl_jersey_number"><?php _e('Jersey Number', 'vireo-league'); ?></label></th>
                <td>
                    <input type="number" id="vsl_jersey_number" name="vsl_jersey_number" 
                           value="<?php echo esc_attr($jersey_number); ?>" min="0" max="99" />
                </td>
            </tr>
            <tr>
                <th><label for="vsl_position"><?php _e('Position', 'vireo-league'); ?></label></th>
                <td>
                    <select id="vsl_position" name="vsl_position">
                        <option value=""><?php _e('Select Position', 'vireo-league'); ?></option>
                        <?php foreach ($positions as $key => $position_name): ?>
                            <option value="<?php echo esc_attr($key); ?>" <?php selected($position, $key); ?>>
                                <?php echo esc_html($position_name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="vsl_date_of_birth"><?php _e('Date of Birth', 'vireo-league'); ?></label></th>
                <td>
                    <input type="date" id="vsl_date_of_birth" name="vsl_date_of_birth" 
                           value="<?php echo esc_attr($date_of_birth); ?>" />
                </td>
            </tr>
            <tr>
                <th><label for="vsl_height"><?php _e('Height (cm)', 'vireo-league'); ?></label></th>
                <td>
                    <input type="number" id="vsl_height" name="vsl_height" 
                           value="<?php echo esc_attr($height); ?>" min="100" max="250" />
                </td>
            </tr>
            <tr>
                <th><label for="vsl_weight"><?php _e('Weight (kg)', 'vireo-league'); ?></label></th>
                <td>
                    <input type="number" id="vsl_weight" name="vsl_weight" 
                           value="<?php echo esc_attr($weight); ?>" min="30" max="200" />
                </td>
            </tr>
            <tr>
                <th><label for="vsl_status"><?php _e('Status', 'vireo-league'); ?></label></th>
                <td>
                    <select id="vsl_status" name="vsl_status">
                        <option value="active" <?php selected($status, 'active'); ?>><?php _e('Active', 'vireo-league'); ?></option>
                        <option value="inactive" <?php selected($status, 'inactive'); ?>><?php _e('Inactive', 'vireo-league'); ?></option>
                        <option value="injured" <?php selected($status, 'injured'); ?>><?php _e('Injured', 'vireo-league'); ?></option>
                        <option value="suspended" <?php selected($status, 'suspended'); ?>><?php _e('Suspended', 'vireo-league'); ?></option>
                        <option value="retired" <?php selected($status, 'retired'); ?>><?php _e('Retired', 'vireo-league'); ?></option>
                    </select>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Match details meta box
     */
    public static function match_details_meta_box($post) {
        wp_nonce_field('vsl_match_meta_nonce', 'vsl_match_meta_nonce');
        
        $league_id = get_post_meta($post->ID, '_vsl_league_id', true);
        $season_id = get_post_meta($post->ID, '_vsl_season_id', true);
        $home_team_id = get_post_meta($post->ID, '_vsl_home_team_id', true);
        $away_team_id = get_post_meta($post->ID, '_vsl_away_team_id', true);
        $match_date = get_post_meta($post->ID, '_vsl_match_date', true);
        $venue = get_post_meta($post->ID, '_vsl_venue', true);
        $status = get_post_meta($post->ID, '_vsl_status', true);
        $home_score = get_post_meta($post->ID, '_vsl_home_score', true);
        $away_score = get_post_meta($post->ID, '_vsl_away_score', true);
        
        // Get leagues, seasons, and teams for dropdowns
        $leagues = get_posts(array('post_type' => 'vsl_league', 'posts_per_page' => -1));
        $seasons = get_posts(array('post_type' => 'vsl_season', 'posts_per_page' => -1));
        $teams = get_posts(array('post_type' => 'vsl_team', 'posts_per_page' => -1));
        ?>
        <table class="form-table">
            <tr>
                <th><label for="vsl_league_id"><?php _e('League', 'vireo-league'); ?></label></th>
                <td>
                    <select id="vsl_league_id" name="vsl_league_id" required>
                        <option value=""><?php _e('Select League', 'vireo-league'); ?></option>
                        <?php foreach ($leagues as $league): ?>
                            <option value="<?php echo $league->ID; ?>" <?php selected($league_id, $league->ID); ?>>
                                <?php echo esc_html($league->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="vsl_season_id"><?php _e('Season', 'vireo-league'); ?></label></th>
                <td>
                    <select id="vsl_season_id" name="vsl_season_id">
                        <option value=""><?php _e('Select Season', 'vireo-league'); ?></option>
                        <?php foreach ($seasons as $season): ?>
                            <option value="<?php echo $season->ID; ?>" <?php selected($season_id, $season->ID); ?>>
                                <?php echo esc_html($season->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="vsl_home_team_id"><?php _e('Home Team', 'vireo-league'); ?></label></th>
                <td>
                    <select id="vsl_home_team_id" name="vsl_home_team_id" required>
                        <option value=""><?php _e('Select Home Team', 'vireo-league'); ?></option>
                        <?php foreach ($teams as $team): ?>
                            <option value="<?php echo $team->ID; ?>" <?php selected($home_team_id, $team->ID); ?>>
                                <?php echo esc_html($team->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="vsl_away_team_id"><?php _e('Away Team', 'vireo-league'); ?></label></th>
                <td>
                    <select id="vsl_away_team_id" name="vsl_away_team_id" required>
                        <option value=""><?php _e('Select Away Team', 'vireo-league'); ?></option>
                        <?php foreach ($teams as $team): ?>
                            <option value="<?php echo $team->ID; ?>" <?php selected($away_team_id, $team->ID); ?>>
                                <?php echo esc_html($team->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="vsl_match_date"><?php _e('Match Date & Time', 'vireo-league'); ?></label></th>
                <td>
                    <input type="datetime-local" id="vsl_match_date" name="vsl_match_date" 
                           value="<?php echo esc_attr($match_date); ?>" />
                </td>
            </tr>
            <tr>
                <th><label for="vsl_venue"><?php _e('Venue', 'vireo-league'); ?></label></th>
                <td>
                    <input type="text" id="vsl_venue" name="vsl_venue" 
                           value="<?php echo esc_attr($venue); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th><label for="vsl_status"><?php _e('Status', 'vireo-league'); ?></label></th>
                <td>
                    <select id="vsl_status" name="vsl_status">
                        <option value="scheduled" <?php selected($status, 'scheduled'); ?>><?php _e('Scheduled', 'vireo-league'); ?></option>
                        <option value="in_progress" <?php selected($status, 'in_progress'); ?>><?php _e('In Progress', 'vireo-league'); ?></option>
                        <option value="completed" <?php selected($status, 'completed'); ?>><?php _e('Completed', 'vireo-league'); ?></option>
                        <option value="postponed" <?php selected($status, 'postponed'); ?>><?php _e('Postponed', 'vireo-league'); ?></option>
                        <option value="cancelled" <?php selected($status, 'cancelled'); ?>><?php _e('Cancelled', 'vireo-league'); ?></option>
                    </select>
                </td>
            </tr>
        </table>
        
        <h4><?php _e('Match Result', 'vireo-league'); ?></h4>
        <table class="form-table">
            <tr>
                <th><label for="vsl_home_score"><?php _e('Home Team Score', 'vireo-league'); ?></label></th>
                <td>
                    <input type="number" id="vsl_home_score" name="vsl_home_score" 
                           value="<?php echo esc_attr($home_score); ?>" min="0" />
                </td>
            </tr>
            <tr>
                <th><label for="vsl_away_score"><?php _e('Away Team Score', 'vireo-league'); ?></label></th>
                <td>
                    <input type="number" id="vsl_away_score" name="vsl_away_score" 
                           value="<?php echo esc_attr($away_score); ?>" min="0" />
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Season details meta box
     */
    public static function season_details_meta_box($post) {
        wp_nonce_field('vsl_season_meta_nonce', 'vsl_season_meta_nonce');
        
        $league_id = get_post_meta($post->ID, '_vsl_league_id', true);
        $start_date = get_post_meta($post->ID, '_vsl_start_date', true);
        $end_date = get_post_meta($post->ID, '_vsl_end_date', true);
        $status = get_post_meta($post->ID, '_vsl_status', true);
        $is_current = get_post_meta($post->ID, '_vsl_is_current', true);
        
        // Get leagues for dropdown
        $leagues = get_posts(array('post_type' => 'vsl_league', 'posts_per_page' => -1));
        ?>
        <table class="form-table">
            <tr>
                <th><label for="vsl_league_id"><?php _e('League', 'vireo-league'); ?></label></th>
                <td>
                    <select id="vsl_league_id" name="vsl_league_id" required>
                        <option value=""><?php _e('Select League', 'vireo-league'); ?></option>
                        <?php foreach ($leagues as $league): ?>
                            <option value="<?php echo $league->ID; ?>" <?php selected($league_id, $league->ID); ?>>
                                <?php echo esc_html($league->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="vsl_start_date"><?php _e('Start Date', 'vireo-league'); ?></label></th>
                <td>
                    <input type="date" id="vsl_start_date" name="vsl_start_date" 
                           value="<?php echo esc_attr($start_date); ?>" />
                </td>
            </tr>
            <tr>
                <th><label for="vsl_end_date"><?php _e('End Date', 'vireo-league'); ?></label></th>
                <td>
                    <input type="date" id="vsl_end_date" name="vsl_end_date" 
                           value="<?php echo esc_attr($end_date); ?>" />
                </td>
            </tr>
            <tr>
                <th><label for="vsl_status"><?php _e('Status', 'vireo-league'); ?></label></th>
                <td>
                    <select id="vsl_status" name="vsl_status">
                        <option value="upcoming" <?php selected($status, 'upcoming'); ?>><?php _e('Upcoming', 'vireo-league'); ?></option>
                        <option value="active" <?php selected($status, 'active'); ?>><?php _e('Active', 'vireo-league'); ?></option>
                        <option value="completed" <?php selected($status, 'completed'); ?>><?php _e('Completed', 'vireo-league'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="vsl_is_current"><?php _e('Current Season', 'vireo-league'); ?></label></th>
                <td>
                    <input type="checkbox" id="vsl_is_current" name="vsl_is_current" value="1" 
                           <?php checked($is_current, '1'); ?> />
                    <label for="vsl_is_current"><?php _e('This is the current active season', 'vireo-league'); ?></label>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Save meta box data
     */
    public static function save_meta_boxes($post_id) {
        // Skip auto-saves and revisions
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if (wp_is_post_revision($post_id)) {
            return;
        }
        
        $post_type = get_post_type($post_id);
        
        // Save league meta
        if ($post_type === 'vsl_league' && 
            isset($_POST['vsl_league_meta_nonce']) && 
            wp_verify_nonce($_POST['vsl_league_meta_nonce'], 'vsl_league_meta_nonce')) {
            
            // Generate league code if not exists
            $league_code = get_post_meta($post_id, '_vsl_league_code', true);
            if (empty($league_code)) {
                $league_code = VSL_Utilities::generate_league_code();
                update_post_meta($post_id, '_vsl_league_code', $league_code);
            }
            
            update_post_meta($post_id, '_vsl_sport', sanitize_text_field($_POST['vsl_sport']));
            update_post_meta($post_id, '_vsl_status', sanitize_text_field($_POST['vsl_status']));
            update_post_meta($post_id, '_vsl_organizer', sanitize_text_field($_POST['vsl_organizer']));
            update_post_meta($post_id, '_vsl_contact_email', sanitize_email($_POST['vsl_contact_email']));
            update_post_meta($post_id, '_vsl_location', sanitize_text_field($_POST['vsl_location']));
        }
        
        // Save team meta
        if ($post_type === 'vsl_team' && 
            isset($_POST['vsl_team_meta_nonce']) && 
            wp_verify_nonce($_POST['vsl_team_meta_nonce'], 'vsl_team_meta_nonce')) {
            
            // Generate team code if not exists
            $team_code = get_post_meta($post_id, '_vsl_team_code', true);
            if (empty($team_code)) {
                $team_code = VSL_Utilities::generate_team_code();
                update_post_meta($post_id, '_vsl_team_code', $team_code);
            }
            
            update_post_meta($post_id, '_vsl_league_id', intval($_POST['vsl_league_id']));
            update_post_meta($post_id, '_vsl_coach_id', intval($_POST['vsl_coach_id']));
            update_post_meta($post_id, '_vsl_status', sanitize_text_field($_POST['vsl_status']));
            update_post_meta($post_id, '_vsl_founded_year', intval($_POST['vsl_founded_year']));
            update_post_meta($post_id, '_vsl_colors', sanitize_text_field($_POST['vsl_colors']));
            update_post_meta($post_id, '_vsl_venue', sanitize_text_field($_POST['vsl_venue']));
        }
        
        // Save player meta
        if ($post_type === 'vsl_player' && 
            isset($_POST['vsl_player_meta_nonce']) && 
            wp_verify_nonce($_POST['vsl_player_meta_nonce'], 'vsl_player_meta_nonce')) {
            
            update_post_meta($post_id, '_vsl_team_id', intval($_POST['vsl_team_id']));
            update_post_meta($post_id, '_vsl_jersey_number', intval($_POST['vsl_jersey_number']));
            update_post_meta($post_id, '_vsl_position', sanitize_text_field($_POST['vsl_position']));
            update_post_meta($post_id, '_vsl_date_of_birth', sanitize_text_field($_POST['vsl_date_of_birth']));
            update_post_meta($post_id, '_vsl_height', intval($_POST['vsl_height']));
            update_post_meta($post_id, '_vsl_weight', intval($_POST['vsl_weight']));
            update_post_meta($post_id, '_vsl_status', sanitize_text_field($_POST['vsl_status']));
        }
        
        // Save match meta
        if ($post_type === 'vsl_match' && 
            isset($_POST['vsl_match_meta_nonce']) && 
            wp_verify_nonce($_POST['vsl_match_meta_nonce'], 'vsl_match_meta_nonce')) {
            
            update_post_meta($post_id, '_vsl_league_id', intval($_POST['vsl_league_id']));
            update_post_meta($post_id, '_vsl_season_id', intval($_POST['vsl_season_id']));
            update_post_meta($post_id, '_vsl_home_team_id', intval($_POST['vsl_home_team_id']));
            update_post_meta($post_id, '_vsl_away_team_id', intval($_POST['vsl_away_team_id']));
            update_post_meta($post_id, '_vsl_match_date', sanitize_text_field($_POST['vsl_match_date']));
            update_post_meta($post_id, '_vsl_venue', sanitize_text_field($_POST['vsl_venue']));
            update_post_meta($post_id, '_vsl_status', sanitize_text_field($_POST['vsl_status']));
            update_post_meta($post_id, '_vsl_home_score', intval($_POST['vsl_home_score']));
            update_post_meta($post_id, '_vsl_away_score', intval($_POST['vsl_away_score']));
        }
        
        // Save season meta
        if ($post_type === 'vsl_season' && 
            isset($_POST['vsl_season_meta_nonce']) && 
            wp_verify_nonce($_POST['vsl_season_meta_nonce'], 'vsl_season_meta_nonce')) {
            
            update_post_meta($post_id, '_vsl_league_id', intval($_POST['vsl_league_id']));
            update_post_meta($post_id, '_vsl_start_date', sanitize_text_field($_POST['vsl_start_date']));
            update_post_meta($post_id, '_vsl_end_date', sanitize_text_field($_POST['vsl_end_date']));
            update_post_meta($post_id, '_vsl_status', sanitize_text_field($_POST['vsl_status']));
            
            $is_current = isset($_POST['vsl_is_current']) ? '1' : '0';
            update_post_meta($post_id, '_vsl_is_current', $is_current);
            
            // If this is set as current, unset others for the same league
            if ($is_current === '1') {
                $league_id = intval($_POST['vsl_league_id']);
                $other_seasons = get_posts(array(
                    'post_type' => 'vsl_season',
                    'meta_query' => array(
                        array(
                            'key' => '_vsl_league_id',
                            'value' => $league_id,
                            'compare' => '='
                        )
                    ),
                    'posts_per_page' => -1,
                    'exclude' => array($post_id)
                ));
                
                foreach ($other_seasons as $season) {
                    update_post_meta($season->ID, '_vsl_is_current', '0');
                }
            }
        }
    }
}