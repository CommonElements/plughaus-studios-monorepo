<?php
/**
 * Add debug function to show which template is being loaded
 */

function debug_template_hierarchy($template) {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('Template being loaded: ' . $template);
    }
    
    // Add visible debug info to admin bar or as comment
    echo '<!-- Template: ' . basename($template) . ' -->';
    
    return $template;
}
add_filter('template_include', 'debug_template_hierarchy');

// Also debug the query
function debug_wp_query() {
    global $wp_query;
    
    if (defined('WP_DEBUG') && WP_DEBUG) {
        echo '<!-- WP Query Debug: ';
        echo 'is_page: ' . ($wp_query->is_page() ? 'true' : 'false') . ', ';
        echo 'is_search: ' . ($wp_query->is_search() ? 'true' : 'false') . ', ';
        echo 'is_404: ' . ($wp_query->is_404() ? 'true' : 'false') . ', ';
        echo 'queried_object_id: ' . $wp_query->get_queried_object_id() . ' ';
        echo '-->';
    }
}
add_action('wp_head', 'debug_wp_query');
?>