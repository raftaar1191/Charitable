<?php
/**
 * Functions to improve compatibility with Ultimate Member.
 *
 * @package     Charitable/Functions/Compatibility
 * @author      Eric Daams
 * @copyright   Copyright (c) 2019, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.6.25
 * @version     1.6.25
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * When a user verifies their email through Ultimate Member's flow,
 * mark them as verified in Charitable too.
 *
 * @since  1.6.25
 *
 * @param  int $user_id The user ID.
 * @return void
 */
function charitable_um_after_user_verified_email( $user_id ) {
	if ( ! array_key_exists( 'act', $_REQUEST ) || ! array_key_exists( 'hash', $_REQUEST ) ) {
		return;
	}

	if ( 'activate_via_email' != $_REQUEST['act'] || ! is_string( $_REQUEST['hash'] ) ) {
		return;
	}

	$user = charitable_get_user( $user_id );
	$user->mark_as_verified( true );
}

add_action( 'um_after_user_is_approved', 'charitable_um_after_user_verified_email' );
