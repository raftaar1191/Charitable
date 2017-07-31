<?php
/**
 * Displays the campaign loop.
 *
 * Override this template by copying it to yourtheme/charitable/campaign-loop.php
 *
 * @author  Studio 164a
 * @package Charitable/Templates/Campaign
 * @since   1.0.0
 * @version 1.2.3
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

$campaigns = $view_args['campaigns'];
$columns   = $view_args['columns'];
$args      = charitable_campaign_loop_args( $view_args );

if ( ! $campaigns->have_posts() ) :
	return;
endif;

if ( $columns > 1 ) :
	$loop_class = sprintf( 'campaign-loop campaign-grid campaign-grid-%d', $columns );
else :
	$loop_class = 'campaign-loop campaign-list';
endif;

/**
 * Add something before the campaign loop.
 *
 * @since   1.5.0
 *
 * @param   WP_Query $campaigns The campaigns.
 * @param   array    $args      Loop args.
 * @param   array    $view_args All arguments passed to the view.
 */
do_action( 'charitable_campaign_loop_before', $campaigns, $args, $view_args );

?>
<ol class="<?php echo $loop_class ?>">

<?php
while ( $campaigns->have_posts() ) :

	$campaigns->the_post();

	charitable_template( 'campaign-loop/campaign.php', $args );

endwhile;

wp_reset_postdata();
?>
</ol>
<?php

/**
 * Add something after the campaign loop.
 *
 * @since   1.5.0
 *
 * @param   WP_Query $campaigns The campaigns.
 * @param   array    $args      Loop args.
 */
do_action( 'charitable_campaign_loop_after', $campaigns, $args, $view_args );
do_action( 'charitable_campaign_loop_after', $campaigns, $args );
