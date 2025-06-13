<?php
/**
 * WooCommerce Plugins Store Page
 * Vireo Designs - Complete e-commerce integration for plugin sales
 */

get_header(); ?>

<main id="primary" class="site-main">
    
    <!-- Store Header -->
    <section class="store-hero">
        <div class="hero-background">
            <div class="hero-pattern"></div>
        </div>
        <div class="container">
            <div class="store-hero-content">
                <h1 class="store-hero-title">
                    Vireo Designs <span class="text-gradient">Plugin Store</span>
                </h1>
                <p class="store-hero-description">
                    Professional WordPress plugins with free versions and premium upgrades. Download, purchase, and manage your licenses all in one place.
                </p>
                <div class="store-features">
                    <div class="store-feature">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
                        </svg>
                        <span>Secure Payments</span>
                    </div>
                    <div class="store-feature">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M5 20h14v-2H5v2zM19 9h-4V3H9v6H5l7 7 7-7z"/>
                        </svg>
                        <span>Instant Downloads</span>
                    </div>
                    <div class="store-feature">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                        <span>License Management</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Plugin Products -->
    <section class="plugin-products">
        <div class="container">
            
            <!-- Property Management Plugin -->
            <div class="product-showcase">
                <div class="product-info">
                    <div class="product-header">
                        <div class="product-icon">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                            </svg>
                        </div>
                        <div class="product-meta">
                            <h2 class="product-title">Vireo Property Management</h2>
                            <p class="product-status">
                                <span class="status-badge in-development">In Active Development</span>
                            </p>
                        </div>
                    </div>
                    
                    <p class="product-description">
                        Complete property portfolio management solution for WordPress. Handle properties, tenants, leases, maintenance requests, and financial reporting with professional-grade tools.
                    </p>
                    
                    <div class="product-features">
                        <h4>Core Features (Free)</h4>
                        <ul class="feature-list">
                            <li>✓ Property & Unit Management</li>
                            <li>✓ Tenant Management</li>
                            <li>✓ Basic Lease Tracking</li>
                            <li>✓ Maintenance Requests</li>
                            <li>✓ Basic Reports</li>
                        </ul>
                        
                        <h4>Pro Features</h4>
                        <ul class="feature-list pro">
                            <li>⭐ Advanced Analytics Dashboard</li>
                            <li>⭐ Payment Automation</li>
                            <li>⭐ Email Templates & Notifications</li>
                            <li>⭐ Advanced Reporting & Exports</li>
                            <li>⭐ Priority Support</li>
                        </ul>
                    </div>
                </div>
                
                <div class="product-purchase">
                    <div class="pricing-cards">
                        
                        <!-- Free Version -->
                        <div class="pricing-card free">
                            <div class="pricing-header">
                                <h3>Free Version</h3>
                                <div class="price">
                                    <span class="currency">$</span>
                                    <span class="amount">0</span>
                                    <span class="period">forever</span>
                                </div>
                            </div>
                            <div class="pricing-features">
                                <p>Perfect for small portfolios</p>
                                <ul>
                                    <li>Up to 10 properties</li>
                                    <li>Basic management tools</li>
                                    <li>Community support</li>
                                    <li>WordPress.org updates</li>
                                </ul>
                            </div>
                            <div class="pricing-action">
                                <button class="btn btn-outline btn-block" disabled>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                    </svg>
                                    Coming Soon
                                </button>
                                <p class="pricing-note">Available when plugin launches</p>
                            </div>
                        </div>
                        
                        <!-- Pro Version -->
                        <div class="pricing-card pro featured">
                            <div class="pricing-badge">Most Popular</div>
                            <div class="pricing-header">
                                <h3>Pro License</h3>
                                <div class="price">
                                    <span class="currency">$</span>
                                    <span class="amount">99</span>
                                    <span class="period">/year</span>
                                </div>
                            </div>
                            <div class="pricing-features">
                                <p>For growing property managers</p>
                                <ul>
                                    <li>Unlimited properties</li>
                                    <li>All pro features</li>
                                    <li>Priority support</li>
                                    <li>Automatic updates</li>
                                    <li>1-year license</li>
                                </ul>
                            </div>
                            <div class="pricing-action">
                                <button class="btn btn-primary btn-block" disabled>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                                    </svg>
                                    Pre-Order Pro
                                </button>
                                <p class="pricing-note">25% off early bird pricing</p>
                            </div>
                        </div>
                        
                    </div>
                    
                    <!-- Early Access -->
                    <div class="early-access">
                        <h4>🎯 Early Access Program</h4>
                        <p>Be among the first to test and provide feedback on Vireo Property Management. Early access includes:</p>
                        <ul>
                            <li>✓ Beta testing access</li>
                            <li>✓ Direct feedback channel with developers</li>
                            <li>✓ 25% discount on Pro license</li>
                            <li>✓ Influence on feature development</li>
                        </ul>
                        <button class="btn btn-secondary btn-block">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                            Join Early Access
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Coming Soon Plugins -->
            <div class="coming-soon-section">
                <h2 class="section-title">Coming Soon</h2>
                <p class="section-description">More professional WordPress plugins are in development</p>
                
                <div class="coming-soon-grid">
                    
                    <!-- Sports League Manager -->
                    <div class="coming-soon-card">
                        <div class="card-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M8 12h8M12 8v8"/>
                            </svg>
                        </div>
                        <h3>Vireo Sports League</h3>
                        <p>Complete sports league management with teams, schedules, statistics, and tournaments.</p>
                        <div class="release-info">
                            <span class="release-date">Q2 2025</span>
                            <span class="release-status">In Development</span>
                        </div>
                        <button class="btn btn-outline btn-sm">Join Waitlist</button>
                    </div>
                    
                    <!-- Equipment Rental -->
                    <div class="coming-soon-card">
                        <div class="card-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm-1 16H9V7h9v14z"/>
                            </svg>
                        </div>
                        <h3>Vireo Equipment Rental</h3>
                        <p>Professional equipment rental management with inventory tracking and booking system.</p>
                        <div class="release-info">
                            <span class="release-date">Q3 2025</span>
                            <span class="release-status">Planned</span>
                        </div>
                        <button class="btn btn-outline btn-sm">Notify Me</button>
                    </div>
                    
                    <!-- Auto Dealer -->
                    <div class="coming-soon-card">
                        <div class="card-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.22.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/>
                            </svg>
                        </div>
                        <h3>Vireo Auto Dealer</h3>
                        <p>Small car dealer management system with inventory, sales tracking, and customer management.</p>
                        <div class="release-info">
                            <span class="release-date">Q4 2025</span>
                            <span class="release-status">Planned</span>
                        </div>
                        <button class="btn btn-outline btn-sm">Learn More</button>
                    </div>
                    
                </div>
            </div>
            
        </div>
    </section>
    
    <!-- Newsletter Signup -->
    <section class="newsletter-section">
        <div class="container">
            <div class="newsletter-content">
                <h2>Stay Updated on New Releases</h2>
                <p>Get notified when new plugins launch and receive exclusive early access offers.</p>
                <form class="newsletter-form">
                    <div class="form-group">
                        <input type="email" placeholder="Enter your email address" required>
                        <button type="submit" class="btn btn-primary">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                            </svg>
                            Subscribe
                        </button>
                    </div>
                    <p class="privacy-note">We respect your privacy. Unsubscribe at any time.</p>
                </form>
            </div>
        </div>
    </section>

</main>

<style>
/* Store-specific styles */
.store-hero {
    padding: var(--spacing-16) 0 var(--spacing-12);
    background: linear-gradient(135deg, var(--gray-50) 0%, #ffffff 100%);
    position: relative;
    overflow: hidden;
}

.store-hero-title {
    font-size: var(--font-size-4xl);
    font-weight: 700;
    margin-bottom: var(--spacing-4);
    text-align: center;
}

.store-hero-description {
    font-size: var(--font-size-lg);
    color: var(--gray-600);
    text-align: center;
    margin-bottom: var(--spacing-8);
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.store-features {
    display: flex;
    justify-content: center;
    gap: var(--spacing-8);
    flex-wrap: wrap;
}

.store-feature {
    display: flex;
    align-items: center;
    gap: var(--spacing-2);
    color: var(--primary-color);
    font-weight: 500;
}

.product-showcase {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: var(--spacing-12);
    margin-bottom: var(--spacing-20);
    background: white;
    border-radius: var(--radius-xl);
    padding: var(--spacing-8);
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--gray-200);
}

.product-header {
    display: flex;
    gap: var(--spacing-4);
    margin-bottom: var(--spacing-6);
}

.product-icon {
    width: 64px;
    height: 64px;
    background: var(--primary-gradient);
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.product-title {
    font-size: var(--font-size-3xl);
    font-weight: 700;
    margin-bottom: var(--spacing-2);
}

.status-badge {
    display: inline-block;
    padding: var(--spacing-1) var(--spacing-3);
    border-radius: var(--radius);
    font-size: var(--font-size-sm);
    font-weight: 500;
}

.status-badge.in-development {
    background: rgba(8, 145, 178, 0.1);
    color: var(--info-color);
}

.product-features {
    margin-top: var(--spacing-6);
}

.product-features h4 {
    margin-bottom: var(--spacing-3);
    color: var(--gray-900);
}

.feature-list {
    list-style: none;
    margin-bottom: var(--spacing-6);
}

.feature-list li {
    padding: var(--spacing-1) 0;
    color: var(--gray-700);
}

.feature-list.pro li {
    color: var(--primary-color);
}

.pricing-cards {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-4);
    margin-bottom: var(--spacing-6);
}

.pricing-card {
    border: 2px solid var(--gray-200);
    border-radius: var(--radius-lg);
    padding: var(--spacing-6);
    background: white;
    position: relative;
}

.pricing-card.featured {
    border-color: var(--primary-color);
    box-shadow: var(--shadow-lg);
}

.pricing-badge {
    position: absolute;
    top: -10px;
    left: 50%;
    transform: translateX(-50%);
    background: var(--primary-color);
    color: white;
    padding: var(--spacing-1) var(--spacing-3);
    border-radius: var(--radius);
    font-size: var(--font-size-sm);
    font-weight: 500;
}

.pricing-header h3 {
    margin-bottom: var(--spacing-2);
}

.price {
    display: flex;
    align-items: baseline;
    margin-bottom: var(--spacing-4);
}

.price .amount {
    font-size: var(--font-size-3xl);
    font-weight: 700;
    color: var(--gray-900);
}

.price .currency,
.price .period {
    color: var(--gray-600);
}

.pricing-features ul {
    list-style: none;
    margin: var(--spacing-4) 0;
}

.pricing-features li {
    padding: var(--spacing-1) 0;
    color: var(--gray-700);
}

.pricing-note {
    text-align: center;
    font-size: var(--font-size-sm);
    color: var(--gray-600);
    margin-top: var(--spacing-2);
}

.early-access {
    background: var(--gray-50);
    padding: var(--spacing-6);
    border-radius: var(--radius-lg);
    border: 1px solid var(--gray-200);
}

.early-access h4 {
    margin-bottom: var(--spacing-3);
    color: var(--gray-900);
}

.early-access ul {
    list-style: none;
    margin: var(--spacing-4) 0;
}

.early-access li {
    padding: var(--spacing-1) 0;
    color: var(--gray-700);
}

.coming-soon-section {
    margin-top: var(--spacing-20);
    text-align: center;
}

.coming-soon-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: var(--spacing-6);
    margin-top: var(--spacing-8);
}

.coming-soon-card {
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-lg);
    padding: var(--spacing-6);
    text-align: center;
    transition: var(--transition);
}

.coming-soon-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.coming-soon-card .card-icon {
    width: 64px;
    height: 64px;
    background: var(--gray-100);
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto var(--spacing-4);
    color: var(--gray-600);
}

.coming-soon-card h3 {
    margin-bottom: var(--spacing-3);
}

.coming-soon-card p {
    color: var(--gray-600);
    margin-bottom: var(--spacing-4);
}

.release-info {
    display: flex;
    justify-content: space-between;
    margin-bottom: var(--spacing-4);
    font-size: var(--font-size-sm);
}

.release-date {
    color: var(--primary-color);
    font-weight: 500;
}

.release-status {
    color: var(--gray-500);
}

.newsletter-section {
    background: var(--gray-900);
    color: white;
    padding: var(--spacing-16) 0;
    text-align: center;
}

.newsletter-content h2 {
    color: white;
    margin-bottom: var(--spacing-4);
}

.newsletter-content p {
    color: var(--gray-300);
    margin-bottom: var(--spacing-6);
}

.newsletter-form .form-group {
    display: flex;
    max-width: 400px;
    margin: 0 auto var(--spacing-3);
    gap: var(--spacing-2);
}

.newsletter-form input {
    flex: 1;
    padding: var(--spacing-3);
    border: 1px solid var(--gray-600);
    border-radius: var(--radius);
    background: var(--gray-800);
    color: white;
}

.newsletter-form input::placeholder {
    color: var(--gray-400);
}

.privacy-note {
    font-size: var(--font-size-sm);
    color: var(--gray-400);
}

/* Responsive */
@media (max-width: 768px) {
    .product-showcase {
        grid-template-columns: 1fr;
        gap: var(--spacing-6);
    }
    
    .store-features {
        flex-direction: column;
        align-items: center;
        gap: var(--spacing-4);
    }
    
    .coming-soon-grid {
        grid-template-columns: 1fr;
    }
    
    .newsletter-form .form-group {
        flex-direction: column;
    }
}
</style>

<?php get_footer(); ?>