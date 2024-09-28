<?php
/**
 * WP Dark Mode Theme Supported Themes
 *
 * @package WP_Dark_Mode
 */

// Namespace.
namespace WP_Dark_Mode\Compatibility;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * WP Dark Mode Theme Supported Themes
 */
if ( ! class_exists( 'Themes' ) ) {

	/**
	 * WP Dark Mode Theme Supported Themes
	 */
	class Themes extends \WP_Dark_Mode\Base {

		/**
		 * Add language attribute to html for the theme has custom class.
		 *
		 * @return void
		 */
		public function _add_attribute() {
			add_filter( 'language_attributes', array( $this, 'add_wp_dark_mode_attribute' ));
		}

		/**
		 * Be One Page
		 *
		 * @return void
		 */
		public function twentytwenty() {
			$this->_add_attribute();
		}

		/**
		 * OceanWP
		 *
		 * @return void
		 */
		public function oceanwp() {
			$this->_add_attribute();
		}

		/**
		 * Avada.
		 *
		 * @return void
		 */
		public function avada() {
			$this->_add_attribute();
		}
		/**
		 * Add WP Dark Mode Attribute
		 *
		 * @param string $attr
		 * @return string
		 */
		public function add_wp_dark_mode_attribute( $attr ) {
			$trigger = \WP_Dark_Mode\Triggers::get_instance();
			$wp_dark_mode_is_preactivated = apply_filters( 'wp_dark_mode_is_preactivated', $trigger->is_preactivated() );
			$attr .= $wp_dark_mode_is_preactivated ? ' data-wp-dark-mode' : '';
			return $attr;
		}
	}
}
