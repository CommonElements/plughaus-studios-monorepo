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

class PSL_Post_Types {
    
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
            'name' => _x('Leagues', 'Post Type General Name', 'plughaus-league'),
            'singular_name' => _x('League', 'Post Type Singular Name', 'plughaus-league'),
            'menu_name' => __('Leagues', 'plughaus-league'),
            'name_admin_bar' => __('League', 'plughaus-league'),
            'archives' => __('League Archives', 'plughaus-league'),
            'attributes' => __('League Attributes', 'plughaus-league'),
            'parent_item_colon' => __('Parent League:', 'plughaus-league'),
            'all_items' => __('All Leagues', 'plughaus-league'),
            'add_new_item' => __('Add New League', 'plughaus-league'),
            'add_new' => __('Add New', 'plughaus-league'),
            'new_item' => __('New League', 'plughaus-league'),
            'edit_item' => __('Edit League', 'plughaus-league'),
            'update_item' => __('Update League', 'plughaus-league'),
            'view_item' => __('View League', 'plughaus-league'),
            'view_items' => __('View Leagues', 'plughaus-league'),
            'search_items' => __('Search Leagues', 'plughaus-league'),
            'not_found' => __('Not found', 'plughaus-league'),
            'not_found_in_trash' => __('Not found in Trash', 'plughaus-league'),
            'featured_image' => __('League Logo', 'plughaus-league'),
            'set_featured_image' => __('Set league logo', 'plughaus-league'),
            'remove_featured_image' => __('Remove league logo', 'plughaus-league'),
            'use_featured_image' => __('Use as league logo', 'plughaus-league'),
        );
        
        $args = array(
            'label' => __('League', 'plughaus-league'),
            'description' => __('Sports leagues and competitions', 'plughaus-league'),
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
        
        register_post_type('psl_league', $args);
    }
    
    /**
     * Register Team post type
     */
    private static function register_team() {
        $labels = array(
            'name' => _x('Teams', 'Post Type General Name', 'plughaus-league'),
            'singular_name' => _x('Team', 'Post Type Singular Name', 'plughaus-league'),
            'menu_name' => __('Teams', 'plughaus-league'),
            'name_admin_bar' => __('Team', 'plughaus-league'),
            'archives' => __('Team Archives', 'plughaus-league'),
            'attributes' => __('Team Attributes', 'plughaus-league'),
            'parent_item_colon' => __('Parent Team:', 'plughaus-league'),
            'all_items' => __('All Teams', 'plughaus-league'),
            'add_new_item' => __('Add New Team', 'plughaus-league'),
            'add_new' => __('Add New', 'plughaus-league'),
            'new_item' => __('New Team', 'plughaus-league'),
            'edit_item' => __('Edit Team', 'plughaus-league'),
            'update_item' => __('Update Team', 'plughaus-league'),
            'view_item' => __('View Team', 'plughaus-league'),
            'view_items' => __('View Teams', 'plughaus-league'),
            'search_items' => __('Search Teams', 'plughaus-league'),
            'not_found' => __('Not found', 'plughaus-league'),
            'not_found_in_trash' => __('Not found in Trash', 'plughaus-league'),
            'featured_image' => __('Team Logo', 'plughaus-league'),
            'set_featured_image' => __('Set team logo', 'plughaus-league'),
            'remove_featured_image' => __('Remove team logo', 'plughaus-league'),
            'use_featured_image' => __('Use as team logo', 'plughaus-league'),
        );
        
        $args = array(
            'label' => __('Team', 'plughaus-league'),
            'description' => __('Sports teams', 'plughaus-league'),
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
        
        register_post_type('psl_team', $args);
    }
    
    /**
     * Register Player post type
     */
    private static function register_player() {
        $labels = array(
            'name' => _x('Players', 'Post Type General Name', 'plughaus-league'),
            'singular_name' => _x('Player', 'Post Type Singular Name', 'plughaus-league'),
            'menu_name' => __('Players', 'plughaus-league'),
            'name_admin_bar' => __('Player', 'plughaus-league'),
            'archives' => __('Player Archives', 'plughaus-league'),
            'attributes' => __('Player Attributes', 'plughaus-league'),
            'parent_item_colon' => __('Parent Player:', 'plughaus-league'),
            'all_items' => __('All Players', 'plughaus-league'),
            'add_new_item' => __('Add New Player', 'plughaus-league'),
            'add_new' => __('Add New', 'plughaus-league'),
            'new_item' => __('New Player', 'plughaus-league'),
            'edit_item' => __('Edit Player', 'plughaus-league'),
            'update_item' => __('Update Player', 'plughaus-league'),
            'view_item' => __('View Player', 'plughaus-league'),
            'view_items' => __('View Players', 'plughaus-league'),
            'search_items' => __('Search Players', 'plughaus-league'),
            'not_found' => __('Not found', 'plughaus-league'),
            'not_found_in_trash' => __('Not found in Trash', 'plughaus-league'),
            'featured_image' => __('Player Photo', 'plughaus-league'),
            'set_featured_image' => __('Set player photo', 'plughaus-league'),
            'remove_featured_image' => __('Remove player photo', 'plughaus-league'),
            'use_featured_image' => __('Use as player photo', 'plughaus-league'),
        );
        
        $args = array(
            'label' => __('Player', 'plughaus-league'),
            'description' => __('Sports players', 'plughaus-league'),
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
        
        register_post_type('psl_player', $args);
    }
    
    /**
     * Register Match post type
     */
    private static function register_match() {
        $labels = array(
            'name' => _x('Matches', 'Post Type General Name', 'plughaus-league'),
            'singular_name' => _x('Match', 'Post Type Singular Name', 'plughaus-league'),
            'menu_name' => __('Matches', 'plughaus-league'),
            'name_admin_bar' => __('Match', 'plughaus-league'),
            'archives' => __('Match Archives', 'plughaus-league'),
            'attributes' => __('Match Attributes', 'plughaus-league'),
            'parent_item_colon' => __('Parent Match:', 'plughaus-league'),
            'all_items' => __('All Matches', 'plughaus-league'),
            'add_new_item' => __('Add New Match', 'plughaus-league'),
            'add_new' => __('Add New', 'plughaus-league'),
            'new_item' => __('New Match', 'plughaus-league'),
            'edit_item' => __('Edit Match', 'plughaus-league'),
            'update_item' => __('Update Match', 'plughaus-league'),
            'view_item' => __('View Match', 'plughaus-league'),
            'view_items' => __('View Matches', 'plughaus-league'),
            'search_items' => __('Search Matches', 'plughaus-league'),
            'not_found' => __('Not found', 'plughaus-league'),
            'not_found_in_trash' => __('Not found in Trash', 'plughaus-league'),
        );
        
        $args = array(
            'label' => __('Match', 'plughaus-league'),
            'description' => __('Sports matches and games', 'plughaus-league'),
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
        
        register_post_type('psl_match', $args);
    }
    
    /**
     * Register Season post type
     */
    private static function register_season() {
        $labels = array(
            'name' => _x('Seasons', 'Post Type General Name', 'plughaus-league'),
            'singular_name' => _x('Season', 'Post Type Singular Name', 'plughaus-league'),
            'menu_name' => __('Seasons', 'plughaus-league'),
            'name_admin_bar' => __('Season', 'plughaus-league'),
            'archives' => __('Season Archives', 'plughaus-league'),
            'attributes' => __('Season Attributes', 'plughaus-league'),
            'parent_item_colon' => __('Parent Season:', 'plughaus-league'),
            'all_items' => __('All Seasons', 'plughaus-league'),
            'add_new_item' => __('Add New Season', 'plughaus-league'),
            'add_new' => __('Add New', 'plughaus-league'),
            'new_item' => __('New Season', 'plughaus-league'),
            'edit_item' => __('Edit Season', 'plughaus-league'),
            'update_item' => __('Update Season', 'plughaus-league'),
            'view_item' => __('View Season', 'plughaus-league'),
            'view_items' => __('View Seasons', 'plughaus-league'),
            'search_items' => __('Search Seasons', 'plughaus-league'),
            'not_found' => __('Not found', 'plughaus-league'),
            'not_found_in_trash' => __('Not found in Trash', 'plughaus-league'),
        );
        
        $args = array(
            'label' => __('Season', 'plughaus-league'),
            'description' => __('Sports seasons', 'plughaus-league'),
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
        
        register_post_type('psl_season', $args);
    }
    
    /**
     * Add meta boxes to post types
     */
    public static function add_meta_boxes() {
        // League meta boxes
        add_meta_box(
            'psl_league_details',
            __('League Details', 'plughaus-league'),
            array(__CLASS__, 'league_details_meta_box'),
            'psl_league',
            'normal',
            'high'
        );
        
        // Team meta boxes
        add_meta_box(
            'psl_team_details',
            __('Team Details', 'plughaus-league'),
            array(__CLASS__, 'team_details_meta_box'),
            'psl_team',
            'normal',
            'high'
        );
        
        // Player meta boxes
        add_meta_box(
            'psl_player_details',
            __('Player Details', 'plughaus-league'),
            array(__CLASS__, 'player_details_meta_box'),
            'psl_player',
            'normal',
            'high'
        );
        
        // Match meta boxes
        add_meta_box(
            'psl_match_details',
            __('Match Details', 'plughaus-league'),
            array(__CLASS__, 'match_details_meta_box'),
            'psl_match',
            'normal',
            'high'
        );
        
        // Season meta boxes
        add_meta_box(
            'psl_season_details',
            __('Season Details', 'plughaus-league'),
            array(__CLASS__, 'season_details_meta_box'),
            'psl_season',
            'normal',
            'high'
        );
    }
    
    /**
     * League details meta box
     */
    public static function league_details_meta_box($post) {
        wp_nonce_field('psl_league_meta_nonce', 'psl_league_meta_nonce');
        
        $sport = get_post_meta($post->ID, '_psl_sport', true);
        $league_code = get_post_meta($post->ID, '_psl_league_code', true);
        $status = get_post_meta($post->ID, '_psl_status', true);
        $organizer = get_post_meta($post->ID, '_psl_organizer', true);
        $contact_email = get_post_meta($post->ID, '_psl_contact_email', true);
        $location = get_post_meta($post->ID, '_psl_location', true);
        
        $sports = PSL_Utilities::get_supported_sports();
        ?>
        <table class="form-table">
            <tr>
                <th><label for="psl_sport"><?php _e('Sport', 'plughaus-league'); ?></label></th>
                <td>
                    <select id="psl_sport" name="psl_sport">
                        <?php foreach ($sports as $key => $sport_data): ?>
                            <option value="<?php echo esc_attr($key); ?>" <?php selected($sport, $key); ?>>
                                <?php echo esc_html($sport_data['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="psl_league_code"><?php _e('League Code', 'plughaus-league'); ?></label></th>
                <td>
                    <input type="text" id="psl_league_code" name="psl_league_code" 
                           value="<?php echo esc_attr($league_code); ?>" class="regular-text" readonly />
                    <p class="description"><?php _e('Unique identifier for this league (auto-generated)', 'plughaus-league'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="psl_status"><?php _e('Status', 'plughaus-league'); ?></label></th>
                <td>
                    <select id="psl_status" name="psl_status">
                        <option value="active" <?php selected($status, 'active'); ?>><?php _e('Active', 'plughaus-league'); ?></option>
                        <option value="inactive" <?php selected($status, 'inactive'); ?>><?php _e('Inactive', 'plughaus-league'); ?></option>
                        <option value="completed" <?php selected($status, 'completed'); ?>><?php _e('Completed', 'plughaus-league'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="psl_organizer"><?php _e('Organizer', 'plughaus-league'); ?></label></th>
                <td>
                    <input type="text" id="psl_organizer" name="psl_organizer" 
                           value="<?php echo esc_attr($organizer); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th><label for="psl_contact_email"><?php _e('Contact Email', 'plughaus-league'); ?></label></th>
                <td>
                    <input type="email" id="psl_contact_email" name="psl_contact_email" 
                           value="<?php echo esc_attr($contact_email); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th><label for="psl_location"><?php _e('Location', 'plughaus-league'); ?></label></th>
                <td>
                    <input type="text" id="psl_location" name="psl_location" 
                           value="<?php echo esc_attr($location); ?>" class="regular-text" />
                    <p class="description"><?php _e('City, region, or venue where matches are played', 'plughaus-league'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Team details meta box
     */
    public static function team_details_meta_box($post) {
        wp_nonce_field('psl_team_meta_nonce', 'psl_team_meta_nonce');
        
        $league_id = get_post_meta($post->ID, '_psl_league_id', true);
        $team_code = get_post_meta($post->ID, '_psl_team_code', true);
        $coach_id = get_post_meta($post->ID, '_psl_coach_id', true);
        $status = get_post_meta($post->ID, '_psl_status', true);
        $founded_year = get_post_meta($post->ID, '_psl_founded_year', true);
        $colors = get_post_meta($post->ID, '_psl_colors', true);
        $venue = get_post_meta($post->ID, '_psl_venue', true);
        
        // Get leagues for dropdown
        $leagues = get_posts(array('post_type' => 'psl_league', 'posts_per_page' => -1));
        
        // Get coaches (users with coach capability)
        $coaches = get_users(array('capability' => 'edit_psl_teams'));
        ?>
        <table class="form-table">
            <tr>
                <th><label for="psl_league_id"><?php _e('League', 'plughaus-league'); ?></label></th>
                <td>
                    <select id="psl_league_id" name="psl_league_id" required>
                        <option value=""><?php _e('Select League', 'plughaus-league'); ?></option>
                        <?php foreach ($leagues as $league): ?>
                            <option value="<?php echo $league->ID; ?>" <?php selected($league_id, $league->ID); ?>>
                                <?php echo esc_html($league->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="psl_team_code"><?php _e('Team Code', 'plughaus-league'); ?></label></th>
                <td>
                    <input type="text" id="psl_team_code" name="psl_team_code" 
                           value="<?php echo esc_attr($team_code); ?>" class="regular-text" readonly />
                    <p class="description"><?php _e('Unique identifier for this team (auto-generated)', 'plughaus-league'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="psl_coach_id"><?php _e('Coach', 'plughaus-league'); ?></label></th>
                <td>
                    <select id="psl_coach_id" name="psl_coach_id">
                        <option value=""><?php _e('Select Coach', 'plughaus-league'); ?></option>
                        <?php foreach ($coaches as $coach): ?>
                            <option value="<?php echo $coach->ID; ?>" <?php selected($coach_id, $coach->ID); ?>>
                                <?php echo esc_html($coach->display_name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="psl_status"><?php _e('Status', 'plughaus-league'); ?></label></th>
                <td>
                    <select id="psl_status" name="psl_status">
                        <option value="active" <?php selected($status, 'active'); ?>><?php _e('Active', 'plughaus-league'); ?></option>
                        <option value="inactive" <?php selected($status, 'inactive'); ?>><?php _e('Inactive', 'plughaus-league'); ?></option>
                        <option value="suspended" <?php selected($status, 'suspended'); ?>><?php _e('Suspended', 'plughaus-league'); ?></option>
                        <option value="disbanded" <?php selected($status, 'disbanded'); ?>><?php _e('Disbanded', 'plughaus-league'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="psl_founded_year"><?php _e('Founded Year', 'plughaus-league'); ?></label></th>
                <td>
                    <input type="number" id="psl_founded_year" name="psl_founded_year" 
                           value="<?php echo esc_attr($founded_year); ?>" min="1800" max="<?php echo date('Y'); ?>" />
                </td>
            </tr>
            <tr>
                <th><label for="psl_colors"><?php _e('Team Colors', 'plughaus-league'); ?></label></th>
                <td>
                    <input type="text" id="psl_colors" name="psl_colors" 
                           value="<?php echo esc_attr($colors); ?>" class="regular-text" />
                    <p class="description"><?php _e('e.g., Blue and White', 'plughaus-league'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="psl_venue"><?php _e('Home Venue', 'plughaus-league'); ?></label></th>
                <td>
                    <input type="text" id="psl_venue" name="psl_venue" 
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
        wp_nonce_field('psl_player_meta_nonce', 'psl_player_meta_nonce');
        
        $team_id = get_post_meta($post->ID, '_psl_team_id', true);
        $jersey_number = get_post_meta($post->ID, '_psl_jersey_number', true);
        $position = get_post_meta($post->ID, '_psl_position', true);
        $date_of_birth = get_post_meta($post->ID, '_psl_date_of_birth', true);
        $height = get_post_meta($post->ID, '_psl_height', true);
        $weight = get_post_meta($post->ID, '_psl_weight', true);
        $status = get_post_meta($post->ID, '_psl_status', true);
        
        // Get teams for dropdown
        $teams = get_posts(array('post_type' => 'psl_team', 'posts_per_page' => -1));
        
        // Get sport-specific positions if team is selected
        $positions = array();
        if ($team_id) {
            $league_id = get_post_meta($team_id, '_psl_league_id', true);
            if ($league_id) {
                $sport_config = PSL_Utilities::get_sport_config($league_id);
                $positions = $sport_config['positions'];
            }
        }
        ?>
        <table class="form-table">
            <tr>
                <th><label for="psl_team_id"><?php _e('Team', 'plughaus-league'); ?></label></th>
                <td>
                    <select id="psl_team_id" name="psl_team_id" required>
                        <option value=""><?php _e('Select Team', 'plughaus-league'); ?></option>
                        <?php foreach ($teams as $team): ?>
                            <option value="<?php echo $team->ID; ?>" <?php selected($team_id, $team->ID); ?>>
                                <?php echo esc_html($team->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="psl_jersey_number"><?php _e('Jersey Number', 'plughaus-league'); ?></label></th>
                <td>
                    <input type="number" id="psl_jersey_number" name="psl_jersey_number" 
                           value="<?php echo esc_attr($jersey_number); ?>" min="0" max="99" />
                </td>
            </tr>
            <tr>
                <th><label for="psl_position"><?php _e('Position', 'plughaus-league'); ?></label></th>
                <td>
                    <select id="psl_position" name="psl_position">
                        <option value=""><?php _e('Select Position', 'plughaus-league'); ?></option>
                        <?php foreach ($positions as $key => $position_name): ?>
                            <option value="<?php echo esc_attr($key); ?>" <?php selected($position, $key); ?>>
                                <?php echo esc_html($position_name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="psl_date_of_birth"><?php _e('Date of Birth', 'plughaus-league'); ?></label></th>
                <td>
                    <input type="date" id="psl_date_of_birth" name="psl_date_of_birth" 
                           value="<?php echo esc_attr($date_of_birth); ?>" />
                </td>
            </tr>
            <tr>
                <th><label for="psl_height"><?php _e('Height (cm)', 'plughaus-league'); ?></label></th>
                <td>
                    <input type="number" id="psl_height" name="psl_height" 
                           value="<?php echo esc_attr($height); ?>" min="100" max="250" />
                </td>
            </tr>
            <tr>
                <th><label for="psl_weight"><?php _e('Weight (kg)', 'plughaus-league'); ?></label></th>
                <td>
                    <input type="number" id="psl_weight" name="psl_weight" 
                           value="<?php echo esc_attr($weight); ?>" min="30" max="200" />
                </td>
            </tr>
            <tr>
                <th><label for="psl_status"><?php _e('Status', 'plughaus-league'); ?></label></th>
                <td>
                    <select id="psl_status" name="psl_status">
                        <option value="active" <?php selected($status, 'active'); ?>><?php _e('Active', 'plughaus-league'); ?></option>
                        <option value="inactive" <?php selected($status, 'inactive'); ?>><?php _e('Inactive', 'plughaus-league'); ?></option>
                        <option value="injured" <?php selected($status, 'injured'); ?>><?php _e('Injured', 'plughaus-league'); ?></option>
                        <option value="suspended" <?php selected($status, 'suspended'); ?>><?php _e('Suspended', 'plughaus-league'); ?></option>
                        <option value="retired" <?php selected($status, 'retired'); ?>><?php _e('Retired', 'plughaus-league'); ?></option>
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
        wp_nonce_field('psl_match_meta_nonce', 'psl_match_meta_nonce');
        
        $league_id = get_post_meta($post->ID, '_psl_league_id', true);
        $season_id = get_post_meta($post->ID, '_psl_season_id', true);
        $home_team_id = get_post_meta($post->ID, '_psl_home_team_id', true);
        $away_team_id = get_post_meta($post->ID, '_psl_away_team_id', true);
        $match_date = get_post_meta($post->ID, '_psl_match_date', true);
        $venue = get_post_meta($post->ID, '_psl_venue', true);
        $status = get_post_meta($post->ID, '_psl_status', true);
        $home_score = get_post_meta($post->ID, '_psl_home_score', true);
        $away_score = get_post_meta($post->ID, '_psl_away_score', true);
        
        // Get leagues, seasons, and teams for dropdowns
        $leagues = get_posts(array('post_type' => 'psl_league', 'posts_per_page' => -1));
        $seasons = get_posts(array('post_type' => 'psl_season', 'posts_per_page' => -1));
        $teams = get_posts(array('post_type' => 'psl_team', 'posts_per_page' => -1));
        ?>
        <table class="form-table">
            <tr>
                <th><label for="psl_league_id"><?php _e('League', 'plughaus-league'); ?></label></th>
                <td>
                    <select id="psl_league_id" name="psl_league_id" required>
                        <option value=""><?php _e('Select League', 'plughaus-league'); ?></option>
                        <?php foreach ($leagues as $league): ?>
                            <option value="<?php echo $league->ID; ?>" <?php selected($league_id, $league->ID); ?>>
                                <?php echo esc_html($league->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="psl_season_id"><?php _e('Season', 'plughaus-league'); ?></label></th>
                <td>
                    <select id="psl_season_id" name="psl_season_id">
                        <option value=""><?php _e('Select Season', 'plughaus-league'); ?></option>
                        <?php foreach ($seasons as $season): ?>
                            <option value="<?php echo $season->ID; ?>" <?php selected($season_id, $season->ID); ?>>
                                <?php echo esc_html($season->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="psl_home_team_id"><?php _e('Home Team', 'plughaus-league'); ?></label></th>
                <td>
                    <select id="psl_home_team_id" name="psl_home_team_id" required>
                        <option value=""><?php _e('Select Home Team', 'plughaus-league'); ?></option>
                        <?php foreach ($teams as $team): ?>
                            <option value="<?php echo $team->ID; ?>" <?php selected($home_team_id, $team->ID); ?>>
                                <?php echo esc_html($team->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="psl_away_team_id"><?php _e('Away Team', 'plughaus-league'); ?></label></th>
                <td>
                    <select id="psl_away_team_id" name="psl_away_team_id" required>
                        <option value=""><?php _e('Select Away Team', 'plughaus-league'); ?></option>
                        <?php foreach ($teams as $team): ?>
                            <option value="<?php echo $team->ID; ?>" <?php selected($away_team_id, $team->ID); ?>>
                                <?php echo esc_html($team->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="psl_match_date"><?php _e('Match Date & Time', 'plughaus-league'); ?></label></th>
                <td>
                    <input type="datetime-local" id="psl_match_date" name="psl_match_date" 
                           value="<?php echo esc_attr($match_date); ?>" />
                </td>
            </tr>
            <tr>
                <th><label for="psl_venue"><?php _e('Venue', 'plughaus-league'); ?></label></th>
                <td>
                    <input type="text" id="psl_venue" name="psl_venue" 
                           value="<?php echo esc_attr($venue); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th><label for="psl_status"><?php _e('Status', 'plughaus-league'); ?></label></th>
                <td>
                    <select id="psl_status" name="psl_status">
                        <option value="scheduled" <?php selected($status, 'scheduled'); ?>><?php _e('Scheduled', 'plughaus-league'); ?></option>
                        <option value="in_progress" <?php selected($status, 'in_progress'); ?>><?php _e('In Progress', 'plughaus-league'); ?></option>
                        <option value="completed" <?php selected($status, 'completed'); ?>><?php _e('Completed', 'plughaus-league'); ?></option>
                        <option value="postponed" <?php selected($status, 'postponed'); ?>><?php _e('Postponed', 'plughaus-league'); ?></option>
                        <option value="cancelled" <?php selected($status, 'cancelled'); ?>><?php _e('Cancelled', 'plughaus-league'); ?></option>
                    </select>
                </td>
            </tr>
        </table>
        
        <h4><?php _e('Match Result', 'plughaus-league'); ?></h4>
        <table class="form-table">
            <tr>
                <th><label for="psl_home_score"><?php _e('Home Team Score', 'plughaus-league'); ?></label></th>
                <td>
                    <input type="number" id="psl_home_score" name="psl_home_score" 
                           value="<?php echo esc_attr($home_score); ?>" min="0" />
                </td>
            </tr>
            <tr>
                <th><label for="psl_away_score"><?php _e('Away Team Score', 'plughaus-league'); ?></label></th>
                <td>
                    <input type="number" id="psl_away_score" name="psl_away_score" 
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
        wp_nonce_field('psl_season_meta_nonce', 'psl_season_meta_nonce');
        
        $league_id = get_post_meta($post->ID, '_psl_league_id', true);
        $start_date = get_post_meta($post->ID, '_psl_start_date', true);
        $end_date = get_post_meta($post->ID, '_psl_end_date', true);
        $status = get_post_meta($post->ID, '_psl_status', true);
        $is_current = get_post_meta($post->ID, '_psl_is_current', true);
        
        // Get leagues for dropdown
        $leagues = get_posts(array('post_type' => 'psl_league', 'posts_per_page' => -1));
        ?>
        <table class="form-table">
            <tr>
                <th><label for="psl_league_id"><?php _e('League', 'plughaus-league'); ?></label></th>
                <td>
                    <select id="psl_league_id" name="psl_league_id" required>
                        <option value=""><?php _e('Select League', 'plughaus-league'); ?></option>
                        <?php foreach ($leagues as $league): ?>
                            <option value="<?php echo $league->ID; ?>" <?php selected($league_id, $league->ID); ?>>
                                <?php echo esc_html($league->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="psl_start_date"><?php _e('Start Date', 'plughaus-league'); ?></label></th>
                <td>
                    <input type="date" id="psl_start_date" name="psl_start_date" 
                           value="<?php echo esc_attr($start_date); ?>" />
                </td>
            </tr>
            <tr>
                <th><label for="psl_end_date"><?php _e('End Date', 'plughaus-league'); ?></label></th>
                <td>
                    <input type="date" id="psl_end_date" name="psl_end_date" 
                           value="<?php echo esc_attr($end_date); ?>" />
                </td>
            </tr>
            <tr>
                <th><label for="psl_status"><?php _e('Status', 'plughaus-league'); ?></label></th>
                <td>
                    <select id="psl_status" name="psl_status">
                        <option value="upcoming" <?php selected($status, 'upcoming'); ?>><?php _e('Upcoming', 'plughaus-league'); ?></option>
                        <option value="active" <?php selected($status, 'active'); ?>><?php _e('Active', 'plughaus-league'); ?></option>
                        <option value="completed" <?php selected($status, 'completed'); ?>><?php _e('Completed', 'plughaus-league'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="psl_is_current"><?php _e('Current Season', 'plughaus-league'); ?></label></th>
                <td>
                    <input type="checkbox" id="psl_is_current" name="psl_is_current" value="1" 
                           <?php checked($is_current, '1'); ?> />
                    <label for="psl_is_current"><?php _e('This is the current active season', 'plughaus-league'); ?></label>
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
        if ($post_type === 'psl_league' && 
            isset($_POST['psl_league_meta_nonce']) && 
            wp_verify_nonce($_POST['psl_league_meta_nonce'], 'psl_league_meta_nonce')) {
            
            // Generate league code if not exists
            $league_code = get_post_meta($post_id, '_psl_league_code', true);
            if (empty($league_code)) {
                $league_code = PSL_Utilities::generate_league_code();
                update_post_meta($post_id, '_psl_league_code', $league_code);
            }
            
            update_post_meta($post_id, '_psl_sport', sanitize_text_field($_POST['psl_sport']));
            update_post_meta($post_id, '_psl_status', sanitize_text_field($_POST['psl_status']));
            update_post_meta($post_id, '_psl_organizer', sanitize_text_field($_POST['psl_organizer']));
            update_post_meta($post_id, '_psl_contact_email', sanitize_email($_POST['psl_contact_email']));
            update_post_meta($post_id, '_psl_location', sanitize_text_field($_POST['psl_location']));
        }
        
        // Save team meta
        if ($post_type === 'psl_team' && 
            isset($_POST['psl_team_meta_nonce']) && 
            wp_verify_nonce($_POST['psl_team_meta_nonce'], 'psl_team_meta_nonce')) {
            
            // Generate team code if not exists
            $team_code = get_post_meta($post_id, '_psl_team_code', true);
            if (empty($team_code)) {
                $team_code = PSL_Utilities::generate_team_code();
                update_post_meta($post_id, '_psl_team_code', $team_code);
            }
            
            update_post_meta($post_id, '_psl_league_id', intval($_POST['psl_league_id']));
            update_post_meta($post_id, '_psl_coach_id', intval($_POST['psl_coach_id']));
            update_post_meta($post_id, '_psl_status', sanitize_text_field($_POST['psl_status']));
            update_post_meta($post_id, '_psl_founded_year', intval($_POST['psl_founded_year']));
            update_post_meta($post_id, '_psl_colors', sanitize_text_field($_POST['psl_colors']));
            update_post_meta($post_id, '_psl_venue', sanitize_text_field($_POST['psl_venue']));
        }
        
        // Save player meta
        if ($post_type === 'psl_player' && 
            isset($_POST['psl_player_meta_nonce']) && 
            wp_verify_nonce($_POST['psl_player_meta_nonce'], 'psl_player_meta_nonce')) {
            
            update_post_meta($post_id, '_psl_team_id', intval($_POST['psl_team_id']));
            update_post_meta($post_id, '_psl_jersey_number', intval($_POST['psl_jersey_number']));
            update_post_meta($post_id, '_psl_position', sanitize_text_field($_POST['psl_position']));
            update_post_meta($post_id, '_psl_date_of_birth', sanitize_text_field($_POST['psl_date_of_birth']));
            update_post_meta($post_id, '_psl_height', intval($_POST['psl_height']));
            update_post_meta($post_id, '_psl_weight', intval($_POST['psl_weight']));
            update_post_meta($post_id, '_psl_status', sanitize_text_field($_POST['psl_status']));
        }
        
        // Save match meta
        if ($post_type === 'psl_match' && 
            isset($_POST['psl_match_meta_nonce']) && 
            wp_verify_nonce($_POST['psl_match_meta_nonce'], 'psl_match_meta_nonce')) {
            
            update_post_meta($post_id, '_psl_league_id', intval($_POST['psl_league_id']));
            update_post_meta($post_id, '_psl_season_id', intval($_POST['psl_season_id']));
            update_post_meta($post_id, '_psl_home_team_id', intval($_POST['psl_home_team_id']));
            update_post_meta($post_id, '_psl_away_team_id', intval($_POST['psl_away_team_id']));
            update_post_meta($post_id, '_psl_match_date', sanitize_text_field($_POST['psl_match_date']));
            update_post_meta($post_id, '_psl_venue', sanitize_text_field($_POST['psl_venue']));
            update_post_meta($post_id, '_psl_status', sanitize_text_field($_POST['psl_status']));
            update_post_meta($post_id, '_psl_home_score', intval($_POST['psl_home_score']));
            update_post_meta($post_id, '_psl_away_score', intval($_POST['psl_away_score']));
        }
        
        // Save season meta
        if ($post_type === 'psl_season' && 
            isset($_POST['psl_season_meta_nonce']) && 
            wp_verify_nonce($_POST['psl_season_meta_nonce'], 'psl_season_meta_nonce')) {
            
            update_post_meta($post_id, '_psl_league_id', intval($_POST['psl_league_id']));
            update_post_meta($post_id, '_psl_start_date', sanitize_text_field($_POST['psl_start_date']));
            update_post_meta($post_id, '_psl_end_date', sanitize_text_field($_POST['psl_end_date']));
            update_post_meta($post_id, '_psl_status', sanitize_text_field($_POST['psl_status']));
            
            $is_current = isset($_POST['psl_is_current']) ? '1' : '0';
            update_post_meta($post_id, '_psl_is_current', $is_current);
            
            // If this is set as current, unset others for the same league
            if ($is_current === '1') {
                $league_id = intval($_POST['psl_league_id']);
                $other_seasons = get_posts(array(
                    'post_type' => 'psl_season',
                    'meta_query' => array(
                        array(
                            'key' => '_psl_league_id',
                            'value' => $league_id,
                            'compare' => '='
                        )
                    ),
                    'posts_per_page' => -1,
                    'exclude' => array($post_id)
                ));
                
                foreach ($other_seasons as $season) {
                    update_post_meta($season->ID, '_psl_is_current', '0');
                }
            }
        }
    }
}