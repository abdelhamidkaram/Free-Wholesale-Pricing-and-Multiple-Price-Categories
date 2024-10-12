<?php
add_action('admin_enqueue_scripts', 'fwpampc_load_admin_style');
function fwpampc_load_admin_style()
{
    
        wp_enqueue_style('fwpampc_admin_css', fwpampc_url() . '/admin/style.css', array(), '1.0.0');
    
}