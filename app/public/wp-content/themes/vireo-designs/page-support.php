<?php
/**
 * Template Name: Support Page
 * Professional support page
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
                    Get <span class="text-gradient">Support</span>
                </h1>
                <p class="page-hero-description">
                    Find answers, get help, and learn how to make the most of our plugins.
                </p>
            </div>
        </div>
    </section>

    <!-- Support Options -->
    <section class="features-overview">
        <div class="container">
            <div class="content-grid">
                
                <!-- Documentation -->
                <div class="content-card">
                    <div class="content-card-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <h3 class="content-card-title">Documentation</h3>
                    <p class="content-card-description">
                        Comprehensive guides, tutorials, and API documentation.
                    </p>
                    <a href="#" class="btn btn-outline btn-block">Browse Docs</a>
                </div>

                <!-- Community Forum -->
                <div class="content-card">
                    <div class="content-card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="content-card-title">Community Forum</h3>
                    <p class="content-card-description">
                        Connect with other users and get help from the community.
                    </p>
                    <a href="#" class="btn btn-outline btn-block">Join Forum</a>
                </div>

                <!-- Priority Support -->
                <div class="content-card">
                    <div class="content-card-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3 class="content-card-title">Priority Support</h3>
                    <p class="content-card-description">
                        Direct support for Pro users with guaranteed response times.
                    </p>
                    <a href="/contact/" class="btn btn-primary btn-block">Contact Support</a>
                </div>
            </div>

            <!-- FAQ Section -->
            <div class="faq-section">
                <h2 class="section-title">Frequently Asked Questions</h2>
                
                <div class="faq-container">
                    
                    <!-- Installation -->
                    <div class="faq-item">
                        <button class="faq-question">
                            How do I install the plugin?
                        </button>
                        <div class="faq-answer">
                            <p>You can install our plugins through the WordPress admin dashboard. Go to Plugins → Add New, search for "Vireo", and click Install. Alternatively, download the plugin from our website and upload it manually.</p>
                        </div>
                    </div>

                    <!-- Free vs Pro -->
                    <div class="faq-item">
                        <button class="faq-question">
                            What's the difference between Free and Pro?
                        </button>
                        <div class="faq-answer">
                            <p>The free version includes all basic functionality like property management, tenant tracking, and basic reporting. Pro adds advanced analytics, payment automation, priority support, and white-label options. <a href="/pricing/">See full comparison</a>.</p>
                        </div>
                    </div>

                    <!-- Licensing -->
                    <div class="faq-item">
                        <button class="faq-question">
                            How does Pro licensing work?
                        </button>
                        <div class="faq-answer">
                            <p>Pro licenses are annual subscriptions that include updates, support, and access to Pro features. You can use the license on one website. Need multiple sites? Contact us for volume pricing.</p>
                        </div>
                    </div>

                    <!-- Compatibility -->
                    <div class="faq-item">
                        <button class="faq-question">
                            Are your plugins compatible with my theme?
                        </button>
                        <div class="faq-answer">
                            <p>Yes! Our plugins are built to work with any properly coded WordPress theme. We follow WordPress coding standards and use standard hooks and filters. If you experience any compatibility issues, our support team is here to help.</p>
                        </div>
                    </div>

                    <!-- Customization -->
                    <div class="faq-item">
                        <button class="faq-question">
                            Can you customize the plugin for my needs?
                        </button>
                        <div class="faq-answer">
                            <p>Absolutely! We offer custom development services for businesses that need specific functionality. <a href="/contact/">Contact us</a> with your requirements and we'll provide a quote.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Support CTA -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">Still Need Help?</h2>
                <p class="cta-description">
                    Our support team is here to help you succeed with our plugins.
                </p>
                <div class="cta-actions">
                    <a href="/contact/" class="btn btn-primary btn-xl">
                        <i class="fas fa-envelope"></i>
                        Contact Support
                    </a>
                    <a href="#" class="btn btn-outline btn-xl">
                        <i class="fas fa-book"></i>
                        Browse Documentation
                    </a>
                </div>
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

<style>
@media (max-width: 768px) {
    .container > div[style*="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr))"] {
        grid-template-columns: 1fr !important;
    }
}
</style>

<?php get_footer(); ?>