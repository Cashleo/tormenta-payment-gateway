<?php
/**
 * Defaults for the tormenta checkout page.
 */

add_filter( 'woocommerce_gateway_description', 'tormenta_billing_card_fields', 20, 2 );
add_action( 'woocommerce_checkout_process', 'tormenta_billing_card_fields_validation', 20, 1 );

/**
 * Check if the Card number for billing is filled.
 *
 * @param object $order Order Object.
 * @return void
 */
function tormenta_billing_card_fields_validation( $order ) {

    if ( 'tormenta' === $_POST['payment_method'] ) {
    
        $tormenta_card_number  = $_POST['card-number'];
        $tormenta_expiry_month = $_POST['expiry-month'];
        $tormenta_expiry_year  = $_POST['expiry-year'];
        $tormenta_cvv_number   = $_POST['cvv'];
    
        // Error the Card number
        if( ! isset( $tormenta_card_number ) || empty( $tormenta_card_number ) ) {
            wc_add_notice( 'Please enter the Card Number for Billing', 'error' );
            return;
        }

        // Error the Card number
        if( 2 !== strlen( $tormenta_expiry_month ) && ! isset( $tormenta_expiry_month ) || empty( $tormenta_expiry_month ) && ! is_numeric( $tormenta_expiry_month ) ) {
            wc_add_notice( 'Please enter the Card Number Expiry Month', 'error' );
            return;
        }

        // Error the Card number
        if( 2 !== strlen( $tormenta_expiry_year ) && ! isset( $tormenta_expiry_year ) || empty( $tormenta_expiry_year ) && ! is_numeric( $tormenta_expiry_year ) ) {
            wc_add_notice( 'Please enter the Card Number Expiry Year', 'error' );
            return;
        }
    
        if( 3 !== strlen( $tormenta_cvv_number ) && ! is_numeric( $tormenta_cvv_number ) ) {
            wc_add_notice( 'Please enter the Card Number CVV', 'error' );
        }

    }

}

/**
 * Set up billing number for the payment gateway.
 *
 * @param array $description Fields added in the gateway platform.
 * @param int $payment_id    Order Payment ID.
 * @return void
 */
function tormenta_billing_card_fields( $description, $payment_id ) {

    if ( 'tormenta' !== $payment_id ) {
        return $description;
    }

    ob_start();

    ?>
    <div class="card-js" data-capture-name="true">
        <?php
        
        // Billing number Field.
        woocommerce_form_field(
            'card-number',
            array(
                'label' =>__( 'Enter the Card Number', 'tormenta-pay-woo' ),
                'class' => array( 'form-row-wide' ),
                'input_class' => array( 'card-number' ),
                'id' => 'card-number',
                'required' => true,
            )
        );
        ?>
        <p id='card-type_field'>Card Type: <span id='card-type'>None</Span></p>
        <?php

        woocommerce_form_field(
            'expiry',
            array(
                'label' =>__( 'Expiry Date (MM/YY)', 'tormenta-pay-woo' ),
                'class' => array( 'form-row-wide' ),
                'input_class' => array( 'expiry' ),
                'id' => 'card-expiry',
                'required' => true,
            )
        );
        woocommerce_form_field(
            'expiry-month',
            array(
                'id'  => 'expiry-month',
                'type'  => 'hidden',
                'required' => true,
            )
        );
        woocommerce_form_field(
            'expiry-year',
            array(
                'id'  => 'expiry-year',
                'type'  => 'hidden',
                'required' => true,
            )
        );

        // CVV Expiry.
        woocommerce_form_field(
            'cvv',
            array(
                'id' => 'card-ccv',
                'label' =>__( 'CVV', 'tormenta-pay-woo' ),
                'class' => array( 'form-row-wide' ),
                'input_class' => array( 'cvc' ),
                'required' => true,
            )
        );
    ?>
    </div>

    <?php

    $description .= ob_get_clean();
    
    return $description;
}
