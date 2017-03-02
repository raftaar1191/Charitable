<?php
/**
 * donation_receipt endpoint.
 *
 * @version     1.5.0
 * @package     Charitable/Classes/Charitable_Donation_Receipt_Endpoint
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if ( ! class_exists( 'Charitable_Donation_Receipt_Endpoint' ) ) :

	/**
	 * Charitable_Donation_Receipt_Endpoint
	 *
	 * @abstract
	 * @since       1.5.0
	 */
	class Charitable_Donation_Receipt_Endpoint extends Charitable_Endpoint {

		/**
		 * @var     string
		 */
		const ID = 'donation_receipt';

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

			add_rewrite_endpoint( 'donation_receipt', EP_ROOT );
			add_rewrite_rule( 'donation-receipt/([0-9]+)/?$', 'index.php?donation_id=$matches[1]&donation_receipt=1', 'top' );

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

			global $wp_rewrite;

			$receipt_page = charitable_get_option( 'donation_receipt_page', 'auto' );

			$donation_id = isset( $args['donation_id'] ) ? $args['donation_id'] : get_the_ID();

			if ( 'auto' != $receipt_page ) {
				return esc_url_raw( add_query_arg( array( 'donation_id' => $donation_id ), get_permalink( $receipt_page ) ) );
			}

			if ( $wp_rewrite->using_permalinks() ) {
				$url = sprintf( '%s/donation-receipt/%d', untrailingslashit( home_url() ), $donation_id );
			} else {
				$url = esc_url_raw( add_query_arg( array(
					'donation_receipt' => 1,
					'donation_id' => $donation_id,
				), home_url() ) );
			}

			return $url;

		}

		/**
		 * Return whether we are currently viewing the endpoint.
		 *
		 * @global  WP_Query $wp_query
		 * @param 	array    $args
		 * @return  boolean
		 * @access  public
		 * @since   1.5.0
		 */
		public function is_page( $args = array() ) {

			global $wp_query;

			$receipt_page = charitable_get_option( 'donation_receipt_page', 'auto' );

			if ( 'auto' != $receipt_page ) {
				return is_page( $receipt_page );
			}

			return is_main_query()
				&& isset( $wp_query->query_vars['donation_receipt'] )
				&& isset( $wp_query->query_vars['donation_id'] );

		}
	}

endif;
