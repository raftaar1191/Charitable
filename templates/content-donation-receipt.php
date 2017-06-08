<?php
/**
 * Displays the donation receipt.
 *
 * Override this template by copying it to yourtheme/charitable/content-donation-receipt.php
 *
 * @author  Studio 164a
 * @package Charitable/Templates/Donation Receipt
 * @since   1.0.0
 * @version 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

$content  = $view_args['content'];
$donation = $view_args['donation'];

/**
 * @hook    charitable_donation_receipt_before
 */
do_action( 'charitable_donation_receipt_before', $donation );

echo $content;

/**
 * @hook    charitable_donation_receipt
 */
do_action( 'charitable_donation_receipt', $donation );

/**
 * @hook    charitable_donation_receipt_after
 */
do_action( 'charitable_donation_receipt_after', $donation );
