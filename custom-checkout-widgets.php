<?php
/**
 * Plugin Name: Advanced Custom Checkout Widgets
 * Plugin URI: https://github.com/coderjahidul/advanced-custom-checkout-widgets
 * Description: Build an advanced custom checkout plugin for WooCommerce that works with Elementor. Support single/multiple product selection, AJAX add-to-cart system, dynamic shipping loading, and modern UI.
 * Version: 1.0.6
 * Author: Grocoder Software Solutions
 * Author URI: https://www.facebook.com/grocoder
 * Text Domain: advanced-checkout
 * Domain Path: /languages
 * 
 * @package AdvancedCheckout
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

define('ADVANCED_CHECKOUT_VERSION', '1.0.6');
define('ADVANCED_CHECKOUT_PATH', plugin_dir_path(__FILE__));
define('ADVANCED_CHECKOUT_URL', plugin_dir_url(__FILE__));

// Require main loader class
require_once ADVANCED_CHECKOUT_PATH . 'includes/class-plugin-loader.php';

// Initialize the plugin
function advanced_checkout_init()
{
    Advanced_Checkout_Loader::instance();
}
add_action('plugins_loaded', 'advanced_checkout_init');
