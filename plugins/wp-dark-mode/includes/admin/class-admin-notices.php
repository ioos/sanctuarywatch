<?php

/**
 * Handles all the notices for WP Dark Mode
 *
 * @package WP Dark Mode
 * @since 5.0.0
 */

// Namespace.
namespace WP_Dark_Mode\Admin;

// Exit if accessed directly.
defined('ABSPATH') || exit(1);

if ( ! class_exists(__NAMESPACE__ . 'Notices') ) {
	/**
	 * Handles all the notices for WP Dark Mode
	 *
	 * @package WP Dark Mode
	 * @since 5.0.0
	 */
	class Notices extends \WP_Dark_Mode\Base {


		// Use Utility trait.
		use \WP_Dark_Mode\Traits\Options;

		// Use Utility trait.
		use \WP_Dark_Mode\Traits\Utility;

		/**
		 * Register ajax actions
		 *
		 * @since 5.0.0
		 */
		public function actions() {
			$this->init_appsero();
			add_action( 'admin_footer', array( $this, 'add_upgrade_popup' ) );
			add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget' ) );
			// Admin notice.
			add_action( 'admin_notices', array( $this, 'admin_notices' ), 0 );
		}

		/**
		 * Initialize Appsero SDK
		 *
		 * @since 5.0.0
		 */
		public function init_appsero() {

			// Load Appsero SDK if not loaded.
			if ( ! class_exists( '\WP_Dark_Mode\Appsero\Client' ) ) {
				require_once WP_DARK_MODE_INCLUDES . '/appsero/Client.php';
			}

			$client = new \WP_Dark_Mode\Appsero\Client(
				'10d1a5ba-96f5-48e1-bc0e-38d39b9a2f85',
				'WP Dark Mode',
				WP_DARK_MODE_FILE
			);

			// Active insights.
			$client->insights()->init();

			if ( function_exists( 'wppool_plugin_init' ) ) {
				$wpdm_plugin = wppool_plugin_init( 'wp_dark_mode', plugin_dir_url( WP_DARK_MODE_FILE ) . '/includes/wppool/background-image.png' );
			}
		}


		/**
		 * Adds dashboard widget
		 *
		 * @since 5.0.0
		 */
		public function add_dashboard_widget() {

			$enabled_dashboard_widget = $this->get_option('analytics_enabled_dashboard_widget');
			if ( ! $enabled_dashboard_widget ) {
				return;
			}

			wp_add_dashboard_widget(
				'wp_dark_mode_dashboard_widget',
				__( 'WP Dark Mode', 'wp-dark-mode' ),
				array( $this, 'render_dashboard_widget' )
			);
		}

		/**
		 * Renders dashboard widget
		 *
		 * @since 5.0.0
		 */
		public function render_dashboard_widget() {
			$args = [
				'is_pro' => $this->is_ultimate(),
				'is_ultimate' => $this->is_ultimate(),
			];
			$this->render_template( 'admin/dashboard-widget', $args );
		}


		/**
		 * Adds upgrade popup
		 *
		 * @since 5.0.0
		 */
		public function add_upgrade_popup() {

			// Bail, if ultimate is active.
			if ( $this->is_ultimate() ) {
				return;
			}

			$this->render_template( 'admin/upgrade-popup' );
		}

		/**
		 * Admin notices
		 *
		 * @since 5.0.0
		 */
		public function admin_notices() {
			echo '<div id="wp-dark-mode-admin-notices"></div>';

			// Upgrade notice.
			$dependency = \WP_Dark_Mode\Dependency::get_instance();

			if ( $dependency->is_wp_dark_mode_ultimate_active() && ! $dependency->is_wp_dark_mode_ultimate_compatible() ) {
				echo '<div class="notice notice-error is-dismissible">';
				echo '<p>' . sprintf(
					wp_kses_post( '<strong>WP Dark Mode %s</strong> is not compatible with the installed version of <strong>WP Dark Mode Ultimate</strong>. Please update the <strong>WP Dark Mode Ultimate 4.0.0 or higher</strong> to function properly.' ),
					esc_html( WP_DARK_MODE_VERSION ),
					esc_html( $dependency->minimum_wp_dark_mode_ultimate_version )
				) . '</p>';
				echo '</div>';

				// Deactivate the ultimate plugin.
				deactivate_plugins( $dependency->wp_dark_mode_ultimate_file );
			}
		}
	}

	// Instantiate the class.
	Notices::init();
}
