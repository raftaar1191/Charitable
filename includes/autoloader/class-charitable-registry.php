<?php
/**
 * Responsible for holding instances of Charitable helper objects.
 *
 * @package   Charitable/Classes/Charitable_Registry
 * @version   1.5.0
 * @author    Eric Daams
 * @copyright Copyright (c) 2017, Studio 164a
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'Charitable_Registry' ) ) :

    /**
     * Charitable_Registry
     *
     * @since 1.5.0
     */
    class Charitable_Registry {

        /**
         * Registered objects.
         *
         * @since 1.5.0
         *
         * @var   array
         */
        private $objects;

        /**
         * Create class object.
         *
         * @since 1.5.0
         */
        public function __construct() {
            $this->objects = array();
        }

        /**
         * Return a registered object.
         *
         * @since  1.5.0
         *
         * @param  string $class The name of the class.
         * @return object|false Returns an object if the class exists, otherwise returns false.
         */
        public function get( $class ) {
            $class_key = $this->get_class_key( $class );

            if ( ! isset( $this->objects[ $class_key ] ) ) {
                if ( $class_key == $class ) {
                    $class = $this->get_class_name( $class_key );
                }

                if ( ! class_exists( $class ) ) {
                    wp_die( sprintf( __( 'Class %s does not exists.', 'charitable' ), $class ) );
                }

                if ( method_exists( $class, 'get_instance' ) ) {
                    $this->objects[ $class_key ] = $class::get_instance();
                } else {
                    $this->objects[ $class_key ] = new $class;
                }
            }
            
            return $this->objects[ $class_key ];
        }

        /**
         * Checks whether an object has already been registered.
         *
         * @since  1.5.0
         *
         * @param  string $class The name of the class.
         * @return boolean
         */
        public function has( $class ) {
            return array_key_exists( $this->get_class_key( $class ), $this->objects );
        }

        /**
         * Register an object that has already been instantiated.
         *
         * @since  1.5.0
         *
         * @param  object $object The object to be registered.
         * @return void
         */
        public function register_object( $object ) {
            $class     = get_class( $object );
            $class_key = $this->get_class_key( $class );

            $this->objects[ $class_key ] = $object;
        }

        /**
         * Given a class name, returns the key for that class.
         *
         * @since  1.5.0
         *
         * @param  string $class The class name.
         * @return string
         */
        public function get_class_key( $class ) {
            if ( false === strpos( $class, 'Charitable_' ) ) {
                return $class;
            }

            return strtolower( str_replace( 'Charitable_', '', $class ) );
        }

        /**
         * Return a sanitized class name.
         *
         * @since  1.5.0
         *
         * @param  string $class_key The class to return.
         * @return string
         */
        public function get_class_name( $class_key ) {
            $class_words = str_replace( '_', ' ', $class_key );
            $class_words = ucwords( $class_words );
            return 'Charitable_' . str_replace( ' ', '_', $class_words );
        }
    }

endif;
