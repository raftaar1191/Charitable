<?php
/**
 * Renders the donation form meta box for the Donation post type.
 *
 * @since  1.5.0
 *
 * @author Studio 164a 
 */
global $post;

$form = new Charitable_Admin_Donation_Form();

?>
<div class="charitable-form-fields cf">        
    <?php
    $i = 1;
    foreach ( $form->get_fields() as $key => $field ) :

        do_action( 'charitable_form_field', $field, $key, $form, $i );

        $i += apply_filters( 'charitable_form_field_increment', 1, $field, $key, $form, $i );

    endforeach;
    ?>
</div><!-- .charitable-form-fields -->
<div class="charitable-form-field charitable-submit-field">
    <button class="button button-primary" type="submit" name="donate"><?php _e( 'Submit Donation', 'charitable' ) ?></button>    
</div><!-- .charitable-submit-field -->
