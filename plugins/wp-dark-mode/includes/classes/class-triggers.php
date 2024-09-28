<?php
/**
 * Triggers for WP Dark Mode
 *
 * @package WP Dark Mode
 * @since 5.0.0
 */

// Namespace.
namespace WP_Dark_Mode;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit( 1 );

if ( ! class_exists( __NAMESPACE__ . 'Triggers' ) ) {
	/**
	 * Triggers for WP Dark Mode
	 *
	 * @package WP Dark Mode
	 * @since 5.0.0
	 */
	class Triggers extends Base {

		// Use option trait.
		use \WP_Dark_Mode\Traits\Options;

		// Use utility trait.
		use \WP_Dark_Mode\Traits\Utility;

		/**
		 * Filters
		 *
		 * @since 5.0.0
		 */
		public function filters() {
			// Add class to html.
			add_filter( 'language_attributes', array( $this, 'language_attributes' ) );
		}

		/**
		 * Is dark mode enabled
		 *
		 * @since 5.0.0
		 * @return bool
		 */
		public function is_preactivated() {

			// Bail, if current page is excluded.
			$excluded = apply_filters( 'wp_dark_mode_is_excluded', false );
			if ( $excluded ) {
				return false;
			}

			$enabled_url_param = $this->get_option( 'accessibility_enabled_url_param' );

			if ( true === $enabled_url_param ) {

				// Obtain the current page URL
				$protocol = (
					( ! empty($_SERVER['HTTPS']) && 'off' !== sanitize_text_field(wp_unslash($_SERVER['HTTPS'])) ) ||
					( 443 === ( isset($_SERVER['SERVER_PORT']) ? sanitize_text_field(wp_unslash($_SERVER['SERVER_PORT'])) : 0 ) )
				) ? 'https://' : 'http://';

				$current_url = $protocol . isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) . ( isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '' ) : '';

				// Parse the URL to get the query string
				$query_string = wp_parse_url($current_url, PHP_URL_QUERY);

				// Parse the query string into an associative array
				wp_parse_str($query_string, $query_params);

				// Check if darkmode is set.
				if ( isset( $query_params['darkmode'] ) ) {
					return true;
				}

				// Check if lightmode is set.
				if ( isset( $query_params['lightmode'] ) ) {
					return false;
				}
			}

			// Check if user has selected dark mode.
			$remember_choice = true || $this->get_option( 'frontend_remember_choice' );

			if ( $remember_choice ) {
				// Evaluate user choice.
				$user_choice = isset( $_COOKIE['wp-dark-mode-choice'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['wp-dark-mode-choice'] ) ) : null;

				if ( isset( $user_choice ) && $user_choice ) {
					return 'dark' === $user_choice;
				}
			}

			// Check automation modes.
			$mode = $this->get_option( 'frontend_mode' );

			switch ( $mode ) {

				// Light-mode.
				case 'default_light':
					return false;

					break;

				// Time based.
				case 'time':
					// Checks if Dark Mode is enabled based on time.
					$is_time_based_dark_mode = apply_filters( 'wp_dark_mode_is_time_based_dark_mode', false );

					return $is_time_based_dark_mode;

					break;

				// Sunset based.
				case 'sunset':
					// Checks if Dark Mode is enabled based on sunset.
					$is_sunset_based_dark_mode = apply_filters( 'wp_dark_mode_is_sunset_based_dark_mode', false );

					return $is_sunset_based_dark_mode;

					break;

				// Device based.
				case 'device':
					$device_mode = isset( $_COOKIE['wp-dark-mode-device'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['wp-dark-mode-device'] ) ) : null;

					if ( isset( $device_mode ) && $device_mode ) {
						return 'dark' === $device_mode;
					}

					break;

				// Default.
				default:
					if ( $this->get_option( 'frontend_enabled' ) ) {
						return true;
					}
					break;
			}

			return false;
		}

		/**
		 * Adds body class for WP Dark Mode
		 *
		 * @since 5.0.0
		 * @param mixed $output Body classes.
		 * @return mixed
		 */
		public function language_attributes( $output ) {

			// Bail, if admin
			if ( is_admin() ) {
				return $output;
			}

			// Bail, if server-side cache is disabled.
			$exclude_cache = wp_validate_boolean( $this->get_option( 'performance_exclude_cache' ) );
			if ( $exclude_cache ) {
				return $output;
			}

			$attr = '';

			$wp_dark_mode_is_preactivated = apply_filters( 'wp_dark_mode_is_preactivated', $this->is_preactivated() );

			// Add attribute.
			$attr .= $wp_dark_mode_is_preactivated ? 'data-wp-dark-mode-active="true" data-wp-dark-mode-loading="true"' : '';

			// Site animation.
			$animation_enabled = $this->get_option( 'animation_enabled' );
			if ( $animation_enabled ) {
				$animation_name = sanitize_title( $this->get_option( 'animation_name' ) );
				$attr .= ' ' . wp_sprintf('data-wp-dark-mode-animation="%s"', $animation_name );
			}

			// Preset.
			$preset = sanitize_title( $this->get_option( 'color_preset_id' ) );

			// Add attribute.
			$attr .= ' ' . wp_sprintf('data-wp-dark-mode-preset="%s"', 0 === $preset ? 'auto' : esc_attr( $preset ) );

			// Output.
			$output .= ' ' . $attr;

			return $output;
		}
	}

	// Instantiate the class.
	Triggers::init();
}
