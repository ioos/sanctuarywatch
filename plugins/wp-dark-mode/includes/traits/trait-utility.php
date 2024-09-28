<?php
/**
 * Utility functions for WP Dark Mode
 *
 * @package WP Dark Mode
 * @since 5.0.0
 */

// Namespace.
namespace WP_Dark_Mode\Traits;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit( 1 );

if ( ! trait_exists( __NAMESPACE__ . 'Utility' ) ) {
	/**
	 * Utility functions for WP Dark Mode
	 *
	 * @package WP Dark Mode
	 * @since 5.0.0
	 */
	trait Utility {

		/**
		 * Returns if WP Dark Mode is enabled or not
		 *
		 * @since 5.0.0
		 * @return bool
		 */
		final public function is_dark_mode_enabled() {
			return wp_validate_boolean( get_option( 'wp_dark_mode_enabled', true ) );
		}

		/**
		 * Is pro version active
		 *
		 * @since 5.0.0
		 * @return bool
		 */
		final public function is_ultimate() {
			return apply_filters( 'wp_dark_mode_is_ultimate', false );
		}

		/**
		 * Renders the template with the given arguments
		 *
		 * @since 5.0.0
		 * @param string $template_name Template name.
		 * @param array  $args Arguments.
		 * @return void
		 */
		public function render_template( $template_name, $args = [] ) { // phpcs:ignore
			$template_path = WP_DARK_MODE_PATH . 'templates/' . $template_name . '.php';

			if ( file_exists( $template_path ) ) {
				include $template_path;
			}
		}
	}
}
