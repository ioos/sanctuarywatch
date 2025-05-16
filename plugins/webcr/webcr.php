<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.noaa.gov
 * @package           Webcr
 *
 * @wordpress-plugin
 * Plugin Name:       Sanctuary Watch Framework
 * Plugin URI:        hhttps://github.com/ioos/sanctuarywatch
 * Description:       This plugin customizes a Wordpress installation for the requirements of the Sanctuary Watch framework.
 * Version:           0.1.0-beta
 * Author:            Sanctuary Watch Team
 * Author URI:        https://www.noaa.gov
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       webcr
 * Requires Plugins:  svg-support
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Prevent direct access
if (!defined('ABSPATH')) exit;

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WEBCR_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-webcr-activator.php
 */
function activate_webcr() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-webcr-activator.php';
	Webcr_Activator::activate();
}

// Include the GitHub Updater class
require_once plugin_dir_path(__FILE__) . 'admin/class-webcr-github-updater.php';

// Initialize the updater 
//if (!WP_DEBUG) {
    new GitHub_Updater(
        __FILE__,
        'ioos', // Your GitHub username
        'sanctuarywatch', // Your repository name
        false, // This is a plugin, not a theme
        'plugins/webcr' // Subdirectory path in the repository
    );
//}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-webcr-deactivator.php
 */
function deactivate_webcr() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-webcr-deactivator.php';
	Webcr_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_webcr' );
register_deactivation_hook( __FILE__, 'deactivate_webcr' );

/**
 * The core plugin class that is used to define
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-webcr.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_webcr() {

	$plugin = new Webcr();
	$plugin->run();

}
run_webcr();