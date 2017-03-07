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

		/**
		 * Set up the endpoint template.
		 *
		 * By default, we will return the default template that WordPress already selected.
		 *
		 * @param 	string $template The default template.
		 * @return  string
		 * @access  public
		 * @since   1.5.0
		 */
		public function get_template( $template ) {
			return $template;
		}

		/**
		 * Get the content to display for the endpoint.
		 *
		 * By default, we will return the default content that is passed by WordPress.
		 *
		 * @param 	string $content
		 * @return  string
		 * @access  public
		 * @since   1.5.0
		 */
		public function get_content( $content ) {
			return $content;
		}

		/**
		 * Return the body class to add for the endpoint.
		 *
		 * By default, this will be the endpoint ID with underscores replaced by hyphens.
		 *
		 * @return 	string
		 * @access 	public
		 * @since 	1.5.0
		 */
		public function get_body_class() {
			return str_replace( '_', '-', $this->get_endpoint_id() );
		}
	}

endif;
