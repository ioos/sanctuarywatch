<?php
  /**
   * Theme Functionality File
   *
   * This file is part of a WordPress theme and is responsible for defining and handling the loading of all theme-specific
   * scripts, styles, and custom post types. The functions are designed to enhance the theme's capabilities, ensuring
   * proper style, script management, and custom post type functionalities. Each function is hooked to an appropriate action or filter within WordPress to ensure they execute at the right time
   * during page load or admin panel interactions. Proper use of actions and filters follows WordPress best practices,
   * aiming to extend the functionality of WordPress themes without modifying core files.
   */

   function enqueue_font_awesome() {
    wp_enqueue_style(
        'font-awesome', 
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css', 
        array(), 
        '6.6.0', 
        'all' 
    );
}

add_action('wp_enqueue_scripts', 'enqueue_font_awesome');





  /**
   * Enqueues the theme's main stylesheet.
   *
   * This function utilizes `wp_enqueue_style` to register the theme's main 
   * stylesheet using the current theme's stylesheet URI. Used to ensure
   * that the main stylesheet is properly added to the HTML output of the WordPress theme.
   *
   * @return void
   */
  function files() {
    wp_enqueue_style( 'style', get_stylesheet_uri() );
  } 
  add_action( 'wp_enqueue_scripts', 'files' );

  /**
   * Enqueues Bootstrap's CSS library with dependency management.
   *
   * This function registers and enqueues the Bootstrap CSS library from a CDN.
   *
   * @return void
   */
  function enqueue_bootstrap_css(){
    wp_register_style('bootstrap', get_template_directory_uri() . '/css/bootstrap.min.css', array('jquery'), 
            null,  array('strategy' => 'defer,'));
    wp_enqueue_style('bootstrap');
  }
  add_action('wp_enqueue_scripts', 'enqueue_bootstrap_css');



  /**
   * Enqueues Bootstrap's JavaScript library with dependency management.
   *
   * This function registers and enqueues the Bootstrap JavaScript library from a CDN. It specifies jQuery as a dependency,
   * meaning jQuery will be loaded before the Bootstrap JavaScript. The script is added to the footer of the HTML document 
   * and is set to defer loading until after the HTML parsing has completed.
   *
   * @return void
   */
  // function enqueue_bootstrap_scripts() {
  //   wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js', array('jquery'), null, array('strategy' => 'defer,'));
  // }
  // add_action('wp_enqueue_scripts', 'enqueue_bootstrap_scripts');
  function enqueue_bootstrap_scripts() {
    wp_enqueue_script(
        'bootstrap-js', 
        'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js', 
        array('jquery'), 
        null, 
        array('strategy' => 'defer') // Corrected the 'strategy' syntax
    );
}
add_action('wp_enqueue_scripts', 'enqueue_bootstrap_scripts');

  function enqueue_api_script(){
    wp_enqueue_script( 'wp-api' );
  }
  add_action('wp_enqueue_scripts', 'enqueue_api_script');



  /**
   * Registers the 'scene' custom post type.
   *
   * This function sets up a new custom post type called 'Scenes'. It is publicly queryable, includes an archive page, 
   * and rewrites the URL slug to the root of the site. The function is hooked into the 'init' action. The CPT 'scene' 
   * supports titles and editor fields by default and is intended to represent individual scenes as distinct post 
   * entries within WordPress.
   *
   * @return void
   */
  //attempting to rewrite scene base url
  //for some reason, despite the post being already registed in the backend by Jai, without registering the new urls doesnt link ;-;
  function register_scene_post_type (){
    $args = array (
      'labels' => array(
        'name' => 'Scenes',
        'singular_name' => 'Scene',
      ),
      'public' => true,
      'has_archive' => true,
      'rewrite' => array(
        'slug' => '',
        'with_front' => false,
        )
    );
    
    register_post_type( 'scene', $args);
  }
  // add_action('init', 'register_scene_post_type' );
//   function add_custom_rewrite_rules() {
//     add_rewrite_rule(
//         '^channel-islands/([^/]+)/?$',
//         'index.php?scene=$matches[1]',
//         'top'
//     );
// }
//   add_action('init', 'add_custom_rewrite_rules');

  /**
   * Registers the 'about' custom post type.
   *
   * This function sets up a new custom post type called 'about'. It
   *
   * @return void
   */
  //attempting to rewrite scene base url
  // function register_about_post_type (){
  //   $args = array (
  //     'labels' => array(
  //       'name' => 'About',
  //       'singular_name' => 'About',
  //     ),
  //     'public' => true,
  //     'has_archive' => true,
  //     'rewrite' => array(
  //       'slug' => 'about',
  //       'with_front' => false,
  //       )
  //   );
    
  //   register_post_type( 'about', $args);
  // }
  // add_action('init', 'register_about_post_type' );

  /**
   * Adds rewrite rules for custom 'scene' post type URLs.
   *
   * This function creates a new rewrite rule that allows URLs to be structured by a scene location and scene name 
   * for the custom post type 'scene'. The URLs will take the form of /{scene_location}/{scene_name}/. 
   * This function uses `add_rewrite_rule` to dictate how URLs are intercepted and parsed by WordPress.
   * The rule is added at the top of the rewrite rules, therefore processed before WordPress's default rules.
   *
   * @return void
   */
  // Add custom rewrite rules
  // function scene_rewrite_rules() {
  //   add_rewrite_rule(
  //       '^([^/]+)/([^/]+)/?$',
  //       'index.php?post_type=scene&scene_location=$matches[1]&name=$matches[2]',
  //       'top'
  //   );
  // }
  // add_action('init', 'scene_rewrite_rules');

  /**
   * Filters the permalink for a 'scene' custom post type.
   *
   * This function adjusts the permalink structure for posts of the 'scene' custom post type based on post metadata.
   * It constructs a URL that includes a base identifier (`webcr-`), a concatenation of the first letters of each
   * word in the `scene_location` metadata (excluding the last word), and the post name. This function is hooked into 
   * the `post_type_link` filter, which allows it to modify the permalink URLs for the 'scene' post type on the fly 
   * as WordPress generates them.
   *
   * @param string $post_link The original permalink URL.
   * @param WP_Post $post The post object for which the permalink is being generated.
   * @return string Modified permalink URL if the post type is 'scene', original URL otherwise.
   */
  //changing the scene url
  // function scene_post_type_permalink($post_link, $post) {
  //   if (is_object($post) && $post->post_type == 'scene') {
  //       $postMeta = get_post_meta($post->ID);
  //       //Undefined array key "scene_location"
  //       $sceneLocation = $postMeta['scene_location'][0];
  //       if ($sceneLocation) {
  //           $scene_base_url = 'channel-islands';
  //           $sceneArr = explode(' ', strtolower($sceneLocation));
  //           $scene_letters = '';
  //           for ($i = 0; $i < count($sceneArr) - 1; $i++) {
  //               $scene_base_url = $scene_base_url . $sceneArr[$i];
  //               $scene_letters = $scene_letters . substr( $sceneArr[$i], 0, 1);
  //           }
  //           //$post_title_url = $scene_letters . str_replace(" ", "-", strtolower($post->post_title));
  //           $post_link = home_url("/$scene_base_url/{$post->post_name}/");
  //       }
  //   }
  //   return $post_link;
  // }
  // add_filter('post_type_link', 'scene_post_type_permalink', 10, 2);

  /**
   * Sets up and flushes rewrite rules on plugin activation.
   *
   * This function is designed to be run on plugin activation. It ensures that the 'scene' custom post type and its associated
   * rewrite rules are registered before flushing WordPress's rewrite rules to prevent 404 errors on newly created post types.
   * The process involves three steps:
   * 1. Registering the 'scene' post type by calling `register_scene_post_type()`.
   * 2. Adding custom rewrite rules specific to the 'scene' post type through `scene_rewrite_rules()`.
   * 3. Flushing the rewrite rules to apply changes using `flush_rewrite_rules()`.
   * This function should be hooked to the `register_activation_hook` to properly initialize the post type and rewrite rules.
   *
   * @return void
   */
  // Flush rewrite rules on activation

  //TODO ensures the custom post type and rewrite rules are registered and applied, which affects URL handling in WordPress.
  // function scene_rewrite_flush() {
  //   register_scene_post_type();
  //   scene_rewrite_rules();
  //   flush_rewrite_rules();
  // }
  // register_activation_hook(__FILE__, 'scene_rewrite_flush');


  /**
   * Retrieves arrays of scene information and photos for a specified post.
   *
   * This function collects and returns two arrays containing scene information and photos respectively. It searches 
   * through post meta to find up to 6 sets of scene information and photos, each potentially containing text and a URL.
   * The function performs a single query per metadata type (`scene_info` and `scene_photo`) for efficiency, and checks 
   * for the existence of both text and URL before adding the data to the return array. This method optimizes memory usage 
   * by avoiding unnecessary data pushing to the array if the data set is incomplete.
   *
   * @param int $post_id The ID of the post from which to gather scene info and photos.
   * @return array An array of two arrays: one for scene info and one for photos, each containing the respective text and URL.
   */
  //get scene photo and info arrays
  function get_scene_info_photo($post_id){
    $scene_info_photo_arr = [[], []];
    for($i = 1; $i <= 6; $i++){
      //ASSUMING BOTH SCENE INFO AND PHOTO HAVE BOTH TEXT AND LINK
      //instead of doing individual queries is it faster to just query once of everything then search the query for the required links
      $scene_info = get_post_meta($post_id, "scene_info".$i);
      $scene_photo = get_post_meta($post_id, "scene_photo".$i);

      //instead of pushing to array(takes up more memory) can just create the dropdown in the if statement
      if($scene_info[0]['scene_info_text'.$i] && $scene_info[0]['scene_info_url'.$i]){
          array_push($scene_info_photo_arr[0], $scene_info[0]);
      }
      if($scene_photo[0]['scene_photo_text'.$i] && $scene_photo[0]['scene_photo_url'.$i]){
          array_push($scene_info_photo_arr[1], $scene_photo[0]);
      }
    }
    return $scene_info_photo_arr;
  }

  /**
   * Generates an HTML accordion section.
   *
   * This function outputs an HTML structure for an accordion section tailored to Bootstrap's collapse plugin. It constructs 
   * a single accordion item with dynamically generated IDs based on the provided title, which are used for the collapsible 
   * target references. The function checks if the provided data array is not empty and iterates over this array to create list 
   * items (`<li>`) with links (`<a>` tags) derived from the data array's elements. Each link displays text and references a URL 
   * both stored as key-value pairs in the array, expected to be indexed by a base key formed by a modified version of the title.
   *
   * @param string $title The title of the accordion section which is also used to create IDs for HTML elements.
   * @param array $dataArr An array of associative arrays, each containing keys formed by appending a suffix to the modified title.
   *                       These keys should map to the URL and text to be used for links within this accordion section.
   * @return void Outputs HTML Accordion directly.
   */
  //GENERATING ACCORDION SECTIONS 
  function generateAccordionSection($title, $dataArr){
    if(!empty($dataArr)){
        $modTitle = str_replace(' ', '_', strtolower($title));
        echo '<div class="accordion-item">';
        echo '<h2 class="accordions-header">';
        echo '<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse' . $modTitle . '"  aria-controls="collapse' . $modTitle . '">';
        echo '<div class="title_size">' . $title . '</div>';
        //echo $title;
        echo '</button>';
        echo '</h2>';
        echo '<div id="collapse' . $modTitle . '" class="accordion-collapse collapse" data-bs-parent="#sceneAccordions">';
        echo '<div class="accordion-body">';
        echo '<ul>';
        for ($i = 0; $i < count($dataArr); $i++) {
            echo '<li><a href="' . $dataArr[$i][$modTitle . '_url' . ($i + 1)] . '">' . $dataArr[$i][$modTitle . '_text' . ($i + 1)] . '</a></li>';
        }
        echo '</ul>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
  }

  /**
   * Generates an array of modal information from an SVG file.
   *
   * This function processes an SVG file to extract child elements of a specified element and 
   * generates an array of information related to these child elements, based on associated 
   * WordPress post metadata. It utilizes DOMDocument and DOMXPath to parse and query the SVG 
   * content and creates a structured array containing details for each child element.
   *
   * @param string $svg_url The URL of the SVG file to be processed.
   * @return array|null An array of associative arrays containing modal information for each 
   *                    child element, or null if the SVG file could not be processed.
   */
  function generateModalArray($svg_url){
    //Check if $svg_url contains anything, return null if nothing
    if($svg_url){
      // Find the path to the SVG file
      $relative_path = ltrim(parse_url($svg_url)['path'], "/");
      $full_path = ABSPATH . $relative_path;

      // Get the contents from the SVG file
      $svg_content = file_get_contents($full_path);

      // If the SVG content could not be loaded, terminate with an error message
      if(!$svg_content){
          die("Fail to load SVG file");
          return null;
      }
      // Load the SVG content into a DOMDocument
      $dom  = new DOMDocument();
      libxml_use_internal_errors(true);
      $dom->loadXML($svg_content);
      libxml_clear_errors();

      // Create a DOMXPath object for querying the DOMDocument
      $xpath = new DOMXPath($dom);

      // Find the element with ID "icons"
      $icons_element = $xpath->query('//*[@id="icons"]')->item(0);

      // If the element with ID "icons" is not found, terminate with an error message
      if($icons_element === null){
          die('Element with ID "icons" not found');
          return null;
      }

      // Get the child nodes of the "icons" element
      $child_elements = $icons_element->childNodes;

      // Initialize an array to hold the IDs of the child elements
      $child_ids = array();
      foreach($child_elements as $child){
        // Check if the child node is an element and has an "id" attribute
          if($child->nodeType === XML_ELEMENT_NODE && $child->hasAttribute('id')) {
              // Add the "id" attribute to the array
              $child_ids[] = $child->getAttribute('id');
          }
      }
      // Sort the IDs alphabetically
      asort($child_ids);
      /*
      json file structure:
      name:
      title:
      post-id:
      function: 
      modal:
      scene:
      external:
      */ 
      // Iterate over the sorted IDs and create an associative array for each one
      for ($i = 0; $i < count($child_ids); $i++){
        // Create a new WP_Query object for the current ID
        $query = new WP_Query(postQuery($child_ids[$i]));
        // Check if there are any posts for the current ID
        if($query->have_posts()){
            // Get the post ID and post object
            $child_post_id = $query->posts[0];
            $post = get_post($child_post_id); //this might not be needed

            // Get the icon type, title, and other meta information
            $icon_type = get_post_meta($child_post_id, "icon_function");
            $icon_title = get_post_meta($child_post_id, "post_title");
            $modal = "";
            $scene_url = "";
            $external_url = "";

            // Determine the URL based on the icon type or Modal
            if($icon_type[0] === "External URL"){
                $external_url = get_post_meta( $child_post_id, "icon_external_url");
            }
            if($icon_type[0] === "Scene"){
                $scene_id = get_post_meta( $child_post_id, "icon_scene_out");
                $scene_url = get_permalink($scene_id[0]);
            }
            //TODO
            if($icon_type[0] === "Modal"){
                $modal = "Modal TODO";
            }

            //Create an array using the information above
            $child_ids[$i] = ["name" => $child_ids[$i], 
                              "title" => $icon_title[0],
                              "post_id" => $child_post_id,
                              "icon_function" => $icon_type[0],
                              "modal" => $modal,
                              "scene" => $scene_url,
                              "external" => $external_url[0]
                            ];
        }
      }

      // Reset the global $post object after query
      wp_reset_postdata();
      return $child_ids;
    }
    return null;
  }

  /**
   * Constructs a query argument array for retrieving posts with a specific meta key value.
   *
   * This function generates an array of arguments tailored for a WordPress query. It targets 
   * any post type and filters posts based on a meta key `modal_icons` matching the provided 
   * icon name. The function ensures that only the IDs of the matching posts are retrieved.
   *
   * @param string $icon_name The value to be matched against the `modal_icons` meta key.
   * @return array The argument array to be used with a WordPress query.
   */
  function postQuery($icon_name){ //maybe add a field named 'modal_scene'
    $args = array(
        'post_type' => 'any', 
        'meta_query' => array(
            array(
                'key'     => 'modal_icons',
                'value'   => $icon_name,
                'compare' => '='
            )
        ),
        'fields' => 'ids' 
    );
    return $args;
  }




  function modal_helper($child_post_id, $child_ids, $child_id, $idx = 0){
          //get icon_type to check if modal
          
          $icon_type = get_post_meta($child_post_id, "icon_function");
          $icon_title = get_post_meta($child_post_id, "post_title");
          $modal = FALSE;
          $external_url =  '';
          $external_scene_id = '';
          $is_modal = get_post_meta($child_post_id,"post_type" );//[0]; //error here?
          $icon_order = get_post_meta($child_post_id,"modal_icon_order");
          //create array/map from child id to different attributes (ie hyperlinks)
          if($is_modal){
            if ($icon_type[0] === "Modal"){
              $modal = TRUE;
            } else if ($icon_type[0] === "External URL"){
              $external_url = get_post_meta( $child_post_id, "icon_external_url")[0];
            } else if ($icon_type[0] === "Scene"){
              $external_scene_id = get_post_meta( $child_post_id, "icon_scene_out");
              $external_url = get_permalink($external_scene_id[0]);

            } 
          $scene_id = get_post_meta($child_post_id, "modal_scene");
          $scenePost = get_post($scene_id[0]);
          $sceneName = get_post_meta($scenePost, "post_title");

          $section_name = get_post_meta($child_post_id, "icon_toc_section")[0];
          $child = $child_id;

          if (array_key_exists($child_id, $child_ids)){
            $child = ($child_id . $idx);
          }

          if ($icon_order[0] == null){
            $modal_icon_order = 1;
          } else {
            $modal_icon_order = intval($icon_order[0]);
          }

          $child_ids[$child] = [
              "title" => $icon_title[0],
              "modal_id" => $child_post_id,
              "external_url" => $external_url,
              "modal" => $modal,
              "scene" => $scenePost,
              "section_name" => $section_name, 
              "original_name" => $child_id,
              "modal_icon_order" => $modal_icon_order,
            ];
          } 
          return $child_ids;
          //add to array
          // $child_ids[$icon_title[0]] = $modal;
  }
  /**
   * Ideal behavior: create n-D array with element IDs and boolean indicating whether or not element has corresponding modal
   *
   *
   * @param $svg_url The URL of the SVG file to be processed.
   * @return array The argument array to be used with a WordPress query.
   */
  function get_modal_array($svg_url){
    // from original function - just preprocessing of the svg url, etc.
    if($svg_url){
      // Find the path to the SVG file
      $relative_path = ltrim(parse_url($svg_url)['path'], "/");
      $full_path = ABSPATH . $relative_path;

      // Get the contents from the SVG file
      $svg_content = file_get_contents($full_path);

      // If the SVG content could not be loaded, terminate with an error message
      if(!$svg_content){
          die("Fail to load SVG file");
          return null;
      }
      // Load the SVG content into a DOMDocument
      $dom  = new DOMDocument();
      libxml_use_internal_errors(true);
      $dom->loadXML($svg_content);
      libxml_clear_errors();

      // Create a DOMXPath object for querying the DOMDocument
      $xpath = new DOMXPath($dom);

      // Find the element with ID "icons"
      $icons_element = $xpath->query('//*[@id="icons"]')->item(0); //could 
      // $icons_id = get_post_meta($icons_element);

      // If the element with ID "icons" is not found, terminate with an error message
      if($icons_element === null){
          die('Element with ID "icons" not found');
          return null;
      }

      // Get the child nodes of the "icons" element
      $child_elements = $icons_element->childNodes;
      $child_ids = array();
      
      foreach($child_elements as $child){
        // $needtocontinue = FALSE;
        if($child->nodeType === XML_ELEMENT_NODE && $child->hasAttribute('id')) {
          // Add the "id" attribute to the array
          $child_id = $child->getAttribute('id');
          //this is a WP_query object for the current child ID
          $query = new WP_Query(postQuery($child_id)); //here, the query produces all the modals with that ID

          $child_post_id_list = $query->posts;
          if (count($child_post_id_list) > 1) {
            // $lastChild = $child_elements->item($child_elements->length - 1);
            // // Get the parent node of the last item
            // $parentNode = $lastChild->parentNode;
            $idx = 1;
            foreach ($child_post_id_list as $cid) {
                // Create a new DOM element for each post ID
                // Append the new element to the parent node of the current child
                // $parentNode = $icon->parentNode;
                // $child_elements->appendChild($newElement);
                $child_ids = modal_helper($cid, $child_ids, $child_id, $idx);
                $idx++;

                
            }
            continue;
        }
        if (!empty($query->posts)){
          $child_post_id = $query->posts[0]; //should not always be 0th index; want to loop through all the posts and select the one that is found on this scene
          $child_ids = modal_helper($child_post_id, $child_ids, $child_id);
        }
          
          
        }
      }
      //reset global $Post object
      wp_reset_postdata();
      return $child_ids;
    }
    return null;
  }

  //enqueue javascript for infographiq
  function enqueue_info_scripts() {
    wp_enqueue_script(
        'script-js',
        get_template_directory_uri() . '/assets/js/script.js',
        array(),
        null,
        array('strategy' => 'defer') 
    );
}
add_action('wp_enqueue_scripts', 'enqueue_info_scripts');

function enqueue_info_scripts2() {
  wp_enqueue_script(
      'index-js',
      get_template_directory_uri() . '/assets/js/index.js',
      array(),
      null,
      array('strategy' => 'defer') 
  );
}
add_action('wp_enqueue_scripts', 'enqueue_info_scripts2');



function enqueue_info_scripts3() {
  wp_enqueue_script(
      'plots-js',
      get_template_directory_uri() . '/assets/js/plots.js',
      array(),
      null,
      array('strategy' => 'defer') 
  );
}
add_action('wp_enqueue_scripts', 'enqueue_info_scripts3');


function enqueue_info_scripts4() {
  wp_enqueue_script(
      'plotly-timeseries-line',
      get_template_directory_uri() . '/assets/js/plotly-timeseries-line.js',
      array(),
      null,
      array('strategy' => 'defer') 
  );
}
add_action('wp_enqueue_scripts', 'enqueue_info_scripts4');

// function enqueue_plotly_script() {
//   wp_enqueue_script(
//       'plotly-js', // Handle name
//       'https://cdn.plot.ly/plotly-latest.min.js', // CDN URL
//       array(), 
//       null, 
//       true // Load in footer
//   );
// }
// add_action('wp_enqueue_scripts', 'enqueue_plotly_script');


?>


