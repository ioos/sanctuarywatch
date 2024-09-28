<?php
/**
 * WP Dark Mode Admin Switches
 *
 * @package WP Dark Mode
 * @since 5.0.0
 */

// Namespace.
namespace WP_Dark_Mode\Admin;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit( 1 );

if ( ! class_exists( __NAMESPACE__ . 'Switches' ) ) {
	/**
	 * WP Dark Mode Admin Switches
	 *
	 * @package WP Dark Mode
	 * @since 5.0.0
	 */
	class Switches extends \WP_Dark_Mode\Base {

		// Use option trait.
		use \WP_Dark_Mode\Traits\Options;

		/**
		 * Adds action hooks
		 *
		 * @since 5.0.0
		 */
		public function actions() {
			add_action( 'admin_bar_menu', [ $this, 'render_admin_switcher_menu' ], 100 );
		}

		/**
		 * Renders admin switcher menu
		 *
		 * @since 5.0.0
		 * @param object $wp_admin_bar
		 */
		public function render_admin_switcher_menu( $wp_admin_bar ) {
			// Bailout, if not admin.
			if ( ! is_admin() ) {
				return;
			}

			// Bailout, if not admin bar.
			if ( ! is_admin_bar_showing() ) {
				return;
			}

			$enabled = $this->get_option( 'admin_enabled' );

			$activated = apply_filters( 'wp_dark_mode_admin_activated', false );

			$switcher = wp_sprintf('<div class="switch wp-dark-mode-ignore %s %s">
			<div class="_track wp-dark-mode-ignore">
			<span class="wp-dark-mode-ignore">Light</span>
			<span class="wp-dark-mode-ignore">Dark</span>
			<div class="_thumb wp-dark-mode-ignore"></div>
		</div></div>', ( ! $enabled ? 'hidden' : '' ), ( $activated ? 'active' : '' ));

			// Add admin bar menu.
			$wp_admin_bar->add_menu(
				[
					'id'    => 'wp-dark-mode-admin-bar-switch',
					'title' => $switcher,
					'href'  => '',
					'class' => 'test-class',
					'meta'  => [
						'class' => 'wp-dark-mode-admin-bar-switch ignore',
					],
				]
			);
		}
	}

	// Instantiate the class.
	Switches::init();
}
