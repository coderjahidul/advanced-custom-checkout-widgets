<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WooCommerce')) {
    echo "WooCommerce is required.";
    return;
}

$checkout = WC()->checkout();

// Handle Pre-selected Products
if (!empty($preselected_product_ids) && is_array($preselected_product_ids) && !is_admin()) {
    // Ensure WooCommerce session is started
    if (WC()->session && !WC()->session->has_session()) {
        WC()->session->set_customer_session_cookie(true);
    }

    $cart = WC()->cart;

    // Ensure cart is initialized
    if (is_null($cart)) {
        wc_load_cart();
        $cart = WC()->cart;
    }

    if ($cart) {
        $added_new = false;

        foreach ($preselected_product_ids as $pre_id) {
            $pre_id = absint($pre_id);
            if ($pre_id <= 0)
                continue;

            // Check if product or variation is already in cart
            $in_cart = false;
            foreach ($cart->get_cart() as $cart_item) {
                if ($cart_item['product_id'] == $pre_id || $cart_item['variation_id'] == $pre_id) {
                    $in_cart = true;
                    break;
                }
            }

            if (!$in_cart) {
                $product = wc_get_product($pre_id);
                if ($product && $product->is_purchasable() && $product->is_in_stock()) {
                    if ($product->is_type('variation')) {
                        // If it's a variation, add it correctly
                        $cart->add_to_cart($product->get_parent_id(), 1, $pre_id);
                    } elseif ($product->is_type('simple')) {
                        // If it's a simple product, add it normally
                        $cart->add_to_cart($pre_id);
                    }
                    // Note: 'variable' products (parents) cannot be added directly without a variation ID
                    $added_new = true;
                }
            }
        }

        // If we added products, recalculate totals to ensure order review is accurate
        if ($added_new) {
            $cart->calculate_totals();
        }
    }
}


// Output notices
woocommerce_output_all_notices();

?>
<div class="adv-checkout-wrapper">
    <!-- Products Grid -->
    <?php if (!empty($product_ids)): ?>
        <div class="adv-products-grid">
            <h3>
                <?php esc_html_e('Select Products', 'advanced-checkout'); ?>
            </h3>
            <div class="adv-products">
                <?php
                foreach ($product_ids as $pid) {
                    $product = wc_get_product($pid);
                    if (!$product)
                        continue;
                    ?>
                    <div class="adv-product-card" data-product-id="<?php echo esc_attr($pid); ?>">
                        <div class="product-img">
                            <?php echo $product->get_image('thumbnail'); ?>
                        </div>
                        <div class="product-info">
                            <h4>
                                <?php echo $product->get_name(); ?>
                            </h4>
                            <p class="price">
                                <?php echo $product->get_price_html(); ?>
                            </p>
                            <?php if ($product->is_type('variable')): ?>
                                <a href="<?php echo esc_url($product->get_permalink()); ?>" class="adv-add-to-cart-variable">
                                    <?php esc_html_e('Select Options', 'advanced-checkout'); ?>
                                </a>
                            <?php else: ?>
                                <div class="actions">
                                    <input type="number" value="1" min="1" class="adv-qty" />
                                    <button class="adv-add-to-cart">
                                        <?php esc_html_e('Add', 'advanced-checkout'); ?>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- WooCommerce Checkout Form -->
    <div class="adv-checkout-form-container">
        <?php if ($checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in()): ?>
            <?php echo esc_html(apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'woocommerce'))); ?>
            <?php return; ?>
        <?php endif; ?>

        <form name="checkout" method="post" class="checkout woocommerce-checkout adv-checkout"
            action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">

            <div class="adv-checkout-columns">
                <!-- Customer Details Col -->
                <div class="adv-checkout-col-1" id="customer_details">
                    <?php if ($checkout->get_checkout_fields()): ?>
                        <?php do_action('woocommerce_checkout_before_customer_details'); ?>
                        <div class="adv-billing-fields">
                            <?php do_action('woocommerce_checkout_billing'); ?>
                        </div>
                        <div class="adv-shipping-fields">
                            <?php do_action('woocommerce_checkout_shipping'); ?>
                        </div>
                        <?php do_action('woocommerce_checkout_after_customer_details'); ?>
                    <?php endif; ?>
                </div>

                <!-- Order Review & Payment Sticky Sidebar -->
                <div class="adv-checkout-col-2">
                    <div class="adv-sticky-sidebar">
                        <h3 id="order_review_heading">
                            <?php esc_html_e('Your order', 'woocommerce'); ?>
                        </h3>
                        <?php do_action('woocommerce_checkout_before_order_review'); ?>

                        <div id="order_review" class="woocommerce-checkout-review-order">
                            <?php do_action('woocommerce_checkout_order_review'); ?>
                        </div>

                        <?php do_action('woocommerce_checkout_after_order_review'); ?>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>