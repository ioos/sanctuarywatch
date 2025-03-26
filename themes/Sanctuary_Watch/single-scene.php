
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

defined( 'ABSPATH' ) || exit; ?>

<?php
get_header();
?>

<?php
//ALL CURRENTLY ASSUME THERE IS THE CORRECT POSTMETA DATA AND THERE ALL SUFFICIENT INFORMATION EXISTS
//IMPLEMENT ERROR CHECKS LATER
// Retrieves the ID of the current post
$post_id = get_the_ID();
$scene_url = get_post_meta($post_id, 'scene_infographic');

$instance = get_post_meta($post_id, 'scene_location', true);
$instance_slug = get_post_meta($instance, 'instance_slug', true);
$overview = get_post_meta($instance, 'instance_overview_scene', true);

?>

<script>
//   document.addEventListener('DOMContentLoaded', function() {
//     const postId = <?php echo json_encode($post_id); ?>;
//     const instanceSlug = <?php echo json_encode($instance_slug); ?>;
//     const overviewSceneId = <?php echo json_encode($overview); ?>;
</script>

<body      >
  <!-- for the mobile image stuff -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700&display=swap" rel="stylesheet">

  <div class="modal" id="mobileModal" style="z-index: 9999; background-color: rgba(0,0,0,0.8);">
  <div class="modal-dialog modal-lg" style="z-index: 9999;margin-top: 60%;max-width: 88%;/* margin-right: 10%; */">
    <div class="modal-content" >

    <div class="modal-header">
        <h4 id = "modal-title1" class="modal-title"> Full Scene Image</h4>
        <button id="close1" type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <!-- Modal body.. -->
      </div>

      <!-- <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
      </div> -->

    </div>
  </div>
</div>

<!-- <body class="p-3 m-0 border-0 bd-example m-0 border-0"> -->
<div class="modal" id="myModal" style="z-index: 9999; background-color: rgba(0,0,0,0.8);">
  <div class="modal-dialog modal-lg" style="z-index: 9999; margin: 10% auto;   ">
    <div class="modal-content" aria-labelledby="modal-title">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 id = "modal-title" class="modal-title"></h4>
        <button id="close" type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <div class="row">
        <div id="tagline-container"  >
            
            </div>
          <div id="accordion-container"  >
        
          </div>
          <!-- <div id="tagline-container"  >
            
          </div> -->
        </div>
      </div>

      <!-- images go here -->
      <ul class="nav nav-tabs" id="myTab" role="tablist" style="margin-left: 1%">
          <!-- <li class="nav-item" role="presentation">
            <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">Home</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">Profile</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact-tab-pane" type="button" role="tab" aria-controls="contact-tab-pane" aria-selected="false">Contact</button>
          </li> -->
        
      </ul>

      <div class="tab-content" id="myTabContent" style="margin-top: 2%; margin-left: 2%; margin-right: 2%">
          <!-- <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0"> -->
            <!-- hard coded -->
            
        <!-- hard coded -->
          <!-- </div> -->
          <!-- <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">ok</div>
          <div class="tab-pane fade" id="contact-tab-pane" role="tabpanel" aria-labelledby="contact-tab" tabindex="0">bruh </div>  -->
          <!-- <div class="tab-pane fade" id="disabled-tab-pane" role="tabpanel" aria-labelledby="disabled-tab" tabindex="0">tuff</div> -->
      </div>
      <!-- image stuff ends here -->

    </div>
  </div>
</div>


<div id="entire_thing" style="
 
  max-width: 1700px !important;
    margin: 0 auto;
    background: #f2f2f2;
    padding-bottom: 3%;
    margin-top: -20px;
    padding-top: 1%;

    
">  
<div id="title-container" style="margin-left: 9%" ></div>
<div id="mobile-view-image"></div>
<div class="container-fluid" id="scene-fluid">
  <div class="row" id="scene-row">
    <div class="col-md-10" style="margin-left: -4%;">
      <div id="svg1" class="responsive-image-container">
        <?php
          $svg_url = get_post_meta($post_id, 'scene_infographic', true); 
          $num_sections = get_post_meta($post_id, 'scene_section_number', true); 
          $scene_sections = [];
          for ($i = 1; $i <= $num_sections; $i++) {
              $curr = 'scene_section' . $i;
              $curr_section = get_post_meta($post_id, $curr, true); 
              $hov_color = 'scene_section_hover_color' . $i;
              $scene_title = 'scene_section_title' . $i;

              $scene_sections[$curr_section[$scene_title]] = $curr_section[$hov_color];
          }
          
          //a bunch of scene meta fields:
          $scene_default_hover_color = get_post_meta($post_id, 'scene_hover_color', true); 
          $scene_text_toggle = get_post_meta($post_id, 'scene_text_toggle', true); 
          $scene_toc_style = get_post_meta($post_id, 'scene_toc_style', true); 
          $scene_full_screen_button = get_post_meta($post_id, 'scene_full_screen_button', true); 
          $scene_same_hover_color_sections	= get_post_meta($post_id, 'scene_same_hover_color_sections', true); 

          $child_ids = get_modal_array($svg_url);
        
        ?>
      </div>
    </div>

    <div class="col-md-2" id="toc-container" style="margin-left: -6%">
    <!-- <button style="margin-bottom: 5px; font-size: large;" class="btn btn-info fa fa-arrows-alt btn-block" id="top-button"> Full Screen</button> -->

      <!-- temporary, make the above a dropdown -->
      <!-- <div class="row">  -->
      <!-- <ul id="toc1">  -->
        <!-- TABLE OF CONTENTS WILL GO HERE -->
      <!-- </ul> -->
      <!-- </div> -->
    </div>
  </div>
  <script>
    let child_ids = <?php echo json_encode($child_ids); ?>;
    let post_id =  <?php echo $post_id; ?>;
    let svg_url =  <?php echo json_encode($scene_url); ?>;
    let num_sections =  <?php echo json_encode($num_sections); ?>;
    let scene_sections =  <?php echo json_encode($scene_sections); ?>;
    let scene_same_hover_color_sections = <?php echo json_encode($scene_same_hover_color_sections); ?>;

    let scene_default_hover_color =  <?php echo json_encode($scene_default_hover_color); ?>;
    let scene_text_toggle =  <?php echo json_encode($scene_text_toggle); ?>;
    let scene_toc_style =  <?php echo json_encode($scene_toc_style); ?>;
    let scene_full_screen_button  = <?php echo json_encode($scene_full_screen_button); ?>;    
  </script>
  <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> -->
</div>
</div>
<?php
//This is where all of the stuff related to make_title will be. 
//variables needed: scene_location, 
global $wpdb;

$query = "SELECT * FROM {$wpdb->postmeta} 
          WHERE (meta_id = %d OR post_id = %d OR meta_key = %s OR meta_value = %s)
          LIMIT 100";

$results = $wpdb->get_results($wpdb->prepare($query, $post_id, $post_id, strval($post_id),  strval($post_id)));

$title_arr = [];
foreach ($results as $row) {
  $nestedArray = unserialize($row->meta_value);
  
  if ($nestedArray === false && $row->meta_value !== 'b:0;') {
      $title_arr[$row->meta_key] = $row->meta_value;
  } else {
    $title_arr[$row->meta_key] = $nestedArray;
  }


}



?>
 </body>
<script>


let title_arr  = <?php echo json_encode($title_arr); ?>;

</script>

  <!-- </body> -->

<?php
get_footer();