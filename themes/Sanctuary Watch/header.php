	<?php
	defined( 'ABSPATH' ) || exit;
	?>
	<!doctype html>
	<html <?php language_attributes();?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		<link rel="profile" href="http://gmpg.org/xfn/11" />
		<?php wp_head(); ?>
		<?php if ( get_header_textcolor() ) : ?>
		<style type="text/css">
			#header,
			#header a { 
				color: #<?php echo esc_attr( get_header_textcolor() ); ?>;
			}
		</style>
		<?php endif; ?>
	</head>
	<body <?php echo esc_html( body_class() ); ?>>
		<?php wp_body_open(); ?>

		<div id="top-bar">
			<a href="https://ioos.us" target="_blank">
				<img src="<?php echo get_stylesheet_directory_uri(); ?>/IOOS_Emblem_Tertiary_B_RGB.png" alt="IOOS EMBLEM LINK">
			</a>
		</div>

		<div id="ioos-breadcrumb">
			<!--First lets display the two links that will always show up-->

			<span id="header-margin">
				<?php
					$postMeta = get_post_meta(get_the_ID());
					$sceneLocation = $postMeta['scene_location'][0];
					if (!empty($sceneLocation)){
						echo '<a href="https://ioos.us" target="_blank">IOOS</a>';
						echo '<p> > </p>';
						echo '<a href="https://sanctuarywatch.ioos.us">Sanctuary Watch</a>';
						echo '<p> > </p>';
						echo '<a href="google.com">'. esc_html($sceneLocation) .'</a>';
					}
				?>
			</span>
		</div>

		<nav class="navbar navbar-expand-lg bg-primary" data-bs-theme="dark">
			<div class="container-fluid">
					<?php
					$scene_base_url = 'webcr-';
					$sceneArr = explode(' ', strtolower($sceneLocation));
					for($i=0; $i < count($sceneArr)-1; $i++){
						$scene_base_url = $scene_base_url.$sceneArr[$i];
					}
					echo "<a class='navbar-brand' href='/$scene_base_url/'>CINMS</a>";
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
										$post_titles[] = [get_the_title(), $scene_order[0]];
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
									$post_name = strtolower(str_replace(' ', '-', $post_title[0]));
									echo "<li class='nav-item'><a class='nav-link' href='/$scene_base_url/$post_name/'>$post_title[0]</a></li>";
								}


							}else {
								echo 'No Scenes Found';
							}
							?>
							<li class='nav-item'>
								<a class='nav-link' href="https://marinebon.org/sanctuaries/" target="_blank">About</a>
							</li>
						</ul>
					</div>
			</div>
		</nav>

		<?php if ( empty( $theme_options['featured_image'] ) || 'show' !== $theme_options['featured_image'] ) : ?>
			<p class="p-4"></p><!-- fixed header fix -->
		<?php endif; ?>