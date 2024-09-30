<?php 
add_action( 'admin_enqueue_scripts', 'load_admin_style' );
function load_admin_style() {
    wp_enqueue_style('fwfw_admin_css', fwfw_url(). '/admin/style.css' );
}