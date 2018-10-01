<?php
/**
 * Sets up the campaign list table in the admin.
 *
 * @package   Charitable
 * @version   1.7.0
 * @author    Deepak Gupta
 * @copyright Copyright (c) 2018, Studio 164a
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Donors Page.
 *
 * Renders the donors page contents.
 *
 * @since  1.0
 * @return void
 */
function charitable_donors_page() {
	$default_views  = charitable_donor_views();
	$requested_view = isset( $_GET['view'] ) ? sanitize_text_field( $_GET['view'] ) : 'donors';
	if ( array_key_exists( $requested_view, $default_views ) && function_exists( $default_views[ $requested_view ] ) ) {
		charitable_render_donor_view( $requested_view, $default_views );
	} else {
		charitable_donors_list();
	}
}

/**
 * Register the views for donor management.
 *
 * @since  1.0
 * @return array Array of views and their callbacks.
 */
function charitable_donor_views() {

	$views = array();
	return apply_filters( 'charitable_donor_views', $views );
}