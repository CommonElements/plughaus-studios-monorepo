<?php
/**
 * Template Name: Plugins Page
 * Professional plugins showcase page
 */

get_header(); ?>

<main id="primary" class="site-main">
    
    <!-- Page Header -->
    <section class="page-hero">
        <div class="hero-background">
            <div class="hero-pattern"></div>
        </div>
        <div class="container">
            <div class="page-hero-content">
                <h1 class="page-hero-title">
                    Our <span class="text-gradient">Plugins</span>
                </h1>
                <p class="page-hero-description">
                    Professional WordPress plugins built for modern business needs. Start free and upgrade when you're ready for more power.
                </p>
            </div>
        </div>
    </section>

    <!-- Plugin Showcase -->
    <section class="features-overview">
        <div class="container">
            
            <!-- Featured Plugin -->
            <div class="featured-plugin">
                <div class="featured-plugin-content">
                    <div class="featured-plugin-text">
                        <div class="badge">Featured Plugin</div>
                        <h2 class="featured-plugin-title">Property Management Pro</h2>
                        <p class="featured-plugin-description">
                            Complete property management solution for WordPress. Manage properties, tenants, leases, and maintenance requests with professional-grade tools.
                        </p>
                        
                        <div class="plugin-badges">
                            <span class="badge badge-success">Free Version Available</span>
                            <span class="badge badge-secondary">Pro Features</span>
                        </div>
                        
                        <div class="featured-plugin-actions">
                            <a href="/pricing/" class="btn btn-primary">
                                <i class="fas fa-download"></i>
                                Download Free
                            </a>
                            <a href="/features/" class="btn btn-outline">
                                <i class="fas fa-info-circle"></i>
                                Learn More
                            </a>
                        </div>
                    </div>
                    
                    <div class="featured-plugin-showcase">
                        <div class="plugin-icon">
                            <i class="fas fa-home"></i>
                            <h3>Property Management</h3>
                            <p>Your complete property management solution</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Plugin Categories -->
            <div class="section-header">
                <h2 class="section-title">Plugin Categories</h2>
                <p class="section-description">
                    Explore our growing ecosystem of professional WordPress plugins
                </p>
            </div>

            <div class="content-grid">
                
                <!-- Property Management -->
                <div class="content-card">
                    <div class="content-card-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <h3 class="content-card-title">Property Management</h3>
                    <p class="content-card-description">
                        Complete property management tools for residential and commercial properties.
                    </p>
                    <div class="plugin-tags">
                        <span class="tag">Properties</span>
                        <span class="tag">Tenants</span>
                        <span class="tag">Leases</span>
                    </div>
                    <a href="/pricing/" class="btn btn-outline btn-block">View Plugin</a>
                </div>

                <!-- Coming Soon - Community Management -->
                <div class="content-card coming-soon">
                    <div class="content-card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="content-card-title">Community Management</h3>
                    <p class="content-card-description">
                        HOA and community association management tools.
                    </p>
                    <div class="plugin-tags">
                        <span class="badge badge-secondary">Coming Soon</span>
                    </div>
                    <button class="btn btn-outline btn-block" disabled>Coming Q2 2025</button>
                </div>

                <!-- Coming Soon - Work Orders -->
                <div class="content-card coming-soon">
                    <div class="content-card-icon">
                        <i class="fas fa-wrench"></i>
                    </div>
                    <h3 class="content-card-title">Work Order Management</h3>
                    <p class="content-card-description">
                        Professional work order and maintenance request system.
                    </p>
                    <div class="plugin-tags">
                        <span class="badge badge-secondary">Coming Soon</span>
                    </div>
                    <button class="btn btn-outline btn-block" disabled>Coming Q3 2025</button>
                </div>
            </div>

            <!-- Plugin Features Comparison -->
            <div class="comparison-table">
                <h3 class="section-title">Free vs Pro Features</h3>
                
                <table>
                    <thead>
                        <tr>
                            <th>Feature</th>
                            <th>Free</th>
                            <th>Pro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Property Management</td>
                            <td><i class="fas fa-check"></i></td>
                            <td><i class="fas fa-check"></i></td>
                        </tr>
                        <tr>
                            <td>Tenant Management</td>
                            <td><i class="fas fa-check"></i></td>
                            <td><i class="fas fa-check"></i></td>
                        </tr>
                        <tr>
                            <td>Basic Reporting</td>
                            <td><i class="fas fa-check"></i></td>
                            <td><i class="fas fa-check"></i></td>
                        </tr>
                        <tr>
                            <td>Advanced Analytics</td>
                            <td><i class="fas fa-times"></i></td>
                            <td><i class="fas fa-check"></i></td>
                        </tr>
                        <tr>
                            <td>Payment Automation</td>
                            <td><i class="fas fa-times"></i></td>
                            <td><i class="fas fa-check"></i></td>
                        </tr>
                        <tr>
                            <td>Priority Support</td>
                            <td><i class="fas fa-times"></i></td>
                            <td><i class="fas fa-check"></i></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">Ready to Get Started?</h2>
                <p class="cta-description">
                    Download our free plugins today and see the difference quality makes.
                </p>
                <div class="cta-actions">
                    <a href="/pricing/" class="btn btn-primary btn-xl">
                        <i class="fas fa-download"></i>
                        Download Free
                    </a>
                    <a href="/contact/" class="btn btn-outline btn-xl">
                        <i class="fas fa-comments"></i>
                        Ask Questions
                    </a>
                </div>
            </div>
        </div>
    </section>

</main>

<style>
@media (max-width: 768px) {
    .container > div[style*="grid-template-columns: 1fr 1fr"] {
        grid-template-columns: 1fr !important;
        gap: 2rem !important;
    }
    
    table {
        font-size: 0.875rem;
    }
}
</style>

<?php get_footer(); ?>