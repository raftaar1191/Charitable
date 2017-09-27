<?php
/**
 * Email Fields Donation class.
 *
 * @since   1.5.0
 * @version 1.5.0
 * @package Charitable/Classes/Charitable_Email_Fields_User
 * @author  Eric Daams
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'Charitable_Email_Fields_User' ) ) :

	/**
	 * Charitable_Email_Fields class.
	 *
	 * @since 1.5.0
	 */
	class Charitable_Email_Fields_User implements Charitable_Email_Fields_Interface {

		/**
		 * The WP_User object.
		 *
		 * @since 1.5.0
		 *
		 * @var   WP_User
		 */
		private $user;

		/**
		 * Set up class instance.
		 *
		 * @since 1.5.0
		 *
		 * @param Charitable_Email $email   The email object.
		 * @param boolean          $preview Whether this is an email preview.
		 */
		public function __construct( Charitable_Email $email, $preview ) {            
			$this->email   = $email;
			$this->preview = $preview;
			$this->user    = $email->get( 'user' );
			$this->fields  = $this->init_fields();
		}

		/**
		 * Get the fields that apply to the current email.
		 *
		 * @since  1.5.0
		 *
		 * @return array
		 */
		public function init_fields() {
			// $fields = array(
			// 	'user_login' => array(
			// 		'description' => __( 'The user login', 'charitable' ),
			// 		'preview'     => 'adam123',
			// 	),
			// 	'reset_link' => array(
			// 		'description' => __( 'The link the user needs to click to reset their password', 'charitable' ),
			// 		'preview'     => add_query_arg( array(
			// 			'key'   => '123123123',
			// 			'login' => 'adam123',
			// 		), charitable_get_permalink( 'reset_password_page' ) ),
			// 	),
			// );
			
			// if ( $this->has_valid_user() ) {
			// 	$fields = array_merge_recursive( $fields, array(
			// 		'user_login' => array( 'value' => $this->user->user_login ),
			// 		'reset_link' => array( 'callback' => array( $this, 'get_reset_link' ) ),
			// 	) );
			// }

			$fields = array();

			/**
			 * Filter the user email fields.
			 *
			 * @since 1.5.0
			 *
			 * @param array            $fields The default set of fields.
			 * @param Charitable_User  $user   Instance of `WP_User`.
			 * @param Charitable_Email $email  Instance of `Charitable_Email`.
			 */
			return apply_filters( 'charitable_email_user_fields', $fields, $this->user, $this->email );
		}

		/**
		 * Return fields.
		 *
		 * @since  1.5.0
		 *
		 * @return array
		 */
		public function get_fields() {
			return $this->fields;
		}

		/**
		 * Checks whether the email has a valid `WP_User` object set.
		 *
		 * @since  1.5.0
		 *
		 * @return boolean
		 */
		public function has_valid_user() {
			return is_a( $this->user, 'WP_User' );
		}		
	}

endif;