<?php

defined( 'ABSPATH' ) || exit;

get_header();

// wp_reset_postdata();
	$abt_post_id = get_the_ID();
	$about_tagline = get_post_meta($abt_post_id, 'about_tagline', true);
	$about_contact_info	 = get_post_meta($abt_post_id, 'about_contact_info', true);
	$about_code = get_post_meta($abt_post_id, 'about_code', true);
	$about_partners = get_post_meta($abt_post_id, 'about_partners', true);
	$about_people = get_post_meta($abt_post_id, 'about_people', true);


	?>

<div class="page-header">
    <h2>About Sanctuary Watch</h2>
    <div class="tagline-content">
        <?php echo $about_tagline; ?>
    </div>
</div>

<div class="about-container">
    <!-- <?php if (!empty($about_contact_info)): ?> -->
    <div class="about-card">
        <h2>Contact Information</h2>
        <div class="card-content">
            <?php echo $about_contact_info; ?>
        </div>
    </div>
    <!-- <?php endif; ?> -->

    <!-- <?php if (!empty($about_code)): ?> -->
    <div class="about-card">
        <h2>Code</h2>
        <div class="card-content">
            <?php echo $about_code; ?>
        </div>
    </div>
    <!-- <?php endif; ?> -->

    <!-- <?php if (!empty($about_partners)): ?> -->
    <div class="about-card">
        <h2>Partners</h2>
        <div class="card-content">
            <?php echo $about_partners; ?>
        </div>
    </div>
    <!-- <?php endif; ?> -->

    <!-- <?php if (!empty($about_people)): ?> -->
    <div class="about-card">
        <h2>People</h2>
        <div class="card-content">
            <?php echo $about_people; ?>
        </div>
    </div>
    <!-- <?php endif; ?> -->
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

<?php  get_footer(); ?>