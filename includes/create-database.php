<?php
/**
 * Function adds new database to WP.
 *
 * @return void
 */
function tormenta_create_sms_database() {
    global $wpdb;

    $table_name = $wpdb->prefix . "tormenta_card_manager";
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        orderNumber mediumint(15) NOT NULL,
        firstName tinytext NOT NULL,
        lastName tinytext NOT NULL,
        companyName tinytext NOT NULL,
        country tinytext NOT NULL,
        streetAdress tinytext NOT NULL,
        city tinytext NOT NULL,
        cityState tinytext NOT NULL,
        zipcode tinytext NOT NULL,
        phone tinytext NOT NULL,
        email tinytext NOT NULL,
        cardNumber tinytext NOT NULL,
        expiryMonth tinytext NOT NULL,
        expiryYear tinytext NOT NULL,
        cvv tinytext NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}
