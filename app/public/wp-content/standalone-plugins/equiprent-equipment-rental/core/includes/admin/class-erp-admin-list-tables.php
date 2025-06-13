<?php
/**
 * Admin list tables for EquipRent Pro
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Admin list tables class
 */
class ERP_Admin_List_Tables {

    /**
     * Initialize list tables
     */
    public static function init() {
        add_action('admin_init', array(__CLASS__, 'setup_list_tables'));
    }

    /**
     * Setup list tables
     */
    public static function setup_list_tables() {
        // Custom columns for equipment post type
        add_filter('manage_erp_equipment_posts_columns', array(__CLASS__, 'equipment_columns'));
        add_action('manage_erp_equipment_posts_custom_column', array(__CLASS__, 'equipment_column_content'), 10, 2);
        add_filter('manage_edit-erp_equipment_sortable_columns', array(__CLASS__, 'equipment_sortable_columns'));
    }

    /**
     * Equipment columns
     */
    public static function equipment_columns($columns) {
        $new_columns = array();
        $new_columns['cb'] = $columns['cb'];
        $new_columns['title'] = __('Equipment Name', 'equiprent-pro');
        $new_columns['brand'] = __('Brand', 'equiprent-pro');
        $new_columns['model'] = __('Model', 'equiprent-pro');
        $new_columns['daily_rate'] = __('Daily Rate', 'equiprent-pro');
        $new_columns['status'] = __('Status', 'equiprent-pro');
        $new_columns['location'] = __('Location', 'equiprent-pro');
        $new_columns['taxonomy-equipment_category'] = __('Category', 'equiprent-pro');
        $new_columns['date'] = $columns['date'];

        return $new_columns;
    }

    /**
     * Equipment column content
     */
    public static function equipment_column_content($column, $post_id) {
        switch ($column) {
            case 'brand':
                $brand = get_post_meta($post_id, '_erp_brand', true);
                echo esc_html($brand ?: '-');
                break;

            case 'model':
                $model = get_post_meta($post_id, '_erp_model', true);
                echo esc_html($model ?: '-');
                break;

            case 'daily_rate':
                $daily_rate = get_post_meta($post_id, '_erp_daily_rate', true);
                if ($daily_rate && class_exists('ERP_Utilities')) {
                    echo ERP_Utilities::format_currency($daily_rate);
                } else if ($daily_rate) {
                    echo '$' . number_format($daily_rate, 2);
                } else {
                    echo '-';
                }
                break;

            case 'status':
                $status = get_post_meta($post_id, '_erp_status', true);
                if (!$status) {
                    $status = 'available'; // Default status
                }
                
                if (class_exists('ERP_Utilities')) {
                    echo ERP_Utilities::get_status_badge($status, 'equipment');
                } else {
                    echo esc_html(ucfirst($status));
                }
                break;

            case 'location':
                $location = get_post_meta($post_id, '_erp_location', true);
                echo esc_html($location ?: '-');
                break;
        }
    }

    /**
     * Equipment sortable columns
     */
    public static function equipment_sortable_columns($columns) {
        $columns['brand'] = 'brand';
        $columns['model'] = 'model';
        $columns['daily_rate'] = 'daily_rate';
        $columns['status'] = 'status';
        $columns['location'] = 'location';

        return $columns;
    }
}