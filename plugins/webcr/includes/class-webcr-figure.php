<?php
/**
 * Register class that defines the Figure custom content type as well as associated Modal functions 
 * 
 */
include_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-webcr-utility.php';
class Webcr_Figure {

    /**
	 * Create Figure custom content type.
	 *
	 * @since    1.0.0
	 */
    function custom_content_type_figure() {
        $labels = array(
            'name'                  => _x( 'Figures', 'Post type general name', 'textdomain' ),
            'singular_name'         => _x( 'Figure', 'Post type singular name', 'textdomain' ),
            'menu_name'             => _x( 'Figures', 'Admin Menu text', 'textdomain' ),
            'name_admin_bar'        => _x( 'Figure', 'Add New on Toolbar', 'textdomain' ),
            'add_new'               => __( 'Add New Figure', 'textdomain' ),
            'add_new_item'          => __( 'Add New Figure', 'textdomain' ),
            'new_item'              => __( 'New Figure', 'textdomain' ),
            'edit_item'             => __( 'Edit Figure', 'textdomain' ),
            'view_item'             => __( 'View Figure', 'textdomain' ),
            'all_items'             => __( 'All Figures', 'textdomain' ),
            'search_items'          => __( 'Search Figures', 'textdomain' ),
            'parent_item_colon'     => __( 'Parent Figures:', 'textdomain' ),
            'not_found'             => __( 'No Figures found.', 'textdomain' ),
            'not_found_in_trash'    => __( 'No Figures found in Trash.', 'textdomain' ),
            'featured_image'        => _x( 'Figure Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'textdomain' ),
            'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
            'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
            'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
            'archives'              => _x( 'Figure archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'textdomain' ),
            'insert_into_item'      => _x( 'Insert into Figure', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'textdomain' ),
            'uploaded_to_this_item' => _x( 'Uploaded to this Figure', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'textdomain' ),
            'filter_items_list'     => _x( 'Filter Figures list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'textdomain' ),
            'items_list_navigation' => _x( 'Figures list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'textdomain' ),
            'items_list'            => _x( 'Figures list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'textdomain' ),
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
            'menu_icon'          => 'dashicons-admin-comments',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array( 'title' ), //array( 'title', 'revisions' ), 
        );
    
        register_post_type( 'figure', $args );
    }


    /**
	 * Create custom fields, using metaboxes, for Figure custom content type.
	 *
	 * @since    1.0.0
	 */
    function create_figure_fields() {
        if (isset($_GET["action"])) {
            if ($_GET["action"] == "edit") {

                $config_metabox = array(

                    /*
                    * METABOX
                    */
                    'type'              => 'metabox',                       // Required, menu or metabox
                    'id'                => $this->plugin_name,              // Required, meta box id, unique, for saving meta: id[field-id]
                    'post_types'        => array( 'figure' ),                 // Post types to display meta box
                    'context'           => 'advanced',                      // 	The context within the screen where the boxes should display: 'normal', 'side', and 'advanced'.
                    'priority'          => 'default',                       // 	The priority within the context where the boxes should show ('high', 'low').
                    'title'             => 'Figure Fields',                  // The title of the metabox
                    'capability'        => 'edit_posts',                    // The capability needed to view the page
                    'tabbed'            => true,
                    'options'           => 'simple',                        // Only for metabox, options is stored az induvidual meta key, value pair.
                );

                // get list of locations, which is saved as a taxonomy
                $function_utilities = new Webcr_Utility();
                $locations = $function_utilities -> returnInstances();

                // used by both scene and icon dropdowns
                if (array_key_exists("post", $_GET)) {
                    $modal_id = intval($_GET["post"]);
                    $scene_id = intval(get_post_meta($modal_id, "modal_scene", true));
                    $scene_titles = $function_utilities -> returnSceneTitles($scene_id, $modal_id);
                    $modal_icons = $function_utilities -> returnIcons($scene_id);
                }

                $fields[] = array(
                    'name'   => 'basic',
                    'title'  => 'Basic',
                    'icon'   => 'dashicons-admin-generic',
                    'fields' => array(

                        array(
                            'id'             => 'figure_location',
                            'type'           => 'select',
                            'title'          => 'Instance',
                            'options'        => $locations,
                            'default_option' => 'Figure Instance',
                            'description' => 'Figure Instance description',
                            'default'     => ' ',
                            'class'      => 'chosen', 
                        ),
                        array(
                            'id'             => 'Figure_scene',
                            'type'           => 'select',
                            'title'          => 'Scene',
                            'options'        => $scene_titles,
                            'description' => 'Figure Scene description',
                        ),
                        array(
                            'id'             => 'modal_icons',
                            'type'           => 'select',
                            'title'          => 'Icons',
                            'options'        => $modal_icons, // array (" " => "Modal Icons")
                            'description' => 'Figure Icons description',
                        ),
                        array(
                            'id'          => 'figure_preview',
                            'type'        => 'button',
                            'title'       => 'Preview Figure',
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
    }

}