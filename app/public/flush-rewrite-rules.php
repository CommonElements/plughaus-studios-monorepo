<?php
/**
 * Flush rewrite rules to fix permalink issues
 */

// Load WordPress
require_once('wp-config.php');
require_once('wp-load.php');

echo "🔄 Flushing rewrite rules...\n";

// Flush rewrite rules
flush_rewrite_rules(true);

echo "✅ Rewrite rules flushed successfully!\n\n";

// Check if pages are accessible
$pages_to_check = array(
    'home' => home_url('/'),
    'plugins' => home_url('/plugins/'),
    'plugin-directory' => home_url('/plugin-directory/'),
    'pricing' => home_url('/pricing/'),
    'about' => home_url('/about/'),
);

echo "🔗 Testing page URLs:\n";
foreach ($pages_to_check as $name => $url) {
    $page = get_page_by_path(str_replace(home_url('/'), '', rtrim($url, '/')));
    if ($page) {
        $template = get_post_meta($page->ID, '_wp_page_template', true);
        echo "✅ {$name}: {$url} (ID: {$page->ID}, Template: " . ($template ?: 'default') . ")\n";
    } else {
        echo "❌ {$name}: {$url} (Page not found)\n";
    }
}

echo "\n📋 All Pages:\n";
$all_pages = get_pages(array('sort_column' => 'menu_order,post_title'));
foreach ($all_pages as $page) {
    $template = get_post_meta($page->ID, '_wp_page_template', true);
    $url = get_permalink($page->ID);
    echo "- {$page->post_title}: {$url} (Template: " . ($template ?: 'default') . ")\n";
}

echo "\n✨ Done! Try visiting the pages now.\n";
?>