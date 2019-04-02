<?php
$input_id = $input_id . '-search-input';

if ( ! empty( $_REQUEST['orderby'] ) ) {
	echo sprintf( '<input type="hidden" name="orderby" value="%1$s" />', esc_attr( $_REQUEST['orderby'] ) );
}

if ( ! empty( $_REQUEST['order'] ) ) {
	echo sprintf( '<input type="hidden" name="order" value="%1$s" />', esc_attr( $_REQUEST['order'] ) );
}
?>
<p class="search-box" role="search">
    <label class="screen-reader-text" for="<?php echo $input_id ?>"><?php echo $text; ?>:</label>
    <input type="search" id="<?php echo $input_id ?>" name="s" value="<?php _admin_search_query(); ?>"/>
	<?php submit_button( $text, 'button', false, false, array(
		'ID' => 'search-submit',
	) ); ?>
</p>