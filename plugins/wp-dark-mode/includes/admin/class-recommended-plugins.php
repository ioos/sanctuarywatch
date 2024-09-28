<?php

/**
 * Handles recommended plugins
 *
 * @package WP Dark Mode
 * @since 5.0.0
 */

// Namespace.
namespace WP_Dark_Mode\Admin;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit( 1 );

if ( ! class_exists( __NAMESPACE__ . 'Recommended_Plugins' ) ) {
	/**
	 * Recommended plugins
	 *
	 * @package WP Dark Mode
	 * @since 5.0.0
	 */
	class Recommended_Plugins extends \WP_Dark_Mode\Base {

		// Use options trait.
		use \WP_Dark_Mode\Traits\Options;

		/**
		 * Plugin slugs
		 *
		 * @var array
		 */
		protected $plugin_slugs = [
			'sheets-to-wp-table-live-sync',
			'stock-sync-with-google-sheet-for-woocommerce',
			'webinar-and-video-conference-with-jitsi-meet',
			'easy-video-reviews',
			'wppool-turnstile-captcha-spam-filter',
			'easy-email-integration',
			'zero-bs-accounting',
			'social-contact-form',
		];

		/**
		 * Allowed tags
		 *
		 * @var array
		 */
		protected $allowed_tags = [
			'a'       => [
				'href'   => [],
				'title'  => [],
				'target' => [],
			],
			'abbr'    => [ 'title' => [] ],
			'acronym' => [ 'title' => [] ],
			'code'    => [],
			'pre'     => [],
			'em'      => [],
			'strong'  => [],
			'ul'      => [],
			'ol'      => [],
			'li'      => [],
			'p'       => [],
			'br'      => [],
		];

		/**
		 * Get the plugin
		 *
		 * @param string $slug Plugin slug.
		 * @return object|bool
		 */
		public function get_plugin( $slug ) {
			$args = [
				'slug'   => $slug,
				'fields' => [
					'short_description' => true,
					'icons'             => true,
					'reviews'           => false, // Excludes all reviews.
				],
			];

			$data = plugins_api( 'plugin_information', $args );

			if ( $data && ! is_wp_error( $data ) ) {
				return $data;
			}

			return false;
		}

		/**
		 * Get the plugins
		 *
		 * @return array
		 */
		public function get_plugins() {
			// Get all plugin in a single request.
			$plugins = get_site_transient( 'wp_dark_mode_recommended_plugins' );

			if ( ! $plugins ) {
				$plugins = [];

				foreach ( $this->plugin_slugs as $slug ) {
					$plugin = $this->get_plugin( $slug );

					if ( $plugin ) {
						$plugins[] = $plugin;
					}
				}

				set_site_transient( 'wp_dark_mode_recommended_plugins', $plugins, WEEK_IN_SECONDS );
			}

			return $plugins;
		}

		/**
		 * Actions needed for the class
		 */
		public function actions() {
			add_action( 'wp_dark_mode_recommended_plugins_page', array( $this, 'render_recommended_plugins_page' ) );
			// Admin init.
			add_action( 'admin_init', [ $this, 'admin_init' ] );
		}

		/**
		 * Render recommended plugins page
		 */
		public function render_recommended_plugins_page() {

			// Add jQuery UI Dialog.
			wp_enqueue_style( 'wp-jquery-ui-dialog' );
			wp_enqueue_script( 'jquery-ui-dialog' );

			// Include the plugin install file.
			require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
			remove_all_filters( 'plugins_api' );

			// Get the plugins.
			$plugins = $this->get_plugins();

			?>

			<style>
				a.hide-recommended-btn {
					background: #1da1f4;
					display: block;
					float: right;
					color: #fff;
					text-decoration: none;
					padding: 5px 20px;
					font-size: 18px;
					/* font-weight: bold; */
					border-radius: 4px;
				}
				.ui-dialog-titlebar {
					background: none !important;
				}
				.ui-dialog {
					z-index: 999999;
					text-align: center;
				}
				.ui-dialog-buttonpane {
					border: none;
					background: transparent;
					padding-top: 0;
				}
				.ui-dialog .ui-dialog-buttonset{
					float:none;
					text-align: center;
				}
				.ui-dialog .ui-dialog-buttonpane .ui-button {
					margin: 0 10px;
				}
				.ui-dialog-buttonpane .ui-dialog-buttonset .red-btn,
				.ui-dialog-buttonpane .ui-dialog-buttonset .purple-btn,
				.ui-dialog-buttonpane .ui-dialog-buttonset .gray-btn {
					background-color: #ffffff;
					color: #fff;
					border-color: #1da1f4;
					line-height: 1.4;
					padding: 5px 0;
					height: auto;
					display: inline-block;
					vertical-align: top;
					font-size: 16px;
					min-width: 150px;
					color: #1da1f4;
				}
				.ui-dialog-buttonpane .ui-dialog-buttonset .red-btn {
					background-color: #1da1f4;
					border-color: #1da1f4;
					color: #ffffff;
				}
			</style>

			<div class="wrap mystickyelement-wrap recommended-plugins">
				<h2>
					<?php esc_html_e( 'Try out our recommended plugins', 'wp-dark-mode' ); ?>
					<a class="hide-recommended-btn" href="#"><?php esc_html_e( 'Hide From Menu', 'wp-dark-mode' ); ?></a>
				</h2>
			</div>

			<div class="wrap recommended-plugins">
				<div class="wp-list-table widefat plugin-install">
					<div class="the-list">
						<?php
						foreach ( $plugins as $single_plugin ) {
							if ( is_object( $single_plugin ) ) {
								$single_plugin = (array) $single_plugin;
							}

							// Display the group heading if there is one.
							if ( isset( $single_plugin['group'] ) && $single_plugin['group'] !== $group ) {
								if ( isset( $single_plugins_group_titles ) ) {
									$group_name = $single_plugin['group'];

									if ( isset( $single_plugins_group_titles[ $group_name ] ) ) {
										$group_name = $single_plugins_group_titles[ $group_name ];
									}
								} else {
									$group_name = $single_plugin['group'];
								}

								// Starting a new group, close off the divs of the last one.
								if ( ! empty( $group ) ) {
									echo '</div></div>';
								}

								echo '<div class="plugin-group"><h3>' . esc_html( $group_name ) . '</h3>';
								// Needs an extra wrapping div for nth-child selectors to work.
								echo '<div class="plugin-items">';

								$group = $single_plugin['group'];
							}

							$plugin_title = wp_kses( $single_plugin['name'], $this->allowed_tags );

							// Remove any HTML from the description.
							$description = wp_strip_all_tags( $single_plugin['short_description'] );

							$version = wp_kses( $single_plugin['version'], $this->allowed_tags );

							$name = wp_strip_all_tags( $plugin_title . ' ' . $version );

							$author = wp_kses( $single_plugin['author'], $this->allowed_tags );

							if ( ! empty( $author ) ) {
								/* translators: %s: Plugin author. */
								$author = ' <cite>' . sprintf( __( 'By %s', 'wp-dark-mode' ), $author ) . '</cite>';
							}

							$requires_php = isset( $single_plugin['requires_php'] ) ? $single_plugin['requires_php'] : null;
							$requires_wp  = isset( $single_plugin['requires'] ) ? $single_plugin['requires'] : null;

							$compatible_php = is_php_version_compatible( $requires_php );
							$compatible_wp  = is_wp_version_compatible( $requires_wp );

							$tested_wp = ( empty( $single_plugin['tested'] ) || version_compare( get_bloginfo( 'version' ), $single_plugin['tested'], '<=' ) );

							$action_links = [];

							if ( current_user_can( 'install_plugins' ) || current_user_can( 'update_plugins' ) ) {
								$plugin_status = install_plugin_install_status( $single_plugin );

								switch ( $plugin_status['status'] ) {
									case 'install':
										if ( $plugin_status['url'] ) {
											if ( $compatible_php && $compatible_wp ) {
												$action_links[] = sprintf(
													'<a class="install-now button" data-slug="%s" href="%s" aria-label="%s" data-name="%s">%s</a>',
													esc_attr( $single_plugin['slug'] ),
													esc_url( $plugin_status['url'] ),
													/* translators: %s: Plugin name and version. */
													esc_attr( sprintf( _x( 'Install %s now', 'plugin', 'wp-dark-mode' ), $name ) ),
													esc_attr( $name ),
													__( 'Install Now', 'wp-dark-mode' )
												);
											} else {
												$action_links[] = sprintf(
													'<button type="button" class="button button-disabled" disabled="disabled">%s</button>',
													_x( 'Cannot Install', 'plugin', 'wp-dark-mode' )
												);
											}
										}
										break;

									case 'update_available':
										if ( $plugin_status['url'] ) {
											if ( $compatible_php && $compatible_wp ) {
												$action_links[] = sprintf(
													'<a class="update-now button aria-button-if-js" data-plugin="%s" data-slug="%s" href="%s" aria-label="%s" data-name="%s">%s</a>',
													esc_attr( $plugin_status['file'] ),
													esc_attr( $single_plugin['slug'] ),
													esc_url( $plugin_status['url'] ),
													/* translators: %s: Plugin name and version. */
													esc_attr( sprintf( _x( 'Update %s now', 'plugin', 'wp-dark-mode' ), $name ) ),
													esc_attr( $name ),
													__( 'Update Now', 'wp-dark-mode' )
												);
											} else {
												$action_links[] = sprintf(
													'<button type="button" class="button button-disabled" disabled="disabled">%s</button>',
													_x( 'Cannot Update', 'plugin', 'wp-dark-mode' )
												);
											}
										}
										break;

									case 'latest_installed':
									case 'newer_installed':
										if ( is_plugin_active( $plugin_status['file'] ) ) {
											$action_links[] = sprintf(
												'<button type="button" class="button button-disabled" disabled="disabled">%s</button>',
												_x( 'Active', 'plugin', 'wp-dark-mode' )
											);
										} elseif ( current_user_can( 'activate_plugin', $plugin_status['file'] ) ) {
											$button_text = __( 'Activate', 'wp-dark-mode' );
											/* translators: %s: Plugin name. */
											$button_label = _x( 'Activate %s', 'plugin', 'wp-dark-mode' );
											$activate_url = add_query_arg(
												[
													'_wpnonce' => wp_create_nonce( 'activate-plugin_' . $plugin_status['file'] ),
													'action'   => 'activate',
													'plugin'   => $plugin_status['file'],
												]
											);

											$action_links[] = sprintf(
												'<a href="%1$s" class="button activate-now" aria-label="%2$s">%3$s</a>',
												esc_url( $activate_url ),
												esc_attr( sprintf( $button_label, $single_plugin['name'] ) ),
												$button_text
											);
										} else {
											$action_links[] = sprintf(
												'<button type="button" class="button button-disabled" disabled="disabled">%s</button>',
												_x( 'Installed', 'plugin', 'wp-dark-mode' )
											);
										}
										break;
								}
							}

							$details_link = self_admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin=' . $single_plugin['slug'] . '&amp;TB_iframe=true&amp;width=600&amp;height=550' );

							$action_links[] = wp_sprintf(
								'<a href="%s" class="thickbox open-plugin-details-modal" aria-label="%s" data-title="%s">%s</a>',
								esc_url( $details_link ),
								/* translators: %s: Plugin name and version. */
								esc_attr( wp_sprintf( __( 'More information about %s', 'wp-dark-mode' ), $name ) ),
								esc_attr( $name ),
								__( 'More Details', 'wp-dark-mode' )
							);

							if ( ! empty( $single_plugin['icons']['svg'] ) ) {
								$single_plugin_icon_url = $single_plugin['icons']['svg'];
							} elseif ( ! empty( $single_plugin['icons']['2x'] ) ) {
								$single_plugin_icon_url = $single_plugin['icons']['2x'];
							} elseif ( ! empty( $single_plugin['icons']['1x'] ) ) {
								$single_plugin_icon_url = $single_plugin['icons']['1x'];
							} else {
								$single_plugin_icon_url = $single_plugin['icons']['default'];
							}

							/**
							 * Filters the install action links for a plugin.
							 *
							 * @since 2.7.0
							 *
							 * @param string[] $action_links An array of plugin action links. Defaults are links to Details and Install Now.
							 * @param array $single_plugin The plugin currently being listed.
							 */
							$action_links = apply_filters( 'plugin_install_action_links', $action_links, $single_plugin );

							$last_updated_timestamp = strtotime( $single_plugin['last_updated'] );
							?>
							<div class="plugin-card plugin-card-<?php echo esc_attr( $single_plugin['slug'] ); ?>">
								<?php
								if ( ! $compatible_php || ! $compatible_wp ) {
									echo '<div class="notice inline notice-error notice-alt"><p>';
									if ( ! $compatible_php && ! $compatible_wp ) {
										echo esc_html__( 'This plugin doesn&#8217;t work with your versions of WordPress and PHP.', 'wp-dark-mode' );
										if ( current_user_can( 'update_core' ) && current_user_can( 'update_php' ) ) {
											wp_sprintf(
											/* translators: 1: URL to WordPress Updates screen, 2: URL to Update PHP page. */
												' ' . wp_kses_post( '<a href="%1$s">Please update WordPress</a>, and then <a href="%2$s">learn more about updating PHP</a>.' ),
												self_admin_url( 'update-core.php' ),
												esc_url( wp_get_update_php_url() )
											);
											wp_update_php_annotation( '</p><p><em>', '</em>' );
										} elseif ( current_user_can( 'update_core' ) ) {
											wp_sprintf(
											/* translators: %s: URL to WordPress Updates screen. */
												' ' . wp_kses_post( '<a href="%s">Please update WordPress</a>.' ),
												self_admin_url( 'update-core.php' )
											);
										} elseif ( current_user_can( 'update_php' ) ) {
											wp_sprintf(
											/* translators: %s: URL to Update PHP page. */
												' ' . wp_kses_post( '<a href="%s">Learn more about updating PHP</a>.' ),
												esc_url( wp_get_update_php_url() )
											);
											wp_update_php_annotation( '</p><p><em>', '</em>' );
										}
									} elseif ( ! $compatible_wp ) {
										esc_html__( 'This plugin doesn&#8217;t work with your version of WordPress.', 'wp-dark-mode' );
										if ( current_user_can( 'update_core' ) ) {
											wp_sprintf(
											/* translators: %s: URL to WordPress Updates screen. */
												' ' . wp_kses_post( '<a href="%s">Please update WordPress</a>.' ),
												self_admin_url( 'update-core.php' )
											);
										}
									} elseif ( ! $compatible_php ) {
										esc_html__( 'This plugin doesn&#8217;t work with your version of PHP.', 'wp-dark-mode' );
										if ( current_user_can( 'update_php' ) ) {
											wp_sprintf(
											/* translators: %s: URL to Update PHP page. */
												' ' . wp_kses_post( '<a href="%s">Learn more about updating PHP</a>.' ),
												esc_url( wp_get_update_php_url() )
											);
											wp_update_php_annotation( '</p><p><em>', '</em>' );
										}
									}
									echo '</p></div>';
								}
								?>
								<div class="plugin-card-top">
									<div class="name column-name">
										<h3>
											<a href="<?php echo esc_url( $details_link ); ?>" class="thickbox open-plugin-details-modal">
												<?php echo esc_html( $plugin_title ); ?>
												<img src="<?php echo esc_attr( $single_plugin_icon_url ); ?>" class="plugin-icon" alt="" />
											</a>
										</h3>
									</div>
									<div class="action-links">
										<?php
										if ( $action_links ) {
											echo '<ul class="plugin-action-buttons"><li>' . wp_kses_post( implode( '</li><li>', $action_links ) ) . '</li></ul>';
										}
										?>
									</div>
									<div class="desc column-description">
										<p><?php echo wp_kses_post( $description ); ?></p>
										<p class="authors"><?php echo wp_kses_post( $author ); ?></p>
									</div>
								</div>
								<div class="plugin-card-bottom">
									<div class="vers column-rating">
										<?php
										wp_star_rating(
											[
												'rating' => $single_plugin['rating'],
												'type'   => 'percent',
												'number' => $single_plugin['num_ratings'],
											]
										);
										?>
										<span class="num-ratings" aria-hidden="true">(<?php echo esc_html( $single_plugin['num_ratings'] ); ?>)</span>
									</div>
									<div class="column-updated">
										<strong><?php esc_html_e( 'Last Updated:', 'wp-dark-mode' ); ?></strong>
										<?php
										/* translators: %s: Human-readable time difference. */
										wp_sprintf( esc_html__( '%s ago', 'wp-dark-mode' ), human_time_diff( $last_updated_timestamp ) );
										?>
									</div>
									<div class="column-downloaded">
										<?php
										if ( $single_plugin['active_installs'] >= 1000000 ) {
											$active_installs_millions = floor( $single_plugin['active_installs'] / 1000000 );
											$active_installs_text     = wp_sprintf(
											/* translators: %s: Number of millions. */
												_nx( '%s+ Million', '%s+ Million', $active_installs_millions, 'Active plugin installations', 'wp-dark-mode' ),
												number_format_i18n( $active_installs_millions )
											);
										} elseif ( 0 === intval( $single_plugin['active_installs'] ) ) {
											$active_installs_text = _x( 'Less Than 10', 'Active plugin installations', 'wp-dark-mode' );
										} else {
											$active_installs_text = number_format_i18n( $single_plugin['active_installs'] ) . '+';
										}
										/* translators: %s: Number of installations. */
										wp_sprintf( esc_html__( '%s Active Installations', 'wp-dark-mode' ), $active_installs_text );
										?>
									</div>
									<div class="column-compatibility">
										<?php
										if ( ! $tested_wp ) {
											echo '<span class="compatibility-untested">' . wp_kses_post( 'Untested with your version of WordPress' ) . '</span>';
										} elseif ( ! $compatible_wp ) {
											echo '<span class="compatibility-incompatible">' . wp_kses_post( '<strong>Incompatible</strong> with your version of WordPress' ) . '</span>';
										} else {
											echo '<span class="compatibility-compatible">' . wp_kses_post( '<strong>Compatible</strong> with your version of WordPress' ) . '</span>';
										}
										?>
									</div>
								</div>
							</div>
							<?php
						}
						?>
					</div>
				</div>
				<div id="hide-recommeded-plugins" style="display:none;" title="<?php echo esc_html__( 'Are you sure?', 'wp-dark-mode' ); ?>">
					<p><?php echo esc_html__( "If you hide the recommended plugins page from your menu, it won't appear there again. Are you sure you'd like to do it?", 'wp-dark-mode' ); ?></p>
				</div>
			</div>

			<script>
				( function( $ ) {
					"use strict";
					$(document).ready(function(){

						$('a.hide-recommended-btn').on('click',function(event){
							event.preventDefault();
							$( "#hide-recommeded-plugins" ).dialog({
								resizable: false,
								modal: true,
								draggable: false,
								height: 'auto',
								width: 400,
								open: function (event, ui) {
									$(".ui-widget-overlay").click(function () {
										$('#hide-recommeded-plugins').dialog('close');
									});
								},
								buttons: {
									"Hide it": {
										click: function () {
											const redirectTo = "<?php echo esc_url( admin_url( 'admin.php' ) ); ?>?hide_wp_dark_mode_recommended_plugins=1&hide_wp_dark_mode_recommended_plugins_nonce=<?php echo esc_attr( wp_create_nonce( 'hide_wp_dark_mode_recommended_plugins_nonce' ) ); ?>";
											console.log(redirectTo)
											window.location = redirectTo;
										},
										text: 'Hide it',
										class: 'btn red-btn'
									},
									"Keep it": {
										click: function () {
											$(this).dialog('close');
										},
										text: 'Keep it',
										class: 'btn alt gray-btn'
									},
								}
							});
						});
					});
				})( jQuery );
			</script>

			<?php
		}

		/**
		 * Admin init
		 */
		public function admin_init() {
			// Hide the recommended plugins page from the menu.
			if ( isset( $_GET['hide_wp_dark_mode_recommended_plugins'] ) && isset( $_GET['hide_wp_dark_mode_recommended_plugins_nonce'] ) ) {
				// Verify the nonce.
				if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['hide_wp_dark_mode_recommended_plugins_nonce'] ) ), 'hide_wp_dark_mode_recommended_plugins_nonce' ) ) {
					return;
				}

				// Update the option.
				update_option( 'hide_wp_dark_mode_recommended_plugins', true );

				// Redirect to the plugins page.
				wp_safe_redirect( admin_url( 'admin.php?page=wp-dark-mode' ) );
				exit;
			}
		}
	}

	// Instantiate the class.
	Recommended_Plugins::init();
}