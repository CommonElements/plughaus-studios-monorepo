<?php
/**
 * Admin Page Creator
 * Add this to functions.php temporarily to create pages from WordPress admin
 */

// Add admin menu item
add_action('admin_menu', 'vireo_add_page_creator_menu');

function vireo_add_page_creator_menu() {
    add_management_page(
        'Create Vireo Pages',
        'Create Pages',
        'manage_options',
        'vireo-create-pages',
        'vireo_create_pages_admin_page'
    );
}

function vireo_create_pages_admin_page() {
    if (isset($_POST['create_pages'])) {
        vireo_create_all_pages();
    }
    
    ?>
    <div class="wrap">
        <h1>Create Vireo Designs Pages</h1>
        
        <?php if (isset($_POST['create_pages'])): ?>
            <div class="notice notice-success"><p>Pages created successfully!</p></div>
        <?php endif; ?>
        
        <p>This will create all the necessary pages for the Vireo Designs website.</p>
        
        <form method="post">
            <?php wp_nonce_field('create_pages', 'create_pages_nonce'); ?>
            <p>
                <input type="submit" name="create_pages" class="button button-primary" value="Create All Pages">
            </p>
        </form>
        
        <h3>Pages that will be created:</h3>
        <ul>
            <li><strong>Plugins</strong> - Main plugin showcase page</li>
            <li><strong>Plugin Directory</strong> - Searchable plugin catalog</li>
            <li><strong>Property Management Pro</strong> - Individual plugin page</li>
            <li><strong>Features</strong> - Feature overview page</li>
            <li><strong>Pricing</strong> - Pricing information</li>
            <li><strong>About</strong> - About the company</li>
            <li><strong>Contact</strong> - Contact form</li>
            <li><strong>Support</strong> - Support resources</li>
            <li><strong>Blog</strong> - Blog posts</li>
        </ul>
    </div>
    <?php
}

function vireo_create_all_pages() {
    if (!wp_verify_nonce($_POST['create_pages_nonce'], 'create_pages')) {
        return false;
    }
    
    $pages_to_create = array(
        'plugins' => array(
            'title' => 'Our Plugins',
            'content' => 'Discover our collection of professional WordPress plugins designed to power your business.',
            'template' => 'page-plugins.php'
        ),
        'plugin-directory' => array(
            'title' => 'Plugin Directory',
            'content' => 'Browse all available plugins with advanced search and filtering capabilities.',
            'template' => 'page-plugin-directory.php'
        ),
        'plugin-property-management' => array(
            'title' => 'Property Management Pro',
            'content' => 'Complete property management solution for WordPress with tenant tracking, lease management, and payment processing.',
            'template' => 'page-plugin-property-management.php'
        ),
        'features' => array(
            'title' => 'Features',
            'content' => 'Discover the powerful features that make our plugins stand out in the WordPress ecosystem.',
            'template' => 'page-features.php'
        ),
        'pricing' => array(
            'title' => 'Pricing',
            'content' => 'Simple, transparent pricing for all our plugins. Choose the plan that works best for your business.',
            'template' => 'page-pricing.php'
        ),
        'about' => array(
            'title' => 'About Us',
            'content' => 'Learn about Vireo Designs and our mission to create professional WordPress plugins for modern businesses.',
            'template' => 'page-about.php'
        ),
        'contact' => array(
            'title' => 'Contact',
            'content' => 'Get in touch with our team. We are here to help with questions, support, and custom development needs.',
            'template' => 'page-contact.php'
        ),
        'support' => array(
            'title' => 'Support',
            'content' => 'Get help with our plugins, find documentation, and access our support resources.',
            'template' => 'page-support.php'
        ),
        'blog' => array(
            'title' => 'Blog',
            'content' => 'Stay updated with the latest news, tutorials, and insights from the Vireo Designs team.',
            'template' => 'page-blog.php'
        )
    );
    
    foreach ($pages_to_create as $slug => $page_data) {
        // Check if page already exists
        $existing_page = get_page_by_path($slug);
        
        if ($existing_page) {
            // Update template if needed
            update_post_meta($existing_page->ID, '_wp_page_template', $page_data['template']);
            continue;
        }
        
        // Create the page
        $page_id = wp_insert_post(array(
            'post_title' => $page_data['title'],
            'post_content' => $page_data['content'],
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_name' => $slug,
            'comment_status' => 'closed',
            'ping_status' => 'closed'
        ));
        
        if ($page_id && !is_wp_error($page_id)) {
            // Set the page template
            update_post_meta($page_id, '_wp_page_template', $page_data['template']);
        }
    }
    
    // Flush rewrite rules
    flush_rewrite_rules();
    
    return true;
}
?>