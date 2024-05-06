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
   * Enqueues Bootstrap's JavaScript library with dependency management.
   *
   * This function registers and enqueues the Bootstrap JavaScript library from a CDN. It specifies jQuery as a dependency,
   * meaning jQuery will be loaded before the Bootstrap JavaScript. The script is added to the footer of the HTML document 
   * and is set to defer loading until after the HTML parsing has completed.
   *
   * @return void
   */
  function enqueue_bootstrap_scripts() {
    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js', array('jquery'), null, array('strategy' => 'defer,'));
  }
  add_action('wp_enqueue_scripts', 'enqueue_bootstrap_scripts');

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
  add_action('init', 'register_scene_post_type' );

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
  function scene_rewrite_rules() {
    add_rewrite_rule(
        '^([^/]+)/([^/]+)/?$',
        'index.php?post_type=scene&scene_location=$matches[1]&name=$matches[2]',
        'top'
    );
  }
  add_action('init', 'scene_rewrite_rules');

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
  function scene_post_type_permalink($post_link, $post) {
    if (is_object($post) && $post->post_type == 'scene') {
        $postMeta = get_post_meta($post->ID);
        $sceneLocation = $postMeta['scene_location'][0];
        if ($sceneLocation) {
            $scene_base_url = 'webcr-';
            $sceneArr = explode(' ', strtolower($sceneLocation));
            $scene_letters = '';
            for ($i = 0; $i < count($sceneArr) - 1; $i++) {
                $scene_base_url = $scene_base_url . $sceneArr[$i];
                $scene_letters = $scene_letters . substr( $sceneArr[$i], 0, 1);
            }
            //$post_title_url = $scene_letters . str_replace(" ", "-", strtolower($post->post_title));
            $post_link = home_url("/$scene_base_url/{$post->post_name}/");
        }
    }
    return $post_link;
  }
  add_filter('post_type_link', 'scene_post_type_permalink', 10, 2);

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
  function scene_rewrite_flush() {
    register_scene_post_type();
    scene_rewrite_rules();
    flush_rewrite_rules();
  }
  register_activation_hook(__FILE__, 'scene_rewrite_flush');


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
?>
