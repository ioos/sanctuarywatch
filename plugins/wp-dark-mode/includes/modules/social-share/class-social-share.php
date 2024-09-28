<?php
/**
 * Social Share Module for WP Dark Mode.
 *
 * @since 2.3.5
 * @author WPPOOL
 * @package WP_DARK_MODE
 */

namespace WP_Dark_Mode;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( __NAMESPACE__ . 'SocialShare' ) ) {

	/**
	 * Class SocialShare
	 * Contains all the functionalities for social share module of WP Dark Mode.
	 *
	 * @package WPDarkMode\Module
	 * @since 2.3.5
	 */
	class SocialShare extends \WP_Dark_Mode\Base {

		// Use traits.
		use \WP_Dark_Mode\Traits\Utility;

		/**
		 * Registers action hooks for social share module of WP Dark Mode.
		 *
		 * @since 2.3.5
		 * @return void
		 */
		public function actions() {
			// Activation.
			add_action( 'wp_dark_mode_loaded', [ $this, 'initialize_social_share' ] );
			// Admin menu page.
			add_action( 'admin_menu', [ $this, 'social_share_admin_menu' ], 40 );
			// Scripts.
			add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ], 10 );
			// Ajax.
			add_action( 'wp_ajax_wpdm_social_share_save_options', [ $this, 'wpdm_social_share_save_options' ], 99 );
			add_action( 'wp_ajax_wpdm_social_share_counter', [ $this, 'wpdm_social_share_counter' ], 99 );
			add_action( 'wp_ajax_no_priv_wpdm_social_share_counter', [ $this, 'wpdm_social_share_counter' ], 99 );

			// Admin header.
			add_action( 'admin_head', [ $this, 'admin_head' ] );

			if ( true === wp_validate_boolean( get_option( 'wpdm_social_share_enable' ) ) ) {
				$this->hooks_if_social_share_enabled();
			}
		}

		/**
		 * Registers filter hooks for social share module of WP Dark Mode.
		 *
		 * @since 2.3.5
		 * @return void
		 */
		public function filters() {
			add_filter( 'wpdarkmode_settings_option_names', [ $this, 'wpdm_settings_option_names' ] );
		}


		/**
		 * Registers hooks if social share is enabled in WP Dark Mode.
		 *
		 * @since 2.3.5
		 * @return void
		 */
		public function hooks_if_social_share_enabled() {

			global $social_share;
			$social_share                            = $this->social_share_options();
			$social_share->all_channels              = $this->channels();
			$social_share->get_kses_extended_ruleset = $this->get_kses_extended_ruleset();

			// Actions.
			add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_scripts' ], 5 );
			add_action( 'wp_head', [ $this, 'wp_head' ], 10 );

			// Filters.
			add_filter( 'the_content', [ $this, 'the_content' ], 90 );
		}

		/**
		 * Performs activation tasks for social share module of WP Dark Mode.
		 *
		 * @since 2.3.4
		 */
		public function initialize_social_share() {
			if ( ! get_option( 'wpdm_social_share_init', false ) ) {
				update_option( 'wpdm_social_share_init', true );

				// Create database table.
				$this->create_database_table();
			}

			// Reset to default options.
			$options = $this->social_share_default_options();

			foreach ( $options as $key => $value ) {
				$name = 'wpdm_social_share_' . $key;

				if ( ! get_option( $name, false ) ) {
					update_option( $name, $value );
				}
			}
		}

		/**
		 * Creates database table for social share module.
		 *
		 * @since 2.3.5
		 * @return void
		 */
		public function create_database_table() {
			global $wpdb;
			$table_name      = $wpdb->prefix . 'wpdm_social_shares';
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
				`ID` int(9) NOT NULL AUTO_INCREMENT,
				`post_id` int(9) DEFAULT '0' NOT NULL,
				`url` text DEFAULT '' NOT NULL,
				`channel` varchar(255) DEFAULT '' NOT NULL,
				`user_id` int(9) DEFAULT '0' NOT NULL,
				`user_agent` text DEFAULT '' NOT NULL,                
				PRIMARY KEY (`ID`)
			) $charset_collate;";

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );
		}

		/**
		 * Extends the default allowed HTML tags for KSES.
		 * Adds SVG tags to the allowed HTML tags.
		 *
		 * @return array
		 */
		public function get_kses_extended_ruleset() {
			$kses_defaults = wp_kses_allowed_html( 'post' );

			$svg_args = [
				'svg'   => [
					'class'           => true,
					'aria-hidden'     => true,
					'aria-labelledby' => true,
					'role'            => true,
					'xmlns'           => true,
					'width'           => true,
					'height'          => true,
					'viewbox'         => true, // <= Must be lower case!
				],
				'g'     => [ 'fill' => true ],
				'title' => [ 'title' => true ],
				'path'  => [
					'd'    => true,
					'fill' => true,
				],
			];

			$allowed_tags = array_merge( $kses_defaults, $svg_args );

			return $allowed_tags;
		}

		/**
		 * Default options for social share module.
		 *
		 * @since 2.3.5
		 * @return array
		 */
		public function social_share_default_options() {

			$social_share_default_options = [
				'enable'                 => 0,
				'channels'               => [
					[
						'id'         => 'facebook',
						'name'       => 'Facebook',
						'visibility' => [
							'desktop' => 1,
							'mobile'  => 1,
						],
					],
					[
						'id'         => 'twitter',
						'name'       => 'Twitter',
						'visibility' => [
							'desktop' => 1,
							'mobile'  => 1,
						],
					],
					[
						'id'         => 'copy',
						'name'       => 'Copy',
						'visibility' => [
							'desktop' => 1,
							'mobile'  => 1,
						],
					],
				],
				'channel_visibility'     => 3,
				'button_template'        => 1,
				'share_via_label'        => 'Sharing is Caring:',
				'shares_label'           => 'Shares',
				'more_label'             => 'More',
				'button_position'        => 'below',
				'button_alignment'       => 'left',
				'button_shape'           => 'rounded',
				'button_size'            => '1.2',
				'button_label'           => 'both',
				'hide_button_on'         => [
					'mobile'  => 0,
					'desktop' => 0,
				],
				'post_types'             => [ 'post', 'page' ],
				'button_spacing'         => 1,
				'show_total_share_count' => 0,
				'minimum_share_count'    => 0,
				'maximum_click_count'    => 30,
				'saved'                  => 0,
			];

			return apply_filters( 'wpdm_social_share_social_share_default_options', $social_share_default_options );
		}

		/**
		 * Get saved options for social share module from database.
		 *
		 * @since 2.3.5
		 * @return object
		 */
		public function social_share_options() {
			$options = $this->social_share_default_options();

			$option_values = array_map( function ( $key ) use ( $options ) {
				return $this->get_section_values( $key, $options );
			}, array_keys( $options ) );

			return (object) array_combine( array_keys( $options ), $option_values );
		}


		/**
		 * Get section values.
		 *
		 * @since 5.0.0
		 * @return array
		 */
		public function get_section_values( $key, $options ) {
			$value = get_option( 'wpdm_social_share_' . $key );
			if ( null === $value ) {
				return $options[ $key ];
			}
			if ( 'hide_button_on' === $key ) {
				$value['mobile'] = wp_validate_boolean( $value['mobile'] );
				$value['desktop'] = wp_validate_boolean( $value['desktop'] );
			}
			return $value;
		}

		/**
		 * Contains available channels.
		 *
		 * @since 2.3.5
		 * @return array
		 */
		public function channels() {
			$channels = [
				[
					'id'   => 'facebook',
					'name' => 'Facebook',
					// phpcs:ignore
					'svg'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30" ><path d="M12,27V15H8v-4h4V8.852C12,4.785,13.981,3,17.361,3c1.619,0,2.475,0.12,2.88,0.175V7h-2.305C16.501,7,16,7.757,16,9.291V11 h4.205l-0.571,4H16v12H12z"></path></svg>',
				],
				[
					'id'   => 'twitter',
					'name' => 'X',
					// phpcs:ignore
					'svg'  => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-twitter-x" viewBox="0 0 16 16">
					<path d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865z"/>
				  </svg>',
					'tags' => [ 'twitter' ],
				],
				[
					'id'   => 'pinterest',
					'name' => 'Pinterest',
					// phpcs:ignore
					'svg'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path d="M8 0a8 8 0 0 0-2.915 15.452c-.07-.633-.134-1.606.027-2.297.146-.625.938-3.977.938-3.977s-.239-.479-.239-1.187c0-1.113.645-1.943 1.448-1.943.682 0 1.012.512 1.012 1.127 0 .686-.437 1.712-.663 2.663-.188.796.4 1.446 1.185 1.446 1.422 0 2.515-1.5 2.515-3.664 0-1.915-1.377-3.254-3.342-3.254-2.276 0-3.612 1.707-3.612 3.471 0 .688.265 1.425.595 1.826a.24.24 0 0 1 .056.23c-.061.252-.196.796-.222.907-.035.146-.116.177-.268.107-1-.465-1.624-1.926-1.624-3.1 0-2.523 1.834-4.84 5.286-4.84 2.775 0 4.932 1.977 4.932 4.62 0 2.757-1.739 4.976-4.151 4.976-.811 0-1.573-.421-1.834-.919l-.498 1.902c-.181.695-.669 1.566-.995 2.097A8 8 0 1 0 8 0z"/></svg>',
				],
				[
					'id'   => 'reddit',
					'name' => 'Reddit',
					// phpcs:ignore
					'svg'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path d="M6.167 8a.831.831 0 0 0-.83.83c0 .459.372.84.83.831a.831.831 0 0 0 0-1.661zm1.843 3.647c.315 0 1.403-.038 1.976-.611a.232.232 0 0 0 0-.306.213.213 0 0 0-.306 0c-.353.363-1.126.487-1.67.487-.545 0-1.308-.124-1.671-.487a.213.213 0 0 0-.306 0 .213.213 0 0 0 0 .306c.564.563 1.652.61 1.977.61zm.992-2.807c0 .458.373.83.831.83.458 0 .83-.381.83-.83a.831.831 0 0 0-1.66 0z"/><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.828-1.165c-.315 0-.602.124-.812.325-.801-.573-1.9-.945-3.121-.993l.534-2.501 1.738.372a.83.83 0 1 0 .83-.869.83.83 0 0 0-.744.468l-1.938-.41a.203.203 0 0 0-.153.028.186.186 0 0 0-.086.134l-.592 2.788c-1.24.038-2.358.41-3.17.992-.21-.2-.496-.324-.81-.324a1.163 1.163 0 0 0-.478 2.224c-.02.115-.029.23-.029.353 0 1.795 2.091 3.256 4.669 3.256 2.577 0 4.668-1.451 4.668-3.256 0-.114-.01-.238-.029-.353.401-.181.688-.592.688-1.069 0-.65-.525-1.165-1.165-1.165z"/></svg>',
				],
				[
					'id'   => 'copy',
					'name' => 'Copy',
					'svg'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
					<path d="M6.5 0A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3Zm3 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3Z"/>
					<path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1A2.5 2.5 0 0 1 9.5 5h-3A2.5 2.5 0 0 1 4 2.5v-1Zm4.5 6V9H10a.5.5 0 0 1 0 1H8.5v1.5a.5.5 0 0 1-1 0V10H6a.5.5 0 0 1 0-1h1.5V7.5a.5.5 0 0 1 1 0Z"/>
					</svg>',
				],
				[
					'id'   => 'linkedin',
					'name' => 'LinkedIn',
					'icon' => 'fa-brands fa-linkedin',
					// phpcs:ignore
					'svg'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path d="M0 1.146C0 .513.526 0 1.175 0h13.65C15.474 0 16 .513 16 1.146v13.708c0 .633-.526 1.146-1.175 1.146H1.175C.526 16 0 15.487 0 14.854V1.146zm4.943 12.248V6.169H2.542v7.225h2.401zm-1.2-8.212c.837 0 1.358-.554 1.358-1.248-.015-.709-.52-1.248-1.342-1.248-.822 0-1.359.54-1.359 1.248 0 .694.521 1.248 1.327 1.248h.016zm4.908 8.212V9.359c0-.216.016-.432.08-.586.173-.431.568-.878 1.232-.878.869 0 1.216.662 1.216 1.634v3.865h2.401V9.25c0-2.22-1.184-3.252-2.764-3.252-1.274 0-1.845.7-2.165 1.193v.025h-.016a5.54 5.54 0 0 1 .016-.025V6.169h-2.4c.03.678 0 7.225 0 7.225h2.4z"/></svg>',
				],
				[
					'id'   => 'telegram',
					'name' => 'Telegram',

					// phpcs:ignore
					'svg'  => '<svg fill="#000000" xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 50 50" width="50px" height="50px"><path d="M46.137,6.552c-0.75-0.636-1.928-0.727-3.146-0.238l-0.002,0C41.708,6.828,6.728,21.832,5.304,22.445	c-0.259,0.09-2.521,0.934-2.288,2.814c0.208,1.695,2.026,2.397,2.248,2.478l8.893,3.045c0.59,1.964,2.765,9.21,3.246,10.758	c0.3,0.965,0.789,2.233,1.646,2.494c0.752,0.29,1.5,0.025,1.984-0.355l5.437-5.043l8.777,6.845l0.209,0.125	c0.596,0.264,1.167,0.396,1.712,0.396c0.421,0,0.825-0.079,1.211-0.237c1.315-0.54,1.841-1.793,1.896-1.935l6.556-34.077	C47.231,7.933,46.675,7.007,46.137,6.552z M22,32l-3,8l-3-10l23-17L22,32z"/></svg>',
				],
				[
					'id'   => 'whatsapp',
					'name' => 'WhatsApp',
					// phpcs:ignore
					'svg'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z"/></svg>',
				],
				[
					'id'   => 'email',
					'name' => 'Email',
					// phpcs:ignore
					// phpcs:ignore
					'svg'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path d="M.05 3.555A2 2 0 0 1 2 2h12a2 2 0 0 1 1.95 1.555L8 8.414.05 3.555ZM0 4.697v7.104l5.803-3.558L0 4.697ZM6.761 8.83l-6.57 4.027A2 2 0 0 0 2 14h12a2 2 0 0 0 1.808-1.144l-6.57-4.027L8 9.586l-1.239-.757Zm3.436-.586L16 11.801V4.697l-5.803 3.546Z"/></svg>',
				],
				[
					'id'   => 'print',
					'name' => 'Print',
					// phpcs:ignore
					'svg'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path d="M0 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-1v-2a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2H2a2 2 0 0 1-2-2V7zm2.5 1a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/></svg>',
				],
				[
					'id'   => 'messenger',
					'name' => 'Messenger',
					// phpcs:ignore
					'svg'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path d="M0 7.76C0 3.301 3.493 0 8 0s8 3.301 8 7.76-3.493 7.76-8 7.76c-.81 0-1.586-.107-2.316-.307a.639.639 0 0 0-.427.03l-1.588.702a.64.64 0 0 1-.898-.566l-.044-1.423a.639.639 0 0 0-.215-.456C.956 12.108 0 10.092 0 7.76zm5.546-1.459-2.35 3.728c-.225.358.214.761.551.506l2.525-1.916a.48.48 0 0 1 .578-.002l1.869 1.402a1.2 1.2 0 0 0 1.735-.32l2.35-3.728c.226-.358-.214-.761-.551-.506L9.728 7.381a.48.48 0 0 1-.578.002L7.281 5.98a1.2 1.2 0 0 0-1.735.32z"/></svg>',
				],
				[
					'id'   => 'instagram',
					'name' => 'Instagram',
					// phpcs:ignore
					'svg'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.917 3.917 0 0 0-1.417.923A3.927 3.927 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.916 3.916 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.926 3.926 0 0 0-.923-1.417A3.911 3.911 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0h.003zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599.28.28.453.546.598.92.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.47 2.47 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.478 2.478 0 0 1-.92-.598 2.48 2.48 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233 0-2.136.008-2.388.046-3.231.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92.28-.28.546-.453.92-.598.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045v.002zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92zm-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217zm0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334z"/></svg>',
				],
				[
					'id'   => 'sms',
					'name' => 'SMS',
					'svg'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
					<path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4.414a1 1 0 0 0-.707.293L.854 15.146A.5.5 0 0 1 0 14.793V2zm5 4a1 1 0 1 0-2 0 1 1 0 0 0 2 0zm4 0a1 1 0 1 0-2 0 1 1 0 0 0 2 0zm3 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
					</svg>',
				],
				[
					'id'   => 'tumblr',
					'name' => 'Tumblr',
					'icon' => 'fa-brands fa-tumblr',
					// phpcs:ignore
					'svg'  => '<svg xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 24 24"><path d="M19.512 17.489l-.096-.068h-3.274c-.153 0-.16-.467-.163-.622v-5.714c0-.056.045-.101.101-.101h3.82c.056 0 .101-.045.101-.101v-5.766c0-.055-.045-.1-.101-.1h-3.803c-.055 0-.1-.045-.1-.101v-4.816c0-.055-.045-.1-.101-.1h-7.15c-.489 0-1.053.362-1.135 1.034-.341 2.778-1.882 4.125-4.276 4.925l-.267.089-.068.096v4.74c0 .056.045.101.1.101h2.9v6.156c0 4.66 3.04 6.859 9.008 6.859 2.401 0 5.048-.855 5.835-1.891l.157-.208-1.488-4.412zm.339 4.258c-.75.721-2.554 1.256-4.028 1.281l-.165.001c-4.849 0-5.682-3.701-5.682-5.889v-7.039c0-.056-.045-.101-.1-.101h-2.782c-.056 0-.101-.045-.101-.101l-.024-3.06.064-.092c2.506-.976 3.905-2.595 4.273-5.593.021-.167.158-.171.159-.171h3.447c.055 0 .101.045.101.101v4.816c0 .056.045.101.1.101h3.803c.056 0 .101.045.101.1v3.801c0 .056-.045.101-.101.101h-3.819c-.056 0-.097.045-.097.101v6.705c.023 1.438.715 2.167 2.065 2.167.544 0 1.116-.126 1.685-.344.053-.021.111.007.13.061l.995 2.95-.024.104z"/></svg>',
				],
				[
					'id'   => 'stumbleupon',
					'name' => 'StumbleUpon',
					// phpcs:ignore
					'svg'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M24 11.779c0-1.459-1.192-2.645-2.657-2.645-.715 0-1.363.286-1.84.746-1.81-1.191-4.259-1.949-6.971-2.046l1.483-4.669 4.016.941-.006.058c0 1.193.975 2.163 2.174 2.163 1.198 0 2.172-.97 2.172-2.163s-.975-2.164-2.172-2.164c-.92 0-1.704.574-2.021 1.379l-4.329-1.015c-.189-.046-.381.063-.44.249l-1.654 5.207c-2.838.034-5.409.798-7.3 2.025-.474-.438-1.103-.712-1.799-.712-1.465 0-2.656 1.187-2.656 2.646 0 .97.533 1.811 1.317 2.271-.052.282-.086.567-.086.857 0 3.911 4.808 7.093 10.719 7.093s10.72-3.182 10.72-7.093c0-.274-.029-.544-.075-.81.832-.447 1.405-1.312 1.405-2.318zm-17.224 1.816c0-.868.71-1.575 1.582-1.575.872 0 1.581.707 1.581 1.575s-.709 1.574-1.581 1.574-1.582-.706-1.582-1.574zm9.061 4.669c-.797.793-2.048 1.179-3.824 1.179l-.013-.003-.013.003c-1.777 0-3.028-.386-3.824-1.179-.145-.144-.145-.379 0-.523.145-.145.381-.145.526 0 .65.647 1.729.961 3.298.961l.013.003.013-.003c1.569 0 2.648-.315 3.298-.962.145-.145.381-.144.526 0 .145.145.145.379 0 .524zm-.189-3.095c-.872 0-1.581-.706-1.581-1.574 0-.868.709-1.575 1.581-1.575s1.581.707 1.581 1.575-.709 1.574-1.581 1.574z"/></svg>',
				],
				[
					'id'   => 'delicious',
					'name' => 'Delicious',
					// phpcs:ignore
					'svg'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M13.271 9.231l1.581.88 2.502-.846v-1.696c0-2.925-2.445-5.202-5.354-5.202-2.897 0-5.354 2.129-5.354 5.17v7.749c0 .702-.568 1.27-1.27 1.27s-1.271-.568-1.271-1.27v-3.284h-4.105v3.328c0 2.963 2.402 5.365 5.365 5.365 2.937 0 5.323-2.361 5.364-5.288v-7.653c0-.702.569-1.27 1.271-1.27s1.271.568 1.271 1.27v1.477zm6.624 2.772v3.437c0 .702-.569 1.27-1.271 1.27s-1.271-.568-1.271-1.27v-3.372l-2.502.847-1.581-.881v3.344c.025 2.941 2.418 5.317 5.364 5.317 2.963 0 5.365-2.402 5.365-5.365v-3.328h-4.104z"/></svg>',
				],
				[
					'id'   => 'evernote',
					'name' => 'Evernote',
					'svg'  => '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
							viewBox="0 0 300 300"><g id="XMLID_338_"><path id="XMLID_340_" d="M269.973,54.436c0-19.195-28.53-21.38-28.53-21.38l-66.983-4.304c0,0-1.391-18.531-14.961-24.952
							c-7.609-3.641-31.238-4.038-53.413-3.706c-5.097,0.065-9.2,4.234-9.2,9.332v42.625c0,6.95-5.625,12.576-12.576,12.576H43.865
							c-10.588,0-18.863,4.105-18.863,12.842c0,8.737,12.443,102.463,29.651,119.604c9.929,9.93,70.759,17.608,83.601,17.608
							c12.841,0,8.537-38.128,12.113-38.128c3.572,0,7.479,21.912,27.667,27.009c20.186,5.095,47.126,4.235,48.517,18.863
							c1.854,19.261,3.574,44.216-8.936,45.936l-28.199,1.19c-8.338,0-7.347-22.902-5.625-22.902c2.848,0,5.229-0.064,7.148-0.064
							c3.179-0.066,5.76-2.646,5.892-5.826l0.531-11.914c0.131-3.44-2.582-6.353-6.023-6.353c-12.313-0.2-38.459,2.249-39.846,25.417
							c-1.723,27.8,2.979,40.841,6.419,43.685c3.441,2.848,9.4,8.407,63.61,8.407C298.037,300,269.973,73.631,269.973,54.436
							L269.973,54.436z M232.838,146.77c-1.918,1.787-10.193-5.229-16.744-5.229c-6.555,0-12.18,5.36-13.901,3.311
							c-1.656-1.988,1.522-18.271,13.901-18.271C228.469,126.581,234.758,144.982,232.838,146.77L232.838,146.77z M232.838,146.77"/><path id="XMLID_351_" d="M73.586,7.838L40.094,42.256c-2.648,2.713-0.729,7.213,3.043,7.213h33.494
							c2.384,0,4.237-1.918,4.237-4.234V10.816C80.93,6.977,76.299,5.057,73.586,7.838L73.586,7.838z M73.586,7.838"/></g></svg>
					',
				],
				[
					'id'   => 'wordpress',
					'name' => 'WordPress',
					// phpcs:ignore
					'svg'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path d="M12.633 7.653c0-.848-.305-1.435-.566-1.892l-.08-.13c-.317-.51-.594-.958-.594-1.48 0-.63.478-1.218 1.152-1.218.02 0 .039.002.058.003l.031.003A6.838 6.838 0 0 0 8 1.137 6.855 6.855 0 0 0 2.266 4.23c.16.005.313.009.442.009.717 0 1.828-.087 1.828-.087.37-.022.414.521.044.565 0 0-.371.044-.785.065l2.5 7.434 1.5-4.506-1.07-2.929c-.369-.022-.719-.065-.719-.065-.37-.022-.326-.588.043-.566 0 0 1.134.087 1.808.087.718 0 1.83-.087 1.83-.087.37-.022.413.522.043.566 0 0-.372.043-.785.065l2.48 7.377.684-2.287.054-.173c.27-.86.469-1.495.469-2.046zM1.137 8a6.864 6.864 0 0 0 3.868 6.176L1.73 5.206A6.837 6.837 0 0 0 1.137 8z"/><path d="M6.061 14.583 8.121 8.6l2.109 5.78c.014.033.03.064.049.094a6.854 6.854 0 0 1-4.218.109zm7.96-9.876c.03.219.047.453.047.706 0 .696-.13 1.479-.522 2.458l-2.096 6.06a6.86 6.86 0 0 0 2.572-9.224z"/><path fill-rule="evenodd" d="M0 8c0-4.411 3.589-8 8-8 4.41 0 8 3.589 8 8s-3.59 8-8 8c-4.411 0-8-3.589-8-8zm.367 0c0 4.209 3.424 7.633 7.633 7.633 4.208 0 7.632-3.424 7.632-7.633C15.632 3.79 12.208.367 8 .367 3.79.367.367 3.79.367 8z"/></svg>',
				],
				[
					'id'   => 'pocket',
					'name' => 'Pocket',
					'icon' => 'fa-brands fa-get-pocket',
					// phpcs:ignore
					'svg'  => '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><path d="M 7 5 C 5.355469 5 4 6.355469 4 8 L 4 15 C 4 21.617188 9.382813 27 16 27 C 22.617188 27 28 21.617188 28 15 L 28 8 C 28 6.355469 26.644531 5 25 5 Z M 7 7 L 25 7 C 25.566406 7 26 7.433594 26 8 L 26 15 C 26 20.535156 21.535156 25 16 25 C 10.464844 25 6 20.535156 6 15 L 6 8 C 6 7.433594 6.433594 7 7 7 Z M 10.65625 11.40625 C 10.273438 11.40625 9.886719 11.582031 9.59375 11.875 C 9.007813 12.460938 9.007813 13.382813 9.59375 13.96875 L 15 19.375 C 15.28125 19.65625 15.664063 19.8125 16.0625 19.8125 C 16.460938 19.8125 16.84375 19.65625 17.125 19.375 L 22.40625 14.125 C 22.992188 13.539063 22.992188 12.585938 22.40625 12 C 21.820313 11.414063 20.867188 11.414063 20.28125 12 L 16.0625 16.21875 L 11.71875 11.875 C 11.425781 11.582031 11.039063 11.40625 10.65625 11.40625 Z"/></svg>',
				],
			];

			return apply_filters( 'wpdm_social_share_channels', $channels );
		}


		/**
		 * Registers admin menu for social share settings page in WP Dark Mode.
		 *
		 * @since 2.3.5
		 */
		public function social_share_admin_menu() {

			add_submenu_page(
				'wp-dark-mode',
				__( 'Social Share - WP Dark Mode', 'wp-dark-mode' ),
				__( 'Social Share', 'wp-dark-mode' ),
				'manage_options',
				'wp-dark-mode-social-share',
				[ $this, 'render_social_share' ],
				1
			);
		}

		/**
		 * Social share settings, template for reset
		 *
		 * @return void
		 * @version 1.0.0
		 */
		public function render_social_share() {
			$this->render_template( 'admin/social-share/base' );
		}

		/**
		 * Loads on the admin head.
		 *
		 * @since 2.3.5
		 */
		public function admin_head() {
			$this->wp_head();
			?>
			<style>
				._wpdm-social-share-admin-menu {
					display: flex;
					align-items: justify-between;
					gap: 5px;
				}

				._wpdm-social-share-new-badge {
					display: inline-flex;
					align-items: center;
					justify-content: center;
					gap: 2px;
					padding: 1.6px 6px;
					border-radius: 50px;
					/* background: #f5621d; */
					color: white;
					white-space: nowrap;
					font-size: 11px;
					font-weight: 600;

					background: linear-gradient(180deg, #EE5913 0%, #FF6F2C 100%); 
				}

				._wpdm-social-share-new-badge svg {
					width: 11px;
					height: 11px;
					fill: currentColor;
				}
			</style>
			<?php
		}

		/**
		 * Loads style on the admin header.
		 *
		 * @since 2.3.5
		 */
		public function wp_head() {
			echo '<style id="social-share-root">
				:root {
					--wp-dark-mode-social-share-scale: ' . esc_html( get_option( 'wpdm_social_share_button_size', 1.2 ) ) . ';
				}
				._fixed-size {
					--wp-dark-mode-social-share-scale: 1.2 !important;
				}
				._fixed-size-large {
					--wp-dark-mode-social-share-scale: 1.4 !important;
				}
			</style>';
		}


		/**
		 * Enqueues scripts and styles on the admin page only.
		 *
		 * @param null|object $hook The page hook.
		 * @since 2.3.5
		 */
		public function admin_enqueue_scripts( $hook = null ) {
			if ( 'wp-dark-mode_page_wp-dark-mode-social-share' !== $hook ) {
				return;
			}

			wp_enqueue_media();
			wp_enqueue_style( 'wp-dark-mode-social-share', WP_DARK_MODE_ASSETS . '/css/admin-social-share.min.css', [], WP_DARK_MODE_VERSION );
			wp_enqueue_script( 'wp-dark-mode-social-share', WP_DARK_MODE_ASSETS . '/js/admin-social-share.min.js', [ 'jquery' ], WP_DARK_MODE_VERSION, true );

			// Localize script.
			wp_localize_script(
				'wp-dark-mode-social-share',
				'wp_dark_mode_social_share',
				[
					'ajax_url'    => admin_url( 'admin-ajax.php' ),
					'nonce'       => wp_create_nonce( 'wp_dark_mode_social_share' ),
					'options'     => $this->social_share_options(),
					'is_pro'      => $this->is_ultimate(),
					'is_ultimate'      => $this->is_ultimate(),
					'post_types'  => array_map( function ( $post_type ) {
						return [
							'id'            => $post_type->name,
							'name'          => $post_type->label,
							'singular_name' => $post_type->labels->singular_name,
						];
					}, get_post_types( [ 'public' => true ], 'objects' ) ),
					'channels'    => $this->channels(),
				]
			);

			// If social share is enabled, enqueue script.
			if ( true === wp_validate_boolean( get_option( 'wpdm_social_share_enable', false ) ) ) {

				// Frontend scripts.
				wp_enqueue_script( 'wp-dark-mode-social-share', WP_DARK_MODE_ASSETS . '/js/social-share-enable.min.js', [ 'jquery' ], WP_DARK_MODE_VERSION, true );
			}
		}

		/**
		 * Enqueues scripts and styles on the frontend.
		 *
		 * @return void
		 */
		public function wp_enqueue_scripts() {
			wp_enqueue_style( 'wp-dark-mode-social-share', WP_DARK_MODE_ASSETS . '/css/social-share.min.css', [], WP_DARK_MODE_VERSION );
			wp_enqueue_script( 'wp-dark-mode-social-share', WP_DARK_MODE_ASSETS . '/js/social-share.min.js', [ 'jquery' ], WP_DARK_MODE_VERSION, true );

			$options = $this->social_share_options();

			// Localize script.
			wp_localize_script(
				'wp-dark-mode-social-share',
				'wp_dark_mode_social_share',
				[
					'ajax_url'    => admin_url( 'admin-ajax.php' ),
					'nonce'       => wp_create_nonce( 'wp_dark_mode_social_share' ),
					'options'     => $options,
					'is_pro'      => $this->is_ultimate(),
					'is_ultimate' => $this->is_ultimate(),
					'permalink'   => get_permalink(),
					'post_id'     => get_the_ID(),
					'title'       => get_the_title(),
					'description' => get_the_excerpt(),
					'labels'      => [
						'copied' => apply_filters( 'wpdm_social_share_label_copied', 'Copied' ),
					],
				]
			);
		}

		/**
		 * Ajax handler for saving settings
		 *
		 * @return void
		 * @version 1.0.0
		 */
		public function wpdm_social_share_save_options() {
			$inputs = file_get_contents( 'php://input' );

			/**
			 * Sanitize inputs.
			 */
			$inputs = sanitize_text_field( $inputs );
			$inputs = json_decode( $inputs, true );

			/**
			 * Check nonce
			 */
			if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $inputs['nonce'] ) ), 'wp_dark_mode_social_share' ) ) {
				wp_send_json_error( __( 'Invalid nonce', 'wp-dark-mode' ) );
			}

			// Bail, if not admin.
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( __( 'Permission denied', 'wp-dark-mode' ) );
			}

			$options = $inputs['options'];

			foreach ( $options as $key => $value ) {
				$value = $this->recursive_sanitizer( $value );
				update_option( 'wpdm_social_share_' . $key, $value );
			}

			wp_send_json_success( $options );
		}

		/**
		 * Recursive sanitizer.
		 *
		 * @param array|string $value Value to sanitize.
		 * @return array|string
		 */
		public function recursive_sanitizer( $value ) {
			if ( is_array( $value ) ) {
				foreach ( $value as $key => $val ) {
					$value[ $key ] = $this->recursive_sanitizer( $val );
				}
			} else {
				$value = sanitize_text_field( $value );
			}

			return $value;
		}

		/**
		 * Represents settings option names.
		 * Filter
		 *
		 * @param array $option_keys Options keys collection.
		 * @since 2.3.5
		 */
		public function wpdm_settings_option_names( $option_keys ) {
			$social_share_options = $this->social_share_default_options();
			$keys                 = array_keys( $social_share_options );

			$key_formatized = array_map( function ( $key ) {
				return 'wpdm_social_share_' . $key;
			}, $keys );

			return array_merge( $option_keys, $key_formatized );
		}

		/**
		 * Get social share buttons.
		 *
		 * @param null|array $options Options container.
		 * @return mixed
		 */
		public function get_social_share_buttons( $options = null ) {
			global $social_share;

			$social_share = (object) array_merge( (array) $social_share, $options );
			$content      = '';

			// Assign template to content.
			ob_start();
			include WP_DARK_MODE_PATH . '/templates/frontend/social-share.php';
			$content = ob_get_clean();

			return $content;
		}

		/**
		 * After post content.
		 *
		 * @param string $content Post content.
		 * @since 2.4.5
		 */
		public function the_content( $content ) {

			// If frontend.
			if ( ! is_singular() ) {
				return $content;
			}

			// If wp_trim_excerpt is called.
			if ( did_action( 'wp_trim_excerpt' ) ) {
				return $content;
			}

			global $social_share;

			$post_types = $social_share->post_types;

			// If post type is not enabled, return content.
			if ( ! $post_types || ! is_array( $post_types ) || ! in_array( get_post_type(), $post_types, false ) ) {
				return $content;
			}

			$post_id   = get_the_ID();
			$permalink = get_permalink();

			$options = [
				'post_id'   => $post_id,
				'permalink' => $permalink,
			];

			$template        = $this->get_social_share_buttons( $options );
			$button_position = $social_share->button_position;

			if ( 'both' === $button_position ) {
				$content = $template . $content . $template;
			} elseif ( 'above' === $button_position ) {
				$content = $template . $content;
			} elseif ( 'below' === $button_position ) {
				$content = $content . $template;
			}

			return $content;
		}

		/**
		 * Get social share count from database.
		 *
		 * @param string $channel   The channel name.
		 * @param number $permalink The Permalink.
		 *
		 * @return int|null
		 */
		public static function get_social_share_count( $channel, $permalink ) {
			global $wpdb;
			$table_name = $wpdb->prefix . 'wpdm_social_shares';

			$post_id = $permalink;

			if ( is_int( $permalink ) ) {
				$permalink = get_permalink( $post_id );
			} else {
				$post_id = url_to_postid( $permalink );
			}

			$shares = $wpdb->get_var( // phpcs:ignore.
				$wpdb->prepare(
					'SELECT COUNT(ID) as count FROM `%s` WHERE channel = %s AND (url = %s OR post_id = %d)',
					$table_name,
					$channel,
					$permalink,
					$post_id
				)
			);

			return $shares;
		}

		/**
		 * Get share count by url
		 *
		 * @param string $url The shared url.
		 * @return array
		 */
		public static function get_share_count_by_url( $url ) {
			global $wpdb;

			$count = $wpdb->get_results( // phpcs:ignore.
				$wpdb->prepare( "SELECT COUNT(ID) as total, channel FROM {$wpdb->prefix}wpdm_social_shares WHERE `url` = %s GROUP BY channel", $url )
			);

			return array_values( $count );
		}

		/**
		 * Social share counter.
		 *
		 * @return void
		 */
		public function wpdm_social_share_counter() {

			$inputs = file_get_contents( 'php://input' );
			$inputs = sanitize_text_field( $inputs );
			$inputs = json_decode( $inputs, true );
			$nonce  = sanitize_text_field( wp_unslash( $inputs['nonce'] ) );

			// Verify nonce.
			if ( ! wp_verify_nonce( $nonce, 'wp_dark_mode_social_share' ) ) {
				wp_send_json_error( __( 'Invalid permission', 'wp-dark-mode' ) );
				wp_die();
			}

			$channel = sanitize_text_field( $inputs['channel'] );

			if ( empty( $channel ) ) {
				wp_send_json_error( __( 'Invalid channel', 'wp-dark-mode' ) );
			}

			$url        = sanitize_text_field( $inputs['url'] );
			$post_id    = sanitize_text_field( $inputs['post_id'] );
			$user_agent = sanitize_text_field( $inputs['user_agent'] );
			$user_id    = get_current_user_id();

			global $wpdb;
			$table_name = $wpdb->prefix . 'wpdm_social_shares';

			// Insert.
			$last_id = $wpdb->insert( // phpcs:ignore.
				$table_name,
				[
					'channel'    => $channel,
					'url'        => $url,
					'post_id'    => $post_id,
					'user_agent' => $user_agent,
					'user_id'    => $user_id,
				]
			);

			$shares       = $this->get_share_count_by_url( $url );
			$total_shares = array_sum( array_column( $shares, 'total' ) );

			if ( null !== $last_id ) {
				wp_send_json_success([
					'channel' => $channel,
					'url'     => $url,
					'shares'  => $shares,
					'total'   => $total_shares,
				]);
			} else {
				wp_send_json_error( __( 'Something went wrong', 'wp-dark-mode' ) );
			}

			wp_die();
		}
	}

    SocialShare::init();
}