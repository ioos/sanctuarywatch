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

		// The class that defines the functions used for the Modal custom content type
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-webcr-modal.php';

		// The class that defines the functions used for the Figure custom content type
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-webcr-figure.php';

		// The class that defines the functions used for the Instance custom content type
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-webcr-instance.php';

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

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles', 10 );  
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts', 10 ); 
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
		$this->loader->add_filter( 'init', $plugin_admin, 'add_content_manager_custom_role'); 
		$this->loader->add_filter( 'admin_menu', $plugin_admin, 'restrict_content_manager_admin_menu', 999); 

		// Load  class and functions associated with Instance custom content type
		$plugin_admin_instance = new Webcr_Instance ( $this->get_plugin_name(), $this->get_version() );		
		$this->loader->add_action( 'init', $plugin_admin_instance, 'custom_content_type_instance' ); //scene
		$this->loader->add_action( 'admin_menu', $plugin_admin_instance, 'create_instance_fields', 1 );
		$this->loader->add_action( 'manage_instance_posts_columns', $plugin_admin_instance, 'change_instance_columns' ); //scene
		$this->loader->add_action( 'manage_instance_posts_custom_column', $plugin_admin_instance, 'custom_instance_column', 10, 2 ); //scene
		$this->loader->add_filter( 'bulk_actions-edit-instance', $plugin_admin_instance, 'remove_bulk_actions' ); 
		$this->loader->add_filter( 'post_row_actions', $plugin_admin_instance, 'custom_content_remove_quick_edit_link', 10, 2 ); //scene

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
		$this->loader->add_action( 'init', $plugin_admin_scene, 'custom_content_type_scene' ); //scene
		$this->loader->add_filter( 'bulk_actions-edit-scene', $plugin_admin_instance, 'remove_bulk_actions' ); 
		$this->loader->add_action( 'wp_ajax_scene_preview', $plugin_admin_scene, 'scene_preview' ); //scene

		// Load  class and functions associated with Modal custom content type
		$plugin_admin_modal = new Webcr_Modal ( $this->get_plugin_name(), $this->get_version() );		
		$this->loader->add_action( 'admin_notices', $plugin_admin_modal, 'modal_admin_notice' ); // scene 
		$this->loader->add_action( 'restrict_manage_posts', $plugin_admin_modal, 'modal_filter_dropdowns' ); //scene 11
		$this->loader->add_action( 'pre_get_posts', $plugin_admin_modal, 'modal_location_filter_results' ); //scene
		$this->loader->add_action( 'admin_menu', $plugin_admin_modal, 'create_modal_fields', 1 );
		$this->loader->add_action( 'manage_modal_posts_columns', $plugin_admin_modal, 'change_modal_columns' ); //scene
		$this->loader->add_action( 'manage_modal_posts_custom_column', $plugin_admin_modal, 'custom_modal_column', 10, 2 ); //scene
		$this->loader->add_action( 'init', $plugin_admin_modal, 'custom_content_type_modal' ); // scene 
		$this->loader->add_filter( 'bulk_actions-edit-modal', $plugin_admin_instance, 'remove_bulk_actions' ); 

		// Load  class and functions associated with Figure custom content type
		$plugin_admin_figure = new Webcr_Figure( $this->get_plugin_name());		
		$this->loader->add_action( 'init', $plugin_admin_figure, 'custom_content_type_figure' ); //scene
		$this->loader->add_action( 'admin_menu', $plugin_admin_figure, 'create_figure_fields', 1 );
		$this->loader->add_action( 'manage_figure_posts_columns', $plugin_admin_figure, 'change_figure_columns' ); //scene
		$this->loader->add_action( 'manage_figure_posts_custom_column', $plugin_admin_figure, 'custom_figure_column', 10, 2 ); //scene
		$this->loader->add_action( 'restrict_manage_posts', $plugin_admin_figure, 'figure_filter_dropdowns' ); //scene 11
		$this->loader->add_action( 'pre_get_posts', $plugin_admin_figure, 'figure_location_filter_results' ); //scene
		$this->loader->add_action( 'admin_notices', $plugin_admin_figure, 'figure_admin_notice' ); // scene 
		$this->loader->add_filter( 'bulk_actions-edit-figure', $plugin_admin_instance, 'remove_bulk_actions' ); 

		// Load class and functions connected to custom taxonomies
		$plugin_admin_taxonomy = new Webcr_Taxonomy( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'init', $plugin_admin_taxonomy, 'custom_location_taxonomy', 0);

		// Load class and functions connected to login screen customization
		$plugin_admin_logo = new Webcr_Login( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'login_enqueue_scripts', $plugin_admin_logo, 'webcr_login_logo' ); //login page 5
		$this->loader->add_action( 'login_headerurl', $plugin_admin_logo, 'webcr_logo_url' ); //login page
		$this->loader->add_action( 'login_headertext', $plugin_admin_logo, 'webcr_logo_url_title' ); //login page
		$this->loader->add_filter( 'login_title', $plugin_admin_logo, 'custom_login_title' ); //login page	

		add_action('admin_enqueue_scripts', 'enqueue_bootstrap_admin', 5);

		function enqueue_bootstrap_admin() {
			// Enqueue Bootstrap CSS
			wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', array(), '5.3.0');
			
			// Enqueue Bootstrap JavaScript
			wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', array('jquery'), '5.3.0', true);
		}

		//Disable Xlmrpc.php file
		add_filter( 'xmlrpc_enabled', '__return_false' );

		//Disable Screen Options in admin screens
		add_filter('screen_options_show_screen', '__return_false');

		function register_instance_rest_fields(){

			$instance_rest_fields = array('instance_short_title', 'instance_slug',
				'instance_type', 'instance_status', 'instance_tile', 'instance_toc_style',
				'instance_colored_sections', 'instance_hover_color', 
				'instance_full_screen_button', 'instance_text_toggle');
	
				$function_utilities = new Webcr_Utility();
				$function_utilities -> register_custom_rest_fields("instance", $instance_rest_fields);
		}

		add_action('rest_api_init', 'register_instance_rest_fields');

		function register_scene_rest_fields() {
			$scene_rest_fields = array('scene_location', 'scene_infographic', 'scene_tagline',
				'scene_info_entries', 'scene_photo_entries', 'scene_section_number');

			for ($i = 1; $i < 7; $i++){
				array_push($scene_rest_fields,'scene_info' . $i, 'scene_photo' . $i);
			}
			$function_utilities = new Webcr_Utility();
			$function_utilities -> register_custom_rest_fields("scene", $scene_rest_fields);
		}

		add_action('rest_api_init', 'register_scene_rest_fields');

		function register_modal_rest_fields() {
			$modal_rest_fields = array('modal_scene','modal_tagline', 'icon_function','modal_info_entries', 
				'modal_photo_entries', 'modal_tab_number');

				for ($i = 1; $i < 7; $i++){
					array_push($modal_rest_fields,'modal_info' . $i, 'modal_photo' . $i, 'modal_tab_title' . $i );
				}
				$function_utilities = new Webcr_Utility();
				$function_utilities -> register_custom_rest_fields("modal", $modal_rest_fields);
		}

		add_action('rest_api_init', 'register_modal_rest_fields');

		function register_figure_rest_fields() {
			$figure_rest_fields = array('figure_modal', 'figure_tab', 'figure_order', 'figure_science_info', 'figure_data_info', 'figure_path', 'figure_image', 'figure_external_url', 'figure_caption_short', 'figure_caption_long');
			$function_utilities = new Webcr_Utility();
			$function_utilities -> register_custom_rest_fields("figure", $figure_rest_fields);
		}

		add_action('rest_api_init', 'register_figure_rest_fields');


		// Add the filter to support filtering by "scene_location" in REST API queries
		function filter_scene_by_scene_location($args, $request) {
			if (isset($request['scene_location'])) {
				$args['meta_query'][] = array(
					'key' => 'scene_location',
					'value' => $request['scene_location'],
					'compare' => 'LIKE', // Change comparison method as needed
				);
			}
			return $args;
		}

		add_filter('rest_scene_query', 'filter_scene_by_scene_location', 10, 2);

		// Hook into init to add rewrite rules
//		add_action('init', 'custom_scene_rewrite_rules');

		function custom_scene_rewrite_rules() {
			add_rewrite_rule(
				'^([^/]+)/([^/]+)/?$',
				'index.php?scene=$matches[2]',
				'top'
			);
		}

		// Hook into post_type_link to customize the permalink for Scene posts - SKANDA COMMENT OUT NEXT LINE
		// add_filter('post_type_link', 'custom_scene_permalink', 10, 2);

		function custom_scene_permalink($post_link, $post) {
			if ($post->post_type !== 'scene') {
				return $post_link;
			}

			$instance_id = get_post_meta($post->ID, 'scene_location', true);

			if (!$instance_id) {
				return $post_link;
			}

			$instance = get_post($instance_id);
			$web_slug = get_post_meta($instance_id, 'instance_slug', true);

			if (!$instance || !$web_slug) {
				return $post_link;
			}

			$post_title = strtolower($post->post_title);
			$post_title = str_replace(' ', '_', $post_title);

			return home_url('/' . $web_slug . '/' . $post_title . '/');
		}

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
