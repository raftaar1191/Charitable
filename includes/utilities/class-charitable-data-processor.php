<?php
/**
 * Responsible for accepting a set of raw data, such as form submission data,
 * and sanitizing and normalizing that data, depending on its data type and
 * the type of data (text, checkbox, etc.).
 *
 * @package   Charitable/Classes/Charitable_Data_Processor
 * @author    Eric Daams
 * @copyright Copyright (c) 2017, Studio 164a
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since     1.5.9
 * @version   1.5.9
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }


if ( ! class_exists( 'Charitable_Data_Processor' ) ) :

	class Charitable_Data_Processor {
		/**
		 * The raw input data.
		 *
		 * @since 1.2.0
		 *
		 * @var   array
		 */
		protected $data;

		/**
		 * The map of fields.
		 *
		 * @since 1.2.0
		 *
		 * @var   array
		 */
		protected $fields;

		/**
		 * This is the output data. It has the same structure
		 * as the map of fields, but has the sanitized &
		 * normalized values from the input data.
		 *
		 * @since 1.2.0
		 *
		 * @var   array
		 */
		protected $output;

		/**
		 * Instantiate the formatter with a dataset and array of fields.
		 *
		 * @since 1.5.9
		 *
		 * @param array $data   The raw input data.
		 * @param array $fields The map of fields.
		 */
		public function __construct( $data, $fields ) {
			$this->data   = $data;
			$this->fields = $fields;
			$this->output = array();

			$this->process_data( $this->fields );
		}

		/**
		 * Return a single field's output data.
		 *
		 * @since  1.5.9
		 *
		 * @param  string       $key       The field key.
		 * @param  string|false $data_type Optional. The data type.
		 * @return mixed|null
		 */
		public function get( $key, $data_type = false ) {
			if ( $data_type ) {
				return $this->get_from_data_type( $key, $data_type );
			} 

			return isset( $this->output[ $key ] ) ? $this->output[ $key ] : null;
		}

		/**
		 * Return a single field's output data, from a data type.
		 *
		 * @since  1.5.9
		 *
		 * @param  string $key       The field key.
		 * @param  string $data_type The data type.
		 * @return mixed|null
		 */
		public function get_from_data_type( $key, $data_type ) {
			return isset( $this->output[ $data_type ][ $key ] ) ? $this->output[ $data_type ][ $key ] : null;
		}

		/**
		 * Returns the full output array.
		 *
		 * @since  1.5.9
		 *
		 * @return array
		 */
		public function output() {
			return $this->output;
		}

		/**
		 * Process an array of fields.
		 *
		 * @since  1.5.9
		 *
		 * @param  array        $fields    Map of fields.
		 * @param  false|string $data_type Optional. Data type.
		 * @return void
		 */
		protected function process_data( $fields, $data_type = false ) {
			$data = array();

			foreach ( $fields as $key => $type ) {
				if ( is_array( $type ) ) {
					$this->process_data( $type, $key );
					continue;
				}

				$data[ $key ] = $this->process_field( $key, $type, $data_type );
			}

			if ( ! empty( $data ) ) {
				$this->set_output( $data, $data_type );
			}			
		}

		/**
		 * Add a set of data to the output array.
		 *
		 * @since  1.5.9
		 *
		 * @param  array        $data      The processed data.
		 * @param  false|string $data_type Optional. Data type.
		 * @return void
		 */
		protected function set_output( $data, $data_type = false ) {
			if ( $data_type ) {
				$this->output[ $data_type ] = $data;
			} else {
				$this->output = $data;
			}
		}

		/**
		 * Apply the correct function to a field, based on the type of function
		 * and the key, type and data type of the field.
		 *
		 * @since  1.5.9
		 *
		 * @param  array $functions Stack of functions, in order of priority, with $args
		 *                          passed as the value of the function.
		 * @return mixed|null
		 */
		protected function apply_function_to_field( $functions, $default = '' ) {
			foreach ( $functions as $function => $args ) {
				if ( method_exists( $this, $function ) ) {
					return call_user_func_array( array( $this, $function ), $args );
				}
			}

			return $default;
		}

		/**
		 * Process a single field, returning the set value or null.
		 *
		 * @since  1.5.9
		 *
		 * @param  string       $key       The field key.
		 * @param  string       $type      The type of field.
		 * @param  string|false $data_type Optional. The data type.
		 * @return mixed|null The set value of the field, or NULL if the
		 *                    field was not contained in the data.
		 */
		protected function process_field( $key, $type, $data_type = false ) {
			/* Retrieve the value. */
			$value = $this->apply_function_to_field( array(
				'process_' . $key       => array( $type, $data_type ),
				'process_' . $data_type => array( $key, $type ),
				'process_' . $type      => array( $key, $data_type ),
				'process_generic_field' => array( $key ),
			) );

			/* Return the value after it is sanitized. */
			return $this->apply_function_to_field( array(
				'sanitize_' . $key       => array( $value, $type, $data_type ),
				'sanitize_' . $data_type => array( $value, $key, $type ),
				'sanitize_' . $type      => array( $value, $key, $data_type ),
			), $value );
		}

		/**
		 * Process a generic field. i.e. One that hasn't been processed
		 * by any of the other processors.
		 *
		 * @since  1.5.9
		 *
		 * @param  string $key The field key.
		 * @return mixed|null The set value of the field, or NULL if the
		 *                    field was not contained in the data.
		 */
		public function process_generic_field( $key ) {
			return array_key_exists( $key, $this->data ) ? $this->data[ $key ] : null;
		}

		/**
		 * Process a checkbox field.
		 *
		 * @since  1.5.9
		 *
		 * @param  string $key The field key.
		 * @return int|string Returns 0 if the checkbox was not checked, or the
		 *                    value of the checkbox if checked.
		 */
		protected function process_checkbox( $key ) {
			return array_key_exists( $key, $this->data ) ? $this->data[ $key ] : 0;
		}

		/**
		 * Sanitize a number.
		 *
		 * @since  1.5.9
		 *
		 * @param  string|int The number to be sanitized.
		 * @return int
		 */
		protected function sanitize_number( $value ) {
			return intval( $value );
		}
	}

endif;