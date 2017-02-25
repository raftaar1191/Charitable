<?php
/**
 * Charitable Endpoint Functions.
 *
 * @package     Charitable/Functions/Endpoints
 * @version     1.5.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

/**
 * Get the endpoint API object.
 *
 * @return  Charitable_Endpoints
 * @since   1.5.0
 */
function charitable_get_endpoints_api() {
	return charitable()->get_endpoints();
}

/**
 * Register a new endpoint.
 *
 * @param   Charitable_Endpoint $endpoint
 * @return  void
 * @since   1.5.0
 */
function charitable_register_endpoint( Charitable_Endpoint $endpoint ) {
	return charitable()->get_endpoints()->register( $endpoint );
}
