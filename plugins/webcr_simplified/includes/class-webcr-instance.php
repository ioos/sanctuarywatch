<?php
/**
 * Register class that defines the Instance custom content type as well as associated Instance functions 
 * 
 */
class Webcr_Instance {

    public function __construct( $plugin_name ) {
		$this->plugin_name = $plugin_name;
	}

    /**
	 * Create Instance custom content type.
	 *
	 * @since    1.0.0
	 */
    function custom_content_type_instance() {
        $labels = array(
            'name'                  => _x( 'Instances', 'Post type general name', 'textdomain' ),
            'singular_name'         => _x( 'Instance', 'Post type singular name', 'textdomain' ),
            'menu_name'             => _x( 'Instances', 'Admin Menu text', 'textdomain' ),
            'name_admin_bar'        => _x( 'Instance', 'Add New on Toolbar', 'textdomain' ),
            'add_new'               => __( 'Add New Instance', 'textdomain' ),
            'add_new_item'          => __( 'Add New Instance', 'textdomain' ),
            'new_item'              => __( 'New Instance', 'textdomain' ),
            'edit_item'             => __( 'Edit Instance', 'textdomain' ),
            'view_item'             => __( 'View Instance', 'textdomain' ),
            'all_items'             => __( 'All Instances', 'textdomain' ),
            'search_items'          => __( 'Search Instances', 'textdomain' ),
            'parent_item_colon'     => __( 'Parent Instances:', 'textdomain' ),
            'not_found'             => __( 'No Instances found.', 'textdomain' ),
            'not_found_in_trash'    => __( 'No Instances found in Trash.', 'textdomain' ),
            'featured_image'        => _x( 'Instance Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'textdomain' ),
            'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
            'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
            'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
            'archives'              => _x( 'Instance archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'textdomain' ),
            'insert_into_item'      => _x( 'Insert into Instance', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'textdomain' ),
            'uploaded_to_this_item' => _x( 'Uploaded to this Instance', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'textdomain' ),
            'filter_items_list'     => _x( 'Filter Instances list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'textdomain' ),
            'items_list_navigation' => _x( 'Instances list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'textdomain' ),
            'items_list'            => _x( 'Instances list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'textdomain' ),
        );
    
        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'show_in_rest'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'instances' ),
            'capability_type'    => 'post',
            'menu_icon'          => 'dashicons-admin-site',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array( 'title' ), //array( 'title', 'revisions' ), 
        );
    
        register_post_type( 'instance', $args );
    }


    /**
	 * Create custom fields, using metaboxes, for Figure custom content type.
	 *
	 * @since    1.0.0
	 */
    function create_instance_fields() {

        $config_metabox = array(

            /*
            * METABOX
            */
            'type'              => 'metabox',                       // Required, menu or metabox
            'id'                => $this->plugin_name,              // Required, meta box id, unique, for saving meta: id[field-id]
            'post_types'        => array( 'instance' ),                 // Post types to display meta box
            'context'           => 'advanced',                      // 	The context within the screen where the boxes should display: 'normal', 'side', and 'advanced'.
            'priority'          => 'default',                       // 	The priority within the context where the boxes should show ('high', 'low').
            'title'             => 'Instance Fields',                  // The title of the metabox
            'capability'        => 'edit_posts',                    // The capability needed to view the page
            'tabbed'            => true,
            'options'           => 'simple',                        // Only for metabox, options is stored az induvidual meta key, value pair.
        );

        $fields[] = array(
            'name'   => 'basic',
            'title'  => 'Basic',
            'icon'   => 'dashicons-admin-generic',
            'fields' => array(
                array(
                    'id'          => 'instance_short_title',
                    'type'        => 'text',
                    'title'       => 'Short Title',
                    'description' => 'What should the instance short title be?',
                    'class'       => 'text-class',
                ),
                array(
                    'id'          => 'instance_slug',
                    'type'        => 'text',
                    'title'       => 'Slug',
                    'description' => 'What should the instance slug be? The slug is used to determine the url of the instance.',
                    'class'       => 'text-class',
                ),
            )
        );

        // instantiate the admin page
        $options_panel = new Exopite_Simple_Options_Framework( $config_metabox, $fields );
    }
}