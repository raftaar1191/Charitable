<?php 
/**
 * Renders the donation details meta box for the Donation post type.
 *
 * @author Studio 164a
 * @since  1.5.0
 */

global $post;

$helper  = charitable_get_donation_actions();
$actions = $helper->get_actions();
?>
<div id="charitable-donation-actions-metabox-wrapper" class="charitable-metabox">
	<div id="charitable-donation-actions-form">
		<?php do_action( 'charitable_donation_actions_start', $post->ID ); ?>
		<select id="charitable_donation_actions" name="charitable_donation_action">
			<option value=""><?php _e( 'Select an action', 'charitable' ) ?></option>
			<?php foreach ( $helper->get_groups() as $label => $group_actions ) : ?>
				<?php if ( ! empty( $label ) ) : ?>
					<optgroup label="<?php echo esc_attr( $label ) ?>">
				<?php endif ?>
					<?php foreach ( $group_actions as $action ) : ?>
						<option value="<?php echo esc_attr( $action ) ?>" data-button-text="<?php echo esc_attr( $actions[ $action ]['button_text'] ) ?>"><?php echo esc_html( $actions[ $action ]['label'] ) ?></option>
					<?php endforeach ?>
				<?php if ( ! empty( $label ) ) : ?>
					</optgroup>
				<?php endif ?>
			<?php endforeach ?>			
		</select>
		<?php do_action( 'charitable_donation_actions_end', $post->ID ); ?>
	</div><!-- #charitable-donation-actions-form -->
	<div id="charitable-donation-actions-submit">
		<button type="submit" class="button-primary" title="<?php esc_attr_e( 'Submit', 'charitable' ) ?>"><?php _e( 'Submit', 'charitable' ) ?></button>
		<div class="clear"></div>
	</div><!-- #charitble-donation-actions-submit -->
</div><!-- #charitable-donation-actions-metabox-wrapper -->
