<?php
/**
 * Renders the donation details meta box for the Donation post type.
 *
 * @author  	Studio 164a
 * @package 	Charitable/Admin Views/Metaboxes
 * @copyright   Copyright (c) 2017, Studio 164a
 * @since   	1.0.0
 */

global $post;

$date_format 	  = get_option( 'date_format' );
$time_format 	  = get_option( 'time_format' );
$date_time_format = "$date_format - $time_format";
?>
<div id="charitable-email-log-metabox" class="charitable-metabox">
	<table class="widefat">
		<thead>
			<tr>
				<th><?php _e( 'Email', 'charitable' ) ?></th>
				<th><?php _e( 'Status', 'charitable' ) ?></th>
				<th><?php _e( 'Action', 'charitable' ) ?></th>
			</th>
		</thead>
		<?php foreach ( Charitable_Emails::get_instance()->get_available_emails() as $email_class ) :
			$email  = new $email_class;
			$log    = $email->get_log( $post->ID );

			if ( empty( $log ) ) :
				$status = __( 'Not sent', 'charitable' );
				$action = __( 'Send now', 'charitable' );
			else :
				if ( current( $log ) ) :
					$status = sprintf( __( '<em>%s:</em> Successfully sent.', 'chairtable' ), date_i18n( $date_time_format, key( $log ) ) );
					$action = __( 'Resend', 'charitable' );
				else :
					$status = sprintf( __( '<em>%s:</em> Attempted to send, but failed.', 'chairtable' ), date_i18n( $date_time_format, key( $log ) ) );
					$action = __( 'Retry', 'charitable' );
				endif;

				array_shift( $log );
			endif;

			?>
			<tr>
				<th><?php echo $email->get_name() ?></th>
				<td><?php echo $status ?></td>
				<td><a href="#" class="button"><?php echo $action ?></a></td>
			</tr>
			<?php if ( count( $log ) ) :
				foreach ( $log as $time => $successful ) :
					if ( $successful ) :
						$status = sprintf( __( '%s: Successfully sent.', 'chairtable' ), date_i18n( $time, $date_time_format ) );
					else :
						$status = sprintf( __( '%s: Attempted to send, but failed.', 'chairtable' ), date_i18n( $time, $date_time_format ) );
					endif;
					?>
					<tr>
						<td></td>
						<td><?php echo $status ?></td>
						<td></td>
					</tr>
				<?php endforeach ?>
			<?php endif ?>
		<?php endforeach ?>		
	</table>
</div>
