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
 * Plugin Name:       Graphic Data Plugin
 * Plugin URI:        hhttps://github.com/ioos/sanctuarywatch_graphicdata
 * Description:       This plugin customizes a Wordpress installation for the requirements of the Graphic Data framework.
 * Version:           0.3.0-beta
 * Author:            Sanctuary Watch Team
 * Author URI:        https://www.noaa.gov
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       graphic_data_plugin
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
 */
define( 'WEBCR_VERSION', '0.2.0-beta' );

// Include the GitHub Updater class
require_once plugin_dir_path(__FILE__) . 'admin/class-webcr-github-updater.php';

// Initialize the GitHub Updater 
    new GitHub_Updater(
        __FILE__,
        'ioos', // the GitHub username
        'sanctuarywatch_graphicdata', // the repository name
        false, // This is a plugin, not a theme
        'plugins/graphic_data_plugin' // Subdirectory path in the repository
    );

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
 * @since    0.2.0-beta
 */
function run_webcr() {

	$plugin = new Webcr();
	$plugin->run();

}
run_webcr();