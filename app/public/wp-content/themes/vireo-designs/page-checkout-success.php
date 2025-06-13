<?php
/**
 * Template Name: Checkout Success
 * Success page after Stripe checkout
 */

get_header(); ?>

<main id="primary" class="site-main">
    
    <!-- Success Header -->
    <section class="hero-section" style="padding: 6rem 0; background: linear-gradient(135deg, var(--success-color) 0%, #059669 100%);">
        <div class="hero-background">
            <div class="hero-pattern"></div>
        </div>
        <div class="container">
            <div class="hero-content" style="text-align: center; color: white;">
                <div class="success-icon" style="font-size: 4rem; margin-bottom: 2rem;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h1 class="hero-title" style="color: white;">
                    Purchase Successful!
                </h1>
                <p class="hero-description" style="font-size: 1.25rem; opacity: 0.9;">
                    Thank you for your purchase. Your order has been processed successfully.
                </p>
            </div>
        </div>
    </section>

    <!-- Order Details -->
    <section style="padding: 4rem 0; background: var(--gray-50);">
        <div class="container">
            <div style="max-width: 600px; margin: 0 auto; background: white; border-radius: 12px; padding: 3rem; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                
                <div id="order-details">
                    <div style="text-align: center; margin-bottom: 2rem;">
                        <i class="fas fa-hourglass-half" style="font-size: 2rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
                        <p>Loading your order details...</p>
                    </div>
                </div>
                
                <!-- Default content when session info loads -->
                <div id="order-content" style="display: none;">
                    <h3 style="margin-bottom: 2rem; text-align: center; color: var(--gray-900);">Order Confirmation</h3>
                    
                    <div class="order-info">
                        <div style="border: 1px solid var(--gray-300); border-radius: 8px; padding: 1.5rem; margin-bottom: 2rem;">
                            <h4 style="margin: 0 0 1rem 0; color: var(--gray-900);">What's Next?</h4>
                            <ul style="margin: 0; padding-left: 1.5rem; color: var(--gray-600);">
                                <li style="margin-bottom: 0.5rem;">Check your email for download instructions</li>
                                <li style="margin-bottom: 0.5rem;">Your license key will be available in your account dashboard</li>
                                <li style="margin-bottom: 0.5rem;">Visit our documentation for setup guides</li>
                                <li>Contact support if you need any assistance</li>
                            </ul>
                        </div>
                        
                        <div class="action-buttons" style="text-align: center;">
                            <a href="/my-account/" class="btn btn-primary" style="margin-right: 1rem;">
                                <i class="fas fa-user"></i>
                                View Account
                            </a>
                            <a href="/support/" class="btn btn-outline">
                                <i class="fas fa-life-ring"></i>
                                Get Support
                            </a>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </section>

    <!-- Additional Resources -->
    <section style="padding: 4rem 0; background: white;">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Getting Started</h2>
                <p class="section-description">
                    Resources to help you make the most of your new plugin
                </p>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-top: 3rem;">
                
                <div style="text-align: center; padding: 2rem; border: 1px solid var(--gray-200); border-radius: 12px;">
                    <div style="width: 60px; height: 60px; background: var(--primary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                        <i class="fas fa-download" style="color: white; font-size: 1.5rem;"></i>
                    </div>
                    <h4 style="margin-bottom: 1rem;">Download & Install</h4>
                    <p style="color: var(--gray-600); margin-bottom: 1.5rem;">Get your plugin files and follow our step-by-step installation guide.</p>
                    <a href="/docs/installation/" class="btn btn-outline">View Guide</a>
                </div>
                
                <div style="text-align: center; padding: 2rem; border: 1px solid var(--gray-200); border-radius: 12px;">
                    <div style="width: 60px; height: 60px; background: var(--secondary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                        <i class="fas fa-cog" style="color: white; font-size: 1.5rem;"></i>
                    </div>
                    <h4 style="margin-bottom: 1rem;">Configuration</h4>
                    <p style="color: var(--gray-600); margin-bottom: 1.5rem;">Learn how to configure your plugin settings for optimal performance.</p>
                    <a href="/docs/configuration/" class="btn btn-outline">Learn More</a>
                </div>
                
                <div style="text-align: center; padding: 2rem; border: 1px solid var(--gray-200); border-radius: 12px;">
                    <div style="width: 60px; height: 60px; background: var(--success-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                        <i class="fas fa-headset" style="color: white; font-size: 1.5rem;"></i>
                    </div>
                    <h4 style="margin-bottom: 1rem;">Premium Support</h4>
                    <p style="color: var(--gray-600); margin-bottom: 1.5rem;">Access our priority support team for personalized assistance.</p>
                    <a href="/support/" class="btn btn-outline">Contact Support</a>
                </div>
                
            </div>
        </div>
    </section>

</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show the order content
    setTimeout(function() {
        document.getElementById('order-details').style.display = 'none';
        document.getElementById('order-content').style.display = 'block';
    }, 1500);
});
</script>

<?php get_footer(); ?>