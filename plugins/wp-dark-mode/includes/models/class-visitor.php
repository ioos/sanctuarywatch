<?php

/**
 * Model for visitor
 *
 * @package WP Dark Mode
 * @since 5.0.0
 */

// Namespace.
namespace WP_Dark_Mode\Model;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit( 1 );

if ( ! class_exists( __NAMESPACE__ . 'Visitor' ) ) {
	/**
	 * Model for visitor
	 *
	 * @package WP Dark Mode
	 * @since 5.0.0
	 */
	class Visitor extends \WP_Dark_Mode\Base {

		/**
		 * Table name
		 *
		 * @since 5.0.0
		 * @var string
		 */
		public $table_name = 'wpdm_visitors';

		// Actions.
		public function actions() {
			// Init database table.
			add_action( 'init', array( $this, 'init_db_table' ) );
		}

		/**
		 * Init database table
		 *
		 * @since 5.0.0
		 * @var array
		 */
		public function init_db_table() {
			global $wpdb;
			$table_name = $wpdb->prefix . $this->table_name;

			// Create table if not exists.
			if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) ) !== $table_name ) { // db call ok; no-cache ok.
				$sql = "CREATE TABLE $table_name (
					ID int(30) NOT NULL AUTO_INCREMENT,
					user_id int(11) NULL DEFAULT NULL,
					meta text NULL DEFAULT NULL,
					ip varchar(20) NULL DEFAULT NULL,
					mode varchar(20) NULL DEFAULT NULL,
					created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
					PRIMARY KEY  (ID)
				) " . $wpdb->get_charset_collate();

				require_once ABSPATH . 'wp-admin/includes/upgrade.php';
				dbDelta( $sql );
			}
		}

		/**
		 * Adds new visitor to WP Dark Mode
		 *
		 * @since 5.0.0
		 * @param array $data
		 * @throws \Exception on inserting data.
		 * @return mixed
		 */
		public function add( $data = array() ) {

			$default_data = array(
				'mode' => 'dark',
				'user_id' => get_current_user_id(),
				'meta' => '',
				'ip' => isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '',
			);

			$data = wp_parse_args( $data, $default_data );

			// Update visitor mode
			global $wpdb;
			$table_name = $wpdb->prefix . 'wpdm_visitors';
			$inserted = $wpdb->insert( $table_name, $data ); // db call ok; no-cache ok.

			if ( is_wp_error( $inserted ) ) {
				throw new \Exception( esc_html__( 'Failed to insert visitor data.', 'wp-dark-mode' ) );
			}

			return $wpdb->insert_id;
		}

		/**
		 * Updates existing visitor to WP Dark Mode
		 *
		 * @param array $data Dataset that will be updated.
		 * @param int ID - Target visitor whom data will be updated.
		 * @throws \Exception on failed updating data.
		 * @return bool
		 */
		public function update( $data = array(), $id = '' ) {
			global $wpdb;
			$table_name = $wpdb->prefix . 'wpdm_visitors';

			$updated = $wpdb->update( $table_name, $data, array( 'ID' => $id ) ); // db call ok; no-cache ok.

			if ( is_wp_error( $updated ) ) {
				throw new \Exception( esc_html__( 'Failed to update visitor data.', 'wp-dark-mode' ) );
			}

			return $updated;
		}

		/**
		 * Get all visitors
		 *
		 * @return mixed
		 */
		public function get_all() {
			global $wpdb;

			$visitors = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpdm_visitors WHERE ID >= %d ORDER BY created_at DESC", 0 ) ); // db call ok; no-cache ok.

			// If no visitors found.
			if ( empty( $visitors ) ) {
				return [];
			}

			$data = array();

			foreach ( $visitors as $visitor ) {

				if ( ! empty($visitor->meta) ) {
					$meta = json_decode($visitor->meta, true);
				} else {
					$meta = array();
                }

				$data[] = array(
					'ID' => intval( $visitor->ID ),
					'user_id' => intval( $visitor->user_id ),
					'mode' => $visitor->mode,
					'meta' => $meta,
					'ip' => $visitor->ip,
					'created_at' => $visitor->created_at,
				);
			}

			return $data;
		}
	}

	// Instantiate the class.
	Visitor::init();
}
