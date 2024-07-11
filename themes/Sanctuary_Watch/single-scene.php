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

//ALL CURRENTLY ASSUME THERE IS THE CORRECT POSTMETA DATA AND THERE ALL SUFFICIENT INFORMATION EXISTS
//IMPLEMENT ERROR CHECKS LATER


// Retrieves the ID of the current post
$post_id = get_the_ID();
$scene_url = get_post_meta($post_id, 'scene_infographic');

// Fetches scene information and photos based on the current post ID
//array structure(triple nested arrays): arr = [[text1, url1],[text2, url2], ....]
// $total_arr = get_scene_info_photo($post_id);
// $scene_info_arr = $total_arr[0];
// $scene_photo_arr = $total_arr[1];
?>
<!-- <body> -->
<!-- <body class="p-3 m-0 border-0 bd-example m-0 border-0"> -->
<div class="modal" id="myModal" style="z-index: 999; background-color: rgba(0,0,0,0.8);">
  <div class="modal-dialog" style="margin: 10% auto; max-width: 80%">
    <div class="modal-content" >

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 id = "modal-title" class="modal-title"></h4>
        <button id="close" type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <div class="row">
          <div id="accordion-container" class="col-6">
        
          </div>
          <div id="tagline-container" class="col-3">
            <!-- tagline goes -->
            tagline here
          </div>
        </div>
      </div>

      <!-- images go here -->
      <ul class="nav nav-tabs" id="myTab" role="tablist">
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

      <div class="tab-content" id="myTabContent">
          <!-- <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">Blah Blah</div>
          <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">ok</div>
          <div class="tab-pane fade" id="contact-tab-pane" role="tabpanel" aria-labelledby="contact-tab" tabindex="0">bruh </div> -->
          <!-- <div class="tab-pane fade" id="disabled-tab-pane" role="tabpanel" aria-labelledby="disabled-tab" tabindex="0">tuff</div> -->
      </div>
      <!-- image stuff ends here -->

    </div>
  </div>
</div>

<div class="container-fluid">
  <div class="row align-items-start">
    <div class="col-12 col-lg-8 mb-3 mb-lg-0">
      <div id="svg1" class="responsive-image-container">
        <?php
          $svg_url = get_post_meta($post_id, 'scene_infographic', true); 
          $child_ids = get_modal_array($svg_url);
        ?>
      </div>
    </div>
    <div class="col-12 col-lg-4">
      <ul id="toc1" > 
            <!-- TABLE OF CONTENTS WILL GO HERE -->
            
          </ul>
        </div>
      </div>
      <script>
        // Convert the array of child IDs to a JSON string and mbed the child_ids_json variable into the html for the JS script to pick up and use.
        let child_ids = <?php echo json_encode($child_ids); ?>;
        let post_id =  <?php echo $post_id; ?>;
        let svg_url =  <?php echo json_encode($scene_url); ?>;
        //Log json file for debugging.
        // console.log(child_id_json);
        
        
    </script>

    </div>
    

  <!-- </body> -->

<?php
get_footer();
?>