<?php
/**
 * Register class that defines the Figure custom content type as well as associated Modal functions 
 * 
 */
include_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-webcr-utility.php';
class Webcr_Figure {

    public function __construct( $plugin_name ) {
		$this->plugin_name = $plugin_name;
	}

    /**
	 * Set columns in admin screen for Figure custom content type.
	 *
     * @link https://www.smashingmagazine.com/2017/12/customizing-admin-columns-wordpress/
	 * @since    1.0.0
	 */
    function change_figure_columns( $columns ) {
        $columns = array (
            'title' => 'Title',
            'figure_instance' => 'Instance',
            'figure_scene' => 'Scene',		
            'figure_modal' => 'Icon',	
            'figure_tab' => 'Tab',		
            'figure_order' => 'Order',			
            'figure_image_location' => 'Image Location',	
            'status' => 'Status',
        );
        return $columns;
    }

    /**
	 * Populate custom fields for Figure content type in the admin screen.
	 *
     * @param string $column The name of the column.
     * @param int $post_id The database id of the post.
	 * @since    1.0.0
	 */
    public function custom_figure_column( $column, $post_id ) {  


        if ( $column === 'figure_instance' ) {
            $instance_id = get_post_meta( $post_id, 'location', true ); 
            echo get_the_title($instance_id ); 
        }

        if ( $column === 'figure_scene' ) {
            $scene_id = get_post_meta( $post_id, 'figure_scene', true );
            $scene_title = get_the_title($scene_id);
            echo $scene_title; 
        }

        if ( $column === 'figure_modal' ) {
            $modal_id = get_post_meta( $post_id, 'figure_modal', true ); 
            echo get_the_title($modal_id ); 
        }

        if ( $column === 'figure_tab' ) {
            echo get_post_meta( $post_id, 'figure_tab', true ); 
        }

        if ( $column === 'figure_order' ) {
            echo get_post_meta( $post_id, 'figure_order', true ); 
        }

        if ( $column === 'figure_image_location' ) {
            echo get_post_meta( $post_id, 'figure_path', true ); 
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
	 * Add filter dropdowns, figure location, for the admin screen for the Figure content type.
	 *
	 * @since    1.0.0
	 */
    function figure_filter_dropdowns () {
        $screen = get_current_screen();
        if ( $screen->id == 'edit-figure' ){
            // Instances dropdown 
            global $wpdb;
            $instances = $wpdb->get_results("
                SELECT ID, post_title 
                FROM {$wpdb->posts} 
                WHERE post_type = 'instance' 
                AND post_status = 'publish' 
                ORDER BY post_title ASC");
    
            echo '<select name="figure_instance" id="figure_instance">';
            echo '<option value="">All Instances</option>';
            foreach ($instances as $instance) {
                $selected = isset($_GET['figure_instance']) && $_GET['figure_instance'] == $instance->ID ? 'selected="selected"' : '';
                echo '<option value="' . $instance->ID . '" ' . $selected . '>' . $instance->post_title . '</option>';
            }
            echo '</select>';

            //Scene dropdown
            echo '<select name="figure_scene" id="figure_scene">';
            echo '<option value="">All Scenes</option>';
            if (isset($_GET['figure_instance']) && $_GET['figure_instance'] != ""){
                $scenes = $wpdb->get_results("
                SELECT p.ID, p.post_title 
                FROM $wpdb->posts p
                INNER JOIN $wpdb->postmeta pm ON p.ID = pm.post_id
                WHERE p.post_type = 'scene' 
                AND p.post_status = 'publish'
                AND pm.meta_key = 'scene_location' 
                AND pm.meta_value = " . $_GET['figure_instance']);

                foreach ($scenes as $scene) {
                    $selected = $_GET['figure_scene'] == $scene->ID ? 'selected="selected"' : '';
                    echo '<option value="' . $scene->ID . '" ' . $selected . '>' . $scene->post_title . '</option>';
                }
            }
            echo '</select>';

            //Icon dropdown
            echo '<select name="figure_icon" id="figure_icon">';
            echo '<option value="">All Icons</option>';
            if (isset($_GET['figure_scene']) && $_GET['figure_scene'] != ""){
                $icons = $wpdb->get_results("
                SELECT p.ID, p.post_title 
                FROM $wpdb->posts p
                INNER JOIN $wpdb->postmeta pm1 ON p.ID = pm1.post_id
                INNER JOIN $wpdb->postmeta pm2 ON p.ID = pm2.post_id
                WHERE p.post_type = 'modal'  
                AND p.post_status = 'publish' 
                AND pm1.meta_key = 'modal_scene' AND pm1.meta_value = " . $_GET['figure_scene'] . 
                " AND pm2.meta_key = 'icon_function' AND pm2.meta_value = 'Modal'");

                foreach ($icons as $icon) {
                    $selected = $_GET['figure_icon'] == $icon->ID ? 'selected="selected"' : '';
                    echo '<option value="' . $icon->ID . '" ' . $selected . '>' . $icon->post_title . '</option>';
                }
            }
            echo '</select>';
        }
    }

    /**
     * Filter the results for the Figure admin screen by the Figure Location, Figure Scene, and Figure Icons dropdown fields
     *
     * @param WP_Query $query The WordPress Query instance that is passed to the function.
     * @since    1.0.0
     */
    function figure_location_filter_results($query){
        global $pagenow;
        $type = 'figure';
        if ($pagenow == 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] == $type && isset($_GET['figure_instance']) && $_GET['figure_instance'] != '') {
            if ( isset($_GET['figure_icon']) && $_GET['figure_icon'] != '') {
                $meta_query = array(
                    array(
                        'key' => 'figure_modal', // The custom field storing the instance ID
                        'value' => $_GET['figure_icon'],
                        'compare' => '='
                    )
                );
            } elseif ( isset($_GET['figure_scene']) && $_GET['figure_scene'] != '') {
                $meta_query = array(
                    array(
                        'key' => 'figure_scene', // The custom field storing the instance ID
                        'value' => $_GET['figure_scene'],
                        'compare' => '='
                    )
                );
            } else {
            $meta_query = array(
                array(
                    'key' => 'location', // The custom field storing the instance ID
                    'value' => $_GET['figure_instance'],
                    'compare' => '='
                )
            );
            }
            $query->set('meta_query', $meta_query);
        }
    }

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
            'show_in_rest'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'figures' ),
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
        $locations = $function_utilities -> returnAllInstances();

        $scene_titles = [];
        $modal_icons = [];
        $modal_tabs = [];

        // used by both scene and icon dropdowns
        if (array_key_exists("post", $_GET)) {
            $figure_id = intval($_GET["post"]);
            // $scene_id = intval(get_post_meta($modal_id, "modal_scene", true));
            $location = get_post_meta($figure_id, "location", true);
            $scene_titles = $function_utilities -> returnScenesFigure($location);
            $scene_id = get_post_meta($figure_id, "figure_scene", true);
            $modal_icons = $function_utilities -> returnFigureIcons($scene_id);
            $modal_id = get_post_meta($figure_id, "figure_modal", true);
            $modal_tabs = $function_utilities -> returnModalTabs($modal_id);
        }

        $fields[] = array(
            'name'   => 'basic',
            'title'  => 'Basic',
            'icon'   => 'dashicons-admin-generic',
            'fields' => array(

                array(
                    'id'             => 'location',
                    'type'           => 'select',
                    'title'          => 'Instance',
                    'options'        => $locations,
                    'description' => 'What instance is this figure part of?',
                 //   'class'      => 'chosen', 
                ),
                array(
                    'id'             => 'figure_scene',
                    'type'           => 'select',
                    'title'          => 'Scene',
                    'options'        => $scene_titles,
                    'description' => 'What scene is this figure part of?',
                ),
                array(
                    'id'             => 'figure_modal',
                    'type'           => 'select',
                    'title'          => 'Icon',
                    'options'        => $modal_icons, // array (" " => "Modal Icons")
                    'description' => 'What icon is this figure part of?',
                ),
                array(
                    'id'             => 'figure_tab',
                    'type'           => 'select',
                    'title'          => 'Tab',
                    'options'        => $modal_tabs, // array (" " => "Modal Icons")
                    'description' => 'What modal tab is this figure part of?',
                ),
                array(
                    'id'      => 'figure_order',
                    'type'    => 'number',
                    'title'   => 'Order',
                    'description' => 'If there are multiple figures in this modal tab, in what order should this figure appear?',
                    'default' => '1',                               
                    'min'     => '1',                                    
                    'max'     => '4',      
                    'step'    => '1',   
                ),
                array(
                    'type' => 'fieldset',
                    'id' => 'figure_science_info',
                    'title'   => 'Monitoring program link',
                    'description' => 'What should the monitoring program icon link to, if anything?',
                    'fields' => array(
                        array(
                            'id'          => 'figure_science_link_text',
                            'type'        => 'text',
                            'title'       => 'Text',
                            'class'       => 'text-class',
                        ),
                        array(
                            'id'          => 'figure_science_link_url',
                            'type'        => 'text',
                            'title'       => 'URL',
                            'class'       => 'text-class',
                        ),
                    ),
                ),
                array(
                    'type' => 'fieldset',
                    'id' => 'figure_data_info',
                    'title'   => 'Data link',
                    'description' => 'What should the data icon link to, if anything?',
                    'fields' => array(
                        array(
                            'id'          => 'figure_data_link_text',
                            'type'        => 'text',
                            'title'       => 'Text',
                            'class'       => 'text-class',
                        ),
                        array(
                            'id'          => 'figure_data_link_url',
                            'type'        => 'text',
                            'title'       => 'URL',
                            'class'       => 'text-class',
                        ),
                    ),
                ),
                array(
                    'id'    => 'interactive_image',
                    'type'  => 'checkbox',
                    'title' => 'Interactive image?',
                    'description' => 'Is this a static figure that needs to be converted to interactive?',
                ),
                array(
                    'id'             => 'figure_path',
                    'type'           => 'select',
                    'title'          => 'Figure Type',
                    'options'        => array("Internal" => "Internal", "External" => "External", "Interactive" => "Interactive"),
                    'default'        => "Internal",
                    'description' => 'Is the figure image stored within this website or at some external location?',
                ),

                array(
                    'id'    => 'figure_image',
                    'type'  => 'image',
                    'title' => 'Figure image',
                    'description' => 'What is the figure image?',
                ),
                array(
                    'id'          => 'figure_external_url',
                    'type'        => 'text',
                    'title'       => 'Figure External URL',
                    'class'       => 'text-class',
                    'description' => 'This external URL should link just to the image itself (that is the URL should end in .png .jpeg .jpg or .tiff)',
                ),
                array(
                    'id'    => 'figure_json',
                    'type'  => 'image',
                    'title' => 'Figure Json',
                    'description' => 'What is the figure json?',
                ),
                array(
                    'id'          => 'figure_json_arguments',
                    'type'        => 'text',
                    'title'       => 'Arguments for Creating Interactive Figure',
                    'class'       => 'text-class',
                    'description' => 'This should be a comma-delimited list of arguments',
                ),
                array(
                    'id'     => 'figure_caption_short',
                    'type'   => 'editor',
                    'title'  => 'Short figure caption', 
                 //   'description' => 'What is the short version of the figure caption?'
                ),
                array(
                    'id'     => 'figure_caption_long',
                    'type'   => 'editor',
                    'title'  => 'Extended caption', 
                  //  'description' => 'This caption appears in the "Click for Details" section under the short caption.'
                ),
                array(
                    'id'          => 'figure_preview',
                    'type'        => 'button',
                    'title'       => 'Preview Figure',
                    'class'        => 'figure_preview',
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

        // make several of the modal custom fields available to the REST API
        register_meta(
            'post', // Object type. In this case, 'post' refers to custom post type 'Figure'
            'figure_modal', // Meta key name
            array(
                'show_in_rest' => true, // Make the field available in REST API
                'single' => true, // Indicates whether the meta key has one single value
                'type' => 'string', // Data type of the meta value
                'description' => 'The figure modal', // Description of the meta key
                'auth_callback' => '__return_false' //Return false to disallow writing
            )
        );

        register_meta(
            'post', // Object type. In this case, 'post' refers to custom post type 'Figure'
            'figure_tab', // Meta key name
            array(
                'show_in_rest' => true, // Make the field available in REST API
                'single' => true, // Indicates whether the meta key has one single value
                'type' => 'string', // Data type of the meta value
                'description' => 'The figure tab', // Description of the meta key
                'auth_callback' => '__return_false' //Return false to disallow writing
            )
        );

        register_meta(
            'post', // Object type. In this case, 'post' refers to custom post type 'Figure'
            'figure_order', // Meta key name
            array(
                'show_in_rest' => true, // Make the field available in REST API
                'single' => true, // Indicates whether the meta key has one single value
                'type' => 'integer', // Data type of the meta value
                'description' => 'The figure tab', // Description of the meta key
                'auth_callback' => '__return_false' //Return false to disallow writing
            )
        );

        register_meta( 
            'post', 
            "figure_science_info", // Meta key name
            array(
                'auth_callback'     => '__return_false' ,
                'single'            => true, // The field contains a single array
                'description' => "URL for figure info", // Description of the meta key
                'show_in_rest'      => array(
                    'schema' => array(
                        'type'  => 'array', // The meta field is an array
                        'items' => array(
                            'type' => 'string', // Each item in the array is a string
                        ),
                    ),
                ),
            ) 
        );

        register_meta( 
            'post', 
            "figure_data_info", // Meta key name
            array(
                'auth_callback'     => '__return_false' ,
                'single'            => true, // The field contains a single array
                'description' => "URL for figure data", // Description of the meta key
                'show_in_rest'      => array(
                    'schema' => array(
                        'type'  => 'array', // The meta field is an array
                        'items' => array(
                            'type' => 'string', // Each item in the array is a string
                        ),
                    ),
                ),
            ) 
        );

        register_meta(
            'post', // Object type. In this case, 'post' refers to custom post type 'Figure'
            'figure_path', // Meta key name
            array(
                'show_in_rest' => true, // Make the field available in REST API
                'single' => true, // Indicates whether the meta key has one single value
                'type' => 'string', // Data type of the meta value
                'description' => 'The figure path', // Description of the meta key
                'auth_callback' => '__return_false' //Return false to disallow writing
            )
        );

        register_meta(
            'post', // Object type. In this case, 'post' refers to custom post type 'Figure'
            'figure_image', // Meta key name
            array(
                'show_in_rest' => true, // Make the field available in REST API
                'single' => true, // Indicates whether the meta key has one single value
                'type' => 'string', // Data type of the meta value
                'description' => 'The figure image, internal', // Description of the meta key
                'auth_callback' => '__return_false' //Return false to disallow writing
            )
        );

        register_meta(
            'post', // Object type. In this case, 'post' refers to custom post type 'Figure'
            'figure_external_url', // Meta key name
            array(
                'show_in_rest' => true, // Make the field available in REST API
                'single' => true, // Indicates whether the meta key has one single value
                'type' => 'string', // Data type of the meta value
                'description' => 'The figure external url', // Description of the meta key
                'auth_callback' => '__return_false' //Return false to disallow writing
            )
        );

        register_meta(
            'post', // Object type. In this case, 'post' refers to custom post type 'Figure'
            'figure_caption_short', // Meta key name
            array(
                'show_in_rest' => true, // Make the field available in REST API
                'single' => true, // Indicates whether the meta key has one single value
                'type' => 'string', // Data type of the meta value
                'description' => 'The short figure caption', // Description of the meta key
                'auth_callback' => '__return_false' //Return false to disallow writing
            )
        );

        register_meta(
            'post', // Object type. In this case, 'post' refers to custom post type 'Figure'
            'figure_caption_long', // Meta key name
            array(
                'show_in_rest' => true, // Make the field available in REST API
                'single' => true, // Indicates whether the meta key has one single value
                'type' => 'string', // Data type of the meta value
                'description' => 'The long figure caption', // Description of the meta key
                'auth_callback' => '__return_false' //Return false to disallow writing
            )
        );

    }  

    public function figure_admin_notice() {
        // First let's determine where we are. We only want to show admin notices in the right places. Namely in one of our custom 
        // posts after it has been updated. The if statement is looking for three things: 1. Figure post type? 2. An individual post (as opposed to the scene
        // admin screen)? 3. A new post

        if (function_exists('get_current_screen')) {
            $current_screen = get_current_screen();
            if ($current_screen){
                if ($current_screen->base == "post" && $current_screen->id =="figure" && !($current_screen->action =="add") ) { 
                    if( isset( $_COOKIE["figure_post_status"] ) ) {
                        $modal_post_status =  $_COOKIE["figure_post_status"];
                        if ($modal_post_status == "post_good") {
                            echo '<div class="notice notice-info is-dismissible"><p>Figure created or updated.</p></div>';
                        } 
                        else {
                            if (isset($_COOKIE["figure_errors"])) {
                                $error_message = "<p>Error or errors in figure</p>";
                                $error_list_coded = stripslashes($_COOKIE["figure_errors"]);
                                $error_list_array = json_decode($error_list_coded);
                                $error_array_length = count($error_list_array);
                                $error_message = $error_message . '<p><ul>';
                                for ($i = 0; $i < $error_array_length; $i++){
                                    $error_message = $error_message . '<li>' . $error_list_array[$i] . '</li>';
                                }
                                $error_message = $error_message . '</ul></p>';
                            }
                            echo '<div class="notice notice-error is-dismissible">' . $error_message . '</div>'; 

      //                      if (isset($_COOKIE["modal_error_all_fields"])) {
        //                        $modal_fields_coded = stripslashes($_COOKIE["modal_error_all_fields"]);
          //                      $modal_fields_array = json_decode($modal_fields_coded, true);	
            //                    $_POST['modal_location'] = $modal_fields_array['modal_location'];
              //                  $_POST['modal_scene'] = $modal_fields_array['modal_scene'];
                //                $_POST['modal_icons'] = $modal_fields_array['modal_icons'];
                  //              $_POST['icon_function'] = $modal_fields_array['icon_function'];
                    //            $_POST['icon_external_url'] = $modal_fields_array['icon_external_url'];
                      //          $_POST['icon_scene_out'] = $modal_fields_array['icon_scene_out'];
                        //        $_POST['modal_tagline'] = $modal_fields_array['modal_tagline'];
                          //      $_POST['modal_info_entries'] = $modal_fields_array['modal_info_entries'];
                     //           $_POST['modal_photo_entries'] = $modal_fields_array['modal_photo_entries'];
                       //         $_POST['modal_tab_number'] = $modal_fields_array['modal_tab_number'];
                       //     }
                        }
                    //   setcookie("scene_post_status", "", time() - 300, "/");
                    }
                    if (isset($_COOKIE["figure_warnings"])){
                        $warning_message = "<p>Warning or warnings in figure</p>";
                        $warning_list_coded = stripslashes($_COOKIE["figure_warnings"]);
                        $warning_list_array = json_decode($warning_list_coded);
                        $warning_array_length = count($warning_list_array);
                        $warning_message = $warning_message . '<p><ul>';
                        for ($i = 0; $i < $warning_array_length; $i++){
                            $warning_message = $warning_message . '<li>' . $warning_list_array[$i] . '</li>';
                        }
                        $warning_message = $warning_message . '</ul></p>';
                        echo '<div class="notice notice-warning is-dismissible">' . $warning_message . '</div>'; 
                    }
                }
            }
        }
    }


}