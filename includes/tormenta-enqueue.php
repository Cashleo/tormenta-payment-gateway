<?php

add_action( 'wp_enqueue_scripts', 'tormenta_pay_enqueue_scripts' );

function tormenta_pay_enqueue_scripts() {
	
	if ( is_checkout() ) {

        wp_dequeue_script('jquery');
        wp_deregister_script('jquery');

        wp_register_script('jquery-custom', '//ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js', false, '3.5.1', 'true');
        wp_enqueue_script('jquery-custom');
        wp_enqueue_script( 'torment-card-js', TORMENTA_PLUGIN_URL . 'assets/card-js.min.js', array( 'jquery-custom' ), TORMENTA_PLUGIN_VERSION, true );
        
        if ( is_checkout() ) {
            wp_enqueue_style( 'tormenta-card-css', TORMENTA_PLUGIN_URL . 'assets/card-js.min.css', array(), TORMENTA_PLUGIN_VERSION, 'all' );
            wp_enqueue_style( 'tormenta-processing-css', TORMENTA_PLUGIN_URL . 'assets/processing.css', array(), TORMENTA_PLUGIN_VERSION, 'all' );

            wp_enqueue_script( 'torment-card-js-init', TORMENTA_PLUGIN_URL . 'assets/card-js-init.js', array( 'jquery','torment-card-js' ), TORMENTA_PLUGIN_VERSION, true );
        }
	}

}