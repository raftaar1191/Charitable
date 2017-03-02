<?php
/**
 * The endpoint registry class, providing a clean way to access details about individual endpoints.
 *
 * @package     Charitable/Classes/Charitable_Endpoints
 * @version     1.5.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'Charitable_Endpoints' ) ) :

	/**
	 * Charitable_Endpoints
	 *
	 * @since       1.5.0
	 */
	class Charitable_Endpoints {

		/**
		 * @var 	Charitable_Endpoint[]
		 * @access 	protected
		 */
		protected $endpoints;

		/**
		 * Create class object.
		 *
		 * @access  public
		 * @since   1.5.0
		 */
		public function __construct() {
			$this->endpoints = array();

			add_action( 'init', array( $this, 'setup_rewrite_rules' ) );
			add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
		}

		/**
		 * Register an endpoint.
		 *
		 * @param 	Charitable_Endpoint $endpoint
		 * @return  void
		 * @access  public
		 * @since   1.5.0
		 */
		public function register( Charitable_Endpoint $endpoint ) {

			$endpoint_id = $endpoint->get_endpoint_id();

			if ( array_key_exists( $endpoint_id, $this->endpoints ) ) {

				charitable_get_deprecated()->doing_it_wrong(
					__METHOD__,
					sprintf( __( 'Endpoint %s has already been registered.', 'charitable' ), $endpoint_id ),
					'1.5.0'
				);

				return;

			}

			$this->endpoints[ $endpoint_id ] = $endpoint;

		}

		/**
		 * Get the permalink/URL of a particular endpoint.
		 *
		 * @param 	string $endpoint
		 * @param   array  $args Optional array of arguments.
		 * @return  string|false
		 * @access  public
		 * @since   1.5.0
		 */
		public function get_page_url( $endpoint, $args = array() ) {

			$endpoint = $this->sanitize_endpoint( $endpoint );

			if ( ! array_key_exists( $endpoint, $this->endpoints ) ) {

				charitable_get_deprecated()->doing_it_wrong(
					__METHOD__,
					sprintf( __( 'Endpoint %s has not been registered.', 'charitable' ), $endpoint ),
					'1.5.0'
				);

				return false;

			}

			$default = $this->endpoints[ $endpoint ]->get_page_url( $args );

			return apply_filters( 'charitable_permalink_' . $endpoint . '_page', $default, $args );

		}

		/**
		 * Checks if we're currently viewing a particular endpoint/page.
		 *
		 * @param 	string $endpoint
		 * @param   array  $args Optional array of arguments.
		 * @return  boolean
		 * @access  public
		 * @since   1.5.0
		 */
		public function is_page( $endpoint, $args = array() ) {

			$endpoint = $this->sanitize_endpoint( $endpoint );

			if ( ! array_key_exists( $endpoint, $this->endpoints ) ) {

				charitable_get_deprecated()->doing_it_wrong(
					__METHOD__,
					sprintf( __( 'Endpoint %s has not been registered.', 'charitable' ), $endpoint ),
					'1.5.0'
				);

				return false;

			}

			$default = $this->endpoints[ $endpoint ]->is_page( $args );

			return apply_filters( 'charitable_is_page_' . $endpoint . '_page', $default, $args );

		}

		/**
		 * Set up the rewrite rules for the site.
		 *
		 * @return  void
		 * @access  public
		 * @since   1.5.0
		 */
		public function setup_rewrite_rules() {

			foreach ( $this->endpoints as $endpoint ) {
				$endpoint->setup_rewrite_rules();
			}

			/* Set up any common rewrite tags */
			add_rewrite_tag( '%donation_id%', '([0-9]+)' );

		}

		/**
		 * Add custom query vars.
		 *
		 * @param 	string[] $vars
		 * @return  string[]
		 * @access  public
		 * @since   1.5.0
		 */
		public function add_query_vars( $vars ) {

			return array_merge( $vars, array( 'donation_id', 'cancel' ) );

		}

		/**
		 * Remove _page from the endpoint (required for backwards compatibility)
		 * and make sure donation_cancel is changed to donation_cancellation.
		 *
		 * @param 	string $endpoint
		 * @return  string
		 * @access  protected
		 * @since   1.5.0
		 */
		protected function sanitize_endpoint( $endpoint ) {

			$endpoint = str_replace( '_page', '', $endpoint );

			if ( 'donation_cancel' == $endpoint ) {
				$endpoint = 'donation_cancellation';
			}

			return $endpoint;

		}
	}

endif; // End class_exists check
