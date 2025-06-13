<?php
/**
 * StudioSnap Post Types - Custom post type definitions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class SS_Post_Types {
    
    public static function init() {
        add_action('init', array(__CLASS__, 'register_post_types'));
        add_action('init', array(__CLASS__, 'register_post_statuses'));
        add_filter('post_updated_messages', array(__CLASS__, 'post_updated_messages'));
    }
    
    /**
     * Register custom post types
     */
    public static function register_post_types() {
        // Register Sessions
        register_post_type('ss_session', array(
            'labels' => array(
                'name' => __('Photography Sessions', 'studiosnap'),
                'singular_name' => __('Session', 'studiosnap'),
                'menu_name' => __('Sessions', 'studiosnap'),
                'add_new' => __('Add Session', 'studiosnap'),
                'add_new_item' => __('Add New Session', 'studiosnap'),
                'edit_item' => __('Edit Session', 'studiosnap'),
                'new_item' => __('New Session', 'studiosnap'),
                'view_item' => __('View Session', 'studiosnap'),
                'search_items' => __('Search Sessions', 'studiosnap'),
                'not_found' => __('No sessions found', 'studiosnap'),
                'not_found_in_trash' => __('No sessions found in trash', 'studiosnap'),
                'all_items' => __('All Sessions', 'studiosnap')
            ),
            'public' => false,
            'publicly_queryable' => false,
            'show_ui' => true,
            'show_in_menu' => 'studiosnap',
            'query_var' => true,
            'rewrite' => false,
            'capability_type' => 'ss_session',
            'map_meta_cap' => true,
            'has_archive' => false,
            'hierarchical' => false,
            'menu_position' => null,
            'supports' => array('title', 'editor', 'custom-fields'),
            'show_in_rest' => true
        ));
        
        // Register Clients
        register_post_type('ss_client', array(
            'labels' => array(
                'name' => __('Clients', 'studiosnap'),
                'singular_name' => __('Client', 'studiosnap'),
                'menu_name' => __('Clients', 'studiosnap'),
                'add_new' => __('Add Client', 'studiosnap'),
                'add_new_item' => __('Add New Client', 'studiosnap'),
                'edit_item' => __('Edit Client', 'studiosnap'),
                'new_item' => __('New Client', 'studiosnap'),
                'view_item' => __('View Client', 'studiosnap'),
                'search_items' => __('Search Clients', 'studiosnap'),
                'not_found' => __('No clients found', 'studiosnap'),
                'not_found_in_trash' => __('No clients found in trash', 'studiosnap'),
                'all_items' => __('All Clients', 'studiosnap')
            ),
            'public' => false,
            'publicly_queryable' => false,
            'show_ui' => true,
            'show_in_menu' => 'studiosnap',
            'query_var' => true,
            'rewrite' => false,
            'capability_type' => 'ss_client',
            'map_meta_cap' => true,
            'has_archive' => false,
            'hierarchical' => false,
            'menu_position' => null,
            'supports' => array('title', 'editor', 'custom-fields'),
            'show_in_rest' => true
        ));
        
        // Register Galleries
        register_post_type('ss_gallery', array(
            'labels' => array(
                'name' => __('Galleries', 'studiosnap'),
                'singular_name' => __('Gallery', 'studiosnap'),
                'menu_name' => __('Galleries', 'studiosnap'),
                'add_new' => __('Add Gallery', 'studiosnap'),
                'add_new_item' => __('Add New Gallery', 'studiosnap'),
                'edit_item' => __('Edit Gallery', 'studiosnap'),
                'new_item' => __('New Gallery', 'studiosnap'),
                'view_item' => __('View Gallery', 'studiosnap'),
                'search_items' => __('Search Galleries', 'studiosnap'),
                'not_found' => __('No galleries found', 'studiosnap'),
                'not_found_in_trash' => __('No galleries found in trash', 'studiosnap'),
                'all_items' => __('All Galleries', 'studiosnap')
            ),
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => 'studiosnap',
            'query_var' => true,
            'rewrite' => array('slug' => 'gallery'),
            'capability_type' => 'ss_gallery',
            'map_meta_cap' => true,
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => null,
            'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
            'show_in_rest' => true
        ));
        
        // Register Packages
        register_post_type('ss_package', array(
            'labels' => array(
                'name' => __('Photography Packages', 'studiosnap'),
                'singular_name' => __('Package', 'studiosnap'),
                'menu_name' => __('Packages', 'studiosnap'),
                'add_new' => __('Add Package', 'studiosnap'),
                'add_new_item' => __('Add New Package', 'studiosnap'),
                'edit_item' => __('Edit Package', 'studiosnap'),
                'new_item' => __('New Package', 'studiosnap'),
                'view_item' => __('View Package', 'studiosnap'),
                'search_items' => __('Search Packages', 'studiosnap'),
                'not_found' => __('No packages found', 'studiosnap'),
                'not_found_in_trash' => __('No packages found in trash', 'studiosnap'),
                'all_items' => __('All Packages', 'studiosnap')
            ),
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => 'studiosnap',
            'query_var' => true,
            'rewrite' => array('slug' => 'photography-package'),
            'capability_type' => 'ss_package',
            'map_meta_cap' => true,
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => null,
            'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
            'show_in_rest' => true
        ));
        
        // Register Invoices
        register_post_type('ss_invoice', array(
            'labels' => array(
                'name' => __('Invoices', 'studiosnap'),
                'singular_name' => __('Invoice', 'studiosnap'),
                'menu_name' => __('Invoices', 'studiosnap'),
                'add_new' => __('Add Invoice', 'studiosnap'),
                'add_new_item' => __('Add New Invoice', 'studiosnap'),
                'edit_item' => __('Edit Invoice', 'studiosnap'),
                'new_item' => __('New Invoice', 'studiosnap'),
                'view_item' => __('View Invoice', 'studiosnap'),
                'search_items' => __('Search Invoices', 'studiosnap'),
                'not_found' => __('No invoices found', 'studiosnap'),
                'not_found_in_trash' => __('No invoices found in trash', 'studiosnap'),
                'all_items' => __('All Invoices', 'studiosnap')
            ),
            'public' => false,
            'publicly_queryable' => false,
            'show_ui' => true,
            'show_in_menu' => 'studiosnap',
            'query_var' => true,
            'rewrite' => false,
            'capability_type' => 'ss_invoice',
            'map_meta_cap' => true,
            'has_archive' => false,
            'hierarchical' => false,
            'menu_position' => null,
            'supports' => array('title', 'custom-fields'),
            'show_in_rest' => true
        ));
        
        // Register Contracts
        register_post_type('ss_contract', array(
            'labels' => array(
                'name' => __('Contracts', 'studiosnap'),
                'singular_name' => __('Contract', 'studiosnap'),
                'menu_name' => __('Contracts', 'studiosnap'),
                'add_new' => __('Add Contract', 'studiosnap'),
                'add_new_item' => __('Add New Contract', 'studiosnap'),
                'edit_item' => __('Edit Contract', 'studiosnap'),
                'new_item' => __('New Contract', 'studiosnap'),
                'view_item' => __('View Contract', 'studiosnap'),
                'search_items' => __('Search Contracts', 'studiosnap'),
                'not_found' => __('No contracts found', 'studiosnap'),
                'not_found_in_trash' => __('No contracts found in trash', 'studiosnap'),
                'all_items' => __('All Contracts', 'studiosnap')
            ),
            'public' => false,
            'publicly_queryable' => false,
            'show_ui' => true,
            'show_in_menu' => 'studiosnap',
            'query_var' => true,
            'rewrite' => false,
            'capability_type' => 'ss_contract',
            'map_meta_cap' => true,
            'has_archive' => false,
            'hierarchical' => false,
            'menu_position' => null,
            'supports' => array('title', 'editor', 'custom-fields'),
            'show_in_rest' => true
        ));
    }
    
    /**
     * Register custom post statuses
     */
    public static function register_post_statuses() {
        // Session statuses
        register_post_status('ss_inquiry', array(
            'label' => __('Inquiry', 'studiosnap'),
            'public' => false,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Inquiry <span class="count">(%s)</span>', 'Inquiries <span class="count">(%s)</span>', 'studiosnap'),
        ));
        
        register_post_status('ss_confirmed', array(
            'label' => __('Confirmed', 'studiosnap'),
            'public' => false,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Confirmed <span class="count">(%s)</span>', 'Confirmed <span class="count">(%s)</span>', 'studiosnap'),
        ));
        
        register_post_status('ss_in_progress', array(
            'label' => __('In Progress', 'studiosnap'),
            'public' => false,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('In Progress <span class="count">(%s)</span>', 'In Progress <span class="count">(%s)</span>', 'studiosnap'),
        ));
        
        register_post_status('ss_completed', array(
            'label' => __('Completed', 'studiosnap'),
            'public' => false,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Completed <span class="count">(%s)</span>', 'Completed <span class="count">(%s)</span>', 'studiosnap'),
        ));
        
        register_post_status('ss_cancelled', array(
            'label' => __('Cancelled', 'studiosnap'),
            'public' => false,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Cancelled <span class="count">(%s)</span>', 'Cancelled <span class="count">(%s)</span>', 'studiosnap'),
        ));
        
        register_post_status('ss_rescheduled', array(
            'label' => __('Rescheduled', 'studiosnap'),
            'public' => false,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Rescheduled <span class="count">(%s)</span>', 'Rescheduled <span class="count">(%s)</span>', 'studiosnap'),
        ));
        
        // Invoice statuses
        register_post_status('ss_draft', array(
            'label' => __('Draft', 'studiosnap'),
            'public' => false,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Draft <span class="count">(%s)</span>', 'Drafts <span class="count">(%s)</span>', 'studiosnap'),
        ));
        
        register_post_status('ss_sent', array(
            'label' => __('Sent', 'studiosnap'),
            'public' => false,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Sent <span class="count">(%s)</span>', 'Sent <span class="count">(%s)</span>', 'studiosnap'),
        ));
        
        register_post_status('ss_paid', array(
            'label' => __('Paid', 'studiosnap'),
            'public' => false,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Paid <span class="count">(%s)</span>', 'Paid <span class="count">(%s)</span>', 'studiosnap'),
        ));
        
        register_post_status('ss_overdue', array(
            'label' => __('Overdue', 'studiosnap'),
            'public' => false,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Overdue <span class="count">(%s)</span>', 'Overdue <span class="count">(%s)</span>', 'studiosnap'),
        ));
        
        // Contract statuses
        register_post_status('ss_pending_signature', array(
            'label' => __('Pending Signature', 'studiosnap'),
            'public' => false,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Pending Signature <span class="count">(%s)</span>', 'Pending Signature <span class="count">(%s)</span>', 'studiosnap'),
        ));
        
        register_post_status('ss_signed', array(
            'label' => __('Signed', 'studiosnap'),
            'public' => false,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Signed <span class="count">(%s)</span>', 'Signed <span class="count">(%s)</span>', 'studiosnap'),
        ));
        
        register_post_status('ss_expired', array(
            'label' => __('Expired', 'studiosnap'),
            'public' => false,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Expired <span class="count">(%s)</span>', 'Expired <span class="count">(%s)</span>', 'studiosnap'),
        ));
    }
    
    /**
     * Custom post updated messages
     */
    public static function post_updated_messages($messages) {
        global $post;
        
        $messages['ss_session'] = array(
            0 => '',
            1 => __('Session updated.', 'studiosnap'),
            2 => __('Custom field updated.', 'studiosnap'),
            3 => __('Custom field deleted.', 'studiosnap'),
            4 => __('Session updated.', 'studiosnap'),
            5 => isset($_GET['revision']) ? sprintf(__('Session restored to revision from %s', 'studiosnap'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
            6 => __('Session created.', 'studiosnap'),
            7 => __('Session saved.', 'studiosnap'),
            8 => __('Session submitted.', 'studiosnap'),
            9 => sprintf(__('Session scheduled for: <strong>%1$s</strong>.', 'studiosnap'), date_i18n(__('M j, Y @ G:i', 'studiosnap'), strtotime($post->post_date))),
            10 => __('Session draft updated.', 'studiosnap')
        );
        
        $messages['ss_client'] = array(
            0 => '',
            1 => __('Client updated.', 'studiosnap'),
            2 => __('Custom field updated.', 'studiosnap'),
            3 => __('Custom field deleted.', 'studiosnap'),
            4 => __('Client updated.', 'studiosnap'),
            5 => isset($_GET['revision']) ? sprintf(__('Client restored to revision from %s', 'studiosnap'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
            6 => __('Client created.', 'studiosnap'),
            7 => __('Client saved.', 'studiosnap'),
            8 => __('Client submitted.', 'studiosnap'),
            9 => sprintf(__('Client scheduled for: <strong>%1$s</strong>.', 'studiosnap'), date_i18n(__('M j, Y @ G:i', 'studiosnap'), strtotime($post->post_date))),
            10 => __('Client draft updated.', 'studiosnap')
        );
        
        $messages['ss_gallery'] = array(
            0 => '',
            1 => sprintf(__('Gallery updated. <a href="%s">View gallery</a>', 'studiosnap'), esc_url(get_permalink($post->ID))),
            2 => __('Custom field updated.', 'studiosnap'),
            3 => __('Custom field deleted.', 'studiosnap'),
            4 => __('Gallery updated.', 'studiosnap'),
            5 => isset($_GET['revision']) ? sprintf(__('Gallery restored to revision from %s', 'studiosnap'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
            6 => sprintf(__('Gallery created. <a href="%s">View gallery</a>', 'studiosnap'), esc_url(get_permalink($post->ID))),
            7 => __('Gallery saved.', 'studiosnap'),
            8 => sprintf(__('Gallery submitted. <a target="_blank" href="%s">Preview gallery</a>', 'studiosnap'), esc_url(add_query_arg('preview', 'true', get_permalink($post->ID)))),
            9 => sprintf(__('Gallery scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview gallery</a>', 'studiosnap'), date_i18n(__('M j, Y @ G:i', 'studiosnap'), strtotime($post->post_date)), esc_url(get_permalink($post->ID))),
            10 => sprintf(__('Gallery draft updated. <a target="_blank" href="%s">Preview gallery</a>', 'studiosnap'), esc_url(add_query_arg('preview', 'true', get_permalink($post->ID))))
        );
        
        $messages['ss_package'] = array(
            0 => '',
            1 => sprintf(__('Package updated. <a href="%s">View package</a>', 'studiosnap'), esc_url(get_permalink($post->ID))),
            2 => __('Custom field updated.', 'studiosnap'),
            3 => __('Custom field deleted.', 'studiosnap'),
            4 => __('Package updated.', 'studiosnap'),
            5 => isset($_GET['revision']) ? sprintf(__('Package restored to revision from %s', 'studiosnap'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
            6 => sprintf(__('Package created. <a href="%s">View package</a>', 'studiosnap'), esc_url(get_permalink($post->ID))),
            7 => __('Package saved.', 'studiosnap'),
            8 => sprintf(__('Package submitted. <a target="_blank" href="%s">Preview package</a>', 'studiosnap'), esc_url(add_query_arg('preview', 'true', get_permalink($post->ID)))),
            9 => sprintf(__('Package scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview package</a>', 'studiosnap'), date_i18n(__('M j, Y @ G:i', 'studiosnap'), strtotime($post->post_date)), esc_url(get_permalink($post->ID))),
            10 => sprintf(__('Package draft updated. <a target="_blank" href="%s">Preview package</a>', 'studiosnap'), esc_url(add_query_arg('preview', 'true', get_permalink($post->ID))))
        );
        
        $messages['ss_invoice'] = array(
            0 => '',
            1 => __('Invoice updated.', 'studiosnap'),
            2 => __('Custom field updated.', 'studiosnap'),
            3 => __('Custom field deleted.', 'studiosnap'),
            4 => __('Invoice updated.', 'studiosnap'),
            5 => isset($_GET['revision']) ? sprintf(__('Invoice restored to revision from %s', 'studiosnap'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
            6 => __('Invoice created.', 'studiosnap'),
            7 => __('Invoice saved.', 'studiosnap'),
            8 => __('Invoice submitted.', 'studiosnap'),
            9 => sprintf(__('Invoice scheduled for: <strong>%1$s</strong>.', 'studiosnap'), date_i18n(__('M j, Y @ G:i', 'studiosnap'), strtotime($post->post_date))),
            10 => __('Invoice draft updated.', 'studiosnap')
        );
        
        $messages['ss_contract'] = array(
            0 => '',
            1 => __('Contract updated.', 'studiosnap'),
            2 => __('Custom field updated.', 'studiosnap'),
            3 => __('Custom field deleted.', 'studiosnap'),
            4 => __('Contract updated.', 'studiosnap'),
            5 => isset($_GET['revision']) ? sprintf(__('Contract restored to revision from %s', 'studiosnap'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
            6 => __('Contract created.', 'studiosnap'),
            7 => __('Contract saved.', 'studiosnap'),
            8 => __('Contract submitted.', 'studiosnap'),
            9 => sprintf(__('Contract scheduled for: <strong>%1$s</strong>.', 'studiosnap'), date_i18n(__('M j, Y @ G:i', 'studiosnap'), strtotime($post->post_date))),
            10 => __('Contract draft updated.', 'studiosnap')
        );
        
        return $messages;
    }
    
    /**
     * Get all session statuses
     */
    public static function get_session_statuses() {
        return array(
            'ss_inquiry' => __('Inquiry', 'studiosnap'),
            'ss_confirmed' => __('Confirmed', 'studiosnap'),
            'ss_in_progress' => __('In Progress', 'studiosnap'),
            'ss_completed' => __('Completed', 'studiosnap'),
            'ss_cancelled' => __('Cancelled', 'studiosnap'),
            'ss_rescheduled' => __('Rescheduled', 'studiosnap')
        );
    }
    
    /**
     * Get all invoice statuses
     */
    public static function get_invoice_statuses() {
        return array(
            'ss_draft' => __('Draft', 'studiosnap'),
            'ss_sent' => __('Sent', 'studiosnap'),
            'ss_paid' => __('Paid', 'studiosnap'),
            'ss_overdue' => __('Overdue', 'studiosnap')
        );
    }
    
    /**
     * Get all contract statuses
     */
    public static function get_contract_statuses() {
        return array(
            'ss_pending_signature' => __('Pending Signature', 'studiosnap'),
            'ss_signed' => __('Signed', 'studiosnap'),
            'ss_expired' => __('Expired', 'studiosnap')
        );
    }
    
    /**
     * Get session types
     */
    public static function get_session_types() {
        return array(
            'portrait' => __('Portrait Session', 'studiosnap'),
            'family' => __('Family Session', 'studiosnap'),
            'headshot' => __('Professional Headshots', 'studiosnap'),
            'event' => __('Event Photography', 'studiosnap'),
            'product' => __('Product Photography', 'studiosnap'),
            'wedding' => __('Wedding Photography', 'studiosnap'),
            'corporate' => __('Corporate Photography', 'studiosnap'),
            'maternity' => __('Maternity Session', 'studiosnap'),
            'newborn' => __('Newborn Session', 'studiosnap'),
            'senior' => __('Senior Portrait', 'studiosnap')
        );
    }
}