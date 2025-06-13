<?php
/**
 * Create Test Page with StudioSnap Booking Form
 */

require_once('./wp-config.php');
require_once('./wp-load.php');

echo "<h1>📄 Creating Test Page for StudioSnap</h1>\n";

// Check if page already exists
$existing_page = get_page_by_title('StudioSnap Booking Test');

if ($existing_page) {
    echo "<p>Page already exists: <a href='" . get_permalink($existing_page->ID) . "'>View StudioSnap Booking Test</a></p>\n";
} else {
    // Create test page
    $page_data = array(
        'post_title' => 'StudioSnap Booking Test',
        'post_content' => '
<h2>Photography Session Booking</h2>
<p>Book your professional photography session using our booking system below:</p>

[studiosnap_booking_form]

<hr>

<h3>About Our Services</h3>
<p>We offer professional photography sessions for portraits, families, events, and more. Our studio is equipped with state-of-the-art equipment and our photographers have years of experience.</p>
        ',
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_author' => 1
    );
    
    $page_id = wp_insert_post($page_data);
    
    if ($page_id) {
        echo "<p style='color: green;'>✅ Test page created successfully!</p>\n";
        echo "<p><strong>View page:</strong> <a href='" . get_permalink($page_id) . "'>StudioSnap Booking Test</a></p>\n";
        echo "<p><strong>Edit page:</strong> <a href='" . admin_url('post.php?post=' . $page_id . '&action=edit') . "'>Edit in Admin</a></p>\n";
    } else {
        echo "<p style='color: red;'>❌ Failed to create test page</p>\n";
    }
}

echo "<hr>\n";
echo "<h2>🎯 Next Actions</h2>\n";
echo "<ol>\n";
echo "<li><strong>Activate StudioSnap:</strong> Go to <a href='" . admin_url('plugins.php') . "'>Plugins page</a> and activate StudioSnap</li>\n";
echo "<li><strong>View Test Page:</strong> Visit the booking test page to see the form in action</li>\n";
echo "<li><strong>Admin Dashboard:</strong> Check <a href='" . admin_url() . "'>WordPress Admin</a> for StudioSnap menu</li>\n";
echo "<li><strong>Submit Test Booking:</strong> Fill out the booking form to test functionality</li>\n";
echo "</ol>\n";

echo "<p><em>Setup completed at " . date('Y-m-d H:i:s') . "</em></p>\n";
?>