<?php
/**
 * Checkout Form
 * 
 * Enhanced checkout form for Vireo Designs plugin purchases
 *
 * @package Vireo_Designs
 */

if (!defined('ABSPATH')) {
    exit;
}

do_action('woocommerce_before_checkout_form', $checkout);

// If checkout registration is disabled and not logged in, the user cannot checkout.
if (!$checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in()) {
    echo esc_html(apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'woocommerce')));
    return;
}

?>

<div class="checkout-page">
    
    <!-- Enhanced Checkout Header -->
    <div class="checkout-header">
        <div class="container">
            <div class="checkout-progress">
                <div class="progress-step active">
                    <div class="step-number">1</div>
                    <div class="step-label">Cart</div>
                </div>
                <div class="progress-line"></div>
                <div class="progress-step active">
                    <div class="step-number">2</div>
                    <div class="step-label">Checkout</div>
                </div>
                <div class="progress-line"></div>
                <div class="progress-step">
                    <div class="step-number">3</div>
                    <div class="step-label">Complete</div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        
        <form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">

            <div class="checkout-content">
                
                <!-- Checkout Form -->
                <div class="checkout-form">
                    
                    <?php if ($checkout->get_checkout_fields()) : ?>

                        <?php do_action('woocommerce_checkout_before_customer_details'); ?>

                        <!-- Customer Information -->
                        <div class="checkout-section">
                            <h3 class="section-title">
                                <i class="fas fa-user"></i>
                                Customer Information
                            </h3>
                            
                            <div class="customer-details" id="customer_details">
                                <div class="woocommerce-billing-fields">
                                    <?php if (wc_ship_to_billing_address_only() && WC()->cart->needs_shipping()) : ?>
                                        <h4><?php esc_html_e('Billing &amp; Shipping', 'woocommerce'); ?></h4>
                                    <?php else : ?>
                                        <h4><?php esc_html_e('Billing details', 'woocommerce'); ?></h4>
                                    <?php endif; ?>

                                    <?php do_action('woocommerce_checkout_billing'); ?>
                                </div>

                                <?php do_action('woocommerce_checkout_shipping'); ?>
                            </div>
                        </div>

                        <?php do_action('woocommerce_checkout_after_customer_details'); ?>

                    <?php endif; ?>
                    
                    <!-- Additional Information -->
                    <?php do_action('woocommerce_checkout_before_order_review_heading'); ?>
                    
                    <?php if (!WC()->cart->is_empty()): ?>
                    <div class="checkout-section">
                        <h3 class="section-title">
                            <i class="fas fa-credit-card"></i>
                            Payment Information
                        </h3>
                        
                        <!-- Payment Methods -->
                        <div class="payment-methods">
                            <?php if (WC()->cart->needs_payment()) : ?>
                                <div id="payment" class="woocommerce-checkout-payment">
                                    <?php if (WC()->cart->needs_payment()) : ?>
                                        <div class="payment-method-intro">
                                            <p>Your payment information is processed securely via Stripe. We never store your credit card details.</p>
                                        </div>
                                        <?php woocommerce_checkout_payment(); ?>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Order Summary Sidebar -->
                <div class="checkout-sidebar">
                    
                    <!-- Order Review -->
                    <div class="order-review-section">
                        <h3 class="section-title">
                            <i class="fas fa-shopping-cart"></i>
                            Order Summary
                        </h3>
                        
                        <div id="order_review" class="woocommerce-checkout-review-order">
                            <?php do_action('woocommerce_checkout_order_review'); ?>
                        </div>
                    </div>
                    
                    <!-- Security & Trust Badges -->
                    <div class="trust-badges">
                        <h4>Secure Checkout</h4>
                        <div class="badges-grid">
                            <div class="trust-badge">
                                <i class="fas fa-shield-alt"></i>
                                <span>SSL Encrypted</span>
                            </div>
                            <div class="trust-badge">
                                <i class="fab fa-stripe"></i>
                                <span>Stripe Secure</span>
                            </div>
                            <div class="trust-badge">
                                <i class="fas fa-download"></i>
                                <span>Instant Download</span>
                            </div>
                            <div class="trust-badge">
                                <i class="fas fa-key"></i>
                                <span>License Included</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Support Info -->
                    <div class="checkout-support">
                        <h4>Need Help?</h4>
                        <p>Our support team is here to help with any questions about your purchase.</p>
                        <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="support-link">
                            <i class="fas fa-headset"></i>
                            Contact Support
                        </a>
                    </div>
                    
                </div>
                
            </div>

        </form>
        
    </div>
    
</div>

<style>
/* Enhanced Checkout Styles */
.checkout-page {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    min-height: 80vh;
    padding: var(--space-8) 0;
    position: relative;
}

.checkout-page:before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: radial-gradient(circle at 20px 20px, rgba(5, 150, 105, 0.03) 1px, transparent 0);
    background-size: 40px 40px;
    pointer-events: none;
}

.checkout-header {
    background: var(--white);
    border-bottom: 1px solid var(--gray-200);
    padding: var(--space-6) 0;
    margin-bottom: var(--space-8);
}

.checkout-progress {
    display: flex;
    align-items: center;
    justify-content: center;
    max-width: 400px;
    margin: 0 auto;
}

.progress-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--space-2);
}

.step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--gray-300);
    color: var(--white);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    transition: all var(--transition-fast);
}

.progress-step.active .step-number {
    background: var(--primary-color);
}

.step-label {
    font-size: var(--text-sm);
    font-weight: 500;
    color: var(--gray-600);
}

.progress-step.active .step-label {
    color: var(--primary-color);
}

.progress-line {
    width: 60px;
    height: 2px;
    background: var(--gray-300);
    margin: 0 var(--space-4);
}

.checkout-content {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: var(--space-8);
    align-items: start;
}

.checkout-form {
    background: var(--white);
    border-radius: var(--radius-2xl);
    padding: var(--space-8);
    box-shadow: var(--shadow-sm);
}

.checkout-sidebar {
    position: sticky;
    top: var(--space-8);
}

.checkout-section {
    margin-bottom: var(--space-8);
    padding-bottom: var(--space-8);
    border-bottom: 1px solid var(--gray-200);
}

.checkout-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.section-title {
    display: flex;
    align-items: center;
    gap: var(--space-3);
    font-size: var(--text-xl);
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: var(--space-6);
}

.section-title i {
    color: var(--primary-color);
}

.order-review-section {
    background: var(--white);
    border-radius: var(--radius-2xl);
    padding: var(--space-6);
    box-shadow: var(--shadow-sm);
    margin-bottom: var(--space-6);
}

.trust-badges {
    background: var(--white);
    border-radius: var(--radius-2xl);
    padding: var(--space-6);
    box-shadow: var(--shadow-sm);
    margin-bottom: var(--space-6);
}

.trust-badges h4 {
    font-size: var(--text-lg);
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: var(--space-4);
    text-align: center;
}

.badges-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--space-3);
}

.trust-badge {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--space-2);
    padding: var(--space-3);
    background: var(--gray-50);
    border-radius: var(--radius-lg);
    text-align: center;
}

.trust-badge i {
    font-size: var(--text-lg);
    color: var(--primary-color);
}

.trust-badge span {
    font-size: var(--text-xs);
    font-weight: 500;
    color: var(--gray-700);
}

.checkout-support {
    background: var(--white);
    border-radius: var(--radius-2xl);
    padding: var(--space-6);
    box-shadow: var(--shadow-sm);
    text-align: center;
}

.checkout-support h4 {
    font-size: var(--text-lg);
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: var(--space-3);
}

.checkout-support p {
    color: var(--gray-600);
    margin-bottom: var(--space-4);
    font-size: var(--text-sm);
}

.support-link {
    display: inline-flex;
    align-items: center;
    gap: var(--space-2);
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
    padding: var(--space-2) var(--space-4);
    border: 1px solid var(--primary-color);
    border-radius: var(--radius-lg);
    transition: all var(--transition-fast);
}

.support-link:hover {
    background: var(--primary-color);
    color: var(--white);
}

.payment-method-intro {
    background: var(--gray-50);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-lg);
    padding: var(--space-4);
    margin-bottom: var(--space-6);
}

.payment-method-intro p {
    margin: 0;
    font-size: var(--text-sm);
    color: var(--gray-600);
    text-align: center;
}

/* Enhanced Stripe Payment Form */
.woocommerce-checkout-payment .payment_methods {
    background: var(--white);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
}

.wc_payment_method {
    border-bottom: 1px solid var(--gray-200);
    margin: 0;
}

.wc_payment_method:last-child {
    border-bottom: none;
}

.wc_payment_method label {
    display: flex;
    align-items: center;
    gap: var(--space-3);
    padding: var(--space-4);
    margin: 0;
    cursor: pointer;
    transition: all var(--transition-fast);
    font-weight: 500;
    color: var(--gray-900);
}

.wc_payment_method label:hover {
    background: var(--gray-50);
}

.wc_payment_method input[type="radio"] {
    margin: 0;
    accent-color: var(--primary-color);
}

.payment_box {
    padding: var(--space-6);
    background: var(--gray-50);
    border-top: 1px solid var(--gray-200);
}

/* Stripe Elements Styling */
.stripe-elements-field {
    padding: var(--space-3) var(--space-4);
    border: 1px solid var(--gray-300);
    border-radius: var(--radius);
    background: var(--white);
    font-size: var(--text-base);
    transition: all var(--transition-fast);
}

.stripe-elements-field:focus-within {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
}

.woocommerce-input-wrapper {
    margin-bottom: var(--space-4);
}

.woocommerce-input-wrapper label {
    display: block;
    margin-bottom: var(--space-2);
    font-weight: 500;
    color: var(--gray-700);
}

/* Order Review Enhancements */
.woocommerce-checkout-review-order-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: var(--space-6);
}

.woocommerce-checkout-review-order-table th,
.woocommerce-checkout-review-order-table td {
    padding: var(--space-3) var(--space-4);
    border-bottom: 1px solid var(--gray-200);
    text-align: left;
}

.woocommerce-checkout-review-order-table th {
    font-weight: 600;
    color: var(--gray-900);
    background: var(--gray-50);
}

.order-total {
    font-weight: 700;
    color: var(--primary-color);
    font-size: var(--text-lg);
}

/* Payment Button Enhancement */
#place_order {
    width: 100%;
    padding: var(--space-4) var(--space-6);
    background: var(--primary-gradient);
    color: var(--white);
    border: none;
    border-radius: var(--radius-lg);
    font-size: var(--text-lg);
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-fast);
    position: relative;
    overflow: hidden;
}

#place_order:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

#place_order:active {
    transform: translateY(0);
}

#place_order:before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left var(--transition-slow);
}

#place_order:hover:before {
    left: 100%;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .checkout-content {
        grid-template-columns: 1fr;
        gap: var(--space-6);
    }
    
    .checkout-sidebar {
        position: static;
        order: -1;
    }
    
    .badges-grid {
        grid-template-columns: 1fr;
    }
    
    .checkout-progress {
        max-width: 300px;
    }
    
    .progress-line {
        width: 40px;
    }
}
</style>

<?php do_action('woocommerce_after_checkout_form', $checkout); ?>