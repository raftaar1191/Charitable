<?php
/**
 * Email Fields Donation class.
 *
 * @since   1.5.0
 * @version 1.5.0
 * @package Charitable/Classes/Charitable_Email_Fields_Donation
 * @author  Eric Daams
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'Charitable_Email_Fields_Donation' ) ) :

    /**
     * Charitable_Email_Fields class.
     *
     * @since 1.5.0
     */
    class Charitable_Email_Fields_Donation extends Charitable_Email_Fields {

        /**
         * The Charitable_Donation object.
         *
         * @since 1.5.0
         *
         * @var   Charitable_Donation
         */
        private $donation;

        /**
         * Set up class instance.
         *
         * @since 1.5.0
         *
         * @param Charitable_Email $email   The email object.
         * @param boolean          $preview Whether this is an email preview.
         */
        public function __construct( Charitable_Email $email, $preview ) {            
            $this->email    = $email;
            $this->preview  = $preview;
            $this->donation = $email->get_donation();
            $this->fields   = $this->init_fields();
        }

        /**
         * Get the fields that apply to the current email.
         *
         * @since  1.5.0
         *
         * @return array
         */
        public function init_fields() {
            if ( ! $this->has_valid_donation() ) {
                return array();
            }

            $donor  = $this->donation->get_donor();
            $fields = array(
                'donor' => array(
                    'description'   => __( 'The full name of the donor', 'charitable' ),
                    'callback'      => array( $donor, 'get_name' ),
                ),
                'donor_first_name' => array(
                    'description'   => __( 'The first name of the donor', 'charitable' ),
                    'callback'      => array( $this, 'get_donor_first_name' ),
                ),
                'donor_email' => array(
                    'description'   => __( 'The email address of the donor', 'charitable' ),
                    'callback'      => array( $donor, 'get_email' ),
                ),
                'donor_address' => array(
                    'description'   => __( 'The donor\'s address', 'charitable' ),
                    'callback'      => array( $donor, 'get_address' ),
                ),
                'donor_phone' => array(
                    'description'   => __( 'The donor\'s phone number', 'charitable' ),
                    'callback'      => array( $this, 'get_donor_phone' ),
                ),
                'donation_id' => array(
                    'description'   => __( 'The donation ID', 'charitable' ),
                    'callback'      => array( $this->donation, 'get_donation_id' ),
                ),
                'donation_summary' => array(
                    'description'   => __( 'A summary of the donation', 'charitable' ),
                    'callback'      => array( $this, 'get_donation_summary' ),
                ),
                'donation_amount' => array(
                    'description'   => __( 'The total amount donated', 'charitable' ),
                    'callback'      => array( $this, 'get_donation_total' ),
                ),
                'donation_date' => array(
                    'description'   => __( 'The date the donation was made', 'charitable' ),
                    'callback'      => array( $this, 'get_donation_date' ),
                ),
                'donation_status' => array(
                    'description'   => __( 'The status of the donation (pending, paid, etc.)', 'charitable' ),
                    'callback'      => array( $this, 'get_donation_status' ),
                ),
                'campaigns' => array(
                    'description'   => __( 'The campaigns that were donated to', 'charitable' ),
                    'callback'      => array( $this, 'get_campaigns_for_donation' ),
                ),
                'campaign_categories' => array(
                    'description'   => __( 'The categories of the campaigns that were donated to', 'charitable' ),
                    'callback'      => array( $this, 'get_campaign_categories_for_donation' ),
                ),
            );
        }

        /**
         * Get the value for a particular email field.
         *
         * @since  1.5.0
         *
         * @param  string $field The field.
         * @param  array  $args  Mixed arguments.
         * @return string
         */
        public function get_field_value( $field, $args ) {
            $value = '';

            if ( $this->preview ) {
                return $this->get_field_preview_value( $field, $args );
            }

            if ( array_key_exists( $field, $this->fields ) ) {
                $callback = false;

                if ( array_key_exists( 'callback', $this->fields[ $field ] ) ) {
                    $callback = $this->fields[ $field ]['callback'];
                } elseif ( method_exists( array( $this, 'get_' . $field ) ) ) {
                    $callback = array( $this, 'get_' . $field );
                }

                if ( is_callable( $callback ) ) {
                    $value = call_user_func( $callback, $value, $args, $this->email );
                }
            }

            /**
             * Filter the returned value.
             *
             * @since 1.0.0
             *
             * @param string           $value The field value.
             * @param array            $args  Mixed arguments.
             * @param Charitable_Email $email The Email object.
             */
            return apply_filters( 'charitable_email_content_field_value_' . $field, $value, $args, $this->email );
        }

        /**
         * Return the field preview value.
         *
         * @since  1.5.0
         *
         * @param  string $field The field to search for.
         * @param  array  $args  Mixed arguments.
         * @return string
         */
        public function get_field_preview_value( $field, $args ) {
               
        }

        /**
         * Checks whether the email has a valid donation object set.
         *
         * @since  1.5.0
         *
         * @return boolean
         */
        public function has_valid_donation() {
            if ( is_null( $this->donation ) || ! is_a( $this->donation, 'Charitable_Donation' ) ) {
                _doing_it_wrong( __METHOD__, __( 'You cannot send this email without a donation!', 'charitable' ), '1.5.0' );
                return false;
            }

            return true;
        }

        /**
         * Return the first name of the donor.
         *
         * @since  1.5.0
         *
         * @return string
         */
        public function get_donor_first_name() {            
            return $this->donation->get_donor()->get_donor_meta( 'first_name' );
        }

        /**
         * Return the donor's phone number.
         *
         * @since  1.5.0
         *
         * @return string
         */
        public function get_donor_phone() {            
            return $this->donation->get_donor()->get_donor_meta( 'phone' );
        }

        /**
         * Returns a summary of the donation, including all the campaigns that were donated to.
         *
         * @since  1.5.0
         *
         * @param  string           $value The content to show in place of the shortcode.
         * @param  mixed[]          $args  Array of optional arguments.
         * @param  Charitable_Email $email The email object.
         * @return string
         */
        public function get_donation_summary( $value, $args, Charitable_Email $email ) {
            $output = '';

            foreach ( $this->donation->get_campaign_donations() as $campaign_donation ) {

                $line_item = sprintf( '%s: %s%s',
                    $campaign_donation->campaign_name,
                    charitable_format_money( $campaign_donation->amount ),
                    PHP_EOL
                );

                $output .= apply_filters( 'charitable_donation_summary_line_item_email', $line_item, $campaign_donation, $args, $email );

            }

            return $output;
        }

        /**
         * Return the total amount donated.
         *
         * @since  1.5.0
         *
         * @param  string $value Content to show in place of shortcode.
         * @return string
         */
        public function get_donation_total( $value ) {
            return charitable_format_money( $this->donation->get_total_donation_amount() );
        }

        /**
         * Returns the date the donation was made.
         *
         * @since  1.5.0
         *
         * @param  string  $value Content to show in place of shortcode.
         * @param  mixed[] $args  Optional arguments.
         * @return string
         */
        public function get_donation_date( $value, $args ) {
            $format = isset( $args['format'] ) ? $args['format'] : get_option( 'date_format' );
            return $this->donation->get_date( $format );
        }

        /**
         * Returns the status of the donation.
         *
         * @since  1.5.0
         *
         * @return string
         */
        public function get_donation_status() {            
            return $this->donation->get_status( true );
        }

        /**
         * Return the campaigns donated to.
         *
         * @since  1.5.0
         *
         * @param  string $value The content to display in place of the shortcode.
         * @param  array  $args  Optional set of arguments.
         * @return string
         */
        public function get_campaigns_for_donation( $value, $args ) {
            $linked = array_key_exists( 'with_links', $args ) ? $args['with_links'] : false;
            return $this->donation->get_campaigns_donated_to( $linked );
        }

        /**
         * Return the categories of the campaigns that were donated to.
         *
         * @since  1.5.0
         *
         * @param  string $value The content to display in place of the shortcode.
         * @return string
         */
        public function get_campaign_categories_for_donation( $value ) {
            $categories = $this->donation->get_campaign_categories_donated_to( 'campaign_category', array(
                'fields' => 'names',
            ) );

            return implode( ', ', $categories );
        }
    }

endif;