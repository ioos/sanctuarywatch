<?php

/**
 * Handles ajax requests for WP Dark Mode
 *
 * @package WP Dark Mode
 * @since 5.0.0
 */

// Namespace.
namespace WP_Dark_Mode;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit( 1 );

if ( ! class_exists( __NAMESPACE__ . 'Ajax' ) ) {
	/**
	 * Handles ajax requests for WP Dark Mode
	 *
	 * @package WP Dark Mode
	 * @since 5.0.0
	 */
	class Ajax extends \WP_Dark_Mode\Base {

		// Use options trait.
		use \WP_Dark_Mode\Traits\Options;

		// Utility trait.
		use \WP_Dark_Mode\Traits\Utility;

		/**
		 * Register ajax actions
		 *
		 * @since 5.0.0
		 */
		public function actions() {
			add_action( 'wp_ajax_wp_dark_mode_update_visitor', array( $this, 'update_visitor' ) );
			add_action( 'wp_ajax_nopriv_wp_dark_mode_update_visitor', array( $this, 'update_visitor' ) );
		}

		/**
		 * Updates options
		 *
		 * @since 5.0.0
		 */
		public function update_visitor() {
			// Check nonce.
			check_ajax_referer( 'wp_dark_mode_nonce', 'nonce' );

			$visitor_id = isset( $_POST['visitor_id'] ) ? intval( wp_unslash( $_POST['visitor_id'] ) ) : false;

			if ( $visitor_id && $visitor_id > 0 ) {
				// Update visitor.
				$this->update_existing_visitor( $visitor_id );
			} else {
				// Insert visitor.
				$this->insert_new_visitor();
			}
		}

		/**
		 * Inserts visitor
		 *
		 * @since 5.0.0
		 */
		public function insert_new_visitor() {
			$user_id = get_current_user_id();
			$ip = isset( $_POST['ip'] ) ? sanitize_text_field( wp_unslash( $_POST['ip'] ) ) : ( isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '' );

			$mode = isset( $_POST['mode'] ) ? sanitize_text_field( wp_unslash( $_POST['mode'] ) ) : 'dark';
			$meta = isset( $_POST['meta'] ) ? sanitize_text_field( wp_unslash( $_POST['meta'] ) ) : '';

			$visitor = new \WP_Dark_Mode\Model\Visitor();

			try {
				$visitor_id = $visitor->add( array(
					'meta' => $meta,
					'user_id' => $user_id,
					'ip' => $ip,
					'mode' => $mode,
				) );
			} catch ( \Exception $e ) {
				wp_send_json_error( $e->getMessage() );
			}

			// Return success.
			if ( $visitor_id ) {
				wp_send_json_success( [
					'visitor_id' => $visitor_id,
					'message' => 'Visitor inserted successfully',
				] );
			} else {
				global $wpdb;
				wp_send_json_error( [
					'message' => 'Visitor not inserted',
					'error' => $wpdb->last_error,
				] );
			}
		}

		/**
		 * Updates visitor
		 *
		 * @param int $visitor_id Visitor ID.
		 * @since 5.0.0
		 */
		public function update_existing_visitor( $visitor_id ) {

			$mode = isset( $_POST['mode'] ) ? sanitize_text_field( wp_unslash( $_POST['mode'] ) ) : 'dark';

			$visitor = new \WP_Dark_Mode\Model\Visitor();

			try {
				$updated = $visitor->update( array(
					'mode' => $mode,
					'user_id' => is_user_logged_in() ? get_current_user_id() : null,
				), intval($visitor_id) );
			} catch ( \Exception $e ) {
				wp_send_json_error( $e->getMessage() );
			}

			// Return success.
			if ( $updated ) {
				wp_send_json_success( [
					'visitor_id' => $visitor_id,
					'message' => 'Visitor updated successfully',
				] );
			} else {
				wp_send_json_error( [
					'visitor_id' => $visitor_id,
					'message' => 'Visitor not updated',
				] );
			}
		}
	}

	// Instantiate the class.
	Ajax::init();
}
