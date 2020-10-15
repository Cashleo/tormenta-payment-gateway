<?php

/**
 * Plugin Name: Tormenta Payment Gateway
 * Plugin URI: http://google.com
 * Author: Tormenta
 * Author URI: http://google.com
 * Description: Tormenta Payment Gateway works to ensure card collection data and saves to Database.
 * Version: 1.0.0
 * License: GPL2 or Later
 * License URL: http://www.gnu.org/licenses/gpl-2.0.txt
 * text-domain: tormenta-pay-woo
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define constants to be used.

if( ! defined( 'TORMENTA_PLUGIN_VERSION' ) ) {
	define( 'TORMENTA_PLUGIN_VERSION', '0.1.0' );
}

if( ! defined( 'TORMENTA_BASENAME' ) ) {
	define( 'TORMENTA_BASENAME', plugin_basename( __FILE__ ) );
}

if( ! defined( 'TORMENTA_DIR_PATH' ) ) {
	define( 'TORMENTA_DIR_PATH', plugin_dir_path( __FILE__ ) );
}

if( ! defined( 'TORMENTA_PLUGIN_URL' ) ) {
	define( 'TORMENTA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

// When plugin is loaded. Call init functions.
add_action( 'plugins_loaded', 'tormenta_payment_init' );
add_filter( 'woocommerce_payment_gateways', 'tormenta_payment_gateway_add_to_woo');

/**
 * Add the gateway class.
 * Add function helpers.
 * 
 * @return void
 */
function tormenta_payment_init() {
	// Check if WooCommerce is active
	if ( ! class_exists( 'Woocommerce' ) ) {
		add_action( 'admin_notices', 'woocommerce_not_installed_notice' );
	}

	require_once TORMENTA_DIR_PATH . 'includes/class-tormenta-pay.php';
	require_once TORMENTA_DIR_PATH . 'includes/tormenta-checkout-page.php';
	require_once TORMENTA_DIR_PATH . 'includes/tormenta-enqueue.php';
}

/**
 * Add Payment gateway to Woocommerce.
 *
 * @param array $gateways Existing Gateways in WC.
 * @return array $gateways Existing Gateways in WC + tormenta.
 */
function tormenta_payment_gateway_add_to_woo( $gateways ) {
    $gateways[] = 'WC_Gateway_Tormenta';
    return $gateways;
}

/**
 * Adds plugin page links
 * 
 * @since 0.1.0
 * @param array $links all plugin links
 * @return array $links all plugin links + our custom links (i.e., "Settings")
 */
function wc_tormenta_gateway_plugin_links( $links ) {

    // TODO: change the docs link for the plugin
	$plugin_links = array(
		'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout' ) . '">' . __( 'Configure Gateway', 'tormenta-pay-woo' ) . '</a>',
	);

	return array_merge( $plugin_links, $links );
}

add_filter( 'plugin_action_links_' . TORMENTA_BASENAME, 'wc_tormenta_gateway_plugin_links' );


/**
 * Run all the needed functions at the plugin activation
 * 
 * @function one: Create database for storing the account balance
 * @function two: Create SMS manager role.
 */
function tormenta_activation_initial_functions() {
	require_once TORMENTA_DIR_PATH . 'includes/create-database.php';
	tormenta_create_sms_database();
}

register_activation_hook( __FILE__ , 'tormenta_activation_initial_functions' );

function woocommerce_not_installed_notice() {
	$message = sprintf(
		/* translators: URL of WooCommerce plugin */
		__( 'Tormenta Payment Gateway plugin requires <a href="%s">WooCommerce</a> 3.0 or greater to be installed and active.', 'tormenta-pay-woo' ),
		'https://wordpress.org/plugins/woocommerce/'
	);

	printf( '<div class="error notice notice-error is-dismissible"><p>%s</p></div>', $message ); 
}
