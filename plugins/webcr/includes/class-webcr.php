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

		// The class that defines the metaboxes used for field entry
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/exopite-simple-options/exopite-simple-options-framework-class.php';

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

		// The class that defines the functions used for the About custom content type
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-webcr-about.php';

		// The class that defines the functions used for the Export Figures Tool
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-webcr-export-figures.php';

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
		$this->loader->add_filter( 'upload_mimes', $plugin_admin, 'allow_svg_uploads'); 
		$this->loader->add_filter( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_bootstrap_admin', 5); 
		add_filter( 'xmlrpc_enabled', '__return_false' ); 		//Disable Xlmrpc.php file
		add_filter('screen_options_show_screen', '__return_false'); //Disable Screen Options in admin screens

		// Load  class and functions associated with About custom content type
		$plugin_admin_about = new Webcr_About ( $this->get_plugin_name(), $this->get_version() );		
		$this->loader->add_action( 'init', $plugin_admin_about, 'custom_content_type_about' ); 
		$this->loader->add_action( 'admin_menu', $plugin_admin_about, 'create_about_fields', 1 );

		// Load  class and functions associated with Instance custom content type
		$plugin_admin_instance = new Webcr_Instance ( $this->get_plugin_name(), $this->get_version() );		
		$this->loader->add_action( 'init', $plugin_admin_instance, 'custom_content_type_instance' ); 
		$this->loader->add_action( 'admin_menu', $plugin_admin_instance, 'create_instance_fields', 1 );
		$this->loader->add_action( 'manage_instance_posts_columns', $plugin_admin_instance, 'change_instance_columns' ); 
		$this->loader->add_action( 'manage_instance_posts_custom_column', $plugin_admin_instance, 'custom_instance_column', 10, 2 ); 
		$this->loader->add_filter( 'bulk_actions-edit-instance', $plugin_admin_instance, 'remove_bulk_actions' ); 
		$this->loader->add_filter( 'post_row_actions', $plugin_admin_instance, 'custom_content_remove_quick_edit_link', 10, 2 ); 
		$this->loader->add_filter( 'rest_api_init', $plugin_admin_instance, 'register_instance_rest_fields' ); 

		// Load  class and functions associated with Scene custom content type
		$plugin_admin_scene = new Webcr_Scene( $this->get_plugin_name(), $this->get_version() );		
		$this->loader->add_action( 'admin_notices', $plugin_admin_scene, 'scene_admin_notice' ); 
		$this->loader->add_action( 'restrict_manage_posts', $plugin_admin_scene, 'scene_filter_dropdowns' ); 
		$this->loader->add_action( 'pre_get_posts', $plugin_admin_scene, 'scene_location_filter_results' ); 
		$this->loader->add_action( 'admin_menu', $plugin_admin_scene, 'create_scene_fields', 1 ); 
		$this->loader->add_action( 'manage_scene_posts_columns', $plugin_admin_scene, 'change_scene_columns' ); 
		$this->loader->add_action( 'manage_scene_posts_custom_column', $plugin_admin_scene, 'custom_scene_column', 10, 2 ); 
		$this->loader->add_filter( 'manage_edit-scene_sortable_columns', $plugin_admin_scene, 'scene_location_column_sortable' ); 
		$this->loader->add_action( 'pre_get_posts', $plugin_admin_scene, 'scene_location_orderby' ); 
		$this->loader->add_action( 'init', $plugin_admin_scene, 'custom_content_type_scene' ); 
		$this->loader->add_filter( 'bulk_actions-edit-scene', $plugin_admin_instance, 'remove_bulk_actions' ); 
		$this->loader->add_action( 'wp_ajax_scene_preview', $plugin_admin_scene, 'scene_preview' ); 
		$this->loader->add_action( 'post_row_actions', $plugin_admin_scene, 'modify_scene_quick_edit_link', 10, 2 ); 
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin_scene, 'enqueue_scene_admin_columns_css'); 
		$this->loader->add_action( 'rest_api_init', $plugin_admin_scene, 'register_scene_rest_fields'); 
		$this->loader->add_filter( 'rest_scene_query', $plugin_admin_scene, 'filter_scene_by_scene_location', 10, 2); 
	//	$this->loader->add_filter( 'rewrite_rules_array', $plugin_admin_scene, 'add_scene_rewrite_rules'); 
//		$this->loader->add_filter( 'post_type_link', $plugin_admin_scene, 'remove_scene_slug', 10, 3); 

		// Load  class and functions associated with Modal custom content type
		$plugin_admin_modal = new Webcr_Modal ( $this->get_plugin_name(), $this->get_version() );		
		$this->loader->add_action( 'admin_notices', $plugin_admin_modal, 'modal_admin_notice' ); 
		$this->loader->add_action( 'restrict_manage_posts', $plugin_admin_modal, 'modal_filter_dropdowns' ); 
		$this->loader->add_action( 'pre_get_posts', $plugin_admin_modal, 'modal_location_filter_results' ); 
		$this->loader->add_action( 'admin_menu', $plugin_admin_modal, 'create_modal_fields', 1 ); 
		$this->loader->add_action( 'manage_modal_posts_columns', $plugin_admin_modal, 'change_modal_columns' ); 
		$this->loader->add_action( 'manage_modal_posts_custom_column', $plugin_admin_modal, 'custom_modal_column', 10, 2 ); 
		$this->loader->add_action( 'init', $plugin_admin_modal, 'custom_content_type_modal' );
		$this->loader->add_filter( 'bulk_actions-edit-modal', $plugin_admin_instance, 'remove_bulk_actions' ); 
		$this->loader->add_action( 'rest_api_init', $plugin_admin_modal, 'register_modal_rest_fields' );
		$this->loader->add_filter( 'rest_modal_query', $plugin_admin_modal, 'filter_modal_by_modal_scene', 10, 2); 

		// Load  class and functions associated with Figure custom content type
		$plugin_admin_figure = new Webcr_Figure( $this->get_plugin_name());		
		$this->loader->add_action( 'init', $plugin_admin_figure, 'custom_content_type_figure' ); 
		$this->loader->add_action( 'admin_menu', $plugin_admin_figure, 'create_figure_fields', 1 );
		$this->loader->add_action( 'manage_figure_posts_columns', $plugin_admin_figure, 'change_figure_columns' ); 
		$this->loader->add_action( 'manage_figure_posts_custom_column', $plugin_admin_figure, 'custom_figure_column', 10, 2 ); 
		$this->loader->add_action( 'restrict_manage_posts', $plugin_admin_figure, 'figure_filter_dropdowns' ); 
		$this->loader->add_action( 'pre_get_posts', $plugin_admin_figure, 'figure_location_filter_results' ); 
		$this->loader->add_action( 'admin_notices', $plugin_admin_figure, 'figure_admin_notice' ); 
		$this->loader->add_filter( 'bulk_actions-edit-figure', $plugin_admin_instance, 'remove_bulk_actions' ); 
		$this->loader->add_action( 'rest_api_init', $plugin_admin_figure, 'register_figure_rest_fields' ); 
		$this->loader->add_filter( 'rest_figure_query', $plugin_admin_figure, 'filter_figure_by_figure_modal', 10, 2); 

		// Load class and functions connected to login screen customization
		$plugin_admin_logo = new Webcr_Login( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'login_enqueue_scripts', $plugin_admin_logo, 'webcr_login_logo' ); 
		$this->loader->add_action( 'login_headerurl', $plugin_admin_logo, 'webcr_logo_url' ); 
		$this->loader->add_action( 'login_headertext', $plugin_admin_logo, 'webcr_logo_url_title' ); 
		$this->loader->add_filter( 'login_title', $plugin_admin_logo, 'custom_login_title' ); 	

		// Load class and functions connected with Export Figures Tool
		$plugin_admin_export_figures = new Webcr_Export_Figures( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_menu', $plugin_admin_export_figures, 'add_export_figures_menu' ); 

		// BEGIN AI CODE

// Custom permalink structure
add_filter('post_type_link', 'custom_permalink_structure', 10, 2);
function custom_permalink_structure($post_link, $post) {
    if ($post->post_type === 'scene') {
        $scene_location = get_post_meta($post->ID, 'scene_location', true);
        if ($scene_location) {
            $instance = get_post($scene_location);
            if ($instance) {
                $instance_slug = get_post_meta($instance->ID, 'instance_slug', true);
                if ($instance_slug) {
                    return home_url("/{$instance_slug}/{$post->post_name}/");
                }
            }
        }
    } elseif ($post->post_type === 'about') {
        return home_url('/about/');
    }
    
    return $post_link;
}

// Modified About post check function with debugging
function check_existing_about_posts() {
    $args = array(
        'post_type' => 'about',
        'post_status' => array('publish', 'draft', 'pending', 'private', 'future', 'trash'),
        'posts_per_page' => -1,
        'fields' => 'ids', // Only get post IDs for efficiency
    );
    
    $existing_about = get_posts($args);
    $count = count($existing_about);
    
    // Optional debugging - remove in production
    if (defined('WP_DEBUG') && WP_DEBUG === true) {
        error_log('About Posts Check - Count: ' . $count);
        error_log('About Posts IDs: ' . print_r($existing_about, true));
    }
    
    return $count;
}

// Add admin notice functionality
add_action('admin_notices', 'display_about_limit_notice');
function display_about_limit_notice() {
    if (isset($_GET['about_limit_reached'])) {
        ?>
        <div class="notice notice-error is-dismissible">
            <p><?php _e('Only one About page can exist. Your new About page was not created.', 'your-text-domain'); ?></p>
        </div>
        <?php
    }
}

// Modified prevention function with better handling
add_action('wp_insert_post_data', 'prevent_multiple_about_posts', 10, 2);
function prevent_multiple_about_posts($data, $postarr) {
    // Only run this check for About post type
    if ($data['post_type'] !== 'about') {
        return $data;
    }

    // Allow updates to existing About posts
    if (!empty($postarr['ID'])) {
        return $data;
    }
    
    // Check if an About post already exists
    $existing_count = check_existing_about_posts();
    
    // Optional debugging - remove in production
    if (defined('WP_DEBUG') && WP_DEBUG === true) {
        error_log('Attempting to create About post');
        error_log('Existing count: ' . $existing_count);
    }
    
    if ($existing_count > 0) {
        // Store the redirect URL with query parameter
        $redirect_url = add_query_arg(
            'about_limit_reached', 
            '1', 
            admin_url('edit.php?post_type=about')
        );
        
        // Redirect and stop post creation
        wp_safe_redirect($redirect_url);
        exit();
    }
    
    return $data;
}

// Modified button visibility function
add_action('admin_head-edit.php', 'modify_about_add_new_button');
function modify_about_add_new_button() {
    global $current_screen;
    
    if ($current_screen->post_type === 'about') {
        $existing_count = check_existing_about_posts();
        
        if ($existing_count > 0) {
            ?>
            <style>
                .page-title-action {
                    display: none !important;
                }
            </style>
            <?php
        }
    }
}

// Add function to verify post type registration
add_action('init', 'verify_about_post_type', 999);
function verify_about_post_type() {
    if (defined('WP_DEBUG') && WP_DEBUG === true) {
        $post_type = get_post_type_object('about');
        if ($post_type) {
            error_log('About post type is registered properly');
//            error_log('About post type capabilities: ' . print_r($post_type->capabilities, true));
        } else {
            error_log('About post type is NOT registered properly');
        }
    }
}

// Modified rewrite rules
add_action('init', 'add_custom_rewrite_rules');
function add_custom_rewrite_rules() {
    // Rule for scenes
    add_rewrite_rule(
        '^([^/]+)/([^/]+)/?$',
        'index.php?post_type=scene&name=$2',
        'top'
    );
    
    // Modified rule for about page
    add_rewrite_rule(
        '^about/?$',
        'index.php?post_type=about&about=true',
        'top'
    );
}

// Add custom query var
add_filter('query_vars', 'add_custom_query_vars');
function add_custom_query_vars($vars) {
    $vars[] = 'about';
    return $vars;
}

// Modify the main query for the about page
add_action('pre_get_posts', 'modify_about_query');
function modify_about_query($query) {
    if (!is_admin() && $query->is_main_query() && get_query_var('about') === 'true') {
        $query->set('post_type', 'about');
        $query->set('posts_per_page', 1);
        $query->is_single = true;
        $query->is_singular = true;
        $query->is_home = false;
        $query->is_archive = false;
        $query->is_page = false;
    }
}

// Template hierarchy filter
add_filter('template_include', 'custom_template_include');
function custom_template_include($template) {
    if (get_query_var('about') === 'true') {
        // Look for single-about.php in theme directory
        $new_template = locate_template(array('single-about.php'));
        if (!empty($new_template)) {
            return $new_template;
        }
        // Fall back to single.php if single-about.php doesn't exist
        $new_template = locate_template(array('single.php'));
        if (!empty($new_template)) {
            return $new_template;
        }
    }
    return $template;
}

// Register activation hook to flush rewrite rules
register_activation_hook(__FILE__, 'flush_rewrite_rules');

// Register deactivation hook to flush rewrite rules
register_deactivation_hook(__FILE__, 'flush_rewrite_rules');

		// END AI CODE

		// Do the following rewrite rules do anything? Commenting them out just to see
		// Ensure the rewrite rules are flushed when the plugin is activated or deactivated - ask skanda
		//		register_activation_hook(__FILE__, 'custom_scene_flush_rewrite_rules');
		//		register_deactivation_hook(__FILE__, 'custom_scene_flush_rewrite_rules');

		//		function custom_scene_flush_rewrite_rules() {
		//			custom_scene_rewrite_rules();
		//			flush_rewrite_rules();
		//		}

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
