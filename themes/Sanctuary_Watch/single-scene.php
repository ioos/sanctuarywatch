<?php
/**
 * Detailed Scene Page Template
 *
 * This template file is designed for displaying detailed pages of the 'scene' post type within a WordPress theme. 
 * It dynamically loads and presents various content elements such as scene information, photos, and infographics 
 * based on associated post metadata. The template handles the presentation logic including conditional rendering 
 * of content sections and integrates Bootstrap components for styling. Key functionalities include:
 *
 * - **Header and Footer**: Incorporates the common header and footer across the site using `get_header()` and `get_footer()`.
 * - **Post Identification**: Retrieves the current post ID with `get_the_ID()` to fetch associated metadata.
 * - **Metadata Retrieval**: Uses a custom function `get_scene_info_photo()` to obtain arrays of text and URLs for 
 *   scene information and photos, which are then passed to another function for rendering as accordion components.
 * - **Conditional Layouts**: Depending on the availability of scene information or photos, the layout adjusts to 
 *   display these elements appropriately. If both information types are available, they are displayed side-by-side;
 *   otherwise, the tagline or main content takes more visual precedence.
 * - **Dynamic Content Rendering**: Content sections for scene information and photos are rendered using the 
 *   `generateAccordionSection()` function which creates Bootstrap accordions dynamically. Additionally, any 
 *   available infographic is displayed as an image.
 * - **Styling and Structure**: Inline styles are used temporarily for layout control, intended to be moved to 
 *   an external CSS file for better maintainability and performance.
 *
 * This template is critical for providing a detailed and interactive view of individual scenes, facilitating 
 * better user engagement and content discovery through well-structured and dynamic data presentation.
 */

defined( 'ABSPATH' ) || exit;

get_header();

//ALL CURRENTLY ASSUME THERE IS POSTMETA AND THERE IS ALL SUFFICIENT INFORMATION
//IMPLEMENT ERROR CHECKS LATER


// Retrieves the ID of the current post
$post_id = get_the_ID();

// Fetches scene information and photos based on the current post ID
//array structure(triple nested arrays): arr = [[text1, url1],[text2, url2], ....]
$total_arr = get_scene_info_photo($post_id);
$scene_info_arr = $total_arr[0];
$scene_photo_arr = $total_arr[1];
?>
<div class="container-fluid main-container">
    <div>
        <!-- Displays the title of the current post -->
        <h1 class="title toc-ignore"><?php echo get_the_title( $post_id ) ?></h1>
    </div>
    <p></p>
    <!-- Temporary Following bootstrap styling, will move to css file later -->
    <div style="display: flex">
        <!-- Accordion section begins -->
        <?php 
            // If the scene info array is not empty, or if its not empty and the scene title is not overview, create the accordion
            if((!empty($scene_info_arr) || !empty($scene_photo_arr)) && $scene_title != 'Overview') : 
        ?> 
            <div style="margin-top: 10px;margin-bottom: 10px; margin-right: 10px; flex: 1;">
                <div class="accordion" id="sceneAccordions">
                    <?php
                    // Function calls to generate accordion sections for scene information and photos
                        generateAccordionSection('Scene Info', $scene_info_arr);
                        generateAccordionSection('Scene Photo', $scene_photo_arr);
                    ?>
                </div>
            </div>
            <div style="margin: 10px; font-style: italic; flex: 20; ">
        <?php else: ?>
            <!-- If no accordion is needed, adjust the container flex sizing -->
            <div style="flex: 20; ">
        <?php endif; 
        // Displays the tagline fetched from post meta
            echo get_post_meta($post_id, "scene_tagline")[0];
        ?>
            </div>
    </div>
    <div class="svg">
    <?php
        // Fetches and displays an SVG graphic if available
        $svg_url = get_post_meta($post_id, 'scene_infographic', true);
        if (!empty($svg_url)) {
            echo '<img src="' . esc_url($svg_url) . '" alt="Description of SVG">';
        }
        /*
        if (!empty($svg_url)) {
            echo '<object type="image/svg+xml" data="' . esc_url($svg_url) . '">Your browser does not support SVGs</object>';
        }
        */ 
    ?>
    </div>
</div>
<?php
get_footer();
?>