<?php
/**
 * Display section heading in settings area.
 *
 * @author 	Studio 164a
 * @package Charitable/Admin View/Settings
 * @since   1.0.0
 */
?>
<?php if ( isset( $view_args['description'] ) ) : ?>

	<div class="charitable-description"><?php echo $view_args['description']  ?></div>

<?php else: ?>
<hr />
<?php endif;