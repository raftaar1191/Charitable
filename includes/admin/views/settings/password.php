<?php
/**
 * Display password field.
 *
 * @author 	Studio 164a
 * @package Charitable/Admin View/Settings
 * @since   1.5.12
 */

$value = charitable_get_option( $view_args['key'] );

if ( empty( $value ) ) :
	$value = isset( $view_args['default'] ) ? $view_args['default'] : '';
endif;

?>

<input type="password"  
	id="<?php printf( 'charitable_settings_%s', implode( '_', $view_args['key'] ) ) ?>" 
	name="<?php printf( 'charitable_settings[%s]', $view_args['name'] ) ?>"
	value="<?php echo esc_attr( $value ) ?>" 
	class="<?php echo esc_attr( $view_args['classes'] ) ?>" 
	<?php echo charitable_get_arbitrary_attributes( $view_args ) ?> />

<?php if ( isset( $view_args['help'] ) ) : ?>

	<div class="charitable-help"><?php echo $view_args['help']  ?></div>

<?php endif;
