<?php
/**
 * Register class that defines the Modal custom content type as well as associated Modal functions 
 * 
 */
include_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-webcr-utility.php';
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
            'show_in_rest'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'modals' ),
            'capability_type'    => 'post',
            'menu_icon'          => 'dashicons-category',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array( 'title' ), //array( 'title', 'revisions' ), 
        );
    
        register_post_type( 'modal', $args );
    }

    /**
	 * Create custom fields, using metaboxes, for Modal custom content type.
	 *
	 * @since    1.0.0
	 */
    function create_modal_fields() {

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

        // get list of locations
        $function_utilities = new Webcr_Utility();
        $locations = $function_utilities -> returnAllInstances();

        $scene_titles =[];
        $modal_icons = [];
        $icon_scene_out = []; 
        $modal_section = [];
        // used by both scene and icon dropdowns
        if (array_key_exists("post", $_GET)) {
            $modal_id = intval($_GET["post"]);
            $scene_id = intval(get_post_meta($modal_id, "modal_scene", true));
            $scene_titles = $function_utilities -> returnSceneTitles($scene_id, $modal_id);
            $modal_icons = $function_utilities -> returnIcons($scene_id);
            $icon_scene_out = $function_utilities -> returnScenesExceptCurrent($scene_id);
            $modal_section = $function_utilities -> returnModalSections($scene_id);
        }

        $fields = array(
            array(
                'id'             => 'modal_published',
                'type'           => 'select',
                'title'          => 'Modal Status*',
                'options'        => array("draft" => "Draft", "published" => "Published"),
                'default' => 'draft',
                'description' => 'Should the modal be live? If set to Draft, the assigned icon for this modal will behave as set in the scene option "Icon visibility in scene, if no associated modal". If set to Published, the icon will be visible in the scene.',
            ),
            array(
                'id'             => 'modal_location',
                'type'           => 'select',
                'title'          => 'Instance*',
                'options'        => $locations,
                'description' => 'In which instance is the modal located?'
            ),
            array(
                'id'             => 'modal_scene',
                'type'           => 'select',
                'title'          => 'Scene*',
                'options'        => $scene_titles,
                'description' => 'In which scene is the modal located?',
            ),
            array(
                'id'             => 'modal_icons',
                'type'           => 'select',
                'title'          => 'Icons*',
                'options'        => $modal_icons, 
                'description' => 'Which icon from the above scene is the modal associated with?',
            ),
            array(
                'id'      => 'modal_icon_order',
                'type'    => 'number',
                'title'   => 'Icon order (optional)',
                'default' => '1',
                // 'unit'    => '$',
                'min'     => '1',
                'max'     => '20',
                'step'    => '1',
                'description' => "In the table of contents to the right of the scene, what is the order in which this icon should appear? Lower numbers will appear first. All icons with the same order number (example: all icons keep the default value of 1), will be sorted alphabetically."
            ),
            array(
                'id'             => 'icon_toc_section',
                'type'           => 'select',
                'title'          => 'Icon Section*',
                'options'        =>  $modal_section,
                'description' => 'Which scene section is this modal associated with?',
            ),
            array(
                'id'             => 'icon_function',
                'type'           => 'select',
                'title'          => 'Icon Action*',
                'options'        => array("External URL" => "External URL", "Modal" => "Modal", "Scene" => "Scene"),
                'description' => 'What should happen when the user clicks on the icon?',
                'default'     => 'Modal',
            ),
            array(
                'id'          => 'icon_external_url',
                'type'        => 'text',
                'title'       => 'Icon External URL*',
                'class'       => 'text-class',   
                'description' => 'What is the external URL that the user should be taken to when the icon is clicked?',  
            ),
            array(
                'id'             => 'icon_scene_out',
                'type'           => 'select',
                'title'          => 'Icon Scene Out*',
                'options'        => $icon_scene_out,  
                'description' => 'What is the scene that the user should be taken to when the icon is clicked?',
                'default'     => "",
            ),
            array(
                'id'          => 'modal_tagline',
                'type'        => 'textarea',
                'title'       => 'Modal Tagline',
                'description' => 'What is the modal tagline?',
            ),
            array(
                'id'      => 'modal_info_entries',
                'type'    => 'range',
                'title'   => 'Number of Modal Info Entries',
                'description' => 'How many info links are there for the modal?',
                'min'     => 0,    
                'default' => 0,    
                'max'     => 6,         
                'step'    => 1,             
            ),    
            array(
                'id'      => 'modal_photo_entries',
                'type'    => 'range',
                'title'   => 'Number of Modal Photo Entries',
                'description' => 'How many photo links are there for the modal?',
                'min'     => 0,    
                'default' => 0,    
                'max'     => 6,         
                'step'    => 1,             
            ),     
            array(
                'id'      => 'modal_tab_number',
                'type'    => 'range',
                'title'   => 'Number of Modal Tabs*',
                'description' => 'How many modal tabs are there?',
                'min'     => 1,    
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
            )         
        );

        // Step 1: Create an array to hold the new info sub-arrays
        $infoFields = array();

        // Step 2: Use a loop to generate the new info sub-arrays
        for ($i = 1; $i <= 6; $i++) {
            $infoFields[] = array(
                'type' => 'fieldset',
                'id' => 'modal_info' . $i,
                'title'   => 'Modal Info Link ' . $i,
                'fields' => array(
                    array(
                        'id'          => 'modal_info_text' . $i,
                        'type'        => 'text',
                        'title'       => 'Text',
                        'class'       => 'text-class',
                    ),
                    array(
                        'id'          => 'modal_info_url' . $i,
                        'type'        => 'text',
                        'title'       => 'URL',
                        'class'       => 'text-class',
                    ),
                ),
            );
        }
        // Step 1: Create an array to hold the new info sub-arrays
        $photoFields = array();

        // Step 2: Use a loop to generate the new info sub-arrays
        for ($i = 1; $i <= 6; $i++) {
            $photoFields[] = array(
                'type' => 'fieldset',
                'id' => 'modal_photo' . $i,
                'title'   => 'Modal Photo Link ' . $i,
                'fields' => array(
                    array(
                        'id'             => 'modal_photo_location' . $i,
                        'type'           => 'select',
                        'title'          => 'Image Location',
                        'options'        => array("Internal" => "Within this site", "External" => "Outside of this site"),
                        'default'     => 'External',
                    ),
                    array(
                        'id'          => 'modal_photo_text' . $i,
                        'type'        => 'text',
                        'title'       => 'Link Text',
                        'class'       => 'text-class',
                    ),
                    array(
                        'id'          => 'modal_photo_url' . $i,
                        'type'        => 'text',
                        'title'       => 'URL',
                        'class'       => 'text-class',
                    ),
                    array(
                        'id'    => 'modal_photo_internal' . $i,
                        'type'  => 'image',
                        'title' => 'Image',
                    ),
                ),
            );
        }

        // Step 1: Create an array to hold the new info sub-arrays
        $tabFields = array();

        // Step 2: Use a loop to generate the new info sub-arrays
        for ($i = 1; $i <= 6; $i++) {
            $tabFields[] = array(
                    'id'          => 'modal_tab_title' . $i,
                    'type'        => 'text',
                    'title'       => 'Modal Tab Title ' . $i. '*',
                    'class'       => 'text-class'
            );
        }

        // Step 3: Insert the new sub-arrays after the second element in the original 'fields' array
        array_splice($fields, 11, 0, $infoFields);
        array_splice($fields, 18, 0, $photoFields);
        array_splice($fields, 25, 0, $tabFields);

        $fieldsHolder[] = array(
            'name'   => 'basic',
            'title'  => 'Basic',
            'icon'   => 'dashicons-admin-generic',
            'fields' => $fields,
        );

        // instantiate the admin page
        $options_panel = new Exopite_Simple_Options_Framework( $config_metabox, $fieldsHolder ); 

        // Create array of fields to be registered with register_meta
        $fieldsToBeRegistered = array(
            array('modal_scene', 'integer', 'The modal scene'),
            array('modal_icon_order', 'integer', 'The modal icon order'),
            array('icon_function', 'string', 'The icon function'),           
            array('modal_published', 'string', 'The icon function'),
            array('modal_tagline', 'string', 'The modal tagline'),
            array('icon_toc_section', 'string', 'The icon table of contents section'),
            array('modal_info_entries', 'integer', 'The number of info links'),
            array('modal_photo_entries', 'integer', 'The number of photo links'),
            array('modal_tab_number', 'integer', 'The number of modal tabs'),
        );

        for ($i = 1; $i < 7; $i++ ) {
            $fieldsToBeRegistered[] = array('modal_tab_title' . $i, 'string', 'Modal tab ' . $i);
        }
        foreach ($fieldsToBeRegistered as $targetSubArray) {
            register_meta(
                'post', // Object type. In this case, 'post' refers to custom post type 'Scene'
                $targetSubArray[0], // Meta key name
                array(
                    'show_in_rest' => true, // Make the field available in REST API
                    'single' => true, // Indicates whether the meta key has one single value
                    'type' => $targetSubArray[1], // Data type of the meta value
                    'description' => $targetSubArray[2], // Description of the meta key
                    'auth_callback' => '__return_false'
                )
            );
        }

        $fieldAndDescription = array(
            array('modal_info', 'Info link '),
            array('modal_photo', 'Photo link '),
            array('modal_photo_internal', 'Internal photo link ')
        );

        for ($i = 1; $i < 7; $i++ ) {
            foreach($fieldAndDescription as $targetFieldAndDescription){
                $target_field = $targetFieldAndDescription[0] . $i;
                $target_description = $targetFieldAndDescription[1] . $i;
                register_meta( 'post', 
                    $target_field,
                    array(
                        'auth_callback'     => '__return_false' ,
                        'single'            => true, // The field contains a single array
                        'description' => $target_description, // Description of the meta key
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
            }
        }
    }

    /**
	 * Register Modal custom fields for use by REST API.
	 *
	 * @since    1.0.0
	 */
    function register_modal_rest_fields() {
        $modal_rest_fields = array('modal_scene','modal_tagline', 'modal_published', 'modal_icon_order', 'icon_function','modal_info_entries', 
            'modal_photo_entries', 'modal_tab_number', 'icon_toc_section');

            for ($i = 1; $i < 7; $i++){
                array_push($modal_rest_fields,'modal_info' . $i, 'modal_photo' . $i, 'modal_tab_title' . $i );
            }
            $function_utilities = new Webcr_Utility();
            $function_utilities -> register_custom_rest_fields("modal", $modal_rest_fields);
    }

    /**
	 * Add a filter to support filtering by "modal_location" in REST API queries.
	 *
	 * @since    1.0.0
	 */
    function filter_modal_by_modal_scene($args, $request) {
        if (isset($request['modal_scene'])) {
            $args['meta_query'][] = array(
                'key' => 'modal_scene',
                'value' => $request['modal_scene'],
                'compare' => 'LIKE', // Change comparison method as needed
            );
        }
        // Filter by icon_function if set
        if (isset($request['icon_function'])) {
            $args['meta_query'][] = array(
                'key'     => 'icon_function',
                'value'   => $request['icon_function'],
                'compare' => '='
            );
        }
        $args['orderby'] = 'title';
        $args['order'] = 'ASC';
        return $args;
    }

    /**
	 * Add two filter dropdowns, field length and scene location, for the admin screen for the Modal content type.
	 *
	 * @since    1.0.0
	 */
    function modal_filter_dropdowns () {
        $screen = get_current_screen();
        if ( $screen->id == 'edit-modal' ){
            $fieldOptions = array(
                array("", "large", "Full tagline"),
                array("", "medium", "Medium tagline"),
                array("", "small", "Short tagline")
            );

            if (isset($_GET["field_length"])) {
                $field_length = $_GET["field_length"];
                switch ($field_length){
                    case "large":
                        $fieldOptions[0][0] = "selected ";
                        break;
                    case "medium":
                        $fieldOptions[1][0] = "selected ";
                        break;
                    case "small":
                        $fieldOptions[2][0] = "selected ";
                        break;
                }
            } else {
                $fieldOptions[2][0] = "selected ";
            }

            $field_length_dropdown = '<select name="field_length" id="field_length">';
            for ($i=0; $i <3; $i++){
                $field_length_dropdown .= '<option ' . $fieldOptions[$i][0] .  'value="' . $fieldOptions[$i][1] .'">' . $fieldOptions[$i][2] . '</option>';
            }
            $field_length_dropdown .= '</select>';

            echo $field_length_dropdown;
            
            // Instances dropdown 
            global $wpdb;
            $instances = $wpdb->get_results("
                SELECT ID, post_title 
                FROM {$wpdb->posts} 
                WHERE post_type = 'instance' 
                AND post_status = 'publish' 
                ORDER BY post_title ASC");
    
            echo '<select name="modal_instance" id="modal_instance">';
            echo '<option value="">All Instances</option>';
            foreach ($instances as $instance) {
                $selected = isset($_GET['modal_instance']) && $_GET['modal_instance'] == $instance->ID ? 'selected="selected"' : '';
                echo '<option value="' . $instance->ID . '" ' . $selected . '>' . $instance->post_title . '</option>';
            }
            echo '</select>';

            //Scene dropdown
            echo '<select name="modal_scene" id="modal_scene">';
            echo '<option value="">All Scenes</option>';
            if (isset($_GET['modal_instance']) && $_GET['modal_instance'] != ""){
                $scenes = $wpdb->get_results("
                SELECT p.ID, p.post_title 
                FROM $wpdb->posts p
                INNER JOIN $wpdb->postmeta pm ON p.ID = pm.post_id
                WHERE p.post_type = 'scene' 
                AND p.post_status = 'publish'
                AND pm.meta_key = 'scene_location' 
                AND pm.meta_value = " . $_GET['modal_instance']);

                foreach ($scenes as $scene) {
                    $selected = $_GET['modal_scene'] == $scene->ID ? 'selected="selected"' : '';
                    echo '<option value="' . $scene->ID . '" ' . $selected . '>' . $scene->post_title . '</option>';
                }
            }
            echo '</select>';

        }
    }

    /**
	 * Set columns in admin screen for Modal custom content type.
	 *
     * @link https://www.smashingmagazine.com/2017/12/customizing-admin-columns-wordpress/
	 * @since    1.0.0
	 */
    function change_modal_columns( $columns ) {
        $columns = array (
            //'cb' => $columns['cb'],
            'title' => 'Title',
            'modal_location' => 'Instance',
            'modal_scene' => 'Scene',		
            'modal_icons' => 'Icon',	
            'icon_function' => 'Function',		
            'modal_tagline' => 'Tagline',			
            'tab_number' => 'Tab #',	
            'status' => 'Status',
        );
        return $columns;
    }

    //hello there
    /**
     * Filter the results for the Modal admin screen by the Modal Location and Modal Scene dropdown fields.
     *
     * @param WP_Query $query The WordPress Query instance that is passed to the function.
     * @since    1.0.0
     */
    function modal_location_filter_results($query){
        global $pagenow;
        $type = 'modal';
        if ($pagenow == 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] == $type && isset($_GET['modal_instance']) && $_GET['modal_instance'] != '') {
            if ( isset($_GET['modal_scene']) && $_GET['modal_scene'] != '') {
                $meta_query = array(
                    array(
                        'key' => 'modal_scene', // The custom field storing the instance ID
                        'value' => $_GET['modal_scene'],
                        'compare' => '='
                    )
                );
            } else {
            $meta_query = array(
                array(
                    'key' => 'modal_location', // The custom field storing the instance ID
                    'value' => $_GET['modal_instance'],
                    'compare' => '='
                )
            );
            }
            $query->set('meta_query', $meta_query);
        }
    }

    /**
	 * Remove "view" link from admin screen for modal posts.
	 *
     * @param string $column The name of the column.
     * @param int $post_id The database id of the post.
	 * @since    1.0.0
	 */

    function remove_view_link_from_modal_post_type($actions, $post) {
        if ($post->post_type === 'modal' && isset($actions['view'])) {
            unset($actions['view']); // Remove the "View" link
        }
        return $actions;
    }

    /**
	 * Populate custom fields for Modal content type in the admin screen.
	 *
     * @param string $column The name of the column.
     * @param int $post_id The database id of the post.
	 * @since    1.0.0
	 */
    public function custom_modal_column( $column, $post_id ) {  

        // maybe knock this next section out
        if (isset($_GET["field_length"])) {
            $field_length = $_GET["field_length"];
        } else {
            $field_length = "small";
        }

        if ( $column === 'modal_location' ) {
            $instance_id = get_post_meta( $post_id, 'modal_location', true ); 
            echo get_the_title($instance_id ); 
         //   echo get_post_meta( $post_id, 'modal_location', true ); 
        }

        if ( $column === 'modal_scene' ) {
            $scene_id = get_post_meta( $post_id, 'modal_scene', true );
            $scene_title = get_the_title($scene_id);
            echo $scene_title; 
        }

        if ( $column === 'modal_icons' ) {
            echo get_post_meta( $post_id, 'modal_icons', true ); 
        }

        if ( $column === 'icon_function' ) {
            echo get_post_meta( $post_id, 'icon_function', true ); 
        }
        
        if ($column === 'modal_tagline'){
            $modal_tagline = get_post_meta( $post_id, 'modal_tagline', true );
            switch ($field_length){
                case "large":
                    echo $modal_tagline;
                    break;
                case "medium":
                    $medium_tagline = new Webcr_Utility();
                    $final_tagline = $medium_tagline -> stringTruncate($modal_tagline, 75);
                    echo $final_tagline;
                    break;
                case "small":
                    if ($modal_tagline != NULL){
                        echo '<span class="dashicons dashicons-yes"></span>';
                    }
                    break;
            }
        }

        if ($column == 'tab_number'){
            $tab_count = 0;
            for ($i = 1; $i < 7; $i++){
                $search_field = "modal_tab_title" . $i;
                $database_value = get_post_meta( $post_id, $search_field, true ); 
                if ($database_value != ""){
                    $tab_count++;
                }
            }
            echo $tab_count; 
        }

        if ($column === "status"){
            date_default_timezone_set('America/Los_Angeles'); 
            $last_modified_time = get_post_modified_time('g:i A', false, $post_id, true);
            $last_modified_date = get_post_modified_time('F j, Y', false, $post_id, true);
            $last_modified_user_id = get_post_meta($post_id, '_edit_last', true);
            if (empty($last_modified_user_id)){
                 $last_modified_user_id = get_post_field('post_author', $post_id);
            }
            $last_modified_user = get_userdata($last_modified_user_id);
            $last_modified_name = $last_modified_user -> first_name . " " . $last_modified_user -> last_name; 

            echo "Last updated at " . $last_modified_time . " Pacific Time on " . $last_modified_date . " by " . $last_modified_name;
        }
    }

    public function modal_admin_notice() {
        // First let's determine where we are. We only want to show admin notices in the right places. Namely in one of our custom 
        // posts after it has been updated. The if statement is looking for three things: 1. Modal post type? 2. An individual post (as opposed to the scene
        // admin screen)? 3. A new post

        if (function_exists('get_current_screen')) {
            $current_screen = get_current_screen();
            if ($current_screen){
                if ($current_screen->base == "post" && $current_screen->id =="modal" && !($current_screen->action =="add") ) { 
                    if( isset( $_COOKIE["modal_post_status"] ) ) {
                        $modal_post_status =  $_COOKIE["modal_post_status"];
                        if ($modal_post_status == "post_good") {
                            echo '<div class="notice notice-info is-dismissible"><p>Modal created or updated.</p></div>';
                        } 
                        else {
                            if (isset($_COOKIE["modal_errors"])) {
                                $error_message = "<p>Error or errors in modal</p>";
                                $error_list_coded = stripslashes($_COOKIE["modal_errors"]);
                                $error_list_array = json_decode($error_list_coded);
                                $error_array_length = count($error_list_array);
                                $error_message = $error_message . '<p><ul>';
                                for ($i = 0; $i < $error_array_length; $i++){
                                    $error_message = $error_message . '<li>' . $error_list_array[$i] . '</li>';
                                }
                                $error_message = $error_message . '</ul></p>';
                            }
                            echo '<div class="notice notice-error is-dismissible">' . $error_message . '</div>'; 
                        }
                    //   setcookie("scene_post_status", "", time() - 300, "/");
                    }
                    if (isset($_COOKIE["modal_warnings"])){
                        $warning_message = "<p>Warning or warnings in modal</p>";
                        $warning_list_coded = stripslashes($_COOKIE["modal_warnings"]);
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
