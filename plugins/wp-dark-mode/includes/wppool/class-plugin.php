<?php
/**
 * WPPOOL Plugin Class
 * Handles all the WPPOOL Plugin related functionalities, promotions, etc.
 *
 * * Data WILL BE ONLY SENT IF user allows to send data from Admin Notice manually.
 *
 * @package WPPOOL_PLUGIN
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit();

/**
 * Plugin Class
 */
if ( ! class_exists( 'WPPOOL_Plugin' ) ) {
	/**
	 * Handles all the WPPOOL Plugin related functionalities, promotions, etc.
	 *
	 * @version 3.0.0
	 */
	class WPPOOL_Plugin {
		/**
		 * Contains instance of Plugin.
		 *
		 * @since 3.0.0
		 * @var self
		 */
		private static $instance = null;

		/**
		 * Returns instance of Plugin.
		 *
		 * @since 3.0.0
		 * @return self
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Contains sdk version.
		 *
		 * @var string
		 */
		public $sdk_version = '3.2.1';

		/**
		 * Contains all public plugins from WPPOOL.
		 *
		 * @var array
		 */
		public $plugins = [
			'wp_dark_mode' => [
				'list_id' => 20,
				'button_link' => 'https://go.wppool.dev/LaSV',
				'button_text' => 'Get Premium',
				'color' => '#FF631A',
			],
			'sheets_to_wp_table_live_sync' => [
				'list_id' => 21,
				'button_link' => 'https://go.wppool.dev/Rimc',
				'button_text' => 'Get Premium',
				'color' => '#1AD26E',
			],
			'easy_video_reviews' => [
				'list_id' => 22,
				'button_text' => 'Get Premium',
				'color' => '#0288FD',
			],
			'jitsi_meet' => [
				'list_id' => 23,
				'button_link' => 'https://go.wppool.dev/8iQC',
				'button_text' => 'Get Premium',
				'color' => '#1D5AE4',
			],
			'zero_bs_accounting' => [
				'list_id' => 24,
				'button_link' => 'https://go.wppool.dev/zbs',
				'button_text' => 'Get Premium',
			],
			'stock_sync_with_google_sheet_for_woocommerce' => [
				'list_id' => 46,
				'button_link' => 'https://go.wppool.dev/dr8d',
				'button_text' => 'Get Premium',
				'color' => '#8F5CCB',
			],
			'stock_notifier_for_woocommerce' => [
				'list_id' => 47,
				'button_link' => 'https://go.wppool.dev/hiE1',
				'button_text' => 'Get Premium',
				'color' => '#3FC250',
			],
			'chat_widgets_for_multivendor_marketplaces' => [
				'list_id' => 26,
				'button_link' => 'https://go.wppool.dev/EiRM',
				'button_text' => 'Get Premium',
				'color' => '#CC22FF',
			],
			'omg_chat_widget' => [
				'list_id' => 26,
				'button_link' => 'https://go.wppool.dev/EiRM',
				'button_text' => 'Get Premium',
				'color' => '#CC22FF',
			],
			'social_contact_form' => [
				'list_id' => 49,
				'button_link' => 'https://go.wppool.dev/2rc7',
				'button_text' => 'Get Premium',
				'color' => '#DC4FF3',
			],
			'elementor_speed_optimizer' => [
				'list_id' => 54,
				'button_link' => 'https://go.wppool.dev/cyVx',
				'button_text' => 'Get Premium',
				'color' => '#C91170',
			],
			'easy_email_integration' => [
				'list_id' => 55,
				'button_text' => 'Get Premium',
			],
			'easy_cloudflare_trunstile' => [
				'list_id' => 56,
				'button_link' => 'https://go.wppool.dev/easycloudflare',
				'button_text' => 'Get Premium',
			],
			'order_sync_with_google_sheet_for_woocommerce' => [
				'list_id' => 65,
				'button_link' => 'https://go.wppool.dev/8aCD',
				'button_text' => 'Get Premium',
				'color' => '#6621ba',
			],
		];

		/**
		 * FluentCRM Tags ID
		 *
		 * @var array
		 */
		public $tags = [
			'free' => 11,
			'paid' => 12,
			'pro' => 13,
			'cancelled' => 23,
		];

		/**
		 * Contains CRM server endpoint. This is where we will send user data.
		 *
		 * @var string
		 */
		public $crm_endpoint = 'https://fluent.wppool.dev/wp-json/contact/sync';

		/**
		 * Contains CRM access token. This is used to authenticate the request.
		 *
		 * @var string
		 */
		public $crm_access_token = '66E6D9A59A5A948B';

		/**
		 * Contains plugin id.
		 *
		 * @var string
		 * @example wp_dark_mode
		 */
		public $plugin_id = 'wp_dark_mode';

		/**
		 * Temporarily stores user data. Like email, name, etc. Later it will be sent to CRM.
		 *
		 * @var array
		 */
		public $user_data = [];

		/**
		 * Returns the plugin list. This is used to get the plugin name, list id, etc.
		 *
		 * @return array
		 */
		public function get_plugins() {
			return apply_filters( 'wppool_plugins', $this->plugins );
		}

		/**
		 * Returns current plugin.
		 *
		 * @return mixed
		 */
		public function get_current_plugin() {
			$plugins = $this->get_plugins();

			return isset( $plugins[ $this->plugin_id ] ) ? $plugins[ $this->plugin_id ] : null;
		}

		/**
		 * Temporarily stores custom tags.
		 *
		 * @var array
		 */

		protected $custom_tags = [];

		/**
		 * Returns the tags.
		 *
		 * @return array
		 */
		public function get_tags() {
			return apply_filters( 'wppool_tags', array_merge( $this->tags, $this->custom_tags ) );
		}
		/**
		 * Temporarily stores custom lists.
		 *
		 * @var array
		 */
		protected $custom_lists = [];

		/**
		 * Returns the lists.
		 *
		 * @return array
		 */
		public function get_list_id() {
			$plugin = $this->get_current_plugin();

			if ( ! $plugin ) {
				return $this->custom_lists;
			}

			return array_merge( $this->custom_lists, [ $plugin['list_id'] ] );
		}

		/**
		 * Constructor.
		 *
		 * @param string $product_slug The product plugin_id.
		 * @param array  $user_data    User data.
		 */
		public function __construct(
			$product_slug = 'wp_dark_mode',
			$user_data = []
		) {
			$this->plugin_id = $this->slugify( $product_slug );
			$this->user_data = is_array( $user_data ) ? $user_data : [];
		}

		/**
		 * Returns the slugged plugin_id from the plugin name or title.
		 *
		 * @param  string $string The plugin_id.
		 * @return string
		 */
		public function slugify( $string = '' ) {
			$string = sanitize_title( $string );
			$string = str_replace( '-', '_', $string );

			return $string;
		}

		/**
		 * Initialize the SDK and add hooks.
		 *
		 * @return void
		 */
		public static function init_plugin_sdk() {
			$instance = new self();

			add_action( 'admin_enqueue_scripts', [ $instance, 'enqueue_scripts' ] );
			add_action( 'admin_footer', [ $instance, 'load_popup_template' ] );

			// Elementor support for popup.
			add_action( 'elementor/editor/after_enqueue_scripts', [ $instance, 'enqueue_scripts' ] );
			add_action( 'elementor/editor/header', [ $instance, 'load_popup_template' ] );
		}

		/**
		 * Get Default Popup Background Image.
		 *
		 * @return string
		 */
		public function get_image_url() {
			return apply_filters( 'wppool_popup_image', ( file_exists( __DIR__ . '/background-image.png' ) ? plugin_dir_url( __FILE__ ) . '/background-image.png' : '' ), $this->plugin_id );
		}

		/**
		 * Loads popup template.
		 *
		 * @return void
		 */
		public function load_popup_template() {
			?>
			<div class="_wppool-popup" id="_wppool-popup" style="display: none;" data-plugin="wp_dark_mode" tabindex="1">
				<div class="_wppool-popup-overlay"></div>
				<div class="_wppool-popup-modal">
					<!-- close  -->
					<div class="_wppool-popup-modal-close"> &times; </div>
					<!-- content section  -->
					<div class="_wppool-popup-modal-footer">
						<!-- countdown  -->
						<div class="_wppool-popup-countdown" style="display: none">
							<span class="_wppool-popup-countdown-text">
								<?php echo esc_html__( 'Deal Ends In', 'wp-dark-mode' ); ?>
							</span>
							<div class="_wppool-popup-countdown-time">
								<div>
									<span data-counter="days">
										<?php echo esc_html__( '00', 'wp-dark-mode' ); ?>
									</span>
									<span>
										<?php echo esc_html__( 'Days', 'wp-dark-mode' ); ?>
									</span>
								</div>
								<span>:</span>
								<div>
									<span data-counter="hours">
										<?php echo esc_html__( '00', 'wp-dark-mode' ); ?>
									</span>
									<span>
										<?php echo esc_html__( 'Hours', 'wp-dark-mode' ); ?>
									</span>
								</div>
								<span>:</span>
								<div>
									<span data-counter="minutes">
										<?php echo esc_html__( '00', 'wp-dark-mode' ); ?>
									</span>
									<span>
										<?php echo esc_html__( 'Minutes', 'wp-dark-mode' ); ?>
									</span>
								</div>
								<span>:</span>
								<div>
									<span data-counter="seconds">
										<?php echo esc_html__( '00', 'wp-dark-mode' ); ?>
									</span>
									<span>
										<?php echo esc_html__( 'Seconds', 'wp-dark-mode' ); ?>
									</span>
								</div>
							</div>
						</div>
						<!-- button  -->
						<a class="_wppool-popup-button" href="">
							<?php
							echo esc_html__(
								'Upgrade to Pro',
								'wp-dark-mode'
							);
							?>
						</a>
					</div>
				</div>
			</div>
			<?php
		}

		/**
		 * Get inline scripts.
		 *
		 * @return string
		 */
		public function get_inline_scripts() {
			return '(function() {

				if (typeof(WPPOOL) !== "undefined") {
					return;
				}

				const $container = jQuery("#_wppool-popup");
		
				// class Popup 
				class Popup {
					/**
					 * Plugin ID
					 *
					 * @type {string} 
					 */
					name = "wp_dark_mode"
		
					/**
					 * Events
					 *
					 * @type {object}
					 */
					events = {}
		
					/**
					 * Constructor
					 *
					 * @param {string} name
					 */
					constructor(name) {
						this.name = name;
					}
		
					/**
					 * Register Event
					 *
					 * @param {string} event
					 * @param {function} callback
					 */
					on(event, callback) {
						if (typeof(this.events[event]) === "undefined") {
							this.events[event] = [];
						}
		
						this.events[event].push(callback);
					}
		
					/**
					 * Trigger Event
					 *
					 * @param {string} event
					 * @param {array} args
					 */
					trigger(event, args = []) {
						if (typeof(this.events[event]) !== "undefined") {
							this.events[event].forEach(callback => {
								callback.apply(this, args);
							});
						}
					}

					/**
					 * Register events
					 *
					 * @return {void}
					 */
					registerEvents() {
						// close container on click overlay 
						jQuery(document).on("click", `[data-plugin="${this.name}"] ._wppool-popup-overlay`, (event) => {
							event.preventDefault();
							event.stopPropagation();
		
							this.trigger("overlayClick", [event, this.data]);
							this.hide();
						});
		
						// close container on click close button
						jQuery(document).on("click", `[data-plugin="${this.name}"] ._wppool-popup-modal-close`, (event) => {
							event.preventDefault();
							event.stopPropagation();
		
							this.trigger("closeClick", [event, this.data]);
							this.hide();
						});
		
						// on click on button 
						jQuery(document).on("click", `[data-plugin="${this.name}"] ._wppool-popup-button`, (event) => {
		
							event.preventDefault();
							event.stopPropagation();
		
							// trigger event close
							this.trigger("buttonClick", [this.data]);
		
							// close modal 
							this.hide();
		
							// navigate to data.button_link if button url is not empty 
							let url = this.data.button_link || null;
		
							// check the url is valid 
							if (url && url.length > 0) {
								// open url in new tab 
								window.open(url, "_blank");
							}
		
						});
		
						// on click on modal 
						jQuery(document).on("click", `[data-plugin="${this.name}"] ._wppool-popup-modal`, (event) => {
							event.stopPropagation();
		
							this.trigger("click", [this.data]);
						});
		
						// close on esc key 
						// close modal on press esc key 
						jQuery(document).on("keyup", (e) => {
							if (e.keyCode == 27) {
								this.hide();
							}
						});
					}
		
					/**
					 * Destroy events
					 *
					 * @return {void}
					 */
					destroyEvents() {
						jQuery(document).off("click", `[data-plugin="${this.name}"] ._wppool-popup-overlay`);
						jQuery(document).off("click", `[data-plugin="${this.name}"] ._wppool-popup-modal-close`);
						jQuery(document).off("click", `[data-plugin="${this.name}"] ._wppool-popup-button`);
						jQuery(document).off("click", `[data-plugin="${this.name}"] ._wppool-popup-modal`);
						jQuery(document).off("keyup");
					}
		
					/**
					 * To Slug
					 *
					 * @param {string} str
					 * @return {string}
					 */
					toSlug(str) {
						return str.toLowerCase().replace(/ /g, "_").replace(/[^\w-]+/g, "");
					}
		
					/**
					 * Get Plugin Data
					 * 
					 * @return {object}
					 */
					get data() {
						const plugin_data = this.name in WPPOOL_Plugins.plugins ? WPPOOL_Plugins.plugins[ this.name ] : null;
		
						plugin_data.name = this.name || null;
		
						plugin_data.background_image = plugin_data.background_image || null;
						// button_text
						plugin_data.button_text = plugin_data.button_text || null;
						// button_link
						plugin_data.button_link = plugin_data.button_link || null;
						// counter from
						plugin_data.from = plugin_data.from || null;
						// counter to
						plugin_data.to = plugin_data.to || null;
		
						return plugin_data;
					}
		
					/**
					 * Show Popup
					 *
					 * @return {void}
					 */
					show() {
						if (!this.data) return;
						this.setPopupData(this.data);
						this.registerEvents();

						// Init counter 
						if(this.data.to) {
							$container.find("._wppool-popup-countdown").show(0);
							this.initCounter(this.data.to);
						}
		
						// Show container
						$container.fadeIn(100);
		
						// trigger event 
						this.trigger("show", [this.data]);
					}
		
					/**
					 * Close
					 *
					 * @return {void}
					 */
					hide() {
						$container.fadeOut(100);
						// trigger event close
						this.trigger("hide", [this.data]);
		
						this.destroyEvents();
					}
		
					/**
					 * Checks if client in Online 
					 *
					 * @return {Boolean} true if client is online
					 */
					get isOnline() {
						return window.navigator.onLine;
					}
		
					/**
					 * Set Popup Data
					 *
					 * @param {object} data
					 */
					setPopupData(data) {
		
						// Change background image if found.
						if ( "background_image" in data && data.background_image && data.background_image.length && this.isOnline) {
							// Change background image.
							$container.find("._wppool-popup-modal").css({
								"background-image": `url(${data.background_image || ""})`
							});
		
							// Check if the image url is valid image url online.
							const fallback_image_url = "' . esc_url( $this->get_image_url() ) . '";
		
							if (data.background_image && data.background_image.length > 0) {
								// check if the image url is valid image url online                     
								fetch(data.background_image).then(response => {
									if (!response.ok) {
										$container.find("._wppool-popup-modal").css({
											"background-image": `url(${fallback_image_url})`
										});
									}
								}).catch(error => {
									// set default image 
									$container.find("._wppool-popup-modal").css({
										"background-image": `url(${fallback_image_url})`
									});
								});
							}
						}
		
						// set button text 
						$container.find("._wppool-popup-button").text(data.button_text || "GET NOW");
		
						// set button link
						$container.find("._wppool-popup-button").attr("href", data.button_link || "");

						if ( data.button_link ) {
							$container.find("._wppool-popup-button").attr("target", "_blank");
						}
		
						// set popup color
						$container.find("._wppool-popup-modal").css({
							"--wppool-popup-color": data.color || "#FF631A"
						});
		
						// set data plugin 
						$container.attr("data-plugin", this.name);
		
						// focus data-plugin 
						$container.find("[data-plugin]").focus();
					}
		
					/**
					 * Update Counter 
					 * @param {string} time  
					 */
					updateCounter(seconds) {
						const $counter = $container.find("._wppool-popup-countdown-time");
						const $days = $counter.find("[data-counter=\"days\"]");
						const $hours = $counter.find("[data-counter=\"hours\"]");
						const $minutes = $counter.find("[data-counter=\"minutes\"]");
						const $seconds = $counter.find("[data-counter=\"seconds\"]");
		
						const days = Math.floor(seconds / (3600 * 24));
						seconds -= days * 3600 * 24;
						const hrs = Math.floor(seconds / 3600);
						seconds -= hrs * 3600;
						const mnts = Math.floor(seconds / 60);
						seconds -= mnts * 60;
		
						$days.text(days);
						$hours.text(hrs);
						$minutes.text(mnts);
						$seconds.text(seconds);
					}
		
					/**
					 * initCounter
					 */
					initCounter(last_date) {
						const countdown = () => {
		
							// system time 
							const now = new Date().getTime();
		
							// set end time to 11:59:59 PM 
							const endDate = new Date(last_date);
							endDate.setHours(23);
							endDate.setMinutes(59);
							endDate.setSeconds(59);
		
							const seconds = Math.floor((endDate.getTime() - now) / 1000);
		
							if (seconds < 0) {
								return false;
							}
		
							this.updateCounter(seconds);
		
							return true;
						}
		
						let result = countdown();
		
						if (result) {
							this.trigger("countdownStart", [this.data]);
						} else {
							this.trigger("countdownFinish", [this.data]);
							$container.find("._wppool-popup-countdown").hide(0);
						}
		
						// update counter every 1 second 
						const counter = setInterval(() => {
		
							const result = countdown();
		
							if (!result) {
								clearInterval(counter);
								this.trigger("counter_end", [this.data]);
								$container.find("._wppool-popup-countdown").hide(0);
							}
		
						}, 1000);
					}
		
					/**
					 * Update counter
					 * @param {int} days
					 * @param {int} hours
					 * @param {int} minutes
					 * @param {int} seconds
					 */
				}
		
				// Big Object Theory for WPPOOL
				var WPPOOL = {
		
					/**
					 * Plugin 
					 * @param String plugin 
					 */
					Popup: function(name = "") {
						if (name) {
							return new Popup(name);
						}
		
						return false;
					},
		
					/**
					 * Plugin 
					 * @param String plugin 
					 */
					Plugin: function(name = "") {
						return this.Popup(name);
					},
		
					/**
					 * Debug log
					 */
					Log: function() {
						if (typeof(WPPOOL_Plugins) === "undefined" || WPPOOL_Plugins.debug != 1) return;
		
						let args = Array.from(arguments);
						console.log(
							"%cwppool",
							"background: #0080ca; color: white; font-size: 9px; padding: 2px 4px; border-radius: 2px;",
							...args
						);
					}
				}
		
				// make WPPOOL global 
		
				window.WPPOOL = WPPOOL;
		
			})(jQuery)';
		}

		/**
		 * Get inline styles.
		 *
		 * @return string
		 */
		public function get_inline_styles() {
			$css =
				':root {
				--wppool-popup-color: #FF631A;
			}
		
			._wppool-popup * {
				all: initial;
				font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Oxygen, Ubuntu, Cantarell, \'Open Sans\', \'Helvetica Neue\', sans-serif;
			}
		
			._wppool-popup {
				position: fixed;
				width: 100%;
				height: 100%;
				padding: 0;
				margin: 0;
				border: 0;
				top: 0;
				left: 0;
				display: flex;
				align-items: center;
				justify-content: center;
				z-index: 99999999 !important;
			}
		
		
			._wppool-popup-overlay {
				position: absolute;
				top: 0;
				left: 0;
				width: 100%;
				height: 100%;
				background: rgba(0, 0, 0, 0.2);
			}
		
			._wppool-popup-modal {
				width: 600px;
				max-width: 600px !important;
				height: 600px;
				max-height: 600px !important;
				color: white;
				background: #222 url(' . esc_url( $this->get_image_url() ) . ') no-repeat center center;
				background-position: center;
				background-size: cover;
				background-repeat: no-repeat;
				padding: 0;
				margin: 0;
				transform: scale(0.9);
				display: flex;
				align-items: flex-end;
				justify-content: center;
				border-radius: 3px;
				box-shadow: 0 0 10px 0 rgb(0 0 0 / 50%);
			}

			._wppool-popup-modal-close {
				position: absolute;
				top: 5px;
				right: 10px;
				font-size: 50px;
				cursor: pointer;
				color: var(--wppool-popup-color);
				transition: .3s;
				display: flex;
				justify-content: center;
				align-items: center;
				width: 40px;
				height: 40px;
				padding: 0;
				margin: 0;
				opacity: .5;
			}

			._wppool-popup-modal-close:hover {
				opacity: 1;
			}

			._wppool-popup-modal-footer {
				width: 100%;
				height: 225px;
				max-height: 225px !important;
				display: flex;
				flex-direction: column;
				align-items: center;
				justify-content: flex-end;
				padding: 15px 0;
			}

			._wppool-popup-countdown {
				display: flex;
				flex-direction: column;
				align-items: center;
				justify-content: space-evenly;
				gap: 10px;
			}

			._wppool-popup-countdown-text {
				font-size: 14px;
				font-weight: 600;
				color: white;
				position: relative;
				line-height: 1.4;
			}

			._wppool-popup-countdown-time {
				display: flex;
				align-items: center;
				justify-content: space-evenly;
				gap: 15px;
			}

			._wppool-popup-countdown-time>div {
				display: flex;
				flex-direction: column;
				align-items: center;
				justify-content: space-evenly;
				gap: 8px;
			}

			._wppool-popup-countdown-time>div>span {
				font-size: 20px;
				font-weight: 600;
				color: white;
			}

			._wppool-popup-countdown-time>div>span:nth-child(1) {
				border: 2px solid rgba(255, 255, 255, 0.6);
				height: 60px;
				width: 56px;
				font-size: 26px;
				display: flex;
				align-items: center;
				justify-content: center;
				border-radius: 5px;
			}

			._wppool-popup-countdown-time>div>span:nth-child(2) {
				font-size: 12px;
				font-weight: 500;
				color: rgb(255, 255, 255 / .8);
			}

			._wppool-popup-countdown-time>span {
				font-size: 50px;
				color: white;
				margin-top: -25px;
			}

			._wppool-popup-button {
				height: 50px;
				background: var(--wppool-popup-color);
				color: #222;
				font-size: 18px;
				font-weight: 600;
				letter-spacing: 0.5px;
				display: inline-flex;
				align-items: center;
				justify-content: center;
				border: 0;
				border-radius: 5px;
				cursor: pointer !important;
				transition: .3s;
				color: white;
				padding: 0 30px;
				margin: 35px 0;
				transition: .2s;
				position: relative;
			}

			._wppool-popup-button:after {
				content: "";
				position: absolute;
				top: 0;
				left: 0;
				width: 0%;
				height: 100%;
				background: rgba(255, 255, 255, 0.2);
				transition: .3s;
			}

			._wppool-popup-button:hover {
				color: white;
			}

			._wppool-popup-button:hover:after {
				width: 100%;
			}

			@media (max-width: 576px) {
				._wppool-popup-countdown {
					transform: scale(.99);
				}
			}';

			return apply_filters( 'wppool_inline_styles', $css );
		}

		/**
		 * Enqueues scripts.
		 *
		 * @return void
		 */
		public function enqueue_scripts() {
			wp_register_script( 'wppool-plugins', '', [], time(), true );

			// Localize script.
			wp_localize_script( 'wppool-plugins', 'WPPOOL_Plugins', [
				'plugins' => $this->get_plugins(),
				'debug' => defined( 'WP_DEBUG' ) && WP_DEBUG,
			] );

			wp_enqueue_script( 'wppool-plugins' );

			// Enqueue inline scripts.
			wp_add_inline_script( 'wppool-plugins', $this->get_inline_scripts() );

			// Enqueue inline styles.
			wp_register_style( 'wppool-plugins', '', [], time() );
			wp_enqueue_style( 'wppool-plugins' );
			wp_add_inline_style( 'wppool-plugins', $this->get_inline_styles() );
		}

		/**
		 * Adds Appsero tracker integration hooks.
		 *
		 * @return void
		 */
		public function listen_appsero() {
			$hook_name =
				str_replace( '_', '-', $this->plugin_id ) . '_tracker_optin';

			add_action(
				$hook_name,
				[ $this, 'plugin_tracker_optin_callback' ],
				10,
				1
			);
		}

		/**
		 * Appsero for older support
		 *
		 * @return void
		 */
		public function appsero() {
			$this->listen_appsero();
		}

		/**
		 * Callback for Appsero tracker integration hooks.
		 *
		 * @param array $data The data.
		 * @return void
		 */
		public function plugin_tracker_optin_callback( $data = [] ) {
			$this->user_data = [
				'email' => $data['admin_email'],
				'first_name' => $data['first_name'],
				'last_name' => $data['last_name'],
			];

			// Subscribe to CRM.
			$this->subscribe();
		}

		/**
		 * Sets custom tags IDs.
		 * set tag
		 *
		 * @param int|array $tag_id The tag IDs.
		 * @return self
		 */
		public function set_tag( $tag_id = null ) {
			if ( $tag_id ) {
				$this->custom_tags = array_merge(
					$this->custom_tags,
					is_array( $tag_id ) ? $tag_id : [ $tag_id ]
				);
			}

			return $this;
		}

		/**
		 * Sets custom list IDs.
		 *
		 * @param int|array $list_id The list IDs.
		 * @return self
		 */
		public function set_list( $list_id = null ) {
			if ( $list_id ) {
				$this->custom_lists = array_merge(
					$this->custom_lists,
					is_array( $list_id ) ? $list_id : [ $list_id ]
				);
			}

			return $this;
		}

		/**
		 * Removes custom tag IDs.
		 *
		 * @param int|array $tag_id The tag IDs.
		 * @return self
		 */
		public function remove_tag( $tag_id = null ) {
			if ( $tag_id ) {
				$this->custom_tags = array_diff(
					$this->custom_tags,
					is_array( $tag_id ) ? $tag_id : [ $tag_id ]
				);
			}

			return $this;
		}

		/**
		 * Removes custom list IDs.
		 *
		 * @param  int|array $list_id The list id.
		 * @return self
		 */
		public function remove_list( $list_id = null ) {
			if ( $list_id ) {
				$this->custom_lists = array_diff(
					$this->custom_lists,
					is_array( $list_id ) ? $list_id : [ $list_id ]
				);
			}

			return $this;
		}

		/**
		 * Returns tag ID by tag name.
		 *
		 * @param string $tag The tag.
		 * @return array
		 */
		public function get_tag_id( $tag = '' ) {
			$tags = $this->get_tags();

			return isset( $tags[ $tag ] ) ? $tags[ $tag ] : [];
		}

		/**
		 * Get current list
		 *
		 * @return array
		 */
		public function get_current_list_id() {
			$plugin = $this->get_current_plugin();

			return $plugin ? $plugin['list_id'] : [];
		}

		/**
		 * Send data to CRM.
		 * Data WILL BE ONLY SENT IF user allowed to send data from Admin Notice.
		 *
		 * @param  mixed $data The data to process.
		 * @throws \Exception If email is not valid.
		 * @return mixed
		 */
		protected function sent_to_fluent_server( $data = [] ) {
			// Check if email isset and email is valid.
			if ( ! isset( $data['email'] ) || ! is_email( $data['email'] ) ) {
				throw new \Exception( 'Email is not valid' );
			}

			$data = isset( $data ) && $data ? $data : $this->user_data;

			$payload = [
				'headers' => [
					'Authorization' => 'Bearer ' . $this->crm_access_token,
				],
				'body' => $data,
			];

			$response = wp_remote_post( $this->crm_endpoint, $payload );

			if ( is_wp_error( $response ) ) {
				throw new \Exception( esc_html( $response->get_error_message() ) );
			}

			$response = json_decode( wp_remote_retrieve_body( $response ), true );

			return $response;
		}

		/**
		 * Subscribes to list and tag.
		 * Data WILL BE ONLY SENT IF user allowed to send data from Admin Notice.
		 *
		 * @param string $tag The tag.
		 * @return mixed
		 */
		public function subscribe( $tag = 'free' ) {
			$data = array_merge( $this->user_data, [
				'tags' => [ $this->get_tag_id( $tag ) ],
				'lists' => $this->get_list_id(),
			] );

			return $this->sent_to_fluent_server( $data );
		}

		/**
		 * Unsubscribes from list
		 * Data WILL BE ONLY SENT IF user allowed to send data from Admin Notice.
		 *
		 * @return array
		 */
		public function unsubscribe_plugin() {
			$data = array_merge( $this->user_data, [
				'remove_lists' => $this->get_list_id(),
			] );

			return $this->sent_to_fluent_server( $data );
		}

		/**
		 * Unsubscribes from tag
		 * Data WILL BE ONLY SENT IF user allowed to send data from Admin Notice.
		 *
		 * @param string $tag The tag name.
		 * @return array
		 */
		public function unsubscribe_tag( $tag = 'free' ) {
			$data = array_merge( $this->user_data, [
				'remove_tags' => $this->get_tag_id( $tag ),
			] );

			return $this->sent_to_fluent_server( $data );
		}

		/**
		 * Unsubscribe from the CRM
		 *
		 * @return array
		 */
		public function unsubscribe() {
			$data = array_merge( $this->user_data, [ 'status' => 'unsubscribed' ] );

			return $this->sent_to_fluent_server( $data );
		}

		/**
		 * Returns the current plugin image
		 *
		 * @return string
		 */
		public function get_plugin_image() {
			return plugin_dir_url( __FILE__ ) . 'background-image.png';
		}

		/**
		 * Init Plugin
		 *
		 * @param string $plugin_id The plugin id.
		 * @param string $image_url The image url.
		 * @return self
		 */
		public static function init( $plugin_id = 'wp_dark_mode', $image_url = null ) {
			$instance = new self( $plugin_id );

			// Add plugin image.
			add_filter( 'wppool_plugins', function ( $plugins ) use ( $instance, $image_url ) {
				$plugins[ $instance->plugin_id ]['background_image'] = isset( $image_url ) ? $image_url : $instance->get_plugin_image();

				return $plugins;
			} );

			// Trigger appsero.
			$instance->appsero();

			return $instance;
		}

		/**
		 * Set image until
		 *
		 * @param string $image_url The image url.
		 * @param string $to End date. Default is 2 weeks from now.
		 * @param string $from Start from. Default is now.
		 * @return mixed
		 */
		public function set_campaign( $image_url = null, $to = null, $from = null ) {
			// Bailout if image url is not valid.
			if ( ! $image_url ) {
				return $this;
			}

			// Set from now if it's not set.
			$from_time = $from ? strtotime( $from . ' 00:00:01' ) : strtotime( 'now' );

			// Set to 2 weeks from now if it's not set.
			$to_time = $to ? strtotime( $to . ' 23:59:59' ) : strtotime( '+2 weeks' );

			$current_time = strtotime( 'now' );

			// If current time is not between from and to date, return.
			if ( $current_time < $from_time || $current_time > $to_time ) {
				return $this;
			}

			// Modify the plugin data.
			add_filter( 'wppool_plugins', function ( $plugins ) use ( $image_url, $to, $from ) {

				$plugins[ $this->plugin_id ]['background_image'] = $image_url;
				$plugins[ $this->plugin_id ]['from'] = $from;
				$plugins[ $this->plugin_id ]['to'] = $to;

				return $plugins;
			} );
		}
	}


	// Bypass Appsero Local.
	add_filter( 'appsero_is_local', '__return_false' );

	// Instantiate the class after plugins loaded.
	add_action( 'plugins_loaded', [ '\WPPOOL_Plugin', 'init_plugin_sdk' ] );

	/**
	 * If WPPOOL_Plugin function does not exists.
	 *
	 * @param string $plugin_id The plugin id.
	 * @param string $image_url The image url.
	 * @return mixed
	 */
	function wppool_plugin_init( $plugin_id = 'wp_dark_mode', $image_url = null ) {
		return WPPOOL_Plugin::init( $plugin_id, $image_url );
	}
}

