<?php
/**
 * Display the search at the top of the Donors list table.
 *
 * @author  Studio 164a
 * @package Charitable/Admin View/Donor Search
 * @since   1.7.0
 * @version 1.7.0
 */

$input_id = $input_id . '-search-input';

if ( ! empty( $_REQUEST['orderby'] ) ) {
	echo sprintf( '<input type="hidden" name="orderby" value="%1$s" />', esc_attr( $_REQUEST['orderby'] ) );
}

if ( ! empty( $_REQUEST['order'] ) ) {
	echo sprintf( '<input type="hidden" name="order" value="%1$s" />', esc_attr( $_REQUEST['order'] ) );
}
?>
<p class="search-box" role="search">
	<label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>"><?php echo $text; ?>:</label>
	<input type="search" id="<?php echo esc_attr( $input_id ); ?>" name="s" value="<?php _admin_search_query(); ?>"/>
	<?php submit_button( $text, 'button', false, false, array( 'ID' => 'search-submit' ) ); ?>
</p>
