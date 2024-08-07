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
        <div class="navbar-wrapper">
            <?php
            $postMeta = get_post_meta(get_the_ID());
            $sceneLocation = $postMeta['scene_location'][0];
            $sceneArr = explode(' ', $sceneLocation);
            $scene_base_url = 'webcr-';
            $nameMeta = get_post($sceneLocation);
            $post = $nameMeta->post_title;
            // echo $nameMeta;
            // $name = get_post_title($nameMeta);
            for($i=0; $i < count($sceneArr)-1; $i++){
                $scene_base_url = $scene_base_url.strtolower($sceneArr[$i]);
                echo $scene_base_url;
            }
            if($sceneLocation){
                echo "<a class='navbar-brand' href='/$nameMeta->instance_slug/'>$post</a>";
            }else {
                echo '<a class="navbar-brand" href=""><img class="navbar-emblem" width="32p" src="' . get_stylesheet_directory_uri() . '/assets/images/onms-logo-no-text-800.png" alt="Sanctuary Watch Navbar Emblem"> Sanctuary Watch</a>';
            }
            ?>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarColor01">
                <ul class="navbar-nav">
                    <?php 
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
                    $query = new WP_Query($args);
                    if ($query->have_posts()){
                        $post_titles = array();
                        while($query->have_posts()) {
                            $query->the_post();
                            // echo get_post_meta(get_the_ID());
                            // echo get_the_ID();
                            $scene_loc = get_post_meta(get_the_ID(), 'scene_location')[0];
                            // echo $scene_loc;
                            $inst_overview_scene = get_post_meta($scene_loc, 'instance_overview_scene')[0];

                            $scene_order = get_post_meta(get_the_ID(), 'scene_order');
                            if(get_the_ID() != $inst_overview_scene){
                                $post_titles[] = [get_the_title(), $scene_order[0], get_the_ID()];
                            }
                        }
                        wp_reset_postdata();
                        function customCompare($a, $b) {
                            $result = $a[1] - $b[1];
                            if ($result==0) {
                                $result = strcmp($a[0], $b[0]);
                            }
                            return $result;
                        }
                        usort($post_titles, 'customCompare');
                        foreach ($post_titles as $post_title){
                            echo "<li class='nav-item'><a class='nav-link' href='". esc_url(get_permalink($post_title[2])) ."'>$post_title[0]</a></li>";
                        }
                    }else {
                        get_template_part( 'parts/navbar-dropdown' );
                    }
                    ?>
                    <li class='nav-item'>
                        <a class='nav-link' href="https://marinebon.org/sanctuaries/" target="_blank">About</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

