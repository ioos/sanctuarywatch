<?php
/**
 * Register class that defines the Modal custom content type as well as associated Modal functions
 * 
 */
class Webcr_Modal {

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
	 * Create Modal custom content type.
	 *
	 * @since    1.0.0
	 */
    function custom_content_type_modal() {
        $labels = array(
            'name'                  => _x( 'Modals', 'Post type general name', 'textdomain' ),
            'singular_name'         => _x( 'Modal', 'Post type singular name', 'textdomain' ),
            'menu_name'             => _x( 'Modals', 'Admin Menu text', 'textdomain' ),
            'name_admin_bar'        => _x( 'Modal', 'Add New on Toolbar', 'textdomain' ),
            'add_new'               => __( 'Add New Modal', 'textdomain' ),
            'add_new_item'          => __( 'Add New Modal', 'textdomain' ),
            'new_item'              => __( 'New Modal', 'textdomain' ),
            'edit_item'             => __( 'Edit Modal', 'textdomain' ),
            'view_item'             => __( 'View Modal', 'textdomain' ),
            'all_items'             => __( 'All Modals', 'textdomain' ),
            'search_items'          => __( 'Search Modals', 'textdomain' ),
            'parent_item_colon'     => __( 'Parent Modals:', 'textdomain' ),
            'not_found'             => __( 'No Modals found.', 'textdomain' ),
            'not_found_in_trash'    => __( 'No Modals found in Trash.', 'textdomain' ),
            'featured_image'        => _x( 'Modal Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'textdomain' ),
            'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
            'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
            'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
            'archives'              => _x( 'Modal archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'textdomain' ),
            'insert_into_item'      => _x( 'Insert into Modal', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'textdomain' ),
            'uploaded_to_this_item' => _x( 'Uploaded to this Modal', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'textdomain' ),
            'filter_items_list'     => _x( 'Filter Modals list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'textdomain' ),
            'items_list_navigation' => _x( 'Modals list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'textdomain' ),
            'items_list'            => _x( 'Modals list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'textdomain' ),
        );
    
        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'modals' ),
            'capability_type'    => 'post',
            'menu_icon'          => 'dashicons-category',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array( 'title', 'revisions' ),
        );
    
        register_post_type( 'modal', $args );
    }

    /**
	 * Create custom fields, using metaboxes, for Modal custom content type.
	 *
	 * @since    1.0.0
	 */
    public function create_modal_fields() {

        $config_metabox = array(

            /*
             * METABOX
             */
            'type'              => 'metabox',                       // Required, menu or metabox
            'id'                => $this->plugin_name,              // Required, meta box id, unique, for saving meta: id[field-id]
            'post_types'        => array( 'modal' ),                 // Post types to display meta box
            'context'           => 'advanced',                      // 	The context within the screen where the boxes should display: 'normal', 'side', and 'advanced'.
            'priority'          => 'default',                       // 	The priority within the context where the boxes should show ('high', 'low').
            'title'             => 'Modal Fields',                  // The title of the metabox
            'capability'        => 'edit_posts',                    // The capability needed to view the page
            'tabbed'            => true,
            'options'           => 'simple',                        // Only for metabox, options is stored az induvidual meta key, value pair.
        );

        // get list of locations, which is saved as a taxonomy
        $locations_array = get_terms(array('taxonomy' => 'location', 'hide_empty' => false));
        $locations=[];
        foreach ( $locations_array as $locations_row ){
            $locations[$locations_row -> name] = $locations_row -> name;
        }

        // DELETE THIS FUNCTION
        function get_scene_posts_with_location($location_value) {
            $args = array(
                'post_type' => 'scene', // Custom content type name
                'posts_per_page' => -1, // Retrieve all posts
                'meta_query' => array(
                    array(
                        'key' => 'scene_location', // Custom field name
                        'value' => $location_value,
                        'compare' => '='
                    )
                )
            );
        
            $query = new WP_Query($args);
            $scene_posts = array();
        
            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    $scene_posts[] = array(
                        'ID' => get_the_ID(),
                        'title' => get_the_title()
                    );
                }
                wp_reset_postdata();
            }
        
            return $scene_posts;
        }

        $scene_posts = get_scene_posts_with_location('Channel Islands NMS');
      //  print_r($scene_posts);

        $fields[] = array(
            'name'   => 'basic',
            'title'  => 'Basic',
            'icon'   => 'dashicons-admin-generic',
            'fields' => array(

                array(
                    'id'             => 'modal_location',
                    'type'           => 'select',
                    'title'          => 'Location',
                    'options'        => $locations,
                    'default_option' => 'Modal Location',
                    'description' => 'Modal Location description',
                     'default'     => ' ',
                     'class'      => 'chosen', 
                ),
                array(
                    'id'             => 'hidden_root_url',
                    'type'           => 'hidden',
                    'default'          => 'hidden',
                ),
                array(
                    'id'             => 'modal_scene',
                    'type'           => 'select',
                    'title'          => 'Scene',
                    'options'        => array (" " => "Modal Scene"), //$locations,
                  //  'default_option' => 'Modal Scene',
                    'description' => 'Modal Scene description',
                   //  'default'     => ' ',
                 //    'class'      => 'chosen', 
                ),

                array(
                    'id'      => 'modal_tab_number',
                    'type'    => 'range',
                    'title'   => 'Number of Modal Tabs',
                    'description' => 'Number of Modal Tabs description',
                    'min'     => 0,    
                     'default' => 1,    
                     'max'     => 6,         
                     'step'    => 1,             
                ),              



                array(
                    'id'          => 'modal_preview',
                    'type'        => 'button',
                    'title'       => 'Preview Modal',
                    'class'        => 'modal_preview',
                    'options'     => array(
                        'href'  =>  '#nowhere',
                        'target' => '_self',
                        'value' => 'Preview',
                        'btn-class' => 'exopite-sof-btn'
                    ),
                ),
            )
        );

        // instantiate the admin page
        $options_panel = new Exopite_Simple_Options_Framework( $config_metabox, $fields );

    }



}