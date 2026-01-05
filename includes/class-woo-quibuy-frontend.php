<?php
/**
 * Frontend Rendering
 *
 * @package Woo_QuiBuy
 */

defined('ABSPATH') || exit;

class Woo_QuiBuy_Frontend
{

    public function add_quick_buy_button()
    {
        global $product;
        if (!$product) {
            return;
        }

        $image_id = $product->get_image_id();
        $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'thumbnail') : wc_placeholder_img_src();
        $title = $product->get_name();
        $price = $product->get_price();

        // Integration with DentalPart Quantity Pricing
        $quantity_pricing_json = '';
        if (class_exists('CrawlFlow\DentalPart\Model\QuantityPrice') && class_exists('CrawlFlow\DentalPart\Features\FlatsomeQuantityPricing')) {
            $pricing_data = \CrawlFlow\DentalPart\Model\QuantityPrice::getPrices($product->get_id());

            // Convert to VND
            $pricing_data_vnd = [];
            foreach ($pricing_data as $qty => $val) {
                $pricing_data_vnd[$qty] = \CrawlFlow\DentalPart\Features\FlatsomeQuantityPricing::usdToVnd((float) $val);
            }

            // Fix 0 price issue by calculating base price
            if ($price == 0 && !empty($pricing_data)) {
                $tiers = $pricing_data;
                ksort($tiers);
                $first_qty = key($tiers);
                $first_tier = reset($tiers);
                $spread = $first_qty > 1 ? 1.25 : 1;
                $base_usd = $first_tier * $spread;
                $price = \CrawlFlow\DentalPart\Features\FlatsomeQuantityPricing::usdToVnd($base_usd);
            }

            if (!empty($pricing_data_vnd)) {
                $quantity_pricing_json = json_encode($pricing_data_vnd);
            }
        }

        echo '<button type="button" class="button woo-quibuy-btn" 
            data-product_id="' . esc_attr($product->get_id()) . '"
            data-product_image="' . esc_attr($image_url) . '"
            data-product_title="' . esc_attr($title) . '"
            data-price="' . esc_attr($price) . '"
            data-product_price="' . esc_attr($price) . '"
            data-quantity-pricing="' . esc_attr($quantity_pricing_json) . '">';
        echo esc_html__('Mua Ngay', 'woo-quibuy');
        echo '</button>';
    }

    public function render_dialog()
    {
        // Check if we are on a WooCommerce page or have products
        // For now, load on all pages or conditionally
        ?>
        <div id="woo-quibuy-modal" class="woo-quibuy-modal" style="display:none;" aria-hidden="true">
            <div class="woo-quibuy-overlay"></div>
            <div class="woo-quibuy-content">
                <button class="woo-quibuy-close">&times;</button>
                <div class="woo-quibuy-body">
                    <!-- Form will be injected here or pre-rendered -->
                    <div id="woo-quibuy-form-container">
                        <?php
                        // Default preset: Bach Hoa Xanh style
                        $form_builder = new Woo_QuiBuy_Form_Builder();
                        echo $form_builder->render_form('bachhoaxanh');
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
