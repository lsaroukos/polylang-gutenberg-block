<?php 
/**
 * Plugin Name: Polylang Gutenberg Block
 * Description: Plugin to render content based on polylang language
 * Version: 1.0.0
 * Requires at least: 6.1
 * Requires PHP: 8.1
 * Author: Lefteris Saroukos
 * License: MIT
 * Text Domain: pll-gtnb-block
 * 
 */

$loader = require_once __DIR__ . '/vendor/autoload.php';

if( !defined('PLL_GTNB_VERSION') ){
    define('PLL_GTNB_VERSION','1.0.0');
    define('ROOT_DIR',__DIR__); //no trailing slash
    define('ROOT_URL', \plugin_dir_url(__FILE__) ); //with trailing slash
}

if( class_exists('\PLL_GTNB\Plugin') ){
    $pll_gtnb = new \PLL_GTNB\Plugin();
}