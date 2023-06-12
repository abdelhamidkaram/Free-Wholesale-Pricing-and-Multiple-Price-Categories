<?php
/*
Plugin Name: DDQ wholesale  
Plugin URI: https://abdelhamid.dev/#wholesale  
Description: Discount depending on the quantity 
Author: Abdelhamid Karam
Author URI: abdelhamid.dev
Requires at least: 5.2
Requires PHP: 5.6.20
version: 1.0.0
*/

if (!defined('DDQ_PLUGIN_FILE')) {
    define('DDQ_PLUGIN_FILE', __FILE__);
}

function ddq_url()
{
    return untrailingslashit(plugins_url('/', DDQ_PLUGIN_FILE));
}


//styles 
require('includes/admin/css-load.php');
require('includes/public/css-load.php');

//admin fields 
require('includes/admin/class-wholesale-field.php');


// public boxes
require('includes/public/boxes-before-quantity.php');


// price recalculate
require('includes/public/product-price-recalculate.php');


