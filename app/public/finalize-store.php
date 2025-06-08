<?php
/**
 * Finalize PlugHaus Studios Store Setup
 * Configure Stripe, License Manager, and final optimizations
 * Run this by visiting: http://plughaus-studios-the-beginning-is-finished.local/finalize-store.php
 */

// Include WordPress
define('WP_USE_THEMES', false);
require_once dirname(__FILE__) . '/wp-load.php';

// Check if user is admin
if (!is_user_logged_in() || !current_user_can('manage_options')) {
    wp_redirect(wp_login_url($_SERVER['REQUEST_URI']));
    exit;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>PlugHaus Studios Store Finalization</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1000px; margin: 0 auto; padding: 20px; }
        h1, h2 { color: #2563eb; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { color: #0066cc; font-style: italic; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 4px; color: #856404; margin: 20px 0; }
        .btn { background: #2563eb; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; margin: 10px 5px 10px 0; }
        .section { background: #f8f9fa; padding: 20px; margin: 20px 0; border-radius: 8px; }
        .code { background: #f1f3f4; padding: 15px; border-radius: 4px; font-family: monospace; margin: 10px 0; overflow-x: auto; white-space: pre-wrap; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .card { background: white; border: 1px solid #dee2e6; padding: 20px; border-radius: 8px; }
        .step { counter-increment: step-counter; position: relative; background: white; border: 1px solid #e2e8f0; padding: 20px; margin: 15px 0; border-radius: 8px; }
        .step::before { content: counter(step-counter); position: absolute; left: -10px; top: -10px; background: #2563eb; color: white; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; }
        .steps { counter-reset: step-counter; }
    </style>
</head>
<body>
    <h1>PlugHaus Studios Store Finalization</h1>
    
    <?php
    // 1. Configure License Manager settings
    echo '<div class="section">';
    echo '<h2>1. License Manager Configuration</h2>';
    
    // Update license manager settings
    if (function_exists('lmfwc_get_settings')) {
        update_option('lmfwc_settings_general_auto_delivery', '1');
        update_option('lmfwc_settings_general_auto_complete', '1');
        update_option('lmfwc_settings_general_hide_license_keys', '0');
        update_option('lmfwc_settings_general_enable_api', '1');
        
        echo '<p class="success">✓ Enabled automatic license delivery</p>';
        echo '<p class="success">✓ Enabled automatic order completion</p>';
        echo '<p class="success">✓ Enabled License Manager API</p>';
    } else {
        echo '<p class="error">✗ License Manager not properly configured</p>';
    }
    
    // Set up license generators for each product
    $products = get_posts(array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    ));
    
    foreach ($products as $product) {
        $enable_license = get_post_meta($product->ID, '_enable_license_management', true);
        if ($enable_license === 'yes') {
            // Update product to enable license generation
            update_post_meta($product->ID, '_lmfwc_licensed_product', '1');
            echo '<p class="success">✓ Enabled licensing for: ' . $product->post_title . '</p>';
        }
    }
    
    echo '</div>';
    
    // 2. Add "Get Pro" buttons to existing plugin cards
    echo '<div class="section">';
    echo '<h2>2. Enhancing Plugin Cards with Pro Buttons</h2>';
    
    // Update the plugin card template to include pro buttons
    $template_dir = get_template_directory();
    $plugin_card_file = $template_dir . '/template-parts/plugin-card.php';
    
    if (file_exists($plugin_card_file)) {
        $card_content = file_get_contents($plugin_card_file);
        
        // Check if pro button is already added
        if (strpos($card_content, 'plughaus_get_plugin_pro_button') === false) {
            // Add pro button section before the closing footer tag
            $pro_button_code = '
        <?php 
        // Add Pro button if linked product exists
        $pro_button = plughaus_get_plugin_pro_button(get_the_ID());
        if ($pro_button) :
        ?>
            <div class="plugin-pro-cta">
                <?php echo $pro_button; ?>
            </div>
        <?php endif; ?>';
            
            $updated_content = str_replace(
                '<footer class="plugin-card-actions">',
                $pro_button_code . "\n    <footer class=\"plugin-card-actions\">",
                $card_content
            );
            
            file_put_contents($plugin_card_file, $updated_content);
            echo '<p class="success">✓ Added Pro buttons to plugin cards</p>';
        } else {
            echo '<p class="info">→ Pro buttons already added to plugin cards</p>';
        }
    }
    
    echo '</div>';
    
    // 3. Create a professional shop page template
    echo '<div class="section">';
    echo '<h2>3. Creating Professional Shop Template</h2>';
    
    $shop_template = $template_dir . '/woocommerce/archive-product.php';
    $shop_dir = dirname($shop_template);
    
    if (!is_dir($shop_dir)) {
        mkdir($shop_dir, 0755, true);
        echo '<p class="success">✓ Created WooCommerce template directory</p>';
    }
    
    if (!file_exists($shop_template)) {
        $shop_content = '<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 */

get_header(\'shop\'); ?>

<div class="woocommerce-shop-header">
    <div class="container">
        <?php if (apply_filters(\'woocommerce_show_page_title\', true)) : ?>
            <h1 class="woocommerce-products-header__title page-title"><?php woocommerce_page_title(); ?></h1>
        <?php endif; ?>
        
        <div class="shop-description">
            <p>Professional WordPress plugins for modern businesses. All plugins include free versions with optional Pro upgrades for advanced features and priority support.</p>
        </div>
        
        <div class="shop-stats">
            <div class="stat">
                <strong><?php echo wp_count_posts(\'product\')->publish; ?></strong>
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
                \'taxonomy\' => \'product_cat\',
                \'hide_empty\' => true,
                \'exclude\' => array(get_option(\'default_product_cat\'))
            ));
            
            if ($categories) :
            ?>
                <div class="product-categories">
                    <a href="<?php echo esc_url(wc_get_page_permalink(\'shop\')); ?>" class="category-filter <?php echo is_shop() && !is_product_category() ? \'active\' : \'\'; ?>">
                        All Plugins
                    </a>
                    <?php foreach ($categories as $category) : ?>
                        <a href="<?php echo esc_url(get_term_link($category)); ?>" class="category-filter <?php echo is_product_category($category->slug) ? \'active\' : \'\'; ?>">
                            <?php echo esc_html($category->name); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if (woocommerce_product_loop()) : ?>
            
            <?php woocommerce_product_loop_start(); ?>
            
            <?php if (wc_get_loop_prop(\'is_shortcode\')) : ?>
                <?php woocommerce_output_all_notices(); ?>
            <?php endif; ?>
            
            <?php while (have_posts()) : ?>
                <?php the_post(); ?>
                <?php wc_get_template_part(\'content\', \'product\'); ?>
            <?php endwhile; ?>
            
            <?php woocommerce_product_loop_end(); ?>
            
            <?php woocommerce_output_all_notices(); ?>
            
            <div class="woocommerce-pagination">
                <?php echo paginate_links(apply_filters(\'woocommerce_pagination_args\', array(
                    \'base\'         => esc_url_raw(str_replace(999999999, \'%#%\', remove_query_arg(\'add-to-cart\', get_pagenum_link(999999999, false)))),
                    \'format\'       => \'\',
                    \'add_args\'     => false,
                    \'current\'      => max(1, get_query_var(\'paged\')),
                    \'total\'        => $GLOBALS[\'wp_query\']->max_num_pages,
                    \'prev_text\'    => \'&larr;\',
                    \'next_text\'    => \'&rarr;\',
                    \'type\'         => \'plain\',
                    \'end_size\'     => 3,
                    \'mid_size\'     => 3,
                ))); ?>
            </div>
            
        <?php else : ?>
            
            <?php woocommerce_output_all_notices(); ?>
            
            <div class="woocommerce-no-products-found">
                <h2>No products found</h2>
                <p>Sorry, no products were found matching your selection.</p>
                <a href="<?php echo esc_url(wc_get_page_permalink(\'shop\')); ?>" class="btn btn-primary">Return to Shop</a>
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

<?php get_footer(\'shop\'); ?>';

        file_put_contents($shop_template, $shop_content);
        echo '<p class="success">✓ Created professional shop template</p>';
    } else {
        echo '<p class="info">→ Shop template already exists</p>';
    }
    
    echo '</div>';
    
    // 4. Add pricing CSS to advanced stylesheet
    echo '<div class="section">';
    echo '<h2>4. Adding E-commerce Styling</h2>';
    
    $advanced_css = $template_dir . '/assets/css/advanced.css';
    if (file_exists($advanced_css)) {
        $css_content = file_get_contents($advanced_css);
        
        if (strpos($css_content, 'woocommerce-shop-header') === false) {
            $ecommerce_css = '

/* E-commerce Styling */
.plugin-pro-cta {
    text-align: center;
    padding: 1rem;
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    border-radius: var(--radius);
    margin: 1rem 0;
}

.btn-pro {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: var(--radius);
    text-decoration: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s;
}

.btn-pro:hover {
    background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(217, 119, 6, 0.3);
}

.woocommerce ul.products {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    list-style: none;
    margin: 0;
    padding: 0;
}

.woocommerce ul.products li.product {
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    transition: all 0.3s ease;
}

.woocommerce ul.products li.product:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-xl);
    border-color: var(--primary-color);
}

.woocommerce ul.products li.product .woocommerce-loop-product__link {
    text-decoration: none;
    color: inherit;
}

.woocommerce ul.products li.product h2 {
    font-size: 1.25rem;
    margin-bottom: 0.5rem;
    color: var(--gray-900);
}

.woocommerce ul.products li.product .price {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-color);
    margin: 1rem 0;
}

.woocommerce ul.products li.product .price del {
    color: var(--gray-400);
    font-size: 1rem;
    margin-right: 0.5rem;
}

.woocommerce ul.products li.product .woocommerce-loop-product__title {
    color: var(--gray-900);
    font-size: 1.25rem;
    margin-bottom: 1rem;
}

.woocommerce .woocommerce-loop-product__title:hover {
    color: var(--primary-color);
}

.single-product .summary {
    padding: 2rem;
    background: white;
    border-radius: var(--radius-lg);
    border: 1px solid var(--gray-200);
}

.single-product .product_title {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    color: var(--gray-900);
}

.single-product .price {
    font-size: 2rem;
    font-weight: 700;
    color: var(--primary-color);
    margin: 1.5rem 0;
}

.single-product .woocommerce-product-details__short-description {
    font-size: 1.125rem;
    line-height: 1.6;
    color: var(--gray-600);
    margin-bottom: 2rem;
}

.cart .btn,
.single_add_to_cart_button {
    background: var(--primary-color);
    color: white;
    padding: 1rem 2rem;
    border: none;
    border-radius: var(--radius);
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-block;
}

.cart .btn:hover,
.single_add_to_cart_button:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
}

/* Pricing page styling */
.pricing-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin: 3rem 0;
}

.pricing-card {
    background: white;
    border: 2px solid var(--gray-200);
    border-radius: var(--radius-xl);
    padding: 2.5rem 2rem;
    text-align: center;
    position: relative;
    transition: all 0.3s ease;
}

.pricing-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-xl);
}

.pricing-card.featured {
    border-color: var(--primary-color);
    transform: scale(1.05);
}

.pricing-card.featured::before {
    content: "Most Popular";
    position: absolute;
    top: -15px;
    left: 50%;
    transform: translateX(-50%);
    background: var(--primary-color);
    color: white;
    padding: 0.5rem 1.5rem;
    border-radius: var(--radius);
    font-size: 0.875rem;
    font-weight: 600;
}

.pricing-card h3 {
    font-size: 1.5rem;
    margin-bottom: 1rem;
    color: var(--gray-900);
}

.pricing-card .price {
    font-size: 3rem;
    font-weight: 700;
    color: var(--primary-color);
    margin: 1.5rem 0;
    line-height: 1;
}

.pricing-card .price small {
    font-size: 1rem;
    color: var(--gray-500);
    font-weight: 400;
}

.pricing-card ul {
    list-style: none;
    padding: 0;
    margin: 2rem 0;
    text-align: left;
}

.pricing-card li {
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--gray-100);
    position: relative;
    padding-left: 2rem;
}

.pricing-card li::before {
    content: "✓";
    position: absolute;
    left: 0;
    color: var(--success-color);
    font-weight: bold;
}

.pricing-card .btn {
    width: 100%;
    margin-top: 2rem;
    padding: 1rem;
    font-size: 1.125rem;
}

@media (max-width: 768px) {
    .pricing-grid {
        grid-template-columns: 1fr;
    }
    
    .pricing-card.featured {
        transform: none;
    }
    
    .woocommerce ul.products {
        grid-template-columns: 1fr;
    }
}';

            file_put_contents($advanced_css, $css_content . $ecommerce_css);
            echo '<p class="success">✓ Added e-commerce styling to CSS</p>';
        } else {
            echo '<p class="info">→ E-commerce styling already added</p>';
        }
    }
    
    echo '</div>';
    
    // 5. Stripe configuration guide
    echo '<div class="section">';
    echo '<h2>5. Stripe Payment Configuration</h2>';
    
    echo '<div class="steps">';
    
    echo '<div class="step">';
    echo '<h3>Get Your Stripe API Keys</h3>';
    echo '<p>1. Go to <a href="https://dashboard.stripe.com/apikeys" target="_blank">Stripe Dashboard → API Keys</a></p>';
    echo '<p>2. Copy your <strong>Publishable key</strong> and <strong>Secret key</strong></p>';
    echo '<p>3. For testing, use the "Test Data" toggle in Stripe dashboard</p>';
    echo '</div>';
    
    echo '<div class="step">';
    echo '<h3>Configure WooCommerce Stripe Settings</h3>';
    echo '<p>1. Go to <a href="' . admin_url('admin.php?page=wc-settings&tab=checkout&section=stripe') . '">WooCommerce → Settings → Payments → Stripe</a></p>';
    echo '<p>2. Enable Stripe and enter your API keys</p>';
    echo '<p>3. Enable "Test mode" for initial testing</p>';
    echo '</div>';
    
    echo '<div class="step">';
    echo '<h3>Test Your Payment Flow</h3>';
    echo '<div class="code">Test Card Numbers:
Success: 4242 4242 4242 4242
Decline: 4000 0000 0000 0002
CVV: Any 3 digits
Expiry: Any future date</div>';
    echo '</div>';
    
    echo '</div>';
    echo '</div>';
    
    // Final status and next steps
    echo '<div class="section">';
    echo '<h2>🚀 Store Finalization Complete!</h2>';
    
    echo '<div class="grid">';
    
    echo '<div class="card">';
    echo '<h3>✅ What\'s Ready:</h3>';
    echo '<ul>';
    echo '<li>Professional product catalog</li>';
    echo '<li>License management system</li>';
    echo '<li>Pro upgrade buttons on plugins</li>';
    echo '<li>Professional shop template</li>';
    echo '<li>E-commerce styling</li>';
    echo '<li>Customer account portal</li>';
    echo '</ul>';
    echo '</div>';
    
    echo '<div class="card">';
    echo '<h3>🔧 Final Steps:</h3>';
    echo '<ol>';
    echo '<li>Configure Stripe API keys</li>';
    echo '<li>Upload plugin ZIP files to products</li>';
    echo '<li>Test purchase and license delivery</li>';
    echo '<li>Set up email templates</li>';
    echo '<li>Go live with real Stripe keys</li>';
    echo '</ol>';
    echo '</div>';
    
    echo '</div>';
    
    echo '<div class="warning">';
    echo '<strong>Quick Links:</strong><br>';
    echo '• <a href="' . admin_url('admin.php?page=wc-settings&tab=checkout&section=stripe') . '">Configure Stripe</a><br>';
    echo '• <a href="' . admin_url('edit.php?post_type=product') . '">Manage Products</a><br>';
    echo '• <a href="' . admin_url('admin.php?page=lmfwc_settings') . '">License Manager Settings</a><br>';
    echo '• <a href="' . home_url('/shop/') . '">View Your Store</a>';
    echo '</div>';
    
    echo '<a href="' . home_url('/shop/') . '" class="btn" style="background: #28a745; font-size: 1.2em; padding: 15px 30px;">🛍️ View Your Store</a>';
    echo '<a href="' . home_url('/pricing/') . '" class="btn" style="background: #17a2b8; font-size: 1.2em; padding: 15px 30px;">💰 View Pricing</a>';
    echo '</div>';
    ?>
    
</body>
</html>