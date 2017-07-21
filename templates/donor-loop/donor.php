<?php
/**
 * Display a single donor within a loop.
 *
 * Override this template by copying it to yourtheme/charitable/donor-loop/donor.php
 *
 * @package Charitable/Templates/Donor
 * @author  Studio 164a
 * @since   1.5.0
 * @version 1.5.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

/* Donor has to be included in the view args. */
if ( ! array_key_exists( 'donor', $view_args ) ) {
	return;
}

$donor = $view_args['donor'];

?>
<li class="donor">
	<?php
	/**
	 * Display details about the donor.
	 *
	 * @param 	Charitable_Donor $donor     The Donor object.
	 * @param 	array 			 $view_args View arguments.
	 * @since 	1.5.0
	 */
	do_action( 'charitable_donor_loop_donor', $donor, $view_args );

	echo $donor->get_avatar();

	if ( $view_args['show_name'] ) : ?>

		<p class="donor-name"><?php echo $donor->get_name() ?></p>

	<?php

	endif;

	if ( $view_args['show_location'] && strlen( $donor->get_location() ) ) : ?>

		<div class="donor-location"><?php echo $donor->get_location() ?></div>

	<?php

	endif;

	if ( $view_args['show_amount'] ) : ?>

		<div class="donor-donation-amount"><?php echo charitable_format_money( $donor->get_amount( $campaign_id ) ) ?></div>

	<?php endif ?>
</li><!-- .donor-<?php echo $donor->donor_id ?> -->

