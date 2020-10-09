<?php

add_action( 'wp_enqueue_scripts', 'tormenta_pay_enqueue_scripts' );

function tormenta_pay_enqueue_scripts() {
	
	if ( is_checkout() ) {

        wp_enqueue_style( 'tormenta-card-css', TORMENTA_PLUGIN_URL . 'assets/card-js.min.css', array(), TORMENTA_PLUGIN_VERSION, 'all' );

        wp_enqueue_script( 'torment-card-js', TORMENTA_PLUGIN_URL . 'assets/card-js.min.js', array( 'jquery' ), TORMENTA_PLUGIN_VERSION, true );
        
        if ( is_checkout() ) {
            wp_enqueue_script( 'torment-card-js-init', TORMENTA_PLUGIN_URL . 'assets/card-js-init.js', array( 'jquery','torment-card-js' ), TORMENTA_PLUGIN_VERSION, true );
        }
	}

}