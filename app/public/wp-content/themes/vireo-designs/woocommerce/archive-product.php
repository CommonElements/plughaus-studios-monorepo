<?php
/**
 * Professional Shop Page Template for Vireo Designs
 * Industry-specific WordPress plugin marketplace
 */

get_header('shop'); ?>

<div class="professional-shop-page">
    
    <!-- Professional Shop Header -->
    <section class="shop-hero">
        <div class="hero-background">
            <div class="hero-pattern"></div>
        </div>
        <div class="container">
            <div class="shop-hero-content">
                <h1 class="shop-title">Professional WordPress Plugins</h1>
                <p class="shop-description">
                    Industry-specific business management solutions built for WordPress. 
                    Professional alternatives to expensive SaaS platforms at 90% cost savings.
                </p>
                <div class="shop-stats">
                    <div class="stat-item">
                        <span class="stat-number"><?php echo wp_count_posts('product')->publish; ?></span>
                        <span class="stat-label">Industry Solutions</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">100%</span>
                        <span class="stat-label">WordPress Native</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">90%</span>
                        <span class="stat-label">Cost Savings</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Industry Categories -->
    <section class="product-categories-section">
        <div class="container">
            <h2 class="section-title">Shop by Industry</h2>
            <div class="categories-grid">
                <div class="category-card">
                    <div class="category-icon">🏢</div>
                    <h3>Property Management</h3>
                    <p>Landlords & Property Managers</p>
                </div>
                <div class="category-card">
                    <div class="category-icon">🏆</div>
                    <h3>Sports & Recreation</h3>
                    <p>Leagues & Tournament Organizers</p>
                </div>
                <div class="category-card">
                    <div class="category-icon">🔧</div>
                    <h3>Equipment Rental</h3>
                    <p>Tool & Equipment Businesses</p>
                </div>
                <div class="category-card">
                    <div class="category-icon">🚗</div>
                    <h3>Automotive</h3>
                    <p>Auto Shops & Small Dealers</p>
                </div>
                <div class="category-card">
                    <div class="category-icon">💪</div>
                    <h3>Fitness & Wellness</h3>
                    <p>Gyms & Fitness Studios</p>
                </div>
                <div class="category-card">
                    <div class="category-icon">📸</div>
                    <h3>Creative Services</h3>
                    <p>Photography & Design Studios</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Professional Products Section -->
    <section class="products-section">
        <div class="container">
            
            <?php if ( woocommerce_product_loop() ) : ?>
                
                <div class="professional-products-grid">
                    <?php while ( have_posts() ) : the_post(); ?>
                        <?php
                        global $product;
                        $product_id = get_the_ID();
                        $status = get_post_meta($product_id, '_vireo_product_status', true) ?: 'available';
                        $category = get_post_meta($product_id, '_vireo_product_category', true) ?: 'Business Management';
                        ?>
                        
                        <div class="professional-product-card <?php echo esc_attr($status); ?>">
                            
                            <!-- Product Icon -->
                            <div class="product-icon">
                                <?php
                                // Smart icons based on product title
                                $icon = '🔧'; // default
                                $title = get_the_title();
                                if (strpos($title, 'Property') !== false) $icon = '🏢';
                                if (strpos($title, 'Sports') !== false) $icon = '🏆';
                                if (strpos($title, 'Equipment') !== false || strpos($title, 'EquipRent') !== false) $icon = '🔧';
                                if (strpos($title, 'Dealer') !== false) $icon = '🚗';
                                if (strpos($title, 'Gym') !== false) $icon = '💪';
                                if (strpos($title, 'Studio') !== false) $icon = '📸';
                                ?>
                                <span style="font-size: 3rem;"><?php echo $icon; ?></span>
                            </div>
                            
                            <!-- Product Header -->
                            <div class="product-header">
                                <h3 class="product-title">
                                    <a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                <div class="product-badges">
                                    <?php if ($status === 'available') : ?>
                                        <span class="badge badge-success">Available Now</span>
                                    <?php elseif ($status === 'coming-soon') : ?>
                                        <span class="badge badge-warning">Coming Q2 2025</span>
                                    <?php else : ?>
                                        <span class="badge badge-info">In Development</span>
                                    <?php endif; ?>
                                    <span class="badge badge-gray"><?php echo esc_html($category); ?></span>
                                </div>
                            </div>
                            
                            <!-- Product Content -->
                            <div class="product-content">
                                <p class="product-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
                            </div>
                            
                            <!-- Product Pricing -->
                            <div class="product-pricing">
                                <?php if ($status === 'available') : ?>
                                    <div class="price-display"><?php echo $product->get_price_html(); ?></div>
                                <?php else : ?>
                                    <div class="price-display">Pricing TBA</div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Product Actions -->
                            <div class="product-actions">
                                <a href="<?php echo get_permalink(); ?>" class="btn btn-secondary">Learn More</a>
                                <?php if ($status === 'available') : ?>
                                    <a href="?add-to-cart=<?php echo $product_id; ?>" class="btn btn-primary">Purchase License</a>
                                <?php else : ?>
                                    <button class="btn btn-outline" disabled>Coming Soon</button>
                                <?php endif; ?>
                            </div>
                            
                        </div>
                        
                    <?php endwhile; ?>
                </div>

            <?php else : ?>
                
                <div class="no-products-found">
                    <h2>No products found</h2>
                    <p>It looks like we don't have any products available right now.</p>
                    <a href="<?php echo home_url('/'); ?>" class="btn btn-primary">Return Home</a>
                </div>

            <?php endif; ?>
            
        </div>
    </section>

    <!-- Value Proposition -->
    <section class="shop-value-props">
        <div class="container">
            <h2 class="section-title">Why Choose Vireo Designs?</h2>
            <div class="value-grid">
                <div class="value-item">
                    <div class="value-icon">💰</div>
                    <h3>90% Cost Savings</h3>
                    <p>Professional features at a fraction of enterprise SaaS pricing. No monthly fees.</p>
                </div>
                <div class="value-item">
                    <div class="value-icon">🎯</div>
                    <h3>Industry Specific</h3>
                    <p>Built for your business type with workflows and features that actually make sense.</p>
                </div>
                <div class="value-item">
                    <div class="value-icon">🔒</div>
                    <h3>Your Data, Your Control</h3>
                    <p>Self-hosted on your WordPress site. Complete ownership and privacy.</p>
                </div>
                <div class="value-item">
                    <div class="value-icon">⚡</div>
                    <h3>WordPress Native</h3>
                    <p>Seamlessly integrates with your existing website and WordPress ecosystem.</p>
                </div>
            </div>
        </div>
    </section>

</div>

<style>
/* Professional Shop Page Styles */
.professional-shop-page {
    background: #f8fafc;
}

.shop-hero {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    padding: 6rem 0;
    color: white;
    position: relative;
    overflow: hidden;
}

.hero-background {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 1;
}

.hero-pattern {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23334155' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}

.shop-hero-content {
    position: relative;
    z-index: 2;
    text-align: center;
    max-width: 800px;
    margin: 0 auto;
}

.shop-title {
    font-size: 3.5rem;
    font-weight: 800;
    margin-bottom: 1.5rem;
    background: linear-gradient(135deg, #ffffff 0%, #94a3b8 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.shop-description {
    font-size: 1.5rem;
    margin-bottom: 3rem;
    opacity: 0.9;
    line-height: 1.6;
}

.shop-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2rem;
    max-width: 600px;
    margin: 0 auto;
}

.stat-item {
    text-align: center;
    padding: 1.5rem;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    backdrop-filter: blur(10px);
}

.stat-number {
    display: block;
    font-size: 2rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 0.875rem;
    opacity: 0.8;
}

.product-categories-section {
    padding: 6rem 0;
    background: white;
}

.section-title {
    text-align: center;
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 3rem;
    color: #1e293b;
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
    margin-top: 3rem;
}

.category-card {
    text-align: center;
    padding: 2rem 1.5rem;
    border: 2px solid #e2e8f0;
    border-radius: 16px;
    transition: all 0.3s ease;
    background: white;
}

.category-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    border-color: #0ea5e9;
}

.category-icon {
    font-size: 2.5rem;
    margin-bottom: 1rem;
}

.category-card h3 {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: #1e293b;
}

.category-card p {
    color: #64748b;
    margin-bottom: 1.5rem;
    font-size: 0.875rem;
}

.products-section {
    padding: 6rem 0;
    background: #f8fafc;
}

.professional-products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
    margin-top: 3rem;
}

.professional-product-card {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    border: 2px solid #e2e8f0;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    position: relative;
}

.professional-product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    border-color: #0ea5e9;
}

.professional-product-card.available {
    border-left: 4px solid #10b981;
}

.professional-product-card.coming-soon {
    border-left: 4px solid #f59e0b;
}

.product-icon {
    text-align: center;
    margin-bottom: 1.5rem;
}

.product-header {
    margin-bottom: 1.5rem;
}

.product-title {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.product-title a {
    color: #1e293b;
    text-decoration: none;
}

.product-badges {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.badge {
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-success {
    background: #10b981;
    color: white;
}

.badge-warning {
    background: #f59e0b;
    color: white;
}

.badge-info {
    background: #0ea5e9;
    color: white;
}

.badge-gray {
    background: #6b7280;
    color: white;
}

.product-content {
    flex-grow: 1;
    margin-bottom: 1.5rem;
}

.product-excerpt {
    color: #64748b;
    line-height: 1.6;
}

.product-pricing {
    margin-bottom: 1.5rem;
}

.price-display {
    font-size: 1.5rem;
    font-weight: 700;
    color: #0ea5e9;
}

.product-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.shop-value-props {
    padding: 6rem 0;
    background: white;
}

.value-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    margin-top: 3rem;
}

.value-item {
    text-align: center;
    padding: 2rem;
}

.value-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.value-item h3 {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 1rem;
    color: #1e293b;
}

.value-item p {
    color: #64748b;
    line-height: 1.6;
}

.no-products-found {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 16px;
    margin: 3rem 0;
}

.no-products-found h2 {
    font-size: 2rem;
    margin-bottom: 1rem;
    color: #1e293b;
}

.no-products-found p {
    color: #64748b;
    margin-bottom: 2rem;
}

/* Mobile responsive */
@media (max-width: 768px) {
    .shop-title {
        font-size: 2.5rem;
    }
    
    .shop-stats {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .categories-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .professional-products-grid {
        grid-template-columns: 1fr;
    }
    
    .value-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .categories-grid {
        grid-template-columns: 1fr;
    }
    
    .product-actions {
        grid-template-columns: 1fr;
    }
}
</style>

<?php get_footer('shop'); ?>