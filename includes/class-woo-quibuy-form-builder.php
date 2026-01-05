<?php
/**
 * Form Builder Class
 *
 * @package Woo_QuiBuy
 */

defined('ABSPATH') || exit;

class Woo_QuiBuy_Form_Builder
{

    /**
     * Get Preset Fields
     * 
     * @param string $preset Preset name.
     * @return array Fields configuration.
     */
    public function get_preset($preset = 'bachhoaxanh')
    {
        $presets = array(
            'bachhoaxanh' => array(
                array(
                    'id' => 'customer_name',
                    'type' => 'text',
                    'label' => 'Họ và tên',
                    'placeholder' => 'Nhập họ tên',
                    'required' => true,
                    'class' => 'form-row-wide'
                ),
                array(
                    'id' => 'customer_phone',
                    'type' => 'tel',
                    'label' => 'Số điện thoại',
                    'placeholder' => 'Nhập số điện thoại',
                    'required' => true,
                    'class' => 'form-row-wide'
                ),
                array(
                    'id' => 'customer_address',
                    'type' => 'textarea',
                    'label' => 'Địa chỉ',
                    'placeholder' => 'Số nhà, tên đường, phường/xã...',
                    'required' => true,
                    'class' => 'form-row-wide'
                ),
            ),
        );

        $result = isset($presets[$preset]) ? $presets[$preset] : $presets['bachhoaxanh'];
        return apply_filters('woo_quibuy_get_preset', $result, $preset);
    }

    public function render_form($preset = 'bachhoaxanh')
    {
        $fields = $this->get_preset($preset);
        ob_start();
        ?>
        <form class="woo-quibuy-form" id="woo-quibuy-checkout-form">
            <h3>
                <?php esc_html_e('Đặt hàng nhanh', 'woo-quibuy'); ?>
            </h3>
            <div class="woo-quibuy-product-summary">
                <!-- Product details will be populated via JS -->
            </div>

            <div class="woo-quibuy-fields">
                <?php foreach ($fields as $field): ?>
                    <?php $this->render_field($field); ?>
                <?php endforeach; ?>
            </div>

            <input type="hidden" name="product_id" id="woo-quibuy-product-id" value="">
            <input type="hidden" name="quantity" id="woo-quibuy-quantity" value="1">

            <button type="submit" class="woo-quibuy-submit button alt">
                <?php esc_html_e('Hoàn tất đơn hàng', 'woo-quibuy'); ?>
            </button>
        </form>
        <?php
        return ob_get_clean();
    }

    private function render_field($field)
    {
        $type = isset($field['type']) ? $field['type'] : 'text';
        $id = isset($field['id']) ? $field['id'] : '';
        $label = isset($field['label']) ? $field['label'] : '';
        $placeholder = isset($field['placeholder']) ? $field['placeholder'] : '';
        $class = isset($field['class']) ? $field['class'] : '';
        $required = isset($field['required']) && $field['required'] ? 'required' : '';

        ?>
        <div class="form-row <?php echo esc_attr($class); ?>">
            <label for="<?php echo esc_attr($id); ?>">
                <?php echo esc_html($label); ?>
                <?php if ($required): ?>
                    <span class="required">*</span>
                <?php endif; ?>
            </label>

            <?php if ('select' === $type):
                $options = isset($field['options']) ? $field['options'] : array();
                ?>
                <select id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($id); ?>" <?php echo $required; ?>>
                    <?php foreach ($options as $key => $value): ?>
                        <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($value); ?></option>
                    <?php endforeach; ?>
                </select>
            <?php elseif ('textarea' === $type): ?>
                <textarea id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($id); ?>"
                    placeholder="<?php echo esc_attr($placeholder); ?>" <?php echo $required; ?>></textarea>
            <?php else: ?>
                <input type="<?php echo esc_attr($type); ?>" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($id); ?>"
                    placeholder="<?php echo esc_attr($placeholder); ?>" <?php echo $required; ?>>
            <?php endif; ?>
        </div>
        <?php
    }
}
