<?php
/**
 * Main Loader Class
 *
 * @package Woo_QuiBuy
 */

defined('ABSPATH') || exit;

class Woo_QuiBuy_Loader
{

    public function run()
    {
        $this->includes();
        $this->hooks();
    }

    private function includes()
    {
        require_once WOO_QUIBUY_DIR . 'includes/class-woo-quibuy-assets.php';
        require_once WOO_QUIBUY_DIR . 'includes/class-woo-quibuy-frontend.php';
        require_once WOO_QUIBUY_DIR . 'includes/class-woo-quibuy-form-builder.php';
        require_once WOO_QUIBUY_DIR . 'includes/class-woo-quibuy-ajax.php';
    }

    private function hooks()
    {
        $assets = new Woo_QuiBuy_Assets();
        add_action('wp_enqueue_scripts', array($assets, 'enqueue_scripts'));

        $frontend = new Woo_QuiBuy_Frontend();
        add_action('wp_footer', array($frontend, 'render_dialog'));

        // Hook for "Quick Buy" button in single product summary
        add_action('woocommerce_single_product_summary', array($frontend, 'add_quick_buy_button'), 35);

        $ajax = new Woo_QuiBuy_Ajax();
        $ajax->init();
    }
}
