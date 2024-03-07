<?php
  function files() {
    wp_enqueue_style( 'style', get_stylesheet_uri() );
  } 
  add_action( 'wp_enqueue_scripts', 'files' );

  function enqueue_bootstrap_scripts() {
    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js', array('jquery'), null, array('strategy' => 'defer,'));
  }
  add_action('wp_enqueue_scripts', 'enqueue_bootstrap_scripts');

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

  // Add custom rewrite rules
  function scene_rewrite_rules() {
    add_rewrite_rule(
        '^([^/]+)/([^/]+)/?$',
        'index.php?post_type=scene&scene_location=$matches[1]&name=$matches[2]',
        'top'
    );
  }
  add_action('init', 'scene_rewrite_rules');

  //changing the scene url
  function scene_post_type_permalink($post_link, $post) {
    if (is_object($post) && $post->post_type == 'scene') {
        $postMeta = get_post_meta($post->ID);
        $sceneLocation = $postMeta['scene_location'][0];
        if ($sceneLocation) {
            $scene_base_url = 'webcr-';
            $sceneArr = explode(' ', strtolower($sceneLocation));
            for ($i = 0; $i < count($sceneArr) - 1; $i++) {
                $scene_base_url = $scene_base_url . $sceneArr[$i];
            }
            $post_link = home_url("/$scene_base_url/{$post->post_name}/");
        }
    }
    return $post_link;
  }
  add_filter('post_type_link', 'scene_post_type_permalink', 10, 2);

  // Flush rewrite rules on activation
  function scene_rewrite_flush() {
    register_scene_post_type();
    scene_rewrite_rules();
    flush_rewrite_rules();
  }
  register_activation_hook(__FILE__, 'scene_rewrite_flush');
?>
