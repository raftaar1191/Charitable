<?php
/**
 * The template used to display the donation amount inputs.
 *
 * @author  Studio 164a
 * @package Charitable/Templates/Donation Form
 * @since   1.0.0
 * @version 1.4.18
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! isset( $view_args['form'] ) ) {
	return;
}

/* @var Charitable_Donation_Form */
$form     = $view_args['form'];
$campaign = $form->get_campaign();

if ( is_null( $campaign ) ) {
	return;
}

$suggested       = $campaign->get_suggested_donations();
$amount          = $campaign->get_donation_amount_in_session();
$currency_helper = charitable_get_currency_helper();

if ( empty( $suggested ) && ! $campaign->get( 'allow_custom_donations' ) ) {
	return;
}

/**
 * @hook    charitable_donation_form_before_donation_amount
 */
do_action( 'charitable_donation_form_before_donation_amount', $view_args['form'] );

?>
<div class="charitable-donation-options">

	<?php

	/**
	 * @hook    charitable_donation_form_before_donation_amounts
	 */
	do_action( 'charitable_donation_form_before_donation_amounts', $view_args['form'] );

	charitable_template( 'donation-form/donation-amount-list.php', array( 'campaign' => $campaign, 'form' => $form ) );
	
	/**
	 * @hook    charitable_donation_form_after_donation_amounts
	 */
	do_action( 'charitable_donation_form_after_donation_amounts', $view_args['form'] );
	?>

</div><!-- #charitable-donation-options-<?php echo $view_args['form']->get_form_identifier() ?> -->

<?php
/**
 * @hook    charitable_donation_form_after_donation_amount
 */
do_action( 'charitable_donation_form_after_donation_amount', $view_args['form'] );
