<?php

add_action('wp_enqueue_scripts', 'fwpampc_public_style_load');

function fwpampc_public_style_load()
{
    if (is_product() || is_shop()) {
        wp_enqueue_style('fwpampc-public-css', fwpampc_url() . '/public/css/fwpampc-style.css', array(), '1.0.0');
    }
}