<?php
/**
 * Class that models the Password Reset email.
 *
 * @version     1.4.0
 * @package     Charitable/Classes/Charitable_Email_Password_Reset
 * @author      Eric Daams
 * @copyright   Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'Charitable_Email_Password_Reset' ) ) :

	/**
	 * Password Reset Email
	 *
	 * @since   1.4.0
	 */
	class Charitable_Email_Password_Reset extends Charitable_Email {

		/* @var string */
		const ID = 'password_reset';

		/**
		 * Whether the email allows you to define the email recipients.
		 *
		 * @since 1.4.0
		 *
		 * @var   boolean
		 */
		protected $has_recipient_field = false;

		/**
		 * The Password Reset email is required.
		 *
		 * @since 1.4.0
		 *
		 * @var   boolean
		 */
		protected $required = true;

		/**
		 * The user data.
		 *
		 * @since 1.4.0
		 *
		 * @var   WP_User
		 */
		protected $user;

		/**
		 * Array of supported object types (campaigns, donations, donors, etc).
		 *		 
		 * @since 1.5.0
		 *
		 * @var   string[]
		 */
		protected $object_types = array( 'user' );

		/**
		 * Instantiate the email class, defining its key values.
		 *
		 * @since 1.4.0
		 *
		 * @param mixed[] $objects
		 */
		public function __construct( $objects = array() ) {
			parent::__construct( $objects );

			$this->name = apply_filters( 'charitable_email_password_reset_name', __( 'User: Password Reset', 'charitable' ) );
			$this->user = isset( $objects['user'] ) ? $objects['user'] : false;
		}

		/**
		 * Returns the current email's ID.
		 *
		 * @since   1.4.0
		 *
		 * @return  string
		 */
		public static function get_email_id() {
			return self::ID;
		}	

		/**
		* Return the recipient for the email.
		*
		* @since   1.0.0
		*
		* @return  string
		*/
		public function get_recipient() {
			if ( ! isset( $this->user ) || ! is_a( $this->user, 'WP_User' ) ) {
				return '';
			}

			return $this->user->user_email;
		}

		/**
		 * Return the default subject line for the email.
		 *
		 * @since   1.4.0
		 *
		 * @return  string
		 */
		protected function get_default_subject() {
			return __( 'Password Reset for [charitable_email show=site_name]', 'charitable' );
		}

		/**
		 * Return the default headline for the email.
		 *
		 * @since   1.4.0
		 *
		 * @return  string
		 */
		protected function get_default_headline() {
			return apply_filters( 'charitable_email_password_reset_default_headline', __( 'Reset your password', 'charitable' ), $this );
		}

		/**
		 * Return the default body for the email.
		 *
		 * @since   1.4.0
		 *
		 * @return  string
		 */
		protected function get_default_body() {
			ob_start();
?>
<p><?php _e( 'Someone requested that the password be reset for the following account:', 'charitable' ) ?></p>
<p><?php _e( 'Username: [charitable_email show=user_login]', 'charitable' ) ?></p>
<p><?php _e( 'If this was a mistake, just ignore this email and nothing will happen.', 'charitable' ) ?></p>
<p><?php _e( 'To reset your password, visit the following address:', 'charitable' ) ?></p>
<p><a href="[charitable_email show=reset_link]">[charitable_email show=reset_link]</a></p>
<?php
		$body = ob_get_clean();

		return apply_filters( 'charitable_email_password_reset_default_body', $body, $this );
		}
	}

endif;
