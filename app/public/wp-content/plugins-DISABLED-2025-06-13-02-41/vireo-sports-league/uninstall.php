<?php
/**
 * Vireo Sports League Manager Uninstall
 *
 * Uninstalling Vireo Sports League Manager deletes user options, tables, and data.
 *
 * @package Vireo_Sports_League
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Check if we should remove data
$remove_data = get_option('vsl_remove_data_on_uninstall', false);

if (!$remove_data) {
    // User chose to keep data, so we're done
    return;
}

global $wpdb;

// Delete options
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'vsl_%'");
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_vsl_%'");
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'vireo_league_%'");

// Delete custom post types
$post_types = array('vsl_team', 'vsl_player', 'vsl_match', 'vsl_venue', 'vsl_season');

foreach ($post_types as $post_type) {
    $posts = get_posts(array(
        'post_type' => $post_type,
        'posts_per_page' => -1,
        'post_status' => 'any',
    ));
    
    foreach ($posts as $post) {
        wp_delete_post($post->ID, true);
    }
}

// Delete taxonomies
$taxonomies = array('vsl_league', 'vsl_sport_type', 'vsl_division');

foreach ($taxonomies as $taxonomy) {
    $terms = get_terms(array(
        'taxonomy' => $taxonomy,
        'hide_empty' => false,
    ));
    
    if (!is_wp_error($terms)) {
        foreach ($terms as $term) {
            wp_delete_term($term->term_id, $taxonomy);
        }
    }
}

// Delete custom tables
$tables = array(
    $wpdb->prefix . 'vsl_standings',
    $wpdb->prefix . 'vsl_statistics',
    $wpdb->prefix . 'vsl_player_stats',
    $wpdb->prefix . 'vsl_team_stats',
    $wpdb->prefix . 'vsl_match_events',
    $wpdb->prefix . 'vsl_registrations',
    $wpdb->prefix . 'vsl_schedules',
);

foreach ($tables as $table) {
    $wpdb->query("DROP TABLE IF EXISTS {$table}");
}

// Delete user meta
$wpdb->query("DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE 'vsl_%'");
$wpdb->query("DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE '_vsl_%'");

// Delete transients
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_vsl_%'");
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_vsl_%'");

// Remove any scheduled cron events
$cron_events = array(
    'vsl_daily_standings_update',
    'vsl_weekly_stats_calculation',
    'vsl_match_reminders',
);

foreach ($cron_events as $event) {
    wp_clear_scheduled_hook($event);
}

// Clear any cached data
wp_cache_flush();