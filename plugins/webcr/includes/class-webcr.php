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

		// The class that defines custom taxonomies
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-webcr-taxonomy.php';

		// The class that defines the functions used to alter the WordPress login screen
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-webcr-login.php';

		// The class that defines the functions used for the Scene custom content type
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-webcr-scene.php';

		// The class that defines the custom post types used
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-webcr-post-types.php';

		// The class that defines the validation methods used for the custom post types
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-webcr-validation.php';

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
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );  
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' ); 
		$this->loader->add_action( 'login_head', $plugin_admin, 'add_favicon' ); 
		$this->loader->add_action( 'admin_head', $plugin_admin, 'add_favicon' ); 
		$this->loader->add_action( 'wp_head', $plugin_admin, 'add_favicon' ); 
		$this->loader->add_action( 'wp_before_admin_bar_render', $plugin_admin, 'remove_comments' ); 
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'remove_comments_menu' ); 
		$this->loader->add_action( 'wp_dashboard_setup', $plugin_admin, 'remove_dashboard_widgets' ); 
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'wppversionremove' ); 
		$this->loader->add_action( 'get_sample_permalink_html', $plugin_admin, 'wppversionremove' ); 
		$this->loader->add_filter( 'get_sample_permalink_html', $plugin_admin, 'hide_permalink' ); 
		$this->loader->add_action( 'admin_head', $plugin_admin, 'remove_header_row' ); 
		$this->loader->add_filter( 'admin_footer_text', $plugin_admin, 'remove_thank_you'); 
		$this->loader->add_filter( 'months_dropdown_results', $plugin_admin, 'remove_all_dates');
		$this->loader->add_filter( 'use_block_editor_for_post', $plugin_admin, 'remove_gutenberg');
		$this->loader->add_filter( 'screen_options_show_screen', $plugin_admin, 'remove_screen_options'); 

		// Load  class and functions associated with Scene custom content type
		$plugin_admin_scene = new Webcr_Scene( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_notices', $plugin_admin_scene, 'scene_admin_notice' ); // scene 
		$this->loader->add_action( 'restrict_manage_posts', $plugin_admin_scene, 'scene_filter_dropdowns' ); //scene 11
		$this->loader->add_action( 'pre_get_posts', $plugin_admin_scene, 'scene_location_filter_results' ); //scene
		$this->loader->add_action( 'admin_menu', $plugin_admin_scene, 'create_scene_fields', 1 ); //scene
		$this->loader->add_action( 'manage_scene_posts_columns', $plugin_admin_scene, 'change_scene_columns' ); //scene
		$this->loader->add_action( 'manage_scene_posts_custom_column', $plugin_admin_scene, 'custom_scene_column', 10, 2 ); //scene
		$this->loader->add_filter( 'manage_edit-scene_sortable_columns', $plugin_admin_scene, 'scene_location_column_sortable' ); //scene
		$this->loader->add_action( 'pre_get_posts', $plugin_admin_scene, 'scene_location_orderby' ); //scene
		$this->loader->add_filter( 'post_row_actions', $plugin_admin_scene, 'scene_remove_quick_edit_link', 10, 2 ); //scene
		$this->loader->add_action( 'init', $plugin_admin_scene, 'custom_content_type_scene' ); //scene
		$this->loader->add_filter( 'bulk_actions-edit-scene', $plugin_admin_scene, 'remove_bulk_actions_scene' ); 
		$this->loader->add_action( 'wp_ajax_scene_preview', $plugin_admin_scene, 'scene_preview' ); //scene

		// Load class and functions connected to custom taxonomies
		$plugin_admin_taxonomy = new Webcr_Taxonomy( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'init', $plugin_admin_taxonomy, 'custom_location_taxonomy', 0);

		// Load class and functions connected to login screen customization
		$plugin_admin_logo = new Webcr_Login( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'login_enqueue_scripts', $plugin_admin_logo, 'webcr_login_logo' ); //login page 5
		$this->loader->add_action( 'login_headerurl', $plugin_admin_logo, 'webcr_logo_url' ); //login page
		$this->loader->add_action( 'login_headertext', $plugin_admin_logo, 'webcr_logo_url_title' ); //login page
		$this->loader->add_filter( 'login_title', $plugin_admin_logo, 'custom_login_title' ); //login page	
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
