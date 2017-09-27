<?php
/**
 * My Donations shortcode class.
 *
 * @version     1.4.0
 * @package     Charitable/Shortcodes/My Donations
 * @category    Class
 * @author      Eric Daams
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'Charitable_My_Donations_Shortcode' ) ) :

	/**
	 * Charitable_My_Donations_Shortcode class.
	 *
	 * @since   1.4.0
	 */
	class Charitable_My_Donations_Shortcode {

		/**
		 * The callback method for the campaigns shortcode.
		 *
		 * This receives the user-defined attributes and passes the logic off to the class.
		 *
		 * @since   1.4.0
		 *
		 * @param   array $atts User-defined shortcode attributes.
		 * @return  string
		 */
		public static function display( $atts ) {
			$defaults = array();

			$args = shortcode_atts( $defaults, $atts, 'charitable_my_donations' );

			ob_start();

			/* If the user is logged out, redirect to login/registration page. */
			if ( ! is_user_logged_in() ) {

				echo Charitable_Login_Shortcode::display( array(
					'redirect' => charitable_get_current_url(),
				) );

				return;
			}

			$user = charitable_get_user( get_current_user_id() );
			$args = array(
				'output'   => 'posts',				
				'orderby'  => 'date',
				'order'    => 'DESC',
				'number'   => -1,
				'donor_id' => $user->get_donor_id(),
			);

			if ( ! $user->is_verified() ) {
				$args['user_id'] = $user->ID;
			}

			$view_args = array(
				'donations' => new Charitable_Donations_Query( $args ),
				'user'      => $user,
			);

			charitable_template( 'shortcodes/my-donations.php', $view_args );

			/**
			 * Filter the output of the shortcode.
			 *
			 * @since 1.4.0
			 *
			 * @param string $output    The default output.
			 * @param array  $view_args The view arguments.
			 * @param array  $args      The query arguments.
			 */
			return apply_filters( 'charitable_my_donations_shortcode', ob_get_clean(), $view_args, $args );
		}
	}

endif;
