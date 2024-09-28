<?php
/**
 * Handles the elementor widget for wp dark mode
 *
 * @since 3.0.6
 * @package WP_DARK_MODE
 */

// Namespace.
namespace WP_Dark_Mode\Module\Elementor;

// if direct access than exit the file.
defined( 'ABSPATH' ) || exit();

/**
 * This file represents plugin elementor widget.
 *
 * @since 3.0.6
 * @package WP_DARK_MODE
 */
if ( ! class_exists( __NAMESPACE__ . 'DarkModeWidget' ) ) {
	/**
	 * Handles the elementor widget for wp dark mode
	 *
	 * @version 1.0.0
	 */
	class DarkModeWidget extends \Elementor\Widget_Base {

		// Dark Mode Utility.
		use \WP_Dark_Mode\Traits\Utility;

		/**
		 * Get dark mode switch name
		 *
		 * @return string
		 * @version 1.0.0
		 */
		public function get_name() {
			return 'wp_dark_mode_switch';
		}

		/**
		 * Get switch title
		 *
		 * @return string
		 * @version 1.0.0
		 */
		public function get_title() {
			return __( 'Dark Mode Switch', 'wp-dark-mode' );
		}

		/**
		 * Get widgets icon
		 *
		 * @return string
		 * @version 1.0.0
		 */
		public function get_icon() {
			return 'eicon-adjust';
		}

		/**
		 * Gets categories type
		 *
		 * @return array
		 * @version 1.0.0
		 */
		public function get_categories() {
			return [ 'basic' ];
		}

		/**
		 * Get dark mode switcher keywords
		 *
		 * @return array
		 * @version 1.0.0
		 */
		public function get_keywords() {
			return [
				'dark',
				'light',
				'night',
				'wppool',
				'wp',
				'switch',
				'moon',
				'sun',
				'dim',
				'dakr',
			];
		}

		/**
		 * Register wp dark mode widgets switcher controls
		 *
		 * @return void
		 * @version 1.0.0
		 */
		public function register_controls() { // phpcs:ignore
			$this->start_controls_section(
				'_section_alignment',
				[
					'label' => __( 'Dark Mode Switch', 'wp-dark-mode' ),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				]
			);

			// Switch style.
			$this->add_control(
				'_switch_style_heading',
				[
					'label' => __( 'Switch Style', 'wp-dark-mode' ),
					'type'  => \Elementor\Controls_Manager::HEADING,
				]
			);

			$this->add_control(
				'style', [
					'type'        => 'wp_dark_mode_switch',
					'options' => array_merge([ 1, 2, 3, 20, 21 ], range(4, 19)),
					'description' => 'Select the Dark Mode Switch Style',
					'default'     => 1,
					'id'          => 'wp-dark-mode-switch',
					'classes' => 'wp-dark-mode-switch-style-elementor',
					'assets_url' => WP_DARK_MODE_ASSETS . '/images/switches/',
					'is_ultimate' => $this->is_ultimate(),
				]
			);

			$this->add_control(
				'size', [
					'label' => __( 'Switch Size', 'wp-dark-mode' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'options' => [
						'0.6' => __( 'XS', 'wp-dark-mode' ),
						'0.8' => __( 'SM', 'wp-dark-mode' ),
						'1.0' => __( 'MD', 'wp-dark-mode' ),
						'1.2' => __( 'XL', 'wp-dark-mode' ),
						'1.4' => __( '2XL', 'wp-dark-mode' ),
						'1.6' => __( '3XL', 'wp-dark-mode' ),
					],
					'default'     => '1.0',
				]
			);

			$this->add_responsive_control(
				'align',
				[
					'label'     => __( 'Alignment', 'wp-dark-mode' ),
					'type'      => \Elementor\Controls_Manager::CHOOSE,
					'options'   => [
						'left'   => [
							'title' => __( 'Left', 'wp-dark-mode' ),
							'icon'  => 'dashicons dashicons-editor-alignleft',
						],
						'center' => [
							'title' => __( 'Center', 'wp-dark-mode' ),
							'icon'  => 'dashicons dashicons-editor-aligncenter',
						],
						'right'  => [
							'title' => __( 'Right', 'wp-dark-mode' ),
							'icon'  => 'dashicons dashicons-editor-alignright',
						],
					],
					'toggle'    => true,
					'default'   => 'left',
					'selectors' => [
						'{{WRAPPER}}' => 'text-align: {{VALUE}};',
					],
				]
			);

			$this->end_controls_section();
		}

		/**
		 * Render the wp dark mode switcher output using by shortcode
		 *
		 * @return mixed
		 * @version 1.0.0
		 */
		public function render() {
			$settings = $this->get_settings_for_display();

			$style = isset( $settings['style'] ) ? $settings['style'] : 1;
			$size = isset( $settings['size'] ) ? $settings['size'] : 1;

			echo do_shortcode( wp_sprintf('[wp_dark_mode style="%s" size="%s"]', esc_attr( $style ), esc_attr( $size ) ) );
		}
	}
}
