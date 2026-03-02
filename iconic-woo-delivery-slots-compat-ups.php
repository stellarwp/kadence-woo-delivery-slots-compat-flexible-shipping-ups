<?php
/**
 * Plugin Name:     WooCommerce Delivery Slots by Kadence [Flexible Shipping UPS]
 * Plugin URI:      https://iconicwp.com/products/woocommerce-delivery-slots/
 * Description:     Compatibility between WooCommerce Delivery Slots by Kadence and Flexible Shipping UPS by WPDesk.
 * Author:          Kadence WP
 * Author URI:      https://www.kadencewp.com/
 * Text Domain:     iconic-woo-delivery-slots-compat-flexible-shipping-ups
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Iconic_Woo_Delivery_Slots_Compat_FSPUS
 */

/**
 * Is Flexible Shipping UPS plugin active?
 *
 * @return bool
 */
function iconic_compat_fsups_is_active() {
	return defined( 'FLEXIBLE_SHIPPING_UPS_VERSION' );
}

/**
 * Add shipping rate options.
 *
 * @param array            $shipping_method_options
 * @param WC_Shipping_Rate $method
 * @param WC_Shipping_Zone $zone
 *
 * @return array
 */
function iconic_compat_fsups_add_shipping_method_options( $shipping_method_options, $method, $zone ) {
	if ( ! iconic_compat_fsups_is_active() ) {
		return $shipping_method_options;
	}

	$class = get_class( $method );

	if ( false === strpos( $class, 'WooCommerceShipping\Ups\UpsShippingMethod' ) ) {
		return $shipping_method_options;
	}

	$services = $method->get_option( 'services' );

	if ( empty( $services ) ) {
		return $shipping_method_options;
	}

	foreach ( $services as $service_id => $service ) {
		$method_id                             = sprintf( 'flexible_shipping_ups:%d:%s', $method->instance_id, $service_id );
		$shipping_method_options[ $method_id ] = esc_html( sprintf( '%s: %s', $zone->get_zone_name(), $service['name'] ) );
	}

	return $shipping_method_options;
}

add_filter( 'iconic_wds_zone_based_shipping_method', 'iconic_compat_fsups_add_shipping_method_options', 10, 3 );

/**
 * Remove default options.
 *
 * @return array
 */
function iconic_compat_fsups_remove_default_shipping_method_options( $shipping_method_options ) {
	if ( ! iconic_compat_fsups_is_active() ) {
		return $shipping_method_options;
	}

	unset( $shipping_method_options['upsfreevendor\wpdesk\woocommerceshipping\ups\upsshippingmethod'] );

	return $shipping_method_options;
}

add_filter( 'iconic_wds_shipping_method_options', 'iconic_compat_fsups_remove_default_shipping_method_options', 10 );
