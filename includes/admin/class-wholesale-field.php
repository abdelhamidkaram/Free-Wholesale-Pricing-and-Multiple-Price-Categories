<?php

add_action('woocommerce_product_options_general_product_data', 'ddq_add_fields');

add_action('woocommerce_process_product_meta', 'ddq_save_fields');

function ddq_add_fields()
{
    global $post;


    woocommerce_wp_checkbox(
        [
            'id' => 'enable_wholesale',
            'placeholder' => 'enable_wholesale ',
            'label' => __('enable wholesale', 'discount-depending-on-the-quantity'),
            'desc'      => __('enable wholesale price ', 'discount-depending-on-the-quantity'),
            'desc_tip'  => 'true'
        ]
    );


    for ($i = 0; $i <  3; $i++) {
        echo '<div class="wholesale">
  <h3 class="wholesale-title">';
        echo 'Wholesale ' . $i + 1 . '</h3>';

        woocommerce_wp_checkbox(
            [
                'id' => 'enable_wholesale' . $i + 1,
                'placeholder' => 'enable_wholesale ' . $i + 1,
                'label' => __('enable wholesale' . $i + 1, 'discount-depending-on-the-quantity'),
                'desc'      => __('enable wholesale ' . $i + 1, 'discount-depending-on-the-quantity'),
                'desc_tip'  => 'true'
            ]
        );

        woocommerce_wp_text_input(
            [
                'id' => 'Wholesale' . $i + 1 . 'quantity',
                'placeholder' => __('from quantity', 'discount-depending-on-the-quantity'),
                'label' => __('from quantity', 'discount-depending-on-the-quantity'),
                'type' => 'number',
                'custom_attributes' => array(
                    'step' => 'any',
                    'min' => '1'
                ),


            ]
        );
        woocommerce_wp_text_input(
            [
                'id' => 'Wholesale' . $i + 1 . 'to_quantity',
                'placeholder' => __('to quantity', 'discount-depending-on-the-quantity'),
                'label' => __('to quantity', 'discount-depending-on-the-quantity'),
                'type' => 'number',
                'custom_attributes' => array(
                    'step' => 'any',
                    'min' => '1'
                ),


            ]
        );
        woocommerce_wp_text_input(
            array(
                'id' => 'Wholesale' . $i + 1 . 'price',
                'placeholder' => __('price', 'discount-depending-on-the-quantity'),
                'label' => __('price', 'discount-depending-on-the-quantity'),
                'type' => 'number',
                'custom_attributes' => array(
                    'step' => 'any',
                    'min' => '0'
                ),


            )
        );
        echo '</div>';
    }
}
function ddq_save_fields($post_id)
{
    $ddq_enable = isset($_POST['enable_wholesale']) ? 'yes' : 'no';
    update_post_meta($post_id, 'enable_wholesale', esc_attr($ddq_enable));

    for ($i = 0; $i < 3; $i++) {

        $ddq_enable_single = $_POST['enable_wholesale' . $i + 1];
        $quantity = $_POST['Wholesale' . $i + 1 . 'quantity'];
        $to_quantity = $_POST['Wholesale' . $i + 1 . 'to_quantity'];
        $price = $_POST['Wholesale' . $i + 1 . 'price'];
        if ($ddq_enable_single) {
            update_post_meta(
                $post_id,
                'enable_wholesale' . $i + 1,
                esc_attr(wc_bool_to_string($ddq_enable_single))
            );
        } else {
            update_post_meta(
                $post_id,
                'enable_wholesale' . $i + 1,
                esc_attr(wc_bool_to_string($ddq_enable_single))
            );
        }

        if (!empty($quantity)) {
            save_quantity( $i , $quantity , $to_quantity ,  $post_id);
        }else{
            disable_wholesale($i , $post_id );
        }


        if (!empty($to_quantity)) {
            save_to_quantity( $i , $quantity , $to_quantity ,  $post_id);
        }else{
            disable_wholesale($i , $post_id );
        }

        if (!empty($price)) {
            update_post_meta(
                $post_id,
                'Wholesale' . $i + 1 . 'price',
                esc_attr($price)
            );
        }else{
            disable_wholesale($i , $post_id );
        }
    }
}


function save_quantity( $i , $quantity , $to_quantity ,  $post_id) {
    $next_quantity = $_POST['Wholesale' . $i + 2 . 'quantity'] ?? 99999999999;
    $next_quantity_enable = $_POST['enable_wholesale' . $i + 2] ;
    if($quantity < $to_quantity ){
        if($quantity > $next_quantity && $next_quantity_enable ){
             update_post_meta(
                $post_id,
                'Wholesale' . $i + 1 . 'quantity',
                esc_attr($next_quantity - 1 )
            );
        }else{
            update_post_meta(
                $post_id,
                'Wholesale' . $i + 1 . 'quantity',
                esc_attr($quantity)
            );
            
        }

    }else{

        update_post_meta(
            $post_id,
            'Wholesale' . $i + 1 . 'quantity',
            esc_attr($to_quantity)
        );
    }
}
function save_to_quantity( $i , $quantity , $to_quantity ,  $post_id) {
    $next_quantity = $_POST['Wholesale' . $i + 2 . 'quantity'] ?? 99999999999;
    $next_to_quantity_enable = $_POST['enable_wholesale' . $i + 2] ;

    if($quantity < $to_quantity){
        if( $to_quantity > $next_quantity  && $next_to_quantity_enable ){
            update_post_meta(
               $post_id,
               'Wholesale' . $i + 1 . 'to_quantity',
               esc_attr($next_quantity - 1)
           );
       }else{
        update_post_meta(
        $post_id,
        'Wholesale' . $i + 1 . 'to_quantity',
        esc_attr($to_quantity)
    );}
    }else{
        update_post_meta(
            $post_id,
            'Wholesale' . $i + 1 . 'to_quantity',
            esc_attr($quantity)
        );
    }
}

function disable_wholesale($i , $post_id ){
    update_post_meta(
        $post_id,
        'enable_wholesale' . $i + 1,
        esc_attr('no')
    );
}