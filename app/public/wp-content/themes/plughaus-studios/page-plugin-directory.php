<?php
/**
 * Template Name: Plugin Directory
 * Browse all PlugHaus Studios plugins
 */

get_header(); ?>

<main id="primary" class="site-main">
    
    <!-- Directory Header -->
    <section class="page-hero">
        <div class="hero-background">
            <div class="hero-pattern"></div>
        </div>
        <div class="container">
            <div class="page-hero-content">
                <h1 class="page-hero-title">
                    Plugin <span class="text-gradient">Directory</span>
                </h1>
                <p class="page-hero-description">
                    Discover professional WordPress plugins designed to power your business. All plugins start free with Pro upgrades available.
                </p>
            </div>
        </div>
    </section>

    <!-- Search and Filters -->
    <section class="plugin-filters">
        <div class="container">
            <div class="filters-wrapper">
                <div class="search-bar">
                    <div class="search-input-wrapper">
                        <i class="fas fa-search"></i>
                        <input type="text" id="plugin-search" placeholder="Search plugins..." class="search-input">
                    </div>
                </div>
                
                <div class="filter-tabs">
                    <button class="filter-tab active" data-filter="all">
                        <i class="fas fa-th"></i>
                        All Plugins
                    </button>
                    <button class="filter-tab" data-filter="property">
                        <i class="fas fa-home"></i>
                        Property Management
                    </button>
                    <button class="filter-tab" data-filter="coming-soon">
                        <i class="fas fa-clock"></i>
                        Coming Soon
                    </button>
                    <button class="filter-tab" data-filter="featured">
                        <i class="fas fa-star"></i>
                        Featured
                    </button>
                </div>
                
                <div class="sort-options">
                    <select id="sort-select" class="sort-select">
                        <option value="popular">Most Popular</option>
                        <option value="newest">Newest</option>
                        <option value="rating">Highest Rated</option>
                        <option value="name">Name A-Z</option>
                    </select>
                </div>
            </div>
        </div>
    </section>

    <!-- Plugin Grid -->
    <section class="plugin-directory">
        <div class="container">
            <div class="plugin-grid" id="plugin-grid">
                
                <!-- Property Management Plugin -->
                <div class="plugin-card featured" data-category="property" data-rating="4.9" data-downloads="10000">
                    <div class="plugin-card-header">
                        <div class="plugin-badges">
                            <span class="badge badge-featured">Featured</span>
                            <span class="badge badge-success">Free Available</span>
                        </div>
                        <div class="plugin-rating">
                            <div class="stars">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <span class="rating-text">4.9 (127)</span>
                        </div>
                    </div>
                    
                    <div class="plugin-card-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    
                    <h3 class="plugin-card-title">Property Management Pro</h3>
                    <p class="plugin-card-description">
                        Complete property management solution with tenant tracking, lease management, and payment processing.
                    </p>
                    
                    <div class="plugin-card-stats">
                        <div class="stat">
                            <i class="fas fa-download"></i>
                            <span>10,000+ downloads</span>
                        </div>
                        <div class="stat">
                            <i class="fas fa-calendar"></i>
                            <span>Updated 2 days ago</span>
                        </div>
                    </div>
                    
                    <div class="plugin-card-tags">
                        <span class="tag">Property Management</span>
                        <span class="tag">Real Estate</span>
                        <span class="tag">Rental</span>
                    </div>
                    
                    <div class="plugin-card-actions">
                        <a href="/plugin/property-management/" class="btn btn-primary btn-block">
                            <i class="fas fa-info-circle"></i>
                            View Details
                        </a>
                        <div class="action-buttons">
                            <a href="/downloads/property-management-free.zip" class="btn btn-outline btn-sm">
                                <i class="fas fa-download"></i>
                                Free
                            </a>
                            <a href="/checkout/?product=property-management-pro" class="btn btn-secondary btn-sm">
                                <i class="fas fa-crown"></i>
                                Pro $99
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Community Management Plugin (Coming Soon) -->
                <div class="plugin-card coming-soon" data-category="coming-soon property" data-rating="0" data-downloads="0">
                    <div class="plugin-card-header">
                        <div class="plugin-badges">
                            <span class="badge badge-secondary">Coming Q2 2025</span>
                        </div>
                        <div class="plugin-rating">
                            <span class="rating-text">Coming Soon</span>
                        </div>
                    </div>
                    
                    <div class="plugin-card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    
                    <h3 class="plugin-card-title">Community Management</h3>
                    <p class="plugin-card-description">
                        HOA and community association management with board communications, dues tracking, and voting systems.
                    </p>
                    
                    <div class="plugin-card-stats">
                        <div class="stat">
                            <i class="fas fa-calendar"></i>
                            <span>Expected Q2 2025</span>
                        </div>
                        <div class="stat">
                            <i class="fas fa-bell"></i>
                            <span>Get notified</span>
                        </div>
                    </div>
                    
                    <div class="plugin-card-tags">
                        <span class="tag">HOA</span>
                        <span class="tag">Community</span>
                        <span class="tag">Board Management</span>
                    </div>
                    
                    <div class="plugin-card-actions">
                        <button class="btn btn-outline btn-block" disabled>
                            <i class="fas fa-clock"></i>
                            Coming Soon
                        </button>
                        <div class="action-buttons">
                            <button class="btn btn-secondary btn-block notify-btn" data-plugin="community-management">
                                <i class="fas fa-bell"></i>
                                Notify When Available
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Work Order Management Plugin (Coming Soon) -->
                <div class="plugin-card coming-soon" data-category="coming-soon maintenance" data-rating="0" data-downloads="0">
                    <div class="plugin-card-header">
                        <div class="plugin-badges">
                            <span class="badge badge-secondary">Coming Q3 2025</span>
                        </div>
                        <div class="plugin-rating">
                            <span class="rating-text">Coming Soon</span>
                        </div>
                    </div>
                    
                    <div class="plugin-card-icon">
                        <i class="fas fa-wrench"></i>
                    </div>
                    
                    <h3 class="plugin-card-title">Work Order Management</h3>
                    <p class="plugin-card-description">
                        Professional work order system with technician scheduling, parts tracking, and customer communication.
                    </p>
                    
                    <div class="plugin-card-stats">
                        <div class="stat">
                            <i class="fas fa-calendar"></i>
                            <span>Expected Q3 2025</span>
                        </div>
                        <div class="stat">
                            <i class="fas fa-bell"></i>
                            <span>Get notified</span>
                        </div>
                    </div>
                    
                    <div class="plugin-card-tags">
                        <span class="tag">Maintenance</span>
                        <span class="tag">Field Service</span>
                        <span class="tag">Work Orders</span>
                    </div>
                    
                    <div class="plugin-card-actions">
                        <button class="btn btn-outline btn-block" disabled>
                            <i class="fas fa-clock"></i>
                            Coming Soon
                        </button>
                        <div class="action-buttons">
                            <button class="btn btn-secondary btn-block notify-btn" data-plugin="work-order-management">
                                <i class="fas fa-bell"></i>
                                Notify When Available
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Event Management Plugin (Future) -->
                <div class="plugin-card coming-soon" data-category="coming-soon events" data-rating="0" data-downloads="0">
                    <div class="plugin-card-header">
                        <div class="plugin-badges">
                            <span class="badge badge-gray">In Planning</span>
                        </div>
                        <div class="plugin-rating">
                            <span class="rating-text">In Planning</span>
                        </div>
                    </div>
                    
                    <div class="plugin-card-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    
                    <h3 class="plugin-card-title">Event Management</h3>
                    <p class="plugin-card-description">
                        Complete event management system with registration, ticketing, and attendee communication tools.
                    </p>
                    
                    <div class="plugin-card-stats">
                        <div class="stat">
                            <i class="fas fa-lightbulb"></i>
                            <span>In development planning</span>
                        </div>
                        <div class="stat">
                            <i class="fas fa-users"></i>
                            <span>Community feedback wanted</span>
                        </div>
                    </div>
                    
                    <div class="plugin-card-tags">
                        <span class="tag">Events</span>
                        <span class="tag">Ticketing</span>
                        <span class="tag">Registration</span>
                    </div>
                    
                    <div class="plugin-card-actions">
                        <button class="btn btn-outline btn-block" disabled>
                            <i class="fas fa-lightbulb"></i>
                            In Planning
                        </button>
                        <div class="action-buttons">
                            <a href="/contact/?subject=event-management-feedback" class="btn btn-secondary btn-block">
                                <i class="fas fa-comment"></i>
                                Share Feedback
                            </a>
                        </div>
                    </div>
                </div>

            </div>

            <!-- No Results Message -->
            <div id="no-results" class="no-results" style="display: none;">
                <div class="no-results-content">
                    <i class="fas fa-search"></i>
                    <h3>No plugins found</h3>
                    <p>Try adjusting your search or filter criteria</p>
                    <button class="btn btn-primary" onclick="clearFilters()">
                        <i class="fas fa-refresh"></i>
                        Clear Filters
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter Signup -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">Stay Updated on New Plugins</h2>
                <p class="cta-description">
                    Be the first to know when we release new plugins and features. Join our newsletter for updates, tips, and exclusive offers.
                </p>
                
                <div class="newsletter-signup">
                    <form class="newsletter-form" id="newsletter-form">
                        <div class="form-group-inline">
                            <input type="email" class="form-input" placeholder="Enter your email address" required>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-envelope"></i>
                                Subscribe
                            </button>
                        </div>
                        <p class="form-note">
                            <i class="fas fa-shield-alt"></i>
                            We respect your privacy. Unsubscribe at any time.
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </section>

</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('plugin-search');
    const filterTabs = document.querySelectorAll('.filter-tab');
    const sortSelect = document.getElementById('sort-select');
    const pluginGrid = document.getElementById('plugin-grid');
    const noResults = document.getElementById('no-results');
    let currentFilter = 'all';
    let currentSort = 'popular';

    // Search functionality
    searchInput.addEventListener('input', function() {
        filterPlugins();
    });

    // Filter tabs
    filterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            filterTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.dataset.filter;
            filterPlugins();
        });
    });

    // Sort functionality
    sortSelect.addEventListener('change', function() {
        currentSort = this.value;
        sortPlugins();
    });

    // Notify buttons
    document.querySelectorAll('.notify-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const plugin = this.dataset.plugin;
            showNotificationModal(plugin);
        });
    });

    function filterPlugins() {
        const searchTerm = searchInput.value.toLowerCase();
        const cards = document.querySelectorAll('.plugin-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const title = card.querySelector('.plugin-card-title').textContent.toLowerCase();
            const description = card.querySelector('.plugin-card-description').textContent.toLowerCase();
            const tags = Array.from(card.querySelectorAll('.tag')).map(tag => tag.textContent.toLowerCase());
            const category = card.dataset.category;

            const matchesSearch = searchTerm === '' || 
                title.includes(searchTerm) || 
                description.includes(searchTerm) || 
                tags.some(tag => tag.includes(searchTerm));

            const matchesFilter = currentFilter === 'all' || 
                category.includes(currentFilter) ||
                (currentFilter === 'featured' && card.classList.contains('featured'));

            if (matchesSearch && matchesFilter) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Show/hide no results
        if (visibleCount === 0) {
            noResults.style.display = 'block';
        } else {
            noResults.style.display = 'none';
        }
    }

    function sortPlugins() {
        const cards = Array.from(document.querySelectorAll('.plugin-card'));
        
        cards.sort((a, b) => {
            switch (currentSort) {
                case 'rating':
                    return parseFloat(b.dataset.rating) - parseFloat(a.dataset.rating);
                case 'newest':
                    // For now, just reverse order (newest first)
                    return 0;
                case 'name':
                    const nameA = a.querySelector('.plugin-card-title').textContent;
                    const nameB = b.querySelector('.plugin-card-title').textContent;
                    return nameA.localeCompare(nameB);
                case 'popular':
                default:
                    return parseInt(b.dataset.downloads) - parseInt(a.dataset.downloads);
            }
        });

        // Re-append sorted cards
        cards.forEach(card => {
            pluginGrid.appendChild(card);
        });
    }

    function showNotificationModal(pluginName) {
        // Create a simple modal for email collection
        const modal = document.createElement('div');
        modal.className = 'notification-modal';
        modal.innerHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Get Notified</h3>
                    <button class="modal-close">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Enter your email to be notified when <strong>${pluginName.replace('-', ' ')}</strong> becomes available:</p>
                    <form class="notification-form">
                        <input type="email" class="form-input" placeholder="Your email address" required>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-bell"></i>
                            Notify Me
                        </button>
                    </form>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        // Close modal functionality
        modal.querySelector('.modal-close').addEventListener('click', () => {
            document.body.removeChild(modal);
        });

        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                document.body.removeChild(modal);
            }
        });

        // Form submission
        modal.querySelector('.notification-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const email = modal.querySelector('input[type="email"]').value;
            
            // Here you would typically send this to your backend
            alert('Thank you! We\'ll notify you when this plugin becomes available.');
            document.body.removeChild(modal);
        });
    }

    window.clearFilters = function() {
        searchInput.value = '';
        currentFilter = 'all';
        currentSort = 'popular';
        
        filterTabs.forEach(t => t.classList.remove('active'));
        filterTabs[0].classList.add('active');
        sortSelect.value = 'popular';
        
        filterPlugins();
        sortPlugins();
    };

    // Newsletter form
    document.getElementById('newsletter-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const email = this.querySelector('input[type="email"]').value;
        
        // Here you would typically send this to your backend
        alert('Thank you for subscribing! We\'ll keep you updated on new plugins.');
        this.reset();
    });
});
</script>

<?php get_footer(); ?>