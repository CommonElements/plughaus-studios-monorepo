<?php
/**
 * Template Name: Plugin Directory
 * Description: Main plugin showcase and download page
 */

get_header(); ?>

<main id="primary" class="site-main plugin-directory">
    
    <!-- Hero Section -->
    <section class="plugin-directory-hero">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">WordPress Plugin Directory</h1>
                <p class="hero-description">
                    Professional business management plugins for WordPress. Choose from free versions on WordPress.org or upgrade to Pro for advanced features.
                </p>
                <div class="hero-actions">
                    <a href="/shop/" class="btn btn-primary">View All Pro Versions</a>
                    <a href="#plugins" class="btn btn-outline">Browse Free Plugins</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Plugin Grid -->
    <section id="plugins" class="plugins-section">
        <div class="container">
            <h2 class="section-title">Available Plugins</h2>
            
            <div class="plugins-grid">
                
                <!-- Property Management Plugin -->
                <div class="plugin-card">
                    <div class="plugin-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <h3 class="plugin-title">Vireo Property Management</h3>
                    <p class="plugin-description">
                        Complete property management solution for landlords and property managers. Track properties, tenants, leases, and maintenance.
                    </p>
                    <div class="plugin-features">
                        <span><i class="fas fa-check"></i> Property Tracking</span>
                        <span><i class="fas fa-check"></i> Tenant Management</span>
                        <span><i class="fas fa-check"></i> Lease Management</span>
                        <span><i class="fas fa-check"></i> Maintenance Requests</span>
                    </div>
                    <div class="plugin-actions">
                        <a href="https://wordpress.org/plugins/vireo-property-management/" class="btn btn-secondary" target="_blank">
                            <i class="fab fa-wordpress"></i> Free Version
                        </a>
                        <a href="/product/vireo-property-management-pro/" class="btn btn-primary">
                            <i class="fas fa-crown"></i> Get Pro - $149/year
                        </a>
                    </div>
                </div>

                <!-- Sports League Manager -->
                <div class="plugin-card">
                    <div class="plugin-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h3 class="plugin-title">Sports League Manager</h3>
                    <p class="plugin-description">
                        Manage sports leagues, teams, players, schedules, and statistics. Perfect for league administrators and tournament organizers.
                    </p>
                    <div class="plugin-features">
                        <span><i class="fas fa-check"></i> Team Management</span>
                        <span><i class="fas fa-check"></i> Player Rosters</span>
                        <span><i class="fas fa-check"></i> Game Schedules</span>
                        <span><i class="fas fa-check"></i> Statistics Tracking</span>
                    </div>
                    <div class="plugin-actions">
                        <a href="https://wordpress.org/plugins/sports-league-manager/" class="btn btn-secondary" target="_blank">
                            <i class="fab fa-wordpress"></i> Free Version
                        </a>
                        <a href="/product/sports-league-manager-pro/" class="btn btn-primary">
                            <i class="fas fa-crown"></i> Get Pro - $129/year
                        </a>
                    </div>
                </div>

                <!-- EquipRent Pro -->
                <div class="plugin-card">
                    <div class="plugin-icon">
                        <i class="fas fa-tools"></i>
                    </div>
                    <h3 class="plugin-title">EquipRent Pro</h3>
                    <p class="plugin-description">
                        Equipment rental management system. Track inventory, bookings, maintenance, and customer rentals with ease.
                    </p>
                    <div class="plugin-features">
                        <span><i class="fas fa-check"></i> Inventory Management</span>
                        <span><i class="fas fa-check"></i> Booking Calendar</span>
                        <span><i class="fas fa-check"></i> Customer Portal</span>
                        <span><i class="fas fa-check"></i> Maintenance Tracking</span>
                    </div>
                    <div class="plugin-actions">
                        <a href="https://wordpress.org/plugins/equiprent/" class="btn btn-secondary" target="_blank">
                            <i class="fab fa-wordpress"></i> Free Version
                        </a>
                        <a href="/product/equiprent-pro/" class="btn btn-primary">
                            <i class="fas fa-crown"></i> Get Pro - $199/year
                        </a>
                    </div>
                </div>

                <!-- DealerEdge -->
                <div class="plugin-card">
                    <div class="plugin-icon">
                        <i class="fas fa-car"></i>
                    </div>
                    <h3 class="plugin-title">DealerEdge</h3>
                    <p class="plugin-description">
                        Auto shop and small dealer management. Track inventory, work orders, customer vehicles, and service history.
                    </p>
                    <div class="plugin-features">
                        <span><i class="fas fa-check"></i> Vehicle Inventory</span>
                        <span><i class="fas fa-check"></i> Work Orders</span>
                        <span><i class="fas fa-check"></i> Customer Management</span>
                        <span><i class="fas fa-check"></i> Parts Tracking</span>
                    </div>
                    <div class="plugin-actions">
                        <a href="https://wordpress.org/plugins/dealeredge/" class="btn btn-secondary" target="_blank">
                            <i class="fab fa-wordpress"></i> Free Version
                        </a>
                        <a href="/product/dealeredge-pro/" class="btn btn-primary">
                            <i class="fas fa-crown"></i> Get Pro - $249/year
                        </a>
                    </div>
                </div>

                <!-- GymFlow -->
                <div class="plugin-card">
                    <div class="plugin-icon">
                        <i class="fas fa-dumbbell"></i>
                    </div>
                    <h3 class="plugin-title">GymFlow</h3>
                    <p class="plugin-description">
                        Fitness studio management for gyms, yoga studios, and personal trainers. Manage members, classes, and payments.
                    </p>
                    <div class="plugin-features">
                        <span><i class="fas fa-check"></i> Member Management</span>
                        <span><i class="fas fa-check"></i> Class Scheduling</span>
                        <span><i class="fas fa-check"></i> Payment Tracking</span>
                        <span><i class="fas fa-check"></i> Trainer Management</span>
                    </div>
                    <div class="plugin-actions">
                        <a href="https://wordpress.org/plugins/gymflow/" class="btn btn-secondary" target="_blank">
                            <i class="fab fa-wordpress"></i> Free Version
                        </a>
                        <a href="/product/gymflow-pro/" class="btn btn-primary">
                            <i class="fas fa-crown"></i> Get Pro - $149/year
                        </a>
                    </div>
                </div>

                <!-- StudioSnap -->
                <div class="plugin-card">
                    <div class="plugin-icon">
                        <i class="fas fa-camera"></i>
                    </div>
                    <h3 class="plugin-title">StudioSnap</h3>
                    <p class="plugin-description">
                        Photography studio management. Handle bookings, client galleries, contracts, and payment processing.
                    </p>
                    <div class="plugin-features">
                        <span><i class="fas fa-check"></i> Client Booking</span>
                        <span><i class="fas fa-check"></i> Gallery Management</span>
                        <span><i class="fas fa-check"></i> Contract Templates</span>
                        <span><i class="fas fa-check"></i> Invoice Generation</span>
                    </div>
                    <div class="plugin-actions">
                        <a href="https://wordpress.org/plugins/studiosnap/" class="btn btn-secondary" target="_blank">
                            <i class="fab fa-wordpress"></i> Free Version
                        </a>
                        <a href="/product/studiosnap-pro/" class="btn btn-primary">
                            <i class="fas fa-crown"></i> Get Pro - $129/year
                        </a>
                    </div>
                </div>
                
            </div>
        </div>
    </section>

    <!-- Bundle Offer -->
    <section class="bundle-section">
        <div class="container">
            <div class="bundle-card">
                <div class="bundle-content">
                    <h2>Business Bundle - Save 40%</h2>
                    <p>Get all 6 pro plugins for one low price. Perfect for agencies or businesses needing multiple solutions.</p>
                    <div class="bundle-price">
                        <span class="original-price">$1,050/year</span>
                        <span class="sale-price">$629/year</span>
                    </div>
                    <a href="/product/business-bundle/" class="btn btn-primary btn-lg">
                        <i class="fas fa-layer-group"></i> Get the Bundle
                    </a>
                </div>
                <div class="bundle-features">
                    <h3>Bundle Includes:</h3>
                    <ul>
                        <li><i class="fas fa-check"></i> All 6 Pro Plugins</li>
                        <li><i class="fas fa-check"></i> Unlimited Site Licenses</li>
                        <li><i class="fas fa-check"></i> Priority Support</li>
                        <li><i class="fas fa-check"></i> Future Plugin Access</li>
                        <li><i class="fas fa-check"></i> Developer Resources</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

</main>

<style>
.plugin-directory-hero {
    background: var(--nature-gradient);
    color: white;
    padding: 4rem 0;
    text-align: center;
}

.hero-title {
    font-size: 3rem;
    margin-bottom: 1rem;
    color: white;
}

.hero-description {
    font-size: 1.25rem;
    margin-bottom: 2rem;
    opacity: 0.9;
    max-width: 700px;
    margin-left: auto;
    margin-right: auto;
}

.hero-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

.plugins-section {
    padding: 4rem 0;
    background: #f8f9fa;
}

.section-title {
    text-align: center;
    font-size: 2.5rem;
    margin-bottom: 3rem;
    color: var(--primary-color);
}

.plugins-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
}

.plugin-card {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.plugin-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}

.plugin-icon {
    width: 60px;
    height: 60px;
    background: var(--primary-color);
    color: white;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin-bottom: 1rem;
}

.plugin-title {
    font-size: 1.5rem;
    margin-bottom: 1rem;
    color: #333;
}

.plugin-description {
    color: #666;
    margin-bottom: 1.5rem;
    line-height: 1.6;
}

.plugin-features {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.5rem;
    margin-bottom: 1.5rem;
}

.plugin-features span {
    font-size: 0.875rem;
    color: #555;
}

.plugin-features i {
    color: #28a745;
    margin-right: 0.5rem;
}

.plugin-actions {
    display: flex;
    gap: 1rem;
}

.plugin-actions .btn {
    flex: 1;
    text-align: center;
    padding: 0.75rem 1rem;
}

.bundle-section {
    padding: 4rem 0;
    background: white;
}

.bundle-card {
    background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
    border-radius: 16px;
    padding: 3rem;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    align-items: center;
}

.bundle-content h2 {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    color: var(--primary-color);
}

.bundle-price {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin: 2rem 0;
}

.original-price {
    text-decoration: line-through;
    color: #999;
    font-size: 1.5rem;
}

.sale-price {
    font-size: 2.5rem;
    font-weight: bold;
    color: var(--primary-color);
}

.bundle-features {
    background: white;
    padding: 2rem;
    border-radius: 12px;
}

.bundle-features h3 {
    margin-bottom: 1rem;
    color: var(--primary-color);
}

.bundle-features ul {
    list-style: none;
    padding: 0;
}

.bundle-features li {
    padding: 0.5rem 0;
    color: #555;
}

.bundle-features i {
    color: #28a745;
    margin-right: 0.5rem;
}

@media (max-width: 768px) {
    .plugins-grid {
        grid-template-columns: 1fr;
    }
    
    .bundle-card {
        grid-template-columns: 1fr;
    }
    
    .hero-actions {
        flex-direction: column;
    }
    
    .plugin-actions {
        flex-direction: column;
    }
}
</style>

<?php get_footer(); ?>