<?php
/**
 * Template Name: Enhanced Contact Page
 * Professional contact page with component library
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
                    Get in <span class="text-gradient">Touch</span>
                </h1>
                <p class="page-hero-description">
                    Have questions about our plugins? Need custom development? We're here to help you succeed.
                </p>
            </div>
        </div>
    </section>

    <!-- Contact Form and Info -->
    <section style="padding: 6rem 0; background: var(--gray-50);">
        <div class="container">
            <div class="form-grid">
                
                <!-- Contact Form -->
                <div class="form-container">
                    <h2 style="font-size: var(--text-2xl); margin-bottom: var(--space-6); color: var(--gray-900);">Send us a message</h2>
                    
                    <?php
                    // Display success/error messages
                    if (isset($_GET['contact'])) {
                        if ($_GET['contact'] === 'success') {
                            echo '<div class="form-success">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" style="vertical-align: middle; margin-right: var(--space-2);">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                    </svg>
                                    Thank you! Your message has been sent successfully.
                                  </div>';
                        } elseif ($_GET['contact'] === 'error') {
                            echo '<div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: var(--radius-lg); padding: var(--space-4); color: var(--error-color); font-weight: 500; margin-bottom: var(--space-6);">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" style="vertical-align: middle; margin-right: var(--space-2);">
                                        <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/>
                                    </svg>
                                    Sorry, there was an error sending your message. Please try again.
                                  </div>';
                        }
                    }
                    ?>
                    
                    <form method="post" action="">
                        <?php wp_nonce_field('vireo_contact_form', 'contact_nonce'); ?>
                        
                        <div class="form-group">
                            <label for="contact_name" class="form-label">Full Name *</label>
                            <input type="text" id="contact_name" name="contact_name" class="form-input" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="contact_email" class="form-label">Email Address *</label>
                            <input type="email" id="contact_email" name="contact_email" class="form-input" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="contact_company" class="form-label">Company (Optional)</label>
                            <input type="text" id="contact_company" name="contact_company" class="form-input">
                        </div>
                        
                        <div class="form-group">
                            <label for="contact_subject" class="form-label">How can we help? *</label>
                            <select id="contact_subject" name="contact_subject" class="form-select" required>
                                <option value="">Select a topic</option>
                                <option value="general">General Inquiry</option>
                                <option value="support">Plugin Support</option>
                                <option value="sales">Sales & Pricing</option>
                                <option value="custom">Custom Development</option>
                                <option value="partnership">Partnership Opportunity</option>
                                <option value="enterprise">Enterprise Solutions</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="contact_message" class="form-label">Message *</label>
                            <textarea id="contact_message" name="contact_message" class="form-textarea" required placeholder="Tell us more about your project or question..."></textarea>
                        </div>
                        
                        <button type="submit" name="submit_contact_form" class="btn btn-primary btn-block">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                            </svg>
                            Send Message
                        </button>
                    </form>
                </div>

                <!-- Contact Info -->
                <div>
                    <h2 style="font-size: var(--text-2xl); margin-bottom: var(--space-6); color: var(--gray-900);">Other ways to reach us</h2>
                    
                    <div class="content-grid" style="gap: var(--space-6);">
                        
                        <!-- Support -->
                        <div class="content-card">
                            <div class="content-card-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                            </div>
                            <h3 class="content-card-title">Plugin Support</h3>
                            <p class="content-card-description">Need help with installation, configuration, or troubleshooting? Our support team is here to help.</p>
                            <a href="/support/" class="btn btn-outline btn-sm">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M13 9V3.5L23.5 12 13 20.5V15c-5.5 0-10 1.5-10 7 0-5 1.5-10 10-12z"/>
                                </svg>
                                Visit Support Center
                            </a>
                        </div>

                        <!-- Sales -->
                        <div class="content-card">
                            <div class="content-card-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>
                                </svg>
                            </div>
                            <h3 class="content-card-title">Sales & Pricing</h3>
                            <p class="content-card-description">Questions about our Pro plans, enterprise pricing, or need a custom quote?</p>
                            <a href="mailto:sales@vireodesigns.com" class="btn btn-outline btn-sm">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                                </svg>
                                Email Sales Team
                            </a>
                        </div>

                        <!-- Development -->
                        <div class="content-card">
                            <div class="content-card-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M9.4 16.6L4.8 12l4.6-4.6L8 6l-6 6 6 6 1.4-1.4zm5.2 0L19.2 12l-4.6-4.6L16 6l6 6-6 6-1.4-1.4z"/>
                                </svg>
                            </div>
                            <h3 class="content-card-title">Custom Development</h3>
                            <p class="content-card-description">Need a custom plugin or modifications to existing ones? Let's discuss your project.</p>
                            <a href="mailto:dev@vireodesigns.com" class="btn btn-outline btn-sm">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                                </svg>
                                Contact Developers
                            </a>
                        </div>

                    </div>

                    <!-- Response Time Info -->
                    <div style="margin-top: var(--space-8); padding: var(--space-6); background: var(--white); border: 1px solid var(--gray-200); border-radius: var(--radius-xl);">
                        <h3 style="font-size: var(--text-lg); margin-bottom: var(--space-4); color: var(--gray-900);">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" style="vertical-align: middle; margin-right: var(--space-2); color: var(--primary-color);">
                                <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"/>
                                <path d="M12.5 7H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                            </svg>
                            Response Times
                        </h3>
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li style="padding: var(--space-2) 0; border-bottom: 1px solid var(--gray-100); display: flex; justify-content: space-between;">
                                <span>General inquiries</span>
                                <strong style="color: var(--primary-color);">24-48 hours</strong>
                            </li>
                            <li style="padding: var(--space-2) 0; border-bottom: 1px solid var(--gray-100); display: flex; justify-content: space-between;">
                                <span>Pro customer support</span>
                                <strong style="color: var(--primary-color);">12-24 hours</strong>
                            </li>
                            <li style="padding: var(--space-2) 0; display: flex; justify-content: space-between;">
                                <span>Enterprise customers</span>
                                <strong style="color: var(--primary-color);">2-4 hours</strong>
                            </li>
                        </ul>
                    </div>
                </div>
                
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section style="padding: 6rem 0; background: white;">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Common Questions</h2>
                <p class="section-description">
                    Quick answers to frequently asked questions
                </p>
            </div>
            
            <div class="faq-container">
                <div class="faq-item">
                    <button class="faq-question">
                        Do you offer free consultations?
                    </button>
                    <div class="faq-answer">
                        <p>Yes! We offer free 30-minute consultations to discuss your project requirements, answer questions about our plugins, and provide recommendations for your specific use case.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        What's your custom development process?
                    </button>
                    <div class="faq-answer">
                        <p>Our custom development follows a structured process: discovery call, detailed proposal, development in phases with regular check-ins, testing, and ongoing support. Projects typically range from 2-12 weeks depending on complexity.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        Do you provide ongoing maintenance?
                    </button>
                    <div class="faq-answer">
                        <p>Yes, we offer maintenance packages for custom projects including updates, security patches, WordPress compatibility, and feature enhancements. Pro plugin customers receive ongoing updates as part of their subscription.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        Can you integrate with third-party services?
                    </button>
                    <div class="faq-answer">
                        <p>Absolutely! We have experience integrating with payment processors, CRMs, email marketing platforms, accounting software, and many other business tools. Let us know what you need to connect.</p>
                    </div>
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

<?php get_footer(); ?>