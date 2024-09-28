<?php
/**
 * Checks all the dependencies for WP Dark Mode
 *
 * @package WP Dark Mode
 * @since 5.0.0
 */

// Namespace.
namespace WP_Dark_Mode;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit( 1 );

if ( ! class_exists( __NAMESPACE__ . 'Dependency' ) ) {

	/**
	 * Checks all the dependencies for WP Dark Mode
	 *
	 * @package WP Dark Mode
	 * @since 5.0.0
	 */
	class Dependency extends Base {

		/**
		 * Minimum PHP version required
		 *
		 * @since 5.0.0
		 * @var string
		 */
		public $minimum_php_version = '7.0';

		/**
		 * Minimum WordPress version required
		 *
		 * @since 5.0.0
		 * @var string
		 */
		public $minimum_wp_version = '5.0';

		/**
		 * WP Dark Mode Free plugin file
		 *
		 * @since 5.0.0
		 * @var string
		 */
		public $wp_dark_mode_ultimate_file = 'wp-dark-mode-ultimate/plugin.php';

		/**
		 * Minimum WP Dark Mode Free version required
		 *
		 * @since 5.0.0
		 * @var string
		 */
		public $minimum_wp_dark_mode_ultimate_version = '4.0.0';


		/**
		 * Checks if PHP version is compatible
		 *
		 * @since 5.0.0
		 * @return bool
		 */
		public function is_php_compatible() {
			// Check if PHP version is compatible.
			return ! version_compare( PHP_VERSION, $this->minimum_php_version, '<' );
		}

		/**
		 * Checks if WordPress version is compatible
		 *
		 * @since 5.0.0
		 * @return bool
		 */
		public function is_wp_compatible() {
			// Check if WordPress version is compatible.
			return ! version_compare( get_bloginfo( 'version' ), $this->minimum_wp_version, '<' );
		}

		/**
		 * Checks if WP Dark Mode Free plugin is installed
		 *
		 * @since 5.0.0
		 * @return bool
		 */
		public function is_wp_dark_mode_ultimate_installed() {
			// Check if WP Dark Mode Free plugin is installed.
			return file_exists( WP_PLUGIN_DIR . '/' . $this->wp_dark_mode_ultimate_file );
		}

		/**
		 * Checks if WP Dark Mode Free plugin is active
		 *
		 * @since 5.0.0
		 * @return bool
		 */
		public function is_wp_dark_mode_ultimate_active() {
			// Check if WP Dark Mode Free plugin is active.
			return is_plugin_active( $this->wp_dark_mode_ultimate_file );
		}

		/**
		 * Checks if WP Dark Mode Free plugin version is compatible
		 *
		 * @since 5.0.0
		 * @return bool
		 */
		public function is_wp_dark_mode_ultimate_compatible() {
			// Check if WP Dark Mode Free plugin version is compatible.
			return ! version_compare( get_plugin_data( WP_PLUGIN_DIR . '/' . $this->wp_dark_mode_ultimate_file )['Version'], $this->minimum_wp_dark_mode_ultimate_version, '<' );
		}

		/**
		 * Checks if all the dependencies are met
		 *
		 * @since 5.0.0
		 * @return bool
		 */
		public function is_compatible() {
			return $this->is_php_compatible() && $this->is_wp_compatible();
		}

		/**
		 * Other plugin extensions
		 *
		 * @since 5.0.0
		 */

		 /**
		  * Is WooCommerce installed
		  *
		  * @since 5.0.0
		  * @return bool
		  */
		public function is_wc_installed() {
			return file_exists( WP_PLUGIN_DIR . '/woocommerce/woocommerce.php' );
		}

		/**
		 * Is WooCommerce active
		 *
		 * @since 5.0.0
		 * @return bool
		 */
		public function is_wc_active() {
			return class_exists( 'WooCommerce' ) && is_plugin_active( 'woocommerce/woocommerce.php' );
		}

		/**
		 * Is Elementor installed
		 *
		 * @since 5.0.0
		 * @return bool
		 */
		public function is_elementor_installed() {
			return file_exists( WP_PLUGIN_DIR . '/elementor/elementor.php' );
		}

		/**
		 * Is Elementor active
		 *
		 * @since 5.0.0
		 * @return bool
		 */
		public function is_elementor_active() {
			return class_exists( 'Elementor\Plugin' ) && is_plugin_active( 'elementor/elementor.php' );
		}
	}

}
