<?php
/**
 * A class providing privacy tools for Charitable.
 *
 * @package   Charitable/Classes/Charitable_Privacy
 * @author    Eric Daams
 * @copyright Copyright (c) 2018, Studio 164a
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since     1.6.0
 * @version   1.6.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Charitable_Privacy' ) ) :

	/**
	 * Charitable_Privacy
	 *
	 * @since 1.6.0
	 */
	class Charitable_Privacy {

		/**
		 * User donation fields.
		 *
		 * @since 1.6.0
		 *
		 * @var   string[]
		 */
		protected $user_donation_fields;

		/**
		 * Set up class instance.
		 *
		 * @since  1.6.0
		 */
		public function __construct() {
			add_filter( 'wp_privacy_personal_data_exporters', array( $this, 'register_exporter' ), 10, 2 );
		}

		/**
		 * Register the data exporter.
		 *
		 * @since  1.6.0
		 *
		 * @param  array $exporters The list of exporters.
		 * @return array
		 */
		public function register_exporter( $exporters ) {
			$exporters[] = array(
				'exporter_friendly_name' => __( 'Charitable Donor Data', 'charitable' ),
				'callback'               => array( $this, 'export_user_data' ),
			);

			return $exporters;
		}

		/**
		 * Return a user's data.
		 *
		 * We export the following pieces of data:
		 *
		 * 1. Registered donor meta. (*_usermeta)
		 * 2. Donor profile records. (*_charitable_donors table)
		 * 3. Donation donor meta. (*_postmeta)
		 *
		 * @since  1.6.0
		 *
		 * @param  string $email The user's email address.
		 * @param  int    $page  The page of data to retrieve.
		 * @return array
		 */
		public function export_user_data( $email, $page = 1 ) {
			$export_items = array();

			if ( 1 === $page ) {
				/* 1. Get registered donor meta. */
				$user = get_user_by( 'email', $email );

				if ( $user instanceof WP_User ) {
					$export_items[] = $this->get_registered_donor_data( $user );
				}
			}

			/* 2. Get donor profile. */
			$profiles = charitable_get_table( 'donors' )->get_personal_data( $email );

			if ( is_array( $profiles ) && 1 === $page ) {
				$export_items = array_merge(
					$export_items,
					array_map( array( $this, 'get_donor_profile_data' ), $profiles )
				);
			}

			/* If there are no donor profiles, there are no donations; return whatever we have. */
			if ( empty( $profiles ) ) {
				return array(
					'data' => $export_items,
					'done' => true,
				);
			}

			/* 3. Donation donor meta */
			if ( ! empty( $this->get_user_donation_fields() ) ) {
				$donor_id     = wp_list_pluck( $profiles, 'donor_id' );
				$donations    = charitable_get_table( 'campaign_donations' )->get_distinct_ids( 'donation_id', $donor_id, 'donor_id' );
				$export_items = array_merge(
					$export_items,
					array_map( array( $this, 'get_personal_donation_meta_data' ), $donations )
				);
			}

			return array(
				'data' => $export_items,
				'done' => true,
			);
		}

		/**
		 * Anonymize user.
		 *
		 * @since  1.6.0
		 *
		 * @param  string $email The user's email address.
		 * @return boolean
		 */
		public function anonymize( $email ) {
		}

		/**
		 * Return a registered donor's data.
		 *
		 * @since  1.6.0
		 *
		 * @param  WP_User $user An instance of `WP_User`.
		 * @return array
		 */
		public function get_registered_donor_data( $user ) {
			$data    = array();
			$form    = new Charitable_Profile_Form;
			$methods = array(
				'get_user_fields',
				'get_address_fields',
				'get_social_fields',
			);
			$key_map = charitable_get_user_mapped_keys();

			foreach ( $methods as $method ) {
				$fields = call_user_func( array( $form, $method ) );

				if ( ! is_array( $fields ) ) {
					continue;
				}

				foreach ( $fields as $key => $field ) {
					$key = array_key_exists( $key, $key_map ) ? $key_map[ $key ] : $key;

					if ( ! $user->has_prop( $key ) ) {
						continue;
					}

					$data[] = array(
						'name'  => array_key_exists( 'label', $field ) ? $field['label'] : ucfirst( str_replace( '_', ' ', $key ) ),
						'value' => $user->{$key},
					);
				}
			}

			/**
			 * Filter the personal donor meta data for a particular registered donor.
			 *
			 * @since 1.6.0
			 *
			 * @param array   $data Set of personal donor data.
			 * @param WP_User $user An instance of `WP_User`.
			 */
			$data = apply_filters( 'charitable_privacy_export_personal_donor_profile_data', $data, $user );

			return array(
				'item_id'     => 'user',
				'group_id'    => 'charitable_users',
				'group_label' => __( 'Donor Meta', 'charitable' ),
				'data'        => $data,
			);
		}

		/**
		 * Returns donor profile data for export.
		 *
		 * @since  1.6.0
		 *
		 * @param  object $profile The profile record.
		 * @return array
		 */
		protected function get_donor_profile_data( $profile ) {
			/**
			 * Filter the personal donor record data.
			 *
			 * @since 1.6.0
			 *
			 * @param array  $data    Set of personal donor data.
			 * @param object $profile The profile record.
			 */
			$data = apply_filters( 'charitable_privacy_export_personal_donor_profile_data', array(
				array(
					'name'  => __( 'First Name', 'charitable' ),
					'value' => $profile->first_name,
				),
				array(
					'name'  => __( 'Last Name', 'charitable' ),
					'value' => $profile->last_name,
				),
				array(
					'name'  => __( 'Email', 'charitable' ),
					'value' => $profile->email,
				),
			) );

			return array(
				'item_id'     => 'donor-' . $profile->donor_id,
				'group_id'    => 'charitable_donors',
				'group_label' => __( 'Donor Profiles', 'charitable' ),
				'data'        => $data,
			);
		}

		/**
		 * Return the donor meta stored with a donor's donation records.
		 *
		 * @since  1.6.0
		 *
		 * @param  int $donation_id A donation ID.
		 * @return array
		 */
		protected function get_personal_donation_meta_data( $donation_id ) {
			$meta = get_post_meta( $donation_id, 'donor', true );
			$data = array();

			foreach ( $this->user_donation_fields as $field_id => $field ) {
				if ( array_key_exists( $field_id, $meta ) ) {
					$data[] = array(
						'name'  => $field->label,
						'value' => $meta[ $field_id ],
					);
				}
			}

			/**
			 * Filter the personal donation meta data for a particular donation.
			 *
			 * @since 1.6.0
			 *
			 * @param array $data        Set of personal donation meta data.
			 * @param array $data        The donor meta array.
			 * @param int   $donation_id The donation ID.
			 */
			$data = apply_filters( 'charitable_privacy_export_personal_donation_meta_data', $data, $meta, $donation_id );

			return array(
				'item_id'     => 'donation-' . $donation_id,
				'group_id'    => 'charitable_donations',
				'group_label' => __( 'Donation Meta', 'charitable' ),
				'data'        => $data,
			);
		}

		/**
		 * Get the user donation fields.
		 *
		 * @since  1.6.0
		 *
		 * @return array
		 */
		protected function get_user_donation_fields() {
			if ( ! isset( $this->user_donation_fields ) ) {
				$this->user_donation_fields = charitable()->donation_fields()->get_data_type_fields( 'user' );
			}

			return $this->user_donation_fields;
		}
	}

endif;
