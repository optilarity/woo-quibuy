<?php
/**
 * Plugin Name: Woo QuiBuy
 * Plugin URI: https://puleeno.com/plugins/woo-quibuy
 * Description: A flexible Quick Buy dialog for WooCommerce with form builder and presets.
 * Version: 1.0.1
 * Author: Puleeno Nguyen
 * Author URI: https://puleeno.com
 * Text Domain: woo-quibuy
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

$woo_quibuy_data = get_file_data(__FILE__, array('Version' => 'Version'), 'plugin');
define('WOO_QUIBUY_VERSION', $woo_quibuy_data['Version'] ?? '1.0.0');
define('WOO_QUIBUY_DIR', plugin_dir_path(__FILE__));
define('WOO_QUIBUY_URL', plugin_dir_url(__FILE__));

require_once WOO_QUIBUY_DIR . 'includes/class-woo-quibuy-loader.php';

function woo_quibuy_init()
{
    $plugin = new Woo_QuiBuy_Loader();
    $plugin->run();
}
add_action('plugins_loaded', 'woo_quibuy_init');
