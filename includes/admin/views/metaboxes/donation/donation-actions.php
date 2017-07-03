<?php 
/**
 * Renders the donation details meta box for the Donation post type.
 *
 * @author  Studio 164a
 * @since   1.5.0
 */
global $post;

?>
<div id="charitable-donation-actions-metabox-wrapper" class="charitable-metabox">
<?php		
	global $donation;

	// This is used by some callbacks attached to hooks such as charitable_donation_actions which rely on the global to determine if actions should be displayed for certain orders.
	if ( ! is_object( $donation ) ) {
		$donation = charitable_get_donation( $post->ID );
	}

	$donation_type_object = get_post_type_object( $post->post_type );
	?>
	<div id="charitable-donation-actions-form">
		<?php do_action( 'charitable_donation_actions_start', $post->ID, $donation ); ?>

		<select id="charitable_donation_actions" name="charitable_donation_action">
			<option value=""><?php _e( 'Select an action', 'charitable' ) ?></option>
			<optgroup label="<?php esc_attr_e( 'Resend Donation Emails', 'charitable' ) ?>">
				<?php
				$mailer           = Charitable_Emails::get_instance();
				$resend_emails = apply_filters( 'charitable_resend_donation_emails_available', array( 'donation_receipt', 'new_donation' ) );

				$enabled_emails   = $mailer->get_available_emails();
				$available_emails = array_intersect_key( $enabled_emails, array_flip( $resend_emails ) );

				$mail_names = $mailer->get_enabled_emails_names();

				if ( ! empty( $available_emails ) ) {
					foreach ( $available_emails as $id => $label ) {
						echo '<option value="send_email_'. esc_attr( $id ) .'">' . esc_html( $mail_names[$id] ) . '</option>';
					}
				}
				?>
			</optgroup>
			<?php foreach( apply_filters( 'charitable_donation_actions', array() ) as $action => $title ) : ?>
				<option value="<?php echo $action; ?>"><?php echo $title; ?></option>
			<?php endforeach ?>
		</select>
		<?php do_action( 'charitable_donation_actions_end', $post->ID, $donation ); ?>
	</div><!-- #charitable-donation-actions-form -->
	<div id="charitable-donation-actions-submit">
		<?php do_action( 'charitable_donation_actions_submit_start', $post->ID, $donation ); ?>
		<button type="submit" class="button-primary" title="<?php esc_attr_e( 'Submit', 'charitable' ) ?>"><?php _e( 'Submit', 'charitable' ) ?></button>
		<?php do_action( 'charitable_donation_actions_submit_end', $post->ID, $donation ); ?>
		<div class="clear"></div>
	</div><!-- #charitble-donation-actions-submit -->
</div><!-- #charitable-donation-actions-metabox-wrapper -->