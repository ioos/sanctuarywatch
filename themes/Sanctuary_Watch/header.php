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

<?php get_template_part( 'template-parts/navbar' ); ?>