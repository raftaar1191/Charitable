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
		 * @var 	string
		 * @access 	protected
		 */
		protected $current_endpoint;

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
			add_filter( 'template_include', array( $this, 'template_loader' ), 12 );
			add_filter( 'the_content', array( $this, 'get_content' ) );
			add_filter( 'body_class', array( $this, 'add_body_classes' ) );
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
		 * Set up the template for an endpoint.
		 *
		 * @param 	string $endpoint
		 * @param 	string $default_template The default template to be used if the endpoint doesn't return its own.
		 * @return  string $template
		 * @access  public
		 * @since   1.5.0
		 */
		public function get_endpoint_template( $endpoint, $default_template ) {

			$endpoint = $this->sanitize_endpoint( $endpoint );

			if ( ! array_key_exists( $endpoint, $this->endpoints ) ) {

				charitable_get_deprecated()->doing_it_wrong(
					__METHOD__,
					sprintf( __( 'Endpoint %s has not been registered.', 'charitable' ), $endpoint ),
					'1.5.0'
				);

				return $default_template;

			}

			return $this->endpoints[ $endpoint ]->get_endpoint_template( $default_template );

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

			foreach ( $this->endpoints as $endpoint ) {
				$vars = $endpoint->add_query_vars( $vars );
			}

			return array_merge( $vars, array( 'donation_id', 'cancel' ) );

		}

		/**
		 * Load templates for our endpoints.
		 *
		 * @param 	string $template The default template.
		 * @return  void
		 * @access  public
		 * @since   1.5.0
		 */
		public function template_loader( $template ) {

			$current_endpoint = $this->get_current_endpoint();

			echo '<pre>'; echo 'current endpoint: ' . $current_endpoint; echo '</pre>';

			if ( ! $current_endpoint ) {
				return $template;
			}

			$template_options = $this->endpoints[ $current_endpoint ]->get_template( $template );

			if ( $template_options == $template ) {
				return $template_options;
			}

			$template_options = apply_filters( 'charitable_' . $current_endpoint. '_page_template', $template_options );

			return charitable_get_template_path( $template_options, $template );

		}

		/**
		 * Get the content to display for the endpoint we're viewing.
		 *
		 * @param 	string       $content
		 * @param 	false|string $endpoint Fetch the content for a specific endpoint.
		 * @return  string
		 * @access  public
		 * @since   1.5.0
		 */
		public function get_content( $content, $endpoint = false ) {

			if ( ! $endpoint ) {
				$endpoint = $this->get_current_endpoint();
			}

			if ( ! $endpoint ) {
				return $content;
			}

			return $this->endpoints[ $endpoint ]->get_content( $content );

		}

		/**
		 * Add any custom body classes defined for the endpoint we're viewing.
		 *
		 * @param 	string[] $classes
		 * @return  string[]
		 * @access  public
		 * @since   1.5.0
		 */
		public function add_body_classes( $classes ) {

			$endpoint = $this->get_current_endpoint();

			if ( ! $endpoint ) {
				return $classes;
			}

			$classes[] = $this->endpoints[ $endpoint ]->get_body_class();

			return $classes;

		}

		/**
		 * Return the current endpoint.
		 *
		 * @return  string|false String if we're on one of our endpoints. False otherwise.
		 * @access  public
		 * @since   1.5.0
		 */
		public function get_current_endpoint() {

			if ( ! isset( $this->current_endpoint ) ) {

				foreach ( $this->endpoints as $endpoint_id => $endpoint ) {

					if ( $endpoint->is_page( array( 'strict' => true ) ) ) {

						$this->current_endpoint = $endpoint_id;

						return $this->current_endpoint;

					}
				}

				$this->current_endpoint = false;
			}

			return $this->current_endpoint;

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
