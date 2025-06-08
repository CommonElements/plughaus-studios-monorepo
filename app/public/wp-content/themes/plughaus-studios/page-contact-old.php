<?php
/**
 * Template Name: Contact Page
 * Professional contact page
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
                        Get in <span class="text-gradient">Touch</span>
                    </h1>
                    <p class="hero-description">
                        Have questions about our plugins? Need custom development? We're here to help.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form and Info -->
    <section style="padding: 6rem 0; background: var(--gray-50);">
        <div class="container">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: start;">
                
                <!-- Contact Form -->
                <div style="background: white; padding: 3rem; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                    <h2 style="font-size: 1.75rem; margin-bottom: 1.5rem; color: var(--gray-900);">Send us a message</h2>
                    
                    <?php
                    // Display success/error messages
                    if (isset($_GET['contact'])) {
                        if ($_GET['contact'] === 'success') {
                            echo '<div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
                                    <i class="fas fa-check-circle"></i> Thank you! Your message has been sent successfully.
                                  </div>';
                        } elseif ($_GET['contact'] === 'error') {
                            echo '<div style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
                                    <i class="fas fa-exclamation-circle"></i> Sorry, there was an error sending your message. Please try again.
                                  </div>';
                        }
                    }
                    ?>
                    
                    <form class="plughaus-contact-form" method="post" action="">
                        <?php wp_nonce_field('plughaus_contact_form', 'contact_nonce'); ?>
                        
                        <div style="margin-bottom: 1.5rem;">
                            <label for="contact_name" style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: var(--gray-700);">Name *</label>
                            <input type="text" id="contact_name" name="contact_name" required style="width: 100%; padding: 0.75rem; border: 2px solid var(--gray-300); border-radius: 8px; font-size: 1rem;">
                        </div>
                        
                        <div style="margin-bottom: 1.5rem;">
                            <label for="contact_email" style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: var(--gray-700);">Email *</label>
                            <input type="email" id="contact_email" name="contact_email" required style="width: 100%; padding: 0.75rem; border: 2px solid var(--gray-300); border-radius: 8px; font-size: 1rem;">
                        </div>
                        
                        <div style="margin-bottom: 1.5rem;">
                            <label for="contact_subject" style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: var(--gray-700);">Subject *</label>
                            <select id="contact_subject" name="contact_subject" required style="width: 100%; padding: 0.75rem; border: 2px solid var(--gray-300); border-radius: 8px; font-size: 1rem;">
                                <option value="">Select a topic</option>
                                <option value="general">General Inquiry</option>
                                <option value="support">Plugin Support</option>
                                <option value="custom">Custom Development</option>
                                <option value="partnership">Partnership</option>
                                <option value="billing">Billing Question</option>
                            </select>
                        </div>
                        
                        <div style="margin-bottom: 2rem;">
                            <label for="contact_message" style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: var(--gray-700);">Message *</label>
                            <textarea id="contact_message" name="contact_message" rows="5" required style="width: 100%; padding: 0.75rem; border: 2px solid var(--gray-300); border-radius: 8px; font-size: 1rem; resize: vertical;"></textarea>
                        </div>
                        
                        <button type="submit" name="submit_contact_form" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 1rem;">
                            <i class="fas fa-paper-plane"></i>
                            Send Message
                        </button>
                    </form>
                </div>

                <!-- Contact Information -->
                <div>
                    <h2 style="font-size: 1.75rem; margin-bottom: 2rem; color: var(--gray-900);">Contact Information</h2>
                    
                    <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); margin-bottom: 2rem;">
                        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
                            <div style="width: 48px; height: 48px; background: var(--primary-color); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white;">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <h4 style="margin: 0 0 0.25rem 0; color: var(--gray-900);">Email</h4>
                                <p style="margin: 0; color: var(--gray-600);">hello@plughausstudios.com</p>
                            </div>
                        </div>
                        
                        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
                            <div style="width: 48px; height: 48px; background: var(--success-color); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white;">
                                <i class="fas fa-life-ring"></i>
                            </div>
                            <div>
                                <h4 style="margin: 0 0 0.25rem 0; color: var(--gray-900);">Support</h4>
                                <p style="margin: 0; color: var(--gray-600);">support@plughausstudios.com</p>
                            </div>
                        </div>
                        
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div style="width: 48px; height: 48px; background: var(--secondary-color); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white;">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <h4 style="margin: 0 0 0.25rem 0; color: var(--gray-900);">Response Time</h4>
                                <p style="margin: 0; color: var(--gray-600);">Usually within 24 hours</p>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Section -->
                    <h3 style="font-size: 1.5rem; margin-bottom: 1.5rem; color: var(--gray-900);">Before you contact us...</h3>
                    
                    <div style="background: white; border: 1px solid var(--gray-200); border-radius: 8px; margin-bottom: 1rem;">
                        <div style="padding: 1.25rem; border-bottom: 1px solid var(--gray-200);">
                            <h4 style="margin: 0; color: var(--gray-900); font-size: 1rem;">Need plugin support?</h4>
                        </div>
                        <div style="padding: 1.25rem; color: var(--gray-600); font-size: 0.95rem;">
                            Check our <a href="/support/" style="color: var(--primary-color);">support documentation</a> first - most questions are answered there.
                        </div>
                    </div>
                    
                    <div style="background: white; border: 1px solid var(--gray-200); border-radius: 8px; margin-bottom: 1rem;">
                        <div style="padding: 1.25rem; border-bottom: 1px solid var(--gray-200);">
                            <h4 style="margin: 0; color: var(--gray-900); font-size: 1rem;">Have a feature request?</h4>
                        </div>
                        <div style="padding: 1.25rem; color: var(--gray-600); font-size: 0.95rem;">
                            We'd love to hear it! Include "Feature Request" in your subject line.
                        </div>
                    </div>
                    
                    <div style="background: white; border: 1px solid var(--gray-200); border-radius: 8px;">
                        <div style="padding: 1.25rem; border-bottom: 1px solid var(--gray-200);">
                            <h4 style="margin: 0; color: var(--gray-900); font-size: 1rem;">Need custom development?</h4>
                        </div>
                        <div style="padding: 1.25rem; color: var(--gray-600); font-size: 0.95rem;">
                            We offer custom plugin development. Include your project details and timeline.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<style>
@media (max-width: 768px) {
    .container > div[style*="grid-template-columns: 1fr 1fr"] {
        grid-template-columns: 1fr !important;
        gap: 2rem !important;
    }
}
</style>

<?php get_footer(); ?>