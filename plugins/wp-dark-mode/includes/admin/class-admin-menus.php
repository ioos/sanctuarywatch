<?php
/**
 * Admin Menus for WP Dark Mode
 *
 * @package WP Dark Mode
 * @since 5.0.0
 */

// Namespace.
namespace WP_Dark_Mode\Admin;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit( 1 );

if ( ! class_exists( __NAMESPACE__ . 'Menus' ) ) {
	/**
	 * Admin Menus for WP Dark Mode
	 *
	 * @package WP Dark Mode
	 * @since 5.0.0
	 */
	class Menus extends \WP_Dark_Mode\Base {

		// Use utility trait.
		use \WP_Dark_Mode\Traits\Utility;

		/**
		 * Adds action hooks
		 *
		 * @since 5.0.0
		 */
		public function actions() {
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			// footer scripts.
			add_action( 'admin_head', array( $this, 'header_scripts' ) );
		}

		/**
		 * Adds admin menu
		 *
		 * @since 5.0.0
		 */
		public function admin_menu() {
			add_menu_page(
				__( 'WP Dark Mode Settings', 'wp-dark-mode' ),
				__( 'WP Dark Mode', 'wp-dark-mode' ),
				'manage_options',
				'wp-dark-mode',
				array( $this, 'render_settings_page' ),
				'dashicons-dashboard',
				45
			);

			// add sub menus.

			// Settings.
			add_submenu_page(
				'wp-dark-mode',
				__( 'Settings', 'wp-dark-mode' ),
				__( 'Settings', 'wp-dark-mode' ),
				'manage_options',
				'wp-dark-mode',
				array( $this, 'render_settings_page' ),
				10
			);

			// Fall back.
			// Settings.
			add_submenu_page(
				'wp-dark-mode-hidden',
				__( 'Settings', 'wp-dark-mode' ),
				__( 'Settings', 'wp-dark-mode' ),
				'manage_options',
				'wp-dark-mode-settings',
				array( $this, 'redirect_to_new_settings_page' ),
				10
			);

			// Get started.
			add_submenu_page(
				'wp-dark-mode',
				__( 'Get Started', 'wp-dark-mode' ),
				__( 'Get Started', 'wp-dark-mode' ),
				'manage_options',
				'wp-dark-mode-get-started',
				array( $this, 'render_get_started_page' ),
				19
			);

			// Social share.
			// add_submenu_page(
			//  'wp-dark-mode',
			//  __( 'Social Share', 'wp-dark-mode' ),
			//  __( 'Social Share', 'wp-dark-mode' ),
			//  'manage_options',
			//  'wp-dark-mode#/social-share',
			//  array( $this, 'render_settings_page' ),
			//  15
			// );

			$hide_wp_dark_mode_recommended_plugins = get_option( 'hide_wp_dark_mode_recommended_plugins', false );

			if ( ! $hide_wp_dark_mode_recommended_plugins ) {
				// Recommended plugins.
				add_submenu_page(
					'wp-dark-mode',
					__( 'Recommended Plugins', 'wp-dark-mode' ),
					__( 'Recommended <br /> Plugins', 'wp-dark-mode' ),
					'manage_options',
					'wp-dark-mode-recommended-plugins',
					array( $this, 'render_recommended_plugins_page' ),
					25
				);
			}

			// Upgrade now.
			if ( ! $this->is_ultimate() ) {
				add_submenu_page(
					'wp-dark-mode',
					__( 'Upgrade Now', 'wp-dark-mode' ),
					wp_sprintf(
						'<span class="wp-dark-mode-upgrade-now">%s <svg width="14" height="11" viewBox="0 0 15 12" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M5.2 0H2.8L0 3.6H4L5.2 0Z" fill="#7CFFCA"></path>
					<path d="M14.4 3.6L11.6 0H9.19995L10.4 3.6H14.4Z" fill="#219D6B"></path>
					<path d="M10.4 3.6H14.4L7.20001 12L10.4 3.6Z" fill="#24A973"></path>
					<path d="M4 3.6H0L7.2 12L4 3.6ZM5.2 0L4 3.6H10.4L9.2 0H5.2Z" fill="#3BF5A9"></path>
					<path d="M7.2 12L4 3.6H10.4L7.2 12Z" fill="#2BD08D"></path>
					</svg></span>',
						__( 'Upgrade Now', 'wp-dark-mode' )
					),
					'manage_options',
					'https://go.wppool.dev/LaSV',
					'',
					40
				);
			}
		}

		/**
		 * Renders settings page
		 *
		 * @since 5.0.0
		 */
		public function render_settings_page() {
			echo '<div id="wp-dark-mode-admin" class="wp-dark-mode-admin" />';
		}

		/**
		 * Redirects to new settings page
		 *
		 * @since 5.0.0
		 */
		public function redirect_to_new_settings_page() {
			echo '<script>window.location.href = "' . esc_url( admin_url( 'admin.php?page=wp-dark-mode' ) ) . '";</script>';
		}

		/**
		 * Renders get started page
		 *
		 * @since 5.0.0
		 */
		public function render_get_started_page() {
			echo '<div id="wp-dark-mode-get-started" class="wp-dark-mode-admin" />';
		}

		/**
		 * Renders recommended plugins page
		 *
		 * @since 5.0.0
		 */
		public function render_recommended_plugins_page() {
			do_action( 'wp_dark_mode_recommended_plugins_page' );
		}

		/**
		 * Adds header scripts
		 *
		 * @since 5.0.0
		 */
		public function header_scripts() {
			?>
			<style>
				.wp-dark-mode-upgrade-now {
					color: #34D399;
					text-transform: uppercase;
					font-size: 13px;
					line-height: 20px;
					font-weight: 600;
					display: flex;
					align-items: center;
					gap: 6px;
					font-family -apple-system, "system-ui", "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif
				}
				.wp-dark-mode-upgrade-now svg {
					transform: translateY(1px);
				}

				li.toplevel_page_wp-dark-mode .dashicons-before {
					position: relative;
				}
				li.toplevel_page_wp-dark-mode .dashicons-before:before {
					position: absolute;
					content: '';
					background: url('<?php echo esc_url( WP_DARK_MODE_ASSETS ) . 'images/icon.svg'; ?>') no-repeat center center !important;
					background-size: 85% !important;
					margin: auto;
					top: 50%;
					left: 50%;
					transform: translate(-50%, -50%);
				}
			</style>
			<?php
		}
	}

	// Instantiate the class.
	Menus::init();
}
