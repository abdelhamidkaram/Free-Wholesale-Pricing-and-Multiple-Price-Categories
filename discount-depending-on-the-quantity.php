<?php
/*
Plugin Name: Free Wholesale Pricing and Multiple Price Categories 
Description: The Wholesale Pricing and Multiple Price Categories plugin is a robust extension designed for your WordPress website, enabling store owners to provide wholesale pricing to their customers. With this plugin, you can easily create multiple price categories based on quantity thresholds, allowing for tiered pricing that caters to bulk purchases.
Short Description: Manage wholesale pricing based on quantity in WooCommerce.
Author: Abdelhamid Karam
Author URI: https://abdelhamid.dev
Requires at least: 5.2
Requires PHP: 5.6.20
Version: 1.0.0
Tested up to: 6.6
License: GPLv2
License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
Tags: b2b,catalog mode , dynamic pricing, wholesale pricing, woocommerce wholesale 
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


