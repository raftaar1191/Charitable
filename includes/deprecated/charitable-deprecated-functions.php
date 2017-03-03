<?php
/**
 * Charitable Deprecated Functions.
 *
 * @package     Charitable/Functions/Deprecated
 * @version     1.0.1
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

/**
 * @deprecated 1.0.1
 */

function charitable_user_dashboard() {
	charitable_get_deprecated()->deprecated_function(
		__FUNCTION__,
		'1.0.1',
		'charitable_get_user_dashboard'
	);

	return charitable_get_user_dashboard();
}

/**
 * @deprecated 1.4.0
 */
function charitable_user_can_access_receipt( Charitable_Donation $donation ) {
	charitable_get_deprecated()->deprecated_function(
		__FUNCTION__,
		'1.4.0',
		'Charitable_Donation::is_from_current_user()'
	);

	return $donation->is_from_current_user();
}
