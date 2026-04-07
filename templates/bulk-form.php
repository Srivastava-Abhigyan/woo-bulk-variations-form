<div class="wc-bulk-variations-form" data-product_id="<?php echo esc_attr($product->get_id()); ?>">
    <h3><?php esc_html_e('Bulk Order', 'woocommerce'); ?></h3>
    <table class="bulk-variations-table">
        <thead>
            <tr>
                <th><?php esc_html_e('Product Name', 'woocommerce'); ?></th>
                <?php
                $attributes = $product->get_variation_attributes();
                foreach ($attributes as $attribute_name => $options) {
                    echo '<th>' . wc_attribute_label($attribute_name) . '</th>';
                }
                ?>
                <th><?php esc_html_e('Price', 'woocommerce'); ?></th>
                <th><?php esc_html_e('Quantity', 'woocommerce'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php for ($i = 0; $i < 5; $i++) : ?>
                <tr>
                    <td class="bulk-product-name"><?php echo esc_html($product->get_name()); ?></td>
                    <?php foreach ($attributes as $attribute_name => $options) : ?>
                        <td>
                            <select class="bulk-attribute" name="attribute_<?php echo esc_attr($attribute_name); ?>[]">
                                <option value=""><?php esc_html_e('Choose an option', 'woocommerce'); ?></option>
                                <?php foreach ($options as $option) : ?>
                                    <option value="<?php echo esc_attr(sanitize_title($option)); ?>"><?php echo esc_html($option); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    <?php endforeach; ?>
                    <td class="bulk-price"><?php echo wc_price(0); ?></td>
                    <td>
                        <input type="number" class="bulk-qty" min="0" value="0">
                    </td>
                </tr>
            <?php endfor; ?>
        </tbody>
    </table>
    <div class="bulk-summary">
        <p><?php esc_html_e('Total Items:', 'woocommerce'); ?> <span class="bulk-total-items">0</span></p>
        <p><?php esc_html_e('Grand Total:', 'woocommerce'); ?> <span class="bulk-grand-total"><?php echo wc_price(0); ?></span></p>
    </div>
    <button class="button bulk-add-to-cart"><?php esc_html_e('Add to Cart', 'woocommerce'); ?></button>
</div>
