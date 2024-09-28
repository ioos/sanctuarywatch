<?php
/**
 * Loads everything for WP Dark Mode to be functional
 *
 * @package WP Dark Mode
 * @since 5.0.0
 */

// Namespace.
namespace WP_Dark_Mode;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit( 1 );

if ( ! class_exists( __NAMESPACE__ . '\Boot' ) ) {

	/**
	 * Loads everything for WP Dark Mode to be functional
	 *
	 * @package WP Dark Mode
	 * @since 5.0.0
	 */
	class Boot {

		/**
		 * Singleton instance
		 *
		 * @since 5.0.0
		 * @var object
		 */
		private static $instance;

		/**
		 * Returns the singleton instance
		 *
		 * @since 5.0.0
		 * @return mixed
		 */
		public static function instance() {
			// Create an instance if not exists, returns only one instance throughout the request.
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Boot ) ) {
				self::$instance = new Boot();
			}

			return self::$instance;
		}

		/**
		 * Defines the constants
		 *
		 * @since 5.0.0
		 * @return void
		 */
		public function define_constants() {
			define( 'WP_DARK_MODE_PATH', plugin_dir_path( WP_DARK_MODE_FILE ) );
			define( 'WP_DARK_MODE_INCLUDES', WP_DARK_MODE_PATH . 'includes/' );
			define( 'WP_DARK_MODE_TEMPLATE', WP_DARK_MODE_PATH . 'templates/' );
			define( 'WP_DARK_MODE_MODULES', WP_DARK_MODE_INCLUDES . 'modules/' );

			define( 'WP_DARK_MODE_URL', plugin_dir_url( WP_DARK_MODE_FILE ) );
			define( 'WP_DARK_MODE_ASSETS', WP_DARK_MODE_URL . 'assets/' );
		}


		/**
		 * Is Dark Mode enabled
		 *
		 * @since 5.0.0
		 * @return bool
		 */
		public function is_dark_mode_enabled() {
			return wp_validate_boolean( get_option( 'wp_dark_mode_enabled', true ) );
		}
		/**
		 * Loads the required files
		 *
		 * @since 5.0.0
		 * @return void
		 */
		public function load_files() {
			$this->load_common_files();
			$this->load_modules();
			$this->load_admin_files();

			if ( $this->is_dark_mode_enabled() ) {
				$this->load_public_files();
			}
		}

		/**
		 * Loads the common files
		 *
		 * @since 5.0.0
		 * @return void
		 */
		public function load_common_files() {
			require_once WP_DARK_MODE_INCLUDES . '/classes/class-config.php';
			require_once WP_DARK_MODE_INCLUDES . '/traits/trait-options.php';
			require_once WP_DARK_MODE_INCLUDES . '/traits/trait-utility.php';
			require_once WP_DARK_MODE_INCLUDES . '/classes/class-base.php';
			require_once WP_DARK_MODE_INCLUDES . '/classes/class-dependency.php';
			require_once WP_DARK_MODE_INCLUDES . '/classes/class-switches.php';

			require_once WP_DARK_MODE_INCLUDES . '/models/class-visitor.php';

			// REST Init.
			add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
		}

		/**
		 * Loads the public files
		 *
		 * @since 5.0.0
		 * @return void
		 */
		public function load_public_files() {
			require_once WP_DARK_MODE_INCLUDES . '/classes/class-assets.php';
			require_once WP_DARK_MODE_INCLUDES . '/classes/class-triggers.php';
			require_once WP_DARK_MODE_INCLUDES . '/classes/class-extended.php';
			require_once WP_DARK_MODE_INCLUDES . '/compatibility/class-compatibility.php';

			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				require_once WP_DARK_MODE_INCLUDES . '/classes/class-ajax.php';
			}
		}

		/**
		 * Loads the admin files
		 *
		 * @since 5.0.0
		 * @return void
		 */
		public function load_admin_files() {

			if ( ! is_admin() ) {
				return;
			}

			require_once WP_DARK_MODE_INCLUDES . '/admin/class-admin-install.php';
			require_once WP_DARK_MODE_INCLUDES . '/admin/class-admin-menus.php';
			require_once WP_DARK_MODE_INCLUDES . '/admin/class-admin-assets.php';

			require_once WP_DARK_MODE_INCLUDES . '/admin/class-admin-switches.php';

			require_once WP_DARK_MODE_INCLUDES . '/wppool/class-plugin.php';
			require_once WP_DARK_MODE_INCLUDES . '/admin/class-admin-notices.php';
			require_once WP_DARK_MODE_INCLUDES . '/admin/class-recommended-plugins.php';

			// Upgrade.
			require_once WP_DARK_MODE_INCLUDES . '/admin/class-admin-upgrade.php';
		}

		/**
		 * Loads the modules
		 *
		 * @since 5.0.0
		 * @return void
		 */
		public function load_modules() {
			// Shortcode.
			require_once WP_DARK_MODE_INCLUDES . '/modules/class-shortcode.php';
			require_once WP_DARK_MODE_INCLUDES . '/modules/social-share/class-social-share.php';
			require_once WP_DARK_MODE_INCLUDES . '/modules/gutenberg/class-block.php';
			require_once WP_DARK_MODE_INCLUDES . '/modules/elementor/class-element.php';
		}

		/**
		 * Starts the plugin
		 *
		 * @since 5.0.0
		 * @return void
		 */
		public static function start() {
			$boot = self::instance();
			// Register hooks.
			$boot->define_constants();
			$boot->load_files();

			// Fires after the plugin is loaded.
			do_action( 'wp_dark_mode_loaded' );
		}

		/**
		 * Register REST API routes
		 *
		 * @since 5.0.0
		 */
		public function register_rest_routes() {
			require_once WP_DARK_MODE_INCLUDES . '/admin/class-admin-rest.php';
		}
	}

	// Start the plugin.
	Boot::start();
}
