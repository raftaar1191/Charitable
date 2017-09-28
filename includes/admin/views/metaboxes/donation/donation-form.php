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

unset( $fields['meta_fields'] );

?>
<div class="charitable-form-fields primary">
    <?php
    $form->view()->render_hidden_fields();
    $form->view()->render_fields( $fields );
    ?>
</div><!-- .charitable-form-fields -->
