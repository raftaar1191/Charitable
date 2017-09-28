<?php
/**
 * Renders the donation form meta box for the Donation post type.
 *
 * @since  1.5.0
 *
 * @author Studio 164a 
 */

$form   = $view_args['form'];
$fields = $form->get_fields();

?>
<div class="charitable-form-fields secondary">
    <?php $form->view()->render_field( $fields['meta_fields'], 'meta_fields' ) ?>
</div>
<div class="charitable-form-field charitable-submit-field">
    <button class="button button-primary" type="submit" name="donate"><?php _e( 'Submit Donation', 'charitable' ) ?></button>    
</div><!-- .charitable-submit-field -->
