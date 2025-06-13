<?php
require_once('./wp-load.php');

echo "<h2>Setting Up Navigation Menu</h2>\n";

// Check if navigation menu exists
$menu_name = 'Primary Navigation';
$menu = wp_get_nav_menu_object($menu_name);

if (!$menu) {
    echo "<p>Creating navigation menu...</p>\n";
    
    // Create the menu
    $menu_id = wp_create_nav_menu($menu_name);
    
    if (is_wp_error($menu_id)) {
        echo "<p>❌ Error creating menu: " . $menu_id->get_error_message() . "</p>\n";
    } else {
        echo "<p>✅ Menu created with ID: $menu_id</p>\n";
        
        // Add menu items
        $menu_items = array(
            array('title' => 'Home', 'url' => home_url('/')),
            array('title' => 'Industries', 'url' => home_url('/industries/'), 'children' => array(
                array('title' => 'Property Management', 'url' => home_url('/industries/property-management/')),
                array('title' => 'Sports Leagues', 'url' => home_url('/industries/sports-leagues/')),
                array('title' => 'Equipment Rental', 'url' => home_url('/industries/equipment-rental/')),
                array('title' => 'Automotive', 'url' => home_url('/industries/automotive/')),
                array('title' => 'Gym & Fitness', 'url' => home_url('/industries/gym-fitness/')),
                array('title' => 'Creative Services', 'url' => home_url('/industries/creative-services/')),
            )),
            array('title' => 'Plugins', 'url' => home_url('/shop/')),
            array('title' => 'About', 'url' => home_url('/about/')),
            array('title' => 'Contact', 'url' => home_url('/contact/')),
        );
        
        $parent_id = 0;
        foreach ($menu_items as $item) {
            $menu_item_id = wp_update_nav_menu_item($menu_id, 0, array(
                'menu-item-title' => $item['title'],
                'menu-item-url' => $item['url'],
                'menu-item-status' => 'publish',
                'menu-item-type' => 'custom',
                'menu-item-parent-id' => 0
            ));
            
            echo "<p>Added: {$item['title']}</p>\n";
            
            // Add children if they exist
            if (isset($item['children']) && is_array($item['children'])) {
                foreach ($item['children'] as $child) {
                    $child_id = wp_update_nav_menu_item($menu_id, 0, array(
                        'menu-item-title' => $child['title'],
                        'menu-item-url' => $child['url'],
                        'menu-item-status' => 'publish',
                        'menu-item-type' => 'custom',
                        'menu-item-parent-id' => $menu_item_id
                    ));
                    echo "<p>  └ Added child: {$child['title']}</p>\n";
                }
            }
        }
        
        // Assign to theme location
        $locations = get_theme_mod('nav_menu_locations');
        $locations['primary'] = $menu_id;
        set_theme_mod('nav_menu_locations', $locations);
        
        echo "<p>✅ Menu assigned to primary location</p>\n";
    }
} else {
    echo "<p>✅ Navigation menu already exists</p>\n";
}

// Check current menu assignment
$locations = get_theme_mod('nav_menu_locations');
echo "<h3>Current Menu Assignments:</h3>\n";
foreach ($locations as $location => $menu_id) {
    $menu = wp_get_nav_menu_object($menu_id);
    echo "<p>$location: " . ($menu ? $menu->name : 'Not set') . "</p>\n";
}

echo "<p><a href='http://vireo.local'>→ Test Navigation</a></p>\n";
echo "<p><a href='http://vireo.local/wp-admin/nav-menus.php'>→ Manage Menus in Admin</a></p>\n";
?>