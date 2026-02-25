# Developer Documentation - Advanced Custom Checkout Widgets

This document provides technical details about the plugin's architecture, hooks, and AJAX flow for developers.

## 🏗️ Architecture Overview

The plugin follows a modular structure to separate concerns between Elementor integration, WooCommerce logic, and asset management.

### 1. Main Loader (`includes/class-plugin-loader.php`)
The `Advanced_Checkout_Loader` class is the central hub. It:
*   Registers the Elementor widget category (`advanced-checkout`).
*   Registers the Elementor widget itself.
*   Enqueues frontend styles and scripts.
*   Localizes data for AJAX (URL and Nonce).

### 2. AJAX Handler (`includes/class-ajax-handler.php`)
Handles asynchronous interactions:
*   **Action**: `adv_add_to_cart`
    *   Adds products to the cart without page reload.
    *   Triggers `WC_AJAX::get_refreshed_fragments()` to update WooCommerce fragments (like mini-cart and order review).

### 3. Elementor Widget (`widgets/class-checkout-widget.php`)
Defines the user interface for editors:
*   Allows entry of Product IDs for the grid.
*   Allows entry of Pre-selected Product IDs.
*   Provides styling controls that map to CSS variables or direct selectors.

### 4. Rendering Template (`templates/checkout-template.php`)
A clean template file that:
*   Handles the logic for automatically adding pre-selected products.
*   Renders the product selection grid.
*   Wraps the standard `[woocommerce_checkout]` logic but with unique classes for custom styling.

## 🔌 Hooks & Filters

### PHP Filters
*   `woocommerce_available_payment_gateways`: Used in `class-ajax-handler.php` to potentially filter or reorder gateways.
*   Standard WooCommerce filters (`woocommerce_add_to_cart_validation`, etc.) are respected during the AJAX add-to-cart process.

### CSS Customization
The plugin uses specific class prefixes (`adv-checkout-`) to avoid conflicts. Key CSS classes:
*   `.adv-checkout-wrapper`: The main container.
*   `.adv-products-grid`: Container for the product selection cards.
*   `.adv-sticky-sidebar`: The sticky container for order review.

## 🚀 Development Workflow

1.  **Adding a New Control**: Add it in `class-checkout-widget.php` under `register_controls()`.
2.  **Modifying AJAX Logic**: Update `class-ajax-handler.php`.
3.  **Changing Layout**: Modify `templates/checkout-template.php` and corresponding CSS in `assets/css/checkout.css`.

## 🧪 Testing
When developing, ensure you test:
*   Adding regular products to cart.
*   Adding variable products (should redirect to product page).
*   Automatic pre-selection of products via Widget settings.
*   Sticky sidebar behavior on different screen sizes.
