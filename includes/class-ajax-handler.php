<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * AJAX Handler Class
 * 
 * Manages all AJAX requests and specific WooCommerce checkout filter logic.
 */
class Advanced_Checkout_Ajax
{

    private static $_instance = null;

    /**
     * Singleton instance of the class
     * 
     * @return Advanced_Checkout_Ajax
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor
     */
    private function __construct()
    {
        add_action('wp_ajax_adv_add_to_cart', [$this, 'add_to_cart']);
        add_action('wp_ajax_nopriv_adv_add_to_cart', [$this, 'add_to_cart']);

        // Provide a filter to auto-select payment gateway if desired
        add_filter('woocommerce_available_payment_gateways', [$this, 'filter_gateways']);

        // Product wise free delivery filter
        add_filter('woocommerce_package_rates', [$this, 'dynamic_free_shipping_by_class'], 100, 2);
    }

    /**
     * AJAX handler to add product to cart
     * 
     * Validates nonce and product data before adding to WooCommerce cart.
     * Returns refreshed fragments upon success.
     */
    public function add_to_cart()
    {
        // Ensure session is started for new users
        if (WC()->session && !WC()->session->has_session()) {
            WC()->session->set_customer_session_cookie(true);
        }

        check_ajax_referer('adv_checkout_nonce', 'nonce');

        $product_id = apply_filters('woocommerce_add_to_cart_product_id', absint($_POST['product_id']));
        $quantity = empty($_POST['quantity']) ? 1 : wc_stock_amount(wp_unslash($_POST['quantity']));
        $variation_id = isset($_POST['variation_id']) ? absint($_POST['variation_id']) : 0;

        $passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity);

        if ($passed_validation && WC()->cart->add_to_cart($product_id, $quantity, $variation_id)) {
            WC_AJAX::get_refreshed_fragments();
        } else {
            wp_send_json_error();
        }
        wp_die();
    }

    /**
     * Filter available payment gateways
     * 
     * Can be used to prioritize or hide specific gateways for the custom checkout.
     * 
     * @param array $available_gateways
     * @return array
     */
    public function filter_gateways($available_gateways)
    {
        if (is_admin()) {
            return $available_gateways;
        }

        // Feature: Filter inactive gateways - WC already handles this but here we can enforce a default logic
        // Such as prioritizing Stripe or Cash on Delivery if configured
        if (isset($available_gateways['cod'])) {
            // Keep COD if it's there
        }

        return $available_gateways;
    }

    /**
     * Product wise free delivery function
     * 
     * If any product in the cart has the shipping class 'free-shipping',
     * all other shipping rates are removed and a custom Free Shipping rate is added.
     * 
     * @param array $rates
     * @param array $package
     * @return array
     */
    public function dynamic_free_shipping_by_class($rates, $package)
    {
        $free_shipping_class = 'free-shipping'; // Ensure this matches your Shipping Class slug
        $found = false;

        // Check items in the shipping package
        foreach ($package['contents'] as $item) {
            $product = $item['data'];

            if ($product && $product->get_shipping_class() === $free_shipping_class) {
                $found = true;
                break;
            }
        }

        if ($found) {
            $free_shipping_label = esc_html__('ফ্রি ডেলিভারি', 'advanced-checkout');

            // Remove all existing shipping methods
            $rates = array();

            // Create the new Free Shipping rate
            $new_rate = new WC_Shipping_Rate();
            $new_rate->set_id('dynamic_free_shipping');
            $new_rate->set_method_id('free_shipping');
            $new_rate->set_instance_id(0);
            $new_rate->set_label($free_shipping_label);
            $new_rate->set_cost(0);
            $new_rate->set_taxes(array());

            $rates['dynamic_free_shipping'] = $new_rate;
        }

        return $rates;
    }

}