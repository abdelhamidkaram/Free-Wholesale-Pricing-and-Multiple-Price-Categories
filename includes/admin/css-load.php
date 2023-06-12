<?php 
add_action( 'admin_enqueue_scripts', 'load_admin_style' );
function load_admin_style() {
    wp_enqueue_style('ddq_admin_css', ddq_url(). '/admin/style.css' );
}