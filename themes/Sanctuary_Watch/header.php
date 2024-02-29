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
				<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/IOOS_Emblem_Tertiary_B_RGB.png" alt="IOOS EMBLEM LINK">
			</a>
		</div>

		<div id="ioos-breadcrumb">
			<!--First lets display the two links that will always show up-->

			<span id="header-margin">
				<?php
					$postMeta = get_post_meta(get_the_ID());
					$sceneLocation = $postMeta['scene_location'][0];
					$sceneArr = explode(' ', $sceneLocation);
					if (!empty($sceneLocation)){
						for($i = 0; $i < count($sceneArr)-1; $i++){
							$scene_loc_webcr = $scene_loc_webcr.$sceneArr[$i].' ';
						}
						echo '<a href="https://ioos.us" target="_blank">IOOS</a>';
						echo '<p> > </p>';
						echo '<a href="https://sanctuarywatch.ioos.us">Sanctuary Watch</a>';
						echo '<p> > </p>';
						echo '<a href="google.com">'. esc_html($scene_loc_webcr.'WebCR') .'</a>';
					}
				?>
			</span>
		</div>

		<nav class="navbar navbar-expand-lg bg-primary" data-bs-theme="dark">
			<div class="container-fluid">
					<?php
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
								//TODO: This block doesnt do the dropdown behavior, fix later
								//TODO: WebCrs Dropdown, need to grab all locations and put in dropdown
								//TEMP SOLUTION - HARDCODE LOCATION
								//TODO: NEED TO FIND WAY TO DYNAMICALLY QUERY DATABASE FOR LOCATION
								echo '<li class="nav-item dropdown">
										<a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">WebCRs</a>
										<div class="dropdown-menu">
											<a class="dropdown-item" href="/webcr-channelislands/overview/">Channel Islands</a>
											<a class="dropdown-item" href="/webcr-floridakeys/overview/">Florida Keys</a>
											<a class="dropdown-item" href="/webcr-olympiccoast/overview/">Olympic Coast</a>
										</div>
									</li>';
								echo '<li class="nav-item dropdown">
										<a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Conservation Issues</a>
										<div class="dropdown-menu">
											<a class="dropdown-item" href="https://sanctsound.ioos.us">Sound</a>
										</div>
									</li>';
							}
							?>
							<li class='nav-item'>
								<a class='nav-link' href="https://marinebon.org/sanctuaries/" target="_blank">About</a>
							</li>
						</ul>
					</div>
			</div>
		</nav>