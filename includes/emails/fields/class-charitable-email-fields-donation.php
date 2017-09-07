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
    class Charitable_Email_Fields_Donation implements Charitable_Email_Fields_Interface {

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
            $fields = array(
                'donor' => array(
                    'description' => __( 'The full name of the donor', 'charitable' ),                    
                    'preview'     => 'John Deere',            
                ),
                'donor_first_name' => array(
                    'description' => __( 'The first name of the donor', 'charitable' ),                    
                    'preview'     => 'John',
                ),
                'donor_email' => array(
                    'description' => __( 'The email address of the donor', 'charitable' ),                    
                    'preview'     => 'john@example.com',
                ),
                'donor_address' => array(
                    'description' => __( 'The donor\'s address', 'charitable' ),                    
                    'preview'     => charitable_get_location_helper()->get_formatted_address( array(
                        'first_name' => 'John',
                        'last_name'  => 'Deere',
                        'company'    => 'Deere & Company',
                        'address'    => 'One John Deere Place',
                        'city'       => 'Moline',
                        'state'      => 'Illinois',
                        'postcode'   => '61265',
                        'country'    => 'US',
                    ) ),
                ),
                'donor_phone' => array(
                    'description' => __( 'The donor\'s phone number', 'charitable' ),                    
                    'preview'     => '1300 000 000',
                ),
                'donation_id' => array(
                    'description' => __( 'The donation ID', 'charitable' ),                    
                    'preview'     => 164,
                ),
                'donation_summary' => array(
                    'description' => __( 'A summary of the donation', 'charitable' ),                    
                    'preview'     => __( 'Fake Campaign: $50.00', 'charitable' ) . PHP_EOL,
                ),
                'donation_amount' => array(
                    'description' => __( 'The total amount donated', 'charitable' ),                    
                    'preview'     => '$50.00',
                ),
                'donation_date' => array(
                    'description' => __( 'The date the donation was made', 'charitable' ),                    
                    'preview'     => date_i18n( get_option( 'date_format' ) ),
                ),
                'donation_status' => array(
                    'description' => __( 'The status of the donation (pending, paid, etc.)', 'charitable' ),                    
                    'preview'     => __( 'Paid', 'charitable' ),
                ),
                'campaigns' => array(
                    'description' => __( 'The campaigns that were donated to', 'charitable' ),                    
                    'preview'     => __( 'Fake Campaign', 'charitable' ),
                ),
                'campaign_categories' => array(
                    'description' => __( 'The categories of the campaigns that were donated to', 'charitable' ),                    
                    'preview'     => __( 'Fake Category', 'charitable' ),
                ),
            );

            if ( $this->has_valid_donation() ) {
                $donor  = $this->donation->get_donor();
                $fields = array_merge_recursive( $fields, array(
                    'donor'               => array( 'callback' => array( $donor, 'get_name' ) ),
                    'donor_first_name'    => array( 'callback' => array( $this, 'get_donor_first_name' ) ),
                    'donor_email'         => array( 'callback' => array( $donor, 'get_email' ) ),
                    'donor_address'       => array( 'callback' => array( $donor, 'get_address' ) ),
                    'donor_phone'         => array( 'callback' => array( $this, 'get_donor_phone' ) ),
                    'donation_id'         => array( 'callback' => array( $this->donation, 'get_donation_id' ) ),
                    'donation_summary'    => array( 'callback' => array( $this, 'get_donation_summary' ) ),
                    'donation_amount'     => array( 'callback' => array( $this, 'get_donation_total' ) ),
                    'donation_date'       => array( 'callback' => array( $this, 'get_donation_date' ) ),
                    'donation_status'     => array( 'callback' => array( $this, 'get_donation_status' ) ),
                    'campaigns'           => array( 'callback' => array( $this, 'get_campaigns_for_donation' ) ),
                    'campaign_categories' => array( 'callback' => array( $this, 'get_campaign_categories_for_donation' ) ),
                ) );
            }            

            /**
             * Filter the donation email fields.
             *
             * @since 1.5.0
             *
             * @param array               $fields   The default set of fields.
             * @param Charitable_Donation $donation Instance of `Charitable_Donation`.
             * @param Charitable_Email    $email    Instance of `Charitable_Email`.
             */
            return apply_filters( 'charitable_email_donation_fields', $fields, $this->donation, $this->email );
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
         * Checks whether the email has a valid donation object set.
         *
         * @since  1.5.0
         *
         * @return boolean
         */
        public function has_valid_donation() {
            return ! is_null( $this->donation ) && is_a( $this->donation, 'Charitable_Donation' );
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