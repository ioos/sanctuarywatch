<?php
/**
 * Extends for WP Dark Mode
 *
 * @package WP Dark Mode
 * @since 5.0.0
 */

// Namespace.
namespace WP_Dark_Mode;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit( 1 );

if ( ! class_exists( __NAMESPACE__ . 'Extended' ) ) {
	/**
	 * Extends for WP Dark Mode
	 *
	 * @package WP Dark Mode
	 * @since 5.0.0
	 */
	class Extended extends Base {

		// Use option trait.
		use \WP_Dark_Mode\Traits\Options;

		/**
		 * Filters
		 *
		 * @since 5.0.0
		 */
		public function filters() {
			// the content
			add_filter( 'wp_dark_mode_excluded_elements', array( $this, 'get_system_default_excluded_elements' ), 10 );
		}

		/**
		 * Get system default excluded elements
		 *
		 * @since 5.0.0
		 * @param string $elements
		 * @return string
		 */
		public function get_system_default_excluded_elements( $elements ) {

			$elements .= ( ! empty( $elements ) ? ', ' : ' ' ) . '#wpadminbar, .wp-dark-mode-switch, .elementor-button-content-wrapper';

			return $elements;
		}
	}

	// Instantiate the class.
	Extended::init();
}
