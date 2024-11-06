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
		$this->loader->add_filter( 'post_type_link', $plugin_admin, 'custom_permalink', 10, 2); // NEW CLAUDE CODE - PERMALINK STRUCTURE FOR ABOUT AND SCENE CONTENT TYPES
		add_filter( 'xmlrpc_enabled', '__return_false' ); 		//Disable Xlmrpc.php file
		add_filter('screen_options_show_screen', '__return_false'); //Disable Screen Options in admin screens

		// Load  class and functions associated with About custom content type
		$plugin_admin_about = new Webcr_About ( $this->get_plugin_name(), $this->get_version() );		
		$this->loader->add_action( 'init', $plugin_admin_about, 'custom_content_type_about' ); 
		$this->loader->add_action( 'admin_menu', $plugin_admin_about, 'create_about_fields', 1 );
		$this->loader->add_action( 'admin_head-edit.php', $plugin_admin_about, 'modify_about_add_new_button' ); // New Claude function for restricting number of about posts
		$this->loader->add_action( 'admin_notices', $plugin_admin_about, 'display_about_limit_notice' );  // New Claude function for restricting number of about posts
		$this->loader->add_action( 'wp_insert_post_data', $plugin_admin_about, 'prevent_multiple_about_posts', 10, 2);  // New Claude function for restricting number of about posts
		$this->loader->add_action( 'template_redirect', $plugin_admin_about, 'handle_about_template' );  // New Claude function for forcing use of single-about.php file for about posts

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

// NEW AI CODE FOR SITE SETTINGS

// Add menu item to WordPress admin
function webcr_add_admin_menu() {
    add_menu_page(
        'Theme Settings', // Page title
        'Theme Settings', // Menu title
        'manage_options', // Capability required
        'theme_settings', // Menu slug
        'webcr_settings_page' // Function to display the page
    );
}
add_action('admin_menu', 'webcr_add_admin_menu');

// Register settings
function webcr_settings_init() {
    // Register a new settings group
    register_setting('theme_settings_group', 'webcr_settings');

    // Add a new section
    add_settings_section(
        'webcr_settings_section',
        'Theme Display Settings',
        'webcr_settings_section_callback',
        'theme_settings'
    );

    // Add fields to the section
    add_settings_field(
        'intro_text',
        'Front Page Introduction',
        'intro_text_field_callback',
        'theme_settings',
        'webcr_settings_section'
    );

    add_settings_field(
        'multiple_instances',
        'Multiple Instance Types',
        'multiple_instances_field_callback',
        'theme_settings',
        'webcr_settings_section'
    );

    add_settings_field(
        'footer_background',
        'Footer Background Color',
        'footer_background_field_callback',
        'theme_settings',
        'webcr_settings_section'
    );
}
add_action('admin_init', 'webcr_settings_init');

// Section callback
function webcr_settings_section_callback() {
 //   echo '<p>Customize your theme\'s appearance and functionality.</p>';
}

// Field callbacks
function intro_text_field_callback() {
    $options = get_option('webcr_settings');
    $value = isset($options['intro_text']) ? $options['intro_text'] : '';
    ?>
    <textarea name="webcr_settings[intro_text]" rows="5" cols="50"><?php echo esc_textarea($value); ?></textarea>
    <p class="description">This text will appear on your site's front page.</p>
    <?php
}

function multiple_instances_field_callback() {
    $options = get_option('webcr_settings');
    $value = isset($options['multiple_instances']) ? $options['multiple_instances'] : '0';
    ?>
    <input type="checkbox" name="webcr_settings[multiple_instances]" value="1" <?php checked('1', $value); ?>>
    <p class="description">Check this if your site has multiple instance types.</p>
    <?php
}

function footer_background_field_callback() {
    $options = get_option('webcr_settings');
    $value = isset($options['footer_background']) ? $options['footer_background'] : '#ffffff';
    ?>
    <input type="color" name="webcr_settings[footer_background]" value="<?php echo esc_attr($value); ?>">
    <p class="description">Choose the background color for your footer.</p>
    <?php
}

// Create the settings page
function webcr_settings_page() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields('theme_settings_group');
            do_settings_sections('theme_settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Optional: Add settings link on plugin page
function add_settings_link($links) {
    $settings_link = '<a href="admin.php?page=theme_settings">' . __('Settings') . '</a>';
    array_push($links, $settings_link);
    return $links;
}
$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'add_settings_link');

// END CODE FOR SITE SETTINGS


// NEW AI CODE FOR TAXONOMY

// Register the instance_type taxonomy if it doesn't exist
function register_instance_type_taxonomy() {
    if (!taxonomy_exists('instance_type')) {
        register_taxonomy('instance_type', 'post', [
            'hierarchical' => false,
            'labels' => [
                'name' => 'Instance Types',
                'singular_name' => 'Instance Type',
                'menu_name' => 'Instance Types',
                'all_items' => 'All Instance Types',
                'edit_item' => 'Edit Instance Type',
                'view_item' => 'View Instance Type',
                'update_item' => 'Update Instance Type',
                'add_new_item' => 'Add New Instance Type',
                'new_item_name' => 'New Instance Type Name',
                'search_items' => 'Search Instance Types',
            ],
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => ['slug' => 'instance-type'],
        ]);

        // Create the "Main" term if it doesn't exist
        $main_term = term_exists('Main', 'instance_type');
        if (!$main_term) {
            $term = wp_insert_term(
                'Main',                 // The term name
                'instance_type',        // The taxonomy
                array(
                    'slug' => 'main',
                    'description' => 'Front page top text'
                )
            );
            
            if (!is_wp_error($term)) {
                update_term_meta($term['term_id'], 'term_order', -1);
            }
        }
    }
}
add_action('init', 'register_instance_type_taxonomy', 0); // Priority 0 to run early

// Register the order meta field for the taxonomy
function register_instance_type_order_meta() {
    register_meta('term', 'term_order', [
        'type' => 'integer',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'absint',
    ]);
}
add_action('init', 'register_instance_type_order_meta');

// Add the admin menu item
function add_instance_type_admin_menu() {
    add_menu_page(
        'Manage Instance Types',
        'Instance Types',
        'manage_categories',
        'manage-instance-types',
        'render_instance_type_admin_page',
        'dashicons-category',
        20
    );
}
add_action('admin_menu', 'add_instance_type_admin_menu');

// Render the admin page
function render_instance_type_admin_page() {
    // Check if taxonomy exists before proceeding
    if (!taxonomy_exists('instance_type')) {
        echo '<div class="error"><p>Error: The instance_type taxonomy is not properly registered.</p></div>';
        return;
    }

    // Handle form submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'add':
                    if (isset($_POST['term_name']) && isset($_POST['term_order'])) {
                        $term_name = sanitize_text_field($_POST['term_name']);
                        $term_slug = sanitize_title($_POST['term_slug']);
                        $term_description = sanitize_textarea_field($_POST['term_description']);
                        $term_order = absint($_POST['term_order']);
                        
                        $args = array(
                            'slug' => $term_slug,
                            'description' => $term_description
                        );
                        
                        $term = wp_insert_term($term_name, 'instance_type', $args);
                        if (!is_wp_error($term)) {
                            update_term_meta($term['term_id'], 'term_order', $term_order);
                        }
                    }
                    break;
                    
                case 'edit':
                    if (isset($_POST['term_id']) && isset($_POST['term_name']) && isset($_POST['term_order'])) {
                        $term_id = absint($_POST['term_id']);
                        $term_name = sanitize_text_field($_POST['term_name']);
                        $term_slug = sanitize_title($_POST['term_slug']);
                        $term_description = sanitize_textarea_field($_POST['term_description']);
                        $term_order = absint($_POST['term_order']);
                        
                        wp_update_term($term_id, 'instance_type', [
                            'name' => $term_name,
                            'slug' => $term_slug,
                            'description' => $term_description
                        ]);
                        update_term_meta($term_id, 'term_order', $term_order);
                    }
                    break;
                    
                case 'delete':
                    if (isset($_POST['term_id'])) {
                        $term_id = absint($_POST['term_id']);
                        wp_delete_term($term_id, 'instance_type');
                    }
                    break;
            }
        }
    }
    
    // Get all instance_type terms
    $terms = get_terms([
        'taxonomy' => 'instance_type',
        'hide_empty' => false,
    ]);

    // Check if we got an error
    if (is_wp_error($terms)) {
        echo '<div class="error"><p>Error retrieving terms: ' . esc_html($terms->get_error_message()) . '</p></div>';
        return;
    }

    // Convert terms to array if it's not already (for older WordPress versions)
    $terms = is_array($terms) ? $terms : array();
    ?>
    <div class="wrap">
        <h1>Manage Instance Types</h1>
        
        <!-- Add new term form -->
        <h2>Add New Instance Type</h2>
        <form method="post" action="">
            <input type="hidden" name="action" value="add">
            <table class="form-table">
                <tr>
                    <th><label for="term_name">Name</label></th>
                    <td><input type="text" name="term_name" id="term_name" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="term_slug">Slug</label></th>
                    <td><input type="text" name="term_slug" id="term_slug" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="term_description">Description</label></th>
                    <td><textarea name="term_description" id="term_description" class="large-text" rows="5"></textarea></td>
                </tr>
                <tr>
                    <th><label for="term_order">Order</label></th>
                    <td><input type="number" name="term_order" id="term_order" class="small-text" required></td>
                </tr>
            </table>
            <?php submit_button('Add New Instance Type'); ?>
        </form>
        
        <!-- List existing terms -->
        <h2>Existing Instance Types</h2>
        <?php if (empty($terms)): ?>
            <p>No instance types found.</p>
        <?php else: ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Description</th>
                        <th>Order</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($terms as $term): 
                        // Ensure $term is a WP_Term object
                        if (!is_object($term) || !isset($term->term_id)) {
                            continue;
                        }
                        $term_order = get_term_meta($term->term_id, 'term_order', true); 
                    ?>
                        <tr>
                            <td><?php echo esc_html($term->name); ?></td>
                            <td><?php echo esc_html($term->slug); ?></td>
                            <td><?php echo esc_html($term->description); ?></td>
                            <td><?php echo esc_html($term_order); ?></td>
                            <td>
                                <button type="button" class="button" 
                                    onclick="showEditForm(
                                        <?php echo esc_js($term->term_id); ?>,
                                        '<?php echo esc_js($term->name); ?>',
                                        '<?php echo esc_js($term->slug); ?>',
                                        '<?php echo esc_js($term->description); ?>',
                                        <?php echo esc_js($term_order); ?>
                                    )">
                                    Edit
                                </button>
                                <form method="post" action="" style="display: inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="term_id" value="<?php echo esc_attr($term->term_id); ?>">
                                    <button type="submit" class="button" onclick="return confirm('Are you sure you want to delete this term?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        
        <!-- Edit form (hidden by default) -->
        <div id="edit-form" style="display: none;">
            <h2>Edit Instance Type</h2>
            <form method="post" action="">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="term_id" id="edit_term_id">
                <table class="form-table">
                    <tr>
                        <th><label for="edit_term_name">Name</label></th>
                        <td><input type="text" name="term_name" id="edit_term_name" class="regular-text" required></td>
                    </tr>
                    <tr>
                        <th><label for="edit_term_slug">Slug</label></th>
                        <td><input type="text" name="term_slug" id="edit_term_slug" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th><label for="edit_term_description">Description</label></th>
                        <td><textarea name="term_description" id="edit_term_description" class="large-text" rows="5"></textarea></td>
                    </tr>
                    <tr>
                        <th><label for="edit_term_order">Order</label></th>
                        <td><input type="number" name="term_order" id="edit_term_order" class="small-text" required></td>
                    </tr>
                </table>
                <?php submit_button('Update Instance Type'); ?>
            </form>
        </div>
        
        <script>
            function showEditForm(termId, termName, termSlug, termDescription, termOrder) {
                document.getElementById('edit-form').style.display = 'block';
                document.getElementById('edit_term_id').value = termId;
                document.getElementById('edit_term_name').value = termName;
                document.getElementById('edit_term_slug').value = termSlug;
                document.getElementById('edit_term_description').value = termDescription;
                document.getElementById('edit_term_order').value = termOrder;
            }
        </script>
    </div>
    <?php
}
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
