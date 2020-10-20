<?php

add_action( 'wp_enqueue_scripts', 'tormenta_pay_enqueue_scripts' );

function tormenta_pay_enqueue_scripts() {
	
	if ( is_checkout() ) {

		wp_enqueue_style( 'tormenta-processing-css', TORMENTA_PLUGIN_URL . 'assets/processing.css', array(), TORMENTA_PLUGIN_VERSION, 'all' );

		wp_enqueue_script( 'torment-card-js-init', TORMENTA_PLUGIN_URL . 'assets/card-js-init.js', array( 'jquery' ), TORMENTA_PLUGIN_VERSION, true );
		$script_data = array(
			'image_path' => TORMENTA_PLUGIN_URL . '/assets/'
		);
		wp_localize_script(
			'torment-card-js-init',
			'credit_cards',
			$script_data
		);
	}

}
