<?php
defined( 'ABSPATH' ) || exit;

get_header();
?>

<div class="container-fluid main-container">
    <?php 
        //ALL CURRENTLY ASSUME THERE IS POSTMETA AND THERE IS ALL SUFFICIENT INFORMATION
        //IMPLEMENT ERROR CHECKS LATER

        $post_id = get_the_ID();
        $scene_title = get_the_title($post_id);
        $scene_tagline = get_post_meta($post_id, "scene_tagline")[0];
        //$post_meta = get_post_meta($post_id);

        //may remove later 
        //current array structure? (triple nested arrays): arr = [[text1, url1],[text2, url2], ....]
        $scene_info_arr = array();
        $scene_photo_arr = array();

        for($i = 1; $i <= 6; $i++){
            //ASSUMING BOTH SCENE INFO AND PHOTO HAVE BOTH TEXT AND LINK
            //instead of doing individual queries is it faster to just query once of everything then search the query for the required links
            $scene_info = get_post_meta($post_id, "scene_info".$i);
            $scene_photo = get_post_meta($post_id, "scene_photo".$i);

            //instead of pushing to array(takes up more memory) can just create the dropdown in the if statement
            if($scene_info[0]['scene_info_text'.$i] && $scene_info[0]['scene_info_url'.$i]){
                array_push($scene_info_arr, $scene_info[0]);
            }
            if($scene_photo[0]['scene_photo_text'.$i] && $scene_photo[0]['scene_photo_url'.$i]){
                array_push($scene_photo_arr, $scene_photo[0]);
            }
        }

        echo '<div>
                <h1 class="title toc-ignore">'. $scene_title .'</h1>
            </div>';
        echo '<p></p>';
    ?>
    <!-- Temporary Following bootstrap styling, will move to css file later -->
    <div style="display: flex">
        <!-- TODO accordian -->
        <div style="margin-top: 10px;margin-bottom: 10px; margin-right: 10px; flex: 1;">
            <!-- make accordion only if there is text&url pair? probably yea -->
            <!-- hardcoding individual categories, assume that only info and photos exist -->
            <?php  //TODO NO DROPDOWNS WORKING AUGHHHHHHH
            ?>
            <div class="accordion" id="accordionExample">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Scene Info
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <ul>
                                <?php 
                                    for ($i = 0; $i < count($scene_info_arr); $i++) {
                                        echo '<li><a href="' . $scene_info_arr[$i]['scene_info_url' . ($i + 1)] . '">' . $scene_info_arr[$i]['scene_info_text' . ($i + 1)] . '</a></li>';
                                    }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            Scene Photos
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <ul>
                                <?php 
                                    for ($i = 0; $i < count($scene_photo_arr); $i++) {
                                        echo '<li><a href="' . $scene_photo_arr[$i]['scene_photo_url' . ($i + 1)] . '">' . $scene_photo_arr[$i]['scene_photo_text' . ($i + 1)] . '</a></li>';
                                    }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style="margin: 10px; font-style: italic; flex: 20; ">
            <?php echo $scene_tagline; ?>
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