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
        <div class="form-row form-row-wide validate-required woocommerce-invalid woocommerce-invalid-required-field" id="card-number_field" data-priority="">
            <label for="card-number" class="">Enter the Card Number&nbsp;<abbr class="required" title="required">*</abbr></label>     <span class="woocommerce-input-wrapper">
            <i class="far fa-credit-card"></i>
                <input type="text" class="input-text card-number" name="card-number" id="card-number" placeholder="---- ---- ---- ----" value="">
            </span>
            <span id="card-type_field">Card Type: <span id="card-type">None</span></span>
        </div>
        <div class="form-row form-row-wide validate-required woocommerce-invalid woocommerce-invalid-required-field" id="card-expiry_field" data-priority="">
            <label for="card-expiry" class="">Expiry Date (MM/YY)&nbsp;<abbr class="required" title="required">*</abbr></label>    
            <span class="woocommerce-input-wrapper">
                <i class="far fa-calendar"></i>
                <input type="text" class="input-text expiry" name="expiry" id="card-expiry" placeholder="MM/YY" value="">
                <input type="hidden" class="input-hidden " name="expiry-month" id="expiry-month" value="">
                <input type="hidden" class="input-hidden " name="expiry-year" id="expiry-year" value="">
            </span>
        </div>
        <div class="form-row form-row-wide validate-required woocommerce-invalid woocommerce-invalid-required-field" id="card-ccv_field" data-priority="">
            <label for="card-ccv" class="">CVV&nbsp;<abbr class="required" title="required">*</abbr></label>    
            <span class="woocommerce-input-wrapper">
                <i class="fas fa-lock"></i>
                <input type="text" class="input-text cvc" name="cvv" id="card-ccv" placeholder="CVV" value="">
            </span>
        </div>
        
    </div>

    <?php

    $description .= ob_get_clean();
    
    return $description;
}
