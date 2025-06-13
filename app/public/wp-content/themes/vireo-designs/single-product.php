<?php
/**
 * Single Product Template
 * Enhanced product page for Vireo Designs plugins
 */

get_header('shop');

while (have_posts()) :
    the_post();
    global $product;
    
    // Get product metadata
    $product_id = get_the_ID();
    $status = get_post_meta($product_id, '_vireo_product_status', true) ?: 'available';
    $industry = get_post_meta($product_id, '_vireo_product_industry', true) ?: 'general';
    $features = get_post_meta($product_id, '_vireo_product_features', true) ?: array();
    $pro_features = get_post_meta($product_id, '_vireo_pro_features', true) ?: array();
    
    // Determine plugin type from title
    $title = get_the_title();
    $is_bundle = strpos($title, 'Bundle') !== false;
    $is_pro = strpos($title, 'Pro') !== false || strpos($title, 'Developer') !== false || strpos($title, 'Agency') !== false;
    
    // Set plugin slug for WordPress.org link
    $plugin_slug = '';
    if (strpos($title, 'Property Management') !== false) {
        $plugin_slug = 'vireo-property-management';
    } elseif (strpos($title, 'Sports League') !== false) {
        $plugin_slug = 'sports-league-manager';
    } elseif (strpos($title, 'EquipRent') !== false) {
        $plugin_slug = 'equiprent';
    } elseif (strpos($title, 'DealerEdge') !== false) {
        $plugin_slug = 'dealeredge';
    } elseif (strpos($title, 'GymFlow') !== false) {
        $plugin_slug = 'gymflow';
    } elseif (strpos($title, 'StudioSnap') !== false) {
        $plugin_slug = 'studiosnap';
    }
    ?>
    
    <div class="single-product-page">
        
        <!-- Product Hero -->
        <section class="product-hero">
            <div class="container">
                <div class="breadcrumbs">
                    <a href="<?php echo home_url('/'); ?>">Home</a>
                    <span class="separator">→</span>
                    <a href="<?php echo home_url('/shop/'); ?>">Shop</a>
                    <span class="separator">→</span>
                    <span class="current"><?php the_title(); ?></span>
                </div>
                
                <div class="product-hero-content">
                    <div class="product-info">
                        <h1 class="product-title"><?php the_title(); ?></h1>
                        <div class="product-meta">
                            <?php if ($is_bundle): ?>
                                <span class="meta-badge bundle"><i class="fas fa-layer-group"></i> Plugin Bundle</span>
                            <?php elseif ($is_pro): ?>
                                <span class="meta-badge pro"><i class="fas fa-crown"></i> Pro Version</span>
                            <?php else: ?>
                                <span class="meta-badge free"><i class="fas fa-gift"></i> Free Available</span>
                            <?php endif; ?>
                            
                            <?php if ($industry && $industry !== 'general'): ?>
                                <span class="meta-badge industry"><?php echo ucfirst($industry); ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="product-excerpt">
                            <?php the_excerpt(); ?>
                        </div>
                        
                        <div class="product-pricing">
                            <div class="price-display">
                                <?php echo $product->get_price_html(); ?>
                                <?php if (!$is_bundle): ?>
                                    <span class="price-period">per year</span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="product-actions">
                                <?php if (!$is_bundle && $plugin_slug): ?>
                                    <a href="https://wordpress.org/plugins/<?php echo $plugin_slug; ?>/" class="btn btn-secondary" target="_blank">
                                        <i class="fab fa-wordpress"></i> Try Free Version
                                    </a>
                                <?php endif; ?>
                                
                                <form class="cart" method="post" enctype='multipart/form-data'>
                                    <?php do_action('woocommerce_before_add_to_cart_button'); ?>
                                    
                                    <button type="submit" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>" class="single_add_to_cart_button btn btn-primary">
                                        <i class="fas fa-shopping-cart"></i> <?php echo esc_html($product->single_add_to_cart_text()); ?>
                                    </button>
                                    
                                    <?php do_action('woocommerce_after_add_to_cart_button'); ?>
                                </form>
                            </div>
                        </div>
                        
                        <div class="product-guarantees">
                            <div class="guarantee-item">
                                <i class="fas fa-shield-alt"></i>
                                <span>30-Day Money Back</span>
                            </div>
                            <div class="guarantee-item">
                                <i class="fas fa-sync"></i>
                                <span>1 Year Updates</span>
                            </div>
                            <div class="guarantee-item">
                                <i class="fas fa-headset"></i>
                                <span>Priority Support</span>
                            </div>
                            <div class="guarantee-item">
                                <i class="fas fa-download"></i>
                                <span>Instant Download</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="product-visual">
                        <?php if (has_post_thumbnail()): ?>
                            <?php the_post_thumbnail('large', array('class' => 'product-image')); ?>
                        <?php else: ?>
                            <div class="product-icon-large">
                                <?php
                                // Show icon based on product type
                                if (strpos($title, 'Property') !== false) {
                                    echo '<i class="fas fa-building"></i>';
                                } elseif (strpos($title, 'Sports') !== false) {
                                    echo '<i class="fas fa-trophy"></i>';
                                } elseif (strpos($title, 'Equip') !== false) {
                                    echo '<i class="fas fa-tools"></i>';
                                } elseif (strpos($title, 'Dealer') !== false) {
                                    echo '<i class="fas fa-car"></i>';
                                } elseif (strpos($title, 'Gym') !== false) {
                                    echo '<i class="fas fa-dumbbell"></i>';
                                } elseif (strpos($title, 'Studio') !== false) {
                                    echo '<i class="fas fa-camera"></i>';
                                } elseif ($is_bundle) {
                                    echo '<i class="fas fa-layer-group"></i>';
                                } else {
                                    echo '<i class="fas fa-plug"></i>';
                                }
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Product Details -->
        <section class="product-details">
            <div class="container">
                <div class="details-tabs">
                    <div class="tab-buttons">
                        <button class="tab-button active" data-tab="description">Description</button>
                        <button class="tab-button" data-tab="features">Features</button>
                        <?php if (!$is_bundle): ?>
                            <button class="tab-button" data-tab="comparison">Free vs Pro</button>
                        <?php endif; ?>
                        <button class="tab-button" data-tab="requirements">Requirements</button>
                    </div>
                    
                    <div class="tab-content">
                        <!-- Description Tab -->
                        <div class="tab-pane active" id="description">
                            <?php the_content(); ?>
                        </div>
                        
                        <!-- Features Tab -->
                        <div class="tab-pane" id="features">
                            <?php if ($is_bundle): ?>
                                <h3>Everything Included in the Bundle</h3>
                                <div class="bundle-includes">
                                    <div class="included-plugin">
                                        <i class="fas fa-building"></i>
                                        <h4>Property Management Pro</h4>
                                        <p>Complete property, tenant, and lease management</p>
                                    </div>
                                    <div class="included-plugin">
                                        <i class="fas fa-trophy"></i>
                                        <h4>Sports League Manager Pro</h4>
                                        <p>Teams, players, schedules, and statistics</p>
                                    </div>
                                    <div class="included-plugin">
                                        <i class="fas fa-tools"></i>
                                        <h4>EquipRent Pro</h4>
                                        <p>Equipment rental and booking management</p>
                                    </div>
                                    <div class="included-plugin">
                                        <i class="fas fa-car"></i>
                                        <h4>DealerEdge Pro</h4>
                                        <p>Auto shop and dealer management</p>
                                    </div>
                                    <div class="included-plugin">
                                        <i class="fas fa-dumbbell"></i>
                                        <h4>GymFlow Pro</h4>
                                        <p>Fitness studio and member management</p>
                                    </div>
                                    <div class="included-plugin">
                                        <i class="fas fa-camera"></i>
                                        <h4>StudioSnap Pro</h4>
                                        <p>Photography studio workflow</p>
                                    </div>
                                </div>
                            <?php else: ?>
                                <h3>Key Features</h3>
                                <div class="features-grid">
                                    <?php
                                    // Default features based on product type
                                    $default_features = array();
                                    if (strpos($title, 'Property') !== false) {
                                        $default_features = array(
                                            'Property portfolio management',
                                            'Tenant and lease tracking',
                                            'Maintenance request system',
                                            'Payment tracking and automation',
                                            'Document management',
                                            'Financial reporting',
                                            'Email notifications',
                                            'Tenant portal access'
                                        );
                                    } elseif (strpos($title, 'Sports') !== false) {
                                        $default_features = array(
                                            'Team and player management',
                                            'Season and schedule creation',
                                            'Game results and statistics',
                                            'League standings',
                                            'Player stats tracking',
                                            'Tournament brackets',
                                            'Public team pages',
                                            'Mobile-responsive design'
                                        );
                                    }
                                    // Add more product-specific features...
                                    
                                    $display_features = !empty($features) ? $features : $default_features;
                                    foreach ($display_features as $feature): ?>
                                        <div class="feature-item">
                                            <i class="fas fa-check"></i>
                                            <span><?php echo esc_html($feature); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Comparison Tab -->
                        <?php if (!$is_bundle): ?>
                        <div class="tab-pane" id="comparison">
                            <h3>Compare Free vs Pro</h3>
                            <table class="comparison-table">
                                <thead>
                                    <tr>
                                        <th>Feature</th>
                                        <th>Free Version</th>
                                        <th>Pro Version</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Basic Management Features</td>
                                        <td><i class="fas fa-check text-success"></i></td>
                                        <td><i class="fas fa-check text-success"></i></td>
                                    </tr>
                                    <tr>
                                        <td>Unlimited Records</td>
                                        <td><i class="fas fa-times text-danger"></i> Limited</td>
                                        <td><i class="fas fa-check text-success"></i></td>
                                    </tr>
                                    <tr>
                                        <td>Advanced Analytics</td>
                                        <td><i class="fas fa-times text-danger"></i></td>
                                        <td><i class="fas fa-check text-success"></i></td>
                                    </tr>
                                    <tr>
                                        <td>Email Automation</td>
                                        <td><i class="fas fa-times text-danger"></i></td>
                                        <td><i class="fas fa-check text-success"></i></td>
                                    </tr>
                                    <tr>
                                        <td>Priority Support</td>
                                        <td><i class="fas fa-times text-danger"></i></td>
                                        <td><i class="fas fa-check text-success"></i></td>
                                    </tr>
                                    <tr>
                                        <td>White Label Options</td>
                                        <td><i class="fas fa-times text-danger"></i></td>
                                        <td><i class="fas fa-check text-success"></i></td>
                                    </tr>
                                    <tr>
                                        <td>API Access</td>
                                        <td><i class="fas fa-times text-danger"></i></td>
                                        <td><i class="fas fa-check text-success"></i></td>
                                    </tr>
                                    <tr>
                                        <td>1 Year of Updates</td>
                                        <td><i class="fas fa-times text-danger"></i></td>
                                        <td><i class="fas fa-check text-success"></i></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Requirements Tab -->
                        <div class="tab-pane" id="requirements">
                            <h3>System Requirements</h3>
                            <div class="requirements-list">
                                <div class="requirement-item">
                                    <i class="fab fa-wordpress"></i>
                                    <div>
                                        <strong>WordPress Version:</strong> 5.8 or higher
                                    </div>
                                </div>
                                <div class="requirement-item">
                                    <i class="fab fa-php"></i>
                                    <div>
                                        <strong>PHP Version:</strong> 7.4 or higher (8.0+ recommended)
                                    </div>
                                </div>
                                <div class="requirement-item">
                                    <i class="fas fa-database"></i>
                                    <div>
                                        <strong>MySQL Version:</strong> 5.6 or higher
                                    </div>
                                </div>
                                <div class="requirement-item">
                                    <i class="fas fa-server"></i>
                                    <div>
                                        <strong>Memory Limit:</strong> 128MB minimum (256MB recommended)
                                    </div>
                                </div>
                            </div>
                            
                            <h3>Installation</h3>
                            <ol class="installation-steps">
                                <li>Purchase and download the plugin ZIP file</li>
                                <li>Go to WordPress Admin → Plugins → Add New</li>
                                <li>Click "Upload Plugin" and select the ZIP file</li>
                                <li>Click "Install Now" and then "Activate"</li>
                                <li>Enter your license key in the plugin settings</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Related Products -->
        <section class="related-products">
            <div class="container">
                <h2 class="section-title">You May Also Like</h2>
                <?php
                $related = wc_get_related_products($product->get_id(), 3);
                if ($related) {
                    echo '<div class="products-grid">';
                    foreach ($related as $related_id) {
                        $related_product = wc_get_product($related_id);
                        ?>
                        <div class="product-card">
                            <div class="product-icon">
                                <i class="fas fa-plug"></i>
                            </div>
                            <h3><?php echo $related_product->get_name(); ?></h3>
                            <div class="product-price">
                                <?php echo $related_product->get_price_html(); ?>
                            </div>
                            <a href="<?php echo get_permalink($related_id); ?>" class="btn btn-secondary">View Details</a>
                        </div>
                        <?php
                    }
                    echo '</div>';
                }
                ?>
            </div>
        </section>
        
    </div>
    
    <style>
    .single-product-page {
        padding-top: 2rem;
    }
    
    .breadcrumbs {
        margin-bottom: 2rem;
        color: #666;
    }
    
    .breadcrumbs a {
        color: #666;
        text-decoration: none;
    }
    
    .breadcrumbs a:hover {
        color: var(--primary-color);
    }
    
    .breadcrumbs .separator {
        margin: 0 0.5rem;
        color: #999;
    }
    
    .product-hero {
        background: #f8f9fa;
        padding: 3rem 0 4rem;
    }
    
    .product-hero-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4rem;
        align-items: center;
    }
    
    .product-title {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        color: #333;
    }
    
    .product-meta {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .meta-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 600;
    }
    
    .meta-badge.bundle {
        background: #fd7e14;
        color: white;
    }
    
    .meta-badge.pro {
        background: #6f42c1;
        color: white;
    }
    
    .meta-badge.free {
        background: #28a745;
        color: white;
    }
    
    .meta-badge.industry {
        background: #e9ecef;
        color: #495057;
    }
    
    .product-excerpt {
        font-size: 1.125rem;
        line-height: 1.6;
        color: #666;
        margin-bottom: 2rem;
    }
    
    .product-pricing {
        margin-bottom: 2rem;
    }
    
    .price-display {
        font-size: 2.5rem;
        font-weight: bold;
        color: var(--primary-color);
        margin-bottom: 1rem;
    }
    
    .price-period {
        font-size: 1rem;
        font-weight: normal;
        color: #666;
    }
    
    .product-actions {
        display: flex;
        gap: 1rem;
        margin-bottom: 2rem;
    }
    
    .product-guarantees {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    .guarantee-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #666;
    }
    
    .guarantee-item i {
        color: var(--primary-color);
    }
    
    .product-visual {
        display: flex;
        justify-content: center;
        align-items: center;
    }
    
    .product-icon-large {
        width: 200px;
        height: 200px;
        background: var(--primary-color);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 20px;
        font-size: 80px;
    }
    
    .product-details {
        padding: 4rem 0;
    }
    
    .tab-buttons {
        display: flex;
        gap: 1rem;
        border-bottom: 2px solid #e9ecef;
        margin-bottom: 2rem;
    }
    
    .tab-button {
        background: none;
        border: none;
        padding: 1rem 2rem;
        font-size: 1rem;
        font-weight: 600;
        color: #666;
        cursor: pointer;
        border-bottom: 3px solid transparent;
        transition: all 0.3s ease;
    }
    
    .tab-button:hover {
        color: var(--primary-color);
    }
    
    .tab-button.active {
        color: var(--primary-color);
        border-bottom-color: var(--primary-color);
    }
    
    .tab-pane {
        display: none;
    }
    
    .tab-pane.active {
        display: block;
    }
    
    .features-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    .feature-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .feature-item i {
        color: #28a745;
    }
    
    .bundle-includes {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 2rem;
        margin-top: 2rem;
    }
    
    .included-plugin {
        text-align: center;
        padding: 2rem;
        background: #f8f9fa;
        border-radius: 12px;
    }
    
    .included-plugin i {
        font-size: 3rem;
        color: var(--primary-color);
        margin-bottom: 1rem;
    }
    
    .included-plugin h4 {
        margin-bottom: 0.5rem;
    }
    
    .comparison-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 2rem;
    }
    
    .comparison-table th,
    .comparison-table td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid #e9ecef;
    }
    
    .comparison-table th {
        background: #f8f9fa;
        font-weight: 600;
    }
    
    .text-success {
        color: #28a745;
    }
    
    .text-danger {
        color: #dc3545;
    }
    
    .requirements-list {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
        margin: 2rem 0;
    }
    
    .requirement-item {
        display: flex;
        align-items: start;
        gap: 1rem;
    }
    
    .requirement-item i {
        font-size: 1.5rem;
        color: var(--primary-color);
        margin-top: 0.25rem;
    }
    
    .installation-steps {
        padding-left: 1.5rem;
        line-height: 2;
    }
    
    .related-products {
        background: #f8f9fa;
        padding: 4rem 0;
    }
    
    .section-title {
        text-align: center;
        font-size: 2rem;
        margin-bottom: 3rem;
        color: #333;
    }
    
    .products-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 2rem;
    }
    
    .product-card {
        background: white;
        padding: 2rem;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    
    .product-card:hover {
        transform: translateY(-5px);
    }
    
    .product-card .product-icon {
        width: 80px;
        height: 80px;
        background: var(--primary-color);
        color: white;
        margin: 0 auto 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 32px;
    }
    
    .product-card h3 {
        margin-bottom: 1rem;
        font-size: 1.25rem;
    }
    
    .product-card .product-price {
        font-size: 1.5rem;
        font-weight: bold;
        color: var(--primary-color);
        margin-bottom: 1rem;
    }
    
    @media (max-width: 768px) {
        .product-hero-content {
            grid-template-columns: 1fr;
        }
        
        .product-guarantees {
            grid-template-columns: 1fr;
        }
        
        .features-grid {
            grid-template-columns: 1fr;
        }
        
        .bundle-includes {
            grid-template-columns: 1fr;
        }
        
        .requirements-list {
            grid-template-columns: 1fr;
        }
        
        .products-grid {
            grid-template-columns: 1fr;
        }
        
        .tab-buttons {
            overflow-x: auto;
            flex-wrap: nowrap;
        }
        
        .tab-button {
            white-space: nowrap;
        }
    }
    </style>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab functionality
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabPanes = document.querySelectorAll('.tab-pane');
        
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetTab = this.getAttribute('data-tab');
                
                // Remove active class from all
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabPanes.forEach(pane => pane.classList.remove('active'));
                
                // Add active class to clicked
                this.classList.add('active');
                document.getElementById(targetTab).classList.add('active');
            });
        });
    });
    </script>
    
    <?php
endwhile;

get_footer('shop');
?>