<?php
/**
 * Template Name: Professional Plugin Directory
 * Complete plugin showcase for Vireo Designs
 */

get_header();
?>

<div class="plugin-directory-page">
    
    <section class="page-header">
        <div class="container">
            <div class="header-content">
                <div class="breadcrumbs">
                    <a href="<?php echo home_url('/'); ?>">Home</a>
                    <span class="separator">→</span>
                    <span class="current">Plugin Directory</span>
                </div>
                
                <h1 class="page-title page-title-with-bird">Complete Plugin Directory</h1>
                <p class="page-description">
                    Browse our complete collection of WordPress plugins designed for small businesses. 
                    Each plugin offers both free and professional versions to fit your needs.
                </p>
                
                <div class="plugin-stats">
                    <div class="stat-item">
                        <span class="stat-number">8</span>
                        <span class="stat-label">Total Plugins</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">8</span>
                        <span class="stat-label">Industries Served</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">100%</span>
                        <span class="stat-label">WordPress Native</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">24/7</span>
                        <span class="stat-label">Support Available</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <section class="plugin-filters">
        <div class="container">
            <div class="filter-controls">
                <div class="filter-group">
                    <label for="industry-filter">Filter by Industry:</label>
                    <select id="industry-filter" class="filter-select">
                        <option value="">All Industries</option>
                        <option value="property-management">Property Management</option>
                        <option value="sports-leagues">Sports Leagues</option>
                        <option value="equipment-rental">Equipment Rental</option>
                        <option value="marina-rv">Marina & RV Resorts</option>
                        <option value="self-storage">Self Storage</option>
                        <option value="nonprofits">Nonprofits</option>
                    </select>
                </div>
                
                <div class="search-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="plugin-search" class="search-input" placeholder="Search plugins...">
                </div>
                
                <button class="filter-reset" onclick="resetFilters()">
                    <i class="fas fa-undo"></i>
                    Reset
                </button>
            </div>
        </div>
    </section>

    <section class="plugins-showcase">
        <div class="container">
            
            <!-- Our Live Plugins -->
            <div class="plugin-category">
                <div class="category-header">
                    <h2>🌟 Currently Available</h2>
                    <p>Professional WordPress plugins developed by Vireo Designs, ready for production use</p>
                </div>
                <div class="section-divider"></div>
                
                <div class="plugins-grid">
                    
                    <!-- Vireo Property Management Plugin -->
                    <?php 
                    $property_plugin_path = WP_PLUGIN_DIR . '/vireo-property-management/vireo-property-management.php';
                    $property_exists = file_exists($property_plugin_path);
                    if ($property_exists) {
                        $property_data = get_plugin_data($property_plugin_path);
                    }
                    ?>
                    
                    <div class="plugin-card featured-card" data-industry="property-management">
                        <div class="plugin-header">
                            <div class="plugin-icon">
                                <i class="fas fa-building"></i>
                            </div>
                            <div class="plugin-meta">
                                <h3 class="plugin-title">Vireo Property Management</h3>
                                <div class="plugin-badges">
                                    <span class="badge badge-success">Active Development</span>
                                    <?php if ($property_exists && isset($property_data['Version'])) : ?>
                                        <span class="badge badge-gray">v<?php echo esc_html($property_data['Version']); ?></span>
                                    <?php endif; ?>
                                    <span class="badge badge-info">Free + Pro</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="plugin-description">
                            <p>Complete property management solution for small landlords. Manage properties, tenants, leases, and maintenance requests. Alternative to expensive Yardi/AppFolio systems.</p>
                        </div>
                        
                        <div class="plugin-stats-row">
                            <div class="plugin-stat">
                                <i class="fas fa-code-branch"></i>
                                <span>In Development</span>
                            </div>
                            <div class="plugin-stat">
                                <i class="fas fa-home"></i>
                                <span>Property Focus</span>
                            </div>
                            <div class="plugin-stat">
                                <i class="fas fa-database"></i>
                                <span>Full Database</span>
                            </div>
                        </div>
                        
                        <div class="plugin-actions">
                            <a href="<?php echo home_url('/plugin-property-management/'); ?>" class="btn btn-secondary">Learn More</a>
                            <a href="<?php echo home_url('/shop/'); ?>" class="btn btn-primary">Pre-Order Pro</a>
                            <?php if ($property_exists) : ?>
                                <span class="plugin-status-badge">✅ Installed</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Vireo Sports League Management Plugin -->
                    <?php 
                    $sports_plugin_path = WP_PLUGIN_DIR . '/vireo-sports-league/vireo-sports-league.php';
                    $sports_exists = file_exists($sports_plugin_path);
                    if ($sports_exists) {
                        $sports_data = get_plugin_data($sports_plugin_path);
                    }
                    ?>
                    
                    <div class="plugin-card featured-card" data-industry="sports-leagues">
                        <div class="plugin-header">
                            <div class="plugin-icon">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <div class="plugin-meta">
                                <h3 class="plugin-title">Vireo Sports League Manager</h3>
                                <div class="plugin-badges">
                                    <span class="badge badge-success">Active Development</span>
                                    <?php if ($sports_exists && isset($sports_data['Version'])) : ?>
                                        <span class="badge badge-gray">v<?php echo esc_html($sports_data['Version']); ?></span>
                                    <?php endif; ?>
                                    <span class="badge badge-info">Free + Pro</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="plugin-description">
                            <p>Comprehensive sports league management with team rosters, scheduling, statistics tracking, and tournament brackets. Perfect for local leagues and recreational sports.</p>
                        </div>
                        
                        <div class="plugin-stats-row">
                            <div class="plugin-stat">
                                <i class="fas fa-code-branch"></i>
                                <span>In Development</span>
                            </div>
                            <div class="plugin-stat">
                                <i class="fas fa-users"></i>
                                <span>Team Management</span>
                            </div>
                            <div class="plugin-stat">
                                <i class="fas fa-chart-bar"></i>
                                <span>Statistics</span>
                            </div>
                        </div>
                        
                        <div class="plugin-actions">
                            <a href="<?php echo home_url('/industries/sports-leagues/'); ?>" class="btn btn-secondary">Learn More</a>
                            <a href="<?php echo home_url('/shop/'); ?>" class="btn btn-primary">Pre-Order Pro</a>
                            <?php if ($sports_exists) : ?>
                                <span class="plugin-status-badge">✅ Installed</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                </div>
            </div>
            
            <!-- Ecosystem Plugins -->
            <div class="plugin-category">
                <div class="category-header">
                    <h2>🏢 Ecosystem Plugins</h2>
                    <p>Comprehensive solutions with core functionality plus expandable add-ons</p>
                </div>
                <div class="section-divider"></div>
                
                <div class="plugins-grid">
                    
                    <!-- Property Management Ecosystem -->
                    <div class="plugin-card ecosystem-card" data-industry="property-management">
                        <div class="plugin-header">
                            <div class="plugin-icon">
                                <i class="fas fa-building"></i>
                            </div>
                            <div class="plugin-meta">
                                <h3 class="plugin-title">Property Management</h3>
                                <div class="plugin-badges">
                                    <span class="badge badge-success">Available</span>
                                    <span class="badge badge-info">Ecosystem</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="plugin-description">
                            <p>Complete property management solution for small landlords. Alternative to expensive Yardi/AppFolio systems.</p>
                        </div>
                        
                        <div class="plugin-pricing">
                            <div class="pricing-tiers">
                                <div class="pricing-tier">
                                    <span class="tier-name">Core</span>
                                    <span class="tier-price">Free</span>
                                </div>
                                <div class="pricing-tier featured">
                                    <span class="tier-name">Pro</span>
                                    <span class="tier-price">$99/year</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="plugin-actions">
                            <a href="<?php echo home_url('/industries/property-management/'); ?>" class="btn btn-secondary">Learn More</a>
                            <?php 
                            // Get WooCommerce product for Property Management Pro
                            $product_query = new WP_Query(array(
                                'post_type' => 'product',
                                'meta_query' => array(
                                    array(
                                        'key' => '_sku',
                                        'value' => 'property-management-pro',
                                        'compare' => 'LIKE'
                                    )
                                ),
                                'posts_per_page' => 1
                            ));
                            
                            if ($product_query->have_posts()) {
                                $product_query->the_post();
                                $product = wc_get_product(get_the_ID());
                                $product_url = get_permalink();
                                wp_reset_postdata();
                                echo '<a href="' . esc_url($product_url) . '" class="btn btn-primary">Get Pro - ' . $product->get_price_html() . '</a>';
                            } else {
                                echo '<a href="' . esc_url(home_url('/shop/')) . '" class="btn btn-primary">Get Pro</a>';
                            }
                            ?>
                        </div>
                    </div>
                    
                    <!-- Equipment Rental Ecosystem -->
                    <div class="plugin-card ecosystem-card" data-industry="equipment-rental">
                        <div class="plugin-header">
                            <div class="plugin-icon">
                                <i class="fas fa-truck"></i>
                            </div>
                            <div class="plugin-meta">
                                <h3 class="plugin-title">Equipment Rental</h3>
                                <div class="plugin-badges">
                                    <span class="badge badge-warning">Coming Q2 2025</span>
                                    <span class="badge badge-info">Ecosystem</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="plugin-description">
                            <p>Complete rental management system for equipment businesses. Track inventory, manage bookings, process payments.</p>
                        </div>
                        
                        <div class="plugin-pricing">
                            <div class="pricing-tiers">
                                <div class="pricing-tier">
                                    <span class="tier-name">Core</span>
                                    <span class="tier-price">Free</span>
                                </div>
                                <div class="pricing-tier featured">
                                    <span class="tier-name">Pro</span>
                                    <span class="tier-price">$89/year</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="plugin-actions">
                            <a href="<?php echo home_url('/industries/equipment-rental/'); ?>" class="btn btn-secondary">Learn More</a>
                            <button class="btn btn-outline" disabled>Coming Soon</button>
                        </div>
                    </div>
                    
                    <!-- Marina & RV Resort Ecosystem -->
                    <div class="plugin-card ecosystem-card" data-industry="marina-rv">
                        <div class="plugin-header">
                            <div class="plugin-icon">
                                <i class="fas fa-anchor"></i>
                            </div>
                            <div class="plugin-meta">
                                <h3 class="plugin-title">Marina & RV Resort</h3>
                                <div class="plugin-badges">
                                    <span class="badge badge-warning">Coming Q3 2025</span>
                                    <span class="badge badge-info">Ecosystem</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="plugin-description">
                            <p>Specialized management for marinas and RV parks. Handle slip/site rentals and seasonal bookings.</p>
                        </div>
                        
                        <div class="plugin-pricing">
                            <div class="pricing-tiers">
                                <div class="pricing-tier">
                                    <span class="tier-name">Core</span>
                                    <span class="tier-price">Free</span>
                                </div>
                                <div class="pricing-tier featured">
                                    <span class="tier-name">Pro</span>
                                    <span class="tier-price">$129/year</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="plugin-actions">
                            <a href="<?php echo home_url('/industries/marina-rv-resorts/'); ?>" class="btn btn-secondary">Learn More</a>
                            <button class="btn btn-outline" disabled>Coming Soon</button>
                        </div>
                    </div>
                    
                </div>
            </div>
            
            <!-- Standalone Plugins -->
            <div class="plugin-category">
                <div class="category-header">
                    <h2>⚡ Standalone Plugins</h2>
                    <p>Focused solutions for specific business needs</p>
                </div>
                <div class="section-divider"></div>
                
                <div class="plugins-grid">
                    
                    <!-- Sports League Management -->
                    <div class="plugin-card standalone-card" data-industry="sports-leagues">
                        <div class="plugin-header">
                            <div class="plugin-icon">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <div class="plugin-meta">
                                <h3 class="plugin-title">Sports League Manager</h3>
                                <div class="plugin-badges">
                                    <span class="badge badge-success">Available</span>
                                    <span class="badge badge-gray">Standalone</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="plugin-description">
                            <p>Comprehensive sports league management for local leagues, tournaments, and recreational sports organizations.</p>
                        </div>
                        
                        <div class="plugin-pricing">
                            <div class="pricing-tiers">
                                <div class="pricing-tier">
                                    <span class="tier-name">Free</span>
                                    <span class="tier-price">$0</span>
                                </div>
                                <div class="pricing-tier featured">
                                    <span class="tier-name">Pro</span>
                                    <span class="tier-price">$79/year</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="plugin-actions">
                            <a href="<?php echo home_url('/industries/sports-leagues/'); ?>" class="btn btn-secondary">Learn More</a>
                            <?php 
                            // Get WooCommerce product for Sports League Pro
                            $sports_product_query = new WP_Query(array(
                                'post_type' => 'product',
                                'meta_query' => array(
                                    array(
                                        'key' => '_sku',
                                        'value' => 'sports-league-pro',
                                        'compare' => 'LIKE'
                                    )
                                ),
                                'posts_per_page' => 1
                            ));
                            
                            if ($sports_product_query->have_posts()) {
                                $sports_product_query->the_post();
                                $sports_product = wc_get_product(get_the_ID());
                                $sports_product_url = get_permalink();
                                wp_reset_postdata();
                                echo '<a href="' . esc_url($sports_product_url) . '" class="btn btn-primary">Get Pro - ' . $sports_product->get_price_html() . '</a>';
                            } else {
                                echo '<a href="' . esc_url(home_url('/shop/')) . '" class="btn btn-primary">Get Pro</a>';
                            }
                            ?>
                        </div>
                    </div>
                    
                    <!-- Self Storage -->
                    <div class="plugin-card standalone-card" data-industry="self-storage">
                        <div class="plugin-header">
                            <div class="plugin-icon">
                                <i class="fas fa-warehouse"></i>
                            </div>
                            <div class="plugin-meta">
                                <h3 class="plugin-title">Self Storage Manager</h3>
                                <div class="plugin-badges">
                                    <span class="badge badge-warning">Coming Q4 2025</span>
                                    <span class="badge badge-gray">Standalone</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="plugin-description">
                            <p>Storage facility management with unit tracking, customer management, and automated billing systems.</p>
                        </div>
                        
                        <div class="plugin-pricing">
                            <div class="pricing-tiers">
                                <div class="pricing-tier">
                                    <span class="tier-name">Basic</span>
                                    <span class="tier-price">$89/year</span>
                                </div>
                                <div class="pricing-tier featured">
                                    <span class="tier-name">Pro</span>
                                    <span class="tier-price">$149/year</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="plugin-actions">
                            <a href="<?php echo home_url('/industries/self-storage/'); ?>" class="btn btn-secondary">Learn More</a>
                            <button class="btn btn-outline" disabled>Coming Soon</button>
                        </div>
                    </div>
                    
                    <!-- Nonprofit Management -->
                    <div class="plugin-card standalone-card" data-industry="nonprofits">
                        <div class="plugin-header">
                            <div class="plugin-icon">
                                <i class="fas fa-heart"></i>
                            </div>
                            <div class="plugin-meta">
                                <h3 class="plugin-title">Nonprofit Manager</h3>
                                <div class="plugin-badges">
                                    <span class="badge badge-warning">Coming Q4 2025</span>
                                    <span class="badge badge-gray">Standalone</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="plugin-description">
                            <p>Comprehensive nonprofit management with donor tracking, volunteer coordination, and event management.</p>
                        </div>
                        
                        <div class="plugin-pricing">
                            <div class="pricing-tiers">
                                <div class="pricing-tier">
                                    <span class="tier-name">Free</span>
                                    <span class="tier-price">$0</span>
                                </div>
                                <div class="pricing-tier featured">
                                    <span class="tier-name">Pro</span>
                                    <span class="tier-price">$99/year</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="plugin-actions">
                            <a href="<?php echo home_url('/industries/nonprofits/'); ?>" class="btn btn-secondary">Learn More</a>
                            <button class="btn btn-outline" disabled>Coming Soon</button>
                        </div>
                    </div>
                    
                </div>
            </div>
            
            <?php
            // Get WooCommerce products that are our plugins
            if (class_exists('WooCommerce')) {
                // Look for products with plugin-related keywords
                $wc_products = new WP_Query(array(
                    'post_type' => 'product',
                    'posts_per_page' => 6,
                    'post_status' => 'publish',
                    's' => 'property management OR sports league OR vireo',
                    'meta_query' => array(
                        array(
                            'key' => '_stock_status',
                            'value' => 'instock',
                            'compare' => '='
                        )
                    )
                ));
                
                // If no search results, get all products
                if (!$wc_products->have_posts()) {
                    wp_reset_postdata();
                    $wc_products = new WP_Query(array(
                        'post_type' => 'product',
                        'posts_per_page' => 6,
                        'post_status' => 'publish',
                        'meta_query' => array(
                            array(
                                'key' => '_stock_status',
                                'value' => 'instock',
                                'compare' => '='
                            )
                        )
                    ));
                }
                
                if ($wc_products->have_posts()) : ?>
                <!-- Available Products -->
                <div class="plugin-category">
                    <div class="category-header">
                        <h2>🛒 Professional Licenses</h2>
                        <p>Pro versions and premium licenses for our WordPress plugins</p>
                    </div>
                    <div class="section-divider"></div>
                    
                    <div class="plugins-grid">
                        <?php while ($wc_products->have_posts()) : $wc_products->the_post(); 
                            $product = wc_get_product(get_the_ID());
                            if (!$product) continue;
                        ?>
                        
                        <div class="plugin-card product-card">
                            <div class="plugin-header">
                                <div class="plugin-icon">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <?php the_post_thumbnail('thumbnail', array('class' => 'plugin-thumb')); ?>
                                    <?php else : ?>
                                        <i class="fas fa-shopping-cart"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="plugin-meta">
                                    <h3 class="plugin-title"><?php the_title(); ?></h3>
                                    <div class="plugin-badges">
                                        <span class="badge badge-success">Available</span>
                                        <?php if ($product->is_on_sale()) : ?>
                                            <span class="badge badge-warning">Sale</span>
                                        <?php endif; ?>
                                        <span class="badge badge-premium">Pro License</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="plugin-description">
                                <?php echo wp_trim_words(get_the_excerpt() ?: $product->get_short_description(), 20); ?>
                            </div>
                            
                            <div class="plugin-pricing">
                                <div class="wc-price-display">
                                    <span class="price"><?php echo $product->get_price_html(); ?></span>
                                    <?php if ($product->is_on_sale()) : ?>
                                        <span class="sale-badge">SAVE <?php echo round((($product->get_regular_price() - $product->get_sale_price()) / $product->get_regular_price()) * 100); ?>%</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="plugin-actions">
                                <a href="<?php the_permalink(); ?>" class="btn btn-secondary">View Details</a>
                                <a href="<?php echo esc_url('?add-to-cart=' . get_the_ID()); ?>" class="btn btn-primary">
                                    <i class="fas fa-shopping-cart"></i> Purchase License
                                </a>
                            </div>
                        </div>
                        
                        <?php endwhile; ?>
                    </div>
                </div>
                <?php endif; wp_reset_postdata(); ?>
            <?php } ?>
            
        </div>
    </section>
    
</div>

<style>
.plugin-directory-page {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    min-height: 100vh;
}

.page-header {
    background: var(--white);
    padding: var(--space-12) 0;
    border-bottom: 1px solid var(--gray-200);
}

.breadcrumbs {
    font-size: var(--text-sm);
    color: var(--gray-600);
    margin-bottom: var(--space-4);
}

.breadcrumbs a {
    color: var(--primary-color);
    text-decoration: none;
}

.separator {
    margin: 0 var(--space-2);
    color: var(--gray-400);
}

.page-title {
    font-size: var(--text-4xl);
    font-weight: 800;
    color: var(--gray-900);
    margin-bottom: var(--space-4);
    background: linear-gradient(135deg, var(--gray-900) 0%, var(--primary-color) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.page-description {
    font-size: var(--text-xl);
    color: var(--gray-600);
    max-width: 600px;
    line-height: 1.6;
    margin-bottom: var(--space-8);
}

.plugin-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: var(--space-6);
}

.stat-item {
    text-align: center;
    padding: var(--space-4);
    background: var(--gray-50);
    border-radius: var(--radius-xl);
    border: 1px solid var(--gray-200);
}

.stat-number {
    display: block;
    font-size: var(--text-3xl);
    font-weight: 800;
    color: var(--primary-color);
    margin-bottom: var(--space-2);
}

.stat-label {
    font-size: var(--text-sm);
    color: var(--gray-600);
    font-weight: 500;
}

.plugin-filters {
    background: var(--white);
    padding: var(--space-8) 0;
    border-bottom: 1px solid var(--gray-200);
}

.filter-controls {
    display: flex;
    gap: var(--space-6);
    align-items: end;
    flex-wrap: wrap;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: var(--space-2);
}

.filter-group label {
    font-size: var(--text-sm);
    font-weight: 500;
    color: var(--gray-700);
}

.filter-select {
    padding: var(--space-2) var(--space-3);
    border: 1px solid var(--gray-300);
    border-radius: var(--radius);
    font-size: var(--text-sm);
    min-width: 150px;
}

.search-container {
    position: relative;
    display: flex;
    align-items: center;
}

.search-icon {
    position: absolute;
    left: var(--space-3);
    color: var(--gray-400);
    z-index: 1;
}

.search-input {
    padding: var(--space-2) var(--space-3) var(--space-2) var(--space-10);
    border: 1px solid var(--gray-300);
    border-radius: var(--radius);
    font-size: var(--text-sm);
    min-width: 200px;
}

.filter-reset {
    background: var(--gray-100);
    border: 1px solid var(--gray-300);
    color: var(--gray-700);
    padding: var(--space-2) var(--space-4);
    border-radius: var(--radius);
    cursor: pointer;
    transition: all var(--transition-fast);
    display: flex;
    align-items: center;
    gap: var(--space-2);
}

.filter-reset:hover {
    background: var(--gray-200);
}

.plugins-showcase {
    padding: var(--space-12) 0;
}

.plugin-category {
    margin-bottom: var(--space-16);
}

.category-header {
    text-align: center;
    margin-bottom: var(--space-8);
}

.category-header h2 {
    font-size: var(--text-3xl);
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: var(--space-3);
}

.category-header p {
    font-size: var(--text-lg);
    color: var(--gray-600);
    max-width: 600px;
    margin: 0 auto;
}

.section-divider {
    height: 1px;
    background: linear-gradient(90deg, transparent, var(--gray-200), transparent);
    margin-bottom: var(--space-8);
}

.plugins-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: var(--space-8);
}

.plugin-card {
    background: var(--white);
    border-radius: var(--radius-2xl);
    padding: var(--space-6);
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--gray-200);
    transition: all var(--transition-fast);
    position: relative;
    overflow: hidden;
}

.plugin-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-xl);
}

.ecosystem-card {
    border-left: 4px solid var(--primary-color);
}

.standalone-card {
    border-left: 4px solid var(--secondary-color);
}

.featured-card {
    border-left: 4px solid var(--accent-color);
    position: relative;
}

.featured-card::before {
    content: "⭐";
    position: absolute;
    top: 1rem;
    right: 1rem;
    font-size: 1.2rem;
}

.plugin-thumb {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: inherit;
}

.plugin-stats-row {
    display: flex;
    gap: var(--space-4);
    margin-bottom: var(--space-4);
    padding: var(--space-3);
    background: var(--gray-50);
    border-radius: var(--radius);
}

.plugin-stat {
    display: flex;
    align-items: center;
    gap: var(--space-2);
    font-size: var(--text-sm);
    color: var(--gray-600);
}

.plugin-stat i {
    color: var(--primary-color);
}

.product-card {
    border-left: 4px solid var(--success-color);
}

.wc-price-display {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: var(--space-3);
    background: var(--gray-50);
    border-radius: var(--radius);
    margin-bottom: var(--space-4);
}

.wc-price-display .price {
    font-size: var(--text-lg);
    font-weight: 700;
    color: var(--primary-color);
}

.sale-badge {
    background: var(--warning-color);
    color: var(--white);
    padding: var(--space-1) var(--space-2);
    border-radius: var(--radius);
    font-size: var(--text-xs);
    font-weight: 600;
}

.section-divider {
    width: 60px;
    height: 3px;
    background: var(--primary-gradient);
    border-radius: var(--radius);
    margin: 0 auto var(--space-8);
}

.plugin-status-badge {
    background: var(--success-color);
    color: var(--white);
    padding: var(--space-1) var(--space-2);
    border-radius: var(--radius);
    font-size: var(--text-xs);
    font-weight: 600;
    margin-left: var(--space-2);
}

.badge-premium {
    background: var(--accent-color);
    color: var(--white);
}

.plugin-actions {
    display: flex;
    gap: var(--space-2);
    align-items: center;
    flex-wrap: wrap;
}

.plugin-header {
    display: flex;
    gap: var(--space-4);
    margin-bottom: var(--space-4);
}

.plugin-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
    border-radius: var(--radius-xl);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    font-size: var(--text-xl);
    flex-shrink: 0;
}

/* Industry-specific icon colors */
[data-industry="property-management"] .plugin-icon {
    background: linear-gradient(135deg, #e0f2fe, #bae6fd);
    color: #0369a1;
}

[data-industry="sports-leagues"] .plugin-icon {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    color: #d97706;
}

[data-industry="equipment-rental"] .plugin-icon {
    background: linear-gradient(135deg, #ede9fe, #ddd6fe);
    color: #7c3aed;
}

[data-industry="marina-rv"] .plugin-icon {
    background: linear-gradient(135deg, #e0f2fe, #bae6fd);
    color: #0891b2;
}

[data-industry="self-storage"] .plugin-icon {
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    color: #dc2626;
}

[data-industry="nonprofits"] .plugin-icon {
    background: linear-gradient(135deg, #fce7f3, #fbcfe8);
    color: #ec4899;
}

.plugin-meta {
    flex: 1;
}

.plugin-title {
    font-size: var(--text-xl);
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: var(--space-2);
}

.plugin-badges {
    display: flex;
    gap: var(--space-2);
    flex-wrap: wrap;
}

.plugin-description {
    margin-bottom: var(--space-4);
    color: var(--gray-600);
    line-height: 1.6;
}

.plugin-pricing {
    margin-bottom: var(--space-6);
}

.pricing-tiers {
    display: flex;
    gap: var(--space-3);
}

.pricing-tier {
    flex: 1;
    padding: var(--space-3);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius);
    text-align: center;
    background: var(--gray-50);
}

.pricing-tier.featured {
    background: linear-gradient(135deg, #0891b2, #7c3aed);
    color: var(--white);
    border-color: #0891b2;
}

.tier-name {
    display: block;
    font-size: var(--text-sm);
    font-weight: 500;
    margin-bottom: var(--space-1);
}

.tier-price {
    display: block;
    font-size: var(--text-lg);
    font-weight: 700;
}

.plugin-actions {
    display: flex;
    gap: var(--space-3);
}

@media (max-width: 768px) {
    .plugins-grid {
        grid-template-columns: 1fr;
    }
    
    .filter-controls {
        flex-direction: column;
        align-items: stretch;
    }
    
    .page-title {
        font-size: var(--text-3xl);
    }
    
    .plugin-actions {
        flex-direction: column;
    }
}
</style>

<script>
function resetFilters() {
    document.getElementById('industry-filter').value = '';
    document.getElementById('plugin-search').value = '';
    
    // Show all plugin cards
    document.querySelectorAll('.plugin-card').forEach(card => {
        card.style.display = 'block';
    });
}

// Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const industryFilter = document.getElementById('industry-filter');
    const searchInput = document.getElementById('plugin-search');
    
    function filterPlugins() {
        const industry = industryFilter.value;
        const search = searchInput.value.toLowerCase();
        
        document.querySelectorAll('.plugin-card').forEach(card => {
            let visible = true;
            
            // Industry filter
            if (industry && card.getAttribute('data-industry') !== industry) {
                visible = false;
            }
            
            // Search filter
            if (search) {
                const title = card.querySelector('.plugin-title').textContent.toLowerCase();
                const description = card.querySelector('.plugin-description').textContent.toLowerCase();
                if (!title.includes(search) && !description.includes(search)) {
                    visible = false;
                }
            }
            
            card.style.display = visible ? 'block' : 'none';
        });
    }
    
    industryFilter.addEventListener('change', filterPlugins);
    searchInput.addEventListener('input', filterPlugins);
});
</script>

<?php get_footer(); ?>