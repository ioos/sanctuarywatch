<?php

defined( 'ABSPATH' ) || exit;
?>

<nav class="navbar navbar-expand-lg bg-primary" data-bs-theme="dark">
	<div class="container-fluid">
        <?php
        $postMeta = get_post_meta(get_the_ID());
        $sceneLocation = $postMeta['scene_location'][0];
        $sceneArr = explode(' ', $sceneLocation);
        $scene_base_url = 'webcr-';
        for($i=0; $i < count($sceneArr)-1; $i++){
            $scene_base_url = $scene_base_url.strtolower($sceneArr[$i]);
        }
        if($sceneLocation){
            echo "<a class='navbar-brand' href='/$scene_base_url/overview/'>CINMS</a>";
        }else {
            echo '<a class="navbar-brand" href=""><img class="navbar-emblem" width="32p" src="' . get_stylesheet_directory_uri() . '/assets/images/onms-logo-no-text-800.png" alt="Sanctuary Watch Navbar Emblem"> Sanctuary Watch</a>';
        }
        ?>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarColor01">
            <ul class="navbar-nav me-auto">
                <?php 
                //custom query for scene_location
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
                        $scene_order = get_post_meta(get_the_ID(), 'scene_order');
                        if(get_the_title() !== 'Overview'){
                            $post_titles[] = [get_the_title(), $scene_order[0], get_the_ID()];
                        }
                    }
                    //reset the post daata
                    wp_reset_postdata();
                    //custom comparison and sort where the scene order value is compared first than alphabetically
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
</nav>