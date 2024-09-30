<?php

add_action('wp_enqueue_scripts' , 'fwfw_public_style_load');

function fwfw_public_style_load()
{
    wp_enqueue_style('fwfw-public-css' , fwfw_url() . '/public/css/fwfw-style.css');
}