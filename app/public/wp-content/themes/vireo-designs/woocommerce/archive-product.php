<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 */

get_header('shop'); ?>

<div class="woocommerce-shop-header">
    <div class="container">
        <?php if (apply_filters('woocommerce_show_page_title', true)) : ?>
            <h1 class="woocommerce-products-header__title page-title"><?php woocommerce_page_title(); ?></h1>
        <?php endif; ?>
        
        <div class="shop-description">
            <p>Professional WordPress plugins for modern businesses. All plugins include free versions with optional Pro upgrades for advanced features and priority support.</p>
        </div>
        
        <div class="shop-stats">
            <div class="stat">
                <strong><?php echo wp_count_posts('product')->publish; ?></strong>
                <span>Premium Plugins</span>
            </div>
            <div class="stat">
                <strong>10,000+</strong>
                <span>Happy Customers</span>
            </div>
            <div class="stat">
                <strong>99.9%</strong>
                <span>Uptime SLA</span>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="woocommerce-shop-content">
        
        <div class="shop-filters">
            <?php
            // Display product categories
            $categories = get_terms(array(
                'taxonomy' => 'product_cat',
                'hide_empty' => true,
                'exclude' => array(get_option('default_product_cat'))
            ));
            
            if ($categories) :
            ?>
                <div class="product-categories">
                    <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="category-filter <?php echo is_shop() && !is_product_category() ? 'active' : ''; ?>">
                        All Plugins
                    </a>
                    <?php foreach ($categories as $category) : ?>
                        <a href="<?php echo esc_url(get_term_link($category)); ?>" class="category-filter <?php echo is_product_category($category->slug) ? 'active' : ''; ?>">
                            <?php echo esc_html($category->name); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if (woocommerce_product_loop()) : ?>
            
            <?php woocommerce_product_loop_start(); ?>
            
            <?php if (wc_get_loop_prop('is_shortcode')) : ?>
                <?php woocommerce_output_all_notices(); ?>
            <?php endif; ?>
            
            <?php while (have_posts()) : ?>
                <?php the_post(); ?>
                <?php wc_get_template_part('content', 'product'); ?>
            <?php endwhile; ?>
            
            <?php woocommerce_product_loop_end(); ?>
            
            <?php woocommerce_output_all_notices(); ?>
            
            <div class="woocommerce-pagination">
                <?php echo paginate_links(apply_filters('woocommerce_pagination_args', array(
                    'base'         => esc_url_raw(str_replace(999999999, '%#%', remove_query_arg('add-to-cart', get_pagenum_link(999999999, false)))),
                    'format'       => '',
                    'add_args'     => false,
                    'current'      => max(1, get_query_var('paged')),
                    'total'        => $GLOBALS['wp_query']->max_num_pages,
                    'prev_text'    => '&larr;',
                    'next_text'    => '&rarr;',
                    'type'         => 'plain',
                    'end_size'     => 3,
                    'mid_size'     => 3,
                ))); ?>
            </div>
            
        <?php else : ?>
            
            <?php woocommerce_output_all_notices(); ?>
            
            <div class="woocommerce-no-products-found">
                <h2>No products found</h2>
                <p>Sorry, no products were found matching your selection.</p>
                <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="btn btn-primary">Return to Shop</a>
            </div>
            
        <?php endif; ?>
        
    </div>
</div>

<style>
.woocommerce-shop-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: white;
    padding: 4rem 0;
    margin-bottom: 3rem;
    text-align: center;
}

.woocommerce-shop-header h1 {
    font-size: 3rem;
    margin-bottom: 1rem;
    color: white;
}

.shop-description {
    font-size: 1.25rem;
    opacity: 0.9;
    margin-bottom: 2rem;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.shop-stats {
    display: flex;
    justify-content: center;
    gap: 3rem;
    margin-top: 2rem;
}

.shop-stats .stat {
    text-align: center;
}

.shop-stats strong {
    display: block;
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.shop-filters {
    margin-bottom: 2rem;
    text-align: center;
}

.product-categories {
    display: flex;
    justify-content: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.category-filter {
    padding: 0.75rem 1.5rem;
    background: white;
    border: 2px solid var(--gray-300);
    border-radius: var(--radius-lg);
    text-decoration: none;
    color: var(--gray-700);
    font-weight: 500;
    transition: all 0.2s;
}

.category-filter:hover,
.category-filter.active {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
}

.woocommerce-no-products-found {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: var(--radius-lg);
    border: 1px solid var(--gray-200);
}

@media (max-width: 768px) {
    .woocommerce-shop-header h1 {
        font-size: 2rem;
    }
    
    .shop-stats {
        flex-direction: column;
        gap: 1rem;
    }
    
    .product-categories {
        flex-direction: column;
        align-items: center;
    }
}
</style>

<?php get_footer('shop'); ?>