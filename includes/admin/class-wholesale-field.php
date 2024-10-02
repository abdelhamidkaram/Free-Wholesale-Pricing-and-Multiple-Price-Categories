<?php
add_action(hook_name: 'woocommerce_product_options_general_product_data', callback: 'fwfw_add_fields');
add_action(hook_name: 'woocommerce_process_product_meta', callback: 'fwfw_save_fields');

/**
 * Add fields for wholesale price.
 *
 * Adds a checkbox for enabling/disabling wholesale price and
 * fields for specifying the quantity range and price for each
 * level of wholesale pricing.
 *
 * @since 1.0
 */
function fwfw_add_fields()
{
    global $post;

    woocommerce_wp_checkbox([
        'id' => 'enable_wholesale',
        /* translators: %d Placeholder For Enable Wholesale number  */
        'placeholder' => 'enable_wholesale',
        /* translators: %d Label For Enable Wholesale number */
        'label' => __('Enable wholesale', 'fwfw-domain'),
        /* translators: %d desc For Enable Wholesale quantity */
        'desc' => __('Enable wholesale price', 'fwfw-domain'),
        'desc_tip' => 'true'
    ]);

    for ($i = 0; $i < 3; $i++) {
        echo '<div class="wholesale">';
        echo '<h3 class="wholesale-title">' . esc_html__('Wholesale ', 'fwfw-domain') . esc_html($i + 1) . '</h3>';

        woocommerce_wp_checkbox([
            'id' => 'enable_wholesale_' . esc_attr($i + 1),
            /* translators: %d placeholder For Enable Wholesale number */
            'placeholder' => sprintf(__('Enable Wholesale %d', 'fwfw-domain'), $i + 1),
            /* translators: %d Label For Enable Wholesale number */
            'label' => sprintf(__('Enable Wholesale %d', 'fwfw-domain'), $i + 1),
            /* translators: %d desc For Enable Wholesale quantity */
            'desc' => sprintf(__('Enable wholesale for quantity level %d', 'fwfw-domain'), $i + 1),

            'desc_tip' => 'true',
        ]);

        woocommerce_wp_text_input([
            'id' => 'Wholesale_' . esc_attr($i + 1) . '_quantity',

            'placeholder' => __('From quantity', 'fwfw-domain'),
            'label' => __('From quantity', 'fwfw-domain'),
            'type' => 'number',
            'custom_attributes' => ['step' => 'any', 'min' => '1'],
        ]);

        woocommerce_wp_text_input([
            'id' => 'Wholesale_' . esc_attr($i + 1) . '_to_quantity',
            'placeholder' => __('To quantity', 'fwfw-domain'),
            'label' => __('To quantity', 'fwfw-domain'),
            'type' => 'number',
            'custom_attributes' => ['step' => 'any', 'min' => '1'],
        ]);

        woocommerce_wp_text_input([
            'id' => 'Wholesale_' . esc_attr($i + 1) . '_price',
            'placeholder' => __('Price', 'fwfw-domain'),
            'label' => __('Price', 'fwfw-domain'),
            'type' => 'number',
            'custom_attributes' => ['step' => 'any', 'min' => '0'],
        ]);

        echo '</div>';
    }
}

function fwfw_save_fields($post_id)
{
    if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'your_action_name' ) ) {
        wp_die( esc_html__( 'Nonce verification failed', 'fwfw-domain' ) );
    }
    
    $fwfw_enable = isset($_POST['enable_wholesale']) ? 'yes' : 'no';
    update_post_meta($post_id, 'enable_wholesale', esc_attr($fwfw_enable));

    for ($i = 0; $i < 3; $i++) {
        $fwfw_enable_single = isset($_POST['enable_wholesale_' . ($i + 1)]) ? sanitize_text_field( wp_unslash( $_POST['enable_wholesale_' . ($i + 1)] ) ) : '';
        $quantity = isset($_POST['Wholesale_' . ($i + 1) . '_quantity']) ? sanitize_text_field( wp_unslash( $_POST['Wholesale_' . ($i + 1) . '_quantity'] ) ) : '';
        $to_quantity = isset($_POST['Wholesale_' . ($i + 1) . '_to_quantity']) ? sanitize_text_field( wp_unslash( $_POST['Wholesale_' . ($i + 1) . '_to_quantity'] ) ) : '';
        $price = isset($_POST['Wholesale_' . ($i + 1) . '_price']) ? sanitize_text_field( wp_unslash( $_POST['Wholesale_' . ($i + 1) . '_price'] ) ) : '';

        if ($fwfw_enable_single) {
            update_post_meta($post_id, 'enable_wholesale_' . ($i + 1), esc_attr($fwfw_enable_single));
        } else {
            update_post_meta($post_id, 'enable_wholesale_' . ($i + 1), 'no');
        }

        if (!empty($quantity)) {
            save_quantity($i, $quantity, $to_quantity, $post_id);
        } else {
            disable_wholesale($i, $post_id);
        }

        if (!empty($to_quantity)) {
            save_to_quantity($i, $quantity, $to_quantity, $post_id);
        } else {
            disable_wholesale($i, $post_id);
        }

        if (!empty($price)) {
            update_post_meta($post_id, 'Wholesale_' . ($i + 1) . '_price', esc_attr($price));
        } else {
            disable_wholesale($i, $post_id);
        }
    }
}


function save_quantity($i, $quantity, $to_quantity, $post_id) {
    if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'your_action_name' ) ) {
        wp_die( esc_html__( 'Nonce verification failed', 'fwfw-domain' ) );
    }
    
    $next_quantity = isset($_POST['Wholesale_' . ($i + 2) . '_quantity']) ? sanitize_text_field(wp_unslash($_POST['Wholesale_' . ($i + 2) . '_quantity'])) : 99999999999;
    $next_quantity_enable = isset($_POST['enable_wholesale_' . ($i + 2)]) ? 'yes' : 'no';

    if ($quantity < $to_quantity) {
        if ($quantity > $next_quantity && $next_quantity_enable === 'yes') {
            update_post_meta($post_id, 'Wholesale_' . ($i + 1) . '_quantity', esc_attr($next_quantity - 1));
        } else {
            update_post_meta($post_id, 'Wholesale_' . ($i + 1) . '_quantity', esc_attr($quantity));
        }
    } else {
        update_post_meta($post_id, 'Wholesale_' . ($i + 1) . '_quantity', esc_attr($to_quantity));
    }
}
function save_to_quantity($i, $quantity, $to_quantity, $post_id)
{
    if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'your_action_name' ) ) {
        wp_die( esc_html__( 'Nonce verification failed', 'fwfw-domain' ) );
    }
    
    $next_quantity = isset($_POST['Wholesale_' . ($i + 2) . '_quantity']) ? sanitize_text_field(wp_unslash($_POST['Wholesale_' . ($i + 2) . '_quantity'])) : 99999999999;
    $next_to_quantity_enable = isset($_POST['enable_wholesale_' . ($i + 2)]);

    if ($quantity < $to_quantity) {
        if ($to_quantity > $next_quantity && $next_to_quantity_enable) {
            update_post_meta($post_id, 'Wholesale_' . ($i + 1) . '_to_quantity', esc_attr($next_quantity - 1));
        } else {
            update_post_meta($post_id, 'Wholesale_' . ($i + 1) . '_to_quantity', esc_attr($to_quantity));
        }
    } else {
        update_post_meta($post_id, 'Wholesale_' . ($i + 1) . '_to_quantity', esc_attr($quantity));
    }
}

function disable_wholesale($i, $post_id)
{
    update_post_meta($post_id, 'enable_wholesale_' . ($i + 1), 'no');
}
