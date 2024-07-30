<?php
/**
 * Template Name: instances.php
 * Template Post Type: instance
 * 
 * This template is designed to display the main content area for each instance within a WordPress theme.
 * It integrates the site header and footer and provides a central container that features an image and detailed text
 * components styled directly within the template. The key elements include:
 *
 * - **Header Inclusion**: Utilizes `get_header()` to embed the standard site-wide header.
 * - **Main Content Container**: A full-width container that aligns the content at the top of the page and
 *   includes both visual and textual elements to engage users:
 *     - An emblem image (logo) for Sanctuary Watch is displayed alongside the site title and a descriptive tagline,
 *       both formatted with specific styles for prominence and readability.
 *     - A detailed description under a styled heading that introduces the WebCRs platform, explaining its purpose
 *       and functionality in tracking ecosystem conditions through interactive tools.
 * - **Footer Inclusion**: Implements `get_footer()` to attach the standard site-wide footer.
 *
 * The content is primarily focused on delivering information through a clean and interactive layout, using inline styles
 * for specific design needs. This setup ensures that the theme maintains a coherent look while also providing specific
 * functionality and information layout tailored to the 'Sanctuary Watch' theme.
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<!-- Main container with Bootstrap styling for fluid layout -->
<div class="container-fluid main-container" style="margin-top: 0px;">
    <div class="image-center" style="padding-bottom: 20px;">
        <span>
            <?php 
                echo '<img width="10%" src="' . get_stylesheet_directory_uri() . '/assets/images/onms-logo-no-text-800.png" alt="Sanctuary Watch Navbar Emblem">';
            ?>
        </span>
        <span style="display: inline-block; text-align: left; vertical-align: middle;">
            <div style="color: #00467F; font-size: 2.7vw; font-weight: bold;">Sanctuary Watch</div>
            <div style="color: #008da8; font-size: 1.5vw; font-style: italic; font-weight: bold;">Web-Enabled Information for Sanctuary Management</div>
        </span>
    </div>
    <div id="webcrs---ecosystem-tracking-tools-for-condition-reporting" class="section level2">
        <h2 style="color: #024880;">WebCRs - Ecosystem Tracking Tools for Condition Reporting</h2>
        <p>THIS IS An INSTANCE.</p>
        <div>AAHHAHAHAH</div>
        
    </div>
    <?php
        $post_id = get_the_ID();
        //   $svg_url = get_post_meta($post_id, 'scene_infographic', true); 
        //   $child_ids = get_modal_array($svg_url);
        ?>
</div>
<script>
    let post_id =  <?php echo $post_id; ?>;

</script>
<?php
get_footer();
?>
