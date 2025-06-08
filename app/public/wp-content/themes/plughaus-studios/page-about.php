<?php
/**
 * Template Name: About Page
 * Professional about page
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
                    About <span class="text-gradient">PlugHaus Studios</span>
                </h1>
                <p class="page-hero-description">
                    We're a WordPress plugin development studio focused on creating professional solutions for modern businesses.
                </p>
            </div>
        </div>
    </section>

    <!-- Our Story -->
    <section style="padding: 6rem 0; background: white;">
        <div class="container">
            <div style="max-width: 800px; margin: 0 auto; text-align: center;">
                <h2 style="font-size: 2.5rem; margin-bottom: 2rem; color: var(--gray-900);">Our Story</h2>
                <p style="font-size: 1.25rem; line-height: 1.6; color: var(--gray-600); margin-bottom: 2rem;">
                    Founded with the mission to bridge the gap between powerful enterprise software and accessible WordPress solutions, 
                    PlugHaus Studios specializes in creating professional-grade plugins that small and medium businesses can actually use.
                </p>
                <p style="font-size: 1.125rem; line-height: 1.6; color: var(--gray-600);">
                    We believe that powerful business tools shouldn't be locked behind expensive enterprise contracts. 
                    Our freemium approach lets you start for free and scale as your business grows.
                </p>
            </div>
        </div>
    </section>

    <!-- Our Values -->
    <section class="features-overview">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Our Values</h2>
                <p class="section-description">
                    The principles that guide everything we do
                </p>
            </div>
            
            <div class="features-grid">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="feature-title">Customer First</h3>
                    <p class="feature-description">
                        Every decision we make starts with our customers. We build solutions for real problems that real businesses face.
                    </p>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-code"></i>
                    </div>
                    <h3 class="feature-title">Quality Code</h3>
                    <p class="feature-description">
                        We follow WordPress coding standards, write clean code, and thoroughly test everything before release.
                    </p>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3 class="feature-title">Transparency</h3>
                    <p class="feature-description">
                        No hidden fees, no surprises. We're upfront about pricing, features, and what you can expect from us.
                    </p>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <h3 class="feature-title">Innovation</h3>
                    <p class="feature-description">
                        We're constantly improving our plugins with new features, better performance, and enhanced user experience.
                    </p>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3 class="feature-title">Community</h3>
                    <p class="feature-description">
                        We're active members of the WordPress community and believe in giving back through open source contributions.
                    </p>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="feature-title">Security</h3>
                    <p class="feature-description">
                        Your data is precious. We implement enterprise-grade security practices to keep your information safe.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section style="padding: 6rem 0; background: white;">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">By the Numbers</h2>
                <p class="section-description">
                    Our impact in the WordPress community
                </p>
            </div>
            
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">10,000+</div>
                    <div class="stat-label">Active Installations</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">50+</div>
                    <div class="stat-label">Countries Served</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">99.9%</div>
                    <div class="stat-label">Uptime SLA</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">4.9/5</div>
                    <div class="stat-label">Customer Rating</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission Section -->
    <section style="padding: 6rem 0; background: linear-gradient(135deg, var(--primary-color), var(--primary-light)); color: white;">
        <div class="container">
            <div style="max-width: 800px; margin: 0 auto; text-align: center;">
                <h2 style="font-size: 2.5rem; margin-bottom: 2rem;">Our Mission</h2>
                <p style="font-size: 1.5rem; line-height: 1.6; opacity: 0.95; margin-bottom: 2rem;">
                    "To democratize powerful business tools by making enterprise-grade WordPress plugins accessible to businesses of all sizes."
                </p>
                <p style="font-size: 1.125rem; line-height: 1.6; opacity: 0.9;">
                    We believe every business deserves access to the tools they need to succeed, 
                    regardless of their size or budget. That's why we start every plugin with a free version 
                    and build Pro features that truly add value.
                </p>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section style="padding: 6rem 0; background: var(--gray-50);">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title" style="color: var(--gray-900);">Ready to Join Our Community?</h2>
                <p class="cta-description" style="color: var(--gray-600);">
                    Start with our free plugins and see why thousands of businesses trust PlugHaus Studios.
                </p>
                <div class="cta-actions">
                    <a href="/plugins/" class="btn btn-primary btn-xl">
                        <i class="fas fa-download"></i>
                        Browse Plugins
                    </a>
                    <a href="/contact/" class="btn btn-outline btn-xl">
                        <i class="fas fa-envelope"></i>
                        Get in Touch
                    </a>
                </div>
            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>