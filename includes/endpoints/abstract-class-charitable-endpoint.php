<?php
/**
 * Endpoint abstract model
 *
 * @version     1.5.0
 * @package     Charitable/Classes/Charitable_Endpoint
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if ( ! class_exists( 'Charitable_Endpoint' ) ) :

	/**
	 * Charitable_Endpoint
	 *
	 * @abstract
	 * @since       1.5.0
	 */
	abstract class Charitable_Endpoint implements Charitable_Endpoint_Interface {

		/**
		 * @var 	string The endpoint's unique identifier.
		 */
		const ID = '';

		/**
		 * Add rewrite rules for the endpoint.
		 *
		 * Unless the child class defines this, this won't do anything for an endpoint.
		 *
		 * @access 	public
		 * @since 	1.5.0
		 */
		public function setup_rewrite_rules() {
			/* Do nothing by default. */
		}
	}

endif;
