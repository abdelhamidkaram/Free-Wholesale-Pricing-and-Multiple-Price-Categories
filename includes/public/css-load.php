<?php

add_action('wp_enqueue_scripts' , 'ddq_public_style_load');

function ddq_public_style_load()
{
    wp_enqueue_style('ddq-public-css' , ddq_url() . '/public/css/ddq-style.css');
}