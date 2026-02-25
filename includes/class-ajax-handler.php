<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Advanced_Checkout_Ajax
{

    private static $_instance = null;

    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __construct()
    {
        add_action('wp_ajax_adv_add_to_cart', [$this, 'add_to_cart']);
        add_action('wp_ajax_nopriv_adv_add_to_cart', [$this, 'add_to_cart']);

        // Provide a filter to auto-select payment gateway if desired
        add_filter('woocommerce_available_payment_gateways', [$this, 'filter_gateways']);
    }

    public function add_to_cart()
    {
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
}
