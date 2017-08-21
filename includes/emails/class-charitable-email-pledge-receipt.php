<?php

defined( 'ABSPATH' ) or die( 'NO, HUMAN' );

if ( ! class_exists( 'Charitable_Email_Pledge_Receipt' ) && class_exists( 'Charitable_Email_Donation_Receipt' ) ) {

  class Charitable_Email_Pledge_Receipt extends Charitable_Email_Donation_Receipt {

    /**
     * @var     string
     */
    CONST ID = 'pledge_receipt';

    /**
     * @var     string[] Array of supported object types (campaigns, donations, donors, etc).
     * @access  protected
     */
    protected $object_types = array( 'donation' );

    /**
     * Instantiate the email class, defining its key values.
     *
     * @param   array   $objects
     * @access  public
     */
    public function __construct( $objects = array() ) {
      parent::__construct( $objects );
      $this->name = apply_filters( 'charitable_email_pledge_receipt_name', __( 'Pledge Receipt', 'charitable' ) );
    }

    /**
     * Returns the current email's ID.
     *
     * @return  string
     * @access  public
     * @static
     * @since   1.0.3
     */
    public static function get_email_id() {
      return self::ID;
    }

    /**
     * Static method that is fired right after a donation is completed, sending the donation receipt.
     *
     * @param   int     $donation_id
     * @return  boolean
     * @access  public
     * @static
     * @since   1.0.0
     */
    public static function send_with_donation_id( $donation_id ) {
      if ( ! charitable_get_helper( 'emails' )->is_enabled_email( self::get_email_id() ) ) {
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
      $email = new Charitable_Email_Pledge_Receipt( array(
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
      return apply_filters( 'charitable_email_pledge_receipt_default_subject', __( 'Thank you for your pledge', 'charitable' ), $this );
    }

    /**
     * Return the default headline for the email.
     *
     * @return  string
     * @access  protected
     * @since   1.0.0
     */
    protected function get_default_headline() {
      return apply_filters( 'charitable_email_pledge_receipt_default_headline', __( 'Your Pledge Receipt', 'charitable' ), $this );
    }

    /**
     * Return the default body for the email.
     *
     * @return  string
     * @access  protected
     * @since   1.0.0
     */
    protected function get_default_body() {
      ob_start();
?>
Dear [charitable_email show=donor_first_name],

Thank you so much for your generous pledge. Somebody from our fundraising team will contact you for your payment.

<strong>Your Pledge</strong>
[charitable_email show=donation_summary]

With thanks, [charitable_email show=site_name]
<?php
      $body = ob_get_clean();

      return apply_filters( 'charitable_email_pledge_receipt_default_body', $body, $this );
    }
  }
}
