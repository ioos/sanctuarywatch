<?php
/**
 * Floating switch template for WP Dark Mode
 *
 * @package WP Dark Mode
 * @since 5.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit( 1 );


// Position.
$position_style = 'right: 10px; bottom: 10px;';

if ( 'left' === $args['position'] ) {
	$position_style = 'left: 10px; bottom: 10px;';
} elseif ( 'right' === $args['position'] ) {
	$position_style = 'right: 10px; bottom: 10px;';
} else {
	$position_style = wp_sprintf('bottom: %spx; %s: %spx;',
		esc_attr( $args['position_bottom_value'] ),
		esc_attr( $args['position_side'] ),
		esc_attr( $args['position_side_value'] )
	);
}

// Group alignment.
$aligned = 'right';
if ( 'left' === $args['position'] || 'left' === $args['position_side'] ) {
	$aligned = 'left';
} else {
	$aligned = 'right';
}

// Size.
$size = $args['size'];
if ( 'custom' === $size ) {
	$size = ( $args['size_custom'] / 100 );
}


$classes = '';

// Attention effect
if ( $args['enabled_attention_effect'] ) {
	$classes .= ' wp-dark-mode-switch-effect-' . esc_attr( strtolower( $args['attention_effect'] ) );
}

// Call to action.
$cta_html = '';
if ( $args['enabled_cta'] ) {
	$cta_html = wp_sprintf( '<div class="wp-dark-mode-switch-cta wp-dark-mode-ignore" style="background: %s; color: %s"> %s </div>',
		esc_attr( $args['cta_background'] ),
		esc_attr( $args['cta_color'] ),
		esc_html( $args['cta_text'] )
	);
}

// Alignment.
if ( 'left' === $aligned ) {
	$classes .= ' reverse';
}

// Display classes.
if ( ! $args['display']['desktop'] ) {
	$classes .= ' wp-dark-mode-hide-desktop';
}

if ( ! $args['display']['tablet'] ) {
	$classes .= ' wp-dark-mode-hide-tablet';
}

if ( ! $args['display']['mobile'] ) {
	$classes .= ' wp-dark-mode-hide-mobile';
}

// Custom text and icon.
$config = [
	'text_light' => '',
	'text_dark' => '',
	'icon_light' => '',
	'icon_dark' => '',
	'style' => $args['style'],
	'size' => $size,
];

if ( $args['enabled_custom_texts'] ) {
	$config['text_light'] = $args['text_light'];
	$config['text_dark'] = $args['text_dark'];
}

if ( $args['enabled_custom_icons'] ) {
	$config['icon_light'] = $args['icon_light'];
	$config['icon_dark'] = $args['icon_dark'];
}
?>

<div class="wp-dark-mode-floating-switch wp-dark-mode-ignore wp-dark-mode-animation wp-dark-mode-animation-bounce <?php echo esc_attr( $classes ); ?>" style="<?php echo esc_attr( $position_style ); ?>">
	<!-- call to action  -->
	<?php echo wp_kses_post( $cta_html ); ?>

	<?php
	$config_attrs = '';
	foreach ( $config as $key => $value ) {
		$config_attrs .= esc_attr( $key ) . '="' . esc_attr( $value ) . '" ';
	}

	$allowed_tags = array_merge(
		wp_kses_allowed_html( 'post' ),
		[
			'div' => [
				'class' => [],
				'tabindex' => [],
				'data-style' => [],
				'data-size' => [],
				'data-text-light' => [],
				'data-text-dark' => [],
				'data-icon-light' => [],
				'data-icon-dark' => [],
			],
		]
	);
	echo wp_kses( do_shortcode( '[wp-dark-mode-switch ' . $config_attrs . ']' ), $allowed_tags );
	?>
</div>