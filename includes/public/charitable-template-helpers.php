<?php
/**
 * Charitable Template Helpers.
 *
 * Functions used to assist with rendering templates.
 *
 * @package     Charitable/Functions/Templates
 * @version     1.2.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Displays a template.
 *
 * @since 1.0.0
 *
 * @param 	string|string[] $template_name A single template name or an ordered array of template.
 * @param 	mixed[] $args 				   Optional array of arguments to pass to the view.
 * @return 	Charitable_Template
 */
function charitable_template( $template_name, array $args = array() ) {
	if ( empty( $args ) ) {
		$template = new Charitable_Template( $template_name );
	} else {
		$template = new Charitable_Template( $template_name, false );
		$template->set_view_args( $args );
		$template->render();
	}

	return $template;
}

/**
 * Return the template path if the template exists. Otherwise, return default.
 *
 * @since 1.0.0
 *
 * @param 	string|string[] $template
 * @return  string The template path if the template exists. Otherwise, return default.
 */
function charitable_get_template_path( $template, $default = '' ) {
	$t = new Charitable_Template( $template, false );
	$path = $t->locate_template();

	if ( ! file_exists( $path ) ) {
		$path = $default;
	}

	return $path;
}

/**
 * Simple CSS compression.
 *
 * Removes all comments, removes spaces after colons and strips out all the whitespace.
 *
 * Based on http://manas.tungare.name/software/css-compression-in-php/
 *
 * @since 1.2.0
 *
 * @param   string $css The block of CSS to be compressed.
 * @return  string The compressed CSS
 */
function charitable_compress_css( $css ) {
	/* 1. Remove comments */
	$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );

	/* 2. Remove space after colons */
	$css = str_replace( ': ', ':', $css );

	/* 3. Remove whitespace */
	$css = str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $css );

	return $css;
}

/**
 * Provides arguments passed to campaigns within the loop.
 *
 * @since 1.2.3
 *
 * @param   mixed[] $view_args Optional. If called by the shortcode, this will contain the arguments passed to the shortcode.
 * @return  mixed[]
 */
function charitable_campaign_loop_args( $view_args = array() ) {
	$defaults = array(
		'button' => 'donate',
	);

	$args = wp_parse_args( $view_args, $defaults );

	return apply_filters( 'charitable_campaign_loop_args', $args );
}

/**
 * Processes arbitrary form attributes into HTML-safe key/value pairs
 *
 * @since 1.3.0
 *
 * @param   array $field Array defining the form field attributes.
 * @return  string       The formatted HTML-safe attributes
 * @see     Charitable_Form::render_field()
 */
function charitable_get_arbitrary_attributes( $field ) {
	if ( ! isset( $field['attrs'] ) ) {
		$field['attrs'] = array();
	}

	/* Add backwards compatibility support for placeholder, min, max, step, pattern and rows. */
	foreach ( array( 'placeholder', 'min', 'max', 'step', 'pattern', 'rows' ) as $attr ) {
		if ( isset( $field[ $attr ] ) && ! isset( $field['attrs'][ $attr ] ) ) {
			$field['attrs'][ $attr ] = $field[ $attr ];
		}
	}

	$output = '';

	foreach ( $field['attrs'] as $key => $value ) {
		$escaped_value = esc_attr( $value );
		$output .= " $key=\"$escaped_value\" ";
	}

	return apply_filters( 'charitable_arbitrary_field_attributes', $output );
}

/**
 * Checks whether we are currently in the main loop on a singular page.
 *
 * This should be used in any functions run on the_content hook, to prevent
 * Charitable's filters touching other the_content instances outside the main
 * loop.
 *
 * @since 1.4.11
 *
 * @return 	boolean
 */
function charitable_is_main_loop() {
	return is_single() && in_the_loop() && is_main_query();
}

/**
 * Returns the current URL.
 *
 * @see 	https://gist.github.com/leereamsnyder/fac3b9ccb6b99ab14f36
 * @global 	WP 		$wp
 * @since 1.0.0
 *
 * @return  string
 */
function charitable_get_current_url() {
	return home_url( add_query_arg( null, null ) );
}

/**
 * Returns the URL to which the user should be redirected after signing on or registering an account.
 *
 * @since 1.0.0
 *
 * @return  string
 */
function charitable_get_login_redirect_url() {
	if ( isset( $_REQUEST['redirect_to'] ) ) {
		$redirect = $_REQUEST['redirect_to'];
	} elseif ( charitable_get_permalink( 'profile_page' ) ) {
		$redirect = charitable_get_permalink( 'profile_page' );
	} else {
		$redirect = home_url();
	}

	return apply_filters( 'charitable_signon_redirect_url', $redirect );
}
