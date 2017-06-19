<?php
/**
 * registration endpoint.
 *
 * @version     1.5.0
 * @package     Charitable/Classes/Charitable_Registration_Endpoint
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if ( ! class_exists( 'Charitable_Registration_Endpoint' ) ) :

	/**
	 * Charitable_Registration_Endpoint
	 *
	 * @abstract
	 * @since       1.5.0
	 */
	class Charitable_Registration_Endpoint extends Charitable_Endpoint {

		/**
		 * @var     string
		 */
		const ID = 'registration';

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
		 * Return the endpoint URL.
		 *
		 * @global 	WP_Rewrite $wp_rewrite
		 * @param 	array      $args
		 * @return  string
		 * @access  public
		 * @since   1.5.0
		 */
		public function get_page_url( $args = array() ) {

			$page = charitable_get_option( 'registration_page', 'wp' );
			$url  = 'wp' == $page ? wp_registration_url() : get_permalink( $page );
			return $url;

		}

		/**
		 * Return whether we are currently viewing the endpoint.
		 *
		 * @global  WP_Post $post
		 * @param 	array   $args
		 * @return  boolean
		 * @access  public
		 * @since   1.5.0
		 */
		public function is_page( $args = array() ) {

			global $post;

			$page = charitable_get_option( 'registration_page', 'wp' );

			if ( 'wp' == $page ) {
				return wp_registration_url() == charitable_get_current_url();
			}

			if ( is_object( $post ) ) {
				return $page == $post->ID;
			}

			return false;

		}
	}

endif;
