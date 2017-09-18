<?php
/**
 * Charitable Fields model.
 *
 * @package   Charitable/Classes/Charitable_Fields
 * @version   1.5.0
 * @author    Eric Daams
 * @copyright Copyright (c) 2017, Studio 164a
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'Charitable_Fields' ) ) :

    /**
     * Charitable_Fields
     *
     * @since 1.5.0
     */
    class Charitable_Donation_Fields implements Charitable_Fields_Interface {

        /**
         * The `Charitable_Field_Registry` instance for this object.
         *
         * @since 1.5.0
         *
         * @var   Charitable_Field_Registry
         */
        private $registry;

        /**
         * Instance of `Charitable_Donation`.
         *
         * @since 1.5.0
         *
         * @var   Charitable_Donation
         */
        private $donation;

        /**
         * Create class object.
         *
         * @since 1.5.0
         *
         * @param Charitable_Fields_Registry $registry An instance of `Charitable_Field_Registry_Interface`.
         * @param Charitable_Donation        $donation A `Charitable_Donation` instance.
         */
        public function __construct( Charitable_Field_Registry_Interface $registry, Charitable_Donation $donation ) {
            $this->registry = $registry;
            $this->donation = $donation;
        }

        /**
         * Get the set value for a particular field.
         *
         * @since  1.5.0
         *
         * @param  string $field The field to get a value for.
         * @return mixed
         */
        public function get( $field ) {
            if ( ! $this->has( $field ) ) {
                return null;
            }
            
            return call_user_func( $this->registry[ $field ]->value_callback, $this->donation, $field );        
        }

        /**
         * Check whether a particular field is registered.
         *
         * @since  1.5.0
         *
         * @param  string $field The field to check for.
         * @return boolean
         */
        public function has( $field ) {
            return array_key_exists( $field, $this->registry );
        }

        /**
         * Check whether a particular field has a callback for getting the value.
         *
         * @since  1.5.0
         *
         * @param  string $field The field to check for.
         * @return boolean
         */
        public function has_value_callback( $field ) {
            return $this->has( $field ) && false !== $this->registry[ $field ]->value_callback;
        }
    }

endif;
