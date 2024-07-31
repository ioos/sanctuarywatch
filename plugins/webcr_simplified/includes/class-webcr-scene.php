<?php
/**
 * Register class that defines the Scene custom content type as well as associated Scene functions
 * 
 */
class Webcr_Scene {

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
	 * Create Scene custom content type.
	 *
	 * @since    1.0.0
	 */
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
            'show_in_rest'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'scenes' ),
            'capability_type'    => 'post',
            'menu_icon'          => 'dashicons-tag',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array( 'title'), //array( 'title', 'revisions' ),
        );
    
        register_post_type( 'scene', $args );
    }

    /**
	 * Create custom fields, using metaboxes, for Scene custom content type.
	 *
	 * @since    1.0.0
	 */
    public function create_scene_fields() {

        $config_metabox = array(

            /*
             * METABOX
             */
            'type'              => 'metabox',                       // Required, menu or metabox
            'id'                => $this->plugin_name,              // Required, meta box id, unique, for saving meta: id[field-id]
            'post_types'        => array( 'scene' ),                 // Post types to display meta box
            'context'           => 'advanced',                      // 	The context within the screen where the boxes should display: 'normal', 'side', and 'advanced'.
            'priority'          => 'default',                       // 	The priority within the context where the boxes should show ('high', 'low').
            'title'             => 'Scene Fields',                  // The title of the metabox
            'capability'        => 'edit_posts',                    // The capability needed to view the page
            'tabbed'            => true,
            'options'           => 'simple',                        // Only for metabox, options is stored az induvidual meta key, value pair.
        );

        // get a list of all instances
        $instances = $this ->  returnAllInstances();

        $fields[] = array(
            'name'   => 'basic',
            'title'  => 'Basic',
            'icon'   => 'dashicons-admin-generic',
            'fields' => array(
                array(
                    'id'             => 'scene_location',
                    'type'           => 'select',
                    'title'          => 'Instance',
                    'options'        => $instances, 
                    'description' => 'What instance is the scene part of? ',
                ),
                array(
                    'id'          => 'scene_tagline',
                    'type'        => 'textarea',
                    'title'       => 'Scene Tagline',
                    'description' => 'What is the tagline for the scene?',
                ),
            )
        );

        // instantiate the admin page
        $options_panel = new Exopite_Simple_Options_Framework( $config_metabox, $fields );
    }

    //Get a list of all instances
    public function returnAllInstances(){
        $args = array(
            'post_type'      => 'instance', // Custom post type name
            'posts_per_page' => -1,         // Retrieve all posts
            'orderby'        => 'title',    // Order by title
            'order'          => 'ASC',      // Sort in ascending order
            'fields'         => 'ids',      // Only retrieve IDs to minimize memory usage
        );
    
        $query = new WP_Query($args);
        $instance = array();
        $instance[""] = "Instances";
        if ($query->have_posts()) {
            foreach ($query->posts as $post_id) {
                $instance[$post_id]= get_the_title($post_id);
            }
        }
        return $instance;
    }
    

}