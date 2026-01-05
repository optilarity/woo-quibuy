<?php
/**
 * Assets Manager
 *
 * @package Woo_QuiBuy
 */

defined('ABSPATH') || exit;

class Woo_QuiBuy_Assets
{

    public function enqueue_scripts()
    {
        // Enqueue CSS
        wp_enqueue_style(
            'woo-quibuy-style',
            WOO_QUIBUY_URL . 'assets/dist/css/style.css',
            array(),
            WOO_QUIBUY_VERSION
        );

        // Enqueue JS
        wp_enqueue_script(
            'woo-quibuy-script',
            WOO_QUIBUY_URL . 'assets/dist/js/app.js',
            array('jquery'),
            WOO_QUIBUY_VERSION,
            true
        );

        wp_localize_script('woo-quibuy-script', 'wooQuiBuyParams', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('woo-quibuy-nonce'),
        ));
    }
}
