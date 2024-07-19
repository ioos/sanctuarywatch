<?php
/**
 * Primary Page Template for Sanctuary Watch
 *
 * This template is designed to display the main content area of the 'Sanctuary Watch' page within a WordPress theme.
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
        <p>The web-enabled Condition Reporting (WebCR) platform pairs artwork
            with information to make it easy to explore and track how ecosystem
            conditions are changing at a sanctuary. Select a sanctuary below to
            start exploring that sanctuary’s ecosystem. Navigate by clicking on
            icons representing major habitats, species of interest, climate and
            ocean drivers, and human connections. Interactive icons and silhouettes
            are linked to status and trend data, images, web stories and other
            related content. The goal of WebCRs are to help us keep our finger on
            the pulse of these dynamic ecosystems and to help us to better
            understand and manage our sanctuaries together. Tiles for other
            sanctuaries will be added below as those tools become available.</p>
        <div>AAHHAHAHAH</div>
        
    </div>
</div>
<?php
get_footer();
?>
