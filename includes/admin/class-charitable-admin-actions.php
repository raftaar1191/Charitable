<?php
/**
 * Registers and performs admin actions.
 *
 * @package   Charitable/Classes/Charitable_Admin_Actions
 * @version   1.5.0
 * @author    Eric Daams
 * @copyright Copyright (c) 2017, Studio 164a
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'Charitable_Admin_Actions' ) ) :

    /**
     * Charitable_Admin_Actions
     *
     * @since 1.5.0
     */
    abstract class Charitable_Admin_Actions {

        /**
         * Create class object.
         *
         * @since 1.5.0
         */
        public function __construct() {
            $this->actions = array();
        }

        /**
         * Register a new action.
         *
         * @since  1.5.0
         *
         * @param  string $action The action key. 
         * @param  array  $args   {
         *     Array of arguments for the action.
         *
         *     @type string   $label           The label to display in the admin.
         *     @type callable $callback        A callback function to run when the action is processed.
         *     @type string   $button_text     Optional. The text to show in the button when this action is selected.
         *     @type callable $active_callback Optional. Any passed callback will receive a donation ID as its only parameter
         *                                     and should return a boolean result: TRUE if the action should be shown for
         *                                     the donation; FALSE if it should not.
         * }
         * @return boolean True if the action was registerd. False if not.
         */
        public function register( $action, $args ) {
            if ( array_key_exists( $this->actions, $action ) ) {
                return false;
            }

            if ( ! array_key_exists( 'label', $args ) || ! array_key_exists( 'callback', $args ) ) {
                return false;
            }

            $this->actions[ $action ] = $args;

            return true;
        }

        /**
         * Do a particular action.
         *
         * @since  1.5.0
         *
         * @param  string $action      The action to do.
         * @param  int    $donation_id The donation ID.
         * @return mixed|WP_Error WP_Error in case of error. Mixed results if the action was performed.
         */
        public function do_action( $action, $donation_id ) {
            if ( ! array_key_exists( $action, $this->actions ) ) {
                return new WP_Error( sprintf( __( 'Action "%s" is not registered.', 'charitable' ), $action ) );
            }

            $action_args = $this->actions[ $action ];

            if ( array_key_exists( 'active_callback', $action_args ) && ! call_user_func( $action_args['active_callback'] ) ) {
                return false;
            }

            add_action( 'charitable_action_' . $action, $ )
        }
    }

endif;
