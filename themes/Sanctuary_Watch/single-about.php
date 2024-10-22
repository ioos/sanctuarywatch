<?php

defined( 'ABSPATH' ) || exit;

// get_header();
if ( get_post_type() == 'about' ) {
    echo '<h1>About Page Template</h1>';
}

$args = array(
	'post_type'      => 'about',
	'posts_per_page' => 10,
);
$loop = new WP_Query($args);
while ( $loop->have_posts() ) {
	$loop->the_post();
	?>
	<div class="entry-content">
		<?php the_title(); ?>
		<?php the_content(); ?>
	</div>
	<?php
}

?>

<script>console.log("ON THE RIGHT PAGE!!!") </script>
<h1> About page </h1>


<?php
// get_footer();
?>