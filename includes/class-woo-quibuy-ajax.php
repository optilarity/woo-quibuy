<?php
/**
 * Ajax Handler
 *
 * @package Woo_QuiBuy
 */

defined('ABSPATH') || exit;

class Woo_QuiBuy_Ajax
{

    public function init()
    {
        add_action('wp_ajax_woo_quibuy_process_order', array($this, 'process_order'));
        add_action('wp_ajax_nopriv_woo_quibuy_process_order', array($this, 'process_order'));
    }

    public function process_order()
    {
        check_ajax_referer('woo-quibuy-nonce', 'nonce');

        $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
        $quantity = isset($_POST['quantity']) ? absint($_POST['quantity']) : 1;

        if (!$product_id) {
            wp_send_json_error(array('message' => __('Sản phẩm không hợp lệ.', 'woo-quibuy')));
        }

        $product = wc_get_product($product_id);
        if (!$product) {
            wp_send_json_error(array('message' => __('Sản phẩm không tồn tại.', 'woo-quibuy')));
        }

        // Create Order
        try {
            $order = wc_create_order();
            if (is_wp_error($order) || !$order) {
                throw new Exception(__('Không thể tạo đơn hàng. Vui lòng thử lại.', 'woo-quibuy'));
            }

            $order->add_product($product, $quantity);

            // Handle Name
            $full_name = isset($_POST['customer_name']) ? sanitize_text_field($_POST['customer_name']) : '';
            $parts = explode(' ', $full_name);
            $last_name = array_pop($parts);
            $first_name = implode(' ', $parts);
            if (empty($first_name)) {
                $first_name = $last_name;
                $last_name = '';
            }

            $phone = isset($_POST['customer_phone']) ? sanitize_text_field($_POST['customer_phone']) : '';
            $address_text = isset($_POST['customer_address']) ? sanitize_textarea_field($_POST['customer_address']) : '';

            // Set Address
            $address = array(
                'first_name' => $first_name,
                'last_name' => $last_name,
                'phone' => $phone,
                'address_1' => $address_text,
                'email' => $phone . '@placeholder.com', // Dummy email to ensure validation passes if strictly required
            );

            $order->set_address($address, 'billing');
            $order->set_address($address, 'shipping');

            // Calculate totals
            $order->calculate_totals();

            // Default to COD or Pending
            $order->set_payment_method('cod');
            $order->set_payment_method_title('Thanh toán khi nhận hàng');

            $order->update_status('processing', __('Đơn hàng tạo từ Quick Buy.', 'woo-quibuy'));
            $order->save();

            wp_send_json_success(array(
                'message' => __('Đặt hàng thành công!', 'woo-quibuy'),
                'redirect_url' => $order->get_checkout_order_received_url(),
            ));

        } catch (Exception $e) {
            error_log('Woo QuiBuy Error: ' . $e->getMessage());
            wp_send_json_error(array('message' => $e->getMessage()));
        }
    }
}
