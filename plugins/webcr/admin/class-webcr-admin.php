<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.noaa.gov
 * @since      1.0.0
 *
 * @package    Webcr
 * @subpackage Webcr/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Webcr
 * @subpackage Webcr/admin
 * @author     Jai Ranganathan <jai.ranganathan@noaa.gov>
 */
class Webcr_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Webcr_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Webcr_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/webcr-admin.css', array(), $this->version, 'all' );

		wp_enqueue_style(
			'font-awesome-admin', $src =
			'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css', 
			$deps = array(), 
			$ver = '6.6.0'
		);

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts($hook_suffix) {

			$tempo = $hook_suffix;
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Webcr_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Webcr_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/webcr-admin.js', array( 'jquery' ), $this->version, array('strategy'  => 'defer') );

		$current_post_type = get_post_type();

		// Load Instance-specific Javascript only when editing/creating a Instance post 
		if ($current_post_type == "instance" && ($hook_suffix == "post.php" || $hook_suffix == "post-new.php")){
			wp_enqueue_script( "webcr-admin-instance", plugin_dir_url( __FILE__ ) . 'js/webcr-admin-instance.js', array( 'jquery' ), $this->version, array('strategy'  => 'defer') );
		}

		// Load Scene-specific Javascript only when editing/creating a Scene post 
		if ($current_post_type == "scene" && ($hook_suffix == "post.php" || $hook_suffix == "post-new.php")){
			wp_enqueue_script( "webcr-admin-scene", plugin_dir_url( __FILE__ ) . 'js/webcr-admin-scene.js', array( 'jquery' ), $this->version, array('strategy'  => 'defer') );
		}

		// Load Modal-specific Javascript only when editing/creating a Modal post 
		if ($current_post_type == "modal" && ($hook_suffix == "post.php" || $hook_suffix == "post-new.php")){
			wp_enqueue_script( "webcr-admin-modal", plugin_dir_url( __FILE__ ) . 'js/webcr-admin-modal.js', array( 'jquery' ), $this->version, array('strategy'  => 'defer') );
		}

		// Load Modal-specific Javascript only when editing/creating a Modal post 
		if ($current_post_type == "figure" && ($hook_suffix == "post.php" || $hook_suffix == "post-new.php")){
			wp_enqueue_script( "webcr-admin-figure", plugin_dir_url( __FILE__ ) . 'js/webcr-admin-figure.js', array( 'jquery' ), $this->version, array('strategy'  => 'defer') );
		}

		// Load Modal-specific Javascript only for admin columns screen 
		if ($current_post_type == "modal" && $hook_suffix == "edit.php" ){
			wp_enqueue_script( "webcr-admin-modal_columns", plugin_dir_url( __FILE__ ) . 'js/webcr-admin-modal-columns.js', array( 'jquery' ), $this->version, array('strategy'  => 'defer') );
		}

		// Load Figure-specific Javascript only for admin columns screen 
		if ($current_post_type == "figure" && $hook_suffix == "edit.php" ){
			wp_enqueue_script( "webcr-admin-figure_columns", plugin_dir_url( __FILE__ ) . 'js/webcr-admin-figure-columns.js', array( 'jquery' ), $this->version, array('strategy'  => 'defer') );
		}

		// Load Figure Export Javascript, but only when on Figure Export Tool page 
		$current_screen = get_current_screen();
		if ($current_screen-> base == "tools_page_export-figures"){
			wp_enqueue_script( "webcr-admin-figure_export", plugin_dir_url( __FILE__ ) . 'js/webcr-admin-export-figures.js', array( 'jquery' ), $this->version, array('strategy'  => 'defer') );
			// Enqueue Bootstrap JavaScript
			wp_enqueue_script('PptxGenJS', 'https://cdn.jsdelivr.net/npm/pptxgenjs@3.12.0/dist/pptxgen.bundle.js', array('jquery'), '3.12.0', true);

		}
	}

    /**
     * Add new image size for admin thumbnail. Function NOT USED as yet.
     *
     * @link https://wordpress.stackexchange.com/questions/54423/add-image-size-in-a-plugin-i-created/304941#304941
     */
    public function add_thumbnail_size() {
        add_image_size( 'new_thumbnail_size', 60, 75, true );
    }

    /**
	 * Remove the ability to access the Comment content type from the admin bar of the dashboard.
	 *
	 * @since    1.0.0
	 */
    public function remove_comments(){
        global $wp_admin_bar;
        $wp_admin_bar->remove_menu('comments');
    }

	/**
	 * Enqueue Bootstrap (version 5.3.0) CSS and Javascript.
	 *
	 * @since    1.0.0
	 */
	function enqueue_bootstrap_admin() {
		// Enqueue Bootstrap CSS
		wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', array(), '5.3.0');
		
		// Enqueue Bootstrap JavaScript
		wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', array('jquery'), '5.3.0', true);
	}

    /**
	 * Remove the ability to access the Comment content type from the sidebar of the dashboard.
	 *
	 * @since    1.0.0
	 */
    public function remove_comments_menu() {
        remove_menu_page('edit-comments.php');
    }

    /**
	 * Remove remove unwanted widgets from the WordPress dashboard.
	 *
	 * @since    1.0.0
	 */
    public function remove_dashboard_widgets(){
        remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
        remove_meta_box('dashboard_primary', 'dashboard', 'side');
    }

    /**
	 * Remove header row before fields for custom content types.
	 *
	 * @since    1.0.0
	 */
    public function remove_header_row() {
        echo '<style>
        .postbox-header {
            display: none;
            } 
        </style>';
    }
	
    /**
	 * Remove WordPress version number from appearing in the lower right of admin footer.
	 *
	 * @since    1.0.0
	 */
    function wppversionremove() {
        remove_filter( 'update_footer', 'core_update_footer' );
    }

    /**
	 * Remove permalink from edit post admin screens.
	 *
	 * @since    1.0.0
	 */
    function hide_permalink() {
        return '';
    }

    /**
	 * Remove screen options metabox from edit post screens.
	 *
	 * @since    1.0.0
	 */
    function remove_screen_options() {
        return "__return_false";
    }

    /**
	 * Remove  "Thank you for creating with wordpress" from the lower left of the footer of admin screens.
	 *
	 * @since    1.0.0
	 */
    function remove_thank_you() {
        return ; 
    }

    /**
	 * Remove  "Thank you for creating with wordpress" from the lower left of the footer of admin screens.
	 *
	 * @since    1.0.0
	 */
    function remove_gutenberg() {
        return FALSE; 
    }

    /**
	 * Remove "All dates" filter from admin screens.
	 *
	 * @since    1.0.0
	 */
    function remove_all_dates() {
        return array(); 
    }

    /**
	 * Change default favicon associated with site to Sanctuary Watch logo
	 *
	 * @since    1.0.0
	 */
    function add_favicon() {
        $favicon_url = plugin_dir_url( __FILE__ ) . 'images/onms-logo-80.png';
        echo '<link rel="shortcut icon" href="' . $favicon_url . '" />';
   }

	/**
	 * Add Content Manager as a role
	 *
	 * @since    1.0.0
	 */
	function add_content_manager_custom_role() {
		//remove_role( 'webcr_content_manager');
		$content_manager_role_exists = wp_roles()->is_role('webcr_content_manager');
		if ($content_manager_role_exists == false) {
			add_role('webcr_content_manager', __('Content Manager'),
				get_role( 'editor' )->capabilities);
		}
	}

	/**
	 * Edit what the Content Manager can see on the dashboard
	 *
	 * @since    1.0.0
	 */
	function restrict_content_manager_admin_menu() {
		if (current_user_can('webcr_content_manager')) {
		//	remove_menu_page('index.php');                  // Dashboard
			remove_menu_page('edit.php');                   // Posts
		//	remove_menu_page('upload.php');                 // Media
			remove_menu_page('edit.php?post_type=page');    // Pages
		//	remove_menu_page('edit-comments.php');          // Comments
		//	remove_menu_page('themes.php');                 // Appearance
		//	remove_menu_page('plugins.php');                // Plugins
		//	remove_menu_page('users.php');                  // Users
		//	remove_menu_page('tools.php');                  // Tools
		//	remove_menu_page('options-general.php');        // Settings

		}
	}

	// Function to add SVG support
	function allow_svg_uploads($mimes) {
		$mimes['svg'] = 'image/svg+xml';
		return $mimes;
	}


}

