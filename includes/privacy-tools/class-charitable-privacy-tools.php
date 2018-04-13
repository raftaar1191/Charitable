<?php
/**
 * A class providing privacy tools for Charitable.
 *
 * @package   Charitable/Classes/Charitable_Privacy_Tools
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

if ( ! class_exists( 'Charitable_Privacy_Tools' ) ) :

	/**
	 * Charitable_Privacy_Tools
	 *
	 * @since 1.6.0
	 */
	class Charitable_Privacy_Tools {

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
				'exporter_friendly_name' => __( 'Charitable Plugin' ),
				'callback'               => array( $this, 'export_user_data' ),
			);

			return $exporters;
		}

		/**
		 * Return a user's data.
		 *
		 * @since  1.6.0
		 *
		 * @param  string $email The user's email address.
		 * @param  int    $page  The page of data to retrieve.
		 * @return array
		 */
		public function export_user_data( $email, $page = 1 ) {
			$export_items = array();

			/**
			 * 1. Get donor profile.
			 */
			$profiles = charitable_get_table( 'donors' )->get_personal_data( $email );

			if ( is_array( $profiles ) ) {
				foreach ( $profiles as $profile ) {
					$export_items[] = array(
						'item_id'     => 'donor-' . $profile->donor_id,
						'group_id'    => 'donors',
						'group_label' => __( 'Donor Profiles', 'charitable' ),
						'data'        => array(
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
						),
					);
				}
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
	}

endif;
