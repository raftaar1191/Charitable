<?php

defined( 'ABSPATH' ) or die( 'NO, HUMAN' );

if ( ! class_exists( 'Charitable_Email_Offline_Donation_Notification' ) && class_exists( 'Charitable_Email_New_Donation' ) ) {
  class Charitable_Email_Offline_Donation_Notification extends Charitable_Email_New_Donation {

    /**
     * @var     string
     */
    CONST ID = 'offline_donation_notification';

    /**
     * @var     string[] Array of supported object types (campaigns, donations, donors, etc).
     * @access  protected
     */
    protected $object_types = array( 'donation' );

    public function __construct( $objects = array() ) {
      parent::__construct( $objects );

      $this->name = apply_filters( 'charitable_email_offline_donation_notification_name', __( 'New Offline Donation Notification', 'charitable' ) );
    }

    /**
     * Returns the current email's ID.
     *
     * @return  string
     * @access  public
     * @static
     */
    public static function get_email_id() {
      return self::ID;
    }

    public static function send_with_donation_id( $donation_id ) {
      /* Verify that the email is enabled. */
      if ( ! charitable_get_helper( 'emails' )->is_enabled_email( Charitable_Email_Offline_Donation_Notification::get_email_id() ) ) {
        return false;
      }

      /* If the donation is not pending, stop here. */
      if ( 'charitable-pending' != get_post_status( $donation_id ) ) {
        return false;
      }

      /* If the donation was not made with the offline payment option, stop here. */
      if ( 'offline' != get_post_meta( $donation_id, 'donation_gateway', true ) ) {
        return false;
      }

      /* All three of those checks passed, so proceed with sending the email. */
      $email = new Charitable_Email_Offline_Donation_Notification( array(
        'donation' => new Charitable_Donation( $donation_id )
      ) );

      $email->send();

      return true;
    }

    /**
     * Return the default subject line for the email.
     *
     * @return  string
     * @access  protected
     */
    protected function get_default_subject() {
      return __( 'You have received a new offline donation', 'charitable' );
    }

    /**
     * Return the default headline for the email.
     *
     * @return  string
     * @access  protected
     */
    protected function get_default_headline() {
      return apply_filters( 'charitable_email_offline_donation_notification_default_headline', __( 'New Offline Donation', 'charitable' ), $this );
    }

    /**
     * Return the default body for the email.
     *
     * @return  string
     * @access  protected
     */
    protected function get_default_body() {
      ob_start();
?>
[charitable_email show=donor] ([charitable_email show=donor_email]) has just made a offline donation!

<strong>Summary</strong>
[charitable_email show=donation_summary]
Donation ID: [charitable_email show=donation_id]
<?php
      $body = ob_get_clean();

      return apply_filters( 'charitable_email_offline_donation_notification_default_body', $body, $this );
    }
  }
}
