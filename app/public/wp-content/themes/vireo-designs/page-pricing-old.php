<?php
/**
 * Template Name: Pricing Page
 * Professional pricing page
 */

get_header(); ?>

<main id="primary" class="site-main">
    
    <!-- Page Header -->
    <section class="hero-section" style="padding: 4rem 0;">
        <div class="hero-background">
            <div class="hero-pattern"></div>
        </div>
        <div class="container">
            <div class="hero-content" style="grid-template-columns: 1fr; text-align: center;">
                <div class="hero-text">
                    <h1 class="hero-title" style="font-size: 3rem;">
                        Simple, Transparent <span class="text-gradient">Pricing</span>
                    </h1>
                    <p class="hero-description">
                        Choose the plan that fits your needs. Start free and upgrade when you're ready for more power.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Cards -->
    <section style="padding: 6rem 0; background: var(--gray-50);">
        <div class="container">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 2rem; max-width: 1000px; margin: 0 auto;">
                
                <!-- Free Plan -->
                <div style="background: white; border: 2px solid var(--gray-200); border-radius: 12px; padding: 2rem; text-align: center; position: relative;">
                    <h3 style="font-size: 1.5rem; margin-bottom: 1rem; color: var(--gray-900);">Free</h3>
                    <div style="font-size: 3rem; font-weight: 700; color: var(--gray-700); margin: 1rem 0; line-height: 1;">$0</div>
                    <div style="color: var(--gray-500); margin-bottom: 2rem;">Forever</div>
                    
                    <ul style="list-style: none; padding: 0; margin: 2rem 0; text-align: left;">
                        <li style="padding: 0.75rem 0; border-bottom: 1px solid var(--gray-100); position: relative; padding-left: 2rem;">
                            <i class="fas fa-check" style="position: absolute; left: 0; color: var(--success-color); font-weight: bold;"></i>
                            Up to 10 properties
                        </li>
                        <li style="padding: 0.75rem 0; border-bottom: 1px solid var(--gray-100); position: relative; padding-left: 2rem;">
                            <i class="fas fa-check" style="position: absolute; left: 0; color: var(--success-color); font-weight: bold;"></i>
                            Basic tenant management
                        </li>
                        <li style="padding: 0.75rem 0; border-bottom: 1px solid var(--gray-100); position: relative; padding-left: 2rem;">
                            <i class="fas fa-check" style="position: absolute; left: 0; color: var(--success-color); font-weight: bold;"></i>
                            Maintenance requests
                        </li>
                        <li style="padding: 0.75rem 0; border-bottom: 1px solid var(--gray-100); position: relative; padding-left: 2rem;">
                            <i class="fas fa-check" style="position: absolute; left: 0; color: var(--success-color); font-weight: bold;"></i>
                            Basic reporting
                        </li>
                        <li style="padding: 0.75rem 0; border-bottom: 1px solid var(--gray-100); position: relative; padding-left: 2rem;">
                            <i class="fas fa-check" style="position: absolute; left: 0; color: var(--success-color); font-weight: bold;"></i>
                            Community support
                        </li>
                    </ul>
                    
                    <a href="/shop/" class="btn btn-outline" style="width: 100%; justify-content: center;">Get Started Free</a>
                </div>

                <!-- Pro Plan -->
                <div style="background: white; border: 2px solid var(--primary-color); border-radius: 12px; padding: 2rem; text-align: center; position: relative; transform: scale(1.05);">
                    <div style="position: absolute; top: -12px; left: 50%; transform: translateX(-50%); background: var(--primary-color); color: white; padding: 0.5rem 1.5rem; border-radius: 50px; font-size: 0.875rem; font-weight: 600;">Most Popular</div>
                    
                    <h3 style="font-size: 1.5rem; margin-bottom: 1rem; color: var(--gray-900);">Pro</h3>
                    <div style="font-size: 3rem; font-weight: 700; color: var(--primary-color); margin: 1rem 0; line-height: 1;">$99</div>
                    <div style="color: var(--gray-500); margin-bottom: 2rem;">per year</div>
                    
                    <ul style="list-style: none; padding: 0; margin: 2rem 0; text-align: left;">
                        <li style="padding: 0.75rem 0; border-bottom: 1px solid var(--gray-100); position: relative; padding-left: 2rem;">
                            <i class="fas fa-check" style="position: absolute; left: 0; color: var(--success-color); font-weight: bold;"></i>
                            Unlimited properties
                        </li>
                        <li style="padding: 0.75rem 0; border-bottom: 1px solid var(--gray-100); position: relative; padding-left: 2rem;">
                            <i class="fas fa-check" style="position: absolute; left: 0; color: var(--success-color); font-weight: bold;"></i>
                            Advanced analytics
                        </li>
                        <li style="padding: 0.75rem 0; border-bottom: 1px solid var(--gray-100); position: relative; padding-left: 2rem;">
                            <i class="fas fa-check" style="position: absolute; left: 0; color: var(--success-color); font-weight: bold;"></i>
                            Payment automation
                        </li>
                        <li style="padding: 0.75rem 0; border-bottom: 1px solid var(--gray-100); position: relative; padding-left: 2rem;">
                            <i class="fas fa-check" style="position: absolute; left: 0; color: var(--success-color); font-weight: bold;"></i>
                            Custom workflows
                        </li>
                        <li style="padding: 0.75rem 0; border-bottom: 1px solid var(--gray-100); position: relative; padding-left: 2rem;">
                            <i class="fas fa-check" style="position: absolute; left: 0; color: var(--success-color); font-weight: bold;"></i>
                            Priority support
                        </li>
                        <li style="padding: 0.75rem 0; border-bottom: 1px solid var(--gray-100); position: relative; padding-left: 2rem;">
                            <i class="fas fa-check" style="position: absolute; left: 0; color: var(--success-color); font-weight: bold;"></i>
                            White-label options
                        </li>
                    </ul>
                    
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <button class="btn btn-primary checkout-btn" 
                                data-price-id="<?php echo defined('PROPERTY_MANAGEMENT_PRO_ANNUAL_PRICE_ID') ? PROPERTY_MANAGEMENT_PRO_ANNUAL_PRICE_ID : ''; ?>"
                                data-product-name="Property Management Pro (Annual)"
                                style="width: 100%; justify-content: center;">
                            <i class="fas fa-credit-card"></i>
                            Get Pro - $99/year
                        </button>
                        <button class="btn btn-outline checkout-btn" 
                                data-price-id="<?php echo defined('PROPERTY_MANAGEMENT_PRO_ONETIME_PRICE_ID') ? PROPERTY_MANAGEMENT_PRO_ONETIME_PRICE_ID : ''; ?>"
                                data-product-name="Property Management Pro (Lifetime)"
                                style="width: 100%; justify-content: center;">
                            <i class="fas fa-infinity"></i>
                            Lifetime - $149
                        </button>
                    </div>
                </div>

                <!-- Enterprise Plan -->
                <div style="background: white; border: 2px solid var(--gray-200); border-radius: 12px; padding: 2rem; text-align: center; position: relative;">
                    <h3 style="font-size: 1.5rem; margin-bottom: 1rem; color: var(--gray-900);">Enterprise</h3>
                    <div style="font-size: 3rem; font-weight: 700; color: var(--gray-700); margin: 1rem 0; line-height: 1;">Custom</div>
                    <div style="color: var(--gray-500); margin-bottom: 2rem;">Contact us</div>
                    
                    <ul style="list-style: none; padding: 0; margin: 2rem 0; text-align: left;">
                        <li style="padding: 0.75rem 0; border-bottom: 1px solid var(--gray-100); position: relative; padding-left: 2rem;">
                            <i class="fas fa-check" style="position: absolute; left: 0; color: var(--success-color); font-weight: bold;"></i>
                            Everything in Pro
                        </li>
                        <li style="padding: 0.75rem 0; border-bottom: 1px solid var(--gray-100); position: relative; padding-left: 2rem;">
                            <i class="fas fa-check" style="position: absolute; left: 0; color: var(--success-color); font-weight: bold;"></i>
                            Custom development
                        </li>
                        <li style="padding: 0.75rem 0; border-bottom: 1px solid var(--gray-100); position: relative; padding-left: 2rem;">
                            <i class="fas fa-check" style="position: absolute; left: 0; color: var(--success-color); font-weight: bold;"></i>
                            Dedicated support
                        </li>
                        <li style="padding: 0.75rem 0; border-bottom: 1px solid var(--gray-100); position: relative; padding-left: 2rem;">
                            <i class="fas fa-check" style="position: absolute; left: 0; color: var(--success-color); font-weight: bold;"></i>
                            SLA guarantee
                        </li>
                        <li style="padding: 0.75rem 0; border-bottom: 1px solid var(--gray-100); position: relative; padding-left: 2rem;">
                            <i class="fas fa-check" style="position: absolute; left: 0; color: var(--success-color); font-weight: bold;"></i>
                            Multi-site licensing
                        </li>
                    </ul>
                    
                    <a href="/contact/" class="btn btn-outline" style="width: 100%; justify-content: center;">Contact Sales</a>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section style="padding: 6rem 0; background: white;">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Frequently Asked Questions</h2>
            </div>
            
            <div style="max-width: 800px; margin: 0 auto;">
                <div style="background: white; border: 1px solid var(--gray-200); border-radius: 8px; margin-bottom: 1rem;">
                    <div style="padding: 1.5rem; border-bottom: 1px solid var(--gray-200);">
                        <h4 style="margin: 0; color: var(--gray-900);">Can I upgrade or downgrade at any time?</h4>
                    </div>
                    <div style="padding: 1.5rem; color: var(--gray-600);">
                        Yes! You can upgrade to Pro at any time, and we'll pro-rate the billing. You can also downgrade at the end of your billing cycle.
                    </div>
                </div>
                
                <div style="background: white; border: 1px solid var(--gray-200); border-radius: 8px; margin-bottom: 1rem;">
                    <div style="padding: 1.5rem; border-bottom: 1px solid var(--gray-200);">
                        <h4 style="margin: 0; color: var(--gray-900);">Is there a free trial for Pro features?</h4>
                    </div>
                    <div style="padding: 1.5rem; color: var(--gray-600);">
                        We offer a 30-day money-back guarantee on all Pro purchases. Try it risk-free!
                    </div>
                </div>
                
                <div style="background: white; border: 1px solid var(--gray-200); border-radius: 8px; margin-bottom: 1rem;">
                    <div style="padding: 1.5rem; border-bottom: 1px solid var(--gray-200);">
                        <h4 style="margin: 0; color: var(--gray-900);">Do you offer discounts for non-profits?</h4>
                    </div>
                    <div style="padding: 1.5rem; color: var(--gray-600);">
                        Yes! We offer a 50% discount for verified non-profit organizations. Contact us for details.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">Ready to Get Started?</h2>
                <p class="cta-description">
                    Join thousands of property managers who trust Vireo Designs.
                </p>
                <div class="cta-actions">
                    <a href="/shop/" class="btn btn-primary btn-xl">
                        <i class="fas fa-download"></i>
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