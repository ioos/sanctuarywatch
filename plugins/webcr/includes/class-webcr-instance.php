<?php
/**
 * Register class that defines the Instance custom content type as well as associated Instance functions 
 * 
 */
include_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-webcr-utility.php';
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

        // get list of locations, which is saved as a taxonomy
        $function_utilities = new Webcr_Utility();
        $locations = $function_utilities -> returnInstances();

        $scene_titles = [];
        $modal_icons = [];
        $modal_tabs = [];


        $fields[] = array(
            'name'   => 'basic',
            'title'  => 'Basic',
            'icon'   => 'dashicons-admin-generic',
            'fields' => array(
                array(
                    'id'          => 'instance_short_title',
                    'type'        => 'text',
                    'title'       => 'Short Title',
                    'description' => 'Instance Short Title',
                    'class'       => 'text-class',
                ),
                array(
                    'id'          => 'instance_slug',
                    'type'        => 'text',
                    'title'       => 'Slug',
                    'description' => 'Instance Slug',
                    'class'       => 'text-class',
                ),
                array(
                    'id'             => 'instance_type',
                    'type'           => 'select',
                    'title'          => 'Type',
                    'options'        => array("Designation" => "Designation", "Issue" => "Issue", "Sanctuary" => "Sanctuary"),
                    'description' => 'What is the instance type?',
                    'class'      => 'chosen', 
                ),
                array(
                    'id'             => 'instance_status',
                    'type'           => 'select',
                    'title'          => 'Status',
                    'options'        => array("Draft" => "Draft", "Published" => "Published"),
                    'default_option' => 'Draft',
                    'description' => 'Is the instance live?',
                    'class'      => 'chosen', 
                ),
                array(
                    'id'    => 'instance_tile',
                    'type'  => 'image',
                    'title' => 'Tile Image',
                    'description' => 'What is the instance image for the front page tile?'
                ),
                array(
                    'id'             => 'instance_toc_style',
                    'type'           => 'select',
                    'title'          => 'Table of Contents Style',
                    'options'        => array("accordion" => "Accordion", "list" => "List", "sectioned_list" => "Sectioned List"),
                    'default' => 'list',
                    'description' => 'What should the table of contents look like?',
                    'class'      => 'chosen', 
                ),
                array(
                    'id'    => 'instance_colored_sections',
                    'type'  => 'checkbox',
                    'title' => 'Colored Sections',
                    'description' => 'Should different sections be colored differently?',
                ),
                array(
                    'id'          => 'instance_hover_color',
                    'type'        => 'text',
                    'title'       => 'Hover Color',
                    'description' => 'What should the hover color or colors be? Any <a target="_blank" href="https://www.w3schools.com/colors/colors_names.asp">CSS-supported value</a> will do.',
                    "default"   => 'yellow',
                    'class'       => 'text-class',
                ),
                array(
                    'id'    => 'instance_full_screen_button',
                    'type'  => 'checkbox',
                    'title' => 'Full Screen Button',
                    'description' => 'Should there be a full screen button?',
                    "default"   => "yes",
                ),
                array(
                    'id'             => 'instance_text_toggle',
                    'type'           => 'select',
                    'title'          => 'Text Toggle',
                    'options'        => array("none" => "No Toggle", "toggle_off" => "Toggle, Default Off", "toggle_on" => "Toggle, Default On"),
                    'default'        => 'none',
                    'description' => 'Should there be a text toggle button?',
                    'class'      => 'chosen', 
                ),
            )
        );

        // instantiate the admin page
        $options_panel = new Exopite_Simple_Options_Framework( $config_metabox, $fields );

    }  


}