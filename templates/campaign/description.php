<?php
/**
 * Displays the campaign description.
 *
 * Override this template by copying it to yourtheme/charitable/campaign/description.php
 *
 * @author  Studio 164a
 * @package Charitable/Templates/Campaign Page
 * @since   1.0.0
 * @version 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="campaign-description">
	<?php echo $view_args['campaign']->description; ?>
</div>
