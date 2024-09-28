<?php

/**
 * Adjusts the upgrade process for WP Dark Mode from older versions
 *
 * @package WP Dark Mode
 * @since 5.0.0
 */

// Namespace.
namespace WP_Dark_Mode\Admin;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit( 1 );

if ( ! class_exists( __NAMESPACE__ . 'Upgrade' ) ) {

	/**
	 * Adjusts the upgrade process for WP Dark Mode from older versions
	 *
	 * @package WP Dark Mode
	 * @since 5.0.0
	 */
	class Upgrade extends \WP_Dark_Mode\Base {

		// Use options trait.
		use \WP_Dark_Mode\Traits\Options;

		/**
		 * Actions needed for the class
		 */
		public function actions() {

			$upgraded_version = get_option( 'wp_dark_mode_upgraded_version', '0.0.0' );

			// Bail, if the version is already upgraded.
			if ( version_compare( '5.0.0', $upgraded_version, '<=' ) ) {
				return;
			}

			add_action( 'admin_init', [ $this, 'run_upgrade' ] );
		}

		/**
		 * Run Upgrade
		 *
		 * @since 5.0.0
		 */
		public function run_upgrade() {
			// Single value options.
			$this->update_one_to_one_options();

			// Upgrade modes.
			$this->upgrade_modes();

			// Upgrade exclude posts.
			$this->upgrade_exclude_posts();

			// Upgrade exclude categories.
			$this->upgrade_exclude_categories();

			$this->upgrade_woocommerce_settings();

			// Upgrade position values.
			$this->upgrade_position_values();

			// Upgrade color presets.
			$this->upgrade_color_presets();

			// Upgrade image settings.
			$this->upgrade_image_settings();

			// Upgrade video settings.
			$this->upgrade_video_settings();

			// Upgrade performance settings.
			$this->upgrade_performance_settings();

			$this->upgrade_notices();

			$this->set_option( 'version', WP_DARK_MODE_VERSION );

			update_option( 'wp_dark_mode_upgraded_version', WP_DARK_MODE_VERSION );
		}



		/**
		 * Old option sets
		 *
		 * @since 5.0.0
		 */
		public function old_option_sets() {

			/**
			 * One to one pair update.
			 */

			 $old_option_sets = [
				 'wp_dark_mode_general' => [
					 'enable_frontend' => 'frontend_enabled',
					 'enable_backend' => 'admin_enabled',
					 'enable_block_editor' => 'admin_enabled_block_editor',
				 ],
				 'wp_dark_mode_advanced' => [
					 'start_at' => 'frontend_time_starts',
					 'end_at' => 'frontend_time_ends',
				 ],
				 'wp_dark_mode_includes_excludes' => [
					 'includes' => 'excludes_elements_includes',
					 'excludes' => 'excludes_elements',
				 ],
				 'wp_dark_mode_switch' => [
					 'show_switcher' => 'floating_switch_enabled',
					 'switch_style' => 'floating_switch_style',
					 'switcher_size' => 'floating_switch_size',
					 'switcher_scale' => 'floating_switch_size_custom',
					 'switcher_position' => 'floating_switch_position',
					 'enable_cta' => 'floating_switch_enabled_cta',
					 'cta_text' => 'floating_switch_cta_text',
					 'cta_text_color' => 'floating_switch_cta_color',
					 'cta_bg_color' => 'floating_switch_cta_background',
					 'enable_menu_switch' => 'menu_switch_enabled',
					 'custom_switch_icon' => 'floating_switch_enabled_custom_icons',
					 'switch_icon_light' => 'floating_switch_icon_light',
					 'switch_icon_dark' => 'floating_switch_icon_dark',
					 'custom_switch_text' => 'floating_switch_enabled_custom_texts',
					 'switch_text_light' => 'floating_switch_text_light',
					 'switch_text_dark' => 'floating_switch_text_dark',
					 'attention_effect' => 'floating_switch_attention_effect ',
					 'show_above_post' => 'content_switch_enabled_top_of_posts',
					 'show_above_page' => 'content_switch_enabled_top_of_pages',
				 ],
				 'wp_dark_mode_custom_css' => [
					 'custom_css' => 'frontend_custom_css',
				 ],
				 'wp_dark_mode_accessibility' => [
					 'font_size_toggle' => 'typography_enabled',
					 'font_size' => 'typography_font_size',
					 'custom_font_size' => 'typography_font_size_custom',
					 'keyboard_shortcut' => 'accessibility_enabled_keyboard_shortcut',
					 'url_parameter' => 'accessibility_enabled_url_param',
					 'dynamic_content_mode' => 'performance_track_dynamic_content',
				 ],
				 'wp_dark_mode_animation' => [
					 'toggle_animation' => 'animation_enabled',
					 'animation' => 'animation_name',
				 ],
				 'wp_dark_mode_analytics_reporting' => [
					 'enable_analytics' => 'analytics_enabled',
					 'dashboard_widget' => 'analytics_enabled_dashboard_widget',
					 'email_reporting' => 'analytics_enabled_email_reporting',
					 'reporting_frequency' => 'analytics_email_reporting_frequency',
					 'reporting_email' => 'analytics_email_reporting_address',
					 'reporting_email_subject' => 'analytics_email_reporting_subject',
				 ],
			 ];

			 return apply_filters( 'wp_dark_mode_old_option_sets', $old_option_sets );
		}

		/**
		 * Update one-to-one pair options
		 *
		 * @since 5.0.0
		 */
		public function update_one_to_one_options() {
			$old_option_sets = $this->old_option_sets();

			// Bail, if old option sets are not set.
			if ( ! $old_option_sets ) {
				return;
			}

			foreach ( $old_option_sets as $section => $options ) {

				$_section = get_option( $section );

				foreach ( $options as $old_key => $new_key ) {
					$value = isset( $_section[ $old_key ] ) ? $_section[ $old_key ] : null;

					// continue if value is null.
					if ( is_null( $value ) ) {
						continue;
					}

					// If the value is on or off, convert it to boolean.
					if ( 'on' === $value || 'off' === $value ) {
						$value = 'on' === $value;
					}

					// For typography_font_size.
					if ( 'typography_font_size' === $new_key ) {
						$upgradables = [
							'120' => '0.8',
							'150' => '1.2',
							'200' => '1.4',
							'custom' => 'custom' // phpcs:ignore
						];

						$value = isset( $upgradables[ $value ] ) ? $upgradables[ $value ] : $value;
					}

					// For floating_switch_size.
					if ( 'floating_switch_size' === $new_key ) {
						$upgradable_sizes = [
							'xs' => '0.8',
							'sm' => '1',
							'normal' => '1.2',
							'xl' => '1.4',
							'xxl' => '1.6',
							'custom' => 'custom' // phpcs:ignore
						];

						$value = isset( $upgradable_sizes[ $value ] ) ? $upgradable_sizes[ $value ] : 'custom';

						if ( 'custom' === $value ) {
							$value = round( $_section['switcher_scale'] / 100, 2 );
						}
					}

					// For floating_switch_attention_effect.
					if ( 'attention_effect' === $old_key ) {
						$this->set_option( 'floating_switch_enabled_attention_effect', 'none' !== $value );
					}

					// update option.
					$this->set_option( $new_key, $value );

				}
			}
		}

		/**
		 * Get old formatted option
		 *
		 * @section string Section name
		 * @key string Option key
		 * @default string Default value
		 * @return mixed
		 * @since 5.0.0
		 */
		public function get_old_settings( $section = 'wp_dark_mode_general', $key = '', $default = '' ) {
			$settings = get_option( $section );
			$value = isset( $settings[ $key ] ) ? $settings[ $key ] : $default;

			// If the value is on or off, convert it to boolean.
			if ( 'on' === $value || 'off' === $value ) {
				$value = 'on' === $value;
			}

			return $value;
		}

		/**
		 * Upgrade modes
		 *
		 * @since 5.0.0
		 */
		public function upgrade_modes() {
			$advance = get_option( 'wp_dark_mode_advanced', null );

			// Bail, if advance settings are not set.
			if ( ! $advance ) {
				return;
			}

			$new_mode = 'device';

			if ( 'on' === $advance['default_mode'] ) {
				$new_mode = 'default';
			} elseif ( 'on' === $advance['time_based_mode'] ) {
				$new_mode = 'time';
			} elseif ( 'on' === $advance['sunset_mode'] ) {
				$new_mode = 'sunset';
			} else {
				$new_mode = 'device';
			}

			// Upgrade time.
			$start_time = isset( $advance['start_at'] ) ? $advance['start_at'] : '18:00';
			$end_time = isset( $advance['end_at'] ) ? $advance['end_at'] : '06:00';

			// update option.
			$this->set_option( 'frontend_time_starts', gmdate('g:i A', strtotime( $start_time )) );
			$this->set_option( 'frontend_time_ends', gmdate('g:i A', strtotime( $end_time )) );

			// update mode.
			$this->set_option( 'frontend_mode', $new_mode );
		}

		/**
		 * Upgrade exclude posts
		 */
		public function upgrade_exclude_posts() {
			// Get all exclude posts.
			$exclude_posts = $this->get_old_settings( 'wp_dark_mode_triggers', 'exclude_posts', [] );
			$exclude_pages = $this->get_old_settings( 'wp_dark_mode_triggers', 'exclude_pages', [] );

			// Merge exclude posts and pages arrays
			if ( ! empty( $exclude_pages ) ) {
				$exclude_posts = ! empty( $exclude_posts ) ? array_merge( $exclude_posts, $exclude_pages ) : $exclude_pages;
			}

			// Get all exclude posts except.
			$exclude_posts_except = $this->get_old_settings( 'wp_dark_mode_triggers', 'exclude_posts_except', [] );
			$exclude_pages_except = $this->get_old_settings( 'wp_dark_mode_triggers', 'exclude_pages_except', [] );

			// Merge exclude posts and pages arrays
			if ( ! empty( $exclude_pages_except ) ) {
				$exclude_posts_except = ! empty( $exclude_posts_except ) ? array_merge( $exclude_posts_except, $exclude_pages_except ) : $exclude_pages_except;
			}

			// Update exclude all posts.
			if ( ! empty( $exclude_posts ) ) {
				$exclude_posts = ! is_array( $exclude_posts ) ? explode( ',', $exclude_posts ) : $exclude_posts;
				$exclude_posts = array_map( 'intval', $exclude_posts );
				$exclude_posts = array_filter( $exclude_posts );

				// update option.
				$this->set_option( 'excludes_posts', $exclude_posts );
			}

			// Update exclude all posts.
			if ( ! empty( $exclude_posts_except ) ) {
				$exclude_posts_except = ! is_array( $exclude_posts_except ) ? explode( ',', $exclude_posts_except ) : $exclude_posts_except;
				$exclude_posts_except = array_map( 'intval', $exclude_posts_except );
				$exclude_posts_except = array_filter( $exclude_posts_except );

				// update option.
				$this->set_option( 'excludes_posts_except', $exclude_posts_except );
			}

			// Update exclude all posts.

			// Get exclude all posts or not.
			$exclude_all_posts = $this->get_old_settings( 'wp_dark_mode_triggers', 'exclude_all_posts', false );
			$exclude_all_pages = $this->get_old_settings( 'wp_dark_mode_triggers', 'exclude_all_pages', false );

			// Merge exclude all posts and pages arrays
			$exclude_all_posts = wp_validate_boolean( $exclude_all_pages ) || wp_validate_boolean($exclude_all_posts);

			$this->set_option( 'excludes_posts_all', $exclude_all_posts );
		}

		/**
		 * Upgrade exclude categories
		 */
		public function upgrade_exclude_categories() {
			// Get all exclude categories.
			$exclude_categories = $this->get_old_settings( 'wp_dark_mode_triggers', 'exclude_categories', [] );
			$exclude_tags = $this->get_old_settings( 'wp_dark_mode_triggers', 'exclude_tags', [] );

			// Merge exclude categories and tags arrays
			if ( $exclude_tags && ! empty( $exclude_tags ) ) {
				$exclude_tags = is_array( $exclude_tags ) ? $exclude_tags : explode( ',', $exclude_tags );
				$exclude_categories = ! empty( $exclude_categories ) ? array_merge( $exclude_categories, $exclude_tags ) : $exclude_tags;
			}

			// Update exclude all categories.
			if ( ! empty( $exclude_categories ) ) {
				$exclude_categories = array_filter( $exclude_categories );

				// update option.
				$this->set_option( 'excludes_taxonomies', $exclude_categories );
			}

			// Get all exclude categories except.
			$exclude_categories_except = $this->get_old_settings( 'wp_dark_mode_triggers', 'specific_categories', [] );
			$exclude_tags_except = $this->get_old_settings( 'wp_dark_mode_triggers', 'specific_tags', [] );

			// Merge exclude categories and tags arrays
			if ( $exclude_tags_except && ! empty( $exclude_tags_except ) ) {
				$exclude_tags_except = is_array( $exclude_tags_except ) ? $exclude_tags_except : explode( ',', $exclude_tags_except );
				$exclude_categories_except = ! empty( $exclude_categories_except ) ? array_merge( $exclude_categories_except, $exclude_tags_except ) : $exclude_tags_except;
			}

			// Update exclude all categories.
			if ( $exclude_categories_except && ! empty( $exclude_categories_except ) ) {
				$exclude_categories_except = array_filter( $exclude_categories_except );

				// update option.
				$this->set_option( 'excludes_taxonomies_except', $exclude_categories_except );
			}

			// Update exclude all categories.

			// Get exclude all categories or not.
			$exclude_all_categories = $this->get_old_settings( 'wp_dark_mode_triggers', 'exclude_all_categories', false );
			$exclude_all_tags = $this->get_old_settings( 'wp_dark_mode_triggers', 'exclude_all_tags', false );

			// Merge exclude all categories and tags arrays
			$exclude_all_categories = wp_validate_boolean( $exclude_all_categories ) || wp_validate_boolean($exclude_all_tags);

			$this->set_option( 'excludes_taxonomies_all', $exclude_all_categories );
		}

		// Upgrade WooCommerce Products.
		public function upgrade_woocommerce_settings() {
			// Get all exclude categories.
			$woo_settings = get_option('wp_dark_mode_wc');

			// Bail, if WooCommerce settings are not set.
			if ( ! $woo_settings ) {
				return;
			}

			$exclude_wc_categories = ! empty( $woo_settings['exclude_wc_categories'] ) ? $woo_settings['exclude_wc_categories'] : [];
			$exclude_wc_categories_except = ! empty( $woo_settings['specific_wc_categories'] ) ? $woo_settings['specific_wc_categories'] : [];
			$exclude_all_wc_categories = ! empty( $woo_settings['exclude_all_wc_categories'] ) ? 'on' === $woo_settings['exclude_all_wc_categories'] : false;

			$exclude_products = ! empty( $woo_settings['exclude_products'] ) ? $woo_settings['exclude_products'] : [];
			$exclude_products_except = ! empty( $woo_settings['specific_products'] ) ? $woo_settings['specific_products'] : [];
			$exclude_all_products = ! empty( $woo_settings['exclude_all_products'] ) ? 'on' === $woo_settings['exclude_all_products'] : false;

			// Update exclude all categories.
			if ( $exclude_wc_categories && ! empty( $exclude_wc_categories ) ) {
				$this->set_option( 'excludes_wc_categories', $exclude_wc_categories );
			}

			// Update exclude all categories.
			if ( $exclude_products && ! empty( $exclude_products ) ) {
				// update option.
				$exclude_products = array_map( 'intval', $exclude_products );
				$this->set_option( 'excludes_wc_products', $exclude_products );
			}

			// Update exclude all categories.
			if ( $exclude_wc_categories_except && ! empty( $exclude_wc_categories_except ) ) {
				$this->set_option( 'excludes_wc_categories_except', $exclude_wc_categories_except );
			}

			// Update exclude all categories.
			if ( $exclude_products_except && ! empty( $exclude_products_except ) ) {
				// update option.
				$exclude_products_except = array_map( 'intval', $exclude_products_except );
				$this->set_option( 'excludes_wc_products_except', $exclude_products_except );
			}

			$this->set_option( 'excludes_wc_categories_all', $exclude_all_wc_categories );
			$this->set_option( 'excludes_wc_products_all', $exclude_all_products );
		}

		/**
		 * Upgrade position values
		 */
		public function upgrade_position_values() {
			$old_position = $this->get_old_settings( 'wp_dark_mode_switch', 'switcher_position', 'right_bottom' );

			// If old_position is not custom.
			if ( 'custom' !== $old_position ) {
				// update option.
				$new_position = 'right_bottom' === $old_position ? 'right' : 'left';
				$this->set_option( 'floating_switch_position', $new_position );
			}

			// If old_position is custom.
			$switch_side = $this->get_old_settings( 'wp_dark_mode_switch', 'switch_side', 'right_bottom' );
			$bottom_spacing = $this->get_old_settings( 'wp_dark_mode_switch', 'bottom_spacing', 10 );
			$side_spacing = $this->get_old_settings( 'wp_dark_mode_switch', 'side_spacing', 10 );

			// update option.
			$updated_switch_side = 'right_bottom' === $switch_side ? 'right' : 'left';
			$this->set_option( 'floating_switch_position_side', $updated_switch_side );

			$this->set_option( 'floating_switch_position_side_value', $side_spacing );
			$this->set_option( 'floating_switch_position_bottom_value', $bottom_spacing );
		}

		/**
		 * Upgrade custom colors
		 */
		public function upgrade_color_presets() {

			$color = get_option( 'wp_dark_mode_color' );

			if ( ! $color ) {
				return;
			}

			// Default preset.
			$preset_id = 1;
			$mode = 'presets';

			// Create custom preset.
			if ( 'on' === $color['customize_colors'] ) {
				// If colors are set, create a new preset.
				$color_presets = $this->get_option( 'color_presets', [] );

				$new_preset = isset( $color_presets[0] ) ? $color_presets[0] : [];

				$new_preset['name'] = 'Custom 1';
				$new_preset['bg'] = isset( $color['darkmode_bg_color'] ) ? $color['darkmode_bg_color'] : '#000000';
				$new_preset['secondary_bg'] = isset( $color['darkmode_bg_color'] ) ? $color['darkmode_bg_color'] : '#000000';
				$new_preset['text'] = isset( $color['darkmode_text_color'] ) ? $color['darkmode_text_color'] : '#ffffff';
				$new_preset['link'] = isset( $color['darkmode_link_color'] ) ? $color['darkmode_link_color'] : '#ffffff';

				// Add new preset.
				$color_presets[] = $new_preset;

				$preset_id = count( $color_presets );

				$mode = 'custom';

				// update option.
				$this->set_option( 'color_presets', $color_presets );
			} else {
				if ( 'on' === $color['enable_preset'] ) {

					// Use existing preset.
					$preset_id = isset( $color['color_preset'] ) ? intval( $color['color_preset'] ) : 0;
					++$preset_id;

					$mode = 'presets';
				} else {
					// Automatic mode.
					$preset_id = 0;

					$mode = 'automatic';
				}
			}

			// update option.
			$this->set_option( 'color_preset_id', $preset_id );
			$this->set_option( 'color_mode', $mode );
		}

		/**
		 * Upgrade image settings
		 */
		public function upgrade_image_settings() {

			// Update image replacements.
			$images       = get_option( 'wp_dark_mode_image_settings' );

			// Bail, if image settings are not set.
			if ( ! $images ) {
				return;
			}

			$light_images = ! empty( $images['light_images'] ) ? $images['light_images'] : [];
			$dark_images  = ! empty( $images['dark_images'] ) ? $images['dark_images'] : [];

			$min_length = min( count( $light_images ), count( $dark_images ) );

			// convert the array where each element will be ['light' => '', 'dark' => ''] format.
			$image_replaces = [];

			for ( $i = 0; $i < $min_length; $i++ ) {
				$image_replaces[] = [
					'light' => $light_images[ $i ],
					'dark'  => $dark_images[ $i ],
				];
			}

			$this->set_option( 'image_replaces', $image_replaces );

			// Update low brightness.
			$new_low_brightness = 'on' === $images['low_brightness'];

			// update option.
			$this->set_option( 'image_enabled_low_brightness', $new_low_brightness );

			// Update grayscale.
			$new_grayscale = 'on' === $images['grayscale'];

			// update option.
			$this->set_option( 'image_enabled_low_grayscale', $new_grayscale );
		}

		/**
		 * Upgrade video settings
		 */
		public function upgrade_video_settings() {
			// Update video replacements.
			$videos       = get_option( 'wp_dark_mode_video_settings' );

			// Bail, if video settings are not set.
			if ( ! $videos ) {
				return;
			}

			$light_videos = ! empty( $videos['light_videos'] ) ? $videos['light_videos'] : [];
			$dark_videos  = ! empty( $videos['dark_videos'] ) ? $videos['dark_videos'] : [];

			$min_length = min( count( $light_videos ), count( $dark_videos ) );

			// convert the array where each element will be ['light' => '', 'dark' => ''] format.
			$video_replaces = [];

			for ( $i = 0; $i < $min_length; $i++ ) {
				$video_replaces[] = [
					'light' => $light_videos[ $i ],
					'dark'  => $dark_videos[ $i ],
				];
			}

			$this->set_option( 'video_replaces', $video_replaces );
		}

		/**
		 * Upgrade performance settings
		 */
		public function upgrade_performance_settings() {
			$performance_mode = $this->get_old_settings( 'wp_dark_mode_performance', 'performance_mode', null );

			// Bail, if performance mode is not set.
			if ( is_null( $performance_mode ) ) {
				return;
			}

			// If performance mode is set, update load_scripts_in_footer.
			if ( 'on' === $performance_mode ) {
				$this->set_option( 'load_scripts_in_footer', true );
			} else {
				$this->set_option( 'load_scripts_in_footer', false );
			}
		}

		/**
		 * Upgrade notices
		 */
		public function upgrade_notices() {
			$install = \WP_Dark_Mode\Admin\Install::get_instance();
			$install->set_notices();
		}
	}

	// Instantiate the class.
	Upgrade::init();
}
