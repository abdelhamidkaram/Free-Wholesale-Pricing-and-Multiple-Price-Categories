<?php 

add_action('woocommerce_before_add_to_cart_quantity', 'fwfw_add_message_before_quantity');

function fwfw_add_message_before_quantity() {
    global $post;

    if (get_post_meta($post->ID, 'enable_wholesale', true) == 'yes') {
        echo '<div class="fwfw-fields-box">';
        for ($i = 0; $i < 3; $i++) {
            
            if (get_post_meta($post->ID, 'enable_wholesale' . esc_attr($i + 1), true) == 'yes') {
                make_box($post, $i);
            }
        }
        echo '</div>';
    }
}

function make_box($post, $i) : void {
    echo '<div class="fwfw-field">';
    echo '<h5>';
    echo esc_html(__('Wholesale',
     'free-wholesale-pricing-and-multiple-price-categories')) . ' ' . esc_html($i + 1);
    echo '</h5>';
    echo esc_html(__('quantity :', 'free-wholesale-pricing-and-multiple-price-categories'));
    echo esc_html(get_post_meta($post->ID, 'Wholesale' . esc_attr($i + 1) . 'quantity', true)) . ' ';
    echo '<br>';
    echo esc_html(__('price :', 'free-wholesale-pricing-and-multiple-price-categories'));
    echo esc_html((float)get_post_meta($post->ID, 'Wholesale' . esc_attr($i + 1) . 'price', true) * (float)get_post_meta($post->ID, 'Wholesale' . esc_attr($i + 1) . 'quantity', true));

    echo '</div>';
}
