<?php
/**
 * Cart Totals
 * Production-ready cart totals for Vireo Designs
 */

defined("ABSPATH") || exit;

?>
<div class="cart_totals <?php echo (WC()->customer->has_calculated_shipping()) ? "calculated_shipping" : ""; ?>">

    <?php do_action("woocommerce_before_cart_totals"); ?>

    <div class="cart-totals-header">
        <h2><?php esc_html_e("Cart totals", "woocommerce"); ?></h2>
    </div>

    <table cellspacing="0" class="shop_table shop_table_responsive">

        <tr class="cart-subtotal">
            <th><?php esc_html_e("Subtotal", "woocommerce"); ?></th>
            <td data-title="<?php esc_attr_e("Subtotal", "woocommerce"); ?>"><?php wc_cart_totals_subtotal_html(); ?></td>
        </tr>

        <?php foreach (WC()->cart->get_coupons() as $code => $coupon) : ?>
            <tr class="cart-discount coupon-<?php echo esc_attr(sanitize_title($code)); ?>">
                <th><?php wc_cart_totals_coupon_label($coupon); ?></th>
                <td data-title="<?php echo esc_attr(wc_cart_totals_coupon_label($coupon, false)); ?>"><?php wc_cart_totals_coupon_html($coupon); ?></td>
            </tr>
        <?php endforeach; ?>

        <?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>

            <?php do_action("woocommerce_cart_totals_before_shipping"); ?>

            <?php wc_cart_totals_shipping_html(); ?>

            <?php do_action("woocommerce_cart_totals_after_shipping"); ?>

        <?php elseif (WC()->cart->needs_shipping() && "yes" === get_option("woocommerce_enable_shipping_calc")) : ?>

            <tr class="shipping">
                <th><?php esc_html_e("Shipping", "woocommerce"); ?></th>
                <td data-title="<?php esc_attr_e("Shipping", "woocommerce"); ?>"><?php woocommerce_shipping_calculator(); ?></td>
            </tr>

        <?php endif; ?>

        <?php foreach (WC()->cart->get_fees() as $fee) : ?>
            <tr class="fee">
                <th><?php echo esc_html($fee->name); ?></th>
                <td data-title="<?php echo esc_attr($fee->name); ?>"><?php wc_cart_totals_fee_html($fee); ?></td>
            </tr>
        <?php endforeach; ?>

        <?php
        if (wc_tax_enabled() && !WC()->cart->display_prices_including_tax()) {
            $taxable_address = WC()->customer->get_taxable_address();
            $estimated_text  = "";

            if (WC()->customer->is_customer_outside_base() && !WC()->customer->has_calculated_shipping()) {
                /* translators: %s location. */
                $estimated_text = sprintf(" <small>" . esc_html__("(estimated for %s)", "woocommerce") . "</small>", WC()->countries->estimated_for_prefix($taxable_address[0]) . WC()->countries->countries[$taxable_address[0]]);
            }

            if ("itemized" === get_option("woocommerce_tax_total_display")) {
                foreach (WC()->cart->get_tax_totals() as $code => $tax) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
                    ?>
                    <tr class="tax-rate tax-rate-<?php echo esc_attr(sanitize_title($code)); ?>">
                        <th><?php echo esc_html($tax->label) . $estimated_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
                        <td data-title="<?php echo esc_attr($tax->label); ?>"><?php echo wp_kses_post($tax->formatted_amount); ?></td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <tr class="tax-total">
                    <th><?php echo esc_html(WC()->countries->tax_or_vat()) . $estimated_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
                    <td data-title="<?php echo esc_attr(WC()->countries->tax_or_vat()); ?>"><?php wc_cart_totals_taxes_total_html(); ?></td>
                </tr>
                <?php
            }
        }
        ?>

        <?php do_action("woocommerce_cart_totals_before_order_total"); ?>

        <tr class="order-total">
            <th><?php esc_html_e("Total", "woocommerce"); ?></th>
            <td data-title="<?php esc_attr_e("Total", "woocommerce"); ?>"><?php wc_cart_totals_order_total_html(); ?></td>
        </tr>

        <?php do_action("woocommerce_cart_totals_after_order_total"); ?>

    </table>

    <div class="security-badges">
        <div class="security-info">
            <div class="security-item">
                <i class="fas fa-shield-alt"></i>
                <span>Secure Checkout</span>
            </div>
            <div class="security-item">
                <i class="fas fa-lock"></i>
                <span>SSL Protected</span>
            </div>
            <div class="security-item">
                <i class="fas fa-undo"></i>
                <span>30-day Refund</span>
            </div>
        </div>
    </div>

    <div class="wc-proceed-to-checkout">
        <?php do_action("woocommerce_proceed_to_checkout"); ?>
    </div>

    <?php do_action("woocommerce_after_cart_totals"); ?>

</div>

<style>
.cart_totals {
    background: white;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border: 1px solid #eee;
}

.cart-totals-header h2 {
    font-size: 24px;
    color: #333;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid #eee;
    font-weight: 700;
}

.cart_totals .shop_table {
    margin-bottom: 25px;
    box-shadow: none;
    border-radius: 0;
}

.cart_totals .shop_table th,
.cart_totals .shop_table td {
    padding: 15px 0;
    border-bottom: 1px solid #eee;
    font-size: 16px;
}

.cart_totals .shop_table th {
    background: none;
    font-weight: 600;
    color: #495057;
    text-align: left;
}

.cart_totals .order-total th,
.cart_totals .order-total td {
    font-size: 20px;
    font-weight: 700;
    color: #2c5aa0;
    border-bottom: none;
    padding-top: 20px;
    border-top: 2px solid #2c5aa0;
}

.security-badges {
    margin: 25px 0;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.security-info {
    display: flex;
    justify-content: space-around;
    align-items: center;
}

.security-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    color: #28a745;
    font-size: 14px;
    font-weight: 600;
}

.security-item i {
    font-size: 24px;
    margin-bottom: 8px;
}

.wc-proceed-to-checkout .checkout-button {
    width: 100%;
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    border: none;
    padding: 18px 30px;
    font-size: 18px;
    font-weight: 700;
    border-radius: 8px;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 1px;
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
}

.wc-proceed-to-checkout .checkout-button:hover {
    background: linear-gradient(135deg, #218838 0%, #1ea085 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
}

@media (max-width: 768px) {
    .security-info {
        flex-direction: column;
        gap: 15px;
    }
    
    .security-item {
        flex-direction: row;
        gap: 10px;
    }
    
    .security-item i {
        margin-bottom: 0;
        font-size: 20px;
    }
}
</style>