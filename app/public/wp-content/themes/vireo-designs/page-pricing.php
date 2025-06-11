<?php
/**
 * Template Name: Enhanced Pricing Page
 * Professional pricing page with component library
 */

get_header(); ?>

<main id="primary" class="site-main">
    
    <!-- Page Header -->
    <section class="page-hero" style="background: var(--nature-gradient); color: white;">
        <div class="container">
            <div class="page-hero-content" style="text-align: center; padding: 6rem 0;">
                <h1 class="page-hero-title" style="color: white; font-size: 3rem; margin-bottom: 1.5rem;">
                    Simple, Transparent <span style="color: var(--vireo-mint);">Pricing</span>
                </h1>
                <p class="page-hero-description" style="color: rgba(255,255,255,0.9); font-size: 1.25rem; max-width: 600px; margin: 0 auto;">
                    Choose the plan that fits your needs. Start free and upgrade when you're ready for more power.
                </p>
            </div>
        </div>
    </section>

    <!-- Pricing Cards -->
    <section style="padding: 6rem 0; background: var(--gray-50);">
        <div class="container">
            <div class="pricing-grid">
                
                <!-- Free Plan -->
                <div class="pricing-card">
                    <div class="pricing-card-header">
                        <h3>Free</h3>
                    </div>
                    <div class="pricing-card-price">$0</div>
                    <div class="pricing-card-period">Forever</div>
                    
                    <ul class="pricing-card-features">
                        <li>Up to 10 properties</li>
                        <li>Basic tenant management</li>
                        <li>Lease tracking</li>
                        <li>Maintenance requests</li>
                        <li>Basic reporting</li>
                        <li>Community support</li>
                        <li class="unavailable">Payment automation</li>
                        <li class="unavailable">Advanced analytics</li>
                    </ul>
                    
                    <div class="pricing-card-action">
                        <a href="/plugins/property-management/" class="btn btn-outline btn-block">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M5 20h14v-2H5v2zM19 9h-4V3H9v6H5l7 7 7-7z"/>
                            </svg>
                            Download Free
                        </a>
                    </div>
                </div>

                <!-- Pro Plan -->
                <div class="pricing-card featured" style="border: 2px solid var(--primary-color); position: relative;">
                    <div style="position: absolute; top: -12px; left: 50%; transform: translateX(-50%); background: var(--primary-color); color: white; padding: 6px 20px; border-radius: 20px; font-size: 0.875rem; font-weight: 600;">
                        Most Popular
                    </div>
                    <div class="pricing-card-header">
                        <h3>Pro</h3>
                    </div>
                    <div class="pricing-card-price" style="color: var(--primary-color); font-size: 3rem; font-weight: 700;">$99</div>
                    <div class="pricing-card-period">per year</div>
                    
                    <ul class="pricing-card-features">
                        <li>Everything in Free</li>
                        <li>Unlimited properties</li>
                        <li>Advanced tenant management</li>
                        <li>Payment automation</li>
                        <li>Advanced analytics & charts</li>
                        <li>Custom workflows</li>
                        <li>Priority support</li>
                        <li>White-label options</li>
                    </ul>
                    
                    <div class="pricing-card-action">
                        <a href="/pricing/" class="btn btn-primary btn-block">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M13 9V3.5L23.5 12 13 20.5V15c-5.5 0-10 1.5-10 7 0-5 1.5-10 10-12z"/>
                            </svg>
                            Get Started
                        </a>
                    </div>
                </div>

                <!-- Enterprise Plan -->
                <div class="pricing-card">
                    <div class="pricing-card-header">
                        <h3>Enterprise</h3>
                    </div>
                    <div class="pricing-card-price">Custom</div>
                    <div class="pricing-card-period">Contact us</div>
                    
                    <ul class="pricing-card-features">
                        <li>Everything in Pro</li>
                        <li>Custom integrations</li>
                        <li>Multi-site licenses</li>
                        <li>Dedicated support</li>
                        <li>SLA guarantees</li>
                        <li>Training & onboarding</li>
                        <li>Custom development</li>
                        <li>API access</li>
                    </ul>
                    
                    <div class="pricing-card-action">
                        <a href="/contact/" class="btn btn-outline btn-block">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                            </svg>
                            Contact Sales
                        </a>
                    </div>
                </div>
                
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section style="padding: 6rem 0; background: white;">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Frequently Asked Questions</h2>
                <p class="section-description">
                    Everything you need to know about our pricing and plans
                </p>
            </div>
            
            <div class="faq-container">
                <div class="faq-item">
                    <button class="faq-question">
                        Can I upgrade or downgrade my plan at any time?
                    </button>
                    <div class="faq-answer">
                        <p>Yes! You can upgrade to Pro at any time directly through your WordPress admin dashboard. If you need to downgrade or have specific requirements, contact our support team and we'll help you transition smoothly.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        What's included in priority support?
                    </button>
                    <div class="faq-answer">
                        <p>Pro customers get priority email support with response times under 24 hours during business days. Enterprise customers receive dedicated support channels and can schedule one-on-one calls with our development team.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        Do you offer refunds?
                    </button>
                    <div class="faq-answer">
                        <p>We offer a 30-day money-back guarantee on all Pro plans. If you're not satisfied, contact us within 30 days for a full refund. No questions asked.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        Can I use the Pro plugin on multiple sites?
                    </button>
                    <div class="faq-answer">
                        <p>Each Pro license covers one WordPress installation. For multiple sites, you can purchase additional licenses or contact us about Enterprise pricing for bulk discounts.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        What payment methods do you accept?
                    </button>
                    <div class="faq-answer">
                        <p>We accept all major credit cards (Visa, MasterCard, American Express) and PayPal. Enterprise customers can also pay by bank transfer or check.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Guarantee Section -->
    <section style="padding: 6rem 0; background: var(--primary-gradient); color: white;">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title" style="color: white;">30-Day Money-Back Guarantee</h2>
                <p class="section-description" style="color: rgba(255,255,255,0.9);">
                    Try Vireo Pro risk-free. If you're not completely satisfied, we'll refund your purchase within 30 days.
                </p>
            </div>
            
            <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem; text-align: center; margin-top: 3rem;">
                <div class="stat-item">
                    <div class="stat-number" style="color: var(--vireo-mint); font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem;">2025</div>
                    <div class="stat-label" style="color: rgba(255,255,255,0.9);">New Studio Launch</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" style="color: var(--vireo-mint); font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem;">100%</div>
                    <div class="stat-label" style="color: rgba(255,255,255,0.9);">WordPress Native</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" style="color: var(--vireo-mint); font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem;">Free</div>
                    <div class="stat-label" style="color: rgba(255,255,255,0.9);">Core Features</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" style="color: var(--vireo-mint); font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem;">Open</div>
                    <div class="stat-label" style="color: rgba(255,255,255,0.9);">Source Foundation</div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section style="padding: 6rem 0; background: var(--gray-50);">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Ready to Get Started?</h2>
                <p class="section-description">
                    Be among the first to experience our innovative WordPress plugins designed for modern business needs.
                </p>
            </div>
            
            <div style="text-align: center;">
                <div style="display: flex; justify-content: center; gap: var(--space-4); margin-bottom: var(--space-8);">
                    <a href="/plugins/property-management/" class="btn btn-outline btn-xl">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M5 20h14v-2H5v2zM19 9h-4V3H9v6H5l7 7 7-7z"/>
                        </svg>
                        Start Free
                    </a>
                    <a href="/pricing/" class="btn btn-primary btn-xl">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M13 9V3.5L23.5 12 13 20.5V15c-5.5 0-10 1.5-10 7 0-5 1.5-10 10-12z"/>
                        </svg>
                        Get Pro
                    </a>
                </div>
                
                <p style="color: var(--gray-500); font-size: var(--text-sm);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="vertical-align: middle; margin-right: var(--space-1);">
                        <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
                    </svg>
                    30-day money-back guarantee • No setup fees • Cancel anytime
                </p>
            </div>
        </div>
    </section>

</main>

<script>
// FAQ Accordion functionality
document.addEventListener('DOMContentLoaded', function() {
    const faqItems = document.querySelectorAll('.faq-item');
    
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        const answer = item.querySelector('.faq-answer');
        
        question.addEventListener('click', function() {
            const isActive = item.classList.contains('active');
            
            // Close all other items
            faqItems.forEach(otherItem => {
                if (otherItem !== item) {
                    otherItem.classList.remove('active');
                    otherItem.querySelector('.faq-answer').style.display = 'none';
                }
            });
            
            // Toggle current item
            if (isActive) {
                item.classList.remove('active');
                answer.style.display = 'none';
            } else {
                item.classList.add('active');
                answer.style.display = 'block';
            }
        });
    });
});
</script>

<?php get_footer(); ?>