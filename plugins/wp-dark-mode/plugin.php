<?php
/**
 * Plugin name: WP Dark Mode
 * Plugin URI: https://wppool.dev/wp-dark-mode
 * Description: WP Dark Mode automatically enables a stunning dark mode of your website based on user's operating system. Supports macOS, Windows, Android & iOS.
 * Version: 5.1.1
 * Author: WPPOOL
 * Author URI: https://wppool.dev
 * License: GPL3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: wp-dark-mode
 * Domain Path: /languages
 *
 * @package WP Dark Mode
 * @since 5.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit( 1 );

// Bail if WP_Dark_Mode defined.
if ( defined( 'WP_DARK_MODE_VERSION' ) ) {
	return;
}

// Check if WP_Dark_Mode defined.
if ( ! defined( 'WP_DARK_MODE_VERSION' ) ) {
	define( 'WP_DARK_MODE_FILE', __FILE__ );
	define( 'WP_DARK_MODE_VERSION', '5.1.1' );

	/**
	 * Loads the boot file.
	 *
	 * @since 5.0.0
	 */
	require_once __DIR__ . '/includes/class-boot.php';
}

/**
 * The main function responsible for returning the one true WP_Dark_Mode
 * instance to functions everywhere.
 * Manipulating any codebase will NOT be supported.
 */
