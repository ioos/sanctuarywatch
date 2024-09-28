<?php

/**
 * Handles API requests for WP Dark Mode admin Settings.
 *
 * @package WP Dark Mode
 * @since 5.0.0
 */

// Namespace.
namespace WP_Dark_Mode\Admin;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit( 1 );

if ( ! class_exists( __NAMESPACE__ . 'REST' ) ) {
	/**
	 * Handles API requests for WP Dark Mode admin Settings.
	 *
	 * @package WP Dark Mode
	 * @since 5.0.0
	 */
	class REST extends \WP_Dark_Mode\Base {

		// Use options trait.
		use \WP_Dark_Mode\Traits\Options;

		/**
		 * Register ajax actions
		 *
		 * @since 5.0.0
		 */
		public function actions() {
			// Add REST API endpoints.
			$this->register_rest_routes();
		}

		/**
		 * Register REST API routes
		 *
		 * @since 5.0.0
		 */
		public function register_rest_routes() {

			// Get settings.
			register_rest_route(
				'wp-dark-mode',
				'/settings',
				array(
					'methods' => 'GET',
					'callback' => array( $this, 'get_settings' ),
					'permission_callback' => array( $this, 'permissions_callback' ),
				)
			);

			// Update options.
			register_rest_route(
				'wp-dark-mode',
				'/settings',
				array(
					'methods' => 'PUT',
					'callback' => array( $this, 'update_settings' ),
					'args' => array(
						'options' => array(
							'required' => false,
							'type' => 'object',
						),
					),
					'permission_callback' => array( $this, 'permissions_callback' ),
				)
			);

			// Reset settings.
			register_rest_route(
				'wp-dark-mode',
				'/settings',
				array(
					'methods' => 'DELETE',
					'callback' => array( $this, 'reset_settings' ),
					'permission_callback' => array( $this, 'permissions_callback' ),
				)
			);

			// Update notice.
			register_rest_route(
				'wp-dark-mode',
				'/notice',
				array(
					'methods' => 'POST',
					'callback' => array( $this, 'update_notice' ),
					'permission_callback' => array( $this, 'permissions_callback' ),
				)
			);

			// Get visitors.
			register_rest_route(
				'wp-dark-mode',
				'/visitors',
				array(
					'methods' => 'GET',
					'callback' => array( $this, 'get_visitors' ),
					'permission_callback' => array( $this, 'permissions_callback' ),
				)
			);

			// Get contents.
			register_rest_route(
				'wp-dark-mode',
				'/contents',
				array(
					'methods' => 'GET',
					'callback' => array( $this, 'get_contents' ),
					'permission_callback' => array( $this, 'permissions_callback' ),
				)
			);
		}


		/**
		 * Permissions callback
		 *
		 * @since 5.0.0
		 * @return bool
		 */
		public function permissions_callback() {
			// return true;
			return current_user_can( 'manage_options' );
		}

		/**
		 * Get settings
		 *
		 * @since 5.0.0
		 */
		public function get_settings( $request ) {
			$settings = $this->get_default_formatted_options();

			// Bail if no settings.
			if ( empty( $settings ) ) {
				return rest_ensure_response( [
					'success' => false,
					'message' => __( 'No settings found.', 'wp-dark-mode' ),
				] );
			}

			// Is option requested.
			if ( $request->has_param ( 'option' ) ) {
				$option = $request->get_param( 'option' );
				if ( isset( $settings[ $option ] ) ) {
					$settings = [ $option => $settings[ $option ] ];
				}
			}

			// Send response.
			return rest_ensure_response( [
				'success' => true,
				'settings' => $settings,
			] );
		}

		/**
		 * Updates settings
		 *
		 * @since 5.0.0
		 */
		public function update_settings( $request ) {

			$option_keys = $this->get_default_formatted_options();
			$updates = [];

			foreach ( $option_keys as $key => $value ) {
				if ( $request->has_param( $key ) ) {
					$this->set_option( $key, $request->get_param( $key ) );
					$updates[ $key ] = $request->get_param( $key );
				}
			}

			// Bail if no updates.
			if ( empty( $updates ) ) {
				return rest_ensure_response( [
					'success' => false,
					'message' => __( 'No settings found.', 'wp-dark-mode' ),
				] );
			}

			// Send response.
			return rest_ensure_response( [
				'success' => true,
				'keys' => $updates,
				'message' => __( 'Settings saved successfully.', 'wp-dark-mode' ),
			] );
		}

		/**
		 * Resets settings
		 *
		 * @since 5.0.0
		 */
		public function reset_settings( $request ) {

			// Third step security check for reset.
			if ( ! $request->has_param( 'confirm_reset' ) || 'yes' !== $request->get_param( 'confirm_reset' ) ) {
				return rest_ensure_response( [
					'success' => false,
					'message' => __( 'Reset not confirmed.', 'wp-dark-mode' ),
				] );
			}

			// Reset settings.
			$this->set_default_options();

			// Send response.
			return rest_ensure_response( [
				'success' => true,
				'message' => __( 'Settings reset successfully.', 'wp-dark-mode' ),
			] );
		}


		/**
		 * Updates notice
		 *
		 * @since 5.0.0
		 */
		public function update_notice( $request ) {

			$notice = $request->get_param( 'notice' );

			// Bail if no notice.
			if ( empty( $notice ) ) {
				return rest_ensure_response( [
					'success' => false,
					'message' => __( 'No notice found.', 'wp-dark-mode' ),
				] );
			}

			$remind = $request->has_param( 'remind' ) ? $request->get_param( 'remind' ) : 'never';

			if ( 'never' === $remind ) {
				// delete transient.
				update_option( wp_sprintf( 'wp_dark_mode_%s_notice', $notice ), 'hide');
			} else {
				// delete options
				delete_option( wp_sprintf( 'wp_dark_mode_%s_notice', $notice ) );
				// set transient.
				set_transient( wp_sprintf( 'wp_dark_mode_%s_notice', $notice ), 'hide', $remind * DAY_IN_SECONDS );
			}

			// Send response.
			return rest_ensure_response( [
				'success' => true,
				'message' => wp_sprintf( '%1$s notice is %2$s', ucfirst( esc_html( $notice ) ), 'never' === $remind ? 'hidden' : 'scheduled to remind after ' . esc_html( $remind ) . ' days' ),
			] );
		}

		/**
		 * Get visitors
		 *
		 * @since 5.0.0
		 */
		public function get_visitors( $request ) {
			$visitor = new \WP_Dark_Mode\Model\Visitor();
			$visitors = $visitor->get_all();

			// Send response.
			return rest_ensure_response( $visitors );
		}

		/**
		 * Get posts
		 *
		 * @since 5.0.0
		 */
		public function get_contents( $request ) {
			$contents = [
				'post_types' => $this->get_post_types(),
				'posts' => $this->get_posts(),
				'taxonomies' => $this->get_taxonomies(),
				'terms' => $this->get_terms(),
				'products' => $this->get_products(),
				'product_categories' => $this->get_product_categories(),
			];

			// Send response.
			return rest_ensure_response( [
				'success' => true,
				'contents' => $contents,
			] );
		}


		/**
		 * Get post types.
		 *
		 * @since 5.0.0
		 * @return array
		 */
		public function get_post_types() {
			$post_types = get_post_types( array(
				'public' => true,
				'show_ui' => true,
				'exclude_from_search' => false,
			), 'objects' );

			$post_types = array_filter( $post_types, function ( $post_type ) {
				return ! in_array( $post_type->name, array( 'attachment', 'product' ), true );
			} );

			// make it to slug => label.
			$post_types = array_combine( wp_list_pluck( $post_types, 'name' ), wp_list_pluck( $post_types, 'label' ) );

			return $post_types;
		}


		/**
		 * Get posts.
		 *
		 * @since 5.0.0
		 * @return array
		 */
		public function get_posts() {

			global $wpdb;

			$posts = $wpdb->get_results( $wpdb->prepare( // phpcs:ignore
				"SELECT `ID`, `post_title` as `title`, `post_type` as `type` FROM {$wpdb->posts} WHERE post_type IN ('post', 'page') AND post_status = %s",
				'publish'
			) );

			return $posts;
		}

		/**
		 * Get taxonomies.
		 *
		 * @since 5.0.0
		 * @return array
		 */
		public function get_taxonomies() {
			$taxonomies = get_taxonomies( array(
				'public' => true,
				'show_ui' => true,
			), 'objects' );

			$taxonomies = array_filter( $taxonomies, function ( $taxonomy ) {
				return ! in_array( $taxonomy->name, array( 'product_cat', 'product_tag', 'post_format' ), true );
			} );

			// make it to slug => label.
			$taxonomies = array_combine( wp_list_pluck( $taxonomies, 'name' ), wp_list_pluck( $taxonomies, 'label' ) );

			return $taxonomies;
		}

		/**
		 * Get terms.
		 *
		 * @since 5.0.0
		 * @return array
		 */
		public function get_terms() {
			$taxonomies = $this->get_taxonomies();

			$terms = array();

			$terms = get_terms(
				array_keys( $taxonomies )
			);

			// make it to id, title and taxonomy
			$terms = array_map( function ( $term ) {
				return array(
					'ID' => intval($term->term_id),
					'title' => $term->name,
					'tax' => $term->taxonomy,
				);
			}, $terms );

			return array_values( $terms );
		}

		/**
		 * Get products.
		 *
		 * @since 5.0.0
		 * @return array
		 */
		public function get_products() {
			$products = array();

			if ( ! class_exists( 'WooCommerce' ) ) {
				return $products;
			}
			global $wpdb;

			$products = $wpdb->get_results( // phpcs:ignore
				$wpdb->prepare(
					"SELECT ID, post_title as title, post_type as `type` FROM $wpdb->posts WHERE post_type = %s AND post_status = %s",
					'product',
					'publish'
				)
			);

			return $products;
		}

		/**
		 * Get product categories.
		 *
		 * @since 5.0.0
		 * @return array
		 */
		public function get_product_categories() {
			$product_categories = array();

			if ( ! class_exists( 'WooCommerce' ) ) {
				return $product_categories;
			}

			$product_categories = get_terms( array(
				'taxonomy' => 'product_cat',
				'hide_empty' => false,
			) );

			// make it to id, title and taxonomy
			$product_categories = array_map( function ( $product_category ) {
				return array(
					'ID' => intval($product_category->term_id),
					'title' => $product_category->name,
					'tax' => $product_category->taxonomy,
				);
			}, $product_categories );

			return $product_categories;
		}
	}

	// Instantiate the class.
	REST::init();
}
