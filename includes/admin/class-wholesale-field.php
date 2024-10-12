<?php
add_action('woocommerce_product_options_general_product_data', 'fwpampc_add_fields');
add_action('woocommerce_process_product_meta', 'fwpampc_save_fields');
/**
 * Add fields for wholesale price.
 *
 * Adds a checkbox for enabling/disabling wholesale price and
 * fields for specifying the quantity range and price for each
 * level of wholesale pricing.
 *
 * @since 1.0
 */
function fwpampc_add_fields()
{
    global $post;
    wp_nonce_field('fwpampc_save_fields_action', 'fwpampc_save_fields_nonce');
    woocommerce_wp_checkbox([
        'id' => 'enable_wholesale',
        /* translators: %d Placeholder For Enable Wholesale number  */
        'placeholder' => 'enable_wholesale',
        /* translators: %d Label For Enable Wholesale number */
        'label' => __('Enable wholesale', 'free-wholesale-pricing-and-multiple-price-categories'),
        /* translators: %d desc For Enable Wholesale quantity */
        'desc' => __('Enable wholesale price', 'free-wholesale-pricing-and-multiple-price-categories'),
        'desc_tip' => 'true',
    ]);

    for ($i = 0; $i < 3; $i++) {
        echo '<div class="wholesale">';
        echo '<h3 class="wholesale-title">' . esc_html__('Wholesale ', 'free-wholesale-pricing-and-multiple-price-categories') . esc_html($i + 1) . '</h3>';

        woocommerce_wp_checkbox(field: [
            'id' => 'enable_wholesale' .  esc_attr($i + 1) ,
            /* translators: %d placeholder For Enable Wholesale number */
            'placeholder' => sprintf(__('Enable Wholesale %d', 'free-wholesale-pricing-and-multiple-price-categories'), $i + 1),
            /* translators: %d Label For Enable Wholesale number */
            'label' => sprintf(__('Enable Wholesale %d', 'free-wholesale-pricing-and-multiple-price-categories'), $i + 1),
            /* translators: %d desc For Enable Wholesale quantity */
            'desc' => sprintf(__('Enable wholesale for quantity level %d', 'free-wholesale-pricing-and-multiple-price-categories'), $i + 1),

            'desc_tip' => 'true',
        ]);

        woocommerce_wp_text_input([
            'id' => 'Wholesale' . esc_attr($i + 1) . 'quantity',
            'placeholder' => __('From quantity', 'free-wholesale-pricing-and-multiple-price-categories'),
            'label' => __('From quantity', 'free-wholesale-pricing-and-multiple-price-categories'),
            'type' => 'number',
            'custom_attributes' => ['step' => 'any', 'min' => '1'],
        ]);

        woocommerce_wp_text_input([
            'id' => 'Wholesale' .  esc_attr($i + 1) . 'to_quantity',
            'placeholder' => __('To quantity', 'free-wholesale-pricing-and-multiple-price-categories'),
            'label' => __('To quantity', 'free-wholesale-pricing-and-multiple-price-categories'),
            'type' => 'number',
            'custom_attributes' => ['step' => 'any', 'min' => '1'],
        ]);

        woocommerce_wp_text_input([
            'id' => 'Wholesale' .  esc_attr($i + 1) . 'price',
            'placeholder' => __('Price', 'free-wholesale-pricing-and-multiple-price-categories'),
            'label' => __('Price', 'free-wholesale-pricing-and-multiple-price-categories'),
            'type' => 'number',
            'custom_attributes' => ['step' => 'any', 'min' => '0'],
        ]);

        echo '</div>';
    }
}

function fwpampc_save_fields($post_id)
{
    // Verify the nonce before processing the form
    if ( ! isset( $_POST['fwpampc_save_fields_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fwpampc_save_fields_nonce'] ) ), 'fwpampc_save_fields_action' ) ) {
        wp_die( esc_html__( 'Nonce verification failed', 'free-wholesale-pricing-and-multiple-price-categories' ) );
    }
    
    $fwpampc_enable = isset($_POST['enable_wholesale']) ? 'yes' : 'no';
    update_post_meta($post_id, 'enable_wholesale', esc_attr($fwpampc_enable));

    for ($i = 0; $i < 3; $i++) {
        $fwpampc_enable_single = isset($_POST['enable_wholesale' . ($i + 1)]) ? sanitize_text_field( wp_unslash( $_POST['enable_wholesale' . ($i + 1)] ) ) : '';
        $quantity = isset($_POST['Wholesale' . ($i + 1) . 'quantity']) ? sanitize_text_field( wp_unslash( $_POST['Wholesale' . ($i + 1) . 'quantity'] ) ) : '';
        $to_quantity = isset($_POST['Wholesale' . ($i + 1) . 'to_quantity']) ? sanitize_text_field( wp_unslash( $_POST['Wholesale' . ($i + 1) . 'to_quantity'] ) ) : '';
        $price = isset($_POST['Wholesale' . ($i + 1) . 'price']) ? sanitize_text_field( wp_unslash( $_POST['Wholesale' . ($i + 1) . 'price'] ) ) : '';

        if ($fwpampc_enable_single) {
            update_post_meta($post_id, 'enable_wholesale' . ($i + 1), esc_attr($fwpampc_enable_single));
        } else {
            update_post_meta($post_id, 'enable_wholesale' . ($i + 1), 'no');
        }

        if (!empty($quantity)) {
            fwpampc_save_quantity($i, $quantity, $to_quantity, $post_id);
        } else {
            fwpampc_disable_wholesale($i, $post_id);
        }

        if (!empty($to_quantity)) {
            fwpampc_save_to_quantity($i, $quantity, $to_quantity, $post_id);
        } else {
            fwpampc_disable_wholesale($i, $post_id);
        }

        if (!empty($price)) {
            update_post_meta($post_id, 'Wholesale' . ($i + 1) . 'price', esc_attr($price));
        } else {
            fwpampc_disable_wholesale($i, $post_id);
        }
    }
}


function fwpampc_save_quantity($i, $quantity, $to_quantity, $post_id) {
        // Verify the nonce before processing the form
        if ( ! isset( $_POST['fwpampc_save_fields_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fwpampc_save_fields_nonce'] ) ), 'fwpampc_save_fields_action' ) ) {
            wp_die( esc_html__( 'Nonce verification failed', 'free-wholesale-pricing-and-multiple-price-categories' ) );
        }
    
    $next_quantity = isset($_POST['Wholesale' . ($i + 2) . 'quantity']) ? sanitize_text_field(wp_unslash($_POST['Wholesale' . ($i + 2) . 'quantity'])) : 99999999999;
    $next_quantity_enable = isset($_POST['enable_wholesale' . ($i + 2)]) ? 'yes' : 'no';

    if ($quantity < $to_quantity) {
        if ($quantity > $next_quantity && $next_quantity_enable === 'yes') {
            update_post_meta($post_id, 'Wholesale' . ($i + 1) . 'quantity', esc_attr($next_quantity - 1));
        } else {
            update_post_meta($post_id, 'Wholesale' . ($i + 1) . 'quantity', esc_attr($quantity));
        }
    } else {
        update_post_meta($post_id, 'Wholesale' . ($i + 1) . 'quantity', esc_attr($to_quantity));
    }
}
function fwpampc_save_to_quantity($i, $quantity, $to_quantity, $post_id)
{
    // Verify the nonce before processing the form
    if ( ! isset( $_POST['fwpampc_save_fields_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fwpampc_save_fields_nonce'] ) ), 'fwpampc_save_fields_action' ) ) {
        wp_die( esc_html__( 'Nonce verification failed', 'free-wholesale-pricing-and-multiple-price-categories' ) );
    }
    $next_quantity = isset($_POST['Wholesale' . ($i + 2) . 'quantity']) ? sanitize_text_field(wp_unslash($_POST['Wholesale' . ($i + 2) . 'quantity'])) : 99999999999;
    $next_to_quantity_enable = isset($_POST['enable_wholesale' . ($i + 2)]);

    if ($quantity < $to_quantity) {
        if ($to_quantity > $next_quantity && $next_to_quantity_enable) {
            update_post_meta($post_id, 'Wholesale' . ($i + 1) . 'to_quantity', esc_attr($next_quantity - 1));
        } else {
            update_post_meta($post_id, 'Wholesale' . ($i + 1) . 'to_quantity', esc_attr($to_quantity));
        }
    } else {
        update_post_meta($post_id, 'Wholesale' . ($i + 1) . 'to_quantity', esc_attr($quantity));
    }
}

function fwpampc_disable_wholesale($i, $post_id)
{
    update_post_meta($post_id, 'enable_wholesale' . ($i + 1), 'no');
}
