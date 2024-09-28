<?php
/**
 * Handles all the installation related tasks for WP Dark Mode
 *
 * @package WP Dark Mode
 * @since 5.0.0
 */

// Namespace.
namespace WP_Dark_Mode\Admin;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit( 1 );

if ( ! class_exists( __NAMESPACE__ . 'Install' ) ) {
	/**
	 * Handles all the installation related tasks for WP Dark Mode
	 *
	 * @package WP Dark Mode
	 * @since 5.0.0
	 */
	class Install extends \WP_Dark_Mode\Base {

		// Use options trait.
		use \WP_Dark_Mode\Traits\Options;

		// Use utility trait.
		use \WP_Dark_Mode\Traits\Utility;

		/**
		 * Get dependency
		 *
		 * @since 5.0.0
		 * @return object
		 */
		public function get_dependency() {
			return \WP_Dark_Mode\Dependency::get_instance();
		}

		/**
		 * Register actions
		 *
		 * @since 5.0.0
		 * @return void
		 */
		public function actions() {
			// Register activation hook.
			register_activation_hook( WP_DARK_MODE_FILE, array( $this, 'activate' ) );

			// Redirect to get started page on activation.
			add_action( 'admin_init', array( $this, 'redirect_to_get_started' ) );
		}

		/**
		 * Register filters
		 *
		 * @since 5.0.0
		 * @return void
		 */
		public function filters() {

			// Plugin action links.
			add_filter( 'plugin_action_links_' . plugin_basename( WP_DARK_MODE_FILE ), array( $this, 'plugin_action_links' ) );
		}

		/**
		 * Runs on plugin activation
		 *
		 * @since 5.0.0
		 * @return void
		 */
		public function activate() {
			// Check if plugin is compatible with current versions.
			if ( ! $this->check_compatibilities() ) {

				// Deactivate the plugin.
				deactivate_plugins( WP_DARK_MODE_FILE );

				return;
			}

			$this->set_option( 'version', WP_DARK_MODE_VERSION );

			// Set default notices
			$this->set_notices();

			// Remove activation transient.
			delete_option( 'wp_dark_mode_activated' );
		}

		/**
		 * Set default notices
		 *
		 * @since 5.0.0
		 * @return void
		 */
		public function set_notices() {

			$notices = [
				[
					'old' => 'review_notice_interval',
					'new' => 'rating_notice',
					'days' => 7,
				],
				[
					'old' => 'affiliate_notice_interval',
					'new' => 'affiliate_notice',
					'days' => 14,
				],
				[
					'old' => 'upgrade_notice',
					'new' => 'upgrade_notice',
					'days' => 10,
				],
			];

			foreach ( $notices as $notice ) {

				if ( $this->get_option( $notice['new'] ) || $this->get_transient( $notice['new'] ) ) {
					continue;
				}

				$old_value = $this->get_option( $notice['old'] );

				if ( ! is_null( $old_value ) ) {
					$this->set_option( $notice['new'], 'off' === $old_value ? 'hide' : 'show' );
				} else {
					$this->set_transient( $notice['new'], 'hide', DAY_IN_SECONDS * $notice['days'] );
				}
			}
		}

		/**
		 * Checks requirements for plugin activation
		 *
		 * @since 5.0.0
		 * @return bool
		 */
		public function check_compatibilities() {

			$dependency = $this->get_dependency();

			// Checks PHP compatibility.
			if ( ! $dependency->is_php_compatible() ) {

				// Throw an error in the WordPress admin console.
				$this->print_error(
					sprintf(
						/* translators: %s: PHP version */
						'WP Dark Mode %1$s requires PHP version %s or greater. Your current PHP version is %s.',
						esc_html( $dependency->minimum_php_version ),
						PHP_VERSION
					)
				);

				return false;
			}

			// Checks WordPress compatibility.
			if ( ! $dependency->is_wp_compatible() ) {

				// Throw an error in the WordPress admin console.
				$this->print_error(
					sprintf(
						/* translators: %s: WordPress version */
						'WP Dark Mode %1$s requires WordPress version %s or greater. Your current WordPress version is %s.',
						esc_html( $dependency->minimum_wp_version ),
						get_bloginfo( 'version' )
					)
				);

				return false;
			}

			// Check if ultimate activated but not compatible.
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

			return true;
		}

		/**
		 * Prints an error notice
		 *
		 * @since 5.0.0
		 * @param string $message Error message.
		 * @return void
		 */
		public function print_error( $message ) {
			// Print notice.
			printf( '<div class="notice notice-error"><p>%s</p></div>', esc_html( $message ) );
		}

		/**
		 * Adds plugin action links
		 *
		 * @since 5.0.0
		 * @param array $links Plugin action links.
		 * @return array
		 */
		public function plugin_action_links( $links ) {
			// check if pro version is installed.
			if ( ! $this->is_ultimate() ) {
				// Add 'Upgrade' link.
				array_unshift(
					$links,
					sprintf(
						'<a href="%s" target="_blank" style="%s">%s</a>',
						'https://wpdarkmode.com/pricing/?utm_source=plugin&utm_medium=link&utm_campaign=wp-dark-mode',
						'color: #25b363;',
						__( 'Upgrade now', 'wp-dark-mode' )
					)
				);
			}

			// Add settings link to first.
			array_unshift(
				$links,
				sprintf(
					'<a href="%s">%s</a>',
					admin_url( 'admin.php?page=wp-dark-mode' ),
					__( 'Settings', 'wp-dark-mode' )
				)
			);

			return $links;
		}


		/**
		 * Redirects to get started on activation
		 *
		 * @since 5.0.0
		 * @return void
		 */
		public function redirect_to_get_started() {
			// Check if it is first time activation.
			if ( ! get_option( 'wp_dark_mode_activated' ) ) {
				// Set the option.
				update_option( 'wp_dark_mode_activated', true );

				// Redirect to settings page.
				wp_safe_redirect( admin_url( 'admin.php?page=wp-dark-mode-get-started' ) );
				exit;
			}
		}
	}

	// Instantiate the class.
	Install::init();
}
