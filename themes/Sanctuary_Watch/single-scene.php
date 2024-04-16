<?php
defined( 'ABSPATH' ) || exit;

get_header();

//ALL CURRENTLY ASSUME THERE IS POSTMETA AND THERE IS ALL SUFFICIENT INFORMATION
//IMPLEMENT ERROR CHECKS LATER

$post_id = get_the_ID();

//array structure(triple nested arrays): arr = [[text1, url1],[text2, url2], ....]
$total_arr = get_scene_info_photo($post_id);
$scene_info_arr = $total_arr[0];
$scene_photo_arr = $total_arr[1];
?>
<div class="container-fluid main-container">
    <div>
        <h1 class="title toc-ignore"><?php echo get_the_title( $post_id ) ?></h1>
    </div>
    <p></p>
    <!-- Temporary Following bootstrap styling, will move to css file later -->
    <div style="display: flex">
        <!--accordian -->
        <?php if((!empty($scene_info_arr) || !empty($scene_photo_arr)) && $scene_title != 'Overview') : ?> 
            <div style="margin-top: 10px;margin-bottom: 10px; margin-right: 10px; flex: 1;">
                <div class="accordion" id="sceneAccordions">
                    <?php
                        generateAccordionSection('Scene Info', $scene_info_arr);
                        generateAccordionSection('Scene Photo', $scene_photo_arr);
                    ?>
                </div>
            </div>
            <div style="margin: 10px; font-style: italic; flex: 20; ">
        <?php else: ?>
            <div style="flex: 20; ">
        <?php endif; 
            echo get_post_meta($post_id, "scene_tagline")[0];
        ?>
            </div>
    </div>
    <div class="svg">
    <?php
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