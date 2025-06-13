<?php
/**
 * Property Listings Block Frontend Render
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Get block attributes with defaults
$layout = $attributes['layout'] ?? 'grid';
$columns = $attributes['columns'] ?? 3;
$posts_per_page = $attributes['postsPerPage'] ?? 6;
$show_pagination = $attributes['showPagination'] ?? true;
$property_type = $attributes['propertyType'] ?? 'all';
$order_by = $attributes['orderBy'] ?? 'date';
$order = $attributes['order'] ?? 'DESC';
$show_excerpt = $attributes['showExcerpt'] ?? true;
$show_price = $attributes['showPrice'] ?? true;
$show_location = $attributes['showLocation'] ?? true;

// Build query arguments
$query_args = array(
    'post_type' => 'property',
    'post_status' => 'publish',
    'posts_per_page' => $posts_per_page,
    'orderby' => $order_by,
    'order' => $order,
    'meta_query' => array(
        'relation' => 'OR',
        array(
            'key' => '_vpm_property_status',
            'value' => 'available',
            'compare' => '='
        ),
        array(
            'key' => '_vpm_property_status',
            'compare' => 'NOT EXISTS'
        )
    )
);

// Add property type filter if specified
if ($property_type && $property_type !== 'all') {
    $query_args['tax_query'] = array(
        array(
            'taxonomy' => 'property_type',
            'field' => 'slug',
            'terms' => $property_type,
        ),
    );
}

// Handle pagination
if ($show_pagination) {
    $paged = get_query_var('paged') ? get_query_var('paged') : 1;
    $query_args['paged'] = $paged;
}

// Execute query
$properties_query = new WP_Query($query_args);

// Generate wrapper classes
$wrapper_classes = array(
    'vpm-property-listings',
    'layout-' . esc_attr($layout),
    'wp-block-vireo-property-property-listings'
);

if ($layout === 'grid') {
    $wrapper_classes[] = 'columns-' . esc_attr($columns);
}

$wrapper_attributes = get_block_wrapper_attributes(array(
    'class' => implode(' ', $wrapper_classes)
));

?>

<div <?php echo $wrapper_attributes; ?>>
    <?php if ($properties_query->have_posts()) : ?>
        <div class="vpm-properties-grid layout-<?php echo esc_attr($layout); ?> columns-<?php echo esc_attr($columns); ?>">
            <?php while ($properties_query->have_posts()) : $properties_query->the_post(); ?>
                <?php
                $property_id = get_the_ID();
                $property_price = get_post_meta($property_id, '_vpm_property_price', true);
                $property_location = get_post_meta($property_id, '_vpm_property_address', true);
                $property_type_terms = get_the_terms($property_id, 'property_type');
                $property_status = get_post_meta($property_id, '_vpm_property_status', true);
                ?>
                
                <article class="vpm-property-item" data-property-id="<?php echo esc_attr($property_id); ?>">
                    <div class="vpm-property-image">
                        <?php if (has_post_thumbnail()) : ?>
                            <a href="<?php the_permalink(); ?>" class="vpm-property-image-link">
                                <?php the_post_thumbnail('medium', array('class' => 'vpm-property-thumbnail')); ?>
                            </a>
                        <?php else : ?>
                            <div class="vpm-property-placeholder">
                                <span class="dashicons dashicons-building"></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($property_status && $property_status !== 'available') : ?>
                            <div class="vpm-property-status-badge">
                                <span class="status-<?php echo esc_attr($property_status); ?>">
                                    <?php echo esc_html(ucfirst($property_status)); ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="vpm-property-content">
                        <h3 class="vpm-property-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                        
                        <?php if ($show_location && $property_location) : ?>
                            <p class="vpm-property-location">
                                <span class="dashicons dashicons-location"></span>
                                <?php echo esc_html($property_location); ?>
                            </p>
                        <?php endif; ?>
                        
                        <?php if ($property_type_terms && !is_wp_error($property_type_terms)) : ?>
                            <div class="vpm-property-type">
                                <?php foreach ($property_type_terms as $term) : ?>
                                    <span class="property-type-badge"><?php echo esc_html($term->name); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($show_price && $property_price) : ?>
                            <p class="vpm-property-price">
                                <strong><?php echo wp_kses_post(vpm_format_price($property_price)); ?></strong>
                                <span class="price-period">/month</span>
                            </p>
                        <?php endif; ?>
                        
                        <?php if ($show_excerpt && has_excerpt()) : ?>
                            <div class="vpm-property-excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="vpm-property-actions">
                            <a href="<?php the_permalink(); ?>" class="vpm-btn vpm-btn-primary">
                                <?php _e('View Details', 'vireo-property'); ?>
                            </a>
                        </div>
                    </div>
                </article>
                
            <?php endwhile; ?>
        </div>
        
        <?php if ($show_pagination && $properties_query->max_num_pages > 1) : ?>
            <div class="vpm-pagination">
                <?php
                echo paginate_links(array(
                    'total' => $properties_query->max_num_pages,
                    'current' => max(1, get_query_var('paged')),
                    'format' => '?paged=%#%',
                    'show_all' => false,
                    'type' => 'list',
                    'end_size' => 2,
                    'mid_size' => 1,
                    'prev_next' => true,
                    'prev_text' => __('&laquo; Previous', 'vireo-property'),
                    'next_text' => __('Next &raquo;', 'vireo-property'),
                    'add_args' => false,
                    'add_fragment' => '',
                ));
                ?>
            </div>
        <?php endif; ?>
        
    <?php else : ?>
        <div class="vpm-no-properties">
            <p><?php _e('No properties found.', 'vireo-property'); ?></p>
        </div>
    <?php endif; ?>
</div>

<?php
// Reset global post data
wp_reset_postdata();

/**
 * Helper function to format price
 */
function vpm_format_price($price) {
    if (!$price) return '';
    
    // Remove any existing formatting
    $price = preg_replace('/[^0-9.]/', '', $price);
    
    if (is_numeric($price)) {
        return '$' . number_format(floatval($price), 0);
    }
    
    return esc_html($price);
}
?>