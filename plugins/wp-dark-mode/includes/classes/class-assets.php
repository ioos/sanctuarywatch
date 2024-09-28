<?php
/**
 * Enqueues script and styles to frontend for WP Dark Mode
 *
 * @package WP Dark Mode
 * @since 5.0.0
 */

// Namespace.
namespace WP_Dark_Mode;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit( 1 );

if ( ! class_exists( __NAMESPACE__ . 'Assets' ) ) {
	/**
	 * Enqueues script and styles to frontend for WP Dark Mode
	 *
	 * @package WP Dark Mode
	 * @since 5.0.0
	 */
	class Assets extends Base {

		// Use options trait.
		use \WP_Dark_Mode\Traits\Options;

		// Use utility trait.
		use \WP_Dark_Mode\Traits\Utility;

		/**
		 * Register hooks.
		 *
		 * @since 5.0.0
		 */
		public function actions() {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			// Modify script async.
			add_filter( 'script_loader_tag', array( $this, 'script_loader_tag' ), 10, 2 );

			// Stylesheet tag.
			add_filter( 'style_loader_tag', array( $this, 'style_loader_tag' ), 10, 2 );
		}

		/**
		 * Check if elementor editor mode.
		 *
		 * @since 5.0.0
		 * @return bool
		 */
		public function is_elementor_editor_mode() {
			if ( ! class_exists( '\Elementor\Plugin' ) ) {
				return false;
			}

			$is_editor_mode = \Elementor\Plugin::$instance->editor->is_edit_mode();

			return $is_editor_mode;
		}

		/**
		 * Enqueue scripts and styles.
		 *
		 * @since 5.0.0
		 */
		public function enqueue_scripts() {

			// Check if the plugin is enabled.
			if ( ! $this->get_option( 'frontend_enabled' ) ) {
				return;
			}

			// Enqueue styles.
			wp_enqueue_style( 'wp-dark-mode', WP_DARK_MODE_ASSETS . 'css/app.min.css', array(), WP_DARK_MODE_VERSION );

			$css = $this->get_inline_styles();
			wp_add_inline_style( 'wp-dark-mode', $css );

			// Load scripts in footer.
			$script_in_footer = apply_filters( 'wp_dark_mode_loads_scripts_in_footer', $this->get_option( 'performance_load_scripts_in_footer' ) );

			// Enqueue scripts.
			wp_enqueue_script( 'wp-dark-mode', WP_DARK_MODE_ASSETS . 'js/app.min.js', [ 'jquery' ], WP_DARK_MODE_VERSION, $script_in_footer );

			// Localize scripts.
			$localize_scripts = array(
				'nonce' => wp_create_nonce( 'wp_dark_mode_nonce' ),
				'is_pro' => $this->is_ultimate(),
				'version' => WP_DARK_MODE_VERSION,
				'is_excluded' => apply_filters( 'wp_dark_mode_is_excluded', false ),
				'excluded_elements' => $this->get_excluded_elements(),
				'options' => $this->get_options(),
				'analytics_enabled' => $this->get_option( 'analytics_enabled' ),
				'url' => [
					'ajax' => admin_url( 'admin-ajax.php' ),
					'home' => home_url(),
					'admin' => admin_url(),
					'assets' => WP_DARK_MODE_ASSETS,
				],
				'debug' => defined( 'WP_DEBUG' ) && WP_DEBUG,
				'additional' => [
					'is_elementor_editor' => class_exists( 'Elementor\Plugin' ) && \Elementor\Plugin::$instance->editor->is_edit_mode(),
				],
			);

			wp_localize_script( 'wp-dark-mode', 'wp_dark_mode_json', apply_filters( 'wp_dark_mode_json', $localize_scripts ) );

			// Inline scripts.
			$js = $this->get_inline_script();
			wp_add_inline_script( 'wp-dark-mode', $js );
		}

		/**
		 * Get excluded elements
		 *
		 * @since 5.0.0
		 * @return string
		 */
		public function get_excluded_elements() {
			$excluded = '';

			if ( true === $this->get_option('excludes_elements_all' ) ) {
				$exclude_all_except = $this->get_option( 'excludes_elements_except', '' );
				$excluded = ! empty( $exclude_all_except ) ? 'html *:not(' . $exclude_all_except . ')' : '*';
			} else {
				$excluded = $this->get_option( 'excludes_elements', '' );
			}

			return apply_filters( 'wp_dark_mode_excluded_elements', $excluded );
		}


		/**
		 * Returns inline styles for WP Dark Mode
		 *
		 * @since 5.0.0
		 * @return string
		 */
		public function get_inline_styles() {

			// Filter for body.
			$filter_brightness = $this->get_option( 'color_filter_brightness' );
			$filter_contrast = $this->get_option( 'color_filter_contrast' );
			$filter_grayscale = $this->get_option( 'color_filter_grayscale' );
			$filter_sepia = $this->get_option( 'color_filter_sepia' );

			$body_filter = wp_sprintf( 'brightness(%s%%) contrast(%s%%) grayscale(%s%%) sepia(%s%%)', $filter_brightness, $filter_contrast, $filter_grayscale, $filter_sepia );

			// Image and video filters.
			$img_brightness = $this->get_option( 'image_enabled_low_brightness' ) ? $this->get_option( 'image_brightness' ) : '100';
			$img_grayscale = $this->get_option( 'image_enabled_low_grayscale' ) ? $this->get_option( 'image_grayscale' ) : '0';
			$video_brightness = $this->get_option( 'video_enabled_low_brightness' ) ? $this->get_option( 'video_brightness' ) : '100';
			$video_grayscale = $this->get_option( 'video_enabled_low_grayscale' ) ? $this->get_option( 'video_grayscale' ) : '0';

			$typography_enabled = $this->get_option( 'typography_enabled' );
			$typography_font_size = $this->get_option( 'typography_font_size' );

			$font_size = 1;
			if ( $typography_enabled ) {
				$font_size = $typography_font_size;
				if ( 'custom' === $typography_font_size ) {
					$font_size = $this->get_option( 'typography_font_size_custom' ) / 100;
				}
			}

			$font_size = wp_sprintf( '%sem', $font_size );

			$css = wp_sprintf('html[data-wp-dark-mode-active], [data-wp-dark-mode-loading] {
				--wp-dark-mode-body-filter: %s;
				--wp-dark-mode-grayscale: %s%%;
	--wp-dark-mode-img-brightness: %s%%;
	--wp-dark-mode-img-grayscale: %s%%;
	--wp-dark-mode-video-brightness: %s%%;
	--wp-dark-mode-video-grayscale: %s%%;

	--wp-dark-mode-large-font-sized: %s;
}' . "\n", $body_filter, $filter_grayscale, $img_brightness, $img_grayscale, $video_brightness, $video_grayscale, $font_size);

			// Preset styles.
			$css .= $this->get_preset_styles();

			// Get Custom CSS.
			$css .= $this->get_custom_css();

			// Minify CSS.
			$css = $this->minify( $css );

			return apply_filters( 'wp_dark_mode_inline_styles', $css );
		}

		/**
		 * Returns preset styles for WP Dark Mode
		 *
		 * @since 5.0.0
		 * @return string
		 */
		public function get_preset_styles() {

			$color_preset_id = $this->get_option( 'color_preset_id' );

			if ( $color_preset_id < 1 ) {
				return sprintf(
					'.wp-dark-mode-active, [data-wp-dark-mode] {
						--wp-dark-mode-background-color: %s;
						--wp-dark-mode-text-color: %s; }',
					'#232323', '#f0f0f0'
				);

				return '';
			}

			$color_presets = $this->get_option( 'color_presets' );
			--$color_preset_id;

			if ( ! isset( $color_presets[ $color_preset_id ] ) ) {
				return '';
			}

			$preset = $color_presets[ $color_preset_id ];

			// Variables.
			$background_color = isset( $preset['bg'] ) && ! empty( $preset['bg'] ) ? $preset['bg'] : '';
			$secondary_color = isset( $preset['secondary_bg'] ) && ! empty( $preset['secondary_bg'] ) ? $preset['secondary_bg'] : '';

			$text_color = isset( $preset['text'] ) && ! empty( $preset['text'] ) ? $preset['text'] : '';
			$link_color = isset( $preset['link'] ) && ! empty( $preset['link'] ) ? $preset['link'] : '';
			$link_hover_color = isset( $preset['link_hover'] ) && ! empty( $preset['link_hover'] ) ? $preset['link_hover'] : '';

			$input_background_color = isset( $preset['input_bg'] ) && ! empty( $preset['input_bg'] ) ? $preset['input_bg'] : '';
			$input_text_color = isset( $preset['input_text'] ) && ! empty( $preset['input_text'] ) ? $preset['input_text'] : '';
			$input_placeholder_color = isset( $preset['input_placeholder'] ) && ! empty( $preset['input_placeholder'] ) ? $preset['input_placeholder'] : '';

			$button_text_color = isset( $preset['button_text'] ) && ! empty( $preset['button_text'] ) ? $preset['button_text'] : '';
			$button_hover_text_color = isset( $preset['button_hover_text'] ) && ! empty( $preset['button_hover_text'] ) ? $preset['button_hover_text'] : '';
			$button_background_color = isset( $preset['button_bg'] ) && ! empty( $preset['button_bg'] ) ? $preset['button_bg'] : '';
			$button_hover_background_color = isset( $preset['button_hover_bg'] ) && ! empty( $preset['button_hover_bg'] ) ? $preset['button_hover_bg'] : '';
			$button_border_color = isset( $preset['button_border'] ) && ! empty( $preset['button_border'] ) ? $preset['button_border'] : '';

			$enable_scrollbar = isset( $preset['enable_scrollbar'] ) && wp_validate_boolean( $preset['enable_scrollbar'] ) ? true : false;

			$track_color = $enable_scrollbar ? ( isset( $preset['scrollbar_track'] ) && ! empty( $preset['scrollbar_track'] ) ? $preset['scrollbar_track'] : '' ) : '';
			$thumb_color = $enable_scrollbar ? ( isset( $preset['scrollbar_thumb'] ) && ! empty( $preset['scrollbar_thumb'] ) ? $preset['scrollbar_thumb'] : '' ) : '';

			$styles = sprintf(
				'html[data-wp-dark-mode-active], [data-wp-dark-mode-loading] { 
	--wp-dark-mode-background-color: %s;
	--wp-dark-mode-secondary-background-color: %s;

	--wp-dark-mode-text-color: %s;
	--wp-dark-mode-link-color: %s;
	--wp-dark-mode-link-hover-color: %s;

	--wp-dark-mode-input-background-color: %s;
	--wp-dark-mode-input-text-color: %s;
	--wp-dark-mode-input-placeholder-color: %s;

	--wp-dark-mode-button-text-color: %s;
	--wp-dark-mode-button-hover-text-color: %s;
	--wp-dark-mode-button-background-color: %s;
	--wp-dark-mode-button-hover-background-color: %s;
	--wp-dark-mode-button-border-color: %s;

	--wp-dark-mode-scrollbar-track-color: %s;
	--wp-dark-mode-scrollbar-thumb-color: %s;
}',
				$background_color, $secondary_color, $text_color, $link_color, $link_hover_color, $input_background_color, $input_text_color, $input_placeholder_color, $button_text_color, $button_hover_text_color, $button_background_color, $button_hover_background_color, $button_border_color, $track_color, $thumb_color
			);

			// Has scrollbar.
			if ( $enable_scrollbar ) {
				$styles .= wp_sprintf(
					'html[data-wp-dark-mode-active], html[data-wp-dark-mode-loading] {
						body::-webkit-scrollbar-track {
							background: var(--wp-dark-mode-scrollbar-track-color) !important;
							background-color: var(--wp-dark-mode-scrollbar-track-color) !important;
						}
				
						body::-webkit-scrollbar-thumb {
							background: var(--wp-dark-mode-scrollbar-thumb-color) !important;
							background-color: var(--wp-dark-mode-scrollbar-thumb-color) !important;
						}

						scrollbar-color: var(--wp-dark-mode-scrollbar-thumb-color) var(--wp-dark-mode-scrollbar-track-color) !important;
			
						body::-webkit-scrollbar {
							width: .5rem;
						}
						   
						body::-webkit-scrollbar-track {
						box-shadow: inset 0 0 3px var(--wp-dark-mode-scrollbar-track-color);
						}
						
						body::-webkit-scrollbar-thumb {
						background-color: var(--wp-dark-mode-scrollbar-thumb-color);
						outline: 1px solid var(--wp-dark-mode-scrollbar-thumb-color);
						}
					}'
				);
			}

			// Minify CSS.
			$styles = $this->minify( $styles );

			// Return the styles.
			return apply_filters( 'wp_dark_mode_preset_styles', $styles );
		}

		/**
		 * Returns custom CSS for WP Dark Mode
		 *
		 * @since 5.0.0
		 * @return string
		 */
		public function get_custom_css() {

			$custom_css = $this->get_option( 'frontend_custom_css' );

			// return if empty
			if ( empty( $custom_css ) ) {
				return '';
			}

			return $this->add_selector( $custom_css, '[data-wp-dark-mode-active]' );
		}

		/**
		 * Adds parent selector to all selectors for a given CSS string, keeping the CSS properties in one line.
		 *
		 * @param string $css CSS string; Nested 1 level only.
		 * @param string $custom_selector Parent selector.
		 * @return string Fixed CSS string.
		 */
		public function add_selector( $css, $custom_selector ) {
			$css = $this->minify( $css );

			// Split the CSS string into an array of individual rules.
			$css_rules = preg_split('/}/', $css);
			// Initialize the new CSS string.
			$new_css = '';
			// Loop through each rule.
			foreach ( $css_rules as $rule ) {
				// Split the rule into the selector and properties.
				$parts = preg_split('/{/', $rule, 2);
				// If the rule has a selector and properties.
				if ( count($parts) === 2 ) {
					// Add the selector to the new CSS string, followed by a newline.
					$new_css .= $custom_selector . ' ' . trim($parts[0]) . " {\n";
					// Add the properties to the new CSS string, indented by one tab.
					$new_css .= "\t" . trim($parts[1]) . "\n";
					// Add the closing curly brace for the rule.
					$new_css .= "}\n";
				}
			}
			return $new_css;
		}

		/**
		 * Minifies CSS.
		 *
		 * @param string $css CSS string.
		 * @return string Minified CSS string.
		 */
		public function minify( $css ) {
			// Remove comments.
			$css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);

			// Remove space after colons.
			$css = str_replace(': ', ':', $css);

			// Remove whitespace.
			$css = str_replace([ "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ], '', $css);

			return $css;
		}

		/**
		 * Returns inline script for WP Dark Mode
		 *
		 * @since 5.0.0
		 * @return string
		 */
		public function get_inline_script() {
			$js = '';

			// Return the script.
			return $js;
		}

		/**
		 * Adds async attribute to script tag
		 *
		 * @since 5.0.0
		 * @param string $tag Script tag.
		 * @param string $handle Script handle.
		 * @return string
		 */
		public function script_loader_tag( $tag, $handle ) {

			// Check if the script is wp-dark-mode.
			if ( 'wp-dark-mode' === $handle ) {

				$execute_as = $this->get_option( 'performance_execute_as' );

				switch ( $execute_as ) {
					case 'async':
						$tag = str_replace( ' src', ' async="true" src', $tag );
						break;

					case 'defer':
						$tag = str_replace( ' src', ' defer src', $tag );
						break;

					case 'sync':
						// Do nothing.
						break;
				}

				// Add nowprocket attribute to script tag.
				if ( strpos( $tag, 'nowprocket' ) === false ) {
					$tag = str_replace( ' src', ' nowprocket src', $tag );
				}

				// LiteSpeed exclude.
				$tag = str_replace( ' src', ' data-no-minify="1" data-no-optimize="1" src', $tag );
			}

			return $tag;
		}

		/**
		 * Style loader tag
		 *
		 * @since 5.0.0
		 * @param string $html HTML tag.
		 * @param string $handle Style handle.
		 * @return string
		 */
		public function style_loader_tag( $html, $handle ) {

			// Check if the script is wp-dark-mode.
			if ( 'wp-dark-mode' === $handle ) {

				// Add nowprocket attribute to script tag.
				if ( strpos( $html, 'nowprocket' ) === false ) {
					$html = str_replace( ' rel', ' nowprocket rel', $html );
				}

				// LiteSpeed exclude.
				$html = str_replace( ' rel', ' data-no-minify="1" data-no-optimize="1" rel', $html );
			}

			return $html;
		}
	}

	// Instantiate the class.
	Assets::init();
}
