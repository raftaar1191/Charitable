<?php
/**
 * Email Fields class.
 *
 * @since   1.5.0
 * @version 1.5.0
 * @package Charitable/Classes/Charitable_Email_Fields
 * @author  Eric Daams
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'Charitable_Email_Fields' ) ) :

    /**
     * Charitable_Email_Fields class.
     *
     * @since 1.5.0
     */
    class Charitable_Email_Fields {

        /**
         * Email object.
         *
         * @since 1.5.0
         *
         * @var   Charitable_Email
         */
        private $email;

        /**
         * Whether this is an email preview.
         *
         * @since 1.5.0
         *
         * @var   boolean
         */
        private $preview;

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
            $fields = array(
                'site_name' => array(
                    'description' => __( 'Your website title', 'charitable' ),
                    'callback'    => array( $this, 'get_site_name' ),
                ),
                'site_url'  => array(
                    'description'   => __( 'Your website URL', 'charitable' ),
                    'callback'      => 'home_url',
                ),
            );

            foreach ( $this->email->get_object_types() as $type ) {
                $class = 'Charitable_Email_Fields_' . ucfirst( $type );

                if ( ! class_exists( $class ) || 'Charitable_Email_Fields' != get_parent_class( $class ) ) {
                    continue;
                }

                $type_fields = new $class( $this->email, $this->preview );
                $fields      = array_merge( $fields, $type_fields->get_fields() );
            }

            /**
             * Filter the email content fields.
             *
             * @since 1.0.0
             *
             * @param array            $fields Registered fields.
             * @param Charitable_Email $email  Instance of `Charitable_Email` type.
             */
            return apply_filters( 'charitable_email_content_fields', $fields, $this->email );
        }

        /**
         * Return the fields array.
         *
         * @since  1.5.0
         *
         * @return array
         */
        public function get_fields() {
            return $this->fields;
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
                if ( array_key_exists( 'callback', $this->fields[ $field ] ) ) {
                    $value = call_user_func( $this->fields[ $field ]['callback'], $value, $args, $this->email );
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
            return '';
        }
    }

endif;