<?php

/**
 * Plugin Name:       Recommender Service
 * Plugin URI:        https://wordpress.org/plugins/recommender/
 * Description:       Wordpress and Woocommerce Plugin for Recommender.ir
 * Version:           1.2.0
 * Author:            Mahshad Kalantari
 * Author URI:        http://mahshad.me/
 * Text Domain:       recommender
 * Domain Path:       /langs
 * License:           GPLv2 or later
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/vendor/autoload.php';

define( 'RECOM_PLUGIN' , plugin_basename( __FILE__ ) );
define( 'RECOM_DIR' , plugin_dir_path( __FILE__ ) );
define( 'RECOM_URL' , plugin_dir_url(  __FILE__ ) );
define( 'RECOM_ASSETS_DIR' , trailingslashit( RECOM_DIR . 'assets' ) );
define( 'RECOM_ASSETS_URL' , trailingslashit( RECOM_URL . 'assets' ) );


\WPRecommenderIr\Init::instance();
register_activation_hook( __FILE__, ['\WPRecommenderIr\Init', 'activation'] );