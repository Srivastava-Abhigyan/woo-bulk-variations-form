<?php
/*
Plugin Name: WooCommerce Bulk Variations Form
Description: Add multiple variations of a product to the cart in bulk.
Version: 1.1
*/

if (!defined('ABSPATH')) exit;

class WC_Bulk_Variations_Form {

    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        add_action('woocommerce_after_variations_form', array($this, 'render_bulk_form'));
        add_action('wp_ajax_bulk_add_to_cart', array($this, 'bulk_add_to_cart'));
        add_action('wp_ajax_nopriv_bulk_add_to_cart', array($this, 'bulk_add_to_cart'));

        // New AJAX handlers for getting variation price
        add_action('wp_ajax_get_variation_price', array($this, 'get_variation_price'));
        add_action('wp_ajax_nopriv_get_variation_price', array($this, 'get_variation_price'));
    }

    public function enqueue_assets() {
        if (is_product()) {
            wp_enqueue_style('bulk-variations-css', plugin_dir_url(__FILE__) . 'css/bulk-variations.css');
            wp_enqueue_script('bulk-variations-js', plugin_dir_url(__FILE__) . 'js/bulk-variations.js', array('jquery'), null, true);

            $format_price_js = 'function(price) { return "' . get_woocommerce_currency_symbol() . '" + price.toFixed(2); }';

            wp_localize_script('bulk-variations-js', 'bulk_vars', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('bulk-variations-nonce'),
                'format_price' => $format_price_js,
                'price_zero_html' => wc_price(0)
            ));
        }

        if (is_order_received_page()) {
            wp_add_inline_script('bulk-variations-js', 'localStorage.clear();');
        }
    }

    public function render_bulk_form() {
        global $product;
        if ($product->is_type('variable')) {
            // echo 'Product is a variable product';
            // echo include plugin_dir_path(__FILE__) . 'templates/bulk-form.php';
            include plugin_dir_path(__FILE__) . 'templates/bulk-form.php';
        }
    }

    public function bulk_add_to_cart() {
        check_ajax_referer('bulk-variations-nonce', 'nonce');

        $product_id = absint($_POST['product_id']);
        // echo $product_id;
        $product = wc_get_product($product_id);
        // echo $product;

        if (!$product || !$product->is_type('variable') || !isset($_POST['rows'])) {
            wp_send_json_error('Invalid product or data.');
        }

        $added = [];
        $error_messages = [];

        foreach ($_POST['rows'] as $row) {
            $attributes = array_map('sanitize_text_field', $row['attributes']);
            // echo $attributes;
            $quantity = absint($row['quantity']);
            // echo $quantity;

            if ($quantity <= 0 || empty($attributes)) {
                continue;
            }

            $variation_id = $this->find_matching_variation_id($product, $attributes);
            // echo $variation_id;

            if ($variation_id) {
                // echo 'Variation ID exists';
                $result = WC()->cart->add_to_cart($variation_id, $quantity);
                // echo $result;
                if ($result) {
                    // echo 'Result found';
                    $added[] = $variation_id;
                } else {
                    $variation = wc_get_product($variation_id);
                    // echo $variation;
                    if ($variation && !$variation->is_in_stock()) {
                        // echo 'Check in stock';
                        $error_messages[] = sprintf(__('Variation %s is out of stock.', 'woocommerce'), $variation->get_name());
                    } 
                    elseif ($variation && $quantity > $variation->get_stock_quantity()) {
                        // echo 'Check stock quantity';
                        // echo $variation->get_stock_quantity();
                        // echo $variation->get_name();
                        $error_messages[] = sprintf(__('Only %d units of variation %s are available.', 'woocommerce'), $variation->get_stock_quantity(), $variation->get_name());
                    } 
                    else {
                        // echo 'Error found';
                        $error_messages[] = __('Unable to add variation to cart.', 'woocommerce');
                    }
                }
            } 
            else {
                // echo 'Variation ID not found';
                $error_messages[] = __('No matching variation found.', 'woocommerce');
            }
        }

        if (!empty($added)) {
            wp_send_json_success([
                'added' => $added,
                'cart_url' => wc_get_cart_url()
            ]);
        } else {
            $error_message = !empty($error_messages) ? implode(' ', $error_messages) : __('No valid variations matched.', 'woocommerce');
            wp_send_json_error($error_message);
        }
    }

    public function get_variation_price() {
        check_ajax_referer('bulk-variations-nonce', 'nonce');

        $product_id = absint($_POST['product_id']);
        // echo $product_id;
        $attributes = isset($_POST['attributes']) ? array_map('sanitize_text_field', $_POST['attributes']) : [];
        // echo $attributes;

        $product = wc_get_product($product_id);
        // echo $product;

        if (!$product || !$product->is_type('variable') || empty($attributes)) {
            wp_send_json_error('Invalid product or attributes.');
        }

        $variation_id = $this->find_matching_variation_id($product, $attributes);
        // echo $variation_id;

        if (!$variation_id) {
            wp_send_json_error('No matching variation found.');
        }

        $variation = wc_get_product($variation_id);
        // echo $variation;

        if (!$variation) {
            wp_send_json_error('Invalid variation.');
        }

        $price_html = wc_price($variation->get_price());
        // echo $price_html;

        wp_send_json_success(['price_html' => $price_html]);
    }

    private function find_matching_variation_id($product, $attributes) {
        if (!$product || !$product->is_type('variable')) {
            // echo 'Invalid product or product is not variable';
            return 0;
        }

        $formatted_attributes = [];
        foreach ($attributes as $key => $value) {
            $formatted_attributes['attribute_' . $key] = $value;
        }

        $variation_id = $product->get_matching_variation($formatted_attributes);
        error_log('Matching variation ID: ' . $variation_id);

        return $variation_id ?: 0;
    }   
}

new WC_Bulk_Variations_Form();