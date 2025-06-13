<?php
/**
 * Create Proper Plugin Landing Pages via WordPress CMS
 * 
 * This creates individual plugin pages through WordPress CMS,
 * NOT hardcoded in theme files.
 */

require_once('./wp-load.php');

echo "<h2>Creating Plugin Landing Pages via WordPress CMS</h2>\n";

// Plugin data for creating proper landing pages
$plugin_data = [
    'property-management' => [
        'title' => 'Property Management Plugin',
        'slug' => 'property-management',
        'wordpress_org_url' => 'https://wordpress.org/plugins/vireo-property-management/',
        'pro_product_url' => '/product/vireo-property-management-pro/',
        'price' => '$149/year',
        'hero_title' => 'Vireo Property Management',
        'hero_subtitle' => 'Professional property management for WordPress. Manage properties, tenants, leases, and maintenance requests with powerful tools designed for landlords and property managers.',
        'features' => [
            '🏢 Property Management' => 'Track multiple properties with detailed information, photos, and documentation.',
            '👥 Tenant Management' => 'Comprehensive tenant profiles with contact information, lease history, and communications.',
            '📄 Lease Tracking' => 'Digital lease management with automatic renewals, rent tracking, and payment history.',
            '🔧 Maintenance Requests' => 'Streamlined maintenance workflow with tenant portal and vendor management.'
        ],
        'free_features' => [
            'Up to 5 properties',
            'Basic tenant management', 
            'Simple lease tracking',
            'Community support'
        ],
        'pro_features' => [
            'Unlimited properties',
            'Advanced analytics',
            'Payment automation',
            'Premium support',
            'Advanced reporting'
        ]
    ],
    
    'sports-league-manager' => [
        'title' => 'Sports League Manager Plugin',
        'slug' => 'sports-league-manager',
        'wordpress_org_url' => 'https://wordpress.org/plugins/vireo-sports-league/',
        'pro_product_url' => '/product/vireo-sports-league-pro/',
        'price' => '$79/year',
        'hero_title' => 'Vireo Sports League Manager',
        'hero_subtitle' => 'Complete sports league management for WordPress. Manage teams, players, schedules, and statistics with professional tools for league administrators.',
        'features' => [
            '⚽ Team Management' => 'Organize teams with rosters, coaching staff, and team information.',
            '👤 Player Profiles' => 'Detailed player profiles with statistics, photos, and performance tracking.',
            '📅 Schedule Management' => 'Create and manage game schedules with automatic notifications.',
            '📊 Statistics Tracking' => 'Comprehensive statistics for players, teams, and leagues.'
        ],
        'free_features' => [
            'Up to 2 leagues',
            'Basic team management',
            'Simple scheduling',
            'Community support'
        ],
        'pro_features' => [
            'Unlimited leagues',
            'Advanced statistics',
            'Tournament brackets',
            'Premium support',
            'Mobile app'
        ]
    ],
    
    'equipment-rental' => [
        'title' => 'Equipment Rental Plugin',
        'slug' => 'equipment-rental',
        'wordpress_org_url' => 'https://wordpress.org/plugins/equiprent-pro/',
        'pro_product_url' => '/product/equiprent-pro/',
        'price' => '$129/year',
        'hero_title' => 'EquipRent Pro',
        'hero_subtitle' => 'Professional equipment rental management for WordPress. Handle inventory, bookings, customers, and billing for your rental business.',
        'features' => [
            '🔧 Inventory Management' => 'Track equipment availability, maintenance schedules, and depreciation.',
            '📅 Booking System' => 'Online booking calendar with real-time availability and automated confirmations.',
            '👥 Customer Management' => 'Customer profiles with rental history, preferences, and payment information.',
            '💰 Billing & Invoicing' => 'Automated billing with late fees, deposits, and damage assessment tools.'
        ],
        'free_features' => [
            'Up to 25 items',
            'Basic booking system',
            'Simple customer management',
            'Community support'
        ],
        'pro_features' => [
            'Unlimited equipment',
            'Advanced booking features',
            'Delivery scheduling',
            'Premium support',
            'Mobile app'
        ]
    ],
    
    'auto-shop-management' => [
        'title' => 'Auto Shop Management Plugin',
        'slug' => 'auto-shop-management',
        'wordpress_org_url' => 'https://wordpress.org/plugins/dealeredge/',
        'pro_product_url' => '/product/dealeredge-pro/',
        'price' => '$149/year',
        'hero_title' => 'DealerEdge',
        'hero_subtitle' => 'Complete auto shop and small dealer management for WordPress. Streamline work orders, inventory, customer relationships, and billing.',
        'features' => [
            '🔧 Work Order Management' => 'Digital work orders with parts, labor, time tracking, and photo documentation.',
            '🚗 Vehicle History' => 'Complete vehicle service history with customer notifications and service reminders.',
            '📦 Inventory Management' => 'Parts inventory with automatic reorder points and supplier integration.',
            '💼 Customer Portal' => 'Customers can view service history, schedule appointments, and receive updates.'
        ],
        'free_features' => [
            'Up to 10 work orders/month',
            'Basic customer management',
            'Simple inventory tracking',
            'Community support'
        ],
        'pro_features' => [
            'Unlimited work orders',
            'Advanced reporting',
            'Multi-location support',
            'Premium support',
            'Mobile app'
        ]
    ],
    
    'fitness-studio-management' => [
        'title' => 'Fitness Studio Management Plugin',
        'slug' => 'fitness-studio-management',
        'wordpress_org_url' => 'https://wordpress.org/plugins/gymflow/',
        'pro_product_url' => '/product/gymflow-pro/',
        'price' => '$99/year',
        'hero_title' => 'GymFlow',
        'hero_subtitle' => 'Professional fitness studio management for WordPress. Handle memberships, class scheduling, trainer management, and member progress tracking.',
        'features' => [
            '👥 Member Management' => 'Comprehensive member profiles with photos, emergency contacts, and membership history.',
            '📅 Class Scheduling' => 'Automated class scheduling with instructor assignments and capacity management.',
            '🏋️ Equipment Booking' => 'Allow members to reserve equipment and training slots in advance.',
            '📊 Progress Tracking' => 'Track member goals, achievements, and workout progress over time.'
        ],
        'free_features' => [
            'Up to 50 members',
            'Basic class scheduling',
            'Simple member management',
            'Community support'
        ],
        'pro_features' => [
            'Unlimited members',
            'Advanced scheduling',
            'Payment processing',
            'Premium support',
            'Mobile app'
        ]
    ],
    
    'photography-studio-management' => [
        'title' => 'Photography Studio Management Plugin',
        'slug' => 'photography-studio-management',
        'wordpress_org_url' => 'https://wordpress.org/plugins/studiosnap/',
        'pro_product_url' => '/product/studiosnap-pro/',
        'price' => '$79/year',
        'hero_title' => 'StudioSnap',
        'hero_subtitle' => 'Complete photography studio management for WordPress. Handle client bookings, session management, contracts, and gallery delivery.',
        'features' => [
            '📸 Session Management' => 'Schedule photo sessions with equipment needs, location details, and shot lists.',
            '👤 Client Portal' => 'Clients can view galleries, approve photos, place orders, and download final images.',
            '📝 Contract Management' => 'Digital contracts with e-signatures, model releases, and usage rights.',
            '🖼️ Gallery Management' => 'Organized photo galleries with client access controls and watermarking.'
        ],
        'free_features' => [
            'Up to 10 sessions/month',
            'Basic client management',
            'Simple gallery sharing',
            'Community support'
        ],
        'pro_features' => [
            'Unlimited sessions',
            'Advanced galleries',
            'Contract automation',
            'Premium support',
            'Mobile app'
        ]
    ]
];

// Create/update individual plugin pages
foreach ($plugin_data as $slug => $data) {
    echo "<h3>Processing: {$data['title']}</h3>\n";
    
    // Check if page already exists
    $existing_page = get_page_by_path("plugins/{$slug}");
    
    // Create the content using WordPress blocks/HTML
    $content = "<!-- wp:heading {\"level\":1} -->
<h1>{$data['hero_title']}</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {\"fontSize\":\"large\"} -->
<p class=\"has-large-font-size\">{$data['hero_subtitle']}</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class=\"wp-block-buttons\">
<!-- wp:button {\"backgroundColor\":\"primary\"} -->
<div class=\"wp-block-button\"><a class=\"wp-block-button__link has-primary-background-color has-background\" href=\"{$data['wordpress_org_url']}\">Download Free Version</a></div>
<!-- /wp:button -->

<!-- wp:button {\"backgroundColor\":\"secondary\"} -->
<div class=\"wp-block-button\"><a class=\"wp-block-button__link has-secondary-background-color has-background\" href=\"{$data['pro_product_url']}\">View Pro Features ({$data['price']})</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->

<!-- wp:heading -->
<h2>Key Features</h2>
<!-- /wp:heading -->

<!-- wp:columns -->
<div class=\"wp-block-columns\">";

    $feature_count = 0;
    foreach ($data['features'] as $title => $description) {
        if ($feature_count % 2 == 0) {
            $content .= "<!-- wp:column -->
<div class=\"wp-block-column\">";
        }
        
        $content .= "<!-- wp:heading {\"level\":3} -->
<h3>{$title}</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>{$description}</p>
<!-- /wp:paragraph -->";

        $feature_count++;
        if ($feature_count % 2 == 0) {
            $content .= "</div>
<!-- /wp:column -->";
        }
    }
    
    if ($feature_count % 2 != 0) {
        $content .= "</div>
<!-- /wp:column -->";
    }

    $content .= "</div>
<!-- /wp:columns -->

<!-- wp:heading -->
<h2>Choose Your Version</h2>
<!-- /wp:heading -->

<!-- wp:columns -->
<div class=\"wp-block-columns\">
<!-- wp:column -->
<div class=\"wp-block-column\">
<!-- wp:heading {\"level\":3} -->
<h3>Free Version</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {\"fontSize\":\"large\",\"textColor\":\"primary\"} -->
<p class=\"has-primary-color has-text-color has-large-font-size\">$0</p>
<!-- /wp:paragraph -->

<!-- wp:list -->";

    foreach ($data['free_features'] as $feature) {
        $content .= "<li>{$feature}</li>";
    }

    $content .= "<!-- /wp:list -->

<!-- wp:button {\"backgroundColor\":\"primary\"} -->
<div class=\"wp-block-button\"><a class=\"wp-block-button__link has-primary-background-color has-background\" href=\"{$data['wordpress_org_url']}\">Download Free</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:column -->

<!-- wp:column {\"className\":\"featured-pricing\"} -->
<div class=\"wp-block-column featured-pricing\">
<!-- wp:heading {\"level\":3} -->
<h3>Pro Version</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {\"fontSize\":\"large\",\"textColor\":\"primary\"} -->
<p class=\"has-primary-color has-text-color has-large-font-size\">{$data['price']}</p>
<!-- /wp:paragraph -->

<!-- wp:list -->";

    foreach ($data['pro_features'] as $feature) {
        $content .= "<li>{$feature}</li>";
    }

    $content .= "<!-- /wp:list -->

<!-- wp:button {\"backgroundColor\":\"primary\"} -->
<div class=\"wp-block-button\"><a class=\"wp-block-button__link has-primary-background-color has-background\" href=\"{$data['pro_product_url']}\">Get Pro Version</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->";

    // Get the plugins parent page
    $plugins_parent = get_page_by_path('plugins');
    $parent_id = $plugins_parent ? $plugins_parent->ID : 0;
    
    if (!$existing_page) {
        // Create new page
        $page_id = wp_insert_post([
            'post_title' => $data['title'],
            'post_content' => $content,
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_name' => $slug,
            'post_parent' => $parent_id
        ]);
        
        if ($page_id && !is_wp_error($page_id)) {
            echo "   ✅ Created: {$data['title']} (ID: $page_id)\n";
            echo "   📍 URL: /plugins/{$slug}/\n";
        } else {
            echo "   ❌ Failed to create: {$data['title']}\n";
            if (is_wp_error($page_id)) {
                echo "   Error: " . $page_id->get_error_message() . "\n";
            }
        }
    } else {
        // Update existing page
        $updated = wp_update_post([
            'ID' => $existing_page->ID,
            'post_content' => $content,
            'post_parent' => $parent_id
        ]);
        
        if ($updated && !is_wp_error($updated)) {
            echo "   ✅ Updated: {$data['title']} (ID: {$existing_page->ID})\n";
            echo "   📍 URL: /plugins/{$slug}/\n";
        } else {
            echo "   ❌ Failed to update: {$data['title']}\n";
        }
    }
}

echo "<h3>Next Steps:</h3>\n";
echo "<ol>\n";
echo "<li>Test plugin landing pages: <a href='http://vireo.local/plugins/'>http://vireo.local/plugins/</a></li>\n";
echo "<li>Update theme to use proper WordPress navigation for free download links</li>\n";
echo "<li>Test user flow from shop → individual plugin page → WordPress.org</li>\n";
echo "<li>Verify all links work correctly</li>\n";
echo "</ol>\n";

echo "<p><strong>✅ All plugin pages created via WordPress CMS (not hardcoded in theme)</strong></p>\n";
?>