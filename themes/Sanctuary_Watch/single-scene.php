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
    <?php
        //GENERATING ACCORDION SECTIONS 
        function generateAccordionSection($title, $dataArr){
            if(!empty($dataArr)){
                $modTitle = str_replace(' ', '_', strtolower($title));
                echo '<div class="accordion-item">';
                echo '<h2 class="accordion-header">';
                echo '<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse' . $modTitle . '"  aria-controls="collapse' . $modTitle . '">';
                echo $title;
                echo '</button>';
                echo '</h2>';
                echo '<div id="collapse' . $modTitle . '" class="accordion-collapse collapse" data-bs-parent="#sceneAccordions">';
                echo '<div class="accordion-body">';
                echo '<ul>';
                for ($i = 0; $i < count($dataArr); $i++) {
                    echo '<li><a href="' . $dataArr[$i][$modTitle . '_url' . ($i + 1)] . '">' . $dataArr[$i][$modTitle . '_text' . ($i + 1)] . '</a></li>';
                }
                echo '</ul>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
        }
    ?>
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
    <?php
        for ($x = 0; $x < 50; $x++){
            echo "<br>";
        }
    ?>
</div>
<?php
get_footer();
?>