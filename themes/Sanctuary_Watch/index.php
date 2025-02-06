<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-HQV3WX3V2W"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-HQV3WX3V2W');
</script>
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

$args = array(
    'post_type'      => 'instance',
    'posts_per_page' => -1, 
);

$instances_query = new WP_Query($args);

$instance_slugs = array(); 
$instance_legacy_urls = [];

if ($instances_query->have_posts()) {
    while ($instances_query->have_posts()) {
        $instances_query->the_post();
        
        $instance_id = get_the_ID();
        $instance_slug = get_post_meta($instance_id, 'instance_slug', true); 
        $instance_overview_scene = get_post_meta($instance_id, 'instance_overview_scene', true); 
        $instance_legacy_content_url = get_post_meta($instance_id, 'instance_legacy_content_url', true);


        if ($instance_slug) {
            $instance_slugs[] = [$instance_slug, $instance_overview_scene]; 
        }
        if ($instance_legacy_content_url){
            $instance_legacy_urls[$instance_id] = $instance_legacy_content_url;
        }
    }
    wp_reset_postdata();
} else {
    // echo 'No instances found.';
}



?>
<script> 

let legacy_urls = <?php echo json_encode($instance_legacy_urls); ?>;
console.log("legacy urls here");
console.log(legacy_urls);

</script>


<body>
<div id="entire_thing" style="
 
  max-width: 1700px !important;
    margin: 0 auto;
    background: #f2f2f2;
    padding-bottom: 9%;
    margin-top: -20px;
    padding-top: 1%;

    
"> 
<div class="container-fluid">
<!-- <i class="fa fa-clipboard-list" role="presentation" aria-label="clipboard-list icon"></i> -->
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
</div>



<!-- Main container with Bootstrap styling for fluid layout -->
<div class="container-fluid main-container" style="margin-top: 0px;">
    <?php 

// BEGIN TEXT





// END TEXT


            $front_page_intro = get_option('webcr_settings')['intro_text'];
            if ($front_page_intro == false) {
                $front_page_intro = "None";
            }
            echo $front_page_intro;
        ?>
</div>

<?php 
$terms = get_terms([
    'taxonomy'   => 'instance_type',
    'hide_empty' => false, // Include terms even if not assigned to posts
]);

if (empty($terms) || is_wp_error($terms)) {
    return; // No terms found or an error occurred
}

// Prepare an array with instance_order
$terms_array = [];
foreach ($terms as $term) {
    $instance_order = get_term_meta($term->term_id, 'instance_order', true);
    $terms_array[] = [
        'id'            => $term->term_id,
        'name'           => $term->name,
        'description'    => $term->description, // Get term description
        'instance_order' => (int) $instance_order, // Ensure numeric sorting
    ];
}

// Sort terms by instance_order
usort($terms_array, function ($a, $b) {
    return $a['instance_order'] - $b['instance_order'];
});


foreach ($terms_array as $term){
    ?>

    <?php 
    echo "<div class='container-fluid main-container'><h2 style='color: #024880; margin-right: auto;'>{$term['name']}</h2></div>";
    echo "<div class='container-fluid main-container' style='margin-top: -30px;'>{$term['description']}</div>";
    echo "<div class='container-fluid main-container' style='margin-top: -30px;'><div class ='row'>";
    $args = [
        'post_type'      => 'instance',
        'posts_per_page' => -1, // Get all posts
        'fields'         => ['ids', 'post_title'], 
        'meta_query'     => [
            [
                'key'     => 'instance_type',
                'value'   => $term["id"],
                'compare' => '='
            ]
        ]
    ];

 
    $instances = new WP_Query($args);
    foreach ($instances->posts as $instance){
        echo '<div class="col-xs-12 col-sm-6 col-md-4"><div class="card" style="margin: 10px;">';
        $tile_image = get_post_meta($instance->ID, "instance_tile")[0];
        echo "<img class='card-img-top' src='{$tile_image}' alt='{$instance->post_title}'>";
        $instance_slug = get_post_meta($instance->ID, "instance_slug")[0]; 
        echo '<div class="card-body">';
        echo "<a href='$instance_slug' class='btn ' style='display: flex; justify-content: center; align-items: center; color: white !important; background-color: #00467F !important'>{$instance->post_title}</a></div>";

        echo "</div></div>";
    }
    echo "</div></div>";
}

?>

</div>
</body>
<script>
    let post_id =  <?php echo $post_id; ?>;
    // let is_logged_in = <?php echo is_user_logged_in(); ?>;
    let is_logged_in = <?php echo json_encode(is_user_logged_in()); ?>;


</script>
<?php
// get_footer();
?>
