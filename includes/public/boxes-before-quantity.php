<?php 


add_action('woocommerce_before_add_to_cart_quantity', 'fwfw_add_message_before_quantity');

function fwfw_add_message_before_quantity()
{
    global $post;

     if(get_post_meta($post->ID , 'enable_wholesale' , true ) == 'yes'){
    echo '<div class="fwfw-fields-box">';
    for ($i = 0; $i < 3; $i++) {
        
        if(get_post_meta($post->ID, 'enable_wholesale'.$i+1, true) == 'yes'){
           make_box($post , $i);
        }
        
        
    }
    echo '</div>';
     }
}


function make_box($post ,$i ) : void {
    echo '<div class="fwfw-field">';
    echo '<h5> ';
    echo __('Wholesale', 'discount-depending-on-the-quantity') .' '. $i + 1;
    echo '</h5>';
    echo __('quantity :', 'discount-depending-on-the-quantity');
    echo get_post_meta($post->ID, 'Wholesale' . $i + 1 . 'quantity', true) . '   ';
    echo '<br>';
    echo __('price :', 'discount-depending-on-the-quantity');
    echo (float)get_post_meta($post->ID, 'Wholesale' . $i + 1 . 'price', true) * (float)get_post_meta($post->ID, 'Wholesale' . $i + 1 . 'quantity', true);

    echo '</div>';
}