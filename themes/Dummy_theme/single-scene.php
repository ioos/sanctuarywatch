<div>u made it</div>

<!-- <?php get_header(); ?> -->
<?php
if (have_posts()) :
    while (have_posts()) : the_post();
        the_title('<h1>', '</h1>');

        // the_content();
    endwhile;
else :
    echo '<p>Womp Womp</p>';
endif;
?>

