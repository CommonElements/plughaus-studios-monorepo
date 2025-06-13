<?php
/**
 * Cart Page
 * Production-ready cart template for Vireo Designs
 */

defined("ABSPATH") || exit;

do_action("woocommerce_before_cart"); ?>

<div class="vireo-cart-container">
    <div class="cart-header">
        <h1 class="cart-title">
            <i class="fas fa-shopping-cart"></i>
            Your Cart
        </h1>
        <p class="cart-subtitle">Review your selected plugins before checkout</p>
    </div>

    <form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
        <?php do_action("woocommerce_before_cart_table"); ?>

        <?php if (WC()->cart->is_empty()) : ?>
            <div class="cart-empty-state">
                <div class="empty-cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h3>Your cart is empty</h3>
                <p>Looks like you haven't added any plugins to your cart yet.</p>
                <a href="<?php echo esc_url(wc_get_page_permalink("shop")); ?>" class="btn btn-primary btn-large">
                    <i class="fas fa-store"></i>
                    Browse Plugins
                </a>
            </div>
        <?php else : ?>
            <table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
                <thead>
                    <tr>
                        <th class="product-thumbnail">&nbsp;</th>
                        <th class="product-name">Plugin</th>
                        <th class="product-price">Price</th>
                        <th class="product-quantity">License Type</th>
                        <th class="product-subtotal">Subtotal</th>
                        <th class="product-remove">&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    <?php do_action("woocommerce_before_cart_contents"); ?>

                    <?php
                    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                        $_product   = apply_filters("woocommerce_cart_item_product", $cart_item["data"], $cart_item, $cart_item_key);
                        $product_id = apply_filters("woocommerce_cart_item_product_id", $cart_item["product_id"], $cart_item, $cart_item_key);

                        if ($_product && $_product->exists() && $cart_item["quantity"] > 0 && apply_filters("woocommerce_cart_item_visible", true, $cart_item, $cart_item_key)) {
                            $product_permalink = apply_filters("woocommerce_cart_item_permalink", $_product->is_visible() ? $_product->get_permalink($cart_item) : "", $cart_item, $cart_item_key);
                            ?>
                            <tr class="woocommerce-cart-form__cart-item <?php echo esc_attr(apply_filters("woocommerce_cart_item_class", "cart_item", $cart_item, $cart_item_key)); ?>">

                                <td class="product-thumbnail">
                                    <?php
                                    $thumbnail = apply_filters("woocommerce_cart_item_thumbnail", $_product->get_image(), $cart_item, $cart_item_key);

                                    if (!$product_permalink) {
                                        echo $thumbnail;
                                    } else {
                                        printf("<a href=\"%s\">%s</a>", esc_url($product_permalink), $thumbnail);
                                    }
                                    ?>
                                </td>

                                <td class="product-name" data-title="<?php esc_attr_e("Product", "woocommerce"); ?>">
                                    <div class="product-info">
                                        <?php
                                        if (!$product_permalink) {
                                            echo wp_kses_post(apply_filters("woocommerce_cart_item_name", $_product->get_name(), $cart_item, $cart_item_key) . "&nbsp;");
                                        } else {
                                            echo wp_kses_post(apply_filters("woocommerce_cart_item_name", sprintf("<a href=\"%s\">%s</a>", esc_url($product_permalink), $_product->get_name()), $cart_item, $cart_item_key));
                                        }

                                        do_action("woocommerce_after_cart_item_name", $cart_item, $cart_item_key);

                                        // Meta data.
                                        echo wc_get_formatted_cart_item_data($cart_item);

                                        // Backorder notification.
                                        if ($_product->backorders_require_notification() && $_product->is_on_backorder($cart_item["quantity"])) {
                                            echo wp_kses_post(apply_filters("woocommerce_cart_item_backorder_notification", "<p class=\"backorder_notification\">" . esc_html__("Available on backorder", "woocommerce") . "</p>", $product_id));
                                        }
                                        ?>
                                        <div class="product-features">
                                            <small class="text-muted">
                                                <i class="fas fa-download"></i> Instant Download
                                                <i class="fas fa-key"></i> License Key Included
                                                <i class="fas fa-support"></i> 1 Year Support
                                                <?php if (strpos($_product->get_name(), 'Bundle') !== false): ?>
                                                    <i class="fas fa-layer-group"></i> Multiple Plugins
                                                <?php endif; ?>
                                                <?php if (strpos($_product->get_name(), 'Developer') !== false || strpos($_product->get_name(), 'Agency') !== false): ?>
                                                    <i class="fas fa-users"></i> Multi-Site Usage
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                        
                                        <?php if (!strpos($_product->get_name(), 'Bundle') && !strpos($_product->get_name(), 'Site')): ?>
                                        <div class="upgrade-suggestions" style="margin-top: 10px; padding: 8px 12px; background: #f0f8ff; border-radius: 6px; border-left: 3px solid #2c5aa0;">
                                            <small style="color: #2c5aa0; font-weight: 500;">
                                                💡 <strong>Save 40%</strong> with our <a href="<?php echo esc_url(home_url('/plugins/')); ?>" style="color: #2c5aa0; text-decoration: underline;">Plugin Bundles</a> 
                                                or view <a href="<?php echo esc_url(home_url('/pricing/')); ?>" style="color: #2c5aa0; text-decoration: underline;">All Pricing Options</a>
                                            </small>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <td class="product-price" data-title="<?php esc_attr_e("Price", "woocommerce"); ?>">
                                    <div class="price-display">
                                        <?php echo apply_filters("woocommerce_cart_item_price", WC()->cart->get_product_price($_product), $cart_item, $cart_item_key); ?>
                                    </div>
                                </td>

                                <td class="product-quantity" data-title="<?php esc_attr_e("License Type", "woocommerce"); ?>">
                                    <div class="license-type">
                                        <?php
                                        // Determine license type from product name or attributes
                                        $license_type = 'Single Site License';
                                        $license_icon = 'fas fa-certificate';
                                        $license_color = '#2c5aa0';
                                        
                                        if (strpos($_product->get_name(), '5-Site') !== false) {
                                            $license_type = '5-Site Developer License';
                                            $license_icon = 'fas fa-code';
                                            $license_color = '#28a745';
                                        } elseif (strpos($_product->get_name(), '25-Site') !== false) {
                                            $license_type = '25-Site Agency License';
                                            $license_icon = 'fas fa-building';
                                            $license_color = '#ffc107';
                                        } elseif (strpos($_product->get_name(), 'Unlimited') !== false) {
                                            $license_type = 'Unlimited Sites';
                                            $license_icon = 'fas fa-infinity';
                                            $license_color = '#6f42c1';
                                        } elseif (strpos($_product->get_name(), 'Bundle') !== false) {
                                            $license_type = 'Plugin Bundle';
                                            $license_icon = 'fas fa-layer-group';
                                            $license_color = '#fd7e14';
                                        }
                                        ?>
                                        <span class="license-badge" style="background-color: <?php echo $license_color; ?>15; color: <?php echo $license_color; ?>; border: 1px solid <?php echo $license_color; ?>40;">
                                            <i class="<?php echo $license_icon; ?>"></i>
                                            <?php echo $license_type; ?>
                                        </span>
                                    </div>
                                </td>

                                <td class="product-subtotal" data-title="<?php esc_attr_e("Subtotal", "woocommerce"); ?>">
                                    <div class="subtotal-display">
                                        <?php echo apply_filters("woocommerce_cart_item_subtotal", WC()->cart->get_product_subtotal($_product, $cart_item["quantity"]), $cart_item, $cart_item_key); ?>
                                    </div>
                                </td>

                                <td class="product-remove">
                                    <?php
                                        echo apply_filters("woocommerce_cart_item_remove_link", sprintf(
                                            "<a href=\"%s\" class=\"remove remove-item\" aria-label=\"%s\" data-product_id=\"%s\" data-product_sku=\"%s\" title=\"%s\"><i class=\"fas fa-times\"></i></a>",
                                            esc_url(wc_get_cart_remove_url($cart_item_key)),
                                            esc_html__("Remove this item", "woocommerce"),
                                            esc_attr($product_id),
                                            esc_attr($_product->get_sku()),
                                            esc_html__("Remove this item", "woocommerce")
                                        ), $cart_item_key);
                                    ?>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                    ?>

                    <?php do_action("woocommerce_cart_contents"); ?>

                    <tr>
                        <td colspan="6" class="actions">
                            <?php if (wc_coupons_enabled()) { ?>
                                <div class="coupon">
                                    <label for="coupon_code"><?php esc_html_e("Coupon:", "woocommerce"); ?></label> 
                                    <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e("Coupon code", "woocommerce"); ?>" /> 
                                    <button type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e("Apply coupon", "woocommerce"); ?>">
                                        <i class="fas fa-tag"></i>
                                        <?php esc_attr_e("Apply coupon", "woocommerce"); ?>
                                    </button>
                                    <?php do_action("woocommerce_cart_coupon"); ?>
                                </div>
                            <?php } ?>

                            <button type="submit" class="button" name="update_cart" value="<?php esc_attr_e("Update cart", "woocommerce"); ?>">
                                <i class="fas fa-sync-alt"></i>
                                <?php esc_html_e("Update cart", "woocommerce"); ?>
                            </button>

                            <?php do_action("woocommerce_cart_actions"); ?>

                            <?php wp_nonce_field("woocommerce-cart", "woocommerce-cart-nonce"); ?>
                        </td>
                    </tr>

                    <?php do_action("woocommerce_after_cart_contents"); ?>
                </tbody>
            </table>
        <?php endif; ?>

        <?php do_action("woocommerce_after_cart_table"); ?>
    </form>

    <?php if (!WC()->cart->is_empty()) : ?>
        <div class="cart-collaterals">
            <?php
                /**
                 * Cart collaterals hook.
                 */
                do_action("woocommerce_cart_collaterals");
            ?>
        </div>
    <?php endif; ?>
</div>

<style>
.vireo-cart-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 20px;
}

.cart-header {
    text-align: center;
    margin-bottom: 40px;
    padding-bottom: 20px;
    border-bottom: 2px solid #eee;
}

.cart-title {
    font-size: 32px;
    color: var(--primary-color, #059669);
    margin-bottom: 10px;
    font-weight: 700;
}

.cart-title i {
    color: var(--primary-color, #059669);
    margin-right: 10px;
}

.cart-subtitle {
    font-size: 18px;
    color: #6c757d;
    margin: 0;
}

.cart-empty-state {
    text-align: center;
    padding: 80px 20px;
    background: #f8f9fa;
    border-radius: 12px;
    border: 2px dashed #dee2e6;
}

.empty-cart-icon i {
    font-size: 80px;
    color: #dee2e6;
    margin-bottom: 30px;
}

.cart-empty-state h3 {
    font-size: 24px;
    color: #495057;
    margin-bottom: 15px;
}

.cart-empty-state p {
    font-size: 16px;
    color: #6c757d;
    margin-bottom: 30px;
}

.btn-large {
    padding: 15px 30px;
    font-size: 16px;
    font-weight: 600;
}

.shop_table {
    width: 100%;
    margin-bottom: 30px;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.shop_table th {
    background: #f8f9fa;
    padding: 20px;
    font-weight: 600;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
}

.shop_table td {
    padding: 25px 20px;
    border-bottom: 1px solid #eee;
    vertical-align: middle;
}

.product-info {
    display: flex;
    flex-direction: column;
}

.product-features {
    margin-top: 10px;
}

.product-features i {
    margin-right: 5px;
    margin-left: 15px;
    color: #28a745;
}

.product-features i:first-child {
    margin-left: 0;
}

.price-display, .subtotal-display {
    font-size: 18px;
    font-weight: 600;
    color: #2c5aa0;
}

.license-badge {
    display: inline-flex;
    align-items: center;
    background: #e8f4f8;
    color: #2c5aa0;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
}

.license-badge i {
    margin-right: 6px;
}

.remove-item {
    background: #dc3545;
    color: white;
    padding: 8px 10px;
    border-radius: 4px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.remove-item:hover {
    background: #c82333;
    color: white;
    transform: scale(1.1);
}

.actions {
    background: #f8f9fa;
    padding: 25px 20px;
}

.coupon {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
}

.coupon input {
    padding: 10px 15px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
}

.actions .button {
    background: #2c5aa0;
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 6px;
    font-weight: 600;
    transition: all 0.3s ease;
    margin-right: 10px;
}

.actions .button:hover {
    background: #1e3f73;
    transform: translateY(-2px);
}

.cart-collaterals {
    margin-top: 40px;
}

@media (max-width: 768px) {
    .shop_table, .shop_table thead, .shop_table tbody, .shop_table th, .shop_table td, .shop_table tr {
        display: block;
    }
    
    .shop_table thead tr {
        position: absolute;
        top: -9999px;
        left: -9999px;
    }
    
    .shop_table tr {
        border: 1px solid #ccc;
        margin-bottom: 10px;
        padding: 15px;
        border-radius: 8px;
    }
    
    .shop_table td {
        border: none;
        padding: 10px 0;
        position: relative;
        padding-left: 50%;
    }
    
    .shop_table td:before {
        content: attr(data-title) ": ";
        position: absolute;
        left: 6px;
        width: 45%;
        padding-right: 10px;
        white-space: nowrap;
        font-weight: 600;
        color: #495057;
    }
}
</style>

<?php do_action("woocommerce_after_cart"); ?>