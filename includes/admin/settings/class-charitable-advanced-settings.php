<?php
/**
 * Charitable Advanced Settings UI.
 *
 * @package     Charitable/Classes/Charitable_Advanced_Settings
 * @version     1.0.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2018, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

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

			return array(
				'section'                     => array(
					'title'    => '',
					'type'     => 'hidden',
					'priority' => 10000,
					'value'    => 'advanced',
				),
				'section_privacy'             => array(
					'title'    => __( 'Privacy', 'charitable' ),
					'type'     => 'heading',
					'priority' => 20,
				),
				'section_privacy_description'   => array(
					'type'      => 'content',
					'priority'  => 21,
					'content'   => __( 'Charitable stores personal data such as donors\' names, email addresses, addresses and phone numbers in your database. Donors may request to have their personal data erased (as of May 2018, this will be a right of European residents), but ' )
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
				'section_dangerous'           => array(
					'title'    => __( 'Dangerous Settings', 'charitable' ),
					'type'     => 'heading',
					'priority' => 100,
				),
				'delete_data_on_uninstall'    => array(
					'label_for' => __( 'Reset Data', 'charitable' ),
					'type'      => 'checkbox',
					'help'      => __( 'DELETE ALL DATA when uninstalling the plugin.', 'charitable' ),
					'priority'  => 105,
				),
			);
		}
	}

endif;
