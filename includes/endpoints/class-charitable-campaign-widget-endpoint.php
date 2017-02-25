<?php
/**
 * donate endpoint.
 *
 * @version     1.5.0
 * @package     Charitable/Classes/Charitable_Campaign_Widget_Endpoint
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if ( ! class_exists( 'Charitable_Campaign_Widget_Endpoint' ) ) :

	/**
	 * Charitable_Campaign_Widget_Endpoint
	 *
	 * @abstract
	 * @since       1.5.0
	 */
	class Charitable_Campaign_Widget_Endpoint extends Charitable_Endpoint {

		/**
		 * @var     string
		 */
		const ID = 'campaign_widget';

		/**
		 * Return the endpoint ID.
		 *
		 * @return 	string
		 * @access 	public
		 * @static
		 * @since 	1.5.0
		 */
		public static function get_endpoint_id() {
			return self::ID;
		}

		/**
		 * Add rewrite rules for the endpoint.
		 *
		 * @access 	public
		 * @since 	1.5.0
		 */
		public function setup_rewrite_rules() {
			add_rewrite_endpoint( 'widget', EP_PERMALINK );
		}

		/**
		 * Return the endpoint URL.
		 *
		 * @return  string
		 * @access  public
		 * @since   1.5.0
		 */
		public function get_page_url() {

		}

		/**
		 * Return whether we are currently viewing the endpoint.
		 *
		 * @return  boolean
		 * @access  public
		 * @since   1.5.0
		 */
		public function is_page() {

		}
	}

endif;
