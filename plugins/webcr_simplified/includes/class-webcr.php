<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.noaa.gov
 * @since      1.0.0
 *
 * @package    Webcr
 * @subpackage Webcr/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Webcr
 * @subpackage Webcr/includes
 * @author     Jai Ranganathan <jai.ranganathan@noaa.gov>
 */
class Webcr {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Webcr_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'WEBCR_VERSION' ) ) {
			$this->version = WEBCR_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'webcr';

		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Webcr_Loader. Orchestrates the hooks of the plugin.
	 * - Webcr_Admin. Defines all hooks for the admin area.
	 * - Webcr_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-webcr-loader.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-webcr-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-webcr-public.php';

		// JAI The class that defines the metaboxes used for field entry
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/exopite-simple-options/exopite-simple-options-framework-class.php';

		// The class that defines the functions used for the Scene custom content type
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-webcr-scene.php';

		// The class that defines the functions used for the Instance custom content type
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-webcr-instance.php';

		$this->loader = new Webcr_Loader();

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		// Load class and functions to change overall look and function of admin screens
		$plugin_admin = new Webcr_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_filter( 'use_block_editor_for_post', $plugin_admin, 'remove_gutenberg');

		// Load  class and functions associated with Instance custom content type
		$plugin_admin_instance = new Webcr_Instance ( $this->get_plugin_name(), $this->get_version() );		
		$this->loader->add_action( 'init', $plugin_admin_instance, 'custom_content_type_instance' ); //scene
		$this->loader->add_action( 'admin_menu', $plugin_admin_instance, 'create_instance_fields', 1 );

		// Load  class and functions associated with Scene custom content type
		$plugin_admin_scene = new Webcr_Scene( $this->get_plugin_name(), $this->get_version() );		
		$this->loader->add_action( 'admin_menu', $plugin_admin_scene, 'create_scene_fields', 1 ); //scene
		$this->loader->add_action( 'init', $plugin_admin_scene, 'custom_content_type_scene' ); //scene

		//Disable Xlmrpc.php file
		add_filter( 'xmlrpc_enabled', '__return_false' );

		// Hook into post_type_link to customize the permalink for Scene posts - SKANDA COMMENT OUT NEXT LINE
		// Add custom rewrite rules to handle scene URLs without the 'scene' slug
		// function add_scene_rewrite_rules($rules) {
		// 	$new_rules = array(
		// 		'([^/]+)/?$' => 'index.php?post_type=scene&name=$matches[1]' // Map URL structure to scene post type
		// 	);
		// 	return $new_rules + $rules;
		// }
		// add_filter('rewrite_rules_array', 'add_scene_rewrite_rules');

		// // Modify the post type link to remove the 'scene' slug
		// function remove_scene_slug($post_link, $post, $leavename) {
		// 	if ('scene' != $post->post_type || 'publish' != $post->post_status) {
		// 		return $post_link;
		// 	}

		// 	// $instance_id = get_post_meta($post->ID, 'scene_location', true);
		// 	// $instance = get_post($instance_id);
		// 	// $web_slug = get_post_meta($instance_id, 'instance_slug', true);
		// 	// // echo $instance;
		// 	// echo $web_slug;

		// 	// if (!$instance || !$web_slug) {
		// 	// 	return $post_link;
		// 	// }

		// 	return home_url('/' . $post->post_name . '/');
		// }
		// add_filter('post_type_link', 'remove_scene_slug', 10, 3);
		
		function add_scene_rewrite_rules($rules) {
			$new_rules = array(
				'([^/]+)/([^/]+)/?$' => 'index.php?post_type=scene&name=$matches[2]&instance_slug=$matches[1]' // Map URL structure to scene post type
			);
			return $new_rules + $rules;
		}
		add_filter('rewrite_rules_array', 'add_scene_rewrite_rules');

		function remove_scene_slug($post_link, $post, $leavename) {
			if ('scene' != $post->post_type || 'publish' != $post->post_status) {
				return $post_link;
			}
		
			$instance_id = get_post_meta($post->ID, 'scene_location', true);
			$instance = get_post($instance_id);
			$web_slug = get_post_meta($instance_id, 'instance_slug', true);
		
			if (!$instance || !$web_slug) {
				return $post_link;
			}
		
			return home_url('/' . $web_slug . '/' . $post->post_name . '/');
		}
		add_filter('post_type_link', 'remove_scene_slug', 10, 3);

		function add_instance_query_var($vars) {
			$vars[] = 'instance_slug';
			return $vars;
		}
		add_filter('query_vars', 'add_instance_query_var');
		

		// add_filter('post_type_link', 'custom_scene_permalink', 10, 2);

		// function custom_scene_permalink($post_link, $post) {
		// 	if ($post->post_type !== 'scene') {
		// 		return $post_link;
		// 	}

		// 	$instance_id = get_post_meta($post->ID, 'scene_location', true);

		// 	if (!$instance_id) {
		// 		return $post_link;
		// 	}

		// 	$instance = get_post($instance_id);
		// 	$web_slug = get_post_meta($instance_id, 'instance_slug', true);

		// 	if (!$instance || !$web_slug) {
		// 		return $post_link;
		// 	}

		// 	$post_title = strtolower($post->post_title);
		// 	$post_title = str_replace(' ', '_', $post_title);

		// 	return home_url('/' . $web_slug . '/' . $post_title . '/');
		// }

		// Ensure the rewrite rules are flushed when the plugin is activated or deactivated
		register_activation_hook(__FILE__, 'custom_scene_flush_rewrite_rules');
		register_deactivation_hook(__FILE__, 'custom_scene_flush_rewrite_rules');

		function custom_scene_flush_rewrite_rules() {
			custom_scene_rewrite_rules();
			flush_rewrite_rules();
}
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Webcr_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Webcr_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
