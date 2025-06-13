<?php
/**
 * Template for displaying the Industries page
 *
 * @package Vireo_Designs
 */

get_header();
?>

<div class="industries-page">
    
    <!-- Hero Section -->
    <section class="page-hero">
        <div class="hero-background"></div>
        <div class="hero-pattern"></div>
        <div class="container">
            <div class="page-hero-content">
                <h1 class="page-hero-title">Industry Solutions</h1>
                <p class="page-hero-description">
                    Specialized WordPress plugins designed for specific industries. 
                    Built by experts who understand your business challenges.
                </p>
            </div>
        </div>
    </section>

    <!-- Industries Grid -->
    <section class="industries-section">
        <div class="container">
            
            <div class="industries-grid">
                
                <!-- Property Management -->
                <div class="industry-card featured">
                    <div class="industry-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <h3 class="industry-title">Property Management</h3>
                    <p class="industry-description">
                        Complete property management suite for landlords, property managers, and real estate professionals. 
                        Manage properties, tenants, leases, and maintenance requests.
                    </p>
                    <div class="industry-features">
                        <span class="feature-tag">Tenant Management</span>
                        <span class="feature-tag">Lease Tracking</span>
                        <span class="feature-tag">Maintenance Requests</span>
                        <span class="feature-tag">Payment Processing</span>
                    </div>
                    <div class="industry-status">
                        <span class="status-badge status-available">Available Now</span>
                    </div>
                    <div class="industry-actions">
                        <a href="<?php echo esc_url(home_url('/industries/property-management/')); ?>" class="btn btn-primary">Learn More</a>
                        <a href="<?php echo esc_url(home_url('/pricing/')); ?>" class="btn btn-outline">View Pricing</a>
                    </div>
                </div>

                <!-- Sports Leagues -->
                <div class="industry-card">
                    <div class="industry-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h3 class="industry-title">Sports Leagues</h3>
                    <p class="industry-description">
                        Complete sports league management with team rosters, scheduling, 
                        statistics tracking, tournament brackets, and player profiles.
                    </p>
                    <div class="industry-features">
                        <span class="feature-tag">Team Management</span>
                        <span class="feature-tag">Game Scheduling</span>
                        <span class="feature-tag">Statistics Tracking</span>
                        <span class="feature-tag">Tournament Brackets</span>
                    </div>
                    <div class="industry-status">
                        <span class="status-badge status-development">In Development</span>
                    </div>
                    <div class="industry-actions">
                        <a href="<?php echo esc_url(home_url('/industries/sports-leagues/')); ?>" class="btn btn-primary">Learn More</a>
                        <a href="#" class="btn btn-outline btn-notify">Notify Me</a>
                    </div>
                </div>

                <!-- Fantasy Sports -->
                <div class="industry-card">
                    <div class="industry-icon">
                        <i class="fas fa-medal"></i>
                    </div>
                    <h3 class="industry-title">Fantasy Sports</h3>
                    <p class="industry-description">
                        Advanced fantasy sports platform with league administration, 
                        scoring systems, player statistics, and comprehensive analytics.
                    </p>
                    <div class="industry-features">
                        <span class="feature-tag">League Administration</span>
                        <span class="feature-tag">Custom Scoring</span>
                        <span class="feature-tag">Player Analytics</span>
                        <span class="feature-tag">Live Scoring</span>
                    </div>
                    <div class="industry-status">
                        <span class="status-badge status-coming-soon">Coming Soon</span>
                    </div>
                    <div class="industry-actions">
                        <a href="<?php echo esc_url(home_url('/industries/fantasy-sports/')); ?>" class="btn btn-primary">Learn More</a>
                        <a href="#" class="btn btn-outline btn-notify">Notify Me</a>
                    </div>
                </div>

                <!-- Gym & Fitness -->
                <div class="industry-card">
                    <div class="industry-icon">
                        <i class="fas fa-dumbbell"></i>
                    </div>
                    <h3 class="industry-title">Gym & Fitness</h3>
                    <p class="industry-description">
                        Streamline gym operations with member management, class scheduling, 
                        trainer assignments, and equipment tracking for fitness centers.
                    </p>
                    <div class="industry-features">
                        <span class="feature-tag">Member Management</span>
                        <span class="feature-tag">Class Scheduling</span>
                        <span class="feature-tag">Trainer Profiles</span>
                        <span class="feature-tag">Equipment Tracking</span>
                    </div>
                    <div class="industry-status">
                        <span class="status-badge status-coming-soon">Coming Soon</span>
                    </div>
                    <div class="industry-actions">
                        <a href="<?php echo esc_url(home_url('/industries/gym-fitness/')); ?>" class="btn btn-primary">Learn More</a>
                        <a href="#" class="btn btn-outline btn-notify">Notify Me</a>
                    </div>
                </div>

                <!-- Equipment Rental -->
                <div class="industry-card">
                    <div class="industry-icon">
                        <i class="fas fa-tools"></i>
                    </div>
                    <h3 class="industry-title">Equipment Rental</h3>
                    <p class="industry-description">
                        Manage equipment inventory, rentals, maintenance schedules, and customer relationships 
                        for construction and tool rental businesses.
                    </p>
                    <div class="industry-features">
                        <span class="feature-tag">Inventory Management</span>
                        <span class="feature-tag">Rental Tracking</span>
                        <span class="feature-tag">Maintenance Logs</span>
                        <span class="feature-tag">Customer Portal</span>
                    </div>
                    <div class="industry-status">
                        <span class="status-badge status-coming-soon">Coming Soon</span>
                    </div>
                    <div class="industry-actions">
                        <a href="<?php echo esc_url(home_url('/industries/equipment-rental/')); ?>" class="btn btn-primary">Learn More</a>
                        <a href="#" class="btn btn-outline btn-notify">Notify Me</a>
                    </div>
                </div>

                <!-- Marina & RV Resorts -->
                <div class="industry-card">
                    <div class="industry-icon">
                        <i class="fas fa-anchor"></i>
                    </div>
                    <h3 class="industry-title">Marina & RV Resorts</h3>
                    <p class="industry-description">
                        Comprehensive solution for marinas and RV parks. Manage slip rentals, 
                        guest reservations, amenities, and seasonal bookings.
                    </p>
                    <div class="industry-features">
                        <span class="feature-tag">Slip Management</span>
                        <span class="feature-tag">Reservations</span>
                        <span class="feature-tag">Seasonal Rates</span>
                        <span class="feature-tag">Guest Services</span>
                    </div>
                    <div class="industry-status">
                        <span class="status-badge status-coming-soon">Coming Soon</span>
                    </div>
                    <div class="industry-actions">
                        <a href="<?php echo esc_url(home_url('/industries/marina-rv-resorts/')); ?>" class="btn btn-primary">Learn More</a>
                        <a href="#" class="btn btn-outline btn-notify">Notify Me</a>
                    </div>
                </div>

                <!-- Self Storage -->
                <div class="industry-card">
                    <div class="industry-icon">
                        <i class="fas fa-warehouse"></i>
                    </div>
                    <h3 class="industry-title">Self Storage</h3>
                    <p class="industry-description">
                        Complete self-storage facility management with unit tracking, 
                        tenant management, automated billing, and access control integration.
                    </p>
                    <div class="industry-features">
                        <span class="feature-tag">Unit Management</span>
                        <span class="feature-tag">Tenant Portal</span>
                        <span class="feature-tag">Automated Billing</span>
                        <span class="feature-tag">Access Control</span>
                    </div>
                    <div class="industry-status">
                        <span class="status-badge status-coming-soon">Coming Soon</span>
                    </div>
                    <div class="industry-actions">
                        <a href="<?php echo esc_url(home_url('/industries/self-storage/')); ?>" class="btn btn-primary">Learn More</a>
                        <a href="#" class="btn btn-outline btn-notify">Notify Me</a>
                    </div>
                </div>

                <!-- Nonprofits -->
                <div class="industry-card">
                    <div class="industry-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3 class="industry-title">Nonprofits</h3>
                    <p class="industry-description">
                        Comprehensive nonprofit management with donor tracking, volunteer coordination, 
                        event management, and fundraising campaign tools.
                    </p>
                    <div class="industry-features">
                        <span class="feature-tag">Donor Management</span>
                        <span class="feature-tag">Volunteer Coordination</span>
                        <span class="feature-tag">Event Planning</span>
                        <span class="feature-tag">Fundraising Tools</span>
                    </div>
                    <div class="industry-status">
                        <span class="status-badge status-coming-soon">Coming Soon</span>
                    </div>
                    <div class="industry-actions">
                        <a href="<?php echo esc_url(home_url('/industries/nonprofits/')); ?>" class="btn btn-primary">Learn More</a>
                        <a href="#" class="btn btn-outline btn-notify">Notify Me</a>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- Why Choose Industry-Specific Solutions -->
    <section class="why-industry-section">
        <div class="container">
            
            <div class="section-header">
                <h2 class="section-title">Why Industry-Specific Solutions?</h2>
                <p class="section-description">
                    Generic software often falls short. Our industry-focused plugins are built by experts 
                    who understand the unique challenges and workflows of your business.
                </p>
            </div>

            <div class="benefits-grid">
                
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <h3>Purpose-Built</h3>
                    <p>Designed specifically for your industry's unique workflows and requirements.</p>
                </div>

                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <h3>Quick Setup</h3>
                    <p>Pre-configured with industry best practices. Get up and running in minutes, not months.</p>
                </div>

                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Expert Support</h3>
                    <p>Our team understands your industry and can provide knowledgeable, relevant support.</p>
                </div>

                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Scalable Growth</h3>
                    <p>Solutions that grow with your business, from startup to enterprise.</p>
                </div>

            </div>

        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="cta-background"></div>
        <div class="cta-pattern"></div>
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">Ready to Transform Your Business?</h2>
                <p class="cta-description">
                    Choose the industry solution that fits your needs and start streamlining your operations today.
                </p>
                <div class="cta-actions">
                    <a href="<?php echo esc_url(home_url('/pricing/')); ?>" class="btn btn-primary btn-lg">View Pricing</a>
                    <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="btn btn-outline btn-lg">Contact Sales</a>
                </div>
            </div>
        </div>
    </section>

</div>

<style>
/* Industries Page Styles */
.industries-section {
    padding: var(--space-16) 0;
    background: var(--gray-50);
}

.industries-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
    gap: var(--space-8);
    margin-top: var(--space-12);
}

.industry-card {
    background: var(--white);
    border: 2px solid var(--gray-200);
    border-radius: var(--radius-2xl);
    padding: var(--space-8);
    transition: all var(--transition-normal);
    position: relative;
    display: flex;
    flex-direction: column;
}

.industry-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-xl);
    border-color: var(--primary-color);
}

.industry-card.featured {
    border-color: var(--primary-color);
    background: linear-gradient(135deg, var(--white) 0%, rgba(5, 150, 105, 0.02) 100%);
}

.industry-card.featured::before {
    content: "Featured";
    position: absolute;
    top: -12px;
    left: var(--space-6);
    background: var(--primary-color);
    color: var(--white);
    padding: var(--space-1) var(--space-3);
    border-radius: var(--radius-full);
    font-size: var(--text-xs);
    font-weight: 700;
    text-transform: uppercase;
}

.industry-icon {
    width: 80px;
    height: 80px;
    background: var(--primary-gradient);
    border-radius: var(--radius-2xl);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: var(--space-6);
    font-size: var(--text-3xl);
    color: var(--white);
}

.industry-title {
    font-size: var(--text-xl);
    font-weight: 700;
    margin-bottom: var(--space-4);
    color: var(--gray-900);
}

.industry-description {
    color: var(--gray-600);
    line-height: 1.6;
    margin-bottom: var(--space-6);
    flex-grow: 1;
}

.industry-features {
    display: flex;
    flex-wrap: wrap;
    gap: var(--space-2);
    margin-bottom: var(--space-6);
}

.industry-status {
    margin-bottom: var(--space-6);
}

.industry-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--space-3);
}

.why-industry-section {
    padding: var(--space-16) 0;
    background: var(--white);
}

.benefits-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: var(--space-8);
    margin-top: var(--space-12);
}

.benefit-item {
    text-align: center;
    padding: var(--space-6);
}

.benefit-icon {
    width: 60px;
    height: 60px;
    background: var(--primary-gradient);
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto var(--space-4);
    font-size: var(--text-xl);
    color: var(--white);
}

.benefit-item h3 {
    font-size: var(--text-lg);
    font-weight: 700;
    margin-bottom: var(--space-3);
    color: var(--gray-900);
}

.benefit-item p {
    color: var(--gray-600);
    line-height: 1.6;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .industries-grid {
        grid-template-columns: 1fr;
        gap: var(--space-6);
    }
    
    .industry-actions {
        grid-template-columns: 1fr;
    }
    
    .benefits-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: var(--space-6);
    }
}

@media (max-width: 480px) {
    .benefits-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php get_footer(); ?>