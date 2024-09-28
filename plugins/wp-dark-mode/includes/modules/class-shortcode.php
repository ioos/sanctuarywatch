<?php
/**
 * Registers the shortcode for WP Dark Mode
 *
 * @package WP Dark Mode
 * @since 5.0.0
 */

// Namespace.
namespace WP_Dark_Mode;

// Exit if accessed directly.
defined('ABSPATH') || exit(1);

if ( ! class_exists(__NAMESPACE__ . 'Shortcode') ) {
	/**
	 * Registers the shortcode for WP Dark Mode
	 *
	 * @package WP Dark Mode
	 * @since 5.0.0
	 */
	class Shortcode extends Base {

		// Use Utility trait.
		use \WP_Dark_Mode\Traits\Utility;

		/**
		 * Registers the hook
		 *
		 * @since 5.0.0
		 */
		public function actions() {
			// Added for backwards compatibility.
			add_shortcode('wp-dark-mode', array( $this, 'render_shortcode' ), 100);
			// Added for backwards compatibility.
			add_shortcode('wp_dark_mode', array( $this, 'render_shortcode' ), 100);

			// Legacy.
			add_shortcode('wp-dark-mode-switch', array( $this, 'render_shortcode' ), 100);
			// Legacy.
			add_shortcode('wp_dark_mode_switch', array( $this, 'render_shortcode' ), 100);
		}

		/**
		 * Renders the shortcode
		 *
		 * @since 5.0.0
		 * @param array $atts Shortcode attributes.
		 * @return string
		 */
		public function render_shortcode( $atts ) {

			$defaults = array(
				'style' => 1,
				'size'  => 1,
				'classes' => '',
				'text_light' => '',
				'text_dark' => '',
				'icon_light' => '',
				'icon_dark' => '',
			);

			$atts = shortcode_atts($defaults, $atts);

			$style = isset($atts['style']) ? sanitize_text_field(wp_unslash($atts['style'])) : 1;
			$size  = isset($atts['size']) ? sanitize_text_field(wp_unslash($atts['size'])) : 1;
			$classes  = isset($atts['classes']) ? sanitize_text_field(wp_unslash($atts['classes'])) : '';
			$text_light  = isset($atts['text_light']) ? sanitize_text_field(wp_unslash($atts['text_light'])) : '';
			$text_dark  = isset($atts['text_dark']) ? sanitize_text_field(wp_unslash($atts['text_dark'])) : '';
			$icon_light  = isset($atts['icon_light']) ? sanitize_text_field(wp_unslash($atts['icon_light'])) : '';
			$icon_dark  = isset($atts['icon_dark']) ? sanitize_text_field(wp_unslash($atts['icon_dark'])) : '';

			// Reset the style if ultimate is not active.
			if ( ! $this->is_ultimate() && $style > 3 ) {
				$style = 1;
			}

			return wp_sprintf(
				'<div class="wp-dark-mode-switch %s" tabindex="0" 
				data-style="%s" data-size="%s" data-text-light="%s" data-text-dark="%s" data-icon-light="%s" data-icon-dark="%s"
				></div>',
				esc_attr($classes),
				esc_attr($style),
				esc_attr($size),
				esc_attr($text_light),
				esc_attr($text_dark),
				esc_attr($icon_light),
				esc_attr($icon_dark)
			);
		}
	}

	// Instantiate the class.
	Shortcode::init();
}
