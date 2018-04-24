<?php
/**
 * Charitable Advanced Settings UI.
 *
 * @package   Charitable/Classes/Charitable_Advanced_Settings
 * @author    Eric Daams
 * @copyright Copyright (c) 2018, Studio 164a
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since     1.0.0
 * @version   1.6.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Charitable_Advanced_Settings' ) ) :

	/**
	 * Charitable_Advanced_Settings
	 *
	 * @final
	 * @since   1.0.0
	 */
	final class Charitable_Advanced_Settings {

		/**
		 * The single instance of this class.
		 *
		 * @var     Charitable_Advanced_Settings|null
		 */
		private static $instance = null;

		/**
		 * Create object instance.
		 *
		 * @since   1.0.0
		 */
		private function __construct() {
		}

		/**
		 * Returns and/or create the single instance of this class.
		 *
		 * @since   1.2.0
		 *
		 * @return  Charitable_Advanced_Settings
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Add the advanced tab settings fields.
		 *
		 * @since   1.0.0
		 *
		 * @return  array<string,array>
		 */
		public function add_advanced_fields() {
			if ( ! charitable_is_settings_view( 'advanced' ) ) {
				return array();
			}

			$data_fields = $this->get_user_donation_field_options();

			return array(
				'section'                       => array(
					'title'    => '',
					'type'     => 'hidden',
					'priority' => 10000,
					'value'    => 'advanced',
				),
				'section_privacy'               => array(
					'title'    => __( 'User Privacy', 'charitable' ),
					'type'     => 'heading',
					'priority' => 20,
				),
				'section_privacy_description'   => array(
					'type'     => 'content',
					'priority' => 21,
					'content'  => '<p>' . __( 'Charitable stores personal data such as donors\' names, email addresses, addresses and phone numbers in your database. Donors may request to have their personal data erased, but you may be legally required to retain some personal data for donations made within a certain time. Below you can control how long personal data is retained for at a minimum, as well as which data fields must be retained.' ) . '</p>'
								. '<p><a href="https://github.com/Charitable/Charitable/blob/master/PRIVACY.md">' . __( 'Read more about Charitable & user privacy', 'charitable' ) . '</a></p>',
				),
				'minimum_data_retention_period' => array(
					'label_for' => __( 'Minimum Data Retention Period', 'charitable' ),
					'type'      => 'select',
					'help'      => sprintf(
						/* translators: %1$s: HTML strong tag. %2$s: HTML closing strong tag. %1$s: HTML break tag. */
						__( 'Prevent personal data from being erased for donations made within a certain amount of time.%3$sChoose %1$sNone%2$s to allow the personal data of any donation to be erased.%3$sChoose %1$sForever%2$s to prevent any personal data from being erased from donations, regardless of how long ago they were made.' ),
						'<strong>',
						'</strong>',
						'<br />'
					),
					'priority'  => 25,
					'default'   => 0,
					'options'   => array(
						0         => __( 'None', 'charitable' ),
						1         => __( 'One year', 'charitable' ),
						2         => __( 'Two years', 'charitable' ),
						3         => __( 'Three years', 'charitable' ),
						4         => __( 'Four years', 'charitable' ),
						5         => __( 'Five years', 'charitable' ),
						6         => __( 'Six years', 'charitable' ),
						7         => __( 'Seven years', 'charitable' ),
						8         => __( 'Eight years', 'charitable' ),
						9         => __( 'Nine years', 'charitable' ),
						10        => __( 'Ten years', 'charitable' ),
						'endless' => __( 'Forever', 'charitable' ),
					),
				),
				'data_retention_fields'         => array(
					'label_for' => __( 'Retained Data', 'charitable' ),
					'type'      => 'multi-checkbox',
					'priority'  => 30,
					'default'   => array_keys( $data_fields ),
					'options'   => $data_fields,
					'help'      => __( 'The checked fields will not be erased fields when personal data is erased for a donation made within the Minimum Data Retention Period.', 'charitable' ),
				),
				'section_dangerous'             => array(
					'title'    => __( 'Dangerous Settings', 'charitable' ),
					'type'     => 'heading',
					'priority' => 100,
				),
				'delete_data_on_uninstall'      => array(
					'label_for' => __( 'Reset Data', 'charitable' ),
					'type'      => 'checkbox',
					'help'      => __( 'DELETE ALL DATA when uninstalling the plugin.', 'charitable' ),
					'priority'  => 105,
				),
			);
		}

		/**
		 * Return the list of user donation field options.
		 *
		 * @since  1.6.0
		 *
		 * @return string[]
		 */
		protected function get_user_donation_field_options() {
			$fields = charitable()->donation_fields()->get_data_type_fields( 'user' );

			return array_combine(
				array_keys( $fields ),
				wp_list_pluck( $fields, 'label' )
			);
		}
	}

endif;
