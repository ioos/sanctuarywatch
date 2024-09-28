<?php
/**
 * Manages all the options for WP Dark Mode
 *
 * @package WP Dark Mode
 * @since 5.0.0
 */

// Namespace.
namespace WP_Dark_Mode\Traits;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit( 1 );

if ( ! trait_exists( __NAMESPACE__ . 'Options' ) ) {
	/**
	 * Manages all the options for WP Dark Mode
	 *
	 * @package WP Dark Mode
	 * @since 5.0.0
	 */
	trait Options {

		/**
		 * Returns the default options for WP Dark Mode
		 *
		 * @since 5.0.0
		 * @var array
		 */
		final public function get_default_options() {
			return \WP_Dark_Mode\Config::get_default_options();
		}

		/**
		 * Formatted options
		 *
		 * @since 5.0.0
		 * @return array
		 */
		final public function get_default_formatted_options() {

			$defaults = array();

			foreach ( $this->get_default_options() as $item => $subitems ) {
				foreach ( $subitems as $subitem => $value ) {
					$defaults[ $item . '_' . $subitem ] = $value['default'];
				}
			}

			return $defaults;
		}



		/**
		 * Returns all the options for WP Dark Mode
		 *
		 * @since 5.0.0
		 * @return array
		 */
		final public function get_options() {

			// Return global options.
			global $wp_dark_mode_options;

			if ( isset( $wp_dark_mode_options ) && $wp_dark_mode_options && is_array( $wp_dark_mode_options ) && ! empty( $wp_dark_mode_options ) ) {
				return $wp_dark_mode_options;
			}

			// Build options array.
			$wp_dark_mode_options = array();

			foreach ( $this->get_default_options() as $option_group_name => $option_group ) {
				foreach ( $option_group as $option_name => $option ) {
					$name  = $option_group_name . '_' . $option_name;

					$value = get_option( 'wp_dark_mode_' . $name, $option['default'] );

					$type = isset( $option['type'] ) ? $option['type'] : 'mixed';

					switch ( $type ) {
						case 'boolean':
							$wp_dark_mode_options[ $name ] = wp_validate_boolean( $value );
							break;

						case 'number':
							$wp_dark_mode_options[ $name ] = intval( $value );
							break;

						case 'array':
							if ( ! is_array( $value ) ) {
								$value = array();
							}

							$wp_dark_mode_options[ $name ] = (array) $value;
							break;

						case 'string':
							if ( ! is_string( $value ) ) {
								$value = '';
							}

							$wp_dark_mode_options[ $name ] = (string) $value;
							break;

						default:
							$wp_dark_mode_options[ $name ] = $value;
							break;
					}
				}
			}

			return $wp_dark_mode_options;
		}

		/**
		 * Returns the value of a specific option from the database
		 *
		 * @since 5.0.0
		 * @param string $name Option name.
		 * @param mixed  $default Default value.
		 * @return mixed
		 */
		final public function get_option( $name, $default = null ) {
			$options = $this->get_options();

			if ( array_key_exists( $name, $options ) ) {
				return $options[ $name ];
			}

			$option = get_option( 'wp_dark_mode_' . $name, $default );
			return $option;
		}

		/**
		 * Sets the value of a specific option in the database
		 *
		 * @since 5.0.0
		 * @param string $option Option name.
		 * @param mixed  $value Option value.
		 * @return bool
		 */
		final public function set_option( $option, $value = null ) {
			$set = update_option( 'wp_dark_mode_' . $option, $value );
			return $set;
		}

		/**
		 * Deletes a specific option from the database
		 *
		 * @since 5.0.0
		 * @param string $option Option name.
		 * @return bool
		 */
		final public function delete_option( $option ) {
			$delete = delete_option( 'wp_dark_mode_' . $option );
			return $delete;
		}

		/**
		 * Get transient
		 *
		 * @since 5.0.0
		 * @param string $name Transient name.
		 * @return mixed
		 */
		final public function get_transient( $name, $default = null ) {
			$value = get_transient( 'wp_dark_mode_' . $name );

			if ( is_null( $value ) ) {
				return $default;
			}

			return $value;
		}

		/**
		 * Set transient
		 *
		 * @since 5.0.0
		 * @param string $name Transient name.
		 * @param mixed  $value Transient value.
		 * @param int    $expiration Expiration time.
		 * @return bool
		 */
		final public function set_transient( $name, $value, $expiration = 0 ) {
			return set_transient( 'wp_dark_mode_' . $name, $value, $expiration );
		}

		/**
		 * Delete transient
		 *
		 * @since 5.0.0
		 * @param string $name Transient name.
		 * @return bool
		 */
		final public function delete_transient( $name ) {
			return delete_transient( 'wp_dark_mode_' . $name );
		}

		/**
		 * Get value
		 * Option or transient
		 *
		 * @since 5.0.0
		 * @param string $name Name.
		 * @param mixed  $default Default value.
		 * @return mixed
		 */
		final public function get_value( $name, $default = null ) {
			$value = $this->get_transient( $name );

			if ( $value ) {
				return $value;
			}

			return $this->get_option( $name, $default );
			;
		}



		/**
		 * Set default options for WP Dark Mode
		 *
		 * @since 5.0.0
		 * @return bool
		 */
		final public function set_default_options() {
			$defaults = $this->get_default_formatted_options();

			foreach ( $defaults as $key => $value ) {
				$this->set_option( $key, $value );
			}

			// After reset.
			do_action( 'wp_dark_mode_after_reset' );

			return true;
		}
	}

}
