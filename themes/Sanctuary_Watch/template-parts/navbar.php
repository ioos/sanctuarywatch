<?php
/**
 * Navigation Bar Template
 *
 * This template file is responsible for generating a dynamic and responsive navigation bar for a WordPress theme,
 * specifically tailored to display links based on the 'scene' custom post type and its associated metadata. The navbar
 * facilitates easy navigation through various scenes and includes a fallback for general site navigation if no scenes
 * are available. It leverages Bootstrap's navbar component to ensure responsiveness and aesthetic integration. Key
 * functionalities include:
 * - **Dynamic Navbar Branding**: Depending on the presence of specific post metadata ('scene_location'), the navbar's
 *   brand logo adjusts dynamically. If 'scene_location' is present, it shows a text link styled as the navbar brand
 *   that leads to the 'overview' of the current scene. If not present, it defaults to displaying the site's logo along
 *   with the 'Sanctuary Watch' text.
 * - **Scene-Specific Navigation Links**:
 *   - Utilizes a custom WP_Query to fetch posts of type 'scene' that match the current 'scene_location'.
 *   - Posts are sorted by a custom field ('scene_order') to control the display order, allowing manual curation of
 *     link arrangement within the navbar.
 *   - Each post title is displayed as a link within the navbar, facilitating quick navigation to different scenes.
 *   - If no matching posts are found, a template part (typically a dropdown menu) is included as a fallback.
 * This navigation bar is crucial for user navigation, offering both adaptability and robust functionality to enhance
 * user experience and site usability.
 */

defined( 'ABSPATH' ) || exit;
?>

<nav class="navbar navbar-expand-lg bg-primary" data-bs-theme="dark">
	<div class="container-fluid">
        <?php
        //Get the post ID of the page and the scene location of the postID
        $postMeta = get_post_meta(get_the_ID());
        $sceneLocation = $postMeta['scene_location'][0];
        //Convert string into array with " " being the seperator
        $sceneArr = explode(' ', $sceneLocation);
        //add url and attach "webcr-" in front
        $scene_base_url = 'webcr-';
        // Concatenate the elements of the location array to form part of the URL, in lowercase
        for($i=0; $i < count($sceneArr)-1; $i++){
            $scene_base_url = $scene_base_url.strtolower($sceneArr[$i]);
        }
        // If sceneLocation is not empty, display a link with the constructed URL
        if($sceneLocation){
            echo "<a class='navbar-brand' href='/$scene_base_url/overview/'>CINMS</a>";
        }else {
            // If sceneLocation is empty, display a default logo and text
            echo '<a class="navbar-brand" href=""><img class="navbar-emblem" width="32p" src="' . get_stylesheet_directory_uri() . '/assets/images/onms-logo-no-text-800.png" alt="Sanctuary Watch Navbar Emblem"> Sanctuary Watch</a>';
        }
        ?>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarColor01">
            <ul class="navbar-nav me-auto">
                <?php 
                // Define arguments for a custom query to fetch posts with a specific 'scene_location'
                $args = array(
                    'post_type' => 'scene',
                    'post_status' => 'publish',
                    'meta_query' => array(
                        array(
                            'key' => 'scene_location',
                            'value' => $sceneLocation,
                            'compare' => '='
                        )
                    )
                );
                // Execute the query
                $query = new WP_Query($args);
                // Check if there are posts matching the query
                if ($query->have_posts()){
                    $post_titles = array();
                    // Iterate over each post and store the title, scene order, and ID
                    while($query->have_posts()) {
                        $query->the_post();
                        $scene_order = get_post_meta(get_the_ID(), 'scene_order');
                        if(get_the_title() !== 'Overview'){
                            $post_titles[] = [get_the_title(), $scene_order[0], get_the_ID()];
                        }
                    }
                    //reset the post daata
                    wp_reset_postdata();
                    // Custom function to sort posts by scene order and then alphabetically
                    function customCompare($a, $b) {
                        $result = $a[1] - $b[1];
                        if ($result==0) {
                            $result = strcmp($a[0], $b[0]);
                        }
                        return $result;
                    }
                    // Sort the posts using the custom comparison function
                    usort($post_titles, 'customCompare');
                    // Display navigation links for each post
                    foreach ($post_titles as $post_title){
                        echo "<li class='nav-item'><a class='nav-link' href='". esc_url(get_permalink($post_title[2])) ."'>$post_title[0]</a></li>";
                    }
                }else {
                    // If no posts found, include a default navbar dropdown
                    get_template_part( 'parts/navbar-dropdown' );
                }
                ?>
                <li class='nav-item'>
                    <a class='nav-link' href="https://marinebon.org/sanctuaries/" target="_blank">About</a>
                </li>
            </ul>
        </div>
	</div>
</nav>