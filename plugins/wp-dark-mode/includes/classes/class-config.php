<?php

/**
 * Contains all the configuration related tasks for WP Dark Mode
 *
 * @package WP Dark Mode
 * @since 5.0.0
 */

// Namespace.


namespace WP_Dark_Mode;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit( 1 );

if ( ! class_exists( __NAMESPACE__ . 'Config' ) ) {
	/**
	 * Contains all the configuration related tasks for WP Dark Mode
	 *
	 * @package WP Dark Mode
	 * @since 5.0.0
	 */
	class Config {

		/**
		 * Instance of the class
		 *
		 * @since 5.0.0
		 * @var object
		 */
		private static $instance;

		/**
		 * Returns the instance of the class
		 *
		 * @since 5.0.0
		 * @return object
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new Config();
			}

			return self::$instance;
		}


		/**
		 * Returns the default options for WP Dark Mode
		 *
		 * @since 5.0.0
		 * @var array
		 */
		public static function get_default_options() {
			$options = array(
				'frontend' => array(
					'enabled' => array(
						'type' => 'boolean',
						'default' => true,
					),
					'mode' => array(
						'type' => 'string',
						'default' => 'device',
						'options' => array(
							'default_light' => [
								'name' => 'Default Light Mode',
								'description' => 'Enable this setting if you want light mode as the default mode of your site. Visitors will find the website in light mode first.',
							],
							'default' => [
								'name' => 'Default Dark Mode',
								'description' => 'Enable this setting if you want dark mode as the default mode of your site. Visitors will find the website in dark mode first.',
							],
							'device' => [
								'name' => 'Use system settings',
								'description' => 'Dark mode will be enabled/disabled depending on the user\'s device settings.',
							],
							'time' => [
								'name' => 'Time based dark mode',
								'description' => 'Automatically enable dark mode based on user\'s given time.',
							],
							'sunset' => [
								'name' => 'Sunset Mode',
								'description' => 'Automatically enable dark mode at sunset based on the user\'s location.',
							],
						),
					),
					'time_starts' => array(
						'type' => 'string',
						'default' => '06:00 PM',
					),
					'time_ends' => array(
						'type' => 'string',
						'default' => '06:00 AM',
					),
					'custom_css' => array(
						'type' => 'string',
						'default' => '',
					),
					'remember_choice' => array(
						'type' => 'boolean',
						'default' => true,
					),
				),
				'admin' => array(
					'enabled' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'enabled_block_editor' => array(
						'type' => 'boolean',
						'default' => true,
					),
				),
				'floating_switch' => array(
					'enabled' => array(
						'type' => 'boolean',
						'default' => true,
					),
					'display' => [
						'type' => 'array',
						'default' => [
							'desktop' => true,
							'mobile' => true,
							'tablet' => true,
						],
					],
					'style' => array(
						'type' => 'number',
						'default' => 1,
					),
					'size' => array(
						'type' => 'mixed',
						'default' => 1,
						'options' => array(
							[
								'name' => 'S',
								'value' => 0.8,
							],
							[
								'name' => 'M',
								'value' => 1,
							],
							[
								'name' => 'L',
								'value' => 1.2,
							],
							[
								'name' => 'XL',
								'value' => 1.4,
							],
							[
								'name' => 'XXL',
								'value' => 1.6,
							],
							[
								'name' => 'Custom',
								'value' => 'custom',
							],
						),
					),
					'size_custom' => array(
						'type' => 'number',
						'default' => 100,
					),
					'position' => array(
						'type' => 'string',
						'options' => array(
							[
								'name' => 'Left',
								'value' => 'left',
								'icon' => '<svg class="w-4 stroke-current" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M3.125 5.625H16.875M3.125 10H16.875M3.125 14.375H10" stroke="stroke-current" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>',
							],
							[
								'name' => 'Right',
								'value' => 'right',
								'icon' => '<svg class="w-4 fill-current" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path fill-rule="evenodd" clip-rule="evenodd" d="M2 4.75C2 4.33579 2.33579 4 2.75 4H17.25C17.6642 4 18 4.33579 18 4.75C18 5.16421 17.6642 5.5 17.25 5.5H2.75C2.33579 5.5 2 5.16421 2 4.75ZM9 15.25C9 14.8358 9.33579 14.5 9.75 14.5H17.25C17.6642 14.5 18 14.8358 18 15.25C18 15.6642 17.6642 16 17.25 16H9.75C9.33579 16 9 15.6642 9 15.25Z" fill="fill-current"/>
							<path fill-rule="evenodd" clip-rule="evenodd" d="M2 10C2 9.58579 2.33579 9.25 2.75 9.25H17.25C17.6642 9.25 18 9.58579 18 10C18 10.4142 17.6642 10.75 17.25 10.75H2.75C2.33579 10.75 2 10.4142 2 10Z" fill="fill-current"/>
							</svg>',
							],
							[
								'name' => 'Custom',
								'value' => 'custom',
								'icon' => '<svg class="w-4 stroke-current" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M14.0514 3.73889L15.4576 2.33265C16.0678 1.72245 17.0572 1.72245 17.6674 2.33265C18.2775 2.94284 18.2775 3.93216 17.6674 4.54235L8.81849 13.3912C8.37792 13.8318 7.83453 14.1556 7.23741 14.3335L5 15L5.66648 12.7626C5.84435 12.1655 6.1682 11.6221 6.60877 11.1815L14.0514 3.73889ZM14.0514 3.73889L16.25 5.93749M15 11.6667V15.625C15 16.6605 14.1605 17.5 13.125 17.5H4.375C3.33947 17.5 2.5 16.6605 2.5 15.625V6.87499C2.5 5.83946 3.33947 4.99999 4.375 4.99999H8.33333" stroke="stroke-current" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>',
							],
						),
						'default' => 'right',
					),
					'position_side' => array(
						'type' => 'string',
						'default' => 'right',
					),
					'position_side_value' => array(
						'type' => 'number',
						'default' => '10',
					),
					'position_bottom_value' => array(
						'type' => 'number',
						'default' => '10',
					),
					'enabled_attention_effect' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'attention_effect' => array(
						'type' => 'string',
						'options' => array(
							'wobble' => 'Wobble',
							'vibrate' => 'Vibrate',
							'flicker' => 'Flicker',
							'shake' => 'Shake',
							'jello' => 'Jello',
							'bounce' => 'Bounce',
							'heartbeat' => 'Heartbeat',
							'blink' => 'Blink',
						),
						'default' => 'wobble',
					),
					'enabled_cta' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'cta_text' => array(
						'type' => 'string',
						'default' => 'Enable Dark Mode',
					),
					'cta_color' => array(
						'type' => 'string',
						'default' => '#ffffff',
					),
					'cta_background' => array(
						'type' => 'string',
						'default' => '#000000',
					),
					'enabled_custom_icons' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'icon_light' => array(
						'type' => 'string',
						'default' => '',
					),
					'icon_dark' => array(
						'type' => 'string',
						'default' => '',
					),
					'enabled_custom_texts' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'text_light' => array(
						'type' => 'string',
						'default' => 'Light',
					),
					'text_dark' => array(
						'type' => 'string',
						'default' => 'Dark',
					),
				),
				'menu_switch' => array(
					'enabled' => array(
						'type' => 'boolean',
						'default' => false,
					),
				),
				'content_switch' => array(
					'enabled_top_of_posts' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'enabled_top_of_pages' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'style' => array(
						'type' => 'number',
						'default' => 1,
					),
				),
				'color' => array(
					'mode' => array(
						'type' => 'string',
						'options' => array( 'automatic', 'presets', 'custom' ),
						'default' => 'automatic',
					),

					'presets' => [
						'type' => 'array',
						'default' => self::predefined_presets(),
					],

					'preset_id' => array(
						'type' => 'number',
						'default' => null,
					),
					'filter_brightness' => array(
						'type' => 'number',
						'default' => 100,
					),
					'filter_contrast' => array(
						'type' => 'number',
						'default' => 90,
					),
					'filter_grayscale' => array(
						'type' => 'number',
						'default' => 0,
					),
					'filter_sepia' => array(
						'type' => 'number',
						'default' => 10,
					),
				),
				'image' => array(
					'replaces' => array(
						'type' => 'array',
						'default' => array(),
					),
					'enabled_low_brightness' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'brightness' => array(
						'type' => 'number',
						'default' => 80,
					),
					'low_brightness_excludes' => array(
						'type' => 'array',
						'default' => [],
					),
					'enabled_low_grayscale' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'grayscale' => array(
						'type' => 'number',
						'default' => 0,
					),
					'low_grayscale_excludes' => array(
						'type' => 'array',
						'default' => [],
					),
				),
				'video' => array(
					'replaces' => array(
						'type' => 'array',
						'default' => array(),
					),
					'enabled_low_brightness' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'brightness' => array(
						'type' => 'number',
						'default' => 80,
					),
					'low_brightness_excludes' => array(
						'type' => 'array',
						'default' => [],
					),
					'enabled_low_grayscale' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'grayscale' => array(
						'type' => 'number',
						'default' => 0,
					),
					'low_grayscale_excludes' => array(
						'type' => 'array',
						'default' => [],
					),
				),
				'animation' => array(
					'enabled' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'name' => array(
						'type' => 'string',
						'options' => array(
							'fade-in' => 'Fade In',
							'pulse' => 'Pulse',
							'flip' => 'Flip',
							'roll' => 'Roll',
							'slide-left' => 'Slide Left',
							'slide-up' => 'Slide Up',
							'slide-right' => 'Slide Right',
							'slide-down' => 'Slide Down',
						),
						'default' => 'fade-in',
					),
				),
				'performance' => array(
					'track_dynamic_content' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'load_scripts_in_footer' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'execute_as' => array(
						'type' => 'string',
						'options' => array(
							'sync' => [
								'name' => 'Best Dark Mode Performance',
								'description' => 'The dark mode scripts will load before the parsing and rendering of your page. Choose this option when executing dark mode is the first priority for you.',
							],
							'async' => [
								'name' => 'Balanced Performance for My Website and Dark Mode',
								'description' => 'The dark mode scripts will be executed at the same time as your page. Choose this option to get a moderate dark mode loading time without compromising website loading speed.',
							],
							'defer' => [
								'name' => 'Prioritize My Website Loading',
								'description' => 'The dark mode script will be fetched asynchronously and executed after page parsing, just before the "DOMContentLoaded" event. Choose this option if your focus is on optimizing your website\'s performance over the WP Dark Mode.',
							],
						),
						'default' => 'sync',
					),
					'exclude_cache' => array(
						'type' => 'boolean',
						'default' => false,
					),
				),
				'excludes' => array(
					'elements' => array(
						'type' => 'string',
						'default' => '',
					),
					'elements_includes' => array(
						'type' => 'string',
						'default' => '',
					),

					'posts' => array(
						'type' => 'array',
						'default' => [],
					),
					'posts_all' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'posts_except' => array(
						'type' => 'array',
						'default' => [],
					),

					'taxonomies' => array(
						'type' => 'array',
						'default' => [],
					),
					'taxonomies_all' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'taxonomies_except' => array(
						'type' => 'array',
						'default' => [],
					),

					// WooCommerce.
					'wc_products' => array(
						'type' => 'array',
						'default' => [],
					),
					'wc_products_all' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'wc_products_except' => array(
						'type' => 'array',
						'default' => [],
					),
					'wc_categories' => array(
						'type' => 'array',
						'default' => [],
					),
					'wc_categories_all' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'wc_categories_except' => array(
						'type' => 'array',
						'default' => [],
					),
				),
				'accessibility' => array(
					'enabled_keyboard_shortcut' => array(
						'type' => 'boolean',
						'default' => true,
					),
					'enabled_url_param' => array(
						'type' => 'boolean',
						'default' => false,
					),
				),
				'typography' => array(
					'enabled' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'font_size' => array(
						'type' => 'mixed',
						'default' => '1.2',
					),
					'font_size_custom' => array(
						'type' => 'number',
						'default' => 100,
					),
				),
				'analytics' => array(
					'enabled' => array(
						'type' => 'boolean',
						'default' => true,
					),
					'enabled_dashboard_widget' => array(
						'type' => 'boolean',
						'default' => true,
					),
					'enabled_email_reporting' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'email_reporting_frequency' => array(
						'type' => 'string',
						'options' => array(
							'daily' => 'Daily',
							'weekly' => 'Weekly',
							'biweekly' => 'Bi-Weekly',
							'monthly' => 'Monthly',
							'quarterly' => 'Quarterly',
							'yearly' => 'Yearly',
						),
						'default' => 'daily',
					),
					'email_reporting_address' => array(
						'type' => 'string',
						'default' => '',
					),
					'email_reporting_subject' => array(
						'type' => 'string',
						'default' => 'WP Dark Mode Analytics Report',
					),
				),
			);

			return apply_filters( 'wp_dark_mode_default_options', $options );
		}

		/**
		 * Default presets
		 *
		 * @since 5.0.0
		 * @return array
		 */
		public static function predefined_presets() {
			$presets = [
				[
					'name' => 'Gold',
					'bg' => '#000',
					'secondary_bg' => '#000',
					'text' => '#dfdedb',
					'link' => '#e58c17',
					'link_hover' => '#e58c17',
					'input_bg' => '#000',
					'input_text' => '#dfdedb',
					'input_placeholder' => '#dfdedb',
					'button_text' => '#dfdedb',
					'button_hover_text' => '#dfdedb',
					'button_bg' => '#141414',
					'button_hover_bg' => '#141414',
					'button_border' => '#1e1e1e',
					'enable_scrollbar' => false,
					'scrollbar_track' => '#141414',
					'scrollbar_thumb' => '#dfdedb',
				],
				[
					'name' => 'Sapphire',
					'bg' => '#1B2836',
					'secondary_bg' => '#1B2836',
					'text' => '#fff',
					'link' => '#459BE6',
					'link_hover' => '#459BE6',
					'input_bg' => '#1B2836',
					'input_text' => '#fff',
					'input_placeholder' => '#fff',
					'button_text' => '#fff',
					'button_hover_text' => '#fff',
					'button_bg' => '#2f3c4a',
					'button_hover_bg' => '#2f3c4a',
					'button_border' => '#394654',
					'enable_scrollbar' => false,
					'scrollbar_track' => '#1B2836',
					'scrollbar_thumb' => '#fff',
				],
				[
					'name' => 'Fuchsia',
					'bg' => '#1E0024',
					'secondary_bg' => '#1E0024',
					'text' => '#fff',
					'link' => '#E251FF',
					'link_hover' => '#E251FF',
					'input_bg' => '#1E0024',
					'input_text' => '#fff',
					'input_placeholder' => '#fff',
					'button_text' => '#fff',
					'button_hover_text' => '#fff',
					'button_bg' => '#321438',
					'button_hover_bg' => '#321438',
					'button_border' => '#321438',
					'enable_scrollbar' => false,
					'scrollbar_track' => '#1E0024',
					'scrollbar_thumb' => '#fff',
				],
				[
					'name' => 'Rose',
					'bg' => '#270000',
					'secondary_bg' => '#270000',
					'text' => '#fff',
					'link' => '#FF7878',
					'link_hover' => '#FF7878',
					'input_bg' => '#270000',
					'input_text' => '#fff',
					'input_placeholder' => '#fff',
					'button_text' => '#fff',
					'button_hover_text' => '#fff',
					'button_bg' => '#3b1414',
					'button_hover_bg' => '#3b1414',
					'button_border' => '#451e1e',
					'enable_scrollbar' => false,
					'scrollbar_track' => '#270000',
					'scrollbar_thumb' => '#fff',
				],
				[
					'name' => 'Violet',
					'bg' => '#160037',
					'secondary_bg' => '#160037',
					'text' => '#EBEBEB',
					'link' => '#B381FF',
					'link_hover' => '#B381FF',
					'input_bg' => '#160037',
					'input_text' => '#EBEBEB',
					'input_placeholder' => '#EBEBEB',
					'button_text' => '#EBEBEB',
					'button_hover_text' => '#EBEBEB',
					'button_bg' => '#2a144b',
					'button_hover_bg' => '#2a144b',
					'button_border' => '#341e55',
					'enable_scrollbar' => false,
					'scrollbar_track' => '#160037',
					'scrollbar_thumb' => '#EBEBEB',
				],
				[
					'name' => 'Pink',
					'bg' => '#121212',
					'secondary_bg' => '#121212',
					'text' => '#E6E6E6',
					'link' => '#FF9191',
					'link_hover' => '#FF9191',
					'input_bg' => '#121212',
					'input_text' => '#E6E6E6',
					'input_placeholder' => '#E6E6E6',
					'button_text' => '#E6E6E6',
					'button_hover_text' => '#E6E6E6',
					'button_bg' => '#262626',
					'button_hover_bg' => '#262626',
					'button_border' => '#303030',
					'enable_scrollbar' => false,
					'scrollbar_track' => '#121212',
					'scrollbar_thumb' => '#E6E6E6',
				],
				[
					'name' => 'Kelly',
					'bg' => '#000A3B',
					'secondary_bg' => '#000A3B',
					'text' => '#FFFFFF',
					'link' => '#3AFF82',
					'link_hover' => '#3AFF82',
					'input_bg' => '#000A3B',
					'input_text' => '#FFFFFF',
					'input_placeholder' => '#FFFFFF',
					'button_text' => '#FFFFFF',
					'button_hover_text' => '#FFFFFF',
					'button_bg' => '#141e4f',
					'button_hover_bg' => '#141e4f',
					'button_border' => '#1e2859',
					'enable_scrollbar' => false,
					'scrollbar_track' => '#000A3B',
					'scrollbar_thumb' => '#FFFFFF',
				],
				[
					'name' => 'Magenta',
					'bg' => '#171717',
					'secondary_bg' => '#171717',
					'text' => '#BFB7C0',
					'link' => '#F776F0',
					'link_hover' => '#F776F0',
					'input_bg' => '#171717',
					'input_text' => '#BFB7C0',
					'input_placeholder' => '#BFB7C0',
					'button_text' => '#BFB7C0',
					'button_hover_text' => '#BFB7C0',
					'button_bg' => '#2b2b2b',
					'button_hover_bg' => '#2b2b2b',
					'button_border' => '#353535',
					'enable_scrollbar' => false,
					'scrollbar_track' => '#171717',
					'scrollbar_thumb' => '#BFB7C0',
				],
				[
					'name' => 'Green',
					'bg' => '#003711',
					'secondary_bg' => '#003711',
					'text' => '#FFFFFF',
					'link' => '#84FF6D',
					'link_hover' => '#84FF6D',
					'input_bg' => '#003711',
					'input_text' => '#FFFFFF',
					'input_placeholder' => '#FFFFFF',
					'button_text' => '#FFFFFF',
					'button_hover_text' => '#FFFFFF',
					'button_bg' => '#144b25',
					'button_hover_bg' => '#144b25',
					'button_border' => '#1e552f',
					'enable_scrollbar' => false,
					'scrollbar_track' => '#003711',
					'scrollbar_thumb' => '#FFFFFF',
				],
				[
					'name' => 'Orange',
					'bg' => '#23243A',
					'secondary_bg' => '#23243A',
					'text' => '#D6CB99',
					'link' => '#FF9323',
					'link_hover' => '#FF9323',
					'input_bg' => '#23243A',
					'input_text' => '#D6CB99',
					'input_placeholder' => '#D6CB99',
					'button_text' => '#D6CB99',
					'button_hover_text' => '#D6CB99',
					'button_bg' => '#37384e',
					'button_hover_bg' => '#37384e',
					'button_border' => '#414258',
					'enable_scrollbar' => false,
					'scrollbar_track' => '#23243A',
					'scrollbar_thumb' => '#D6CB99',
				],

				[
					'name' => 'Yellow',
					'bg' => '#151819',
					'secondary_bg' => '#151819',
					'text' => '#D5D6D7',
					'link' => '#DAA40B',
					'link_hover' => '#DAA40B',
					'input_bg' => '#151819',
					'input_text' => '#D5D6D7',
					'input_placeholder' => '#D5D6D7',
					'button_text' => '#D5D6D7',
					'button_hover_text' => '#D5D6D7',
					'button_bg' => '#292c2d',
					'button_hover_bg' => '#292c2d',
					'button_border' => '#333637',
					'enable_scrollbar' => false,
					'scrollbar_track' => '#151819',
					'scrollbar_thumb' => '#D5D6D7',
				],
				[
					'name' => 'Facebook',
					'bg' => '#18191A',
					'secondary_bg' => '#18191A',
					'text' => '#DCDEE3',
					'link' => '#2D88FF',
					'link_hover' => '#2D88FF',
					'input_bg' => '#18191A',
					'input_text' => '#DCDEE3',
					'input_placeholder' => '#DCDEE3',
					'button_text' => '#DCDEE3',
					'button_hover_text' => '#DCDEE3',
					'button_bg' => '#2c2d2e',
					'button_hover_bg' => '#2c2d2e',
					'button_border' => '#363738',
					'enable_scrollbar' => false,
					'scrollbar_track' => '#18191A',
					'scrollbar_thumb' => '#DCDEE3',
				],

				[
					'name' => 'Twitter',
					'bg' => '#141d26',
					'secondary_bg' => '#141d26',
					'text' => '#fff',
					'link' => '#1C9CEA',
					'link_hover' => '#1C9CEA',
					'input_bg' => '#141d26',
					'input_text' => '#fff',
					'input_placeholder' => '#fff',
					'button_text' => '#fff',
					'button_hover_text' => '#fff',
					'button_bg' => '#28313a',
					'button_hover_bg' => '#28313a',
					'button_border' => '#323b44',
					'enable_scrollbar' => false,
					'scrollbar_track' => '#141d26',
					'scrollbar_thumb' => '#fff',
				],
			];

			return apply_filters( 'wp_dark_mode_default_presets', $presets );
		}
	}
}
