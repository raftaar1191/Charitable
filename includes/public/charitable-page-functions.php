<?php
/**
 * Charitable Page Functions.
 *
 * @package 	Charitable/Functions/Page
 * @version     1.0.0
 * @author 		Eric Daams
 * @copyright 	Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

/**
 * Displays a template.
 *
 * @param 	string|string[] $template_name A single template name or an ordered array of template.
 * @param 	mixed[] $args 				   Optional array of arguments to pass to the view.
 * @return 	Charitable_Template
 * @since 	1.0.0
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
 * @param 	string|string[] $template
 * @return  string The template path if the template exists. Otherwise, return default.
 * @since   1.0.0
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
 * Checks whether the current request is for an email preview.
 *
 * This is used when you call charitable_is_page( 'email_preview' ).
 * In general, you should use charitable_is_page() instead since it will
 * take into account any filtering by plugins/themes.
 *
 * @return  boolean
 * @since   1.0.0
 */
function charitable_is_email_preview() {
	return isset( $_GET['charitable_action'] ) && 'preview_email' == $_GET['charitable_action'];
}

add_filter( 'charitable_is_page_email_preview', 'charitable_is_email_preview', 2 );

/**
 * Returns the URL to which the user should be redirected after signing on or registering an account.
 *
 * @return  string
 * @since   1.0.0
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

/**
 * Returns the current URL.
 *
 * @see 	https://gist.github.com/leereamsnyder/fac3b9ccb6b99ab14f36
 * @global 	WP 		$wp
 * @return  string
 * @since   1.0.0
 */
function charitable_get_current_url() {
	return home_url( add_query_arg( null, null ) );
}

/**
 * Verifies whether the current user can access the donation receipt.
 *
 * @param   Charitable_Donation $donation
 * @return  boolean
 * @since   1.1.2
 */
function charitable_user_can_access_receipt( Charitable_Donation $donation ) {
	charitable_get_deprecated()->deprecated_function(
		__FUNCTION__,
		'1.4.0',
		'Charitable_Donation::is_from_current_user()'
	);

	return $donation->is_from_current_user();
}
