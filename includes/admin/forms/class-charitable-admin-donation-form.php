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
	class Charitable_Admin_Donation_Form extends Charitable_Form {

		/**
		 * Create a donation form object.
		 *
		 * @since 1.0.0
		 *
		 * @param Charitable_Campaign|null $campaign Campaign receiving the donation.
		 */
		public function __construct() {
			$this->campaign = $campaign;
			$this->id       = uniqid();

			$this->attach_hooks_and_filters();
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
					'type'     => 'charitable-fieldset',
					'fields'   => $this->get_donation_fields(),
					'priority' => 20,
				),
				'user_fields' => array(
					'legend'   => __( 'Donor Details', 'charitable' ),
					'type'     => 'fieldset',
					'fields'   => $this->get_user_fields(),
					'class'    => 'charitable-fieldset',
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
			return array();
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
	}

endif;
