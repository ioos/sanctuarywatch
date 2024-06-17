<?php
/**
 * Register class that defines the Figure custom content type as well as associated Modal functions 
 * 
 */

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
	 * Create custom fields, using metaboxes, for Scene custom content type.
	 *
	 * @since    1.0.0
	 */
    public function create_figure_fields() {

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
                    'id'             => 'figure_location',
                    'type'           => 'select',
                    'title'          => 'Instance',
                    'options'        => $locations,
                    'default_option' => 'Figure Instance',
                    'description' => 'Figure Instance',
                     'default'     => ' ',
                     'class'      => 'chosen', 
                ),

                array(
                    'id'    => 'figure_infographic',
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

        register_meta(
            'post', // Object type. In this case, 'post' refers to custom post type 'Scene'
            'scene_location', // Meta key name
            array(
                'show_in_rest' => true, // Make the field available in REST API
                'single' => true, // Indicates whether the meta key has one single value
                'type' => 'string', // Data type of the meta value
                'description' => 'The location of the scene', // Description of the meta key
                'sanitize_callback' => 'sanitize_text_field', // Callback function to sanitize the value
                'auth_callback' => function () {
                    return true; // Return true to allow reading, false to disallow writing
                }
            )
        );
 
        register_meta(
            'post', // Object type. In this case, 'post' refers to custom post type 'Scene'
            'scene_infographic', // Meta key name
            array(
                'show_in_rest' => true, // Make the field available in REST API
                'single' => true, // Indicates whether the meta key has one single value
                'type' => 'string', // Data type of the meta value
                'description' => 'The url of the infographic', // Description of the meta key
                'sanitize_callback' => 'sanitize_text_field', // Callback function to sanitize the value
                'auth_callback' => function () {
                    return true; // Return true to allow reading, false to disallow writing
                }
            )
        );

    }
}