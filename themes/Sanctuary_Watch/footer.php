<?php
defined('ABSPATH') || exit;
$instance_num =  get_post_meta(get_the_ID(), 'scene_location', true);
// $instance_contact_person = get_post_meta($instance_num, 'instance_contact_person', true);
// $instance_more_info = get_post_meta($instance_num, 'instance_more_info', true);
$instance_footer_about = get_post_meta($instance_num, 'instance_footer', true);

?>
<script> 
let instance_contact_person =  <?php echo json_encode($instance_contact_person); ?>;
let instance_more_info =  <?php echo json_encode($instance_more_info); ?>;
let instance_num = <?php echo json_encode($instance_num); ?>;
let instance_footer_about = <?php echo json_encode($instance_footer_about); ?>;
console.log("footer stuff");
// console.log(instance_num);
// console.log(instance_contact_person);
// console.log(instance_more_info);
// console.log('more footer');
console.log(instance_footer_about);
</script>

<footer class="site-footer" style="background-color: #03386c; color: white; padding: 32px 12px; font-size: 13px;">
    <div class="container" style="margin: 0 auto; max-width: 1200px; padding: 0 15px;">
        <div class="row" style="display: flex; flex-wrap: wrap; margin: 0 -15px;">
            <div class="col-md-4" style="flex: 0 0 33.333%; padding: 0 15px;">
                <h5 style="color: white; font-weight: bold; margin-bottom: 12px;"><?php echo esc_html($instance_footer_about['instance_footer_about']); ?></h5>
                <a href="<?php echo esc_html($instance_more_info['instance_url']); ?>" id="about-sanctuary-btn" style="
                <!-- <a href="<?php echo esc_html($instance_footer_about['instance_url']); ?> id="about-sanctuary-btn" style=" -->
                    color: white; 
                    padding: 6px 0;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                    margin-bottom: 12px;
                    font-size: 17px;
                    display: inline-block;
                ">
                    <?php echo esc_html($instance_more_info['instance_link_text']); ?>
                </a>
            </div>
            
            <div class="col-md-4" style="flex: 0 0 33.333%; padding: 0 15px;">
                <h5 style="color: white; font-weight: bold; margin-bottom: 12px;">Contact</h5>
                <ul style="list-style: none; padding: 0; font-size: 17px;">
                    <li class="contact-person">
                        <a href="#" style="color: lightgrey; text-decoration: none;">
                            <strong><?php echo esc_html($instance_contact_person['instance_contact_name']); ?>, <?php echo esc_html($instance_contact_person['instance_contact_title']); ?></strong>
                        </a>
                    </li>
                    <li>
                        <a href="mailto:<?php echo esc_attr($instance_contact_person['instance_contact_email']); ?>" 
                           style="color: lightgrey; text-decoration: underline;">
                            <?php echo esc_html($instance_contact_person['instance_contact_email']); ?>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="col-md-4" style="flex: 0 0 33.333%; padding: 0 15px;">
                <h5 style="color: white; font-weight: bold; margin-bottom: 12px;">Reports</h5>
                <ul style="list-style: none; padding: 0; font-size: 17px;">
                    <!-- <li><a href="#" style="color: lightgrey; text-decoration: none;">Resources</a></li>
                    <li><a href="#" style="color: lightgrey; text-decoration: none;">News & Updates</a></li>
                    <li><a href="#" style="color: lightgrey; text-decoration: none;">Visit Us</a></li> -->
                    <li><?php echo esc_html($instance_footer_about['instance_footer_reports']); ?></li>
                </ul>
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>