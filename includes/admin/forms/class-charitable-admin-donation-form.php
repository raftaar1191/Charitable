<?php
/**
 * Admin donation form model class.
 *
 * @version   1.5.0
 * @package   Charitable/Classes/Charitable_Admin_Donation_Form
 * @author    Eric Daams
 * @copyright Copyright (c) 2017, Studio 164a
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'Charitable_Admin_Donation_Form' ) ) :

	/**
	 * Charitable_Admin_Donation_Form
	 *
	 * @since  1.5.0
	 */
	class Charitable_Admin_Donation_Form extends Charitable_Admin_Form {

		/**
		 * Current Charitable_Donation object, or false if it's a new donation.
		 *
		 * @since 1.5.0
		 *
		 * @var   Charitable_Donation|false
		 */
		protected $donation;

		/**
		 * Create a donation form object.
		 *
		 * @since 1.5.0
		 *
		 * @param Charitable_Donation|false $donation For existing donations, the `Charitable_Donation` instance.
		 *                                            False for new donations.
		 */
		public function __construct( $donation ) {
			$this->id       = uniqid();
			$this->donation = $donation;

			$this->attach_hooks_and_filters();
		}

		/**
		 * Return the current Charitable_Donation instance, or false if this is a new donation.
		 *
		 * @since  1.5.0
		 *
		 * @return Charitable_Donation|false
		 */
		public function get_donation() {
			return $this->donation;
		}

		/**
		 * Return the donation form fields.
		 *
		 * @since  1.5.0
		 *
		 * @return array[]
		 */
		public function get_fields() {
			$fields = apply_filters( 'charitable_admin_donation_form_fields', array(
				'donation_fields' => array(
					'legend'   => __( 'Donation', 'charitable' ),
					'type'     => 'fieldset',
					'fields'   => $this->get_donation_fields(),
					'priority' => 20,
				),
				'user_fields' => array(
					'legend'   => __( 'Donor Details', 'charitable' ),
					'type'     => 'fieldset',
					'fields'   => $this->get_user_fields(),
					'class'    => 'fieldset',
					'priority' => 40,
				),
			), $this );

			uasort( $fields, 'charitable_priority_sort' );

			return $fields;
		}

		/**
		 * Get donation fields.
		 *
		 * @since  1.5.0
		 *
		 * @return array
		 */
		public function get_donation_fields() {
			if ( ! $this->donation ) {
				$value = array();
			} else {
				$value = $this->donation->get_campaign_donations();
			}

			return array(
				'campaign_donations' => array(
					'type'  => 'campaign-donations',
					'value' => $value,
				),
			);
		}

		/**
		 * Get the user fields.
		 *
		 * @since  1.5.0
		 *
		 * @return array
		 */
		public function get_user_fields() {
			$fields = charitable()->donation_fields()->get_admin_form_fields();
			$keys   = array_keys( $fields );
			$fields = array_combine(
				$keys,
				array_map( array( $this, 'set_field_value' ), wp_list_pluck( $fields, 'admin_form' ), $keys )
			);

			uasort( $fields, 'charitable_priority_sort' );

			return $fields;
		}

		/**
		 * Validate the form submission.
		 *
		 * @since  1.4.4
		 *
		 * @return boolean
		 */
		public function validate_submission() {

			/* If we have already validated the submission, return the value. */
			if ( $this->validated ) {
				return $this->valid;
			}

			$this->validated = true;

			$this->valid = $this->validate_security_check()
				&& $this->check_required_fields( $this->get_merged_fields() )
				&& $this->validate_amount();

			$this->valid = apply_filters( 'charitable_validate_admin_donation_form_submission', $this->valid, $this );

			return $this->valid;

		}

		/**
		 * Return the donation values.
		 *
		 * @since  1.5.0
		 *
		 * @return array
		 */
		public function get_donation_values() {
			$submitted = $this->get_submitted_values();


			return apply_filters( 'charitable_admin_donation_form_submission_values', $values, $submitted, $this );
		}

		/**
		 * Render the donation form.
		 *
		 * @since  1.5.0
		 *
		 * @return void
		 */
		public function render() {
			charitable_template( 'donation-form/form-donation.php', array(
				'campaign' => null,
				'form'     => $this,
				'form_id'  => 'charitable-admin-donation-form',
			) );
		}

		/**
		 * Render a form field.
		 *
		 * @since  1.5.0
		 *
		 * @param  array           $field     Field definition.
		 * @param  string          $key       Field key.
		 * @param  Charitable_Form $form      The form object.
		 * @param  int             $index     The current index.
		 * @param  string          $namespace Namespace for the form field's name attribute.
		 * @return boolean False if the field was not rendered. True otherwise.
		 */
		public function render_field( $field, $key, $form, $index = 0, $namespace = null ) {
			if ( ! $form->is_current_form( $this->id ) ) {
				return false;
			}

			if ( ! isset( $field['type'] ) ) {
				return false;
			}

			
		}

		/**
		 * Set a field's initial value.
		 *
		 * @since  1.5.0
		 *
		 * @param  array  $field Field definition.
		 * @param  string $key   The key of the field.
		 * @return array
		 */
		protected function set_field_value( $field, $key ) {
			$field['value'] = $field['default'];

			if ( array_key_exists( $key, $_POST ) ) {
				$field['value'] = $_POST[ $key ];
			} elseif ( array_key_exists( 'donation_id', $_GET ) ) {
				$donation = charitable_get_donation( $_GET['donation_id'] );
				$field['value'] = $donation->get( $key );
			}

			return $field;
		}
	}

endif;
