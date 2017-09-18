<?php
/**
 * Donation form interface.
 *
 * This defines a strict interface that donation forms must implement.
 *
 * @version   1.5.0
 * @package   Charitable/Interfaces/Charitable_Form_View_Interface
 * @author    Eric Daams
 * @copyright Copyright (c) 2017, Studio 164a
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! interface_exists( 'Charitable_Form_View_Interface' ) ) :

    /**
     * Charitable_Form_View_Interface interface.
     *
     * @since 1.5.0
     */
    interface Charitable_Form_View_Interface {

        /**
         * Render a form.
         *
         * @since  1.5.0
         *
         * @return void
         */
        public function render();

        /**
         * Render all of a form's fields.
         *
         * @since  1.5.0
         *
         * @return void
         */
        public function render_fields();

        /**
         * Render a specific form fields.
         *
         * @since  1.5.0
         *         
         * @param  array  $field Field definition.
         * @param  string $key   Field key.
         * @param  array  $args  Mixed array of arguments.
         * @return boolean
         */
        public function render_field( $field, $key, $args );
    }

endif; // End interface_exists check.