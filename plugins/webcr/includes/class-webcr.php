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
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
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
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Webcr_Loader. Orchestrates the hooks of the plugin.
	 * - Webcr_i18n. Defines internationalization functionality.
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
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-webcr-i18n.php';

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

		// The class that creates the location taxonomy
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-webcr-taxonomy.php';

		// The class that defines the custom post types used
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-webcr-post-types.php';

		// The class that defines the validation methods used for the custom post types
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-webcr-validation.php';

		$this->loader = new Webcr_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Webcr_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Webcr_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Webcr_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'login_enqueue_scripts', $plugin_admin, 'webcr_login_logo' );
		$this->loader->add_action( 'login_headerurl', $plugin_admin, 'webcr_logo_url' );
		$this->loader->add_action( 'login_headertext', $plugin_admin, 'webcr_logo_url_title' );
		$this->loader->add_action( 'login_head', $plugin_admin, 'add_favicon' );
		$this->loader->add_action( 'admin_head', $plugin_admin, 'add_favicon' );
		$this->loader->add_action( 'wp_head', $plugin_admin, 'add_favicon' );
		$this->loader->add_filter( 'login_title', $plugin_admin, 'custom_login_title' );
		$this->loader->add_filter( 'login_title', $plugin_admin, 'custom_login_title' );

		// JAI - add warning
		// JAI URL for warning messages https://www.wpbeginner.com/wp-tutorials/how-to-add-admin-notices-in-wordpress/
		// five classes: notice-warning, notice-error, notice-info, notice-success, plus is-dismissable
		function webcr_admin_notice() {
			// First let's determine where we are. We only want to show admin notices in the right places. Namely in one of our custom 
			// posts after it has been updated. The if statement is looking for three things: 1. Scene post type? 2. An individual post (as opposed to the scene
			// admin screen)? 3. A new post?
			$current_screen = get_current_screen();
			if ($current_screen->base == "post" && $current_screen->id =="scene" && !($current_screen->action =="add") ) { 
				if( isset( $_COOKIE["scene_post_status"] ) ) {
					$scene_post_status =  $_COOKIE["scene_post_status"];
					if ($scene_post_status == "post_good") {
						echo '<div class="notice notice-info is-dismissible"><p>Scene created or updated.</p></div>';
					} 
					else {
						$error_message = "<p>Error or errors in scene</p>";
						if (isset($_COOKIE["scene_errors"])) {
							$error_list_coded = stripslashes($_COOKIE["scene_errors"]);
							$error_list_array = json_decode($error_list_coded);
							$error_array_length = count($error_list_array);
							$error_message = $error_message . '<p><ul>';
							for ($i = 0; $i < $error_array_length; $i++){
								$error_message = $error_message . '<li>' . $error_list_array[$i] . '</li>';
							}
							$error_message = $error_message . '</ul></p>';
						}
						echo '<div class="notice notice-error is-dismissible">' . $error_message . '</div>'; 

						if (isset($_COOKIE["scene_error_all_fields"])) {
							$scene_fields_coded = stripslashes($_COOKIE["scene_error_all_fields"]);
							$scene_fields_array = json_decode($scene_fields_coded, true);		
							$fg = 55;				
						//	echo "<script>console.log(" . count($scene_fields_array) . ");</script>";
							$_POST['scene_info_link'] = $scene_fields_array['scene_info_link'];
						}


					}
					setcookie("scene_post_status", "", time() - 300, "/");

				}
			}
		}
		add_action( 'admin_notices', 'webcr_admin_notice' );

		//JAI - adjust length of output in columns for scene admin table
		function scene_output_length () {
		
			$fieldOptions = array(
				array("", "large", "Full values"),
				array("", "medium", "Medium values"),
				array("", "small", "Short values")
			);

			if (isset($_GET["field_length"])) {
				$field_length = $_GET["field_length"];
				switch ($field_length){
					case "large":
						$fieldOptions[0][0] = "selected ";
						break;
					case "medium":
						$fieldOptions[1][0] = "selected ";
						break;
					case "small":
						$fieldOptions[2][0] = "selected ";
						break;
				}
			}

			$field_length_dropdown = '<select name="field_length" id="field_length">';
			for ($i=0; $i <3; $i++){
				$field_length_dropdown .= '<option ' . $fieldOptions[$i][0] .  'value="' . $fieldOptions[$i][1] .'">' . $fieldOptions[$i][2] . '</option>';
			}
			$field_length_dropdown .= '</select>';

			echo $field_length_dropdown;
		}
		add_action('restrict_manage_posts', 'scene_output_length');

		// JAI CREATE CUSTOM CONTENT TYPES
		$plugin_post_types = new Plugin_Name_Post_Types();
		//JAI - add Scene custom content type
		// $this->loader->add_action( 'init', $plugin_post_types, 'create_custom_post_type_scene', 999 );

		// Jai - remove permalink field from admin screens
		function hide_permalink() {
			return '';
		}
		add_filter( 'get_sample_permalink_html', 'hide_permalink' );

		//JAI Get rid of screen options tab
		add_filter('screen_options_show_screen', '__return_false');

		//JAI - remove admin footers - that is, "Thank you for creating with wordpress" in lower left, plus wordpress version in lower right
		add_filter( 'admin_footer_text', '__return_false' );

		function wppversionremove() {
			remove_filter( 'update_footer', 'core_update_footer' );
		}
		add_action( 'admin_menu', 'wppversionremove' );

		// JAI - get rid of gutenberg
        add_filter('use_block_editor_for_post', '__return_false', 10);
		//JAI - remove header row before fields in custom content types
		function my_custom_fonts() {
			echo '<style>
			.postbox-header {
				display: none;
				} 
			</style>';
			}

		add_action('admin_head', 'my_custom_fonts');
		
		// JAI - add custom LOCATION taxonomy
		add_action( 'init', 'custom_location_taxonomy', 0 );

		//JAI - add exopite fields to scene custom content type
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'create_scene_fields', 1 );

		//JAI - change admin columns for scene custom content type	
			// https://www.smashingmagazine.com/2017/12/customizing-admin-columns-wordpress/

		function change_scene_columns( $columns ) {
			$columns['scene_location'] = 'Location';
			$columns['scene_infographic'] = 'Infographic';		
			$columns['scene_tagline'] = 'Tagline';			
			$columns['scene_info_link'] = 'Info Link';		
			$columns['scene_info_photo_link'] = 'Photo Link';
			$columns['scene_order'] = 'Order';					
			return $columns;
		}

		add_filter( 'manage_scene_posts_columns', 'change_scene_columns' );

		//JAI - function for shortening string without chopping words
		function tokenTruncate($string, $your_desired_width) {
			$parts = preg_split('/([\s\n\r]+)/', $string, null, PREG_SPLIT_DELIM_CAPTURE);
			$parts_count = count($parts);
		  
			$length = 0;
			//$last_part = 0;
			for ($last_part = 0; $last_part < $parts_count; ++$last_part) {
			  $length += strlen($parts[$last_part]);
			  if ($length > $your_desired_width) { break; }
			}
		  
			return implode(array_slice($parts, 0, $last_part));
		  }

		function custom_scene_column( $column, $post_id ) {  
			// scene location column

			if (isset($_GET["field_length"])) {
				$field_length = $_GET["field_length"];
			} else {
				$field_length = "large";
			}

			if ( $column === 'scene_location' ) {
				echo get_post_meta( $post_id, 'scene_location', true ); 
			}

			if ( $column === 'scene_infographic' ) {
					$scene_infographic = get_post_meta($post_id, 'scene_infographic', true);
					if (!empty($scene_infographic)) {
							echo '<img src="' . esc_url($scene_infographic) . '" style="max-width:100px; max-height:100px;" /><br>';
					}
			}

			if ($column == 'scene_tagline'){
				$scene_tagline = get_post_meta( $post_id, 'scene_tagline', true );
				switch ($field_length){
					case "large":
						echo $scene_tagline;
						break;
					case "medium":
						echo tokenTruncate($scene_tagline, 75);
						break;
					case "small":
						if ($scene_tagline != NULL){
							echo '<span class="dashicons dashicons-yes"></span>';
						}
						break;
				}
			}

			if ($column == 'scene_info_photo_link'){
				$photo_link_value = get_post_meta( $post_id, 'scene_info_photo_link', true );
				switch ($field_length){
					case "large":
						echo $photo_link_value;
						break;
					case "medium":
						echo substr($photo_link_value, 0, 40);
						break;
					case "small":
						if ($photo_link_value != NULL){
							echo '<span class="dashicons dashicons-yes"></span>';
						}
						break;
				}
			}

			if ($column == 'scene_info_link'){
				$link_value = get_post_meta( $post_id, 'scene_info_link', true );
				switch ($field_length){
					case "large":
						echo $link_value;
						break;
					case "medium":
						echo substr($link_value, 0, 40);
						break;
					case "small":
						if ($link_value != NULL){
							echo '<span class="dashicons dashicons-yes"></span>';
						}
						break;
				}
			}
			if ( $column === 'scene_order' ) {
				echo get_post_meta( $post_id, 'scene_order', true ); 
			}

		}
		add_action( 'manage_scene_posts_custom_column', 'custom_scene_column', 10, 2);


			// JAI - new function for adding scenes 
			function custom_content_type_scene() {
				$labels = array(
					'name'                  => _x( 'Scenes', 'Post type general name', 'textdomain' ),
					'singular_name'         => _x( 'Scene', 'Post type singular name', 'textdomain' ),
					'menu_name'             => _x( 'Scenes', 'Admin Menu text', 'textdomain' ),
					'name_admin_bar'        => _x( 'Scene', 'Add New on Toolbar', 'textdomain' ),
					'add_new'               => __( 'Add New Scene', 'textdomain' ),
					'add_new_item'          => __( 'Add New Scene', 'textdomain' ),
					'new_item'              => __( 'New Scene', 'textdomain' ),
					'edit_item'             => __( 'Edit Scene', 'textdomain' ),
					'view_item'             => __( 'View Scene', 'textdomain' ),
					'all_items'             => __( 'All Scenes', 'textdomain' ),
					'search_items'          => __( 'Search Scenes', 'textdomain' ),
					'parent_item_colon'     => __( 'Parent Scenes:', 'textdomain' ),
					'not_found'             => __( 'No Scenes found.', 'textdomain' ),
					'not_found_in_trash'    => __( 'No Scenes found in Trash.', 'textdomain' ),
					'featured_image'        => _x( 'Scene Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'textdomain' ),
					'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
					'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
					'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
					'archives'              => _x( 'Scene archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'textdomain' ),
					'insert_into_item'      => _x( 'Insert into Scene', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'textdomain' ),
					'uploaded_to_this_item' => _x( 'Uploaded to this Scene', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'textdomain' ),
					'filter_items_list'     => _x( 'Filter Scenes list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'textdomain' ),
					'items_list_navigation' => _x( 'Scenes list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'textdomain' ),
					'items_list'            => _x( 'Scenes list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'textdomain' ),
				);
			
				$args = array(
					'labels'             => $labels,
					'public'             => true,
					'publicly_queryable' => true,
					'show_ui'            => true,
					'show_in_menu'       => true,
					'query_var'          => true,
					'rewrite'            => array( 'slug' => 'scenes' ),
					'capability_type'    => 'post',
					'menu_icon'          => 'dashicons-tag',
					'has_archive'        => true,
					'hierarchical'       => false,
					'menu_position'      => null,
					'supports'           => array( 'title', 'revisions' ),
				);
			
				register_post_type( 'scene', $args );
			}
			
			add_action( 'init', 'custom_content_type_scene' );

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
