<?php

namespace WPS\WP\Customizer;

add_action( 'customize_register', 'genesis_customize_register' );
/**
 * Include, instantiate, and initialize the Genesis Customizer object.
 *
 * @param \WP_Customize_Manager $wp_customize WP Customizer Manager object.
 */
function genesis_customize_register( \WP_Customize_Manager $wp_customize ) {

	$customizer = Customizer::get_instance();
	$customizer->init( $wp_customize );

}