<?php
/**
 * Renders the custom styles added by Charitable.
 *
 * Override this template by copying it to yourtheme/charitable/custom-styles.css.php
 *
 * @author  Studio 164a
 * @package Charitable/Templates/CSS
 * @since   1.0.0
 * @version 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filter the default highlight colour.
 *
 * @since 1.0.0
 *
 * @param string $colour Default colour as a CSS-compatible string (hex, rgb, etc.)
 */
$default_colour = apply_filters(
	'charitable_default_highlight_colour',
	'#f89d35'
);

$highlight_colour = charitable_get_option( 'highlight_colour', $default_colour );

?>
<style id="charitable-highlight-colour-styles">
.campaign-raised .amount,
.campaign-figures .amount,
.donors-count,
.time-left,
.charitable-form-field a:not(.button),
.charitable-form-fields .charitable-fieldset a:not(.button),
.charitable-notice,
.charitable-notice .errors a {
<<<<<<< HEAD
	color: <?php echo $highlight_colour; ?>;
=======
	color: <?php echo $highlight_colour ?>;
>>>>>>> release/1.6.28
}

.campaign-progress-bar .bar,
.donate-button,
.charitable-donation-form .donation-amount.selected,
.charitable-donation-amount-form .donation-amount.selected {
<<<<<<< HEAD
	background-color: <?php echo $highlight_colour; ?>;
=======
	background-color: <?php echo $highlight_colour ?>;
>>>>>>> release/1.6.28
}

.charitable-donation-form .donation-amount.selected,
.charitable-donation-amount-form .donation-amount.selected,
.charitable-notice,
.charitable-drag-drop-images li:hover a.remove-image,
.supports-drag-drop .charitable-drag-drop-dropzone.drag-over {
<<<<<<< HEAD
	border-color: <?php echo $highlight_colour; ?>;
=======
	border-color: <?php echo $highlight_colour ?>;
>>>>>>> release/1.6.28
}

<?php do_action( 'charitable_custom_styles', $highlight_colour ); ?>
</style>
