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
	 * Provide warning messages for user entry errors with posts of the Scene content type.
	 *
     * There are five WordPress warning classes: notice-warning, notice-error, notice-info, notice-success, plus is-dismissable
     * 
	 * @since    1.0.0
     * @link https://www.wpbeginner.com/wp-tutorials/how-to-add-admin-notices-in-wordpress/
	 */
    public function scene_admin_notice() {
        // First let's determine where we are. We only want to show admin notices in the right places. Namely in one of our custom 
        // posts after it has been updated. The if statement is looking for three things: 1. Scene post type? 2. An individual post (as opposed to the scene
        // admin screen)? 3. A new post?
        $current_screen = get_current_screen();
        if ($current_screen->base == "post" && $current_screen->id =="scene" && !($current_screen->action =="add") ) { 
            if( isset( $_COOKIE["scene_post_status"] ) ) {
                $scene_post_status =  $_COOKIE["scene_post_status"];
                if ($scene_post_status == "post_good") {
                    echo '<div class="notice notice-info is-dismissible"><p>Scene created or updated.</p></div>';
                } 
                else {
                    if (isset($_COOKIE["scene_errors"])) {
                        $error_message = "<p>Error or errors in scene</p>";
                        $error_list_coded = stripslashes($_COOKIE["scene_errors"]);
                        $error_list_array = json_decode($error_list_coded);
                        $error_array_length = count($error_list_array);
                        $error_message = $error_message . '<p><ul>';
                        for ($i = 0; $i < $error_array_length; $i++){
                            $error_message = $error_message . '<li>' . $error_list_array[$i] . '</li>';
                        }
                        $error_message = $error_message . '</ul></p>';
                    }
                    echo '<div class="notice notice-error is-dismissible">' . $error_message . '</div>'; 

                    if (isset($_COOKIE["scene_error_all_fields"])) {
                        $scene_fields_coded = stripslashes($_COOKIE["scene_error_all_fields"]);
                        $scene_fields_array = json_decode($scene_fields_coded, true);	
                        $_POST['scene_location'] = $scene_fields_array['scene_location'];
                        $_POST['scene_infographic'] = $scene_fields_array['scene_infographic'];
                        $_POST['scene_tagline'] = $scene_fields_array['scene_tagline'];
                        $_POST['scene_info_entries'] = $scene_fields_array['scene_info_entries'];
                        $_POST['scene_photo_entries'] = $scene_fields_array['scene_photo_entries'];

                    }
                }
             //   setcookie("scene_post_status", "", time() - 300, "/");
            }
            if (isset($_COOKIE["scene_warnings"])){
                $warning_message = "<p>Warning or warnings in scene</p>";
                $warning_list_coded = stripslashes($_COOKIE["scene_warnings"]);
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

    /**
	 * Add two filter dropdowns, field length and scene location, for the admin screen for the Scene content type.
	 *
	 * @since    1.0.0
	 */
    function scene_filter_dropdowns () {
        $screen = get_current_screen();
        if ( $screen->id == 'edit-scene' ){
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
            }

            $field_length_dropdown = '<select name="field_length" id="field_length">';
            for ($i=0; $i <3; $i++){
                $field_length_dropdown .= '<option ' . $fieldOptions[$i][0] .  'value="' . $fieldOptions[$i][1] .'">' . $fieldOptions[$i][2] . '</option>';
            }
            $field_length_dropdown .= '</select>';

            echo $field_length_dropdown;
            
            $locations_array = get_terms(array('taxonomy' => 'location', 'hide_empty' => false));
            $locations = array(array("All Locations", ""));
            foreach ( $locations_array as $locations_row ){
                array_push($locations, array($locations_row -> name,"")); 
            }

            if (isset($_GET["scene_location"])) { 
                $scene_location = str_replace("_", " ", $_GET["scene_location"]);
                $i = 0;
                foreach ($locations as $location_row) {
                    if ($location_row[0] == $scene_location) {
                        $locations[$i][1] = "selected ";
                        break;
                    }
                    $i++;
                }
            }

            $location_dropdown = '<select name="scene_location" id="scene_location">';
            foreach ($locations as $location_row) {
                $location_dropdown .= '<option ' . $location_row[1] . 'value="' . $location_row[0] .'">' . $location_row[0]  . '</option>';
            }
            $location_dropdown .= '</select>';
            echo $location_dropdown;
        }
    }

    /**
     * Filter the results for the Scene admin screen by the Scene Location dropdown field.
     *
     * @param WP_Query $query The WordPress Query instance that is passed to the function.
     * @since    1.0.0
     */
    function scene_location_filter_results($query){
        if ( isset($_GET['post_type']) ) {
            $post_type = $_GET['post_type'];
            if ($post_type = "scene"){
                if(isset($_GET['scene_location'])){
                    $scene_location = str_replace("_", " ", $_GET['scene_location']);
                    if($scene_location != "All Locations"){
                        $meta_query = array( 'relation' => 'OR' );

                        array_push( $meta_query, array(
                            'key' => "scene_location",
                            'value' => $scene_location,
                            'compare' => 'LIKE'
                        ));
                        $query->set( 'meta_query', $meta_query );
                    }
                }
            }
        }
    }

    /**
	 * Set columns in admin screen for Scene custom content type.
	 *
     * @link https://www.smashingmagazine.com/2017/12/customizing-admin-columns-wordpress/
	 * @since    1.0.0
	 */
    function change_scene_columns( $columns ) {
        $columns = array (
            //'cb' => $columns['cb'],
            'title' => 'Title',
            'scene_location' => 'Location',
            'scene_infographic' => 'Infographic',		
            'scene_tagline' => 'Tagline',			
            'scene_info_link' => 'Info Link Number',		
            'scene_info_photo_link' => 'Photo Link Number',
            'scene_order' => 'Order',	
            'status' => 'Status',
        );
        return $columns;
    }

    /**
	 * Populate custom fields for Scene content type in the admin screen.
	 *
     * @param string $column The name of the column.
     * @param int $post_id The database id of the post.
	 * @since    1.0.0
	 */
    public function custom_scene_column( $column, $post_id ) {  
        // scene location column

        if (isset($_GET["field_length"])) {
            $field_length = $_GET["field_length"];
        } else {
            $field_length = "large";
        }

        if ( $column === 'scene_location' ) {
            echo get_post_meta( $post_id, 'scene_location', true ); 
        }

        if ( $column === 'scene_infographic' ) {
                $scene_infographic = get_post_meta($post_id, 'scene_infographic', true);
                if (!empty($scene_infographic)) {
                        echo '<img src="' . esc_url($scene_infographic) . '" style="max-width:100px; max-height:100px;" /><br>';
                }
        }

        if ($column == 'scene_tagline'){
            $scene_tagline = get_post_meta( $post_id, 'scene_tagline', true );
            switch ($field_length){
                case "large":
                    echo $scene_tagline;
                    break;
                case "medium":
                    echo $this->stringTruncate($scene_tagline, 75);
                    break;
                case "small":
                    if ($scene_tagline != NULL){
                        echo '<span class="dashicons dashicons-yes"></span>';
                    }
                    break;
            }
        }

        if ($column == 'scene_info_photo_link'){
            $url_count = 0;
            for ($i = 1; $i < 7; $i++){
                $search_fieldset = "scene_photo" . $i;
                $search_field = "scene_photo_url" . $i;
                $database_value = get_post_meta( $post_id, $search_fieldset, true )[$search_field]; 
                if ($database_value != ""){
                    $url_count++;
                }
            }
            echo $url_count; 

        }

        if ($column == 'scene_info_link'){

            $url_count = 0;
            for ($i = 1; $i < 7; $i++){
                $search_fieldset = "scene_info" . $i;
                $search_field = "scene_info_url" . $i;
                $database_value = get_post_meta( $post_id, $search_fieldset, true )[$search_field]; 
                if ($database_value != ""){
                    $url_count++;
                }
            }
            echo $url_count; 

        }
        if ( $column === 'scene_order' ) {
            echo get_post_meta( $post_id, 'scene_order', true ); 
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
	 * Shorten string without cutting words midword.
	 *
     * @param string $string The string to be shortened.
     * @param int $your_desired_width The number of characters in the shortened string.
	 * @since    1.0.0
	 */
    public function stringTruncate($string, $your_desired_width) {
        $parts = preg_split('/([\s\n\r]+)/', $string, null, PREG_SPLIT_DELIM_CAPTURE);
        $parts_count = count($parts);
        
        $length = 0;
        //$last_part = 0;
        for ($last_part = 0; $last_part < $parts_count; ++$last_part) {
            $length += strlen($parts[$last_part]);
            if ($length > $your_desired_width) { break; }
        }
        
        return implode(array_slice($parts, 0, $last_part));
    }

    /**
	 * Make Location a sortable column in the admin screen for the Scene custom content type.
	 *
	 * @since    1.0.0
	 */
    function scene_location_column_sortable($columns) {
        $columns['scene_location'] = 'scene_location';
        return $columns;
    }

    /**
	 * Provide sorting logic for Location column in the admin screen for the Scene custom content type.
	 *
     * @param WP_Query $query The WordPress Query instance that is passed to the function.
	 * @since    1.0.0
	 */
    function scene_location_orderby($query) {
        if (!is_admin() || !$query->is_main_query()) {
            return;
        }

        if ($query->get('orderby') == 'scene_location') {
            $query->set('meta_key', 'scene_location');
            $query->set('orderby', 'meta_value');
        }
    }

    /**
	 * Remove Bulk Actions dropdown from Scene admin screen.
	 *
     * @param array $actions An array of the available bulk actions.
	 * @since    1.0.0
	 */
    function remove_bulk_actions_scene($actions) {
        global $post_type;
    
        if ($post_type === 'scene') {
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
	 * Remove Quick Edit links from Scene admin screen.
	 *
     * @param string[] $actions An array of row action links.
     * @param int $post The database id of the post.
	 * @since    1.0.0
	 */
    function scene_remove_quick_edit_link($actions, $post) {
        global $current_screen;
    
        if ($current_screen->post_type === 'scene') {
            unset($actions['inline hide-if-no-js']);
        }
        return $actions;
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
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'scenes' ),
            'capability_type'    => 'post',
            'menu_icon'          => 'dashicons-tag',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array( 'title', 'revisions' ),
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

        // get list of locations, which is saved as a taxonomy
        $locations_array = get_terms(array('taxonomy' => 'location', 'hide_empty' => false));
        $locations=[];
        foreach ( $locations_array as $locations_row ){
            $locations[$locations_row -> name] = $locations_row -> name;
        }

        $fields[] = array(
            'name'   => 'basic',
            'title'  => 'Basic',
            'icon'   => 'dashicons-admin-generic',
            'fields' => array(

                array(
                    'id'             => 'scene_location',
                    'type'           => 'select',
                    'title'          => 'Location',
                    'options'        => $locations,
                    'default_option' => 'Scene Location',
                    'description' => 'Scene Location',
                     'default'     => ' ',
                     'class'      => 'chosen', 
                ),

                array(
                    'id'    => 'scene_infographic',
                    'type'  => 'image',
                    'title' => 'Scene Infographic',
                    'description' => 'Infographic description'
                ),
                array(
                    'id'          => 'scene_tagline',
                    'type'        => 'textarea',
                    'title'       => 'Scene Tagline',
                    'description' => 'Tagline description',
                ),
                array(
                    'id'      => 'scene_info_entries',
                    'type'    => 'range',
                    'title'   => 'Number of Scene Info Entries',
                    'description' => 'Number of Scene Info Entries description',
                    'min'     => 0,    
                     'default' => 1,    
                     'max'     => 6,         
                     'step'    => 1,             
                ),              
                array(
                    'type' => 'fieldset',
                    'id' => 'scene_info1',
                    'title'   => 'Scene Info Link 1',
                    'description' => 'Scene Info Link 1 description',
                    'fields' => array(
                        array(
                            'id'          => 'scene_info_text1',
                            'type'        => 'text',
                            'title'       => 'Text',
                            'class'       => 'text-class',
                        ),
                        array(
                            'id'          => 'scene_info_url1',
                            'type'        => 'text',
                            'title'       => 'URL',
                            'class'       => 'text-class',
                        ),
                    ),
                ),
                array(
                    'type' => 'fieldset',
                    'id' => 'scene_info2',
                    'title'   => 'Scene Info Link 2',
                    'description' => 'Scene Info Link 2 description',
                    'fields' => array(
                        array(
                            'id'          => 'scene_info_text2',
                            'type'        => 'text',
                            'title'       => 'Text',
                            'class'       => 'text-class',
                        ),
                        array(
                            'id'          => 'scene_info_url2',
                            'type'        => 'text',
                            'title'       => 'URL',
                            'class'       => 'text-class',
                        ),
                    ),
                ),
                array(
                    'type' => 'fieldset',
                    'id' => 'scene_info3',
                    'title'   => 'Scene Info Link 3',
                    'description' => 'Scene Info Link 3 description',
                    'fields' => array(
                        array(
                            'id'          => 'scene_info_text3',
                            'type'        => 'text',
                            'title'       => 'Text',
                            'class'       => 'text-class',
                        ),
                        array(
                            'id'          => 'scene_info_url3',
                            'type'        => 'text',
                            'title'       => 'URL',
                            'class'       => 'text-class',
                        ),
                    ),
                ),
                array(
                    'type' => 'fieldset',
                    'id' => 'scene_info4',
                    'title'   => 'Scene Info Link 4',
                    'description' => 'Scene Info Link 4 description',
                    'fields' => array(
                        array(
                            'id'          => 'scene_info_text4',
                            'type'        => 'text',
                            'title'       => 'Text',
                            'class'       => 'text-class',
                        ),
                        array(
                            'id'          => 'scene_info_url4',
                            'type'        => 'text',
                            'title'       => 'URL',
                            'class'       => 'text-class',
                        ),
                    ),
                ),
                array(
                    'type' => 'fieldset',
                    'id' => 'scene_info5',
                    'title'   => 'Scene Info Link 5',
                    'description' => 'Scene Info Link 5 description',
                    'fields' => array(
                        array(
                            'id'          => 'scene_info_text5',
                            'type'        => 'text',
                            'title'       => 'Text',
                            'class'       => 'text-class',
                        ),
                        array(
                            'id'          => 'scene_info_url5',
                            'type'        => 'text',
                            'title'       => 'URL',
                            'class'       => 'text-class',
                        ),
                    ),
                ),

                array(
                    'type' => 'fieldset',
                    'id' => 'scene_info6',
                    'title'   => 'Scene Info Link 6',
                    'description' => 'Scene Info Link 6 description',
                    'fields' => array(
                        array(
                            'id'          => 'scene_info_text6',
                            'type'        => 'text',
                            'title'       => 'Text',
                            'class'       => 'text-class',
                        ),
                        array(
                            'id'          => 'scene_info_url6',
                            'type'        => 'text',
                            'title'       => 'URL',
                            'class'       => 'text-class',
                        ),
                    ),
                ),

                array(
                    'id'      => 'scene_photo_entries',
                    'type'    => 'range',
                    'title'   => 'Number of Scene Photo Entries',
                    'description' => 'Number of Scene Photo Entries description',
                    'min'     => 0,    
                     'default' => 1,    
                     'max'     => 6,         
                     'step'    => 1,             
                ),              
                array(
                    'type' => 'fieldset',
                    'id' => 'scene_photo1',
                    'title'   => 'Scene Photo Link 1',
                    'description' => 'Scene Photo 1 description',
                    'fields' => array(
                        array(
                            'id'          => 'scene_photo_text1',
                            'type'        => 'text',
                            'title'       => 'Text',
                            'class'       => 'text-class',
                        ),
                        array(
                            'id'          => 'scene_photo_url1',
                            'type'        => 'text',
                            'title'       => 'URL',
                            'class'       => 'text-class',
                        ),
                    ),
                ),
                array(
                    'type' => 'fieldset',
                    'id' => 'scene_photo2',
                    'title'   => 'Scene Photo Link 2',
                    'description' => 'Scene Photo Link 2 description',
                    'fields' => array(
                        array(
                            'id'          => 'scene_photo_text2',
                            'type'        => 'text',
                            'title'       => 'Text',
                            'class'       => 'text-class',
                        ),
                        array(
                            'id'          => 'scene_photo_url2',
                            'type'        => 'text',
                            'title'       => 'URL',
                            'class'       => 'text-class',
                        ),
                    ),
                ),
                array(
                    'type' => 'fieldset',
                    'id' => 'scene_photo3',
                    'title'   => 'Scene Photo Link 3',
                    'description' => 'Scene Photo Link 3 description',
                    'fields' => array(
                        array(
                            'id'          => 'scene_photo_text3',
                            'type'        => 'text',
                            'title'       => 'Text',
                            'class'       => 'text-class',
                        ),
                        array(
                            'id'          => 'scene_photo_url3',
                            'type'        => 'text',
                            'title'       => 'URL',
                            'class'       => 'text-class',
                        ),
                    ),
                ),
                array(
                    'type' => 'fieldset',
                    'id' => 'scene_photo4',
                    'title'   => 'Scene Photo Link 4',
                    'description' => 'Scene Photo Link 4 description',
                    'fields' => array(
                        array(
                            'id'          => 'scene_photo_text4',
                            'type'        => 'text',
                            'title'       => 'Text',
                            'class'       => 'text-class',
                        ),
                        array(
                            'id'          => 'scene_photo_url4',
                            'type'        => 'text',
                            'title'       => 'URL',
                            'class'       => 'text-class',
                        ),
                    ),
                ),
                array(
                    'type' => 'fieldset',
                    'id' => 'scene_photo5',
                    'title'   => 'Scene Photo Link 5',
                    'description' => 'Scene Photo Link 5 description',
                    'fields' => array(
                        array(
                            'id'          => 'scene_photo_text5',
                            'type'        => 'text',
                            'title'       => 'Text',
                            'class'       => 'text-class',
                        ),
                        array(
                            'id'          => 'scene_photo_url5',
                            'type'        => 'text',
                            'title'       => 'URL',
                            'class'       => 'text-class',
                        ),
                    ),
                ),

                array(
                    'type' => 'fieldset',
                    'id' => 'scene_photo6',
                    'title'   => 'Scene Photo Link 6',
                    'description' => 'Scene Photo Link 6 description',
                    'fields' => array(
                        array(
                            'id'          => 'scene_photo_text6',
                            'type'        => 'text',
                            'title'       => 'Text',
                            'class'       => 'text-class',
                        ),
                        array(
                            'id'          => 'scene_photo_url6',
                            'type'        => 'text',
                            'title'       => 'URL',
                            'class'       => 'text-class',
                        ),
                    ),
                ),

                array(
                    'id'      => 'scene_order',
                    'type'    => 'number',
                    'title'   => 'Scene Order',
                    'description' => 'Add description',
                    'default' => '1',                               
                    'min'     => '1',                                    
                    'max'     => '10',      
                    'step'    => '1',   
                ),

                array(
                    'id'          => 'scene_preview',
                    'type'        => 'button',
                    'title'       => 'Preview Scene',
                    'class'        => 'scene_preview',
                    'options'     => array(
                        'href'  =>  '#',
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

    function scene_preview() {
      
        header("Location: ".$_SERVER["HTTP_REFERER"]);
      //  echo "hello";
        wp_die();
    }

}