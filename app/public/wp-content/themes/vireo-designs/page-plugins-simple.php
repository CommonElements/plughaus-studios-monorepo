<?php
/**
 * Template Name: Simple Plugins Test
 * Simple test to verify template loading
 */

get_header();
?>

<div style="padding: 2rem; background: #f0f8f0; border: 2px solid green; margin: 2rem;">
    <h1>🎯 PLUGINS PAGE TEMPLATE IS WORKING!</h1>
    <p>This confirms the template system is functioning correctly.</p>
    <p>Current page: <?php the_title(); ?></p>
    <p>Template file: page-plugins-simple.php</p>
    <p>URL: <?php echo get_permalink(); ?></p>
</div>

<?php get_footer(); ?>