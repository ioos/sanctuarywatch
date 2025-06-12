<?php
defined('ABSPATH') || exit;
$instance_num =  get_post_meta(get_the_ID(), 'scene_location', true);
$instance_footer = get_post_meta($instance_num, 'instance_footer', true);
$sitewide_footer = get_option('webcr_settings')['intro_text'];


if (!empty($instance_footer) || !empty($sitewide_footer)) {
    $instance_footer_element_number =count($instance_footer);

    echo '<footer class="site-footer" >';
    echo '<div class="container" style="margin: 0 auto; max-width: 1200px;">';
    echo '<div class="row">';

    if (!empty($sitewide_footer)) {
        // Apply flex styling to .col-sm to center its direct child (the new wrapper)
        echo '<div class="col-sm footer-column">';
        // This wrapper will be centered in .col-sm, and its text content will be left-aligned.
        echo '  <div class="footer-content-wrapper">';
        echo '    <h6 class="text-white">' . ucfirst($footer_component[$i]) . '</h6>';
        echo '    <div class="footer_component">';
        echo $footer_entry;
        echo '    </div>';
        echo '  </div>'; // Closing footer-content-wrapper
        echo '</div>';
    }

    if (!empty($instance_footer)) {
        $footer_component = array('about','contact','reports');
        for ($i = 0; $i < 3; $i++) {
            $footer_array_key = 'instance_footer_' . $footer_component[$i];
            $footer_entry = $instance_footer[$footer_array_key];
            if (!empty($footer_entry)) {
                // Apply flex styling to .col-sm to center its direct child (the new wrapper)
                echo '<div class="col-sm footer-column">';
                // This wrapper will be centered in .col-sm, and its text content will be left-aligned.
                echo '  <div class="footer-content-wrapper">';
                echo '    <h6 class="text-white">' . ucfirst($footer_component[$i]) . '</h6>';
                echo '    <div class="footer_component">';
                echo $footer_entry;
                echo '    </div>';
                echo '  </div>'; // Closing footer-content-wrapper
                echo '</div>';
            } 
        }   
    }

    echo '</div>';
    echo '</div>';
    echo '</footer>';
} 

wp_footer();
echo '</body>';
echo '</html>';