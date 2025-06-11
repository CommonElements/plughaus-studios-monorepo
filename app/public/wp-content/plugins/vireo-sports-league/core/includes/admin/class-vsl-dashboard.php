<?php
/**
 * Dashboard functionality for PlugHaus Sports League
 * 
 * @package PlugHaus_Sports_League
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class PSL_Dashboard {
    
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
        if (!PSL_Utilities::current_user_can_view_leagues()) {
            return;
        }
        
        wp_add_dashboard_widget(
            'psl_overview_widget',
            __('Sports League Overview', 'plughaus-league'),
            array($this, 'overview_widget')
        );
        
        wp_add_dashboard_widget(
            'psl_recent_matches_widget',
            __('Recent Matches', 'plughaus-league'),
            array($this, 'recent_matches_widget')
        );
    }
    
    /**
     * Overview widget
     */
    public function overview_widget() {
        $stats = $this->get_overview_stats();
        ?>
        <div class="psl-dashboard-overview">
            <div class="psl-stats-grid">
                <div class="psl-stat">
                    <span class="psl-stat-number"><?php echo esc_html($stats['leagues']); ?></span>
                    <span class="psl-stat-label"><?php _e('Active Leagues', 'plughaus-league'); ?></span>
                </div>
                <div class="psl-stat">
                    <span class="psl-stat-number"><?php echo esc_html($stats['teams']); ?></span>
                    <span class="psl-stat-label"><?php _e('Teams', 'plughaus-league'); ?></span>
                </div>
                <div class="psl-stat">
                    <span class="psl-stat-number"><?php echo esc_html($stats['players']); ?></span>
                    <span class="psl-stat-label"><?php _e('Players', 'plughaus-league'); ?></span>
                </div>
                <div class="psl-stat">
                    <span class="psl-stat-number"><?php echo esc_html($stats['matches']); ?></span>
                    <span class="psl-stat-label"><?php _e('Matches Played', 'plughaus-league'); ?></span>
                </div>
            </div>
            <p>
                <a href="<?php echo admin_url('admin.php?page=psl-dashboard'); ?>" class="button button-primary">
                    <?php _e('View Full Dashboard', 'plughaus-league'); ?>
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
            echo '<p>' . __('No recent matches found.', 'plughaus-league') . '</p>';
            return;
        }
        
        echo '<ul class="psl-recent-matches">';
        foreach ($matches as $match) {
            $home_team = get_the_title($match->home_team_id);
            $away_team = get_the_title($match->away_team_id);
            $home_score = get_post_meta($match->ID, '_psl_home_score', true);
            $away_score = get_post_meta($match->ID, '_psl_away_score', true);
            $match_date = get_post_meta($match->ID, '_psl_match_date', true);
            
            echo '<li>';
            printf(
                '<strong>%s %d - %d %s</strong><br><small>%s</small>',
                esc_html($home_team),
                esc_html($home_score),
                esc_html($away_score),
                esc_html($away_team),
                esc_html(PSL_Utilities::format_match_date($match_date))
            );
            echo '</li>';
        }
        echo '</ul>';
        
        echo '<p><a href="' . admin_url('edit.php?post_type=psl_match') . '">' . __('View All Matches', 'plughaus-league') . '</a></p>';
    }
    
    /**
     * Get overview statistics
     */
    private function get_overview_stats() {
        return array(
            'leagues' => wp_count_posts('psl_league')->publish,
            'teams' => wp_count_posts('psl_team')->publish,
            'players' => wp_count_posts('psl_player')->publish,
            'matches' => wp_count_posts('psl_match')->publish,
        );
    }
    
    /**
     * Get recent matches
     */
    private function get_recent_matches() {
        return get_posts(array(
            'post_type' => 'psl_match',
            'posts_per_page' => 5,
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => '_psl_status',
                    'value' => 'completed',
                    'compare' => '='
                )
            ),
            'orderby' => 'date',
            'order' => 'DESC'
        ));
    }
}