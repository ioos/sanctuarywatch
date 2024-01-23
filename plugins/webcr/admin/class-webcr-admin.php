<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.noaa.gov
 * @since      1.0.0
 *
 * @package    Webcr
 * @subpackage Webcr/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Webcr
 * @subpackage Webcr/admin
 * @author     Jai Ranganathan <jai.ranganathan@noaa.gov>
 */
class Webcr_Admin {

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
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Webcr_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Webcr_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/webcr-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Webcr_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Webcr_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/webcr-admin.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( "webcr-admin-scene", plugin_dir_url( __FILE__ ) . 'js/webcr-admin-scene.js', array( 'jquery' ), $this->version, array('strategy'  => 'defer') );
	}

    // JAI - function to change wordpress login screen logo
    public function webcr_login_logo() { ?>
        <style type="text/css">
            #login h1 a, .login h1 a {
                background-image: url(<?php echo plugin_dir_url( __FILE__ ); ?>images/onms-logo-800.png);
            height:150px;
            width:150px;
            background-size: 150px 150px;
            background-repeat: no-repeat;
                padding-bottom: 1px;
            }
        </style>
    <?php }

    //JAI - two functions to change wordpress login screen. 1) Change url associated with logo. 2) Change header text
    public function webcr_logo_url() {
        return home_url();
    }

    public function webcr_logo_url_title() {
        return 'Sanctuary Watch';
    }

    // JAI - Change wordpress login screen page title
    public function custom_login_title( $login_title ) {
        return str_replace(array( ' &lsaquo;', ' &#8212; WordPress'), array( ' &bull;', ' Sanctuary Watch'),$login_title );
    }

    // JAI - change default favicon
    function add_favicon() {
        $favicon_url = plugin_dir_url( __FILE__ ) . 'images/onms-logo-80.png';
        echo '<link rel="shortcut icon" href="' . $favicon_url . '" />';
   }

    public function get_all_emails() {

        $all_users = get_users();

        $user_email_list = array();

        foreach ($all_users as $user) {
            $user_email_list[esc_html($user->user_email)] = esc_html($user->display_name);
        }

        return $user_email_list;

    }

    public function test_sanitize_callback( $val ) {
        return str_replace ( 'a', 'b', $val );
    }

    // JAI - new santization function
    public function wrapper_sanitize_url($val) {
        return sanitize_url( $val, array( 'https' ) );
    }


    // JAI - create scene entry fields
    public function create_scene_fields() {

        /*
         * To add a metabox.
         * This normally go to your functions.php or another hook
         */
        $config_metabox = array(

            /*
             * METABOX
             */
            'type'              => 'metabox',                       // Required, menu or metabox
            'id'                => $this->plugin_name,              // Required, meta box id, unique, for saving meta: id[field-id]
            'post_types'        => array( 'scene' ),                 // Post types to display meta box
            // 'post_types'        => array( 'post', 'page' ),         // Could be multiple
            'context'           => 'advanced',                      // 	The context within the screen where the boxes should display: 'normal', 'side', and 'advanced'.
            'priority'          => 'default',                       // 	The priority within the context where the boxes should show ('high', 'low').
            'title'             => 'Scene Fields',                  // The title of the metabox
            'capability'        => 'edit_posts',                    // The capability needed to view the page
            'tabbed'            => true,
            // 'multilang'         => false,                        // To turn of multilang, default off except if you have qTransalte-X.
            'options'           => 'simple',                        // Only for metabox, options is stored az induvidual meta key, value pair.
        );

        // JAI - get list of locations
        $locations_array = get_terms(array('taxonomy' => 'location', 'hide_empty' => false));
        $locations=[];
        foreach ( $locations_array as $locations_row ){
            array_push($locations, $locations_row -> name );    
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
                ),

                array(
                    'id'    => 'scene_infographic',
                    'type'  => 'image',
                    'title' => 'Scene Infographic',
                    'description' => 'Infographic description'
  //                  'sanitize'    => array( $this, 'test_sanitize_callback' ),
                ),
                array(
                    'id'          => 'scene_tagline',
                    'type'        => 'textarea',
                    'title'       => 'Scene Tagline',
                    'description' => 'Tagline description',
                ),

                array(
                    'id'          => 'scene_info_link',
                    'type'        => 'text',
                    'title'       => 'Scene Info Link',
                    'class'       => 'text-class',
                    'description' => 'Add description',
 //                   'sanitize'    => array( $this, 'wrapper_sanitize_url' )
                ),

                array(
                    'id'          => 'scene_info_photo_link',
                    'type'        => 'text',
                    'title'       => 'Scene Info Photo Link',
                    'class'       => 'text-class',
                    'description' => 'Add description',
  //                  'sanitize'    => array( $this, 'wrapper_sanitize_url' )
                ),
            )
        );

        /**
         * instantiate your admin page
         */ 
        $options_panel = new Exopite_Simple_Options_Framework( $config_metabox, $fields );

    }

    /**
     * Add new image size for admin thumbnail.
     *
     * @link https://wordpress.stackexchange.com/questions/54423/add-image-size-in-a-plugin-i-created/304941#304941
     */
    public function add_thumbnail_size() {
        add_image_size( 'new_thumbnail_size', 60, 75, true );
    }

    public function add_style_to_admin_head() {
        global $post_type;
        if ( 'test' == $post_type ) {
            ?>
                <style type="text/css">
                    .column-thumbnail {
                        width: 80px !important;
                    }
                    .column-title {
                        width: 30% !important;
                    }
                </style>
            <?php
        }
    }

    /**
     * To sort, Exopite Simple Options Framework need 'options' => 'simple'.
     * Simple options is stored az induvidual meta key, value pair, otherwise it is stored in an array.
     *
     *
     * Meta key value paars need to sort as induvidual.
     *
     * I implemented this option because it is possible to search in serialized (array) post meta:
     * @link https://wordpress.stackexchange.com/questions/16709/meta-query-with-meta-values-as-serialize-arrays
     * @link https://stackoverflow.com/questions/15056407/wordpress-search-serialized-meta-data-with-custom-query
     * @link https://www.simonbattersby.com/blog/2013/03/querying-wordpress-serialized-custom-post-data/
     *
     * but there is no way to sort them with wp_query or SQL.
     * @link https://wordpress.stackexchange.com/questions/87265/order-by-meta-value-serialized-array/87268#87268
     * "Not in any reliable way. You can certainly ORDER BY that value but the sorting will use the whole serialized string,
     * which will give * you technically accurate results but not the results you want. You can't extract part of the string
     * for sorting within the query itself. Even if you wrote raw SQL, which would give you access to database functions like
     * SUBSTRING, I can't think of a dependable way to do it. You'd need a MySQL function that would unserialize the value--
     * you'd have to write it yourself.
     * Basically, if you need to sort on a meta_value you can't store it serialized. Sorry."
     *
     * It is possible to get all required posts and store them in an array and then sort them as an array,
     * but what if you want multiple keys/value pair to be sorted?
     *
     * UPDATE
     * it is maybe possible:
     * @link http://www.russellengland.com/2012/07/how-to-unserialize-data-using-mysql.html
     * but it is waaay more complicated and less documented as meta query sort and search.
     * It should be not an excuse to use it, but it is not as reliable as it should be.
     *
     * @link https://wpquestions.com/Order_by_meta_key_where_value_is_serialized/7908
     * "...meta info serialized is not a good idea. But you really are going to lose the ability to query your
     * data in any efficient manner when serializing entries into the WP database.
     *
     * The overall performance saving and gain you think you are achieving by serialization is not going to be noticeable to
     * any major extent. You might obtain a slightly smaller database size but the cost of SQL transactions is going to be
     * heavy if you ever query those fields and try to compare them in any useful, meaningful manner.
     *
     * Instead, save serialization for data that you do not intend to query in that nature, but instead would only access in
     * a passive fashion by the direct WP API call get_post_meta() - from that function you can unpack a serialized entry
     * to access its array properties too."
     */
    public function manage_sortable_columns( $columns ) {

        $columns['text_1'] = 'text_1';
        $columns['color_2'] = 'color_2';
        $columns['date_2'] = 'date_2';

        return $columns;

    }

    public function manage_posts_orderby( $query ) {

        if( ! is_admin() || ! $query->is_main_query() ) {
            return;
        }

        /**
         * meta_types:
         * Possible values are 'NUMERIC', 'BINARY', 'CHAR', 'DATE', 'DATETIME', 'DECIMAL', 'SIGNED', 'TIME', 'UNSIGNED'.
         * Default value is 'CHAR'.
         *
         * @link https://codex.wordpress.org/Class_Reference/WP_Meta_Query
         */
        $columns = array(
            'text_1'  => 'char',
            'color_2' => 'char',
            'date_2'  => 'date',
        );

        foreach ( $columns as $key => $type ) {

            if ( $key === $query->get( 'orderby') ) {
                $query->set( 'orderby', 'meta_value' );
                $query->set( 'meta_key', $key );
                $query->set( 'meta_type', $type );
                break;
            }

        }

    }
    // END ADD/REMOVE/REORDER/SORT CUSTOM POST TYPE LIST COLUMNS (test)




}

