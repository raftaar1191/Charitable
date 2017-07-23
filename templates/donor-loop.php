<?php
/**
 * Display a list of donors, either for a specific campaign or sitewide.
 *
 * Override this template by copying it to yourtheme/charitable/donor-loop.php
 *
 * @package Charitable/Templates/Donor
 * @author  Studio 164a
 * @since   1.5.0
 * @version 1.5.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

/* Donors have to be included in the view args. */
if ( ! array_key_exists( 'donors', $view_args ) ) {
	return;
}

$donors      = $view_args['donors'];
$args        = $view_args;
$campaign_id = $view_args['campaign_id'];

if ( ! charitable_is_campaign_page() && 'current' == $campaign_id ) {
	return;
}

if ( 'all' == $campaign_id ) {
	$args['campaign_id'] = false;
} elseif ( 'currenty' == $campaign_id ) {
	$args['campaign_id'] = get_the_ID();
}

$orientation = array_key_exists( 'orientation', $view_args ) ? $view_args['orientation'] : 'vertical';

if ( $donors->count() ) : ?>	
	<ol class="donors-list donors-list-<?php echo $orientation ?>">
		<?php
		foreach ( $donors as $donor ) :

			$args['donor'] = $donor;

			charitable_template( 'donor-loop/donor.php', $args );

		endforeach;
		?>
	</ol>
<?php else : ?>
	<p><?php _e( 'No donors yet. Be the first!', 'charitable' ) ?></p>
<?php endif;


// .donors-list { list-style: none; padding-left: 0; margin-left: 0; }
// .donors-list .donor { padding-bottom: 1em; }
// .donors-list .donor-name { margin-bottom: 0; }
// .donors-list .donor-location { font-style: italic; }
// .donors-list.donors-list-horizontal .donor {float: left;padding: 0 20px 1em 0;}
// .donors-list.donors-list-vertical .donor { border-bottom: 1px solid #e6e6e6; }