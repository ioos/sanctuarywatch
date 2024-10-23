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

        $scene_titles = array("" => "Scenes");

        // used by both scene and icon dropdowns
        if (array_key_exists("post", $_GET)) {
            $instance_id = intval($_GET["post"]);
            $scene_titles = $function_utilities -> returnInstanceScenes($instance_id );
        }

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
                    'title'       => 'URL',
                    'description' => 'What should the instance slug be? The slug is used to determine the url of the instance.',
                    'class'       => 'text-class',
                ),
                array(
                    'id'             => 'instance_type',
                    'type'           => 'select',
                    'title'          => 'Type',
                    'options'        => array("Designation" => "Designation", "Issue" => "Issue", "Sanctuary" => "Sanctuary"),
                    'description' => 'What is the instance type?',
                   // 'class'      => 'chosen', 
                ),
                array(
                    'id'             => 'instance_overview_scene',
                    'type'           => 'select',
                    'title'          => 'Overview Scene',
                    'options'        => $scene_titles,
                    'description' => 'What is the overview scene for the Instance?',
                ),
                array(
                    'id'             => 'instance_status',
                    'type'           => 'select',
                    'title'          => 'Status',
                    'options'        => array("Draft" => "Draft", "Published" => "Published"),
                    'default' => 'Draft',
                    'description' => 'Is the instance live?',
                //    'class'      => 'chosen', 
                ),
                array(
                    'id'    => 'instance_tile',
                    'type'  => 'image',
                    'title' => 'Tile Image',
                    'description' => 'What is the instance image for the front page tile? The image should be 250 pixels wide and 200 pixels tall.'
                ),
                array(
                    'id'             => 'instance_legacy_content',
                    'type'           => 'select',
                    'title'          => 'Legacy Content',
                    'options'        => array("no" => "No", "yes" => "Yes"),
                    'default' => 'no',
                    'description' => 'Should the Instance tile point to legacy content?',
                ),
                array(
                    'id'          => 'instance_legacy_content_url',
                    'type'        => 'text',
                    'title'       => 'Legacy Content URL',
                    'description' => 'What is the URL of the legacy content?',
                    'class'       => 'text-class',
                ),
                array(
                    'type' => 'fieldset',
                    'id' => 'instance_footer',
                    'title'   => 'Instance footer information',
                    'description' => 'Information appearing in the footer for all of the Scenes for this instance.',
                    'fields' => array(
                        array(
                            'id'     => 'instance_footer_about',
                            'type'   => 'editor',
                            'title'  => 'About the instance', 
                            'description' => 'This is information that appears in the left "About" column of the footer'        
                        ),
                        array(
                            'id'     => 'instance_footer_contact',
                            'type'   => 'editor',
                            'title'  => 'Contact person for the instance', 
                            'description' => 'This is information that appears in the center "Contact" column of the footer'        
                        ),
                        array(
                            'id'          => 'instance_footer_reports',
                            'type'   => 'editor',
                            'title'  => 'Relevant reports associated with the instance', 
                            'description' => 'This is information that appears in the right "Reports" column of the footer'     
                        ),
                    ),
                ),
                array(
                    'id'          => 'instance_check',
                    'type'        => 'button',
                    'title'       => 'Check Instance for Errors',
                    'class'        => 'instance_check',
                    'description' => 'This button is not functional yet.',
                    'options'     => array(
                        'href'  =>  '#nowhere',
                        'target' => '_self',
                        'value' => 'Error Check',
                        'btn-class' => 'exopite-sof-btn'
                    ),
                ),
            )
        );

        // instantiate the admin page
        $options_panel = new Exopite_Simple_Options_Framework( $config_metabox, $fields );

        // make several of the instance custom fields available to the REST API
        $instance_rest_fields = array(
            array('instance_short_title', 'string'), 
            array('instance_slug', 'string'), 
            array('instance_type', 'string'), 
            array('instance_status', 'string'), 
            array('instance_tile', 'string'), 
            array('instance_toc_style', 'string'), 
            array('instance_colored_sections', 'string'), 
            array('instance_hover_color', 'string'), 
            array('instance_full_screen_button', 'string'), 
            array('instance_overview_scene', 'integer'),
            array('instance_text_toggle', 'string'));

            $this->register_meta_nonarray_fields($instance_rest_fields);
    }  

    function register_meta_nonarray_fields ($rest_fields){
        foreach ($rest_fields as $target_field){
            register_meta(
                'post', // Object type. In this case, 'post' refers to custom post type 'Figure'
                $target_field[0], // Meta key name
                array(
                    'show_in_rest' => true, // Make the field available in REST API
                    'single' => true, // Indicates whether the meta key has one single value
                    'type' => $target_field[1], // Data type of the meta value
                    'auth_callback' => '__return_false' //Return false to disallow writing
                )
            );
        }
    }

    /**
	 * Register Instance custom fields for use by REST API.
	 *
	 * @since    1.0.0
	 */
    function register_instance_rest_fields(){
        $instance_rest_fields = array('instance_short_title', 'instance_slug',
            'instance_type', 'instance_status', 'instance_tile', 'instance_overview_scene');
            $function_utilities = new Webcr_Utility();
            $function_utilities -> register_custom_rest_fields("instance", $instance_rest_fields);
    }

    /**
	 * Set columns in admin screen for Scene custom content type.
	 *
     * @link https://www.smashingmagazine.com/2017/12/customizing-admin-columns-wordpress/
	 * @since    1.0.0
	 */
    public function change_instance_columns( $columns ) {
        $columns = array (
            'title' => 'Title',
            'tile' => 'Tile',
            'type' => 'Type',
            'state' => 'State',		
            'status' => 'Status',
        );
        return $columns;
    }

    // Populate columns for admin screen for Instance custom content type
    public function custom_instance_column( $column, $post_id ) {  

        if ( $column === 'type' ) {
            echo get_post_meta($post_id, 'instance_type', true);
        }

        if ( $column === 'tile' ) {
            $instance_tile = get_post_meta($post_id, 'instance_tile', true);
            if (!empty($instance_tile)) {
                    echo '<img src="' . esc_url($instance_tile) . '" style="max-width:100px; max-height:100px;" /><br>';
            }
    }

        if ( $column === 'state' ) {
            echo get_post_meta($post_id, 'instance_status', true);
        }

        if ($column === "status"){
            date_default_timezone_set('America/Los_Angeles'); 
            $last_modified_time = get_post_modified_time('g:i A', false, $post_id, true);
            $last_modified_date = get_post_modified_time('F j, Y', false, $post_id, true);
            $last_modified_user_id = get_post_field('post_author', $post_id);
            $last_modified_user = get_userdata($last_modified_user_id);
            $last_modified_name = $last_modified_user -> first_name . " " . $last_modified_user -> last_name; 

            echo "Last updated at " . $last_modified_time . " Pacific Time on " . $last_modified_date . " by " . $last_modified_name;
        }
    }


    /**
	 * Remove Bulk Actions dropdown from Scene, Modal, Figure, and Instance admin screens.
	 *
     * @param array $actions An array of the available bulk actions.
	 * @since    1.0.0
	 */
    function remove_bulk_actions($actions) {
        global $post_type;
    
        if ($post_type === 'scene' || $post_type === 'modal' || $post_type === 'figure' || $post_type === 'instance') {
            unset($actions['bulk-edit']);
            unset($actions['edit']);
            unset($actions['trash']);
            unset($actions['spam']);
            unset($actions['unspam']);
            unset($actions['delete']);
        }
        return $actions;
    }

    /**
	 * Remove Quick Edit links from all custom content admin screens.
	 *
     * @param string[] $actions An array of row action links.
     * @param int $post The database id of the post.
	 * @since    1.0.0
	 */
    function custom_content_remove_quick_edit_link($actions, $post) {
        global $current_screen;
        $current_post_type = $current_screen->post_type;
        if ($current_post_type  == 'instance' || $current_post_type  == 'figure' ||$current_post_type  == 'modal') {
            unset($actions['inline hide-if-no-js']);
        }
        return $actions;
    }


}