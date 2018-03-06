<?php
/**
 * Returns an array of all the default campaign fields.
 *
 * @package   Charitable/Campaign Fields
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

/**
 * Filter the set of default campaign fields.
 *
 * This filter is provided primarily for internal use by Charitable
 * extensions, as it allows us to add to the registered campaign fields
 * as soon as possible.
 *
 * @since 1.5.0
 *
 * @param array $fields The multi-dimensional array of keys in $key => $args format.
 */
return apply_filters( 'charitable_default_campaign_fields', array(
	'suggested_donations'    => array(
		'label'          => __( 'Suggested Donation Amounts', 'charitable' ),
		'data_type'      => 'meta',
		'admin_form'     => array(
			'section'  => 'campaign-donation-options',
			'type'     => 'array',
			'view'     => 'metaboxes/campaign-donation-options/suggested-amounts',
			'priority' => 4,
		),
		'show_in_export' => true,
	),
	'allow_custom_donations' => array(
		'label'          => __( 'Allow Custom Donations', 'charitable' ),
		'data_type'      => 'meta',
		'admin_form'     => array(
			'section'  => 'campaign-donation-options',
			'type'     => 'checkbox',
			'priority' => 6,
		),
		'show_in_export' => true,
	),
) );
