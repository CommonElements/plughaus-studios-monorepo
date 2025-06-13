<?php
/**
 * Simple Page Creator for PlugHaus Studios
 * Creates the basic SQL to insert pages manually
 */

// Database configuration - update these if needed
$db_host = 'localhost';
$db_name = 'local';
$db_user = 'root';
$db_pass = 'root';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>PlugHaus Studios Page Creator</h2>\n";
    
    // Check what pages already exist
    $existing_pages = $pdo->query("SELECT post_name, post_title FROM wp_posts WHERE post_type = 'page' AND post_status = 'publish'")->fetchAll(PDO::FETCH_KEY_PAIR);
    
    echo "<h3>Existing Pages:</h3>\n<ul>\n";
    foreach ($existing_pages as $slug => $title) {
        echo "<li>$title ($slug)</li>\n";
    }
    echo "</ul>\n";
    
    // Pages to create
    $pages_to_create = array(
        'plugins' => array(
            'title' => 'Our Plugins',
            'content' => 'Discover our collection of professional WordPress plugins.',
            'template' => 'page-plugins.php'
        ),
        'plugin-directory' => array(
            'title' => 'Plugin Directory', 
            'content' => 'Browse all available plugins with search and filtering.',
            'template' => 'page-plugin-directory.php'
        ),
        'plugin-property-management' => array(
            'title' => 'Property Management Pro',
            'content' => 'Complete property management solution for WordPress.',
            'template' => 'page-plugin-property-management.php'
        )
    );
    
    echo "<h3>Creating Missing Pages:</h3>\n";
    
    foreach ($pages_to_create as $slug => $page_data) {
        if (isset($existing_pages[$slug])) {
            echo "<p>✓ Page '{$page_data['title']}' already exists</p>\n";
            continue;
        }
        
        // Insert the page
        $stmt = $pdo->prepare("
            INSERT INTO wp_posts (
                post_author, post_date, post_date_gmt, post_content, post_title, 
                post_excerpt, post_status, comment_status, ping_status, post_password,
                post_name, to_ping, pinged, post_modified, post_modified_gmt,
                post_content_filtered, post_parent, guid, menu_order, post_type, 
                post_mime_type, comment_count
            ) VALUES (
                1, NOW(), UTC_TIMESTAMP(), :content, :title, 
                '', 'publish', 'closed', 'closed', '',
                :slug, '', '', NOW(), UTC_TIMESTAMP(),
                '', 0, '', 0, 'page',
                '', 0
            )
        ");
        
        $stmt->execute([
            ':content' => $page_data['content'],
            ':title' => $page_data['title'],
            ':slug' => $slug
        ]);
        
        $page_id = $pdo->lastInsertId();
        
        // Set the page template
        $stmt = $pdo->prepare("
            INSERT INTO wp_postmeta (post_id, meta_key, meta_value) 
            VALUES (?, '_wp_page_template', ?)
        ");
        $stmt->execute([$page_id, $page_data['template']]);
        
        echo "<p>✓ Created page '{$page_data['title']}' (ID: $page_id) with template {$page_data['template']}</p>\n";
    }
    
    echo "<h3>URLs to Test:</h3>\n<ul>\n";
    $base_url = 'http://plughaus-studios-the-beginning-is-finished.local';
    foreach ($pages_to_create as $slug => $page_data) {
        echo "<li><a href='$base_url/$slug/' target='_blank'>{$page_data['title']}</a></li>\n";
    }
    echo "</ul>\n";
    
    echo "<p><strong>Success!</strong> All pages created. You can now visit the Plugin section.</p>\n";
    
} catch (PDOException $e) {
    echo "<h2>Database Error</h2>\n";
    echo "<p>Could not connect to database: " . $e->getMessage() . "</p>\n";
    echo "<p>Please check your database configuration in Local by Flywheel.</p>\n";
}
?>