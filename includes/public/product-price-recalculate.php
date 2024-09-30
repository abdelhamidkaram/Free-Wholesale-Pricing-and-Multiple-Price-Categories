<?php

function fwfw_wholesale_quantity_or_zero($wholesale_number , $post_id ): float {
    return (empty(get_post_meta($post_id, 'Wholesale' . $wholesale_number . 'quantity', true))) ? 0 : get_post_meta($post_id, 'Wholesale' . $wholesale_number . 'quantity', true);
}
function fwfw_wholesale_to_quantity_or_zero($wholesale_number , $post_id ): float {
    return (empty(get_post_meta($post_id, 'Wholesale' . $wholesale_number . 'to_quantity', true))) ? 0 : get_post_meta($post_id, 'Wholesale' . $wholesale_number . 'to_quantity', true);
}

function fwfw_wholesale_price_or_base_price($wholesale_number , $post_id , $base_price): float {
    return (empty(get_post_meta($post_id, 'Wholesale' . $wholesale_number . 'quantity', true))) ? $base_price : get_post_meta($post_id, 'Wholesale' . $wholesale_number . 'price', true);
}




//calculate price in cart 

add_action('woocommerce_before_calculate_totals', 'custom_modify_cart_item_price');

function custom_modify_cart_item_price($cart) {

   if (is_admin() && !defined('DOING_AJAX')
    || empty($cart->cart_contents)) {
      return;
   }

   foreach ($cart->cart_contents as $cart_item) {
      if ($cart_item['data']->get_price() > 0) {
         $calculate = calculate_custom_price($cart_item['quantity'] ,$cart_item['data']->get_price() , $cart_item['product_id'] );

         $new_price = $calculate !=0 ? $calculate : $cart_item['data']->get_price() ; 

         $cart_item['data']->set_price($new_price);

      }
   }
}

function calculate_custom_price($quantity  ,$base_price ,  $post_id) {




   if(get_post_meta( $post_id, 'enable_wholesale' , true) == 'no'){
      return $base_price ;
   } 

   if ($quantity >= fwfw_wholesale_quantity_or_zero(1 , $post_id) && $quantity < fwfw_wholesale_to_quantity_or_zero(1 , $post_id)) {
      
      if(get_post_meta( $post_id, 'enable_wholesale1' , true) == 'no'){
         return $base_price ;
      }
      return fwfw_wholesale_price_or_base_price(1 , $post_id, $base_price); 
   } 
   
   if ($quantity >= fwfw_wholesale_quantity_or_zero(2 , $post_id)&& $quantity < fwfw_wholesale_to_quantity_or_zero(2 , $post_id)) {
     
      if(get_post_meta( $post_id, 'enable_wholesale2' , true) == 'no'){
         return $base_price ;
      }
      return fwfw_wholesale_price_or_base_price(2 , $post_id, $base_price); 
   }
   
   if ($quantity >= fwfw_wholesale_quantity_or_zero(3 , $post_id)&& $quantity < fwfw_wholesale_to_quantity_or_zero(3 , $post_id) ) {
     
      if(get_post_meta( $post_id, 'enable_wholesale3' , true) == 'no'){
         return $base_price ;
      }

      return fwfw_wholesale_price_or_base_price(3 , $post_id, $base_price); 
   } 

   return $base_price ; 



}
