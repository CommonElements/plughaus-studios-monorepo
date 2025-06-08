<?php
/**
 * Professional Homepage Template
 * Inspired by top WordPress plugin sites
 */

get_header(); ?>

<main id="primary" class="site-main">
    
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-background">
            <div class="hero-pattern"></div>
        </div>
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <div class="hero-badge">
                        <i class="fas fa-star"></i>
                        Trusted by 10,000+ WordPress Sites
                    </div>
                    <h1 class="hero-title">
                        Powerful WordPress Plugins<br>
                        <span class="text-gradient">Built for Modern Business</span>
                    </h1>
                    <p class="hero-description">
                        Transform your WordPress site into a powerful business platform. 
                        Our plugins help you manage properties, process payments, automate workflows, 
                        and scale your operations with confidence.
                    </p>
                    <div class="hero-actions">
                        <a href="/shop/" class="btn btn-primary btn-lg">
                            <i class="fas fa-rocket"></i>
                            Get Started Free
                        </a>
                        <a href="/demo/" class="btn btn-outline btn-lg">
                            <i class="fas fa-play"></i>
                            View Demo
                        </a>
                    </div>
                    <div class="hero-trust">
                        <span class="trust-text">Trusted by leading companies:</span>
                        <div class="trust-logos">
                            <div class="trust-logo">Property Managers</div>
                            <div class="trust-logo">Agencies</div>
                            <div class="trust-logo">Consultants</div>
                            <div class="trust-logo">Developers</div>
                        </div>
                    </div>
                </div>
                <div class="hero-visual">
                    <div class="hero-demo">
                        <div class="demo-window">
                            <div class="demo-header">
                                <div class="demo-dots">
                                    <span></span><span></span><span></span>
                                </div>
                                <div class="demo-title">PlugHaus Dashboard</div>
                            </div>
                            <div class="demo-content">
                                <div class="demo-stats">
                                    <div class="stat-card">
                                        <div class="stat-number">247</div>
                                        <div class="stat-label">Properties</div>
                                    </div>
                                    <div class="stat-card">
                                        <div class="stat-number">$84k</div>
                                        <div class="stat-label">Revenue</div>
                                    </div>
                                    <div class="stat-card">
                                        <div class="stat-number">98%</div>
                                        <div class="stat-label">Occupancy</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Features Overview -->
    <section class="features-overview">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Everything You Need to Succeed</h2>
                <p class="section-description">
                    Comprehensive WordPress plugins designed to streamline your business operations
                </p>
            </div>
            
            <div class="features-grid">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3 class="feature-title">Lightning Fast</h3>
                    <p class="feature-description">
                        Optimized for performance with efficient code and smart caching
                    </p>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="feature-title">Enterprise Security</h3>
                    <p class="feature-description">
                        Bank-grade security with regular audits and WordPress standards compliance
                    </p>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3 class="feature-title">Mobile Ready</h3>
                    <p class="feature-description">
                        Responsive design that works perfectly on all devices and screen sizes
                    </p>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-puzzle-piece"></i>
                    </div>
                    <h3 class="feature-title">Easy Integration</h3>
                    <p class="feature-description">
                        Seamlessly integrates with popular WordPress plugins and themes
                    </p>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3 class="feature-title">24/7 Support</h3>
                    <p class="feature-description">
                        Get help when you need it with our dedicated support team
                    </p>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-sync-alt"></i>
                    </div>
                    <h3 class="feature-title">Automatic Updates</h3>
                    <p class="feature-description">
                        Stay current with automatic updates and new feature releases
                    </p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Plugin Showcase -->
    <section class="plugin-showcase">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Our WordPress Plugins</h2>
                <p class="section-description">
                    Professional solutions for every business need
                </p>
            </div>
            
            <div class="plugin-grid">
                <?php
                $featured_plugins = get_posts(array(
                    'post_type' => 'phstudios_plugin',
                    'posts_per_page' => 4,
                    'post_status' => 'publish'
                ));
                
                $plugin_icons = ['home', 'credit-card', 'file-alt', 'chart-line'];
                
                foreach ($featured_plugins as $index => $plugin) :
                    setup_postdata($plugin);
                    $icon = $plugin_icons[$index % count($plugin_icons)];
                    $status = get_post_meta($plugin->ID, '_plugin_status', true) ?: 'available';
                    $downloads = get_post_meta($plugin->ID, '_download_count', true) ?: '1000';
                    $rating = get_post_meta($plugin->ID, '_rating', true) ?: '4.8';
                ?>
                    <div class="plugin-card">
                        <div class="plugin-header">
                            <div class="plugin-icon">
                                <i class="fas fa-<?php echo $icon; ?>"></i>
                            </div>
                            <div class="plugin-status status-<?php echo $status; ?>">
                                <?php echo ucwords(str_replace('-', ' ', $status)); ?>
                            </div>
                        </div>
                        <div class="plugin-content">
                            <h3 class="plugin-title"><?php the_title(); ?></h3>
                            <p class="plugin-excerpt"><?php echo esc_html(get_the_excerpt()); ?></p>
                            <div class="plugin-stats">
                                <div class="stat">
                                    <i class="fas fa-download"></i>
                                    <?php echo number_format($downloads); ?>+
                                </div>
                                <div class="stat">
                                    <i class="fas fa-star"></i>
                                    <?php echo $rating; ?>/5
                                </div>
                            </div>
                        </div>
                        <div class="plugin-actions">
                            <a href="<?php the_permalink(); ?>" class="btn btn-outline">Learn More</a>
                            <a href="/shop/" class="btn btn-primary">Get Plugin</a>
                        </div>
                    </div>
                <?php endforeach; wp_reset_postdata(); ?>
            </div>
        </div>
    </section>
    
    <!-- Social Proof -->
    <section class="social-proof">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Loved by WordPress Professionals</h2>
            </div>
            
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"PlugHaus Property Management has transformed how we handle our rental portfolio. The automation features alone save us 20+ hours per week."</p>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="author-info">
                            <div class="author-name">Sarah Johnson</div>
                            <div class="author-title">Property Manager, Johnson Real Estate</div>
                        </div>
                    </div>
                    <div class="testimonial-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"The payment processing integration is seamless. Our clients love the smooth checkout experience and we love the detailed analytics."</p>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="author-info">
                            <div class="author-name">Mike Chen</div>
                            <div class="author-title">CEO, Tech Solutions LLC</div>
                        </div>
                    </div>
                    <div class="testimonial-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"Outstanding support team and rock-solid plugins. We've built our entire business platform on PlugHaus solutions."</p>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="author-info">
                            <div class="author-name">Emily Rodriguez</div>
                            <div class="author-title">CTO, Digital Agency Co</div>
                        </div>
                    </div>
                    <div class="testimonial-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
            </div>
            
            <div class="social-proof-stats">
                <div class="proof-stat">
                    <div class="stat-number">10,000+</div>
                    <div class="stat-label">Active Sites</div>
                </div>
                <div class="proof-stat">
                    <div class="stat-number">99.9%</div>
                    <div class="stat-label">Uptime</div>
                </div>
                <div class="proof-stat">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Support</div>
                </div>
                <div class="proof-stat">
                    <div class="stat-number">4.9/5</div>
                    <div class="stat-label">Rating</div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">Ready to Transform Your WordPress Site?</h2>
                <p class="cta-description">
                    Join thousands of satisfied customers who trust PlugHaus Studios for their business needs.
                </p>
                <div class="cta-actions">
                    <a href="/shop/" class="btn btn-primary btn-xl">
                        <i class="fas fa-rocket"></i>
                        Start Free Today
                    </a>
                    <a href="/contact/" class="btn btn-outline btn-xl">
                        <i class="fas fa-comments"></i>
                        Talk to Sales
                    </a>
                </div>
                <div class="cta-guarantee">
                    <i class="fas fa-shield-alt"></i>
                    30-day money-back guarantee
                </div>
            </div>
        </div>
    </section>
    
</main>

<?php get_footer(); ?>