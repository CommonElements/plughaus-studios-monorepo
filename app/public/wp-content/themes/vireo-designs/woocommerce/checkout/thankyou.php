<?php
/**
 * Thankyou page
 *
 * Enhanced thank you page for Vireo Designs plugin purchases
 *
 * @package Vireo_Designs
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="thankyou-page">
    
    <!-- Success Header -->
    <div class="success-header">
        <div class="container">
            <div class="success-content">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h1 class="success-title">🎉 Thank You for Your Purchase!</h1>
                <p class="success-description">Your Vireo Designs plugins are ready for download</p>
            </div>
        </div>
    </div>

    <div class="container">
        
        <?php if ($order) : ?>
            
            <div class="thankyou-content">
                
                <!-- Order Summary -->
                <div class="order-summary-section">
                    <div class="section-header">
                        <h2><i class="fas fa-receipt"></i> Order Summary</h2>
                        <span class="order-number">Order #<?php echo $order->get_order_number(); ?></span>
                    </div>
                    
                    <div class="order-details">
                        <div class="order-meta">
                            <div class="meta-item">
                                <span class="meta-label">Date:</span>
                                <span class="meta-value"><?php echo wc_format_datetime($order->get_date_created()); ?></span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Email:</span>
                                <span class="meta-value"><?php echo $order->get_billing_email(); ?></span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Total:</span>
                                <span class="meta-value"><?php echo $order->get_formatted_order_total(); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Plugin Downloads & Licenses -->
                <div class="downloads-section">
                    <h2><i class="fas fa-download"></i> Your Plugin Downloads</h2>
                    
                    <?php 
                    $has_plugins = false;
                    foreach ($order->get_items() as $item_id => $item):
                        $product = $item->get_product();
                        if ($product && has_term('plugins', 'product_cat', $product->get_id())):
                            $has_plugins = true;
                            $license_key = $order->get_meta('_vireo_license_' . $product->get_id());
                            $activation_limit = $order->get_meta('_vireo_license_limit_' . $product->get_id());
                    ?>
                    
                    <div class="plugin-download-card">
                        <div class="plugin-info">
                            <h3><?php echo $product->get_name(); ?></h3>
                            <p class="plugin-description"><?php echo wp_trim_words($product->get_short_description(), 20); ?></p>
                        </div>
                        
                        <div class="plugin-license">
                            <h4>License Key</h4>
                            <div class="license-key-display">
                                <code class="license-key"><?php echo $license_key ?: 'Generating...'; ?></code>
                                <button class="copy-license" data-license="<?php echo esc_attr($license_key); ?>">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                            <p class="license-info">
                                <i class="fas fa-info-circle"></i>
                                Activation limit: <?php echo $activation_limit ?: 1; ?> site(s)
                            </p>
                        </div>
                        
                        <div class="plugin-actions">
                            <?php if ($product->get_downloads()): ?>
                                <?php foreach ($product->get_downloads() as $download): ?>
                                    <a href="<?php echo esc_url($download['file']); ?>" class="btn btn-primary download-btn">
                                        <i class="fas fa-download"></i>
                                        Download Plugin
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            
                            <a href="<?php echo get_permalink($product->get_id()); ?>" class="btn btn-secondary">
                                <i class="fas fa-book"></i>
                                View Documentation
                            </a>
                        </div>
                    </div>
                    
                    <?php endforeach; ?>
                    
                    <?php if (!$has_plugins): ?>
                        <div class="no-plugins-notice">
                            <p>No plugin downloads found in this order.</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Installation Instructions -->
                <div class="instructions-section">
                    <h2><i class="fas fa-cogs"></i> Installation Instructions</h2>
                    
                    <div class="instruction-steps">
                        <div class="step">
                            <div class="step-number">1</div>
                            <div class="step-content">
                                <h4>Download Your Plugin</h4>
                                <p>Click the download button above to get your plugin ZIP file.</p>
                            </div>
                        </div>
                        
                        <div class="step">
                            <div class="step-number">2</div>
                            <div class="step-content">
                                <h4>Install on WordPress</h4>
                                <p>Go to Plugins → Add New → Upload Plugin in your WordPress admin.</p>
                            </div>
                        </div>
                        
                        <div class="step">
                            <div class="step-number">3</div>
                            <div class="step-content">
                                <h4>Activate & License</h4>
                                <p>Activate the plugin and enter your license key in the plugin settings.</p>
                            </div>
                        </div>
                        
                        <div class="step">
                            <div class="step-number">4</div>
                            <div class="step-content">
                                <h4>Start Using</h4>
                                <p>Configure your settings and start enjoying your new functionality!</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Support & Contact -->
                <div class="support-section">
                    <h2><i class="fas fa-headset"></i> Need Help?</h2>
                    
                    <div class="support-grid">
                        <div class="support-card">
                            <i class="fas fa-book"></i>
                            <h4>Documentation</h4>
                            <p>Comprehensive guides and tutorials</p>
                            <a href="<?php echo home_url('/support/'); ?>" class="support-link">View Docs</a>
                        </div>
                        
                        <div class="support-card">
                            <i class="fas fa-envelope"></i>
                            <h4>Email Support</h4>
                            <p>Get help from our support team</p>
                            <a href="<?php echo home_url('/contact/'); ?>" class="support-link">Contact Us</a>
                        </div>
                        
                        <div class="support-card">
                            <i class="fas fa-comments"></i>
                            <h4>Community</h4>
                            <p>Connect with other users</p>
                            <a href="#" class="support-link">Join Community</a>
                        </div>
                    </div>
                </div>
                
            </div>
            
        <?php else : ?>
            
            <div class="no-order-found">
                <h2>Order not found</h2>
                <p>We couldn't find your order. Please contact support if you believe this is an error.</p>
                <a href="<?php echo home_url('/contact/'); ?>" class="btn btn-primary">Contact Support</a>
            </div>
            
        <?php endif; ?>
        
    </div>
    
</div>

<style>
/* Thank You Page Styles */
.thankyou-page {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    min-height: 80vh;
}

.success-header {
    background: var(--primary-gradient);
    color: var(--white);
    padding: var(--space-12) 0;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.success-header:before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: radial-gradient(circle at 20px 20px, rgba(255,255,255,0.1) 1px, transparent 0);
    background-size: 40px 40px;
}

.success-content {
    position: relative;
    z-index: 2;
}

.success-icon {
    font-size: 4rem;
    margin-bottom: var(--space-4);
    color: var(--secondary-color);
}

.success-title {
    font-size: var(--text-4xl);
    font-weight: 800;
    margin-bottom: var(--space-3);
    line-height: 1.2;
}

.success-description {
    font-size: var(--text-xl);
    opacity: 0.9;
    margin: 0;
}

.thankyou-content {
    padding: var(--space-12) 0;
    max-width: 1000px;
    margin: 0 auto;
}

/* Section Styles */
.order-summary-section,
.downloads-section,
.instructions-section,
.support-section {
    background: var(--white);
    border-radius: var(--radius-2xl);
    padding: var(--space-8);
    margin-bottom: var(--space-8);
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--gray-200);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--space-6);
    padding-bottom: var(--space-4);
    border-bottom: 1px solid var(--gray-200);
}

.section-header h2 {
    display: flex;
    align-items: center;
    gap: var(--space-3);
    color: var(--gray-900);
    font-size: var(--text-2xl);
    font-weight: 700;
    margin: 0;
}

.section-header i {
    color: var(--primary-color);
}

.order-number {
    background: var(--primary-color);
    color: var(--white);
    padding: var(--space-2) var(--space-4);
    border-radius: var(--radius-full);
    font-size: var(--text-sm);
    font-weight: 600;
}

/* Order Details */
.order-meta {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: var(--space-4);
}

.meta-item {
    display: flex;
    justify-content: space-between;
    padding: var(--space-3);
    background: var(--gray-50);
    border-radius: var(--radius);
}

.meta-label {
    font-weight: 500;
    color: var(--gray-600);
}

.meta-value {
    font-weight: 600;
    color: var(--gray-900);
}

/* Plugin Download Cards */
.plugin-download-card {
    background: var(--gray-50);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-xl);
    padding: var(--space-6);
    margin-bottom: var(--space-6);
}

.plugin-info h3 {
    color: var(--gray-900);
    font-size: var(--text-xl);
    font-weight: 700;
    margin-bottom: var(--space-2);
}

.plugin-description {
    color: var(--gray-600);
    margin-bottom: var(--space-4);
}

.plugin-license h4 {
    color: var(--gray-800);
    font-size: var(--text-lg);
    font-weight: 600;
    margin-bottom: var(--space-3);
}

.license-key-display {
    display: flex;
    align-items: center;
    gap: var(--space-3);
    margin-bottom: var(--space-3);
}

.license-key {
    background: var(--white);
    border: 1px solid var(--gray-300);
    border-radius: var(--radius);
    padding: var(--space-3) var(--space-4);
    font-family: 'Courier New', monospace;
    font-size: var(--text-sm);
    color: var(--gray-900);
    flex: 1;
    word-break: break-all;
}

.copy-license {
    background: var(--primary-color);
    color: var(--white);
    border: none;
    border-radius: var(--radius);
    padding: var(--space-3);
    cursor: pointer;
    transition: all var(--transition-fast);
}

.copy-license:hover {
    background: var(--primary-color-dark);
    transform: scale(1.05);
}

.license-info {
    font-size: var(--text-sm);
    color: var(--gray-600);
    margin: 0;
}

.plugin-actions {
    display: flex;
    gap: var(--space-4);
    margin-top: var(--space-4);
}

/* Installation Steps */
.instruction-steps {
    display: grid;
    gap: var(--space-6);
}

.step {
    display: flex;
    gap: var(--space-4);
    align-items: flex-start;
}

.step-number {
    width: 40px;
    height: 40px;
    background: var(--primary-gradient);
    color: var(--white);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    flex-shrink: 0;
}

.step-content h4 {
    color: var(--gray-900);
    font-size: var(--text-lg);
    font-weight: 600;
    margin-bottom: var(--space-2);
}

.step-content p {
    color: var(--gray-600);
    margin: 0;
}

/* Support Grid */
.support-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: var(--space-6);
}

.support-card {
    background: var(--gray-50);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-xl);
    padding: var(--space-6);
    text-align: center;
    transition: all var(--transition-fast);
}

.support-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.support-card i {
    font-size: 2.5rem;
    color: var(--primary-color);
    margin-bottom: var(--space-4);
}

.support-card h4 {
    color: var(--gray-900);
    font-size: var(--text-lg);
    font-weight: 600;
    margin-bottom: var(--space-2);
}

.support-card p {
    color: var(--gray-600);
    margin-bottom: var(--space-4);
}

.support-link {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
    transition: color var(--transition-fast);
}

.support-link:hover {
    color: var(--primary-color-dark);
}

/* Responsive Design */
@media (max-width: 768px) {
    .success-title {
        font-size: var(--text-3xl);
    }
    
    .section-header {
        flex-direction: column;
        gap: var(--space-3);
        align-items: flex-start;
    }
    
    .plugin-actions {
        flex-direction: column;
    }
    
    .license-key-display {
        flex-direction: column;
        align-items: stretch;
    }
}
</style>

<script>
// Copy license key functionality
document.addEventListener('DOMContentLoaded', function() {
    const copyButtons = document.querySelectorAll('.copy-license');
    
    copyButtons.forEach(button => {
        button.addEventListener('click', function() {
            const license = this.getAttribute('data-license');
            
            if (navigator.clipboard) {
                navigator.clipboard.writeText(license).then(() => {
                    this.innerHTML = '<i class="fas fa-check"></i>';
                    setTimeout(() => {
                        this.innerHTML = '<i class="fas fa-copy"></i>';
                    }, 2000);
                });
            } else {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = license;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                
                this.innerHTML = '<i class="fas fa-check"></i>';
                setTimeout(() => {
                    this.innerHTML = '<i class="fas fa-copy"></i>';
                }, 2000);
            }
        });
    });
});
</script>

<?php do_action('woocommerce_thankyou', $order->get_id()); ?>