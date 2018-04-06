<?php
/**
 * Sets up the /reports/ API route.
 *
 * @package   Charitable/Classes/Charitable_API_Route_Reports
 * @author    Eric Daams
 * @copyright Copyright (c) 2018, Studio 164a
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since     1.6.0
 * @version   1.6.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Charitable_API_Route_Reports' ) ) :

	/**
	 * Charitable_API_Route_Reports
	 *
	 * @since 1.6.0
	 */
	class Charitable_API_Route_Reports extends Charitable_API_Route {

		/**
		 * Route base.
		 *
		 * @since 1.6.0
		 *
		 * @var   string
		 */
		protected $base;

		/**
		 * Set up class instance.
		 *
		 * @since  1.6.0
		 *
		 * @return void
		 */
		public function __construct() {
			parent::__constrct();

			$this->base = 'reports';
		}

		/**
		 * Register the routes for this controller.
		 *
		 * @since 1.6.0
		 */
		public function register_routes() {
			register_rest_route(
				$this->namespace,
				'/' . $this->base,
				array(
					array(
						'methods'              => WP_REST_Server::READABLE,
						'callback'             => array( $this, 'get_reports' ),
						'permissions_callback' => array( $this, 'user_can_get_charitable_reports' ),
					),
				)
			);

			register_rest_route(
				$this->namespace,
				'/' . $this->base . '/donations/',
				array(
					array(
						'methods'              => WP_REST_Server::READABLE,
						'callback'             => array( $this, 'get_donations_report' ),
						'permissions_callback' => array( $this, 'user_can_get_charitable_reports' ),
					),
				)
			);
		}

		/**
		 * Return a response with all registered reports.
		 *
		 * @since  1.6.0
		 *
		 * @return WP_REST_Response
		 */
		public function get_reports() {
			return new WP_REST_Response( array(
				'slug'        => 'donations',
				'description' => __( 'List of donation reports.', 'charitable' ),
				'_links'      => array(
					'self' => array(
						'href' => get_site_url( null, '/wp-json/' . $this->namespace . '/' . $this->base . '/donations/' ),
					),
				),
			), 200 );
		}

		/**
		 * Return a response with the donations report.
		 *
		 * @since  1.6.0
		 *
		 * @return WP_REST_Response
		 */
		public function get_donations_report() {
			return new WP_REST_Response( array(
				'amount'    => '',
				'donations' => '',
				'donors'    => '',
			), 200 );
		}
	}

endif;
