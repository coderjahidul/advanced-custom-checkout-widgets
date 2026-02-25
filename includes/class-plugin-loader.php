<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Main Loader Class
 * 
 * Responsible for initializing the plugin, registering Elementor widgets,
 * and enqueuing frontend assets.
 */
class Advanced_Checkout_Loader
{

    private static $_instance = null;

    /**
     * Singleton instance of the class
     * 
     * @return Advanced_Checkout_Loader
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
        // Load dependencies
        $this->includes();

        // Hooks
        add_action('elementor/widgets/register', [$this, 'register_widgets']);
        add_action('elementor/elements/categories_registered', [$this, 'register_category']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);

        // Initialize AJAX
        Advanced_Checkout_Ajax::instance();
    }

    /**
     * Include dependency files
     */
    private function includes()
    {
        require_once ADVANCED_CHECKOUT_PATH . 'includes/class-ajax-handler.php';
    }

    /**
     * Register Elementor Category
     * 
     * @param \Elementor\Elements_Manager $elements_manager
     */
    public function register_category($elements_manager)
    {
        $elements_manager->add_category(
            'advanced-checkout',
            [
                'title' => esc_html__('Advanced Checkout', 'advanced-checkout'),
                'icon' => 'fa fa-shopping-cart',
            ]
        );
    }

    /**
     * Register Elementor Widget
     * 
     * @param \Elementor\Widgets_Manager $widgets_manager
     */
    public function register_widgets($widgets_manager)
    {
        require_once ADVANCED_CHECKOUT_PATH . 'widgets/class-checkout-widget.php';
        $widgets_manager->register(new \Elementor_Advanced_Checkout_Widget());
    }

    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_scripts()
    {
        // Theme style
        wp_enqueue_style('woodmart-style', get_template_directory_uri() . '/style.css', [], ADVANCED_CHECKOUT_VERSION);
        wp_register_style('advanced-checkout-style', ADVANCED_CHECKOUT_URL . 'assets/css/checkout.css', [], ADVANCED_CHECKOUT_VERSION);
        wp_register_script('advanced-checkout-script', ADVANCED_CHECKOUT_URL . 'assets/js/checkout.js', ['jquery', 'wc-checkout'], ADVANCED_CHECKOUT_VERSION, true);

        // Localize script for AJAX
        wp_localize_script('advanced-checkout-script', 'adv_checkout_obj', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('adv_checkout_nonce'),
        ]);
    }
}