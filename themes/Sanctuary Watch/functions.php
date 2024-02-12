<?
/*
add_action( 'wp_enqueue_scripts', 'parent_enqueue_styles');
function parent_enqueue_styles() {
    wp_enqueue_style('parent-style', get_template_directory_uri().'/style.css');
}

function sanctuary_watch_enqueue_styles() {
    $parent_style = 'developry-lite';
    wp_enqueue_style( $parent_style, get_template_directory_uri(  ).'./style.css');
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri(  ).'./style.css', 
    array($parent_style), wp_get_theme()->get('Version'));
}
add_action( 'wp_enqueue_scripts', 'sanctuary_watch_enqueue_styles');
*/
?>
