<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-HQV3WX3V2W"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-HQV3WX3V2W');
</script>
<?php

defined( 'ABSPATH' ) || exit;

get_header();

// wp_reset_postdata();
	$abt_post_id = get_the_ID();
    $numberAboutBoxes = get_post_meta($abt_post_id, 'numberAboutBoxes', true);
    $about_post_title = get_post_meta($abt_post_id, 'post_title', true);
    $about_central_array = get_post_meta($abt_post_id, 'centralAbout', true);
    $about_central_main = $about_central_array['aboutMain'];
    $about_central_details= $about_central_array['aboutDetail'];
	?>

<div class="page-header">
    <h2><?php echo $about_central_main; ?></h2>
    <div class="tagline-content">
        <?php echo $about_central_details; ?>
    </div>
</div>

<!-- Loop through all the possible aboutBoxes and populate them dynamically if there is content in any of them content. -->
<!-- Number of boxes needed is grabbed from the database.-->
<div class="about-container">
    <?php
    for ($i = 1; $i <= $numberAboutBoxes; $i++) {
        $aboutBox_array = get_post_meta($abt_post_id, "aboutBox$i", true);
        $aboutBox_title = $aboutBox_array["aboutBoxTitle$i"] ?? '';
        $aboutBox_main = $aboutBox_array["aboutBoxMain$i"] ?? '';
        $aboutBox_details = $aboutBox_array["aboutBoxDetail$i"] ?? '';

        // If aboutBox_title, aboutBox_main, or aboutBox_details is not empty, then go ahead and create the card. 
        if (!empty($aboutBox_title) || !empty($aboutBox_main) || !empty($aboutBox_details)) {
            ?>
            <div class="about-card">
                <h2><?php echo esc_html($aboutBox_title); ?></h2>
                <div class="card-content">
                    <?php echo ($aboutBox_main); ?>
                    <?php if (!empty($aboutBox_details)): ?>
                        <details>
                            <summary>Learn More...</summary>
                            <?php echo ($aboutBox_details); ?>
                        </details>
                    <?php endif; ?>
                </div>
            </div>
            <?php
        }
    }
    ?>
</div>


<style>
.page-header {
    text-align: center;
    max-width: 800px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.page-header h2 {
    color: #333;
    font-size: 2rem;
    margin-bottom: 1rem;
}

.tagline-content {
    color: #666;
    line-height: 1.6;
    font-size: 1.1rem;
    margin-bottom: 1rem;
}

.about-container {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 2rem;
    padding: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

.about-card {
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
    transition: transform 0.2s ease;
}

.about-card:hover {
    transform: translateY(-5px);
}

.about-card h2 {
    color: #333;
    margin-bottom: 1rem;
    font-size: 1.5rem;
    border-bottom: 2px solid #eee;
    padding-bottom: 0.5rem;
}

.card-content {
    color: #666;
    line-height: 1.6;
}

@media (max-width: 768px) {
    .about-container {
        grid-template-columns: 1fr;
        padding: 1rem;
    }
    
    .page-header {
        margin: 1rem auto;
    }
    
    .tagline-content {
        margin-bottom: 2rem;
    }
}
</style>

<!-- <?php  get_footer(); ?> -->