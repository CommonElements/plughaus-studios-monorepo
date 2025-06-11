<?php
/**
 * Dashboard functionality for Vireo Sports League
 * 
 * @package Vireo_Sports_League
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class VSL_Dashboard {
    
    /**
     * Initialize dashboard
     */
    public function __construct() {
        add_action('wp_dashboard_setup', array($this, 'add_dashboard_widgets'));
    }
    
    /**
     * Add dashboard widgets
     */
    public function add_dashboard_widgets() {
        if (!VSL_Utilities::current_user_can_view_leagues()) {
            return;
        }
        
        wp_add_dashboard_widget(
            'vsl_overview_widget',
            __('Sports League Overview', 'vireo-league'),
            array($this, 'overview_widget')
        );
        
        wp_add_dashboard_widget(
            'vsl_recent_matches_widget',
            __('Recent Matches', 'vireo-league'),
            array($this, 'recent_matches_widget')
        );
    }
    
    /**
     * Overview widget
     */
    public function overview_widget() {
        $stats = $this->get_overview_stats();
        ?>
        <div class="vsl-dashboard-overview">
            <div class="vsl-stats-grid">
                <div class="vsl-stat">
                    <span class="vsl-stat-number"><?php echo esc_html($stats['leagues']); ?></span>
                    <span class="vsl-stat-label"><?php _e('Active Leagues', 'vireo-league'); ?></span>
                </div>
                <div class="vsl-stat">
                    <span class="vsl-stat-number"><?php echo esc_html($stats['teams']); ?></span>
                    <span class="vsl-stat-label"><?php _e('Teams', 'vireo-league'); ?></span>
                </div>
                <div class="vsl-stat">
                    <span class="vsl-stat-number"><?php echo esc_html($stats['players']); ?></span>
                    <span class="vsl-stat-label"><?php _e('Players', 'vireo-league'); ?></span>
                </div>
                <div class="vsl-stat">
                    <span class="vsl-stat-number"><?php echo esc_html($stats['matches']); ?></span>
                    <span class="vsl-stat-label"><?php _e('Matches Played', 'vireo-league'); ?></span>
                </div>
            </div>
            <p>
                <a href="<?php echo admin_url('admin.php?page=vsl-dashboard'); ?>" class="button button-primary">
                    <?php _e('View Full Dashboard', 'vireo-league'); ?>
                </a>
            </p>
        </div>
        <?php
    }
    
    /**
     * Recent matches widget
     */
    public function recent_matches_widget() {
        $matches = $this->get_recent_matches();
        
        if (empty($matches)) {
            echo '<p>' . __('No recent matches found.', 'vireo-league') . '</p>';
            return;
        }
        
        echo '<ul class="vsl-recent-matches">';
        foreach ($matches as $match) {
            $home_team = get_the_title($match->home_team_id);
            $away_team = get_the_title($match->away_team_id);
            $home_score = get_post_meta($match->ID, '_vsl_home_score', true);
            $away_score = get_post_meta($match->ID, '_vsl_away_score', true);
            $match_date = get_post_meta($match->ID, '_vsl_match_date', true);
            
            echo '<li>';
            printf(
                '<strong>%s %d - %d %s</strong><br><small>%s</small>',
                esc_html($home_team),
                esc_html($home_score),
                esc_html($away_score),
                esc_html($away_team),
                esc_html(VSL_Utilities::format_match_date($match_date))
            );
            echo '</li>';
        }
        echo '</ul>';
        
        echo '<p><a href="' . admin_url('edit.php?post_type=vsl_match') . '">' . __('View All Matches', 'vireo-league') . '</a></p>';
    }
    
    /**
     * Get overview statistics
     */
    private function get_overview_stats() {
        return array(
            'leagues' => wp_count_posts('vsl_league')->publish,
            'teams' => wp_count_posts('vsl_team')->publish,
            'players' => wp_count_posts('vsl_player')->publish,
            'matches' => wp_count_posts('vsl_match')->publish,
        );
    }
    
    /**
     * Get recent matches
     */
    private function get_recent_matches() {
        return get_posts(array(
            'post_type' => 'vsl_match',
            'posts_per_page' => 5,
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => '_vsl_status',
                    'value' => 'completed',
                    'compare' => '='
                )
            ),
            'orderby' => 'date',
            'order' => 'DESC'
        ));
    }
}