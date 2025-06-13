<?php
require_once('./wp-load.php');

echo "<h2>WooCommerce Product URLs</h2>\n";

$products = wc_get_products(array(
    'limit' => -1,
    'status' => 'publish'
));

echo "<table border='1' style='border-collapse: collapse; width: 100%;'>\n";
echo "<tr><th>Product Name</th><th>Product URL</th><th>Suggested Free Page</th></tr>\n";

foreach ($products as $product) {
    $product_url = get_permalink($product->get_id());
    $product_name = $product->get_name();
    
    // Generate suggested free page URL
    $suggested_free_url = '';
    if (strpos($product_name, 'Property') !== false) {
        $suggested_free_url = '/plugins/property-management/';
    } elseif (strpos($product_name, 'Sports') !== false) {
        $suggested_free_url = '/plugins/sports-league-manager/';
    } elseif (strpos($product_name, 'EquipRent') !== false) {
        $suggested_free_url = '/plugins/equipment-rental/';
    } elseif (strpos($product_name, 'Dealer') !== false) {
        $suggested_free_url = '/plugins/auto-shop-management/';
    } elseif (strpos($product_name, 'Gym') !== false) {
        $suggested_free_url = '/plugins/fitness-studio-management/';
    } elseif (strpos($product_name, 'Studio') !== false) {
        $suggested_free_url = '/plugins/photography-studio-management/';
    }
    
    echo "<tr>";
    echo "<td>$product_name</td>";
    echo "<td><a href='$product_url'>$product_url</a></td>";
    echo "<td>$suggested_free_url</td>";
    echo "</tr>\n";
}

echo "</table>\n";

echo "<h3>WordPress Pages to Create:</h3>\n";
$pages_to_create = [
    'Property Management Plugin' => '/plugins/property-management/',
    'Sports League Manager Plugin' => '/plugins/sports-league-manager/',
    'Equipment Rental Plugin' => '/plugins/equipment-rental/',
    'Auto Shop Management Plugin' => '/plugins/auto-shop-management/',
    'Fitness Studio Management Plugin' => '/plugins/fitness-studio-management/',
    'Photography Studio Management Plugin' => '/plugins/photography-studio-management/',
];

foreach ($pages_to_create as $title => $url) {
    echo "<p>• <strong>$title</strong> at $url</p>\n";
}
?>