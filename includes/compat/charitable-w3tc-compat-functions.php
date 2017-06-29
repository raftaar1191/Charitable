<?php
/**
 * Functions to improve compatibility with W3TC.
 *
 * @package     Charitable/Functions/Compatibility
 * @version     1.4.18
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Clear the campaign page cache after a donation is received.
 *
 * @param   int $campaign_id The campaign ID.
 * @return  void
 * @since   1.4.18
 */
function charitable_compat_w3tc_clear_campaign_cache( $campaign_id ) {
	w3tc_flush_post( $campaign_id );
}

add_action( 'charitable_flush_campaign_cache', 'charitable_compat_w3tc_clear_campaign_cache' );
