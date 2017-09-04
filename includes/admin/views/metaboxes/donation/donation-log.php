<?php
/**
 * Renders the donation details meta box for the Donation post type.
 *
 * @author  Studio 164a
 * @since   1.0.0
 */
global $post;

$logs = get_post_meta( $post->ID, '_donation_log', true );

$date_format = get_option( 'date_format' );
$time_format = get_option( 'time_format' );
$date_time_format = "$date_format - $time_format";

foreach ( array( '_email_donation_receipt_log', '_email_new_donation_log' ) as $log_key ) {
	$email_log = get_post_meta( $post->ID, $log_key, true );

	if ( $email_log && is_array( $email_log ) ) {
		foreach ( $email_log as $time => $sent ) {
			if ( $sent ) {
				$message = sprintf( __( '%s was sent successfully. <a href="#">Resend it now.</a>', 'charitable' ), $log_key );
			} else {
				$message = sprintf( __( '%s failed to send. <a href="#">Retry email send.</a>', 'charitable' ), $log_key );
			}

			$logs[] = array(
				'time' => $time,
				'message' => $message,
			);
		}
	}
}

// echo '<pre>'; var_dump( $logs ); echo '</pre>';
?>
<div id="charitable-donation-log-metabox" class="charitable-metabox">
	<table class="widefat">
		<thead>
			<tr>
				<th><?php _e( 'Date &amp; Time', 'charitable' ) ?></th>
				<th><?php _e( 'Log', 'charitable' ) ?></th>
			</th>
		</thead>
		<?php foreach ( $logs as $log ) : ?>
		<tr>
			<td><?php echo date_i18n( $date_time_format, $log['time'] ) ?></td>
			<td><?php echo $log['message'] ?></td>
		</tr>
		<?php endforeach ?>
	</table>
</div>
