<?php
/**
 * Template Name: Plugin Directory
 * Unified plugins directory showcasing all Vireo Designs plugins
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
                            <a href="<?php echo home_url('/shop/'); ?>?product=property-management-pro" class="btn btn-primary">Get Pro</a>
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
                            <a href="<?php echo home_url('/shop/'); ?>?product=sports-league-pro" class="btn btn-primary">Get Pro</a>
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

.plugin-header {
    display: flex;
    gap: var(--space-4);
    margin-bottom: var(--space-4);
}

.plugin-icon {
    width: 60px;
    height: 60px;
    background: var(--primary-gradient);
    border-radius: var(--radius-xl);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
    font-size: var(--text-xl);
    flex-shrink: 0;
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
    background: var(--primary-color);
    color: var(--white);
    border-color: var(--primary-color);
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