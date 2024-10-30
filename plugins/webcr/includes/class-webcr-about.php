<?php
/**
 * Register class that defines the Figure custom content type as well as associated Modal functions 
 * 
 */
include_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-webcr-utility.php';
class Webcr_About {

    public function __construct( $plugin_name ) {
		$this->plugin_name = $plugin_name;
	}

    /**
	 * Create About custom content type.
	 *
	 * @since    1.0.0
	 */
    function custom_content_type_about() {
        $labels = array(
            'name'                  => _x( 'About Page', 'Post type general name', 'textdomain' ),
            'singular_name'         => _x( 'About Page', 'Post type singular name', 'textdomain' ),
            'menu_name'             => _x( 'About Page', 'Admin Menu text', 'textdomain' ),
            'name_admin_bar'        => _x( 'About', 'Add New on Toolbar', 'textdomain' ),
            'add_new'               => __( 'Add New About Page', 'textdomain' ),
            'add_new_item'          => __( 'Add New About Page', 'textdomain' ),
            'new_item'              => __( 'About Page', 'textdomain' ),
            'edit_item'             => __( 'Edit About Page', 'textdomain' ),
            'view_item'             => __( 'View About Page', 'textdomain' ),
            'all_items'             => __( 'All About Pages', 'textdomain' ),
            'search_items'          => __( 'Search About Pages', 'textdomain' ),
            'parent_item_colon'     => __( 'Parent About Page:', 'textdomain' ),
            'not_found'             => __( 'No About Pages found.', 'textdomain' ),
            'not_found_in_trash'    => __( 'No About Pages found in Trash.', 'textdomain' ),
            'featured_image'        => _x( 'About Page Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'textdomain' ),
            'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
            'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
            'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
            'archives'              => _x( 'About Page archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'textdomain' ),
            'insert_into_item'      => _x( 'Insert into About Page', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'textdomain' ),
            'uploaded_to_this_item' => _x( 'Uploaded to this About Page', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'textdomain' ),
            'filter_items_list'     => _x( 'Filter About Page list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'textdomain' ),
            'items_list_navigation' => _x( 'About Page list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'textdomain' ),
            'items_list'            => _x( 'About Page list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'textdomain' ),
        );
    
        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'show_in_rest'       => true,
            'query_var'          => true,
   //         'rewrite'            => array( 'slug' => 'about' ),
   'rewrite'         => [
    'slug'       => 'about',  // Set slug to 'about'
    'with_front' => false,
],
            'capability_type'    => 'post',
            'menu_icon'          => 'dashicons-admin-site-alt3',
            'has_archive'        => false, //true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array( 'title' ), //array( 'title', 'revisions' ), 
        );
    
        register_post_type( 'about', $args );
    }


    /**
	 * Create custom fields, using metaboxes, for About custom content type.
	 *
	 * @since    1.0.0
	 */
    function create_about_fields() {

        $config_metabox = array(

            /*
            * METABOX
            */
            'type'              => 'metabox',                       // Required, menu or metabox
            'id'                => $this->plugin_name,              // Required, meta box id, unique, for saving meta: id[field-id]
            'post_types'        => array( 'about' ),                 // Post types to display meta box
            'context'           => 'advanced',                      // 	The context within the screen where the boxes should display: 'normal', 'side', and 'advanced'.
            'priority'          => 'default',                       // 	The priority within the context where the boxes should show ('high', 'low').
            'title'             => 'About Fields',                  // The title of the metabox
            'capability'        => 'edit_posts',                    // The capability needed to view the page
            'tabbed'            => true,
            'options'           => 'simple',                        // Only for metabox, options is stored az induvidual meta key, value pair.
        );

        // get list of locations, which is saved as a taxonomy
        $function_utilities = new Webcr_Utility();

        $fields[] = array(
            'name'   => 'basic',
            'title'  => 'Basic',
            'icon'   => 'dashicons-admin-generic',
            'fields' => array(
                array(
                    'id'   => 'about_tagline',
                    'type' => 'textarea',
                    'title'       => 'Tagline',
                    'description' => 'What is the tagline for the About page that appears above the tiles?'
                ),
                array(
                    'id'     => 'about_contact_info',
                    'type'   => 'editor',
                    'title'  => 'Contact info', 
                    'description' => 'What information should appear in the Contact Info tile?'
                ),
                array(
                    'id'     => 'about_partners',
                    'type'   => 'editor',
                    'title'  => 'Partners', 
                    'description' => 'What information should appear in the Partners tile?'
                ),
                array(
                    'id'     => 'about_code',
                    'type'   => 'editor',
                    'title'  => 'Use our Code', 
                    'description' => 'What information should appear in the Use Our Code tile?'
                ),
                array(
                    'id'     => 'about_people',
                    'type'   => 'editor',
                    'title'  => 'People Involved', 
                    'description' => 'What information should appear in the People Involved tile?'
                ),
            )
        );

        // instantiate the admin page
        $options_panel = new Exopite_Simple_Options_Framework( $config_metabox, $fields );
    }  
}