<?php
/*
Plugin Name: free wholesale
Description: FWFW is a plugin for calculate the Discount depending on the quantity
Tags: wholesale , discount , plugin , free , wordpress , wholesale plugin , wholesale plugin for wordpress , free wholesale plugin , free wholesale plugin for wordpress , woocommerce 
Author: Abdelhamid Karam
Author URI: https://abdelhamid.dev
Requires at least: 5.2
Requires PHP: 5.6.20
version: 1.0.0
License: GPLv2
License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
*/

if (!defined('fwfw_PLUGIN_FILE')) {
    define('fwfw_PLUGIN_FILE', __FILE__);
}

function fwfw_url()
{
    return untrailingslashit(plugins_url('/', fwfw_PLUGIN_FILE));
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


